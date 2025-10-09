<?php
    use dr\modules\Mod_Sys_Websites\models\TSysWebsites;
    
    use dr\modules\Mod_Sys_Websites\controllers\TCRUDDetailSaveWebsites;    

            
    //session started in bootstrap
    include_once '../../bootstrap_cms_auth.php';
        
    
    //return url
    $sReturnURL = 'index.php';


    $objModel = new dr\modules\Mod_Sys_Websites\models\TSysWebsites(); 

    $objCRUD = new TCRUDDetailSaveWebsites($objModel, $sReturnURL);
    
    $objForm = $objCRUD->getForm();
    
    //====== page defaults
    
    // if ($objModel->getNew())
    // {          
    //     $sTitle = transm(CMS_CURRENTMODULE, 'detail_title_newrecord', CMS_CURRENTMODULE.': Add record to database');
    // }
    // else
    //     $sTitle = transcms('detail_title_edititem', 'Edit: [item]', 'item', $objModel->getDisplayRecordShort());
    if ($objModel->getNew())   
        $sTitle = transcms(TRANS_DETAILSAVE_CREATERECORD_TITLE, '[module]: Add record to database', 'module', CMS_CURRENTMODULE);
    else
        $sTitle = transcms(TRANS_DETAILSAVE_EDITRECORD_TITLE, 'Edit: [item]', 'item', $objModel->getDisplayRecordShort());

   
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;



    //============ RENDER de templates

    $sHTMLContentMain = renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsave.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());
    

?>