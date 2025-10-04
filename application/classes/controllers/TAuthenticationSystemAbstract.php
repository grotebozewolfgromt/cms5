<?php


namespace dr\classes\controllers;

use dr\classes\models\TSysUsersAbstract;
use dr\classes\models\TSysUsersFloodDetectAbstract;
use dr\classes\models\TSysUsersSessionsAbstract;
use dr\classes\models\TSysModel;

use dr\classes\dom\FormGenerator;    
use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputEmail;
use dr\classes\dom\tag\form\InputHidden;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\Characterwhitelist;
use dr\classes\dom\validator\Emailaddress;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\validator\Required;
use dr\classes\dom\validator\StrongPassword;
use dr\classes\dom\validator\Repeatfieldvalue;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDateTime;
use dr\classes\mail\TMailSend;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');



/**
 * Description of TAuthenticationSystemAbstract
 * login actions to do with authentication of a user in a system like CMS, webshop etc.
 * 
 * this class is accompanied with TSysUsersAbstract, TUserSessionsAbstract and TSysUsersFloodDetectAbstract class.
 * if it fails, the user is sent back to the script where the login happened,
 * if it is successful the user is send to a url you can define in the constructor
 * 
 * the goal of this class is:
 * - to keep it lightweight due to OOP performance issues in PHP, so no parent class (and if: it needs to be as flat as possible when it comes to parent classes depth)
 * - by inheriting this class you have ALL login features you might possibly need (flood detection, loginsessions, password recovery, creating account etc...) with MINIMAL EFFORT
 * 
 * to get messages from the controller use: 
 * ->getMessageNormal()
 * ->getMessageError()
 * don't use the _GET parameters for messages because not every message (like logout) is sent via _GET
 * 
 * You can have multiple instances of this class running on 1 website, as long as the generated id via getControllerID() is different.
 * ($_SESSION is used on every instance, but getControllerID() makes it unique so they won't interfere)
 * 
 * 
 * 
 ************************************
 * LOGIN TOKEN
 ************************************
 * Logintokens are tokens stored in session or cookie that authenticate a user.
 * A logintoken is basically a temporary password for the session.
 * By using a logintoken you don't have to store a password in a session or cookie (which is much safer) 
 * 
 ************************************
 * EMAIL TOKEN
 ************************************
 * Email tokens are tokens embedded in an email to verify a user, on create account and password resets.
 * it is basically a login token in functionality, except that is only works for create account and password resets 
 * Email tokens are stored database as a field in the users table (TUserAbstract)
 * Both password resets and account creation use the same tokenfields in database and token functions (only their purpose is different)
 * 
 ************************************
 * FLOOD DETECTION
 ************************************
 * This class uses flood detection for failed login attempts, successfull login attempts, password change attempts, account create attempts
 * based on ip, browser fingerprint, username (if available) and sessions.
 * 
 * Also flood detection (failed login attempts, successfull login attempts, password change attempts, account create attempts) 
 * is done via sessions (alongside database).
 * In order to bypass this flood detection as attacker, you have generate a new sessionid on every page load (or disable cookies),
 * 
 ***********************************
 * Fingerprints
 ************************************
 * browser fingerprinting is used in 2 areas:
 * -user sessions (includes ip address)
 * -flood control (no ip address)
 * 
 * an ip address included makes a fingerprint more unique, however when it comes to flood control, 
 * you can try to flood a site by constantly changing your ip address.
 * So we don't want an ip address in the flood control.
 * As an extra security bonus, the fingerprints of the usersession dbtable and floodcontrol dbtabel 
 * are different
 * 
 ************************************
 * HOW TO USE THIS CLASS:
 ************************************
 * 
 *************************************
 * IF LOGIN DOESN'T WORK, CHECK THE FOLLOWING:
 *************************************
 * -https: if APP_ISHTTPS is wrong, cookies won't work. if the loginform is http and the rest https, this won't work
 * -if cookies are disabled in the browser
 * -This class won't work: if the user would renew their phpsessionid with every page reload
 * -do you have multiple instances of this controller on 1 website that generate the same id via $this->getControllerID() (every instance must have its own ID, otherwise they will interfere with each other)
 * 
 * 
 * @author drenirie
 * 
 * 16 jan 2020: TLoginControllerAbstract() session id is refreshed on login and logout to prevent cookie stealing
 * 16 jan 2020: TLoginControllerAbstract() flood detection werkt nu
 * 16 jan 2020: TLoginControllerAbstract() failed login attempts worden verwijderd bij succesvol login
 * 17 jan 2020: TLoginControllerAbstract() getUseKeepLoggedIn() toegevoegd en bijgehorende functionaliteit toegevoegd
 * 3 nov 2020: TLoginControllerAbstract(): authenticate() checks first on session, then on cookie. this is much safer
 * 4 nov 2020: TLoginControllerAbstract(): getIsLoggedIn() function added
 * 11 dec 2020: TLoginControllerAbstract(): some renames getForm() -> getFormLogin, objForm -> objFormLogin
 * 11 dec 2020: TLoginControllerAbstract(): added form for password recovery
 * 21,22,23 juni 2021: TLoginControllerAbstract(): a lot has happened. the security increased soooo much
 * 11 sept 2021: TLoginControllerAbstract(): detect flood on emailaddress fingerprint create account
 * 11 sept 2021: TLoginControllerAbstract(): create account with encrypted and fingerprinted emailaddress (including: check duplicate email addressses)
  * 11 sept 2021: TLoginControllerAbstract(): detect flood on emailaddress fingerprint password reset
  * 2 okt 2021: TLoginControllerAbstract(): extra preventTimingAttacks() added when formchecks fail
  * 21 jan 2023: TLoginControllerAbstract(): uses recaptchav3 features of FormGenerator, instead of implementing it ourselves
  * 9 nov 2023: TAuthenticationSystemAbstract(): undeclared successurlchangepassword, declared
  * 9 nov 2023: TAuthenticationSystemAbstract(): undeclared passwordrepeat field, declared
  * 19 apr 2024: TAuthenticationSystemAbstract() renamed to TAuthenticationSystemAbstract
  * 15 nov 2024: TAuthenticationSystemAbstract(): authenticate() renamed naar authenticateUser() om verschil te maken tussen users en ip-adres authenticatie
  * 15 nov 2024: TAuthenticationSystemAbstract(): registerAccountCreateAttempt() registreert nu ook
 * 
 */
abstract class TAuthenticationSystemAbstract
{
    const SESSIONCOOKIEARRAYKEY_LOGINTOKENID                    = 'sT1'; //numeric db-session-id, plaintext in db (it's randomid, not the real record id)
    const SESSIONCOOKIEARRAYKEY_LOGINTOKENENCRYPTED             = 'sT3'; //plaintext in db, plaintext in session/cookie
    const SESSIONARRAYKEY_FLOODDETECT_FAILEDLOGINATTEMPTS       = 'sFlDeFLA'; //stores array of integer timestamps for flood detection on failed login attempts
    const SESSIONARRAYKEY_FLOODDETECT_SUCCESSFULLOGINATTEMPTS   = 'sFlDeSLA'; //stores array of integer timestamps for flood detection on succeeded login attempts
    const SESSIONARRAYKEY_FLOODDETECT_PASSWORDRESETATTEMPTS     = 'sFlDePRA'; //stores array of integer timestamps for flood detection on password reset attempts
    const SESSIONARRAYKEY_FLOODDETECT_CREATEACCOUNTATTEMPTS     = 'sFlDeCAT'; //stores array of integer timestamps for flood detection on create-account attempts
    // const SESSIONARRAYKEY_FORMTOKEN                             = 'sFoTo'; //stores array of formtokens given out in the past
    
    const BOGUSLOGINTOKENSGENERATED = 5;//generate 15 bogus login tokens (including the 3 existing ones (token1, token2, dbsessionrandomid) ); 20 tokens or so is too much it will generate a 502 error 
    const LOGINTOKENPREFIX = 'sT';//prefix for the login token

