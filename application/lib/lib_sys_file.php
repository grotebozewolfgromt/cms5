<?php
/**
 * In this library exist only file/url related functions
 * ook voor het genereren van bestandnamen etc.
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * 13 okt 09
 * ==========
 * -extractFileFromPath() en extractDirectoryFromPath aangepast met native PHP functies
 *
 * 16 mei 2010
 * ==========
 * -getFileNameWithoutExtension() aangepast, als file geen extensie dan filenaam teruggegeven
 *
 * 18 mei 2010
 * ==========
 * -saveToFile aangepast met PHP_EOL
 * 
 * 5 april 2019
 * =========-
 * -getFileFolderArray() extensions gaf bug
 * 
 * 28 okt 2020
 * ========
 * -lib_sys_file: uploadFilesRearrangeArray() added
 * -lib_sys_file: function tempdir() added
 * -lib_sys_file: function copyRecursive($sSource, $sDestination) added
 * -lib_sys_file: function renameRecursive($sSource, $sDestination) added
 * -lib_sys_file: function getFileFolderArray() performance improvement
 
 * 
 * 8 juli 2012: utf8_decode() voor readFromFileString
 * 11 juli 2012: saveToFile() bugfix, als utf8 was, dan nogmaals naar utf8 converten, nu wordt utf8 gedetecteerd
 * 11 JULI 2012: addToFile()  bugfix, als utf8 was, dan nogmaals naar utf8 converten, nu wordt utf8 gedetecteerd
 * 11 juli 2012: saveToFileString()bugfix, als utf8 was, dan nogmaals naar utf8 converten, nu wordt utf8 gedetecteerd
 * 11 juli 2012: loadFromFileString() detects and returns always a UTF8 string (before it didn't detect, and always converted)
 * 11 juli 2012: loadFromFile() detects and returns always a UTF8 array (before it didn't detect, and always converted)
 * 13 juli 2012: lib_file: saveToFileString() had een undefined variable, verangen door PHP_EOL
 * 2 mei 2014: lib_file: filterDirectorytraversal hier naar toe verhuisd
 * 2 mei 2014: lib_file: filterDirectorytraversal  bugfix: roep filter sql injection aan ipv filterDirectoryTraversal
 * 2 mei 2014: lib_file: filterDirectorytraversal heet nu: filterFileName()
 * 2 mei 2014: lib_file: filterDirectorytraversal heeft extra filter voor verkeerde bestandsnamen
 * 4 aug 2014: lib_file: getFileFolderArray() betere ondersteuning vooor geen teruggave
  * 4 apr 2014: lib_file: filterFileName vervangen, werkt nu met whitelist
  * 18 nov 2022: lib_file: filterForDirectoryTraversal() added
  * 18 nov 2022: lib_file: filterForDirectoryTraversal() improved
  * 19 nov 2022: lib_file: getParentDirectory() implemented not hardcoded directory separator
  * 30 janv 2025: lib_file: FIX: filterDirectoryTraversal() support for recursion
  * 30 janv 2025: lib_file: isDirectoryTraversal() added
  * 30 janv 2025: lib_file: concatDirFileSafe() added
  * 2 feb 2025: lib_file: isDirectoryTraversal() updated for empty param
  * 11 sept 2025: lib_file: isMimeImage() added
  * 18 sept 2025: lib_file: filterFileName() updated for directory traversal
  * 19 sept 2025: lib_file: added: getMimeTypeExtension(), getMimeTypeFile()
  * 25 sept 2025: lib_file: changed: generateUniqueFileName() with a far more intelligent algo to generate a unique filename
 *
 * @author Dennis Renirie
 */



//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_file.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');





/**
 * Converts a string to a valid UNIX filename and filters for directory traversal
 * 
 * @param $string The filename to be converted
 * @return $string The filename converted
 */
function filterFileName ($sFile) 
{
        return sanitizeWhitelist($sFile, WHITELIST_FILENAME);
}

/**
 * alias for filterFileName
 */
function sanitizeFileName($sString)
{
        return filterFileName($sString);
}

/**
 * sanatizes string filename or directory
 * filters filename or directory for unwanted characters
 * 
 * WARNING:
 * DO NOT USE THIS FUNCTION TO AVOID DIRECTORY TRAVERSAL, 
 * USE isDirectoryTraversal() OR concatDirFileSafe() INSTEAD
 * 
 * If there is some kind of hacky way to still get ../ somehow
 * which is not checked in this function, things go south
 * isDirectoryTraversal() checks for basepath, which is much safer
 * 
 *
 * example of directory traversal:
 *
 * $template = 'red.php';
 * $template = $_COOKIE['TEMPLATE'];
 * include ("/home/users/phpguru/templates/" . $template);
 *
 * An attack against this system could be to send the following HTTP request:
 * GET /vulnerable.php HTTP/1.0
 * Cookie: TEMPLATE=../../../../../../../../../etc/passwd
 * 
 * @param string $bDirSwitchingAllowed filters ALL directories from string --> makes this function less reliable!!!
 * @param string $sBasePath checks if filepath exists within basepath --> prevents from leaving basepath
 * @deprecated
 */
function filterDirectoryTraversal($sFilePath, $bDirSwitchingAllowed = false)
{



        $sFiltered = '';

        //call recursively to exclude double nested things.
        $sFiltered = filterRecursiveDirectoryTraversal($sFilePath, $bDirSwitchingAllowed);
        return $sFiltered;

}

/**
 * don;t execute function in itself.
 * This is used by filterDirectoryTraversal();
 * 
 * @deprecated
 */
