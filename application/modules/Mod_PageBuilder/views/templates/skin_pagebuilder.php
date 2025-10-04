<!DOCTYPE html>
<html>
<head>
	<title><?php echo $sHTMLTitle; ?></title>
	<meta name="description" content="<?php echo $sHTMLMetaDescription; ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" type="image/png" href="<?php echo APP_URL_CMS_IMAGES ?>/projecticons/icon128.png">
    <meta name="viewport" content="width=800, initial-scale=1">                     
    <style>
        <?php 
            include_once APP_PATH_CMS_STYLESHEETS.DIRECTORY_SEPARATOR.'global_reset.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_notification.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_dialogs.css';
            include_once APP_PATH_CMS_VIEWS_STYLESHEETS.DIRECTORY_SEPARATOR.'theme_uploadfilemanager.css';
            include_once getPathModuleCSS($sModule, true).'pagebuilder-global.css';
            include_once getPathModuleCSS($sModule, true).'pagebuilder-panels.css';
            include_once getPathModuleCSS($sModule, true).'pagebuilder-header.css';
            include_once getPathModuleCSS($sModule, true).'pagebuilder-designer.css';
            include_once getPathModuleCSS($sModule, true).'pagebuilder-tabs.css';
            include_once getPathModuleCSS($sModule, true).'pagebuilder-frontpage-theme.css';

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
            // // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'style.css'; 
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
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_ui.js'; //---> uses PHP
            include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'ajaxform.js'; //---> uses PHP
            include_once APP_PATH_CMS_VIEWS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'cms_dialogs.js'; //---> uses PHP
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DesignObject.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOGrid.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOText.js';         
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DO2Column.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DO3Column.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOContainer.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOH1.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOH2.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOH3.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOH4.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOH5.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOH6.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOParagraph.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOHTML.js'; 
            include_once getPathModuleJS($sModule, true).'designobjects'.DIRECTORY_SEPARATOR.'DOImage.js'; 
            include_once getPathModuleJS($sModule, true).'pagebuilder-dragdrop.js'; 
            include_once getPathModuleJS($sModule, true).'pagebuilder-global.js'; 
            include_once getPathModuleJS($sModule, true).'pagebuilder-panels.js'; 
            include_once getPathModuleJS($sModule, true).'pagebuilder-tabs.js'; 
            include_once getPathModuleJS($sModule, true).'pagebuilder-header.js'; 
            include_once getPathModuleJS($sModule, true).'pagebuilder-designer.js'; 
        ?>
    </script>        
	<meta name="viewport" content="initial-scale=1.0, width=device-width">
	<meta charset="UTF-8">	
