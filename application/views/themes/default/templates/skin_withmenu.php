<?php
    use dr\modules\Mod_Sys_Websites\models\TSysWebsites;

    includeJSWebcomponent('dr-dialog');
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $sHTMLTitle; ?></title>
	<meta name="description" content="<?php echo $sHTMLMetaDescription ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" type="image/png" href="<?php echo APP_URL_CMS_IMAGES ?>/projecticons/icon128.png">
    <meta name="viewport" content="width=800, initial-scale=1">     
    <link rel="stylesheet" href="<?php echo APP_URL_CMS ?>/vendor/cookieconsent/cookieconsent.css" media="print" onload="this.media='all'">
    <?php
        if (!APP_DEBUGMODE)
        {
            ?>
                <script defer src="<?php echo APP_URL_CMS ?>/vendor/cookieconsent/cookieconsent.js"></script>
                <script defer src="<?php echo APP_URL_CMS ?>/vendor/cookieconsent/cookieconsent-init.js"></script>                     
            <?php
        }
    ?>
    <style>
        <?php 
            include_once APP_PATH_CMS_STYLESHEETS.DIRECTORY_SEPARATOR.'global_reset.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_global.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_withmenu.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_notification.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_dialogs.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_drpopover.css';
            // include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_contextmenu_old.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_recorddetailsaveajax.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_recordlist.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_paginator.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_uploadfilemanager.css'; //--> is using php
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_webcomponents.css';
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-context-menu'.DIRECTORY_SEPARATOR.'style.css';
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'style.css';
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-progress-bar'.DIRECTORY_SEPARATOR.'style.css'; 
            // // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-spinner'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-date'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-time'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox'.DIRECTORY_SEPARATOR.'style.css';
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox-group'.DIRECTORY_SEPARATOR.'style.css'; 
            // // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-number'.DIRECTORY_SEPARATOR.'style.css'; 
            // // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'style.css'; 
            // // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-text'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-db-filters'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'style.css'; 
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-tabsheets'.DIRECTORY_SEPARATOR.'style.css'; 
            
            //include everything from $arrIncludeCSS
            global $arrIncludeCSS;
            foreach ($arrIncludeCSS as $sPath)
                include_once $sPath;
        ?>
    </style>          
    <script>
        <?php 
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_inet.js';
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_string.js';
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_types.js';
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_file.js';
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_dom.js'; //---> uses PHP                        
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_darkmode.js'; //---> uses PHP                        
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_dragdrop.js'; //---> uses PHP            
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_notification.js';
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_tabs.js'; //---> uses PHP
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui_contextmenu_org.js'; //---> uses PHP
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui.js'; //---> uses PHP
        
            //zebraprint
            if (APP_CMS_PRINTING_ZPL_ENABLE)   
            {      
                include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'zebraprint'.DIRECTORY_SEPARATOR.'lib_zebraprint.js'; //---> uses PHP            
                include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'zebraprint'.DIRECTORY_SEPARATOR.'ZPL.js'; //---> uses PHP            
                include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'zebraprint'.DIRECTORY_SEPARATOR.'ZPLTest.js'; //---> uses PHP            
            }
            include_once APP_PATH_CMS_VIEWS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'cms_header.js'; //---> uses PHP
            include_once APP_PATH_CMS_VIEWS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'cms_dialogs.js'; //---> uses PHP

            //include everything from $arrIncludeJS
            global $arrIncludeJS;
            foreach ($arrIncludeJS as $sPath)
                include_once $sPath;
        ?>
    </script>            
	<meta name="viewport" content="initial-scale=1.0, width=device-width">
	<meta charset="UTF-8">	
