<?php


namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use DateTimeZone;
use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\controllers\TCRUDDetailSaveController_org;
use dr\classes\locale\TLocalisation;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\form\Label;
use dr\classes\dom\tag\form\InputDatetime;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\Script;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\webcomponents\DRInputDateTime;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersHistory;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use  dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 * 
 * 14-11-2024: detailsave_users: ip address bij sessies toegevoegd
 */
class detailsave_users extends TCRUDDetailSaveController
{
    private $objEdtUsername = null;//dr\classes\dom\tag\form\InputText
    private $objEdtUsernamePublic = null;//dr\classes\dom\tag\form\InputText
    private $objEdtPasswordOld = null;//dr\classes\dom\tag\form\InputPassword
    private $objEdtPasswordNew = null;//dr\classes\dom\tag\form\InputPassword
    private $objEdtPasswordRepeat = null;//dr\classes\dom\tag\form\InputPassword
    private $objEdtEmail = null;//dr\classes\dom\tag\form\InputText
    private $objEdtLoginExpiresDate = null;//dr\classes\dom\tag\form\InputDate
    private $objEdtLoginExpiresTime = null;//dr\classes\dom\tag\form\InputTime
    private $objEdtPasswordExpiresDate = null;//dr\classes\dom\tag\form\InputText
    private $objEdtPasswordExpiresTime = null;//dr\classes\dom\tag\form\InputTime
    private $objEdtAccountExpiresDate = null;//dr\classes\dom\tag\form\InputText
    private $objEdtAccountExpiresTime = null;//dr\classes\dom\tag\form\InputTime  

    //localisation
    private $objOptLanguage = null;//dr\classes\dom\tag\form\Select   
    private $objOptTimezone = null;//dr\classes\dom\tag\form\Select   
    private $objEdtDateFormatShort = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDateFormatLong = null;//dr\classes\dom\tag\form\InputText
    private $objEdtTimeFormatShort = null;//dr\classes\dom\tag\form\InputText
    private $objEdtTimeFormatLong = null;//dr\classes\dom\tag\form\InputText
    private $objOptWeekStartsOn = null; //dr\classes\dom\tag\form\Select   
    private $objEdtThousandSeparator = null; //dr\classes\dom\tag\form\InputText
    private $objEdtDecimalSeparator = null; //dr\classes\dom\tag\form\InputText
    
    //accessibility
    private $objChkLoginEnabled = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objOptGroup = null;//dr\classes\dom\tag\form\Select  user role
    private $objOptAccount = null;//dr\classes\dom\tag\form\Select  Usergroup   
     

    private $objLblHintSessions1 = null;//Label
    private $objLblHintSessions2 = null;//Label
    private $objUserSessions = null;//TSysCMSUserSessions

    private $objLblHintHistory = null;//Label
    private $objUserLoginHistory = null;//TSysCMSUserLoginHistory
        
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        // global $objLocalisation;
        global $objAuthenticationSystem;
                
