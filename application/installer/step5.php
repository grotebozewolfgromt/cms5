<?php

use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\TInstallerScreen;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap_admin.php');

/**
 * GDPR
 * 
 * installler/step5.php created 09-10-2025
 * 
 * @author dennis renirie
 */
class step5 extends TInstallerScreen
{

	const SESSIONAK_INSTALLER_RETAINDAYS = 'contact-retaindatadays';
	const SESSIONAK_INSTALLER_ENCRYPTFIELDS = 'contact-encryptfields';
	const SESSIONAK_INSTALLER_SEARCHFIELDS = 'contact-searchfields';

	private $iRetainData = 3650;
	private $arrFields = array();

	private $bValidForm = true;

	private $sURLSubmitForm = 'step5.php?'.TInstallerScreen::GETVARIABLE_MODE.'='.TInstallerScreen::MODE_SUBMITTEDFORM;

	/**
	 * SCREEN 1: settings regarding gdpr
	 */
	public function screenSettings()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();


		//==== SET MODE
		if (!isset($_GET[TInstallerScreen::GETVARIABLE_MODE]))//the first time entering this screen, then the user is inputting data in the form
			$this->setMode(TInstallerScreen::MODE_INPUTFORM);


		//=== MODE: input
		if ($this->getMode() == TInstallerScreen::MODE_INPUTFORM)
		{
			$this->setURLNextButton($this->sURLSubmitForm);
			$this->setTextNextButton('Save');			
		}

		
		//==== MODE: submitted
		if ($this->getMode() == TInstallerScreen::MODE_SUBMITTEDFORM)
		{
			$this->setURLNextButton($this->sURLSubmitForm);
			$this->setTextNextButton('Create');	

			$sBody.= '<h2>Doing checks:</h2>';
			$sBody.= $this->sanitizeForm();
			$sBody.= $this->validateForm();
			$this->saveFormInSession();
			$sBody.= '<br>';			

			//good form
			if ($this->bValidForm)
			{
				$sBody.= $this->saveInConfigFile();
				header('location: step6.php');  
				return; //EXIT
			}
			
		}



		ob_start();
		?>
			<h2>Data retention</h2>
			<label for="edtDaysRetainData">How many days would you like retain data? (3650 = 10 years, 0 = never anonymize)</label><br>
			<input type="text" name="edtDaysRetainData" value="3650"><br>
			<ul>
				<li>After the period above expires, data will be anonymized.</li>
				<li><span style="color:darkred;">Anonymization is permanent and can not be undone.</span></li>
				<li>The date of last contact is used to determine whether to anonymize data or not.</li>
			</ul>
			<br>
			<h2>Encrypt fields</h2>
			To abide by the GDPR you are required to encrypt personal identifyable fields of contacts.<br>
			Encryption prevents data leaks in data breaches, but encryption makes fields <b>unsearchable</b> and <b>unsortable</b>.<br>
			<br>
			<label>Which fields would you like to encrypt and make unsearchable?</label><br>
			<input type="checkbox" name="chkFieldsEncrypt[]" value="name" checked> Last name<br>
			<input type="checkbox" name="chkFieldsEncrypt[]" value="address" checked> Address<br>
			<input type="checkbox" name="chkFieldsEncrypt[]" value="postalcode" checked> Postal Code / Zip code<br>
			<input type="checkbox" name="chkFieldsEncrypt[]" value="phone" checked> Phone number<br>
			<input type="checkbox" name="chkFieldsEncrypt[]" value="email" checked> Email address<br>
			<br>
			<i>Notes</i><br>
			<ul>
				<li><span style="color:darkred;">This choice is permanent and can not be undone<span></li>
				<li>For high risk environments, like a website on the internet: <b>select all fields</b></li>
				<li>For low risk environments, like a local hosted web application: you could decide to select less fields</li>
			</ul>
			<br>
			<h2>Add values to search field</h2>
			To make searching contacts easier, you can let us automatically store values in the contact-search-field upon saving a contact.<br>
			<label>Which values would you like to store in the search field?</label><br>
			<input type="checkbox" name="chkFieldsSearch[]" value="name"> Last name<br>
			<input type="checkbox" name="chkFieldsSearch[]" value="address"> Address<br>
			<input type="checkbox" name="chkFieldsSearch[]" value="postalcode"> Postal Code / Zip code<br>
			<input type="checkbox" name="chkFieldsSearch[]" value="phone"> Phone number<br>
			<input type="checkbox" name="chkFieldsSearch[]" value="email"> Email address<br>
			<br>
			<i>Notes</i><br>
			<ul>
				<li>The contact-search-field is NOT encrypted, and will leak data when a data breach occurs</li>
				<li>For high risk environments, like a website on the internet: <b>select no fields</b></li>
				<li>For low risk environments, like a local hosted web application: you could decide to select more fields</li>
			</ul>			
		<?php
		$sBody = ob_get_contents();
		ob_end_clean();  

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


