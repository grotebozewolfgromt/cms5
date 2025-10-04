<?php

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\controllers\TCRUDListControllerAJAX;
use dr\classes\dom\tag\webcomponents\DRDBFilter;
use dr\classes\models\TSysModel;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TVATClasses;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_vatclasses extends TCRUDListControllerAJAX
{
    
    /**
     * defines database query to execute
     *
     * 
     * @return integer how many levels of tables to auto join: -1=unlimited, 0=none; 1=1level
     */
    public function defineDBQuery()
    {
        $objModel = $this->objModel;
        $objModel->select(array(
            TVATClasses::FIELD_NAME, 
            TVATClasses::FIELD_DESCRIPTION, 
            TVATClasses::FIELD_ISDEFAULT, 
            TSysModel::FIELD_CHECKOUTEXPIRES,
            TSysModel::FIELD_CHECKOUTSOURCE,
            TSysModel::FIELD_LOCKED,
            TSysModel::FIELD_LOCKEDSOURCE,
            TSysModel::FIELD_POSITION
                                    ));

      
        //===show what?

        // $arrTableColumnsShow = array(
        $this->arrTableColumnsShow = array(
            array('', TVATClasses::FIELD_NAME, transm(CMS_CURRENTMODULE, 'list_vatclasses_column_'.TVATClasses::FIELD_NAME, 'Name')),
            array('', TVATClasses::FIELD_DESCRIPTION, transm(CMS_CURRENTMODULE, 'list_vatclasses_column_'.TVATClasses::FIELD_DESCRIPTION, 'Description')),
            array('', TVATClasses::FIELD_ISDEFAULT, transm(CMS_CURRENTMODULE, 'list_vatclasses_column_'.TVATClasses::FIELD_ISDEFAULT, 'Default')),
            array('', TSysModel::FIELD_POSITION, transm(CMS_CURRENTMODULE, 'list_vatclasses_column_'.TSysModel::FIELD_POSITION, 'Order'))
                );
        
        //defining database filters
        $objFilters = $this->objDBFilters;

        //name
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField('', TVATClasses::FIELD_NAME);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TVATClasses::FIELD_NAME, 'Name'));
        $objFilters->addFilter($objFilter);

        //description
        $objFilter = new DRDBFilter();
        $objFilter->setStatus(DRDBFilter::STATUS_AVAILABLE); //showing in menu instead of directly visible
        $objFilter->setDisabled(true);//disabled by default when adding filter chip
        $objFilter->setType(DRDBFilter::TYPE_STRING);
        $objFilter->setDBTableField('', TVATClasses::FIELD_DESCRIPTION);
        $objFilter->setNameNice(transm(CMS_CURRENTMODULE, 'dbfilter_column_'.TVATClasses::FIELD_DESCRIPTION, 'Description'));
        $objFilters->addFilter($objFilter);
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
        return new TVATClasses();
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
        return Mod_POSWebshop::PERM_CAT_VATCLASSES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_vatclasses';
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
        return transm(CMS_CURRENTMODULE, 'tab_title_vatclasses', 'VAT classes');
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