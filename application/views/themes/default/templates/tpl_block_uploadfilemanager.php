<!-- dialog -->
<dialog id="dlgUploadFileManager">
    <div class="dialog-header">
        <div class="dialog-button-x" id="btnUploadFileManagerX">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </div>
        <div class="dialog-title"><?php echo transcms('cms_dialog_uploadfilemanager_header_text', 'Upload file manager') ?></div>
    </div>
    <div class="dialog-body" id="dlgUploadFileManagerBody">

        <!-- actual contents -->
        <div id="uploadfilemanager-toolbar">                
            <div id="uploadfilemanager-toolbar-column-filter">
                <label for="sel-uploadfilemanager-filter"><?php echo transcms('cms_dialog_uploadfilemanager_label_filter', 'Filter');?></label>
                <button><svg class="iconchangefill" id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M368,224c26.5,0,48-21.5,48-48c0-26.5-21.5-48-48-48c-26.5,0-48,21.5-48,48C320,202.5,341.5,224,368,224z"/><path d="M452,64H60c-15.6,0-28,12.7-28,28.3v327.4c0,15.6,12.4,28.3,28,28.3h392c15.6,0,28-12.7,28-28.3V92.3   C480,76.7,467.6,64,452,64z M348.9,261.7c-3-3.5-7.6-6.2-12.8-6.2c-5.1,0-8.7,2.4-12.8,5.7l-18.7,15.8c-3.9,2.8-7,4.7-11.5,4.7   c-4.3,0-8.2-1.6-11-4.1c-1-0.9-2.8-2.6-4.3-4.1L224,215.3c-4-4.6-10-7.5-16.7-7.5c-6.7,0-12.9,3.3-16.8,7.8L64,368.2V107.7   c1-6.8,6.3-11.7,13.1-11.7h357.7c6.9,0,12.5,5.1,12.9,12l0.3,260.4L348.9,261.7z"/></g></svg></button><!-- avoid whitespace
                --><button><svg class="iconchangecolor" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></button>
            </div>                        
            <div id="uploadfilemanager-toolbar-column-sorton">
                <label for="sel-uploadfilemanager-sorton"><?php echo transcms('cms_dialog_uploadfilemanager_label_sorton', 'Sort on');?></label>
                <button><svg class="iconchangecolor" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg></button><!-- avoid whitespace
                --><button><svg class="iconchangecolor" enable-background="new 0 0 32 32" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="calendar_1_"><path d="M29.334,3H25V1c0-0.553-0.447-1-1-1s-1,0.447-1,1v2h-6V1c0-0.553-0.448-1-1-1s-1,0.447-1,1v2H9V1   c0-0.553-0.448-1-1-1S7,0.447,7,1v2H2.667C1.194,3,0,4.193,0,5.666v23.667C0,30.806,1.194,32,2.667,32h26.667   C30.807,32,32,30.806,32,29.333V5.666C32,4.193,30.807,3,29.334,3z M30,29.333C30,29.701,29.701,30,29.334,30H2.667   C2.299,30,2,29.701,2,29.333V5.666C2,5.299,2.299,5,2.667,5H7v2c0,0.553,0.448,1,1,1s1-0.447,1-1V5h6v2c0,0.553,0.448,1,1,1   s1-0.447,1-1V5h6v2c0,0.553,0.447,1,1,1s1-0.447,1-1V5h4.334C29.701,5,30,5.299,30,5.666V29.333z" fill="currentColor"/><rect fill="currentColor" height="3" width="4" x="7" y="12"/><rect fill="currentColor" height="3" width="4" x="7" y="17"/><rect fill="currentColor" height="3" width="4" x="7" y="22"/><rect fill="currentColor" height="3" width="4" x="14" y="22"/><rect fill="currentColor" height="3" width="4" x="14" y="17"/><rect fill="currentColor" height="3" width="4" x="14" y="12"/><rect fill="currentColor" height="3" width="4" x="21" y="22"/><rect fill="currentColor" height="3" width="4" x="21" y="17"/><rect fill="currentColor" height="3" width="4" x="21" y="12"/></g></svg></button>
            </div>    
            <div id="uploadfilemanager-toolbar-column-sortorder">
                <label for="sel-uploadfilemanager-sortorder"><?php echo transcms('cms_dialog_uploadfilemanager_label_sortorder', 'Order');?></label>
                <button class="pressed"><svg class="iconchangefill" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="9 16.172 2.929 10.101 1.515 11.515 10 20 10.707 19.293 18.485 11.515 17.071 10.101 11 16.172 11 0 9 0"/></svg></button><!-- avoid whitespace
                --><button><svg class="iconchangefill" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="9 3.828 2.929 9.899 1.515 8.485 10 0 10.707 .707 18.485 8.485 17.071 9.899 11 3.828 11 20 9 20 9 3.828"/></svg></button>
            </div>                                
            <div id="uploadfilemanager-toolbar-column-view">
                <label for="sel-uploadfilemanager-view"><?php echo transcms('cms_dialog_uploadfilemanager_label_view', 'View');?></label>
                <button class="pressed"><svg class="iconchangecolor" enable-background="new 0 0 48 48" viewBox="0 0 48 48" width="48px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><rect fill="currentColor" height="12" width="12"/><rect fill="currentColor" height="12" width="12" x="18"/><rect fill="currentColor" height="12" width="12" x="36"/><rect fill="currentColor" height="12" width="12" y="18"/><rect fill="currentColor" height="12" width="12" x="18" y="18"/><rect fill="currentColor" height="12" width="12" x="36" y="18"/><rect fill="currentColor" height="12" width="12" y="36"/><rect fill="currentColor" height="12" width="12" x="18" y="36"/><rect fill="currentColor" height="12" width="12" x="36" y="36"/></g></svg></button><!-- avoid whitespace
                --><button><svg class="iconchangecolor" enable-background="new 0 0 48 48" viewBox="0 0 48 48" width="48px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Layer_3"><g><rect fill="currentColor" height="6" width="6" y="10"/><rect fill="currentColor" height="6" width="6" y="19.917"/><rect fill="currentColor" height="6" width="6" y="30.084"/><rect fill="currentColor" height="6" width="38" x="10" y="10"/><rect fill="currentColor" height="6" width="38" x="10" y="19.917"/><rect fill="currentColor" height="6" width="38" x="10" y="30.084"/></g></g></svg></button>
            </div>                        
            <div id="uploadfilemanager-toolbar-column-search">
                <label for="sel-uploadfilemanager-search"><?php echo transcms('cms_dialog_uploadfilemanager_label_search', 'Search');?></label>
                <input id="sel-uploadfilemanager-search" type="search">
            </div>                        
            <div id="#uploadfilemanager-toolbar-column-space">
                &nbsp;<!-- empty space -->
            </div>                        
            <div id="uploadfilemanager-toolbar-column-upload">
                <button id="btn-uploadfilemanager-upload"><svg class="iconfillcolor" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg><?php echo transcms('cms_dialog_uploadfilemanager_button_upload', 'Upload');?></button>
            </div>                
        </div>

        <div id="uploadfilemanager-filespanel">
            <div id="uploadfilemanager-filelistpanel">
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
                file 1<br>
                file 2<br>
                file 3<br>
            </div>
            <div id="uploadfilemanager-previewpanel">
                <div id="uploadfilemanager-previewpanel-flexbox">
                    <div id="uploadfilemanager-previewpanel-flexbox-image">
                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;stroke:#000;stroke-linejoin:round;stroke-width:2px;}</style></defs><title/><g data-name="335-Document" id="_335-Document"><polygon class="cls-1" points="10 1 4 7 4 31 28 31 28 1 10 1"/><polyline class="cls-1" points="10 1 10 7 4 7"/><rect class="cls-1" height="12" width="14" x="9" y="14"/><line class="cls-1" x1="16" x2="16" y1="14" y2="26"/><line class="cls-1" x1="9" x2="23" y1="20" y2="20"/></g></svg>                        
                    </div>
                    <div id="uploadfilemanager-previewpanel-flexbox-filename">
                        filenameasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf.jpeg
                    </div>
                    <div id="uploadfilemanager-previewpanel-flexbox-size">
                        <?php echo transcms('cms_dialog_uploadfilemanager_previewpanel_title_filesize', 'Size') ?>: 3kb
                    </div>                    
                    <div id="uploadfilemanager-previewpanel-flexbox-date">
                        <?php echo transcms('cms_dialog_uploadfilemanager_previewpanel_title_date', 'Date') ?>: 2-12-2055
                    </div>                    
                    <div id="uploadfilemanager-previewpanel-flexbox-rename">
                        <button><?php echo transcms('cms_dialog_uploadfilemanager_previewpanel_button_rename', 'Rename') ?></button>
                    </div>
                    <div id="uploadfilemanager-previewpanel-flexbox-delete">
                        <button><?php echo transcms('cms_dialog_uploadfilemanager_previewpanel_button_delete', 'Delete') ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- end contents -->

    </div>
    <div class="dialog-footer">
        <button id="btnDialogUploadFileManagerCancel"><?php echo transcms('cms_dialog_uploadfilemanager_button_exit', 'Exit') ?></button>
        <button id="btnDialogUploadFileManagerUseFile" autofocus class="default"><?php echo transcms('cms_dialog_uploadfilemanager_button_usefile', 'Use file') ?></button>
    </div>
</dialog>


