
<?php
/**
 * DEFAULT template: Overview of modules.
 * If you want to specify your own custom overview, add acopy of this template to 
 * the module directory and make sure you load that in the module-controller
 */
    use dr\classes\models\TSysModel;
    use dr\modules\Mod_Sys_Modules\models\TSysModules;
    use dr\modules\Mod_Sys_Localisation\models\TSysActiveLanguagesPerSite;
    use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
    
    
//    
//while($objModel->next())
//{
//    echo $objModel->get(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID).' '.$objModel->get(TSysLanguages::FIELD_LANGUAGE, TSysLanguages::getTable()).'<Br>';
//}
//$objModel->count();
//die('hello'.$objModel->count());
//    
//    
//    
//    
    
    

    $sTranslatedEdit = '';
    $sTranslatedEdit = transcms('recordlist_edit', 'edit record');
    $sTranslatedTranslated = '';
    $sTranslatedTranslated = transcms('recordlist_translated', 'Translated');
    $sTranslatedNeedsTranslation = '';
    $sTranslatedNeedsTranslation = transcms('recordlist_needstranslation', 'Needs translation');

    
        

?>



<form action="<?php echo APP_URLTHISSCRIPT;?>" method="get" name="frmBulkActions" id="frmBulkActions">
    
    <table class="overview_table">
        <thead>
            <tr> 
                <th>
                    <?php echo transcms('column-language', 'Language') ?>
                </th>
                <th>
                    <?php echo transcms('column-locale', 'Locale') ?>
                </th>
                <th>
                    <?php echo transcms('column-translated', 'Translated') ?>
                </th>
                <th>
                    &nbsp;<!-- space edit/lock/translate icon -->
                </th> 
           </tr>
        </thead>
        <tbody>
            <?php
               
                
            
                $bEditAllowed = false;
                $bEditAllowed = auth(APP_ADMIN_CURRENTMODULE, 'item', 'edit', 'allowed');
                                                               
               
               
                //show existing
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
                            <?php echo $objModel->get(TSysLanguages::FIELD_LANGUAGE, TSysLanguages::getTable()); ?>
                        </td>
                        <td>
                            <?php echo $objModel->get(TSysLanguages::FIELD_LOCALE, TSysLanguages::getTable()); ?>
                        </td>
                        <td>
                            <img alt="<?php echo $sTranslatedTranslated ?>" src="<?php echo APP_URL_ADMIN_IMAGES ?>/icon-checked-true32x32.png">
                        </td>
                        <td>
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
                                        echo '<a href="'.$sURLDetailPage.'?'.ACTION_VARIABLE_ID.'='.$objModel->getID().'">';
                                    echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-edit32x32.png" alt="'.$sTranslatedEdit.'">';
                                    if (isset($sURLDetailPage))
                                        echo '</a>';                                       
                                   
                                }
                                
                            ?>
                        </td>                                    
                    </tr>			
                    <?php
                }
                
                
                //show untranslated
                while ($objAvailableLanguages->next())
                {
                    ?>
                    <tr>
                        <td>
                            <?php echo $objAvailableLanguages->get(TSysLanguages::FIELD_LANGUAGE, TSysLanguages::getTable()); ?>
                        </td>
                        <td>
                            <?php echo $objAvailableLanguages->get(TSysLanguages::FIELD_LOCALE, TSysLanguages::getTable()); ?>
                        </td>
                        <td>
                            <img alt="<?php echo $sTranslatedTranslated ?>" src="<?php echo APP_URL_ADMIN_IMAGES ?>/icon-checked-false32x32.png">
                        </td>
                        <td>
                            <?php 
                            

                                
                                //edit-icon
                                if (isset($sURLDetailPage) && $bEditAllowedThisRecord)
                                {   
                                    if (isset($sURLDetailPage))
                                        echo '<a href="'.$sURLDetailPage.'?'.ACTION_VARIABLE_PARENTID.'='.$_GET[ACTION_VARIABLE_ID].'">';
                                    echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-edit32x32.png" alt="'.$sTranslatedEdit.'">';
                                    if (isset($sURLDetailPage))
                                        echo '</a>';                                       
                                   
                                }
                                
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
            echo '<img src="'.APP_URL_ADMIN_IMAGES.'/icon-alert-grey128x128.png"><br>';
            echo transcms('message_noitemstodisplay','[ no items to display ]');
            echo '<br>';
            echo '</center>';
        }
    ?>      
    

          