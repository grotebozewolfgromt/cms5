<?php

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
use dr\classes\models\TSysModel;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TProductCategories;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TProductsLanguages;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_productcategories extends TCRUDListControllerAJAX
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
            TProductCategories::FIELD_ID,
            TProductCategories::FIELD_NAME,
            TProductCategories::FIELD_URLSLUG,
            TProductCategories::FIELD_META_DEPTHLEVEL,
            TProductCategories::FIELD_CHECKOUTEXPIRES,
            TProductCategories::FIELD_CHECKOUTSOURCE,
            TProductCategories::FIELD_LOCKED,
            TProductCategories::FIELD_LOCKEDSOURCE,
            TProductCategories::FIELD_RECORDCREATED,
            TProductCategories::FIELD_RECORDCHANGED,
            TProductCategories::FIELD_POSITION,
            TProductCategories::FIELD_ISDEFAULT,
            TProductCategories::FIELD_ISFAVORITE,
                                ));      
        //===show what?

        // $arrTableColumnsShow = array(
        $this->arrTableColumnsShow = array(
            array('', TProductCategories::FIELD_ID, transm(CMS_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_ID, 'ID')),
            array('', TProductCategories::FIELD_NAME, transm(CMS_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_NAME, 'Name')),
            array('', TProductCategories::FIELD_URLSLUG, transm(CMS_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_URLSLUG, 'Slug')),
            array('', TProductCategories::FIELD_ISDEFAULT, transm(CMS_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_ISDEFAULT, 'Default')),
            array('', TProductCategories::FIELD_ISFAVORITE, transm(CMS_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_ISFAVORITE, 'Favorite')),
            array('', TSysModel::FIELD_RECORDCREATED, transm(CMS_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_RECORDCREATED, 'Created')),
            array('', TSysModel::FIELD_RECORDCHANGED, transm(CMS_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_RECORDCHANGED, 'Changed')),
            array('', TSysModel::FIELD_POSITION, 'Pos'),
                );
        
        //defining database filters
        $objFilters = $this->objDBFilters;

        //name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TProductCategories::getTable(), TProductCategories::FIELD_NAME);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TProductCategories::FIELD_NAME, 'Name'));
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
        return new TProductCategories();
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
        return Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_productcategories';
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
        return transm(CMS_CURRENTMODULE, 'tab_title_productcategories', 'Product categories');
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