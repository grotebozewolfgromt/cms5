<?php

namespace dr\classes\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

/**
 * An abstract class for users website, cms other webapps etc.
 * 
 * when you inherit this class add a link (id) to a usergroup/role
 * 
 * is a user allowed to log in?
 * $obj = new TUsers
 * $obj->loadFromDBByLoginAllowed($user, $pass) --> the record is loaded if success
 * 
 * you can ask a user to set a new password by using:
 * $obj->needsNewPassword()
 * 
 * ENCRYPTION
 * this class supports encryption of passwords in a way you don't have to bother 
 * with them. 
 * Out of security reasons we use one way encryption, 
 * the password_hash() functions of php are used.
 * 
 * You can set passwords, this class will automatically encrypt the password and 
 * stores ONLY the encrypted version into memory.
 * Therefore it is not possible to get a plain password with getPassword(), 
 * only an encrypted one out of security reasons.
 * This method of encryption is done this way out of consistency, because when 
 * loading from database we only have an encrypted version available (and not a plain one).
 * 
 * FAKE PASSWORD FIELDS
 * This class generates fake password fields for security reasons.
 * If someone manages to get access to the database somehome 
 * (injection, hack whatever) they still don't know what the real database
 * password field is, since the fake password fields change too when 
 * you set the real (uncrypted) password.
 * It may be security by obscurity, but with 5 fake fields you need 5x the amount of system resources
 * or 5 times the amount of brute force attempts.
 * 
 * with ->getNoFakePasswordFields() you return the number of fake password fields you want to create.
 * Make sure to create more fake password fields than the index-number of the password field
 * for example:
 * if the real password field is named 'sPassword2', make sure to create at least 2 fake fields
 * 
 * PASSWORD EXPIRATION 
 * if date is zero = no password expiration
 * date in the past = password is expired
 * date in the future = password is not expired
 * password expiration will NOT affect the ability to login, it's merely for GUI
 * purposes to prompt the user to refresh their password
 * (NOT to confuse with LOGIN EXPIRATION which DOES influence the ability to login)
 * 
 * LOGIN EXPIRATION
 * after this date the user can't login anymore. handy for trial users 
 * 
 * USERNAME and EMAIL
 * we don't want to force using emailaddress as username, there are maybe cases, you don't want that
 * 
 * DISABLE LOGIN
 * you can easily kick a user out for the system by switching login-enabled to false.
 * 
 * EMAIL ADDRESS
 * we want to have an email address for password retrieval
 * 
 * AUTO DELETION
 * you can use this class also for automatic expiration.
 * When you use a system that is used based on a recurring fee, you can auto expire
 * a user login when payment is due.
 * Also handy for trial users
 * The users-account class has also the same auto-delete-feature, but that deletes all users in that account 
 * (These features are separated from each other (the one doesn't need the other to function properly))
 * 
 * LOGIN TOKENS
 * we could choose to store username and password in _SESSION and _COOKIE arrays.
 * especially the cookie is dangerous because the user can read it.
 * in stead we use 3 login tokens that represent the equivalent to username and 
 * password. With these 3 tokens you can also log in.
 * Token1 is the id and needs to be unique (stored in plain text in db and _SESSION/_COOKIE)
 * Token2 (stored in plain text in db and _SESSION/_COOKIE)
 * Token3 is stored in plain text in db and encrypted in _SESSION/_COOKIE
 * 
 * 
 * created 10 jan 2020
 * 16 jan 2020: TSysUsersAbstract: extra filter on username, password and tokens parameter in loadFromDBBy.. functions
 * 3 nov 2020: TSysUsersAbstract: loadFromDBByUserLoginAllowed() checkt op checksum, dat voorkomt database tempering, dat is een SUPER VEILIG IDEE!!!
 * 10 nov 2020: TSysUsersAbstract: added fake password fields
 * 11 sept 2021: TUserAbstract: added const for emailfingerprint digest
 * 11 okt 2022: TUserAbstract: added moved to here all the googleid stuff
 */

abstract class TSysUsersAbstract extends TSysModel
{
    //some field names are abbreviated for security reasons
    const FIELD_USERNAME                = 'sUsername';
    const FIELD_PASSWORDENCRYPTED       = 'sPassword1'; //the real password is stored here
    const FIELD_LOGINENABLED            = 'bLoEn'; //only possible to log in when user is enabled
    const FIELD_LOGINEXPIRES            = 'dtLoEx'; //date on which the user can't log in anymore
    const FIELD_LASTLOGIN               = 'dtLastLogin'; //date on which the user can't log in anymore
    const FIELD_PASSWORDEXPIRES         = 'dtPasswordExpires'; //date on which the user needs to set a new password, it will not affect the ability to login!
    const FIELD_EMAILADDRESSENCRYPTED   = 'sEAE';//Email Address Encrypted internally stored in encrypted form - 2 way encrypted email address
    const FIELD_EMAILADDRESSFINGERPRINT = 'sEAF';//Fingerprint Email Address, so we can lookup a record based on email address. We can't salt this, because we need to be able to search on it in the database for password recovery
    const FIELD_UPDATEPERMISSIONS       = 'bUpdatePermissions';//update user permissions (for auth()) on next page load?
    const FIELD_EMAILTOKENENCRYPTED     = 'sEMTO';//temp token for emails 
    const FIELD_EMAILTOKENEXPIRES       = 'dtEMTOEX';//expiration date for email token
    const FIELD_DELETEAFTER             = 'dtDeAf';//expiration date for a user. after this date the user will be deleted by a cron job
    const FIELD_GOOGLEID                = 'sGID'; //google openid 255 chars https://openid.net/specs/openid-connect-core-1_0.html#IDToken (called 'SUB' ID). used for login-with-google