function filterRecursiveDirectoryTraversal($sOrgValue, $bDirSwitchingAllowed)
{
        $sNewValue = '';
        $sNewValue = $sOrgValue;

        //filter double dots and slash used in traversal
        $sNewValue = str_replace ('..'.DIRECTORY_SEPARATOR, '', $sNewValue);
        $sNewValue = str_replace (htmlentities('..'.DIRECTORY_SEPARATOR), '', $sNewValue);
        $sNewValue = str_replace (urlencode('..'.DIRECTORY_SEPARATOR), '', $sNewValue);
        $sNewValue = str_replace (mb_convert_encoding('..'.DIRECTORY_SEPARATOR, 'UTF-8', mb_detect_encoding($sNewValue)), '', $sNewValue);


        //filter only double dots used in traversal
        $sNewValue = str_replace ('..', '', $sNewValue);
        $sNewValue = str_replace (htmlentities('..'), '', $sNewValue);
        $sNewValue = str_replace (urlencode('..'), '', $sNewValue);
        $sNewValue = str_replace (mb_convert_encoding('..', 'UTF-8', mb_detect_encoding($sNewValue)), '', $sNewValue);
        
        //and thanks to microsoft also some additional UTF8 characters
        $sNewValue = str_replace('%c1%1c', '', $sNewValue);
        $sNewValue = str_replace('%c0%af', '', $sNewValue);
        $sNewValue = str_replace('%c0%9v', '', $sNewValue);

        if ($bDirSwitchingAllowed)
                $sNewValue = filterBadCharsWhiteListLiteral($sNewValue, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-. '.DIRECTORY_SEPARATOR);
        else
                $sNewValue = filterBadCharsWhiteListLiteral($sNewValue, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-. ');

                
        if ($sOrgValue == $sNewValue) //this filtering made no difference, so quit
                return $sNewValue;
        else
                return filterRecursiveDirectoryTraversal($sNewValue, $bDirSwitchingAllowed);
}

/**
 * checks if the resolved filepath exists in the base path to avoid directory traversal
 * This is based on ACTUAL file checks on the directories in the system to see if they exist (doesn't do checks on files)
 * if it is traversed inside the sSafeBaseDir then it isn't considered directory traversal
 * 
 * this method might give problems on windows, because files are case insensive on windows, i.e. c:\ IS NOT C:\
 * 
 * @param string $sSafeBaseDir without directoryseparator at the end - a verified safe existing basepath for the file to exist in - file cannot exists outside this basepath!!!
 * @param string $sDirtyFileNameOrPath without trailing directoryseparator - a dirty filename or filesubpath in base directory (this can be a concatenated directory + file)
 */
function isDirectoryTraversal($sSafeBaseDir, $sDirtySubDir = '', $sDirtyFileName = '')
{
        $sDirtyConcatPath = '';
        $sRealBaseDir = '';
        $sResolvedPath = '';

        //check if $sSafeBaseDir exists
        $sRealBaseDir = realpath($sSafeBaseDir);
        if ($sRealBaseDir === false)
                logError(__FILE__.__LINE__.__FUNCTION__, 'basedir "'.$sSafeBaseDir.'" does not exist');

        //check if dir empty
        if (($sDirtySubDir === '') && ($sDirtyFileName === ''))
                return false;

        //concatenate
        $sDirtyConcatPath = $sSafeBaseDir;
        if ($sDirtySubDir != '') // if there is a directory, then add it to path
                $sDirtyConcatPath.= DIRECTORY_SEPARATOR.$sDirtySubDir;
        if ($sDirtyFileName != '') // if there is a filename, then add it to path
                $sDirtyConcatPath.= DIRECTORY_SEPARATOR.$sDirtyFileName;

        //construct untraversed directory
        if (file_exists($sDirtyConcatPath)) //when file exists
        {
                $sResolvedPath = realpath($sDirtyConcatPath); //realpath works only when file exists
                if ($sResolvedPath === false) //dir NOT exists (in other words: traversal)
                        return true;
        }
        else //when file not exists: check for suspicious characterz
        {
                $sResolvedPath = filterBadCharsWhiteListLiteral($sDirtyConcatPath, WHITELIST_PATH);
                if ($sResolvedPath !== $sDirtyConcatPath)//if changed after filtering: traversal detected
                        return true;
        }

        
        //is ResolvedDir INSIDE SafeBaseDir?
        $iPos = strpos($sResolvedPath, $sSafeBaseDir); //case SENTIVE compare, might give problem under windows when cases differ, but don't want to change it because it is important under linux
        return ($iPos === false); //true means OUTSIDE: directory traversal detected
}

/** 
 * concatenate directory with file in a safe way to avoid directory traversal
 * this function ALWAYS returns a safe path, even if it is $sSafeBaseDir when errors are found.
 * this function checks if the subdirectories actually exist AND
 * if the resolved filepath is within the safe basedirectory
 * 
 * if subdirectories within sDirtyFileNameOrPath don't exist, it will return the $sSafeBaseDir + DIRECTORY_SEPARATOR + filename
 * 
* @param string $sSafeBasePath without directoryseparator at the end - a verified safe existing basepath for the file to exist in (this function will not work if this dir doesn't exist)- file cannot exists outside this basepath!!!
* @param string $sDirtyFileNameOrPath without trailing directoryseparator - a dirty filename or filesubpath in base directory (this can be a concatenated directory + file)
*/
function concatDirFileSafe($sSafeBaseDir, $sDirtySubDir = '', $sDirtyFileName = '')
{
        $sDirtyConcatPath = '';
        $sRealBaseDir = '';
        $sNewBasePath = '';
        $sReturnPath = '';

        //check if $sSafeBaseDir exists
        $sRealBaseDir = realpath($sSafeBaseDir);
        if ($sRealBaseDir === false)
                logError(__FILE__.__LINE__.__FUNCTION__, 'basedir "'.$sSafeBaseDir.'" does not exist');

        //check if dir empty
        if (($sDirtySubDir === '') && ($sDirtyFileName === ''))
                return false;

        //filter filename
        $sDirtyFileName = filterBadCharsWhiteListLiteral($sDirtyFileName, WHITELIST_FILENAME);

        //concatenate
        $sDirtyConcatPath = $sSafeBaseDir;
        if ($sDirtySubDir != '') // if there is a directory, then add it to path
                $sDirtyConcatPath.= DIRECTORY_SEPARATOR.$sDirtySubDir;
        if ($sDirtyFileName != '') // if there is a filename, then add it to path
                $sDirtyConcatPath.= DIRECTORY_SEPARATOR.$sDirtyFileName;

        //construct untraversed directory
        if (file_exists($sDirtyConcatPath)) //when file exists
        {
                $sResolvedPath = realpath($sDirtyConcatPath); //dir exists?
                if ($sResolvedPath === false) //dir NOT exists (in other words: traversal)
                        return $sSafeBaseDir; //replace directory with safe directory
                else
                        $sNewBasePath = $sResolvedPath; //dirty directory is now considered safe
        }
        else //when file NOT exists
        {
                $sResolvedPath = filterBadCharsWhiteListLiteral($sDirtyConcatPath, WHITELIST_PATH);
                if ($sResolvedPath !== $sDirtyConcatPath)//if changed after filtering: traversal detected
                        return $sSafeBaseDir; //replace directory with safe directory
                else
                        $sNewBasePath = $sResolvedPath; //dirty directory is now considered safe               
        }

        //is ResolvedDir INSIDE SafeBaseDir? (=good). OUTSIDE sSafeBaseDir (=traversed)
        $iPos = strpos($sNewBasePath, $sSafeBaseDir); //case sentitive compare, this might go wrong under windows which is case insensitive, but i dont want to make it an insensitive compare since we need to case sensitive compare under linux
        if ($iPos === false) //OUTSIDE: directory traversal detected
        {
                $sReturnPath = $sSafeBaseDir; //return the thing we know is safe
        }
        else
        {
                $sReturnPath = $sResolvedPath;
        }


        return $sReturnPath;
}


/**
 * het includen van alle files in een directory met een bepaalde extensie
 * 
 * @param string $sDirectory
 * @param string $sExtension
 * @return bool true, false if not exists or error occured
 */
function requireAll($sDirectory, $sExtension = '.php')
{
    if (is_dir($sDirectory))
    {

        $dh = opendir($sDirectory);
        //deb($dir);
        while(false !== ($file = readdir($dh)))
        if (strpos($file, $sExtension) != false)
        {
            require_once $sDirectory.$file;
        }
        closedir($dh);
        return true;
    }
    else
    {
        throw new Exception('Directory "'.$sDirectory.'" does not exist');
        return false;
    }
}

/**
 * het extraheren van een file uit een path. Bijvoorbeeld : je hebt /home/var/local/index.php en je wilt hebben : index.php (dus zonder /home/var/local/)
 * 
 * @param string $sPath
 * @return string
 */
function extractFileFromPath($sPath)
{
    return basename($sPath);
}

/**
 * het extraheren van een directory uit een path. Bijvoorbeeld : je hebt /home/var/local/index.php en je wilt hebben : /home/var/local/ (dus zonder index.php)
 *
 * @param string $sPath
 * @return string inclusief laatste / of \
 */
function extractDirectoryFromPath($sPath)
{
    return dirname($sPath).DIRECTORY_SEPARATOR;
}

/**
 * lezen van bestandsgrootte
 * 
 * @param string $sFilePath (path van een file)
 * @return int grootte van bestand in kilobytes
 */
function getFileSize($sFilePath)
{
        if (is_file($sFilePath))
        {
                $sizekb = (int)(filesize($sFilePath) / 1024);

                if ($sizekb == 0)
                {
                        $size = "1 kb";
                }
                else
                {
                        $size = "$sizekb kb";

                }
        }
        else
                $size = "0 kb";

        return($size);
}

/**
 * get parent directory from path
 * if $sPath == /var/www/project1/test, then this function will output /var/www/project1
 * 
 * @param string $sPath
 * @return string previous directory of supplies string
 * @return string $sDirectorySeparator either '\' or '/'.
 * @return 
 */
function getParentDirectory($sPath, $sDirectorySeparator = DIRECTORY_SEPARATOR)
{
        $iTempPos = strrpos($sPath, $sDirectorySeparator); //pos opvragen van laatste /
        $sResult = substr($sPath, 0, $iTempPos);  //alles voor de laatste / pakken en als restult geven

        return($sResult);
}

/**
 * upload a file to this site from the webserver
 * 
 * This function outputs dutch data to the screen
 * 
 * @param int $iMaxuploadsize
 * @param <type> $sFieldName
 * @param <type> $sNewPathFile
 * @param <type> $arrAllowedExtensions
 */
function uploadFile($sFieldName, $sNewPathFile, $arrAllowedExtensions, $iMaxuploadsize = 0)
{
	function uploadFileFunctionWithExtension($sFieldName, $sExtension, $sNewPathFile)
	{
		$bSuccessSub = false;

		if (strtoupper(getFileExtension($sFieldName['name'])) == strtoupper($sExtension))
		{
			if ( move_uploaded_file($sFieldName['tmp_name'], $sNewPathFile) )
			{
				echo "upload van '".$sFieldName['name']."' succesvol voltooid";
				$bSuccessSub = true;
			}
			else
			{
				echo  "upload van '".$sFieldName['name']."' MISLUKT !";
			}
		}

		return($bSuccessSub);
	}

        if (is_uploaded_file($sFieldName['tmp_name']))
        {
                //echo "uploaden";

                if ($sFieldName['size'] > $iMaxuploadsize)
                {
                        echo "Het bestand neemt te veel ruimte in beslag";
                }

                if (is_array($arrAllowedExtensions))
                {
                			$iCountExt = count ($arrAllowedExtensions);
                        for ($iArrayTeller = 0; $iArrayTeller < $iCountExt; $iArrayTeller++)
                        {
                                uploadFileFunctionWithExtension($sFieldName, $arrAllowedExtensions[$iArrayTeller], $sNewPathFile);
                        }
                }
                else
                {
                        uploadFileFunctionWithExtension($sFieldName, $arrAllowedExtensions, $sNewPathFile);
                }
        }


}

/**
 * wrapper for rmdirrecursive
 * @param <type> $sDirectory
 * @return <type> 
 */
function removedir($sDirectory)
{
    return rmdirrecursive($sDirectory);
}

/**
 * wrapper for rmdirrecursive
 * @param <type> $sDirectory
 * @return <type>
 */
function deletedir($sDirectory)
{
    return rmdirrecursive($sDirectory);
}

/**
 * recursive delete directory.
 * PHP verrot het verwijderen van een directory als deze niet leeg is.
 * 
 * @param string $sDirectory
 * @return bool
 */
function rmdirrecursive($sDirectory)
{
   $dh=opendir($sDirectory);
   while ($file=readdir($dh))
   {
        if($file!="." && $file!="..")
        {
                $fullpath=$sDirectory."/".$file;
                if(!is_dir($fullpath))
                {
                        unlink($fullpath);
                }else{
                        rmdirrecursive($fullpath);
                }
        }
   }
   closedir($dh);
   if(rmdir($sDirectory))
   {
           return true;
   }else{
           return false;
   }
}

/**
 * het verkrijgen van alle bestanden/(sub)directories uit een directory
 * Geeft het resultaat in een 1d string array terug
 *
 * @param string $sDirectory
 * @param bool $bDirectories mappen ook in array zetten ?
 * @param bool $bFiles bestanden ook in array zetten ?
 * @return array met strings met bestanden en directories
 */
/*
function getFileFolderArray($sDirectory, $bDirectories, $bFiles)
{
        $arrFiles = array();
        if (is_dir($sDirectory))
        {
                $d = dir($sDirectory);

                while (false !== ($file = $d->read()))
                {
                        $path = $sDirectory.$file;
                        if ($file != "." && $file != "..")
                        {
                                if ($bDirectories)
                                        if (is_dir($sDirectory.$file))
                                                $arrFiles[] = $file;
                                if ($bFiles)
                                        if (is_file($sDirectory.$file))
                                                $arrFiles[] = $file;
                        }
                }
                $d->close();
        }
        if (is_array($arrFiles))
            if (count($arrFiles) > 0)
                sort($arrFiles);

        return $arrFiles;
}

*/

/**
 * subdirectories en/of bestanden uit een directory in array plaatsen (uitbreiding op getFileFolderArray)
 * je kunt nu echter toegestane bestandsextensies opgeven
 * 
 * @param string $sDirectory
 * @param bool $bDirectories mappen ook in array zetten ?
 * @param bool $bFiles bestanden ook in array zetten ?
 * @param array $arrExtensions array van extensions zonder punt bv jpg, jpeg, bmp
 * @return array
 */
/*
function getFileFolderArrayExtension($sDirectory, $bDirectories, $bFiles, $arrExtensions)
{
        $arrFiles = array();
        if (is_dir($sDirectory))
        {
                $d = dir($sDirectory);

                while (false !== ($file = $d->read()))
                {
                        $path = $sDirectory.$file;
                        if ($file != "." && $file != "..")
                        {
                                if ($bDirectories)
                                        if (is_dir($sDirectory.$file))
                                                $arrFiles[] = $file;
                                if ($bFiles)
                                        if (is_file($sDirectory.$file))
                                                for ($iTeller = 0; $iTeller < count($arrExtensions)-1; $iTeller++)
                                                        if (strtoupper($arrExtensions[$iTeller]) == strtoupper(getFileExtension($sDirectory.$file)))
                                                                $arrFiles[] = $file;
                        }
                }
                $d->close();
        }
        if (is_array($arrFiles))        
            if (count($arrFiles) > 0)
                sort($arrFiles);

        return $arrFiles;
}
 * */
 

/**
 * het verkrijgen van alle bestanden/(sub)directories uit een directory
 * Geeft het resultaat in een 1d string array terug, false on failure
 * 
 * deze functie is geavanceerder dan php's eigen scandir()
 * Je kunt bij deze functie filteren op directory, file en bestandsextensie 
 * daarbij worden de unix punt (.) en punt-punt (..) niet terug gegeven
 * 
 * directories worden herkend met is_dir.
 * als er onvoldoende rechten zijn voor de directory werkt is_dir niet goed!
 * maw. deze functie werkt dan niet goed
 * 
 * Als je alleen een directory inhoud wilt hebben en niet filteren op directory, file of extensie
 * gebruik dan scandir(). dat is sneller
 *
 * @param string $sDirectory
 * @param bool $bAllowDirectories mappen ook in array zetten ?
 * @param bool $bAllowFiles bestanden ook in array zetten ?
 * @param array $arrExtensions de extensies waarop gefilterd moet worden - de vorm van de array: array('exe', 'doc', 'xls');
 * @return array met strings met bestanden en directories / false if failed
 */
function getFileFolderArray($sDirectory, $bAllowDirectories=true, $bAllowFiles=true, $arrExtensions = null)
{
    $arrResult = array();
    
    if ($arrExtensions != null)//foutieve parameter ondervangen
    {
        if (!is_array($arrExtensions))
            $arrExtensions = null;
    }
    
    //directory filteren zodat er geen separator achter komt
    if (str_ends_with($sDirectory, DIRECTORY_SEPARATOR))
        $sDirectory = removeLastChar($sDirectory);
    
    if (is_dir($sDirectory))
    {
        $arrTempFiles= scandir($sDirectory);

        
        if ($arrTempFiles)
        {
            foreach ($arrTempFiles as $sFile) //array filteren
            {                
                if (($sFile != '.') && ($sFile != '..') && ($sFile != '.DS_Store') && (!startswith($sFile, '@')))
                {        
                    //files
                    if ($bAllowFiles)
                    {
                        if (is_file($sDirectory.DIRECTORY_SEPARATOR.$sFile))
                        {
                            if ($arrExtensions != null)
                            {
                                foreach ($arrExtensions as $sExtension)
                                {
                                    if (endswith($sFile, '.'.$sExtension))
                                         $arrResult[] = $sFile;   
                                }
                            }    
                            else
                                $arrResult[] = $sFile;
                        }
                    }
                    
                    //directories
                    if ($bAllowDirectories)
                    {
                        if (is_dir($sDirectory.DIRECTORY_SEPARATOR.$sFile.DIRECTORY_SEPARATOR))
                           $arrResult[] = $sFile;
                        
                    }
                    
                }
            }    
            return $arrResult;
                
        }
        else
            return false;
    }
    else
        return false;
    
}


/**
 * extensie van bestand verkrijgen
 *
 * @param string $sFile
 * @return string
 */
function getFileExtension($sFile)
{
        // $iPosPunt = strrpos($sFile, ".");
        // $iLengthExtension = strlen($sFile) - $iPosPunt -1;
        // return substr($sFile, $iPosPunt + 1, $iLengthExtension);
        $arrPathParts = pathinfo($sFile);
        if (isset($arrPathParts['extension']))
                return $arrPathParts['extension'];      
        else
                return '';
}

/**
 * bestandsnaam verkrijgen zonder extensie
 * (er wordt gezocht naar de laatste punt(.) in de string)
 * als $sFile geen extensie heeft, wordt $sFile weer teruggegeven
 * 
 * @param string $sFile
 * @return string
 */
function getFileNameWithoutExtension($sFile)
{
        $iPosPunt = strrpos($sFile, ".");
        $iLengthFileNameWithoutExtension = $iPosPunt;

        if ($iLengthFileNameWithoutExtension == false) //als geen extensie, dan alleen filenaam teruggeven
            return $sFile;
        else
            return substr($sFile, 0, $iLengthFileNameWithoutExtension);
}

/**
 * generate a unique filename in a directory
 * 
 * WARNING: 
 * this function does no checks on directory traversal in filename, 
 * that would make the results of this function unreliable 
 * (if i would filter characters than it would change the file name, hence it could become not unique anymore)
 *
 * @param string $sDirectory
 * @param string $sDefaultFileName default file name including extension
 * @return string
 */
function generateUniqueFileName($sDirectory, $sDefaultFileName)
{
        //declare
        $arrFilesInDir = array();
        $sNewFileName = '';
        $arrPathParts = array();
        $iStrRPosOpen = 0;
        $iStrRPosClose = 0;
        $iLengthNumber = 0;
        $sBeforeOpenClose = '';//part before opening and closing brackets, like (2)
        $iHighestValue = 0;

        //init
        $arrFilesInDir = getFileFolderArray($sDirectory);
        $sNewFileName = $sDefaultFileName;
        $arrPathParts = pathinfo($sDefaultFileName);

        //if default is available, return it
        if (!is_file($sDirectory.DIRECTORY_SEPARATOR.$sDefaultFileName))
                return $sDefaultFileName;


        //filter brackets in $sDefaultFileName
        $iStrRPosOpen = strrpos($sDefaultFileName, '(');
        if ($iStrRPosOpen !== false)
                $arrPathParts['filename'] = substr($sDefaultFileName, 0, $iStrRPosOpen);

        //look for highest number between brackets
        foreach ($arrFilesInDir as $sFileInDir)
        {                
                $iStrRPosOpen = strrpos($sFileInDir, '(');

                if($iStrRPosOpen !== false) //open bracket exists
                {
                        $sBeforeOpenClose = substr($sFileInDir, 0, $iStrRPosOpen);

                        if ($sBeforeOpenClose == $arrPathParts['filename']) //if file-in-dir it has the same name as $sDefaultFileName
                        {
                                $iStrRPosClose = strrpos($sFileInDir, ')');
                                $iLengthNumber = $iStrRPosClose - $iStrRPosOpen - 1;
                                $sNumber = substr($sFileInDir, $iStrRPosOpen+1, $iLengthNumber);
                                if (is_numeric($sNumber))
                                        $iHighestValue = (int)$sNumber;
                        }
                }
        }

        //create new file name
        $sNewFileName = $arrPathParts['filename'].'('.($iHighestValue+1).').'.$arrPathParts['extension'];

        return $sNewFileName;

}


/**
 * net zoals in windows weergeven wat voor type file het is d.m.v. een plaatje
 * er worden een flink aantal bestandsextensies herkend.
 * Terug gegeven wordt de bestandsnaam van het plaatje in de vorm van: file-avi.gif voor een avi file
 *
 * als $sFile='filmpje.avi', wordt het resultaat: 'file-avi.gif'
 *
 * @param string $sFile
* @return string met bestandsnaam van het plaatje dat bij de extensie hoort
 */
function getImageByExtension($sFile)
{
        if (is_dir($sFile))
        {
                return "file-folder.gif";
        }
        else
        {
                switch(getFileExtension(strtolower($sFile)))
                {
                        case "avi":
                                return "file-avi.gif";
                                break;
                        case "mpg":
                                return "file-avi.gif";
                                break;
                        case "mpeg":
                                return "file-avi.gif";
                                break;
                        case "mov":
                                return "file-avi.gif";
                                break;
                        case "bat":
                                return "file-bat.gif";
                                break;
                        case "dfm":
                                return "file-dfm.gif";
                                break;
                        case "gif":
                                return "file-gif.gif";
                                break;
                        case "dll":
                                return "file-dll.gif";
                                break;
                        case "dpr":
                                return "file-dpr.gif";
                                break;
                        case "exe":
                                return "file-exe.gif";
                                break;
                        case "com":
                                return "file-com.gif";
                                break;
                        case "gif":
                                return "file-gif.gif";
                                break;
                        case "htm":
                                return "file-htm.gif";
                                break;
                        case "html":
                                return "file-htm.gif";
                                break;
                        case "asp":
                                return "file-htm.gif";
                                break;
                        case "php":
                                return "file-htm.gif";
                                break;
                        case "pl":
                                return "file-htm.gif";
                                break;
                        case "ini":
                                return "file-ini.gif";
                                break;
                        case "jpg":
                                return "file-jpg.gif";
                                break;
                        case "jpeg":
                                return "file-jpg.gif";
                                break;
                        case "png":
                                return "file-jpg.gif";
                                break;
                        case "bmp":
                                return "file-jpg.gif";
                                break;
                        case "mdb":
                                return "file-mdb.gif";
                                break;
                        case "csv":
                                return "file-txt.gif";
                                break;
                        case "sql":
                                return "file-txt.gif";
                                break;
                        case "png":
                                return "file-txt.gif";
                                break;
                        case "java":
                                return "file-txt.gif";
                                break;
                        case "mp3":
                                return "file-mp3.gif";
                                break;
                        case "msi":
                                return "file-msi.gif";
                                break;
                        case "pas":
                                return "file-pas.gif";
                                break;
                        case "pdf":
                                return "file-pdf.gif";
                                break;
                        case "ppt":
                                return "file-ppt.gif";
                                break;
                        case "rar":
                                return "file-rar.gif";
                                break;
                        case "ttf":
                                return "file-ttf.gif";
                                break;
                        case "txt":
                                return "file-txt.gif";
                                break;
                        case "vsd":
                                return "file-vsd.gif";
                                break;
                        case "doc":
                                return "file-doc.gif";
                                break;
                        case "xls":
                                return "file-xls.gif";
                                break;
                        case "zip":
                                return "file-zip.gif";
                                break;
                        case "bmp":
                                return "file-bmp.gif";
                                break;
                        case "xml":
                                return "file-xml.gif";
                                break;
                        case "rtf":
                                return "file-rtf.gif";
                                break;
                        case "tar":
                                return "file-tar.gif";
                                break;
                        case "psd":
                                return "file-psd.gif";
                                break;
                        case "png":
                                return "file-png.gif";
                                break;
                        case "bmp":
                                return "file-bmp.gif";
                                break;

                        default:
                                return "file.gif";
                                break;
                }//einde case
        }//einde else is_dir
}


/**
 * load contents from a file in UTF8 format and put it in an array
 *
 * @param string $sFileName path of file to read
 * @return array
 */
function loadFromFile($sFileName)
{

    $arrFileContents = array();

    try
    {
        $sContents = file_get_contents($sFileName);
        if (isUTF8String($sContents))
            $sContents = file_get_contents($sFileName);
        else
            $sContents = utf8_decode(file_get_contents($sFileName));
            
        $arrFileContents = strToArr($sContents);
    }
    catch (Exception $objException)
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException);
        return false;
    }

    return $arrFileContents;
}

