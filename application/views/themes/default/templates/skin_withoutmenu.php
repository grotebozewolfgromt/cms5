<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $sHTMLTitle ?></title>
        <meta name="description" content="<?php echo $sHTMLMetaDescription ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" type="image/png" href="<?php echo APP_URL_CMS_IMAGES ?>/projecticons/icon128.png">
        <meta name="viewport" content="width=300, initial-scale=1">
        <link rel="stylesheet" href="<?php echo APP_URL_CMS ?>/vendor/cookieconsent/cookieconsent.css" media="print" onload="this.media='all'">
        <script defer src="<?php echo APP_URL_CMS ?>/vendor/cookieconsent/cookieconsent.js"></script>
        <script defer src="<?php echo APP_URL_CMS ?>/vendor/cookieconsent/cookieconsent-init.js"></script>  
        <script src="<?php echo APP_URL_CMS_JAVASCRIPTS.'/lib_global_notification.js'?>"></script>                  
        <?php /*
        <link href="<?php echo APP_URL_CMS_STYLESHEETS ?>/global_reset.css" rel="stylesheet" type="text/css">        
        <link href="<?php echo APP_URL_CMS_VIEWS_STYLESHEETS ?>/theme_global.css" rel="stylesheet" type="text/css">                       
        <link href="<?php echo APP_URL_CMS_VIEWS_STYLESHEETS ?>/theme_withoutmenu.css" rel="stylesheet" type="text/css">
        <link href="<?php echo APP_URL_CMS_VIEWS_STYLESHEETS ?>/theme_notification.css" rel="stylesheet" type="text/css">                       
        <script>
            <?php include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_global_header.js'; ?>
            <?php include_once APP_PATH_CMS_VIEWS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'cms_header.js'; ?>           
        </script> */
        ?>
        <style>
            <?php 
                include_once APP_PATH_CMS_STYLESHEETS.DIRECTORY_SEPARATOR.'global_reset.css';
                include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_global.css';
                include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_withoutmenu.css';
                include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_notification.css';
                include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_dialogs.css';                
                // include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_webcomponents.css';
            ?>
        </style>          
        <script>
            <?php 
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_inet.js';
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_string.js';
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_types.js';
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_file.js';
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_dom.js'; //---> uses PHP                        
                include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_darkmode.js'; //---> uses PHP                        
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_dragdrop.js'; //---> uses PHP            
                include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_notification.js';
                // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_tabs.js'; //---> uses PHP
                include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui.js'; //---> uses PHP                   
                include_once APP_PATH_CMS_VIEWS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'cms_header.js'; //---> uses PHP
            ?>
        </script>           
    </head>
    <body>    
        <?php include(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_notifications.php'); ?>        
        <?php //include(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_dialogs.php'); ?>
        <div id="progressbar">
            <div id="progressbar-indicator"></div>
        </div>        
        <div class="login-container">
            <?php echo $sHTMLContentMain; ?>
        </div>
        
        <!-- javascript that the page need to be fully loaded for -->
        <script>
            <?php 
                //always include (instead of linking with HTML), because it uses PHP
                include_once APP_PATH_CMS_JAVASCRIPTS.'/lib_footer.js'; 
                include_once APP_PATH_CMS_VIEWS_JAVASCRIPTS.'/cms_footer.js'; 
            ?>
        </script>         
    </body>

</html>