<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;

use dr\classes\dom\FormGenerator;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\validator\Characterwhitelist;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\validator\Required;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist;
use dr\classes\models\TSysUsersFloodDetectAbstract;
use dr\classes\models\TSysUsersSessionsAbstract;
use dr\classes\types\TDateTime;

/**
 * Description of TLoginFormControllerAbstract
 *
 * This is the form shown to the user that prompts the user for login credentials: username and password
 * 
 * This is a CMS unaware template, so you can reuse it for the frontend of your site (i.e. webshop login)
 * The idea is that you can add functionality, like 2FA and all login forms support it
 * 
 * @author dennis renirie
 * 19 apr 2024: loginform created
 * 14 nov 2024: TLoginFormControllerAbstract:handleLogInUsernamePasswordSubmitted() heeft support voor blacklists
 * 15 nov 2024: TLoginFormControllerAbstract: getNewLoginIPBlackWhitelistModel toegevoegd
 */

abstract class TLoginFormControllerAbstract extends TControllerAbstract
{
    protected $objAuthenticationSystem = null;//TAuthenticationSystemAbstract();

    protected $objFormLogin = null;//dr\classes\dom\FormGenerator

    protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    protected $objEdtUsername = null;//dr\classes\dom\tag\form\InputText
    protected $objEdtPassword = null;//dr\classes\dom\tag\form\InputPassword
    protected $objChkKeepMeLoggedIn = null;//dr\classes\dom\tag\form\InputCheck

    private $bShowPasswordRecoverLink = true; //for the template: determines if a link is shown for password recovery. We wan't to show the link when too many failed login attempts, but not show when too many overall attempts
    private $bShowCreateAccountLink = true; //for the template: determines if a link is shown for creating an account. We wan't to show the link when too many failed login attempts OR when it's disabled, but not show when too many overall attempts

    protected $objGoogleClient = null;//Google_Client: for sign-in-with-google    


    public function __construct()
    {
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Loading page: '.APP_URLTHISSCRIPT);
                
        //sign-in-with-google
        if ($this->objAuthenticationSystem->getUseSigninWithGoogle())
        {
            require_once APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'googleapi/vendor/autoload.php';// we can't do it earlier, because we don't know yet if we need to use the google login (only TCMSAuthenticationSystem knows, because its a CMS setting). also it might take unnessary system resources when google method is not used

            $this->objGoogleClient = new \Google_Client();    

            $this->objGoogleClient->setClientId(APP_GOOGLEAPI_CLIENTID);
            $this->objGoogleClient->setClientSecret(APP_GOOGLEAPI_CLIENTSECRET);
            $this->objGoogleClient->setApplicationName($this->objAuthenticationSystem->getApplicationName());
                  
            $this->objGoogleClient->setRedirectUri(removeVariableFromURL(APP_URLTHISSCRIPT)); //this needs to match very literally in google cloud developer console (including parameters)
            // $this->objGoogleClient->setRedirectUri('https://www.socialvideoplaza.com/testyoutubeapi/');//echt letterlijk overnemen van google cloud developer console
            // $this->objGoogleClient->setRedirectUri('https://www.bedrijfsuitje.events/application/');//echt letterlijk overnemen van google cloud developer console
            
            //scopes (what does the application have access to?)
            // $this->objGoogleClient->addScope("email");
            // $this->objGoogleClient->addScope("profile");
            // $this->objGoogleClient->addScope("https://www.googleapis.com/auth/youtube");         
            if (APP_GOOGLEAPI_SCOPES)
            {
                if (is_array(APP_GOOGLEAPI_SCOPES))
                {
                    foreach(APP_GOOGLEAPI_SCOPES as $sGoogleApiScope)
                        $this->objGoogleClient->addScope($sGoogleApiScope);
                }
                else
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'APP_GOOGLEAPI_SCOPES is not an array');
            }
        }

        //parent::__construct();//--> I EXPLICITYLY DISABLE __construct() of the parent because it calls the rendering of templates, that results in headers being sent, and I can't do header redirects anymore! Instead I call render() and populate() manually

