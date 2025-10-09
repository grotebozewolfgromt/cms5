<?php

use dr\classes\TInstallerScreen;

error_reporting(0);
ini_set('display_errors', 'off');

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_typedef.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_inet.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_string.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_types.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_framework.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TInstallerScreen.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFilePHPConstants.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileApplication.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileWebsite.php');



/**
 * STEP 2: preinstall checks
 * 
 * 
 * 
 * installler/step2.php created 19-8-2025
 * 
 * @author dennis renirie
 */
class step2 extends TInstallerScreen
{

	/**
	 * SCREEN 1: checking prequisites
	 */
	public function screenDoChecks()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();
		$this->setURLNextButton('step3.php');
		$this->setURLPreviousButton('step1.php');

		//checks
		$sBody.= $this->checkInt64();
		$sBody.= $this->checkPHPMods();


		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * check 64 bit int
	 */
	private function checkInt64()
	{
		$sBody = 'Checking 64 bit integer: ';
		if (PHP_INT_MAX == 9223372036854775807) //if 64 bit integer
		{
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';
		}
		else
		{
			$sBody.= TInstallerScreen::STATUS_WARNING.'<br>';
			$sBody.= 'This PHP version uses a max integer value of '.PHP_INT_MAX.'.<br>';
			if (PHP_INT_MAX == 2147483647)
				$sBody.= 'This is a 32 bit integer, instead of 64 bit.<br>';
			$sBody.= 'This software assumes 64 bits integers, which can lead to unexpected behavior when 32 bit values are exceeded.<br>';
			$sBody.= 'This will happen for example when you import a database on a 32 bit php version that was used on a 64 bit system.<br>';
			$sBody.= 'Using a 32 bit database on a 32 bit system will work fine.<br>';
			$sBody.= 'But you are able to store more records on a 64 bit system.<br>';
			error_log('WARNING: This PHP version doesnt use 64 bit integers. Installation will continue. PHP_INT_MAX == '.PHP_INT_MAX);    
		}

		return $sBody;
	}

	/**
	 * check php mods
	 */
	private function checkPHPMods()
	{
		//init
		$arrMods = array();
		$sBody = 'Checking PHP modules: ';

		$arrMods = getPHPExtensionsNotLoaded();
		if (count($arrMods) > 0)
		{
			$sBody.= TInstallerScreen::STATUS_WARNING.'<br>';
			$sBody.= 'The following modules are not loaded: ';

			$sBody.= '<b>';
			$sBody.= implode(', ', $arrMods);
			$sBody.= '</b><br>';
			error_log('install: WARNING: php module '. implode(', ', $arrMods) .' not loaded');				

			//==== give extra info on missing modules
			if (in_array(PHP_EXT_ZIP, $arrMods))
				$sBody.= '- zip: is used to extract modules on installation, without the user having to unzip them manually.<br>';

			if (in_array(PHP_EXT_INTL, $arrMods))
				$sBody.= '- intl: is support for international languages; used by 3rd party software ESCPOS, which is used to print to Epson ticket printers.<br>';

			if (in_array(PHP_EXT_GD, $arrMods))
				$sBody.= '- gd: is used to resize images, like the images you upload to your website.<br>';

			if (in_array(PHP_EXT_FILEINFO, $arrMods))
				$sBody.= '- fileinfo: is used to check if an uploaded file is of the right type.<br>Fileinfo extension prevents users from uploading files with malicious payloads that compromise the system, website and thus its visitors.<br>It is <b>STRONGLY recommended</b> to install/enable "fileinfo" for your own safety!';

			if (in_array(PHP_EXT_EXIF, $arrMods))
				$sBody.= '- exif: is used to check if an uploaded file is of the right image type.<br>Exif extension prevents users from uploading image files with malicious payloads that compromise the system, website and thus its visitors.<br>It is <b>STRONGLY recommended</b> to install/enable "exif" for your own safety!';
			  
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';

		return $sBody;
	}

	
	//=== ABSTRACT FUNCTIONS ===

	/**
	 * return the local path of the template
	 * @return string
	 */
	public function getPathTemplate()
	{
		return dirname(__FILE__).DIRECTORY_SEPARATOR.'tpl_installerscreen.php';
	}

	/**
	 * return the <h1> title of the screen
	 * @return string
	 */
	function getTitle()
	{
		return 'Prequisites checks';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'In order to install the software we must run some checks to see if your system is compatible with the software.';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenDoChecks';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenDoChecks');
	}



}

$objScreen = new step2();