</head>
<body>
    <?php include(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_notifications.php'); ?>
    <?php //include(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_dialogs.php'); ?>
    <?php 
        // CMS_UPLOADFILEMANAGER->renderLayout();
    ?>

    <!-- dialogs -->

        <!-- exit dialog -->
        <?php /* REPLACED BY CRUD AJAX CONTROLLER
        <dialog id="dlgExitPagebuilder">
            <div class="dialog-header">
                <div class="dialog-button-x" id="btnDialogExitpagebuilderX">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="dialog-title"><?php echo transm($sModule, 'pagebuilder_dialog_exit_title', 'Quit') ?></div>
            </div>
            <div class="dialog-body">
                <?php echo transm($sModule, 'pagebuilder_dialog_exit_body', 'There are unsaved changes.<br>Are you sure you want to exit the pagebuilder?') ?>
            </div>
            <div class="dialog-footer">
                <button id="btnDialogExitPagebuilderDoStay" autofocus class="default"><?php echo transm($sModule, 'pagebuilder_dialog_exit_button_stay', 'Stay') ?></button>
                <button id="btnDialogExitPagebuilderDoExitNoSave"><?php echo transm($sModule, 'pagebuilder_dialog_exit_button_exitnosave', 'Exit & DON\'T save') ?></button>
                <!-- <button id="btnDialogExitDoExitSave"><?php echo transm($sModule, 'pagebuilder_dialog_exit_button_exitsave', 'Exit & save') ?></button> -->
            </div>
        </dialog>
        */ ?>

        <!-- hyperlink dialog -->
        <dialog id="dlgHyperlink">
            <div class="dialog-header">
                <div class="dialog-button-x" id="btnDialogHyperlinkX">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="dialog-title"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_title', 'Create or edit hyperlink') ?></div>
            </div>
            <div class="dialog-body">
                <label for="edtHyperlinkDialogDescription"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_label_hyperlinkdescription', 'Description') ?></label>
                <input type="text" id="edtHyperlinkDialogDescription" name="edtHyperlinkDialogDescription">
                <label for="edtHyperlinkDialogURL"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_label_hyperlinktarget', 'Target location URL') ?></label>
                <input type="text" id="edtHyperlinkDialogURL" name="edtHyperlinkDialogURL">
                <input type="checkbox" id="chkHyperlinkDialogOpenTab" name="chkHyperlinkDialogOpenTab">
                <label for="chkHyperlinkDialogOpenTab"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_label_opennewtab', 'Open in new tab') ?></label><br>
                <input type="checkbox" id="chkHyperlinkDialogNoFollow" name="chkHyperlinkDialogNoFollow">
                <label for="chkHyperlinkDialogNoFollow"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_label_nofollow', 'Mark as: nofollow') ?></label><br>
                <input type="checkbox" id="chkHyperlinkDialogSponsored" name="chkHyperlinkDialogSponsored">
                <label for="chkHyperlinkDialogSponsored"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_label_sponsored', 'Mark as: sponsored link') ?></label>
            </div>
            <div class="dialog-footer">
                <button id="btnDialogHyperlinkApply" autofocus class="default"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_button_apply', 'Apply') ?></button>
                <button id="btnDialogHyperlinkCancel"><?php echo transm($sModule, 'pagebuilder_dialog_hyperlink_button_cancel', 'Cancel') ?></button>
            </div>
        </dialog>        

    <!-- END dialogs -->

    
    <!-- iconsheet: preloads icons to use in JS -->
        <div id="iconsheet">

            <!-- bold -->
            <svg id="icon-bold" class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <g id="Layer_2" data-name="Layer 2">
                <g id="invisible_box" data-name="invisible box">
                <rect width="48" height="48" fill="none"/>
                </g>
                <g id="Layer_6" data-name="Layer 6">
                <path d="M34.4,23.1A11,11,0,0,0,26,5H9V43H28A11,11,0,0,0,39,32,10.8,10.8,0,0,0,34.4,23.1ZM15,11H26a5,5,0,0,1,0,10H15ZM28,37H15V27H28a5,5,0,0,1,0,10Z"/>
                </g>
            </g>
            </svg>

            <!-- italic -->
            <svg id="icon-italic" class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <g id="Layer_2" data-name="Layer 2">
                    <g id="invisible_box" data-name="invisible box">
                    <rect width="48" height="48" fill="none"/>
                    </g>
                    <g id="Layer_6" data-name="Layer 6">
                    <polygon points="41 5 15 5 15 11 24.7 11 17.3 37 7 37 7 43 33 43 33 37 23.3 37 30.7 11 41 11 41 5 41 5"/>
                    </g>
                </g>
            </svg>                    

            <!-- strikethrough -->
            <svg id="icon-strikethrough" class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <g id="Layer_2" data-name="Layer 2">
                    <g id="invisible_box" data-name="invisible box">
                        <rect width="48" height="48" fill="none"/>
                    </g>
                    <g id="Layer_6" data-name="Layer 6">
                    <g>
                        <path d="M21.1,20c.1,0,.9-.1,0-.3C17.4,19,15,16.9,15,15s3.7-5,9-5,7.7,1.9,8.7,3.8A2.7,2.7,0,0,1,33,15l4.7-4.6C35.3,6.5,30,4,24,4,15.6,4,9,8.8,9,15a9,9,0,0,0,1.6,5Z"/>
                        <path d="M44,22H4v4H24c4.9,0,9,2.8,9,6s-4.1,6-9,6-8.2-2.2-8.9-5.1l-4.5,4.5C13.1,41.3,18.2,44,24,44c8.3,0,15-5.4,15-12a10.1,10.1,0,0,0-2-6h7Z"/>
                    </g>
                    </g>
                </g>
            </svg> 

            <!-- link: unlinked -->
            <svg id="icon-link-unlinked" class="changeiconcolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>

            <!-- menu -->
            <svg id="icon-menu" class="changeiconcolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
            </svg>


        </div>

    <!-- END: iconsheet -->


    <div id="progressbar">
        <div id="progressbar-indicator"></div>
    </div>
    <div class="header">
        <div class="headercolumn headercolumn-exit">
            <button id="btnExit" onmousedown="handleExitPagebuilder()">
                <dr-icon-spinner>
                    <svg class="iconchangecolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>              
                </dr-icon-spinner>    
                <div class="buttontext"><?php echo transm($sModule, 'pagebuilder_button_exit', 'Exit'); ?></div>        
            </button>
        </div>
        <div class="headercolumn headercolumn-darklightmode">
            <!--everything in this <DIV> will be hidden on mobile and tablet -->
            <button id="btnDarkMode" onclick="toggleDarkLightMode();">
                <svg class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <g id="Layer_2" data-name="Layer 2">
                        <g id="invisible_box" data-name="invisible box">
                        <rect width="48" height="48" fill="none"/>
                        </g>
                        <g id="Q3_icons" data-name="Q3 icons">
                        <path d="M18.2,11.2a22.6,22.6,0,0,0-1,6.9c.3,8.8,6.7,16.8,15,19.7A14.5,14.5,0,0,1,26.3,39H24.6a15,15,0,0,1-6.4-27.7m6-6.2h-.1a19.2,19.2,0,0,0-17,21.1A19.2,19.2,0,0,0,24.2,42.9h2.1a19.2,19.2,0,0,0,14.4-6.4A.9.9,0,0,0,40,35H38.1c-8.8-.5-16.6-8.4-16.9-17.1A17.4,17.4,0,0,1,25,6.6,1,1,0,0,0,24.2,5Z"/>
                        </g>
                    </g>
                </svg>
            </button>        
        </div>
        <div class="headercolumn headercolumn-panels">
            <button id="btnNewDesktop" onmousedown="toggleNewPanelDesktop();">
                <svg class="iconchangefill" fill="#000000" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><path d="M28,4H4A2,2,0,0,0,2,6V26a2,2,0,0,0,2,2H28a2,2,0,0,0,2-2V6A2,2,0,0,0,28,4Zm0,22H12V6H28Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>
            </button><!-- [prevent whitespace]
        --><button id="btnNewMobile" onclick="toggleNewPanelMobile();">          
                <svg class="iconchangefill" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" >
                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                </svg>
            </button><!-- [prevent whitespace]
        --><button id="btnDetailsDesktop" onmousedown="toggleDetailsPanelDesktop();">
                <svg class="iconchangefill" fill="#000000" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><path d="M28,4H4A2,2,0,0,0,2,6V26a2,2,0,0,0,2,2H28a2,2,0,0,0,2-2V6A2,2,0,0,0,28,4ZM4,6H20V26H4Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>
            </button><!-- [prevent whitespace]
        --><button id="btnDetailsMobile" onmousedown="toggleDetailsPanelMobile();">       
                <svg class="iconchangefill" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                </svg>              
            </button>                              
        </div>         
        <div class="headercolumn headercolumn-viewports">
            <button id="btnDesktopViewport" onclick="setDesktopViewport()">
                <svg class="iconchangefill" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd" />
                </svg>                         
            </button><!-- [prevent whitespace]
        --><button id="btnTabletViewport" onmousedown="setTabletViewport()">
                <svg class="iconchangecolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-15a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 4.5v15a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </button><!-- [prevent whitespace]
        --><button id="btnMobileViewport" onmousedown="setMobileViewport()">
                <svg class="iconchangefill" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M10.5 18.75a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z" />
                    <path fill-rule="evenodd" d="M8.625.75A3.375 3.375 0 0 0 5.25 4.125v15.75a3.375 3.375 0 0 0 3.375 3.375h6.75a3.375 3.375 0 0 0 3.375-3.375V4.125A3.375 3.375 0 0 0 15.375.75h-6.75ZM7.5 4.125C7.5 3.504 8.004 3 8.625 3H9.75v.375c0 .621.504 1.125 1.125 1.125h2.25c.621 0 1.125-.504 1.125-1.125V3h1.125c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-6.75A1.125 1.125 0 0 1 7.5 19.875V4.125Z" clip-rule="evenodd" />
                </svg>                          
            </button> 
        </div>
        <div class="headercolumn headercolumn-cursormode">  
            <button id="btnCursorModeEdit" onmousedown="setCursorModeTextEditor()">          
                <svg class="iconchangefill" width="100%" height="100%" viewBox="0 0 1000 1000" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                    <g transform="matrix(1.53068,0,0,1.53068,-522.179,-453.522)">
                        <g transform="matrix(745.657,0,0,745.657,747.699,816.273)">
                        </g>
                        <text x="333px" y="816.273px" style="font-family:'ArialMT', 'Arial';font-size:745.657px;">a</text>
                    </g>
                    <g transform="matrix(6.62564,0,0,6.62564,680.754,92.9537)">
                        <path d="M3.28,64.8C1.47,64.8 0,63.33 0,61.52C0,59.71 1.47,58.24 3.28,58.24L18.52,58.24L18.52,21.51C18.05,16.98 16.66,13.62 14.54,11.25C12.24,8.67 8.97,7.16 5.01,6.51C3.23,6.22 2.02,4.54 2.31,2.75C2.6,0.97 4.28,-0.24 6.07,0.05C11.48,0.94 16.05,3.12 19.41,6.89C20.27,7.85 21.04,8.91 21.72,10.06C22.58,8.57 23.6,7.26 24.77,6.1C28.28,2.62 32.98,0.72 38.59,0.02C40.39,-0.2 42.02,1.08 42.24,2.88C42.46,4.68 41.18,6.31 39.38,6.53C35.18,7.05 31.77,8.37 29.38,10.74C27.01,13.09 25.51,16.59 25.08,21.52L25.08,58.23L40.56,58.23C42.37,58.23 43.84,59.7 43.84,61.51C43.84,63.32 42.37,64.79 40.56,64.79L25.08,64.79L25.08,101.35C25.51,106.28 27.01,109.79 29.38,112.13C31.77,114.49 35.18,115.82 39.38,116.34C41.18,116.56 42.46,118.2 42.24,119.99C42.02,121.79 40.39,123.07 38.59,122.85C32.98,122.15 28.28,120.25 24.77,116.77C23.6,115.61 22.58,114.3 21.72,112.81C21.04,113.97 20.27,115.02 19.41,115.98C16.04,119.75 11.47,121.93 6.07,122.82C4.29,123.11 2.61,121.9 2.31,120.12C2.02,118.34 3.23,116.66 5.01,116.36C8.97,115.71 12.24,114.2 14.54,111.62C16.66,109.25 18.05,105.88 18.52,101.36L18.52,64.8L3.28,64.8Z" style="fill-rule:nonzero;"/>
                    </g>
                </svg>
            </button><!-- [prevent whitespace]
        --><button id="btnCursorModeSelection" onmousedown="setCursorModeSelection();">          
                <svg class="iconchangefill" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 121.65 122.88" style="enable-background:new 0 0 121.65 122.88" xml:space="preserve">
                    <g>
                        <path d="M1.96,0.28L1.91,0.3L1.88,0.32c-0.07,0.03-0.13,0.06-0.19,0.1L1.67,0.43L1.61,0.46L1.58,0.48C1.55,0.5,1.52,0.52,1.49,0.54 l0,0L1.45,0.57L1.44,0.57L1.41,0.59L1.38,0.61L1.34,0.64L1.29,0.68l-0.01,0C0.73,1.11,0.33,1.69,0.14,2.36 C0.03,2.7-0.01,3.07,0,3.43v2.05c0.02,2.55,2.78,4.12,4.98,2.8c0.67-0.41,1.15-1.02,1.4-1.73h3.46c2.55-0.02,4.12-2.78,2.8-4.98 C12.03,0.59,11,0.01,9.84,0H3.42C2.94-0.02,2.44,0.07,1.96,0.28L1.96,0.28z M101.11,122.86c0.09,0.02,0.19,0.02,0.29,0 c0.03-0.02,0.07-0.04,0.1-0.05l9.76-5.63c0.09-0.06,0.15-0.16,0.18-0.26c0.02-0.08,0.02-0.16-0.01-0.21l-10.7-18.65l0,0 c-0.09-0.16-0.15-0.33-0.19-0.51c-0.19-0.94,0.41-1.85,1.35-2.04l15.7-3.25c0.02-0.01,0.04-0.01,0.06-0.01 c1.35-0.28,2.5-0.76,3.26-1.36c0.37-0.29,0.62-0.59,0.72-0.87c0.06-0.18,0.03-0.39-0.09-0.63c-0.22-0.41-0.66-0.87-1.39-1.36 L66.79,51.49l4.95,64.46c0.07,0.88,0.24,1.49,0.48,1.88c0.14,0.23,0.31,0.35,0.5,0.39c0.29,0.06,0.67-0.01,1.11-0.18 c0.9-0.36,1.88-1.12,2.81-2.15l10.71-12.02l0,0c0.12-0.13,0.26-0.25,0.43-0.35c0.83-0.48,1.89-0.2,2.37,0.63l10.8,18.59 C100.97,122.8,101.03,122.84,101.11,122.86L101.11,122.86L101.11,122.86z M1.61,0.46C1.57,0.49,1.53,0.51,1.49,0.54L1.61,0.46 L1.61,0.46z M6.56,18.59c-0.02-2.55-2.78-4.12-4.98-2.8C0.59,16.4,0.01,17.43,0,18.59v6.55c0.02,2.55,2.78,4.12,4.98,2.8 c0.99-0.61,1.57-1.64,1.58-2.8V18.59L6.56,18.59z M6.56,38.26c-0.02-2.55-2.78-4.12-4.98-2.8C0.59,36.06,0.01,37.1,0,38.26v6.55 c0.02,2.55,2.78,4.12,4.98,2.8c0.99-0.61,1.57-1.64,1.58-2.8V38.26L6.56,38.26z M6.56,57.92c-0.02-2.55-2.78-4.12-4.98-2.8 c-0.99,0.61-1.57,1.64-1.58,2.8v6.56c0.02,2.55,2.78,4.12,4.98,2.8c0.99-0.61,1.57-1.64,1.58-2.8V57.92L6.56,57.92z M6.56,77.59 c-0.02-2.55-2.78-4.12-4.98-2.8c-0.99,0.61-1.57,1.64-1.58,2.8v6.55c0.02,2.55,2.78,4.12,4.98,2.8c0.99-0.61,1.57-1.64,1.58-2.8 V77.59L6.56,77.59z M6.56,97.25c-0.02-2.55-2.78-4.12-4.98-2.8c-0.99,0.61-1.57,1.64-1.58,2.8v6.56c0.02,2.55,2.78,4.12,4.98,2.8 c0.99-0.61,1.57-1.64,1.58-2.8V97.25L6.56,97.25z M13.13,103.79c-2.55,0.02-4.12,2.78-2.8,4.98c0.61,0.99,1.64,1.57,2.8,1.58h6.55 c2.55-0.02,4.12-2.78,2.8-4.98c-0.61-0.99-1.64-1.57-2.8-1.58H13.13L13.13,103.79z M32.79,103.79c-2.55,0.02-4.12,2.78-2.8,4.98 c0.61,0.99,1.64,1.57,2.8,1.58h6.56c2.55-0.02,4.12-2.78,2.8-4.98c-0.61-0.99-1.64-1.57-2.8-1.58H32.79L32.79,103.79z M52.46,103.79c-2.55,0.02-4.12,2.78-2.8,4.98c0.61,0.99,1.64,1.57,2.8,1.58h6.56c2.55-0.02,4.12-2.78,2.8-4.98 c-0.61-0.99-1.64-1.57-2.8-1.58H52.46L52.46,103.79z M103.79,63.36c0.02,2.55,2.78,4.12,4.98,2.8c0.99-0.61,1.57-1.64,1.58-2.8 v-6.56c-0.02-2.55-2.78-4.12-4.98-2.8c-0.99,0.61-1.57,1.64-1.58,2.8V63.36L103.79,63.36z M103.79,43.7 c0.02,2.55,2.78,4.12,4.98,2.8c0.99-0.61,1.57-1.64,1.58-2.8v-6.56c-0.02-2.55-2.78-4.12-4.98-2.8c-0.99,0.61-1.57,1.64-1.58,2.8 V43.7L103.79,43.7z M103.79,24.03c0.02,2.55,2.78,4.12,4.98,2.8c0.99-0.61,1.57-1.64,1.58-2.8v-6.55c-0.02-2.55-2.78-4.12-4.98-2.8 c-0.99,0.61-1.57,1.64-1.58,2.8V24.03L103.79,24.03z M104.63,6.56c0.99,1.1,2.69,1.49,4.14,0.61c0.99-0.61,1.57-1.64,1.58-2.8V3.42 c0.03-0.61-0.12-1.25-0.47-1.84c-0.61-0.99-1.64-1.57-2.8-1.58h-5.47c-2.55,0.02-4.12,2.78-2.8,4.98c0.61,0.99,1.64,1.57,2.8,1.58 H104.63L104.63,6.56z M88.5,6.56c2.55-0.02,4.12-2.78,2.8-4.98C90.69,0.59,89.66,0.01,88.5,0h-6.55c-2.55,0.02-4.12,2.78-2.8,4.98 c0.61,0.99,1.64,1.57,2.8,1.58H88.5L88.5,6.56z M68.83,6.56c2.55-0.02,4.12-2.78,2.8-4.98c-0.61-0.99-1.64-1.57-2.8-1.58h-6.56 c-2.55,0.02-4.12,2.78-2.8,4.98c0.61,0.99,1.64,1.57,2.8,1.58H68.83L68.83,6.56z M49.17,6.56c2.55-0.02,4.12-2.78,2.8-4.98 c-0.61-0.99-1.64-1.57-2.8-1.58h-6.56c-2.55,0.02-4.12,2.78-2.8,4.98c0.61,0.99,1.64,1.57,2.8,1.58H49.17L49.17,6.56z M29.5,6.56 c2.55-0.02,4.12-2.78,2.8-4.98C31.7,0.59,30.66,0.01,29.5,0h-6.55c-2.55,0.02-4.12,2.78-2.8,4.98c0.61,0.99,1.64,1.57,2.8,1.58 H29.5L29.5,6.56z"/>
                    </g>
                </svg>
            </button>                
        </div>
        <div class="headercolumn headercolumn-title">
            <!--everything in this <DIV> will be hidden on mobile and tablet -->          
            <label for="edtNameInternal" id="lblTitleHeader">23123</label>
        </div>        
        <div class="headercolumn headercolumn-save">
            <button id="btnSave" onmousedown="handleSavePagebuilder()">
                <svg id="svgSaveIcon" class="iconchangecolor" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>                
                <svg id="svgSaveIconSpinner" class="iconchangefill spinner" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
                    <path d="M41.9 23.9c-.3-6.1-4-11.8-9.5-14.4-6-2.7-13.3-1.6-18.3 2.6-4.8 4-7 10.5-5.6 16.6 1.3 6 6 10.9 11.9 12.5 7.1 2 13.6-1.4 17.6-7.2-3.6 4.8-9.1 8-15.2 6.9-6.1-1.1-11.1-5.7-12.5-11.7-1.5-6.4 1.5-13.1 7.2-16.4 5.9-3.4 14.2-2.1 18.1 3.7 1 1.4 1.7 3.1 2 4.8.3 1.4.2 2.9.4 4.3.2 1.3 1.3 3 2.8 2.1 1.3-.8 1.2-2.5 1.1-3.8 0-.4.1.7 0 0z"/>
                </svg>
                <div class="buttontext"><?php echo transm($sModule, 'pagebuilder_button_save', 'Save'); ?></div>        
            </button>                       
        </div>
        <div class="headercolumn headercolumn-preview">
            <?php 
                if ($sURLPreview != '') 
                {
                    ?>
                    <button id="btnPreview" onmousedown="handlePreview()">
                        <svg class="iconchangecolor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path fill-rule="evenodd" d="M15.75 2.25H21a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V4.81L8.03 17.03a.75.75 0 0 1-1.06-1.06L19.19 3.75h-3.44a.75.75 0 0 1 0-1.5Zm-10.5 4.5a1.5 1.5 0 0 0-1.5 1.5v10.5a1.5 1.5 0 0 0 1.5 1.5h10.5a1.5 1.5 0 0 0 1.5-1.5V10.5a.75.75 0 0 1 1.5 0v8.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V8.25a3 3 0 0 1 3-3h8.25a.75.75 0 0 1 0 1.5H5.25Z" clip-rule="evenodd" />
                        </svg>   
                        <div class="buttontext"><?php echo transm($sModule, 'pagebuilder_button_preview', 'Preview'); ?></div>                          
                    </button>                        
                    <?php
                }
            ?>
        </div>
    </div>
    <div class="panel-new-mobile">
        <div class="panel-newmobile-types-search">
            <select id="selNewMobileCategory"></select>
            <input type="search" id="edtNewMobileSearch" placeholder="<?php echo transm($sModule, 'pagebuilder_newpanel_inputsearch_placeholder', 'Search'); ?>" onchange="searchForDesignObjects(this)">
        </div>   
        <div class="tabscontent" id="tabscontentmobile">  
        </div>
    </div>
    <div class="panel-details-mobile">
        <div class="tabsheads">
            <div class="tabhead selected"   id="tab-head-details-document-mobile"><?php echo transm($sModule, 'pagebuilder_tabhead_document_text', 'document'); ?></div>
            <div class="tabhead"            id="tab-head-details-structure-mobile"><?php echo transm($sModule, 'pagebuilder_tabhead_structure_text', 'structure'); ?></div>
            <div class="tabhead"            id="tab-head-details-element-mobile"><?php echo transm($sModule, 'pagebuilder_tabhead_element_text', 'element'); ?></div>
        </div>
        <div class="tabscontent">
            <div id="tab-content-details-document-mobile">

            </div>
            <div id="tab-content-details-structure-mobile">
                details structre
            </div>
            <div id="tab-content-details-element-mobile">
                detauls element
            </div>
        </div>
    </div>    
    <div class="page">
        <div class="pagecolumn-panel-new-desktop panelNewDesktopVisible">
            <div class="panel-newdesktop-types-search">
                <select id="selNewDesktopCategory"></select>
                <input type="search" id="edtNewDesktopSearch" placeholder="<?php echo transm($sModule, 'pagebuilder_newpanel_inputsearch_placeholder', 'Search'); ?>" onchange="searchForDesignObjects(this)">
            </div>
            <div class="tabscontent" id="tabscontentdesktop">
                <!-- hakkieflakkie desktop -->
                <!--
                <div id="tab-content-new-structures-desktop">structures
                 
           
                </div>
                <div id="tab-content-new-blocks-desktop" class="designobject-grid-desktop">blocks   

                </div>
                <div id="tab-content-new-elements-desktop" class="designobject-grid-desktop">                    
                    <div class="designobject-grid-sectiontitle"><?php echo transm($sModule, 'pagebuilder_designobject-grid_sectiontitle_headings', 'headings'); ?></div>
                    <div class="designobject-grid">
                        <div class="designobject-createtab designObjectInspectorH1 draggable" data-searchlabelscsv="h1,<?php echo transm($sModule, 'pagebuilder_designobject_h1_seachslabelscsv', 'header1, header 1, heading1, heading 1'); ?>" onmousedown="designObjectClickInInspector('designObjectInspectorH1')" draggable="true">
                            <svg class="iconchangefill" viewBox="-5 -7 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-header-1"><path d='M2 4h4V1a1 1 0 1 1 2 0v8a1 1 0 1 1-2 0V6H2v3a1 1 0 1 1-2 0V1a1 1 0 1 1 2 0v3zm9.52.779H10V3h3.36v7h-1.84V4.779z' /></svg>
                            <div class="designobject-createtab-text">
                                <?php echo transm($sModule, 'pagebuilder_designobject_h1_text', 'heading 1'); ?>
                            </div>                            
                        </div>               
                        <div class="designobject-createtab designObjectInspectorH2 draggable" data-searchlabelscsv="h2,<?php echo transm($sModule, 'pagebuilder_designobject_h2_seachslabelscsv', 'header2, header 2, heading2, heading 2'); ?>" onmousedown="designObjectClickInInspector('designObjectInspectorH2')" draggable="true">
                            <svg class="iconchangefill" viewBox="-4.5 -7 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-header-2"><path d='M2 4h4V1a1 1 0 1 1 2 0v8a1 1 0 1 1-2 0V6H2v3a1 1 0 1 1-2 0V1a1 1 0 1 1 2 0v3zm12.88 4.352V10H10V8.986l.1-.246 1.785-1.913c.43-.435.793-.77.923-1.011.124-.23.182-.427.182-.587 0-.14-.04-.242-.127-.327a.469.469 0 0 0-.351-.127.443.443 0 0 0-.355.158c-.105.117-.165.288-.173.525l-.012.338h-1.824l.016-.366c.034-.735.272-1.33.718-1.77.446-.44 1.02-.66 1.703-.66.424 0 .805.091 1.14.275.336.186.606.455.806.8.198.343.3.7.3 1.063 0 .416-.23.849-.456 1.307-.222.45-.534.876-1.064 1.555l-.116.123-.254.229h1.938z' /></svg>
                            <div class="designobject-createtab-text">
                            <?php echo transm($sModule, 'pagebuilder_designobject_h2_text', 'heading 2'); ?>
                            </div>                            
                        </div>  
                        <div class="designobject-createtab draggable designObjectInspectorH3" data-searchlabelscsv="h3,<?php echo transm($sModule, 'pagebuilder_designobject_h3_seachslabelscsv', 'header3, header 3, heading3, heading 3'); ?>" onmousedown="designObjectClickInInspector('designObjectInspectorH3')" draggable="true"> 
                            <svg class="iconchangefill" viewBox="-4.5 -6.5 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-header-3"><path d='M2 4h4V1a1 1 0 1 1 2 0v8a1 1 0 1 1-2 0V6H2v3a1 1 0 1 1-2 0V1a1 1 0 1 1 2 0v3zm12.453 2.513l.043.055c.254.334.38.728.38 1.172 0 .637-.239 1.187-.707 1.628-.466.439-1.06.658-1.763.658-.671 0-1.235-.209-1.671-.627-.436-.418-.673-.983-.713-1.676L10 7.353h1.803l.047.295c.038.238.112.397.215.49.1.091.23.137.402.137a.566.566 0 0 0 .422-.159.5.5 0 0 0 .158-.38c0-.163-.067-.295-.224-.419-.17-.134-.438-.21-.815-.215l-.345-.004v-1.17l.345-.004c.377-.004.646-.08.815-.215.157-.124.224-.255.224-.418a.5.5 0 0 0-.158-.381.566.566 0 0 0-.422-.159.568.568 0 0 0-.402.138c-.103.092-.177.251-.215.489l-.047.295H10l.022-.37c.04-.693.277-1.258.713-1.675.436-.419 1-.628 1.67-.628.704 0 1.298.22 1.764.658.468.441.708.991.708 1.629a1.892 1.892 0 0 1-.424 1.226z' /></svg>
                            <div class="designobject-createtab-text">
                                <?php echo transm($sModule, 'pagebuilder_designobject_h3_text', 'heading 3'); ?>
                            </div>                            
                        </div>                         
                        <div class="designobject-createtab draggable designObjectInspectorH4" data-searchlabelscsv="h4,<?php echo transm($sModule, 'pagebuilder_designobject_h4_seachslabelscsv', 'header4, header 4, heading4, heading 4'); ?>" onmousedown="designObjectClickInInspector('designObjectInspectorH4')" draggable="true">
                            <svg class="iconchangefill" viewBox="-4.5 -7 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-header-4"><path d='M2 4h4V1a1 1 0 1 1 2 0v8a1 1 0 1 1-2 0V6H2v3a1 1 0 1 1-2 0V1a1 1 0 1 1 2 0v3zm10.636 4.74H10V7.302l.06-.198 2.714-4.11h1.687v3.952h.538V8.74h-.538V10h-1.825V8.74zm.154-1.283V5.774l-1.1 1.683h1.1z' /></svg>
                            <div class="designobject-createtab-text">
                                <?php echo transm($sModule, 'pagebuilder_designobject_h4_text', 'heading 4'); ?>
                            </div>                            
                        </div>       
                        <div class="designobject-createtab draggable designObjectInspectorH5" data-searchlabelscsv="h5,<?php echo transm($sModule, 'pagebuilder_designobject_h5_seachslabelscsv', 'header5, header 5, heading5, heading 5'); ?>" onmousedown="designObjectClickInInspector('designObjectInspectorH5')" draggable="true">
                            <svg class="iconchangefill" viewBox="-4 -6.5 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-header-5"><path d='M2 4h4V1a1 1 0 1 1 2 0v8a1 1 0 1 1-2 0V6H2v3a1 1 0 1 1-2 0V1a1 1 0 1 1 2 0v3zm8.003 4.317h2.68c.386 0 .699-.287.699-.642 0-.355-.313-.642-.698-.642H10.01l.002-.244L10 3h5.086v1.888h-3.144l.014.617h1.114c1.355 0 2.469.984 2.523 2.23.052 1.21-.972 2.231-2.288 2.28l-.095.001-3.21-.02V8.73l.003-.414z' /></svg>
                            <div class="designobject-createtab-text">
                                <?php echo transm($sModule, 'pagebuilder_designobject_h5_text', 'heading 5'); ?>
                            </div>                            
                        </div>   
                        <div class="designobject-createtab draggable designObjectInspectorH6" data-searchlabelscsv="h6,<?php echo transm($sModule, 'pagebuilder_designobject_h6_seachslabelscsv', 'header6, header 6, heading6, heading 6'); ?>" onmousedown="designObjectClickInInspector('designObjectInspectorH6')" draggable="true">                            
                            <svg class="iconchangefill" viewBox="-4.5 -7 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-header-6"><path d='M2 4h4V1a1 1 0 1 1 2 0v8a1 1 0 1 1-2 0V6H2v3a1 1 0 1 1-2 0V1a1 1 0 1 1 2 0v3zm11.949 2.057c.43.44.651.999.651 1.64 0 .629-.228 1.18-.67 1.62-.442.437-.99.663-1.613.663a2.212 2.212 0 0 1-1.649-.693c-.43-.45-.652-.985-.652-1.58 0-.224.034-.449.1-.672.063-.211.664-1.627.837-1.966.251-.491.65-1.204 1.197-2.137l1.78.652-.917 1.88c.249.113.733.386.936.593zm-1.63.765a.85.85 0 0 0-.858.863.85.85 0 0 0 .252.613.865.865 0 0 0 1.48-.614.844.844 0 0 0-.25-.611.866.866 0 0 0-.623-.251z' /></svg>
                            <div class="designobject-createtab-text">
                                <?php echo transm($sModule, 'pagebuilder_designobject_h6_text', 'heading 6'); ?>
                            </div>                            
                        </div>                                                                                            
                    </div>
                </div>
                <div id="tab-content-new-variables-desktop" class="designobject-grid-desktop">variables

                </div>     
                -->           
            </div>

        </div>

        <div class="pagecolumn-middle">
            <div class="toolbar">

                <!--
                <div id="toolbarBlockHTML">

                    <button id="btnToolbarBlockHTMLBol" onclick="">
                        <svg class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <g id="Layer_2" data-name="Layer 2">
                            <g id="invisible_box" data-name="invisible box">
                            <rect width="48" height="48" fill="none"/>
                            </g>
                            <g id="Layer_6" data-name="Layer 6">
                            <path d="M34.4,23.1A11,11,0,0,0,26,5H9V43H28A11,11,0,0,0,39,32,10.8,10.8,0,0,0,34.4,23.1ZM15,11H26a5,5,0,0,1,0,10H15ZM28,37H15V27H28a5,5,0,0,1,0,10Z"/>
                            </g>
                        </g>
                        </svg>
                    </button>   

                    <button id="btnToolbarBlockHTMLItalic" onclick="">
                        <svg class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="invisible_box" data-name="invisible box">
                                <rect width="48" height="48" fill="none"/>
                                </g>
                                <g id="Layer_6" data-name="Layer 6">
                                <polygon points="41 5 15 5 15 11 24.7 11 17.3 37 7 37 7 43 33 43 33 37 23.3 37 30.7 11 41 11 41 5 41 5"/>
                                </g>
                            </g>
                        </svg>                    
                    </button>

                    <button id="btnToolbarBlockHTMLStrikethrough" onclick="">
                        <svg class="iconchangefill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="invisible_box" data-name="invisible box">
                                    <rect width="48" height="48" fill="none"/>
                                </g>
                                <g id="Layer_6" data-name="Layer 6">
                                <g>
                                    <path d="M21.1,20c.1,0,.9-.1,0-.3C17.4,19,15,16.9,15,15s3.7-5,9-5,7.7,1.9,8.7,3.8A2.7,2.7,0,0,1,33,15l4.7-4.6C35.3,6.5,30,4,24,4,15.6,4,9,8.8,9,15a9,9,0,0,0,1.6,5Z"/>
                                    <path d="M44,22H4v4H24c4.9,0,9,2.8,9,6s-4.1,6-9,6-8.2-2.2-8.9-5.1l-4.5,4.5C13.1,41.3,18.2,44,24,44c8.3,0,15-5.4,15-12a10.1,10.1,0,0,0-2-6h7Z"/>
                                </g>
                                </g>
                            </g>
                        </svg> 
                    </button>                       

                    <button id="btnToolbarBlockHTMLLink" onclick="">
                        <svg class="changeiconcolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                        </svg>
                    </button>     
                    
                    <button id="btnToolbarBlockHTMLMenu" onclick="">
                        <svg class="changeiconcolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                        </svg>
                    </button>
                    
                </div>                
                -->
            </div>
            <div id="designviewport-container">
                <div class="designviewport-spacer"></div>
                <div class="viewport-desktop" id="designviewport-resizecontainer"> 
                    <div id="designer-root">
                        <?php echo $sData; ?>
                    </div>
                    <div id="panel-add">
                        <div id="panel-add-dragtarget" class="visible droppable" onclick="toggleAddPanelContents()">
                            <svg class="iconchangecolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <div id="panel-add-hinttext">
                                <?php echo transm($sModule, 'pagebuilder_panel_adddragtarget_text_dragorclick', 'Drag new object here to add<br>or click plus-icon to add new object'); ?>
                            </div>
                        </div>
                        <div id="panel-add-designobjects">
                            <!-- filled by js -->
                            <div id="panel-add-designobjects-back" onclick="toggleAddPanelContents()">
                                <?php echo transm($sModule, 'pagebuilder_panel_adddesignobjects_text_goback', 'Go back'); ?>
                            </div>                             
                        </div>
                    </div>  
                </div>              
                <div class="designviewport-spacer"></div>                
            </div>
        </div>
        <div class="pagecolumn-panel-details-desktop panelDetailsDesktopVisible">
            <div class="tabsheads">
                <div class="tabhead selected"   id="tab-head-details-document-desktop"   ><?php echo transm($sModule, 'pagebuilder_tabhead_document_text', 'document'); ?></div>
                <div class="tabhead"            id="tab-head-details-structure-desktop"  ><?php echo transm($sModule, 'pagebuilder_tabhead_structure_text', 'structure'); ?></div>
                <div class="tabhead"            id="tab-head-details-element-desktop"    ><?php echo transm($sModule, 'pagebuilder_tabhead_element_text', 'element'); ?></div>
            </div>
            <div class="tabscontent">
                <div id="tab-content-details-document-desktop">
                    <?php echo $sDivDocumentDetails; ?>
                </div>
                <div id="tab-content-details-structure-desktop">tab-structuredesktop
                </div>                
                <div id="tab-content-details-element-desktop">
                    <?php echo transm($sModule, 'pagebuilder_panel_detail_defaulttext_selectelement', 'Select element in designer'); ?>
                </div>
            </div>
        </div>
    </div>


    <!-- javascript that the page need to be fully loaded for -->
    <script>
        <?php 
            // //JS Webcomponents
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
            // // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'dr-paginator.js';             
            // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-tabsheets'.DIRECTORY_SEPARATOR.'dr-tabsheets.js';

            //include everything from $arrIncludeJSDOMEnd
            global $arrIncludeJSDOMEnd;
            foreach ($arrIncludeJSDOMEnd as $sPath)
            {
                include_once($sPath);
                // echo "\n";
            }

        ?>
    </script> 
</body>
</html>