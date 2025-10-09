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
        
    ?>
                <?php
                                
                    //value of te ID field (because getID() doesn't always exist, it can be getRandomID() or getUniqueID())
                    $iIDValue = 0; //default

                    $bEditAllowed = false;
                    $bEditAllowed = auth(CMS_CURRENTMODULE, $objCRUD->getAuthorisationCategory(), AUTH_OPERATION_CHANGE);
                                                                
                    
                    //ONLY allow up and down if sorted on iOrder
                    $bOrderOneUpDownAllowed = false;
                    $arrQBSortItem = array();
                    if (count($arrQBSort) > 0) 
                    {
                        $arrQBSortItem = $arrQBSort[0]; ///we only have to know if the first column in QBSort is iOrder (more columns doesn't matter, because if it isn't the first sort column, you don't see anything of moving up or down )
                        if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == TSysModel::FIELD_POSITION) //is actually sorted on iOrder?
                        {
                            if (auth(CMS_CURRENTMODULE, $objCRUD->getAuthorisationCategory(), AUTH_OPERATION_CHANGEPOSITION)) //if also allowed by authentication, then it is allowed to show
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
                                    $sSortOrder = '';
                                    
                                    foreach($arrQBSort as $arrQBSortItem) //technically there can be multiple rows sorted in model, although not supported (yet) in GUI, because via the url is currently only sort column passed
                                    {
                                        if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == $sColumnName) //is actually sorted on this column in database?
                                            $sSortOrder = $arrQBSortItem[TSysModel::QB_SORTINDEX_ORDER];
                                    }                            
                            
                                    switch ($iColType)
                                    {
                                            case TP_DATETIME:                                                
                                                    $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getDateTimeAsString(); 
                                                    break;
                                            case TP_BOOL:  
                                                    if ($objModel->get($sColumnName, $sTableName, true))
                                                        $sColumnValue = '<svg class="iconchangefill" viewBox="6 6 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M10.5858 13.4142L7.75735 10.5858L6.34314 12L10.5858 16.2427L17.6568 9.1716L16.2426 7.75739L10.5858 13.4142Z" fill="currentColor"/></svg>';
                                                    else
                                                        $sColumnValue = '&nbsp;';
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
                                            $sColumnValue = '<a href="'.$sURL.'"><svg class="iconchangefill" alt="'.$sTranslatedMoveOneUp.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,64.5 64.5,256.5 176.5,256.5 176.5,448.5 336.5,448.5 336.5,256.5 448.5,256.5 "/></svg></a>';                                        
                                        }
                                        else
                                        {
                                            $sColumnValue = '<svg class="iconchangefill" style="opacity: 0.5" alt="'.$sTranslatedMoveOneUp.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,64.5 64.5,256.5 176.5,256.5 176.5,448.5 336.5,448.5 336.5,256.5 448.5,256.5 "/></svg>';
                                        }
                                        
                                        
                                        //down arrow
                                        if ($bOrderOneDownAllowed)
                                        {
                                            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUP, ACTION_VALUE_ORDERONEDOWN); //move one down
                                            $sColumnValue.= '<a href="'.$sURL.'"><svg class="iconchangefill" alt="'.$sTranslatedMoveOneDown.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,448.5 448.5,256.5 336.5,256.5 336.5,64.5 176.5,64.5 176.5,256.5 64.5,256.5 "/></svg></a>';                                        
                                        }
                                        else
                                        {
                                            $sColumnValue.= '<svg class="iconchangefill" style="opacity: 0.5" alt="'.$sTranslatedMoveOneDown.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,448.5 448.5,256.5 336.5,256.5 336.5,64.5 176.5,64.5 176.5,256.5 64.5,256.5 "/></svg>';
                                        }                                    
        
                                    }   
                                    
                                    ?>
                                        <td  class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnValue ?></td>
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
                                        $sIconNormalId = 'imgEdit';//default --> gets replaced later
                                        $sIconSpinnerId = 'imgSpinner';//default --> gets replaced later
                                        
                                        if (isset($sURLDetailPage))
                                        {
                                            if ($objModel->getTableUseRandomIDAsPrimaryKey())
                                            {
                                                $sIconNormalId.= $objModel->getRandomID();
                                                $sIconSpinnerId.= $objModel->getRandomID();                                                
                                                echo '<button onmousedown="toggleSpinnerIcon(\''.$sIconNormalId.'\',\''.$sIconSpinnerId.'\',true); location.href=\''.addVariableToURL($sURLDetailPage, ACTION_VARIABLE_UNIQUEID, $objModel->getRandomID()).'\';"  onclick="return false">';                                                 
                                            }
                                            
                                            if ($objModel->getTableUseIDField())
                                            {
                                                $sIconNormalId.= $iIDValue;
                                                $sIconSpinnerId.= $iIDValue;
                                                echo '<button onmousedown="toggleSpinnerIcon(\''.$sIconNormalId.'\',\''.$sIconSpinnerId.'\',true); location.href=\''.addVariableToURL($sURLDetailPage, ACTION_VARIABLE_ID, $iIDValue).'\'" onclick="return false">';                                                
                                            }
                                        }

                                        echo '<svg id="'.$sIconNormalId.'" class="iconchangefill" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M11.6768 4.38395L15.6128 8.31995L7.57485 16.3586C7.36976 16.5636 7.12302 16.7212 6.85215 16.821L6.68687 16.8739L2.6319 17.9798C2.28531 18.0743 1.96524 17.7857 2.00279 17.4452L2.01796 17.3658L3.12386 13.3109C3.20017 13.031 3.33624 12.7718 3.52191 12.5508L3.63917 12.4229L11.6768 4.38395ZM13.245 2.81706C14.3318 1.73025 16.0939 1.73025 17.1807 2.81706C18.2222 3.85858 18.2656 5.52026 17.3109 6.61346L17.1807 6.75273L16.3198 7.61295L12.3838 3.67695L13.245 2.81706Z" /></svg>';
                                        echo '<svg id="'.$sIconSpinnerId.'" class="iconchangefill spinner" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg"><path d="M41.9 23.9c-.3-6.1-4-11.8-9.5-14.4-6-2.7-13.3-1.6-18.3 2.6-4.8 4-7 10.5-5.6 16.6 1.3 6 6 10.9 11.9 12.5 7.1 2 13.6-1.4 17.6-7.2-3.6 4.8-9.1 8-15.2 6.9-6.1-1.1-11.1-5.7-12.5-11.7-1.5-6.4 1.5-13.1 7.2-16.4 5.9-3.4 14.2-2.1 18.1 3.7 1 1.4 1.7 3.1 2 4.8.3 1.4.2 2.9.4 4.3.2 1.3 1.3 3 2.8 2.1 1.3-.8 1.2-2.5 1.1-3.8 0-.4.1.7 0 0z"></path></svg>';

                                        if (isset($sURLDetailPage))
                                            echo '</button>';  
                                                                            
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
