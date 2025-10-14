<?php
/**
 * DEFAULT template: Overview of modules.
 * If you want to specify your own custom overview, add acopy of this template to 
 * the module directory and make sure you load that in the module-controller
 */
    use dr\classes\models\TSysModel;
    use dr\modules\Mod_Sys_Modules\models\TSysModules;
    use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
    use dr\classes\controllers\TCRUDListController;
    
    include_once APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php'; //getNextSortOrder
    
    //caching most used translations
    $sTranslatedBooleanYes = '';
    $sTranslatedBooleanYes = transcms('boolean_yes', 'yes');    
    $sTranslatedBooleanNo = '';
    $sTranslatedBooleanNo = transcms('boolean_no', 'no');
    $sTranslatedMoveOneUp = '';
    $sTranslatedMoveOneUp = transcms('recordlist_move_up', 'move record up');
    $sTranslatedMoveOneDown = '';
    $sTranslatedMoveOneDown = transcms('recordlist_move_down', 'move record down');
    $sTranslatedEdit = '';
    $sTranslatedEdit = transcms('recordlist_edit', 'edit record');
    $sTranslatedTranslate = ''; //translate icon
    $sTranslatedTranslate = transcms('recordlist_translate', 'translate record to other language');//translate icon
    
    $bShowTranslateIcon = $objModel->getTableUseTranslationLanguageID();
        
//=========== EXPLANATION ========
?>

<!-- <div class="tabexplanation">
    <?php
        if (isset($arrTabsheets))
        {   
            foreach($arrTabsheets as $arrTab)
            {
                if (endswith(APP_URLTHISSCRIPT, $arrTab[0])) //check for current tab
                {
                    if (isset($arrTab[3])) 
                        echo $arrTab[3]; //show description
                }
            }
        }
    ?>
</div> -->
<?php


//=========== QUICKSEARCH ========
/*
$sQuickSearchFieldValue = '';
if(isset($_GET[TCRUDListController::FIELD_QUICKSEARCH]))
{
    $sQuickSearchFieldValue = $_GET[TCRUDListController::FIELD_QUICKSEARCH];
}
    */
?>

<!-- <div class="overview_quicksearch">
    <form name="frmQuickSearch" id="frmQuickSearch" action="<?php echo APP_URLTHISSCRIPT; ?>">
        <input type="image" src="<?php echo APP_URL_CMS_IMAGES ?>/icon-quicksearch128x128.png" >        
        <input type="search" name="<?php echo TCRUDListController::FIELD_QUICKSEARCH; ?>" class="quicksearchbox" placeholder="<?php echo transcms('overview_edit_quicksearch_default', 'search'); ?>" value="<?php echo $sQuickSearchFieldValue; ?>" onsearch="onQuickSearch(this)">        
    </form>
</div> -->


<?php
//=========== FILTERS ========
//suspended

?>