		//==== check days
		$sBody.= 'Numeric days ';
		if (!is_numeric($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_RETAINDAYS]))
		{
			$sBody.= TInstallerScreen::STATUS_FAILED.'<br>';
			$sBody.= 'Days is not numeric!<br>';
			$this->bValidForm = false;
		}
		else
			$sBody.= TInstallerScreen::STATUS_SUCCESS.'<br>';		
	

		return $sBody;
	}	

	/**
	 * sanitizes form input
	 */
	private function sanitizeForm()
	{
		if (isset($_POST['edtDaysRetainData']))
		{
			$_POST['edtDaysRetainData'] = filterBadCharsWhiteList($_POST['edtDaysRetainData'], '0123456789');
		}

	}

	/**
	 * stores form values in session
	 */
	private function saveFormInSession()
	{
		if (isset($_POST['edtDaysRetainData']))
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_RETAINDAYS] 		= $_POST['edtDaysRetainData'];
		else
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_RETAINDAYS]			= 0;

		if (isset($_POST['chkFieldsEncrypt']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_ENCRYPTFIELDS] 		= $_POST['chkFieldsEncrypt'];
		else
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_ENCRYPTFIELDS] 		= array();

		if (isset($_POST['chkFieldsSearch']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_SEARCHFIELDS] 		= $_POST['chkFieldsSearch'];
		else
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_SEARCHFIELDS] 		= array();
	}

	
	/**
	 * stores values in config file
	 */
	private function saveInConfigFile()
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

			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_RETAINDAYS]);        

			//config: searchable fields: create array with proper fields
			$arrDBFields = array();
			if ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_SEARCHFIELDS])
			{
				foreach ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_SEARCHFIELDS] as $sFormField)
				{
					switch($sFormField)
					{
						case 'name':
							$arrDBFields[] = TSysContacts::FIELD_LASTNAME;
							break;
						case 'address':
							$arrDBFields[] = TSysContacts::FIELD_BILLINGADDRESSMISC;
							$arrDBFields[] = TSysContacts::FIELD_BILLINGADDRESSSTREET;
							$arrDBFields[] = TSysContacts::FIELD_DELIVERYADDRESSMISC;
							$arrDBFields[] = TSysContacts::FIELD_DELIVERYADDRESSSTREET;					
							break;
						case 'postalcode':
							$arrDBFields[] = TSysContacts::FIELD_BILLINGPOSTALCODEZIP;
							$arrDBFields[] = TSysContacts::FIELD_DELIVERYPOSTALCODEZIP;
							break;
						case 'phone':
							$arrDBFields[] = TSysContacts::FIELD_PHONENUMBER1;
							$arrDBFields[] = TSysContacts::FIELD_PHONENUMBER2;
							break;
						case 'email':
							$arrDBFields[] = TSysContacts::FIELD_EMAILADDRESSENCRYPTED;
							$arrDBFields[] = TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED;
							break;

					}
				}
			}
			if (count($arrDBFields) > 0)
				$objConfig->set('APP_DATAPROTECTION_CONTACTS_SEARCHFIELDS', implode(',',$arrDBFields));        
			else
				$objConfig->set('APP_DATAPROTECTION_CONTACTS_SEARCHFIELDS', '');


			//store fields to encrypt
			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME', false); //default
			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS', false); //default
			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP', false); //default
			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER', false); //default
			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS', false); //default
			if ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_ENCRYPTFIELDS])
			{
				foreach ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step5::SESSIONAK_INSTALLER_ENCRYPTFIELDS] as $sFormField)
				{
					switch($sFormField)
					{
						case 'name':
							$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME', true);
							break;
						case 'address':
							$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS', true);
							break;
						case 'postalcode':
							$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP', true);
							break;
						case 'phone':
							$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER', true);
							break;
						case 'email':
							$objConfig->set('APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS', true);
							break;

					}
				}
			}			
			

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
		return 'Privacy laws';
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'To abide by privacy protection laws like GDPR, we have to ask you some questions';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenSettings';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenSettings');
	}

	/**
	 * specify what is the previous controller in the process.
	 * this will be the default url for previous button,
	 * which you can override this with setURLPreviousButton()
	 */	
	public function getURLPreviousController()
	{
		return 'step4.php';
	}		


	/**
	 * specify what is the next controller in the process.
	 * this will be the default url for next button,
	 * which you can override this with setURLNextButton()
	 */
	public function getURLNextController()
	{
		return 'step6.php';
	}	
}

$objScreen = new step5();