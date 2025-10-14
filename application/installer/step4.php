<?php

use dr\classes\TInstallerScreen;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap.php');

/**
 * New method installation file based on TInstallScreen
 * 
 * WARNING:
 * I specifically prevent the config files from being loaded and shown in the form,
 * in order to prevent that the installer isn't deleted, and EVERYONE can read the database username +password
 * by looking at the HTML source code
 * 
 * installler/step4.php created 17-8-2025
 * 
 * @author dennis renirie
 */
class step4 extends TInstallerScreen
{
 	private $sDefaultHost = 'localhost';      
 	private $sDefaultUsername = 'root';
 	private $sDefaultPassword = '';
 	private $sDefaultSchema = 'cms5';
 	private $sDefaultPort = '3306';
 	private $sDefaultTablePrefix = 'tbl';

	const SESSIONAK_INSTALLER_HOST = 'host';
	const SESSIONAK_INSTALLER_USER = 'user';
	const SESSIONAK_INSTALLER_PASSWORD = 'password';
	const SESSIONAK_INSTALLER_SCHEMA = 'schema';
	const SESSIONAK_INSTALLER_PORT = 'port';
	const SESSIONAK_INSTALLER_PREFIX = 'prefix';


	/**
	 * SCREEN 1: shows screen for users to enter the database credentials
	 */
	public function screenEnterDBCredentials()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();
		$this->setURLNextButton('step4.php?'.TInstallerScreen::GETVARIABLE_ACTION.'=screenCheckDBCredentials');


