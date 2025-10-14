<?php
namespace dr\modules\Support\controllers;

// use dr\classes\controllers\TContactFormController;


use dr\modules\Mod_ContactForm\controllers\TContactFormController;
// use dr\classes\controllers\TControllerAbstract;
// use dr\classes\patterns\TContactForm;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class supportcontactform extends TContactFormController
{
    public function __construct()
    {
        $this->setHyperlinksAllowed(false);//hyperlinks are allowed in a support form
        $this->setRecaptchaV3Use(true); //form doesn't exist yet  

        parent::__construct();//renders controller
    }



    public function populate()
    {
        
    }

    /**
     * at what percentage is spam considered spam?
     * 
     * @return int
     */
    public function getSpamScoreThreshold()
    {
        return 70;
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


}