    const GETARRAYKEYLOGOUT = 'logout';//if $_GET[GETARRAYKEYLOGOUT] ==  GETARRAYKEYLOGOUTVALUE then log out and render all authentications useless
    const GETARRAYKEYLOGOUTVALUE = '1';
    const GETARRAYKEYCONTROLLERID = 'cuid';//if we need to supply the Controller id for this class, such as logout
    const GETARRAYKEYMESSAGENORMAL = 'mn'; //if we need to use messages via url
    const GETARRAYKEYMESSAGEERROR = 'me'; //if we need to use error messages via url
    const GETARRAYKEY_USER_RANDOMID = 'id1'; //user randomid, used for password recovery and account creation
    const GETARRAYKEY_USER_EMAILTOKEN = 'id2'; //user emailtoken, used for password recovery and account creation
    const GETARRAYKEY_USER_USERNAME = 'id3'; //hashed username, used for password recovery and account creation
        
    const FLOODDETECT_COUNTUNIT_PERDAY = 1;//1=each day, 14=every 2 weeks, 30=each month
    const FLOODDETECT_COUNTUNIT_PERMONTH = 30;//1=each day, 14=every 2 weeks, 30=each month

    // const RECAPTCHA_SITEVERIFYURL = 'https://www.google.com/recaptcha/api/siteverify';

    const CHARSALLOWED_FORMFIELDS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789:;_+=.,!@#$%^&*(){}?'; //all fields: username, password, email etc. First line of defense agains attackers -- these need to be consistent with the StrongPassword class, otherwise they could mutually exclude each other


    //AK=Array Key
    private $sAKSCATokenID = '';// the same as SESSIONCOOKIEARRAYKEY_LOGINTOKENID but with controller-id added    
    private $sAKSCAToken = '';// the same as SESSIONCOOKIEARRAYKEY_LOGINTOKEN2ENCRYPTED but with controller-id added
    private $sAKFloodDetectionFailedLoginAttempts = '';// the same as SESSIONARRAYKEY_FLOODDETECT_FAILEDLOGINATTEMPTS but with controller-id added
    private $sAKFloodDetectionSuccessfulLoginAttempts = '';// the same as SESSIONARRAYKEY_FLOODDETECT_SUCCESSFULLOGINATTEMPTS but with controller-id added
    private $sAKFloodDetectionPasswordResetAttempts = '';// the same as SESSIONARRAYKEY_FLOODDETECT_PASSWORDRESETATTEMPTS but with controller-id added
    private $sAKFloodDetectionCreateAccountAttempts = '';// the same as SESSIONARRAYKEY_FLOODDETECT_CREATEACCOUNTATTEMPTS but with controller-id added
    // private $sAKFormToken = '';// the same as SESSIONARRAYKEY_FLOODDETECT_CREATEACCOUNTATTEMPTS but with controller-id added
    
    private $sControllerID = null;  
    private $objUsers = null;  
    private $objUsersFloodDetectModel = null;  
    private $objUsersSessions = null;  
    private $objUsersLoginHistory = null;  
    private $objLanguage = null;//language object of the user
    private $sURLLoginForm = '';
    private $sURLLoginSuccess = '';    
    private $sURLLoginSuccessUserChangePassword = '';    
    private $sURLPasswordRecoverEnterEmail = '';    
    private $sURLPasswordRecoverEnterPassword = '';    
    private $sMailbotFromEmailAddress = 'noreply@example.com';
    private $sMailbotFromName = 'System';
    private $sMessageNormal = '';//messages like: "logged out successfully"
    private $sMessageError = '';//messages like: "login failed"
    private $bUseKeepLoggedIn = false;//use the keep-me-logged-in system
    
    // private $objFormLogin = null;//dr\classes\dom\FormGenerator
    // private $objFormPassworRecover = null;//dr\classes\dom\FormGenerator
    // protected $objFormCreateAccount = null;//dr\classes\dom\FormGenerator --> protected because we can have some dedicated stuff with extra fields like language in the child class

    protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    protected $objEdtUsername = null;//dr\classes\dom\tag\form\InputText
    protected $objEdtPassword = null;//dr\classes\dom\tag\form\InputPassword
    protected $objEdtPasswordRepeat = null;//dr\classes\dom\tag\form\InputPassword
    protected $objEdtEmailAddress = null;//dr\classes\dom\tag\form\InputText
    // protected $objChkKeepMeLoggedIn = null;//dr\classes\dom\tag\form\InputCheck
    protected $objHdEmailToken = null;//dr\classes\dom\tag\form\InputHidden --> hidden field for password recover
    protected $objHdUserRandomID = null;//dr\classes\dom\tag\form\InputHidden --> hidden field for password recover
    protected $objHdUsernameHashed = null;//dr\classes\dom\tag\form\InputHidden --> hidden field for password recover
    // protected $objHDFormToken = null;//dr\classes\dom\tag\form\InputHidden --> hidden field for a form token. this token is given out by this class (stored in session) and needs to be present on submitting a form. This way we know for sure that THIS form is used and NOT one from an external site

    private $bIsLoggedIn = false; //registers if the controller is logged in or not. this DOES NOT include the user permissions!!!!!!
    // private $bShowPasswordRecoverLink = true; //for the template: determines if a link is shown for password recovery. We wan't to show the link when too many failed login attempts, but not show when too many overall attempts
    // private $bShowCreateAccountLink = true; //for the template: determines if a link is shown for creating an account. We wan't to show the link when too many failed login attempts OR when it's disabled, but not show when too many overall attempts

    // private $objGoogleClient = null;//Google_Client: for sign-in-with-google