/**
 * saves the the array with strings to a file in UTF-8 format (detects and converts in necesary)
 * if file not exists it creates the file
 * 
 * @param array $arrContents array met strings die opgeslagen worden in het bestand
 * @param string $sFileName
 * @param string $sLineEndingCharacter
 * @param $iChmod unix style change access mode code: 777 all access, if -1 cmod will not be performed
 * @return bool succesful ?
 */
function saveToFile($arrContents, $sFileName, $sLineEndingCharacter = PHP_EOL, $iChmod = -1)
{
    try
    {
        if ($sFileName == '')
            throw new Exception('saveToFile(): supplied filename is empty!');

        $iFilePointer = fopen($sFileName, 'w'); //file openen of createn

        if ($iFilePointer != false)
        {
            //array naar file brengen
            foreach ($arrContents as $sLine)
            {
                $sLineWithEOF = $sLine.$sLineEndingCharacter;
                if (isUTF8String($sLineWithEOF))
                    fwrite($iFilePointer,$sLineWithEOF);
                else
                    fwrite($iFilePointer, mb_convert_encoding($sLineWithEOF, "UTF-8", mb_detect_encoding($sLineWithEOF)));
            }

            //file opslaan
            $bResult = fclose($iFilePointer);
            
            //rechten wijzigen
            if ($iChmod > -1)
            {
                if (!chmod($sFileName, $iChmod))
                {
                    $bResult = false;
                    throw new Exception('saveToFile(): chmod could not be performed, maybe lack of rights ???');
                }
            }

            return $bResult;
            
        }
        else
            throw new Exception('saveToFile(): file access denied (maybe a chmod helps)');

    }
    catch (Exception $objException)
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException);
        return false;
    }

}

