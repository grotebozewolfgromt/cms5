<?php

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\controllers\TCRUDListController;
use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\classes\models\TSysModel;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TTransactions;
use dr\modules\Mod_POSWebshop\models\TTransactionsTypes;

// use dr\modules\Mod_Sys_Contacts\Mod_Sys_Contacts;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_transactionstypes extends TCRUDListControllerAJAX
{
    
    /**
     * defines database query to execute
     * 
     * @return integer how many levels of tables to auto join: -1=unlimited, 0=none; 1=1level
     */
    public function defineDBQuery()
    {
        $objModel = $this->objModel;
        $objModel->select(array(
            TTransactionsTypes::FIELD_NAME, 
            TTransactionsTypes::FIELD_DESCRIPTION, 
            TTransactionsTypes::FIELD_AVAILABLESTOCKADD,
            TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT,
            TTransactionsTypes::FIELD_RESERVEDSTOCKADD,
            TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT,
            TTransactionsTypes::FIELD_FINANCIALADD,
            TTransactionsTypes::FIELD_FINANCIALSUBTRACT,
            TTransactionsTypes::FIELD_ISDEFAULT,
            TTransactionsTypes::FIELD_ISFAVORITE,
            TTransactionsTypes::FIELD_ISDEFAULTINVOICE,
            TTransactionsTypes::FIELD_ISDEFAULTORDER,
            TTransactionsTypes::FIELD_COLORBACKGROUND,
            TTransactionsTypes::FIELD_NEWNUMBERINCREMENT,
            TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS,
            TSysModel::FIELD_POSITION
                                    ));

      
        //===show what?

        // $arrTableColumnsShow = array(
        $this->arrTableColumnsShow = array(
            array('', TTransactionsTypes::FIELD_NAME, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_NAME, 'Type name')),
            array('', TTransactionsTypes::FIELD_DESCRIPTION, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_DESCRIPTION, 'Description')),
            array('', TTransactionsTypes::FIELD_AVAILABLESTOCKADD, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_AVAILABLESTOCKADD, 'Avail +')),
            array('', TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT, 'Avail -')),
            array('', TTransactionsTypes::FIELD_RESERVEDSTOCKADD, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_RESERVEDSTOCKADD, 'Res +')),
            array('', TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT, 'Res -')),
            array('', TTransactionsTypes::FIELD_FINANCIALADD, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_FINANCIALADD, '$ +')),
            array('', TTransactionsTypes::FIELD_FINANCIALSUBTRACT, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_FINANCIALSUBTRACT, '$ -')),
            array('', TTransactionsTypes::FIELD_ISDEFAULT, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_ISDEFAULT, 'Default')),
            array('', TTransactionsTypes::FIELD_ISFAVORITE, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_ISFAVORITE, 'Fav')),
            array('', TTransactionsTypes::FIELD_ISDEFAULTINVOICE, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_ISDEFAULTINVOICE, 'Invoice')),
            array('', TTransactionsTypes::FIELD_ISDEFAULTORDER, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_ISDEFAULTORDER, '0rder')),
            array('', TTransactionsTypes::FIELD_COLORBACKGROUND, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_COLORBACKGROUND, 'Color')),
            array('', TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, 'Start')),
            array('', TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, transm(CMS_CURRENTMODULE, 'list_column_'.TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, 'Pay')),
            array('', TSysModel::FIELD_POSITION, transm(CMS_CURRENTMODULE, 'list_column_'.TSysModel::FIELD_POSITION, 'Order'))
                );
        
        //defining database filters
        $objFilters = $this->objDBFilters;

        //name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_NAME);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_NAME, 'Name'));
        $objFilters->addFilter($objFilter);

        //description
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_DESCRIPTION);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_DESCRIPTION, 'Description'));
        $objFilters->addFilter($objFilter);

        //start number
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_NUMBER);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_NEWNUMBERINCREMENT);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, 'Start increment number'));
        $objFilters->addFilter($objFilter);

        //payment within # days
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_NUMBER);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, 'Payment within # days'));
        $objFilters->addFilter($objFilter);

        //available stock +
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_AVAILABLESTOCKADD);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_AVAILABLESTOCKADD, 'Available Stock +'));
        $objFilters->addFilter($objFilter);

        //available stock -
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT, 'Available Stock -'));
        $objFilters->addFilter($objFilter);

        //reserved stock +
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_RESERVEDSTOCKADD);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_RESERVEDSTOCKADD, 'Reserved Stock +'));
        $objFilters->addFilter($objFilter);

        //reserved stock -
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT, 'Reserved Stock -'));
        $objFilters->addFilter($objFilter);

        //financial +
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_FINANCIALADD);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_FINANCIALADD, 'Financial Transaction +'));
        $objFilters->addFilter($objFilter);        

        //financial -
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField('', TTransactionsTypes::FIELD_FINANCIALSUBTRACT);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TTransactionsTypes::FIELD_FINANCIALSUBTRACT, 'Financial Transaction -'));
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
        return new TTransactionsTypes();
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
        return Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_transactionstypes';
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
        //global CMS_CURRENTMODULE;
        return transm(CMS_CURRENTMODULE, 'tab_title_transactiontypes', 'Transaction types');
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