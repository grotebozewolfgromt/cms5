<?php

use dr\classes\TInstallerScreen;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap_cms.php');

/**
 * Create a new user in the CMS
 * 
 * 
 * installler/step6.php created 21-8-2025
 * 
 * @author dennis renirie
 */
class step6 extends TInstallerScreen
{
	private $sDefaultUsername = '';
	private $sDefaultPassword = '';
	private $sDefaultPasswordRepeat = '';
	private $sDefaultEmail = '';

	private $bValidForm = true;

	private $sURLSubmitForm = 'step6.php?'.TInstallerScreen::GETVARIABLE_MODE.'='.TInstallerScreen::MODE_SUBMITTEDFORM;

	/**
	 * SCREEN 1: shows screen for users to enter the database credentials
	 */
	public function screenEnterLoginCredentials()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();
		$this->setURLPreviousButton('index.php');	
		$this->setTextPreviousButton('Abort');	

		//==== SET MODE
		if (!isset($_GET[TInstallerScreen::GETVARIABLE_MODE]))//the first time entering this screen, then the user is inputting data in the form
			$this->setMode(TInstallerScreen::MODE_INPUTFORM);


		//=== MODE: input
		if ($this->getMode() == TInstallerScreen::MODE_INPUTFORM)
		{
			$this->setURLNextButton($this->sURLSubmitForm);
			$this->setTextNextButton('Create');			
		}

		
		//==== MODE: submitted
		if ($this->getMode() == TInstallerScreen::MODE_SUBMITTEDFORM)
		{
			$this->setURLNextButton($this->sURLSubmitForm);
			$this->setTextNextButton('Create');	

			$sBody.= '<h2>Doing checks:</h2>';
			$this->setFormAsDefaultVars();
			$sBody.= $this->validateForm();
			$sBody.= '<br>';			

			//good form
			if ($this->bValidForm)
			{
				$this->createUser();
				return; //EXIT
			}
			
		}

		//==== SHOW FORM
		ob_start();
		?>

			<h2>Enter login credentials:</h2>

			<label for="edtUsername">User name</label><br>
			<input type="text" name="edtUsername" value="<?php echo $this->sDefaultUsername; ?>"><br>
			<label for="edtPassword">Password</label><br>
			<input type="password" name="edtPassword" value="<?php echo $this->sDefaultPassword; ?>"><br>
			<label for="edtPasswordRepeat">Repeat password</label><br>
			<input type="password" name="edtPasswordRepeat" value=""><br>
			<label for="edtEmail">Email address (to email status messages)</label><br>
			<input type="text" name="edtEmail" value="<?php echo $this->sDefaultEmail; ?>"><br>
			<br>
			Write these credentials down, because you need them in the future to log in.<br>
			You can change these credentials after logging in if you want to.
		<?php
		$sBody.= ob_get_contents();
		ob_end_clean();  