/**
 * add a line of text to a file UTF8 safe
 * if file not exists it creates the file
 *
 * @param string $sLine line to add to the file
 * @param string $sFileName filepath to add the d
 * @param string $sLineEndingCharacter
 * @return bool writing ok ?
 */
function addToFile($sLine, $sFileName, $sLineEndingCharacter = "\n")
{
    try
    {
        $fp = fopen($sFileName, 'a');

        
        $sLineWithEOF = $sLine.$sLineEndingCharacter;
        if (isUTF8String($sLineWithEOF))
            fwrite($fp,$sLineWithEOF);
        else
            fwrite($fp,$sLineWithEOF);                    

        return fclose($fp);

    }
    catch (Exception $objException)
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException);
        return false;
    }

}

/**
 * saves a single string to a file UTF8-style
 *
 * @param string $sStringToWriteToFile
 * @param string $sFileName
 * @return bool true if ok
 */
function saveToFileString($sStringToWriteToFile, $sFileName)
{
    try
    {
        $fp = fopen($sFileName, 'w');

        if (isUTF8String($sStringToWriteToFile))
            fwrite($fp,$sStringToWriteToFile);           
        else
            fwrite($fp, mb_convert_encoding($sStringToWriteToFile, "UTF-8", mb_detect_encoding($sStringToWriteToFile)));

        return fclose($fp);

    }
    catch (Exception $objException)
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException);
        return false;
    }
}

