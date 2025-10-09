
<?php

/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;
    use dr\modules\Mod_Sys_Modules\models\TSysModules;
    use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
    use dr\classes\controllers\TCRUDListController;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_Modules\Mod_Sys_Modules;

// include_once APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php'; //getNextSortOrder
    

    $bAllowInstall = auth(CMS_CURRENTMODULE, Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, Mod_Sys_Modules::PERM_OP_INSTALL);
    $bAllowDelete = auth(CMS_CURRENTMODULE, Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, TModuleAbstract::PERM_OP_DELETE);
    $sTransInstall = transm(CMS_CURRENTMODULE, 'button_install', 'install');
    $sTransDelete = transm(CMS_CURRENTMODULE, 'button_delete', 'delete');  
    // $sURLThisScript = getURLThisScript();
?>
<form action="<?php echo getURLThisScript();?>" method="get" name="frmBulkActions" id="frmBulkActions">
     
    <div class="overview_table_background">
        <table class="overview_table">
            <thead>
                <tr> 
                    <th class="column-display-on-mobile">
                        <?php echo transm(CMS_CURRENTMODULE, 'column-display-on-mobile-header', 'Module') ?>
                    </th>        
                    <th class="column-display-on-desktop">
                        <?php echo transm(CMS_CURRENTMODULE, 'column-display-on-mobile-header', 'Module') ?>
                    </th>                          
                    <th>
                        <?php 
                            //=========== CREATE NEW ========
                            if ($bAllowInstall)
                            {
                                ?>                            
                                    <input type="button" onclick="window.location.href = '<?php echo $sURLDetailPage; ?>';" value="<?php echo transm(CMS_CURRENTMODULE, 'item_uploadmodule', 'upload'); ?>" class="button_normal">
                                <?php
                            }
                        ?>   
                    </th>                              
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($arrUninstalledModules as $sCurrUninstalledModule)
                    {                  
                        ?>
                        <tr>
                            <td class="column-display-on-mobile">
                                <?php echo $sCurrUninstalledModule; ?>
                            </td>             
                            <td class="column-display-on-desktop">
                                <?php echo $sCurrUninstalledModule ?>
                            </td>           
                            <td>

                                <?php 
                                
                                    if (isset($sURLDetailPage) && $bAllowInstall)
                                    {
                                        ?>
                                        <input type="button" onclick="window.location.href = '<?php echo addVariableToURL($sURLDetailPage, ACTION_VARIABLE_ID, $sCurrUninstalledModule); ?>';" value="<?php echo $sTransInstall ?>" class="button_normal">
                                        <?php
                                    }
                                    else
                                        echo '&nbsp;';   
                                    
                                    if ($bAllowDelete)
                                    {
                                        ?>
                                        <input type="button" onclick="window.location.href = '<?php echo addVariableToURL(addVariableToURL(APP_URLTHISSCRIPT, ACTION_VARIABLE_DELETE, '1'), ACTION_VARIABLE_ID, $sCurrUninstalledModule); ?>';" value="<?php echo $sTransDelete ?>" class="button_cancel">
                                        <?php                                    
                                    }
                                    else
                                        echo '&nbsp;';   
                                
                                    
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
        if (!$arrUninstalledModules)
        {
            echo '<center>';
            echo '<img src="'.APP_URL_CMS_IMAGES.'/icon-alert-grey128x128.png"><br>';
            echo transm(CMS_CURRENTMODULE, 'message_nomodulestodisplay','[ all available modules are installed ]');
            echo '<br>';
            echo '</center>';
        }
    ?>    
        
   
</form>   


