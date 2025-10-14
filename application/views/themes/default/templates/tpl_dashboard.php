<?php
    use dr\modules\Mod_Sys_Modules\models\TSysModules;
    use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;    
 
?>

<h1><?php echo transcms('tpl_dashboard_h1_title', 'Welcome to [applicationname], [user]', 'applicationname', APP_APPLICATIONNAME, 'user', $objAuthenticationSystem->getUsers()->getUsernamePublic()); ?></h1>
<!-- <h2><?php echo transcms('tpl_dashboard_h2_modules', 'modules'); ?></h2> -->

Still a bit empty here, but imagine all kinds of fancy stuff ;)



<!-- <button onclick="document.getElementById('progressbar').classList.add('visible');">progresss</button> -->
<?php
    /*
    //display
    $arrKeys = array_keys($arrCats);
    foreach($arrKeys as $sCatName)
    {
        $arrMods = $arrCats[$sCatName];

        echo '<h3>'.$sCatName.'</h3>';
        echo '<div class="tilesenclosure">';

        foreach ($arrMods as $iIndexMod)
        {
            $objSysModulesDB->setRecordPointerToIndex($iIndexMod);

            $sIconPath = APP_URL_ADMIN_IMAGES.'/icon-module128x128.png';  //default                              
            if (is_file(getPathModuleImages($objSysModulesDB->getNameInternal()).DIRECTORY_SEPARATOR.'icon-module128x128.png'))
                $sIconPath = getURLModuleImages($objSysModulesDB->getNameInternal()).'/icon-module128x128.png'; 
            
            //$bModDirExists = file_exists(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$objSysModulesDB->getNameInternal());                                 
            
            ?>
            <div class="tilebox tileboxwithoutsub">
                <div class="tileboxinner">
                    <a href="<?php echo getURLModule($objSysModulesDB->getNameInternal()); ?>">
                        <div class="tileimage">
                            <img src="<?php echo $sIconPath ?>" alt="<?php echo str_replace('"', '', transm($objSysModulesDB->getNameInternal(), $objSysModulesDB->getNameInternal(), $objSysModulesDB->getNameDefault())); ?>">
                        </div>
                        <div class="titletitle">
                            <?php 
                                echo transm($objSysModulesDB->getNameInternal(), $objSysModulesDB->getNameInternal(), $objSysModulesDB->getNameDefault()); 
                            ?>
                        </div>
                    </a>
                </div>
            </div>
            <?php                                 
        }                                

        echo '</div>'; //end tilesenclosure
    }
                            
                        
//    $sIconPath = '';
//    foreach($arrSysModules as $sModule)
//    {
//        $sIconPath = APP_URL_ADMIN_IMAGES.'/icon-module16x16.png';                                
//        if (is_file(getPathModuleImages($sModule).DIRECTORY_SEPARATOR.'icon-module16x16.png'))
//            $sIconPath = getURLModuleImages($sModule).'/icon-module16x16.png';
//        echo '<img src="'.$sIconPath.'"><a href="'. getURLModule($sModule).'/index.php">'.transm($sModule, 'cmsmodulelist_modulename',$sModule).'</a><br>';
//    }
    */                  
?>

          
                                      