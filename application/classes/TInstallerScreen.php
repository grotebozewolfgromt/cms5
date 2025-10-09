<?php
namespace dr\classes;


/**
 * Description of TInstallerScreen controller
 * This class represents a screen in the installer
 * Each screen (=TInstaller instance) is takes the user through the installation process. 
 * 
 * This class does not work with the traditional templates/controllers from the framework, 
 * This class is designed to be independent of all other classes and framework functions.
 * It is assumed that the bootstrap is NOT loaded yet, and config files NOT read!!!
 * This way we can create config files!
 * 
 * NOT USED IN THIS ABSTRACT PARENT CLASS ARE:
 * - web components
 * - form generator
 * - traditional MVC controllers
 * - bootstrap
 * - framework functions
 * - paths from the framework
 * (you can include these in the child classes if you want to)
 * 
 * 
 *  APP_INSTALLER_ENABLED
 * ====================================
 * This class will return a 404 error when the installer is disabled.
 * Whether the installer is disabled, depends on the setting APP_INSTALLER_ENABLED in the framework config file for this host.
 * You can disable this check by overloading: useCheckInstallerEnabled().
 * WHEN NO CONFIG FILE IS PRESENT: IT ENABLES THE INSTALLER (it is not a working framework without one, thus it needs to be created by the installer)
 * 
 * 
 * INSTALL PASSWORD
 * ================
 * The class supports an install password.
 * This password is stored in the framework config file.
 * It is meant as a WEAK protection against accidentialy running the installer by malicious individuals
 * when the installer is not removed.
 * This to prevent installs, overwrites, deletion and updates by third parties.
 * The amount of password tries is stored in the framework config file as well.
 * When the amount exceeds 3 then the installer is blocked completely, 
 * until you reset the number to <3 in the framework config file
 * 
 * 
 * SERVER SENT EVENTS
 * ==================
 * This class works with Server Sent Events to update the 2 progress bars and the messages in the window
 * 
 * 17 aug 2025: TInstaller: created
 * 
 * @author dennis renirie
 */


$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFilePHPConstants.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileApplication.php');
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TConfigFileWebsite.php');

abstract class TInstallerScreen
{
	const STATUS_SUCCESS = '<span class="noerror">[SUCCESS]</span>';
	const STATUS_SKIPPED = '<span class="noerror">[SKIPPED]</span>';
	const STATUS_DONE = '<span class="noerror">[DONE]</span>';
	const STATUS_WARNING = '<span class="warning">[WARNING]</span>';
	const STATUS_FAILED = '<span class="error">[ERROR]</span>';

	const GETVARIABLE_ACTION = 'action';//parameter in url (needs to be url safe)
	const GETVARIABLE_MODE = 'mode';//parameter in url (needs to be url safe)

	const SESSIONAK_INSTALLER = 'cmsinstaller'; //Session Array Key for the cms installer to store for example form values so users can go forward or back and the form-values are retained.
	const SESSIONAK_INSTALLER_INSTALLPASSWORD = 'installpassword'; //Session Array Key for the cms installer to store the installpassword
	const SESSIONAK_INSTALLER_CONFIGFILEEXISTS = 'configfileexists';//stores whether a config file already existed. True=existing config file/False=created new config. I store this value because I don't want to expose values from an existing config file
	const SESSIONAK_INSTALLER_LOGINUSERNAME = 'loginusername';//
	const SESSIONAK_INSTALLER_PASSWORDSTARS = 'loginpasswordstars';//only the amount of stars
	const SESSIONAK_INSTALLER_EMAIL = 'emailaddress';//

	const MODE_INSTALL = 'install'; //value in url: installs software = default (needs to be url safe)
	const MODE_UPDATE = 'update'; //value in url: upgrades software (needs to be url safe)
	const MODE_UNINSTALL = 'uninstall'; //value in url: removes installation (needs to be url safe)
	const MODE_INPUTFORM = 'inputform'; //value in url: user needs to input values in form. (needs to be url safe)
	const MODE_SUBMITTEDFORM = 'submittedform'; //value in url: checks validity of form. (needs to be url safe)
	const MODE_INVALIDCONFIRMATION = 'invalidconfirmation'; //value in url: if the user submitted an invalid confirmation (when a user has to type 'confirm' in textbox to confirm)

