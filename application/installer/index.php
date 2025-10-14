<?php

use dr\classes\TInstallerScreen;

error_reporting(0);
ini_set('display_errors', 'off');

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TInstallerScreen.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_typedef.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_types.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_framework.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_inet.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_string.php');


/**
 * New method installation file based on TInstallScreen
 * 
 * 
 * 
 * installler/step4.php created 17-8-2025
 * 
 * @author dennis renirie
 */
class index extends TInstallerScreen
{

	/**
	 * SCREEN 1: what would you like to do?
	 */
	public function screenWhatwanttodo()
	{

		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();
		$this->setURLNextButton('?'.index::GETVARIABLE_ACTION.'=screenRedirect');
		$this->setURLPreviousButton('https://www.archimedescms.com');
		$this->setTextPreviousButton('Exit');
		
		$objConfigDefaults = $this->loadConfigDefaults();
		$this->setTitle($this->getTitle().' to '.$objConfigDefaults->get('APP_APPLICATIONNAME').' '.$objConfigDefaults->get('APP_VERSION'));

    	ob_start();
		?>
		 	<h2>What would you like to do?</h2>
			<input type="radio" id="install" name="whatwanttodo" value="install" checked>
			<label for="install">Install</label><br>
			<br>
			<input type="radio" id="update" name="whatwanttodo" value="update">
			<label for="update">Update</label><br>
			<br>
			<input type="radio" id="uninstall" name="whatwanttodo" value="uninstall">
			<label for="uninstall">Uninstall</label><br>	
			<br>	
			<br>
			<h2>Note</h2>		
			This installer is in English.<br>
			Once you've installed <?php echo $objConfigDefaults->get('APP_APPLICATIONNAME'); ?>, users are able to choose a different language.
		<?php
	    $sBody = ob_get_contents();
	    ob_end_clean();  
		
		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * SCREEN 2: header redirects to proper screen
	 */
	public function screenRedirect()
	{
		switch ($_POST['whatwanttodo'])
		{
			case 'install':
				header('Location: step1.php');
				break;
			case 'update':
				header('Location: step6.php?'.TInstallerScreen::GETVARIABLE_MODE.'='.TInstallerScreen::MODE_UPDATE);
				break;
			case 'uninstall':
				header('Location: uninstall.php');
				break;
			default: //no valid
				header('Location: index.php');
		}

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
		return 'Welcome';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'This web software installer guides you step-by-step through the process of installing and configuring the software.<br>Please read the instructions througout this process carefully.';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenWhatwanttodo';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenWhatwanttodo', 'screenRedirect');
	}

	/**
	 * does this class need an automatic check on the installpassword?
	 * 
	 * @return boolean
	 */
	public function useInstallPassword()
	{
		return false;
	}


	/**
	 * specify what is the next controller in the process.
	 * this will be the default url for next button,
	 * which you can override this with setURLNextButton()
	 */
	public function getURLNextController()
	{
		return ''; //it depends on the user input
	}


	/**
	 * specify what is the previous controller in the process.
	 * this will be the default url for previous button,
	 * which you can override this with setURLPreviousButton()
	 */	
	public function getURLPreviousController()
	{
		return 'https://www.archimedescms.com';
	}

}

$objScreen = new index();