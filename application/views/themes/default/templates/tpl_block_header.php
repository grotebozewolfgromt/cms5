<?php 
    use dr\classes\dom\tag\A;
    use dr\modules\Mod_Sys_Settings\models\TSysCMSMenu;
?>

<div class="header">
        
    <div class="header-menuicon">
        <a href="#" onclick="event.preventDefault(); toggleHamburgerMenu();">
            <svg fill="currentColor" stroke="currentColor" height="32px" id="Layer_1" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2  S29.104,22,28,22z"/></svg>
        </a>
    </div>

    <div class="header-logo">
        <?php echo APP_APPLICATIONNAME; ?>
    </div>

    <div class="header-search">
        <!-- search -->
    </div>

    <div class="header-shortcuts">
        <?php

            //==== SHOW MENU
            // starttest('menucache');

            //declare + init
            $bBuildCacheToolbar = true;
            $sSAKToolbarCache = '';//Session Array Key for caching menu in $_SESSION[SESSIONARRAYKEY_CACHE]

            if (isset($objAuthenticationSystem)) //only a menu when a user is authenticated
            {
                $sSAKToolbarCache = 'tpl_block_header_toolbar_'.$objAuthenticationSystem->getUsers()->getUserRoleID();

                //==== SHOULD RENEW CACHE? ====
                if (
                    isset($_SESSION[SESSIONARRAYKEY_CACHE][$sSAKToolbarCache]['timestamp'])
                    &&
                    isset($_SESSION[SESSIONARRAYKEY_CACHE][$sSAKToolbarCache]['cacheddata'])
                    )
                {
                    $bBuildCacheToolbar = ($_SESSION[SESSIONARRAYKEY_CACHE][$sSAKToolbarCache]['timestamp'] < time() - HOUR_IN_SECS);//1 hour cache
                }


                // $bBuildCacheToolbar = true; //uncomment for debugging


                //==== CREATE NEW CACHE ====
                if($bBuildCacheToolbar) //invalid cache
                {
                    $sRenderedMenu = '';
                    $objAHref = null;
                    // tracepoint('old cache, so create new cache');

                    $objMenu = new TSysCMSMenu();
                    $objMenu->where(TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, true);                    
                    $objMenu->orderBy(TSysCMSMenu::FIELD_POSITION, SORT_ORDER_ASCENDING);
                    $objMenu->limit(100);
                    $objMenu->loadFromDB();
                    while ($objMenu->next())
                    {
                        $objA = new A();

                        //link to controller or custom url?
                        if ($objMenu->getURL() !== '')//custom url overwrites module + controller
                        {
                            $objA->setHref($objMenu->getURL());
                        }
                        else
                        {
                            if ($objMenu->getController() !== '') //controller exists
                                $objA->setHref(getURLModule($objMenu->getModuleNameInternal()).'/'.$objMenu->getController()); //call controller
                            else //no controller
                                $objA->setHref(getURLModule($objMenu->getModuleNameInternal())); //call index.php
                        }

                        if ($objMenu->getOpenNewTab())
                            $objA->setTarget();
                        $objA->setInnerHTML($objMenu->getSVGIcon());
                        $objA->setTitle($objMenu->getNameDefault());
                        
                        $sRenderedMenu.= $objA->render();
                    }
                        

                    $_SESSION[SESSIONARRAYKEY_CACHE][$sSAKToolbarCache]['timestamp'] = time();
                    $_SESSION[SESSIONARRAYKEY_CACHE][$sSAKToolbarCache]['cacheddata'] = $sRenderedMenu;

                    echo $sRenderedMenu;
                }
                else //retrieve from cache
                {
                    // tracepoint('retrieve from cache');

                    echo $_SESSION[SESSIONARRAYKEY_CACHE][$sSAKToolbarCache]['cacheddata'];
                }

            }

            unset($sSAKToolbarCache); //we don't need it anymore and don't want it to be available for other scripts
            unset($bBuildCacheToolbar); //we don't need it anymore and don't want it to be available for other scripts
            // stoptest('menucache');

        ?>
    </div>    

    <?php 
    if (APP_DEMOMODE)
    {
        echo '<div class="headerdemo">';
        echo '<b>'.transcms('headerdemo_message_thisisdemo','This is a demo version. Certain functionality is disabled and data is reset periodically').'</b><br>';
        echo '</div>';
    }
?>
</div> <!-- end header -->
