<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;

use dr\classes\dom\FormGenerator;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputEmail;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\validator\Characterwhitelist;
use dr\classes\dom\validator\Emailaddress;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\validator\Repeatfieldvalue;
use dr\classes\dom\validator\Required;
use dr\classes\dom\validator\StrongPassword;
use dr\classes\mail\TMailSend;
use dr\classes\models\TSysUsersAbstract;
use dr\classes\types\TDateTime;

/**
 * Description of TCreateAccountEmailConfirmedControllerAbstract
 *
 * This is the form shown to the user that prompts the user to enter email, username and password to create an account
 * 
 * This is a CMS unaware template, so you can reuse it for the frontend of your site (i.e. webshop login)
 * 
 * @author dennis renirie
 * 22 apr 2024: TCreateAccountEmailConfirmedControllerAbstract created
 *
 */

abstract class TCreateAccountEmailConfirmedControllerAbstract extends TControllerAbstract
{
    protected $objAuthenticationSystem = null;//TAuthenticationSystemAbstract();

    // protected $objFormCreateAccount = null;//dr\classes\dom\FormGenerator --> protected because we can have some dedicated stuff with extra fields like language in the child class

    // protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    // protected $objEdtUsername = null;//dr\classes\dom\tag\form\InputText
    // protected $objEdtEmailAddress = null;//dr\classes\dom\tag\form\InputText
    // protected $objEdtPassword = null;//dr\classes\dom\tag\form\InputPassword
    // protected $objEdtPasswordRepeat = null;//dr\classes\dom\tag\form\InputPassword


    public function __construct()
    {
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Loading page: '.getURLThisScript());
        //parent::__construct();//--> I EXPLICITYLY DISABLE __construct() of the parent because it calls the rendering of templates, that results in headers being sent, and I can't do header redirects anymore! Instead I call render() and populate() manually

        //execute
        $this->populate();//manually call populate() BEFORE handleLoginLogout(), instead of parent::__construct() --> we need the form object and the editboxes objects in handleLoginLogout()
        $this->handleCreateAccountEmailConfirmed(); //--> this might die() the script and header redirect to other page when trying to login!!! In other words: it will never reach code below this function call.
        $this->render();//manually call populate() AFTER handleLoginLogout(), instead of parent::__construct() --> it renders http headers
    }



    /**
     * return form object
     *
     * @return TForm
     */
    // public function getFormCreateAccount()
    // {
    //     return $this->objFormCreateAccount;
    // } 

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
        // $objFormCreateAccount = $this->objFormCreateAccount;
        $sURLBackToLogin = $this->objAuthenticationSystem->getURLLoginForm();

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


    /**
     * generate the form object
     * 
     */
    public function populate(){}    


    /**
     * handle account creation when user clicked on link in email
     * 
     */
    public function handleCreateAccountEmailConfirmed()
    {
        $bResult = true;        
        $iUserRandomID = 0;
        $sEmailTokenDecrypted = '';
        $sUsernameHashed = '';


        //if creating accounts is switched off
        if ($this->objAuthenticationSystem->getCanAnyoneCreateAccount() === false) 
        {
            // $this->objFormCreateAccount = null;
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_createaccount_errormessage_creatingaccountdisabled', 'Sorry, regretfully it is not possible to create an account at this moment.')); 
            return false;
        }


        //we want to run all the checks on form called and form submitted to prevent fraud
        if (is_numeric($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID])) //force numeric, otherwise will default to 0
            $iUserRandomID = $_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID];
        $sEmailTokenDecrypted = $_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_EMAILTOKEN];
        $sUsernameHashed = $_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_USERNAME];



        //we start here with all the checks
        if (!$this->objAuthenticationSystem->detectFloodAll()) //extra strict flood detection
        {           
            $objUsersTemp = null;
            $objUsersTemp = $this->objAuthenticationSystem->getUsers()->getCopy();

            $objUsersTemp->find(TSysUsersAbstract::FIELD_RANDOMID, $iUserRandomID);
            $objUsersTemp->limitOne();
            $objUsersTemp->loadFromDB();    

            if ($objUsersTemp->count() == 1)
            {

                //check for validity token
                if (
                    ($objUsersTemp->isValidEmailToken($sEmailTokenDecrypted)) 
                    && 
                    (password_verify($objUsersTemp->getUsername(), $sUsernameHashed)) 
                    )
                {
                    //check for email token expiration
                    if ($objUsersTemp->getEmailTokenExpires()->isInTheFuture())
                    {

                        //enable account
                        $objUsersTemp->setEmailTokenEmpty();
                        $objUsersTemp->setEmailTokenExpires();
                        $objUsersTemp->setLoginEnabled(true);   
                        $objUsersTemp->setDeleteAfter();

                        if ((int)(getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS)) == 0)
                        {
                            $objUsersTemp->setPasswordExpires();
                        }
                        else
                        {
                            $objTempExpirePassword = new TDateTime(time());
                            $objTempExpirePassword->addDays((int)getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS));
                            $objUsersTemp->setPasswordExpires($objTempExpirePassword);
                            unset($objTempExpirePassword);
                        }


                        if ($objUsersTemp->saveToDB())
                        {
                            $this->objAuthenticationSystem->setMessageNormal(transg('authenticationsystem_message_handleaccountcreation_accountactivated', 'Account succesfully activated!<br>Click on the link below to log in with your credentials'));
                            $bResult = true;
                        }
                        else
                        {
                            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handleaccountcreation_errorsave_accountnotactivated', 'Error saving. Account NOT activated!')); //we are deliberately vague for security reasons
                            $bResult = false;    
                        }

                    }
                    else // email token expired
                    {
                        $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handleaccountcreation_emailtokenexpired', 'Sorry, your activation timed out! Create another account. The current account is scheduled for deletion.')); 
                        $bResult = false;        
                    }
                }
                else //login token not correct
                {
                    $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handleaccountcreation_emailtokennotcorrect', 'Something went wrong with activating your account.')); //we are deliberately vague for security reasons
                    $bResult = false;    
                }
            }
            else //no records found in db
            {
                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handleaccountcreation_usernotfound', 'Something went wrong with the user. Account NOT activated!')); //we are deliberately vague for security reasons
                $bResult = false;
            }

        } //end  no flood detection
        else    //flood detected       
        {            
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handleaccountcreation_flooddetected', 'Sorry, account NOT activated for security reasons.'));
            // $this->objFormCreateAccount = null; //prevent attacker from creating
            $bResult = false; 
        }   

        
        return $bResult;      
    }

   


   /*****************************************
     * 
     *  ABSTRACT FUNCTIONS
     * 
     *****************************************/

      
}
