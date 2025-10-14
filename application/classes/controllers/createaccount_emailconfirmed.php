<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;
use dr\classes\controllers\TCMSAuthenticationSystem;
use dr\classes\dom\tag\form\Select;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms_url.php');

/**
 * Description of createaccount_emailconfirmed 
 *
 * This is the form shown to the user that prompts the user for login credentials: username and password
 * 
 * @author dennis renirie
 * 19 apr 2024: createaccount_emailconfirmed created
 *
 */

class createaccount_emailconfirmed extends TCreateAccountEmailConfirmedControllerAbstract
{    
    
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
        $sTitle = transcms('createaccount_emailconfirmation_title', 'Account activation');
        $sHTMLTitle = transcms('createaccount_emailconfirmation_htmltitle', 'Account activation');
        $sHTMLMetaDescription = transcms('createaccount_emailconfirmation_htmlmetadescription', 'Account activation');
        
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
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_createaccount_emailconfirmed.php';
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
  
}