    //localization
    const FIELD_LANGUAGEID              = 'iLanguageID';
    const FIELD_TIMEZONE                = 'sTimezone';
    const FIELD_DATEFORMATSHORT         = 'sDateFormatShort';
    const FIELD_DATEFORMATLONG          = 'sDateFormatLong';
    const FIELD_TIMEFORMATSHORT         = 'sTimeFormatShort';
    const FIELD_TIMEFORMATLONG          = 'sTimeFormatLong';
    const FIELD_WEEKSTARTSON            = 'iWeekStartsOn'; //Monday = 0, Sunday = 7
    const FIELD_THOUSANDSEPARATOR       = 'sThousandSeparator'; //. or ,?
    const FIELD_DECIMALSEPARATOR        = 'sDecimalSeparator'; //. or ,?

    const FIELD_PASSWORDFAKEPREFIX      = 'sPassword';//prefix without the field-number (for the names of fake password fields, because they are numbered)
        
    const SEED_EMAILADDRESSFINGERPRINT ='49fr04jeoiej3iu4f09834rjib34frhuierf9hriu4EF09j34oin34r'; //seed to make it harder to decrypt, when we change it up per class not every table has the same seed
    const DIGEST_EMAILADDRESSFINGERPRINT = ENCRYPTION_DIGESTALGORITHM_SHA512;

    const ENCRYPTION_EMAILADDRESS_PASSPHRASE = '2834hef93hr0ewhjweioweE3df4A+3idmo3nzo#klajd'; //passphrase for the encryption algo

    /**
     * get username
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->get(TSysUsersAbstract::FIELD_USERNAME);
    }

    /**
     * set username
     * 
     * @param string $sName
     */
    public function setUsername($sName)
    {
        $this->set(TSysUsersAbstract::FIELD_USERNAME, $sName);
    }

    /**
     * get the encrypted password for user
     * 
     * @return string
     */
    public function getPasswordEncrypted()
    {
        return $this->get(TSysUsersAbstract::FIELD_PASSWORDENCRYPTED);
    }

    /**
     * set uncrypted password for user
     * setPasswordUncrypted('123') will encrypt the password and stores the 
     * encrypted version of '123' in to the internal data storage
     * 
     * You can reset the tokens also with parameter $bResetTokens:
     * The user may want to change a password because of an unauthorised login
     * to prevent the session and cookie used to log in, we change them
     * 
     * @param string $sPassword
     */
    public function setPasswordDecrypted($sPassword)
    {
        $sEncr = '';
        $sEncr = password_hash($sPassword, PASSWORD_DEFAULT);
        $this->set(TSysUsersAbstract::FIELD_PASSWORDENCRYPTED, $sEncr);   
        
        $iNoFakePasswordFields = $this->getNoFakePasswordFields();
        for ($iIndex = 0; $iIndex <= $iNoFakePasswordFields; $iIndex++)
        {
            $sEncr = password_hash(generatePassword(10, 20), PASSWORD_DEFAULT);
            if (TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex != TSysUsersAbstract::FIELD_PASSWORDENCRYPTED) //prevent overwriting the real password
                $this->set(TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex, $sEncr);   
        }
    }        

    /**
     * get email address
     * 
     * @return string
     */
    // public function getEmailAddress_OLD()
    // {
    //     return $this->get(TSysUsersAbstract::FIELD_EMAILADDRESS_OLD);
    // }

    /**
     * set email address
     * 
     * @param string $sEmail
     */
    // public function setEmailAddress_OLD($sEmail)
    // {
    //     $this->set(TSysUsersAbstract::FIELD_EMAILADDRESS_OLD, $sEmail);
    // }


    /**
     * get email address and decrypt it
     * 
     * @return string
     */
    public function getEmailAddressDecrypted()
    {
        return $this->get(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, '', true);
    }

    /**
     * encrypts and sets email address AND email identifier
     * 
     * @param string $sEmail
     */
    public function setEmailAddressDecrypted($sUncryptedEmail)
    {
        $this->set(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, $sUncryptedEmail, '', true);
        $this->set(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, getFingerprintEmail($sUncryptedEmail, TSysUsersAbstract::SEED_EMAILADDRESSFINGERPRINT, TSysUsersAbstract::DIGEST_EMAILADDRESSFINGERPRINT));
    }


    /**
     * getting email fingerprint from an email address without getting or setting it in this class
     *
     * @param string $sEmailAddress
     * @return void
     */
    public function generateFingerprintEmail($sEmailAddress)
    {
        return getFingerprintEmail($sEmailAddress, TSysUsersAbstract::SEED_EMAILADDRESSFINGERPRINT, TSysUsersAbstract::DIGEST_EMAILADDRESSFINGERPRINT);        
    }


    /**
     * is the user able to log in?
     * 
     * @return boolean
     */
    public function getLoginEnabled()
    {
        return $this->get(TSysUsersAbstract::FIELD_LOGINENABLED);
    }

