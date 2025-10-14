<?php
/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;
    use dr\modules\Mod_Sys_Modules\models\TSysModules;
    use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
    use dr\classes\controllers\TCRUDListController;
    
    
    include_once APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php'; //getNextSortOrder
    
        //most used translations cachen
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
    
    
    //====DEFINE COLUMNS 
    //there are more columns than loaded from database
    //reasons why specifying them here:
    //-you can change the order of the columns super easy (without programming bugs)
    //-maintainance / preventing bugs
    $iColumnCount = 9;
    
    $iColumnIndexIcon = 0;
    $iColumnIndexNameTranslated = 1;
    $iColumnIndexNameInternal = 2;
    $iColumnIndexCategory = 3;
    // $iColumnIndexEnabled = 4;
    $iColumnIndexVisibleCMS = 4;
    $iColumnIndexVisibleFrontEnd = 5;
    $iColumnIndexDirExists = 6;
    $iColumnIndexSettings = 7;
    $iColumnIndexOrder  = 8;
    
    $arrDBColumnNamesIndices = array();
    $arrDBColumnNamesIndices[$iColumnIndexNameTranslated] = $arrTableColumnsShow[0]; //appoint the indexes of $arrTableColumnsShow to the columns
    $arrDBColumnNamesIndices[$iColumnIndexNameInternal] = $arrTableColumnsShow[1]; //appoint the indexes of $arrTableColumnsShow to the columns
    $arrDBColumnNamesIndices[$iColumnIndexCategory] =  $arrTableColumnsShow[4];//appoint the indexes of $arrTableColumnsShow to the columns
    // $arrDBColumnNamesIndices[$iColumnIndexEnabled] = $arrTableColumnsShow[2];//appoint the indexes of $arrTableColumnsShow to the columns
    $arrDBColumnNamesIndices[$iColumnIndexVisibleCMS] = $arrTableColumnsShow[2];//appoint the indexes of $arrTableColumnsShow to the columns
    $arrDBColumnNamesIndices[$iColumnIndexVisibleFrontEnd] = $arrTableColumnsShow[3];//appoint the indexes of $arrTableColumnsShow to the columns
    $arrDBColumnNamesIndices[$iColumnIndexOrder] = $arrTableColumnsShow[4];//appoint the indexes of $arrTableColumnsShow to the columns
   
    //===end defining columns

    
    
    
    
    
//=========== QUICKSEARCH ========
    $sQuickSearchFieldValue = '';
    if(isset($_GET[TCRUDListController::FIELD_QUICKSEARCH]))
        $sQuickSearchFieldValue = $_GET[TCRUDListController::FIELD_QUICKSEARCH];
    ?>
    <div class="overview_quicksearch">
        <form name="frmQuickSearch" id="frmQuickSearch" action="<?php echo APP_URLTHISSCRIPT; ?>">
            <input type="image" src="<?php echo APP_URL_ADMIN_IMAGES ?>/icon-quicksearch128x128.png" >        
            <input type="search" name="<?php echo TCRUDListController::FIELD_QUICKSEARCH; ?>" class="quicksearchbox" placeholder="<?php echo transcms('overview_edit_quicksearch_default', 'search'); ?>" value="<?php echo $sQuickSearchFieldValue; ?>" onsearch="onQuickSearch(this)">        
        </form>
    </div>

<?php
//=========== FILTERS ========
//if (auth(APP_ADMIN_CURRENTMODULE, 'filters', 'show'))
//{
//    if (isset($objFormFilters))
//    {
//        echo $objFormFilters->renderHTMLNode();
//    }
//}
?>


