<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;

use dr\classes\dom\FormGenerator;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputEmail;
use dr\classes\dom\tag\form\InputHidden;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\validator\TCharacterWhitelist;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRepeatFieldValue;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TStrongPassword;
use dr\classes\mail\TMailSend;
use dr\classes\models\TSysUsersAbstract;
use dr\classes\types\TDateTime;

/**
 * Description of TPasswordRecoverEnterNewPassword
 *
 * This is the form shown to the user after he clicks on the link in the email.
 * This prompts him to enter another password
 * 
 * This is a CMS unaware template, so you can reuse it for the frontend of your site (i.e. webshop login)
 * 
 * @author dennis renirie
 * 22 apr 2024: TPasswordRecoverEnterEmailControllerAbstract created
 *
 */

abstract class TPasswordRecoverEnterNewPasswordControllerAbstract extends TControllerAbstract
{
    protected $objAuthenticationSystem = null;//TAuthenticationSystemAbstract();

    private $objFormPasswordRecover = null;//dr\classes\dom\FormGenerator

    protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    protected $objHdUserRandomID = null;//dr\classes\dom\tag\form\InputHidden
    protected $objHdEmailToken = null;//dr\classes\dom\tag\form\InputHidden
    protected $objHdUsernameHashed = null;//dr\classes\dom\tag\form\InputHidden
    protected $objEdtPassword = null;//dr\classes\dom\tag\form\InputPassword
    protected $objEdtPasswordRepeat = null;//dr\classes\dom\tag\form\InputPassword


    public function __construct()
    {
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Loaded page: '.getURLThisScript());

        //parent::__construct();//--> I EXPLICITYLY DISABLE __construct() of the parent because it calls the rendering of templates, that results in headers being sent, and I can't do header redirects anymore! Instead I call render() and populate() manually

        //execute
        $this->populate();//manually call populate() BEFORE handleLoginLogout(), instead of parent::__construct() --> we need the form object and the editboxes objects in handleLoginLogout()
        $this->handlePassworRecoverEnterPassword(); //--> this might die() the script and header redirect to other page when trying to login!!! In other words: it will never reach code below this function call.
        $this->render();//manually call populate() AFTER handleLoginLogout(), instead of parent::__construct() --> it renders http headers
    }



