<?php
/**
 * controller 
 */
      
    //session started in bootstrap
    include_once '../../bootstrap_admin_auth.php';
 
    

    //===fill tabsheets array (only if you want tabsheets)
    $arrTabsheets = $objCurrentModule->getTabsheets(); 
    
    
    //============ RENDER de templates
 
    
    
    $sTitle = transm(CMS_CURRENTMODULE, CMS_CURRENTMODULE);
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;
    
    
    $sHTMLContentMain = renderTemplate('tpl_fileuploadprogress.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());

    
?>