<form action="<?php echo getURLThisScript();?>" method="get" name="frmBulkActions" id="frmBulkActions">
    
    <div class = "overview_table_background">
        <table class="overview_table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" name="chkCheckAll" onchange="toggleAllCheckboxes(this, '<?php echo BULKACTION_VARIABLE_CHECKBOX_RECORDID; ?>[]')">
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
                    $sSortColumnName = ''; //sort column NAME from $_GET[ACTION_VARIABLE_SORTCOLUMNINDEX] (so name, not the index)
                    $iSortColIndex = 0;
                    $sSortOrder = '';                        
                    $sColumnHead = '';
                    $sColumnName = '';   
                    $arrQBSort = array();
                    $arrQBSort = $objModel->getQBSort();//array(TSysModel::QB_SORTINDEX_TABLE => $sTable, TSysModel::QB_SORTINDEX_FIELD => $sField, TSysModel::QB_SORTINDEX_ORDER => $sSortOrder);                            
                    $arrQBSelect = array(); // to determine on which column to sort
                    $arrQBSelect = $objModel->getQBSelectFrom();   
                    $iCountQBSelect = count($arrQBSelect);
                    
                    //going through every column
                    for ($iColumnIndex = 0; $iColumnIndex < $iColumnCount; $iColumnIndex++)                  
                    {
                        //reset
                        $iSortColIndex = 0;
                        $sSortOrder = '';                        
                        $sColumnHead = '';
                        $sColumnName = '';
                        $sTableName = '';
                        $bCSSClassTDShowOnDesktop = true; //add css class  showOnDesktop - some columns you want to show on mobile AND desktop (like the sort up-down);
                        
                        //is database field?
                        if (array_key_exists($iColumnIndex, $arrDBColumnNamesIndices))
                        {                
                            
                            //$sTableName = $arrDBColumnNamesIndices[$iColumnIndex][0];
                            $sColumnName = $arrDBColumnNamesIndices[$iColumnIndex][1];
                            $sColumnHead = $arrDBColumnNamesIndices[$iColumnIndex][2];
                            
                            
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
                                        $sColumnHead.='<img src="'.APP_URL_ADMIN_IMAGES.'/icon-sortasc16x16.png">';
                                    }

                                    if ($sSortOrder == SORT_ORDER_DESCENDING)
                                    {
                                        $sColumnHead.='<img src="'.APP_URL_ADMIN_IMAGES.'/icon-sortdesc16x16.png">';
                                    }                                      
                                }
                            }   

                        }//end is db field
                        else//custom defined fields
                        {
                            switch($iColumnIndex)
                            {   
                                case $iColumnIndexIcon://icon                                
                                    $sColumnHead = ''; 
                                    break;
                                case $iColumnIndexDirExists: //directory exists
                                    $sColumnHead = transm(APP_ADMIN_CURRENTMODULE, 'overview_column_checks', 'checks'); //translate the column name
                                    break;
                                case $iColumnIndexSettings: //settings
                                    $sColumnHead = transm(APP_ADMIN_CURRENTMODULE, 'overview_column_settings', 'settings'); //translate the column name
                                    break;
                                default:
                                    $sColumnHead = transm(APP_ADMIN_CURRENTMODULE, 'overview_column_unknown', '[unknown]'); //translate the column name                               
                            }                                
                        }                        

                        ?>                    
                            <th class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnHead; ?></th>
                        <?php
                    }
                ?>
                <th>
                    <input type="button" onclick="window.location.href = '<?php echo $sURLUploadModule; ?>';" value="<?php echo transm(APP_ADMIN_CURRENTMODULE, 'item_uploadmodule', 'upload'); ?>" class="button_normal">
                </th>                              
            </tr>
        </thead>
        <tbody>
            <?php
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
                            <input type="checkbox" name="<?php echo BULKACTION_VARIABLE_CHECKBOX_RECORDID ?>[]" value="<?php echo $objModel->getID() ?>">
                        </td>
                        <td class="column-display-on-mobile">
                            <?php echo $objModel->getDisplayRecordShort(); ?>
                        </td>                        
                        <?php
                            //walking through every column - MANUALLY INDEXING                                
                            for ($iColumnIndex = 0; $iColumnIndex < $iColumnCount; $iColumnIndex++)                  
                            {
                                $bCSSClassTDShowOnDesktop = true; //add css class  showOnDesktop - some columns you want to show on mobile AND desktop (like the sort up-down);
                                $sColumnValue = '';
                                
                                //is database field?
                                if (array_key_exists($iColumnIndex, $arrDBColumnNamesIndices))
                                {
                                    $sTableName = $arrDBColumnNamesIndices[$iColumnIndex][0];
                                    $sColumnName = $arrDBColumnNamesIndices[$iColumnIndex][1];
                                    //$sColumnHead = $arrDBColumnNamesIndices[$iColumnIndex][2];                                    

                                    
                                            
                                    $iColType = $objModel->getFieldType($sColumnName);

                                    switch ($iColType)
                                    {
                                            case TP_DATETIME:
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getDateTimeAsString(); 
                                                    break;
                                            case TP_BOOL:  
                                                if ($objModel->get($sColumnName, $sTableName, true))
                                                    $sColumnValue = '<img alt="'.$sTranslatedBooleanYes.'" src="'.APP_URL_ADMIN_IMAGES.'/icon-checked-true32x32.png">';
                                                else
                                                    $sColumnValue = '<img alt="'.$sTranslatedBooleanNo.'" src="'.APP_URL_ADMIN_IMAGES.'/icon-checked-false32x32.png">';
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
                                        $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $objModel->getID());//move record id
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
                                            $sColumnValue = '<a href="'.$sURL.'"><img alt="'.$sTranslatedMoveOneUp.'" src="'.APP_URL_ADMIN_IMAGES.'/icon-up-enabled32x32.png"></a>';                                        
                                        }
                                        else
                                        {
                                            $sColumnValue = '<img alt="'.$sTranslatedMoveOneUp.'" src="'.APP_URL_ADMIN_IMAGES.'/icon-up-disabled32x32.png">';
                                        }


                                        //down arrow
                                        if ($bOrderOneDownAllowed)
                                        {
                                            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUP, ACTION_VALUE_ORDERONEDOWN); //move one down
                                            $sColumnValue.= '<a href="'.$sURL.'"><img alt="'.$sTranslatedMoveOneDown.'" src="'.APP_URL_ADMIN_IMAGES.'/icon-down-enabled32x32.png"></a>';                                        
                                        }
                                        else
                                        {
                                            $sColumnValue.= '<img alt="'.$sTranslatedMoveOneDown.'" src="'.APP_URL_ADMIN_IMAGES.'/icon-down-disabled32x32.png">';
                                        }                                    

                                    }

                                }
                                else//the fields we produce ourselves manually
                                {
                                    
                                    $sInternalModuleName = $objModel->getNameInternal();
                                    
                                    switch ($iColumnIndex) //which field
                                    {
                                        case $iColumnIndexIcon: //icon
                                            $sIconPath = APP_URL_ADMIN_IMAGES.'/icon-module32x32.png';  //default                              
                                            if (is_file(getPathModuleImages($sInternalModuleName).DIRECTORY_SEPARATOR.'icon-module32x32.png'))
                                                $sIconPath = getURLModuleImages($sInternalModuleName).'/icon-module32x32.png';     
                                            $sColumnValue = '<img src="'.$sIconPath.'" alt="'.'">';
                                            break;
                                        // case $iColumnIndexNameTranslated: //translated name
                                        //     if (file_exists(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sInternalModuleName))
                                        //         $sColumnValue = transm($sInternalModuleName, 'cmsmodulelist_modulename',$sInternalModuleName);
                                        //     else
                                        //         $sColumnValue = '&nbsp;';
                                        //     break;
                                        case $iColumnIndexDirExists: //directory exists
                                            if (file_exists(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sInternalModuleName))
                                            {
                                                $sColumnValue = '<img alt="'.transcms('boolean_yes', 'yes').'" src="'.APP_URL_ADMIN_IMAGES.'/icon-checked-true32x32.png">&nbsp;';
                                                $sColumnValue.= transm(APP_ADMIN_CURRENTMODULE, 'cmsmodulelist_moduleexists_folder_yes', 'folder exists');
                                            }
                                            else
                                            {
                                                $sColumnValue = '<img alt="'.transcms('boolean_no', 'no').'" src="'.APP_URL_ADMIN_IMAGES.'/icon-checked-false32x32.png">&nbsp;';
                                                $sColumnValue.= transm(APP_ADMIN_CURRENTMODULE, 'cmsmodulelist_moduleexists_folder_no', 'folder missing');
                                            }
                                            $sColumnValue.= '<br>';
                                            if (file_exists(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sInternalModuleName.DIRECTORY_SEPARATOR.'index.php'))
                                            {
                                                $sColumnValue.= '<img alt="'.transcms('boolean_yes', 'yes').'" src="'.APP_URL_ADMIN_IMAGES.'/icon-checked-true32x32.png">&nbsp;';
                                                $sColumnValue.= transm(APP_ADMIN_CURRENTMODULE, 'cmsmodulelist_moduleexists_index_yes', 'index file exists');                                                
                                            }
                                            else
                                            {
                                                $sColumnValue.= '<img alt="'.transcms('boolean_no', 'no').'" src="'.APP_URL_ADMIN_IMAGES.'/icon-checked-false32x32.png">&nbsp;';
                                                $sColumnValue.= transm(APP_ADMIN_CURRENTMODULE, 'cmsmodulelist_moduleexists_index_no', 'index file missing');                                                
                                            }
                                            break; 
                                        case $iColumnIndexSettings: //settings
                                            //$sModuleLongName = '\\dr\\modules\\'.$sInternalModuleName.'\\'.$sInternalModuleName;
                                            $sModuleLongName = getModuleFullNamespaceClass($sInternalModuleName);
                                            $objModule = new $sModuleLongName();     


                                            if ($objModule->getURLSettingsCMS())                        
                                                $sColumnValue.= '<a href="'.addVariableToURL($objModule->getURLSettingsCMS(), ACTION_VARIABLE_RETURNURL, getURLThisScript()).'" style="display:block">settings</a>';
                                                
                                            if ($objModule->getURLSupport())                        
                                                $sColumnValue.= '<a href="'.$objModule->getURLSupport().'" style="display:block" target="_blank">support</a>';

                                            if ($objModule->getURLAuthor())                        
                                                $sColumnValue.= '<a href="'.$objModule->getURLAuthor().'" style="display:block" target="_blank">website</a>';


                                            if (!$sColumnValue)
                                                $sColumnValue = '&nbsp;';

                                            break;
                                    }
                                     
                                     
                                }
                                ?>
                                    <td class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnValue ?></td>
                                <?php
                            }
                        ?>
                        <td>
                            <?php 
                            /*
                                if (isset($sURLDetailPage) && $bEditAllowed)
                                {
                                    ?><a href="<?php if (isset($sURLDetailPage)) { echo $sURLDetailPage.'?'.ACTION_VARIABLE_ID.'='.$objModel->getID(); } ?>"><img src="<?php echo APP_URL_ADMIN_IMAGES.DIRECTORY_SEPARATOR;?>icon-edit32x32.png"></a><?php
                                }
                                else
                                    echo '&nbsp;';   
                             * */
                                                              
                            ?>
                           <?php 
                            
                                
                                //checkout
                                if ($bRecordCheckedOut)
                                    echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-checkout-locked32x32.png" alt="'.transcms('recordlist_record_checkedout', 'Record CHECKED OUT by [source], not available for editing', 'source', $objModel->getCheckoutSource()) .'">';

                                
                                //lock
                                if ($bRecordLocked)
                                    echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-lock-closed32x32.png" alt="'.transcms('recordlist_record_locked', 'Record LOCKED by [source], not available for editing','source', $objModel->getLockedSource()).'">';
                               
                                
                                //edit-icon
                                if (isset($sURLDetailPage) && $bEditAllowedThisRecord)
                                {   
                                    
                                    if (isset($sURLDetailPage))
                                    {
                                        if ($objModel->getTableUseRandomIDAsPrimaryKey())
                                            echo '<a href="'.$sURLDetailPage.'?'.ACTION_VARIABLE_UNIQUEID.'='.$objModel->getRandomID().'">'; //I choose not to use addvariableToID() because of speed
                                        
                                        if ($objModel->getTableUseIDField())
                                            echo '<a href="'.$sURLDetailPage.'?'.ACTION_VARIABLE_ID.'='.$objModel->getID().'">'; //I choose not to use addvariableToID() because of speed
                                    }
                                    echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-edit32x32.png" alt="'.$sTranslatedEdit.'">';
                                    if (isset($sURLDetailPage))
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
    </div>

    <?php   
        //==== NO RECORDS? ====
        if ($objModel->count() == 0)
        {
            echo '<center>';
            echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-alert-grey128x128.png"><br>';
            echo transcms('message_noitemstodisplay','[ no items to display ]');
            echo '<br>';
            echo '</center>';
        }
    ?>    
    
    <?php
    
    //=========== BULK ACTIONS ========

    if (isset($objSelectBulkActions))
    {
        ?>
            <div class="overview_bulkactions">
                <?php echo transcms('overview_bulkactions', 'Bulk actions') ?>
                <?php echo $objSelectBulkActions->renderHTMLNode(); ?>
                <input type="button" value="<?php echo transcms('overview_bulkactions_button_execute', 'execute on checked items');?>" onclick="confirmBulkAction('<?php echo $objSelectBulkActions->getID();?>')" class="button_normal">
            </div>
        <?php
    }
    ?>    
</form>   



<div class="overview_paginator">
<?php
//       $objPaginator->setCurrentPage(1);

       $objUL = $objPaginator->generateHTMLList();
       $objUL->setClass('paginator');
       echo $objUL->renderHTMLNode();
?>
</div>               
                                      