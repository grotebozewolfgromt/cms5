<?php
namespace dr\classes\controllers;

use dr\classes\types\TDateTime;
use dr\classes\locale\TLocalisation;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * WARNING
 * This is not a controller in the traditional sense, it has some slight differences
 * 
 * DIRECTORY STRUCTURE
 * Root upload dir
 *       |---module dir (dir name requested from module)
 *             |---module subdir (i.e. all uploads for a blog post)
 *                   |---module subsub dir (user created directory)
 *                               |---[...] (user created directories)
 * 
 * 
 */
class uploadfilemanager
{
   //url parameters
   const ACTION_VARIABLE_SHOWFILELIST = 'filelist';
   const ACTION_VALUE_SHOWFILELIST =  '1';
   const ACTION_VARIABLE_MODULE = 'mod'; //internal module name which requests the uploadfile manager
   const ACTION_VARIABLE_MODULESUBDIR = 'modsubdir'; //subdirectory in the module (i.e. each blog post has its own subdirectory in module directory 'blogs')
   const ACTION_VARIABLE_MODULESUBSUBDIR = 'modsubsubdir'; //subdirectory in the module subdir

   //fields in JSON renderFileList-response:
   //1 per response:
   const JSON_FILELISTRESPONSE_MESSAGE             = 'message'; //general message, like: "Please correct input"
   const JSON_FILELISTRESPONSE_ERRORCODE           = 'errorcode'; //errorcode 0 = OK. >0 look at message
   const JSON_FILELISTRESPONSE_TOTALFILECOUNT      = 'totalfilecount'; //number of files in directory
   const JSON_FILELISTRESPONSE_TOTALFILESIZEBYTES  = 'totalfilesizebytes'; //all file sizes in directory added
   const JSON_FILELISTRESPONSE_SUBDIRECTORY        = 'subdirectory'; //subdirectory in upload directory . The actual subdirectory that is shown to user. Directory can be filtered for directory traversal, then it falls back to root upload directory
   const JSON_FILELISTRESPONSE_FILES               = 'files'; //array of files
   //0-N per response, included in JSON_SAVERESPONSE_ERRORS array:
   const JSON_FILELISTRESPONSE_FIELD_ISDIRECTORY   = 'isdirectory'; //is it a directory? 1= directory 0=file
   const JSON_FILELISTRESPONSE_FIELD_FILENAME      = 'filename'; //name of the file
   const JSON_FILELISTRESPONSE_FIELD_SIZEBYTES     = 'sizebytes'; //file size in bytes
   const JSON_FILELISTRESPONSE_FIELD_DATEINTEGER   = 'dateinteger'; //date as integer (for sorting purposes)
   const JSON_FILELISTRESPONSE_FIELD_DATENICE      = 'datenice'; //date as string in a nice locale aware form
   

   //JSON error codes
   const JSONAK_RESPONSE_OK = 0;
   const JSON_ERRORCODE_UPLOADDIRNOTEXIST = 1;
   const JSON_ERRORCODE_SUBDIRECTORYDOESNOTEXISTS = 2; //a sub directory requested that does not exist
   const JSON_ERRORCODE_DIRECTORYTRAVERSALDETECTED = 3;
   const JSON_ERRORCODE_MODULENOTFOUND = 4;
   const JSON_ERRORCODE_USERNOTALLOWED_USEUPLOADFILEMANAGER = 5;
   const JSON_ERRORCODE_USERNOTALLOWED_USEMODULE = 6;
   const JSON_ERRORCODE_USERNOTALLOWED_GODIRUP = 7;
   const JSON_ERRORCODE_USERNOTALLOWED_GODIRDOWN = 8;
   const JSON_ERRORCODE_USERNOTALLOWED_UPLOADFILES = 9;
   const JSON_ERRORCODE_USERNOTALLOWED_CREATEDIR = 10;
   const JSON_ERRORCODE_USERNOTALLOWED_DELDIR = 11;
   const JSON_ERRORCODE_USERNOTALLOWED_DELFILE = 12;
   
   const JSONAK_RESPONSE_ERRORCODE_UNKNOWN = 999;
   