		$this->renderHTMLTemplate(get_defined_vars());
	}


	/**
	 * handles submitted user login credentials
	 */
	public function createUser()
	{
		//init
		global $sCMSRootPath;
		$this->enableNextButton(); //when errors occur, I disable it -> this can be done in other functions that are called below
		$this->enablePreviousButton(); //when errors occur, I disable it -> this can be done in other functions that are called below
		$this->setURLNextButton('step7.php');
		$this->setURLPreviousButton('step6.php');
		$this->setTextNextButton('Next &gt;');
		$this->setTextPreviousButton('&lt; Back');

		$sBody = '';
		$bSuccess = true;

		$this->setFormAsDefaultVars();
		$this->setFormAsSessionVars();

		//==== CREATING A USER
        //technically we are not creating a new user, just changing the credentials of the first user
		$sBody.= 'Creating user "'.$this->sDefaultUsername.'" in database ';

        $objUser = new TSysCMSUsers();
        $objUser->loadFromDB(); //we should only have 1 user
        $objUser->limitOne(); //we should only have 1 user
		if ($objUser->count() > 0)
		{
			$objUser->setUsername($this->sDefaultUsername);
			$objUser->setUsernamePublic($this->sDefaultUsername);
			$objUser->setPasswordDecrypted($this->sDefaultPassword);
			$objUser->setPasswordExpires(null);
			$objUser->setEmailAddressDecrypted($this->sDefaultEmail);
			$objUser->setLoginEnabled(true); //===> default is false
			if ($objUser->saveToDB())
			{
				$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';
			}
			else
			{
				$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
				error_log('install: create user: saving user failed');
			}
		}
		else
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			error_log('install: create user: loading existing user failed');
		}

		//==== SAVE EMAIL ADDRESS IN CONFIG	
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

			//set actual value in config file
			$objConfig->set('APP_EMAIL_ADMIN', $this->sDefaultEmail);        

			$sBody.= 'Save values to config file application for host: '.$_SERVER['SERVER_NAME'].' ';
			if ($objConfig->saveFile($this->sConfigPathApplication))
			{
				$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';
			}
			else
			{
				$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			}

		}

		
		$sBody.= '<br>We are almost there, click "Next"-button.';

		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * validates form input
	 */
	private function validateForm()
	{
		//init
		$sBody = '';
		$sSanitized = '';
		$sWhitelistUsername = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';
		$sWhitelistPassword = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-%@^!?=&*+$#.~;|{}';
		$sWhitelistEmail = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-@.';


		//==== check empty username
		$sBody.= 'Check empty username ';
		if ($this->sDefaultUsername == '')
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Empty username is not allowed!<br>';
			$this->bValidForm = false;
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';		

		//==== check invalid chars username
		$sBody.= 'Check invalid characters in username ';
		$sSanitized = filterBadCharsWhiteList($this->sDefaultUsername, $sWhitelistUsername);
		if ($this->sDefaultUsername !== $sSanitized)
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Username contains invalid characters. The characters that are allowed: '.$sWhitelistUsername.'<br>';
			$this->bValidForm = false;
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';		

	

		//==== check empty password
		$sBody.= 'Check empty password ';
		if ($this->sDefaultPassword == '')
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Empty password is not allowed!<br>';
			$this->bValidForm = false;
		}	
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';		
	

		//==== check invalid chars password
		$sBody.= 'Check invalid characters in password ';
		$sSanitized = filterBadCharsWhiteList($this->sDefaultPassword, $sWhitelistPassword);
		if ($this->sDefaultPassword !== $sSanitized)
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Password contains invalid characters. The characters that are allowed: '.$sWhitelistPassword.'<br>';
			$this->bValidForm = false;
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';		



		//==== check same password
		$sBody.= 'Check passwords if passwords are the same ';
		if ($this->sDefaultPassword != $this->sDefaultPasswordRepeat)
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Passwords do NOT match!<br>';
			$this->bValidForm = false;
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';

		//==== check invalid chars email
		$sBody.= 'Check invalid characters in email address ';
		$sSanitized = filterBadCharsWhiteList($this->sDefaultEmail, $sWhitelistEmail);
		if ($this->sDefaultEmail !== $sSanitized)
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Email address contains invalid characters. The characters that are allowed: '.$sWhitelistEmail.'<br>';
			$this->bValidForm = false;
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';			

		return $sBody;
	}
	
	/**
	 * copy $_POST to internal default variables like $sDefaultUsername
	 */
	private function setFormAsDefaultVars()
	{
		if (isset($_POST['edtUsername']))
			$this->sDefaultUsername = $_POST['edtUsername'];			
		if (isset($_POST['edtPassword']))
			$this->sDefaultPassword = $_POST['edtPassword'];			
		if (isset($_POST['edtPasswordRepeat']))
			$this->sDefaultPasswordRepeat = $_POST['edtPasswordRepeat'];			
		if (isset($_POST['edtEmail']))
			$this->sDefaultEmail = $_POST['edtEmail'];			
	}

	/**
	 * copy $this->sDefaultUsername to $_SESSION
	 */
	private function setFormAsSessionVars()
	{
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_LOGINUSERNAME] = $this->sDefaultUsername;
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_PASSWORDSTARS] = generateChars('*', strlen($this->sDefaultPassword));
		$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_EMAIL] = $this->sDefaultEmail;
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
		return 'Create login';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'In this step we are going to create a user for '.APP_CMS_APPLICATIONNAME.' to log in.';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenEnterLoginCredentials';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenEnterLoginCredentials', 'screenCreateUser');
	}



}

$objScreen = new step6();