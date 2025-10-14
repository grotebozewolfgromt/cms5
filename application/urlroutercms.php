<?php
/**
 * url router
 * controller instantiator for CMS5
 * 
 * this is deliberately NOT a class for the sake of speed (OOP and PHP are not good friends)
 * 
 * I also want to include AS LITTLE libraries as possible for the sake of speed
 * 
 * @author Dennis Renirie
 * 18 apr 2024: urlrouter.php: supports instantiating controllers from cms global controller directory if including .php file doesn't exist in root
 */


//session started in bootstrap
include_once 'bootstrap.php';

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_inet.php');

$sURLPath = '';
$arrURLPath = array();
$iCountURLPathLength = 0;
$sModule = '';
$sController = '';
// $sControllerPath = '';
$sControllerClass = '';
$bIsModule = false; //is cms or module
// $bIsWebcomponent = false; //is webcomponent? (php counterpart of js webcomponent)

//==== inspect the url  on length
//and determine the module and controller
$sURLPath = ltrimLiteral(APP_URLTHISSCRIPT, APP_URL_ADMIN.'/'); //APP_URL_ADMIN and getURLThisScript() should be the same for the first part
$sURLPath = explode('?', $sURLPath)[0]; //strip parameters
$arrURLPath = explode('/', $sURLPath);
$iCountURLPathLength = count($arrURLPath);


switch ($iCountURLPathLength)
{
    case 1: //controller in root (this includes the index that is an empty element in the array)
        $sController = $arrURLPath[0];
        $bIsModule = false;
        break;
    // case 2: //web component?
    //     if (APP_ADMIN_WEBCOMPONENTSDIR === $arrURLPath[0])
    //     {
    //         $bIsWebcomponent = true;
    //         $sController = $arrURLPath[1];
    //     }
    //     break;
    case 3: //controller in module
        if (APP_ADMIN_MODULESDIR === $arrURLPath[0])
        {
            $sController = $arrURLPath[2];
            $sModule = $arrURLPath[1];
            $bIsModule = true;
        }
        break;
    default:
        include APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_404.php';
        die();
}

//==== WEB COMPONENT?




//=========================== INDEX


//index cms
if (($sController == '') && ($sModule == ''))
{
    include 'index.php';
    die();
}

//index of module
if (($sController == '') && ($sModule != ''))
{
    include APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModule.DIRECTORY_SEPARATOR.'index.php';
    die();
}

//========================== INSTALLER?
if (($sController == 'install') && ($sModule == ''))
{
    if (is_dir(APP_PATH_CMS_INSTALLER))
    {
        header('Location: '.APP_URL_ADMIN_INSTALLERSCRIPT);
    }
    else
    {
        echo 'The installer does not exist (anymore).';
        logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'installer directory does not exist');
    }

    die();
}
else
{
    include_once 'bootstrap_admin.php';
}

//=========================== WEBCOMPONENT?
// if ($bIsWebcomponent)
// {
//     $sControllerClass = '';
//     $sController = preg_replace( '/[^abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_]/', '', $sController ); //filter for security reasons to prevent things like directory traversal   
//     $sControllerClass = 'dr\\classes\\dom\\tag\\webcomponents\\'.$sController;
//     $objController = new $sControllerClass();     
//     die();    
// }

//=========================== MODULE?
if (!$bIsModule) //NOT MODULE: then it is CMS controller
{
    $sControllerClass = '';
    $sController = preg_replace( '/[^abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_]/', '', $sController ); //filter for security reasons to prevent things like directory traversal   
    $sControllerClass = 'dr\\classes\\controllers\\'.$sController;
    $objController = new $sControllerClass();     
    die();    
}
else //IS MODDULE:  instantiate proper controller
{
    $sControllerClass = '';
    $sController = preg_replace( '/[^abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_]/', '', $sController ); //filter for security reasons to prevent things like directory traversal   
    // $sController = filterBadCharsWhiteList($sController, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'); //filter for security reasons to prevent things like directory traversal
    $sModule = preg_replace( '/[^abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_]/', '', $sModule ); //filter for security reasons to prevent things like directory traversal   
    // $sModule = filterBadCharsWhiteList($sModule, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'); //filter for security reasons to prevent things like directory traversal
    $sControllerClass = 'dr\\modules\\'.$sModule.'\\controllers\\'.$sController;

    $objController = new $sControllerClass();
    die();
}



?>