   /**
    * by default doesn't return anything
    *
    * STANDALONE CONTROLLER: this controller is called by url
    * SUPPORTIVE CONTROLLER: this controller is called in bootstrap to be available everywhere.
    **/
   public function __construct($bIsStandAloneController = true)
   {
      //ONLY when standalone controller: do all the actions
      //otherwise actions are called twice in standalone controller: 1x in the bootstrap, 1x by the url
      if ($bIsStandAloneController)
      {
         if (isset($_GET[uploadfilemanager::ACTION_VARIABLE_SHOWFILELIST]))
         {
            if ($_GET[uploadfilemanager::ACTION_VARIABLE_SHOWFILELIST] == uploadfilemanager::ACTION_VALUE_SHOWFILELIST)
            {
               $this->renderFileList();
               return;
            }
         }

         echo 'Well, that didn\'t work ...';
      }

      
   }

   /**
    * returns JSON
    *
    * @param string $sDirtySubDir 
   */
   public function renderFileList()
   {
      //declare
      $arrJSONResponse = array();
      $arrFSFiles = array(); //temp File System files to add to $arrJSONFiles
      $sSafeFileDir = '';//temp concatenated file directory to read files from
      $iTotalFileCount = 0; //number of files returned to user
      $iTotalFileSize = 0; //size in bytes
      global $objLocalisation;

      //==== init / default resonse
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_ok', 'Ok!');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSONAK_RESPONSE_OK;
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_TOTALFILECOUNT] = 0;
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_TOTALFILESIZEBYTES] = 0;
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_SUBDIRECTORY] = '';
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_FILES] = array();

         $sSafeFileDir = APP_PATH_UPLOADS_PUBLIC;

      //==== authorize user
         if (!$this->getAuthUseUploadFileManager())
            return;


      //==== existence uploaddir
         if (!$this->checkUploadDirExists($arrJSONResponse))
            return;


      //==== check module existence and modulesubdir
         if (!$this->checkModule($arrJSONResponse, $sSafeFileDir))
            return;

      //==== check subdir
         if (isset($_GET[uploadfilemanager::ACTION_VARIABLE_MODULESUBDIR]))
         {
            if (!$this->checkModuleSubDirExists($arrJSONResponse, $sSafeFileDir, $_GET[uploadfilemanager::ACTION_VARIABLE_MODULESUBDIR]))
               return;
         }

      //==== check subsubdir (this is a user created subdir)
         if (isset($_GET[uploadfilemanager::ACTION_VARIABLE_MODULESUBDIR]) && isset($_GET[uploadfilemanager::ACTION_VARIABLE_MODULESUBSUBDIR]))
         {
            if (!$this->getAuthGoDirDown())
               return;

            if (!$this->checkModuleSubDirExists($arrJSONResponse, $sSafeFileDir, $_GET[uploadfilemanager::ACTION_VARIABLE_MODULESUBSUBDIR]))
               return;
         }
         else
         {
            if (!$this->getAuthGoDirUp())
               return;
         }

      //==== read and return files in JSON
      $arrFSFiles = getFileFolderArray($sSafeFileDir); 
      if ($arrFSFiles)
      {
         foreach ($arrFSFiles as $sFileName)
         {
            $sFilePath = concatDirFileSafe($sSafeFileDir, $sFileName); //preventing a file name from causing directory traversal (shouldnt be able to happen, but still ... better safe than sorry)

            if (!startswith($sFileName, '.')) //skip hidden files/directories, this is how you hide porn :)
            {
               ++$iTotalFileCount;
               $iTotalFileSize += filesize($sFilePath);


               $arrTempJSONFiles = array(); //temp files in JSON format to add to $arrJSONResponse
               $arrTempJSONFiles[uploadfilemanager::JSON_FILELISTRESPONSE_FIELD_ISDIRECTORY] = boolToInt(is_dir($sFilePath));
               $arrTempJSONFiles[uploadfilemanager::JSON_FILELISTRESPONSE_FIELD_FILENAME] = $sFileName;
               $arrTempJSONFiles[uploadfilemanager::JSON_FILELISTRESPONSE_FIELD_SIZEBYTES] = filesize($sFilePath);
               $arrTempJSONFiles[uploadfilemanager::JSON_FILELISTRESPONSE_FIELD_DATEINTEGER] = filemtime($sFilePath);

               $objDateTime = new TDateTime(filemtime($sFilePath));
               $arrTempJSONFiles[uploadfilemanager::JSON_FILELISTRESPONSE_FIELD_DATENICE] = $objDateTime->getDateTimeAsString($objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT).' '.$objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT));

               $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_FILES][] = $arrTempJSONFiles;               
            }
         }
         
      }

      //==== update totals
      $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_TOTALFILECOUNT] = $iTotalFileCount;
      $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_TOTALFILESIZEBYTES] = $iTotalFileSize;
      $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_SUBDIRECTORY] = ''; //default: root
      // if ($sSafeModuleSubSubDir !== APP_PATH_UPLOADS_PUBLIC)
      //    $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_SUBDIRECTORY] = $sDirtySubDir; //by now it is considered "safe" instead "dirty", because the isDirectoryTraversal() would have error-returned already


      header(JSONAK_RESPONSE_HEADER);
      echo json_encode($arrJSONResponse);               
      return; //stop further execution to display the error             
   }


   /**
    * check if upload dir exists, otherwise return json error
    */
   private function checkUploadDirExists(&$arrJSONResponse)
   {
      if (!is_dir(APP_PATH_UPLOADS_PUBLIC))
      {
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_uploaddirdoesnotexist', 'The upload directory does not exist. Is it set correctly in the configuration file?');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_UPLOADDIRNOTEXIST;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error              
      }

      return true;
   }

   /**
    * check if module exists and if use has access, otherwise json error
    */
   private function checkModule(&$arrJSONResponse, &$sReturnSafeModuleDir)
   {
      global $objAuthenticationSystem;
      $bModuleFound = false;
      $sDirtyModuleName = '';
      $sSafeModuleName = '';
      

      if (isset($_GET[uploadfilemanager::ACTION_VARIABLE_MODULE])) //only if module name exists in url
      {
         $sDirtyModuleName = $_GET[uploadfilemanager::ACTION_VARIABLE_MODULE];

         //retrieve module name based on folders (=quicker than database)
         $arrModuleFolders = getModuleFolders();
         $iCountMods = count($arrModuleFolders);
         for ($iModIndex = 0; $iModIndex < $iCountMods; $iModIndex++)
         {

            //if module found
            if ($arrModuleFolders[$iModIndex] === $sDirtyModuleName)
            {
               $bModuleFound = true;
               $sSafeModuleName = $arrModuleFolders[$iModIndex];

               $sClassMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
               $objCurrMod = new $sClassMod;
               $sReturnSafeModuleDir = APP_PATH_UPLOADS_PUBLIC.DIRECTORY_SEPARATOR.$objCurrMod->getUploadFileManagerDir();

               $iModIndex = $iCountMods; //exit loop
            }            
         }      

      }    
      

      if (!$this->getAuthUseModule($sSafeModuleName))
      {            
         return false; //stop further execution to display the error                    
      }

      //generate error: module not found
      if (!$bModuleFound)
      {
         //log module not found
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: POSSIBLE ENUMERATION ATTEMPT. Module not found. User tried to use module "'.$sDirtyModuleName.'"', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: POSSIBLE ENUMERATION ATTEMPT. Module not found. User tried to use module "'.$sDirtyModuleName.'"', $objAuthenticationSystem->getUsers()->getUsername());


         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_modulenotfound', 'Directory does not exist'); //being deliberately super vague to prevent enumeration by attacker
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_MODULENOTFOUND;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error             
      }      

      return true;
   }

   /**
    * check if subdirectory in module directory exists, otherwise json error
    */
   private function checkModuleSubDirExists(&$arrJSONResponse, &$sReturnSafeSubDir, &$sDirtySubDir)
   {
      global $objAuthenticationSystem;

      if (isDirectoryTraversal($sReturnSafeSubDir, $sDirtySubDir))
      {        
         //log traversal attempt
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: DIRECTORY MANIPULATION/TRAVERSAL DETECTED!!! User tried to use directory "'.$sDirtySubDir.'"', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: DIRECTORY MANIPULATION/TRAVERSAL DETECTED!!! User tried to use directory "'.$sDirtySubDir.'"', $objAuthenticationSystem->getUsers()->getUsername());

         //create entry in floodlog
         $objFlood = new TSysCMSUsersFloodDetect();
         $objFlood->setUsername($objAuthenticationSystem->getUsers()->getUsername());
         $objFlood->setUsernameHashed($objAuthenticationSystem->getUsers()->getUsername());
         $objFlood->setDateAttempt(new TDateTime());
         $objFlood->setIP(getIPAddressClient());
         $objFlood->setFingerprintBrowser(getFingerprintBrowser());
         $objFlood->setUserAgent($_SERVER['HTTP_USER_AGENT']);
         $objFlood->setIsDirectoryTraversalAttempt(true);
         $objFlood->setNote('Directory traversal via uploadfilemanager for directory: '.$sDirtySubDir);
         $objFlood->saveToDB();

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_directorytraversaldetected', 'Directory does not exist');//being deliberately vague to not tip of a attacker!!!
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_DIRECTORYTRAVERSALDETECTED;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                            
      }       
      
      $sReturnSafeSubDir = concatDirFileSafe($sReturnSafeSubDir, $sDirtySubDir);

      return true;
   }
   

   /**
    * user authorized?
    */
   public function getAuthUseUploadFileManager()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_ACCESS))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_accessuploadfilemanager_usernotallowed', 'User not allowed to use this feature');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_USEUPLOADFILEMANAGER;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }

   /**
    * user authorized?
    */
   public function getAuthUseModule($sSafeModuleNameInternal)
   {
      global $objAuthenticationSystem;

      if (!auth($sSafeModuleNameInternal, AUTH_CATEGORY_MODULEACCESS, AUTH_OPERATION_MODULEACCESS))
      {
         //log module not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: POSSIBLE ENUMERATION ATTEMPT. User access module not allowed. User tried to use module "'.$sSafeModuleNameInternal.'"', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: POSSIBLE ENUMERATION ATTEMPT. User access module not allowed. User tried to use module "'.$sSafeModuleNameInternal.'"', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_accessmodule_usernotallowed', 'User not allowed to use this feature');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_USEMODULE;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }   

   /**
    * user authorized?
    */
   public function getAuthGoDirUp()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_GODIRUP))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to go up dir uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to go up dir uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_godirup_usernotallowed', 'User not allowed to change directory');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_GODIRUP;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }

   public function getAuthGoDirDown()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_GODIRDOWN))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to go down dir uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to go down dir uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_godirdown_usernotallowed', 'User not allowed to change directory');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_GODIRDOWN;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }


   public function getAuthCreateDir()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_CREATEDIR))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to create dir in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to create dir uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_createdir_usernotallowed', 'User not allowed to create directory');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_CREATEDIR;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }   

   public function getAuthUploadFiles()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_UPLOADFILES))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to upload files in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to upload files in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_uploadfiles_usernotallowed', 'User not allowed to upload files');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_UPLOADFILES;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }     


   public function getAuthDelDir()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_DELDIR))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to delete dir in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to delete dir in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());


         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_deletedirectory_usernotallowed', 'User not allowed to delete directories');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_DELDIR;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }        


   public function getAuthDelFile()
   {
      global $objAuthenticationSystem;

      if (!auth(AUTH_MODULE_CMS, AUTH_CATEGORY_UPLOADFILEMANAGER, AUTH_OPERATION_UPLOADFILEMANAGER_DELFILE))
      {
         //log not allowed
         logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to delete file in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());
         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: User access not allowed to delete file in uploadfilemanager.', $objAuthenticationSystem->getUsers()->getUsername());

         //create json error
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_MESSAGE] = transcms('tfileuploadmanagercontroller_error_deletefile_usernotallowed', 'User not allowed to delete file');
         $arrJSONResponse[uploadfilemanager::JSON_FILELISTRESPONSE_ERRORCODE] = uploadfilemanager::JSON_ERRORCODE_USERNOTALLOWED_DELFILE;

         header(JSONAK_RESPONSE_HEADER);
         echo json_encode($arrJSONResponse);               
         return false; //stop further execution to display the error                
      }
      return true;
   }           

   /**
    * renders HTML for fileuploadmanager
    */
   public function renderLayout()
   {
      $sURLJSONFileList = addVariableToURL(APP_URL_ADMIN.'/uploadfilemanager', uploadfilemanager::ACTION_VARIABLE_SHOWFILELIST, uploadfilemanager::ACTION_VALUE_SHOWFILELIST);

      echo '<script>';
      echo renderTemplate(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'uploadfilemanager.js', get_defined_vars());
      echo '</script>';

      echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_block_uploadfilemanager.php', get_defined_vars());
   }

}