/**
 * load a string from a file
 *
 * @param string $sStringToWriteToFile
 * @return UTF8 string from the file
 */
function loadFromFileString($sStringToWriteToFile)
{

    try
    {
        $sContents = file_get_contents($sStringToWriteToFile);
        if (isUTF8String($sContents))
                return $sContents;
        else
                return mb_convert_encoding($sContents, "UTF-8", mb_detect_encoding($sContents));
            //return utf8_decode($sContents);
    }
    catch (Exception $objException)
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException);
        return false;
    }
}

/**
 * returns maximum upload size (wich is set in de php.ini) in bytes
 *
 * this function uses takes the post_max_size and upload_max_filesize of the php.ini in account
 *
 */
function getMaxFileUploadSize()
{
    $iResult = 0;
    
    $sMaxPostSize = ini_get('post_max_size');
    $iMaxPostSize = convertReadableBinairySizeToBytes($sMaxPostSize);
    $sMaxUploadSize = ini_get('upload_max_filesize');
    $iMaxUploadSize = convertReadableBinairySizeToBytes($sMaxUploadSize);
    
    $iResult = $iMaxUploadSize; //default

    if ($iMaxPostSize < $iResult) //als postsize kleiner, dan is dit het resultaat
        $iResult = $iMaxPostSize;

    return $iResult;
}