    /**
     * set if the user able to log in
     * 
     * @param boolean $bAllowed
     */
    public function setLoginEnabled($bAllowed)
    {
        $this->set(TSysUsersAbstract::FIELD_LOGINENABLED, $bAllowed);
    }           

    /**
     * default the datetime object is null, which results in NO EXPIRATION
     * 
     * Can be used for automatic payments, if you do not pay, you can't login
     *
     * @param TDateTime $objDateTime when null then an invalid date (timestamp 0) will be set, so never expires
     */
    public function setLoginExpires($objDateTime = null)
    {
        $this->setTDateTime(TSysUsersAbstract::FIELD_LOGINEXPIRES, $objDateTime);
    }        

    /**
     * when does the user login expire?
     * can return an object with timestamp 0 when NO EXPIRATION date set
     * 
     * Can be used for automatic payments, if you do not pay, you can't login
     * 
     * @return TDateTime
     */
    public function getLoginExpires()
    {
        return $this->get(TSysUsersAbstract::FIELD_LOGINEXPIRES);
    }        
    
    /**
     * set the last time the user logged in
     * 
     * @param TDateTime $objDateTime
     */
    public function setLastLogin($objDateTime = null)
    {
        $this->setTDateTime(TSysUsersAbstract::FIELD_LASTLOGIN, $objDateTime);
    }        

    /**
     * when did the user log in for the last time
     * 
     * @return TDateTime
     */
    public function getLastLogin()
    {
        return $this->get(TSysUsersAbstract::FIELD_LASTLOGIN);
    }       
    
    /**
     * set the time on which user has to set a new password for security reasons
     * (has nothing to do with password_needs_rehash(), needsNewPassword() does that)
     * 
     * if date is zero = no password expiration
     * date in the past = password is expired
     * date in the future = password is not expired
     * password expiration will not affect the ability to login, it's merely for GUI
     * purposes to prompt the user to refresh their password
     *  
     * @param TDateTime $objDateTime
     */
    public function setPasswordExpires($objDateTime = null)
    {
        $this->setTDateTime(TSysUsersAbstract::FIELD_PASSWORDEXPIRES, $objDateTime);
    }        

    /**
     * get the time on which user has to set a new password for security reasons
     * (has nothing to do with password_needs_rehash(), needsNewPassword() does that)
     * 
     * if date is zero = no password expiration
     * date in the past = password is expired
     * date in the future = password is not expired
     * password expiration will not affect the ability to login, it's merely for GUI
     * purposes to prompt the user to refresh their password
     * 
     * @return TDateTime
     */
    public function getPasswordExpires()
    {
        return $this->get(TSysUsersAbstract::FIELD_PASSWORDEXPIRES);
    }        
    
     
   /**
     * update user permissions from db on next page load?
     *
     * @return bool
     */
    public function getUpdatePermissions()
    {
        return $this->get(TSysUsersAbstract::FIELD_UPDATEPERMISSIONS);
    }


    /**
     * update user permissions from db on next page load?
     *
     * @param bool $bUpdate
     * @return void
     */
    public function setUpdatePermissions($bUpdate)
    {
        $this->set(TSysUsersAbstract::FIELD_UPDATEPERMISSIONS, $bUpdate);
    }
    

   /**
     * get email token
     * (token that is sent in emails to verify validity)
     *
     * @return string
     */
    public function getEmailTokenEncrypted()
    {
        return $this->get(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED);
    }


    /**
     * set email token
     * (token that is sent in emails to verify validity)
     *
     * @param string $sToken
     * @return void
     */
    public function setEmailTokenDecrypted($sToken)
    {
        // $this->set(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, $sToken);
        $sEncr = '';
        $sEncr = password_hash($sToken, PASSWORD_DEFAULT);
        $this->set(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, $sEncr);          
    }    

    /**
     * empty the email token field
     * When we don't need email token, we empty it
     *
     * @return boolean
     */
    public function setEmailTokenEmpty()
    {
        $this->set(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, '');          
    }

    /**
     * check if email token field is empty
     *
     * @return boolean
     */
    public function getEmailTokenIsEmpty()
    {
        return $this->get(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED) == '';
    }    

    /**
     * compare an external token (sent via email) with the internal one
     * if they are equal this function returns true, otherwise false
     * if token is empty it returns also false
     * @param string $sUncryptedTokenSentByEmail
     * @return boolean
     */
    public function isValidEmailToken($sUncryptedTokenSentByEmail)
    {
        if ($this->getEmailTokenIsEmpty())
            return false;

        return password_verify($sUncryptedTokenSentByEmail, $this->get(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED));
    }
    
   /**
     * get email token expiration date+time
     * (token that is sent in emails to verify validity)
     *
     * @return TDateTime
     */
    public function getEmailTokenExpires()
    {
        return $this->get(TSysUsersAbstract::FIELD_EMAILTOKENEXPIRES);
    }


    /**
     * email token expiration date+time
     * (token that is sent in emails to verify validity)
     *
     * @param TDateTime $objDateTime
     * @return void
     */
    public function setEmailTokenExpires($objDateTime = null)
    {
        $this->setTDateTime(TSysUsersAbstract::FIELD_EMAILTOKENEXPIRES, $objDateTime);
    }    


    /**
     * when is the user scheduled for deletion?
     * after this date an account will be deleted in with a cron job
     *
     * @param TDateTime $objDateTime when null then an invalid date (timestamp 0) will be set, no deletion scheduled
     */
    public function setDeleteAfter($objDateTime = null)
    {
        $this->setTDateTime(TSysUsersAbstract::FIELD_DELETEAFTER, $objDateTime);
    }        

