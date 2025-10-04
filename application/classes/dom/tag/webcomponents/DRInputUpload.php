<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\models\TSysModel;
use dr\classes\types\TDecimal;
use Exception;
use GdImage;

// include_once('bootstrap')

/**
 * represents a <dr-input-upload>
 * part of PHP counterpart for web component <dr-input-upload>
 * 
 * 
 * FILE HISTORY
 * =============
 * This class keeps a file history array in the session ($_SESSION[SESSIONAK_DRINPUTUPLOAD][SESSIONAK_DRINPUTUPLOAD_FILEHISTORY])
 * This array is filled with file paths when uploading OR this component is filled by loading a record from database.
 * This prevents a user from just deleting or renaming (and by extension moving) any file on the server, only files that this component has touched in the past.
 * This prevents injections or directory traversal attacks
 * 
 * XHR and Fetch
 * =============
 * By default we use Fetch to get and send information to/from server.
 * However Fetch doesn't support partial progress (5%, 10%, 20% in progress bar) support yet.
 * This is why we use XHR for the upload function.
 * 
 * TIPS
 * =============
 * - to upload multiple files at once, use setMultiple();
 * 
 * ADVANTAGES
 * ==========
 * - renaming files for SEO purposes
 * - very strict control on uploaded files, to prevent users uploading a malicious payload
 * - progress bar showing upload and processing file
 * - automatic resizing for images and conversion of images to .webp
 * - drag and drop file
 * 
 * WARNING:
 * ========
 * - WITHOUT excuteURLParams() THIS CLASS DOESN'T WORK!!!!! ==> see more info below
 * - By design, this class does NOT scale up images, only scales DOWN when maximum width/height are exceeded
 * - setStrictMimeTypeChecking(true) (=default) will compare the file header against the file extension when uploading. Meaning that a .webp file must have a webp file header. But with .csv files this can lead to a problem because they can have a text/plain file header, in that case: set this value to false for weaker security
 * 
 * UPLOADING MULTIPLE FILES
 * ========================
 * - the "name" property is automatically converted to a name with brackets ([]) in PHP (not the javascript version), so <dr-input-upload name="upload"> ==> <dr-input-upload name="upload[]">
 * - to retrieve values from $_POST you only need ONE INSTANCE of this class!
 * - to set values from database you need to loop through the database records and create an instance for each record
 * 
 * 
 * EXAMPLE in TCRUDDetailSaveControllerAJAX controller:
 * =========
 * CREATING:
        $this->objImage = new DRInputUpload();
        $this->objImage->setNameAndID('uplImage'); //NOTICE: when you have a multiple uploads <dr-input-upload> that the id will be automatically changed
        $this->objImage->setMultiple(true);//upload multiple files at once. NOTICE: the name will be automatically changed to the name with brackets for an array
        $this->objImage->setOnchange("setDirtyRecord()");        
        $this->objImage->setUploadDirPath(APP_PATH_UPLOADS.DIRECTORY_SEPARATOR.$objCurrentModule->getUploadDir().DIRECTORY_SEPARATOR.$this->getUploadDir());
        $this->objImage->setUploadDirURL(APP_URL_UPLOADS.'/'.$objCurrentModule->getUploadDir().'/'.$this->getUploadDir());
        $this->objImage->setAcceptArray(MIME_TYPES_IMAGES_GD);
        $this->objImage->setResizeImages(true);
        // $this->objImage->setMaxUploadSize(50000);
        if ($this->objImage->excuteURLParams()) //must be done AFTER the settings like uploaddir
            $this->stopHandlingURLParams(); //==== IMPORTANT!!
        $this->getFormGenerator()->addQuick(
            $this->objImage, 
            '', 
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_image_description', 'Image'),
            transm(CMS_CURRENTMODULE, 'form_productcategories_field_image_infoicon', 'Attach an image representing the products in the product category'),
        ); 
 * RETRIEVING ALL FIELDS AT ONCE AND SET IN MODEL:
 *      $this->objImage->viewToModelImage($this->getModel());
 * RETRIEVING INDIVIDUAL VALUES: 
 * 		$sTemp = $this->objImage->getValueSubmittedFileName();
 * 		$sTemp = $this->objImage->getValueSubmittedFileNameMedium();
 * 		$sTemp = $this->objImage->getValueSubmittedImageMaxHeight();
 * 		$sTemp = $this->objImage->getValueSubmittedImageThumbnailWidth();
 * 		etc...
 * SETTING ALL FIELDS AT ONCE IN MODEL:
 * 		$this->objImage->modelToViewImage($this->getModel());
 * SETTING INDIVIDUAL VALUES: 
 * 		$this->objImage->setFileName()
 * 		$this->objImage->setFileNameLarge();
 * 		etc...
 * 
 * 
 *************************************************************************************************************
 *      W A R N I N G :        WITHOUT excuteURLParams() THIS CLASS DOESN'T WORK!!!!!
 *************************************************************************************************************
 * YOU NEED TO CALL excuteURLParams() EXPLICITLY AFTER INSTANTIATION!!!
 * excuteURLParams handles the URL parameters and execute actions like: uploading, deleting and renaming.
 * Reason: this is NOT done in the constructor itself (like in many controllers), because you want to be able to set 
 * things like the upload directory BEFORE executing the upload/delete/rename.
 *
 *
 * Base examples: 
 * https://www.youtube.com/watch?v=Wtrin7C4b7w (drag and drop + file preview)
 * https://www.youtube.com/watch?v=H-091qVG6LM&t=902s (progressbar)
 * 
 * 
 * @todo: platte versie maken voor andere file types, zoals word of pdf
 * @todo: if file name already exists when uploading, rename it automatically
 * @todo: icoontjes van file type als geen image. dus een word-icoon bij een word document
 * @todo: paste image from clipboard and upload it
 * @todo: file history in sessie kan lang worden en daardoor vertragen. datum bijhouden en oude items wissen.
 * @todo: required attribute
 *  
 * 
 * 
 * @author Dennis Renirie
 * 26 aug 2025: DRInputUpload created
 *
 */
class DRInputUpload extends FormInputAbstract
{
	const ACTION_VARIABLE_UPLOAD = 'inputupload-upload';// $_POST['inputupload-upload']. When has value 1 uploads the file (XHR request)
	const ACTION_VARIABLE_DELETE = 'inputupload-delete';// $_POST['inputupload-delete']. When has value 1 deletes the file
	const ACTION_VARIABLE_RENAME = 'inputupload-rename';// $_POST['inputupload-rename']. When has value 1 renames the file
	const ACTION_VARIABLE_FIELDNAME = 'inputupload-fieldname';// $_POST['inputupload-fieldname']. the name of the current form field (there can be multiple form fields). This is the name in the form
	const ACTION_VARIABLE_FILENAME = 'inputupload-filename';// $_POST['inputupload-filename']. the name of the old file (or max size image)
	const ACTION_VARIABLE_FILENAMENEW = 'inputupload-filenamenew';// $_POST['inputupload-filenamenew']. the name of the old file
	const ACTION_VARIABLE_FILENAMELARGE = 'inputupload-filenamelarge';// $_POST['inputupload-filenamelarge']. the name of the old file
	const ACTION_VARIABLE_FILENAMEMEDIUM = 'inputupload-filenamemedium';// $_POST['inputupload-filenamemedium']. the name of the old file
	const ACTION_VARIABLE_FILENAMETHUMBNAIL = 'inputupload-filenamethumbnail';// $_POST['inputupload-filenamethumbnail']. the name of the old file
	const ACTION_VARIABLE_FILEALT = 'inputupload-filealt';// $_POST['inputupload-filealt']. the alt-name of the file. <img alt="mountain">
	const FIELD_UPLOADFILE_NAME = 'fileToUpload'; //the name of the internal form field that selects files and enables uploads
	