/**
 * add a path to the php.ini include path configuration for this website
 * @param string $sPath path to add to the includepath
 */
function addIncludePath($sPath)
{
    set_include_path(get_include_path() . PATH_SEPARATOR . $sPath);
}

/**
 * get the temporary directory
 *
 */
function getTempDir()
{
    $tmpdir = array();
    foreach (array($_ENV, $_SERVER) as $tab) {
            foreach (array('TMPDIR', 'TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
                    if (isset($tab[$key])) {
                            if (($key == 'windir') or ($key == 'SystemRoot')) {
                    $dir = realpath($tab[$key] . '\\temp');
                } else {
                    $dir = realpath($tab[$key]);
                }
                            if ($this->_isGoodTmpDir($dir)) {
                                    return $dir;
                            }
                    }
            }
    }
    $upload = ini_get('upload_tmp_dir');
    if ($upload) {
        $dir = realpath($upload);
            if ($this->_isGoodTmpDir($dir)) {
                    return $dir;
            }
    }
    if (function_exists('sys_get_temp_dir')) {
        $dir = sys_get_temp_dir();
            if ($this->_isGoodTmpDir($dir)) {
                    return $dir;
            }
    }
    // Attemp to detect by creating a temporary file
    $tempFile = tempnam(md5(uniqid(rand(), TRUE)), '');
    if ($tempFile) {
            $dir = realpath(dirname($tempFile));
        unlink($tempFile);
        if ($this->_isGoodTmpDir($dir)) {
            return $dir;
        }
    }

    throw new Exception('Could not determine temp directory');
}


/**
 * gets the contents of a website and returns it in an array
 *
 * @param string $sURL
 * @return array
 */
function loadFromUrl($sURL)
{
    return file($sURL);
}

/**
 * gets the contents of a website and returns it in a string
 *
 * @param string $sURL
 * @return string
 */
function loadFromUrlString($sURL)
{
    return file_get_contents($sURL);
}

/*****************************************************************

/**
 * read the text from a Microsoft Word file
 *
I don't pretend that it makes a complete success of extracting the text from all Word documents, but I've found it very reliable for the vast majority of the several thousand docs I've used it with. The function returns text from the Word document as a string,&nbsp;with all the formatting removed. Please note that some parts of the Word document (header, footer etc) are not parsed.</P><FONT color=#cc0000><XMP><?php

 * This approach uses detection of NUL (chr(00)) and end line (chr(13))
to decide where the text is:
- divide the file contents up by chr(13)
- reject any slices containing a NUL
- stitch the rest together again
- clean up with a regular expression
 * 
 * @param string $sWordFile
 * @return string text from Microsoft Word file
 */
function getTextMSWordDcoumentNonXML($sWordFile)
{
    $fileHandle = fopen($sWordFile, "r");
    $line = @fread($fileHandle, filesize($sWordFile));
    $lines = explode(chr(0x0D),$line);
    $outtext = "";
    foreach($lines as $thisline)
      {
        $pos = strpos($thisline, chr(0x00));
        if (($pos !== FALSE)||(strlen($thisline)==0))
          {
          } else {
            $outtext .= $thisline." ";
          }
      }
    $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
    return $outtext;
}



/**
 * function write_ini_file()
 * 
 * the counterpart for the php function parse_ini_file to write the contents of the associative array to an ini file
 * 
 * @param array $assoc_arr
 * @param string $path
 * @param string $has_sections
 * @return boolean|number
 */
function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
	$content = '';
	if ($has_sections) {
		foreach ( $assoc_arr as $key => $elem ) {
			$content .= '[' . $key . "]\n";
			foreach ( $elem as $key2 => $elem2 ) {
				if (is_array ( $elem2 )) {
					for($i = 0; $i < count ( $elem2 ); $i ++) {
						$content .= $key2 . '[] = "' . $elem2 [$i] . "\"\n";
					}
				} else if ($elem2 == '')
					$content .= $key2 . " = \n";
				else
					$content .= $key2 . ' = "' . $elem2 . "\"\n";
			}
			$content .= "\n";
		}
	} else {
		foreach ( $assoc_arr as $key => $elem ) {
			if (is_array ( $elem )) {
				for($i = 0; $i < count ( $elem ); $i ++) {
					$content .= $key . '[] = "' . $elem [$i] . "\"\n";
				}
			} else if ($elem == '')
				$content .= $key . " = \n";
			else
				$content .= $key . ' = "' . $elem . "\"\n";
		}
	}
	
	if (! $handle = fopen ( $path, 'w' )) {
		return false;
	}
	
	$success = fwrite ( $handle, $content );
	fclose ( $handle );
	
	return $success;
}

/*========================================================================================================================\
* name				: includeAll
* programmer name 	: 
* date 				: 28 juni 2005
* input				: $dir: string - wich directory to include ? ;$ext - file extensie
* output			: 
* use for			: het includen van alle files in een directory met een bepaalde extensie
* description		: 
\========================================================================================================================*/   
function includeAll($sDir, $ext = '.php')
{
    $strDH = opendir($sDir);
   
    while(false !== ($file = readdir($strDH)))
    if (strpos($file, $ext) != false)
    {
        include_once($sDir.$file);
    }
    closedir($strDH);
}

