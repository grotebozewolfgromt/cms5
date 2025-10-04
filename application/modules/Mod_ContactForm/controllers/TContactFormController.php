<?php
namespace dr\modules\Mod_ContactForm\controllers;

use dr\classes\controllers\TControllerAbstract;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\Textarea;
use dr\classes\dom\validator\Emailaddress;
use dr\classes\dom\FormGenerator;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\validator\CheckboxChecked;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\validator\Required;
use dr\classes\mail\TMailSend;
use dr\classes\TSpamDetector;
use dr\modules\Mod_ContactForm\Mod_ContactForm;
use dr\modules\Mod_ContactForm\models\TContactFormCategories;
use dr\modules\Mod_ContactForm\models\TContactFormSubmissions;

/**
 * Description of TContactForm
 *
 * This class represents a standard email contact form.
 * Extend this class if you want more fields or functionality
 * This is a standard controller you can use for contact forms
 * 
 * With this class you can:
 * - send emails with this form 
 * - good filters on all fields to prevent email injection
 * - automatically filter on spam
 * - honeypot included
 * - automatic url removal
 * 
 * @todo checkbox optioneel: I understand: only business related and product support inquiries will be answered
 * @todo similar_text() implementeren op herkenning blocked
 * @todo blacklist ip adressen bots bijhouden
  * 
 * HONEYPOT:
 * This class uses a honeypot to 'catch' bots.
 * A honeypot field (firstname) looks like a normal field that a user can fill in,
 * however this field is not shown to a human user (hidden with css).
 * A bot fills out this field, so we know it is spam
 * 
 * with a simple javascript, you can hide the parent elements of the honeypot
        const objNodes = document.getElementsByClassName("letitbee");
        let iNodeCount = objNodes.length;
        for (i = 0; i < iNodeCount; i++) 
        {
            // objNodes[i].parentElement.style.visibility = 'hidden';
            objNodes[i].parentElement.style.height = 0; //otherwise it still takes up space
            objNodes[i].parentElement.style.overflow = 'hidden';
        } 
 * 
 * 
 * @author dennis renirie
 * 11 mrt 2022: TContactFormController created
 * 18 jan 2023: TContactFormController toevoegingen. Senden email werkt nog niet
 * 19 jan 2023: TContactFormController updates + senden email werkt nu
 * 20 jan 2023: TContactFormController optimized: email adres check works, email via template works, fields removed werkt
 * 13 mrt 2024: TContactFormController moved to Mod_ContactForm
 *
 */

abstract class TContactFormController extends TControllerAbstract
{
    //form elements: these are availble to the child class. for example to change css classes or translations or whatever
    protected $objForm = null;//dr\classes\dom\FormGenerator
    protected $objHoneyPot = null;//dr\classes\dom\InputText --> visually hidden with js/css, this field is filled out by bots but is not visible to a human user. this is how we can detect spam
    protected $objFrom = null;//dr\classes\dom\InputText
    protected $objEmailAddress = null;//dr\classes\dom\InputText
    protected $objCategoriesOptionSelect = null; //dr\classes\dom\Option --> <select><option> list with email topics. Either <option> or <input type="text"> will be used
    protected $objTopicInputText = null; //dr\classes\dom\InputText --> if no <select>, then use this edit box for email topic
    protected $objChkNoLinks = null;//dr\classes\dom\Check
    protected $objChkNoSalesMessages = null;//dr\classes\dom\Check
    protected $objMessage = null; //dr\classes\dom\InputTextarea
    protected $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit


    //internal vars
    protected $objFormSubmissions = null; //TContactFormSubmission
    protected $sMessageSuccess = '';//what to show on screen if submit was successful?
    protected $sMessageError =  '';//what to show on screen when there was an error?
    protected $arrBlockedSenderNames = array();//1d array blacklist with bad senders "eric jones"
    protected $arrBlockedSenderAddresses = array();//1d array blacklist with bad senders "eric jones"
    protected $arrBlockedTopics = array();//1d array blacklist with bad subjects/topics

    protected $objSpamName = null;//TSpamDetector class
    protected $objSpamSubject = null;//TSpamDetector class
    protected $objSpamBody = null;//TSpamDetector class

