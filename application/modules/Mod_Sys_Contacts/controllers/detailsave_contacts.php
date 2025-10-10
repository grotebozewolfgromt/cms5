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
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\form\Textarea;
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

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_contacts extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtRecordId = null;//dr\classes\dom\tag\form\InputText
    private $objEdtNiceId = null;//dr\classes\dom\tag\form\InputText
    private $objEdtCustomIdentifier = null;//dr\classes\dom\tag\form\InputText
    private $objTagKeywords = null;
    private $objEdtCompanyName = null;//dr\classes\dom\tag\form\InputText
    private $objCbxSalutations = null; //DRCombobox
    private $objEdtFirstNameInitials = null;//dr\classes\dom\tag\form\InputText
    private $objEdtLastName = null;//dr\classes\dom\tag\form\InputText
    private $objCbxLastNamePrefix = null; //DRCombobox
    private $objEdtEmailAddress = null;//dr\classes\dom\tag\form\InputText
    private $objChkOnMailingList = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkOnBlackList = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objSelCountryCodePhone1 = null;//dr\classes\dom\tag\form\DRCombobox
    private $objEdtPhone1 = null;//dr\classes\dom\tag\form\InputText
    private $objSelCountryCodePhone2 = null;//dr\classes\dom\tag\form\DRCombobox    
    private $objEdtPhone2 = null;//dr\classes\dom\tag\form\InputText        
    private $objEdtChamberCommerce = null;//dr\classes\dom\tag\form\InputText        
    private $objTxtArNotes = null;//dr\classes\dom\tag\form\Textarea
    private $objDTFirstContact = null;  //dr\classes\dom\tag\webcomponents\DRInputDateTime
    private $objDTLastContact = null;  //dr\classes\dom\tag\webcomponents\DRInputDateTime
    
    private $objEdtBillingAddressMisc = null;//dr\classes\dom\tag\form\InputText
    private $objEdtBillingAddressStreet = null;//dr\classes\dom\tag\form\InputText
    private $objEdtBillingPostalCode = null;//dr\classes\dom\tag\form\InputText
    private $objEdtBillingCity = null;//dr\classes\dom\tag\form\InputText
    private $objEdtBillingStateRegion = null;//dr\classes\dom\tag\form\InputText
    private $objSelBillingCountryID = null;//dr\classes\dom\tag\form\Select
    private $objEdtBillingVatNumber = null;//dr\classes\dom\tag\form\InputText
    private $objEdtBillingEmailAddress = null;//dr\classes\dom\tag\form\InputText
    private $objEdtBillingBankAccountNo = null;//dr\classes\dom\tag\form\InputText    

    private $objEdtDeliveryAddressMisc = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDeliveryAddressStreet = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDeliveryPostalCode = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDeliveryCity = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDeliveryStateRegion = null;//dr\classes\dom\tag\form\InputText
    private $objSelDeliveryCountryID = null;//dr\classes\dom\tag\form\Select

    private $objChkIsClient = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkIsSupplier = null;//dr\classes\dom\tag\form\InputCheckbox

    private $objCountries = null;
    private $objSalutations = null;
    private $objLastNamePrefixes = null;

    private $iDefaultCountryID = 0;
    private $iDefaultSalutationID = 0;
    private $iDefaultLastNamePrefixID = 0;


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
        // $sFormSectionType = transm(CMS_CURRENTMODULE, 'form_section_general', 'General');
        $sFormSectionBusiness = '';
        $sFormSectionBusiness = transm(CMS_CURRENTMODULE, 'form_section_business', 'Business');
        $sFormSectionId = '';
        $sFormSectionId = transm(CMS_CURRENTMODULE, 'form_section_identification', 'Indentification');
        $sFormSectionPersonal = '';
        $sFormSectionPersonal = transm(CMS_CURRENTMODULE, 'form_section_personal', 'Personal');
        $sFormSectionBilling = '';
        $sFormSectionBilling = transm(CMS_CURRENTMODULE, 'form_section_billing', 'Address');
        $sFormSectionDelivery = '';
        $sFormSectionDelivery = transm(CMS_CURRENTMODULE, 'form_section_delivery', 'Delivery');
        $sFormSectionMisc = '';
        $sFormSectionMisc = transm(CMS_CURRENTMODULE, 'form_section_misc', 'Miscellaneous');


            //is client
        $this->objChkIsClient = new InputCheckbox();
        $this->objChkIsClient->setNameAndID('chkIsClient');
        $this->objChkIsClient->setOnkeyup("setDirtyRecord()");          
        $this->getFormGenerator()->add($this->objChkIsClient, '', transm(CMS_CURRENTMODULE, 'form_field_isclient', 'is client'));   
            
            //is supplier
        $this->objChkIsSupplier = new InputCheckbox();
        $this->objChkIsSupplier->setNameAndID('chkIsSupplier');
        $this->objChkIsSupplier->setOnkeyup("setDirtyRecord()");                  
        $this->getFormGenerator()->add($this->objChkIsSupplier,  '', transm(CMS_CURRENTMODULE, 'form_field_issupplier', 'is supplier'));         
    

            //on mailing list
        $this->objChkOnMailingList = new InputCheckbox();
        $this->objChkOnMailingList->setNameAndID('chkOnMailingList');
        $this->objChkOnMailingList->setOnkeyup("setDirtyRecord()");                          
        $this->getFormGenerator()->add($this->objChkOnMailingList, '', transm(CMS_CURRENTMODULE, 'form_field_onmailinglist', 'on mailing list'));   
        
                //on black list
        $this->objChkOnBlackList = new InputCheckbox();
        $this->objChkOnBlackList->setNameAndID('chkOnBlackList');
        $this->objChkOnBlackList->setOnkeyup("setDirtyRecord()");                                  
        $this->getFormGenerator()->add($this->objChkOnBlackList, '', transm(CMS_CURRENTMODULE, 'form_field_onblacklist', 'on blacklist'));   
    

            //record id
        $this->objEdtRecordId = new InputText();
        $this->objEdtRecordId->setNameAndID('edtRecordId');
        $this->objEdtRecordId->setClass('quarterwidthtag');    
        $this->objEdtRecordId->setReadOnly(true);             
        $this->getFormGenerator()->addQuick($this->objEdtRecordId, $sFormSectionId, transm(CMS_CURRENTMODULE, 'form_field_recordid', 'Contact id'),  transm(CMS_CURRENTMODULE, 'form_field_recordid_iconinfo', 'This is a unique number used internally to identify this contact in [application].<br>This number is automatically assigned by [application].<br>You can NOT change this number.<br><br>Be aware that this number is enumerable, meaning that malicious actors can use this information to access other records.<br>Malicious actors know that when id 100 exists that probably 99 and 101 also exist.<br>To counter this, you can use Nice Id.<br><br>This identifyer is NOT encrypted, thus searchable.','application', APP_CMS_APPLICATIONNAME));

            //Nice id
        $this->objEdtNiceId = new InputText();
        $this->objEdtNiceId->setNameAndID('edtNiceId');
        $this->objEdtNiceId->setClass('quarterwidthtag');    
        $this->objEdtNiceId->setReadOnly(true);               
        $this->getFormGenerator()->addQuick($this->objEdtNiceId, $sFormSectionId, transm(CMS_CURRENTMODULE, 'form_field_niceid', 'Nice Id'),  transm(CMS_CURRENTMODULE, 'form_field_niceid_iconinfo', 'This is a unique alfanumeric identifier to identify this contact in [application].<br>This identifier is automatically assigned.<br>This identifier is random and therefore not enumerable, hence safer to work with.<br><br>You can not change this identifyer.<br>If you want to, use Custom Id instead.<br><br>This identifyer is NOT encrypted, thus searchable.','application', APP_CMS_APPLICATIONNAME));


            //custom identifier
        $this->objEdtCustomIdentifier = new InputText();
        $this->objEdtCustomIdentifier->setNameAndID('edtCustomIdentifier');
        $this->objEdtCustomIdentifier->setClass('quarterwidthtag');         
        $this->objEdtCustomIdentifier->setMaxLength(50);                
        $objValidator = new TMaximumLength(50);
        $this->objEdtCustomIdentifier->addValidator($objValidator);    
        $this->objEdtCustomIdentifier->setOnchange("validateField(this, true)");
        $this->objEdtCustomIdentifier->setOnkeyup("setDirtyRecord()");          
        // $this->getFormGenerator()->add($this->objEdtCustomIdentifier, '', transm(CMS_CURRENTMODULE, 'form_field_customid', 'Custom Id (to identify this contact just to you, so you can search for it)'));
        $this->getFormGenerator()->addQuick($this->objEdtCustomIdentifier, $sFormSectionId, transm(CMS_CURRENTMODULE, 'form_field_customid', 'Custom Id'),  transm(CMS_CURRENTMODULE, 'form_field_customid_iconinfo', 'To identify this contact just to you, so you can search for it.<br>This can be your own a in-house customer id, account id, connection id etc.<br>It needs to be something that makes sense to you.<br><br>This information is not encrypted, thus searchable.'));


            //search keywords
        $this->objTagKeywords = new InputText();
        $this->objTagKeywords->setNameAndID('edtTagSearchKeywords');
        $this->objTagKeywords->setClass('fullwidthtag');         
        $this->objTagKeywords->setMaxLength(255);                
        $objValidator = new TMaximumLength(255);
        $this->objTagKeywords->addValidator($objValidator);    
        $this->objTagKeywords->setOnchange("validateField(this, true)");
        $this->objTagKeywords->setOnkeyup("setDirtyRecord()");          
        // $this->getFormGenerator()->add($this->objTagKeywords, '', transm(CMS_CURRENTMODULE, 'form_field_searchkeywords', 'Search keywords'));
        $this->getFormGenerator()->addQuick($this->objTagKeywords, $sFormSectionId, transm(CMS_CURRENTMODULE, 'form_field_searchkeywords', 'Search keywords'),  transm(CMS_CURRENTMODULE, 'form_field_searchkeywords_iconinfo', 'Enter search keywords to find this contact.<br>This information is not encrypted, thus searchable.<br><br>To comply with data protection regulations, we encrypt sensitive personal data.<br>This is safe, but doesn\'t allow you to search for this data.<br>To help you find your contacts, you can use search keywords to identify this contact only to you.<br>Some keywords can be generated automatically when saving.'));


            //company name
        $this->objEdtCompanyName = new InputText();
        $this->objEdtCompanyName->setNameAndID('edtCompanyName');
        $this->objEdtCompanyName->setClass('fullwidthtag');                         
        $this->objEdtCompanyName->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtCompanyName->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtCompanyName->addValidator($objValidator);     
        $this->objEdtCompanyName->setOnchange("validateField(this, true)");
        // $this->objEdtCompanyName->setOnkeyup("setDirtyRecord()");               
        $this->getFormGenerator()->add($this->objEdtCompanyName, $sFormSectionBusiness, transm(CMS_CURRENTMODULE, 'form_field_companyname', 'Company name')); 

            //chamber of commerce number
        $this->objEdtChamberCommerce = new InputText();
        $this->objEdtChamberCommerce->setNameAndID('edtChamberOfCommerceNumber');
        $this->objEdtChamberCommerce->setClass('fullwidthtag');                         
        $this->objEdtChamberCommerce->setMaxLength(25);    
        $objValidator = new TMaximumLength(25);
        $this->objEdtChamberCommerce->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtChamberCommerce->addValidator($objValidator);     
        $this->objEdtChamberCommerce->setOnchange("validateField(this, true)");
        $this->objEdtChamberCommerce->setOnkeyup("setDirtyRecord()");               
        // $this->getFormGenerator()->add($this->objEdtChamberCommerce, '', transm(CMS_CURRENTMODULE, 'form_field_chamberofcommerceno', 'Chamber of commerce registration number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtChamberCommerce, $sFormSectionBusiness, transm(CMS_CURRENTMODULE, 'form_field_chamberofcommerceno', 'Chamber of commerce # (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_chamberofcommerceno_iconinfo', 'Chamber of commerce registration number.<br>This data encrypted, thus not searchable.'));
                

            //salutations
        $this->objCbxSalutations = new DRInputCombobox();
        $this->objCbxSalutations->setNameAndID('cbxSalutations');
        $this->objCbxSalutations->setClass('quarterwidthtag'); 
        $this->objCbxSalutations->setOnchange("setDirtyRecord()");               
        $this->getFormGenerator()->addQuick($this->objCbxSalutations, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_salutations', 'Salutation'),  transm(CMS_CURRENTMODULE, 'form_field_salutation_iconinfo', 'How do you whish to address this contact? Mr. Mrs. Ms.'));


            //first name
        $this->objEdtFirstNameInitials = new InputText();
        $this->objEdtFirstNameInitials->setNameAndID('edtFirstName');
        $this->objEdtFirstNameInitials->setClass('fullwidthtag');                         
        $this->objEdtFirstNameInitials->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtFirstNameInitials->addValidator($objValidator);    
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtFirstNameInitials->addValidator($objValidator);    
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtFirstNameInitials->addValidator($objValidator);     
        $this->objEdtFirstNameInitials->setOnchange("validateField(this, true)");
        $this->objEdtFirstNameInitials->setOnkeyup("setDirtyRecord()");                            
        $this->getFormGenerator()->add($this->objEdtFirstNameInitials, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_firstnameinitials', 'Initials')); 

            //last name prefix
        // $this->objEdtLastNamePrefix = new InputText();
        // $this->objEdtLastNamePrefix->setNameAndID('edtLastNamePrefix');
        // // $this->objEdtLastName->setClass('fullwidthtag');                         
        // $this->objEdtLastNamePrefix->setMaxLength(20);    
        // $objValidator = new TMaximumLength(20);
        // $this->objEdtLastNamePrefix->addValidator($objValidator);          
        // $this->objEdtLastNamePrefix->setOnchange("validateField(this, true)");
        // $this->objEdtLastNamePrefix->setOnkeyup("setDirtyRecord()");                            
        // // $this->getFormGenerator()->add($this->objEdtLastNamePrefix, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_lastnameprefix', 'Last name prefix (van de, von der etc)')); 
        // $this->getFormGenerator()->addQuick($this->objEdtLastNamePrefix, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_lastnameprefix', 'Last name prefix'),  transm(CMS_CURRENTMODULE, 'form_field_lastnameprefix_iconinfo', 'Like: van de, von der etc, von.<br>This data not encrypted, thus searchable.'));
            //last name prefix
        $this->objCbxLastNamePrefix = new DRInputCombobox();
        $this->objCbxLastNamePrefix->setNameAndID('cbxLastNamePrefix');
        $this->objCbxLastNamePrefix->setClass('quarterwidthtag'); 
        $this->objCbxLastNamePrefix->setOnchange("setDirtyRecord()");               
        $this->getFormGenerator()->addQuick($this->objCbxLastNamePrefix, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_lastnameprefix', 'Last name prefix'),  transm(CMS_CURRENTMODULE, 'form_field_lastnameprefix_iconinfo', 'Like: van de, van, von der etc, von'));
    

            //last name
        $this->objEdtLastName = new InputText();
        $this->objEdtLastName->setNameAndID('edtLastName');
        $this->objEdtLastName->setClass('fullwidthtag');                         
        $this->objEdtLastName->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtLastName->addValidator($objValidator);   
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtLastName->addValidator($objValidator);           
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtLastName->addValidator($objValidator);     
        $this->objEdtLastName->setOnchange("validateField(this, true)");
        $this->objEdtLastName->setOnkeyup("setDirtyRecord()");                            
        // $this->getFormGenerator()->add($this->objEdtLastName, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_lastname', 'Last name (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtLastName, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_lastname', 'Last name (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_lastname_iconinfo', 'Last name.<br>This data encrypted, thus not searchable.'));


            //email
        $this->objEdtEmailAddress = new InputText();
        $this->objEdtEmailAddress->setNameAndID('edtEmailAddress');
        $this->objEdtEmailAddress->setClass('fullwidthtag');                         
        $this->objEdtEmailAddress->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtEmailAddress->addValidator($objValidator); 
        $objValidator = new TEmailAddress(true, true, true);
        $this->objEdtEmailAddress->addValidator($objValidator); 
        $objValidator = new TLowercase();
        $this->objEdtEmailAddress->addValidator($objValidator);         
        $this->objEdtEmailAddress->setOnchange("validateField(this, true)");
        $this->objEdtEmailAddress->setOnkeyup("setDirtyRecord()");                            
        $this->getFormGenerator()->addQuick($this->objEdtEmailAddress, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_emailaddress', 'Email address (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_emailaddress_iconinfo', 'Email address is encrypted, thus not searchable.'));

            //country code phone1
        $this->objSelCountryCodePhone1 = new DRInputCombobox();
        // $this->objSelCountryCodePhone1->setClass('quarterwidthtag');
        $this->objSelCountryCodePhone1->setNameAndID('selCountryCodePhone1');
        $this->objSelCountryCodePhone1->setOnchange("setDirtyRecord()");                            
        // $this->getFormGenerator()->add($this->objSelCountryCodePhone1, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_countrycodephone1', 'Country code'));

            //phone1
        $this->objEdtPhone1 = new InputText();
        $this->objEdtPhone1->setNameAndID('edtPhone1');
        $this->objEdtPhone1->setClass('halfwidthtag');                         
        $this->objEdtPhone1->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtPhone1->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_NUMERIC.' -');
        $this->objEdtPhone1->addValidator($objValidator);                 
        $this->objEdtPhone1->setOnchange("validateField(this, true)");
        $this->objEdtPhone1->setOnkeyup("setDirtyRecord()");                            
        $this->getFormGenerator()->addArray(array($this->objSelCountryCodePhone1, $this->objEdtPhone1), $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_phonenumber1', 'Phone number 1 (including area code, starting with 0)'), true, '', false,  transm(CMS_CURRENTMODULE, 'form_field_phonenumber1_infoicon', '<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Encrypted, not searchable</li></ul>'));

            //country code phone2
        $this->objSelCountryCodePhone2 = new DRInputCombobox();
        // $this->objSelCountryCodePhone2->setClass('quarterwidthtag');
        $this->objSelCountryCodePhone2->setNameAndID('selCountryCodePhone2');
        $this->objSelCountryCodePhone2->setOnchange("setDirtyRecord()");                            
        // $this->getFormGenerator()->add($this->objSelCountryCodePhone2, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_countrycodephone2', 'Country code'));

            //phone2
        $this->objEdtPhone2 = new InputText();
        $this->objEdtPhone2->setNameAndID('edtPhone2');
        $this->objEdtPhone2->setClass('halfwidthtag');                         
        $this->objEdtPhone2->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtPhone2->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_NUMERIC.' -');
        $this->objEdtPhone2->addValidator($objValidator);         
        $this->objEdtPhone2->setOnchange("validateField(this, true)");
        $this->objEdtPhone2->setOnkeyup("setDirtyRecord()");                            
        // $this->getFormGenerator()->add($this->objEdtPhone2, $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_phonenumber2', 'Phone number 2 (encrypted, not searchable)')); 
        $this->getFormGenerator()->addArray(array($this->objSelCountryCodePhone2, $this->objEdtPhone2), $sFormSectionPersonal, transm(CMS_CURRENTMODULE, 'form_field_phonenumber2', 'Phone number 2 (including area code, starting with 0)'), true, '', false,  transm(CMS_CURRENTMODULE, 'form_field_phonenumber2_infoicon', '<ul><li>Don\'t include country code in phone number, select country instead</li><li>Include area code, starting with 0</li><li>Encrypted, not searchable</li></ul>'));
    
            //billing: address line 2: street
        $this->objEdtBillingAddressStreet = new InputText();
        $this->objEdtBillingAddressStreet->setNameAndID('edtBillingAddressLineStreet');
        $this->objEdtBillingAddressStreet->setClass('fullwidthtag');                         
        $this->objEdtBillingAddressStreet->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtBillingAddressStreet->addValidator($objValidator); 
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtBillingAddressStreet->addValidator($objValidator);      
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingAddressStreet->addValidator($objValidator);     
        $this->objEdtBillingAddressStreet->setOnchange("validateField(this, true)");
        $this->objEdtBillingAddressStreet->setOnkeyup("setDirtyRecord()");                            
        // $this->getFormGenerator()->add($this->objEdtBillingAddressStreet, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_FIELD_BILLINGADDRESSSTREET', 'Street + house number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingAddressStreet, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingaddressstreet', 'Street + house number (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_billingaddressstreet_iconinfo', 'Street and house number is encrypted, thus not searchable.'));


            //billing: address line 1: misc
        $this->objEdtBillingAddressMisc = new InputText();
        $this->objEdtBillingAddressMisc->setNameAndID('edtBillingAddressLineMisc');
        $this->objEdtBillingAddressMisc->setClass('fullwidthtag');                         
        $this->objEdtBillingAddressMisc->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtBillingAddressMisc->addValidator($objValidator);    
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtBillingAddressMisc->addValidator($objValidator);                     
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingAddressMisc->addValidator($objValidator);     
        $this->objEdtBillingAddressMisc->setOnchange("validateField(this, true)");
        $this->objEdtBillingAddressMisc->setOnkeyup("setDirtyRecord()");                            
        // $this->getFormGenerator()->add($this->objEdtBillingAddressMisc, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_FIELD_BILLINGADDRESSMISC', 'Appt. building/ company dept. etc (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingAddressMisc, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingaddressmisc', 'Extra address info (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_billingaddressmisc_iconinfo', 'Apartment building, company dept, floor, 2nd red door on the left etc.<br>Data is encrypted, thus not searchable.'));

            
            //billing: postal code or zip
        $this->objEdtBillingPostalCode = new InputText();
        $this->objEdtBillingPostalCode->setNameAndID('edtBillingPostalCode');
        // $this->objEdtBillingPostalCode->setClass('fullwidthtag');                         
        $this->objEdtBillingPostalCode->setMaxLength(10);    
        $objValidator = new TMaximumLength(10);
        $this->objEdtBillingPostalCode->addValidator($objValidator);          
        $objValidator = new TUppercase();
        $this->objEdtBillingPostalCode->addValidator($objValidator); 
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.' ');
        $this->objEdtBillingPostalCode->addValidator($objValidator); 

        $this->objEdtBillingPostalCode->setOnchange("validateField(this, true)");
        $this->objEdtBillingPostalCode->setOnkeyup("setDirtyRecord()");                            
        $this->getFormGenerator()->add($this->objEdtBillingPostalCode, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingpostalcodezip', 'Postal code/zip (encrypted)')); 
    
            //billing: city
        $this->objEdtBillingCity = new InputText();
        $this->objEdtBillingCity->setNameAndID('edtBillingCity');
        $this->objEdtBillingCity->setClass('fullwidthtag');                         
        $this->objEdtBillingCity->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtBillingCity->addValidator($objValidator);    
        // $objValidator = new TUppercaseFirstChar(); //doesnt work with: 's-Hertogenbosch
        // $this->objEdtBillingCity->addValidator($objValidator); 
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingCity->addValidator($objValidator);     
        $this->objEdtBillingCity->setOnchange("validateField(this, true)");
        $this->objEdtBillingCity->setOnkeyup("setDirtyRecord()");                            
        $this->getFormGenerator()->add($this->objEdtBillingCity, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingcity', 'City')); 

            //billing: state/region
        $this->objEdtBillingStateRegion = new InputText();
        $this->objEdtBillingStateRegion->setNameAndID('edtBillingState');
        $this->objEdtBillingStateRegion->setClass('fullwidthtag');                         
        $this->objEdtBillingStateRegion->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtBillingStateRegion->addValidator($objValidator);          
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtBillingStateRegion->addValidator($objValidator);           
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingStateRegion->addValidator($objValidator);     
        $this->objEdtBillingStateRegion->setOnchange("validateField(this, true)");
        $this->objEdtBillingStateRegion->setOnkeyup("setDirtyRecord()");                            
        $this->getFormGenerator()->add($this->objEdtBillingStateRegion, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingstateregion', 'State/region')); 
            

            //billing: country
        $this->objSelBillingCountryID = new DRInputCombobox();
        $this->objSelBillingCountryID->setNameAndID('optBillingCountryID');
        $this->objSelBillingCountryID->setOnchange("setDirtyRecord()");                            
        $this->getFormGenerator()->add($this->objSelBillingCountryID, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingcountry', 'Country'));

            //billing: vat no
        $this->objEdtBillingVatNumber = new InputText();
        $this->objEdtBillingVatNumber->setNameAndID('edtBillingVATNumber');
        $this->objEdtBillingVatNumber->setClass('fullwidthtag');                         
        $this->objEdtBillingVatNumber->setMaxLength(20);    
        $objValidator = new TMaximumLength(20);
        $this->objEdtBillingVatNumber->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingVatNumber->addValidator($objValidator);     
        $this->objEdtBillingVatNumber->setOnchange("validateField(this, true)");
        $this->objEdtBillingVatNumber->setOnkeyup("setDirtyRecord()");             
        // $this->getFormGenerator()->add($this->objEdtBillingVatNumber, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingvatno', 'VAT number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingVatNumber, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingvatno', 'VAT number / Tax id (encrypted'),  transm(CMS_CURRENTMODULE, 'form_field_billingvatno_iconinfo', 'VAT number or (sales) tax id.<br>Data is encrypted, thus not searchable.'));

            //billing: bank account no
        $this->objEdtBillingBankAccountNo = new InputText();
        $this->objEdtBillingBankAccountNo->setNameAndID('edtBillingBankAccountNumber');
        $this->objEdtBillingBankAccountNo->setClass('fullwidthtag');                         
        $this->objEdtBillingBankAccountNo->setMaxLength(20);    
        $objValidator = new TMaximumLength(20);
        $this->objEdtBillingBankAccountNo->addValidator($objValidator);    
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtBillingBankAccountNo->addValidator($objValidator);     
        $this->objEdtBillingBankAccountNo->setOnchange("validateField(this, true)");
        $this->objEdtBillingBankAccountNo->setOnkeyup("setDirtyRecord()");             
        // $this->getFormGenerator()->add($this->objEdtBillingBankAccountNo, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingbankaccoutno', 'Bank account number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingBankAccountNo, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingbankaccountno', 'IBAN Bank account (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_billingbankaccountno_iconinfo', 'IBAN or Bank account number / id.<br>Data is encrypted, thus not searchable.'));
            
            //billing: email
        $this->objEdtBillingEmailAddress = new InputText();
        $this->objEdtBillingEmailAddress->setNameAndID('edtBillingEmailAddress');
        $this->objEdtBillingEmailAddress->setClass('fullwidthtag');                         
        $this->objEdtBillingEmailAddress->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtBillingEmailAddress->addValidator($objValidator);          
        $this->objEdtBillingEmailAddress->setOnchange("validateField(this, true)");
        $this->objEdtBillingEmailAddress->setOnkeyup("setDirtyRecord()");             
        // $this->getFormGenerator()->add($this->objEdtBillingEmailAddress, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingemailaddress', 'Email address (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtBillingEmailAddress, $sFormSectionBilling, transm(CMS_CURRENTMODULE, 'form_field_billingemailaddress', 'Email address (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_field_billingemailaddress_iconinfo', 'Email address is encrypted, thus not searchable.'));
                            
    
            //delivery: address line 2
        $this->objEdtDeliveryAddressStreet = new InputText();
        $this->objEdtDeliveryAddressStreet->setNameAndID('edtDeliveryAddressLineStreet');
        $this->objEdtDeliveryAddressStreet->setClass('fullwidthtag');                         
        $this->objEdtDeliveryAddressStreet->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator); 
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator);  
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator);             
        $this->objEdtDeliveryAddressStreet->setOnchange("validateField(this, true)");
        $this->objEdtDeliveryAddressStreet->setOnkeyup("setDirtyRecord()");          
        // $this->getFormGenerator()->add($this->objEdtDeliveryAddressStreet, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSSTREET', 'Street + house number (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryAddressStreet, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSSTREET', 'Street + house number (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSSTREET_iconinfo', 'Address is encrypted, thus not searchable.'));


            //delivery: address line 1
        $this->objEdtDeliveryAddressMisc = new InputText();
        $this->objEdtDeliveryAddressMisc->setNameAndID('edtDeliveryAddressLineMisc');
        $this->objEdtDeliveryAddressMisc->setClass('fullwidthtag');                         
        $this->objEdtDeliveryAddressMisc->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtDeliveryAddressMisc->addValidator($objValidator);  
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryAddressMisc->addValidator($objValidator);
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryAddressStreet->addValidator($objValidator);             
        $this->objEdtDeliveryAddressMisc->setOnchange("validateField(this, true)");
        $this->objEdtDeliveryAddressMisc->setOnkeyup("setDirtyRecord()");          
        // $this->getFormGenerator()->add($this->objEdtDeliveryAddressMisc, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSMISC', 'Appt. building/ company dept. etc (encrypted, not searchable)')); 
        $this->getFormGenerator()->addQuick($this->objEdtDeliveryAddressMisc, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSMISC', 'Extra address info (encrypted)'),  transm(CMS_CURRENTMODULE, 'form_FIELD_DELIVERYADDRESSMISC_iconinfo', 'Apartment building, company dept, floor, 2nd red door on the left etc.<br>Data is encrypted, thus not searchable.'));

            
            //delivery: postal code or zip
        $this->objEdtDeliveryPostalCode = new InputText();
        $this->objEdtDeliveryPostalCode->setNameAndID('edtDeliveryPostalCode');
        // $this->objEdtDeliveryPostalCode->setClass('fullwidthtag');                         
        $this->objEdtDeliveryPostalCode->setMaxLength(10);    
        $objValidator = new TMaximumLength(10);
        $this->objEdtDeliveryPostalCode->addValidator($objValidator);   
        $objValidator = new TUppercase();
        $this->objEdtDeliveryPostalCode->addValidator($objValidator);                   
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.' ');
        $this->objEdtDeliveryPostalCode->addValidator($objValidator); 
        $this->objEdtDeliveryPostalCode->setOnchange("validateField(this, true)");
        $this->objEdtDeliveryPostalCode->setOnkeyup("setDirtyRecord()");         
        $this->getFormGenerator()->add($this->objEdtDeliveryPostalCode, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_field_deliverypostalcodezip', 'Postal code/zip (encrypted)')); 
    
            //delivery: city
        $this->objEdtDeliveryCity = new InputText();
        $this->objEdtDeliveryCity->setNameAndID('edtDeliveryCity');
        $this->objEdtDeliveryCity->setClass('fullwidthtag');                         
        $this->objEdtDeliveryCity->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtDeliveryCity->addValidator($objValidator);   
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryCity->addValidator($objValidator);       
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryCity->addValidator($objValidator);             
        $this->objEdtDeliveryCity->setOnchange("validateField(this, true)");
        $this->objEdtDeliveryCity->setOnkeyup("setDirtyRecord()");                     
        $this->getFormGenerator()->add($this->objEdtDeliveryCity, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_field_deliverycity', 'City')); 

            //delivery: state/region
        $this->objEdtDeliveryStateRegion = new InputText();
        $this->objEdtDeliveryStateRegion->setNameAndID('edtDeliveryState');
        $this->objEdtDeliveryStateRegion->setClass('fullwidthtag');                         
        $this->objEdtDeliveryStateRegion->setMaxLength(50);    
        $objValidator = new TMaximumLength(50);
        $this->objEdtDeliveryStateRegion->addValidator($objValidator);    
        $objValidator = new TUppercaseFirstChar();
        $this->objEdtDeliveryStateRegion->addValidator($objValidator); 
        $objValidator = new TCharacterWhitelist(WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.' -().');
        $this->objEdtDeliveryStateRegion->addValidator($objValidator);         
        $this->objEdtDeliveryStateRegion->setOnchange("validateField(this, true)");
        $this->objEdtDeliveryStateRegion->setOnkeyup("setDirtyRecord()");                     
        $this->getFormGenerator()->add($this->objEdtDeliveryStateRegion, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_field_deliverystateregion', 'State/region')); 
            

            //delivery: country
        // $this->objSelDeliveryCountryID = new Select();
        $this->objSelDeliveryCountryID = new DRInputCombobox();
        $this->objSelDeliveryCountryID->setNameAndID('optDeliveryCountryID');
        $this->objSelDeliveryCountryID->setOnchange("setDirtyRecord()");                     
        $this->getFormGenerator()->add($this->objSelDeliveryCountryID, $sFormSectionDelivery, transm(CMS_CURRENTMODULE, 'form_field_deliverycountry', 'Country'));
               

            //first contact
        $this->objDTFirstContact = new DRInputDateTime();
        $this->objDTFirstContact->setNameAndID('dtFirstContact');
        $this->objDTFirstContact->setAllowEmptyDateTime(true);
        $this->objDTFirstContact->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong());
        $this->objDTFirstContact->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong());
        $this->objDTFirstContact->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objDTFirstContact->setOnchange("setDirtyRecord()");          
        $this->getFormGenerator()->add($this->objDTFirstContact, $sFormSectionMisc, transm(CMS_CURRENTMODULE, 'form_field_first_contact', 'First contact')); 

            //last contact
        $this->objDTLastContact = new DRInputDateTime();
        $this->objDTLastContact->setNameAndID('dtLastContact');
        $this->objDTLastContact->setAllowEmptyDateTime(true);        
        $this->objDTLastContact->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong());
        $this->objDTLastContact->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong());
        $this->objDTLastContact->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objDTLastContact->setOnchange("setDirtyRecord()");                  
        $this->getFormGenerator()->add($this->objDTLastContact, $sFormSectionMisc, transm(CMS_CURRENTMODULE, 'form_field_last_contact', 'Last contact')); 

           //notes
        $this->objTxtArNotes = new Textarea();
        $this->objTxtArNotes->setNameAndID('txtArNotes');
        $this->objTxtArNotes->setClass('fullwidthtag');     
        $objValidator = new TCharacterWhitelist(WHITELIST_SAFE);
        $this->objTxtArNotes->addValidator($objValidator);         
        $this->objTxtArNotes->setOnchange("validateField(this, true)");
        $this->objTxtArNotes->setOnkeyup("setDirtyRecord()");               
        $this->getFormGenerator()->add($this->objTxtArNotes, $sFormSectionMisc, transm(CMS_CURRENTMODULE, 'form_field_notes', 'Notes (only seen by you)')); 
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
        $this->getModel()->set(TSysContacts::FIELD_CUSTOMID, $this->objEdtCustomIdentifier->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_COMPANYNAME, $this->objEdtCompanyName->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, $this->objEdtChamberCommerce->getValueSubmitted(), '', true);

        $this->getModel()->set(TSysContacts::FIELD_SALUTATIONID, $this->objCbxSalutations->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_FIRSTNAMEINITALS, $this->objEdtFirstNameInitials->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_LASTNAME, $this->objEdtLastName->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_LASTNAMEPREFIXID, $this->objCbxLastNamePrefix->getValueSubmitted());
        $this->getModel()->setEmailAddressDecrypted($this->objEdtEmailAddress->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_ONMAILINGLIST, $this->objChkOnMailingList->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_ONBLACKLIST, $this->objChkOnBlackList->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysContacts::FIELD_COUNTRYIDCODEPHONE1, $this->objSelCountryCodePhone1->getValueSubmittedAsInt());
        $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER1, $this->objEdtPhone1->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_COUNTRYIDCODEPHONE2, $this->objSelCountryCodePhone2->getValueSubmittedAsInt());        
        $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER2, $this->objEdtPhone2->getValueSubmitted(), '', true);        
        $this->getModel()->set(TSysContacts::FIELD_NOTES, $this->objTxtArNotes->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_FIRSTCONTACT, $this->objDTFirstContact->getValueSubmittedAsTDateTimeISO());
        $this->getModel()->set(TSysContacts::FIELD_LASTCONTACT, $this->objDTLastContact->getValueSubmittedAsTDateTimeISO());

        $this->getModel()->set(TSysContacts::FIELD_BILLINGADDRESSMISC, $this->objEdtBillingAddressMisc->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGADDRESSSTREET, $this->objEdtBillingAddressStreet->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, $this->objEdtBillingPostalCode->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_BILLINGCITY, $this->objEdtBillingCity->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGSTATEREGION, $this->objEdtBillingStateRegion->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGCOUNTRYID, $this->objSelBillingCountryID->getValueSubmittedAsInt());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGVATNUMBER, $this->objEdtBillingVatNumber->getValueSubmitted(), '', true);
        $this->getModel()->setBillingEmailAddressDecrypted($this->objEdtBillingEmailAddress->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, $this->objEdtBillingBankAccountNo->getValueSubmitted(), '', true);

        $this->getModel()->set(TSysContacts::FIELD_DELIVERYADDRESSMISC, $this->objEdtDeliveryAddressMisc->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYADDRESSSTREET, $this->objEdtDeliveryAddressStreet->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, $this->objEdtDeliveryPostalCode->getValueSubmitted(), '', true);
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYCITY, $this->objEdtDeliveryCity->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYSTATEREGION, $this->objEdtDeliveryStateRegion->getValueSubmitted());
        $this->getModel()->set(TSysContacts::FIELD_DELIVERYCOUNTRYID, $this->objSelDeliveryCountryID->getValueSubmittedAsInt());
    
        //==== correct for the Dutchies
        //billing postal code
        $objCountries = new TSysCountries();
        $objCountries->loadFromDBByID($this->objSelBillingCountryID->getValueSubmittedAsInt());
        if ($objCountries->getISO2() == 'NL')
            $this->getModel()->set(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, formatPostalCodeDutch($this->objEdtBillingPostalCode->getValueSubmitted()), '', true);

        //delivery postal code
        $objCountries = new TSysCountries();
        $objCountries->loadFromDBByID($this->objSelDeliveryCountryID->getValueSubmittedAsInt());
        if ($objCountries->getISO2() == 'NL')
            $this->getModel()->set(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, formatPostalCodeDutch($this->objEdtDeliveryPostalCode->getValueSubmitted()), '', true);

        //phone 1
        $objCountries = new TSysCountries();
        $objCountries->loadFromDBByID($this->objSelCountryCodePhone1->getValueSubmittedAsInt());
        if ($objCountries->getISO2() == 'NL')
            $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER1, formatPhoneNumberDutch($this->objEdtPhone1->getValueSubmitted()), '', true);

        //phone 2
        $objCountries = new TSysCountries();
        $objCountries->loadFromDBByID($this->objSelCountryCodePhone2->getValueSubmittedAsInt());
        if ($objCountries->getISO2() == 'NL')
            $this->getModel()->set(TSysContacts::FIELD_PHONENUMBER2, formatPhoneNumberDutch($this->objEdtPhone2->getValueSubmitted()), '', true);
        

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
        $this->objEdtLastName->setValue($this->getModel()->get(TSysContacts::FIELD_LASTNAME, '', true));
        if ($this->getModel()->getNew())
            $this->objLastNamePrefixes->generateHTMLSelect($this->iDefaultLastNamePrefixID, $this->objCbxLastNamePrefix);    
        else
            $this->objLastNamePrefixes->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_LASTNAMEPREFIXID), $this->objCbxLastNamePrefix);    
        $this->objEdtEmailAddress->setValue($this->getModel()->get(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, '', true));        
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objSelCountryCodePhone1);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_COUNTRYIDCODEPHONE1), $this->objSelCountryCodePhone1);    
        $this->objEdtPhone1->setValue($this->getModel()->get(TSysContacts::FIELD_PHONENUMBER1, '', true));   
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objSelCountryCodePhone2);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_COUNTRYIDCODEPHONE2), $this->objSelCountryCodePhone2);            $this->objEdtPhone2->setValue($this->getModel()->get(TSysContacts::FIELD_PHONENUMBER2, '', true));        
        $this->objTxtArNotes->setValue($this->getModel()->get(TSysContacts::FIELD_NOTES));
        $this->objDTFirstContact->setValueAsTDateTime($this->getModel()->get(TSysContacts::FIELD_FIRSTCONTACT));
        $this->objDTLastContact->setValueAsTDateTime($this->getModel()->get(TSysContacts::FIELD_LASTCONTACT));

        //billing adress
        $this->objEdtBillingAddressMisc->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGADDRESSMISC, '', true));
        $this->objEdtBillingAddressStreet->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGADDRESSSTREET, '', true));
        $this->objEdtBillingPostalCode->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, '', true));
        $this->objEdtBillingCity->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGCITY));
        $this->objEdtBillingStateRegion->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGSTATEREGION));
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objSelBillingCountryID);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_BILLINGCOUNTRYID), $this->objSelBillingCountryID);    
        $this->objEdtBillingVatNumber->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGVATNUMBER, '', true));
        $this->objEdtBillingEmailAddress->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, '', true));
        $this->objEdtBillingBankAccountNo->setValue($this->getModel()->get(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, '', true));

        //delivery address
        $this->objEdtDeliveryAddressMisc->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYADDRESSMISC, '', true));
        $this->objEdtDeliveryAddressStreet->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYADDRESSSTREET, '', true));
        $this->objEdtDeliveryPostalCode->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, '', true));
        $this->objEdtDeliveryCity->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYCITY));
        $this->objEdtDeliveryStateRegion->setValue($this->getModel()->get(TSysContacts::FIELD_DELIVERYSTATEREGION));
        if ($this->getModel()->getNew())//country default or existing id
            $this->objCountries->generateHTMLSelect($this->iDefaultCountryID, $this->objSelDeliveryCountryID);    
        else
            $this->objCountries->generateHTMLSelect($this->getModel()->get(TSysContacts::FIELD_DELIVERYCOUNTRYID), $this->objSelDeliveryCountryID);


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
        //global CMS_CURRENTMODULE;

        if ($this->getModel()->getNew())   
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_contact_new', 'Create new contact<dr-icon-info>To comply with data protection regulations, we encrypt sensitive personal data.<br>This is safe, but doesn\'t allow you to search for this data.<br><br>To help you find your contacts, you can use search keywords to identify this contact.<br>Some keywords can be generated automatically when saving, depending on your settings.<br><br>Be aware that these search keywords are stored without encryption, when a data breach occurs this information is exposed.</dr-icon-info>');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_contact_edit', 'Edit contact: [contact]<dr-icon-info>To comply with data protection regulations, we encrypt sensitive personal data.<br>This is safe, but doesn\'t allow you to search for this data.<br><br>To help you find your contacts, you can use search keywords to identify this contact.<br>Some keywords can be generated automatically when saving, depending on your settings.<br><br>Be aware that these search keywords are stored without encryption, when a data breach occurs this information is exposed.</dr-icon-info>', 'contact', $this->getModel()->getDisplayRecordShort());           
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
        return auth(CMS_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Contacts::PERM_CAT_CONTACTS, TModuleAbstract::PERM_OP_DELETE);
    }    


    /**
     * correct additional fields
     */
    // protected function handleValidateField()
    // {
    //     $objCountry = null;

    //     $tmep = $this->objModel;

    //     if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] == $this->objEdtBillingPostalCode->getId())
    //     {
    //         $objCountry = new TSysCountries();
    //         // $objCountry->load
    //         // $itemp = $this->iDefaultCountryID;
    //     }

        
    //     return parent::handleValidateField();
    // }
    
}