            //username
        $this->objEdtUsername = new InputText();
        $this->objEdtUsername->setNameAndID('edtUsername');
        $this->objEdtUsername->setClass('fullwidthtag');   
        $this->objEdtUsername->setRequired(true);   
        $this->objEdtUsername->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtUsername->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtUsername->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtUsername, '', transm($this->getModule(), 'userdetail_form_field_username', 'username'));

            //username public
        $this->objEdtUsernamePublic = new InputText();
        $this->objEdtUsernamePublic->setNameAndID('edtUsernamePublic');
        $this->objEdtUsernamePublic->setClass('fullwidthtag');   
        $this->objEdtUsernamePublic->setRequired(true);   
        $this->objEdtUsernamePublic->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtUsernamePublic->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtUsernamePublic, '', transm($this->getModule(), 'userdetail_form_field_usernamepublic', 'public username (published on websites etc)'));        

            //password old
        $sPassHint = '';
        if (!$this->getModel()->getNew())//existing record
            $sPassHint = ' '.transm($this->getModule(), 'form_field_passwordold_hint_existingrecord_leaveempty', '(only if you want to change password, otherwise leave empty)');
        $this->objEdtPasswordOld = new InputPassword();
        $this->objEdtPasswordOld->setNameAndID('edtPasswordOld');
        $this->objEdtPasswordOld->setClass('fullwidthtag');   
        $this->objEdtPasswordOld->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        if (!$this->getModel()->getNew())//existing record
            $this->getFormGenerator()->add($this->objEdtPasswordOld, '', transm($this->getModule(), 'userdetail_form_field_passwordold', 'current password').$sPassHint); 

            //password new
        $sPassHint = '';
        if (!$this->getModel()->getNew())//existing record
            $sPassHint = ' '.transm($this->getModule(), 'form_field_passwordnew_hint_existingrecord_leaveempty', '(if you want to change password, otherwise leave empty)');
        $this->objEdtPasswordNew = new InputPassword();
        $this->objEdtPasswordNew->setNameAndID('edtPasswordNew');
        $this->objEdtPasswordNew->setClass('fullwidthtag');   
        $this->objEdtPasswordNew->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->getFormGenerator()->add($this->objEdtPasswordNew, '', transm($this->getModule(), 'userdetail_form_field_passwordnew', 'new password').$sPassHint); 

            //password repeat
        $sPassRepeatHint = '';
        if (!$this->getModel()->getNew())//existing record
            $sPassHint = ' '.transm($this->getModule(), 'form_field_passwordrepeat_hint_existingrecord_leaveempty', '(only if you want to change password)');
        $this->objEdtPasswordRepeat = new InputPassword();
        $this->objEdtPasswordRepeat->setNameAndID('edtPasswordRepeat');
        $this->objEdtPasswordRepeat->setClass('fullwidthtag');   
        $this->objEdtPasswordRepeat->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->getFormGenerator()->add($this->objEdtPasswordRepeat, '', transm($this->getModule(), 'userdetail_form_field_passwordnewrepeat', 'repeat new password').$sPassHint); 
        
        
             //email (can be empty)
        $this->objEdtEmail = new InputText();
        $this->objEdtEmail->setNameAndID('edtEmail');   
        $this->objEdtEmail->setClass('fullwidthtag');   
        $this->objEdtEmail->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtEmail->addValidator($objValidator);    
        $objValidator = new TEmailAddress(true);
        $this->objEdtEmail->addValidator($objValidator);            
        $this->getFormGenerator()->add($this->objEdtEmail, '', transm($this->getModule(), 'userdetail_form_field_emailaddress', 'email address'));
        

        //login expires 
        $this->objEdtLoginExpiresDate = new DRInputDateTime();
        $this->objEdtLoginExpiresDate->setNameAndID('edtLoginExpiresDate');
        $this->objEdtLoginExpiresDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
        $this->objEdtLoginExpiresDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
        $this->objEdtLoginExpiresDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objEdtLoginExpiresDate->setAllowEmptyDateTime(true);
        $this->getFormGenerator()->add($this->objEdtLoginExpiresDate, '', transm($this->getModule(), 'userdetail_form_field_loginexpires', 'login expires (user can\'t log in after this date) (leave empty for no expiration)'));        
        
        //password expires 
        $this->objEdtPasswordExpiresDate = new DRInputDateTime();
        $this->objEdtPasswordExpiresDate->setNameAndID('edtPasswordExpiresDate');
        $this->objEdtPasswordExpiresDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
        $this->objEdtPasswordExpiresDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
        $this->objEdtPasswordExpiresDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objEdtPasswordExpiresDate->setAllowEmptyDateTime(true);
        $this->getFormGenerator()->add($this->objEdtPasswordExpiresDate, '', transm($this->getModule(), 'form_field_passwordexpires', 'password expires (prompts user to change password on login) (leave empty for no expiration)'));        
        

        //scheduled for deletion after (and is delete in cron job afterwards)
        $this->objEdtAccountExpiresDate = new DRInputDateTime();
        $this->objEdtAccountExpiresDate->setNameAndID('edtAccountExpiresDate');
        $this->objEdtAccountExpiresDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
        $this->objEdtAccountExpiresDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
        $this->objEdtAccountExpiresDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objEdtAccountExpiresDate->setAllowEmptyDateTime(true);
        $this->getFormGenerator()->add($this->objEdtAccountExpiresDate, '', transm($this->getModule(), 'userdetail_form_field_scheduleddeletion', 'Auto delete user after (deleted by cron job, leave empty for no expiration)'));        
                


        //=== accessibility & ownership

        $sTransSectionAccess = transm($this->getModule(), 'section_accessibility_title', 'Accessibility & ownership');

        //is user enabled to log in?        
        // if (auth($this->getModule(), $this->getAuthorisationCategory(), 'edit: user enabled (to log in)'))
        {
            $this->objChkLoginEnabled = new InputCheckbox();
            $this->objChkLoginEnabled->setNameAndID('edtLoginEnabled');
            $this->getFormGenerator()->add($this->objChkLoginEnabled, $sTransSectionAccess, transm($this->getModule(), 'userdetail_form_field_enabled', 'able to log in'));         
        }

        //user group / role
        $this->objOptGroup = new Select();
        $this->objOptGroup->setNameAndID('optGroup');
        $this->objOptGroup->setClass('quarterwidthtag');          
        $this->getFormGenerator()->add($this->objOptGroup, $sTransSectionAccess, transm($this->getModule(), 'userdetail_form_field_userrole', 'User role'));

        //user acount
        $this->objOptAccount = new Select();
        $this->objOptAccount->setNameAndID('optAccount');
        $this->objOptAccount->setClass('quarterwidthtag');         
        $this->getFormGenerator()->add($this->objOptAccount, $sTransSectionAccess, transm($this->getModule(), 'userdetail_form_field_organisation', 'Part of organisation'));


        //==== localisation

        $sTransSectionLocalization = transm($this->getModule(), 'section_localization_title', 'Localization');
        
        //language
        $this->objOptLanguage = new Select();
        $this->objOptLanguage->setNameAndID('optLanguage');
        $this->objOptLanguage->setClass('quarterwidthtag');         
        $this->getFormGenerator()->add($this->objOptLanguage, $sTransSectionLocalization, transm($this->getModule(), 'userdetail_form_field_language', 'language'));
        
        //timezone
        $this->objOptTimezone = new Select();
        $this->objOptTimezone->setNameAndID('optTimeZone');
        $this->objOptTimezone->setClass('quarterwidthtag'); 
        $this->getFormGenerator()->add($this->objOptTimezone, $sTransSectionLocalization, transm($this->getModule(), 'form_field_timezone', 'time zone'));

        //date format short
        $this->objEdtDateFormatShort = new InputText();
        $this->objEdtDateFormatShort->setNameAndID('edtDateFormat');
        $this->objEdtDateFormatShort->setClass('quarterwidthtag');   
        $this->objEdtDateFormatShort->setRequired(true);   
        $this->objEdtDateFormatShort->setMaxLength(20);
        $objValidator = new TMaximumLength(20);
        $this->objEdtDateFormatShort->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtDateFormatShort, $sTransSectionLocalization, transm($this->getModule(), 'form_field_dateformat', 'date format (Europe: "d-m-Y", USA: "m/d/Y")'));        

        //date format long
        $this->objEdtDateFormatLong = new InputText();
        $this->objEdtDateFormatLong->setNameAndID('edtDateFormatLong');
        $this->objEdtDateFormatLong->setClass('quarterwidthtag');   
        $this->objEdtDateFormatLong->setRequired(true);   
        $this->objEdtDateFormatLong->setMaxLength(20);
        $objValidator = new TMaximumLength(20);
        $this->objEdtDateFormatLong->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtDateFormatLong, $sTransSectionLocalization, transm($this->getModule(), 'form_field_dateformatlong', 'date format long (Europe: "d-m-Y", USA: "m/d/Y")'));        
        
        //time format short
        $this->objEdtTimeFormatShort = new InputText();
        $this->objEdtTimeFormatShort->setNameAndID('edtTimeFormat');
        $this->objEdtTimeFormatShort->setClass('quarterwidthtag');   
        $this->objEdtTimeFormatShort->setRequired(true);   
        $this->objEdtTimeFormatShort->setMaxLength(20);
        $objValidator = new TMaximumLength(20);
        $this->objEdtTimeFormatShort->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtTimeFormatShort, $sTransSectionLocalization, transm($this->getModule(), 'form_field_timeformat', 'time format (Europe: "H:i", USA: "g:i a")'));

        //time format long
        $this->objEdtTimeFormatLong = new InputText();
        $this->objEdtTimeFormatLong->setNameAndID('edtTimeFormatLong');
        $this->objEdtTimeFormatLong->setClass('quarterwidthtag');   
        $this->objEdtTimeFormatLong->setRequired(true);   
        $this->objEdtTimeFormatLong->setMaxLength(20);
        $objValidator = new TMaximumLength(20);
        $this->objEdtTimeFormatLong->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtTimeFormatLong, $sTransSectionLocalization, transm($this->getModule(), 'form_field_timeformatlong', 'time format long (Europe: "H:i:s", USA: "g:i:s a")'));

        //week starts on monday or sunday
        $this->objOptWeekStartsOn = new Select();
        $this->objOptWeekStartsOn->setNameAndID('optWeekStartsOn');
        $this->objOptWeekStartsOn->setClass('quarterwidthtag');          
        $this->getFormGenerator()->add($this->objOptWeekStartsOn, $sTransSectionLocalization, transm($this->getModule(), 'form_field_weekstartsone', 'week starts on'));

        //thousand separator
        $this->objEdtThousandSeparator = new InputText();
        $this->objEdtThousandSeparator->setNameAndID('edtThousandSeparator');
        $this->objEdtThousandSeparator->setClass('quarterwidthtag');   
        $this->objEdtThousandSeparator->setRequired(true);   
        $this->objEdtThousandSeparator->setMaxLength(1);
        $objValidator = new TMaximumLength(1);
        $this->objEdtThousandSeparator->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtThousandSeparator, $sTransSectionLocalization, transm($this->getModule(), 'form_field_thousandseparator', 'thousand separator (. or ,)'));        

        //decimal separator
        $this->objEdtDecimalSeparator = new InputText();
        $this->objEdtDecimalSeparator->setNameAndID('edtDecimalSeparator');
        $this->objEdtDecimalSeparator->setClass('quarterwidthtag');   
        $this->objEdtDecimalSeparator->setRequired(true);   
        $this->objEdtDecimalSeparator->setMaxLength(1);
        $objValidator = new TMaximumLength(1);
        $this->objEdtDecimalSeparator->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtDecimalSeparator, $sTransSectionLocalization, transm($this->getModule(), 'form_field_decimalseparator', 'decimal separator (. or ,)'));        


        //==== sessions
        //done in modelToView()

        //==== login history
        //done in modelToView()
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_CMSUsers::PERM_CAT_USERS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        global $objAuthenticationSystem;
        $objUserSessionsCurr = $objAuthenticationSystem->getUserSessions();
        
        $this->getModel()->set(TSysCMSUsers::FIELD_USERNAME, $this->objEdtUsername->getValueSubmitted());
        $this->getModel()->set(TSysCMSUsers::FIELD_USERNAMEPUBLIC, $this->objEdtUsernamePublic->getValueSubmitted());
        // $this->getModel()->set(TSysCMSUsers::FIELD_EMAILADDRESS_OLD, $this->objEdtEmail->getValueSubmitted());
        $this->getModel()->setEmailAddressDecrypted($this->objEdtEmail->getValueSubmitted());
        
        //password
        if ($this->objEdtPasswordNew->getValueSubmitted()) //only set if there is a value in the field
        {                        
            $this->getModel()->setPasswordDecrypted($this->objEdtPasswordNew->getValueSubmitted(), true);
            
            //the user may want to change a password because of an unauthorised login
            //to be sure we also change the tokens
            $objUserSessionsDel = new TSysCMSUsersSessions();
            $objUserSessionsDel->find(TSysCMSUsersSessions::FIELD_USERID, $this->getModel()->getID()); //all sessions of user
            $objUserSessionsDel->find(TSysCMSUsersSessions::FIELD_RANDOMID, $objUserSessionsCurr->getRandomID(), COMPARISON_OPERATOR_NOT_EQUAL_TO); //except current session
            $objUserSessionsDel->deleteFromDB(true);

            //set new expiration date on password --> is not changed here automatically, but you can do it by hand by filling out expiration date
            // $iDaysExpires = 0;
            // $iDaysExpires = (int)getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS);
            // if ($iDaysExpires > 0)
            // {
            //     $this->getModel()->getPasswordExpires()->setNow();
            //     $this->getModel()->getPasswordExpires()->addDays($iDaysExpires);
            // }            
            
        }
        
        //login expires
        $this->getModel()->set(TSysCMSUsers::FIELD_LOGINEXPIRES, $this->objEdtLoginExpiresDate->getValueSubmittedAsTDateTimeISO());
        
        //password expires
        $this->getModel()->set(TSysCMSUsers::FIELD_PASSWORDEXPIRES, $this->objEdtPasswordExpiresDate->getValueSubmittedAsTDateTimeISO());

        
        //account expires
        $this->getModel()->set(TSysCMSUsers::FIELD_DELETEAFTER, $this->objEdtAccountExpiresDate->getValueSubmittedAsTDateTimeISO());

        //==== accessability

        //user enabled
        // if (auth($this->getModule(), $this->getAuthorisationCategory(), 'edit: user enabled (to log in)'))
            $this->getModel()->set(TSysCMSUsers::FIELD_LOGINENABLED, $this->objChkLoginEnabled->getValueSubmittedAsBool());        
                
        //user role
        $this->getModel()->set(TSysCMSUsers::FIELD_USERROLEID, $this->objOptGroup->getValueSubmittedAsInt());

        //user account
        $this->getModel()->set(TSysCMSUsers::FIELD_CMSORGANISATIONSID, $this->objOptAccount->getValueSubmittedAsInt());


        //==== localisation

        //language
        $this->getModel()->set(TSysCMSUsers::FIELD_LANGUAGEID, $this->objOptLanguage->getValueSubmittedAsInt());

        //timezone
        $this->getModel()->setTimezone($this->objOptTimezone->getValueSubmitted());

        //date format short
        $this->getModel()->setDateFormatShort($this->objEdtDateFormatShort->getValueSubmitted());

        //date format long
        $this->getModel()->setDateFormatLong($this->objEdtDateFormatLong->getValueSubmitted());

        //time format short
        $this->getModel()->setTimeFormatShort($this->objEdtTimeFormatShort->getValueSubmitted());

        //time format long
        $this->getModel()->setTimeFormatLong($this->objEdtTimeFormatLong->getValueSubmitted());

        //week starts on
        $this->getModel()->setWeekStartsOn($this->objOptWeekStartsOn->getValueSubmittedAsInt());

        //thousand separator
        $this->getModel()->setThousandSeparator($this->objEdtThousandSeparator->getValueSubmitted());

        //decimal separator
        $this->getModel()->setDecimalSeparator($this->objEdtDecimalSeparator->getValueSubmitted());


        //=== user sessions

        $objSessions = $this->objUserSessions;
        $objSessions->resetRecordpointer();
        while($objSessions->next())
        {
            $sVarName = 'chkSession'.$objSessions->getRandomID();
            if (isset($_POST[$sVarName]))
            {
                if ($_POST[$sVarName ] == '1')
                {                
                    $objSessionsDel = $objSessions->getCopy();
                    $objSessionsDel->newQuery();
                    $objSessionsDel->findRandomID($objSessions->getRandomID());
                    $objSessionsDel->deleteFromDB(true);
                    unset($objSessionsDel);
                }
            }
        } 
        $this->objUserSessions->loadFromDB();
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        $this->objEdtUsername->setValue($this->getModel()->get(TSysCMSUsers::FIELD_USERNAME));
        $this->objEdtUsernamePublic->setValue($this->getModel()->get(TSysCMSUsers::FIELD_USERNAMEPUBLIC));
        //$this->objEdtPassword->setValue($this->getModel()->get(TSysCMSUsers::FIELD_PASSWORDENCRYPTED)); --> don't show password (it's no use because it's encrypted)
        // $this->objEdtEmail->setValue($this->getModel()->get(TSysCMSUsers::FIELD_EMAILADDRESS_OLD)); 
        $this->objEdtEmail->setValue($this->getModel()->get(TSysCMSUsers::FIELD_EMAILADDRESSENCRYPTED, '', true)); 
        
        //login expires
        /*
        // if (auth($this->getModule(), $this->getAuthorisationCategory(), 'edit: date login expires'))        
        {
            $this->objEdtLoginExpiresDate->setValue($this->getModel()->getDateAsString(TSysCMSUsers::FIELD_LOGINEXPIRES, $this->getDateFormatDefault())); 
            $this->objEdtLoginExpiresTime->setValue($this->getModel()->getTimeAsString(TSysCMSUsers::FIELD_LOGINEXPIRES, $this->getTimeFormatDefault())); 
        }
        */
        $this->objEdtLoginExpiresDate->setValueAsTDateTime($this->getModel()->get(TSysCMSUsers::FIELD_LOGINEXPIRES));

        //password expires
        // // if (auth($this->getModule(), $this->getAuthorisationCategory(), 'edit: date password expires'))
        // {
        //     $this->objEdtPasswordExpiresDate->setValue($this->getModel()->getDateAsString(TSysCMSUsers::FIELD_PASSWORDEXPIRES, $this->getDateFormatDefault())); 
        //     $this->objEdtPasswordExpiresTime->setValue($this->getModel()->getTimeAsString(TSysCMSUsers::FIELD_PASSWORDEXPIRES, $this->getTimeFormatDefault())); 
        // }
        $this->objEdtPasswordExpiresDate->setValueAsTDateTime($this->getModel()->get(TSysCMSUsers::FIELD_PASSWORDEXPIRES));

        //account expires
        // $this->objEdtAccountExpiresDate->setValue($this->getModel()->getDateAsString(TSysCMSUsers::FIELD_DELETEAFTER, $this->getDateFormatDefault())); 
        // $this->objEdtAccountExpiresTime->setValue($this->getModel()->getTimeAsString(TSysCMSUsers::FIELD_DELETEAFTER, $this->getTimeFormatDefault())); 
        $this->objEdtAccountExpiresDate->setValueAsTDateTime($this->getModel()->get(TSysCMSUsers::FIELD_DELETEAFTER));
    
        //==== accessibility

        //enabled
        // if (auth($this->getModule(), $this->getAuthorisationCategory(), 'edit: user enabled (to log in)'))
            $this->objChkLoginEnabled->setChecked($this->getModel()->get(TSysCMSUsers::FIELD_LOGINENABLED));   
        
        
        //user roles
        $objGroups = new TSysCMSUsersRoles();
        $objGroups->find(TSysCMSUsersRoles::FIELD_ISANONYMOUS, false); //exclude anonymous users
        $objGroups->sort(TSysCMSUsersRoles::FIELD_ROLENAME);
        $objGroups->limitNone();
        $objGroups->loadFromDB();
        $objGroups->generateHTMLSelect($this->getModel()->get(TSysCMSUsers::FIELD_USERROLEID), $this->objOptGroup);

        //user accounts
        $objAccounts = new TSysCMSOrganizations();
        $objAccounts->sort(TSysCMSOrganizations::FIELD_CUSTOMID);
        $objAccounts->limitNone();
        $objAccounts->loadFromDB();
        $objAccounts->generateHTMLSelect($this->getModel()->get(TSysCMSUsers::FIELD_CMSORGANISATIONSID), $this->objOptAccount);

        //==== localization
        

        //language
        $objLangs = new TSysLanguages();
        $objLangs->sort(TSysLanguages::FIELD_LANGUAGE);
        $objLangs->limitNone();
        $objLangs->loadFromDBByCMSLanguage();
        $objLangs->generateHTMLSelect($this->getModel()->get(TSysCMSUsers::FIELD_LANGUAGEID), $this->objOptLanguage);
        
        //timezones
        $arrTimezones = array();
        $arrTimezones = DateTimeZone::listIdentifiers();
        $this->objOptTimezone->generateFromArray($arrTimezones, false, $this->getModel()->getTimeZone());

        //date format short
        $this->objEdtDateFormatShort->setValue($this->getModel()->getDateFormatShort());

        //date format long
        $this->objEdtDateFormatLong->setValue($this->getModel()->getDateFormatLong());

        //time format short
        $this->objEdtTimeFormatShort->setValue($this->getModel()->getTimeFormatShort());

        //time format long
        $this->objEdtTimeFormatLong->setValue($this->getModel()->getTimeFormatLong());

        //week starts on
        $this->objOptWeekStartsOn->generateDaysOfTheWeek($this->getModel()->getWeekStartsOn());

        //thousand separator
        $this->objEdtThousandSeparator->setValue($this->getModel()->getThousandSeparator());

        //decimal separator
        $this->objEdtDecimalSeparator->setValue($this->getModel()->getDecimalSeparator());


        //==== sessions
        $this->populateSessions();

        //==== login history
        $this->populateLoginHistory();        

    }
    
   /**
     * is called when a record is loaded
     */
    public function onLoad()
    {
        //needs to change password?
        if (!$this->getModel()->getNew())
            if ($this->getModel()->needsNewPassword())
                sendMessageError(transm($this->getModule(), 'message_user_needstochangepassword', 'This user needs to change password for security reasons'));


        if (isset($_GET[ACTION_VARIABLE_ID])) //not a new record (we don't have sessions yet)
        {
            if (is_numeric($_GET[ACTION_VARIABLE_ID]))
            {
                //sesions
                $this->objUserSessions->newQuery();
                $this->objUserSessions->find(TSysCMSUsersSessions::FIELD_USERID, $_GET[ACTION_VARIABLE_ID]);
                $this->objUserSessions->limit(0);//no limit
                $this->objUserSessions->loadFromDB();

                //history
                $this->objUserLoginHistory->newQuery();
                $this->objUserLoginHistory->find(TSysCMSUsersSessions::FIELD_USERID, $_GET[ACTION_VARIABLE_ID]);
                $this->objUserLoginHistory->limit(0);//no limit
                $this->objUserLoginHistory->loadFromDB();
            }
      
        }                
    }
    
    /**
     * is called when a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * @return boolean it will NOT SAVE
     */
    public function onSavePre()
    {                        
        //if username not unique
        if ($this->getModel()->isUsernameTakenDB($this->objEdtUsername->getValueSubmitted()))
        {
            sendMessageError(transm($this->getModule(), 'message_usernamenotunique', 'User NOT SAVED, choose another username'));//don't give a reason due to security reasons         
            return false;
        }
        
        //check current password
        if (!$this->objEdtPasswordOld->getValueSubmittedEmpty())
        {
            $objTempUser = new TSysCMSUsers();
            if (!$objTempUser->loadFromDBByUsername($this->objEdtUsername->getValueSubmitted()))
                return false;
            if (!password_verify($this->objEdtPasswordOld->getValueSubmitted(), $objTempUser->getPasswordEncrypted()))
            {   
                sendMessageError(transm($this->getModule(), 'message_passwordold_isnotcorrect', 'User NOT SAVED, your current password isn\'t correct'));
                return false;
            }
        }    
        
        //password and password repeat need to match
        if ($this->objEdtPasswordNew->getValueSubmitted() != $this->objEdtPasswordRepeat->getValueSubmitted())
        {
            sendMessageError(transm($this->getModule(), 'message_passwords_donotmatch', 'User NOT SAVED, passwords do not match. The two passwords need to match in order to set the new password'));//don't give a reason due to security reasons         
            return false;
        }        
        
        return true;
    }
    
    /**
     * is called AFTER a record is saved
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return boolean returns true on success otherwise false
     */
    public function onSavePost($bWasSaveSuccesful){ return true; }


    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
        $this->objUserSessions = new TSysCMSUsersSessions();
        $this->objUserLoginHistory = new TSysCMSUsersHistory();
    }  

    /**
     * sometimes you don;t want to user the checkin checkout system, even though the model supports it
     * for example: the settings.
     * The user needs to be able to navigate through the tabsheets, without locking records
     * 
     * ATTENTION: if this method returns true and the model doesn't support it: the checkinout will NOT happen!
     * 
     * @return bool return true if you want to use the check-in/checkout-system
     */
    public function getUseCheckinout()
    {
        return true;
    }    


    /**
     * populate sessions
     * we need to call them twice: once on form load and once when clicked save and sessions are deleted
     */
    private function populateSessions()
    {
        global $objAuthenticationSystem;
        $this->objLblHintSessions1 = new Label();  
        $this->objLblHintSessions2 = new Label();  
        $sTransSectionSessions = '';
        $sTransSectionSessions = transm($this->getModule(), 'section_sessions_title', 'Open login sessions');
        $sTempTextCheckbox = '';

        if (isset($_GET[ACTION_VARIABLE_ID])) //existing record
        {
            $objSessions = $this->objUserSessions;//speed things up

            //label explanation
            if ($objSessions->count() > 0)
            {
                $this->objLblHintSessions1->setText(transm($this->getModule(), 'form_label_sessions_userloggedinhere', 'User is logged in [times] time(s) on these devices:', 'times', $objSessions->count()));
                $this->objLblHintSessions2->setText(transm($this->getModule(), 'form_label_sessions_checktodelete', '(to delete session: click checkbox and click save)'));
            }
            else
            {
                $this->objLblHintSessions1->setText(transm($this->getModule(), 'form_label_sessions_nosessions', 'User isn\'t logged in anywhere'));
            }
            $this->getFormGenerator()->add($this->objLblHintSessions1, $sTransSectionSessions);        
            $this->getFormGenerator()->add($this->objLblHintSessions2, $sTransSectionSessions);        

            //display sessions
            $objSessions->resetRecordpointer();
            while($objSessions->next())
            {             
                $objCheckbox = new InputCheckbox();
                $objCheckbox->setNameAndID('chkSession'.$objSessions->getRandomID());
                
                $sTempTextCheckbox = '';
                $sTempTextCheckbox.= $objSessions->getSessionStarted()->getDateTimeAsString($this->getDateTimeFormatDefault()) . ': '.$objSessions->getIPAddress().', '.$objSessions->getBrowser().', '.$objSessions->getOperatingSystem();
    
                if ($objSessions->getRandomID() == $objAuthenticationSystem->getUserSessions()->getRandomID())
                {
                    $sTempTextCheckbox.= ' '.transm($this->getModule(), 'message_sessions_iscurrentsession', '(CURRENT SESSION)');
                    $objCheckbox->setDisabled(true);
                }

                $this->getFormGenerator()->add($objCheckbox, $sTransSectionSessions, $sTempTextCheckbox);
                unset($objCheckbox);//I don't save the checkboxes in an array or something, when we need them, we create new ones
            }   
            
        }
    }

    /**
     * populate login history
     */
    private function populateLoginHistory()
    {
        $this->objLblHintHistory = new Label();  
        $sTransSectionLoginHistory = '';
        $sTransSectionLoginHistory = transm($this->getModule(), 'section_loginhistory_title', 'Login history');

        if (isset($_GET[ACTION_VARIABLE_ID])) //existing record
        {
            $objHistory = $this->objUserLoginHistory;//speed things up

            //label explanation
            if ($objHistory->count() > 0)
            {
                $this->objLblHintHistory->setText(transm($this->getModule(), 'form_label_loginhistory_explanation', 'User has [times] entries in history on the following devices:', 'times', $this->objUserLoginHistory->count()));
            }
            else
            {
                $this->objLblHintHistory->setText(transm($this->getModule(), 'form_label_loginhistory_nosessions', 'User hasn\'t any history yet'));
            }
            $this->getFormGenerator()->add($this->objLblHintHistory, $sTransSectionLoginHistory);        

            //display history
            $objHistory->resetRecordpointer();
            while($objHistory->next())
            {             
                $objHistoryLine = new Label();
                $objHistoryLine->setNameAndID('lblHistory'.$objHistory->getRandomID());
                $objHistoryLine->setText('• '.$objHistory->getUsername().' '.$objHistory->getDateCreated()->getDateTimeAsString($this->getDateTimeFormatDefault()) . ': '.$objHistory->getIPAddress().', '.$objHistory->getUserAgent(). ' - '.$objHistory->getNotes());

                $this->getFormGenerator()->add($objHistoryLine, $sTransSectionLoginHistory);
                unset($objHistoryLine);//I don't save the labels in an array or something, when we need them, we create new ones
            }   
            
        }
    }



   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysCMSUsers(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsave.php';
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
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php';
    }

    /**
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        return 'list_users';
    }

    /**
     * return page title
     * This title is different for creating a new record and editing one.
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "create a new user" or "edit user John" (based on if $objModel->getNew())
     *
     * @return string
     */
    public function getTitle()
    {
        //global CMS_CURRENTMODULE;

        if ($this->getModel()->getNew())   
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_user_new', 'Create new user');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_user_edit', 'Edit user: [username]', 'username', $this->getModel()->getUsername());   
    }

    /**
     * show tabsheets on top of the page?
     *
     * @return bool
     */
    public function showTabs()
    {
        return false;
    }    

}