    /**
     * when is the user scheduled for deletion?
     * after this date an account will be deleted in with a cron job
     * 
     * 
     * @return TDateTime
     */
    public function getDeleteAfter()
    {
        return $this->get(TSysUsersAbstract::FIELD_DELETEAFTER);
    }        

    /**
     * set google id
     * (used for login-with-google)
     * 
     * @param string $sGoogleID open id (called sub id)
     */
    public function setGoogleID($sGoogleID)
    {
        $this->set(TSysUsersAbstract::FIELD_GOOGLEID, $sGoogleID);
    }        

    /**
     * get google id
     * (used for login-with-google)
     * 
     * @return TDateTime
     */
    public function getGoogleID()
    {
        return $this->get(TSysUsersAbstract::FIELD_GOOGLEID);
    }   

    /** 
     * get language
     * 
     * @return string
     */
    public function getLanguageID()
    {
        return $this->get(TSysUsersAbstract::FIELD_LANGUAGEID);
    }
    
    /**
     * set language
     * 
     * @param integer $iID
     */
    public function setLanguageID($iID)
    {
        $this->set(TSysUsersAbstract::FIELD_LANGUAGEID, $iID);
    }    

    /** 
     * get timezone, i.e. Europe/Amsterdam
     * 
     * @return string
     */
    public function getTimeZone()
    {
        return $this->get(TSysUsersAbstract::FIELD_TIMEZONE);
    }
    
    /**
     * set timezone, i.e. Europe/Amsterdam
     * 
     * @param string $sTimezone
     */
    public function setTimeZone($sTimezone = 'Europe/Amsterdam')
    {
        $this->set(TSysUsersAbstract::FIELD_TIMEZONE, $sTimezone);
    }        

    /** 
     * get date format, i.e. d-m-Y
     * 
     * @return string
     */
    public function getDateFormatShort()
    {
        return $this->get(TSysUsersAbstract::FIELD_DATEFORMATSHORT);
    }
    
    /**
     * set date format, i.e. d-m-Y
     * 
     * @param string $sFormat
     */
    public function setDateFormatShort($sFormat = 'd-m-Y')
    {
        $this->set(TSysUsersAbstract::FIELD_DATEFORMATSHORT, $sFormat);
    }       

    /** 
     * get date format, i.e. d-m-Y
     * 
     * @return string
     */
    public function getDateFormatLong()
    {
        return $this->get(TSysUsersAbstract::FIELD_DATEFORMATLONG);
    }
    
    /**
     * set date format, i.e. d-m-Y
     * 
     * @param string $sFormat
     */
    public function setDateFormatLong($sFormat = 'd-m-Y')
    {
        $this->set(TSysUsersAbstract::FIELD_DATEFORMATLONG, $sFormat);
    }    

    /** 
     * get time format, i.e. H:i
     * 
     * @return string
     */
    public function getTimeFormatShort()
    {
        return $this->get(TSysUsersAbstract::FIELD_TIMEFORMATSHORT);
    }
    
    /**
     * set time format, i.e. H:i
     * 
     * @param string $sFormat
     */
    public function setTimeFormatShort($sFormat = 'H:i')
    {
        $this->set(TSysUsersAbstract::FIELD_TIMEFORMATSHORT, $sFormat);
    }     

    /** 
     * get time format, i.e. H:i
     * 
     * @return string
     */
    public function getTimeFormatLong()
    {
        return $this->get(TSysUsersAbstract::FIELD_TIMEFORMATLONG);
    }
    
    /**
     * set time format, i.e. H:i
     * 
     * @param string $sFormat
     */
    public function setTimeFormatLong($sFormat = 'H:i')
    {
        $this->set(TSysUsersAbstract::FIELD_TIMEFORMATLONG, $sFormat);
    }      

    /** 
     * get on which day the week starts
     * monday = 1, sunday = 0
     * 
     * @return int
     */
    public function getWeekStartsOn()
    {
        return $this->get(TSysUsersAbstract::FIELD_WEEKSTARTSON);
    }
    
    /**
     * set on which day the week starts
     * monday = 1, sunday = 0
     * 
     * @param int $iDay
     */
    public function setWeekStartsOn($iDay = 0)
    {
        $this->set(TSysUsersAbstract::FIELD_WEEKSTARTSON, $iDay);
    } 

    /** 
     * get thousand separator: . or ,
     * two hundred thousand = 200.000 when thousand separator is dot (.)
     * 
     * @return string
     */
    public function getThousandSeparator()
    {
        return $this->get(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR);
    }

    /**
     * set thousand separator: . or ,
     * two hundred thousand = 200.000 when thousand separator is dot (.)
     * 
     * @param string $sSeparator
     */
    public function setThousandSeparator($sSeparator = ',')
    {
        $this->set(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, $sSeparator);
    }      


    /** 
     * get decimal separator: . or ,
     * 2 cents is = 0,02 when decimal separator is comma (,)
     * 
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->get(TSysUsersAbstract::FIELD_DECIMALSEPARATOR);
    }
        
    /**
     * set decimal separator: . or ,
     * 2 cents is = 0,02 when decimal separator is comma (,)
     * 
     * @param string $sSeparator
     */
    public function setDecimalSeparator($sSeparator = ',')
    {
        $this->set(TSysUsersAbstract::FIELD_DECIMALSEPARATOR, $sSeparator);
    }       