        //execute
        $this->handleAuthenticationIPAddress(); //--> this might die() the script and show 401 or 404 error 
        $this->populate();//manually call populate() BEFORE handleLoginLogout(), instead of parent::__construct() --> we need the form object and the editboxes objects in handleLoginLogout()
        $this->handleLoginLogout(); //--> this might die() the script and header redirect to other page when trying to login!!! In other words: it will never reach code below this function call.
        $this->render();//manually call populate() AFTER handleLoginLogout(), instead of parent::__construct() --> it renders http headers
    }

    /**
     * used for communicating with the template wether to show the link or not.
     * 
     * 
     * @return void
     */    
    public function getShowPasswordRecoverLink()
    {
        return $this->bShowPasswordRecoverLink;
    }

    /**
     * used for communicating with the template wether to show the link or not.
     * 
     * 
     * @return bool
     */      
    protected function setShowPasswordRecoverLink($bShow)
    {
        $this->bShowPasswordRecoverLink = $bShow;
    }

    /**
     * used for communicating with the template wether to show the link or not.
     * 
     * if getCanAnyoneCreateAccount() == false, this function will ALLWAYS return false
     * 
     * @return void
     */
    public function getShowCreateAccountLink()
    {
        if (!$this->objAuthenticationSystem->getCanAnyoneCreateAccount())
            return false;
        return $this->bShowCreateAccountLink;
    }

    /**
     * used for communicating with the template wether to show the link or not.
     * 
     * 
     * @return bool
     */    
    protected function setShowCreateAccountLink($bShow)
    {
        $this->bShowCreateAccountLink = $bShow;
    }

    /**
     * 
     * @return FormGenerator
     */
    public function getFormLogin()
    {
        return $this->objFormLogin;
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
        $bShowCreateAccountLink = $this->getShowCreateAccountLink();
        $bShowPasswordRecoverLink = $this->getShowPasswordRecoverLink();
        $objFormLogin = $this->objFormLogin;

        return get_defined_vars();
    }

    /**
     * This function adds LATE BINDING variables to template which are NOT cached 
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
        return get_defined_vars();
    }    


    protected function populate()
    {
        $this->objFormLogin = new FormGenerator('login-'.$this->objAuthenticationSystem->getControllerID(), $this->objAuthenticationSystem->getURLLoginForm());
        $this->objFormLogin->setRecaptchaV3Use($this->objAuthenticationSystem->getUseRecapthaLogin());

            //username
        $this->objEdtUsername = new InputText();
        $this->objEdtUsername->setNameAndID('edtUsername');
        $this->objEdtUsername->setClass('fullwidthtag');                 
        $this->objEdtUsername->setRequired(true); 
        $this->objEdtUsername->setMaxLength(255);    
        $objValidator = new Maximumlength(100, false);
        $this->objEdtUsername->addValidator($objValidator);        
        $objValidator = new Required();
        $this->objEdtUsername->addValidator($objValidator);       
        $objValidator = new Characterwhitelist(TAuthenticationSystemAbstract::CHARSALLOWED_FORMFIELDS);
        $this->objEdtUsername->addValidator($objValidator);
        $this->objFormLogin->add($this->objEdtUsername, '', transg('loginform_field_username', 'username')); 


            //password
        $this->objEdtPassword = new InputPassword();
        $this->objEdtPassword->setNameAndID('edtPassword');
        $this->objEdtPassword->setClass('fullwidthtag');                 
        $this->objEdtPassword->setRequired(true); 
        $this->objEdtPassword->setMaxLength(255);    
        $objValidator = new Maximumlength(100);
        $this->objEdtPassword->addValidator($objValidator);        
        $objValidator = new Required();
        $this->objEdtPassword->addValidator($objValidator);       
        $objValidator = new Characterwhitelist(TAuthenticationSystemAbstract::CHARSALLOWED_FORMFIELDS);
        $this->objEdtPassword->addValidator($objValidator);
        $this->objFormLogin->add($this->objEdtPassword, '', transg('loginform_field_password', 'password'));         
        
            //keep-me-logged-in
        if ($this->objAuthenticationSystem->getUseKeepLoggedIn())
        {
            $this->objChkKeepMeLoggedIn = new InputCheckbox();
            $this->objChkKeepMeLoggedIn->setNameAndID('edtKeepMeLoggedIn');
            $this->objFormLogin->add($this->objChkKeepMeLoggedIn, '', transg('loginform_field_keepmeloggedin', 'keep me logged in'));        
        }
        

        if (!$this->objAuthenticationSystem->getUseRecapthaLogin())
            $this->objSubmit = new InputSubmit();
        else
            $this->objSubmit = new InputButton();
        $this->objSubmit->setValue(transg('loginform_button_login', 'log in')); 
        $this->objSubmit->setNameAndID('btnSubmit');     
        $this->objSubmit->setClass('fullwidthtag');   
        $this->objFormLogin->makeRecaptchaV3SubmitButton($this->objSubmit);  
        $this->objFormLogin->add($this->objSubmit);        
    }

    /**
     * the user tries to log in, handle the authentication and send to the right pages (success or failed)
     * if form is not submitted, this function does nothing.
     */
    public function handleLoginLogout()
    {   
        $sUsername = '';    
        logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'started handleLoginLogout()');
        // die('dikke lul 3 bier');

        //get username for flood detection
        if ($this->objFormLogin) //when login disabled, there is no username
            $sUsername = $this->objEdtUsername->getValueSubmitted();

        //with too many failed login attempts we still want to show the create-account and password-recover link
        if (!$this->objAuthenticationSystem->detectFloodFailedLoginAttempts($sUsername))
        {
            if ((!$this->objAuthenticationSystem->detectFloodGeneric()) && (!$this->objAuthenticationSystem->detectFloodSuccessFulLoginAttempts($sUsername)))
            {        
                if (!$this->isLoggingOut())
                {
                    $this->handleLogIn();
                }
                else //is in the process of logging out?
                {
                    $this->handleLogOut();
                }
            }//end: flood detection
            else
            {            
                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_message_toomanyattempts', 'It is very busy right now, try again later.'));
                $this->objFormLogin = null; //prevent attacker from logging in 
                // $this->objFormPassworRecover = null; //prevent attacker from recovering password
                $this->objAuthenticationSystem->setShowCreateAccountLink(false);
                $this->objAuthenticationSystem->setShowPasswordRecoverLink(false);   
                preventTimingAttack(100, 400);
            }
        }
        else
        {
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_message_toomanyfailedloginattempts', 'Too many failed login attempts, your ability to login is temporarily suspended. try again later.'));
            $this->objFormLogin = null; //prevent attacker from logging in 
            $this->objAuthenticationSystem->setShowCreateAccountLink(false);
            $this->objAuthenticationSystem->setShowPasswordRecoverLink(true);
            preventTimingAttack(100, 500);
        }
    }
   
    /**
     * do necessary things to log user in
     *
     */    
    private function handleLogIn()
    {
        // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'started handleLogin()');
        //is user already/still logged in?
        if ($this->objAuthenticationSystem->authenticateUser())
        {               
            // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'authenticate() done.');
            
            //update session
            $this->objAuthenticationSystem->getUserSessions()->setSessionUpdated(new TDateTime(time()));
            $this->objAuthenticationSystem->getUserSessions()->saveToDB();
            
            //does user need to change password?
            if ($this->objAuthenticationSystem->getUsers()->getPasswordExpires()->isInThePast() && (!$this->objAuthenticationSystem->getUsers()->getPasswordExpires()->isZero()))
            {
                // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'Location: '.$this->sURLLoginSuccessUserChangePassword);
                header('Location: '.$this->objAuthenticationSystem->getURLLoginSuccessUserChangePassword());
                die(); //prevent further script execution
            }
            else
            {
                // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'Location: '.$this->sURLLoginSuccess);
                header('Location: '.$this->objAuthenticationSystem->getURLLoginSuccess());
                die(); //prevent further script execution
            }
        }
        else //not logged in, try to log in
        {
            //is form submitted or is it the first view of the page?
            //else it's the first view of the page, do nothing and show the login form
            if ($this->objFormLogin->isFormSubmitted())
            {
                $this->handleLogInUsernamePasswordSubmitted();
            }

            //SIGN IN WITH GOOGLE: callback from google api (sign-in-with-google)?
            if ($this->objAuthenticationSystem->getUseSigninWithGoogle())
            {
                if (isset($_GET['code'])) //parameter specific to google api
                {
                    $this->handleLogInGoogleSigninCallback();
                }
            }
                        
        }        
    }

   /**
     * do nessasary things to log user out
     *
     */
    private function handleLogOut()
    {
        //we don't want to destroy the session because some things are stored in the session like 
        //the flood detect of successful logins

        $this->objAuthenticationSystem->setIsLoggedIn(false);        
        
        //DELETE LOGIN SESSION FROM DATABASE
        //we still need to authenticate, because then the user is loaded from db, 
        //otherwise we don't know which session to remove from db
        //we could delete a sessions based on id, but that would make is possible
        //to stage a DOS attack by filling out ids of other sessions in the
        //cookie, we don't want that        
        if ($this->objAuthenticationSystem->authenticateUser()) 
        {

            // if(!$this->objUsersSessions->deleteFromDB_OLD($this->objUsersSessions->getRandomID(), TSysUsersSessionsAbstract::FIELD_RANDOMID))
            //     error_log(__CLASS__.': '.__FUNCTION__.': '.__LINE__.': deletefromdb failed');
            $iUID = 0;
            $iUID = $this->objAuthenticationSystem->getUserSessions()->getRandomID();
            $this->objAuthenticationSystem->getUserSessions()->clear();
            $this->objAuthenticationSystem->getUserSessions()->find(TSysUsersSessionsAbstract::FIELD_RANDOMID, $iUID);
            if (!$this->objAuthenticationSystem->getUserSessions()->deleteFromDB(true))
                error_log(__CLASS__.': '.__FUNCTION__.': '.__LINE__.': deletefromdb failed');            
        }
        else
                error_log(__CLASS__.': '.__FUNCTION__.': '.__LINE__.': authentication failed');


        //====in database: delete old failed login attempts        
        //based on ip address --> removed 15-11-2024 to increase security
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->clear();
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->find(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, getIPAddressClient());
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->find(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, true);
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->deleteFromDB(true);

        //based on username --> removed 15-11-2024 to increase security
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->clear();
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->find(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, TSysUsersFloodDetectAbstract::hashUsername($this->objEdtUsername->getValueSubmitted()));        
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->find(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, true);
        // $this->objAuthenticationSystem->getUsersFloodDetectModel()->deleteFromDB(true);
        
        
        
        //delete all cookie tokens (including bogus tokens)
        if ($this->objAuthenticationSystem->getUseKeepLoggedIn()) //keeploggedin
        {
            $sTokenName = '';
            for ($iCounter = 1; $iCounter <= TAuthenticationSystemAbstract::BOGUSLOGINTOKENSGENERATED; $iCounter++)
            {
                $sTokenName = $this->objAuthenticationSystem->getControllerID().TAuthenticationSystemAbstract::LOGINTOKENPREFIX.$iCounter;

                if (!setcookie($sTokenName, '', time() - 3600, '/', getDomain(), APP_ISHTTPS, true))
                    error_log('setcookie '.$sTokenName.' failed (delete cookie)');                

            }    
        }
        
        
        //delete all session tokens
        $sTokenName = '';
        for ($iCounter = 1; $iCounter <= TAuthenticationSystemAbstract::BOGUSLOGINTOKENSGENERATED; $iCounter++)
        {
            $sTokenName = $this->objAuthenticationSystem->getControllerID().TAuthenticationSystemAbstract::LOGINTOKENPREFIX.$iCounter;

            if (isset($_SESSION[$sTokenName]))
                unset($_SESSION[$sTokenName]);                

        }   
        
        //delete failed login attempts from session
        unset($_SESSION[$this->objAuthenticationSystem->getFloodDetectionFailedLoginAttemptsKey()]);
        // unset($_SESSION[$this->sAKFloodDetectionSuccessfulLoginAttempts]); --> kind of pointless when we do this, since this is the whole reason why we track this in the first place
        // unset($_SESSION[$this->sAKFloodDetectionPasswordResetAttempts]); --> out of security I won't delete it, it's still weird when a user tries to reset a password too often
        // unset($_SESSION[$this->sAKFloodDetectionCreateAccountAttempts]); --> out of security I won't delete it, it's still weird when a user tries to create new accounts too often
        

        // $this->clearFormTokens();


        $sSIDOld = '';
        $sSIDOld = session_id();            
        session_regenerate_id(true); //make a new session id (and keep the current session) to prevent someone stealing your session id and delete the old session file on server
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'User logout: Changed phpsessionid from: '.$sSIDOld.' ==> '.session_id());
        

        //===SIGN OUT FROM GOOGLE: logout from Google if we are signed-in-via google
        if (isset($_SESSION[SESSIONARRAYKEY_GOOGLEAPI_TOKEN])) //exists when login-via-google is enabled
        {
            if (isset($_SESSION[SESSIONARRAYKEY_GOOGLEAPI_TOKEN]['access_token']))//even if login-via-google is enabled, it might not have been used to log in by the user
            {
                $this->objGoogleClient->setAccessToken($_SESSION[SESSIONARRAYKEY_GOOGLEAPI_TOKEN]['access_token']);
                $this->objGoogleClient->revokeToken();
                unset($_SESSION[SESSIONARRAYKEY_GOOGLEAPI_TOKEN]);
                logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'User sign-out-from google');
            }
        }


        $this->objAuthenticationSystem->setMessageNormal(transg('authenticationsystem_logoutcompleted', 'log out completed'));
