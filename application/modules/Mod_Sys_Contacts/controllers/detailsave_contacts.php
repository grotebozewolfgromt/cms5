<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Contacts\controllers;

use dr\classes\controllers\TAJAXFormController;
use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\controllers\TCRUDDetailSaveControllerAJAX;
use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\form\Textarea;
use dr\classes\dom\tag\webcomponents\DRInputText;
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
use dr\classes\dom\tag\webcomponents\DRInputDateTime;
use dr\classes\dom\validator\TCharacterWhitelist;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TOnlyNumeric;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TLowercase;
use dr\classes\dom\validator\TUppercase;
use dr\classes\dom\validator\TUppercaseFirstChar;
use dr\classes\patterns\TModuleAbstract;
use dr\classes\types\TDateTime;
//don't forget ;)
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_Blog\controllers\detailsave_blog;
use dr\modules\Mod_Sys_Contacts\Mod_Sys_Contacts;
use dr\modules\Mod_Sys_Contacts\models\TSysContactsLastNamePrefixes;
use dr\modules\Mod_Sys_Contacts\models\TSysContactsSalutations;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_contacts extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtRecordId = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtNiceId = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtCustomIdentifier = null;//dr\classes\dom\tag\form\DRInputText
    private $objTagKeywords = null;
    private $objEdtCompanyName = null;//dr\classes\dom\tag\form\DRInputText
    private $objCbxSalutations = null; //DRCombobox
    private $objEdtFirstNameInitials = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtLastName = null;//dr\classes\dom\tag\form\DRInputText
    private $objCbxLastNamePrefix = null; //DRCombobox
    private $objEdtEmailAddress = null;//dr\classes\dom\tag\form\DRInputText
    private $objChkOnMailingList = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkOnBlackList = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objCbxCountryCodePhone1 = null;//dr\classes\dom\tag\form\DRCombobox
    private $objEdtPhone1 = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtPhone1Note = null;//dr\classes\dom\tag\form\DRInputText
    private $objCbxCountryCodePhone2 = null;//dr\classes\dom\tag\form\DRCombobox    
    private $objEdtPhone2 = null;//dr\classes\dom\tag\form\DRInputText    
    private $objEdtPhone2Note = null;
    private $objEdtChamberCommerce = null;//dr\classes\dom\tag\form\DRInputText        
    private $objTxtArNotes = null;//dr\classes\dom\tag\form\Textarea
    private $objDTFirstContact = null;  //dr\classes\dom\tag\webcomponents\DRInputDateTime
    private $objDTLastContact = null;  //dr\classes\dom\tag\webcomponents\DRInputDateTime
    
    private $objEdtBillingAddressMisc = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtBillingAddressStreet = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtBillingPostalCode = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtBillingCity = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtBillingStateRegion = null;//dr\classes\dom\tag\form\DRInputText
    private $objCbxBillingCountryID = null;//dr\classes\dom\tag\form\Select
    private $objEdtBillingVatNumber = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtBillingEmailAddress = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtBillingBankAccountNo = null;//dr\classes\dom\tag\form\DRInputText    
    private $objEdtBillingBIC = null;//dr\classes\dom\tag\form\DRInputText    

    private $objEdtDeliveryAddressMisc = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtDeliveryAddressStreet = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtDeliveryPostalCode = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtDeliveryCity = null;//dr\classes\dom\tag\form\DRInputText
    private $objEdtDeliveryStateRegion = null;//dr\classes\dom\tag\form\DRInputText
    private $objCbxDeliveryCountryID = null;//dr\classes\dom\tag\form\Select

    private $objChkIsClient = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkIsSupplier = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkAllowedPurchaseCredit = null;//dr\classes\dom\tag\form\InputCheckbox

    private $objCountries = null;
    private $objSalutations = null;
    private $objLastNamePrefixes = null;

    private $iDefaultCountryID = 0;
    private $iDefaultSalutationID = 0;
    private $iDefaultLastNamePrefixID = 0;

    private $sTextFieldEncryptedNo = '';
    private $sTextFieldEncryptedYes = '';


    public function initModel()
    {
        $this->objModel->setSalutationID($this->iDefaultSalutationID);

        $this->objModel->setLastNamePrefixID($this->iDefaultLastNamePrefixID);

        $this->objModel->setBillingCountryID($this->iDefaultCountryID);
        $this->objModel->setDeliveryCountryID($this->iDefaultCountryID);
        $this->objModel->setCountryIDCodePhoneNumber1($this->iDefaultCountryID);
        $this->objModel->setCountryIDCodePhoneNumber2($this->iDefaultCountryID);


        $objNow = new TDateTime(time());
        $this->objModel->setFirstContact($objNow);
        $this->objModel->setLastContact($objNow);
    }


    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        global $objAuthenticationSystem;

        // $sFormSectionType = '';
        // $sFormSectionType = transm(APP_ADMIN_CURRENTMODULE, 'form_section_general', 'General');
        $sFormSectionBusiness = '';
        $sFormSectionBusiness = transm(APP_ADMIN_CURRENTMODULE, 'form_section_business', 'Business');
        $sFormSectionId = '';
        $sFormSectionId = transm(APP_ADMIN_CURRENTMODULE, 'form_section_identification', 'Indentification');
        $sFormSectionPersonal = '';
        $sFormSectionPersonal = transm(APP_ADMIN_CURRENTMODULE, 'form_section_personal', 'Personal');
        $sFormSectionBilling = '';
        $sFormSectionBilling = transm(APP_ADMIN_CURRENTMODULE, 'form_section_billing', 'Address');
        $sFormSectionDelivery = '';
        $sFormSectionDelivery = transm(APP_ADMIN_CURRENTMODULE, 'form_section_delivery', 'Delivery');
        $sFormSectionMisc = '';
        $sFormSectionMisc = transm(APP_ADMIN_CURRENTMODULE, 'form_section_misc', 'Miscellaneous');

        $this->sTextFieldEncryptedNo = transm(APP_ADMIN_CURRENTMODULE, 'form_iconinfo_encrypted_no', '<br><br>Encrypted: no<br>Searchable: yes<br>Exposes data in data breach: yes');
        $this->sTextFieldEncryptedYes =  transm(APP_ADMIN_CURRENTMODULE,  'form_iconinfo_encrypted_yes', '<br><br>Encrypted: yes<br>Searchable: no<br>Exposes data in data breach: no');

            //record id
        $this->objEdtRecordId = new DRInputText();
        $this->objEdtRecordId->setNameAndID('edtRecordId');
        $this->objEdtRecordId->setClass('quarterwidthtag');    
        $this->objEdtRecordId->setReadOnly(true);             
        $this->getFormGenerator()->addQuick($this->objEdtRecordId, $sFormSectionId, transm(APP_ADMIN_CURRENTMODULE, 'form_field_recordid', 'Contact id'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_recordid_iconinfo', 'This is a unique number used internally to identify this contact in [application].<br>This number is automatically assigned by [application].<br>You can NOT change this number.<br><br>Be aware that this number is enumerable, meaning that malicious actors can use this information to access other records.<br>Malicious actors know that when id 100 exists that probably 99 and 101 also exist.<br>To counter this, you can use Nice Id.[encrypt]','application', APP_APPLICATIONNAME, 'encrypt', $this->getTextEncryptedIconInfo(false)));

            //Nice id
        $this->objEdtNiceId = new DRInputText();
        $this->objEdtNiceId->setNameAndID('edtNiceId');
        $this->objEdtNiceId->setClass('quarterwidthtag');    
        $this->objEdtNiceId->setReadOnly(true);               
        $this->getFormGenerator()->addQuick($this->objEdtNiceId, $sFormSectionId, transm(APP_ADMIN_CURRENTMODULE, 'form_field_niceid', 'Nice Id'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_niceid_iconinfo', 'This is a unique alfanumeric identifier to identify this contact in [application].<br>This identifier is automatically assigned.<br>This identifier is random and therefore not enumerable, hence safer to work with.<br><br>You can not change this identifyer.<br>If you want to, use Custom Id instead.[encrypt]','application', APP_APPLICATIONNAME, 'encrypt', $this->getTextEncryptedIconInfo(false)));


            //custom identifier
        $this->objEdtCustomIdentifier = new DRInputText();
        $this->objEdtCustomIdentifier->setNameAndID('edtCustomIdentifier');
        $this->objEdtCustomIdentifier->setClass('quarterwidthtag');         
        $this->objEdtCustomIdentifier->setMaxLength(50);                
        $objValidator = new TMaximumLength(50);
        $this->objEdtCustomIdentifier->addValidator($objValidator);    
        // $this->getFormGenerator()->add($this->objEdtCustomIdentifier, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_customid', 'Custom Id (to identify this contact just to you, so you can search for it)'));
        $this->getFormGenerator()->addQuick($this->objEdtCustomIdentifier, $sFormSectionId, transm(APP_ADMIN_CURRENTMODULE, 'form_field_customid', 'Custom Id'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_customid_iconinfo', 'To identify this contact just to you, so you can search for it.<br>This can be your own a in-house customer id, account id, connection id etc.<br>It needs to be something that makes sense to you.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));


            //search keywords
        $this->objTagKeywords = new DRInputText();
        $this->objTagKeywords->setNameAndID('edtTagSearchKeywords');
        $this->objTagKeywords->setClass('fullwidthtag');         
        $this->objTagKeywords->setMaxLength(255);                
        $objValidator = new TMaximumLength(255);
        $this->objTagKeywords->addValidator($objValidator);    
        // $this->getFormGenerator()->add($this->objTagKeywords, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_searchkeywords', 'Search keywords'));
        $this->getFormGenerator()->addQuick($this->objTagKeywords, $sFormSectionId, transm(APP_ADMIN_CURRENTMODULE, 'form_field_searchkeywords', 'Search keywords'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_searchkeywords_iconinfo', 'Enter search keywords to find this contact.<br>To help you find your contacts, you can use search keywords to identify this contact only to you.<br>Some keywords can be generated automatically when saving.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));


            //company name
        $this->objEdtCompanyName = new DRInputText();
        $this->objEdtCompanyName->setNameAndID('edtCompanyName');
        $this->objEdtCompanyName->setClass('fullwidthtag');                         
        $this->objEdtCompanyName->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtCompanyName->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtCompanyName->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtCompanyName, $sFormSectionBusiness, transm(APP_ADMIN_CURRENTMODULE, 'form_field_companyname', 'Company name', transm(APP_ADMIN_CURRENTMODULE, 'form_field_companyname_iconinfo', '[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)))); 
        $this->getFormGenerator()->addQuick($this->objEdtCompanyName, $sFormSectionBusiness, transm(APP_ADMIN_CURRENTMODULE, 'form_field_companyname', 'Company name'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_companyname_iconinfo', 'Name of the company.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));

    
            //chamber of commerce number
        $this->objEdtChamberCommerce = new DRInputText();
        $this->objEdtChamberCommerce->setNameAndID('edtChamberOfCommerceNumber');
        $this->objEdtChamberCommerce->setClass('halfwidthtag');                         
        $this->objEdtChamberCommerce->setMaxLength(25);    
        $objValidator = new TMaximumLength(25);
        $this->objEdtChamberCommerce->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtChamberCommerce->addValidator($objValidator);     
        $this->getFormGenerator()->addQuick($this->objEdtChamberCommerce, $sFormSectionBusiness, transm(APP_ADMIN_CURRENTMODULE, 'form_field_chamberofcommerceno', 'Chamber of commerce #'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_chamberofcommerceno_iconinfo', 'Chamber of commerce registration number.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(true)));
                
            //billing: vat no
        $this->objEdtBillingVatNumber = new DRInputText();
        $this->objEdtBillingVatNumber->setNameAndID('edtBillingVATNumber');
        $this->objEdtBillingVatNumber->setClass('fullwidthtag');                         
        $this->objEdtBillingVatNumber->setMaxLength(20);    
        $objValidator = new TMaximumLength(20);
        $this->objEdtBillingVatNumber->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingVatNumber->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtBillingVatNumber, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingvatno', 'VAT number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingVatNumber, $sFormSectionBusiness, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingvatno', 'VAT number'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingvatno_iconinfo', 'VAT number or (sales) tax id.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(true)));

            //salutations
        $this->objCbxSalutations = new DRInputCombobox();
        $this->objCbxSalutations->setNameAndID('cbxSalutations');
        $this->objCbxSalutations->setClass('quarterwidthtag'); 
        $this->getFormGenerator()->addQuick($this->objCbxSalutations, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_salutations', 'Salutation'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_salutation_iconinfo', 'How do you whish to address this contact? Mr. Mrs. Ms.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));


            //first name
        $this->objEdtFirstNameInitials = new DRInputText();
        $this->objEdtFirstNameInitials->setNameAndID('edtFirstName');
        $this->objEdtFirstNameInitials->setClass('fullwidthtag');                         
        $this->objEdtFirstNameInitials->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtFirstNameInitials->addValidator($objValidator);    
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtFirstNameInitials->addValidator($objValidator);    
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtFirstNameInitials->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtFirstNameInitials, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_firstnameinitials', 'Initials'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_firstnameinitials_iconinfo', 'First name or initials.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false))); 
        $this->getFormGenerator()->addQuick($this->objEdtFirstNameInitials, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_firstnameinitials', 'Initials'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_firstnameinitials_iconinfo', 'First name or initials.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(true)));

            //last name prefix
        // $this->objEdtLastNamePrefix = new DRInputText();
        // $this->objEdtLastNamePrefix->setNameAndID('edtLastNamePrefix');
        // // $this->objEdtLastName->setClass('fullwidthtag');                         
        // $this->objEdtLastNamePrefix->setMaxLength(20);    
        // $objValidator = new TMaximumLength(20);
        // $this->objEdtLastNamePrefix->addValidator($objValidator);          
        // $this->objEdtLastNamePrefix->setOnchange("validateField(this, true)");
        // $this->objEdtLastNamePrefix->setOnkeyup("setDirtyRecord()");                            
        // // $this->getFormGenerator()->add($this->objEdtLastNamePrefix, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastnameprefix', 'Last name prefix (van de, von der etc)')); 
        // $this->getFormGenerator()->addQuick($this->objEdtLastNamePrefix, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastnameprefix', 'Last name prefix'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastnameprefix_iconinfo', 'Like: van de, von der etc, von.<br>This data not encrypted, thus searchable.'));
            //last name prefix
        $this->objCbxLastNamePrefix = new DRInputCombobox();
        $this->objCbxLastNamePrefix->setNameAndID('cbxLastNamePrefix');
        $this->objCbxLastNamePrefix->setClass('quarterwidthtag'); 
        $this->getFormGenerator()->addQuick($this->objCbxLastNamePrefix, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastnameprefix', 'Last name prefix'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastnameprefix_iconinfo', 'Like: van de, van, von der etc, von.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));
    

            //last name
        $this->objEdtLastName = new DRInputText();
        $this->objEdtLastName->setNameAndID('edtLastName');
        $this->objEdtLastName->setClass('fullwidthtag');                         
        $this->objEdtLastName->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtLastName->addValidator($objValidator);   
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtLastName->addValidator($objValidator);           
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtLastName->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtLastName, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastname', 'Last name (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtLastName, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastname', 'Last name'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_lastname_iconinfo', 'Last name or surname.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME)));


            //email
        $this->objEdtEmailAddress = new DRInputText();
        $this->objEdtEmailAddress->setNameAndID('edtEmailAddress');
        $this->objEdtEmailAddress->setClass('fullwidthtag');                         
        $this->objEdtEmailAddress->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtEmailAddress->addValidator($objValidator); 
        $objValidator = new TEmailAddress(true, true, true);
        $this->objEdtEmailAddress->addValidator($objValidator); 
        $objValidator = new TLowercase();
        $this->objEdtEmailAddress->addValidator($objValidator);         
        $this->getFormGenerator()->addQuick($this->objEdtEmailAddress, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_emailaddress', 'Email address'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_emailaddress_iconinfo', 'Personal email address.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS)));

            //country code phone1
        $this->objCbxCountryCodePhone1 = new DRInputCombobox();
        // $this->objCbxCountryCodePhone1->setClass('quarterwidthtag');
        $this->objCbxCountryCodePhone1->setNameAndID('selCountryCodePhone1');
        // $this->getFormGenerator()->add($this->objCbxCountryCodePhone1, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_countrycodephone1', 'Country code'));

            //phone1
        $this->objEdtPhone1 = new DRInputText();
        $this->objEdtPhone1->setNameAndID('edtPhone1');
        $this->objEdtPhone1->setClass('quaterwidthtag');                         
        $this->objEdtPhone1->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtPhone1->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_NUMERIC.' -');
        $this->objEdtPhone1->addValidator($objValidator);                 
        $this->objEdtPhone1->setOnchange("validateField(this, true, '".$this->objCbxCountryCodePhone1->getId()."')");
        // $this->getFormGenerator()->addArray(array($this->objCbxCountryCodePhone1, $this->objEdtPhone1), $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber1', 'Phone number 1 (including area code, starting with 0)'), true, '', false,  transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber1_infoicon', '<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Encrypted, not searchable</li></ul>'));


            //phone 1 note
        $this->objEdtPhone1Note = new DRInputText();
        $this->objEdtPhone1Note->setNameAndID('edtPhone1Note');
        $this->objEdtPhone1Note->setClass('quaterwidthtag');                         
        $this->objEdtPhone1Note->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtPhone1Note->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -');
        $this->objEdtPhone1Note->addValidator($objValidator);                 
        $this->getFormGenerator()->addArray(array($this->objCbxCountryCodePhone1, $this->objEdtPhone1, $this->objEdtPhone1Note), $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber1', 'Phone 1: Country code, phone number and notes'), true, '', false,  transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber1_infoicon', 'Country code, phone number and notes.<br>Rules:<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Separate area code and subscriber number with a dash (-)</li><li>Notes could be:<ul><li>only after 9pm</li><li>phone number brother</li><li>Only send text messages to this number</li></ul></li></ul>[encrypted]', 'encrypted', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER)));


            //country code phone2
        $this->objCbxCountryCodePhone2 = new DRInputCombobox();
        // $this->objCbxCountryCodePhone2->setClass('quarterwidthtag');
        $this->objCbxCountryCodePhone2->setNameAndID('selCountryCodePhone2');
        // $this->getFormGenerator()->add($this->objCbxCountryCodePhone2, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_countrycodephone2', 'Country code'));

            //phone2
        $this->objEdtPhone2 = new DRInputText();
        $this->objEdtPhone2->setNameAndID('edtPhone2');
        $this->objEdtPhone2->setClass('quaterwidthtag');                         
        $this->objEdtPhone2->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtPhone2->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_NUMERIC.' -');
        $this->objEdtPhone2->addValidator($objValidator);         
        // $this->objEdtPhone2->setOnchange("validateField(this, true)");
        // $this->getFormGenerator()->add($this->objEdtPhone2, $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2', 'Phone number 2 (encrypted, not searchable)')); 
        $this->objEdtPhone2->setOnchange("validateField(this, true, '".$this->objCbxCountryCodePhone2->getId()."')");
        // $this->getFormGenerator()->addArray(array($this->objCbxCountryCodePhone2, $this->objEdtPhone2), $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2', 'Phone number 2 (including area code, starting with 0)'), true, '', false,  transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2_infoicon', '<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Encrypted, not searchable</li></ul>'));
    
            //phone 2 note
        $this->objEdtPhone2Note = new DRInputText();
        $this->objEdtPhone2Note->setNameAndID('edtPhone2Note');
        $this->objEdtPhone2Note->setClass('quaterwidthtag');                         
        $this->objEdtPhone2Note->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtPhone2Note->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -');
        $this->objEdtPhone2Note->addValidator($objValidator);                 
        // $this->getFormGenerator()->addArray(array($this->objCbxCountryCodePhone2, $this->objEdtPhone2, $this->objEdtPhone2Note), $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2', 'Phone number 2 (including area code, starting with 0)'), true, '', false,  transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2_infoicon', 'Rules:<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Separate area code and subscriber number with a dash (-)</li><li>Encrypted, not searchable</li><li>3rd field is for notes, like:<ul><li>only after 9pm</li><li>= phonenumber brother</li><li>Only send text messages to this number</li></ul></li></ul>'));
        $this->getFormGenerator()->addArray(array($this->objCbxCountryCodePhone2, $this->objEdtPhone2, $this->objEdtPhone2Note), $sFormSectionPersonal, transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2', 'Phone 2: Country code, phone number and notes'), true, '', false,  transm(APP_ADMIN_CURRENTMODULE, 'form_field_phonenumber2_infoicon', 'Country code, phone number and notes.<br>Rules:<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Separate area code and subscriber number with a dash (-)</li><li>Notes could be:<ul><li>only after 9pm</li><li>phone number brother</li><li>Only send text messages to this number</li></ul></li></ul>[encrypted]', 'encrypted', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER)));

            //billing: country
        $this->objCbxBillingCountryID = new DRInputCombobox();
        $this->objCbxBillingCountryID->setNameAndID('optBillingCountryID');
        // $this->getFormGenerator()->add($this->objCbxBillingCountryID, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingcountry', 'Country'));
        $this->getFormGenerator()->addQuick($this->objCbxBillingCountryID, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingcountry', 'Country'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingcountry_iconinfo', 'Country or residence.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));

            //billing: address line 2: street
        $this->objEdtBillingAddressStreet = new DRInputText();
        $this->objEdtBillingAddressStreet->setNameAndID('edtBillingAddressLineStreet');
        $this->objEdtBillingAddressStreet->setClass('fullwidthtag');                         
        $this->objEdtBillingAddressStreet->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtBillingAddressStreet->addValidator($objValidator); 
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtBillingAddressStreet->addValidator($objValidator);      
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingAddressStreet->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtBillingAddressStreet, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_BILLINGADDRESSSTREET', 'Street + house number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingAddressStreet, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingaddressstreet', 'Street + house number'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingaddressstreet_iconinfo', 'Street and house number.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS)));


            //billing: address line 1: misc
        $this->objEdtBillingAddressMisc = new DRInputText();
        $this->objEdtBillingAddressMisc->setNameAndID('edtBillingAddressLineMisc');
        $this->objEdtBillingAddressMisc->setClass('fullwidthtag');                         
        $this->objEdtBillingAddressMisc->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtBillingAddressMisc->addValidator($objValidator);    
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtBillingAddressMisc->addValidator($objValidator);                     
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingAddressMisc->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtBillingAddressMisc, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_BILLINGADDRESSMISC', 'Appt. building/ company dept. etc (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingAddressMisc, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingaddressmisc', 'Extra address info'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingaddressmisc_iconinfo', 'Apartment building, company dept, floor, 2nd red door on the left etc.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS)));

            
            //billing: postal code or zip
        $this->objEdtBillingPostalCode = new DRInputText();
        $this->objEdtBillingPostalCode->setNameAndID('edtBillingPostalCode');
        $this->objEdtBillingPostalCode->setClass('quarterwidthtag');                         
        $this->objEdtBillingPostalCode->setMaxLength(10);    
        $objValidator = new TMaximumLength(10);
        $this->objEdtBillingPostalCode->addValidator($objValidator);          
        $objValidator = new TUppercase();
        $this->objEdtBillingPostalCode->addValidator($objValidator); 
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.' ');
        $this->objEdtBillingPostalCode->addValidator($objValidator); 
        $this->objEdtBillingPostalCode->setOnchange("validateField(this, true, '".$this->objCbxBillingCountryID->getId()."')");
        $this->getFormGenerator()->addQuick($this->objEdtBillingPostalCode, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingpostalcodezip', 'Postal code/zip'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingpostalcodezip_iconinfo', 'Postal code or zip code.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP)));

    
            //billing: city
        $this->objEdtBillingCity = new DRInputText();
        $this->objEdtBillingCity->setNameAndID('edtBillingCity');
        $this->objEdtBillingCity->setClass('fullwidthtag');                         
        $this->objEdtBillingCity->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtBillingCity->addValidator($objValidator);    
        // $objValidator = new TUppercaseFirstChar(); //doesnt work with: 's-Hertogenbosch
        // $this->objEdtBillingCity->addValidator($objValidator); 
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingCity->addValidator($objValidator);     
        $this->getFormGenerator()->addQuick($this->objEdtBillingCity, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingcity', 'City'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingcity_iconinfo', 'City.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));        

            //billing: state/region
        $this->objEdtBillingStateRegion = new DRInputText();
        $this->objEdtBillingStateRegion->setNameAndID('edtBillingState');
        $this->objEdtBillingStateRegion->setClass('fullwidthtag');                         
        $this->objEdtBillingStateRegion->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtBillingStateRegion->addValidator($objValidator);          
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtBillingStateRegion->addValidator($objValidator);           
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingStateRegion->addValidator($objValidator);     
        // $this->getFormGenerator()->add($this->objEdtBillingStateRegion, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingstateregion', 'State/region/provice'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingstateregion_iconinfo', 'State, region or province.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false))); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingStateRegion, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingstateregion', 'State/region/provice'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingstateregion_iconinfo', 'State, region or province.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));        
    
            //billing: bank account no
        $this->objEdtBillingBankAccountNo = new DRInputText();
        $this->objEdtBillingBankAccountNo->setNameAndID('edtBillingBankAccountNumber');
        $this->objEdtBillingBankAccountNo->setClass('halfwidthtag');                         
        $this->objEdtBillingBankAccountNo->setMaxLength(20);    
        $objValidator = new TMaximumLength(20);
        $this->objEdtBillingBankAccountNo->addValidator($objValidator);    
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingBankAccountNo->addValidator($objValidator);                
        $this->getFormGenerator()->addQuick($this->objEdtBillingBankAccountNo, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingbankaccountno', 'IBAN'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingbankaccountno_iconinfo', 'International Bank Account Number (IBAN) or account id.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(true)));
            
            //billing: BIC/SWIFT
        $this->objEdtBillingBIC = new DRInputText();
        $this->objEdtBillingBIC->setNameAndID('edtBIC');
        $this->objEdtBillingBIC->setClass('halfwidthtag');                         
        $this->objEdtBillingBIC->setMaxLength(20);    
        $objValidator = new TMaximumLength(20);
        $this->objEdtBillingBIC->addValidator($objValidator);    
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.' -().');
        $this->objEdtBillingBIC->addValidator($objValidator);                
        // $this->getFormGenerator()->addQuick($this->objEdtBillingBIC, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingbic', 'BIC/SWIFT'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingbic_iconinfo', 'BIC or SWIFT are bank identification codes for routing and identifying financial transactions.[encrypt]', $this->getTextEncryptedIconInfo(false)));
        $this->getFormGenerator()->addQuick($this->objEdtBillingBIC, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingbic', 'BIC/SWIFT'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingbic_iconinfo', 'BIC or SWIFT are bank identification codes for routing and identifying financial transactions.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));
            
            //billing: email
        $this->objEdtBillingEmailAddress = new DRInputText();
        $this->objEdtBillingEmailAddress->setNameAndID('edtBillingEmailAddress');
        $this->objEdtBillingEmailAddress->setClass('fullwidthtag');                         
        $this->objEdtBillingEmailAddress->setMaxLength(100);    
        $objValidator = new TEmailAddress(true, true, true);
        $this->objEdtBillingEmailAddress->addValidator($objValidator); 
        $objValidator = new TMaximumLength(100);
        $this->objEdtBillingEmailAddress->addValidator($objValidator);                      
        // $this->getFormGenerator()->add($this->objEdtBillingEmailAddress, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingemailaddress', 'Email address (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingEmailAddress, $sFormSectionBilling, transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingemailaddress', 'Billing email address'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_billingemailaddress_iconinfo', 'Email address for billing.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS)));
                        

            //delivery: country
        // $this->objCbxDeliveryCountryID = new Select();
        $this->objCbxDeliveryCountryID = new DRInputCombobox();
        $this->objCbxDeliveryCountryID->setNameAndID('optDeliveryCountryID');
        // $this->getFormGenerator()->add($this->objCbxDeliveryCountryID, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycountry', 'Country'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycountry_iconinfo', 'Country or residence.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));
        $this->getFormGenerator()->addQuick($this->objCbxDeliveryCountryID, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycountry', 'Country'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycountry_iconinfo', 'Country or residence.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));
           

            //delivery: address line 2
        $this->objEdtDeliveryAddressStreet = new DRInputText();
        $this->objEdtDeliveryAddressStreet->setNameAndID('edtDeliveryAddressLineStreet');
        $this->objEdtDeliveryAddressStreet->setClass('fullwidthtag');                         
        $this->objEdtDeliveryAddressStreet->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator); 
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator);                 
        // $this->getFormGenerator()->add($this->objEdtDeliveryAddressStreet, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSSTREET', 'Street + house number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryAddressStreet, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSSTREET', 'Street + house number'),  transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSSTREET_iconinfo', 'Street name and house number if it deviates from the billing address.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS)));


            //delivery: address line 1
        $this->objEdtDeliveryAddressMisc = new DRInputText();
        $this->objEdtDeliveryAddressMisc->setNameAndID('edtDeliveryAddressLineMisc');
        $this->objEdtDeliveryAddressMisc->setClass('fullwidthtag');                         
        $this->objEdtDeliveryAddressMisc->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtDeliveryAddressMisc->addValidator($objValidator);  
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryAddressMisc->addValidator($objValidator);
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator);                      
        // $this->getFormGenerator()->add($this->objEdtDeliveryAddressMisc, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSMISC', 'Appt. building/ company dept. etc (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryAddressMisc, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSMISC', 'Extra address info'),  transm(APP_ADMIN_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSMISC_iconinfo', 'Apartment building, company dept, floor, 2nd red door on the left etc.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS)));

            
            //delivery: postal code or zip
        $this->objEdtDeliveryPostalCode = new DRInputText();
        $this->objEdtDeliveryPostalCode->setNameAndID('edtDeliveryPostalCode');
        $this->objEdtDeliveryPostalCode->setClass('quarterwidthtag');                         
        $this->objEdtDeliveryPostalCode->setMaxLength(10);    
        $objValidator = new TMaximumLength(10);
        $this->objEdtDeliveryPostalCode->addValidator($objValidator);   
        $objValidator = new TUppercase();
        $this->objEdtDeliveryPostalCode->addValidator($objValidator);                   
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.' ');
        $this->objEdtDeliveryPostalCode->addValidator($objValidator);      
        $this->objEdtDeliveryPostalCode->setOnchange("validateField(this, true, '".$this->objCbxDeliveryCountryID->getId()."')");
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryPostalCode, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverypostalcodezip', 'Postal code/zip'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverypostalcodezip_iconinfo', 'Postal code or Zip code.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP)));
    
            //delivery: city
        $this->objEdtDeliveryCity = new DRInputText();
        $this->objEdtDeliveryCity->setNameAndID('edtDeliveryCity');
        $this->objEdtDeliveryCity->setClass('fullwidthtag');                         
        $this->objEdtDeliveryCity->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtDeliveryCity->addValidator($objValidator);   
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryCity->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryCity->addValidator($objValidator);                                
        // $this->getFormGenerator()->add($this->objEdtDeliveryCity, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycity', 'City'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycity_iconinfo', 'City of residence.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryCity, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycity', 'City'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverycity_iconinfo', 'City of residence.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));

            //delivery: state/region
        $this->objEdtDeliveryStateRegion = new DRInputText();
        $this->objEdtDeliveryStateRegion->setNameAndID('edtDeliveryState');
        $this->objEdtDeliveryStateRegion->setClass('fullwidthtag');                         
        $this->objEdtDeliveryStateRegion->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtDeliveryStateRegion->addValidator($objValidator);    
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryStateRegion->addValidator($objValidator); 
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryStateRegion->addValidator($objValidator);         
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryStateRegion, $sFormSectionDelivery, transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverystateregion', 'State/region/province'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_deliverystateregion_iconinfo', 'State, region or province.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));

            //is client
        $this->objChkIsClient = new InputCheckbox();
        $this->objChkIsClient->setNameAndID('chkIsClient');
        // $this->objChkIsClient->setOnkeyup("setDirtyRecord()");          
        $this->getFormGenerator()->add($this->objChkIsClient, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_isclient', 'is client'));   
            
            //is supplier
        $this->objChkIsSupplier = new InputCheckbox();
        $this->objChkIsSupplier->setNameAndID('chkIsSupplier');
        // $this->objChkIsSupplier->setOnkeyup("setDirtyRecord()");                  
        $this->getFormGenerator()->add($this->objChkIsSupplier,  $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_issupplier', 'is supplier'));         
    

            //on mailing list
        $this->objChkOnMailingList = new InputCheckbox();
        $this->objChkOnMailingList->setNameAndID('chkOnMailingList');
        // $this->objChkOnMailingList->setOnkeyup("setDirtyRecord()");                          
        $this->getFormGenerator()->add($this->objChkOnMailingList, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_onmailinglist', 'on mailing list'));   
        
                //on black list
        $this->objChkOnBlackList = new InputCheckbox();
        $this->objChkOnBlackList->setNameAndID('chkOnBlackList');
        // $this->objChkOnBlackList->setOnkeyup("setDirtyRecord()");                                  
        $this->getFormGenerator()->add($this->objChkOnBlackList, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_onblacklist', 'on blacklist'));   
    
                //allowed purchase on credit
        $this->objChkAllowedPurchaseCredit = new InputCheckbox();
        $this->objChkAllowedPurchaseCredit->setNameAndID('chkAllowedPurchaseOnCredit');
        // $this->objChkAllowedPurchaseCredit->setOnkeyup("setDirtyRecord()");                                  
        $this->getFormGenerator()->add($this->objChkAllowedPurchaseCredit, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_allowedpurchaseoncredit', 'allowed purchase on credit'));   
    


            //first contact
        $this->objDTFirstContact = new DRInputDateTime();
        $this->objDTFirstContact->setNameAndID('dtFirstContact');
        $this->objDTFirstContact->setAllowEmptyDateTime(true);
        $this->objDTFirstContact->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong());
        $this->objDTFirstContact->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong());
        $this->objDTFirstContact->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day      
        $this->getFormGenerator()->add($this->objDTFirstContact, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_first_contact', 'First contact')); 

            //last contact
        $this->objDTLastContact = new DRInputDateTime();
        $this->objDTLastContact->setNameAndID('dtLastContact');
        $this->objDTLastContact->setAllowEmptyDateTime(true);        
        $this->objDTLastContact->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong());
        $this->objDTLastContact->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong());
        $this->objDTLastContact->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->getFormGenerator()->add($this->objDTLastContact, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_last_contact', 'Last contact')); 

           //notes
        $this->objTxtArNotes = new Textarea();
        $this->objTxtArNotes->setNameAndID('txtArNotes');
        $this->objTxtArNotes->setClass('fullwidthtag');     
        $objValidator = new TCharacterWhitelist(WHITELIST_SAFE);
        $this->objTxtArNotes->addValidator($objValidator);         
        // $this->getFormGenerator()->add($this->objTxtArNotes, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_notes', 'Notes (only seen by you)'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_notes_iconinfo', 'Extra notes regarding contact.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));     
        $this->getFormGenerator()->addQuick($this->objTxtArNotes, $sFormSectionMisc, transm(APP_ADMIN_CURRENTMODULE, 'form_field_notes', 'Notes (only seen by you)'),  transm(APP_ADMIN_CURRENTMODULE, 'form_field_notes_iconinfo', 'Extra notes regarding contact.[encrypt]', 'encrypt', $this->getTextEncryptedIconInfo(false)));
    }

    /**
     * little helper function to return
     */
    private function getTextEncryptedIconInfo($bEncrypted)
    {
        if ($bEncrypted)
            return $this->sTextFieldEncryptedYes; //is cached before used
        else
            return $this->sTextFieldEncryptedNo; //is cached before used
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Contacts::PERM_CAT_CONTACTS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        $this->getModel()->set(TSysContacts::FIELD_ISCLIENT, $this->objChkIsClient->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_ISSUPPLIER, $this->objChkIsSupplier->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_ALLOWEDPURCHASEONCREDIT, $this->objChkAllowedPurchaseCredit->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_CUSTOMID, $this->objEdtCustomIdentifier->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_COMPANYNAME, $this->objEdtCompanyName->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, $this->objEdtChamberCommerce->getValueSubmitted(), '', true);

        $this->getModel()->set(TSysContacts::FIELD_SALUTATIONID, $this->objCbxSalutations->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_FIRSTNAMEINITALS, $this->objEdtFirstNameInitials->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_LASTNAME, $this->objEdtLastName->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME);
        $this->getModel()->set(TSysContacts::FIELD_LASTNAMEPREFIXID, $this->objCbxLastNamePrefix->getValueSubmitted());
        $this->getModel()->setEmailAddressDecrypted($this->objEdtEmailAddress->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_ONMAILINGLIST, $this->objChkOnMailingList->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_ONBLACKLIST, $this->objChkOnBlackList->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_COUNTRYIDCODEPHONE1, $this->objCbxCountryCodePhone1->getValueSubmittedAsInt());
        $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER1, $this->objEdtPhone1->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER);
        $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER1NOTE, $this->objEdtPhone1Note->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_COUNTRYIDCODEPHONE2, $this->objCbxCountryCodePhone2->getValueSubmittedAsInt());        
        $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER2, $this->objEdtPhone2->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER);        
        $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER2NOTE, $this->objEdtPhone2Note->getValueSubmitted());        
        $this->getModel()->set(TSysContacts::FIELD_NOTES, $this->objTxtArNotes->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_FIRSTCONTACT, $this->objDTFirstContact->getValueSubmittedAsTDateTimeISO());
        $this->getModel()->set(TSysContacts::FIELD_LASTCONTACT, $this->objDTLastContact->getValueSubmittedAsTDateTimeISO());

        $this->getModel()->set(TSysContacts::FIELD_BILLINGADDRESSMISC, $this->objEdtBillingAddressMisc->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGADDRESSSTREET, $this->objEdtBillingAddressStreet->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, $this->objEdtBillingPostalCode->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGCITY, $this->objEdtBillingCity->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGSTATEREGION, $this->objEdtBillingStateRegion->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGCOUNTRYID, $this->objCbxBillingCountryID->getValueSubmittedAsInt());
        $this->getModel()->set(TSysContacts::FIELD_VATNUMBER, $this->objEdtBillingVatNumber->getValueSubmitted(), '', true);
        $this->getModel()->setBillingEmailAddressDecrypted($this->objEdtBillingEmailAddress->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, $this->objEdtBillingBankAccountNo->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGBICSWIFT, $this->objEdtBillingBIC->getValueSubmitted());

        $this->getModel()->set(TSysContacts::FIELD_DELIVERYADDRESSMISC, $this->objEdtDeliveryAddressMisc->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS);
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYADDRESSSTREET, $this->objEdtDeliveryAddressStreet->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS);
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, $this->objEdtDeliveryPostalCode->getValueSubmitted(), '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP);
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYCITY, $this->objEdtDeliveryCity->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYSTATEREGION, $this->objEdtDeliveryStateRegion->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYCOUNTRYID, $this->objCbxDeliveryCountryID->getValueSubmittedAsInt());
    
        //==== correct for the Dutchies
        // //billing postal code
        // $objCountries = new TSysCountries();
        // $objCountries->loadFromDBByID($this->objCbxBillingCountryID->getValueSubmittedAsInt());
        // if ($objCountries->getISO2() == 'NL')
        //     $this->getModel()->set(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, formatPostalCodeDutch($this->objEdtBillingPostalCode->getValueSubmitted()), '', true);

        // //delivery postal code
        // $objCountries = new TSysCountries();
        // $objCountries->loadFromDBByID($this->objCbxDeliveryCountryID->getValueSubmittedAsInt());
        // if ($objCountries->getISO2() == 'NL')
        //     $this->getModel()->set(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, formatPostalCodeDutch($this->objEdtDeliveryPostalCode->getValueSubmitted()), '', true);

        // //phone 1
        // $objCountries = new TSysCountries();
        // $objCountries->loadFromDBByID($this->objCbxCountryCodePhone1->getValueSubmittedAsInt());
        // if ($objCountries->getISO2() == 'NL')
        //     $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER1, formatPhoneNumberDutch($this->objEdtPhone1->getValueSubmitted()), '', true);

        // //phone 2
        // $objCountries = new TSysCountries();
        // $objCountries->loadFromDBByID($this->objCbxCountryCodePhone2->getValueSubmittedAsInt());
        // if ($objCountries->getISO2() == 'NL')
        //     $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER2, formatPhoneNumberDutch($this->objEdtPhone2->getValueSubmitted()), '', true);
        

        //==== search keywords NEEDS TO BE LAST, because it uses the values we set earlier
        $this->getModel()->set(TSysContacts::FIELD_SEARCHKEYWORDS, $this->objModel->generateSearchKeywordsField($this->objTagKeywords->getValueSubmitted()));                
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        $this->objChkOnMailingList->setChecked($this->getModel()->get(TSysContacts::FIELD_ONMAILINGLIST));
        $this->objChkOnBlackList->setChecked($this->getModel()->get(TSysContacts::FIELD_ONBLACKLIST));
        $this->objChkIsClient->setChecked($this->getModel()->get(TSysContacts::FIELD_ISCLIENT));
        $this->objChkIsSupplier->setChecked($this->getModel()->get(TSysContacts::FIELD_ISSUPPLIER));
        $this->objChkAllowedPurchaseCredit->setChecked($this->getModel()->get(TSysContacts::FIELD_ALLOWEDPURCHASEONCREDIT));
        
        $this->objEdtRecordId->setValue($this->getModel()->get(TSysContacts::FIELD_ID));
        $this->objEdtNiceId->setValue($this->getModel()->get(TSysContacts::FIELD_NICEID));
        $this->objEdtCustomIdentifier->setValue($this->getModel()->get(TSysContacts::FIELD_CUSTOMID));
        $this->objTagKeywords->setValue($this->getModel()->get(TSysContacts::FIELD_SEARCHKEYWORDS));

        $this->objEdtCompanyName->setValue($this->getModel()->get(TSysContacts::FIELD_COMPANYNAME));
        $this->objEdtChamberCommerce->setValue($this->getModel()->get(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, '', true));

        if ($this->getModel()->getNew())
            $this->objSalutations->generateHTMLSelect($this->iDefaultSalutationID, $this->objCbxSalutations);    
        else
            $this->objSalutations->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_SALUTATIONID), $this->objCbxSalutations);    

        $this->objEdtFirstNameInitials->setValue($this->getModel()->get(TSysContacts::FIELD_FIRSTNAMEINITALS));
        $this->objEdtLastName->setValue($this->getModel()->get(TSysContacts::FIELD_LASTNAME, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME));
        if ($this->getModel()->getNew())
            $this->objLastNamePrefixes->generateHTMLSelect($this->iDefaultLastNamePrefixID, $this->objCbxLastNamePrefix);    
        else
            $this->objLastNamePrefixes->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_LASTNAMEPREFIXID), $this->objCbxLastNamePrefix);    
        $this->objEdtEmailAddress->setValue($this->getModel()->get(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS));        
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objCbxCountryCodePhone1);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_COUNTRYIDCODEPHONE1), $this->objCbxCountryCodePhone1);    
        $this->objEdtPhone1->setValue($this->getModel()->get(TSysContacts::FIELD_PHONENUMBER1, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER));   
        $this->objEdtPhone1Note->setValue($this->getModel()->get(TSysContacts::FIELD_PHONENUMBER1NOTE));   
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objCbxCountryCodePhone2);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_COUNTRYIDCODEPHONE2), $this->objCbxCountryCodePhone2);            
        $this->objEdtPhone2->setValue($this->getModel()->get(TSysContacts::FIELD_PHONENUMBER2, '', true));        
        $this->objEdtPhone2Note->setValue($this->getModel()->get(TSysContacts::FIELD_PHONENUMBER2NOTE));        

        $this->objTxtArNotes->setValue($this->getModel()->get(TSysContacts::FIELD_NOTES));
        $this->objDTFirstContact->setValueAsTDateTime($this->getModel()->get(TSysContacts::FIELD_FIRSTCONTACT));
        $this->objDTLastContact->setValueAsTDateTime($this->getModel()->get(TSysContacts::FIELD_LASTCONTACT));

        //billing adress
        $this->objEdtBillingAddressMisc->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGADDRESSMISC, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS));
        $this->objEdtBillingAddressStreet->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGADDRESSSTREET, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS));
        $this->objEdtBillingPostalCode->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP));
        $this->objEdtBillingCity->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGCITY));
        $this->objEdtBillingStateRegion->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGSTATEREGION));
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objCbxBillingCountryID);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_BILLINGCOUNTRYID), $this->objCbxBillingCountryID);    
        $this->objEdtBillingVatNumber->setValue($this->getModel()->get(TSysContacts::FIELD_VATNUMBER, '', true));
        $this->objEdtBillingEmailAddress->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS));
        $this->objEdtBillingBankAccountNo->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, '', true));
        $this->objEdtBillingBIC->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGBICSWIFT));

        //delivery address
        $this->objEdtDeliveryAddressMisc->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYADDRESSMISC, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS));
        $this->objEdtDeliveryAddressStreet->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYADDRESSSTREET, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS));
        $this->objEdtDeliveryPostalCode->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, '', APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP));
        $this->objEdtDeliveryCity->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYCITY));
        $this->objEdtDeliveryStateRegion->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYSTATEREGION));
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objCbxDeliveryCountryID);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_DELIVERYCOUNTRYID), $this->objCbxDeliveryCountryID);


        unset($objCountries);
    }



   /**
     * is called when a record is loaded
     */
    public function onLoadPost()
    {
               
    }
    
   /**
     * is called when a record is loaded
     */
    public function onLoadPre()
    {
        //salutations
        $this->objSalutations->sort(TSysContactsSalutations::FIELD_POSITION);
        $this->objSalutations->loadFromDB();

        while($this->objSalutations->next()) //we need all salutations (not only the default)
            if ($this->objSalutations->getIsDefault() == true)
                $this->iDefaultSalutationID = $this->objSalutations->getID();

        //last name prefixes
        $this->objLastNamePrefixes->sort(TSysContactsLastNamePrefixes::FIELD_POSITION);
        $this->objLastNamePrefixes->loadFromDB();

        while($this->objLastNamePrefixes->next()) //we need all salutations (not only the default)
            if ($this->objLastNamePrefixes->getIsDefault() == true)
                $this->iDefaultLastNamePrefixID = $this->objLastNamePrefixes->getID();

        //country
        $this->objCountries->sort(TSysCountries::FIELD_COUNTRYNAME);
        $this->objCountries->loadFromDB(); //we need all countries (not only the default)

        while($this->objCountries->next())
            if ($this->objCountries->getIsDefault() == true)
                $this->iDefaultCountryID = $this->objCountries->getID();

    
    }

    /**
     * is called BEFORE a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @return array, empty array = no errors
     */
    public function onSavePre() { return array(); }    

    /**
     * is called AFTER a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return array, empty array = no errors
     */
    public function onSavePost($bWasSaveSuccesful){ return array(); }
    
    
    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
        $this->objCountries = new TSysCountries();
        $this->objSalutations = new TSysContactsSalutations();
        $this->objLastNamePrefixes = new TSysContactsLastNamePrefixes();
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
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysContacts(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsaveajax.php';
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
        return 'list_contacts';
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
        //global APP_ADMIN_CURRENTMODULE;

        if ($this->getModel()->getNew())   
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_contact_new', 'Create new contact<dr-icon-info>To comply with data protection regulations, we encrypt sensitive personal data.<br>This is safe, but doesn\'t allow you to search for this data.<br><br>To help you find your contacts, you can use search keywords to identify this contact.<br>Some keywords can be generated automatically when saving, depending on your settings.<br><br>Be aware that these search keywords are stored without encryption, when a data breach occurs this information is exposed.</dr-icon-info>');
        else
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_contact_edit', 'Edit contact: [contact]<dr-icon-info>To comply with data protection regulations, we encrypt sensitive personal data.<br>This is safe, but doesn\'t allow you to search for this data.<br><br>To help you find your contacts, you can use search keywords to identify this contact.<br>Some keywords can be generated automatically when saving, depending on your settings.<br><br>Be aware that these search keywords are stored without encryption, when a data breach occurs this information is exposed.</dr-icon-info>', 'contact', $this->getModel()->getDisplayRecordShort());           
    }

    /**
     * show tabsheets on top of the page?
     *
     * @return bool
     */
    // public function showTabs()
    // {
    //     return false;
    // }    

    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd etc)
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return 'contacts';
    }        

 /**
     * is this user allowed to create this record?
     * 
     * CRUD: Crud
     */
    public function getAuthCreate()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_DELETE);
    }    


    /**
     * correct additional fields
     */
    protected function handleValidateField()
    {
        $objCountry = null;
        $sValueOtherField = "";
        $sIdOtherField = "";
        $sIdCurrentField = "";
        $arrJSONResponse = array();
        $bHandled = false;

        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_HTMLFIELDID] = $_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD];
        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = $_POST[$_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD]];                
        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_ERRORS] = array();


        //==== correct Dutch phone number and postal code
        if (isset($_POST[TAJAXFormController::JSON_VALIDATEREQUEST_FIELD_OTHERFIELDID]) && isset($_POST[TAJAXFormController::JSON_VALIDATEREQUEST_FIELD_OTHERFIELDVALUE]))
        {
            $sIdOtherField = $_POST[TAJAXFormController::JSON_VALIDATEREQUEST_FIELD_OTHERFIELDID];
            $sValueOtherField = $_POST[TAJAXFormController::JSON_VALIDATEREQUEST_FIELD_OTHERFIELDVALUE];
            $sIdCurrentField = $_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD];

            if (is_numeric($sValueOtherField))
            {
                $objCountry = new TSysCountries();
                $objCountry->loadFromDBByID($sValueOtherField); //we are only dealing with countryid's here, so I can safely assume the value is always a countyid
                if ($objCountry->getISO2() == 'NL')
                {
                    switch($sIdCurrentField)
                    {
                        case $this->objEdtPhone1->getID():
                            $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = formatPhoneNumberDutch($_POST[$this->objEdtPhone1->getID()]);                
                            $bHandled = true;
                            break;
                        case $this->objEdtPhone2->getID():
                            $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = formatPhoneNumberDutch($_POST[$this->objEdtPhone2->getID()]);                
                            $bHandled = true;
                            break;                        
                        case $this->objEdtBillingPostalCode->getID():
                            $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = formatPostalCodeDutch($_POST[$this->objEdtBillingPostalCode->getID()]);                
                            $bHandled = true;
                            break;
                        case $this->objEdtDeliveryPostalCode->getID():
                            $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = formatPostalCodeDutch($_POST[$this->objEdtDeliveryPostalCode->getID()]);                
                            $bHandled = true;
                            break;
                    }
                }
            }
        }


        if ($bHandled)
        {
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return; //stop further execution
        }
        else
        {
            return parent::handleValidateField();
        }
    }
 
    /**
     * You can check a field if is valid
     * Then it looks at the validators
     */
    /*
    protected function handleValidateField()
    {
        //declare
        $arrErrors = array();
        $arrFGElements = array(); //FG = Form Generator
        $sFilteredValue = '';
        $iCountVal = 0;
        $arrJSONResponse = array();
        
        //although the code of both arrays (internal + form generator) is the same, the array structure is NOT the same

        //==== INTERNAL ARRAY: call validators
        foreach ($this->arrFormHTMLElements as $objFormElement) 
        {
            //only check one field
            if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] == $objFormElement->getID())
            {
                $iCountVal = $objFormElement->countValidators();
                $sFilteredValue = $objFormElement->getValueSubmitted(); //init value
                for ($iIndex = 0; $iIndex < $iCountVal; ++$iIndex) 
                {
                    $objValidator = $objFormElement->getValidator($iIndex);
                    $sFilteredValue = $objValidator->filterValue($sFilteredValue);
                    if (!$objValidator->isValid($sFilteredValue)) //check validators of fields
                    {
                        $arrErrors[] = array
                        (
                            TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_MESSAGE => $objValidator->getErrorMessage(),
                        );       
                    }
                }
            }
        }

        //==== FORM GENERATOR: call validators
        $arrFGElements = $this->objFormGenerator->getElements();
        foreach ($arrFGElements as $objFormElement) 
        {
            //only check one field
            if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] == $objFormElement->getID())
            {
                $iCountVal = $objFormElement->countValidators();
                $sFilteredValue = $objFormElement->getValueSubmitted(); //init value
                for ($iIndex = 0; $iIndex < $iCountVal; ++$iIndex) 
                {
                    $objValidator = $objFormElement->getValidator($iIndex);
                    $sFilteredValue = $objValidator->filterValue($sFilteredValue);                    
                    if (!$objValidator->isValid($sFilteredValue)) //check validators of fields
                    {
                        $arrErrors[] = array
                        (
                            TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_MESSAGE => $objValidator->getErrorMessage(),
                        );                       
                    }
                }
            }
        }

        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_HTMLFIELDID] = $_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD];
        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = $sFilteredValue;                
        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_ERRORS] = $arrErrors;
    
        header(JSONAK_RESPONSE_HEADER);
        echo json_encode($arrJSONResponse);               
        return; //stop further execution to display the error           
    }    
    */
}