		//prevent exposing existing values in config file
		if (!$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_CONFIGFILEEXISTS])
		{
			//retrieve previously entered values
			$this->loadFormFromSession();

			ob_start();
			?>
				<h2>Enter database credentials:</h2>
				<label for="edtHost">Host name or IP address</label><br>
				<input type="text" name="edtHost" value="<?php echo $this->sDefaultHost; ?>"><br>
				<label for="edtUsername">User name</label><br>
				<input type="text" name="edtUsername" value="<?php echo $this->sDefaultUsername; ?>"><br>
				<label for="edtPassword">Password</label><br>
				<input type="password" name="edtPassword" value="<?php echo $this->sDefaultPassword; ?>"><br>
				<label for="edtSchema">Database schema</label><br>
				<input type="text" name="edtSchema" value="<?php echo $this->sDefaultSchema; ?>"><br>
				<label for="edtPort">Port</label><br>
				<input type="text" name="edtPort" value="<?php echo $this->sDefaultPort; ?>"><br>                
				<label for="edtTablePrefix">Table prefix (store multiple instances of <?php echo APP_APPLICATIONNAME; ?> in 1 schema)</label><br>
				<input type="text" name="edtTablePrefix" value="<?php echo $this->sDefaultTablePrefix; ?>"><br>                
				<br>
				If you don't know these credentials, reach out to your web hosting provider or system administrator.
			<?php
			$sBody = ob_get_contents();
			ob_end_clean();  
		}
		else
		{
			$sBody.= 'Look for existing config file: '.TInstallerScreen::STATUS_SUCCESS.'<br>';
			$sBody.= '<br>Click "Next"-button to test database connection.<br>';
		}


		$this->renderHTMLTemplate(get_defined_vars());
	}


	/**
	 * SCREEN 2: handles submitted database credentials
	 */
	public function screenCheckDBCredentials()
	{
		//init
		global $sCMSRootPath;
		$this->disableNextButton(); //when errors occur, I disable it -> this can be done in other functions that are called below
		$this->enablePreviousButton(); //when errors occur, I disable it -> this can be done in other functions that are called below
		$this->setURLPreviousButton('step4.php'); //plain step4
		$sBody = '';

		//sanitize
		$this->sanitizeForm();

		//prepare $_SESSION so we can load database credentials from it
		$this->saveFormInSession();//LOAD FROM FORM
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_CONFIGFILEEXISTS]))//LOAD FROM CONFIG
		{
			if ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_CONFIGFILEEXISTS])
				$this->loadConfigInSession();
		}		

		//test database connection with data from $_SESSION
		$sBody.= $this->testDBConnection();
		
		//connection success?
		if ($this->getNextButtonEnabled()) //save to config
		{
			$sBody.= $this->saveDBCredentialsInConfig();
		}
		else //reset $_SESSION
		{
			//when config file existed, reset $_SESSION to default
			if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_CONFIGFILEEXISTS]))
				if ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_CONFIGFILEEXISTS])
					$this->resetFormDefaultsInSession();

		}
		
		$sBody.= '<br>We are ready to install, click "Next"-button to start installation.';

		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * sanitizes form input
	 */
	private function sanitizeForm()
	{
		if (isset($_POST['edtHost']))
		{
			$_POST['edtHost'] = filterBadCharsWhiteList($_POST['edtHost'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789:_-.');
		}

		if (isset($_POST['edtUsername']))
		{
			$_POST['edtUsername'] = filterBadCharsWhiteList($_POST['edtUsername'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-');
		}

		if (isset($_POST['edtPassword']))
		{
			$_POST['edtPassword'] = filterStringWhitelist($_POST['edtPassword'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-%@^!?=&*+$#.~');
		}

		if (isset($_POST['edtSchema']))
		{
			$_POST['edtSchema'] = filterStringWhitelist($_POST['edtSchema'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-');
		}

		if (isset($_POST['edtPort']))
		{
			$_POST['edtPort'] = filterStringWhitelist($_POST['edtPort'], '0123456789');
		}

		if (isset($_POST['edtTablePrefix']))
		{
			$_POST['edtTablePrefix'] = filterStringWhitelist($_POST['edtTablePrefix'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-');
		}
	}

	/**
	 * stores form values in session
	 */
	private function saveFormInSession()
	{
		if (isset($_POST['edtHost']))
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST] 		= $_POST['edtHost'];
		if (isset($_POST['edtUsername']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER] 		= $_POST['edtUsername'];
		if (isset($_POST['edtPassword']))
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD] 	= $_POST['edtPassword'];
		if (isset($_POST['edtSchema']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA] 	= $_POST['edtSchema'];
		if (isset($_POST['edtPort']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT] 		= $_POST['edtPort'];
		if (isset($_POST['edtTablePrefix']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PREFIX] 	= $_POST['edtTablePrefix'];
	}

	/**
	 * reset values session to defaults
	 */
	private function resetFormDefaultsInSession()
	{
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST] 		= $this->sDefaultHost;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER] 		= $this->sDefaultUsername;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD] 	= $this->sDefaultPassword;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA] 	= $this->sDefaultSchema;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT] 		= $this->sDefaultPort;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PREFIX] 	= $this->sDefaultTablePrefix;

		//also reset configfileexists because the values were invalid
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_CONFIGFILEEXISTS] = false; 

	}

	/**
	 * stores form values to configuration files
	 */
	private function saveDBCredentialsInConfig()
	{
		//init
		$sBody = '';

		//==== CONFIG FRAMEWORK
		$objConfig = new dr\classes\TConfigFileApplication();
		$sBody.= 'Open config file application for host: '.$_SERVER['SERVER_NAME'].' ';
		if (file_exists($this->sConfigPathApplication))//load existing config file (so we can overwrite values)
		{			
			if (!$objConfig->loadFile($this->sConfigPathApplication))
			{
				$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
				$this->disableNextButton();
			}
			else
			{
				$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';
			}

			$objConfig->set('APP_DB_HOST', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST]);        
			$objConfig->set('APP_DB_USER', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER]);        
			$objConfig->set('APP_DB_PASSWORD', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD]);        
			$objConfig->set('APP_DB_DATABASE', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA]);        
			$objConfig->set('APP_DB_PORT', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT]);        
			$objConfig->set('APP_DB_TABLEPREFIX', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PREFIX]);    

			$sBody.= 'Save database credentials to config file application for host: '.$_SERVER['SERVER_NAME'].' ';
			if ($objConfig->saveFile($this->sConfigPathApplication))
			{
				$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';
			}
			else
			{
				$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
				$this->disableNextButton();
			}

		}

		return $sBody;
	}

	/**
	 * retrieve form values from session
	 */
	private function loadFormFromSession()
	{
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST]))
			$this->sDefaultHost 		= $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST];
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER]))		
			$this->sDefaultUsername 	= $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER];
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD]))		
			$this->sDefaultPassword 	= $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD];
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA]))					
			$this->sDefaultSchema 		= $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA];
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT]))		
			$this->sDefaultPort			= $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT];
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PREFIX]))		
			$this->sDefaultTablePrefix 	= $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PREFIX];
	}



	/**
	 * loads config file framework
	 */
	private function loadConfigInSession()
	{
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST] 		= APP_DB_HOST;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER] 		= APP_DB_USER;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD] 	= APP_DB_PASSWORD;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA] 	= APP_DB_DATABASE;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT] 		= APP_DB_PORT;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PREFIX] 	= APP_DB_TABLEPREFIX;
	}	

	/**
	 * test database connection
	 */
	private function testDBConnection()
	{
		$sBody = '';

		//switch all errors off
		function dummyFunctionTestconnection(){}; //dummy function
		error_reporting(0);
		ini_set('display_errors', 'off');
        register_shutdown_function( 'dummyFunctionTestconnection' ); //dummy function callback 
        set_error_handler( 'dummyFunctionTestconnection' ); //dummy function callback
        set_exception_handler( 'dummyFunctionTestconnection' ); //dummy function callback

		//start database connection
		$objConn = new dr\classes\db\TDBConnectionMySQL();
		$objConn->setErrorReporting(dr\classes\db\TDBConnection::REPORT_ERROR_OFF);
		$objConn->setHost($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_HOST]);
		$objConn->setUsername($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_USER]);
		$objConn->setPassword($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PASSWORD]);
		// $objConn->setDatabaseName($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_SCHEMA]);--> dont use here, we only check connection details, because the database schema doesnt exist yet and will therefore give an error
		$objConn->setPort($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step4::SESSIONAK_INSTALLER_PORT]);
		$sBody.= 'Testing connection to database server ';
		if ($objConn->connect())
		{
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';

			$this->enablePreviousButton();
			$this->enableNextButton();		

		}
		else
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= '<b>Please revise your credentials by clicking the "'.$this->getTextPreviousButton().'"-button</b>';

			$this->enablePreviousButton();
			$this->disableNextButton();
		}		
	

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
		return 'Database connection';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'In this step you are going to enter MySQL/MariaDB database credentials.<br>When clicking the "'.$this->getTextNextButton().'"-button, the database connection will be tested.<br>Testing might take a couple of seconds, so please be patient.';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenEnterDBCredentials';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenEnterDBCredentials', 'screenCheckDBCredentials');
	}

	/**
	 * specify what is the previous controller in the process.
	 * this will be the default url for previous button,
	 * which you can override this with setURLPreviousButton()
	 */	
	public function getURLPreviousController()
	{
		return 'step3.php';
	}		


	/**
	 * specify what is the next controller in the process.
	 * this will be the default url for next button,
	 * which you can override this with setURLNextButton()
	 */
	public function getURLNextController()
	{
		return 'step5.php';
	}	

}

$objScreen = new step4();