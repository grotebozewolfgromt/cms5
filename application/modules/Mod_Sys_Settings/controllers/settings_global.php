<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Settings\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\Label;
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
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\classes\TConfigFileApplication;
use dr\modules\Mod_Sys_Settings\Mod_Sys_Settings;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


/**
 * Description of settings_global
 *
 * @author drenirie
 */
class settings_global extends TCRUDDetailSaveController
{
    private $objEdtEmailSysAdmin = null;//dr\classes\dom\tag\form\InputText
    private $objChkAnyoneCanRegister = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objOptNewUserRegisterGroupID = null;//dr\classes\dom\tag\form\Select
    private $objEdtUserPasswordExpiresDays = null;//dr\classes\dom\tag\form\InputText
    private $objEdtPaginatorMaxResult = null;//dr\classes\dom\tag\form\Select
    private $objEdtMailbotFromEmailaddress = null;//dr\classes\dom\tag\form\InputText
    private $objEdtMailbotFromName = null;//dr\classes\dom\tag\form\InputText
    private $objSelTheme = null;//dr\classes\dom\tag\form\Select 
    private $objChkWebsitesInNavigation = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkSaveSuccessNotification = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkLoginOnlyWhiteListedIPs = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkStealthMode = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objLblResize = null;//dr\classes\dom\tag\form\Label
    private $objEdtImgWidthMax = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgHeightMax = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgWidthLarge = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgHeightLarge = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgWidthMedium = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgHeightMedium = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgWidthThumbnail = null; //dr\classes\dom\tag\form\InputText
    private $objEdtImgHeightThumbnail = null; //dr\classes\dom\tag\form\InputText

    private $objConfigFile = null;//TConfigFileApplication


    public function __construct()
    {
        $this->objConfigFile = new TConfigFileApplication();

        parent::__construct();
    }

    /**
     * OVERLOADED FROM PARENT!!!!
     * handle db loading or creating a new record
     */
    protected function handleNewEditRecord()
    {
        global $objAuthenticationSystem;

        //obtain newest settings (someone might have changed them in the meantime)
        if (!$this->getModel()->loadFromDB())
        {
            error_log(__CLASS__.': settingsReload() ERROR');
            sendMessageError(transcms('message_loadsettings_failed', 'Failed to load global settings'));
        }

        if (auth($this->getModule(), $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CHANGE)) //only if you have rights to save: check in. otherwise when saving you need to checkin again
        {        
            //WE DONT USE checkout/checkin otherwise you can't click from one tab to another.
            //first check if all the records are not checked out
            // while ($this->getModel()->next())
            // {            
            //     if ($this->getModel()->getCheckedOut())
            //     {
            //         showAccessDenied(transcms('error_settings_recordlocked', 'settings are locked for editing by another user'));
            //         die();
            //     }
            // }

            //checkout mechanics: the actual checkout
            // $this->getModel()->resetRecordPointer();
            // while ($this->getModel()->next())
            // {            
            //     $this->getModel()->checkoutNowDB($this->getModel()->getID(), $this->getModule().': '.$this->getAuthorisationCategory().': by user: '.$objAuthenticationSystem->getUsers()->getUsername()); 
            // }
        }

    }

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        //CMS is called "system" in this screen to not confuse users when it's not used as CMS

        $sTransSectionCMS = transm($this->getModule(), 'sectiontitle_cms', 'System');
        $sTransSectionCMSMemberships = transm($this->getModule(), 'sectiontitle_cms_security', 'System: security');
        $sTransSectionCMSThemes = transm($this->getModule(), 'sectiontitle_cms_layout', 'System: layout');
        $sTransSectionCMSImages = transm($this->getModule(), 'sectiontitle_cms_images', 'System: images');

