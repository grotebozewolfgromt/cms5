<div class="pagetitlewrapper">
    <h1>
        <?php echo $sTitle; ?>
        <?php
            if (isset($arrTabsheets))
            {   
                foreach($arrTabsheets as $arrTab)
                {
                    if (endswith(APP_URLTHISSCRIPT, $arrTab[0])) //check for current tab
                    {
                        if (isset($arrTab[3])) 
                            echo '<dr-icon-info>'.$arrTab[3].'</dr-icon-info>'; //show description
                    }
                }
            }
        ?>
    </h1>
    <?php
        //button: "create new"
        if ($bAllowedCreateNew)
            echo '<button onmousedown="onNewRecordClick(this)" class="button_normal" id="btnNewRecord">'.transcms('modellistajax_createnew_button_text', 'Create new').'&nbsp;<dr-icon-spinner><svg fill="currentColor" viewBox="0 -6 32 32" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Guides__x26__Forms"/><g id="Icons"><polygon points="25,15 17,15 17,7 15,7 15,15 7,15 7,17 15,17 15,25 17,25 17,17 25,17  "/></g></svg></dr-icon-spinner></button>';
    ?>
</div>


<div class="overview_filters">
    <div class="overview_header"><?php echo transcms('modellistajax_dbfilters_header', 'Database filters'); ?><dr-icon-info><?php echo transcms('modellistajax_dbfilters_explanation', 'Database filters are used to filter data from a database.<br>This allows you to search for specific values.<br><ul><li>Add filter: use the "New Filter"-button, the filter is added.</li><li>To enable or disable the filter: click on the filter-icon before the text.</li><li>To edit a filter: click on the filter text.</li><li>To search multiple columns at once (called: quicksearch):<br>Type your query next to "new filter" and press Enter</li></ul>'); ?></dr-icon-info></div>
    <?php
        echo $objDBFilters->render();
    ?>
</div>        



<form action="<?php echo APP_URLTHISSCRIPT;?>" method="get" name="frmBulkActions" id="frmBulkActions">
    <!-- record list -->
    <dr-drag-drop class="dragdroprecordlist" type="table" dropbehavior="after">
        <div class="overview_table_background">
            <!-- replaced by JSON table -->
        </div>
    <dr-drag-drop>
    
    <!-- paginator -->
    <dr-paginator></dr-paginator>

    <!-- bulk actions -->
    <?php    
    //=========== BULK ACTIONS ========
    if (isset($objSelectBulkActions)) //auth() is checked in crud controller
    {
        ?>
            <div class="overview_bulkactions">
            <div class="overview_header"><?php echo transcms('modellistajax_bulkactions_header', 'Bulk actions'); ?><dr-icon-info><?php echo transcms('modellistajax_bulkactions_explanation', 'With bulk-actions you can perform an action on multiple records in the database at once.<br>Select the appropriate action in the combobox and then click button: "Execute bulk action".'); ?></dr-icon-info></div>
                <?php echo $objSelectBulkActions->renderHTMLNode(); ?>
                <button type="button" onclick="confirmBulkActionAJAX(this, '<?php echo $objSelectBulkActions->getID();?>')" class="button_normal"><dr-icon-spinner><svg viewBox="0 -220 512 650" fill="currentColor" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M424.5,216.5h-15.2c-12.4,0-22.8-10.7-22.8-23.4c0-6.4,2.7-12.2,7.5-16.5l9.8-9.6c9.7-9.6,9.7-25.3,0-34.9l-22.3-22.1  c-4.4-4.4-10.9-7-17.5-7c-6.6,0-13,2.6-17.5,7l-9.4,9.4c-4.5,5-10.5,7.7-17,7.7c-12.8,0-23.5-10.4-23.5-22.7V89.1  c0-13.5-10.9-25.1-24.5-25.1h-30.4c-13.6,0-24.4,11.5-24.4,25.1v15.2c0,12.3-10.7,22.7-23.5,22.7c-6.4,0-12.3-2.7-16.6-7.4l-9.7-9.6  c-4.4-4.5-10.9-7-17.5-7s-13,2.6-17.5,7L110,132c-9.6,9.6-9.6,25.3,0,34.8l9.4,9.4c5,4.5,7.8,10.5,7.8,16.9  c0,12.8-10.4,23.4-22.8,23.4H89.2c-13.7,0-25.2,10.7-25.2,24.3V256v15.2c0,13.5,11.5,24.3,25.2,24.3h15.2  c12.4,0,22.8,10.7,22.8,23.4c0,6.4-2.8,12.4-7.8,16.9l-9.4,9.3c-9.6,9.6-9.6,25.3,0,34.8l22.3,22.2c4.4,4.5,10.9,7,17.5,7  c6.6,0,13-2.6,17.5-7l9.7-9.6c4.2-4.7,10.2-7.4,16.6-7.4c12.8,0,23.5,10.4,23.5,22.7v15.2c0,13.5,10.8,25.1,24.5,25.1h30.4  c13.6,0,24.4-11.5,24.4-25.1v-15.2c0-12.3,10.7-22.7,23.5-22.7c6.4,0,12.4,2.8,17,7.7l9.4,9.4c4.5,4.4,10.9,7,17.5,7  c6.6,0,13-2.6,17.5-7l22.3-22.2c9.6-9.6,9.6-25.3,0-34.9l-9.8-9.6c-4.8-4.3-7.5-10.2-7.5-16.5c0-12.8,10.4-23.4,22.8-23.4h15.2  c13.6,0,23.3-10.7,23.3-24.3V256v-15.2C447.8,227.2,438.1,216.5,424.5,216.5z M336.8,256L336.8,256c0,44.1-35.7,80-80,80  c-44.3,0-80-35.9-80-80l0,0l0,0c0-44.1,35.7-80,80-80C301.1,176,336.8,211.9,336.8,256L336.8,256z"/></svg></dr-icon-spinner>&nbsp;<?php echo transcms('overview_bulkactions_button_execute', 'Execute bulk action');?></button>
            </div>
        <?php
    }

    ?>    
</form>   



          