	protected $sTextNextButton = 'Next &gt;';//function names that are allowed to be called as actions. You can also see this as a whitelist for functions to prevent malicious behavior
	protected $sTextPreviousButton = '&lt; Previous';//function names that are allowed to be called as actions. You can also see this as a whitelist for functions to prevent malicious behavior
	protected $bNextButtonEnabled = false;
	protected $bPreviousButtonEnabled = false;
	protected $bPreviousButtonVisible = true;
	protected $sURLNextButton = '';//the <a href=""> link to go to when clicking the "next" button
	protected $sURLPreviousButton = '';//the <a href=""> link to go to when clicking the "previous" button
	protected $sURLSSE = '';//the <a href=""> link that sends the Server Sent Events
	private $sTitle = '';
	private $sDescription = '';

	protected $sConfigPathDefaults;
	protected $sConfigPathApplication;
	protected $sConfigPathWebsite;

	protected $sMode = TInstallerScreen::MODE_INSTALL; //the mode can determine the behavior of a screen. It is more specific than action. You use mode in an action to determine behavior in an action.

	public function __construct()
	{	
		global $sCMSRootPath;
		$this->sConfigPathDefaults = $sCMSRootPath.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'application_defaults.php';
		$this->sConfigPathApplication = $sCMSRootPath.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'application_'.$_SERVER['SERVER_NAME'].'.php';
		$this->sConfigPathWebsite = $sCMSRootPath.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'website_defaultsite_'.$_SERVER['SERVER_NAME'].'.php'; //'defaultsite' is the name of the site

		//DEBUG: for debugging purposes, unquote the lines below to simulate a fresh installer visit
		// session_start();
		// unset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER]); //for debugging: trigger first time enter screen
		// unset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_INSTALLPASSWORD]); //for debugging: trigger first time enter screen

		//set mode
		if (isset($_GET[TInstallerScreen::GETVARIABLE_MODE]))
			$this->sMode = $_GET[TInstallerScreen::GETVARIABLE_MODE];

		//text can depend on mode
		$this->sTitle = $this->getTitle();
		$this->sDescription = $this->getDescription();		

		//check access
		if (!$this->handleInstallerEnabled()) 
			return;//return on error 

		//check installpassword
		if (!$this->checkInstallPassword()) //only works when framework config file is loaded
			return;//return on error 


		//=== default: call renderScreen();
		if (!isset($_GET[TInstallerScreen::GETVARIABLE_ACTION]))
		{
			//call default action function	
			call_user_func( array( $this, $this->getDefaultAction() ));			
			return; 
		}

		//=== call proper class function
		//check if function allowed
		if (!in_array($_GET[TInstallerScreen::GETVARIABLE_ACTION], $this->getAllowedActions()))
		{
			echo 'invalid action';
			return;
		}

		//check if function exists
		if (!method_exists($this, $_GET[TInstallerScreen::GETVARIABLE_ACTION])) 
		{
			echo 'invalid action';
			return;
		}
		
		//call function
		call_user_func( array( $this, $_GET[TInstallerScreen::GETVARIABLE_ACTION] ));
	}


	/**
	 * checks if user can use the installer.
	 * If not, it returns a header 404 error!
	 * 
	 *
	 * @return bool true=ok (when no check and password is ok), false=password not ok
	 */
	private function handleInstallerEnabled()
	{
		$sBody = '';

		//abort when no check needed
		if (!$this->useCheckInstallerEnabled())
			return true;

		//when config file is loaded
		if (defined('APP_INSTALLER_ENABLED'))
		{
			if (APP_INSTALLER_ENABLED === true)
				return true;
		}
		else //try to load config file manually
		{
			$objConfig = new TConfigFileApplication();
			if (file_exists($this->sConfigPathApplication))
			{
				if ($objConfig->loadFile($this->sConfigPathApplication))
				{
					if ($objConfig->getAsBool('APP_INSTALLER_ENABLED'))
						return true;
				}
			}
			else
				return true; //when there is no config file found, we enable the installer
		}

		//only when blocked it reaches this part of the code
		//give error
		header("HTTP/1.0 404 Not Found");
		if (!include_once('tpl_404.php'))
			echo 'Error 404 - file not found';
		// $this->setTextPreviousButton('Exit');
		// $this->setURLPreviousButton('https://www.archimedescms.com');
		// $this->disableNextButton();
		// $this->enablePreviousButton();
		// $this->setTitle('Error 404');
		// $this->setDescription('File not found');

		// $sBody.= 'The installer is not available';

		// $this->renderHTMLTemplate(get_defined_vars());			

		return false;
	}	

