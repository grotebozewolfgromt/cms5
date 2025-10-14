<?php

namespace dr\modules\Mod_Sys_Settings\controllers;

use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_Settings\Mod_Sys_Settings;
use dr\modules\Mod_Sys_Settings\models\TSysCMSMenu;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_cmsmenu extends TCRUDListControllerAJAX
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
            TSysCMSMenu::FIELD_ID,
            TSysCMSMenu::FIELD_NAMEDEFAULT,
            TSysCMSMenu::FIELD_URL,
            TSysCMSMenu::FIELD_SVGICON,
            TSysCMSMenu::FIELD_PERMISSIONRESOURCE,
            TSysCMSMenu::FIELD_CONTROLLER,
            TSysCMSMenu::FIELD_MODULENAMEINTERNAL,
            TSysCMSMenu::FIELD_OPENNEWTAB,
            TSysCMSMenu::FIELD_META_DEPTHLEVEL,
            TSysCMSMenu::FIELD_CHECKOUTEXPIRES,
            TSysCMSMenu::FIELD_CHECKOUTSOURCE,
            TSysCMSMenu::FIELD_LOCKED,
            TSysCMSMenu::FIELD_LOCKEDSOURCE,
            TSysCMSMenu::FIELD_POSITION,
            TSysCMSMenu::FIELD_ISFAVORITE,
            TSysCMSMenu::FIELD_ISVISIBLEMENU,
            TSysCMSMenu::FIELD_ISVISIBLETOOLBAR,
                                ));      
        //===show what?

        // $arrTableColumnsShow = array(
        $this->arrTableColumnsShow = array(
            array('', TSysCMSMenu::FIELD_ID, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_ID, 'ID')),
            array('', TSysCMSMenu::FIELD_SVGICON, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_SVGICON, 'Icon')),
            array('', TSysCMSMenu::FIELD_NAMEDEFAULT, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_NAMEDEFAULT, 'Name default')),
            // array('', TSysCMSMenu::FIELD_MODULECONTROLLER, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_MODULECONTROLLER, 'Module URL')),
            // array('', TSysCMSMenu::FIELD_URL, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_URL, 'URL')),
            // array('', TSysCMSMenu::FIELD_PERMISSIONRESOURCE, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_PERMISSIONRESOURCE, 'Permission')),
            array('', TSysCMSMenu::FIELD_ISFAVORITE, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_ISFAVORITE, 'Favorite')),
            array('', TSysCMSMenu::FIELD_ISVISIBLEMENU, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_ISVISIBLEMENU, 'Menu')),
            array('', TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, 'Toolbar')),
            array('', TSysCMSMenu::FIELD_OPENNEWTAB, transm(CMS_CURRENTMODULE, 'list_cmsmenu_column_'.TSysCMSMenu::FIELD_OPENNEWTAB, 'New tab')),
            array('', TSysModel::FIELD_POSITION, 'Pos'),
                );
        
        //defining database filters
        $objFilters = $this->objDBFilters;

        //name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_NAMEDEFAULT);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_NAMEDEFAULT, 'Name default'));
        $objFilters->addFilter($objFilter);

        //module controller
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_CONTROLLER);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_CONTROLLER, 'Module URL'));
        $objFilters->addFilter($objFilter);

        //url
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_URL);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_URL, 'URL'));
        $objFilters->addFilter($objFilter);

        //permission resource
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_PERMISSIONRESOURCE);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_PERMISSIONRESOURCE, 'Permission resource'));
        $objFilters->addFilter($objFilter);

        //visible in menu
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_ISVISIBLEMENU);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_PERMISSIONRESOURCE, 'Visible in menu'));
        $objFilters->addFilter($objFilter);

        //visible in toolbar
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_ISVISIBLETOOLBAR);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, 'Visible in toolbar'));
        $objFilters->addFilter($objFilter);
        
        //Open in new tab
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_BOOLEAN);
        $objFilter->setDBTableField(TSysCMSMenu::getTable(), TSysCMSMenu::FIELD_OPENNEWTAB);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TSysCMSMenu::FIELD_OPENNEWTAB, 'Open in new tab'));
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
        return new TSysCMSMenu();
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
        return Mod_Sys_Settings::PERM_CAT_CMSMENU;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_cmsmenu';
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
        return transm(CMS_CURRENTMODULE, 'tab_title_cmsmenu', 'CMS menu');
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