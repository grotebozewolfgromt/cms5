<?php

use dr\classes\TInstallerScreen;

error_reporting(0);
ini_set('display_errors', 'off');
session_start();

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
$sRootPath = dirname( $sCMSRootPath );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_typedef.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_inet.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_string.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lib_sys_types.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TInstallerScreen.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFilePHPConstants.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileApplication.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileWebsite.php');


/**
 * STEP 3: location CMS + create config files
 * 
 * 
 * 
 * installler/step3.php created 19-8-2025
 * 
 * @author dennis renirie
 */
class step3 extends TInstallerScreen
{

	/**
	 * SCREEN 1: location CMS
	 */
	/*
	public function askLocation()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();
		$this->setURLNextButton('step3.php?'.TInstallerScreen::GETVARIABLE_ACTION.'=createConfigs');
		$this->setURLPreviousButton('step2.php');

		//ask location
    	ob_start();
		?>
			<h2>Administration panel location</h2>
			<label for="edtCMSLocation">In which subdirectory do you want to install the administation panel?<br>
			- This location is for websites: Content Management System location.<br>
			- This location is for web applications: the location of the application itself.<br>
			<br>
			The default is 'application', but for security reasons it is wise to choose another location,<br>
			especially in the case of websites available on the internet, because 'malicious actors' also know this location.
			</label><br>	
			<br>
			<?php echo dirname(dirname( dirname(getURLThisScript()))); ?>/<input type="text" name="edtCMSDir" value="<?php echo 'application' ?>" style="width: 200px;">
		<?php
	    $sBody = ob_get_contents();
	    ob_end_clean();  


		$this->renderHTMLTemplate(get_defined_vars());
	}*/

	/**
	 * SCREEN 2: config file creation
	 */
	public function createConfigs()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();

		//rename directory
		// $sBody.= $this->renameDir(); --> doesn't work

		//create config files
		$sBody.= $this->createConfigFileApplication();
		$sBody.= $this->createConfigFileWebsite();

		if ($this->getNextButtonEnabled())
		{
			$sBody.= '<br>';
			$sBody.= 'Click "Next"-button to set up a database connection';
		}

		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * sanitizes form input
	 */
	// private function sanitizeForm()
	// {
	// 	if (isset($_POST['edtCMSDir']))
	// 	{
	// 		$_POST['edtCMSDir'] = filterBadCharsWhiteList($_POST['edtCMSDir'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-');
	// 	}
	// }


	/**
	 * creates config files for framework
	 */
	private function createConfigFileApplication()
	{
		//init
		$sBody = '';
		global $sCMSRootPath;

		//==== CONFIG APPLICATION
		$objConfig = new dr\classes\TConfigFileApplication();
		if (file_exists($this->sConfigPathApplication))//LOAD: existing config file (so we can overwrite values)
		{
			$sBody.= 'Config file application for host already exists for '.$_SERVER['SERVER_NAME'].', try to load: ';

			if (!$objConfig->loadFile($this->sConfigPathApplication))
			{
				$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
				$this->disableNextButton();
			}
			else
			{
				$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';

				//store in session
				$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_INSTALLPASSWORD] = $objConfig->get('APP_INSTALLER_PASSWORD');
				$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_CONFIGFILEEXISTS] = true;
			}

