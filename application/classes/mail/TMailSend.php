<?php


namespace dr\classes\mail;

include_once APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'PHPMailer.php';
include_once APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Exception.php';
// include_once APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'POP3.php';
include_once APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'SMTP.php';
// include_once APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'OAuth.php';



/**
 * Description of TMailSend
 * 
 * wrapper class for PHPMailer , but with flood detection
 * 
 * 14 dec 2020: created
 * 18 jan 2023: TMailSend: filter_var() statements for email addresses added;
 * 
 * @todo write proper standalone mail class to replace PHPMailer
 * @author drenirie
 */
class TMailSend
{
    private $objMail = null;
    private $sErrorMessage = '';

    const SESSION_KEY_FLOODPROTECTION_TIMESTAMP = 'TMailSend_floodprotection_timestamp'; // $_SESSION[] key of the timestamp of the last sent email 
    const SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER = 'TMailSend_floodprotection_emailcounter'; // $_SESSION[] key of the number emails sent since last timestamp
    const FLOODPROTECTION_WITHIN_SECS = 30;//when is something considered flood? After how many seconds is someone allowed to send email again?
    const FLOODPROTECTION_MAX_EMAILS = 3;//how many emails before it is considered flood?

    public function  __construct()
    {
        $this->objMail = new \PHPMailer\PHPMailer\PHPMailer();

        //init and session timeout prevention
        if (!isset($_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_TIMESTAMP]))
            $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_TIMESTAMP] = 0;
        if (!isset($_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER]))
            $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER] = 0;    
        
        // $this->objMail->Mailer = 'smtp';
        // $this->objMail->Host = 'smtp.bhosted.nl';
        // $this->objMail->Port = 465;
        // $this->objMail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        // $this->objMail->SMTPAuth = true;
        // $this->objMail->Username = '';
        // $this->objMail->Password = '';


    }

    public function __destruct()
    {
       unset($this->objMail);
    }


    /**
     * set email address from sender
     *
     * @param string $sEmailAddress
     * @param string $sName
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function setFrom($sEmailAddress, $sName = '')
    {
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);
        return $this->objMail->setFrom($sEmailAddress, $sName);
    }

    /**
     * alias for setFrom()
     */
    public function setSender($sEmailAddress, $sName = '')
    {
        $this->setFrom($sEmailAddress, $sName);
    }


    /**
     * add email address from receipient
     *
     * @param string $sEmailAddress
     * @param string $sName
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addTo($sEmailAddress, $sName = '')
    {
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);
        return $this->objMail->addAddress($sEmailAddress, $sName);
    }


    /**
     * set to: field (only one emailadress)
     *
     * use ->addTo() to add multiple
     * 
     * @param string $sEmailAddress
     * @param string $sName
     * @return bool
     */
    public function setTo($sEmailAddress, $sName = '')
    {
        $this->objMail->clearAddresses();
        return $this->objMail->addAddress($sEmailAddress, $sName);
    }


    /**
     * add email address from sender (reply-to)
     *
     * @param string $sEmailAddress
     * @param string $sName
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addReplyTo($sEmailAddress, $sName = '')
    {
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);
        return $this->objMail->addReplyTo($sEmailAddress, $sName);
    }    

    /**
     * add email address CC receipient 
     *
     * @param string $sEmailAddress
     * @param string $sName
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addCC($sEmailAddress, $sName = '')
    {
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);
        return $this->objMail->addCC($sEmailAddress, $sName);
    }      
    

    /**
     * set cc: field (only one emailadress)
     *
     * use ->addCC() to add multiple
     * 
     * @param string $sEmailAddress
     * @param string $sName
     * @return bool
     */
    public function setCC($sEmailAddress, $sName = '')
    {
        $this->objMail->clearCCs();
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);
        return $this->objMail->addCC($sEmailAddress, $sName);
    }

    
    /**
     * add email address BCC receipient 
     *
     * @param string $sEmailAddress
     * @param string $sName
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addBCC($sEmailAddress, $sName = '')
    {
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);
        return $this->objMail->addCC($sEmailAddress, $sName);
    }     

    /**
     * set bcc: field (only one emailadress)
     *
     * use ->addBCC() to add multiple
     * 
     * @param string $sEmailAddress
     * @param string $sName
     * @return bool
     */
    public function setBCC($sEmailAddress, $sName = '')
    {
        $this->objMail->clearBCCs();
        $sEmailAddress = filter_var($sEmailAddress, FILTER_SANITIZE_EMAIL);        
        return $this->objMail->addBCC($sEmailAddress, $sName);
    }    

    /**
     * add attachment
     *
     * @param string $sFilePath
     * @param string $sName
     * 
     * @return bool true on success
     */
    public function addAttachment($sFilePath, $sName = '')
    {
        return $this->objMail->addAttachment($sFilePath, $sName);
    }

    /**
     * set attachment
     *
     * use ->addAttachment() to add multiple
     * 
     * @param string $sFilePath
     * @param string $sName
     * 
     * @return bool true on success
     */
    public function setAttachment($sFilePath, $sName = '')
    {
        $this->objMail->clearAttachments();
        return $this->objMail->addAttachment($sFilePath, $sName);
    }    

    /**
     * What is the subject of the email
     *
     * @param string $sEmailTopic
     * @return void
     */
    public function setSubject($sEmailTopic)
    {
        $this->objMail->Subject = $sEmailTopic;
    }

    /**
     * What is the subject of the email
     *
     * @param string $sBody
     * @return void
     */
    public function setBody($sBody, $bIsHTML)
    {
        if (!is_bool($bIsHTML))
             $bIsHTML = false;

        $this->objMail->isHTML($bIsHTML);

        if ($bIsHTML)
            $this->objMail->Body = $sBody;
        else
            $this->objMail->AltBody = $sBody;
    }    

    /**
     * send email
     *
     * @return boolean
     */
    public function send()
    {        
        //====detect flood

        //flood period passed?
        // tracepoint('nieuwe send()');
        // vardump(date('d-m-Y h:i', $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_TIMESTAMP]));        
        // vardump($_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER]);        
        if ($_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_TIMESTAMP] + TMailSend::FLOODPROTECTION_WITHIN_SECS < time()) 
        {
            // tracepoint('pipo' );
            // vardump(date('d-m-Y h:i', $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_TIMESTAMP]));
            $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_TIMESTAMP] = time();//register new timestamp
            $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER] = 0;//reset email counter
        }
        else //not passed
        {
            $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER] = $_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER] + 1;//add emails to email counter
        }

        //no flood detected
        if ($_SESSION[TMailSend::SESSION_KEY_FLOODPROTECTION_EMAILCOUNTER] < TMailSend::FLOODPROTECTION_MAX_EMAILS)        
        {
            //send mail
            return $this->objMail->send();
        }
        else //flood detected
        {
            $this->sErrorMessage = transg(__CLASS__.'_errormessage_flooddetected', 'Sorry, you sent too many emails at once');

            return false;
        }

    }

    /**
     * returns latest error message.
     * The error message of TMailSend has priority over that of PHPMailer
     *
     * @return void
     */
    public function getErrorMessage()
    {
        if ($this->sErrorMessage)
            return $this->sErrorMessage;
        if ($this->objMail->ErrorInfo)
            return $this->objMail->ErrorInfo;
        
        return '';
    }

    /**
     * clear all to addresses
     *
     * @return void
     */
    public function resetTo()
    {
        $this->objMail->clearAddresses();
    }

   /**
     * clear all cc addresses
     *
     * @return void
     */
    public function resetCC()
    {
        $this->objMail->clearCCs();
    }    

   /**
     * clear all bcc addresses
     *
     * @return void
     */
    public function resetBCC()
    {
        $this->objMail->clearBCCs();
    }    

}

?>