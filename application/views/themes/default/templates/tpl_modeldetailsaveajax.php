<script>
    <?php
        // include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'ajaxform.js'; //---> uses PHP
    ?>
</script>



<div id="detailsave-header">
    <div class="headercolumn headercolumn-exit">
        <button id="btnExit" onmousedown="handleExitAFC(exitAfterSave, document.querySelector('#btnExit dr-icon-spinner'))">
            <dr-icon-spinner>
                <svg class="iconchangecolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" transform="rotate(180)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>              
            </dr-icon-spinner>
            <div class="buttontext"><?php echo transm(APP_ADMIN_CURRENTMODULE, 'detailsave_button_exit', 'Exit'); ?></div>        
        </button>
    </div>        
    <div class="headercolumn-title">
        <label for="edtNameInternal" id="lblTitleHeader"><?php echo $sTitle; ?></label>
    </div>
    <div class="headercolumn-language">
        <?php 
            if (isset($objLanguages))
            {
                echo '<dr-combobox>';
                //@todo                
                echo '</dr-combobox>';
            }
        ?>
    </div>
    <div class="headercolumn headercolumn-save">
        <button id="btnSave" onmousedown="handleSaveAFC(null, document.querySelector('#btnSave dr-icon-spinner'))">
            <dr-icon-spinner>
                <svg id="svgSaveIcon" class="iconchangecolor" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>           
            </dr-icon-spinner>     
            <!-- <svg id="svgSaveIconSpinner" class="iconchangefill spinner" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
                <path d="M41.9 23.9c-.3-6.1-4-11.8-9.5-14.4-6-2.7-13.3-1.6-18.3 2.6-4.8 4-7 10.5-5.6 16.6 1.3 6 6 10.9 11.9 12.5 7.1 2 13.6-1.4 17.6-7.2-3.6 4.8-9.1 8-15.2 6.9-6.1-1.1-11.1-5.7-12.5-11.7-1.5-6.4 1.5-13.1 7.2-16.4 5.9-3.4 14.2-2.1 18.1 3.7 1 1.4 1.7 3.1 2 4.8.3 1.4.2 2.9.4 4.3.2 1.3 1.3 3 2.8 2.1 1.3-.8 1.2-2.5 1.1-3.8 0-.4.1.7 0 0z"/>
            </svg> -->
            <div class="buttontext"><?php echo transm(APP_ADMIN_CURRENTMODULE, 'detailsavetemplate_button_save', 'Save'); ?></div>        
        </button>                       
    </div>
</div>

<div id="detailsave-body">
    <?php
        echo $objFormGenerator->generate()->renderHTMLNode();
    ?>
</div>

                
                                      