    /**
     * 
     * @return FormGenerator
     */
    public function getFormPasswordRecover()
    {
        return $this->objFormPasswordRecover;
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
        $objFormPasswordRecover = $this->objFormPasswordRecover;
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
     * generate the form object and all the UI elements to recover a password (=password + button)
     * 
     */
    protected function populate()
    {
        $this->objFormPasswordRecover = new FormGenerator('passwordrecover-'.$this->objAuthenticationSystem->getControllerID(), $this->objAuthenticationSystem->getURLPasswordRecoverEnterPassword());
        $this->objFormPasswordRecover->setRecaptchaV3Use($this->objAuthenticationSystem->getUseRecapthaLogin());

            //form token
        // $this->objHDFormToken = new InputHidden();
        // $this->objHDFormToken->setNameAndID('hdFT');
        // $this->objHDFormToken->setValue($this->generateFormToken()); //we set the value here, otherwise we have to create a new method() for only 1 line
        // $this->getFormPasswordRecover()->add($this->objHDFormToken);    

            //user randomid
        $this->objHdUserRandomID = new InputHidden();
        $this->objHdUserRandomID->setNameAndID('hdID1'); //deliberately vague name
        $this->objHdUserRandomID->setValueSubmitted($this->getFormPasswordRecover()->getForm()->getMethod());//bring over the values from the old (failed) form to the new form
        if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID]))
            $this->objHdUserRandomID->setValue($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID]);
        $this->getFormPasswordRecover()->add($this->objHdUserRandomID);                 

            //email token
        $this->objHdEmailToken = new InputHidden();
        $this->objHdEmailToken->setNameAndID('hdID2'); //deliberately vague name
        $this->objHdEmailToken->setValueSubmitted($this->getFormPasswordRecover()->getForm()->getMethod());//bring over the values from the old (failed) form to the new form
        if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_EMAILTOKEN]))
            $this->objHdEmailToken->setValue($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_EMAILTOKEN]);            
        $this->getFormPasswordRecover()->add($this->objHdEmailToken);                 
    
            //hashed username
        $this->objHdUsernameHashed = new InputHidden();
        $this->objHdUsernameHashed->setNameAndID('hdID3'); //deliberately vague name
        $this->objHdUsernameHashed->setValueSubmitted($this->getFormPasswordRecover()->getForm()->getMethod()); //bring over the values from the old (failed) form to the new form
        if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_USERNAME]))
            $this->objHdUsernameHashed->setValue($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_USERNAME]);            
        $this->getFormPasswordRecover()->add($this->objHdUsernameHashed);                 

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
        $this->getFormPasswordRecover()->add($this->objEdtPassword, '', transg('passwordrecover_field_password', 'password'));         

            //confirm password
        $this->objEdtPasswordRepeat = new InputPassword();
        $this->objEdtPasswordRepeat->setNameAndID('edtPasswordConfirm');
        $this->objEdtPasswordRepeat->setClass('fullwidthtag');                 
        $this->objEdtPasswordRepeat->setRequired(true); 
        $objValidator = new TRepeatFieldValue('', $this->objEdtPassword);
        $this->objEdtPasswordRepeat->addValidator($objValidator);                
        $this->getFormPasswordRecover()->add($this->objEdtPasswordRepeat, '', transg('createaccount_field_passwordrepeat', 'Repeat password'));         


        //submit
        if (!$this->objAuthenticationSystem->getUseRecapthaLogin())
            $this->objSubmit = new InputSubmit();
        else
            $this->objSubmit = new InputButton();
        $this->objSubmit->setValue(transg('passwordrecoverform_button_changepassword', 'change password')); 
        $this->objSubmit->setNameAndID('btnSubmit');     
        $this->objSubmit->setClass('fullwidthtag');   
        $this->getFormPasswordRecover()->makeRecaptchaV3SubmitButton($this->objSubmit);  
        $this->getFormPasswordRecover()->add($this->objSubmit);        
    }    

    /**
     * handle password recovery: when user has to enter or just entered a new password
     */
    public function handlePassworRecoverEnterPassword()
    {
        $bResult = true;        
        $iUserRandomID = 0;
        $sEmailTokenDecrypted = '';
        $sUsernameHashed = '';

        //we want to run all the checks on form called and form submitted to prevent fraud
        if ($this->getFormPasswordRecover()->isFormSubmitted())
        {

            //get submitted values for later
            if (is_numeric($this->objHdUserRandomID->getValueSubmittedAsInt())) //force numeric, otherwise will default to 0
                $iUserRandomID = $this->objHdUserRandomID->getValueSubmittedAsInt();
            $sEmailTokenDecrypted = $this->objHdEmailToken->getValueSubmitted();
            $sUsernameHashed = $this->objHdUsernameHashed->getValueSubmitted();


            //check valid form 
            if (!$this->getFormPasswordRecover()->isValid()) 
            {
                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_passwordrecover_enterpassword_correctinput', 'Please correct input errors'));
                preventTimingAttack(100,400);                
                return false; //directly go away!
            }             

            
        }
        else
        {
            if (isset($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID]))
            {
                if (is_numeric($_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID])) //force numeric, otherwise will default to 0
                    $iUserRandomID = $_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID];
                $sEmailTokenDecrypted = $_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_EMAILTOKEN];
                $sUsernameHashed = $_GET[TAuthenticationSystemAbstract::GETARRAYKEY_USER_USERNAME];
            }
            else
            {
                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_passwordrecover_enterpassword_GETarrayUserIDempty', 'Sorry, couldn\'t identify user'));//I want to be discrete about the exact reason
                return false;
            }
            
        }


        //we start here with all the checks
        if ((!$this->objAuthenticationSystem->detectFloodPasswordResetAttempts()) && (!$this->objAuthenticationSystem->detectFloodGeneric())) //we only want password attempts and generic attempts, otherwise when you have exceeded max inlog attempts it will also block here
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

                        //form submitted? then save the password
                        if ($this->getFormPasswordRecover()->isFormSubmitted())
                        {
                            $objUsersTemp->setEmailTokenEmpty();
                            $objUsersTemp->setEmailTokenExpires();
                            $objUsersTemp->setPasswordDecrypted($this->objEdtPassword->getValueSubmitted());
                            if ($objUsersTemp->saveToDB())
                            {
                                $this->objAuthenticationSystem->setMessageNormal(transg('authenticationsystem_message_handlepasswordrecover_passwordchanged', 'Password successfully changed!')); //we are deliberately vague for security reasons
                                $bResult = true;
                            }
                            else
                            {
                                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_message_handlepasswordrecover_errorsave_passwordnotchanged', 'Error saving. Password NOT changed!')); //we are deliberately vague for security reasons
                                $bResult = false;    
                            }
                        }
                    }
                    else // email token expired
                    {
                        $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlepasswordrecover_emailtokenexpired', 'Sorry, your request timed out!')); 
                        $bResult = false;        
                    }
                }
                else //login token not correct
                {
                    $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlepasswordrecover_emailtokennotcorrect', 'Something went wrong with changing your password. Password NOT changed!')); //we are deliberately vague for security reasons
                    $bResult = false;    
                }
            }
            else //no records found in db
            {
                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlepasswordrecover_usernotfound', 'Something went wrong with the user. Password NOT changed!')); //we are deliberately vague for security reasons
                $bResult = false;
            }

        } //end  no flood detection
        else    //flood detected       
        {            
            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_erroressage_handlepasswordrecover_flooddetected', 'Password NOT changed! You tried to change your password too many times.'));
            $this->objFormPasswordRecover = null; //prevent attacker from recovering password
            $bResult = false; 
        }   

        //remove form if form is submitted OR error occured
        if ($this->getFormPasswordRecover()) //can be null on error
            if ($this->getFormPasswordRecover()->isFormSubmitted() || (!$bResult))
                $this->objFormPasswordRecover = null;
        
        return $bResult;        
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
     * get path of the email password recovery email
     *
     * @return string
     */
    abstract public function getPathEmailTemplatePasswordRecover();    



}