    protected $bHyperLinksAllowed = false;//hyperlinks are regarded as evil. checkbox will be added and hyperlinks will be filtered
    protected $bSalesMessagesAllowed = false;//unsollicited sales messages are regarded as evil. checkbox will be added, the spamdetector will detect sales messages as spam
    protected $bRecaptchaV3Use = false;//use recaptcha v3 for the form?

    const HONEYPOT_FIELDNAME = 'edtFirstName1'; //it needs to be a human readable, we can't name this 'honeypot' otherwise bots will know
    const HONEYPOT_CSSCLASS = 'contactform_firstname1'; //it needs to be a human readable we can't name this 'honeypot'

    const SPAM_ERRORMESSAGE = 'Our systems have determined that your message is likely spam'; //error message when bots/spam detected. This is deliberately very vague not to tip off spammers

    public function __construct()
    {
        $this->arrBlockedSenderNames[] = 'eric jones'; 
        $this->arrBlockedSenderNames[] = 'zoe tan'; 
        $this->arrBlockedSenderNames[] = 'pablo neidig'; 
        $this->arrBlockedSenderNames[] = 'James J. Fair'; 
        $this->arrBlockedSenderNames[] = 'Mark Schaefer'; 

        $this->arrBlockedSenderAddresses[] = 'eric.jones'; //as in "eric.jones.z.mail@gmail.com"
        $this->arrBlockedSenderAddresses[] = 'ericjones'; //as in "ericjonesmyemail@gmail.com"
        $this->arrBlockedSenderAddresses[] = 'marks@nutricompany.com'; //Mark Schaefer

        $this->arrBlockedTopics[] = 'talk talk'; //Turn SurfSurfSurf into Talk Talk Talk
        $this->arrBlockedTopics[] = 'Why not TALK with your leads'; //eric jones spam
        $this->arrBlockedTopics[] = 'how to turn eyeballs into phone calls'; //eric jones spam
        $this->arrBlockedTopics[] = 'Who needs eyeballs you need BUSINESS'; //eric jones spam
        $this->arrBlockedTopics[] = 'Strike when the irons hot'; //eric jones spam
        $this->arrBlockedTopics[] = 'Instead congrats';//eric jones spam
        $this->arrBlockedTopics[] = 'Your site more leads'; //eric jones spam        
        $this->arrBlockedTopics[] = 'Cool website'; //eric jones spam        
        $this->arrBlockedTopics[] = 'There they go';         
        $this->arrBlockedTopics[] = 'Try this get more leads'; //eric jones spam        
        $this->arrBlockedTopics[] = 'how to turn eyeballs into phone calls'; //eric jones spam        
        $this->arrBlockedTopics[] = 'hi'; 
        $this->arrBlockedTopics[] = 'delivery'; //as in "RE: Delivery For You"
        $this->arrBlockedTopics[] = 'Online Pharmacy'; //as in "Canadian Online Pharmacy"
        $this->arrBlockedTopics[] = 'canadian'; //as in "Canadian Online Pharmacy"
        $this->arrBlockedTopics[] = 'shop'; //as in "men online shop"
        $this->arrBlockedTopics[] = 'confirm'; //as in "confirm Your Order"
        $this->arrBlockedTopics[] = 're:'; //implying a reply, which is weird for an online form
        $this->arrBlockedTopics[] = 'FIND YOUR PERFECT PARTNER IN THE USA'; 
        $this->arrBlockedTopics[] = 'Quick question about '; //as in "Quick question about learnhowtoproducemusic.com"

        $this->objSpamName = new TSpamDetector();
        $this->objSpamSubject = new TSpamDetector();
        $this->objSpamBody = new TSpamDetector();
        $this->objFormSubmissions = new TContactFormSubmissions;

        $this->populateInternal();

        //handle form submit
        if ($this->objForm->isFormSubmitted())
            $this->handleFormSubmit();           
        
        parent::__construct();//renders controller

    }

    /**
     * hyperlinks allowed
     * Not allowed will add a checkbox:hyperlinks will be automatically removed from messages. The reader can't see nor click them.
     * 
     * @param bool $bAllowed
     */
    public function setHyperlinksAllowed($bAllowed)
    {
        $this->bHyperLinksAllowed = $bAllowed;
    }