	/**
	 * checks installpassword from config file against $_SESSION
	 * and generates an input screen if not
	 * 
	 * WARNING: a loaded application config file is assumed!
	 *
	 * @return bool true=ok (when no check and password is ok), false=password not ok
	 */
	private function checkInstallPassword()
	{
		$sBody = '';
		$bAllowed = false;
		$iPassTries = 0;
		if (session_status() === PHP_SESSION_NONE) //we use $_SESSION that might not have been started yet
			session_start();

		//==== DON'T CHECK PASSWORD
		if (!$this->useInstallPassword())
			return true;

		//==== LOAD CONFIG FILE
		// if (defined('APP_INSTALLER_PASSWORD_TRIES'))//config file loaded?
		// {						
		// 	$objConfig = new TConfigFileApplication();
		// 	if (file_exists($this->sConfigPathApplication))//load existing config file (so we can overwrite values)
		// 	{			
		// 		if (!$objConfig->loadFile($this->sConfigPathApplication))
		// 		{
		// 			$this->disableNextButton();
		// 		}

		// 		$iPassTries = $objConfig->getAsInt('APP_INSTALLER_PASSWORD_TRIES') + 1;

		// 		//update tries
		// 		if (isset($_POST['edtInstallPassword']))
		// 			if (APP_INSTALLER_PASSWORD !== $_POST['edtInstallPassword'])
		// 				$objConfig->set('APP_INSTALLER_PASSWORD_TRIES', $iPassTries);		

		// 		//save
		// 		$objConfig->saveFile($this->sConfigPathApplication);
		// 	}
		// }
		$objConfig = new TConfigFileApplication();
		if (file_exists($this->sConfigPathApplication))//load existing config file (so we can overwrite values)
		{			
			if (!$objConfig->loadFile($this->sConfigPathApplication))
			{
				$this->disableNextButton();
			}

			$iPassTries = $objConfig->getAsInt('APP_INSTALLER_PASSWORD_TRIES') + 1;

			//update tries
			if (isset($_POST['edtInstallPassword']))
			{
				if ($objConfig->get('APP_INSTALLER_PASSWORD') !== $_POST['edtInstallPassword'])
				{
					$objConfig->setAsInt('APP_INSTALLER_PASSWORD_TRIES', $iPassTries);		
					$objConfig->saveFile($this->sConfigPathApplication);
				}
			}
		}
		else
			return true; //when no config file exists, no framework is used, thus needs to be installed

		

		//MAX PASSWORD ATTEMPTS EXCEEDED?
		if ($iPassTries > 3) //only available AFTER loading config file
		{
			$this->enablePreviousButton();
			$this->setTextPreviousButton('Exit');
			$this->setURLPreviousButton('https://www.google.com');
			$this->disableNextButton();
			$this->setURLNextButton('');

			$sBody.= 'Check installpassword max attempts '.TInstallerScreen::STATUS_FAILED.'<br><br>';
			$sBody.= 'The maximum amount of password attempts is exceeded.<br>';
			$sBody.= 'This might have been you or someone else.<br>';
			$sBody.= 'For security reasons, access to this installer is permanently disabled.<br>';

			$this->renderHTMLTemplate(get_defined_vars());	
			
			$bAllowed = false;
			return false;//EXIT
		}
		

		//COPY FORM PASSWORD IN SESSION
		if (isset($_POST['edtInstallPassword']))
		{
			//password itself
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_INSTALLPASSWORD]	= $_POST['edtInstallPassword'];
		}