    /**
     * looks in database if usernames already exists in database
     * this function excludes the current record 
     * (it looks at all records except with current id if it's an existing record)
     *  
     * @param string $sUsername
     */
    public function isUsernameTakenDB($sUsername)
    {
        $bResult = false;
        $objClone = clone $this;
        $objClone->clear();

        //exclude current record
        if (!$this->getNew())
            $objClone->find(TSysUsersAbstract::FIELD_ID, $this->getID(), COMPARISON_OPERATOR_NOT_EQUAL_TO);
        
        if ($objClone->loadFromDBByUsername($sUsername))
        {
            if ($objClone->count() > 0) //username taken
                $bResult = true;
        }
        
        unset($objClone);
        return $bResult;        
    }
    
    /**
     * this function loads user with username from database
     * and determines if the current loaded user 
     * is allowed to log in based on the $sUsername and $sPassword.
     * it takes into account the password, login expired and enabled
     * (password expiration is not taken into account)
     * 
     * this method is safe for sql injections
     * 
     * returns false if $sUsername or $sPassword is empty
     * 
     * TRUE = verified and allowed to login, record loaded into memory
     * FALSE = not allowed to log in, RECORD NOT LOADED into memory
     * 
     * @param string $sUsername
     * @param string $sPassword password
     * @return boolean allowed
     */
    public function loadFromDBByUserLoginAllowed($sUsername, $sPassword)
    {

        if ($sUsername == '')
            return false;
        if ($sPassword == '')
            return false;
        
        //just to be sure: filter on XSS and weird characters
        $sUsername = strip_tags($sUsername);
        $sPassword = strip_tags($sPassword);        
        $sUsername = filterBadCharsWhiteList($sUsername, REGEX_TEXT_NORMAL, true);
//        $sPassword = filterBadCharsWhiteList($sPassword, REGEX_TEXT_NORMAL, true); -->runs through password_verify() anyway
        
        $this->clear();
        $this->find(TSysUsersAbstract::FIELD_USERNAME, $sUsername);
        if ($this->loadFromDB(1)) //needs to be at least 1, so the child class can read additional data from externally-referenced-tables like useraccounts.
        {
            
            if ($this->count() > 0)
            {
                if (password_verify($sPassword, $this->getPasswordEncrypted()))
                {
                    if ($this->getLoginEnabled())
                    {
                        if ($this->getLoginExpires()->isInTheFuture() || $this->getLoginExpires()->isZero())
                        {
                            if ($this->isChecksumValid())
                            {
                                $this->newQuery(); //otherwise you build upon the existing query
                                return true;
                            }
                            else
                            {
                                logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'isChecksumValid() failed for user. return false', $sUsername);
                                preventTimingAttack(10,200);
                                return false;
                            }
                        }
                    }
                    else
                    {
                        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'user->getLoginEnabled() failed for user. return false', $sUsername);
                        preventTimingAttack(20,500);
                        return false;
                    }
                }
                else
                {
                    logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'password_verify() failed for user. return false', $sUsername);
                    preventTimingAttack(40,200);
                    return false;
                }
            }            
        }
        $this->clear();
        return false;
    }



    /**
     * load user from database with username $sUsername
     * 
     * @param string $sUsername
     */
    public function loadFromDBByUsername($sUsername)
    {
        $bResult = false;
        $this->find(TSysUsersAbstract::FIELD_USERNAME, $sUsername);
        if ($this->loadFromDB(true))
            $bResult = true;
        $this->newQuery();
        return $bResult;
    }

    
    /**
     * this function determines if the user needs to set a new password
     * 
     * this function returns true, if:
     * - there is a new encryption algoritm for encrypting passwords (password_needs_rehash)
     * - the password is expired
     */
    public function needsNewPassword()
    {
        if (password_needs_rehash($this->getPasswordEncrypted(), PASSWORD_DEFAULT))
            return true;

        if ($this->getPasswordExpires()->isInThePast() && (!$this->getPasswordExpires()->isZero()) )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * automatically set some stuff
     * 
     * @param boolean $bResetDirtyNewOnSuccess
     * @param boolean $bStartOwnDatabaseTransaction
     * @param boolean $bCheckForLock
     * @return boolean
     */
//    public function saveToDB($bResetDirtyNewOnSuccess = true, $bStartOwnDatabaseTransaction = true, $bCheckForLock = false)    
//    {
//        if ($this->getDaysAutoRenewPasswordExpiration() > 0)
//        {
//            $this->getPasswordExpires()->setNow();
//            $this->getPasswordExpires()->addDays($this->getDaysAutoRenewPasswordExpiration());
//        }
//        
//        return parent::saveToDB($bResetDirtyNewOnSuccess, $bStartOwnDatabaseTransaction, $bCheckForLock);
//    }
    
    
    
    
     /**
     * additions to the install procedure
     * 
     * @param array $arrPreviousDependenciesModelClasses
     */
    public function install($arrPreviousDependenciesModelClasses = null)
    {
        return parent::install($arrPreviousDependenciesModelClasses);
    }     
        


    /**
     * check if email tokens are expired, if yes then empty the token field
     *
     * @return bool
     */
    public function deleteEmailTokensExpired()
    {
        $bResult = false;
        $objNow = new TDateTime();
        $objNow->setNow();
        $objZero = new TDateTime();
        
        $objTempUsers = $this->getCopy();
        $objTempUsers->find(TSysUsersAbstract::FIELD_EMAILTOKENEXPIRES, $objNow, COMPARISON_OPERATOR_LESS_THAN);
        $objTempUsers->find(TSysUsersAbstract::FIELD_EMAILTOKENEXPIRES, $objZero, COMPARISON_OPERATOR_GREATER_THAN);
        
        if ($objTempUsers->loadFromDB())
        {
            while($objTempUsers->next())
            {
                $objTempUsers->setEmailTokenEmpty();
                $objTempUsers->setEmailTokenExpires();
            }
        }
        $bResult = $objTempUsers->saveToDBAll();

        unset($objNow);
        unset($objZero);
        unset($objTempUsers);

        return $bResult;
    }

    /**
     * delete all users where the account date is expired
     *
     * @return bool
     */
    public function deleteUsersExpired()
    {
        $bResult = true;
        $objNow = new TDateTime();
        $objNow->setNow();
        $objZero = new TDateTime();
        
        $objTempUsers = $this->getCopy();
        $objTempUsers->find(TSysUsersAbstract::FIELD_DELETEAFTER, $objNow, COMPARISON_OPERATOR_LESS_THAN);
        $objTempUsers->find(TSysUsersAbstract::FIELD_DELETEAFTER, $objZero, COMPARISON_OPERATOR_GREATER_THAN);
        
        if ($objTempUsers->loadFromDB())
        {
            while($objTempUsers->next())
            {
                if (!$objTempUsers->deleteFromDB(true, true))
                    $bResult = false;
            }
        }

        unset($objNow);
        unset($objZero);
        unset($objTempUsers);

        return $bResult;    
    }

	
    /**
     * This function is called in the constructor and the clear() function
     * this is used to define default values for fields
     * 
     * initialize values
     */
    public function initRecord()
    {
        $this->setLoginEnabled(false);
        $this->setLoginExpires();
        
    }
	
	
	
    /**
     * defines the fields in the tables
     * i.e. types, default values, enum values, referenced tables etc
    */
    public function defineTable()
    {
       

        //username
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_USERNAME, '');
        $this->setFieldType(TSysUsersAbstract::FIELD_USERNAME, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_USERNAME, 100);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_USERNAME, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_USERNAME, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_USERNAME, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_USERNAME, true);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_USERNAME, false);//it is already UNIQUE
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_USERNAME, true);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_USERNAME, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_USERNAME, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_USERNAME, false);
		$this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_USERNAME);			          

        // real and fake password fields (they are technically the same)
        $iNoFakePasswordFields = $this->getNoFakePasswordFields();
        $bRealPasswordCreated = false;
        for ($iIndex = 0; $iIndex <= $iNoFakePasswordFields; $iIndex++)
        {
            $this->setFieldCopyProps(TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex, TSysUsersAbstract::FIELD_USERNAME);       
            $this->setFieldUnique(TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex, false);
            $this->setFieldIndexed(TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex, false);
            $this->setFieldFulltext(TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex, false);

            if (TSysUsersAbstract::FIELD_PASSWORDFAKEPREFIX.$iIndex == TSysUsersAbstract::FIELD_PASSWORDENCRYPTED)
                $bRealPasswordCreated = true;
        }  

        //create real password if it wasn't created earlier
        if (!$bRealPasswordCreated) //make sure we have a password-field
        {
            $this->setFieldCopyProps(TSysUsersAbstract::FIELD_PASSWORDENCRYPTED, TSysUsersAbstract::FIELD_USERNAME);       
            $this->setFieldUnique(TSysUsersAbstract::FIELD_PASSWORDENCRYPTED, false);
        }

        //enabled
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldType(TSysUsersAbstract::FIELD_LOGINENABLED, CT_BOOL);
        $this->setFieldLength(TSysUsersAbstract::FIELD_LOGINENABLED, 0);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_LOGINENABLED, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_LOGINENABLED, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_LOGINENABLED, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_LOGINENABLED, false);	
		$this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_LOGINENABLED);			                  

        //login exires
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldType(TSysUsersAbstract::FIELD_LOGINEXPIRES, CT_DATETIME);
        $this->setFieldLength(TSysUsersAbstract::FIELD_LOGINEXPIRES, 0);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_LOGINEXPIRES, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_LOGINEXPIRES, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_LOGINEXPIRES, true);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_LOGINEXPIRES, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_LOGINEXPIRES, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_LOGINEXPIRES, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_LOGINEXPIRES, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_LOGINEXPIRES, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_LOGINEXPIRES, false);	
		$this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_LOGINEXPIRES);			                          

        //last login
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_LASTLOGIN, TSysUsersAbstract::FIELD_LOGINEXPIRES);       
        
        //email OLD
        // $this->setFieldCopyProps(TSysUsersAbstract::FIELD_EMAILADDRESS_OLD, TSysUsersAbstract::FIELD_USERNAME);       
        // $this->setFieldUnique(TSysUsersAbstract::FIELD_EMAILADDRESS_OLD, false);
        
        //2-way encrypted email address
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, '');
        $this->setFieldType(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, CT_LONGTEXT);
        $this->setFieldLength(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, 0);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, true);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
		$this->setFieldEncryptionCypher(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysUsersAbstract::FIELD_EMAILADDRESSENCRYPTED, TSysUsersAbstract::ENCRYPTION_EMAILADDRESS_PASSPHRASE);			                          


        //email fingerprint, so we can lookup the record based on email address
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, '');
        $this->setFieldType(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, 128);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, true);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, true);//for quick lookup
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);//for quick lookup
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
		$this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_EMAILADDRESSFINGERPRINT);			                                  

        //password expires
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_PASSWORDEXPIRES, TSysUsersAbstract::FIELD_LOGINEXPIRES);       
           
        //update permissions
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_UPDATEPERMISSIONS, TSysUsersAbstract::FIELD_LOGINENABLED);       

        //email token
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, '');
        $this->setFieldType(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, 255);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, true);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, true); //for quick lookup
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, false); //for quick lookup
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED, false);    
		$this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_EMAILTOKENENCRYPTED);			                                              
      
        //email token expires
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_EMAILTOKENEXPIRES, TSysUsersAbstract::FIELD_LOGINEXPIRES);           

        //delete after
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_DELETEAFTER, TSysUsersAbstract::FIELD_LOGINEXPIRES);  
        
        //google id (openid)
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_GOOGLEID, '');
        $this->setFieldType(TSysUsersAbstract::FIELD_GOOGLEID, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_GOOGLEID, 100);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_GOOGLEID, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_GOOGLEID, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_GOOGLEID, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_GOOGLEID, false);//this field can be null, when no googleid is used (then it is not unique anymore)
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_GOOGLEID, true);//for quick lookup
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_GOOGLEID, false);//for quick lookup
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_GOOGLEID, null);
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_GOOGLEID, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_GOOGLEID, false);     
        $this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_GOOGLEID);  
        
        
        //language		
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_LANGUAGEID, '');
        $this->setFieldType(TSysUsersAbstract::FIELD_LANGUAGEID, CT_INTEGER64);
        $this->setFieldLength(TSysUsersAbstract::FIELD_LANGUAGEID, 0);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_LANGUAGEID, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_LANGUAGEID, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_LANGUAGEID, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_LANGUAGEID, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_LANGUAGEID, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_LANGUAGEID, true);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_LANGUAGEID, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_LANGUAGEID, TSysLanguages::class);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_LANGUAGEID, TSysLanguages::getTable());
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_LANGUAGEID, TSysModel::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_LANGUAGEID);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_LANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_LANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT); //dont delete when language is deleted (which it never should btw)
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_LANGUAGEID, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_LANGUAGEID, true);
        $this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_LANGUAGEID);  
        
        //timezone		
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_TIMEZONE, 'Europe/Amsterdam');
        $this->setFieldType(TSysUsersAbstract::FIELD_TIMEZONE, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_TIMEZONE, 50);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_TIMEZONE, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_TIMEZONE, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_TIMEZONE, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_TIMEZONE, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_TIMEZONE, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_TIMEZONE, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_TIMEZONE, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_TIMEZONE, null); 
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_TIMEZONE, false);
        $this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_TIMEZONE);        

        //date format short
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_DATEFORMATSHORT, 'd-m-Y');
        $this->setFieldType(TSysUsersAbstract::FIELD_DATEFORMATSHORT, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_DATEFORMATSHORT, 20);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_DATEFORMATSHORT, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_DATEFORMATSHORT, null); 
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_DATEFORMATSHORT, false);
        $this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_DATEFORMATSHORT);      

        //date format long
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_DATEFORMATLONG, TSysUsersAbstract::FIELD_DATEFORMATSHORT);

        //time format short
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_TIMEFORMATSHORT, TSysUsersAbstract::FIELD_DATEFORMATSHORT);
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_TIMEFORMATSHORT, 'H:i');

        //time format long
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_TIMEFORMATLONG, TSysUsersAbstract::FIELD_TIMEFORMATSHORT);

        //week starts on	
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_WEEKSTARTSON, 0); //Monday = 0, Sunday = 6
        $this->setFieldType(TSysUsersAbstract::FIELD_WEEKSTARTSON, CT_INTEGER32);
        $this->setFieldLength(TSysUsersAbstract::FIELD_WEEKSTARTSON, 1);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_WEEKSTARTSON, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_WEEKSTARTSON, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_WEEKSTARTSON, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_WEEKSTARTSON, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_WEEKSTARTSON, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_WEEKSTARTSON, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_WEEKSTARTSON, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_WEEKSTARTSON, null); 
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_WEEKSTARTSON, false);
        $this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_WEEKSTARTSON); 

        //thousand separator
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, '.'); //, or .
        $this->setFieldType(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, CT_VARCHAR);
        $this->setFieldLength(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, 1);
        $this->setFieldDecimalPrecision(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, 0);
        $this->setFieldPrimaryKey(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldNullable(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldEnumValues(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null);
        $this->setFieldUnique(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldIndexed(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldFulltext(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldForeignKeyClass(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null);
        $this->setFieldForeignKeyTable(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null);
        $this->setFieldForeignKeyField(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null);
        $this->setFieldForeignKeyJoin(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, null); 
        $this->setFieldAutoIncrement(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldUnsigned(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR, false);
        $this->setFieldEncryptionDisabled(TSysUsersAbstract::FIELD_THOUSANDSEPARATOR);   
        
        //decimal separator
        $this->setFieldCopyProps(TSysUsersAbstract::FIELD_DECIMALSEPARATOR, TSysUsersAbstract::FIELD_THOUSANDSEPARATOR);
        $this->setFieldDefaultValue(TSysUsersAbstract::FIELD_DECIMALSEPARATOR, ',');
    }
            
	

    /**
     * use the auto-added id-field ?
     * @return bool
    */
    public function getTableUseIDField()
    {
        return true;	
    }


    /**
     * use the auto-added date-changed & date-created field ?
     * @return bool
    */
    public function getTableUseDateCreatedChangedField()
    {
        //out of security reasons disabled, otherwise you could see the time a user changed his password, which makes the time element in a password vulnerable
        return false; 
    }


    /**
     * use the checksum field ?
     * @return bool
    */
    public function getTableUseChecksumField()
    {
        return true;
    }

    /**
     * order field to switch order between records
    */
    public function getTableUseOrderField()
    {
        return false;
    }

    /**
     * use checkout for locking file for editing
    */
    public function getTableUseCheckout()
    {
        return true;
    }

    /**
     * use locking file for editing
    */
    public function getTableUseLock()
    {
        return true;
    }        

    /**
     * use image in your record?
     * if you don't want a small and large version, use this one
    */
    public function getTableUseImageFile()
    {
        return false;
    }


    /**
     * opvragen of records fysiek uit de databasetabel verwijderd moeten worden
     *
     * returnwaarde interpretatie:
     * true = fysiek verwijderen uit tabel
     * false = record-hidden-veld gebruiken om bij te houden of je het record kan zien in overzichten
     *
     * @return bool moeten records fysiek verwijderd worden ?
    */
    public function getTablePhysicalDeleteRecord()
    {
        return true;
    }




    /**
     * type of primary key field
     *
     * @return integer with constant CT_AUTOINCREMENT or CT_INTEGER32 or something else that is not recommendable
    */
    public function getTableIDFieldType()
    {
        return CT_AUTOINCREMENT;
    }


    /**
     * OVERSCHRIJF DOOR CHILD KLASSE ALS NODIG
     *
     * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
     * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
     *
     *
     * return '??? - functie niet overschreven door child klasse';
    */
    public function getDisplayRecordShort()
    {
        return $this->get(TSysUsersAbstract::FIELD_USERNAME);
    }



    /**
     * for the automatic database table upgrade system to work this function
     * returns the version number of this class
     * The update system can compare the version of the database with the Business Logic
     *
     * default with no updates = 0
     * first update = 1, second 2 etc
     *
     * @return int
    */
    public function getVersion()
    {
        return 0;
    }
    
    /**
     * DEZE FUNCTIE MOET OVERGEERFD WORDEN DOOR DE CHILD KLASSE
     *
     * checken of alle benodigde waardes om op te slaan wel aanwezig zijn
     *
     * @return bool true=ok, false=not ok
    */
    public function areValuesValid()
    {   
        return true;
    }



    /**
     * update the table in the database
     * (may have been changes to fieldnames, fields added or removed etc)
     *
     * @param int $iFromVersion upgrade vanaf welke versie ?
     * @return bool is alles goed gegaan ? true = ok (of er is geen upgrade gedaan)
    */
    protected function refactorDBTable($iFromVersion)
    {
        return true;
    }	
    
    /**
     * use a second id that has no follow-up numbers?
     */
    public function getTableUseRandomID()
    {
        return true;
    }
    
    /**
     * is randomid field a primary key?
     */        
    public function getTableUseRandomIDAsPrimaryKey()
    {
       return false;
    }

	/**
	 * use a third character-based id that has no logically follow-up numbers?
	 * 
	 * a tertiary unique key (uniqueid) can be useful for security reasons like login sessions: you don't want to _POST the follow up numbers in url
	 */
	public function getTableUseUniqueID()
	{
		return true;
	}    
    

	/**
	 * use a random string id that has no logically follow-up numbers
	 * 
	 * this is used to produce human readable identifiers
	 * @return bool
	 */
	public function getTableUseNiceID()
	{
		return false;
	}	
        
    /**
     * is this model a translation model?
     *
     * @return bool is this model a translation model?
     */
    public function getTableUseTranslationLanguageID()
    {
        return false;
    }    

	/**
	 * Want to use the 'isdefault' field in database table?
	 * Returning true allows 1 record to be the default record in a table
	 * This is useful for creating records with foreign fields without user interference OR 
	 * selecting records in GUI elements like comboboxes
	 * 
	 * example: select the default language in a combobox
	 * 
	 * @return bool
	 */
	public function getTableUseIsDefault()
	{
		return false;
	}    
    
	/**
	 * can a record be favorited by the user?
	 *
	 * @return bool
	 */
	public function getTableUseIsFavorite()
	{
		return false;
	}
        
	/**
	 * can record be transcanned?
	 * Trashcan is an extra step in for deleting a record
	 *
	 * @return bool
	 */
	public function getTableUseTrashcan()
	{
		return false;
	}	

	/**
	 * use a field for search keywords?
	 * (also known als tags or labels)
	 *
	 * @return bool
	 */
	public function getTableUseSearchKeywords()
	{
		return false;
	}	

    /****************************************************************************
     *              ABSTRACT METHODS
    ****************************************************************************/
    
    /**
     * how many fake password fields do you want
     */
    abstract public function getNoFakePasswordFields();
    
}

?>