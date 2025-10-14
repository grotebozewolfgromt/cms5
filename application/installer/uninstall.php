<?php

use dr\classes\TInstallerScreen;
use dr\modules\Mod_Sys_Modules\models\TSysModules;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap_admin.php');

/**
 * Uninstall framework
 * 
 * 
 * 
 * installler/uninstall.php created 22-8-2025
 * 
 * @author dennis renirie
 */
class uninstall extends TInstallerScreen
{
	CONST EDITBOX_TEXT_CONFIRM = 'removealldata';

	/**
	 * SCREEN 1: shows confirmation
	 */
	public function screenConfirmUninstall()
	{
		//init
	    $sBody = '';
		$this->enableNextButton();
		$this->enablePreviousButton();
		$this->setURLNextButton('uninstall.php?'.TInstallerScreen::GETVARIABLE_ACTION.'=screenSubmitConfirmation');	
		$this->setTextNextButton('Uninstall');	
		$this->setURLPreviousButton('index.php');	
		$this->setTextPreviousButton('Previous');
		

    	ob_start();
		?>
			<script>
				// function checkAgreeLicense()
				// {
				// 	objCheck1 = document.getElementById('chkRead');
				// 	objCheck2 = document.getElementById('chkAgree');
				// 	objBtnNext = document.getElementById('btnNext');

				// 	if ((objCheck1) && (objCheck2))
				// 	{
				// 		objBtnNext.disabled = !((objCheck1.checked) && (objCheck2.checked)); //user can also uncheck
				// 	}
				// }
			</script>
			<h2>Confirm uninstall</h2>
			<label for="edtConfirm">To delete all data from the database, type "<?php echo uninstall::EDITBOX_TEXT_CONFIRM; ?>" in the text box below:</label><br>				
			<?php
				if (isset($_GET[TInstallerScreen::GETVARIABLE_MODE]))
				{
					if ($_GET[TInstallerScreen::GETVARIABLE_MODE] == TInstallerScreen::MODE_INVALIDCONFIRMATION)
						echo '<span class="error">Text doesn\'t match "'.uninstall::EDITBOX_TEXT_CONFIRM.'"</span>';
				}
			?>
			<input type="text" id="edtConfirm" name="edtConfirm">
		<?php
	    $sBody.= ob_get_contents();
	    ob_end_clean();  


		$this->renderHTMLTemplate(get_defined_vars());
	}

	/**
	 * SCREEN 2: called when submitted confirmation in textbox
	 */
	public function screenSubmitConfirmation()
	{
		$bSuccess = false;

		if (isset($_POST['edtConfirm']))
		{
			if ($_POST['edtConfirm'] == uninstall::EDITBOX_TEXT_CONFIRM)
				$bSuccess = true;
		}

		
		if ($bSuccess) //successfull, redirect forward
		{
			header('Location: uninstall.php?'.TInstallerScreen::GETVARIABLE_ACTION.'=screenUninstall');
			return;
		}
		else //not successfull, redirect back
		{
			header('Location: uninstall.php?'.TInstallerScreen::GETVARIABLE_MODE.'='.TInstallerScreen::MODE_INVALIDCONFIRMATION);
			return;
		}
	}
	
	/**
	 * SCREEN 3: actual uninstall
	 */
	public function screenUninstall()
	{
		//init
	    $sBody = '';
		$this->disableNextButton();
		$this->disablePreviousButton();
		$this->setURLSSE('uninstall.php?'.TInstallerScreen::GETVARIABLE_ACTION.'=runUninstall&'.TInstallerScreen::GETVARIABLE_MODE.'='.$this->getMode());
		$this->setURLNextButton('index.php');
		$this->setURLPreviousButton('index.php');
		$this->setTextNextButton('Finish');
		

		$this->enableMaintenanceMode(true); //we never get out of this mode

		$this->renderHTMLProgressScreen(get_defined_vars());

	}

