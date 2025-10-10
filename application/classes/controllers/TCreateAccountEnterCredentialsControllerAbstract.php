<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;

use dr\classes\dom\FormGenerator;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputEmail;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\validator\TCharacterwhitelist;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRepeatFieldValue;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TStrongPassword;
use dr\classes\mail\TMailSend;
use dr\classes\models\TSysUsersAbstract;
use dr\classes\types\TDateTime;

/**
 * Description of TCreateAccountEnterCredentialsControllerAbstract
 *
 * This is the form shown to the user that prompts the user to enter email, username and password to create an account
 * 
 * This is a CMS unaware template, so you can reuse it for the frontend of your site (i.e. webshop login)
 * 
 * @author dennis renirie
 * 22 apr 2024: TCreateAccountEnterCredentialsControllerAbstract created
 *
 */

abstract class TCreateAccountEnterCredentialsControllerAbstract extends TControllerAbstract
{
    protected $objAuthenticationSystem = null;//TAuthenticationSystemAbstract();

    protected $objFormCreateAccount = null;//dr\classes\dom\FormGenerator --> protected because we can have some dedicated stuff with extra fields like language in the child class

    protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    protected $objEdtUsername = null;//dr\classes\dom\tag\form\InputText
    protected $objEdtEmailAddress = null;//dr\classes\dom\tag\form\InputText
    protected $objEdtPassword = null;//dr\classes\dom\tag\form\InputPassword
    protected $objEdtPasswordRepeat = null;//dr\classes\dom\tag\form\InputPassword


    public function __construct()
    {
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Loaded page: '.getURLThisScript());
        //parent::__construct();//--> I EXPLICITYLY DISABLE __construct() of the parent because it calls the rendering of templates, that results in headers being sent, and I can't do header redirects anymore! Instead I call render() and populate() manually

        //execute
        $this->populate();//manually call populate() BEFORE handleLoginLogout(), instead of parent::__construct() --> we need the form object and the editboxes objects in handleLoginLogout()
        $this->handleCreateAccountEnterCredentials(); //--> this might die() the script and header redirect to other page when trying to login!!! In other words: it will never reach code below this function call.
        $this->render();//manually call populate() AFTER handleLoginLogout(), instead of parent::__construct() --> it renders http headers
    }



