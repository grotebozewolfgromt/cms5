<?php

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
use dr\classes\models\TSysModel;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TProductsLanguages;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_products extends TCRUDListControllerAJAX
{
    
    /**
     * defines database query to execute
     * 
     * @return integer how many levels of tables to auto join: -1=unlimited, 0=none; 1=1level
     */
    public function defineDBQuery()
    {
        $objModel = $this->objModel;
        $objProdLang = new TProductsLanguages();
        $objSysLang = new TSysLanguages();

        $objModel->select(array(
            TProducts::FIELD_ID,
            TProducts::FIELD_RANDOMID,
            TSysModel::FIELD_CHECKOUTEXPIRES,
            TSysModel::FIELD_CHECKOUTSOURCE,
            TSysModel::FIELD_LOCKED,
            TSysModel::FIELD_LOCKEDSOURCE,
            TSysModel::FIELD_RECORDCREATED,
            TSysModel::FIELD_RECORDCHANGED,
                                ));
        $objModel->select(array(
            TProductsLanguages::FIELD_NAME
        ), $objProdLang);
        $objModel->select(array(
            TSysLanguages::FIELD_LANGUAGE
        ), $objSysLang);


        $objModel->joinLeft(TProducts::getTable(), TProducts::FIELD_ID, TProductsLanguages::getTable(), TProductsLanguages::FIELD_PRODUCTID);
        $objModel->joinLeft(TProductsLanguages::getTable(), TProductsLanguages::FIELD_TRANSLATIONLANGUAGEID, TSysLanguages::getTable(), TSysLanguages::FIELD_ID);
      
        //===show what?

        // $arrTableColumnsShow = array(
        $this->arrTableColumnsShow = array(
            array('', TProducts::FIELD_ID, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_ID, 'ID')),
            array(TProductsLanguages::getTable(), TProductsLanguages::FIELD_NAME, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TProductsLanguages::FIELD_NAME, 'Name')),
            array(TSysLanguages::getTable(), TSysLanguages::FIELD_LANGUAGE, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TSysLanguages::FIELD_LANGUAGE, 'Language')),
            array('', TSysModel::FIELD_RECORDCREATED, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_RECORDCREATED, 'Created')),
            array('', TSysModel::FIELD_RECORDCHANGED, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_RECORDCHANGED, 'Changed')),
                );
        
        //defining database filters
        $objFilters = $this->objDBFilters;

        //name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TProductsLanguages::getTable(), TProductsLanguages::FIELD_NAME);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TProductsLanguages::FIELD_NAME, 'Name'));
        $objFilters->addFilter($objFilter);

        //description
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TProductsLanguages::getTable(), TProductsLanguages::FIELD_DESCRIPTION);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TProductsLanguages::FIELD_DESCRIPTION, 'Description'));
        $objFilters->addFilter($objFilter);

        //language
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_HTMLELEMENT);
        $objFilter->setDBTableField(TSysLanguages::getTable(), TSysLanguages::FIELD_ID);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_language', 'Language'));
        $objSysLang->loadFromDBByCMSLanguage(); //load languages from database
        $objCombobox = new DRInputCombobox();
        while ($objSysLang->next())
        {
            $objDiv = new Div(); //for items inside the combox
            $objDiv->setTextContent($objSysLang->getLanguage());
            $objDiv->setAttribute('value', $objSysLang->getID());
            $objCombobox->appendChild($objDiv);
        }
        $objFilter->setHTMLElement($objCombobox);
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
        return new TProducts();
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
        return Mod_POSWebshop::PERM_CAT_PRODUCTS;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_products';
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
        return transm(APP_ADMIN_CURRENTMODULE, 'tab_title_products', 'Products');
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