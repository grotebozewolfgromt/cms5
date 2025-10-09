<?php

use dr\classes\TInstallerScreen;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap_cms.php');

/**
 * Installation overview + link to log in + email report
 * 
 * 
 * Example of Softaculous:
 * De installatie van WordPress 6.0.1 is voltooid. Hieronder kunt u de details van de installatie zien:
Path : /home/...
URL : https://...
Admin URL : https://s...
Admin gebruikersnaam: de...
Admin wachtwoord: ********
Admin Email: em....
MySQL database: be...
MySQL DB gebruiker: be...
MySQL DB host: localhost
MySQL DB wachtwoord: ***********
Update notificatie: Actief
Auto Upgrade : Enabled (Major and Minor)
Automated Backups : Elke dag
Backup Rotation : 7
Tijdstip van installatie : Augustus 21, 2021, 7:01 pm

If you wish to unsubscribe from such emails, go to your Control Panel -> Softaculous -> Email Settings
 * 
 * 
 * installler/step7.php created 22-8-2025
 * 
 * @author dennis renirie
 */
class step8 extends TInstallerScreen
{

	/**
	 * SCREEN 1: shows overview of installation
	 */
	public function screenInstallationReport()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->disablePreviousButton();
		$this->setURLNextButton(APP_URL_CMS);	
		$this->setTextNextButton('Log in');	
		$this->setURLPreviousButton('step7.php');	
		$this->setTextPreviousButton('Previous');	

		//set configs
		$this->disableInstaller();
		$this->enableMaintenanceMode(false);
		
		$sBody.= 'Installation finished: '.date('Y-m-d H:i').'h<br>';
		$sBody.= '<h2>Paths</h2>';
		$sBody.= 'Path: '.APP_PATH_CMS.'<br>';
		$sBody.= 'URL: <a href="'.APP_URL_CMS.'" target="_blank">'.APP_URL_CMS.'</a><br>';
		$sBody.= 'Path config file application: '.$this->sConfigPathApplication.'<br>';
		$sBody.= '<h2>Login</h2>';
		$sBody.= 'User name: '.$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_LOGINUSERNAME].'<br>';
		$sBody.= 'Password: '.$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_PASSWORDSTARS].'<br>';
		$sBody.= 'Email: '.$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][TInstallerScreen::SESSIONAK_INSTALLER_EMAIL].'<br>';
		$sBody.= '<h2>Database</h2>';		
		$sBody.= 'Host: '.APP_DB_HOST.'<br>';
		$sBody.= 'User name: '.APP_DB_USER.'<br>';
		$sBody.= 'Password: '.generateChars('*', strlen(APP_DB_PASSWORD)).'<br>';
		$sBody.= 'Database schema: '.APP_DB_DATABASE.'<br>';
		$sBody.= 'Port: '.APP_DB_PORT.'<br>';
		$sBody.= 'Table Prefix: '.APP_DB_TABLEPREFIX.'<br>';
		$sBody.= '<br>';
		$sBody.= 'Thank you for installing '.APP_CMS_APPLICATIONNAME.'.<br>';
		$sBody.= 'Click "Log in" to start.';


		$this->renderHTMLTemplate(get_defined_vars());
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
		return 'Installation report';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'The installation of '.APP_CMS_APPLICATIONNAME.' is complete, below you can find the details of the installation.<br>These details are important: write them down, copy-paste or print them, so you can\'t lose them';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenInstallationReport';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenInstallationReport');
	}


}

$objScreen = new step8();