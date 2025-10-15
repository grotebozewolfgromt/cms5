<?php
namespace dr\modules\Mod_Sys_Contacts\controllers;

use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\controllers\TCRUDListController;
use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\modules\Mod_Sys_Contacts\Mod_Sys_Contacts;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_contacts extends TCRUDListControllerAJAX
{
    
    /**
     * executes the controller
     * this function is ONLY called on a cache miss
     * to generate new content for the cache and to 
     * display to the screen
     *
     * 
     * @return array with variables, use: "return get_defined_vars();" to use all variables declared in the execute() function
     */
    /*
    public function execute()
    {
        // global $objCurrentModule;
        //global APP_ADMIN_CURRENTMODULE;        
        // global $arrTabsheets;        


        $objModel = $this->objModel;
        $objModel->select(array(
            TSysContacts::FIELD_ID, 
            TSysContacts::FIELD_CHECKOUTEXPIRES,
            TSysContacts::FIELD_CHECKOUTSOURCE,
            TSysContacts::FIELD_LOCKED,
            TSysContacts::FIELD_LOCKEDSOURCE,
            TSysContacts::FIELD_CUSTOMID,
            TSysContacts::FIELD_COMPANYNAME,
            TSysContacts::FIELD_FIRSTNAMEINITALS,
            TSysContacts::FIELD_LASTNAME,
            TSysContacts::FIELD_BILLINGADDRESSSTREET,
            TSysContacts::FIELD_BILLINGPOSTALCODEZIP,
            TSysContacts::FIELD_BILLINGCITY
                                    ));
      
        $this->executeDB();
      
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysContacts::FIELD_CUSTOMID, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_CUSTOMID, 'Identifier label')),
            array('', TSysContacts::FIELD_COMPANYNAME, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_COMPANYNAME, 'Company')),
            array('', TSysContacts::FIELD_FIRSTNAMEINITALS, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_FIRSTNAMEINITALS, 'Initials')),
            array('', TSysContacts::FIELD_LASTNAME, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_LASTNAME, 'Last name')),
            array('', TSysContacts::FIELD_BILLINGADDRESSSTREET, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_BILLINGADDRESSSTREET, 'Address')),
            array('', TSysContacts::FIELD_BILLINGPOSTALCODEZIP, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_BILLINGPOSTALCODEZIP, 'Postal/zip')),
            array('', TSysContacts::FIELD_BILLINGCITY, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_BILLINGCITY, 'City'))        
                );
        
    
        // $bNoRecordsToDisplay = false;
        // if ($objModel != null)
        // {
        //     if ($objModel->count() == 0)
        //             $bNoRecordsToDisplay = true;
        // }
             
        return get_defined_vars();    
    }
    */

   /**
     * defines database query to execute
     * 
     * @return integer how many levels of tables to auto join: -1=unlimited, 0=none; 1=1level
     */
    public function defineDBQuery()
    {
        $objModel = $this->objModel;

        $objModel->select(array(
            TSysContacts::FIELD_ID, 
            TSysContacts::FIELD_NICEID, 
            TSysContacts::FIELD_CHECKOUTEXPIRES,
            TSysContacts::FIELD_CHECKOUTSOURCE,
            TSysContacts::FIELD_LOCKED,
            TSysContacts::FIELD_LOCKEDSOURCE,
            TSysContacts::FIELD_CUSTOMID,
            TSysContacts::FIELD_COMPANYNAME,
            TSysContacts::FIELD_FIRSTNAMEINITALS,
            TSysContacts::FIELD_LASTNAME,
            TSysContacts::FIELD_BILLINGADDRESSSTREET,
            TSysContacts::FIELD_BILLINGPOSTALCODEZIP,
            TSysContacts::FIELD_BILLINGCITY
                                    ));
     
        //===show what?

        $this->arrTableColumnsShow = array(
            array('', TSysContacts::FIELD_ID, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_ID, 'Id')),
            array('', TSysContacts::FIELD_NICEID, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_NICEID, 'Nice Id')),
            array('', TSysContacts::FIELD_CUSTOMID, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_CUSTOMID, 'Custom id')),
            array('', TSysContacts::FIELD_COMPANYNAME, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_COMPANYNAME, 'Company')),
            array('', TSysContacts::FIELD_FIRSTNAMEINITALS, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_FIRSTNAMEINITALS, 'Initials')),
            array('', TSysContacts::FIELD_LASTNAME, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_LASTNAME, 'Last name')),
            array('', TSysContacts::FIELD_BILLINGADDRESSSTREET, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_BILLINGADDRESSSTREET, 'Address')),
            array('', TSysContacts::FIELD_BILLINGPOSTALCODEZIP, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_BILLINGPOSTALCODEZIP, 'Postal')),
            array('', TSysContacts::FIELD_BILLINGCITY, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_BILLINGCITY, 'City'))        
                );
                
        
        //defining database filters
        $objFilters = $this->objDBFilters;


        //company name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_COMPANYNAME);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_COMPANYNAME, 'Company name'));
        $objFilters->addFilter($objFilter);

        //custom identifier
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_CUSTOMID);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_CUSTOMID, 'Custom Id'));
        $objFilters->addFilter($objFilter);

        //id
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_NUMBER);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_ID);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_ID, 'Record Id'));
        $objFilters->addFilter($objFilter);        


        //unique id
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_NUMBER);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_UNIQUEID);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_UNIQUEID, 'Unique Id'));
        $objFilters->addFilter($objFilter);     

        //nice id
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_NICEID);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_NICEID, 'Nice Id'));
        $objFilters->addFilter($objFilter);  
        
        //last name
        if (!APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME)
        {
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_LASTNAME);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_LASTNAME, 'Last name'));
            $objFilters->addFilter($objFilter);  
        }

        
        if (!APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS)
        {
            //address: billing
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_BILLINGADDRESSSTREET);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_BILLINGADDRESSSTREET, 'Street (billing)'));
            $objFilters->addFilter($objFilter);  

            //address: delivery
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_DELIVERYADDRESSSTREET);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_DELIVERYADDRESSSTREET, 'Street (delivery)'));
            $objFilters->addFilter($objFilter);  
        }     


        if (!APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP)
        {
            //postal code: billing
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_BILLINGPOSTALCODEZIP);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_BILLINGPOSTALCODEZIP, 'Postal code (billing)'));
            $objFilters->addFilter($objFilter);  
            
            //postal code: delivery
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_BILLINGPOSTALCODEZIP);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, 'Postal code (delivery)'));
            $objFilters->addFilter($objFilter);  
        }     

        if (!APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS)
        {
            //personal email
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_EMAILADDRESSENCRYPTED);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_EMAILADDRESSENCRYPTED, 'Email address (personal)'));
            $objFilters->addFilter($objFilter);  
            
            //billing email
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, 'Email address (billing)'));
            $objFilters->addFilter($objFilter);  
        }     

        if (!APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER)
        {
            //phone 1
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_PHONENUMBER1);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_PHONENUMBER1, 'Phone number 1'));
            $objFilters->addFilter($objFilter);  
            
            //phone 2
            $objFilter = new DRDBFilter();
            $objFilter->setStatus(DRDBFilter::STATUS_APPLIED); //showing in menu instead of directly visible
            $objFilter->setDisabled(true);//disabled by default when adding filter chip
            $objFilter->setType(DRDBFilter::TYPE_STRING);
            $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_PHONENUMBER2);
            $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_PHONENUMBER2, 'Phone number 2'));
            $objFilters->addFilter($objFilter);  
        }           

        //notes
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_NOTES);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_NOTES, 'Notes'));
        $objFilters->addFilter($objFilter);     

        //first contact
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_DATE);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_FIRSTCONTACT);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_FIRSTCONTACT, 'First contact'));
        $objFilters->addFilter($objFilter);      

        //last contact
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_DATE);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_LASTCONTACT);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_LASTCONTACT, 'Last contact'));
        $objFilters->addFilter($objFilter);   

        //billing city
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_BILLINGCITY);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_BILLINGCITY, 'City (billing)'));
        $objFilters->addFilter($objFilter);          
        
        //delivery city
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_DELIVERYCITY);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_DELIVERYCITY, 'City (delivery)'));
        $objFilters->addFilter($objFilter);   
        
        //search keywords
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysContacts::getTable(), TSysContacts::FIELD_SEARCHKEYWORDS);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TSysContacts::FIELD_SEARCHKEYWORDS, 'Search keywords'));
        $objFilters->addFilter($objFilter);   
        
        
        return 0;
    }


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modellistajax.php';
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
     * return new TSysModel object
     * 
     * @return TSysModel;
     */
    public function getNewModel()
    {
        return new TSysContacts();
    }

    /**
     * return permission category 
     * =class constant of module class
     * 
     * for example: Mod_Sys_CMSUsers::PERM_CAT_USERS
     *
     * @return string
     */
    public function getAuthorisationCategory()
    {
        return Mod_Sys_Contacts::PERM_CAT_CONTACTS;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_contacts';
    }

    /**
     * return page title
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "create a new user" or "edit user John" (based on if $objModel->getNew())
     *
     * @return string
     */
    function getTitle()
    {
        //global APP_ADMIN_CURRENTMODULE;
        return transm(APP_ADMIN_CURRENTMODULE, TRANS_MODULENAME_TITLE, 'Contacts');
    }

    /**
     * show tabsheets on top?
     *
     * @return bool
     */
    public function showTabs()
    {
        return true;
    }      
  
}