    // /**
    //  * return form object
    //  *
    //  * @return TForm
    //  */
    public function getFormCreateAccount()
    {
        return $this->objFormCreateAccount;
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
        $objFormCreateAccount = $this->objFormCreateAccount;
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
     * generate the form object and all the UI elements to create an account
     * (=username, email address, password + button)
     * 
     */
    public function populate()
    {
        $this->objFormCreateAccount = new FormGenerator('createaccount-'.$this->objAuthenticationSystem->getControllerID(), getURLCreateAccountEnterCredentials());
        $this->objFormCreateAccount->setRecaptchaV3Use($this->objAuthenticationSystem->getUseRecapthaLogin());

            
            //username
        $this->objEdtUsername = new InputText();
        $this->objEdtUsername->setNameAndID('edtUsername');
        $this->objEdtUsername->setClass('fullwidthtag');                 
        $this->objEdtUsername->setRequired(true); 
        $this->objEdtUsername->setMaxLength(255);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtUsername->addValidator($objValidator);        
        $objValidator = new TRequired();
        $this->objEdtUsername->addValidator($objValidator);       
        // $objValidator = new TCharacterwhitelist(transcms('form_error_charactersnotallowed', 'One or more characters are not allowed'), TAuthenticationSystemAbstract::CHARSALLOWED_FORMFIELDS);
        // $this->objEdtUsername->addValidator($objValidator);
        $this->objFormCreateAccount->add($this->objEdtUsername, '', transg('createaccountform_field_username', 'Username')); 
    
        

            //email address
        $this->objEdtEmailAddress = new InputEmail();
        $this->objEdtEmailAddress->setNameAndID('edtEmailAddress');
        $this->objEdtEmailAddress->setClass('fullwidthtag');                 
        $this->objEdtEmailAddress->setRequired(true); 
        $this->objEdtEmailAddress->setMaxLength(100);    
        $objValidator = new TEmailAddress();
        $this->objEdtEmailAddress->addValidator($objValidator);        
        $objValidator = new TMaximumLength(100);
        $this->objEdtEmailAddress->addValidator($objValidator);        
        $objValidator = new TRequired();
        $this->objEdtEmailAddress->addValidator($objValidator);       
        $this->objFormCreateAccount->add($this->objEdtEmailAddress, '', transg('createaccountform_field_emailaddress', 'Email address')); 
        

            //password
        $this->objEdtPassword = new InputPassword();
        $this->objEdtPassword->setNameAndID('edtPassword');
        $this->objEdtPassword->setClass('fullwidthtag');                 
        $this->objEdtPassword->setRequired(true); 
        $this->objEdtPassword->setMaxLength(255);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtPassword->addValidator($objValidator);        
        $objValidator = new TRequired();
        $this->objEdtPassword->addValidator($objValidator);       
        $objValidator = new TStrongPassword();
        $this->objEdtPassword->addValidator($objValidator);        
        $objValidator = new TCharacterwhitelist(TAuthenticationSystemAbstract::CHARSALLOWED_FORMFIELDS);
        $this->objEdtPassword->addValidator($objValidator);
        $this->objFormCreateAccount->add($this->objEdtPassword, '', transg('createaccount_field_password', 'Password'));         

        
            //confirm password
        $this->objEdtPasswordRepeat = new InputPassword();
        $this->objEdtPasswordRepeat->setNameAndID('edtPasswordConfirm');
        $this->objEdtPasswordRepeat->setClass('fullwidthtag');                 
        $this->objEdtPasswordRepeat->setRequired(true); 
        $objValidator = new TRepeatFieldValue('', $this->objEdtPassword);
        $this->objEdtPasswordRepeat->addValidator($objValidator);                
        $this->objFormCreateAccount->add($this->objEdtPasswordRepeat, '', transg('createaccount_field_passwordrepeat', 'Repeat password'));         
    
        
            //populate by child class
        $this->populateFormCreateAccountEnterCredentialsChild();


        if (!$this->objAuthenticationSystem->getUseRecapthaLogin())
            $this->objSubmit = new InputSubmit();
        else
            $this->objSubmit = new InputButton();
        $this->objSubmit->setValue(transg('createnewaccountform_button_sendemail', 'Submit')); 
        $this->objSubmit->setNameAndID('btnSubmit');     
        $this->objSubmit->setClass('fullwidthtag');   
        $this->objFormCreateAccount->makeRecaptchaV3SubmitButton($this->objSubmit);  
        $this->objFormCreateAccount->add($this->objSubmit);        
        
    }    


    /**
     * if a user tried to create an account but the email address is already in use with another user
     * instead of showing an error (and giving away to the outside world that we have this email address in our database)
     * we send an email to remind the user that they can reset their password
     *
     * @param string $sEmailAddress
     * @param string $sUsername
     * @return void
     */
    private function handleCreateAccountEnterCredentialsEmailaddressExists($sEmailAddress, $sUsername)
    {
        $bResult = false;

        //setting email variables
        $sApplicationName = $this->objAuthenticationSystem->getApplicationName();
        $sURLResetPassword = $this->objAuthenticationSystem->getURLPasswordRecoverEnterEmail();
        $sURLLogin = $this->objAuthenticationSystem->getURLLoginForm();
        $sHTMLContentMain = renderTemplate($this->getPathEmailTemplateCreateAccountEmailExists(), get_defined_vars());
        $sHTMLEmailTemplateSkin = renderTemplate($this->getPathEmailSkin(), get_defined_vars());
                            
        $objMail = new TMailSend();
        $objMail->setTo($sEmailAddress, $sUsername);
        $objMail->setFrom($this->objAuthenticationSystem->getMailbotFromEmailAddress(), $this->objAuthenticationSystem->getMailbotFromName());
        $objMail->setSubject(transg('authenticationsystem_createaccount_email_subject_emailaddressexists', 'Your emailaddress already exists with an associated [applicationname] account', 'applicationname', $this->objAuthenticationSystem->getApplicationName()));
        $objMail->setBody($sHTMLEmailTemplateSkin, true);
        if ($objMail->send())
        {
            $this->objAuthenticationSystem->setMessageNormal(transg('authenticationsystem_createaccount_emailaddressexists_message_emailsent', 'An email was sent to [emailaddress] with instructions.', 'emailaddress', $sEmailAddress));
            $bResult = true;//we want to let the user to know that everything is ok
        }
        else
        {
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_createaccount_emailaddressexists_message_emailerror', 'An error has occured sending your email.'));
            error_log('EMAIL ERROR: '.__CLASS__.' '.$objMail->getErrorMessage());
            $bResult = false;//we want to let the user know: error
        }          

        return $bResult;
    }


    /**
     * handle account creation when user creates a new account
     * 
     * this is done in the cms controller since we want to use some extra features of the cms
     * like language, the default usergroup
     */
    public function handleCreateAccountEnterCredentials()
    {
        $bResult = false;
        $sEmailAddress = '';
        $sNewEmailTokenDecrypted = '';
        $sHashedUsername = '';

        //if creating accounts is switched off
        if ($this->objAuthenticationSystem->getCanAnyoneCreateAccount() === false) 
        {
            $this->objFormCreateAccount = null;
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_createaccount_errormessage_creatingaccountdisabled', 'Regretfully it is not possible to create an account')); 
            return false;
        }

        $this->modelToViewCreateAccountEnterCredentials();

        
        //form submitted?
        if ($this->objFormCreateAccount->isFormSubmitted())
        {

            if ($this->objFormCreateAccount->isValid()) 
            {
                $this->objAuthenticationSystem->registerAccountCreateAttempt($this->objEdtEmailAddress->getValueSubmitted(), $this->objEdtUsername->getValueSubmitted());
                if (!$this->objAuthenticationSystem->detectFloodAll()) //extra strict flood detection
                {                   
                    $objUsersTemp = null;
                    $objUsersTemp = $this->objAuthenticationSystem->getUsers()->getCopy();
                    $sEmailAddress = $this->objEdtEmailAddress->getValueSubmitted();
                    $iCountMatchesEmailAddresses = 0; 

                    if (isValidEmail($sEmailAddress))
                    {                    
                        $objUsersTemp->find(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, $objUsersTemp->generateFingerprintEmail($sEmailAddress));                            
                        $objUsersTemp->loadFromDB();      

                        //from fingerprints: count email address matches in database
                        if ($objUsersTemp->next())
                        {
                            //I want to make 100% sure that the emailaddress matches the encrypted email address, 
                            //different email addresses technically could generate the same fingerprint (although rare)
                            if ($objUsersTemp->isMatchUncryptedValue(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED,  $sEmailAddress))
                                $iCountMatchesEmailAddresses++;
                        }

                        //if email already exists in database
                        //send email with question if you are looking to recover password (safer (you dont let users know there is already an email address in your database) and more user-friendlier solution than an error)
                        if ($iCountMatchesEmailAddresses >=1)
                        {
                            $this->objFormCreateAccount = null;
                            return $this->handleCreateAccountEnterCredentialsEmailaddressExists($objUsersTemp->getEmailAddressDecrypted(), $objUsersTemp->getUsername());
                        }
                        else
                        {
                            $objUsersNew = null; 
                            $objUsersNew = $this->objAuthenticationSystem->getUsers()->getCopy();

                            //is username already taken?
                            if ($objUsersNew->isUsernameTakenDB($this->objEdtUsername->getValueSubmitted()))
                            {
                                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_createaccount_errormessage_usernametaken', 'You can\'t choose this username. Choose another one.')); //we are very discrete about the exact natur of the error for security reasons
                                return false;
                            }

                            $objUsersNew->setUsername($this->objEdtUsername->getValueSubmitted());
                            $objUsersNew->setPasswordDecrypted($this->objEdtPassword->getValueSubmitted());                                
                            $objUsersNew->setEmailAddressDecrypted($this->objEdtEmailAddress->getValueSubmitted());
                            $this->handleCreateAccountEnterCredentialsChild($objUsersNew); //handle child class specifics

                            
                            if ($objUsersNew->saveToDB(true, true)) //save ok
                            {
                                $this->objFormCreateAccount = null;

                                
                                $objTempExpireTokenAndAccount = new TDateTime(time());
                                $objTempExpireTokenAndAccount->addHour(1);
                                $sNewEmailTokenDecrypted = password_hash(generatePassword(10, 50), PASSWORD_DEFAULT); //the hash to add extra randomness
                                $sHashedUsername = password_hash($objUsersNew->getUsername(), PASSWORD_DEFAULT); 
                                $objUsersNew->setEmailTokenDecrypted($sNewEmailTokenDecrypted);
                                $objUsersNew->setEmailTokenExpires($objTempExpireTokenAndAccount);
                                $objUsersNew->setDeleteAfter($objTempExpireTokenAndAccount);//--> schedule account for deletion if user does not activate in time
                                $objUsersNew->saveToDB();
                                
                                //==SEND EMAIL
                                //setting email variables
                                $sApplicationName = $this->objAuthenticationSystem->getApplicationName();
                                $sURLCreateAccountEmailConfirm = getURLCreateAccountEmailConfirm();
                                $sURLCreateAccountEmailConfirm = addVariableToURL($sURLCreateAccountEmailConfirm,TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID, $objUsersNew->getRandomID());
                                $sURLCreateAccountEmailConfirm = addVariableToURL($sURLCreateAccountEmailConfirm,TAuthenticationSystemAbstract::GETARRAYKEY_USER_EMAILTOKEN, $sNewEmailTokenDecrypted);
                                $sURLCreateAccountEmailConfirm = addVariableToURL($sURLCreateAccountEmailConfirm,TAuthenticationSystemAbstract::GETARRAYKEY_USER_USERNAME, $sHashedUsername);
                                $sUserName = $objUsersNew->getUsername();
                                $sHTMLContentMain = renderTemplate($this->getPathEmailTemplateCreateAccountActivate(), get_defined_vars());
                                $sHTMLEmailTemplateSkin = renderTemplate($this->getPathEmailSkin(), get_defined_vars()); 
                            
                                
                                $objMail = new TMailSend();
                                $objMail->setTo($sEmailAddress, $sUserName);
                                $objMail->setFrom($this->objAuthenticationSystem->getMailbotFromEmailAddress(), $this->objAuthenticationSystem->getMailbotFromName());
                                $objMail->setSubject(transg('authenticationsystem_createaccount_email_subject_activateaccount', 'Activate your new [applicationname] account', 'applicationname', $this->objAuthenticationSystem->getApplicationName()));
                                $objMail->setBody($sHTMLEmailTemplateSkin, true);
                                if ($objMail->send())
                                {
                                    $this->objAuthenticationSystem->setMessageNormal(transg('authenticationsystem_createaccount_message_emailsent', 'An activation email was sent to [emailaddress].<br>Click on the link in the email to activate your account and be able to log in.', 'emailaddress', $this->objEdtEmailAddress->getValueSubmitted()));
                                    $bResult = true;//we want to let the user to know that everything is ok
                                }
                                else
                                {
                                    $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_createaccount_message_emailerror', 'An error has occured sending your email.'));
                                    logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'EMAIL ERROR: '.$objMail->getErrorMessage());
                                    $bResult = false;//we want to let the user know: error
                                }                
                                        

                            }
                            else //save failed
                            {
                                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_somethingwrong', 'Oops, something went wrong'));
                                logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'SAVE of user "'.$this->objEdtUsername->getValueSubmitted().'" FAILED!');
                                logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'account creation failed for user (saveToDB() failed)', $this->objEdtUsername->getValueSubmitted());
                                return false;
                            }

                            unset($objUsersNew);

                        } //end: count matches db
                        unset($objUsersTemp);
                    }
                    else //email not valid: this reeks of an attack
                    {
                        $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_message_handlecreateaccount_emailaddressnotvalid', 'This is not a valid email address'));
                        return false; 
                    }                
                }
                else    //flood detected       
                {            
                    $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlecreateaccount_entercredentials_flooddetected', 'Account NOT created. You tried to create too many accounts.'));
                    $this->objFormCreateAccount = null; //prevent attacker creating account
                    return false;
                }     
            }
            else
            {
                //showing a form is always quicker than database actions when successfull, 
		        //to keep some mystery about what is going on behind the scenes, slow down the form errors
		        preventTimingAttack(0,200);

                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlecreateaccount_correctinputerrors', 'Please correct input errors')); 
                $bResult = false;                   
            }
        }

        return $bResult;
    }  

   
    /**
     * transfer database elements to form
     * 
     * (the namegiving is because of consistency with other controllers, there is not a lot of 'model' in 'modelToView')
     */
    protected function modelToViewCreateAccountEnterCredentials()
    {  
        $this->objEdtUsername->setValueSubmitted($this->objFormCreateAccount->getForm()->getMethod());
        $this->objEdtEmailAddress->setValueSubmitted($this->objFormCreateAccount->getForm()->getMethod());



        $this->modelToViewCreateAccountEnterCredentialsChild();
    }


   /*****************************************
     * 
     *  ABSTRACT FUNCTIONS
     * 
     *****************************************/

   /**
     * the path of the email skin
     *
     * @return string
     */
    abstract public function getPathEmailSkin();
    

    /**
     * specific handling of the child class inside handleCreateAccountEnterCredentials();
     *
     * @param TSysUsersAbstract $objUsersNew
     * @return void
     */
    abstract public function handleCreateAccountEnterCredentialsChild($objUsersNew);

    /**
     * transfer database elements to form for child class
     * 
     * (the namegiving is because of consistency with other controllers, there is not a lot of 'model' in 'modelToView')
     */
    abstract protected function modelToViewCreateAccountEnterCredentialsChild();

    /**
     * generate the form object and all the UI elements to create an account
     * (=username, email address, password + button are already created)
     * 
     */
    abstract public function populateFormCreateAccountEnterCredentialsChild();    


    /**
     * get path of the email emailaddress exists template
     *
     * @return string
     */
    abstract public function getPathEmailTemplateCreateAccountEmailExists();

    /**
     * get path of the email temlate to acticate account
     *
     * @return string
     */
    abstract public function getPathEmailTemplateCreateAccountActivate();


  
      
}
