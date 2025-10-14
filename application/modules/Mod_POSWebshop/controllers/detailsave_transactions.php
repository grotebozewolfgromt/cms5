<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\controllers\TControllerAbstract;
use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\controllers\TCRUDDetailSaveController_org;
use dr\classes\locale\TLocalisation;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputColor;
use dr\classes\dom\tag\form\Textarea;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\form\Label;
use dr\classes\dom\tag\form\InputDatetime;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\Script;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\ColorHex;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\Time;
use dr\classes\locale\TTranslation;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use  dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersAccounts;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\modules\Mod_Sys_Localisation\models\TSysCurrencies;
use dr\classes\models\TSysUsersAbstract;
use dr\classes\types\TCurrency;
use dr\classes\types\TDecimal;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TTransactionsTypes;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;
use dr\modules\Mod_POSWebshop\models\TTransactions;
use dr\modules\Mod_POSWebshop\models\TTransactionsLines;

include_once(APP_PATH_CMS . DIRECTORY_SEPARATOR . 'bootstrap_admin_auth.php');




/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class detailsave_transactions extends TCRUDDetailSaveController
{
    //fields
    public $objSelTransactionsType = null; //dr\classes\dom\tag\form\Select
    public $objSelCurrency = null; //dr\classes\dom\tag\form\Select     
    public $objHidBuyer = null; //dr\classes\dom\tag\form\Select     
    public $objEdtPurchaseOrderNo = null; //dr\classes\dom\tag\form\InputText
    public $objTxtNotesInternal = null; //dr\classes\dom\tag\form\Textarea
    public $objTxtNotesExternal = null; //dr\classes\dom\tag\form\Textarea

    //lines
    public $objEdtQuantity = null; //dr\classes\dom\tag\form\InputText
    public $objEdtDescription = null; //dr\classes\dom\tag\form\InputText
    public $objEdtVATPercentage = null; //dr\classes\dom\tag\form\InputText
    public $objEdtPurchasePriceExclVAT = null; //dr\classes\dom\tag\form\InputText
    public $objEdtDiscountPriceExclVAT = null; //dr\classes\dom\tag\form\InputText
    public $objEdtPriceExclVAT = null; //dr\classes\dom\tag\form\InputText


    // private $objForm = null; ////dr\classes\dom\tag\form\Form --> NOT a form generator, because it has so many custom elements
    public $objHidFormSubmitted = null; //dr\classes\dom\tag\form\InputHidden this field is used to detect of form is submitted. dom element is filled with number when the form is submitted.
    public $objHidCSRFToken = null; //dr\classes\dom\tag\form\InputHidden this field is used to detect Cross Site Request Forgery


    public $objTransactionLines = null; //TTransactionsLines

    /**
     * 
     */
    public function __construct()
    {
        $this->objTransactionLines = new TTransactionsLines();

        parent::__construct();
    }

    /**
     * define the fields that are in the detail screen
     * 
     */
    protected function populate()
    {
        //obligatory fields for forms
        $this->objHidFormSubmitted = $this->getFormGenerator()->getFormSubmittedDOMElement(); //use the hidden field from form generator to detect if form is submitted
        $this->objHidCSRFToken = $this->getFormGenerator()->getCSRFTokenDOMElement(); //use the hidden field to detect cross site forgergy

        //transactions-types
        $this->objSelTransactionsType = new Select();
        $this->objSelTransactionsType->setNameAndID('edtTransactionsTypeID');
        // $this->objSelTransactionsType->setClass('fullwidthtag');
        // $this->getFormGenerator()->add($this->objSelTransactionsType, '', transm($this->getModule(), 'form_field_name', 'Name'));


        //currency
        $this->objSelCurrency = new Select();
        $this->objSelCurrency->setNameAndID('selCurrencyID');
        // $this->getFormGenerator()->add($this->objSelCurrency, '', transm($this->getModule(), 'form_field_isstock', 'Stock managing transaction (stock reduced or increased when transaction completed)'));


        //buyer
        $this->objHidBuyer = new Select();
        $this->objHidBuyer->setNameAndID('hdBuyerID');
        // $this->getFormGenerator()->add($this->objSelCurrency, '', transm($this->getModule(), 'form_field_isstock', 'Stock managing transaction (stock reduced or increased when transaction completed)'));



        //purchase order number
        $this->objEdtPurchaseOrderNo = new InputText();
        $this->objEdtPurchaseOrderNo->setNameAndID('edtPurchaseOrderNo');
        // $this->objEdtPurchaseOrderNo->setClass('fullwidthtag');   
        $this->objEdtPurchaseOrderNo->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtPurchaseOrderNo->addValidator($objValidator);
        // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));


        //==== LINES

            //quantity
            $this->objEdtQuantity = new InputText(true);
            $this->objEdtQuantity->setName('edtQuantity');
            // $this->objEdtQuantity->setClass('fullwidthtag');   
            $this->objEdtQuantity->setMaxLength(10);
            $objValidator = new TMaximumLength(10);
            $this->objEdtQuantity->addValidator($objValidator);
            // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));


            //description
            $this->objEdtDescription = new InputText(true);
            $this->objEdtDescription->setName('edtDescription');
            // $this->objEdtDescription->setClass('fullwidthtag');   
            $this->objEdtDescription->setMaxLength(50);
            $objValidator = new TMaximumLength(50);
            $this->objEdtDescription->addValidator($objValidator);
            // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));


            //vat percentage
            $this->objEdtVATPercentage = new InputText(true);
            $this->objEdtVATPercentage->setName('edtVATPercentage');
            // $this->objEdtVATPercentage->setClass('fullwidthtag');   
            $this->objEdtVATPercentage->setMaxLength(10);
            $objValidator = new TMaximumLength(10);
            $this->objEdtVATPercentage->addValidator($objValidator);
            // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));
        

            //purchase price
            $this->objEdtPurchasePriceExclVAT = new InputText(true);
            $this->objEdtPurchasePriceExclVAT->setName('edtPurchasePrice');
            // $this->objEdtPurchasePriceExclVAT->setClass('fullwidthtag');   
            $this->objEdtPurchasePriceExclVAT->setMaxLength(10);
            $objValidator = new TMaximumLength(10);
            $this->objEdtPurchasePriceExclVAT->addValidator($objValidator);
            // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));


            //discount price
            $this->objEdtDiscountPriceExclVAT = new InputText(true);
            $this->objEdtDiscountPriceExclVAT->setName('edtDiscountPrice');
            // $this->objEdtDiscountPriceExclVAT->setClass('fullwidthtag');   
            $this->objEdtDiscountPriceExclVAT->setMaxLength(10);
            $objValidator = new TMaximumLength(10);
            $this->objEdtDiscountPriceExclVAT->addValidator($objValidator);
            // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));
            

            //unit price
            $this->objEdtPriceExclVAT = new InputText(true);
            $this->objEdtPriceExclVAT->setName('edtUnitPrice');
            // $this->objEdtPriceExclVAT->setClass('fullwidthtag');   
            $this->objEdtPriceExclVAT->setMaxLength(10);
            $objValidator = new TMaximumLength(10);
            $this->objEdtPriceExclVAT->addValidator($objValidator);
            // $this->getFormGenerator()->add($this->objEdtPurchaseOrderNo, '', transm($this->getModule(), 'form_field_newincrementednumber', 'New transaction starts at number'));
                 


        //internal notes
        $this->objTxtNotesInternal = new Textarea();
        $this->objTxtNotesInternal->setNameAndID('txtInternalNotes');
        $this->objTxtNotesInternal->setClass('fullwidthtag');   
        $this->objTxtNotesInternal->addValidator($objValidator);
        // $this->getFormGenerator()->add($this->objTxtAddress, '', transm($this->getModule(), 'form_field_addressseller', 'Address seller'));


         //external notes
         $this->objTxtNotesExternal = new Textarea();
         $this->objTxtNotesExternal->setNameAndID('txtExternalNotes');
         $this->objTxtNotesExternal->setClass('fullwidthtag');   
         $this->objTxtNotesExternal->addValidator($objValidator);
        //  $this->getFormGenerator()->add($this->objTxtAddress, '', transm($this->getModule(), 'form_field_addressseller', 'Address seller'));
 
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory()
    {
        return Mod_POSWebshop::PERM_CAT_TRANSACTIONS;
    }

    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        global $objAuthenticationSystem;


        //===== HEADER ====

        //transaction type
        $this->getModel()->set(TTransactions::FIELD_TRANSACTIONSTYPEID, $this->objSelTransactionsType->getValueSubmittedAsInt());

        //currency
        $this->getModel()->set(TTransactions::FIELD_CURRENCYID, $this->objSelCurrency->getValueSubmittedAsInt());

        //buyer
        $this->getModel()->set(TTransactions::FIELD_BUYERCONTACTID, $this->objHidBuyer->getValueSubmittedAsInt());

        //purchase order number
        $this->getModel()->set(TTransactions::FIELD_PURCHASEORDERNUMBER, $this->objEdtPurchaseOrderNo->getValueSubmitted());


        //==== LINES ====
        $iTotalLines = count($this->objEdtDescription->getValueSubmitted()); //get length of array of one of the arrays, doesn't matter which one
        if ($iTotalLines > 0)
        {
            $this->objTransactionLines->resetRecordPointer();
            for ($iLC = 0; $iLC < $iTotalLines; $iLC++)
            {
                $this->objTransactionLines->newRecord();

                $this->objTransactionLines->set(TTransactionsLines::FIELD_QUANTITY, new TDecimal($this->objEdtQuantity->getValueSubmitted()[$iLC], 4));
                $this->objTransactionLines->set(TTransactionsLines::FIELD_DESCRIPTION, $this->objEdtDescription->getValueSubmitted()[$iLC]);
                $this->objTransactionLines->set(TTransactionsLines::FIELD_VATPERCENTAGE, new TDecimal($this->objEdtVATPercentage->getValueSubmitted()[$iLC]));
                $this->objTransactionLines->set(TTransactionsLines::FIELD_UNITPURCHASEPRICEEXCLVAT, new TDecimal($this->objEdtPurchasePriceExclVAT->getValueSubmitted()[$iLC]));
                $this->objTransactionLines->set(TTransactionsLines::FIELD_UNITDISCOUNTEXCLVAT, new TDecimal($this->objEdtDiscountPriceExclVAT->getValueSubmitted()[$iLC]));
                $this->objTransactionLines->set(TTransactionsLines::FIELD_UNITPRICEEXCLVAT, new TDecimal($this->objEdtPriceExclVAT->getValueSubmitted()[$iLC]));
                $this->objTransactionLines->set(TTransactionsLines::FIELD_POSITION, ($iLC+1)); //use a new order value
            }
        }      
        //save is done in onSavePost(), 
        //because there we have the transactionid to store in transaction lines
        //BUT we need to read the lines here from the fields, because we need to calculate the meta fields to save them in TTransaction


        //==== NOTES ===

        //internal notes
        $this->getModel()->set(TTransactions::FIELD_NOTESINTERNAL, $this->objTxtNotesInternal->getValueSubmitted());

        //external notes
        $this->getModel()->set(TTransactions::FIELD_NOTESEXTERNAL, $this->objTxtNotesExternal->getValueSubmitted());


        //==== HISTORY ===




        //==== AUTO GENERATED ====

        //user who created the transaction
        $this->getModel()->set(TTransactions::FIELD_CREATEDBYCONTACTID, $objAuthenticationSystem->getUsers()->getID());

        //date
        $this->getModel()->set(TTransactions::FIELD_DATEFINALIZED, new TDateTime());

        //meta fields
        $this->getModel()->set(TTransactions::FIELD_META_TOTALPRICEINCLVAT, $this->objTransactionLines->calculateTotalPriceInclVat());
        $this->getModel()->set(TTransactions::FIELD_META_TOTALPRICEEXCLVAT, $this->objTransactionLines->calculateTotalPriceExclVat());
        $this->getModel()->set(TTransactions::FIELD_META_TOTALPURCHASEPRICEEXCLVAT, $this->objTransactionLines->calculateTotalPurchasePriceExclVat());
        $this->getModel()->set(TTransactions::FIELD_META_TOTALVAT, $this->objTransactionLines->calculateTotalVat());
        // $this->getModel()->set(TTransactions::FIELD_META_AMOUNTDUE, 0);--> @todo from transaction payments

    }

    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {
       
        //==== HEADER ====

        //transactions-types
        $objTypes = new TTransactionsTypes();
        $objTypes->sort(TTransactionsTypes::FIELD_POSITION);
        $objTypes->limit(1000);
        $objTypes->loadFromDB();
        $objTypes->generateHTMLSelect($this->getModel()->get(TTransactions::FIELD_TRANSACTIONSTYPEID), $this->objSelTransactionsType);
                      
        //currency
        $objCurr = new TSysCurrencies();
        $objCurr->sort(TSysCurrencies::FIELD_POSITION);
        $objCurr->where(TSysCurrencies::FIELD_ISFAVORITE, true);
        $objCurr->loadFromDB();
        $objCurr->generateHTMLSelect($this->getModel()->get(TTransactions::FIELD_CURRENCYID), $this->objSelCurrency);

        //buyer contact id
        $objContacts = new TSysContacts();
        $objContacts->sort(TSysContacts::FIELD_CUSTOMID);
        $objContacts->where(TSysContacts::FIELD_ISCLIENT, true); 
        $objContacts->limitNone(); //not very fun, but hope to have a better solution in the future
        $objContacts->loadFromDB();
        $objContacts->generateHTMLSelect($this->getModel()->get(TTransactions::FIELD_BUYERCONTACTID), $this->objHidBuyer);

        //purchase order number
        $this->objEdtPurchaseOrderNo->setValue($this->getModel()->get(TTransactions::FIELD_PURCHASEORDERNUMBER));


        //==== TRANSACTIONS LINES ====
        if ($this->isNewRecord()) //new record? create 1 line as default (otherwise we can't add lines later, because those are copied from the first line)
        {
            $this->objTransactionLines->newRecord();
            $this->objTransactionLines->setQuantity(new TDecimal('1'));
        }
        else //existing record? Load from Database!
        {
            $this->objTransactionLines->clear();
            // $this->objTransactionLines->select();
            $this->objTransactionLines->where(TTransactionsLines::FIELD_TRANSACTIONSID, $_GET[ACTION_VARIABLE_ID]);
            $this->objTransactionLines->sort(TTransactionsLines::FIELD_POSITION);
            $this->objTransactionLines->limit(1000);
            $this->objTransactionLines->loadFromDB();
        }
        //fields are actually set in the template

        //===== NOTES ====

        //internal notes
        $this->objTxtNotesInternal->setValue($this->getModel()->get(TTransactions::FIELD_NOTESINTERNAL));

        //external notes
        $this->objTxtNotesExternal->setValue($this->getModel()->get(TTransactions::FIELD_NOTESEXTERNAL));
    }

    /**
     * is called when a record is loaded
     */
    public function onLoad()
    {
    }

    /**
     * is called when a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * @return boolean it will NOT SAVE
     */
    public function onSavePre()
    {
        return true;
    }

    /**
     * is called AFTER a record is saved
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return boolean returns true on success otherwise false
     */
    public function onSavePost($bWasSaveSuccesful)
    {
        //we need the transaction id first. 
        //We only get id on a new transaction after the transaction is created
             
        //delete old lines
        $this->objTransactionLines->where(TTransactionsLines::FIELD_TRANSACTIONSID, $_GET[ACTION_VARIABLE_ID]);
        $this->objTransactionLines->deleteFromDB(true);

        //go through all transaction lines and update the transaction id, so we can save the lines
        $this->objTransactionLines->resetRecordPointer();
        while ($this->objTransactionLines->next())
        {
            $this->objTransactionLines->set(TTransactionsLines::FIELD_TRANSACTIONSID, $this->getModel()->getID());
        }

        if (!$this->objTransactionLines->saveToDBAll())
            return false;

        //history
        //@todo

        
        return true;
    }


    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate()
    {
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
        return false;
    }



    /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TTransactions();
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return getPathModuleTemplates($this->getModule(), true).'tpl_detailsave_transactions.php';
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
        return APP_PATH_CMS_TEMPLATES . DIRECTORY_SEPARATOR . 'skin_withmenu.php';
    }

    /**
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        return 'list_transactions';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_transactions_new', 'Create new transaction');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_transactions_edit', 'Edit transaction');
            // return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_transactions_edit', 'Edit transaction: [name]', 'name', $this->getModel()->getName());
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
