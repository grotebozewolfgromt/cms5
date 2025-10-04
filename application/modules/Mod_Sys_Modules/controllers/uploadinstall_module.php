<?php

/**
 * cronjob
 */

namespace dr\modules\Mod_Sys_Modules\controllers;

use dr\classes\controllers\TControllerAbstract;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputFile;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\Textarea;
use dr\classes\dom\FormGenerator; 

//don't forget ;)
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Modules\Mod_Sys_Modules;
use ZipArchive;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


/**
 * Description of settings cronjob
 *
 * @author drenirie
 */
class uploadinstall_module extends TControllerAbstract
{    
    // private $obModel = null;
    private $sReturnURL = '';
    private $sModuleToInstall = '';
    private $arrUploadInstallMessages = array();

    public function __construct()
    {
        //global CMS_CURRENTMODULE;

        //handle authentication    
        if (!auth(CMS_CURRENTMODULE, Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, Mod_Sys_Modules::PERM_OP_INSTALL))
        {
            showAccessDenied(transm(CMS_CURRENTMODULE, 'message_moduleuninstall_notallowed', 'You are not allowed to uninstall modules'));
            die();
        }

        //==== declare return url etc
        // $this->objModel = new TSysModules();
        $this->sReturnURL = 'list_uninstalledmodules';
        $this->sModuleToInstall = '';
        if (isset($_GET[ACTION_VARIABLE_ID]))
            $this->sModuleToInstall = $_GET[ACTION_VARIABLE_ID]; //modules is passed as id

        //==== check if module is valid (to prevent spoofing)
        $arrModulesInModulesDir = getFileFolderArray(APP_PATH_MODULES, true, false);
        if (!in_array($this->sModuleToInstall, $arrModulesInModulesDir))
            $this->sModuleToInstall = ''; //making empty if not valid            


        parent::__construct();
    }