			return $sBody;	
		}
		else //CREATE: new config file
		{
			$sInstallPassword = '';
			$sInstallPassword = generateRandomString(8, 12, 'abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789_');

			//store in session
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_INSTALLPASSWORD] = $sInstallPassword;
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_CONFIGFILEEXISTS] = false;

			//use defaults from defaults config
			$objConfig = $this->loadConfigDefaults();

			//store config values specific to the system environment
			$objConfig->set('APP_PEPPER', generateRandomString(20, 30, 'abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789-_(){}.=+'));        
			$objConfig->set('APP_CRONJOBID', generateRandomString(10, 12, 'abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789-_'));        
			$objConfig->set('APP_INSTALLER_PASSWORD', $sInstallPassword);			
			$objConfig->set('APP_PATH_DOMAIN_CMS', getDomain(getURLThisScript(), false));        
			$objConfig->set('APP_URL_CMS', dirname(dirname(getURLThisScript())));        
			$objConfig->set('APP_PATH_UPLOADS', dirname($sCMSRootPath).DIRECTORY_SEPARATOR.'uploads');        
			$objConfig->set('APP_URL_UPLOADS', dirname(dirname(dirname(getURLThisScript()))).'/uploads');        
			if ($_SERVER['REQUEST_SCHEME'] === 'http')
				$objConfig->set('APP_ISHTTPS', false);        
			if ($_SERVER['REQUEST_SCHEME'] === 'https')
				$objConfig->set('APP_ISHTTPS', true);    
			$objConfig->set('APP_MAINTENANCEMODE', true);
		}
		$sBody.= 'Creating config file application for host: '.$_SERVER['SERVER_NAME'].' ';
		if ($objConfig->saveFile($this->sConfigPathApplication))
		{
			$sBody.= TInstallerScreen::STATUS_DONE.'<br>';
		}
		else
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$this->disableNextButton();
		}

		return $sBody;
	}
		
	/**
	 * creates config files for website
	 */
	private function createConfigFileWebsite()
	{
		//init
		$sBody = '';
		global $sCMSRootPath;

		$objConfig = new dr\classes\TConfigFileWebsite();
		if (file_exists($this->sConfigPathWebsite))//load existing config file (so we can overwrite values)
		{
			$sBody.= 'Config file for default website exists for '.$_SERVER['SERVER_NAME'].', try to load: ';

			if (!$objConfig->loadFile($this->sConfigPathWebsite))
			{
				$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
				$this->disableNextButton();
			}
			else
			{
				$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';
			}

			return $sBody;			
		}
		else //for new config files
		{
			$objConfig->set('APP_PATH_DOMAIN', getDomain(getURLThisScript(), false));
			$objConfig->set('APP_PATH_LOCAL', dirname($sCMSRootPath));
			$objConfig->set('APP_PATH_WWW', 'https://'.$_SERVER['SERVER_NAME']);
			$objConfig->set('APP_PATH_DOMAIN', $_SERVER['SERVER_NAME']);
		}   

		$sBody.= 'Creating config file website ';
		if ($objConfig->saveFile($this->sConfigPathWebsite))
		{
			$sBody.= TInstallerScreen::STATUS_DONE.'<br>';
		}
		else
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$this->disableNextButton();
		}
		
		return $sBody;
	}

	/**
	 * renames directory of CMS
	 * 
	 * @todo doesnt work, probably because its the current directory
	 */
	/*
	private function renameDir()
	{
		//init
		$sBody = '';
		global $sCMSRootPath;
		global $sRootPath;

		//filter dir
		$this->sanitizeForm();
		$sCMSDir = $_POST['edtCMSDir'];
		// var_dump($sCMSDir);

		//actual rename
		$sBody.= 'Rename directory to "'.$sCMSDir.'": ';		
		// $sBody.= 'from "'.$sCMSRootPath.'":  to'.$sRootPath.DIRECTORY_SEPARATOR.$sCMSDir;		
		if (!rename($sCMSRootPath.'\\', $sRootPath.DIRECTORY_SEPARATOR.$sCMSDir.'\\'))
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';

		//update urls
		//@todo		

		return $sBody;
	}
	*/

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
		return 'Create configuration files';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'In this step we are going to create configuration files.<br>The configuration files store settings of the software, like path and database information';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'createConfigs';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('createConfigs');
	}


	/**
	 * specify what is the previous controller in the process.
	 * this will be the default url for previous button,
	 * which you can override this with setURLPreviousButton()
	 */	
	public function getURLPreviousController()
	{
		return 'step2.php';
	}		


	/**
	 * specify what is the next controller in the process.
	 * this will be the default url for next button,
	 * which you can override this with setURLNextButton()
	 */
	public function getURLNextController()
	{
		return 'step4.php';
	}	

}

$objScreen = new step3();