		//COMPARE SESSION PASSWORD WITH CONFIG FILE
		if (isset($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_INSTALLPASSWORD]))
		{
			if ($objConfig->get('APP_INSTALLER_PASSWORD') == $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_INSTALLPASSWORD])
				$bAllowed = true;
		}
		
		//EXIT WHEN ALLOWED
		if ($bAllowed)
			return true;

		//WHEN NOT ALLOWED: show message
		$this->enablePreviousButton();
		$this->setURLPreviousButton('index.php');
		$this->enableNextButton();
		$this->setURLNextButton('');
		$sBody.= 'Check installpassword '.TInstallerScreen::STATUS_FAILED.'<br>';

		ob_start();
		?>
			<b>Sorry, but you need the installation password from the framework config file to proceed.</b><br>
			The installation password you can find in the section "APP_INSTALLER_PASSWORD"<br>
			<br>
			<label for="edtInstallPassword">Enter installation password</label><br>
			<input type="password" name="edtInstallPassword"><br>
		<?php
		$sBody.= ob_get_contents();
		ob_end_clean();  
		
		$this->renderHTMLTemplate(get_defined_vars());				

		return $bAllowed;
	}

	/**
	 * disable installer in application config file
	 */
	protected function disableInstaller()
	{
		//init


		$objConfig = new TConfigFileApplication();
		if (file_exists($this->sConfigPathApplication))//LOAD: existing config file (so we can overwrite values)
		{
			if (!$objConfig->loadFile($this->sConfigPathApplication))
			{
				error_log(__FILE__.': disableInstaller loading config file failed: '.$this->sConfigPathApplication);
			}
			else
			{
				$objConfig->setAsBool('APP_INSTALLER_ENABLED', false);

				if (!$objConfig->saveFile($this->sConfigPathApplication))
					error_log(__FILE__.': disableInstaller saving config file failed: '.$this->sConfigPathApplication);
			}	
		}
	}	

	/**
	 * enable or disable maintenance mode
	 */
	protected function enableMaintenanceMode($bEnabled)
	{
		//init


		$objConfig = new TConfigFileApplication();
		if (file_exists($this->sConfigPathApplication))//LOAD: existing config file (so we can overwrite values)
		{
			if (!$objConfig->loadFile($this->sConfigPathApplication))
			{
				error_log(__FILE__.': enableMaintenanceMode() loading config file failed: '.$this->sConfigPathApplication);
			}
			else
			{
				$objConfig->setAsBool('APP_MAINTENANCEMODE', $bEnabled);

				if (!$objConfig->saveFile($this->sConfigPathApplication))
					error_log(__FILE__.': enableMaintenanceMode() saving config file failed: '.$this->sConfigPathApplication);
			}	
		}
	}		

	/**
	 * renders HTML template with the following variables: $arrVariables
	 * $arrTemplateVariables['sBody'] = '<b>text in body</b>';
	 * this variable will be available as $sBody in the template
	 * 
	 * proper call for this function is $this->renderHTMLTemplate(get_defined_vars());
	 * 
	 * @param array $arrTemplateVariables array with variables for the template
	 */
	protected function renderHTMLTemplate($arrTemplateVariables = array())
	{
		//set all vars
		$arrKeys = array();
		$arrKeys = array_keys($arrTemplateVariables);
		foreach ($arrKeys as $sKey)
		{
			$$sKey = $arrTemplateVariables[$sKey];
		}

		$sTitle = $this->sTitle;
		$sDescription = $this->sDescription;
		$bNextButtonEnabled = $this->bNextButtonEnabled;
		$bPreviousButtonEnabled = $this->bPreviousButtonEnabled;
		$sURLNextButton = $this->sURLNextButton;
		$sURLPreviousButton = $this->sURLPreviousButton;
		$sURLSSE = $this->sURLSSE;

		include_once($this->getPathTemplate());
	}

	/**
	 * renders html for a universal screen with log window and 2 progress bars
	 * and calls renderHTMLTemplate() to render it
	 */
	protected function renderHTMLProgressScreen($arrTemplateVariables = array()) 
	{
		$sBody = '';

		//set all vars
		$arrKeys = array();
		$arrKeys = array_keys($arrTemplateVariables);
		foreach ($arrKeys as $sKey)
		{
			$$sKey = $arrTemplateVariables[$sKey];
		}
		unset($arrTemplateVariables); //to prevent that get_defined_vars() picks up $arrTemplateVariables as well


   		ob_start();
		?>
		    <!-- <input type="button" onclick="startTask();"  value="Start" />
        	<input type="button" onclick="stopTask();"  value="Stop" /> -->
			<script>
				//start immediately after page done loading
				window.addEventListener("load", (event) => 
				{
  					startTask();
				});
			</script>

			<!-- progress bars -->
        	<progress id="pbGlobal" value="0" max="100" style="width:100%; height:10px;"></progress>  
        	<span id="spGlobalPercentage" style="display:block; text-align:center; width:100%">0%</span>		
        	<progress id="pbSub" value="0" max="100" style="margin-top:10px; display:block; width:100%; height:10px;"></progress>  
        	<span id="spSubPercentage" style="display:block; text-align:center; width:100%">0%</span>		

			<!-- progress messages -->
			<div style="width: 100%; min-height:200px;" id="dvLogPanel">				
			</div><br>
			<br>

		<?php
	    $sBody.= ob_get_contents();
	    ob_end_clean();  		

		$this->renderHTMLTemplate(get_defined_vars());		
	}

	/**
	 * Send a Server Sent Event message to client to update status in the log and update the progress bars
	 * 
	 * @param string $sID unique string for each message (otherwise it generates an error on the client side)
	 * @param string $sHTMLMessage message in html format to send to client
	 * @param int $iProgressGlobal the progress of the global progressbar. This is the X in "X of Y".
	 * @param int $iProgressGlobalMax the maximum value the global progressbar can reach. This is the Y in "X of Y". default is 100
	 * @param int $iProgressSub the progress of the subtask progressbar. This is the X in "X of Y".
	 * @param int $iProgressSubMax the maximum value the subtask progressbar can reach. This is the Y in "X of Y". default is 100
	 */
	protected function sendSSEMessage($sID, $sHTMLMessage, $iProgressGlobal, $iProgressGlobalMax, $iProgressSub, $iProgressSubMax) 
	{
		$arrJSONData = array(
			'message' => $sHTMLMessage , 
			'progressglobal' => $iProgressGlobal,
			'progresssub' => $iProgressSub,
			'progressglobalmax' => $iProgressGlobalMax,
			'progresssubmax' => $iProgressSubMax
		);
		
		echo "id: $sID" . PHP_EOL;
		echo "data: " . json_encode($arrJSONData) . PHP_EOL;
		echo PHP_EOL;
		
		ob_flush();
		flush();
	}	

	public function disableNextButton()
	{
		$this->bNextButtonEnabled = false;
	}

	public function enableNextButton()
	{
		$this->bNextButtonEnabled = true;
	}

	public function getNextButtonEnabled()
	{
		return $this->bNextButtonEnabled;
	}

	public function setNextButtonEnabled($bEnabled)
	{
		$this->bNextButtonEnabled = $bEnabled;
	}

	public function disablePreviousButton()
	{
		$this->bPreviousButtonEnabled = false;
	}

	public function enablePreviousButton()
	{
		$this->bPreviousButtonEnabled = true;
	}

	public function getPreviousButtonEnabled()
	{
		return $this->bPreviousButtonEnabled;
	}

	public function setPreviousButtonEnabled($bEnabled)
	{
		$this->bPreviousButtonEnabled = $bEnabled;
	}

	/**
	 * set URL Next button 
	 */
	public function setURLNextButton($sAHref)
	{
		$this->sURLNextButton = $sAHref;
	}

	/**
	 * set URL previous button 
	 */
	public function setURLPreviousButton($sAHref)
	{
		$this->sURLPreviousButton = $sAHref;
	}

	/**
	 * return the text on the next button, for example 'next' or 'finish'
	 */
	public function getTextNextButton()
	{
		return $this->sTextNextButton;
	}

	/**
	 * sets text for the "next" button
	 */
	public function setTextNextButton($sText)
	{
		$this->sTextNextButton = $sText;
	}

	/**
	 * return the text on the next button, for example 'next' or 'finish'
	 */
	public function getTextPreviousButton()
	{
		return $this->sTextPreviousButton;
	}

	/**
	 * sets text for the "previous" button
	 */
	public function setTextPreviousButton($sText)
	{
		$this->sTextPreviousButton = $sText;
	}

	/**
	 * sets url that sends Server Sent Events
	 */
	public function setURLSSE($sURL)
	{
		$this->sURLSSE = $sURL;
	}

	/**
	 * return mode like MODE_INSTALL
	 */
	public function getMode()
	{
		return $this->sMode;
	}

	/**
	 * set mode like MODE_INSTALL
	 */
	public function setMode($sMode = TInstallerScreen::MODE_INSTALL)
	{
		return $this->sMode = $sMode;
	}


	public function setTitle($sTitle)
	{
		$this->sTitle = $sTitle;
	}

	public function setDescription($sDescription)
	{
		$this->sDescription = $sDescription;
	}


	/**
	 * loads application_defaults.php and returns object
	 * returns null when load fails
	 * 
	 * @return TConfigFileApplication
	 */
	public function loadConfigDefaults()
	{
		if (file_exists($this->sConfigPathDefaults))//load existing config file (so we can overwrite values)
		{			
			$objConfigDefaults = new TConfigFileApplication();
			if (!$objConfigDefaults->loadFile($this->sConfigPathDefaults))
				return null;
			else
				return $objConfigDefaults;
		}
		else
			return null;
	
		return null;//shouldnt get here
	}

	/**
	 * does this class need an automatic check on if the installer is enabled
	 * 
	 * WARNING: This function needs the existence of a framework config file for this host, loaded or not!
	 * 
	 * @return boolean
	 */
	public function useCheckInstallerEnabled()
	{
		return true;
	}

	/**
	 * does this class need an automatic check on the installpassword?
	 * 
	 * @return boolean
	 */
	public function useInstallPassword()
	{
		return true;
	}	 



	//=== ABSTRACT FUNCTIONS ===

	/**
	 * return the local path of the template
	 * @return string
	 */
	abstract public function getPathTemplate();

	/**
	 * return the <h1> title of the screen
	 * @return string
	 */
	abstract public function getTitle();

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	abstract public function getDescription();

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	abstract public function getDefaultAction();


	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	abstract public function getAllowedActions();



}