    /**
     * This function adds EARLY BINDING variables which are cached
     * (see description on top of this class for more info)
     * 
     * executes the things you want to cache
     * this function is ONLY called on a cache miss 
     * (if caching enabled, if NOT enabled it's ALWAYS called).
     * This function generates content for the cache file and for displaying on-screen
     * 
     * this function is executed BEFORE bindVarsLate(), because it's early binding
     * 
     * @return array with variables, use: "return get_defined_vars();" to use all variables declared in the execute() function
     */
    public function bindVarsEarly()
    {
        global $objCurrentModule;
        //global CMS_CURRENTMODULE;
        global $objAuthenticationSystem;
        

        //==== call install function if parameter id = module
        if ($this->sModuleToInstall)
        {
            $this->installModuleLocal($this->sModuleToInstall);
        }


        //==== populate form (we don't use the crud controller)
        $objForm = new FormGenerator('detailsave', getURLThisScript());
        $objForm->getForm()->setEnctype(Form::ENCTYPE_MULTIPART_FORMDATA);

            //commandpanel first (later again)            
                    //submit
                $objSubmit = new InputSubmit();    
                $objSubmit->setValue(transm(CMS_CURRENTMODULE, 'form_button_upload', 'upload'));
                $objSubmit->setName('btnSubmit');
                if ((!$this->sModuleToInstall) && (!$objForm->isFormSubmitted())) //only add on first load
                    if (auth(CMS_CURRENTMODULE, Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, Mod_Sys_Modules::PERM_OP_UPLOAD))    
                        $arrCommandPanel[] = $objSubmit;        
        
                    //close
                $objCancel = new InputButton();    
                $objCancel->setValue(transcms('form_button_close', 'close'));
                $objCancel->setName('btnClose');    
                $objCancel->setClass('button_cancel');    
                $objCancel->setOnclick('window.location.href = \''.$this->sReturnURL.'\';');    
                $arrCommandPanel[] = $objCancel;
        
                $objForm->addArray($arrCommandPanel, 'commands_top');
                $objForm->assignCSSClassSection('commands_top', 'div_commandpanel');          
                
                    //upload elements
                $objFile = new InputFile();   
                $objFile->setNameArray('btnUploadFiles');     
                $objFile->setMultiple(true);
                if ((!$this->sModuleToInstall) && (!$objForm->isFormSubmitted())) //only add on first load
                    $objForm->add($objFile);
                
                    //install after upload
                $objInstallAfterUpload = new InputCheckbox();
                $objInstallAfterUpload->setName('chkInstallAfterUpload');
                $objInstallAfterUpload->setChecked(true);
                if ((!$this->sModuleToInstall) && (!$objForm->isFormSubmitted())) //only add on first load
                    $objForm->add($objInstallAfterUpload, '', transm(CMS_CURRENTMODULE, 'checkbox_installafterupload', 'install module after upload'));            
                
                    //messages
                $objMessages = new Textarea();
                $objMessages->setName('lblMessages');
                $objMessages->setContentEditable(false);
                $objMessages->setClass('fullwidthtag');
                if (($this->sModuleToInstall) || ($objForm->isFormSubmitted())) //only add after uploading or when a module is supplied with url
                    $objForm->add($objMessages, '', transm(CMS_CURRENTMODULE, 'label_progressmessages', 'Progress messages'));            


                //add another command panel on the bottom
                $objForm->addArray($arrCommandPanel, 'commands_bottom');  
                $objForm->assignCSSClassSection('commands_bottom', 'div_commandpanel');  

        //==== process uploaded file(s)
        //index-keys $arrUploadFiles: 
        //[name] (original filename without path)
        //[type]
        //[tmp_name] (temp filepath)
        //[size]

        if ($_FILES)
            $arrUploadFiles = uploadFilesRearrangeArray($_FILES[$objFile->getName()]);
        else
            $arrUploadFiles = array();

        //security: if not allowed, prevent from being extracted (and thus installed)
        if (!auth(CMS_CURRENTMODULE, Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, Mod_Sys_Modules::PERM_OP_UPLOAD))    
            $arrUploadFiles = array();


        foreach($arrUploadFiles as $sUploadFile)
        {        
            $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_uploaded','file uploaded: [file]', 'file', $sUploadFile['name']);

            if (stristr($sUploadFile['type'], 'zip') !== false)
            {
                $sTempDir = tempdir();
                // $sTempDir = APP_PATH_MODULES.DIRECTORY_SEPARATOR.'temp';
                // var_dump($sTempDir);
                // mkdir($sTempDir);
                
                //extract objZip in temp directory
                // var_dump($sTempDir);
                $bZipOK = true;
                $objZip = new ZipArchive;
                if ($objZip->open($sUploadFile['tmp_name']) === TRUE) 
                {
                    $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_extractingzipfile','extracting [file]', 'file', $sUploadFile['name']);
                    $objZip->extractTo($sTempDir);
                    $objZip->close();
                    $bZipOK = true;
                } 
                else 
                {
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'installupload module: '.$sUploadFile['name'].': zip extraction failed');
                    $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_extractingzipfile_failed','file [file] extraction failed', 'file', $sUploadFile['name']);
                    $bZipOK = false;
                }
                
                //copy files
                if ($bZipOK)
                {
                    $arrZipArchiveFiles = getFileFolderArray($sTempDir);
                    foreach ($arrZipArchiveFiles as $arrZipArchiveFile)//if multiple directories are in the zip file
                    {
                        $bInstallOK = true;
                        $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_movingfilesfromtemp','copying files to module directory');

                        //store all the directories (=modules) that were extracted in the temp directory (we have to install only the new modules)
                        $arrModsInTempDir = getFileFolderArray($sTempDir, true, false);

                        //move contents of temp dir to module directory
                        if ($bInstallOK)
                        {
                            if (renameRecursive($sTempDir, APP_PATH_MODULES) === false)
                            {
                                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'installupload module: moving files from temp to module dir failed: '.$sUploadFile['name'].'');
                                $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_movingfilesfromtemp_failed','FAILED!!! moving files to module directory');
                            }
                            else
                            {
                                $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_movingfilesfromtemp_success','moving files completed');                        

                                //only install if checkbox checked
                                if ($objInstallAfterUpload->getValueSubmittedAsBool())
                                {
                                    foreach ($arrModsInTempDir as $sModDir)
                                    {
                                        if (!$this->installModuleLocal($sModDir))
                                        {
                                            $bInstallOK = false;
                                            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'installupload module: install failed: '.$sModDir);
                                        }
                                    }
                                }
                                else
                                    $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'filesuploadedbutnotinstalled','files moved to module directory (but module not installed)');                        
                            }
                        }

                    }
                }

            }
            else
            {
                //remove file  
                if (file_exists($sUploadFile['tmp_name']))          
                    unlink($sUploadFile['tmp_name']);
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'FAIL: upload file: '.$sUploadFile['tmp_name'].' is not a *.zip file. File removed from temporary directory');
                $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'file_notazipfile','file [file] is not a zip file', 'file', $sUploadFile['name']);
            }
                
        }
    
        //==== updating permissions 
        //if you add a new module, the permissions are in the database, but not available yet to the user
        //in other words: although a module is installed and the user has permissions, he doesn't see it, 
        //because the permissions array is outdated
        //therefore we force a reload of the permissions
        $objAuthenticationSystem->populatePermissionsSessionArray();



        
        //===== outputting messages to textarea
        $objMessages->setText(implode("\n", $this->arrUploadInstallMessages));

        //====== page defaults
        
        $sTitle = transm(CMS_CURRENTMODULE, 'pagetitle_uploadmodule', CMS_CURRENTMODULE.': upload module');
        $sHTMLTitle = $sTitle;
        $sHTMLMetaDescription = $sTitle;
        $arrTabsheets = $objCurrentModule->getTabsheets(); 

        return get_defined_vars();
    }


    /**
     * This function adds LATE BINDING variables which are NOT cached 
     * (for more info: see description on top of this class)
     * 
     * executes the things you always want to execute, even on a cache miss
     * bindVarsEarly() is executed first, then bindVarsLate()
     *  
     * These variables that aren't resolved by php in the cache file
     * This way you can add dynamic php code to an otherwise cached page
     * 
     * These late binding variables need to be in the following format in the template: [variablename]
     * (Otherwise PHP will resolve variables in thecachefile with the format: $variablename)
     * 
     * This function is executed AFTER bindVarsEarly()
     * 
     * @return array with variables, use: "return get_defined_vars();" to use all variables declared in the execute() function
     */
    public function bindVarsLate()
    {
        return;
    }



    /**
     * install module
     *
     * @param string $sModule
     * @return bool
     */
    private function installModuleLocal($sModule) //it's a function so we can call it later in a loop when uploading multiple modules in one zip file
    {
        //global CMS_CURRENTMODULE;

        if (auth(CMS_CURRENTMODULE, Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, Mod_Sys_Modules::PERM_OP_INSTALL))
        {
            $sTempModClass = '';
            $sTempModClass = getModuleFullNamespaceClass($sModule);
            $objCurrentModule = new $sTempModClass;    
            
            $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'installmodule_install', 'installing module [module]', 'module', $sModule);
            if (!$objCurrentModule->installModule())
            {
                error_log('installing module failed for module '.$sModule);
                $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'installmodule_install_failed', 'installing module [module] FAILED!', 'module', $sModule);
                return false;
            }          
            unset($sTempModClass);
            unset($objCurrentModule);
            
            $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'installmodule_install_success', 'installing module [module] successful!', 'module', $sModule);
            $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'installmodule_install_success_clickclose', 'after clicking "close" [module] will be available', 'module', $sModule);

        }
        else
        {
            $this->arrUploadInstallMessages[] = transm(CMS_CURRENTMODULE, 'installmodule_install_notallowed', 'installing module [module] NOT allowed!', 'module', $sModule);            
            return false;
        }

        return true;
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        //global CMS_CURRENTMODULE;
        return getPathModuleTemplates(CMS_CURRENTMODULE, true).'tpl_uploadinstall_module.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.CMS_CURRENTMODULE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_uploadinstall_module.php';
    }

    /**
     * return path of the skin template
     * 
     * return '' if no skin
     *
     * @return string
     */
    public function getSkinPath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php';
    }    

}