    /**
     * use recapcha v3 on form
     * 
     * @param bool $bUse
     */
    public function setRecaptchaV3Use($bUse)
    {
        $this->bRecaptchaV3Use = $bUse;

        if ($this->objForm) //form object can be null before parent constructor is called in child class constructor
            $this->objForm->setRecaptchaV3Use($bUse);
    }

    /**
     * use recapcha v3 on form
     * 
     * returns alsways false when APP_GOOGLE_RECAPTCHAV3_ENABLE == false;
     */
    public function getRecaptchaV3Use()
    {
        if (APP_GOOGLE_RECAPTCHAV3_ENABLE == false)
            return false;

        return $this->bRecaptchaV3Use;

    }    

    /**
     * handle the form being submitted
     */
    protected function handleFormSubmit()
    {
        //====FORM CHECKS
        if (!$this->objForm->isValid())
        {
            $this->sMessageError = transg(__CLASS__.'_message_contactform_inputerror', 'Form not submitted due to an input error');        
            $this->objForm->setSubmittedValuesAsValues();
            return false;
        }


        //===SPAM CHECKS

        //check email address
        if (!$this->checkEmailAddressForSpam())
            return false;

        //check sender names
        if (!$this->checkSenderNameForSpam())
            return false;

        //check honeypot
        if (!$this->checkHoneyPotForSpam())
            return false;

        //check topics
        if (!$this->checkTopicForSpam())
            return false;
        
        //check body
        if (!$this->checkBodyForSpam())
            return false;
     

        //====SAVE TO DB
        //we need the spam detection first, for the results to be saved to the database
        if (!$this->saveToDB())
            return false;


        //====SEND EMAIL
        if ($this->getSpamScore() < $this->getSpamScoreThreshold())        
        {
            if (!$this->sendEmail())
                return false;

            $this->removeAllFields();//if sending gives error, we want to have the fields, on success we want to remove them

            //show success message
            $this->sMessageSuccess = transg(__CLASS__.'_message_contactform_submitsuccess', 'Thank you.<br>Your message is recorded in our system under ticket #[ticketno]', 'ticketno', $this->objFormSubmissions->getRandomID());
        }
        else
        {
            $this->removeAllFields();//if spam remove all fields

            $this->sMessageError.= transg(__CLASS__.'_message_contactform_submit_likelyspam', 'Our systems have classified your message as spam.<br>We\'ve fingerprinted your device and recorded it as spam message #[ticketno] to train our spam detection algorithms.<br>Your message is deleted and will NOT be read!', 'ticketno', $this->objFormSubmissions->getRandomID());
        }

    }

    protected function checkEmailAddressForSpam()
    {
        $sEmailAddress = '';
        $sEmailAddress = filter_var($this->objEmailAddress->getValueSubmitted(), FILTER_SANITIZE_EMAIL);

        //check dirty characters in email address
        if ($sEmailAddress != $this->objEmailAddress->getValueSubmitted())
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Email address "'.$sEmailAddress.'" contains dirty characters (email address shown in this error message is filtered)');
            $this->sMessageError = transg(__CLASS__.'_message_contactform_emailaddressdirty', TContactFormController::SPAM_ERRORMESSAGE);//be vague about exact cause
            $this->removeAllFields();
            return false;
        }

