<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;
use dr\classes\controllers\TCMSAuthenticationSystem;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms_url.php');

/**
 * Description of loginform for CMS
 *
 * This is the form shown to the user that prompts the user for login credentials: username and password
 * 
 * @author dennis renirie
 * 19 apr 2024: loginform created
 *
 */

class loginform extends TLoginFormControllerAbstract
{
    private $objPermCountries = null;//TSysCMSPermissionsCountries();

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
        $sTitle = APP_APPLICATIONNAME;
        $sHTMLTitle = transcms('index_htmltitle', '[applicationname] login','applicationname', APP_APPLICATIONNAME);
        $sHTMLMetaDescription = transcms('index_htmlmetadescription', 'Protected sitemanager environment');
    
        $objAuthenticationSystem = $this->objAuthenticationSystem;
        $objPermCountries = $this->objPermCountries;

        return array_merge(get_defined_vars(), parent::bindVarsEarly());
    }

    /**
     * populate a screen:
     * - populate forms with fields
     * - query database
     * please override in child! 
     * 
     * In this abstract class contains a hollow function that serves purely as a structural template, 
     * so no need to call parent::populate();
     */
    public function populate()
    {
        $this->objPermCountries = new TSysCMSPermissionsCountries();
        $objTempCountries = new TSysCountries();  
        $this->objPermCountries->select(array(TSysCountries::FIELD_ID));
        $this->objPermCountries->select(array(TSysCountries::FIELD_COUNTRYNAME), $objTempCountries);               
        $this->objPermCountries->loadFromDB(true);

        parent::populate();
    }


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_loginform.php';
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
     * returns new TSysBlackWhitelistAbstract
     * 
     * @return TSysBlackWhitelistAbstract
     */
    public function getNewIPBlackWhitelistModel()
    {
        return new TSysCMSLoginIPBlackWhitelist();
    }

    /**
     * in stealth mode: all blacklisted and non-whitelisted IP addresses are served 404 errors
     *  instead of 401 errors. 
     * This prevents automated directory enumeration
     * 
     * @return bool
     */
    public function getStealthModeBlackWhitelistedIPs()
    {
        return APP_ADMIN_STEALTHMODEBLACKWHITELISTEDIPS;
    }    

}