	/**
	 * runs the actual uninstallation process
	 * Server Sent Events are used to progress through installation
	 */
	public function runUninstall()
	{	
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache'); 		
		$bSuccess = true; //when errors occur, this becomes false
		$iTotalSteps = 2;

		// //LONG RUNNING TASK
		// for($i = 1; $i <= 5; $i++) {
		// 	$this->sendSSEMessage($i, 'on iteration ' . $i . ' of 10' , $i*10, 5, 100, 100); 

		// 	sleep(1);
		// }

		// $this->objTableVersionsFromDBModels = new dr\classes\models\TSysTableVersions();

		
		//step 1: uninstall modules
		if (!$this->uninstallModules(0,$iTotalSteps))
			$bSuccess = false;

		//step 2: uninstall system tables
		if (!$this->uninstallSystemTables(1,$iTotalSteps))
			$bSuccess = false;


		//finish
		if ($bSuccess)
			$this->sendSSEMessage('CLOSE', '<br><b>UnInstallation proces completed </b> '.TInstallerScreen::STATUS_SUCCESS.'<br>Click "Finish"-button to go back to the installer home screen.', 100, 100, 100, 100);		
		else
			$this->sendSSEMessage('CLOSE', '<br><b>UnInstallation process completed, but there were errors</b> '.TInstallerScreen::STATUS_FAILED.'<br>Scroll up to see where the error occurred.<br>Refresh the page to run the uninstall again, or click "Finish"-button to continue.', 100, 100, 100, 100);		

	}

	/**
	 * uninstall modules
	 */
	private function uninstallModules($iCurrentStep, $iMaxSteps)
	{
		$iCountModules = 0;
		$sID = 'uninstallModules';
		$iModelIndex = 0;
		$bSuccess = true;

		$objModules = new TSysModules();
		$objModules->loadFromDB(); //load all modules
		$iCountModules = $objModules->count();


        $this->sendSSEMessage($sID.'message', 'Removing data modules:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountModules); 
        error_log('removing data modules ... ');   

        
        while ($objModules->next())
        {
            $sCurrMod = getModuleFullNamespaceClass($objModules->getNameInternal());

            $this->sendSSEMessage($sID.$objModules->getNameInternal().'title', ' - Removing '.$objModules->getNameInternal().' ', $iCurrentStep, $iMaxSteps, $iModelIndex+1, $iCountModules); 
            error_log('removing data module '.$sCurrMod.' ...');
            
            $objCurrMod = new $sCurrMod;
            if (!$objCurrMod->uninstallModule(false))
            {
				$this->sendSSEMessage($sID.$objModules->getNameInternal().'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iModelIndex+1, $iCountModules); 
                error_log('removing tables (TSysModel) failed for module '.$sCurrMod);
                $bSuccess = false;
            } 
			else		
				$this->sendSSEMessage($sID.$objModules->getNameInternal().'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iModelIndex+1, $iCountModules); 			


			$iModelIndex++;
        }
         
        return $bSuccess; 
	}

	/**
	 * uninstall system tables
	 */
	private function uninstallSystemTables($iCurrentStep, $iMaxSteps)
	{
		$sID = 'uninstallSystemTables';
        $arrSystemDBTables = getSystemModelsInstantiated();
		$iCountTables = 0;
		$iCountTables = count($arrSystemDBTables);
		$iCounter = 0;
		$bSuccess = true;

        $this->sendSSEMessage($sID.'message', 'Removing system database tables:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountTables); 
        error_log('removing system database tables ... ');
   
  
        //in reversed order, because of dependencies, somehow array_reverse($arrSystemDBTables) doesn't work
        $iMaxIndexSysTables = count($arrSystemDBTables) -1;//the index starts at 0, so the count is one too many
        $objModel = null;
        for ($iTableIndex = $iMaxIndexSysTables; $iTableIndex >= 0; $iTableIndex--) //for in reverse
        {
            $objModel = $arrSystemDBTables[$iTableIndex];

			$this->sendSSEMessage($sID.$objModel::getTable().'title', ' - Removing '.$objModel::getTable().' ', $iCurrentStep, $iMaxSteps, $iCounter+1, $iCountTables); 
			error_log('refactor table '.$objModel::getTable().' ...');	

            if (!$objModel->uninstallDB(null))
            {
				$this->sendSSEMessage($sID.$objModel::getTable().'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iCounter+1, $iCountTables); 
				error_log('remove failed of table '.$objModel::getTable());
                
                $bSuccess = false;
            }       
			else		
				$this->sendSSEMessage($sID.$objModel::getTable().'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iCounter+1, $iCountTables); 			


			$iCounter++; 
        }


		return $bSuccess;
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
		return 'Uninstall '.APP_CMS_APPLICATIONNAME;
	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		return 'By uninstalling '.APP_CMS_APPLICATIONNAME.' you will remove all data in the database.<br>Please be patient until the process is finished.';
	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenConfirmUninstall';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenConfirmUninstall', 'screenSubmitConfirmation', 'screenUninstall', 'runUninstall');
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
		return '';
	}		


}

$objScreen = new uninstall();