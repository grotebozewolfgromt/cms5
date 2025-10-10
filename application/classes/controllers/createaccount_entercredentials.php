<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;
use dr\classes\controllers\TCMSAuthenticationSystem;
use dr\classes\dom\tag\form\Select;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\modules\Mod_Sys_Contacts\models\TSysContactsLastNamePrefixes;
use dr\modules\Mod_Sys_Contacts\models\TSysContactsSalutations;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms_url.php');

/**
 * Description of createaccount_entercredentials
 *
 * the page that is called when a user clicks on the link in an email to create an account
 * 
 * @author dennis renirie
 * 19 apr 2024: createaccount_entercredentials created
 *
 */

class createaccount_entercredentials extends TCreateAccountEnterCredentialsControllerAbstract
{
    private $objSelLanguage = null;//dr\classes\dom\tag\form\Select
    
    /**
     * constructor
     */
    public function __construct()    
    {
        $this->objAuthenticationSystem = new TCMSAuthenticationSystem(); //CMS specific authentication system


        parent::__construct();//first init variables before calling parent, which calls the execution of the form
    }

    /**
     * This function adds EARLY BINDING variables to template, which are cached (if cache enabled)
     * (see description on top of this class for more info)
     * 
     * this is the plain-old-fashioned way of doing php with regular php variables etc.
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
        $sTitle = transcms('createaccount_entercredentials_title', 'Create a new account');
        $sHTMLTitle = transcms('createaccount_entercredentials_htmltitle', 'Create a new account');
        $sHTMLMetaDescription = transcms('createaccount_entercredentials_htmlmetadescription', 'Create a new account');
    
        $objAuthenticationSystem = $this->objAuthenticationSystem;
        $sURLBackToLogin = $this->objAuthenticationSystem->getURLLoginForm();

        return array_merge(get_defined_vars(), parent::bindVarsEarly());
    }



    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_createaccount_entercredentials.php';
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
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withoutmenu.php';
    }
  

   /**
     * the path of the email skin
     *
     * @return string
     */
    public function getPathEmailSkin()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_email.php';
    }
    
    /**
     * get path of the email temlate to acticate account
     *
     * @return string
     */
    public function getPathEmailTemplateCreateAccountActivate()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_email_createaccount_clicktoconfirm.php';
    }

    /**
     * get path of the email emailaddress exists template
     *
     * @return string
     */
    public function getPathEmailTemplateCreateAccountEmailExists()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_email_createaccount_emailexists.php';
    }    


    /**
     * generate the form object and all the UI elements to create an account specific to this child class
     * (=username, email address, password + button are already created)
     * 
     */
    public function populateFormCreateAccountEnterCredentialsChild()
    {
            //language
        $this->objSelLanguage = new Select();
        $this->objSelLanguage->setNameAndID('selLanguage');
        $this->objSelLanguage->setClass('halfwidthtag');    
        $this->objFormCreateAccount->add($this->objSelLanguage, '', transg('createaccount_field_language', 'language'));
    }  
    
    /**
     * transfer database elements to form
     * 
     * (the namegiving is because of consistency with other controllers, there is not a lot of 'model' in 'modelToView')
     */                 
    protected function modelToViewCreateAccountEnterCredentialsChild()
    {  
        //language
        $objLangs = new \dr\modules\Mod_Sys_Localisation\models\TSysLanguages();
        $objLangs->sort(\dr\modules\Mod_Sys_Localisation\models\TSysLanguages::FIELD_LANGUAGE);
        $objLangs->loadFromDBByCMSLanguage();
        $objLangs->generateHTMLSelect($this->objSelLanguage->getValueSubmitted(), $this->objSelLanguage);
        
    }    


    /**
     * specific handling of the child class inside handleCreateAccountEnterCredentials();
     *
     * @param TSysUsersAbstract $objUsersNew
     * @return void
     */
    public function handleCreateAccountEnterCredentialsChild($objUsersNew)
    {
        $objUsersNew->setLanguageID($this->objSelLanguage->getValueSubmittedAsInt());
        $objUsersNew->setUserRoleID(getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_NEWUSER_ROLEID)); //11-10-2023: changed getUserRoleID to setUserRoleID
        $objUsersNew->setUsernamePublic($objUsersNew->getUsername());

          

        //create new contact (needed for user-account)
        $objContact = new TSysContacts();
        $objContact->createContactDefaultsDB();
        $objContact->setIsClient(true);
        $objContact->setCustomIdentifier($objUsersNew->getUsername());
        $objContact->setEmailAddressDecrypted($this->objEdtEmailAddress->getValueSubmitted());
        $objContact->setBillingEmailAddressDecrypted($this->objEdtEmailAddress->getValueSubmitted());
        $objContact->saveToDB(true, false);

        //create user-account (needed for user)
        $objAccount = new TSysCMSOrganizations();
        $objAccount->setCustomIdentifier($objUsersNew->getUsername());
        $objAccount->setLoginEnabled(true);
        $objAccount->setContactID($objContact->getID());
        $objAccount->saveToDB(true, false);
        
        $objUsersNew->setCMSOrganisationsID($objAccount->getID());

        unset($objContact);            
        unset($objAccount);            
        unset($objCountries);            
    }     

}