            //email sys administrator
        $this->objEdtEmailSysAdmin = new InputText();
        $this->objEdtEmailSysAdmin->setNameAndID('edtEmailSysAdmin');
        $this->objEdtEmailSysAdmin->setClass('fullwidthtag');   
        $this->objEdtEmailSysAdmin->setRequired(true);   
        $this->objEdtEmailSysAdmin->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtEmailSysAdmin->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtEmailSysAdmin, $sTransSectionCMS, transm($this->getModule(), 'form_field_emailsysadmin', 'Email address system administrator (whole system, all sites, all login controllers etc'));

            //max results paginator
        $this->objEdtPaginatorMaxResult = new InputNumber();
        $this->objEdtPaginatorMaxResult->setNameAndID('edtMaxResultsPaginator');
        $this->objEdtPaginatorMaxResult->setClass('quarterwidthtag');   
        $this->objEdtPaginatorMaxResult->setRequired(true);   
        $this->getFormGenerator()->add($this->objEdtPaginatorMaxResult, $sTransSectionCMS, transm($this->getModule(), 'form_field_paginatormaxresultsperpage', '# Records shown per page'));

            //emailbot from email address
        $this->objEdtMailbotFromEmailaddress = new InputText();
        $this->objEdtMailbotFromEmailaddress->setNameAndID('edtMailbotFromEmailaddress');
        $this->objEdtMailbotFromEmailaddress->setClass('fullwidthtag');   
        // $this->objEdtMailbotFromEmailaddress->setRequired(true);   
        $this->objEdtMailbotFromEmailaddress->setMaxLength(255);
        $objValidator = new TEmailAddress(false);
        $this->objEdtMailbotFromEmailaddress->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtMailbotFromEmailaddress, $sTransSectionCMS, transm($this->getModule(), 'form_field_mailbot_from_emailaddress', 'FROM Email address mailbot (i.e. used for password recovery)'));
    
            //emailbot from email name
        $this->objEdtMailbotFromName = new InputText();
        $this->objEdtMailbotFromName->setNameAndID('edtMailbotFromName');
        $this->objEdtMailbotFromName->setClass('fullwidthtag');   
        $this->objEdtMailbotFromName->setRequired(true);   
        $this->objEdtMailbotFromName->setMaxLength(100);
        $objValidator = new TMaximumLength(100);
        $this->objEdtMailbotFromName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtMailbotFromName, $sTransSectionCMS, transm($this->getModule(), 'form_field_mailbot_from_name', 'FROM name mailbot (i.e. used for password recovery)'));
    
            //anyone can make user account
        $this->objChkAnyoneCanRegister = new InputCheckbox();
        $this->objChkAnyoneCanRegister->setNameAndID('chkAnyoneCanRegister');
        $this->getFormGenerator()->add($this->objChkAnyoneCanRegister, $sTransSectionCMSMemberships, transm($this->getModule(), 'form_field_anyonecanregister', 'Anyone can create account'));         

            //login only allowed from whitelisted IP addresses
        $this->objChkLoginOnlyWhiteListedIPs = new InputCheckbox();
        $this->objChkLoginOnlyWhiteListedIPs->setNameAndID('chkLoginOnlyWhitelistedIP');
        $this->getFormGenerator()->add($this->objChkLoginOnlyWhiteListedIPs, $sTransSectionCMSMemberships, transm($this->getModule(), 'form_field_loginonlyallowedwhitelistedips', 'Login only allowed from whitelisted IP addresses'));         
    
            //stealth mode whitelisted IP addresses
        $this->objChkStealthMode = new InputCheckbox();
        $this->objChkStealthMode->setNameAndID('chkStealthModeWhitelistedIP');
        $this->getFormGenerator()->add($this->objChkStealthMode, $sTransSectionCMSMemberships, transm($this->getModule(), 'form_field_stealthmodewhitelistedips', 'Enable stealth mode for unfamiliar IP addresses (system will produce 404-errors pretending it doesn\'t exist ==> only works when login-only-whitelisted-ip is enabled)'));         
    
            //user groupid
        $this->objOptNewUserRegisterGroupID = new Select();
        $this->objOptNewUserRegisterGroupID->setNameAndID('optDefaultUsergroupID');
        $this->objOptNewUserRegisterGroupID->setClass('quarterwidthtag');   
        $this->getFormGenerator()->add($this->objOptNewUserRegisterGroupID, $sTransSectionCMSMemberships, transm($this->getModule(), 'form_field_usergroup', 'Default role new users'));

            //auto expire password days
        $this->objEdtUserPasswordExpiresDays = new InputNumber();
        $this->objEdtUserPasswordExpiresDays->setNameAndID('edtUserPasswordExpiresDays');
        $this->objEdtUserPasswordExpiresDays->setClass('quarterwidthtag');   
        $this->objEdtUserPasswordExpiresDays->setRequired(true);   
        $this->getFormGenerator()->add($this->objEdtUserPasswordExpiresDays, $sTransSectionCMSMemberships, transm($this->getModule(), 'form_field_userpasswordexpiresdays', 'Users need to change password after X days (0 = never, 1 = 1 day, 2 = 2 days etc.)'));


            //websites in navigation
        $this->objChkWebsitesInNavigation = new InputCheckbox();
        $this->objChkWebsitesInNavigation->setNameAndID('chkWebsitesInNavigation');
        $this->getFormGenerator()->add($this->objChkWebsitesInNavigation, $sTransSectionCMSThemes, transm($this->getModule(), 'form_field_websitesinnavigation', 'Show websites in navigation'));         
    
            //show save notification
        $this->objChkSaveSuccessNotification = new InputCheckbox();
        $this->objChkSaveSuccessNotification->setNameAndID('objChkSaveSuccessNotification');
        $this->getFormGenerator()->add($this->objChkSaveSuccessNotification, $sTransSectionCMSThemes, transm($this->getModule(), 'form_field_savesuccessnotification', 'Show notification when save is successful'));
    
            //themes
        $this->objSelTheme = new Select();
        $this->objSelTheme->setNameAndID('optCMSTheme');
        $this->objSelTheme->setClass('quarterwidthtag');   
        $this->getFormGenerator()->add($this->objSelTheme, $sTransSectionCMSThemes, transm($this->getModule(), 'form_field_theme', 'Theme'));         


            //image size: label
        $this->objLblResize = new Label();
        $this->getFormGenerator()->add($this->objLblResize, $sTransSectionCMSImages, transm($this->getModule(), 'form_label_resizeimages', 'Images are automatically resized when uploading. Here you can set the resolutions for the resized images:'));         

            //image size: max
        $this->objEdtImgWidthMax = new InputNumber();
        $this->objEdtImgWidthMax->setNameAndID('edtImgWidthMax');
        $this->objEdtImgWidthMax->setClass('quarterwidthtag');   
        $this->objEdtImgWidthMax->setRequired(true);   
        $this->objEdtImgWidthMax->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgWidthMax->addValidator($objValidator);    

        $this->objEdtImgHeightMax = new InputNumber();
        $this->objEdtImgHeightMax->setNameAndID('edtImgHeightMax');
        $this->objEdtImgHeightMax->setClass('quarterwidthtag');   
        $this->objEdtImgHeightMax->setRequired(true);   
        $this->objEdtImgHeightMax->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgHeightMax->addValidator($objValidator);    

        $this->getFormGenerator()->addArray(array($this->objEdtImgWidthMax, $this->objEdtImgHeightMax), $sTransSectionCMSImages, transm($this->getModule(), 'form_field_image_size_max', 'Maximum size (width px, height px)'));

            //image size: large
        $this->objEdtImgWidthLarge = new InputNumber();
        $this->objEdtImgWidthLarge->setNameAndID('edtImgWidthLarge');
        $this->objEdtImgWidthLarge->setClass('quarterwidthtag');   
        $this->objEdtImgWidthLarge->setRequired(true);   
        $this->objEdtImgWidthLarge->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgWidthLarge->addValidator($objValidator);    

        $this->objEdtImgHeightLarge = new InputNumber();
        $this->objEdtImgHeightLarge->setNameAndID('edtImgHeightLarge');
        $this->objEdtImgHeightLarge->setClass('quarterwidthtag');   
        $this->objEdtImgHeightLarge->setRequired(true);   
        $this->objEdtImgHeightLarge->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgHeightLarge->addValidator($objValidator);    

        $this->getFormGenerator()->addArray(array($this->objEdtImgWidthLarge, $this->objEdtImgHeightLarge), $sTransSectionCMSImages, transm($this->getModule(), 'form_field_image_size_large', 'Large size (width px, height px)'));
                
            //image size: medium
        $this->objEdtImgWidthMedium = new InputNumber();
        $this->objEdtImgWidthMedium->setNameAndID('edtImgWidthMedium');
        $this->objEdtImgWidthMedium->setClass('quarterwidthtag');   
        $this->objEdtImgWidthMedium->setRequired(true);   
        $this->objEdtImgWidthMedium->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgWidthMedium->addValidator($objValidator);    

        $this->objEdtImgHeightMedium = new InputNumber();
        $this->objEdtImgHeightMedium->setNameAndID('edtImgHeightMedium');
        $this->objEdtImgHeightMedium->setClass('quarterwidthtag');   
        $this->objEdtImgHeightMedium->setRequired(true);   
        $this->objEdtImgHeightMedium->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgHeightMedium->addValidator($objValidator);    

        $this->getFormGenerator()->addArray(array($this->objEdtImgWidthMedium, $this->objEdtImgHeightMedium), $sTransSectionCMSImages, transm($this->getModule(), 'form_field_image_size_medium', 'Medium size (width px, height px)'));
                
            //image size: thumbnail size
        $this->objEdtImgWidthThumbnail = new InputNumber();
        $this->objEdtImgWidthThumbnail->setNameAndID('edtImgWidthThumbnail');
        $this->objEdtImgWidthThumbnail->setClass('quarterwidthtag');   
        $this->objEdtImgWidthThumbnail->setRequired(true);   
        $this->objEdtImgWidthThumbnail->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgWidthThumbnail->addValidator($objValidator);    

        $this->objEdtImgHeightThumbnail = new InputNumber();
        $this->objEdtImgHeightThumbnail->setNameAndID('edtImgHeightThumbnail');
        $this->objEdtImgHeightThumbnail->setClass('quarterwidthtag');   
        $this->objEdtImgHeightThumbnail->setRequired(true);   
        $this->objEdtImgHeightThumbnail->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtImgHeightThumbnail->addValidator($objValidator);    

        $this->getFormGenerator()->addArray(array($this->objEdtImgWidthThumbnail, $this->objEdtImgHeightThumbnail), $sTransSectionCMSImages, transm($this->getModule(), 'form_field_image_size_thumbnail', 'Small/thumbnail size (width px, height px)'));
                
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Settings::PERM_CAT_GLOBALSETTINGS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {    
        //==== Settings from Database: we have multiple records to deal with
        $this->getModel()->resetRecordPointer();
        while($this->getModel()->next())
        {
            //pick out the record we want to set

            //email sys admin
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_SYSTEM, SETTINGS_SYSTEM_EMAILSYSADMIN))
            {
                $this->getModel()->set(TSysSettings::FIELD_VALUE, $this->objEdtEmailSysAdmin->getValueSubmitted());
            }

            //default group id
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_NEWUSER_ROLEID))
            {
                $this->getModel()->set(TSysSettings::FIELD_VALUE, intToStr($this->objOptNewUserRegisterGroupID->getValueSubmittedAsInt()));
            }

            //password expires days
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS))
            {
                $this->getModel()->set(TSysSettings::FIELD_VALUE, intToStr($this->objEdtUserPasswordExpiresDays->getValueSubmittedAsInt()));
            }            

            //max records per page
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE))
            {
                $this->getModel()->set(TSysSettings::FIELD_VALUE, intToStr($this->objEdtPaginatorMaxResult->getValueSubmitted()));
            }

            //mailbot email address
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_SYSTEMMAILBOT_FROM_EMAILADDRESS))
            {
                $this->getModel()->set(TSysSettings::FIELD_VALUE, $this->objEdtMailbotFromEmailaddress->getValueSubmitted());
            }            

            //mailbot name
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_SYSTEMMAILBOT_FROM_NAME))
            {
                $this->getModel()->set(TSysSettings::FIELD_VALUE, $this->objEdtMailbotFromName->getValueSubmitted());
            }                   

            
        }

        //$this->getModel()->saveToDBAll(); --> dit werkt maar dat hoort bij handleSubmitted() afgehandeld te worden
        
        //==== Settings from config file

        //theme
        if (file_exists(APP_PATH_CMS_VIEWS_THEMES.DIRECTORY_SEPARATOR.$this->objSelTheme->getValueSubmitted()))//double check directory exists, because when it goes wrong the whole UI is gone!
            $this->objConfigFile->set('APP_ADMIN_THEME', $this->objSelTheme->getValueSubmitted());
        else
        {
            sendMessageError(transm($this->getModule(), 'errormessage_themes_dirnotfound', 'Themes directory "[directory]" not found. Setting NOT saved!', 'directory', $this->objSelTheme->getValueSubmitted()));
            logError(__FILE__.__LINE__, 'CMS theme directory "'.$this->objSelTheme->getValueSubmitted().'" does not exist. setting not saved');
        }

        //show websites in navigation
        $this->objConfigFile->set('APP_ADMIN_SHOWWEBSITESINNAVIGATION', $this->objChkWebsitesInNavigation->getValueSubmittedAsBool());

        //show save notification
        $this->objConfigFile->set('APP_ADMIN_SAVESUCCESSNOTIFICATION', $this->objChkSaveSuccessNotification->getValueSubmittedAsBool());

        //anyone can register
        $this->objConfigFile->set('APP_ADMIN_ANYONECANREGISTERACCOUNT', $this->objChkAnyoneCanRegister->getValueSubmittedAsBool());

        //login only whitelisted ips
        $this->objConfigFile->set('APP_ADMIN_LOGINONLYWHITELISTEDIPS', $this->objChkLoginOnlyWhiteListedIPs->getValueSubmittedAsBool());

        //stealth mode whitelisted ips
        $this->objConfigFile->set('APP_ADMIN_STEALTHMODEBLACKWHITELISTEDIPS', $this->objChkStealthMode->getValueSubmittedAsBool());

        //image resize
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_MAX_WIDTHPX', $this->objEdtImgWidthMax->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_MAX_HEIGHTPX', $this->objEdtImgHeightMax->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_LARGE_WIDTHPX', $this->objEdtImgWidthLarge->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_LARGE_HEIGHTPX', $this->objEdtImgHeightLarge->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_MEDIUM_WIDTHPX', $this->objEdtImgWidthMedium->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_MEDIUM_HEIGHTPX', $this->objEdtImgHeightMedium->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_THUMBNAIL_WIDTHPX', $this->objEdtImgWidthThumbnail->getValueSubmittedAsInt());
        $this->objConfigFile->set('APP_ADMIN_IMAGE_RESIZE_THUMBNAIL_HEIGHTPX', $this->objEdtImgHeightThumbnail->getValueSubmittedAsInt());
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  

        //==== Settings from Database: we have multiple records to deal with
        $this->getModel()->resetRecordPointer();
        while($this->getModel()->next())
        {
            //pick out the record we want to set

            //email sys admin
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_SYSTEM, SETTINGS_SYSTEM_EMAILSYSADMIN))
            {
                $this->objEdtEmailSysAdmin->setValue($this->getModel()->get(TSysSettings::FIELD_VALUE));
            }

            //default usergroup id
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_NEWUSER_ROLEID))
            {    

                $objGroups = new TSysCMSUsersRoles();
                $objGroups->sort(TSysCMSUsersRoles::FIELD_ROLENAME);
                $objGroups->loadFromDB();
                $objGroups->generateHTMLSelect($this->getModel()->get(TSysSettings::FIELD_VALUE), $this->objOptNewUserRegisterGroupID);
            }            

            //password expires days
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS))
            {
                $this->objEdtUserPasswordExpiresDays->setValue($this->getModel()->get(TSysSettings::FIELD_VALUE));
            }

            //records per page
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE))
            {
                $this->objEdtPaginatorMaxResult->setValue($this->getModel()->get(TSysSettings::FIELD_VALUE));
            }

            //mailbot email address
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_SYSTEMMAILBOT_FROM_EMAILADDRESS))
            {
                $this->objEdtMailbotFromEmailaddress->setValue($this->getModel()->get(TSysSettings::FIELD_VALUE));
            }            

            //mailbot name
            if ($this->getModel()->getResource() == getSettingsResourceString(SETTINGS_MODULE_CMS, SETTINGS_CMS_SYSTEMMAILBOT_FROM_NAME))
            {
                $this->objEdtMailbotFromName->setValue($this->getModel()->get(TSysSettings::FIELD_VALUE));
            }               

        }

        //==== Settings from config file

        //theme
        $arrThemeDirs = getFileFolderArray(APP_PATH_CMS_VIEWS_THEMES, true, false);
        $this->objSelTheme->clear();
        foreach($arrThemeDirs as $sDir)
        {
            $this->objSelTheme->addOption($sDir, $sDir, ($sDir == APP_ADMIN_THEME));
        }

        //anyone can register
        $this->objChkAnyoneCanRegister->setChecked(APP_ADMIN_ANYONECANREGISTERACCOUNT);

        //websites in navigation
        $this->objChkWebsitesInNavigation->setChecked(APP_ADMIN_SHOWWEBSITESINNAVIGATION);

        //websites in navigation
        $this->objChkSaveSuccessNotification->setChecked(APP_ADMIN_SAVESUCCESSNOTIFICATION);

        //login only allowed for whitelisted ip addresses
        $this->objChkLoginOnlyWhiteListedIPs->setChecked(APP_ADMIN_LOGINONLYWHITELISTEDIPS);

        //stealth mode
        $this->objChkStealthMode->setChecked(APP_ADMIN_STEALTHMODEBLACKWHITELISTEDIPS);

        //image sizes
        $this->objEdtImgWidthMax->setValue(APP_ADMIN_IMAGE_RESIZE_MAX_WIDTHPX);
        $this->objEdtImgHeightMax->setValue(APP_ADMIN_IMAGE_RESIZE_MAX_HEIGHTPX);
        $this->objEdtImgWidthLarge->setValue(APP_ADMIN_IMAGE_RESIZE_LARGE_WIDTHPX);
        $this->objEdtImgHeightLarge->setValue(APP_ADMIN_IMAGE_RESIZE_LARGE_HEIGHTPX);
        $this->objEdtImgWidthMedium->setValue(APP_ADMIN_IMAGE_RESIZE_MEDIUM_WIDTHPX);
        $this->objEdtImgHeightMedium->setValue(APP_ADMIN_IMAGE_RESIZE_MEDIUM_HEIGHTPX);
        $this->objEdtImgWidthThumbnail->setValue(APP_ADMIN_IMAGE_RESIZE_THUMBNAIL_WIDTHPX);
        $this->objEdtImgHeightThumbnail->setValue(APP_ADMIN_IMAGE_RESIZE_THUMBNAIL_HEIGHTPX);
    }
    
   /**
     * is called when a record is loaded
     */
    public function onLoad()
    {
        $this->objConfigFile->loadFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);
    }
    
    /**
     * is called when a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * @return boolean it will NOT SAVE
     */
    public function onSavePre()
    {
        return $this->objConfigFile->saveFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);
    }
    
    /**
     * is called AFTER a record is saved
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return boolean returns true on success otherwise false
     */
    public function onSavePost($bWasSaveSuccesful)
    {
        //refresh the settings in the session array
        settingsReload();

        return true;
    }



    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() {}  


    /**
     * sometimes you don;t want to user the checkin checkout system, even though the model supports it
     * for example: the settings.
     * The user needs to be able to navigate through the tabsheets, without locking records
     * 
     * ATTENTION: if this method returns true and the model doesn't support it: the checkinout will NOT happen!
     * 
     * @return bool return true if you want to use the check-in/checkout-system
     */
    public function getUseCheckinout()
    {
        return false;
    }    


   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysSettings(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsave.php';
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

    /**
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        return getURLCMSDashboard();
    }

    /**
     * return page title
     * This title is different for creating a new record and editing one.
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "create a new user" or "edit user John" (based on if $objModel->getNew())
     *
     * @return string
     */
    public function getTitle()
    {
        //global APP_ADMIN_CURRENTMODULE;

        return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_settingsglobal_edit', 'Settings global');           
    }

    /**
     * show tabsheets on top of the page?
     *
     * @return bool
     */
    public function showTabs()
    {
        return true;
    }    

    /**
     * We forcefully fake that it is an existing record.
     * We have no id paramete, because technically we have MULTIPLE records instead of just one 
     * like normally is the case in a CRUD controller
     * 
     */
    public function isNewRecord()
    {
        return false;
    }    

}
