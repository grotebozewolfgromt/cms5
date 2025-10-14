<?php
/**
 * Block to generate a CMS menu on the left side of the screen.
 * 
 * REASONING + CACHING
 * ===================
 * I cache the menu for performance reasons to $_SESSION, 
 * so we don't have to load from the database action every page load (=SSLLLOOOWWW)
 * Technically/Esthetically speaking it is preferred to do caching and building menus in a MVC controller, 
 * but then every controller has to implement this individually for each controller in the CMS, 
 * or I need an extra layer of controllers only for the CMS (only to implement this 1 feature). 
 * I don't want that because to keep controller hierarchy as flat as possible to reduce load times.
 * Doing a bit of controller logic for the menu with a template 'block' allows every 'skin' to implement this block or not
 * 
 * BAREBONES CACHING
 * =================
 * For permance reasons I do caching 100% barebone, without fancy OOP controllers that can cache.
 * This code is loaded on EVERY page load, hence it needs to be super fast.
 * 
 * CACHING BASED ON ROLES
 * ======================
 * The cache is based on a role id. 
 * This way users with the same role refresh each others' cache on the same computer, 
 * thus keeping other responsive
 * 
 * 14 aug 2025: tpl_block_menu: created
 * 15 aug 2025: tpl_block_menu: caching implemented
 * 
 */
use dr\modules\Mod_Sys_Settings\models\TSysCMSMenu;


//==== SHOW MENU
// starttest('menucache');

//declare + init
$bBuildCacheMenu = true;
$sSAKMenuCache = '';//Session Array Key for caching menu in $_SESSION[SESSIONARRAYKEY_CACHE]

if (isset($objAuthenticationSystem)) //only a menu when a user is authenticated
{
    $sSAKMenuCache = 'tpl_block_menu_'.$objAuthenticationSystem->getUsers()->getUserRoleID();

    //==== SHOULD RENEW CACHE? ====
    if (
        isset($_SESSION[SESSIONARRAYKEY_CACHE][$sSAKMenuCache]['timestamp'])
        &&
        isset($_SESSION[SESSIONARRAYKEY_CACHE][$sSAKMenuCache]['cacheddata'])
        )
    {
        $bBuildCacheMenu = ($_SESSION[SESSIONARRAYKEY_CACHE][$sSAKMenuCache]['timestamp'] < time() - HOUR_IN_SECS);//1 hour cache
    }

    // $bBuildCacheMenu = true; //uncomment for debugging

    //==== CREATE NEW CACHE ====
    if($bBuildCacheMenu) //invalid cache
    {
        // tracepoint('old cache, so create new cache');
        $sRenderedMenu = '';

        $objMenu = new TSysCMSMenu();
        $objMenu->where(TSysCMSMenu::FIELD_ISVISIBLEMENU, true);                    
        $objMenu->orderBy(TSysCMSMenu::FIELD_POSITION, SORT_ORDER_ASCENDING);
        $objMenu->limit(1000);
        $objMenu->loadFromDB();
        $objUL = $objMenu->generateHTMLUlTree();
        $objUL->setClass('leftcolumn_modules');
        $sRenderedMenu = $objUL->render();
        $_SESSION[SESSIONARRAYKEY_CACHE][$sSAKMenuCache]['timestamp'] = time();
        $_SESSION[SESSIONARRAYKEY_CACHE][$sSAKMenuCache]['cacheddata'] = $sRenderedMenu;

        echo $sRenderedMenu;
    }
    else //retrieve from cache
    {
        // tracepoint('retrieve from cache');

        echo $_SESSION[SESSIONARRAYKEY_CACHE][$sSAKMenuCache]['cacheddata'];
    }

}

unset($sSAKMenuCache); //we don't need it anymore and don't want it to be available for other scripts
unset($bBuildCacheMenu); //we don't need it anymore and don't want it to be available for other scripts
// stoptest('menucache');


?> 
        <h1><?php echo transcms('skin_loggedin_menu_section_system_name','System') ?></h1>
        <ul class="leftcolumn_modules">                    
            <li>
                <ul>
                    <li>
                        <a href="<?php echo getURLCMSDashboard(); ?>">
                            <svg class="" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>

                            <!-- <img src="<?php echo APP_URL_ADMIN_IMAGES; ?>/icon-home.png" alt="home"> -->
                            <?php echo transcms('menuitem_dashboard', 'Dashboard');?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); toggleDarkLightMode()">
                            <!-- <img src="<?php echo APP_URL_ADMIN_IMAGES; ?>/icon-darkmode-dark512x512.png" alt="dark mode"> -->
                            <svg class="" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                            </svg>
                            <?php echo transcms('menuitem_darkmode', 'Dark/light Mode');?>
                        </a>                    
                    </li>
                    <li>
                    <?php
                        if (auth(AUTH_MODULE_CMS, AUTH_CATEGORY_SYSSETTINGS, AUTH_OPERATION_VIEW))
                        {
                            ?>
                            <a href="<?php echo getURLCMSSettings(); ?>">
                                <!-- <img src="<?php echo APP_URL_ADMIN_IMAGES; ?>/icon-settings.png" alt="settings"> -->
                                <svg class="" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <?php echo transcms('menuitem_settings', 'Settings');?>
                                </a>
                            <?php
                        }
                    ?>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); confirmLogout();">
                            <!-- <img src="<?php echo APP_URL_ADMIN_IMAGES; ?>/icon-logout.png" alt="logout"> -->
                            <svg class="" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                            </svg>                  
                            <?php echo transcms('menuitem_logout', 'Log out');?>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>