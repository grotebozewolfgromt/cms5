<?php

/**
 * cronjob
 */

namespace dr\modules\Mod_Sys_Settings\controllers;

use dr\classes\controllers\TCacheControllerAbstract;
use dr\classes\controllers\TControllerAbstract;
use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TOnlyNumeric;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;

//don't forget ;)
use dr\modules\Mod_Sys_Settings\models\TSysSettings;
use dr\classes\patterns\TModuleAbstract;
use dr\classes\TConfigFileApplication;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\modules\Mod_Sys_Settings\Mod_Sys_Settings;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


/**
 * Description of settings cronjob
 *
 * @author drenirie
 */
class settings_maintenance extends TControllerAbstract
{    
    private $objConfigFile = null;

    public function __construct()
    {
        //global APP_ADMIN_CURRENTMODULE;
        $this->objConfigFile = new TConfigFileApplication();      
        $this->objConfigFile->loadFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);          

        //handle authentication    
        if (!auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_MAINTENANCE, Mod_Sys_Settings::PERM_OP_VIEW))
        {
            showAccessDenied(transm(APP_ADMIN_CURRENTMODULE, 'message_cronjob_notallowed', 'you are not allowed to view the cronjob tab'));
            die();
        }

        $this->enableDisableInstaller();

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
        //global APP_ADMIN_CURRENTMODULE;

        $sTitle = transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_settings_maintenance', 'System maintenance');   
        $sHTMLTitle = $sTitle;
        $sHTMLMetaDescription = $sTitle;    
        $arrTabsheets = $objCurrentModule->getTabsheets(); 
        $bInstallerEnabled = $this->objConfigFile->getAsBool('APP_INSTALLER_ENABLED');

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
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        //global APP_ADMIN_CURRENTMODULE;
        return getPathModuleTemplates(APP_ADMIN_CURRENTMODULE, true).'tpl_settings_maintenance.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.APP_ADMIN_CURRENTMODULE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_settings_cronjob.php';
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


    private function enableDisableInstaller()
    {
        if (isset($_GET['enableinstaller']))
        {
            // $this->objConfigFile->loadFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);

            //disable installer
            if ($_GET['enableinstaller'] === '0')
                $this->objConfigFile->setAsBool('APP_INSTALLER_ENABLED', false);

            //enable installer
            if ($_GET['enableinstaller'] === '1')
                $this->objConfigFile->setAsBool('APP_INSTALLER_ENABLED', true);

            $this->objConfigFile->saveFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);                            
        }
    }

}
