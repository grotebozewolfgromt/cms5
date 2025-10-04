<?php
/**
 * In this library all CMS specific functions for CMS5
 *
 * 27 april 2019: created
 * @author Dennis Renirie
 */


/**
 * translate originaltext for CMS
 * It uses the languagefiles of the cms
 * 
 * if language content not found, it returns the $sOriginalText
 * @param string $sUniqueKey 
 * @param string $sDefaultEnglishTranslation
 */ 
function transcms($sUniqueKey, $sDefaultEnglishTranslation = '', $sVariable1 = '', $sValue1 = '', $sVariable2 = '', $sValue2 = '', $sVariable3 = '', $sValue3 = '')
{
    global $objTranslationCMS;
// vardump($objTranslationCMS);   
    if ($objTranslationCMS)
    {          
        return $objTranslationCMS->translate($sUniqueKey, $sDefaultEnglishTranslation, $sVariable1, $sValue1, $sVariable2, $sValue2, $sVariable3, $sValue3);
    }
    else
    {
    	if ($sDefaultEnglishTranslation == '')    		
       	 	return $sUniqueKey;
    	else
    		return $sDefaultEnglishTranslation;
    }    
}


/**
 * return permissions array
 * this function is the same as a TModuleAbstract->getPermissions(),
 * but instead of a module a applies to the CMS specific 
 *
 * @return array
 */
function getPermissionsCMS()
{
    return array(
        AUTH_CATEGORY_SYSSETTINGS => array (  
                                            AUTH_OPERATION_VIEW
                                        ),
        AUTH_CATEGORY_SYSSITES => array(
                                            AUTH_OPERATION_SYSSITES_VISIBILITY,
                                            AUTH_OPERATION_SYSSITES_SWITCH
                                        ),
        AUTH_CATEGORY_PAGEBUILDER => array (  
                                            AUTH_OPERATION_DELETE,
                                            AUTH_OPERATION_CREATE,
                                            AUTH_OPERATION_CHANGE,
                                            AUTH_OPERATION_VIEW
                                        ),
        AUTH_CATEGORY_UPLOADFILEMANAGER => array (
                                            AUTH_OPERATION_UPLOADFILEMANAGER_ACCESS,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_UPLOADFILES,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_GODIRUP,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_GODIRDOWN,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_CREATEDIR,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_DELDIR,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_DELFILE,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_DELDIR_OWN,
                                            AUTH_OPERATION_UPLOADFILEMANAGER_DELFILE_OWN,
                                        )
                                        
        );   
}

/**
 * render templates for an "access denied message"
 *
 * @return void
 */
function showAccessDenied($sExtraMessage = '')
{
    global $objAuthenticationSystem;

    $sUser = '';
    if ($objAuthenticationSystem)
        if ($objAuthenticationSystem->getUsers())
            $sUser = $objAuthenticationSystem->getUsers()->getUsername();
    logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'showAccessDenied(): showed access denied to user with message: "'.$sExtraMessage.'"', $sUser);

    header('HTTP/1.0 401 Unauthorized');
    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_accessdenied.php', get_defined_vars()); 
}

/**
 * render templates for an "404 not found"
 *
 * @return void
 */
function show404()
{
    http_response_code(404);
    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_404.php', get_defined_vars()); 
}



?>
