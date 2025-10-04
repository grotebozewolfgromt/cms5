<?php

namespace dr\modules\Mod_PageBuilder\controllers;


use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsStatusses;
use dr\classes\models\TSysModel;
use dr\modules\Mod_PageBuilder\models\TPageBuilderWebpages;
use dr\modules\Mod_PageBuilder\Mod_PageBuilder;


// use dr\modules\Mod_Sys_Contacts\Mod_Sys_Contacts;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_statusses extends TCRUDListController
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
    public function execute()
    {
        //global CMS_CURRENTMODULE;
        // global $arrTabsheets;        


        $objModel = $this->objModel;
        $objModel->select(array(
            TPageBuilderDocumentsStatusses::FIELD_NAME, 
            TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT
                                    ));
        $this->executeDB(1);
      
        //===show what?
        $arrTableColumnsShow = array(
            array('', TPageBuilderDocumentsStatusses::FIELD_NAME, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderDocumentsStatusses::FIELD_NAME, 'Name')),
            array('', TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, 'Default')),
                );
        
             
        return get_defined_vars();    
    }


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modellist.php';
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
        return new TPageBuilderDocumentsStatusses();
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
        return Mod_PageBuilder::PERM_CAT_STATUSSES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_statusses';       
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
        return transm(CMS_CURRENTMODULE, 'tab_title_statusses', 'Web page statusses');
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