    /**
     * 
     * this login system needs to be unique, otherwise the CMS and the Webshop for example
     * use the same variables for users, which will conflict with each other
     * name the login for the cms 'cms' and webshop 'webshop' for example, 
     * but if you have 2 webshops use 2 different ids
     * 
     */
    public function __construct()
    {   
        $this->bIsLoggedIn = false;

        $this->sURLLoginForm = $this->getURLLoginForm();
        $this->sURLLoginSuccess = $this->getURLLoginSuccess();
        $this->sURLLoginSuccessUserChangePassword = $this->getURLLoginSuccessUserChangePassword();
        $this->sURLPasswordRecoverEnterEmail = $this->getURLPasswordRecoverEnterEmail();
        $this->sURLPasswordRecoverEnterPassword = $this->getURLPasswordRecoverEnterPassword();

        $this->sMailbotFromEmailAddress = $this->getMailbotFromEmailAddress();
        $this->sMailbotFromName = $this->getMailbotFromName();

        $this->objUsers = $this->getNewUsers();
        $this->objUsersFloodDetectModel = $this->getNewUsersFloodDetectModel();
        $this->objUsersSessions = $this->getNewUsersSessions();
        $this->objUsersLoginHistory = $this->getNewUsersLoginHistory();
        $this->objLanguage = $this->getNewLanguage();
        $this->sControllerID = $this->getControllerID();
        
        $this->sAKSCATokenID = $this->getTokenIDSessionCookieKey();
        $this->sAKSCAToken = $this->getTokenSessionCookieKey();
        $this->sAKFloodDetectionFailedLoginAttempts = $this->getFloodDetectionFailedLoginAttemptsKey();
        $this->sAKFloodDetectionSuccessfulLoginAttempts = $this->getFloodDetectionSuccessfulLoginAttemptsKey();
        $this->sAKFloodDetectionPasswordResetAttempts = $this->getFloodDetectionPasswordResetAttemptsKey();
        $this->sAKFloodDetectionCreateAccountAttempts = $this->getFloodDetectionCreateAccountAttemptsKey();
        // $this->sAKFormToken = $this->getFormTokenKey();
        
        $this->bUseKeepLoggedIn = $this->getUseKeepLoggedIn();
                
        if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEYMESSAGENORMAL]))
            $this->sMessageNormal = $_GET[TAuthenticationSystemAbstract::GETARRAYKEYMESSAGENORMAL];
        if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEYMESSAGEERROR]))
            $this->sMessageError = $_GET[TAuthenticationSystemAbstract::GETARRAYKEYMESSAGEERROR];     
    }
    
    public function getIsLoggedIn()
    {
        return $this->bIsLoggedIn;
    }

    public function setIsLoggedIn($bLoggedIn)
    {
        $this->bIsLoggedIn = $bLoggedIn;
    }


    public function getTokenIDSessionCookieKey()
    {
        return $this->sControllerID.TAuthenticationSystemAbstract::SESSIONCOOKIEARRAYKEY_LOGINTOKENID;
    }
    
    public function getTokenSessionCookieKey()
    {
        return $this->sControllerID.TAuthenticationSystemAbstract::SESSIONCOOKIEARRAYKEY_LOGINTOKENENCRYPTED;
    }

    public function getFloodDetectionFailedLoginAttemptsKey()
    {
        return $this->sControllerID.TAuthenticationSystemAbstract::SESSIONARRAYKEY_FLOODDETECT_FAILEDLOGINATTEMPTS;
    }
    
    public function getFloodDetectionSuccessfulLoginAttemptsKey()
    {
        return $this->sControllerID.TAuthenticationSystemAbstract::SESSIONARRAYKEY_FLOODDETECT_SUCCESSFULLOGINATTEMPTS;
    }    
    
    public function getFloodDetectionPasswordResetAttemptsKey()
    {
        return $this->sControllerID.TAuthenticationSystemAbstract::SESSIONARRAYKEY_FLOODDETECT_PASSWORDRESETATTEMPTS;
    }
    
    public function getFloodDetectionCreateAccountAttemptsKey()
    {
        return $this->sControllerID.TAuthenticationSystemAbstract::SESSIONARRAYKEY_FLOODDETECT_CREATEACCOUNTATTEMPTS;
    }    

    
    /**
     * determines wether to use a cookie or session
     * 
     * @todo this doesn't work ,because we dont have the form to our disposal
     * @return boolean true = cookie, false = session
     */
    public function getUseCookie()
    {
        //the _COOKIE array is only set on the next page load, so we can determin to use a cookie by the checkbox
        if ($this->objChkKeepMeLoggedIn)
            return $this->objChkKeepMeLoggedIn->getValueSubmittedAsBool();
        
        //if we have no objChkKeepMeLoggedIn object then look at the cookie
        return isset($_COOKIE[$this->getTokenIDSessionCookieKey()]);
    }
    
    /**
     * retun users db object
     * 
     * @return TSysUsersAbstract
     */
    public function getUsers()
    {
        return $this->objUsers;
    }
    
    /**
     * return loginattempts db object
     * @return TSysUsersFloodDetectAbstract
     */
    public function getUsersFloodDetectModel()
    {
        return $this->objUsersFloodDetectModel;
    }
    
    /**
     * return usersessions db object
     * @return TSysUsersSessionsAbstract
     */
    public function getUserSessions()
    {
        return $this->objUsersSessions;
    }
    
    public function getNewLanguage()
    {
        return new \dr\modules\Mod_Sys_Localisation\models\TSysLanguages();
    }
    
    public function getLanguages()
    {
        return $this->objLanguage;
    }

    /**
     * set message that is not an error
     *
     * @param string $sMessage
     * @return void
     */
    public function setMessageNormal($sMessage)
    {
        $this->sMessageNormal = $sMessage;
    }

    
    /**
     * return message that are not errormessages like "log out successful"
     * 
     * @return string
     */
    public function getMessageNormal()
    {
        return $this->sMessageNormal;
    }
    
    /**
     * set error message
     *
     * @param string $sMessage
     * @return void
     */
    public function setMessageError($sMessage)
    {
        $this->sMessageError = $sMessage;
    }


    /**
     * return translated error messages like: "login failed"
     * 
     * @return string
     */
    public function getMessageError()
    {
        return $this->sMessageError;
    }

     
    /**
     * handles user authentication on every page
     * 
     * the function authenticateUser() does only a checks but does no referals to 
     * other pages, this function does
     */
    public function handleAuthentication()
    {
        ////global CMS_CURRENTMODULE;
        $bResult = false;
        // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'start'); 
        
        if (!$this->authenticateIPAddress())
        {        
            logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'ip address: '.getIPAddressClient().' REFUSED based on blacklist/whitelist rules');    

            if ($this->getStealthModeBlackWhitelistedIPs())
                show404();
            else
                showAccessDenied();

            die(); //we die() here because a redirection can cause endless redirection if username and password are correct
        }


        $bResult = $this->authenticateUser(); //auth this object
        
        // preventTimingAttack(50, 200);

        if (!$bResult) //when authentication failed, get out of here
        {
            $sUserName = '?';
            if ($this->objUsers)
                $sUserName = $this->objUsers->getUsername();
            logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'user: '.$sUserName.' --> Login session expired on page: '.getURLThisScript().' . $this->authenticateUser() returned false for user agent '.$_SERVER['HTTP_USER_AGENT'].'. Go to Location: '.$this->sURLLoginForm);

            // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'authentication failed, goto Location: '.$this->sURLLoginForm);
            // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'header redirect 1');
            header('HTTP/1.0 401 Unauthorized');
            header('Location: '.addVariableToURL($this->sURLLoginForm, TAuthenticationSystemAbstract::GETARRAYKEYMESSAGEERROR, transg('authenticationsystem_sessionexpired', 'Login session expired')));            
            die();
        }        
        
        //handle additional authentication by child object
        if ($bResult)
        {            
            $bResult = $this->handleAuthenticationChild();

            if (!$bResult)
            {
                $sUserName = '?';
                if ($this->objUsers)
                    $sUserName = $this->objUsers->getUsername();
    
                logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'user: '.$sUserName.' --> $this->handleAuthenticationChild() returned false');
            }
        }

        
        return $bResult;
    }

    /**
     * authenticate IP address with login blackwhitelist object
     * 
     * @return boolean
     */
    public function authenticateIPAddress()
    {
        $objBlacklist = $this->getNewIPBlackWhitelistModel();
        return $objBlacklist->authenticateIPAddressDB(getIPAddressClient());
    }


    /**
     * authenticate user: 
     * see if user is already logged in by looking at
     * cookie and session
     * 
     * return true if authenticated, false if not authenticated
     * 
     * you need to call handleAuthentication() on every page to handle the 
     * authentication and send the user away if failed, this function does
     * ONLY a check
     * 
     * in authenticateUser() session is loaded from database
     * 
     * @return boolean
     */
    public function authenticateUser()
    {
        $bAuth = false;
        // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'start');


        /* reversed checking: 3 nov 2020. 
        //otherwise it is always possible to login with a cookie. the session is much safer to check first
        //because it is managed on the server, cookies managed on the client-side (where most likely the security-thread is)
        */
        
        //====new method 3 nov 2020: first try session, then cookies
        if (isset($_SESSION[$this->sAKSCATokenID]))
        {
            if (isset($_SESSION[$this->sAKSCAToken]))
            {
                // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'try session');

                $bAuth = $this->objUsersSessions->loadFromDBByTokensLoginAllowed(
                        $this->objUsers,
                        $this->objLanguage,
                        $_SESSION[$this->sAKSCATokenID],
                        getFingerprintBrowser(),
                        $_SESSION[$this->sAKSCAToken]); //-->loads only sessions, not users
                
                if (!$bAuth)
                    logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'session auth failed');                        
            }  
            else
            {
                logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'isset($_SESSION[$this->sAKSCAToken2]) == false: '.$this->sAKSCAToken);
            }                                                
           
        }
        else
        {
            logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'isset($_SESSION[$this->sAKSCATokenID]) == false: '. $this->sAKSCATokenID);
        }                     
        
        //if session authentication failed, try cookie 
        if (!$bAuth)//session failed?
        {
            if ($this->bUseKeepLoggedIn) //are we allowed to use the cookie at all?
            {
                if (isset($_COOKIE[$this->sAKSCATokenID]))
                {
                    if (isset($_COOKIE[$this->sAKSCAToken]))
                    {
                        logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'try cookies, '.$_COOKIE[$this->sAKSCAToken]);
                        $bAuth = $this->objUsersSessions->loadFromDBByTokensLoginAllowed(
                                $this->objUsers,
                                $this->objLanguage,
                                $_COOKIE[$this->sAKSCATokenID], 
                                getFingerprintBrowser(),
                                $_COOKIE[$this->sAKSCAToken]); //-->loads only sessions, not users
                    }  

                    if (!$bAuth)
                        logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'cookie auth failed');                        

                }
                else
                {
                    logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'isset($_COOKIE[$this->sAKSCATokenID]) == false');
                }


                //session could be timed out when logged-in with cookie, then we need to reload the permissions from database
                $this->populatePermissionsSessionArray(false);    
            }       
        }
        else
        {
            // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'$bAuth = true (session)');
        }        

        //need to update permissions NOW??
        if ($bAuth)
        {
            if ($this->objUsers->count() > 0)
            {  
                if ($this->objUsers->getUpdatePermissions())
                {
                    $this->populatePermissionsSessionArray();
    
                    //write to db that we updated the permissions
                    if ($this->objUsers->getUpdatePermissions())
                    {
                        $objTempUser = $this->objUsers->getCopy();
                        $objTempUser->findID($this->objUsers->getID()) ;
                        $objTempUser->loadFromDB();
                        $objTempUser->setUpdatePermissions(false);
                        $objTempUser->saveToDB();
                    }   
    
                }          

                //we log every page a user follows in the access logs. If we ever need to figure out the steps of the user, we can do that this way
                //we log this here, because we have the data about the validated user AND the page information
                logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'Loaded page: "'.getURLThisScript().'"', $this->objUsers->getUserName());
            }            
        }
        
        
        $this->setIsLoggedIn($bAuth);

             

        return $bAuth; 
    }
    
    /**
     * register session or Cookie when login succesful
     * 
     * @param bool $bChkKeepMeLoggedIn did the user check the checkbox keep-me-logged-in?
     */
    protected function registerSessionOrCookie($bChkKeepMeLoggedIn = false)
    {
        session_regenerate_id(true); //make a new session id (and keep the current session) to prevent someone stealing your session id and delete the old session file on server
        
        $sEncryptedToken2 = '';
        $sEncryptedToken2 = password_hash($this->objUsersSessions->getLoginToken2WithHashSeed(), PASSWORD_DEFAULT);
        
        $bUseCookie = false;        
        if ($this->bUseKeepLoggedIn) //keep-logged-in is enabled, then we may have a chance to use the cookie, otherwise it's always session
        {
            $bUseCookie = $bChkKeepMeLoggedIn;
        }
        
        // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'use cookie')        ;
        //use cookie
        if ($bUseCookie)
        {            
            if (!setcookie($this->sAKSCATokenID, $this->objUsersSessions->getRandomID(), time() + (DAY_IN_SECS * APP_COOKIE_EXPIREDAYS), '/', getDomain(), APP_ISHTTPS, true)) // 86400 = 1 day
                $_SESSION[$this->sAKSCATokenID] = $this->objUsersSessions->getRandomID(); //default to session if failed
            if (!setcookie($this->sAKSCAToken, $sEncryptedToken2, time() + (DAY_IN_SECS * APP_COOKIE_EXPIREDAYS), '/', getDomain(), APP_ISHTTPS, true)) // 86400 = 1 day
                $_SESSION[$this->sAKSCAToken] = $sEncryptedToken2; //default to session if failed
            
            //generate bogus tokens
            $sTokenName = '';
            $sTokenValue = '';
            for ($iCounter = 1; $iCounter <= TAuthenticationSystemAbstract::BOGUSLOGINTOKENSGENERATED; $iCounter++)
            {
                $sTokenName = $this->sControllerID.TAuthenticationSystemAbstract::LOGINTOKENPREFIX.$iCounter;
                
                if (($sTokenName != $this->sAKSCATokenID) && ($sTokenName != $this->sAKSCAToken))
                {
                    $sTokenValue = generatePassword(10,100);
                    if (!setcookie($sTokenName, $sTokenValue, time() + (DAY_IN_SECS * APP_COOKIE_EXPIREDAYS), '/', getDomain(), APP_ISHTTPS, true)) // 86400 = 1 day
                        $_SESSION[$sTokenName] = $sTokenValue; //default to session if failed                    
                }
                
            }
        }
        else //use session
        {
            $_SESSION[$this->sAKSCATokenID] = $this->objUsersSessions->getRandomID();
            $_SESSION[$this->sAKSCAToken] = $sEncryptedToken2;
            
            //generate bogus tokens
            $sTokenName = '';
            $sTokenValue = '';
            for ($iCounter = 1; $iCounter <= TAuthenticationSystemAbstract::BOGUSLOGINTOKENSGENERATED; $iCounter++)
            {
                $sTokenName = $this->sControllerID.TAuthenticationSystemAbstract::LOGINTOKENPREFIX.$iCounter;
                
                if (($sTokenName != $this->sAKSCATokenID) && ($sTokenName != $this->sAKSCAToken))
                {
                    $sTokenValue = generatePassword(10,100);
                    $_SESSION[$sTokenName] = $sTokenValue; 
                }
                
            }            
        }
    }
    
    /**
     * register a FAILED login attempt
     * 
     * @param string $sUsername
     */
    public function registerFailedLoginAttempt($sUsername)
    {        
        //in session:
        if (isset($_SESSION[$this->sAKFloodDetectionFailedLoginAttempts]))  
        {      
            $_SESSION[$this->sAKFloodDetectionFailedLoginAttempts][] = time();
        }
        else
            $_SESSION[$this->sAKFloodDetectionFailedLoginAttempts] = array(time());


        //in database:
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__,': failed login attempt with username: '. $sUsername, $sUsername);
        $this->objUsersFloodDetectModel->clear();
        $this->objUsersFloodDetectModel->newRecord();
        $this->objUsersFloodDetectModel->setUsername($sUsername);
        $this->objUsersFloodDetectModel->setUsernameHashed($sUsername);
        $this->objUsersFloodDetectModel->setIP(getIPAddressClient());
        $this->objUsersFloodDetectModel->setDateAttempt(new TDateTime(time()));        
        $this->objUsersFloodDetectModel->setIsFailedLoginAttempt(true);        
        $this->objUsersFloodDetectModel->setFingerprintBrowser(getFingerprintBrowser(false));        
        $this->objUsersFloodDetectModel->setUserAgent($_SERVER['HTTP_USER_AGENT']);        
        if (!$this->objUsersFloodDetectModel->saveToDB())
            logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'objUsersFloodDetectModel->saveToDB() register failed-login-attempt failed', $sUsername);
    }

    /**
     * register a SUCCESSFULL login attempt
     * 
     * @param string $sUsername
     */
    private function registerSuccessfulLoginAttempt($sUsername)
    {
        //in session:
        if (isset($_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts]))  
        {      
            $_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts][] = time();
        }
        else
            $_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts] = array(time());



        //====in database: register new succesful attempt
        $this->objUsersFloodDetectModel->clear();
        $this->objUsersFloodDetectModel->newRecord();
        $this->objUsersFloodDetectModel->setUsername($sUsername);
        $this->objUsersFloodDetectModel->setUsernameHashed(TSysUsersFloodDetectAbstract::hashUsername($sUsername));
        $this->objUsersFloodDetectModel->setIP(getIPAddressClient());
        $this->objUsersFloodDetectModel->setDateAttempt(new TDateTime(time()));        
        $this->objUsersFloodDetectModel->setIsSucceededLoginAttempt(true);        
        $this->objUsersFloodDetectModel->setFingerprintBrowser(getFingerprintBrowser(false));        
        $this->objUsersFloodDetectModel->setUserAgent($_SERVER['HTTP_USER_AGENT']);        
        if (!$this->objUsersFloodDetectModel->saveToDB())
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'objUsersFloodDetectModel->saveToDB() register succesful-login-attempt failed', $sUsername);
    }    
    
    /**
     * register a password reset
     * 
     */
    public function registerPasswordReset($sEmailAddress)
    {
        //in session:
        if (isset($_SESSION[$this->sAKFloodDetectionPasswordResetAttempts]))  
        {      
            $_SESSION[$this->sAKFloodDetectionPasswordResetAttempts][] = time();
        }
        else
            $_SESSION[$this->sAKFloodDetectionPasswordResetAttempts] = array(time());


        //in database:
        $this->objUsersFloodDetectModel->clear();
        $this->objUsersFloodDetectModel->newRecord();
        if ($this->objEdtEmailAddress) //this can be null when clicked on a link in the email
            $this->objUsersFloodDetectModel->setEmailAddressUncrypted($sEmailAddress);
        $this->objUsersFloodDetectModel->setIP(getIPAddressClient());
        $this->objUsersFloodDetectModel->setDateAttempt(new TDateTime(time()));        
        $this->objUsersFloodDetectModel->setIsPasswordReset(true);        
        $this->objUsersFloodDetectModel->setFingerprintBrowser(getFingerprintBrowser(false));        
        $this->objUsersFloodDetectModel->setUserAgent($_SERVER['HTTP_USER_AGENT']);        
        if (!$this->objUsersFloodDetectModel->saveToDB())
            logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'objUsersFloodDetectModel->saveToDB() register password reset failed');
    }


    /**
     * register an account creation
     * 
     */
    public function registerAccountCreateAttempt($sEmailAddress, $sUsername)
    {
        //in session:
        if (isset($_SESSION[$this->sAKFloodDetectionCreateAccountAttempts]))  
        {      
            $_SESSION[$this->sAKFloodDetectionCreateAccountAttempts][] = time();
        }
        else
            $_SESSION[$this->sAKFloodDetectionCreateAccountAttempts] = array(time());


        //in database: 
        $this->objUsersFloodDetectModel->clear();
        $this->objUsersFloodDetectModel->newRecord();
        $this->objUsersFloodDetectModel->setIP(getIPAddressClient());
        $this->objUsersFloodDetectModel->setUsername($sUsername);
        $this->objUsersFloodDetectModel->setUsernameHashed($sUsername);
        $this->objUsersFloodDetectModel->setDateAttempt(new TDateTime(time()));        
        $this->objUsersFloodDetectModel->setIsCreateAccountAttempt(true);        
        $this->objUsersFloodDetectModel->setFingerprintBrowser(getFingerprintBrowser(false));
        $this->objUsersFloodDetectModel->setUserAgent($_SERVER['HTTP_USER_AGENT']);                
        $this->objUsersFloodDetectModel->setEmailAddressUncrypted($sEmailAddress);

        if (!$this->objUsersFloodDetectModel->saveToDB())
            logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'objUsersFloodDetectModel->saveToDB() register creation attempt failed');
    }

    /**
     * register a new session in database
     */
    protected function registerNewUserSessionInDB()
    {
        //@todo check if session already exists, then only update the field: dateupdated
        $this->objUsersSessions->clear();
        $this->objUsersSessions->setIPAddress(getIPAddressClient());
        $this->objUsersSessions->setUserID($this->objUsers->getID());
        $this->objUsersSessions->generateTokens(false);
        $this->objUsersSessions->setFingerprintBrowser(getFingerprintBrowser());
        $this->objUsersSessions->setUserAgent($_SERVER['HTTP_USER_AGENT']);  
        $this->objUsersSessions->setSessionStarted(new TDateTime(time()));
        $this->objUsersSessions->setSessionUpdated(new TDateTime(time()));
        $this->objUsersSessions->setOperatingSystem(getBrowserOS());
        $this->objUsersSessions->setBrowser(getBrowserName());

        if (!$this->objUsersSessions->saveToDB())
        {
            logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'objUsersSessions->saveToDB() registerNewUserSessionInDBfailed');
            return false;
        }

        return true;
    }

    /**
     * register a new user login history entry in database
     */
    protected function registerNewUserLoginHistoryInDB()
    {
        $this->objUsersLoginHistory->clear();
        $this->objUsersLoginHistory->setIPAddress(getIPAddressClient());
        $this->objUsersLoginHistory->setUserID($this->objUsers->getID());
        $this->objUsersLoginHistory->setUsername($this->objUsers->getUsername());
        $this->objUsersLoginHistory->setFingerprintBrowser(getFingerprintBrowser());
        $this->objUsersLoginHistory->setUserAgent($_SERVER['HTTP_USER_AGENT']);  
        $this->objUsersLoginHistory->setIsLogin(true);  
        $this->objUsersLoginHistory->setNotes('login succesful');  

        if (!$this->objUsersLoginHistory->saveToDB())
        {
            logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'objUsersLoginHistory->saveToDB() registerNewUserLoginHistoryInDB');
            return false;
        }

        return true;
    }


    /**
     * when a login is approved, take all the nessesary steps to ACTUALLY login, 
     * like: creating a login session, setting last-time-logged, register login attempt in etc
     * 
     * @param string $sUsername
     * @param bool $bChkKeepMeLoggedIn did the user check the checkbox keep-me-logged-in?
     */
    public function registerLogin($sUsername, $bChkKeepMeLoggedIn = false)
    {
        //register last login
        $this->objUsers->setLastLogin(new TDateTime(time()));

        //register session, cookie and success login attempt
        $this->registerNewUserSessionInDB();
        $this->registerNewUserLoginHistoryInDB();
        $this->registerSessionOrCookie($bChkKeepMeLoggedIn);
        $this->registerSuccessfulLoginAttempt($sUsername);

        if($this->objUsers->saveToDB())
        {
            $this->onLoginSuccess();
            $this->populatePermissionsSessionArray();    
            
            if ($this->objUsers->getPasswordExpires()->isInThePast() && (!$this->objUsers->getPasswordExpires()->isZero()))
            {
                // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,': Location: '.$this->sURLLoginSuccessUserChangePassword);

                header('Location: '.$this->sURLLoginSuccessUserChangePassword);
                die();   
            }
            else
            {
                // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'Location: '.$this->sURLLoginSuccess);

                header('Location: '.$this->sURLLoginSuccess);
                die();    
            }
        }
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'objUsers->saveToDB() failed');
    }


    
    /**
     * the logout url is based on loginformurl but with parameters for logout
     * unique identifier
     */
    public function getURLLogOut()
    {
        $sURL = '';
        $sURL = $this->sURLLoginForm;
        $sURL = addVariableToURL($sURL, TAuthenticationSystemAbstract::GETARRAYKEYLOGOUT, TAuthenticationSystemAbstract::GETARRAYKEYLOGOUTVALUE);
        $sURL = addVariableToURL($sURL, TAuthenticationSystemAbstract::GETARRAYKEYCONTROLLERID, md5($this->sControllerID));
        return $sURL;
    }
    
    
    /**
     * prevent too many failed attempts
     * this prevents brute force password crack attacks
     * 
     * this function DOES ALL the checks and calls all the detectFlood..() functions
     * 
     * 
     * @return bool true=flood, false=NO flood detected
     */
    public function detectFloodAll()
    {        

        if ($this->detectFloodGeneric())
            return true;

        if ($this->detectFloodFailedLoginAttempts())
            return true;
        
        if ($this->detectFloodSuccessFulLoginAttempts())
            return true;

        if ($this->detectFloodPasswordResetAttempts())
            return true;            

        if ($this->detectFloodCreateAccountAttempts())
            return true;            


        return false;
    }
    

    /**
     * this detects a flood OF ALL ATTEMPTS (successful logins, failed logins, password resets and account creates)
     *      
     * we rather have a Denial Of Service on logging in, than choking the whole system 
     * if you can't detect ip address (because its changing on every reload) 
     * or can't detect session (because its deleted on every reload), 
     * or can't detect fingerprint (because it changed on every reload), 
     * this is the last resort by detecting all attempts regardless of ip-address, fingerprint, or session
     *
     * @return bool true=flood, false=NO flood detected
     */
    public function detectFloodGeneric()
    {
        $iTSNow = time();
        $objDateAnHourAgo = new TDateTime($iTSNow);
        $objDateYesterdaySameTime = new TDateTime($iTSNow);
        $objDateLastMonthSameTime = new TDateTime($iTSNow);

        $objDateAnHourAgo->subtractHours(1);
        $objDateYesterdaySameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERDAY);
        $objDateLastMonthSameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERMONTH);


         //==== ALL LOGIN ATTEMPTS
            //database: detect flood based per HOUR
            $this->objUsersFloodDetectModel->clear();
            $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_RANDOMID, 'countattempts');
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateAnHourAgo, COMPARISON_OPERATOR_GREATER_THAN); //wait 1 hour  
            $this->objUsersFloodDetectModel->loadFromDB();
            if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
            {
                if ($this->objUsersFloodDetectModel->getAsInt('countattempts') > $this->getMaxAllowedAttemptsPerHour())
                {
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__,' database flood detected per hour based on all attempts. From ip:'. getIPAddressClient());
                    preventTimingAttack(50, 700);
                    return true;
                }
            }


            //database: detect flood based per DAY
            $this->objUsersFloodDetectModel->clear();
            $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_RANDOMID, 'countattempts');
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateYesterdaySameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 24 hours   
            $this->objUsersFloodDetectModel->loadFromDB();
            if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
            {                
                if ($this->objUsersFloodDetectModel->getAsInt('countattempts') > $this->getMaxAllowedAttemptsPerDay())
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database flood detected per day based on all attempts. Last ip:'. getIPAddressClient());
                    preventTimingAttack(10, 150);

                    $sEmailMessage = '';
                    $sEmailMessage = 'Detected flood on ALL attempts ('.$this->getMaxAllowedAttemptsPerDay().' attempts exceeded).<br>It may be busy, but it could also be an attack.<br>'.$this->getApplicationName().' is not allowing anymore logins today';
                    $this->sendEmailToSystemAdmin('flood detected '.$this->getApplicationName(), $sEmailMessage);  

                    return true;
                }
            }

        return false;
    }

    /**
     * this detects a flood of failed login attempts
     *      
     * @param string $sUsername username that the user inputted on the form
     * @return bool true=flood, false=NO flood detected
     */
    public function detectFloodFailedLoginAttempts($sUsername = '')
    {
        $iTSNow = time();
        $objDateAnHourAgo = new TDateTime($iTSNow);
        $objDateYesterdaySameTime = new TDateTime($iTSNow);
        $iDateYesterdaySameTime = 0; //timestamp
        $objDateLastMonthSameTime = new TDateTime($iTSNow);
        $iDateLastMonthSameTime = 0; //timestamp

        $objDateAnHourAgo->subtractHours(1);
        $objDateYesterdaySameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERDAY);
        $iDateYesterdaySameTime = $objDateYesterdaySameTime->getTimestamp();
        $objDateLastMonthSameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERMONTH);
        $iDateLastMonthSameTime = $objDateLastMonthSameTime->getTimestamp();


        //==== FAILED LOGIN ATTEMPTS

        
            //session: failed logins
            $iCountAttempts = 0;
            if (isset($_SESSION[$this->sAKFloodDetectionFailedLoginAttempts]))
            {
                if (is_array($_SESSION[$this->sAKFloodDetectionFailedLoginAttempts]))
                {
                    foreach ($_SESSION[$this->sAKFloodDetectionFailedLoginAttempts] as $iTSAtt) //iTimestampAttempt
                    {
                        //everything within a day counts as an attempt
                        if ($iDateYesterdaySameTime < $iTSAtt)
                            $iCountAttempts++;
                    }
                }
                if ($iCountAttempts > $this->getMaxAllowedFailedLoginAttemptsPerDay())
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'session flood detected on failed login attempts from ip address: '. getIPAddressClient());
                    preventTimingAttack(2, 250);
                    return true;
                }            
            }
                    
            //database: detect flood based on ip 
            $this->objUsersFloodDetectModel->clear();
            $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 'countips');
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, getIPAddressClient());
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateYesterdaySameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 24 hours   
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, true); 
            $this->objUsersFloodDetectModel->loadFromDB();
            if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
            {
                if ($this->objUsersFloodDetectModel->getAsInt('countips') > $this->getMaxAllowedFailedLoginAttemptsPerDay())
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database flood detected based on IP-addres. From ip:'. getIPAddressClient());
                    preventTimingAttack(100, 300);
                    return true;
                }
            }
        



            //database: detect flood based on hashed username 
            //a smart attacker changes his IP address, but the username stays the same
            if ($sUsername != '') //only can flood detect when there is a username to begin with, there is none when the form is shown for the first time
            {
                $this->objUsersFloodDetectModel->clear();
                $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, 'countusers');
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, TSysUsersFloodDetectAbstract::hashUsername($sUsername));
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateYesterdaySameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 24 hours
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, true);                 
                $this->objUsersFloodDetectModel->loadFromDB();
                if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
                {
                    if ($this->objUsersFloodDetectModel->getAsInt('countusers') > $this->getMaxAllowedFailedLoginAttemptsPerDay())
                    {
                        logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database flood detected based on username. From ip: '. getIPAddressClient().' on user:'.$sUsername);
                        preventTimingAttack(50, 100);                            
                        return true;
                    }
                }
            }
                     

        
            //database: detect flood based on browser-fingerprint
            $this->objUsersFloodDetectModel->clear();
            $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 'countfingerprints');
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, getFingerprintBrowser(false));
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateYesterdaySameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 24 hours   
            $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, true); 
            $this->objUsersFloodDetectModel->loadFromDB();
            if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
            {
                if ($this->objUsersFloodDetectModel->getAsInt('countfingerprints') > ($this->getMaxAllowedFailedLoginAttemptsPerDay() * 10)) //since the browser fingerprinter is not super unique we take this very loose by multiplying the amount of attempts by 10
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database flood detected based on IP-addres. From ip: '. getIPAddressClient().' on user:'.$this->objEdtUsername->getValueSubmitted());
                    preventTimingAttack(10, 60);
                    return true;
                }
            }   

                   
        return false;    
    }

    /**
     * this detects a flood of successful login attempts
     *      
     * @param string $sUsername username that the user inputted on the form
     * @return bool true=flood, false=NO flood detected
     */
    public function detectFloodSuccessFulLoginAttempts($sUsername = '')
    {
        $iTSNow = time();
        $objDateAnHourAgo = new TDateTime($iTSNow);
        $objDateYesterdaySameTime = new TDateTime($iTSNow);
        $iDateYesterdaySameTime = 0; //timestamp
        $objDateLastMonthSameTime = new TDateTime($iTSNow);
        $iDateLastMonthSameTime = 0; //timestamp

        $objDateAnHourAgo->subtractHours(1);
        $objDateYesterdaySameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERDAY);
        $iDateYesterdaySameTime = $objDateYesterdaySameTime->getTimestamp();
        $objDateLastMonthSameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERMONTH);
        $iDateLastMonthSameTime = $objDateLastMonthSameTime->getTimestamp();

        //==== SUCCEEEDED LOGIN ATTEMPTS

            //session:  successful logins
            $iCountAttempts = 0;
            if (isset($_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts]))
            {
                if (is_array($_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts]))
                {
                    foreach ($_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts] as $iTSAtt) //iTimestampAttempt
                    {
                        //everything within a day counts as an attempt
                        if ($iDateYesterdaySameTime < $iTSAtt)
                            $iCountAttempts++;
                    }
                }
                if ($iCountAttempts > $this->getMaxAllowedSuccessfulLoginAttemptsPerDay())
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'session flood detected on successful login attempts from ip address: '. getIPAddressClient());
                    preventTimingAttack(0, 600);
                    return true;
                }            
            }

            //database: detect flood based on username
            if ($sUsername != '') //this could be null 
            {
                //I deliberately do not do a find() on fingerprint, so the search in the database is broader
                $this->objUsersFloodDetectModel->clear();
                $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 'countlogins');
                // $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_USERNAME, $this->objEdtUsername->getValueSubmitted());
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, TSysUsersFloodDetectAbstract::hashUsername($sUsername));
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateYesterdaySameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 24 hours                       
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISSUCCEEDEDLOGINATTEMPT, true); 
                $this->objUsersFloodDetectModel->loadFromDB();
                if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
                {
                    if ($this->objUsersFloodDetectModel->getAsInt('countlogins') > $this->getMaxAllowedSuccessfulLoginAttemptsPerDay()) 
                    {
                        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database flood detected based on IP-addres. From ip: '. getIPAddressClient().' on user: '.$sUsername);
                        preventTimingAttack(50, 700);
                        return true;
                    }
                }                        
            }

        return false;
    }

    /**
     * this detects a flood of password reset attempts
     *      
     * @param string $sEmailAddress email address in plain text 
     * @return bool true=flood, false=NO flood detected
     */
    public function detectFloodPasswordResetAttempts($sEmailAddress = '')
    {
        $iTSNow = time();
        $objDateAnHourAgo = new TDateTime($iTSNow);
        $objDateYesterdaySameTime = new TDateTime($iTSNow);
        $iDateYesterdaySameTime = 0; //timestamp
        $objDateLastMonthSameTime = new TDateTime($iTSNow);
        $iDateLastMonthSameTime = 0; //timestamp

        $objDateAnHourAgo->subtractHours(1);
        $objDateYesterdaySameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERDAY);
        $iDateYesterdaySameTime = $objDateYesterdaySameTime->getTimestamp();
        $objDateLastMonthSameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERMONTH);
        $iDateLastMonthSameTime = $objDateLastMonthSameTime->getTimestamp();


        //==== PASSWORD RESET

            //session: password resets
            $iCountAttempts = 0;
            if (isset($_SESSION[$this->sAKFloodDetectionPasswordResetAttempts]))
            {
                if (is_array($_SESSION[$this->sAKFloodDetectionPasswordResetAttempts]))
                {
                    foreach ($_SESSION[$this->sAKFloodDetectionPasswordResetAttempts] as $iTSAtt) //iTimestampAttempt
                    {
                        //everything within a day counts as an attempt
                        if ($iDateYesterdaySameTime < $iTSAtt)
                            $iCountAttempts++;
                    }
                }
                if ($iCountAttempts > $this->getMaxAllowedPasswordResetsPerDay())
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'session flood detected on password reset attempts from ip address: '. getIPAddressClient());
                    preventTimingAttack(50, 100);
                    return true;
                }            
            }        

            //database: 
            // if (($this->objFormPassworRecover) || isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID])) //on form existence of form or when click on link in email  --> 22-4-2024 removed
            if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID])) //on form existence of form or when click on link in email                        
            {
                //I deliberately do not do a find() on fingerprint, so the search in the database is broader
                $this->objUsersFloodDetectModel->clear();
                $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 'countresets');
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateYesterdaySameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 24 hours   
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISPASSWORDRESET, true); 
                $this->objUsersFloodDetectModel->loadFromDB();
                if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
                {
                    if ($this->objUsersFloodDetectModel->getAsInt('countresets') > $this->getMaxAllowedPasswordResetsPerDay()) 
                    {
                        logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database password reset flood detected based on IP-addres. From ip: '. getIPAddressClient());
                        preventTimingAttack(0, 90);
                        return true;
                    }
                }                    
            }

            //email address fingerprint: 
            if ($sEmailAddress != '')
            {
                $this->objUsersFloodDetectModel->clear();
                $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, 'countcreates');
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL,  $this->objUsersFloodDetectModel->generateEmailAddressFingerprint($sEmailAddress)); 
                // $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISPASSWORDRESET, true); --> now it also includes create account attempts
                $this->objUsersFloodDetectModel->loadFromDB();
                if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
                {
                    if ($this->objUsersFloodDetectModel->getAsInt('countcreates') > $this->getMaxAllowedPasswordResetsPerDay())
                    {
                        logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database password reset flood detected based on email address. From email address: '. $sEmailAddress);
                        preventTimingAttack(100, 400);
                        return true;
                    }
                }                    
            }            

        return false;
    }    

    /**
     * this detects a flood of creating accounts
     *      
     * @param string $sEmailAddress
     * @return bool true=flood, false=NO flood detected
     */    
    protected function detectFloodCreateAccountAttempts($sEmailAddress = '')
    {
        $iTSNow = time();
        $objDateAnHourAgo = new TDateTime($iTSNow);
        $objDateYesterdaySameTime = new TDateTime($iTSNow);
        $iDateYesterdaySameTime = 0; //timestamp
        $objDateLastMonthSameTime = new TDateTime($iTSNow);
        $iDateLastMonthSameTime = 0; //timestamp

        $objDateAnHourAgo->subtractHours(1);
        $objDateYesterdaySameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERDAY);
        $iDateYesterdaySameTime = $objDateYesterdaySameTime->getTimestamp();
        $objDateLastMonthSameTime->subtractDays(TAuthenticationSystemAbstract::FLOODDETECT_COUNTUNIT_PERMONTH);
        $iDateLastMonthSameTime = $objDateLastMonthSameTime->getTimestamp();

        //==== CREATE ACCOUNT

            //session: account creates
            $iCountAttempts = 0;
            if (isset($_SESSION[$this->sAKFloodDetectionCreateAccountAttempts]))
            {
                if (is_array($_SESSION[$this->sAKFloodDetectionCreateAccountAttempts]))
                {
                    foreach ($_SESSION[$this->sAKFloodDetectionCreateAccountAttempts] as $iTSAtt) //iTimestampAttempt
                    {
                        //everything within a day counts as an attempt
                        if ($iDateLastMonthSameTime < $iTSAtt)
                            $iCountAttempts++;
                    }
                }
                if ($iCountAttempts > $this->getMaxAllowedCreateAccountsPerMonth())
                {
                    logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'session flood detected on account creation attempts from ip address: '. getIPAddressClient());
                    preventTimingAttack(200, 600);
                    return true;
                }            
            }             
        
            //database: 
            if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID])) //when click on link in email
            {
                //I deliberately do not do a find() on browser fingerprint, so the search in the database is broader
                $this->objUsersFloodDetectModel->clear();
                $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 'countcreates');
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateLastMonthSameTime, COMPARISON_OPERATOR_GREATER_THAN); //wait 1 month   
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISCREATEACCOUNTATTEMPT, true); 
                $this->objUsersFloodDetectModel->loadFromDB();
                if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
                {
                    if ($this->objUsersFloodDetectModel->getAsInt('countcreates') > $this->getMaxAllowedCreateAccountsPerMonth())
                    {
                        logError (__CLASS__.': '.__FUNCTION__.': '.__LINE__,'database create account flood detected based on IP-addres. From ip: '. getIPAddressClient());
                        preventTimingAttack(90, 500);
                        return true;
                    }
                }                    
            }

            //email address fingerprint: 
            //although 1-emailaddress-at-a-time is standard. users can create and delete accounts multiple times
            //too many creations of an account under the same email address is very suspicious (especially from different IP addresses, so this detection goes beyond IPs)
            if ($sEmailAddress != '') 
            {
                $this->objUsersFloodDetectModel->clear();
                $this->objUsersFloodDetectModel->countResults(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, 'countcreates');
                $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL,  $this->objUsersFloodDetectModel->generateEmailAddressFingerprint($sEmailAddress)); 
                // $this->objUsersFloodDetectModel->find(TSysUsersFloodDetectAbstract::FIELD_ISCREATEACCOUNTATTEMPT, true); --> now it also includes password reset attempts
                $this->objUsersFloodDetectModel->loadFromDB();
                if ($this->objUsersFloodDetectModel->count() > 0)//is there a result at all?
                {
                    if ($this->objUsersFloodDetectModel->getInt('countcreates') > $this->getMaxAllowedCreateAccountsWithSameEmail())
                    {
                        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'database create account based on email address. More accounts created ('.$this->objUsersFloodDetectModel->getAsInt('countcreates').') than allowed ('.$this->getMaxAllowedCreateAccountsWithSameEmail().'). From email address: '. $sEmailAddress);
                        preventTimingAttack(100, 400);
                        return true;
                    }
                }                    
            }


        return false;
    }



    
    /****************************************************************************
     *              ABSTRACT METHODS
    ****************************************************************************/
    
    /**
     * unique  class login ID to prevent 2 (or more) login classes on the same site 
     * using the same variables for usernames,passwords etc.
     * 
     * return a constant string that is always the same to identify this class,
     * for example 'cms' or 'webshop'
     * DO NOT GENERATE with uniqid() then this class won't authenticate!!!!!!!!!!!
     * 
     * @return string
     */
    abstract public function getControllerID();
    


    /**
     * get url to create a new account
     * 
     * for example for a cms it could be: https://www.mysite.com/cms/createaccount_entercredentials.php
     *      
     * 
     * @return string
     */
    abstract public function getURLCreateAccountEnterCredentials();    
    
    

    /**
     * get a new object of 
     * @return TSysUsersAbstract
     */
    abstract public function getNewUsers();

    /**
     * get a new object of 
     * @return TSysUsersFloodDetectAbstract
     */
    abstract public function getNewUsersFloodDetectModel();
    
    /**
     * get a new object of 
     * @return TSysUsersSessionsAbstract
     */
    abstract public function getNewUsersSessions();

    /**
     * get a new object of 
     * @return TSysUsersLoginHistory
     */
    abstract public function getNewUsersLoginHistory();


    /**
     * disable or enable the 'keep me logged in' system via a cookie
     */
    abstract public function getUseKeepLoggedIn();
    

    /**
     * populate $_SESSION permission array
     * (user authorisation system)
     * 
     * this function is called right after onLoginSuccess()
     * 
     * @param bool $bForceReload regenerate permissionsarray. on false only load when permissionsarray is empty (default = true)
     * @return bool success??
     */
    abstract protected function populatePermissionsSessionArray($bForceReload = true);
    
    /**
     * if you have additional permissions definied, handle them in this function
     * (user authorisation system)
     * 
     * 
     * @return bool accessgranted
     */
    abstract protected function handleAuthenticationChild();   

    
    /**
     * the mailbot that sends account confirmations and password reset emails
     * has a from emailaddress, for example: system@example.com
     * 
     * @return string email address of the email bot
     */
    abstract public function getMailbotFromEmailAddress();  

    /**
     * the mailbot that sends account confirmations and password reset emails
     * has a name, for example John Doe in: John Doe <system@example.com>
     * 
     * @return string the name of email address of the email bot
     */
    abstract public function getMailbotFromName();    
    

    /**
     * get name of the application
     * (for example: 'CMS 5')
     * This is used in emails (account activation and password reset)
     * But since this is an abstract controller wich is also used outside the cms, we need to request it
     *
     * @return string
     */
    abstract public function getApplicationName();


    /**
     * is anyone allowed to create an account?
     * for closed systems like a CMS we'd like to switch this off (return false)
     * but for open systems like a webshop we can enable this (return true)
     * 
     * @return bool
     */
    abstract public function getCanAnyoneCreateAccount();


    /**
     * how many failed logins can a user have per day?
     * 
     * @return int
     */
    abstract public function getMaxAllowedFailedLoginAttemptsPerDay();

    /**
     * how many times can a user log-in in a day?
     * how many times can a user log-in in a day?
     * when someone logs in 200 times a day, this smells fishy
     * 
     * @return int
     */
    abstract public function getMaxAllowedSuccessfulLoginAttemptsPerDay();


    /**
     * how many times can a user reset a password in a day?
     * 
     * @return int
     */
    abstract public function getMaxAllowedPasswordResetsPerDay();


    /**
     * how many times can a user create an account PER MONTH?
     * 
     * @return int
     */
    abstract public function getMaxAllowedCreateAccountsPerMonth();

    /**
     * How many times can a user create an account under the same email address.
     * Although 1-at-a-time is ALWAYS standard, this function returns how many creates are allowed
     * in the entire history of the logs in the database.
     * (if you delete the logs once every 6 months, it is keeping track of the last 6 months).
     * 
     * for example: a user can delete an account and create a new one with the same email address.
     * How many times is allowed?
     * Too many times is very suspicious.
     * 
     * @return int
     */
    abstract public function getMaxAllowedCreateAccountsWithSameEmail();    

    /**
     * how many times can the site accept an attempt PER HOUR?
     * this is all attempts: failed logins, successful logins, change password, create accounts
     * We do per day and per hour
     * 
     * this to prevents an overflow of login-type actions to cause a Denial Of Service for logged in users,
     * we rather have a Denial Of Service on logging in than the whole system going down
     * 
     * For a CMS 20 is pretty generous, 
     * but for a site like youtube 20 is pretty conservative
     * 
     * @return int
     */
    abstract public function getMaxAllowedAttemptsPerHour();    

    /**
     * how many times can the site accept an attempt PER DAY?
     * this is ALL attempts for ALL USERS combined: failed logins, successful logins, change password, create accounts
     * 
     * this to prevents an overflow of login-type actions to cause a Denial Of Service for logged in users,
     * we rather have a Denial Of Service on logging in than the whole system going down
     * 
     * For a CMS 100 is pretty generous, 
     * but for a site like youtube 100 is pretty conservative
     * 
     * @return int
     */
    abstract public function getMaxAllowedAttemptsPerDay();    

    /**
     * use google's recaptcha to login?
     * you may want to use this for a cms, but not a webshop
     * 
     * @return bool
     */
    abstract public function getUseRecapthaLogin();  

    /**
     * send an email to system administrator
     * 
     * @return bool
     */
    abstract protected function sendEmailToSystemAdmin($sSubject, $sMessage);
    

    /**
     * what is the url of the loginform?
     * use full urls with https in front (no relative urls)!
     * with relative urls it wil redirect to the relative script which 
     * can be non-existing if not in root directory!
     * 
     * for example for a cms it could be: https://www.mysite.com/cms/index.php
     *      
     * This class will redirect logouts and login-fails to this url.    
     * 
     * DO NOT USE getURLThisScript()! because it depends on the script that 
     * calls this class, this class does header redirects, the script will change
     * at it will call itself indefinitely
     * 
     * @return string
     */
    abstract public function getURLLoginForm();
    
    /**
     * what url do you want to forward if a login is succesful?
     * use full urls with https in front (no relative urls)!
     * with relative urls it wil redirect to the relative script which 
     * can be non-existing if not in root directory!
     * 
     * for example for a cms it could be: https://www.mysite.com/cms/home.php
     *      
     * This class will redirect successful logins to this url.
     * 
     * @return string
     */
    abstract public function getURLLoginSuccess();

    /**
     * what url do you want to forward if a login is succesful?
     * BUT THE USER NEEDS TO CHANGE HIS/HER PASSWORD
     * 
     * use full urls with https in front (no relative urls)!
     * with relative urls it wil redirect to the relative script which 
     * can be non-existing if not in root directory!
     * 
     * for example for a cms it could be: https://www.mysite.com/cms/settings.php
     *      
     * This class will redirect successful logins to this url.
     * 
     * @return string
     */
    abstract public function getURLLoginSuccessUserChangePassword();    
    
    /**
     * what url do you want to forward to to recover a password
     * 
     * for example for a cms it could be: https://www.mysite.com/cms/passwordrecover_enteremail.php
     *      
     * 
     * @return string
     */
    abstract public function getURLPasswordRecoverEnterEmail();    

    /**
     * what url do you want to forward to to recover a enter 
     * 
     * for example for a cms it could be: https://www.mysite.com/cms/passwordrecover_enterpassword.php
     *      
     * 
     * @return string
     */
    abstract public function getURLPasswordRecoverEnterPassword();        

    /**
     * use google to sign in?
     * you may want to use this for a cms, but not a webshop
     * 
     * @return bool
     */
    abstract public function getUseSigninWithGoogle(); 

    /**
     * you can define extra functionality when a login is succesful.
     * this function is called right before the header redirect
     */
    abstract public function onLoginSuccess();
    
    /**
     * you can define extra functionality when a logging out
     */
    abstract public function onLogout();    
    
    
    /**
     * get new TSysBlackWhitelistAbstract object
     * 
     * @return TSysBlackWhitelistAbstract
     */
    abstract public function getNewIPBlackWhitelistModel();

    /**
     * in stealth mode: all blacklisted and non-whitelisted IP addresses are served 404 errors
     *  instead of 401 errors. 
     * This prevents automated directory enumeration
     * 
     * @return bool
     */
    abstract public function getStealthModeBlackWhitelistedIPs();

}