/**
 * for uploading files with a html form:
 * this function rearranges the array in 1 file per array element
 * 
 * The original format is:
 * Array
(
    [name] => Array
        (
            [0] => foo.txt
            [1] => bar.txt
        )

    [type] => Array
        (
            [0] => text/plain
            [1] => text/plain
        )

    [tmp_name] => Array
        (
            [0] => /tmp/phpYzdqkD
            [1] => /tmp/phpeEwEWG
        )

    [error] => Array
        (
            [0] => 0
            [1] => 0
        )

    [size] => Array
        (
            [0] => 123
            [1] => 456
        )
)
 * 
 * this function converts that array to a 1 element per file:
 * Array
(
    [0] => Array
        (
            [name] => foo.txt
            [type] => text/plain
            [tmp_name] => /tmp/phpYzdqkD
            [error] => 0
            [size] => 123
        )

    [1] => Array
        (
            [name] => bar.txt
            [type] => text/plain
            [tmp_name] => /tmp/phpeEwEWG
            [error] => 0
            [size] => 456
        )
)
 * @author some dude on php.net https://www.php.net/manual/en/features.file-upload.multiple.php
 * @param string $file_post
 * @return array
 */
function uploadFilesRearrangeArray(&$file_post) 
{

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
    
        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
    
        return $file_ary;
}

/**
 * creates a temporary file in the temp folder 
 * (don't forget to delete it after using it)
 * 
 * some dude @ https://stackoverflow.com/questions/1707801/making-a-temporary-dir-for-unpacking-a-zipfile-into
 * 
 * @return string
 */
function tempdir() 
{
        $tempfile=tempnam(sys_get_temp_dir(),'');
        // you might want to reconsider this line when using this snippet.
        // it "could" clash with an existing directory and this line will
        // try to delete the existing one. Handle with caution.
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        if (is_dir($tempfile)) { return $tempfile; }
}

/**
 * copy a file or directory
 * if directory it will be done recursively
 * 
 * 
 * @param string $sSource source file or directory
 * @param string $sDestination destination file or directory
 */
function copyRecursive($sSource, $sDestination)
{
        $bResult = true;
        $arrFiles = getFileFolderArray($sSource);
        $sFullSourcePath = '';
        
        if ($arrFiles)
        {
                foreach ($arrFiles as $sFile)
                {
                        $sFullSourcePath = $sSource.DIRECTORY_SEPARATOR.$sFile;
                        $sFullDestinationPath = $sDestination.DIRECTORY_SEPARATOR.$sFile;
                        if (is_dir($sFullSourcePath))
                        {
                                if (!is_dir($sFullDestinationPath)) //if directory already exists, dont create it
                                        mkdir($sFullDestinationPath); //create destination directory

                                if (copyRecursive($sFullSourcePath, $sFullDestinationPath) === false)
                                        $bResult = false;
                        }
                        else
                        {
                                if (copy($sFullSourcePath, $sFullDestinationPath) === false)
                                        $bResult = false;
                        }
                }
        }
        
        return $bResult;
}


/**
 * Copy a file, or recursively copy a folder and its contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       int      $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */
// function xcopy($source, $dest, $permissions = 0755)
// {




//     $sourceHash = xcopyhashDirectory($source);
//     // Check for symlinks
//     if (is_link($source)) {
//         return symlink(readlink($source), $dest);
//     }

//     // Simple copy for a file
//     if (is_file($source)) {
//         return copy($source, $dest);
//     }

//     // Make destination directory
//     if (!is_dir($dest)) {
//         mkdir($dest, $permissions);
//     }

//     // Loop through the folder
//     $dir = dir($source);
//     while (false !== $entry = $dir->read()) {
//         // Skip pointers
//         if ($entry == '.' || $entry == '..') {
//             continue;
//         }

//         // Deep copy directories
//         if($sourceHash != xcopyhashDirectory($source."/".$entry)){
//              xcopy("$source/$entry", "$dest/$entry", $permissions);
//         }
//     }

//     // Clean up
//     $dir->close();
//     return true;
// }


//         // In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated
//         function xcopyhashDirectory($directory){
//                 if (! is_dir($directory)){ return false; }
        
//                 $files = array();
//                 $dir = dir($directory);
        
//                 while (false !== ($file = $dir->read())){
//                 if ($file != '.' and $file != '..') {
//                         if (is_dir($directory . '/' . $file)) { $files[] = xcopyhashDirectory($directory . '/' . $file); }
//                         else { $files[] = md5_file($directory . '/' . $file); }
//                 }
//                 }
        
//                 $dir->close();
        
//                 return md5(implode('', $files));
//         }

/**
 * recursive version of rename for directories
 *
 * @param string $sSource
 * @param string $sDestination
 * @return bool
 */
function renameRecursive($sSource, $sDestination)
{
    $bResult = true;
    if (copyRecursive($sSource, $sDestination) === false)
            $bResult = false;

    if ($bResult)
    {
            if (rmdirrecursive($sSource) === false)
                    $bResult = false;
    }

    return $bResult;
}

/**
 * create .htaccess file in directory with contents $sContent
 * 
 * if not exists, create one.
 * if exists overwrite
 * @param string $sDirectory
 * @param bool $bOverwrite overwrite existing htaccess when it exists?
 * @param string $sContent
 * @return bool error
 */
function createHtaccessFile($sDirectory, $bOverwrite = false, $sContent = 'Deny from all')
{
    $sFile = $sDirectory.DIRECTORY_SEPARATOR.'.htaccess';

    //if file exists and may not be overwritten, do nothing
    if (file_exists($sFile) && (!$bOverwrite))
        return true;
    
    //create htaccess that blocks access to dir
    $fhHtaccess = fopen($sFile, 'w'); 
    if ($fhHtaccess !== false)
    {
        fwrite($fhHtaccess, $sContent);
        fclose($fhHtaccess);
        return true;
    }
    else
        return false;
}


/**
 * returns if mime-type is image
 * 
 * For security, I check against KNOWN image types, not just any image type
 */
function isMimeImage($sMimeType)
{
    switch ($sMimeType)
    {
        case MIME_TYPE_BMP:
        case MIME_TYPE_GIF:
        case MIME_TYPE_ICO:
        case MIME_TYPE_PNG:
        case MIME_TYPE_PSD:
        case MIME_TYPE_SVG:
        case MIME_TYPE_AVIF:
        case MIME_TYPE_JPEG:
        case MIME_TYPE_TIFF:
        case MIME_TYPE_WEBP:
            return true;
            break;
    }

    return false;
}

/**
 * return the maximum upload size in bytes
 */
function getFileUploadMaxSizeBytes()
{
    $iMaxUpload = convertPHPSizeToBytes(ini_get("upload_max_filesize"));
    $iMaxPost = convertPHPSizeToBytes(ini_get("post_max_size"));

    return min($iMaxUpload, $iMaxPost);
}