	const JSON_ERRORCODE_FILENAMENOTSPECIFIED = 100;
	const JSON_ERRORCODE_DIRECTORYTRAVERSAL = 101;
	const JSON_ERRORCODE_FILENOTEXIST = 102;
	const JSON_ERRORCODE_DELETEFAILED = 103;
	const JSON_ERRORCODE_FILENOTINHISTORY = 104;
	const JSON_ERRORCODE_RENAMEFAILED = 105;
	const JSON_ERRORCODE_FIELDNAMENOTSPECIFIED = 106;
	const JSON_ERRORCODE_FILEARRAYEMPTY = 107;
	const JSON_ERRORCODE_CREATEDIRFAILED = 108;
	const JSON_ERRORCODE_MIMETYPECHECKFAILED = 109;
	const JSON_ERRORCODE_FILEUPLOADERRORCODE = 110;
	const JSON_ERRORCODE_MOVEUPLOADEDFILEFAILED = 111;
	const JSON_ERRORCODE_RESIZEIMAGEFAILED = 112;
	const JSON_ERRORCODE_PHPEXT_GD_NOTLOADED = 113; //php extension GD not loaded
	const JSON_ERRORCODE_IMAGETYPENOTSUPPORTED = 114; 

	const JSONAK_RESPONSE_FILENAME = 'filename'; //filename of max size image
	const JSONAK_RESPONSE_FILENAME_LARGE = 'filenamelarge';
	const JSONAK_RESPONSE_FILENAME_MEDIUM = 'filenamemedium';
	const JSONAK_RESPONSE_FILENAME_THUMBNAIL = 'filenamethumbnail';
	const JSONAK_RESPONSE_IMAGE_ALT = 'imagealt';
	const JSONAK_RESPONSE_IMAGE_MAX_WIDTH = 'imagemaxwidth';
	const JSONAK_RESPONSE_IMAGE_MAX_HEIGHT = 'imagemaxheight';
	const JSONAK_RESPONSE_IMAGE_LARGE_WIDTH = 'imagelargewidth';
	const JSONAK_RESPONSE_IMAGE_LARGE_HEIGHT = 'imagelargeheight';
	const JSONAK_RESPONSE_IMAGE_MEDIUM_WIDTH = 'imagemediumwidth';
	const JSONAK_RESPONSE_IMAGE_MEDIUM_HEIGHT = 'imagemediumheight';
	const JSONAK_RESPONSE_IMAGE_THUMBNAIL_WIDTH = 'imagethumbnailwidth';
	const JSONAK_RESPONSE_IMAGE_THUMBNAIL_HEIGHT = 'imagethumbnailheight';
	const JSONAK_RESPONSE_FILE_EXISTS = 'fileexists';

	const SESSIONAK_DRINPUTUPLOAD = 'DRInputUpload'; //==> $_SESSION[SESSIONAK_DRINPUTUPLOAD]
	const SESSIONAK_DRINPUTUPLOAD_FILEHISTORY = 'filehistory';//==> $_SESSION[SESSIONAK_DRINPUTUPLOAD][SESSIONAK_DRINPUTUPLOAD_FILEHISTORY]: 1d ARRAY with file paths: a user can't just delete/rename any file on the server, it needs to be in the history array. This to prevents things like directory traversals

	private $sUploadDirPath = APP_PATH_UPLOADS;//the local path where the file should be uploaded
	private $sUploadDirURL = APP_URLTHISSCRIPT;//the url directory where uploaded files can be found
	private $sUploadNewURL = APP_URLTHISSCRIPT; //which url should be called to upload the file?
	private $sDeleteURL = APP_URLTHISSCRIPT; //which url should be called to delete the file?
	private $sRenameURL = APP_URLTHISSCRIPT; //which url should be called to delete the file?
	private $arrAcceptedFileTypes = MIME_TYPES_IMAGES_GD;
	private $bResizeImages = true; //resize images on upload? Default = true
	private $sFileName = ''; //filename of the upload. When image. When bResizeImages == true this is the max-size image. When bResizeImages == false this is the unresized image file
	private $sFileNameLarge = ''; //filename of the large-size image file (when bResizeImages == true)
	private $sFileNameMedium = ''; //filename of the medium-size image file (when bResizeImages == true)
	private $sFileNameThumbnail = ''; //filename of the thumbnail-size file (when bResizeImages == true)
	private $sImageAlt = ''; //alt text for the image file: <img alt="mountain">
	private $iImageMaxWidth = 0; 
	private $iImageMaxHeight = 0; 
	private $iImageLargeWidth = 0; 
	private $iImageLargeHeight = 0; 
	private $iImageMediumWidth = 0; 
	private $iImageMediumHeight = 0; 
	private $iImageThumbnailWidth = 0; 
	private $iImageThumbnailHeight = 0; 
	private $bFileExists = true; //registers if a file is found on disk or not
	private $arrCachedDecodedJSONSubmittedValue = null; //2d array ($arr[0]['filename'], $arr[1]['filename'] etc). We cache the decoded json string, so we don't have to jsondecode() every time to request each individual value.
	private $bStrictMemeTypeChecking = true;//when true uploaded files must have the same file extension as their content. So a .webp must have a webp header in the file. However csv files for example can have a text/plain header while their mime type is text/csv. In that case set this variable to false

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-input-upload');

		//setting defaults for urls
		$this->sUploadNewURL = addVariableToURL(APP_URLTHISSCRIPT, DRInputUpload::ACTION_VARIABLE_UPLOAD, '1');
		$this->sUploadNewURL = addVariableToURL($this->sUploadNewURL, ACTION_VARIABLE_RENDERVIEW, ACTION_VALUE_RENDERVIEW_JSONDATA);
		$this->sDeleteURL = addVariableToURL(APP_URLTHISSCRIPT, DRInputUpload::ACTION_VARIABLE_DELETE, '1');
		$this->sDeleteURL = addVariableToURL($this->sDeleteURL, ACTION_VARIABLE_RENDERVIEW, ACTION_VALUE_RENDERVIEW_JSONDATA);
		$this->sRenameURL = addVariableToURL(APP_URLTHISSCRIPT, DRInputUpload::ACTION_VARIABLE_RENAME, '1');
		$this->sRenameURL = addVariableToURL($this->sRenameURL, ACTION_VARIABLE_RENDERVIEW, ACTION_VALUE_RENDERVIEW_JSONDATA);
		$this->sUploadDirURL = dirname(APP_URLTHISSCRIPT);

		// $this->setAttribute('disabled', ''); 
		// $this->setAttribute('value', ''); 
		$this->setAttribute('transdrop', transg('dr-input-upload-clickordropfilehere', 'Click or drop file here'));
		$this->setAttribute('transtypenotsupported', transg('dr-input-upload-filetypenotsupported', 'File type is not supported.<br><br>Supported are:<br>'));
		$this->setAttribute('transdelete', transg('dr-input-upload-delete', 'Delete'));
		$this->setAttribute('transpreview', transg('dr-input-upload-preview', 'Preview'));
		$this->setAttribute('transrename', transg('dr-input-upload-rename', 'Rename'));
		$this->setAttribute('transalt', transg('dr-input-upload-alt', 'Set img alt text'));
		$this->setAttribute('transaltdescription', transg('dr-input-upload-altdescription', 'Change or set ALT text of image: <dr-icon-info>Images can have an alternative (alt) text to describe the contents of the image.<br>This is useful for SEO purposes, text-based browsers, screen readers and if image can not be displayed because of an error.<br>Example: &lt;img src=&quot;mountain.jpg&quot; alt=&quot;boat on a lake with mountain in background&quot;&gt;</dr-icon-info>'));
		$this->setAttribute('transaltsetbutton', transg('dr-input-upload-altsetbutton', 'Set text'));
		$this->setAttribute('transcopyurlclipboard', transg('dr-input-upload-copyurltoclipboard', 'Copy URL to clipboard'));
		$this->setAttribute('transcopyurlclipboarddone', transg('dr-input-upload-copyurltoclipboarddone', 'Copied URL to clipboard:'));
		$this->setAttribute('transclose', transg('dr-input-upload-close', 'Close'));
		$this->setAttribute('transcancel', transg('dr-input-upload-cancel', 'Cancel'));
		$this->setAttribute('transerror', transg('dr-input-upload-error', 'Error'));
		$this->setAttribute('transerrorunknown', transg('dr-input-upload-errorunknown', 'Error occurred when uploading.<br>Do you have a working network/internet connection?'));
		$this->setAttribute('transerrortimeout', transg('dr-input-upload-errortimeout', 'Connection timed out'));
		$this->setAttribute('transok', transg('dr-input-upload-ok', 'Ok'));
		$this->setAttribute('transuploading', transg('dr-input-upload-uploading', 'Uploading file ...'));
		$this->setAttribute('transprocessing', transg('dr-input-upload-processing', 'Processing file ...'));
		$this->setAttribute('transqueue', transg('dr-input-upload-queue', 'In queue ...'));
		$this->setAttribute('transabort', transg('dr-input-upload-abort', 'Abort upload'));
		$this->setAttribute('transerrormaxsizeexceed', transg('dr-input-upload-maxfilesizeexceeded', 'Maximum file size exceeded.<br>Max file size: '));
		$this->setAttribute('whitelist', WHITELIST_FILENAME);
		$this->setAttribute('accept', implode(',', $this->arrAcceptedFileTypes));
		$this->setAttribute('maxsize', getMaxFileUploadSize());

