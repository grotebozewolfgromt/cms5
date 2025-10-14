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
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TInstallerScreen.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFilePHPConstants.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileApplication.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileWebsite.php');



/**
 * STEP 1: license agreement
 * 
 * 
 * 
 * 
 * installler/step1.php created 19-8-2025
 * 
 * @author dennis renirie
 */
class step1 extends TInstallerScreen
{

	/**
	 * SCREEN 1: license agreement
	 */
	public function screenShowLicense()
	{
		//init
	    $sBody = '';
		$this->disableNextButton();
		$this->enablePreviousButton();
		$this->setURLNextButton('?'.TInstallerScreen::GETVARIABLE_ACTION.'=screenLicenseAgree');
		// $this->setURLPreviousButton('index.php');

    	ob_start();
		?>
			<script>
				function checkAgreeLicense()
				{
					objCheck1 = document.getElementById('chkRead');
					objCheck2 = document.getElementById('chkAgree');
					objBtnNext = document.getElementById('btnNext');

					if ((objCheck1) && (objCheck2))
					{
						objBtnNext.disabled = !((objCheck1.checked) && (objCheck2.checked)); //user can also uncheck
					}
				}
			</script>
			<iframe src="https://www.archimedescms.com/en/software-license-standard/" style="width: 100%; min-height:300px;">
				<!-- software license -->
			</iframe><br>
			<br>
			<input type="checkbox" id="chkRead" name="chkRead" value="read" onclick="checkAgreeLicense();">
			<label for="chkRead" onclick="checkAgreeLicense();">I've read the software license agreement</label><br>	
			<input type="checkbox" id="chkAgree" name="chkAgree" value="agree" onclick="checkAgreeLicense();">			
			<label for="chkAgree" onclick="checkAgreeLicense();">I agree with the software license agreement</label><br>				
		<?php
	    $sBody = ob_get_contents();
	    ob_end_clean();  

		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * SCREEN 2: header redirects to proper screen
	 */
	public function screenLicenseAgree()
	{
		//although we do a javascript check, this function forces a server-side check
		if (isset($_POST['chkRead']) && (isset($_POST['chkRead'])))
		{
			if (($_POST['chkRead'] == 'read') && ($_POST['chkAgree'] == 'agree'))
			{
				header('Location: step2.php');
				return;
			}
		}

		//not agreed
		header('Location: '.$this->getURLNextController());
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
		return 'Software License Agreement';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'To install, you must agree with the Software License Agreement.';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenShowLicense';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenShowLicense', 'screenLicenseAgree');
	}



	/**
	 * specify what is the previous controller in the process.
	 * this will be the default url for previous button,
	 * which you can override this with setURLPreviousButton()
	 */	
	public function getURLPreviousController()
	{
		return 'index.php';
	}		

	/**
	 * specify what is the next controller in the process.
	 * this will be the default url for next button,
	 * which you can override this with setURLNextButton()
	 */
	public function getURLNextController()
	{
		return 'step2.php';
	}



}

$objScreen = new step1();