//        logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__.': Location: '.$this->sURLLoginFailed);
//        header('Location: '.$this->sURLLoginFailed);        --> this class assumes you are currently on the logout page by generating an url that is the logout url (so no need to go there)
        
        $this->objAuthenticationSystem->onLogout();
    }    
        
    
    /**
     * handle login procedure when username and password are submitted on form
     */
    private function handleLogInUsernamePasswordSubmitted()
    {
        $sUsername = '';
        $sPassword = '';
        $objBlackWhitelist = null;
        $bLoginSuccess = false; //default

        $sUsername = $this->objEdtUsername->getValueSubmitted();
        $sPassword = $this->objEdtPassword->getValueSubmitted();

        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'LoginForm submitted with username "'.$sUsername.'" and a password with '.strlen($sPassword).' characters', $sUsername);


        //check input errors
        if ($this->objFormLogin->isValid()) 
        {
            //first: check blacklist & whitelist
            $objBlackWhitelist = $this->getNewIPBlackWhitelistModel();
            $objBlackWhitelist->loadFromDBByIPAddress(getIPAddressClient());
            if ($objBlackWhitelist->isAllowedLogic(getIPAddressClient()))
            {
                //check username & password
                if ($this->objAuthenticationSystem->getUsers()->loadFromDBByUserLoginAllowed($sUsername, $sPassword))
                {
                    $bLoginSuccess = true;

                    logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'LoginForm submitted. User "'.$sUsername.'" approved', $sUsername);
                    $this->objAuthenticationSystem->registerLogin($sUsername, $this->objChkKeepMeLoggedIn->getValueSubmittedAsBool());
                }
            }
            else
            {   
                logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'IP address '.getIPAddressClient().' is NOT allowed to login because of blacklist/whitelist rules', $sUsername);                    
            }

            //handle failed login attempt
            //we handle blacklist and whitelist and failed username/password at the same time
            //this way we don't let the user know he is blacklisted to avoid them trying from another IP address
            if (!$bLoginSuccess)
            {
                $this->objAuthenticationSystem->registerFailedLoginAttempt($sUsername);

                preventTimingAttack(300,500);
                header('Location: '.addVariableToURL($this->objAuthenticationSystem->getURLLoginForm(), TAuthenticationSystemAbstract::GETARRAYKEYMESSAGEERROR, transg('authenticationsystem_loginfailed', 'login failed, user credentials are not valid')));
                die();
            }

        }
        else
        {
            logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'LoginForm submitted but had input errors', $sUsername);                    
            preventTimingAttack(300,500);
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlelogin_correctinputerrors', 'Please correct input errors'));             
        }
    }


    /**
     * handle the callback from sign-in-with-google
     */
    private function handleLogInGoogleSigninCallback()
    {
        try
        { 
            $arrGoogleToken = $this->objGoogleClient->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION[SESSIONARRAYKEY_GOOGLEAPI_TOKEN] = $arrGoogleToken;  

    
            if (isset($arrGoogleToken['error']))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $arrGoogleToken['error_description']);
                return false;
            }
            else
            {
                $this->objGoogleClient->setAccessToken($arrGoogleToken['access_token']);

                $objGoogle_oauth = new \Google_Service_Oauth2($this->objGoogleClient);
                $objGoogle_account_info = $objGoogle_oauth->userinfo->get();

                //does user exist?
                if ($this->objAuthenticationSystem->getUsers()->loadFromDBByGoogleID($objGoogle_account_info->id)) //===> assuming loadFromDBByGoogleID() exists. It doesn't on standard user\
                {
                    //geo-location check
                    //@todo geo location check (via whitelist ip + google location)
                    //registerFailedLoginAttempt

                    if ($this->objAuthenticationSystem->getUsers->count() == 0) //doesn't exist? create user!
                    {
                        // echo $objGoogle_account_info->id.'<br>';  // 
                        // echo $objGoogle_account_info->email.'<br>';  // This is null if the 'email' scope is not present.
                        // echo $objGoogle_account_info->name. '<br>';
                        // echo $objGoogle_account_info->familyName. '<br>';
                        // echo $objGoogle_account_info->gender. '<br>';
                        // echo $objGoogle_account_info->givenName. '<br>';
                        // echo $objGoogle_account_info->locale. '<br>';
                        // echo $objGoogle_account_info->picture. '<br>';

                        $this->objAuthenticationSystem->getUsers()->newRecord();
                        if (!$this->objAuthenticationSystem->getUsers()->handleLogInGoogleSigninCallbackCreateNewUser($objGoogle_oauth))
                            return false;   //can fail when account creation is now allowed for example                     
                        $this->objAuthenticationSystem->getUsers()->setGoogleID($objGoogle_account_info->id); //===> assuming setGoogleID() exists. It doesn't on standard user\
                        $this->objAuthenticationSystem->getUsers()->setUsername($objGoogle_account_info->email); 
                        $this->objAuthenticationSystem->getUsers()->setEmailAddressDecrypted($objGoogle_account_info->email); 

                        //if (!$this->objUsers->saveToDBAll()) {} //saved in registerLogin()
                    }
                    $this->objAuthenticationSystem->registerLogin($objGoogle_account_info->email, false);
                }
                else
                {
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'loading user with google id '.$objGoogle_account_info->id.' failed');
                    return false;
                }
            }

        }
        catch (\Exception $e) 
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Caught exception: ',  $e->getMessage());
            return false;
        } 
    }

    /**
     * handle the authentication of IP address based on Blacklist and whitelist rules
     */
    protected function handleAuthenticationIPAddress()
    {
        $objBlacklist = $this->getNewIPBlackWhitelistModel();
        if (!$objBlacklist->authenticateIPAddressDB(getIPAddressClient()))
        {        
            logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'ip address: '.getIPAddressClient().' REFUSED based on blacklist/whitelist rules');    

            if ($this->getStealthModeBlackWhitelistedIPs())
            {
                show404();
                die(); //we die() here to avoid endless redirection
            }
            // else
            //     showAccessDenied(); --> I don't show access denied, because it gives the attacker a huge hint that his IP-address is blocked and he should try with another IP address
        }        
    }

    /**
     * is in the process of loggin out?
     * 
     * it checks for the controllerid to prevent it's logging out the wrong user session
     */
    protected function isLoggingOut()
    {
        //'logout' exists
        if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEYLOGOUT]))
        {
            $this->objAuthenticationSystem->setIsLoggedIn(false);

            //if 'logout' == 1
            if ($_GET[TAuthenticationSystemAbstract::GETARRAYKEYLOGOUT] == TAuthenticationSystemAbstract::GETARRAYKEYLOGOUTVALUE)
            {
                //if controller-id exists
                if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEYCONTROLLERID]))
                {
                    //if controller-id is correct
                    if ($_GET[TAuthenticationSystemAbstract::GETARRAYKEYCONTROLLERID] == md5($this->objAuthenticationSystem->getControllerID()))
                        return true;
                }
            }
        }
        
        return false;
    }    

    /**
     * get google authentication url
     * 
     * 
     * @return string
     */
    public function getURLGoogleAuth()  
    {
        if (!$this->objGoogleClient)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'google client == null. cant return url');
            return;
        }
        
        return $this->objGoogleClient->createAuthUrl();

    }    

   /*****************************************
     * 
     *  ABSTRACT FUNCTIONS
     * 
     *****************************************/
    
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
