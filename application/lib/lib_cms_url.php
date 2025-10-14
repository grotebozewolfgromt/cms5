<?php
/**
 * In this library urls of CMS5
 * NOT THE WEBSITES managed by CMS5
 * websites managed by CMS5 have their own custom lib_url
 * 
 * lookup urls so it is really easy to change locations of file
 * if you can use a global constant, do that!
 * but sometimes you need business logic over and over again to generate an url. 
 * for example: a weblog that is in the footer, on the left and on a separate page.
 * use 1 function to generate detail-pages of the weblog.
 *
 * 
 * EXAMPLE
 * function getURLPlaats($sPrettyUrlTitlePlaats)
 * {
 *      return APP_PATH_WWW.'/'.'plaats/'.$sPrettyUrlTitlePlaats.'';
 * }   
 *
 * 10 mei 2019 getModuleFromURL() bugfix, geen controle op directory
 * 12 dec 2021: lib_cms_url: getModuleFromURL() filter op illegale karakters
 * 18 apr 2024: lib_cms_url: getPathModuleTemplates() added
 * 
 * @author Dennis Renirie
 */

//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');
        

/**
 * return the name of the module by looking at the current url
 * 
 * @return string
 */
function getModuleFromURL()
{
    $arrDirsCurrURL = array();
    $arrBasePathURL = array();
    $sURL = '';
    $iCountBasePath = 0;
    $iCountDirsCurr = 0;
    $sModuleName = '';
    
    
    /***
     * WHAT AM I TRYING TO DO HERE?
     * example: 
     * if this is url: https://www.bedrijfsuitje.events/application/modules/Mod_Sys_Localisation/index.php
     * then this is basepath-url of modules: https://www.bedrijfsuitje.events/application/modules
     * in other words: I know that the string thing AFTER the 'modules' part is the name of the module
     * count module-basepath array = 5
     * cound van de url = 7
     * because i know the modulebasepath is part of the current url
     * i know that the index of the urlarray with the modulename = count of the modulebasepath = 5
     */
    
    
    $arrDirsCurrURL = explode('/', getURLThisScript()); //performance test show that explode is faster than substr() by a ratio 1 to 10
    $arrBasePathURL = explode('/', APP_URL_MODULES); //performance test show that explode is faster than substr() by a ratio 1 to 10
//tracepoint('gloinkasdf'.APP_URL_MODULES. ' -- '.getURLThisScript())          ;
    $iCountBasePath = count($arrBasePathURL);
    $iCountDirsCurr = count($arrDirsCurrURL);          
    

    if ($iCountDirsCurr > $iCountBasePath) //checken of the current directory uberhaupt wel groter is dan van de basepath, anders geen modulenaam teruggeven
    {
        //performance test show that this code is 8x faster on average
        $bStructureEqual = true;
        for ($iIndex = 0; $iIndex < $iCountBasePath; $iIndex++)
        {
           if ($arrDirsCurrURL[$iIndex] !=  $arrBasePathURL[$iIndex])
               $bStructureEqual = false;
        }

        if ($bStructureEqual)
            $sModuleName = $arrDirsCurrURL[$iCountBasePath];

        //faster than this code
//            if (stristr(getURLThisScript(), APP_URL_MODULES) != '')
//                $sModuleName = $arrDirsCurrURL[$iCountBasePath];
    }

    $sModuleName = filterBadCharsWhiteList($sModuleName, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'); //filter for security reasons to prevent things like directory traversal

    
    return $sModuleName;
}


function getURLCMSSettings()
{
    // return APP_URL_ADMIN.'/settings.php';
    return APP_URL_MODULES.'/Mod_Sys_Settings/';
}

function getURLCMSDashboard()
{
    return APP_URL_ADMIN.'/dashboard';
}

function getURLCMSCronjob()
{
    return APP_URL_ADMIN.'/cronjob.php?'.ACTION_VARIABLE_ID.'='.APP_CRONJOBID;
}

function getURLCMSLogin()
{
    return APP_URL_ADMIN.'/loginform';
}

function getPathModuleImages($sModuleName)
{
    return APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'images';
}




/**
 * return url of the images of a module
 */
function getURLModuleImages($sModuleName)
{
    return APP_URL_MODULES.'/'.$sModuleName.'/views/images';
}

function getPathModule($sModuleName)
{
    return APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName;
}

function getPathModuleConfigFile($sModuleName)
{
    return APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'config.php';
}

/**
 * return path of the templates of a module
 * 
 * @param string $sModuleName
 * @param bool $bIncludePostDirectorySeparator include DIRECTORY_SEPARATOR at the end of the path?
 */
function getPathModuleTemplates($sModuleName, $bIncludePostDirectorySeparator = false)
{
    $sPath = '';
    $sPath.= APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates';
    if ($bIncludePostDirectorySeparator)
        $sPath.= DIRECTORY_SEPARATOR;

    return $sPath; 
}

function getPathModuleCSS($sModuleName, $bIncludePostDirectorySeparator = false)
{
    $sPath = '';
    $sPath.= APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'css';
    if ($bIncludePostDirectorySeparator)
        $sPath.= DIRECTORY_SEPARATOR;

    return $sPath; 
}

function getPathModuleJS($sModuleName, $bIncludePostDirectorySeparator = false)
{
    $sPath = '';
    $sPath.= APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'js';
    if ($bIncludePostDirectorySeparator)
        $sPath.= DIRECTORY_SEPARATOR;

    return $sPath; 
}


function getURLModule($sModuleName)
{
    return APP_URL_MODULES.'/'.$sModuleName;
}

function getURLModuleCSS($sModuleName, $bIncludePostDirectorySeparator = false)
{
    $sURL = '';
    $sURL.= APP_URL_MODULES.'/'.$sModuleName.'/views/css';
    if ($bIncludePostDirectorySeparator)
        $sURL.= '/';

    return $sURL;     
}

function getURLModuleJS($sModuleName, $bIncludePostDirectorySeparator = false)
{
    $sURL = '';
    $sURL.= APP_URL_MODULES.'/'.$sModuleName.'/views/js';
    if ($bIncludePostDirectorySeparator)
        $sURL.= '/';

    return $sURL;     
}

function getURLPagebuilder($sModuleName)
{
    return APP_URL_MODULES.'/'.$sModuleName.'/pagebuilder';
}

function getURLPasswordRecoverEnterEmail()
{
    return APP_URL_ADMIN.'/passwordrecover_enteremail';
}

function getURLPasswordRecoverEnterNewPassword()
{
    return APP_URL_ADMIN.'/passwordrecover_enternewpassword';
}

function getURLCreateAccountEnterCredentials()
{
    return APP_URL_ADMIN.'/createaccount_entercredentials';
}

function getURLCreateAccountEmailConfirm()
{
    return APP_URL_ADMIN.'/createaccount_emailconfirmed';
}


?>