		//proper includes
		includeJSWebcomponent('dr-context-menu');
		includeJSWebcomponent('dr-dialog');
		includeJSWebcomponent('dr-input-text');
		includeJSWebcomponent('dr-progress-bar');
		includeJSWebcomponent('dr-icon-info');
		includeJSWebcomponent('dr-input-upload'); //dependencies first
	}

	/**
	 * 1. handles URL parameters: delete, rename, upload
	 * 2. sets proper urls for actions: delete, rename, upload	
	 * 
	 * WARNING: make sure you do settings like setUploadDirPath() FIRST!!! (otherwise it is handled before the proper upload directory or form-field-name is set)
	 * 
	 * @return bool whether the url parameters are handled, NOT if there were errors because we want to prevent further execution of the controller like saving
	 */
	public function excuteURLParams()
	{

		//==== make changes to urls based on the name upload path and form-field-name
		$this->sUploadNewURL = addVariableToURL($this->sUploadNewURL, DRInputUpload::ACTION_VARIABLE_FIELDNAME, $this->getName());
		$this->sDeleteURL = addVariableToURL($this->sDeleteURL, DRInputUpload::ACTION_VARIABLE_FIELDNAME, $this->getName());
		$this->sRenameURL = addVariableToURL($this->sRenameURL, DRInputUpload::ACTION_VARIABLE_FIELDNAME, $this->getName());


		$this->setAttribute('uploaddirurl', $this->sUploadDirURL);
		$this->setAttribute('uploadnewurl', $this->sUploadNewURL);
		$this->setAttribute('uploadfield', DRInputUpload::FIELD_UPLOADFILE_NAME);
		$this->setAttribute('deleteurl', $this->sDeleteURL);
		$this->setAttribute('renameurl', $this->sRenameURL);



		//==== UPLOAD
		if (isset($_GET[DRInputUpload::ACTION_VARIABLE_UPLOAD]))
		{
			if ($_GET[DRInputUpload::ACTION_VARIABLE_UPLOAD] == 1)
			{
				$this->handleUpload();
				return true;
			}
		}


		//==== DELETE
		if (isset($_GET[DRInputUpload::ACTION_VARIABLE_DELETE]))
		{
			if ($_GET[DRInputUpload::ACTION_VARIABLE_DELETE] == 1)
			{
				$this->handleDelete();
				return true;
			}
		}

		//==== RENAME
		if (isset($_GET[DRInputUpload::ACTION_VARIABLE_RENAME]))
		{
			if ($_GET[DRInputUpload::ACTION_VARIABLE_RENAME] == 1)
			{
				$this->handleRename();
				return true;
			}
		}

		return false;//url params not handled
	}


	/**
	 * what is maximum upload size in bytes?
	 * WARNING: can't set a value that exceeds getMaxFileUploadSize()!!!
	 */
	public function setMaxUploadSize($iSizeBytes)
	{
		if (getMaxFileUploadSize() < $iSizeBytes)
			$this->setAttributeAsInt('maxsize', getMaxFileUploadSize());
		else
			$this->setAttributeAsInt('maxsize', $iSizeBytes);
	}

	/**
	 * what is maximum upload size in bytes?
	 * WARNING: it can return getMaxFileUploadSize() instead of the value set with setMaxUploadSize()
	 */
	public function getMaxUploadSize()
	{
		$iValueBytes = $this->getAttributeAsInt('maxsize');

		if (getMaxFileUploadSize() < $iValueBytes)
			$iValueBytes = getMaxFileUploadSize(); 

		return $iValueBytes;
	}

	/**
	 * should component automatically resize images when uploading?
	 */
	public function setResizeImages($bResize)
	{
		$this->bResizeImages = $bResize;
	}

	/**
	 * should component automatically resize images when uploading?
	 */
	public function getResizeImages()
	{
		return $this->bResizeImages;
	}

	/**
	 * should enable strict mime type checking?
	 */
	public function setStrictMimeTypeChecking($bStrict)
	{
		$this->bStrictMemeTypeChecking = $bStrict;
	}

	/**
	 * should enable strict mime type checking?
	 */
	public function getStrictMimeTypeChecking()
	{
		return $this->bStrictMemeTypeChecking;
	}

	/**
	 * set disabled
	 */
	public function setDisabled($bDisabled)
	{
		if ($bDisabled)
			$this->setAttribute('disabled', '');
		else
			$this->removeAttribute('disabled');
	}


	/**
	 * get disabled
	 * 
	 * @return boolean
	 */
	public function getDisabled()
	{
		if ($this->hasAttribute('disabled'))
			return $this->getAttributeAsBool('disabled');
		else
			return false;
	}	

	/**
	 * set accepted file types as array
	 * @param $arrTypes - for example: array(MIME_TYPE_PNG, MIME_TYPE_JPG)
	 */
	public function setAcceptArray($arrTypes)
	{
		$this->arrAcceptedFileTypes = $arrTypes;
		$this->setAttribute('accept', implode(',', $this->arrAcceptedFileTypes));
	}

	/**
	 * set value with a string where the decimal separator is ALWAYS a dot (.)
	 */
	public function setValue($sValue)
	{
		$this->setAttribute('value', $sValue);
	}

	/**
	 * get value. the return value is a string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->getAttribute('value');
	}

	/**
	 * set multiple files can be uploaded at once
	 * 
	 * WARNING: 
	 * The "name"-attribute will be automatically changed to a name with brackets when bAllowMultipleUploads == true
	 */
	public function setMultiple($bAllowMultipleUploads)
	{
		$this->setAttributeAsBool('multiple', $bAllowMultipleUploads);
		$this->setIsArray($bAllowMultipleUploads); //=====================> makes name an array when multiple uploads are possible
	}

	/**
	 * get multiple files can be uploaded at once
	 * 
	 * @return string
	 */
	public function getMultiple()
	{
		return $this->getAttributeAsBool('multiple');
	}	

	/**
	 * set the upload path WITHOUT trailing slash, ie c:\xamp\htdocs\cms5\uploads
	 * this is the local path on the server, this is not exposed in the webcomponent for security reasons.
	 * When not specified, the default upload directory is assumed
	 */
	public function setUploadDirPath($sDirectoryPath = APP_PATH_UPLOADS)
	{
		$this->sUploadDirPath = $sDirectoryPath;
	}

	/**
	 * get the upload directory
	 * local path on server, this is not exposed in the webcomponent for security reasons.
	 * When not specified, the default upload directory APP_PATH_UPLOADS is returned
	 * 	 
	 * @return string
	 */
	public function getUploadDirPath()
	{
		return $this->sUploadDirPath;
	}

	/**
	 * set the upload directory
	 * URL on server
	 * When not specified, the default upload directory APP_URL_UPLOADS is assumed
	 */
	public function setUploadDirURL($sDirectoryURL = APP_URL_UPLOADS)
	{
		$this->sUploadDirURL = $sDirectoryURL;
		$this->setAttribute('uploaddirurl', $this->sUploadDirURL);		
	}

	/**
	 * get the upload directory
	 * URL on server
	 * When not specified, the default upload directory APP_URL_UPLOADS is returned
	 * 	 
	 * @return string
	 */
	public function getUploadDirURL()
	{
		return $this->sUploadDirURL;
	}




	/**
	 * handles upload itself, done by javascript via XHR
	 */
	private function handleUpload()
	{
		$sName = '';
		$sType = '';
		$sTempName = '';
		$iError = 0;
		$sUploadPath = '';

        //==== DEFAULT ====
        $arrJSONResponse = array(
                                    JSONAK_RESPONSE_MESSAGE => '',
                                    JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_OK,
                                );		

		//==== is a fieldname set?
		if (!isset($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME]))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_GET['.DRInputUpload::ACTION_VARIABLE_FIELDNAME.'] doesn\'t exist. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FIELDNAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}
		elseif ($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME] === '')
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_GET['.DRInputUpload::ACTION_VARIABLE_FIELDNAME.'] is empty. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FIELDNAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}
	

		//==== OURS TO HANDLE? or another DRInputUpload component? ====
		if ($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME] !== $this->getName())
			return false;


		//==== are the files at all to upload?====
		if (count($_FILES) == 0)
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_FILES array is empty. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILEARRAYEMPTY;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}

		//==== create directory
		if (!is_dir($this->sUploadDirPath))
		{
			if (!mkdir($this->sUploadDirPath, 0777, true))
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'creation of directory "'.$this->sUploadDirPath.'" failed');			

				$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            	$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_CREATEDIRFAILED;
			
            	header(JSONAK_RESPONSE_HEADER);
            	echo json_encode($arrJSONResponse);               
            	return false; //stop further execution to display the error  
			}
		}

		//==== set local vars ====
		$sFileName = sanitizeFileName(basename($_FILES[DRInputUpload::FIELD_UPLOADFILE_NAME]['name']));
		$sFileName = generateUniqueFileName($this->sUploadDirPath, $sFileName); //search for a filename that is not in use
		$sMimeType = $_FILES[DRInputUpload::FIELD_UPLOADFILE_NAME]['type'];
		$sTempName = $_FILES[DRInputUpload::FIELD_UPLOADFILE_NAME]['tmp_name'];
		$iError = $_FILES[DRInputUpload::FIELD_UPLOADFILE_NAME]['error'];
		$sUploadPath = $this->sUploadDirPath.DIRECTORY_SEPARATOR.$sFileName;


		//==== PROPER MIME TYPE?
		if (!$this->isValidFileType($sTempName, $sMimeType))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'file type ('.$sMimeType.') not in array with accepted file types: '.implode(",", $this->arrAcceptedFileTypes));			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_mimetypecheckfailed', 'File type not supported');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_MIMETYPECHECKFAILED;
		
			header(JSONAK_RESPONSE_HEADER);
			echo json_encode($arrJSONResponse);               
			return false; //stop further execution to display the error  
		}			


		//==== ERRORS OCCURRED?
		if ($iError > 0)
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'file upload returned error code: '.$iError);			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILEUPLOADERRORCODE;
		
			header(JSONAK_RESPONSE_HEADER);
			echo json_encode($arrJSONResponse);               
			return false; //stop further execution to display the error  
		}

		//==== MOVE TEMP => proper directory
		if (!move_uploaded_file($sTempName, $sUploadPath)) //error
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'move uploaded file failed: '.$sUploadPath);			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_MOVEUPLOADEDFILEFAILED;
		
			header(JSONAK_RESPONSE_HEADER);
			echo json_encode($arrJSONResponse);               
			return false; //stop further execution to display the error  
		}

		//==== resize image
		if (isMimeImage($sMimeType) && ($this->bResizeImages)) //only resize when 1. it is an image 2. we should resize images
		{
			if (!$this->resizeImage($arrJSONResponse, $sMimeType, $sUploadPath)) //error
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'resize image file failed: '.$sUploadPath);			

				//remove uploaded file
				if (!unlink($sUploadPath))
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'removing uploaded file failed: '.$sUploadPath);			


				if (($arrJSONResponse[JSONAK_RESPONSE_MESSAGE] == '') || ($arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] == 0))
				{
					$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_resizefailed', 'Resizing image failed.<br>Uploaded file removed from server!');
					$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_RESIZEIMAGEFAILED;
				}
			
				header(JSONAK_RESPONSE_HEADER);
				echo json_encode($arrJSONResponse);               
				return false; //stop further execution to display the error  
			}
		}
		else //unresized images and regular files
		{
			//==== UPDATE FILE HISTORY
			$this->addToFileHistory($sUploadPath);

			//==== UPDATE JSON RESPONSE
			$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME] = basename($sUploadPath);
		}

		//==== IMG ALT TEXT ==== <img alt="mountain">
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_ALT] = '';//empty alt by default
		

        //==== HANDLE OK MESSAGE ====
        $arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_message_upload_ok', 'Upload succesful');
		$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = JSONAK_RESPONSE_OK;

		header(JSONAK_RESPONSE_HEADER);
		echo json_encode($arrJSONResponse);               
		return true; //stop further execution to display the ok message       

	}

	/**
	 * resizes images
	 * 
	 * @todo this function is meant to also support Imagick in the future (hence it sends GD resize to another function)
	 * 
	 * @return bool returns true when resizing was successful or not an image, returns false when resizing failed
	 */
	private function resizeImage(&$arrJSONResponse, &$sMimeType, &$sUploadPath)
	{
		if (!extension_loaded(PHP_EXT_GD))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Cannot resize, PHP extension not loaded: '.PHP_EXT_GD);			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_phpextensiongd_notloaded', 'Cannot resize image.<br>Image manipulation extension not loaded');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_PHPEXT_GD_NOTLOADED;
		
			return false; //stop further execution to display the error  
		}
		else
		{
			if (!$this->resizeImageGD($arrJSONResponse, $sMimeType, $sUploadPath))
				return false;
		}

		return true;
	}	

	/**
	 * resizes images with GD extension
	 * 
	 * @return bool returns true when resizing was successful or not an image, returns false when resizing failed
	 */
	private function resizeImageGD(&$arrJSONResponse, &$sMimeType, &$sUploadPath)
	{
		//JSONAK_RESPONSE_FILENAME	
		$objImgOrg = null;
		$sPathMaxTemp = ''; //this is only a tempory path, because we rename it back to the original filename
		$sPathMax = ''; 
		$sPathLarge = '';
		$sPathMedium = '';
		$sPathThumbnail = '';
		$objImgMax = null;
		$objImgLarge = null;
		$objImgMedium = null;
		$objImgThumbnail = null;

		//default JSON response
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_WIDTH] = 0; 
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_HEIGHT] = 0;
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_WIDTH] = 0;
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_HEIGHT] = 0;
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_WIDTH] = 0;
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_HEIGHT] = 0;
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_WIDTH] = 0;
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_HEIGHT] = 0;


		//==== READ THE ORIGINAL IMAGE
		disableCustomErrorHandler(); //GD throws an error you can't suppress

		switch($sMimeType)
		{
			case MIME_TYPE_AVIF:
				$objImgOrg = imagecreatefromavif($sUploadPath); 
				break;			
			case MIME_TYPE_BMP:
				$objImgOrg = imagecreatefrombmp($sUploadPath); 
				break;			
			case MIME_TYPE_GIF:
				$objImgOrg = imagecreatefromgif($sUploadPath); 
				break;			
			case MIME_TYPE_JPEG:
				$objImgOrg = imagecreatefromjpeg($sUploadPath);
				break;
			case MIME_TYPE_PNG:
				$objImgOrg = imagecreatefrompng($sUploadPath); 
				break;
			case MIME_TYPE_WEBP:
				$objImgOrg = imagecreatefromwebp($sUploadPath); 
				break;
			default:
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Mime type "'.$sMimeType.'" not supported');			

				$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_imagetypenotrecognized', 'Image type not supported');
				$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_IMAGETYPENOTSUPPORTED;
			
				return false; //stop further execution to display the error  
		}

		enableCustomErrorHandler(true); //restore error handler

		//==== check if read was successful
		if ($objImgOrg == false)
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Creating image object failed for image "'.$sUploadPath.'"');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_imagetypenotrecognized', 'Image type not supported');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_IMAGETYPENOTSUPPORTED;
		
			return false; //stop further execution to display the error  			
		}

			
		//==== RESIZE IMAGE		
		$objImgMax = 		$this->imagescaleGD($objImgOrg, APP_CMS_IMAGE_RESIZE_MAX_WIDTHPX, APP_CMS_IMAGE_RESIZE_MAX_HEIGHTPX, $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_WIDTH], $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_HEIGHT]);
		$objImgLarge = 		$this->imagescaleGD($objImgOrg, APP_CMS_IMAGE_RESIZE_LARGE_WIDTHPX, APP_CMS_IMAGE_RESIZE_LARGE_HEIGHTPX, $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_WIDTH], $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_HEIGHT]);
		$objImgMedium = 	$this->imagescaleGD($objImgOrg, APP_CMS_IMAGE_RESIZE_MEDIUM_WIDTHPX, APP_CMS_IMAGE_RESIZE_MEDIUM_HEIGHTPX, $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_WIDTH], $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_HEIGHT]);
		$objImgThumbnail = 	$this->imagescaleGD($objImgOrg, APP_CMS_IMAGE_RESIZE_THUMBNAIL_WIDTHPX, APP_CMS_IMAGE_RESIZE_THUMBNAIL_HEIGHTPX, $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_WIDTH], $arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_HEIGHT]);

		//==== CREATE FILE NAMES
		$sPathMaxTemp = 	concatDirFileSafe($this->sUploadDirPath, '', generateUniqueFileName($this->sUploadDirPath, $this->generateFileNameMax(basename($sUploadPath), 'webp')));
		$sPathMax = 		concatDirFileSafe($this->sUploadDirPath, '', generateUniqueFileName($this->sUploadDirPath, $this->generateFileNameMax(basename($sUploadPath), 'webp', false)));
		$sPathLarge = 		concatDirFileSafe($this->sUploadDirPath, '', generateUniqueFileName($this->sUploadDirPath, $this->generateFileNameLarge(basename($sUploadPath), 'webp')));
		$sPathMedium = 		concatDirFileSafe($this->sUploadDirPath, '', generateUniqueFileName($this->sUploadDirPath, $this->generateFileNameMedium(basename($sUploadPath), 'webp')));
		$sPathThumbnail = 	concatDirFileSafe($this->sUploadDirPath, '', generateUniqueFileName($this->sUploadDirPath, $this->generateFileNameThumbnail(basename($sUploadPath), 'webp')));

		//==== SAVE RESIZED IMAGE
		//I choose WebP, because AVIF is ridiculously slow: 27 seconds for 4k image on an intel i9k 10th gen, compared to 2 seconds for webp for the same 4k image
		imagewebp($objImgMax, $sPathMaxTemp, 85);
		imagewebp($objImgLarge, $sPathLarge, 85);
		imagewebp($objImgMedium, $sPathMedium, 85);
		imagewebp($objImgThumbnail, $sPathThumbnail, 85);

		//==== CLEAN UP
		imagedestroy($objImgOrg);
		imagedestroy($objImgMax);
		imagedestroy($objImgLarge);
		imagedestroy($objImgMedium);
		imagedestroy($objImgThumbnail);

		//==== DELETE & RENAME ORIGINAL
		//we can only delete and rename it after the original file is read and closed

		//delete original
		if (!unlink($sUploadPath))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'removing original file failed: '.$sUploadPath);			
		else
			$this->removeFromFileHistory($sUploadPath);

		//rename to original (but smaller and maybe a different extension than original)
		if (!rename($sPathMaxTemp, $sPathMax))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'renaming max size file to original file failed: '.$sPathMaxTemp.' ==> '.$sPathMax);			

		//==== UPDATE FILE HISTORY
		$this->addToFileHistory($sPathMax);
		$this->addToFileHistory($sPathLarge);
		$this->addToFileHistory($sPathMedium);
		$this->addToFileHistory($sPathThumbnail);

		//==== UPDATE JSON RESPONSE
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME] = basename($sPathMax);
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_LARGE] = basename($sPathLarge);
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_MEDIUM] = basename($sPathMedium);
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_THUMBNAIL] = basename($sPathThumbnail);

		return true;
	}	

	/**
	 * creates a path for a max sized image
	 * 
	 * @param string $sOrgFileName original file path
	 * @param string $sExtensionWithoutDot file exension without dot(.) like 'jpg' or 'webp'
	 * @param bool $bAddMaxToFilename adds '_max' to filename
	 */
	private function generateFileNameMax($sOrgFileName, $sExtensionWithoutDot, $bAddMaxToFilename = true)
	{
		$arrPath = array();
    	$arrPath = pathinfo($sOrgFileName);



		//add max
		if ($bAddMaxToFilename)
		{
			if (strrpos($sOrgFileName, '_max')) //if '_max' already exists then don't add
				return $arrPath['filename'].'.'.$sExtensionWithoutDot;			
			else
				return $arrPath['filename'].'_max.'.$sExtensionWithoutDot;
		}
		else
			return $arrPath['filename'].'.'.$sExtensionWithoutDot;
	}

	/**
	 * creates a path for a large sized image
	 */
	private function generateFileNameLarge($sOrgFileName, $sExtensionWithoutDot)
	{
		$arrPath = array();
    	$arrPath = pathinfo($sOrgFileName);

		if (strrpos($sOrgFileName, '_large')) //if '_large' already exists then don't add
			return $arrPath['filename'].'.'.$sExtensionWithoutDot;
		else
			return $arrPath['filename'].'_large.'.$sExtensionWithoutDot;
	}

	/**
	 * creates a path for a medium sized image
	 */
	private function generateFileNameMedium($sOrgFileName, $sExtensionWithoutDot)
	{
		$arrPath = array();
    	$arrPath = pathinfo($sOrgFileName);

		if (strrpos($sOrgFileName, '_medium')) //if '_medium' already exists then don't add
			return $arrPath['filename'].'.'.$sExtensionWithoutDot;
		else
			return $arrPath['filename'].'_medium.'.$sExtensionWithoutDot;
	}

	/**
	 * creates a path for a thumbnail sized image
	 */
	private function generateFileNameThumbnail($sOrgFileName, $sExtensionWithoutDot)
	{
		$arrPath = array();
    	$arrPath = pathinfo($sOrgFileName);

		if (strrpos($sOrgFileName, '_thumbnail')) //if '_thumbnail' already exists then don't add
			return $arrPath['filename'].'.'.$sExtensionWithoutDot;
		else
			return $arrPath['filename'].'_thumbnail.'.$sExtensionWithoutDot;
	}

	/**
	 * creates an alt text (<img alt="mountain">) based on a filename
	 */
	private function generateAltImage($sFilename)
	{
		$arrParts = pathinfo($sFilename);

		return $arrParts['filename']; //strip the file extension
	}

	/**
	 * Scales a gd image.
	 * It checks if the max width and height are exceeded and scales proportionally.
	 *
	 * WARNING:
	 * By design, this function does not scale up, only scales down when maximum width/height are exceeded
	 * 
	 * explanation:
	 * I calculate 2 multiplicationfactors (1 for width, 1 for height).
	 * And I apply the highest factor to both height and width
	 * 
	 * @param GdImage $objImgOrg
	 * @param int $iMaxWidth maximum image width that can not be exceeded
	 * @param int $iMaxHeight maximum image height that can not be exceed
	 * @param int $iNewWidth the resized image width (meant as return value)
	 * @param int $iNewHeight the resized image height (meant as return value)
	 * @return GdImage
	 */
	private function imagescaleGD(GdImage $objImgOrg, $iMaxWidth, $iMaxHeight, &$iNewWidth, &$iNewHeight)
	{
		$iWidthImgOrg = imagesx($objImgOrg);
		$iHeightImgOrg = imagesy($objImgOrg);
		$fMultiplyFactorWidth = 0.0;
		$fMultiplyFactorHeight = 0.0;

		//needs resize
		if (($iWidthImgOrg > $iMaxWidth) || ($iHeightImgOrg > $iMaxHeight)) 
		{		
			$fMultiplyFactorWidth = $iWidthImgOrg / $iMaxWidth;
			$fMultiplyFactorHeight = $iHeightImgOrg / $iMaxHeight;

			//which multiply factor is highest?
			if (compareFloat($fMultiplyFactorWidth, $fMultiplyFactorHeight, 5) == 1) //width is bigger than height
			{
				$iNewWidth = floor($iWidthImgOrg / $fMultiplyFactorWidth); //use multiplication factor of width
				$iNewHeight = floor($iHeightImgOrg / $fMultiplyFactorWidth); //use multiplication factor of width
			}
			else //height is bigger than width
			{
				$iNewWidth = floor($iWidthImgOrg / $fMultiplyFactorHeight); //use multiplication factor of height
				$iNewHeight = floor($iHeightImgOrg / $fMultiplyFactorHeight); //use multiplication factor of height
			}

			return imagescale($objImgOrg, $iNewWidth, $iNewHeight, IMG_BICUBIC); // IMG_NEAREST_NEIGHBOUR, IMG_BILINEAR_FIXED, IMG_BICUBIC, IMG_BICUBIC_FIXED
		}
		else //no resize: return copy
		{
			$iNewWidth = $iWidthImgOrg;
			$iNewHeight = $iHeightImgOrg;

			return imagecrop($objImgOrg, array('x'=>0,'y'=>0,'width'=>$iWidthImgOrg,'height'=>$iHeightImgOrg)); //makes a clone of the original (i use the crop function that doesn't crop)
		}
	}

	/**
	 * handles file deletion
	 */
	private function handleDelete()
	{

        //==== DEFAULT ====
        $arrJSONResponse = array(
                                    JSONAK_RESPONSE_MESSAGE => '',
                                    JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_OK,
                                );


		//==== FIELDNAME SET? ===
		if (!isset($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME]))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_GET['.DRInputUpload::ACTION_VARIABLE_FIELDNAME.'] doesn\'t exist. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FIELDNAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}
		elseif ($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME] === '')
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_GET['.DRInputUpload::ACTION_VARIABLE_FIELDNAME.'] is empty. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FIELDNAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}


		//==== OURS TO HANDLE? or another DRInputUpload component? ====
		if ($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME] !== $this->getName())
			return false;


		//==== FILE NAME SPECIFIED? ====
		if (!isset($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: file name not specified in url parameter: '.DRInputUpload::ACTION_VARIABLE_FILENAME);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILENAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   			
		}


		//==== DIRECTORY TRAVERSAL? ====
		$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME] = filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);
		if (isDirectoryTraversal($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]))						
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: directory traversal detected in: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_DIRECTORYTRAVERSAL;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error 
		}

		//==== CHECK FILE HISTORY ====
		$sFileExtension =  		getFileExtension($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);
		$sSafePathMax = 		concatDirFileSafe($this->sUploadDirPath, '', filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]), $sFileExtension, false);
		$sSafePathLarge = 		concatDirFileSafe($this->sUploadDirPath, '', filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMELARGE]), $sFileExtension);
		$sSafePathMedium = 		concatDirFileSafe($this->sUploadDirPath, '', filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMEMEDIUM]), $sFileExtension);
		$sSafePathThumbnail = 	concatDirFileSafe($this->sUploadDirPath, '', filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMETHUMBNAIL]), $sFileExtension);


		//regular delete
		if (!$this->deleteInternal($sSafePathMax, $arrJSONResponse))
			return false;

		//for resized images
		if ($this->bResizeImages)
		{
			if (!$this->deleteInternal($sSafePathLarge, $arrJSONResponse))
				return false;
			if (!$this->deleteInternal($sSafePathMedium, $arrJSONResponse))
				return false;
			if (!$this->deleteInternal($sSafePathThumbnail, $arrJSONResponse))
				return false;
		}

		
        //==== HANDLE OK MESSAGE ====
        $arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_message_delete_ok', 'Delete succesful');
		$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = JSONAK_RESPONSE_OK;

		header(JSONAK_RESPONSE_HEADER);
		echo json_encode($arrJSONResponse);               
		return true; //stop further execution to display the ok message       
				
	}	

	/**
	 * deletes a file internally
	 * (subprocess of handleRename())
	 * 
	 * because we need to delete multiple files at once when it is an image, 
	 * it is more efficient to use deleteInternal() on each file
	 */	
	private function deleteInternal($sSafePath, &$arrJSONResponse)
	{


		if(!$this->existsInFileHistory($sSafePath))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: file not in history array: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILENOTINHISTORY;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error 			
		}


		//==== FILE EXISTS? ====
		if (!file_exists($sSafePath))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: file to delete does not exist: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');			
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILENOTEXIST;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error
		}
				

		//==== ACTUAL DELETE ====
		if (!unlink($sSafePath)) //actual file deletion
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: failed to delete file: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');			
			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_DELETEFAILED;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error			
		}

		$this->removeFromFileHistory($sSafePath);

		return true;
	}


	/**
	 * handles file rename
	 */
	private function handleRename()
	{
		$bSuccess = true;
		$iDelIndex = -1;

        //==== DEFAULT ====
        $arrJSONResponse = array(
                                    JSONAK_RESPONSE_MESSAGE => '',
                                    JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_OK,
                                );
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME] 				= '';
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_LARGE] 		= '';
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_MEDIUM]		= '';
		$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_THUMBNAIL]		= '';

		//==== FIELDNAME SET? ===
		if (!isset($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME]))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_GET['.DRInputUpload::ACTION_VARIABLE_FIELDNAME.'] doesn\'t exist. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FIELDNAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}
		elseif ($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME] === '')
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$_GET['.DRInputUpload::ACTION_VARIABLE_FIELDNAME.'] is empty. can not upload');			

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FIELDNAMENOTSPECIFIED;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}


		//==== OURS TO HANDLE? or another DRInputUpload component? ====
		if ($_GET[DRInputUpload::ACTION_VARIABLE_FIELDNAME] !== $this->getName())
			return false;


		//==== url vars exist? ====
		if ((!isset($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME])) || (!isset($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW])))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: url variables dont exist: '.DRInputUpload::ACTION_VARIABLE_FILENAME.' or '.DRInputUpload::ACTION_VARIABLE_FILENAMENEW);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILENAMENOTSPECIFIED;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error    
		}

		//==== directory traversal? ====
		$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME] = filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);
		$_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW] = filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]);
		$bDirTravOrg = isDirectoryTraversal($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);
		$bDirTravNew = isDirectoryTraversal($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]);

		if ($bDirTravOrg || $bDirTravNew)
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: directory traversal detected: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME].' or '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]);

			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_DIRECTORYTRAVERSAL;
			
            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error    
		}

		//==== avoid dot file exension (like .csv)
		//make sure there is no dot (.) in the new filename
		$_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW] = getFileNameWithoutExtension($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]);
		$_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW] = filterBadCharsBlackList($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW], '.'); //the rest of the filename can't contain a dot (.), because that might lead to extension manipulation by user	

		//==== set path ====
		$sFileExtension =  				getFileExtension($_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);
		$sSafePathOldMax = 				concatDirFileSafe($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAME], $sFileExtension);
		$sSafePathOldLarge = 			concatDirFileSafe($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAMELARGE], $sFileExtension);
		$sSafePathOldMedium = 			concatDirFileSafe($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAMEMEDIUM], $sFileExtension);
		$sSafePathOldThumbnail = 		concatDirFileSafe($this->sUploadDirPath, '', $_GET[DRInputUpload::ACTION_VARIABLE_FILENAMETHUMBNAIL], $sFileExtension);
		$sSafeFileNameNewMax = 			$this->generateFileNameMax(filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]), $sFileExtension, false);
		$sSafeFileNameNewLarge = 		$this->generateFileNameLarge(filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]), $sFileExtension, false);
		$sSafeFileNameNewMedium = 		$this->generateFileNameMedium(filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]), $sFileExtension, false);
		$sSafeFileNameNewThumbnail = 	$this->generateFileNameThumbnail(filterFileName($_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]), $sFileExtension, false);
		$sSafeFileNameNewMax = 			generateUniqueFileName($this->sUploadDirPath, $sSafeFileNameNewMax);
		$sSafeFileNameNewLarge = 		generateUniqueFileName($this->sUploadDirPath, $sSafeFileNameNewLarge);
		$sSafeFileNameNewMedium = 		generateUniqueFileName($this->sUploadDirPath, $sSafeFileNameNewMedium);
		$sSafeFileNameNewThumbnail = 	generateUniqueFileName($this->sUploadDirPath, $sSafeFileNameNewThumbnail);
		$sSafePathNewMax = 				concatDirFileSafe($this->sUploadDirPath, '', $sSafeFileNameNewMax);
		$sSafePathNewLarge = 			concatDirFileSafe($this->sUploadDirPath, '', $sSafeFileNameNewLarge);
		$sSafePathNewMedium = 			concatDirFileSafe($this->sUploadDirPath, '', $sSafeFileNameNewMedium);
		$sSafePathNewThumbnail = 		concatDirFileSafe($this->sUploadDirPath, '', $sSafeFileNameNewThumbnail);

		//regular rename
		if ($this->renameInternal($sSafePathOldMax, $sSafePathNewMax, $arrJSONResponse))
			$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME] = $sSafeFileNameNewMax;
		else
			return false;

		//for resized images
		if ($this->bResizeImages)
		{
			//large
			if ($this->renameInternal($sSafePathOldLarge, $sSafePathNewLarge, $arrJSONResponse))
				$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_LARGE] = $sSafeFileNameNewLarge;
			else
				return false;

			//medium
			if ($this->renameInternal($sSafePathOldMedium, $sSafePathNewMedium, $arrJSONResponse))
				$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_MEDIUM] = $sSafeFileNameNewMedium;
			else
				return false;

			//thumbnail
			if ($this->renameInternal($sSafePathOldThumbnail, $sSafePathNewThumbnail, $arrJSONResponse))
				$arrJSONResponse[DRInputUpload::JSONAK_RESPONSE_FILENAME_THUMBNAIL] = $sSafeFileNameNewThumbnail;
			else
				return false;
		}

        //==== HANDLE OK MESSAGE ====
		$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_message_rename_ok', 'Rename succesful');
		$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = JSONAK_RESPONSE_OK;

		header(JSONAK_RESPONSE_HEADER);
		echo json_encode($arrJSONResponse);               
		return true; //stop further execution to display the ok message       
	}	


	/**
	 * renames a file internally
	 * (subprocess of handleRename())
	 * 
	 * because we need to rename multiple files at once when it is an image, 
	 * it is more efficient to use renameInternal() on each file
	 */
	private function renameInternal($sPathFrom, $sPathTo, &$arrJSONResponse)
	{
		//==== check file history ====
		if(!$this->existsInFileHistory($sPathFrom))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: file not in history array: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);

			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILENOTINHISTORY;
			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error    			
		}

		//==== check file exists ====
		if (!file_exists($sPathFrom))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: file to rename does not exist: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME]);

			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_FILENOTEXIST;
			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error    					
		}

		//==== actual rename ====
		if (!rename($sPathFrom, $sPathTo)) //actual rename
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'DRInputUpload: rename failed from: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAME].' to: '.$_GET[DRInputUpload::ACTION_VARIABLE_FILENAMENEW]);

			$arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = DRInputUpload::JSON_ERRORCODE_RENAMEFAILED;
			$arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('drinputupload_error_vague', 'Oops, something went wrong');

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($arrJSONResponse);               
            return false; //stop further execution to display the error   
		}
		else //update file history
		{
			$this->removeFromFileHistory($sPathFrom);
			$this->addToFileHistory($sPathTo);

		}

		return true;
	}


	/**
	 * adds path to file history
	 */
	private function addToFileHistory($sPath)
	{
		//add to file history
		if (isset($_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY]))//if exists
		{	
			if (!in_array($sPath, $_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY])) //if file NOT exists in history: add it
				$_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY][] = $sPath;
		}
		else //when not set: create it
		{
		   	$_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY][] = $sPath;				
		}		
		
	}

	/**
	 * removes path from file history
	 */
	private function removeFromFileHistory($sPath)
	{
		$iDelIndex = 0;

		if (isset($_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY]))
		{
			$iDelIndex = array_search($sPath, $_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY]);
			unset($_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY][$iDelIndex]);//remove old			
		}

	}

	/**
	 * returns if path is in file history
	 */
	private function existsInFileHistory($sPath)
	{
		if (isset($_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY]))//if exists
		{	
			if (in_array($sPath, $_SESSION[DRInputUpload::SESSIONAK_DRINPUTUPLOAD][DRInputUpload::SESSIONAK_DRINPUTUPLOAD_FILEHISTORY]))
				return true;			
		}

		return false;
	}

	/**
	 * checks is file is of valid type
	 * a webp is indeed a webp, and a pdf indeed a pdf
	 * 
	 * @param string $sPathFile
	 * @param string $sMimeTypeDirty the mime type the file is supposed to be. You CANNOT TRUST this value, because it might be spoofed by the user (by renaming the file to another extension for example)
	 * @return bool true=valid, false=invalid
	 */
	private function isValidFileType($sPathFile, $sMimeTypeDirty)
	{
		$sRealMimeType = '';

		//CHECK 1: real mime type
		$sRealMimeType = getMimeTypeFile($sPathFile);
		if ($sRealMimeType === false)
			return false;

		if (!in_array($sRealMimeType, $this->arrAcceptedFileTypes, true))
			return false;

		//CHECK 2: rudimentary check for php-opening-tag (to prevent uploading php scripts)
		if (!in_array(MIME_TYPE_PHP, $this->arrAcceptedFileTypes))
		{
			$fhUploadedFile = fopen($sPathFile, 'r');
			while(!feof($fhUploadedFile)) 
			{
				$sLine = fgets($fhUploadedFile);

				//php opening tag 1
				$iPos = stripos($sLine, '<?php');
				if ($iPos !== false)
					return false; //php tag found

				//php opening tag 2
				$iPos = stripos($sLine, '<?=');
				if ($iPos !== false)
					return false; //php tag found			
			}
			fclose($fhUploadedFile);
		}

		//CHECK 3: real mime type VS dirty mime type
		//we've collected data on real mime types. Let's compare it to the user-supplied mime type.
		//problem with this method: a csv: $sRealMimeType == 'text\plain' and $sMimeTypeDirty == 'text\csv' which returns false, while it is valid
		if ($this->bStrictMemeTypeChecking)
		{
			if ($sRealMimeType !== '')//is there something to check?
				if ($sRealMimeType !== $sMimeTypeDirty) //it should match! Otherwise there is something fishy going on
					return false;
		}

		return true;
	}

	/**
	 * set submitted data from webcomponent into model
	 * function parses $_POST[$this->getName] JSON and then sets values in $objModel
	 * When multiple uploads: it creates new records in $objModel, but doesn't save them!
	 * 
	 * SPEED:
	 * this function is preferred over calling separate functions like getValueSubmittedFileName(), getValueSubmittedFileNameLarge() etc
	 * because of speed
	 * individual functions call jsondecode() every time, this function only calls it once for all values
	 * 
	 * WARNING: only for images, because it sets the image fields of $objModel
	 * 
	 * @param TSysModel $objModel 
	 * @param string $sFormMethod $_GET or $_POST 
	 */
	public function viewToModelImage(TSysModel $objModel, $sFormMethod = Form::METHOD_POST)
	{
		$arrPOSTGETValues = array();
		$iCountUploads = 0;

		//skedaddle: only for images
		if (!$objModel->getTableUseImageFile())
			return;

		//retrieve via proper form method
		$arrPOSTGETValues = $this->jsonDecodeForm($sFormMethod);

		//update cache
		$this->arrCachedDecodedJSONSubmittedValue = $arrPOSTGETValues;

		//set values in model
		$iCountUploads = count($arrPOSTGETValues);
		for ($iIndex = 0; $iIndex < $iCountUploads; ++$iIndex)
		{
			if ($iIndex > 0)
				$objModel->newRecord();

			//skedaddle: only for images (although rare, it can happen that you upload a non-image file in a class that also uses images)
			$sFullPath = concatDirFileSafe($this->sUploadDirPath, '', $arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_FILENAME]);
			if ($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_FILENAME] !== '') //only do checks when there is an actual file, otherwise a user cant delete a file (wich returns filename '')
			{
				if ($sFullPath === false)
					return;
				if (!is_file($sFullPath))
					return;
				if (!isMimeImage(getMimeTypeFile($sFullPath)))
					return;		
			}


			$objModel->setImageFileMax($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_FILENAME]);
			$objModel->setImageFileLarge($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_FILENAME_LARGE]);
			$objModel->setImageFileMedium($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_FILENAME_MEDIUM]);
			$objModel->setImageFileThumbnail($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_FILENAME_THUMBNAIL]);
			$objModel->setImageAlt($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_ALT]);
			$objModel->setImageMaxWidth($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_WIDTH]);
			$objModel->setImageMaxHeight($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_HEIGHT]);
			$objModel->setImageLargeWidth($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_WIDTH]);
			$objModel->setImageLargeHeight($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_HEIGHT]);
			$objModel->setImageMediumWidth($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_WIDTH]);
			$objModel->setImageMediumHeight($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_HEIGHT]);
			$objModel->setImageThumbnailWidth($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_WIDTH]);
			$objModel->setImageThumbnailHeight($arrPOSTGETValues[$iIndex][DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_HEIGHT]);			
		}
	}


	/**
	 * JSON decodes $_POST or $_GET into a 2d array
	 * like: 
	 * $arrData[0]['filename'] //when multiple uploads == false
	 * $arrData[1]['filename'] //when multiple uploads == true
	 * etc
	 * 
	 * this function is used to create $this->arrCachedDecodedJSONSubmittedValue
	 */
	private function jsonDecodeForm($sFormMethod = Form::METHOD_POST)
	{
		$arrFormMethod = array();
		$arrResult = array();
		$arrTemp = array();
		$arrKeys = array();
		$iCountUploads = 0;
		$iLenArr = 0;

		if ($sFormMethod == Form::METHOD_POST)
			$arrFormMethod = $_POST[$this->getName()];
		if ($sFormMethod == Form::METHOD_GET)
			$arrFormMethod = $_GET[$this->getName()];

		if (is_array($arrFormMethod))//multiple file uploads
		{
			$iLenArr = count($arrFormMethod);
			for($iIndex = 0; $iIndex < $iLenArr; ++$iIndex)
			{
				$arrTemp = json_decode($arrFormMethod[$iIndex], true);
				if ($arrTemp !== null)
					$arrResult[] = $arrTemp;
			}
		}
		else //only 1 file upload
		{
			$arrTemp = json_decode($arrFormMethod, true);
			if ($arrTemp !== null)
				$arrResult[] = $arrTemp;

		}

		//sanitize fields
		if ($arrResult !== null)
		{
			$iCountUploads = count($arrResult);
			for ($iIndex = 0; $iIndex < $iCountUploads; ++$iIndex)
			{
				$arrKeys = array_keys($arrResult[$iIndex]);
				foreach ($arrKeys as $sKey)
					$arrResult[$iIndex][$sKey] = sanitizeWhitelist($arrResult[$iIndex][$sKey], WHITELIST_FILENAME);				
			}
		}	

		return $arrResult;
	}

	/**
	 * internal function to 
	 * 1. json decode the $_POST (or $_GET) 
	 * 2. sanitize value 
	 * 3. return value
	 * 
	 * Used by functions like getValueSubmittedFileName();
	 * 
	 * @param string $sFormMethod $_GET or $_POST like Form::METHOD_POST
	 * @param string $sJSONField field in json data like DRInputUpload::JSONAK_RESPONSE_FILENAME
	 * @param int $iGETPOSTArrayIndex index in the $_GET or $_POST when it is an array (this happens when there are multiple uploads). Use index=0 when only one upload
	 * @return string
	 */
	private function getValueSubmittedJSON($sFormMethod, $sJSONField, $iGETPOSTArrayIndex = 0)
	{
		$arrPOSTGETValues = array();

		//update cache
		if ($this->arrCachedDecodedJSONSubmittedValue == null)
		{
			//retrieve via proper form method
			$arrPOSTGETValues = $this->jsonDecodeForm($sFormMethod);

			//update cache
			$this->arrCachedDecodedJSONSubmittedValue = $arrPOSTGETValues;
		}

		return $this->arrCachedDecodedJSONSubmittedValue[$iGETPOSTArrayIndex];
	}

	/**
	 * returns file name.
	 * In case of resized images, it returns the max size image
	 */
	public function getValueSubmittedFileName($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_FILENAME, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedFileNameLarge($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_FILENAME_LARGE, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedFileNameMedium($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_FILENAME_MEDIUM, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedFileNameThumbnail($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_FILENAME_THUMBNAIL, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageMaxWidth($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_WIDTH, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageMaxHeight($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_HEIGHT, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageLargeWidth($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_WIDTH, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageLargeHeight($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_HEIGHT, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageMediumHeight($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_HEIGHT, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageMediumWidth($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_WIDTH, $iGETPOSTArrayIndex);
	}
	
	public function getValueSubmittedImageThumbnailWidth($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_WIDTH, $iGETPOSTArrayIndex);
	}

	public function getValueSubmittedImageThumbnailHeight($sFormMethod = Form::METHOD_POST, $iGETPOSTArrayIndex = 0)
	{
		return $this->getValueSubmittedJSON($sFormMethod, DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_HEIGHT, $iGETPOSTArrayIndex);
	}
	
	/**
	 * set data from $objModel to internal values of this object 
	 * 
	 * WARNING: only for images, because it gets the image fields of $objModel
	 */
    public function modelToViewImage(TSysModel $objModel)
    { 
		$arrJSON = array();
		$sEncodedJSON = '';

		//skedaddle: only for images
		if (!$objModel->getTableUseImageFile())
			return;

		//skedaddle: only for images (although rare, it can happen that you upload a non-image file in a class that also uses images)
		$sFullPath = concatDirFileSafe($this->sUploadDirPath, '', $objModel->getImageFileMax());
		if ($sFullPath === false)
			return;

		$this->sFileName = $objModel->getImageFileMax();
		$this->sFileNameLarge = $objModel->getImageFileLarge();
		$this->sFileNameMedium = $objModel->getImageFileMedium();
		$this->sFileNameThumbnail = $objModel->getImageFileThumbnail();
		$this->sImageAlt = $objModel->getImageAlt();
		$this->iImageMaxWidth = $objModel->getImageMaxWidth();
		$this->iImageMaxHeight = $objModel->getImageMaxHeight();
		$this->iImageLargeWidth = $objModel->getImageLargeWidth();
		$this->iImageLargeHeight = $objModel->getImageLargeHeight();
		$this->iImageMediumWidth = $objModel->getImageMediumWidth();
		$this->iImageMediumHeight = $objModel->getImageMediumHeight();
		$this->iImageThumbnailWidth = $objModel->getImageThumbnailWidth();
		$this->iImageThumbnailHeight = $objModel->getImageThumbnailHeight();
		$this->bFileExists = is_file($sFullPath);		

		
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_FILENAME] 				= $this->sFileName;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_FILENAME_LARGE] 		= $this->sFileNameLarge;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_FILENAME_MEDIUM]		= $this->sFileNameMedium;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_FILENAME_THUMBNAIL]		= $this->sFileNameThumbnail;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_ALT]				= $this->sImageAlt;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_WIDTH]		= $this->iImageMaxWidth;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_MAX_HEIGHT]		= $this->iImageMaxHeight;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_WIDTH]		= $this->iImageLargeWidth;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_LARGE_HEIGHT]		= $this->iImageLargeHeight;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_WIDTH]		= $this->iImageMediumWidth;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_MEDIUM_HEIGHT]	= $this->iImageMediumHeight;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_WIDTH]	= $this->iImageThumbnailWidth;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_IMAGE_THUMBNAIL_HEIGHT]	= $this->iImageThumbnailHeight;
		$arrJSON[DRInputUpload::JSONAK_RESPONSE_FILE_EXISTS]			= $this->bFileExists;

		$sEncodedJSON = json_encode($arrJSON);

		if ($sEncodedJSON !== false) //if json_encode fails, it returns false
			$this->setValue($sEncodedJSON);

		//add to file history, so we know it's a valid file
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileName));
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileNameLarge));
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileNameMedium));
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileNameThumbnail));		
	}


	/**
	 * set filename of uploaded file.
	 * When image, and they are resized: this is the max size image
	 */
	public function setFileName($sFileName)
	{
		$this->sFileName = $sFileName;
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileName));
	}

	/**
	 * set filename of large-size image file.
	 */
	public function setFileNameLarge($sFileName)
	{
		$this->sFileNameLarge = $sFileName;
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileNameLarge));
	}

	/**
	 * set filename of medium-size image file.
	 */
	public function setFileNameMedium($sFileName)
	{
		$this->sFileNameMedium = $sFileName;
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileNameMedium));
	}

	/**
	 * set filename of thumbnail-size image file.
	 */
	public function setFileNameThumbnail($sFileName)
	{
		$this->sFileNameThumbnail = $sFileName;
		$this->addToFileHistory(concatDirFileSafe($this->sUploadDirPath, '', $this->sFileNameThumbnail));
	}

}

?>
