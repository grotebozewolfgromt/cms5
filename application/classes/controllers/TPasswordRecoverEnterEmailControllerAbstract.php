<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;

use dr\classes\dom\FormGenerator;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputEmail;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\mail\TMailSend;
use dr\classes\models\TSysUsersAbstract;
use dr\classes\types\TDateTime;

/**
 * Description of TPasswordRecoverEnterEmailControllerAbstract
 *
 * This is the form shown to the user that prompts the user to enter email to recover their password
 * 
 * This is a CMS unaware template, so you can reuse it for the frontend of your site (i.e. webshop login)
 * 
 * @author dennis renirie
 * 22 apr 2024: TPasswordRecoverEnterEmailControllerAbstract created
 *
 */

abstract class TPasswordRecoverEnterEmailControllerAbstract extends TControllerAbstract
{
    protected $objAuthenticationSystem = null;//TAuthenticationSystemAbstract();

    private $objFormPasswordRecover = null;//dr\classes\dom\FormGenerator

    protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    protected $objEdtEmailAddress = null;//dr\classes\dom\tag\form\InputText


    public function __construct()
    {
        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Loaded page: '.getURLThisScript());

        //parent::__construct();//--> I EXPLICITYLY DISABLE __construct() of the parent because it calls the rendering of templates, that results in headers being sent, and I can't do header redirects anymore! Instead I call render() and populate() manually

        //execute
        $this->populate();//manually call populate() BEFORE handleLoginLogout(), instead of parent::__construct() --> we need the form object and the editboxes objects in handleLoginLogout()
        $this->handlePassworRecoverEnterEmail(); //--> this might die() the script and header redirect to other page when trying to login!!! In other words: it will never reach code below this function call.
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


    protected function populate()
    {
 
        $this->objFormPasswordRecover = new FormGenerator('passwordrecover-'.$this->objAuthenticationSystem->getControllerID(), $this->objAuthenticationSystem->getURLPasswordRecoverEnterEmail());
        $this->objFormPasswordRecover->setRecaptchaV3Use($this->objAuthenticationSystem->getUseRecapthaLogin());

            //form token
        // $this->objHDFormToken = new InputHidden();
        // $this->objHDFormToken->setNameAndID('hdFT');
        // $this->objHDFormToken->setValue($this->generateFormToken()); //we set the value here, otherwise we have to create a new method() for only 1 line
        // $this->getFormPasswordRecover()->add($this->objHDFormToken);    

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
        $this->getFormPasswordRecover()->add($this->objEdtEmailAddress, '', transg('loginform_field_emailaddress', 'email address')); 
        

        //submit
        /*
        if (!$this->getUseRecapthaLogin())
        {
            $this->objSubmit = new InputSubmit();    
            $this->objSubmit->setValue(transg('passwordrecoverform_button_sendemail', 'send email'));
            $this->objSubmit->setNameAndID('btnSubmit');
            $this->objSubmit->setClass('input_type_button');
            $this->getFormPasswordRecover()->add($this->objSubmit);
        }
        else
        {
            $this->objSubmitRecaptcha = new InputButton();    
            $this->objSubmitRecaptcha->setValue(transg('passwordrecoverform_button_sendemail', 'send email'));
            $this->objSubmitRecaptcha->setNameAndID('btnSubmit');
            $this->objSubmitRecaptcha->setClass('g-recaptcha input_type_button');
            $this->objSubmitRecaptcha->setDataSitekey(APP_GOOGLE_RECAPTCHAV3_SITEKEY);
            $this->objSubmitRecaptcha->setDataCallback('onSubmitRecaptcha');
            $this->objSubmitRecaptcha->setDataAction('submit');
            $this->getFormPasswordRecover()->add($this->objSubmitRecaptcha);
        }
        */
        if (!$this->objAuthenticationSystem->getUseRecapthaLogin())
            $this->objSubmit = new InputSubmit();
        else
            $this->objSubmit = new InputButton();
        $this->objSubmit->setValue(transg('passwordrecoverform_button_sendemail', 'send email')); 
        $this->objSubmit->setNameAndID('btnSubmit');     
        $this->objSubmit->setClass('fullwidthtag');   
        $this->getFormPasswordRecover()->makeRecaptchaV3SubmitButton($this->objSubmit);  
        $this->getFormPasswordRecover()->add($this->objSubmit);                    

    
    
    }

    /**
     * the user tries to log in, handle the authentication and send to the right pages (success or failed)
     * if form is not submitted, this function does nothing.
     */
    public function handlePassworRecoverEnterEmail()
    {
        $bResult = false;
        $sEmailAddress = '';
        $sHTMLContentMain = ''; //for email skin
        $sHTMLEmailTemplateSkin = '';
        $sNewEmailTokenDecrypted = '';
        $sHashedUsername = '';

        //form submitted?
        if ($this->getFormPasswordRecover()->isFormSubmitted())
        {            
            if ($this->getFormPasswordRecover()->isValid()) 
            {
                /*
                if (!$this->isRecaptchaValid())
                {
                    $this->registerPasswordReset(); //needs to be registered BEFORE the flood detection                
                    $this->setMessageError(transg('authenticationsystem_errormessage_passwordrecoverenteremail_recaptchafailed', 'Recaptcha failed sorry'));             
                    preventTimingAttack(100,600);
                    return false;
                }
                */
    
                $this->objAuthenticationSystem->registerPasswordReset($this->objEdtEmailAddress->getValueSubmitted()); //needs to be registered BEFORE the flood detection                
                
                if ((!$this->objAuthenticationSystem->detectFloodPasswordResetAttempts($this->objEdtEmailAddress->getValueSubmitted())) && (!$this->objAuthenticationSystem->detectFloodGeneric()))
                {           
                    $objUsersTemp = null;
                    $iCountMatchesEmailAddresses = 0;
                    $objUsersTemp = $this->objAuthenticationSystem->getUsers()->getCopy();
                    $sEmailAddress = $this->objEdtEmailAddress->getValueSubmitted();

                    //the html validator is supposed to check it before, but for extra security: we check it again
                    if (isValidEmail($sEmailAddress))
                    {                 
                        $objUsersTemp->clear();
                        $objUsersTemp->find(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, $objUsersTemp->generateFingerprintEmail($sEmailAddress));
                        $objUsersTemp->loadFromDB();

                        //from fingerprints: count email address matches in database
                        while ($objUsersTemp->next())
                        {   
                            ///I want to make 100% sure that the emailaddress matches the encrypted email address, 
                            //different email addresses technically could generate the same fingerprint (although rare)
                            if ($objUsersTemp->isMatchUncryptedValue(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, $sEmailAddress))
                                $iCountMatchesEmailAddresses++;
                        }


                        //if email found in database
                        if ($iCountMatchesEmailAddresses > 0)
                        {
                            $objTempExpireToken = new TDateTime(time());
                            $objTempExpireToken->addHour(1);        
                            $sNewEmailTokenDecrypted = password_hash(generatePassword(10, 50), PASSWORD_DEFAULT); //the hash to add extra randomness
                            $sHashedUsername = password_hash($objUsersTemp->getUsername(), PASSWORD_DEFAULT); 
                            $objUsersTemp->setEmailTokenDecrypted($sNewEmailTokenDecrypted);
                            $objUsersTemp->setEmailTokenExpires($objTempExpireToken);
                            $objUsersTemp->saveToDB();

                            //set email variables
                            $sApplicationName = $this->objAuthenticationSystem->getApplicationName();
                            $sURLPasswordRecover = $this->objAuthenticationSystem->getURLPasswordRecoverEnterPassword();
                            $sURLPasswordRecover = addVariableToURL($sURLPasswordRecover,TAuthenticationSystemAbstract::GETARRAYKEY_USER_RANDOMID, $objUsersTemp->getRandomID());
                            $sURLPasswordRecover = addVariableToURL($sURLPasswordRecover,TAuthenticationSystemAbstract::GETARRAYKEY_USER_EMAILTOKEN, $sNewEmailTokenDecrypted);
                            $sURLPasswordRecover = addVariableToURL($sURLPasswordRecover,TAuthenticationSystemAbstract::GETARRAYKEY_USER_USERNAME, $sHashedUsername);
                            $sUserName = $objUsersTemp->getUsername();
                            $sHTMLContentMain = renderTemplate($this->getPathEmailTemplatePasswordRecover(), get_defined_vars());
                            $sHTMLEmailTemplateSkin = renderTemplate($this->getPathEmailSkin(), get_defined_vars());
                        
                            
                            $objMail = new TMailSend();
                            $objMail->setTo($sEmailAddress, $sUserName);
                            $objMail->setFrom($this->objAuthenticationSystem->getMailbotFromEmailAddress(), $this->objAuthenticationSystem->getMailbotFromName());
                            $objMail->setSubject(transg('authenticationsystem_passwordrecover_email_subject_resetpassword', 'Your password reset request'));
                            $objMail->setBody($sHTMLEmailTemplateSkin, true);
                            if ($objMail->send())
                            {
                                $bResult = true;//we want to let the user to know that everything is ok
                            }
                            else
                            {
                                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_resetpassword_message_emailerror', 'An error has occured sending your email.'));
                                error_log('EMAIL ERROR: '.__CLASS__.' '.$objMail->getErrorMessage());
                                $bResult = false;//we want to let the user know: error
                            }
                            
                            
                        }
                        else //email NOT found in DB
                        {
                            //we DON'T show a message that the email wasn't found in the database for security reasons
                            //someone could 'peek' in the database this way and then brute force attack user accounts with that email address
                            // $this->sMessageError = transg('authenticationsystem_errormessage_emailnotindatabase', 'Email address does not exist in database');                        
                            //the message to the user is send later (see below)

                            preventTimingAttack(10,450);

                            $bResult = true;//we want to let the user to know that everything is ok
                        }

                        //deliberately it says also OK when emailaddress was NOT found in database (security reasons described above)
                        if ($bResult)
                        {
                            $this->objFormPasswordRecover = null;
                            $this->objAuthenticationSystem->setMessageNormal(transg('authenticationsystem_passwordrecover_message_emailsent', 'An email was sent to the registered user (if the user is present in our system)'));
                        }
                        else
                            $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_passwordrecover_message_somethingwrong', 'Oops, something went wrong'));

                    }
                    else //this reeks of an attack
                    {
                        $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_passwordrecover_message_handlepasswordrecover_emailaddressnotvalid', 'This is not a valid email address'));
                        $bResult = false;  
                    }

                } //end  no flood detection
                else    //flood detected       
                {            
                    $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_message_handlepasswordrecover_flooddetected', 'Submission not processed for security reasons'));
                    $this->objFormPasswordRecover = null; //prevent attacker from recovering password
                    $bResult = false; 
                }   
       
            }
            else
            {
                //showing a form is always quicker than database actions when successfull, 
		        //to keep some mystery about what is going on behind the scenes, slow down the form errors
		        preventTimingAttack(0,200);

                $this->objAuthenticationSystem->setMessageError(transg('authenticationsystem_errormessage_handlepasswordresetenteremail_correctinputerrors', 'Please correct input errors')); 
                $bResult = false;               
            }
        }
        
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
