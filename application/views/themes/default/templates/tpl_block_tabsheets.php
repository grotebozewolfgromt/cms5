<?php
    //display tabsheets

    //declarations (for speed)
    $sTempTabUrl = '';
    $sTempTabPermCat = '';
    $sTempTabNameTrans = '';
    $sTempTabDescriptionTrans = '';
    $sTempLiClass = '';
    $bShowTab = false;

    if (count($arrTabsheets) > 1)//dont show tabs, if there is only one tab
    {
        ?>
        <div class="scrollable-tabs-container">
            <div class="left-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </div>
            <ul>
            <?php

            foreach($arrTabsheets as $arrTab)
            {
                $bShowTab = false; //default

                //cms tabs or module tabs?
                if (APP_ADMIN_CURRENTMODULE) //module
                {
                    $sTempTabUrl = $arrTab[0];
                    $sTempTabPermCat = $arrTab[1];
                    $sTempTabNameTrans = transm(APP_ADMIN_CURRENTMODULE, 'tabsheets_module_name_'.$arrTab[2],$arrTab[2]);
                    $sTempTabDescriptionTrans = transm(APP_ADMIN_CURRENTMODULE, 'tabsheets_module_explanation_'.$arrTab[3], $arrTab[3]);
                    $bShowTab = auth(APP_ADMIN_CURRENTMODULE, $sTempTabPermCat, AUTH_OPERATION_VIEW);
                }
                else
                {
                    $sTempTabUrl = $arrTab[0];
                    $sTempTabPermCat = $arrTab[1];
                    $sTempTabNameTrans = transcms('tabsheets_cms_name_'.$arrTab[2],$arrTab[2]); //possible name collision if tabs have the same names on multiple pages
                    $sTempTabDescriptionTrans = transcms('tabsheets_cms_explanation_'.$arrTab[3], $arrTab[3]); //possible name collision if tabs have the same names on multiple pages
                    $bShowTab = auth(AUTH_MODULE_CMS, $sTempTabPermCat, AUTH_OPERATION_VIEW);
                }


                if ($bShowTab)
                {
                    //if tabsheet is selected
                    $sTempLiClass = '';
                    
                    //@todo tab selection doesn't work when url parameter exists
                    if (str_ends_with(APP_URLTHISSCRIPT, $sTempTabUrl)) 
                        $sTempLiClass = ' class="active"';
                    echo '<li>';
                    echo '<a href="'.$sTempTabUrl.'" title="'.$sTempTabDescriptionTrans.'"'.$sTempLiClass.'>';                
                    echo $sTempTabNameTrans;
                    echo '</a>';
                    echo '</li>'."\n";                 
                }
            }
            ?>
            </ul>
            <div class="right-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
    
        </div>
        <?php        
    }
    
    //unset used vars prevent possible collisions with names later in scirpts
    unset($sTempTabUrl);
    unset($sTempTabPermCat);
    unset($sTempTabNameTrans);
    unset($sTempTabDescriptionTrans);
    unset($sTempLiClass);
    
?> 