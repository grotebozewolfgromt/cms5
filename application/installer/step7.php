<?php

use dr\classes\models\TSysContactsAbstract;
use dr\classes\TInstallerScreen;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap_cms.php');

/**
 * GDPR
 * 
 * installler/step7.php created 09-10-2025
 * 
 * @author dennis renirie
 */
class step7 extends TInstallerScreen
{

	const SESSIONAK_INSTALLER_RETAINDAYS = 'contact-retaindatadays';
	const SESSIONAK_INSTALLER_SEARCHFIELDS = 'contact-searchfields';

	private $iRetainData = 3650;
	private $arrFields = array();

	private $bValidForm = true;

	private $sURLSubmitForm = 'step7.php?'.TInstallerScreen::GETVARIABLE_MODE.'='.TInstallerScreen::MODE_SUBMITTEDFORM;

	/**
	 * SCREEN 1: settings regarding gdpr
	 */
	public function screenSettings()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->disablePreviousButton();
		$this->setURLPreviousButton('step6.php');	
		$this->setTextPreviousButton('Previous');	


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
				header('location: step8.php');  
				return; //EXIT
			}
			
		}



		ob_start();
		?>
			<h2>Data retention</h2>
			<label for="edtDaysRetainData">How many days would you like retain data? (3650 = 10 years, 0 = never anonymize)</label><br>
			<input type="text" name="edtDaysRetainData" value="3650"><br>
			<ul>
				<li>After this period expires, data will be anonymized.</li>
				<li>The date of last contact is used to determine whether to anonymize.</li>
				<li>Anonymization is permanent and can not be undone.</li>
			</ul>
			<h2>Searchable fields</h2>
			Because of the GDPR we are required to encrypt personal identifyable fields of contacts.<br>
			Encryption prevents data leaks in data breaches, but encryption makes fields <b>unsearchable</b>.<br>
			<br>
			<label>Which fields would you like to be unencrypted and thus able to search?</label><br>
			<input type="checkbox" name="chkFields[]" value="name"> Last name<br>
			<input type="checkbox" name="chkFields[]" value="address"> Address<br>
			<input type="checkbox" name="chkFields[]" value="postalcode"> Postal Code / Zip code<br>
			<input type="checkbox" name="chkFields[]" value="phone"> Phone number<br>
			<input type="checkbox" name="chkFields[]" value="email"> Email address<br>
			<br>
			<i>Notes</i><br>
			<ul>
				<li>Data above will be <b>unencrypted</b>, thus <b>leaks data</b> when a data breach occurs.</li>
				<li>For high risk environments, like a website on the internet, it's wise to <b>select no fields</b></li>
				<li>For low risk environments, like a locally hosted web application, you can select more fields</li>
				<li>Please select only the fields that you strictly need to identify your contact.</li>
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
		if (!is_numeric($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_RETAINDAYS]))
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
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_RETAINDAYS] 		= $_POST['edtDaysRetainData'];
		else
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_RETAINDAYS]			= 0;

		if (isset($_POST['chkFields']))		
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_SEARCHFIELDS] 		= $_POST['chkFields'];
		else
			$_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_SEARCHFIELDS] 		= array();
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

			$objConfig->set('APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS', $_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_RETAINDAYS]);        

			//create array with proper fields
			$arrDBFields = array();
			if ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_SEARCHFIELDS])
			{
				foreach ($_SESSION[TInstallerScreen::SESSIONAK_INSTALLER][step7::SESSIONAK_INSTALLER_SEARCHFIELDS] as $sFormField)
				{
					switch($sFormField)
					{
						case 'name':
							$arrDBFields[] = TSysContactsAbstract::FIELD_LASTNAME;
							break;
						case 'address':
							$arrDBFields[] = TSysContactsAbstract::FIELD_BILLINGADDRESSMISC;
							$arrDBFields[] = TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET;
							$arrDBFields[] = TSysContactsAbstract::FIELD_DELIVERYADDRESSMISC;
							$arrDBFields[] = TSysContactsAbstract::FIELD_DELIVERYADDRESSSTREET;					
							break;
						case 'postalcode':
							$arrDBFields[] = TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP;
							$arrDBFields[] = TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP;
							break;
						case 'phone':
							$arrDBFields[] = TSysContactsAbstract::FIELD_PHONENUMBER1;
							$arrDBFields[] = TSysContactsAbstract::FIELD_PHONENUMBER2;
							break;
						case 'email':
							$arrDBFields[] = TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED;
							$arrDBFields[] = TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSENCRYPTED;
							break;

					}
				}
			}
			if (count($arrDBFields) > 0)
				$objConfig->set('APP_DATAPROTECTION_CONTACTS_SEARCHFIELDS', implode(',',$arrDBFields));        
			else
				$objConfig->set('APP_DATAPROTECTION_CONTACTS_SEARCHFIELDS', '');        


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


}

$objScreen = new step7();