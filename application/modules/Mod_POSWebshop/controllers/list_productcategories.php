<?php

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
use dr\classes\models\TSysModel;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TProductCategories;
use dr\modules\Mod_POSWebshop\models\TProductCategoriesLanguages;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TProductsLanguages;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


/**
 * I assume the parent TProductCategories as primary (for id and position and depth level) and the translations is secondary
 */
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
        $objModel->select(array(
            TProductCategoriesLanguages::FIELD_NAME,
            TProductCategoriesLanguages::FIELD_URLSLUG,
                                ), new TProductCategoriesLanguages());   
        $objModel->select(array(
            TSysLanguages::FIELD_LANGUAGE,
                                ), new TSysLanguages());   

        $objModel->joinLeft(TProductCategories::getTable(), TProductCategories::FIELD_ID, TProductCategoriesLanguages::getTable(), TProductCategoriesLanguages::FIELD_PRODUCTCATEGORYID);                                
        $objModel->joinLeft(TProductCategoriesLanguages::getTable(), TProductCategoriesLanguages::FIELD_TRANSLATIONLANGUAGEID, TSysLanguages::getTable(), TSysLanguages::FIELD_ID);

        $objModel->where(TSysLanguages::FIELD_ISDEFAULT, true, COMPARISON_OPERATOR_EQUAL_TO, TSysLanguages::getTable()); //i need at least 1 field from TSysLanguages for where to work
        
        //===show what?
        $this->arrTableColumnsShow = array(
            array('', TProductCategories::FIELD_ID, transm(APP_ADMIN_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_ID, 'ID')),
            array(TProductCategoriesLanguages::getTable(), TProductCategoriesLanguages::FIELD_NAME, transm(APP_ADMIN_CURRENTMODULE, 'list_productcategories_column_'.TProductCategoriesLanguages::FIELD_NAME, 'Name')),
            array(TProductCategoriesLanguages::getTable(), TProductCategoriesLanguages::FIELD_URLSLUG, transm(APP_ADMIN_CURRENTMODULE, 'list_productcategories_column_'.TProductCategoriesLanguages::FIELD_URLSLUG, 'Slug')),
            array('', TProductCategories::FIELD_ISDEFAULT, transm(APP_ADMIN_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_ISDEFAULT, 'Default')),
            array('', TProductCategories::FIELD_ISFAVORITE, transm(APP_ADMIN_CURRENTMODULE, 'list_productcategories_column_'.TProductCategories::FIELD_ISFAVORITE, 'Favorite')),
            array('', TSysModel::FIELD_RECORDCREATED, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_RECORDCREATED, 'Created')),
            array('', TSysModel::FIELD_RECORDCHANGED, transm(APP_ADMIN_CURRENTMODULE, 'list_products_column_'.TProducts::FIELD_RECORDCHANGED, 'Changed')),
            array('', TSysModel::FIELD_POSITION, 'Pos'),
                );
        
        //defining database filters
        $objFilters = $this->objDBFilters;

        //name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TProductCategories::getTable(), TProductCategoriesLanguages::FIELD_NAME);
        $objFilter->setNameNice(transm(APP_ADMIN_CURRENTMODULE, 'dbfilter_column_'.TProductCategoriesLanguages::FIELD_NAME, 'Name'));
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
        //global APP_ADMIN_CURRENTMODULE;
        return transm(APP_ADMIN_CURRENTMODULE, 'tab_title_productcategories', 'Product categories');
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