<?php


namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveControllerAJAX;
use dr\classes\locale\TLocalisation;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\FormInputAbstract;
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
use dr\classes\dom\tag\webcomponents\DRInputCheckbox;
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
use dr\classes\dom\tag\webcomponents\DRInputNumber;
use dr\classes\dom\tag\webcomponents\DRInputText;
use dr\classes\dom\tag\webcomponents\DRInputUpload;
use dr\classes\dom\validator\ColorHex;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use  dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersAccounts;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\models\TSysUsersAbstract;
use dr\classes\models\TTreeModel;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TProductCategories;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_POSWebshop\models\TVATClassesCountries;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of detailssave_products
 *
 * @author drenirie
 */
class detailsave_productcategories extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtName = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objEdtURLSlug = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objImage = null;//dr\classes\dom\tag\webcomponents\DRInputUpload
    // private $objCbxParent = null;//dr\classes\dom\tag\webcomponents\DRInputCombobo --> bovenliggende node
    // private $objCbxInsertAfter = null;//dr\classes\dom\tag\webcomponents\DRInputCombobo --> sibling node
    private $objChkDefault = null; //dr\classes\dom\tag\webcomponents\DRCheckbox
    private $objChkFavorite = null; //dr\classes\dom\tag\webcomponents\DRCheckbox
   
    private $objParentCategories = null; //TProductCategories to select a new parent
    private $objSiblingNodes = null; //TProductCategories

    public function initModel()
    {         
    }


    /**
     * render shizzle to screen
     *
     * @param $arrVars extra variables to add to the render (you can call this method in one of the child classes)
     * @return void
     */
    // public function render($arrVars = array())
    // {
    //     $arrVars['objEdtName'] = $this->objEdtName;
    //     $arrVars['objEdtURLSlug'] = $this->objEdtURLSlug;
    //     $arrVars['objCbxParent'] = $this->objCbxParent;

    //     parent::render($arrVars);
    // }

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        global $objCurrentModule;
        
        //category name
        $this->objEdtName = new DRInputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');   
        // $this->objEdtName->setValue('testvalue1');   
        // $this->objEdtName->setPlaceholder('full product name');   
        $this->objEdtName->setOnchange("validateField(this, true)");
        $this->objEdtName->setOnkeyup("setDirtyRecord()");
        $this->objEdtName->setShowCharCounter(false);        
        $this->objEdtName->setRequired(true);  
        $this->objEdtName->setMinLength(5);         
        $this->objEdtName->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtName->addValidator($objValidator);   
        $this->objEdtName->setWhitelist(WHITELIST_SAFE);                 
        // $this->getFormGenerator()->add($this->objEdtName, '', transm(CMS_CURRENTMODULE, 'form_productcategories_field_name', 'Name'));
        $this->getFormGenerator()->addQuick(
            $this->objEdtName, 
            '', 
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_name_description', 'Name'),
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_name_infoicon', 'Name of the product category, for example: "car radios" or "vacuum cleaners"'),
        );
        

        //url slug
        $this->objEdtURLSlug = new DRInputText();
        $this->objEdtURLSlug->setNameAndID('edtURLSlug');
        $this->objEdtURLSlug->setClass('fullwidthtag');   
        $this->objEdtURLSlug->setRequired(true); 
        // $this->objEdtURLSlug->setValue('testvalue2');   
        // $this->objEdtURLSlug->setPlaceholder('URL slug');   
        $this->objEdtURLSlug->setOnchange("validateField(this, true)");
        $this->objEdtURLSlug->setOnkeyup("setDirtyRecord()");
        // $this->objEdtURLSlug->setShowCharCounter(true);
        $this->objEdtURLSlug->setMinLength(5);
        $this->objEdtURLSlug->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtURLSlug->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtURLSlug->addValidator($objValidator);  
        $this->objEdtURLSlug->setWhitelist(WHITELIST_URLSLUG);
        $this->getFormGenerator()->addQuick(
            $this->objEdtURLSlug, 
            '', 
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_urlslug_description', 'URL slug'),
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_urlslug_infoicon', 'A URL slug is the last part of a URL.<br>The URL slug identifies this category, so it must be unique.<br>The URL slug is only used for websites.<br>We aware that URL can not contain certain characters, like a space.'),
        );

        //upload image
        $this->objImage = new DRInputUpload();
        $this->objImage->setNameAndID('uplImage'); //NOTICE: when you have a multiple uploads <dr-input-upload> that the id will be automatically changed
        $this->objImage->setMultiple(true);//upload multiple files at once. NOTICE: the name will be automatically changed to the name with brackets for an array
        $this->objImage->setOnchange("setDirtyRecord()");        
        $this->objImage->setUploadDirPath(APP_PATH_UPLOADS.DIRECTORY_SEPARATOR.$objCurrentModule->getUploadDir().DIRECTORY_SEPARATOR.$this->getUploadDir());
        $this->objImage->setUploadDirURL(APP_URL_UPLOADS.'/'.$objCurrentModule->getUploadDir().'/'.$this->getUploadDir());
        $this->objImage->setAcceptArray(MIME_TYPES_IMAGES_GD);
        // $this->objImage->setAcceptArray(array(MIME_TYPE_CSV, MIME_TYPE_TEXT));
        $this->objImage->setStrictMimeTypeChecking(true);

        $this->objImage->setResizeImages(true);
        // $this->objImage->setMaxUploadSize(50000);
        if ($this->objImage->excuteURLParams()) //must be done AFTER the settings like uploaddir
            $this->stopHandlingURLParams(); //==== IMPORTANT!!
        $this->getFormGenerator()->addQuick(
            $this->objImage, 
            '', 
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_image_description', 'Image'),
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_image_infoicon', 'Add an image representing the products in the product category'),
        ); 
        // $this->getFormGenerator()->addArray(array($this->objImage, $this->objImage), '', 'omschrijving');        


        // //parent node
        // $this->objCbxParent = new DRInputCombobox();
        // $this->objCbxParent->setNameAndID('cbxParent');
        // $this->objCbxParent->setClass('fullwidthtag');  
        // $this->objCbxParent->setOnchange("setDirtyRecord()"); 
        // $this->getFormGenerator()->add($this->objCbxParent, '', transm(CMS_CURRENTMODULE, 'form_productcategories_field_parentnode', 'Parent node'));  

        // //insert after node
        // $this->objCbxInsertAfter = new DRInputCombobox();
        // $this->objCbxInsertAfter->setNameAndID('cbxParent');
        // $this->objCbxInsertAfter->setClass('fullwidthtag');  
        // $this->objCbxInsertAfter->setOnchange("setDirtyRecord()"); 
        // $this->getFormGenerator()->add($this->objCbxInsertAfter, '', transm(CMS_CURRENTMODULE, 'form_productcategories_field_insertafter', 'Insert after node'));  

            //default
        $this->objChkDefault = new DRInputCheckbox();
        $this->objChkDefault->setNameAndID('chkDefault');
        $this->objChkDefault->setOnchange("setDirtyRecord()");
        $this->objChkDefault->setOnkeyup("setDirtyRecord()");        
        $this->objChkDefault->setLabel(transm(CMS_CURRENTMODULE, 'form_productcategories_field_default', 'Is selected by default'));
        $this->getFormGenerator()->add($this->objChkDefault);  

            //favorite
        $this->objChkFavorite = new DRInputCheckbox();
        $this->objChkFavorite->setNameAndID('chkFavorite');
        $this->objChkFavorite->setOnchange("setDirtyRecord()");
        $this->objChkFavorite->setOnkeyup("setDirtyRecord()");        
        $this->objChkFavorite->setLabel(transm(CMS_CURRENTMODULE, 'form_productcategories_field_favorite', 'Is favorite'));
        $this->getFormGenerator()->add($this->objChkFavorite);  
    
    }


    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //name
        $this->getModel()->set(TProductCategories::FIELD_NAME, $this->objEdtName->getValueSubmitted());

        //slug
        $this->getModel()->set(TProductCategories::FIELD_URLSLUG, $this->objEdtURLSlug->getValueSubmitted());         
        
        //image upload
        $this->objImage->viewToModelImage($this->getModel());
        // $sTemp = $this->objImage->getValueSubmittedFileName();
  		// $sTemp = $this->objImage->getValueSubmittedFileNameMedium();
  		// $sTemp = $this->objImage->getValueSubmittedImageMaxHeight();
  		// $sTemp = $this->objImage->getValueSubmittedImageThumbnailWidth();
  
        //default
        $this->getModel()->set(TProductCategories::FIELD_ISDEFAULT, $this->objChkDefault->getValueSubmittedAsBool());             

        //favorite
        $this->getModel()->set(TProductCategories::FIELD_ISFAVORITE, $this->objChkFavorite->getValueSubmittedAsBool());             

    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtName->setValue($this->getModel()->get(TProductCategories::FIELD_NAME));

        //slug
        $this->objEdtURLSlug->setValue($this->getModel()->get(TProductCategories::FIELD_URLSLUG));

        //image upload
        $this->objImage->modelToViewImage($this->getModel());

        //default
        $this->objChkDefault->setChecked($this->getModel()->get(TProductCategories::FIELD_ISDEFAULT));   

        //favorite
        $this->objChkFavorite->setChecked($this->getModel()->get(TProductCategories::FIELD_ISFAVORITE));   
    }
    
   /**
     * is called after a record is loaded
     */
    public function onLoadPost()
    {
        // $this->objParentCategories->orderBy(TProductCategories::FIELD_POSITION);
        // $this->objParentCategories->where(TProductCategories::FIELD_ID, $this->getModel()->getID(), COMPARISON_OPERATOR_NOT_EQUAL_TO); //can't attach current item to itself
        // $this->objParentCategories->loadFromDB();

        // $this->objSiblingNodes->orderBy(TProductCategories::FIELD_POSITION);
        // $this->objSiblingNodes->where(TProductCategories::FIELD_PARENTID, $this->getModel()->getParentID()); //can't attach current item to itself
        // $this->objSiblingNodes->where(TProductCategories::FIELD_ID, $this->getModel()->getID(), COMPARISON_OPERATOR_NOT_EQUAL_TO); //can't attach current item to itself
        // $this->objSiblingNodes->loadFromDB();
    }

   /**
     * is called before a record is loaded
     */
    public function onLoadPre()
    {

    }

    
    /**
     * is called BEFORE a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @return array, empty array = no errors
     */
    public function onSavePre() { return array(); }    

    /**
     * is called AFTER a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return array, empty array = no errors
     */
    public function onSavePost($bWasSaveSuccesful)
    { 
        // if ($bWasSaveSuccesful)
        // {
        //     //determine new position
        //     $iIDInsertAfter = $this->objCbxInsertAfter->getValueSubmittedAsInt();
        //     if (!$this->getModel()->positionChangeDB($this->getModel()->getID(), $iIDInsertAfter))
        //         sendMessageError("position change failed");
        // }
        return array();
    }
    

    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
        // $this->objParentCategories = new TProductCategories();
        // $this->objSiblingNodes = new TProductCategories();
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
        return true;
    }    



   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TProductCategories(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsaveajax.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.CMS_CURRENTMODULE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_detailsave_products.php';
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
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        return 'list_productcategories';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_productcategoriess_new_title', 'Create new product category');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_productcategoriess_edit_title', 'Edit product category');   
    }


    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd etc)
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return 'productcategories';
    }    

   /**
     * is this user allowed to create this record?
     * 
     * CRUD: Crud
     */
    public function getAuthCreate()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES, TModuleAbstract::PERM_OP_DELETE);
    }



}