</head>
<body>
<?php 
    include_once(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_notifications.php');
    //include_once(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_dialogs.php'); 

    // CMS_UPLOADFILEMANAGER->renderLayout();
?>
<div id="progressbar">
    <div id="progressbar-indicator"></div>
</div>
<div id="page">

    <div class="hamburgermenu-container" onclick="toggleHamburgerMenu();">
        <!-- we need the canvas to be the entire space, otherwise padding and margin results in cutoff on the bottomside of the page -->
        <div class="hamburgermenu">
            <!-- filled with javascript later (menu on left is copied with javascript) -->
        </div>
    </div>

    <!-- <a href="#" onclick="event.preventDefault(); toggleHamburgerMenu();"><img src="<?php echo APP_URL_CMS_IMAGES; ?>/icon-menu.png" alt="menu"></a> -->

 
	<?php include(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_header.php'); ?>
	
	

    <div class="leftcolumn">
                    
    <?php
    
        if (auth(AUTH_MODULE_CMS, AUTH_CATEGORY_SYSSITES, AUTH_OPERATION_SYSSITES_VISIBILITY) && APP_CMS_SHOWWEBSITESINNAVIGATION)
        {
            $bAllowedSiteChange = true;
            $bAllowedSiteChange = auth(AUTH_MODULE_CMS, AUTH_CATEGORY_SYSSITES, AUTH_OPERATION_SYSSITES_SWITCH);
            $objWebsites = new TSysWebsites();
            $objWebsites->loadFromDB();

            if ($bAllowedSiteChange)
            {
                $objSelect = null;
                $objSelect = $objWebsites->generateHTMLSelect(APP_WEBSITEID_SELECTEDINCMS);
                $objSelect->setNameAndID('selChangeWebsite');
                $objSelect->setOnchange('handleChangeWebsite();');
                $objSelect->display();
                ?>
                <?php 
            }
            else
            {
                $objWebsites->resetRecordPointer();
                while($objWebsites->next())
                {
                    if ($objWebsites->getID() == APP_WEBSITEID_SELECTEDINCMS)  
                        echo $objWebsites->getWebsiteName();
                }
            }
        }
    ?>   
        <?php /*
        <ul class="leftcolumn_modules">
        <?php

            //display menu with tabsheets from modules
            $arrKeys = array_keys($arrCats);
            foreach($arrKeys as $sCatName)
            {
                $arrMods = $arrCats[$sCatName];
                
                echo '<li>'.$sCatName;
                echo '<ul>';
                
                foreach ($arrMods as $iIndexMod)
                {
                    $objSysModulesDB->setRecordPointerToIndex($iIndexMod);

                    // $sIconPath = APP_URL_CMS_IMAGES.'/icon-module16x16.png';  //default                              
                    // if (is_file(getPathModuleImages($objSysModulesDB->getNameInternal()).DIRECTORY_SEPARATOR.'icon-module16x16.png'))
                    //     $sIconPath = getURLModuleImages($objSysModulesDB->getNameInternal()).'/icon-module16x16.png';    


                    $sHTMLSelectedMod = '';                                    
                    if ($objSysModulesDB->getNameInternal() == APP_ADMIN_CURRENTMODULE)
                        $sHTMLSelectedMod = ' class="selected"';
                    
                    //only display if module folder exists
                    if(file_exists(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$objSysModulesDB->getNameInternal()))
                    {                                        
                        if (auth($objSysModulesDB->getNameInternal(), AUTH_CATEGORY_MODULEACCESS, AUTH_OPERATION_MODULEACCESS))
                        {
                            echo '<li'.$sHTMLSelectedMod.'>';                                        
                            echo '<a href="'.getURLModule($objSysModulesDB->getNameInternal()).'/index.php">';
                            // echo '<img src="'.$sIconPath.'">';
                            echo $objSysModulesDB->getIconSVG();// getIconSVG
                            echo transm($objSysModulesDB->getNameInternal(), TRANS_MODULENAME_MENU, $objSysModulesDB->getNameDefault());
                            // echo $objSysModulesDB->getNameDefault();
                            echo '</a>';              

                            //tabsheets of current module
                            // if ($sMod == APP_ADMIN_CURRENTMODULE)
                            // {
                            //     $arrTempTabs = $objCurrentModule->getTabsheets();
                            //     $arrTabKeys = array();
                            //     $arrTabKeys = array_keys($arrTempTabs);

                            //     if (count ($arrTempTabs) > 1) //only display if more than one tabsheet (the first one is always the default hook into the cms: index.php of a module)
                            //     {
                            //         echo '<ul>';
                            //         foreach($arrTabKeys as $sTabKey)
                            //         {
                            //             echo '<li>';
                            //             echo '<a href="'.getURLModule($sMod).'/'.$sTabKey.'">';
                            //             echo transm($sMod, 'cmsmodulelist_modulename_tabsheet_'.$arrTempTabs[$sTabKey],$arrTempTabs[$sTabKey]);
                            //             echo '</a>';
                            //             echo '</li>';

                            //         }
                            //         echo '</ul>';
                            //     }
                            // }

                            echo '</li>';  
                        }
                    }
                }                                
                
                echo '</ul>';
                echo '</li>';
            }  
            unset($arrCats);
        ?>
        </ul>
        */ ?>

        <?php
            //============ CMS MENU INSERTED HERE ==============
            include_once(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_menu.php'); 
        ?>

    </div> <!-- end left column -->
            
    <div class="maincolumn">
        <div class="maincolumn-centerwrapper">
            <!-- <h1><?php echo $sTitle; ?></h1> -->
            <?php             
                if (isset($arrTabsheets))
                {
                    if ($arrTabsheets)
                    {
                        include_once APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_tabsheets.php'; 
                    }
                }
            ?>                
            <div class="maincolumn-contentwrapper">
                <?php echo $sHTMLContentMain; ?>
            </div>
        </div>
    </div> <!-- end maincolumn -->	
	
</div><!-- end page-->



<!-- javascript that the page need to be fully loaded for -->
<script>
    <?php 



        //=== always include (instead of linking with HTML), because it uses PHP

        //JS Webcomponents
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-components-lib.js';
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'dr-popover.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-progress-bar'.DIRECTORY_SEPARATOR.'dr-progress-bar.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-spinner'.DIRECTORY_SEPARATOR.'dr-icon-spinner.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-context-menu'.DIRECTORY_SEPARATOR.'dr-context-menu.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-date'.DIRECTORY_SEPARATOR.'dr-input-date.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-time'.DIRECTORY_SEPARATOR.'dr-input-time.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-datetime'.DIRECTORY_SEPARATOR.'dr-input-datetime.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox'.DIRECTORY_SEPARATOR.'dr-input-checkbox.js';
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox-group'.DIRECTORY_SEPARATOR.'dr-input-checkbox-group.js';
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-number'.DIRECTORY_SEPARATOR.'dr-input-number.js';
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'dr-input-combobox.js';
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-text'.DIRECTORY_SEPARATOR.'dr-input-text.js';
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-db-filters'.DIRECTORY_SEPARATOR.'dr-db-filters.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'dr-paginator.js'; 
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-tabsheets'.DIRECTORY_SEPARATOR.'dr-tabsheets.js'; 


        //include everything from $arrIncludeJSDOMEnd
        global $arrIncludeJSDOMEnd;
        foreach ($arrIncludeJSDOMEnd as $sPath)
        {
            include_once $sPath;
            // echo "\n";
        }        
    ?>

</script> 
</body>
</html>