/**
 * get mime type by looking at the file extension
 */
function getMimeTypeExtension($sFilePath)
{
    $sFileExtension = '';
    $sFileExtension = getFileExtension($sFilePath);
    
    $arrExtensions['aac'] = 'audio/aac';
    $arrExtensions['abw'] = 'application/x-abiword';
    $arrExtensions['arc'] = 'application/x-freearc';
    $arrExtensions['avi'] = 'video/x-msvideo';
    $arrExtensions['azw'] = 'application/vnd.amazon.ebook';
    $arrExtensions['bin'] = 'application/octet-stream';
    $arrExtensions['bmp'] = 'image/bmp';
    $arrExtensions['bz'] = 'application/x-bzip';
    $arrExtensions['bz2'] = 'application/x-bzip2';
    $arrExtensions['cda'] = 'application/x-cdf';
    $arrExtensions['csh'] = 'application/x-csh';
    $arrExtensions['css'] = 'text/css';
    $arrExtensions['csv'] = 'text/csv';
    $arrExtensions['doc'] = 'application/msword';
    $arrExtensions['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $arrExtensions['eot'] = 'application/vnd.ms-fontobject';
    $arrExtensions['epub'] = 'application/epub+zip';
    $arrExtensions['gz'] = 'application/gzip';
    $arrExtensions['gif'] = 'image/gif';
    $arrExtensions['htm'] = 'text/html';
    $arrExtensions['html'] = 'text/html';
    $arrExtensions['ico'] = 'image/vnd.microsoft.icon';
    $arrExtensions['ics'] = 'text/calendar';
    $arrExtensions['jar'] = 'application/java-archive';
    $arrExtensions['jpeg'] = 'image/jpeg';
    $arrExtensions['jpg'] = 'image/jpeg';
    $arrExtensions['js'] = 'text/javascript';
    $arrExtensions['json'] = 'application/json';
    $arrExtensions['jsonld'] = 'application/ld+json';
    $arrExtensions['mid'] = 'audio/midi audio/x-midi';
    $arrExtensions['midi'] = 'audio/midi audio/x-midi';
    $arrExtensions['mjs'] = 'text/javascript';
    $arrExtensions['mp3'] = 'audio/mpeg';
    $arrExtensions['mp4'] = 'video/mp4';
    $arrExtensions['mpeg'] = 'video/mpeg';
    $arrExtensions['mpkg'] = 'application/vnd.apple.installer+xml';
    $arrExtensions['odp'] = 'application/vnd.oasis.opendocument.presentation';
    $arrExtensions['ods'] = 'application/vnd.oasis.opendocument.spreadsheet';
    $arrExtensions['odt'] = 'application/vnd.oasis.opendocument.text';
    $arrExtensions['oga'] = 'audio/ogg';
    $arrExtensions['ogv'] = 'video/ogg';
    $arrExtensions['ogx'] = 'application/ogg';
    $arrExtensions['opus'] = 'audio/opus';
    $arrExtensions['otf'] = 'font/otf';
    $arrExtensions['png'] = 'image/png';
    $arrExtensions['pdf'] = 'application/pdf';
    $arrExtensions['php'] = 'application/x-httpd-php';
    $arrExtensions['ppt'] = 'application/vnd.ms-powerpoint';
    $arrExtensions['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    $arrExtensions['rar'] = 'application/vnd.rar';
    $arrExtensions['rtf'] = 'application/rtf';
    $arrExtensions['sh'] = 'application/x-sh';
    $arrExtensions['svg'] = 'image/svg+xml';
    $arrExtensions['swf'] = 'application/x-shockwave-flash';
    $arrExtensions['tar'] = 'application/x-tar';
    $arrExtensions['tif'] = 'image/tiff';
    $arrExtensions['tiff'] = 'image/tiff';
    $arrExtensions['ts'] = 'video/mp2t';
    $arrExtensions['ttf'] = 'font/ttf';
    $arrExtensions['txt'] = 'text/plain';
    $arrExtensions['vsd'] = 'application/vnd.visio';
    $arrExtensions['wav'] = 'audio/wav';
    $arrExtensions['weba'] = 'audio/webm';
    $arrExtensions['webm'] = 'video/webm';
    $arrExtensions['webp'] = 'image/webp';
    $arrExtensions['woff'] = 'font/woff';
    $arrExtensions['woff2'] = 'font/woff2';
    $arrExtensions['xhtml'] = 'application/xhtml+xml';
    $arrExtensions['xls'] = 'application/vnd.ms-excel';
    $arrExtensions['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $arrExtensions['xml'] = 'application/xml';
    $arrExtensions['xul'] = 'application/vnd.mozilla.xul+xml';
    $arrExtensions['zip'] = 'application/zip';
    $arrExtensions['3gp'] = 'video/3gpp';
    $arrExtensions['3g2'] = 'video/3gpp2';
    $arrExtensions['7z'] = 'application/x-7z-compressed'; 

    if (array_key_exists($sFileExtension, $arrExtensions))
        return $arrExtensions[$sFileExtension];
    else
        return 'application/octet-stream';
}

/**
 * tries different methods to determine the mime type of a file by opening the file
 * 
 * EXPLANATION
 * checks range in severity from most safe to loose (depending on installed extension)
 * 
 * @return string. false if not recognised or error
 */
function getMimeTypeFile($sFilePath)
{
    $sRealMimeType = '';

    //CHECK1 : for images only 
    if (isMimeImage(getMimeTypeExtension($sFilePath)))
    {
        //CHECK: EXIF
        if (function_exists('exif_imagetype'))
        {
            $iExifType = exif_imagetype($sFilePath);
            $sRealMimeType = image_type_to_mime_type($iExifType); //when $iExifType == false, it returns application/octet-stream

            if ($iExifType === false) //doesnt recognize file
                return false;
            
            return $sRealMimeType;
        }
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: exif_imagetype() function does not exist, probably because php "exif" extension is not loaded. It is strongly recommended to install this extension!');
    }


    //CHECK 2: file info check
    //finfo_open is better because its newer, thats why its after than mime_content_type()
    if (function_exists('finfo_open'))
    {
        $objFileInfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type aka mimetype extension
        $sRealMimeType = finfo_file($objFileInfo, $sFilePath);
        finfo_close($objFileInfo);

        return $sRealMimeType;
    }
    else
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: finfo_open() function does not exist, probably because php "fileinfo" extension is not loaded. It is strongly recommended to install this extension!');


    //CHECK 3: mime-type check 
    if (function_exists('mime_content_type'))
    {
        $sRealMimeType = mime_content_type($sFilePath);

        return $sRealMimeType;
    }
    else
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'WARNING: mime_content_type() function does not exist, probably because php "fileinfo" extension is not loaded. It is strongly recommended to install this extension!');


    return false; //could find a way to check
}


?>