<form action="<?php echo APP_URLTHISSCRIPT;?>" method="get" name="frmBulkActions" id="frmBulkActions">
    <div class="overview_table_background">
        <table class="overview_table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" name="chkCheckAll" onClick="toggleAllCheckboxes(this, '<?php echo BULKACTION_VARIABLE_CHECKBOX_RECORDID ?>[]')">
                    </th>     
                    <th class="column-display-on-mobile">
                        <?php echo transcms('column-display-on-mobile-header', 'Record') ?>
                    </th>
                    <?php
                        /**
                         * we work with sort column indexes in stead of sort column names to pass through the visible url
                         * to prevent messing with the column names
                         */
                    
                        $sColumnHead = '';
                        $sURL = '';
                        $sSortOrder = ''; //sort order from $_GET[ACTION_VARIABLE_SORTORDER]
                        $arrQBSort = array();
                        $arrQBSort = $objModel->getQBSort();//array(TSysModel::QB_SORTINDEX_TABLE => $sTable, TSysModel::QB_SORTINDEX_FIELD => $sField, TSysModel::QB_SORTINDEX_ORDER => $sSortOrder);                            
                        $arrQBSelect = array(); // to determine on which column to sort
                        $arrQBSelect = $objModel->getQBSelectFrom();   
                        $iCountQBSelect = count($arrQBSelect);
                        $iSortColIndex = 0; //index in $arrQBSelect
                        $arrQBSelectRow = array();
                        
                        //going through every column
                        foreach ($arrTableColumnsShow as $arrColumn)
                        {     
                            $iSortColIndex = 0;
                            $sSortOrder = '';                        
                            $sTableName = $arrColumn[0];
                            $sColumnName = $arrColumn[1];
                            $sColumnHead = $arrColumn[2];                        
                            $bCSSClassTDShowOnDesktop = true; //add css class  showOnDesktop - some columns you want to show on mobile AND desktop (like the sort up-down);
                                
                            //figure out the index of the sortcolumn
                            for ($iSCICounter = 0; $iSCICounter < $iCountQBSelect; $iSCICounter++)
                            {
                                if ($arrQBSelect[$iSCICounter][TSysModel::QB_SELECTINDEX_FIELD] == $sColumnName)
                                {
                                    if (($arrQBSelect[$iSCICounter][TSysModel::QB_SELECTINDEX_TABLE] == $sTableName) || $sTableName == '') //tablenames of the current TSysModel are empty (and later replaced by the real tablename)
                                    {
                                        $iSortColIndex = $iSCICounter;
                                        $iSCICounter = $iCountQBSelect;//jump out of for loop, we found the sort column
                                    }
                                }
                            }
                            
                            //determine sort order
                            foreach($arrQBSort as $arrQBSortItem) //technically there can be multiple rows sorted in model, although not supported (yet) in GUI, because via the url is currently only sort column passed
                            {
                                if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == $sColumnName) //is actually sorted on this column in database?
                                    $sSortOrder = $arrQBSortItem[TSysModel::QB_SORTINDEX_ORDER];
                            }                            

                            //only add link when NOT an encrypted field
                            $bAddLinkToHead = true;
                            if ($objModel->getFieldsDefinedExists($sColumnName))
                                $bAddLinkToHead = ($objModel->getFieldEncryptionDisabled($sColumnName));

                            if ($bAddLinkToHead)
                            {
                                $sURL = APP_URLTHISSCRIPT;
                                $sURL = addVariableToURL($sURL, ACTION_VARIABLE_SORTCOLUMNINDEX, $iSortColIndex);//sort on column INDEX!!
                                $sURL = addVariableToURL($sURL, ACTION_VARIABLE_SORTORDER, getNextSortOrder($sSortOrder));//sort order
                                $sColumnHead = '<a href="'.$sURL.'">'.$sColumnHead.'</a>';   
                            }

                            //sort order arrows
                            foreach($arrQBSort as $arrQBSortItem) //technically there can be multiple rows sorted in model, although not supported (yet) in GUI, because via the url is currently only sort column passed
                            {
                                if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == $sColumnName) //is actually sorted on this column in database?
                                {
                                    $bCSSClassTDShowOnDesktop = false;

                                    if ($sSortOrder == SORT_ORDER_ASCENDING)
                                    {
                                        $sColumnHead.='<img src="'.APP_URL_CMS_IMAGES.'/icon-sortasc16x16.png">';
                                    }

                                    if ($sSortOrder == SORT_ORDER_DESCENDING)
                                    {
                                        $sColumnHead.='<img src="'.APP_URL_CMS_IMAGES.'/icon-sortdesc16x16.png">';
                                    }      
                                }
                            }                            

                        
                            ?>                    
                                <th class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnHead; ?></th>
                            <?php
                        }
                    ?>
                    <th>
                        <?php 
                            //=========== CREATE NEW ========
                            if (auth(APP_ADMIN_CURRENTMODULE, $objCRUD->getAuthorisationCategory(), AUTH_OPERATION_CREATE))
                            {
                                ?>                            
                                    <input type="button" onclick="window.location.href = '<?php echo $sURLDetailPage; ?>';" value="<?php echo transcms('item_create', 'New'); ?>" class="button_normal">
                                <?php
                            }
                        ?>   
                    </th> 
                    

                </tr>
            </thead>
            <tbody>
                <?php
                                
                    //value of te ID field (because getID() doesn't always exist, it can be getRandomID() or getUniqueID())
                    $iIDValue = 0; //default

                    $bEditAllowed = false;
                    $bEditAllowed = auth(APP_ADMIN_CURRENTMODULE, $objCRUD->getAuthorisationCategory(), AUTH_OPERATION_CHANGE);
                                                                
                    
                    //ONLY allow up and down if sorted on iOrder
                    $bOrderOneUpDownAllowed = false;
                    $arrQBSortItem = array();
                    if (count($arrQBSort) > 0) 
                    {
                        $arrQBSortItem = $arrQBSort[0]; ///we only have to know if the first column in QBSort is iOrder (more columns doesn't matter, because if it isn't the first sort column, you don't see anything of moving up or down )
                        if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == TSysModel::FIELD_POSITION) //is actually sorted on iOrder?
                        {
                            if (auth(APP_ADMIN_CURRENTMODULE, $objCRUD->getAuthorisationCategory(), AUTH_OPERATION_CHANGEPOSITION)) //if also allowed by authentication, then it is allowed to show
                            {
                                $bOrderOneUpDownAllowed = true;
                            }
                        }
                    }
                
                    
                    while($objModel->next())
                    {    
                        //value of te ID field (because getID() doesn't always exist, it can be getRandomID() or getUniqueID())
                        $iIDValue = 0; //default fallback
                        if ($objModel->getTableUseIDField())
                        {
                            $iIDValue = $objModel->getID();
                        }
                        else //looking for alternatives to the ID field
                        {
                            if ($objModel->getTableUseRandomID()) //test FIRST because it is integer based (counting is faster)
                                $iIDValue = $objModel->getRandomID();
                            elseif ($objModel->getTableUseUniqueID()) //test LAST because it is character based (counting is slower)
                                $iIDValue = $objModel->getUniqueID(); 
                        }

                        //edit alowed?
                        $bEditAllowedThisRecord = true;
                        $bEditAllowedThisRecord = $bEditAllowed; //temp only for this record so we can change the privilges based on locks and checkout

                        //checkout
                        $bRecordCheckedOut = false;
                        if ($objModel->getTableUseCheckout())
                        {
                            $objDateCheckoutExpire = null;
                            $objDateCheckoutExpire = $objModel->getCheckoutExpires();
                            if ($objDateCheckoutExpire->isInTheFuture())
                            {
                                $bRecordCheckedOut = true;
                                $bEditAllowedThisRecord = false;
                            }
                        }

                        //lock
                        $bRecordLocked = false;
                        if ($objModel->getTableUseLock())
                        {
                            if ($objModel->getLocked())
                            {
                                $bRecordLocked = true;                            
                                $bEditAllowedThisRecord = false;
                            }
                        }                        
                        
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="<?php echo BULKACTION_VARIABLE_CHECKBOX_RECORDID ?>[]" value="<?php echo $iIDValue; ?>" onchange="toggleRowColorCheckboxClick(this)">
                            </td>
                            <td class="column-display-on-mobile">
                                <?php echo $objModel->getDisplayRecordShort(); ?>
                            </td>
                            <?php
                                foreach ($arrTableColumnsShow as $arrColumn)
                                {
                                    $sTableName = $arrColumn[0];
                                    $sColumnName = $arrColumn[1];
                                    $bCSSClassTDShowOnDesktop = true; //add css class  showOnDesktop - some columns you want to show on mobile AND desktop (like the sort up-down);                                
                                    $sColumnValue = '';
                                    $iColType = $objModel->getFieldType($sColumnName);

                                    switch ($iColType)
                                    {
                                            case TP_DATETIME:                                                
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getDateTimeAsString(); 
                                                    break;
                                            case TP_BOOL:  
                                                    if ($objModel->get($sColumnName, $sTableName, true))
                                                        $sColumnValue = '<img alt="'.$sTranslatedBooleanYes.'" src="'.APP_URL_CMS_IMAGES.'/icon-checked-true32x32.png">';
                                                    else
                                                        $sColumnValue = '<img alt="'.$sTranslatedBooleanNo.'" src="'.APP_URL_CMS_IMAGES.'/icon-checked-false32x32.png">';
                                                    break;
                                            case TP_COLOR:  
                                                    $sColumnValue = '<div class="color" style="height:10px;width:40px; background-color: #'.$objModel->get($sColumnName, $sTableName, true).'"></div>';
                                                    break;                                                       
                                            case TP_CURRENCY:
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getValueFormatted();
                                                    break;
                                            case TP_DECIMAL:
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getValueFormatted();
                                                    break;
                                            case TP_FLOAT:
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getAsString();
                                                    break;
                                            case CT_IPADDRESS:
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName);
                                                    break;
                                            default: 
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true);
                                    }            
                                    
                                    //CHANGE ORDER up and down arrows
                                    if ($sColumnName == TSysModel::FIELD_POSITION)
                                    {
                                        $bCSSClassTDShowOnDesktop = false;
                                        $bOrderOneUpAllowed = $bOrderOneUpDownAllowed; //per item can be disabled if it is the first or last item
                                        $bOrderOneDownAllowed = $bOrderOneUpDownAllowed; //per item can be disabled if it is the first or last item
                                        
                                        if (!$bEditAllowedThisRecord) //record can be locked or checked out 
                                        {
                                            $bOrderOneUpAllowed = false;
                                            $bOrderOneDownAllowed = false;
                                        }
                                        
                                        $sURL = APP_URLTHISSCRIPT;
                                        $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $iIDValue);//move record id
                                        $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUPDOWN, ACTION_VALUE_ORDERONEUPDOWN);//Change Order 1 record
                                        $sURL = addVariableToURL($sURL, ACTION_VARIABLE_SORTORDER, $sSortOrder);//Change Order 1 record                                    
                                        
                                        //not allowed if it's first item on first page
                                        if ($objPaginator->isFirstPage())
                                        {
                                            if ($objModel->isFirstRecord())
                                                $bOrderOneUpAllowed = false;
                                        }
                                        
                                        //not allowed if it's the last item on the last page
                                        if ($objPaginator->isLastPage())
                                        {
                                            if ($objModel->isLastRecord())
                                                $bOrderOneDownAllowed = false;                                        
                                        }                                    
                                        
                                        
                                        //up arrow
                                        if ($bOrderOneUpAllowed)
                                        {
                                            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUP, ACTION_VALUE_ORDERONEUP); //move one up
                                            $sColumnValue = '<a href="'.$sURL.'"><img alt="'.$sTranslatedMoveOneUp.'" src="'.APP_URL_CMS_IMAGES.'/icon-up-enabled32x32.png"></a>';                                        
                                        }
                                        else
                                        {
                                            $sColumnValue = '<img alt="'.$sTranslatedMoveOneUp.'" src="'.APP_URL_CMS_IMAGES.'/icon-up-disabled32x32.png">';
                                        }
                                        
                                        
                                        //down arrow
                                        if ($bOrderOneDownAllowed)
                                        {
                                            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUP, ACTION_VALUE_ORDERONEDOWN); //move one down
                                            $sColumnValue.= '<a href="'.$sURL.'"><img alt="'.$sTranslatedMoveOneDown.'" src="'.APP_URL_CMS_IMAGES.'/icon-down-enabled32x32.png"></a>';                                        
                                        }
                                        else
                                        {
                                            $sColumnValue.= '<img alt="'.$sTranslatedMoveOneDown.'" src="'.APP_URL_CMS_IMAGES.'/icon-down-disabled32x32.png">';
                                        }                                    
        
                                    }   
                                    
                                    ?>
                                        <td class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnValue ?></td>
                                    <?php
                                }
                            ?>
                            <td>
                                <?php 
                                
                                    
                                    //checkout
                                    if ($bRecordCheckedOut)
                                        echo '<img src="'.APP_URL_CMS_IMAGES.'/icon-checkout-locked32x32.png" alt="'.transcms('recordlist_record_checkedout', 'Record CHECKED OUT by [source], not available for editing', 'source', $objModel->getCheckoutSource()) .'">';

                                    
                                    //lock
                                    if ($bRecordLocked)
                                        echo '<img src="'.APP_URL_CMS_IMAGES.'/icon-lock-closed32x32.png" alt="'.transcms('recordlist_record_locked', 'Record LOCKED by [source], not available for editing','source', $objModel->getLockedSource()).'">';
                                
                                    
                                    //edit-icon
                                    if (isset($sURLDetailPage) && $bEditAllowedThisRecord)
                                    {   
                                        if (isset($sURLDetailPage))
                                        {
                                            if ($objModel->getTableUseRandomIDAsPrimaryKey())
                                                echo '<a href="'.addVariableToURL($sURLDetailPage, ACTION_VARIABLE_UNIQUEID, $objModel->getRandomID()).'">'; //I choose not to use addvariableToID() because of speed
                                            
                                            if ($objModel->getTableUseIDField())
                                                echo '<a href="'.addVariableToURL($sURLDetailPage, ACTION_VARIABLE_ID, $iIDValue).'">'; //I choose not to use addvariableToID() because of speed
                                        }
                                        echo '<img src="'.APP_URL_CMS_IMAGES.'/icon-edit32x32.png" alt="'.$sTranslatedEdit.'">';
                                        if (isset($sURLDetailPage))
                                            echo '</a>';                                       
                                    
                                    }
                                    
                                    //translate
                                    if ($bShowTranslateIcon)
                                    {
                                        if (isset($sURLTranslatePage))
                                            echo '<a href="'.$sURLTranslatePage.'?'.ACTION_VARIABLE_ID.'='.$iIDValue.'">';
                                        echo '<img src="'.APP_URL_CMS_IMAGES.'/icon-translate32x32.png" alt="'.$sTranslatedTranslate.'">';
                                        if (isset($sURLTranslatePage))
                                            echo '</a>';          
                                    }

                                    
                                    unset($objDateCheckoutExpire);
                                ?>
                            </td>                                    
                        </tr>			
                        <?php
                    }
                ?>
            </tbody>
        </table>    

        <?php   
            //==== NO RECORDS? ====
            if ($objModel->count() == 0)
            {
                echo '<center>';
                echo '<img src="'.APP_URL_CMS_IMAGES.'/icon-alert-grey128x128.png"><br>';
                echo transcms('message_noitemstodisplay','[ no items to display ]');
                echo '<br>';
                echo '</center>';
            }
        ?>          
    </div>
    
    
    <?php
    
    //=========== BULK ACTIONS ========
    if (isset($objSelectBulkActions)) //auth() is checked in crud controller
    {
        ?>
            <div class="overview_bulkactions">
                <?php echo $objSelectBulkActions->renderHTMLNode(); ?>
                <input type="button" value="<?php echo transcms('overview_bulkactions_button_execute', 'execute');?>" onclick="confirmBulkAction('<?php echo $objSelectBulkActions->getID();?>')" class="button_normal">
            </div>
        <?php
    }

    ?>    
</form>   



<div class="overview_paginator">
<?php
       $objUL = $objPaginator->generateHTMLList();
       $objUL->setClass('paginator');
       echo $objUL->renderHTMLNode();       
?>
    <div class="paginator_textshowingXfromY">
        <?php
            if ($objPaginator->getTotalItemsCount() > 0)
            {
                echo transcms('paginator_showingXtoYofZ', 'Showing [start] to [finish] of [total] entries', 
                'start', $objPaginator->getCurrentPageFirstItem(),
                'finish', $objPaginator->getCurrentPageLastItem(),
                'total', $objPaginator->getTotalItemsCount()
                );   
            }
            else
            {
                echo transcms('paginator_showingXtoYofZ_noentries', 'Showing 0 entries'); 
            }
        ?>
    </div>
</div>  
          