        //check blocked list
        foreach ($this->arrBlockedSenderAddresses as $sBlocked)
        {
            if (stripos($sEmailAddress, $sBlocked) !== false) //if exists
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Email address "'.$sEmailAddress.'" is on blacklist. Entry: "'.$sBlocked.'"');
                $this->sMessageError = transg(__CLASS__.'_message_contactform_emailaddressonblacklist', TContactFormController::SPAM_ERRORMESSAGE);//be vague about exact cause
                // die(transg('message_contactform_emailaddressonblacklist', 'Suspicious activity detected'));
                $this->removeAllFields();
                return false;
            }
        }

        return true;
    }

    protected function checkSenderNameForSpam()
    {
        $sSender = $this->objFrom->getValueSubmitted();

        //check blocked list
        foreach ($this->arrBlockedSenderNames as $sBlocked)
        {
            if (stripos($sSender, $sBlocked) !== false) //if exists
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Sender name "'.$sSender.'" is on blacklist. Entry: "'.$sBlocked.'"');
                $this->sMessageError = transg(__CLASS__.'_message_contactform_sendernameonblacklist', TContactFormController::SPAM_ERRORMESSAGE);//be vague about exact cause
                $this->removeAllFields();
                return false;
            }
        }        


        //check for spam
        $this->objSpamName->setText($sSender);

        $this->objSpamName->detectURLs();
        $this->objSpamName->detectBlocked();
        $this->objSpamName->detectNumbers();
        $this->objSpamName->detectBadEmojis();
        $this->objSpamName->detectPunctuation();
        $this->objSpamName->detectCAPITALS();  
        //I don't return false because, I want to send the email anyway but add a spam score to the footer



        return true;
    }

    protected function checkHoneyPotForSpam()
    {
        $iPotLen = 0;
        $iPotLen = strlen($this->objHoneyPot->getValueSubmitted());

        if ($iPotLen > 0)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'form Honeypot triggered');
            $this->sMessageError = transg(__CLASS__.'_message_contactform_honeypottriggered', TContactFormController::SPAM_ERRORMESSAGE);//be vague about exact cause
            $this->removeAllFields();
            return false;
        }
            
        return true;
    }

    /**
     * check email subject
     */
    protected function checkTopicForSpam()
    {
        $sTopic = $this->objTopicInputText->getValueSubmitted();

        //check blocked list
        foreach ($this->arrBlockedTopics as $sBlocked)
        {
            if (stripos($sTopic, $sBlocked) !== false) //if exists
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Email subject "'.$sTopic.'" is on blacklist. Entry: "'.$sBlocked.'"');
                $this->sMessageError = transg(__CLASS__.'_message_contactform_emailsubjectonblacklist', TContactFormController::SPAM_ERRORMESSAGE);//be vague about exact cause
                $this->removeAllFields();
                return false;
            }
        }       
        
        //check for spam
        $this->objSpamSubject->setText($sTopic);

        $this->objSpamSubject->detectURLs();
        $this->objSpamSubject->detectBlocked();
        $this->objSpamSubject->detectNumbers();
        $this->objSpamSubject->detectBadEmojis();
        $this->objSpamSubject->detectPunctuation();
        $this->objSpamSubject->detectCAPITALS();  
        $this->objSpamSubject->detectNonLatinCharacterSet(true);  
        //I don't return false because, I want to send the email anyway but add a spam score to the footer


        return true;

    }

    protected function checkBodyForSpam()
    {
        $sBody = '';
        $sBody = $this->objMessage->getValueSubmitted();

        $this->objSpamBody->setText($sBody);

        $this->objSpamBody->detectURLs();
        $this->objSpamBody->detectBlocked();
        $this->objSpamBody->detectNumbers();
        $this->objSpamBody->detectBadEmojis();
        $this->objSpamBody->detectPunctuation();
        $this->objSpamBody->detectCAPITALS();
        $this->objSpamBody->detectNonLatinCharacterSet(true);
        //I don't return false because, I want to send the email anyway but add a spam score to the footer

        return true;
    }

    /**
     * save the form submission to database
     * 
     * @return bool
     */
    protected function saveToDB()
    {
        //checks
        if (!is_numeric($this->objCategoriesOptionSelect->getValueSubmitted()))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'category id not numeric. returned false');
            return false;
        }

        //put in db
        $this->objFormSubmissions->setContactFormCategoryID($this->objCategoriesOptionSelect->getValueSubmittedAsInt());
        $this->objFormSubmissions->setNameDecrypted(filterURL($this->objFrom->getValueSubmitted()));
        $this->objFormSubmissions->setTopic(filterURL($this->objTopicInputText->getValueSubmitted()));
        $this->objFormSubmissions->setEmailAddressDecrypted($this->objEmailAddress->getValueSubmitted());
        $this->objFormSubmissions->setMessage(filterURL($this->objMessage->getValueSubmitted()));
        $this->objFormSubmissions->setSpamLikelyHood($this->getSpamScore());
        $this->objFormSubmissions->setNotesInternal($this->getSpamReport());
        $this->objFormSubmissions->setIPAddressDecrypted($_SERVER['REMOTE_ADDR']);
        if (!$this->objFormSubmissions->saveToDB())
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'objSubmissions->saveToDB() FAILED!!!');
            return false;
        }

        return true;
    }

    /**
     * this function determines the spam score in % (0=low, 100=high)
     * it takes all the scores from the spam detector and returns it as 1 score
     * 
     * @todo load last 1000 manually marked spam messages and compare current message to those manually spam messages
     */
    private function getSpamScore()
    {
        $iScore = 0;

        //spamname
        if ($this->objSpamName->getScore() > $iScore)        
            $iScore = $this->objSpamName->getScore();
        
        //subject
        if ($this->objSpamSubject->getScore() > $iScore)
            $iScore = $this->objSpamSubject->getScore();

        //body
        if ($this->objSpamBody->getScore() > $iScore)
            $iScore = $this->objSpamBody->getScore();

        return $iScore;
    }


    /**
     * return a spam report (only to be seen by administrators)
     * 
     * @return string
     */
    private function getSpamReport()
    {
        $sReport = '';

        $sReport.= '================================================================'."\n";
        $sReport.= 'Spam Report'."\n";
        $sReport.= '================================================================'."\n";
        $sReport.= "\n";

        $sReport.= 'Spam score name: '.$this->objSpamName->getScore().'%'."\n";    
        if ($this->objSpamName->getLog())
            $sReport.= implode("\n",$this->objSpamName->getLog())."\n";
        $sReport.= "\n";

        $sReport.= 'Spam score subject: '.$this->objSpamSubject->getScore().'%'."\n";
        if ($this->objSpamSubject->getLog())
            $sReport.= implode("\n",$this->objSpamSubject->getLog())."\n";
        $sReport.= "\n";    

        $sReport.= 'Spam score body: '.$this->objSpamBody->getScore().'%'."\n";
        if ($this->objSpamBody->getLog())
            $sReport.= implode("\n",$this->objSpamBody->getLog())."\n";
        $sReport.= "\n";

        return $sReport;
    }

    protected function sendEmail()
    {
        $sSubject = '';
        $sEmailAddress = '';
        $sName = '';
        $sBody = '';        
        $iCategoryIndex = 0; //index of array with categories
        
        //==== CONSTRUCT EMAIL

        //CATEGORIES
        if (is_numeric($this->objCategoriesOptionSelect->getValueSubmitted()))
        {
            $objCats = new TContactFormCategories();
            $objCats->limitOne();
            $objCats->loadFromDBByID($this->objCategoriesOptionSelect->getValueSubmittedAsInt());
          
            $sSubject = $objCats->getName().' - ';

            unset($objCats);
        }

        //SUBJECT
        $sSubject.= $this->objTopicInputText->getValueSubmitted();
        if (!$this->bHyperLinksAllowed)
            $sSubject = filterURL($sSubject, '[url removed]', true, false); //no tld detect, because that leads sometimes to false positives

        //NAME
        $sName = $this->objFrom->getValueSubmitted();

        //EMAIL ADDRESS
        $sEmailAddress = filter_var($this->objEmailAddress->getValueSubmitted(), FILTER_SANITIZE_EMAIL);

        //BODY
        $sBody = strip_tags($this->objMessage->getValueSubmitted());
        if (!$this->bHyperLinksAllowed)
            $sBody = filterURL($sBody, '[url removed]', true, false); //no tld detect, because that leads sometimes to false positives            
        $sBody = nl2br($sBody);
        
        $objSpamName = $this->objSpamName;
        $objSpamSubject = $this->objSpamSubject;
        $objSpamBody = $this->objSpamBody;

            
        $sHTMLContentMain = renderTemplate($this->getPathEmailTemplate(), get_defined_vars());
        $sHTMLEmailTemplateSkin = renderTemplate($this->getPathEmailSkin(), get_defined_vars()); 


        //SEND EMAIL
        $objMail = new TMailSend();
        $objMail->setTo(APP_EMAIL_ADMIN);
        $objMail->setFrom($sEmailAddress, $sName);
        $objMail->setSubject($sSubject);
        $objMail->setBody($sHTMLEmailTemplateSkin, true);
        if (!$objMail->send())
        {
            $this->sMessageError = transg(__CLASS__.'_message_contactform_emailsenterror', 'Sorry, an error occured when sending the message'); //I don't mention the word "email" for security reasons
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Error sending email to "'.$sEmailAddress.'": '.$objMail->getErrorMessage());
            $this->objForm->setSubmittedValuesAsValues();
            return false;
        }
        unset($objMail);

        return true;
    }


    protected function populateInternal()
    {
        //====FORM
        $this->objForm = new FormGenerator('frmContactForm', getURLThisScript());
        $this->objForm->setRecaptchaV3Use($this->bRecaptchaV3Use); //as soon as objForm exists, we can set this. This statement needs to be before submit

        //====HONEYPOT
        $this->objHoneyPot = new InputText();
        $this->objHoneyPot->setNameAndID('edtFirstName');
        $this->objHoneyPot->setClass('fullwidthtag letitbee');
        $this->objForm->add($this->objHoneyPot, '', transg(__CLASS__.'_contactform_field_honeypot', 'First name'));//this has to have a normal looking field name to prevent raising any description

        //====SENDER NAME
        $this->objFrom = new InputText();
        $this->objFrom->setNameAndID('edtLastName'); //it is called last name because of the honeypot
        $this->objFrom->setClass('fullwidthtag');
        $this->objFrom->setRequired(true);   
        $this->objFrom->setMaxLength(100);
        $objValidator = new Maximumlength(100);
        $this->objFrom->addValidator($objValidator);    
        $objValidator = new Required();
        $this->objFrom->addValidator($objValidator);    
        $this->objForm->add($this->objFrom, '', transg(__CLASS__.'_contactform_field_fromsender', 'Name'));

        //====EMAIL ADDRESS
        $this->objEmailAddress = new InputText();
        $this->objEmailAddress->setNameAndID('edtEmailAddress');
        $this->objEmailAddress->setClass('fullwidthtag');
        $this->objEmailAddress->setRequired(true);   
        $this->objEmailAddress->setMaxLength(100);
        $objValidator = new Maximumlength(100);
        $this->objEmailAddress->addValidator($objValidator);    
        $objValidator = new Required();
        $this->objEmailAddress->addValidator($objValidator);    
        $objValidator = new Emailaddress(true, true);
        $this->objEmailAddress->addValidator($objValidator);          
        $this->objForm->add($this->objEmailAddress, '', transg(__CLASS__.'_contactform_field_emailaddress', 'Email address'));

        //====CATEGORIES
        $this->objCategoriesOptionSelect = new Select();
        $this->objCategoriesOptionSelect->setNameAndID('optCategory');
        $this->objCategoriesOptionSelect->setClass('fullwidthtag');
        $this->objForm->add($this->objCategoriesOptionSelect, '', transg(__CLASS__.'_contactform_field_category', 'Category'));

        $objCats = new TContactFormCategories();
        $objCats->sort(TContactFormCategories::FIELD_POSITION);
        $objCats->limitNone();
        $objCats->loadFromDB();
        $objCats->generateHTMLSelect('', $this->objCategoriesOptionSelect);
        unset($objCats);


        //====TOPIC
        $this->objTopicInputText = new InputText();
        $this->objTopicInputText->setNameAndID('edtTopic');
        $this->objTopicInputText->setClass('fullwidthtag');        
        $objValidator = new Maximumlength(100);
        $this->objTopicInputText->addValidator($objValidator);    
        $objValidator = new Required();
        $this->objTopicInputText->addValidator($objValidator);    
        $this->objForm->add($this->objTopicInputText,'', transg(__CLASS__.'_contactform_field_topic', 'Topic'));


        //====POPULATE EXTRA FIELDS FROM CHILD CLASS
        $this->populate();

        //====CHECK: NO LINKS
        $this->objChkNoLinks = new InputCheckbox();
        $this->objChkNoLinks->setNameAndID('chkUnderstandLinksNotAllowed');
        $this->objChkNoLinks->setValue('1');
        if (!$this->bHyperLinksAllowed)
        {
            $objValidator = new CheckboxChecked(transg(__CLASS__.'_form_error_requiredfield', 'This is a required field'), '1');
            $this->objChkNoLinks->addValidator($objValidator);    
    
            $this->objForm->add($this->objChkNoLinks, '', transg(__CLASS__.'_contactform_field_understandnolinksallowed', 'I understand: Including hyperlinks will be regarded as spam and automatically deleted.'));           
        }

        //====CHECK: NO COMMERCIAL SALES EMAILS
        $this->objChkNoSalesMessages = new InputCheckbox();
        $this->objChkNoSalesMessages->setNameAndID('chkUnderstandSalesNotAllowed');
        $this->objChkNoSalesMessages->setValue('1');
        if (!$this->bSalesMessagesAllowed)
        {
            $objValidator = new CheckboxChecked(transg(__CLASS__.'_form_error_requiredfield', 'This is a required field'), '1');
            $this->objChkNoSalesMessages->addValidator($objValidator);    
    
            $this->objForm->add($this->objChkNoSalesMessages, '', transg(__CLASS__.'_contactform_field_understandnosalesmessagesallowed', 'I understand: Unsollicited sales messages will be regarded as spam and automatically deleted.'));           
        }        
        
        //====MESSAGE
        $this->objMessage = new Textarea();
        $this->objMessage->setNameAndID('txtMessage');
        $this->objMessage->setClass('fullwidthtag');                
        $objValidator = new Required(transg(__CLASS__.'_form_error_requiredfield', 'This is a required field'));
        $this->objMessage->addValidator($objValidator);    
        $this->objForm->add($this->objMessage, '', transg(__CLASS__.'_contactform_field_message', 'Message'));

        //====RECAPTCHA / SUBMIT
        if ($this->getRecaptchaV3Use())
            $this->objSubmit = new InputButton();    
        else
            $this->objSubmit = new InputSubmit();    
        $this->objSubmit->setValue(transg(__CLASS__.'_contactform_button_submit', 'Submit'));
        $this->objSubmit->setName('btnSubmit');
        $this->objSubmit->setClass('button_normal');
        $this->objForm->makeRecaptchaV3SubmitButton($this->objSubmit);
        $this->objForm->add($this->objSubmit);        
    }

    /**
     * When the form is submitted, remove all fields
     */
    public function removeAllFields()
    {
        $this->objForm = null;
    }


    /**
     * This function adds EARLY BINDING variables to template, which are cached (if cache enabled)
     * (see description on top of this class for more info)
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
        $sTitle = transg('contactform_title', 'Contact Us');
        $sHTMLTitle = transg('contactform_htmltitle', 'Contact Us');
        $sHTMLMetaDescription = transg('contactform_metadescription', 'Please fill out the form to contact us');
        $objForm = $this->objForm;

        $sMessageSuccess = $this->sMessageSuccess;
        $sMessageError= $this->sMessageError;

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
    public function bindVarsLate() {}   




    /**
     * return path of the template for the email message
     *
     * feel free to override!
     *
     * @return string
     */
    public function getPathEmailTemplate()
    {
        //global CMS_CURRENTMODULE;
        return getPathModuleTemplates(CMS_CURRENTMODULE, true).'tpl_emailmessage_toadmin.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.'Mod_ContactForm'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'tpl_emailmessage_toadmin.php';
    }

    /**
     * return path of the template for the email message
     * 
     * feel free to override!
     *
     * @return string
     */
    public function getPathEmailSkin()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_email.php';        
    }  


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        //global CMS_CURRENTMODULE;
        return getPathModuleTemplates(CMS_CURRENTMODULE, true).'tpl_contactform.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.'Mod_ContactForm'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_contactform.php';

    }    

    /*****************************************
     * 
     *  ABSTRACT FUNCTIONS
     * 
     *****************************************/



    /**
     * populate the form with extra fields not included in the abstract class.
     * this function is called in the private function populateInternal
     * in the parent class.
     * 
     * @return void
     */
    abstract public function populate();


   
    /**
     * at what percentage is spam considered spam?
     * 
     * @return int
     */
    abstract public function getSpamScoreThreshold();

}

?>
