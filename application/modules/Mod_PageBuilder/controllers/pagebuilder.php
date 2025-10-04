<?php

namespace dr\modules\Mod_PageBuilder\controllers;

use dr\classes\controllers\TPageBuilderControllerAbstract;
use dr\modules\Mod_PageBuilder\models\TPageBuilderWebpages;
use dr\modules\Mod_PageBuilder\Mod_PageBuilder;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class pagebuilder extends TPageBuilderControllerAbstract
{


     /**
     * what is the category that the auth() function uses?
     */
    /*
    protected function getAuthorisationCategory() 
    {
        return Mod_PageBuilder::PERM_CAT_WEBPAGES;
    }
    */

    /**
     * returns a new TPageBuilderDocumentsAbstract model object
     *
     * @return TPageBuilderDocumentsAbstract
     */
    public function getNewModel()
    {
        return new TPageBuilderWebpages();
    }

    /**
     * create form elements
     */
    /*
    protected function populate()
    {

        parent::populate();
    }
    */



    /**
     * returns a div with detail controls for the document like: 
     * title, featured image, language, website etc
     * 
     * the returned html is a late-binded variable (= not cached)
     * 
     * returns something like this:
     * <div>
     *   title:<input type="edit">
     *   language:<select><option="nl">dutch</option></select>
     * </div>
     * 
     * @return string with <div> and controls inside the div
     */
    public function getDIVDocumentDetailsChild()
    {
        return '';
    }

    /**
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        return $_GET[ACTION_VARIABLE_RETURNURL];
    }

    /**
     * returns an array with all the designobjects to use in the pagebuilder
     * 
     * structure of array: array["custom-html-element-tagname"] = "Corresponding JS Class"
     * for example: array["do-paragraph"] = "DOParagraph"
     * @return array
     */
    public function getDesignObjects()    
    {
        $arrDO = array();
        $arrDO['do-paragraph'] = 'DOParagraph';
        $arrDO['do-h1'] = 'DOH1';
        $arrDO['do-h2'] = 'DOH2';
        $arrDO['do-h3'] = 'DOH3';
        $arrDO['do-h4'] = 'DOH4';
        $arrDO['do-h5'] = 'DOH5';
        $arrDO['do-h6'] = 'DOH6';
        $arrDO['do-2column'] = 'DO2column';
        $arrDO['do-3column'] = 'DO3column';
        $arrDO['do-grid'] = 'DOGrid';
        $arrDO['do-container'] = 'DOContainer';
        $arrDO['do-html'] = 'DOHTML';
        $arrDO['do-image'] = 'DOImage';
        return $arrDO;
    }


    /**
     * returns string with default name used as nameInternal
     * 
     * @return string
     */
    public function getDefaultNameInternal()
    {
        return 'webpage';
    }
    
    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd etc)
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return $this->objModel->getID().'_'.generatePrettyURLSafeURL($this->objModel->getHTMLTitle());
    }

    /**
     * Use these features in the pagebuilder?
     */
    public function getUseWebsite()
    {
        return true;
    }

    public function getUseURLSlug()
    {
        return true;
    }    

    public function getUseCanonicalURL()
    {
        return true;
    }

    public function getUse301RedirectURL()
    {
        return true;
    }

    public function getUseHTMLTitle()
    {
        return true;
    }    
    
    public function getUseHTMLDescription()
    {
        return true;
    }    
    
    public function getUseHTMLMetaDescription()
    {
        return true;
    }    
    
    public function getUsePassword()
    {
        return true;
    }    
    
    public function getUsePublishDate()
    {
        return true;
    }
    
    public function getUseVisibility()
    {
        return true;
    }    


    public function getAuthCreate()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CREATE);
    }    

    public function getAuthView()    
    {
        return (auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_VIEW) || auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_VIEW_OWN));
    }    

    public function getAuthChange()    
    {
        return (auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE) || auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_OWN));
    }    
    
    
    public function getAuthDelete()    
    {
        return (auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_DELETE) || auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_DELETE_OWN));
    }    
    
    public function getAuthChangeAuthor()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_AUTHOR);
    }    
    
    public function getAuthChangeWebsite()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_WEBSITE);
    }    
    
    public function getAuthChangeVisibility()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_VISIBILITY);
    }    
    
    public function getAuthChangePublishDate()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_PUBLISHDATE);
    }    
    
    public function getAuthChangePassword()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_PAGEPASSWORD);
    }    
    
    public function getAuthChangeStatus()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_STATUS);
    }    

    public function getAuthChangeURLSlug()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_URLSLUG);
    }    

    public function getAuthChangeCanonical()    
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_CANONICAL);
    }    

    public function getAuthChange301Redirect()        
    {
        return auth(CMS_CURRENTMODULE, Mod_PageBuilder::PERM_CAT_WEBPAGES, Mod_PageBuilder::PERM_OP_CHANGE_301REDIRECT);
    }    

}