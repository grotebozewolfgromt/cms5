<?php

use dr\classes\TInstallerScreen;

define('APP_MAINTENANCEMODE_SKIPCHECK', true); //skip maintenance-mode check in bootstrap

$sCMSRootPath = dirname( dirname(__FILE__) );
include_once($sCMSRootPath.DIRECTORY_SEPARATOR.'bootstrap_cms.php');

/**
 * STEP 6: create schema + inserting data into database
 * 
 * 
 * installler/step6.php created 20-8-2025
 * 
 * @author dennis renirie
 */
class step6 extends TInstallerScreen
{
	private $objTableVersionsFromDBModels = null;
    private $arrPreviousDependenciesModelClasses = array();

	/**
	 * SCREEN 1: insert data
	 */
	public function screenInsertData()
	{
		//init
	    $sBody = '';
		$this->disableNextButton();
		$this->disablePreviousButton();
		$this->setURLSSE('step6.php?'.TInstallerScreen::GETVARIABLE_ACTION.'=runInstallUpdate&'.TInstallerScreen::GETVARIABLE_MODE.'='.$this->getMode());

		if ($this->getMode() == TInstallerScreen::MODE_UPDATE) //update
		{
			$this->setURLNextButton(APP_URL_CMS);
			$this->setURLPreviousButton('index.php');
			$this->setTextNextButton('Log In');
		}
		else //install
		{
			// $this->setURLNextButton('step7.php');
			// $this->setURLPreviousButton('step5.php');
		}

		



		$this->renderHTMLProgressScreen(get_defined_vars());
	}

	/**
	 * runs the actual installation process
	 */
	public function runInstallUpdate()
	{
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache'); 		
		$bSuccess = true; //when errors occur, this becomes false
		$iTotalSteps = 11;

		// //LONG RUNNING TASK
		// for($i = 1; $i <= 5; $i++) {
		// 	$this->sendSSEMessage($i, 'on iteration ' . $i . ' of 10' , $i*10, 5, 100, 100); 

		// 	sleep(1);
		// }

		$this->objTableVersionsFromDBModels = new dr\classes\models\TSysTableVersions();


		// ==== INSTALLATION ONLY ==== 
		
		//step 1: create database schema
		if (!$this->installDBSchema(0,$iTotalSteps))
		{
			$this->sendSSEMessage('CLOSE', '<br><b>Installation failed. This is critical error, the installation is aborted.</b>.<br>', 100, 100, 100, 100);		
			return false;
		}

		//step 2: create system tables
		if (!$this->installSystemDatabaseTables(1,$iTotalSteps))
			$bSuccess = false;

		//step 3: install modules
		if (!$this->installModules(2,$iTotalSteps))
			$bSuccess = false;

		// ==== UPDATING ==== 		

		//step 4: update table versions (1/2)
		if (!$this->updateTableVersions1(3,$iTotalSteps))
			$bSuccess = false;

		//step 5: update system tables
		if (!$this->updateSytemTableVersions(4,$iTotalSteps))
			$bSuccess = false;

		//step 6: update modules
		if (!$this->updateModules(5,$iTotalSteps))
			$bSuccess = false;

		//step 7: permissions
		if (!$this->updatePermissions(6,$iTotalSteps))
			$bSuccess = false;

		//step 8: settings
		if (!$this->updateSettings(7,$iTotalSteps))
			$bSuccess = false;

		//step 9: update table versions (2/2)
		if (!$this->updateTableVersions2(8,$iTotalSteps))
			$bSuccess = false;

		// ==== PROPAGATE DATA ==== 
		//I propagate data at the end, because that makes the chance bigger that we have a working installation
		//when the installation process is interrupted.

		//step 11: propagate data system tables
		if (!$this->propagateDataSystemTables(9,$iTotalSteps))
			$bSuccess = false;

		//step 12: propagate data modules
		if (!$this->propagateDataModules(10 ,$iTotalSteps))
			$bSuccess = false;

		//finish
		if ($this->getMode() == TInstallerScreen::MODE_UPDATE)
		{
			if ($bSuccess)
				$this->sendSSEMessage('CLOSE', '<br><b>Update proces completed </b> '.TInstallerScreen::STATUS_SUCCESS.'<br>Click "Log In"-button to go back to the admin panel.', 100, 100, 100, 100);		
			else
				$this->sendSSEMessage('CLOSE', '<br><b>Update process completed, but there were errors</b> '.TInstallerScreen::STATUS_FAILED.'<br>Scroll up to see where the error occurred.<br>Refresh the page to run the installation again, or click "Finish".', 100, 100, 100, 100);		

			//update config file to disable the updater
			$this->disableInstaller();
		}
		else
		{
			if ($bSuccess)
				$this->sendSSEMessage('CLOSE', '<br><b>Installation proces completed </b> '.TInstallerScreen::STATUS_SUCCESS.'<br>Click "'.$this->getTextNextButton().'"-button to create a new user.', 100, 100, 100, 100);		
			else
				$this->sendSSEMessage('CLOSE', '<br><b>Installation process completed, but there were errors</b> '.TInstallerScreen::STATUS_FAILED.'<br>Scroll up to see where the error occurred.<br>Refresh the page to run the installation again, or click "'.$this->getTextNextButton().'"-button to continue.', 100, 100, 100, 100);		
		}
	}

	/**
	 * create database schema
	 */	
	private function installDBSchema($iCurrentStep, $iMaxSteps)
	{
		$sID = 'installDBSchema';


		//start database connection
		$objConn = new dr\classes\db\TDBConnectionMySQL();
		$objConn->setErrorReporting(dr\classes\db\TDBConnection::REPORT_ERROR_OFF);
		$objConn->setHost(APP_DB_HOST);
		$objConn->setUsername(APP_DB_USER);
		$objConn->setPassword(APP_DB_PASSWORD);
		// $objConn->setDatabaseName(APP_DB_DATABASE);--> dont use here, we only check connection details, because the database schema doesnt exist yet and will therefore give an error
		$objConn->setPort(APP_DB_PORT);

		$this->sendSSEMessage($sID.'message', 'Establish database connection ', $iCurrentStep, $iMaxSteps, 0, 4); 
		if ($objConn->connect())
		{
			$this->sendSSEMessage($sID.'connectionconfirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, 1, 4); 

			$objQuery = $objConn->getPreparedStatement();

			$this->sendSSEMessage($sID.'messagecheckexists', 'Check if database schema "'.APP_DB_DATABASE.'" exists ', $iCurrentStep, $iMaxSteps, 2, 4); 
			if (!$objQuery->databaseExists(APP_DB_DATABASE))
			{          
				$this->sendSSEMessage($sID.'schemaexistsdone', TInstallerScreen::STATUS_DONE.'<br>Schema doesn\'t, create new schema "'.APP_DB_DATABASE.'" ', $iCurrentStep, $iMaxSteps, 3, 4); 

				if ($objQuery->createDatabase(APP_DB_DATABASE))
					$this->sendSSEMessage($sID.'createschemasucces', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, 4, 4); 
				else
					$this->sendSSEMessage($sID.'createschemasucces', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, 4, 4);
			}
			else
			{
				$this->sendSSEMessage($sID.'schemaexistsdone', TInstallerScreen::STATUS_DONE.'<br>Schema "'.APP_DB_DATABASE.'" already exists. So don\'t create it. '.TInstallerScreen::STATUS_DONE.'<br>', $iCurrentStep, $iMaxSteps, 4, 4); 
			}

		}
		else
		{
			$this->sendSSEMessage($sID.'connectionfailed', TInstallerScreen::STATUS_FAILED.'<br><b>Please revise your database credentials by clicking the "'.$this->getTextPreviousButton().'"-button</b>', $iCurrentStep, $iMaxSteps, 4, 4); 
		}		

		return true;
	}

	/**
	 * install db tables
	 */
	private function installSystemDatabaseTables($iCurrentStep, $iMaxSteps)
	{
		$iCountTables = 0;
		$iModCounter = 0;
		$sID = 'systbl';
		$sTableName = '';
        $arrSystemDBTables = getSystemModelsInstantiated();
        $iCountTables = count($arrSystemDBTables);

        $this->sendSSEMessage($sID.'message', 'Creating system database tables:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountTables); 
        error_log('creating system database tables:');

        foreach ($arrSystemDBTables as $objModel)
        {
			$sTableName = $objModel::getTable();
			
			$this->sendSSEMessage($sID.$sTableName, ' - Create '.$sTableName.' ', $iCurrentStep, $iMaxSteps, $iModCounter+1, $iCountTables); 

			if (!$objModel->install(null))
			{
				$this->sendSSEMessage($sID.$sTableName.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iModCounter+1, $iCountTables); 
				error_log('table creation failed for table '.$sTableName);
				return false;
			}
			
			$this->sendSSEMessage($sID.$sTableName.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iModCounter+1, $iCountTables); 
			++$iModCounter;
        }        

		return true;
	}

	/**
	 * install modules
	 */
	private function installModules($iCurrentStep, $iMaxSteps)
	{
		$iCountModules = 0;
		$sID = 'modules';
		$sCurrMod = '';
        $arrModuleFolders = getModuleFolders();
		$iCountModules = count($arrModuleFolders);
		$bSuccess = true;

        $this->sendSSEMessage($sID.'message', 'Installing modules:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountModules); 
        error_log('Installing modules:');

        for ($iModIndex = 0; $iModIndex < $iCountModules; $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
            $objCurrMod = new $sCurrMod;

            $this->sendSSEMessage($sID.$sCurrMod, ' - Installing '.get_class_short($objCurrMod).' ', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules); 
            error_log('installing module '.$sCurrMod.' ...');
            
            if (!$objCurrMod->installModule())
            {
				$this->sendSSEMessage($sID.$sCurrMod.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules); 
				error_log('install failed for module '.$sCurrMod);
                
                $bSuccess = false;
            }         
			else
				$this->sendSSEMessage($sID.$sCurrMod.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules); 			
        }		

		return $bSuccess;
	}	

	/**
	 * update table versions (1/2)
	 */
	private function updateTableVersions1($iCurrentStep, $iMaxSteps)
	{
		$sID = 'updatetableversions1';

        $this->sendSSEMessage($sID.'message', 'Synchronizing table versions ', $iCurrentStep, $iMaxSteps, 0, 1); 
        error_log('Table version system: running synchronisation ...');
        
        
        if ($this->objTableVersionsFromDBModels->synchronizeTablesDB())
        { 
           $this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);             
		   return true;
        }
        else
        {
           $this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);             
           error_log('synchronizeTablesDB() failed!');   
           
           //it failed but we continue to run the script
		   return false;
        }		

		return true;
	}

	/**
	 * update system table versions
	 */
	private function updateSytemTableVersions($iCurrentStep, $iMaxSteps)
	{
		$iCountModels = 0;
		$sID = 'updateSytemTableVersions';
		$arrSystemDBTables = getSystemModelsInstantiated();
		$iCountModels = count($arrSystemDBTables);
		$iModelIndex = 0;


        $this->sendSSEMessage($sID.'message', 'Refactoring system database tables:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountModels); 
        error_log('Refactoring system database tables:');

        foreach ($arrSystemDBTables as $objSystemObject) //tmodel
        {
			$sTableName = $objSystemObject::getTable();

			$this->sendSSEMessage($sID.$sTableName.'title', ' - Refactor '.$sTableName.' ', $iCurrentStep, $iMaxSteps, $iModelIndex+1, $iCountModels); 
			error_log('refactor table '.$sTableName.' ...');			
			
			if (!$objSystemObject->refactorDB($this->arrPreviousDependenciesModelClasses, $this->objTableVersionsFromDBModels))
			{
				$this->sendSSEMessage($sID.$sTableName.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iModelIndex+1, $iCountModels); 
				error_log('refactor failed for table '.$sTableName);
                
                return false;
			}
			else		
				$this->sendSSEMessage($sID.$sTableName.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iModelIndex+1, $iCountModels); 			

			$iModelIndex++;
        }                   
       


		return true;
	}		


	/**
	 * update modules
	 */
	private function updateModules($iCurrentStep, $iMaxSteps)
	{
		$sID = 'updatemodules';
		$arrModuleFolders = getModuleFolders();
		$iCountModules = 0;
		$iCountModules = count($arrModuleFolders);

        $this->sendSSEMessage($sID.'message', 'Updating modules:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountModules); 
        error_log('Updating module database tables:');


        for ($iModIndex = 0; $iModIndex < count($arrModuleFolders); $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
            $objCurrMod = new $sCurrMod;

			$this->sendSSEMessage($sID.$sCurrMod.'title', ' - Updating '.get_class_short($objCurrMod).' ', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules); 
			error_log('refactor table '.$sCurrMod.' ...');			
            
            if (!$objCurrMod->updateModels($this->arrPreviousDependenciesModelClasses, $this->objTableVersionsFromDBModels))
            {
				$this->sendSSEMessage($sID.$sCurrMod.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules);                     
                error_log('updating tables (TSysModel) failed for module '.$sCurrMod);
                return false;
            }     
			else
				$this->sendSSEMessage($sID.$sCurrMod.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules); 
        }
		
		return true;
	}	

	/**
	 * update permissions
	 */	
	private function updatePermissions($iCurrentStep, $iMaxSteps)
	{
		$sID = 'updatepermissions';

        $this->sendSSEMessage($sID.'message', 'Updating permissions, this can take a couple of seconds ', $iCurrentStep, $iMaxSteps, 0, 1); 
        error_log('updating permissions ... ');   

        $objPermissions = new dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions();
        if (!$objPermissions->updatePermissions())
        {
			$this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);                     
            error_log('updating permissions failed');
            return false;
        }
		else
		{
			$this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);                     		
			return true;
		}

		return true;
	}

	/**
	 * update settings
	 */	
	private function updateSettings($iCurrentStep, $iMaxSteps)
	{
		$sID = 'updatepermissions';

        $this->sendSSEMessage($sID.'message', 'Updating settings ', $iCurrentStep, $iMaxSteps, 0, 1); 
        error_log('updating settings ... ');   

        $objSettings = new dr\modules\Mod_Sys_Settings\models\TSysSettings();
        if (!$objSettings->updateSettingsDB())
        {
            $this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);                      
            error_log('updating settings failed');
            return false;
        }
		else
		{
			$this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);                     		
			return true;
		}
 
		return true;
	}	

	/**
	 * update table versions (2/2)
	 */	
	private function updateTableVersions2($iCurrentStep, $iMaxSteps)
	{
		$sID = 'updatepermissions2';

        $this->sendSSEMessage($sID.'message', 'Table version system: updating version numbers ', $iCurrentStep, $iMaxSteps, 0, 1); 
        error_log('table version system: updating version numbers ... ');
        
        if ($this->objTableVersionsFromDBModels->saveToDB())
        {
			$this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);                                            
			return true;
        }
        else
        {
			$this->sendSSEMessage($sID.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, 1, 1);                                            
            error_log('table version system: saveToDB() failed');   
            
            return false;
        }
        
        unset($this->objTableVersionsFromDBModels);		
		return true;
	}	

	/**
	 * propagate data system tables
	 */	
	private function propagateDataSystemTables($iCurrentStep, $iMaxSteps)
	{
		$sID = 'propagateDataSystemTables';
        $arrSystemDBTables = getSystemModelsInstantiated();
		$iCountModels = 0;
		$iCountModels = count($arrSystemDBTables);
		$iIndex = 0;
		$bSuccess = true;

        $this->sendSSEMessage($sID.'message', 'Propagate data for system tables:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountModels); 
        error_log('propagating system database tables:');
        
        foreach ($arrSystemDBTables as $objModel)
        {
			$sCurrModel = $objModel::getTable();

			$this->sendSSEMessage($sID.$sCurrModel.'title', ' - Propagating data in '.$sCurrModel.' ', $iCurrentStep, $iMaxSteps, $iIndex+1, $iCountModels); 
			error_log('refactor table '.$sCurrModel.' ...');	

			if (!$objModel->propagateData())
			{
				$this->sendSSEMessage($sID.$sCurrModel.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iIndex+1, $iCountModels);                     
				error_log('propagating data failed for: '.$sCurrModel);                
				$bSuccess =  false;
			}
			else
				$this->sendSSEMessage($sID.$sCurrModel.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iIndex+1, $iCountModels);                     


			$iIndex++;
        }        


		return $bSuccess;
	}

	/**
	 * propagate data modules
	 */	
	private function propagateDataModules($iCurrentStep, $iMaxSteps)
	{
		$sID = 'propagateDataModules';
		$bSuccess = true;		
		$arrModuleFolders = array();
		$arrModuleFolders = getModuleFolders();
		$iCountModules = count($arrModuleFolders);

        $this->sendSSEMessage($sID.'message', 'Propagate data modules:<br>', $iCurrentStep, $iMaxSteps, 0, $iCountModules); 
        error_log('propagating data modules:');
        
        for ($iModIndex = 0; $iModIndex < $iCountModules; $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
			$objCurrMod = new $sCurrMod;

            $this->sendSSEMessage($sID.$sCurrMod.'title', ' - Propagating data for '.get_class_short($objCurrMod).' ', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules); 
            error_log('propagate data module '.$sCurrMod.' ...');
            

            if (!$objCurrMod->propagateDataModule())
            {
				$this->sendSSEMessage($sID.$sCurrMod.'confirm', TInstallerScreen::STATUS_FAILED.'<br>', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules);
                error_log('data propagation failed for module '.$sCurrMod);

                $bSuccess = false;
            }            
			else
				$this->sendSSEMessage($sID.$sCurrMod.'confirm', TInstallerScreen::STATUS_SUCCESS.'<br>', $iCurrentStep, $iMaxSteps, $iModIndex+1, $iCountModules);
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
		if ($this->getMode() == TInstallerScreen::MODE_UPDATE)
			return 'Update '.APP_CMS_APPLICATIONNAME;
		else		
			return 'Install '.APP_CMS_APPLICATIONNAME;

	}

	/**
	 * give a brief description of the current screen
	 * @return string
	 */
	public function getDescription()
	{
		if ($this->getMode() == TInstallerScreen::MODE_UPDATE)
			return 'We are going to update the software for you.<br>Don\'t close this webpage until the update in finished, please be patient.';
		else
			return 'We are going to install the software for you.<br>Don\'t close this webpage until the installation in finished, please be patient.';

	}

	/**
	 * return the default action when no action is specified
	 * @return string
	 */
	public function getDefaultAction()
	{
		return 'screenInsertData';
	}

	/**
	 * return a 1d array with functions that are allowed to be executed as action:
	 * $_GET[GETVARIABLE_ACTION] action
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		return array('screenInsertData', 'runInstallUpdate');
	}



	/**
	 * specify what is the previous controller in the process.
	 * this will be the default url for previous button,
	 * which you can override this with setURLPreviousButton()
	 */	
	public function getURLPreviousController()
	{
		return 'step5.php';
	}		


	/**
	 * specify what is the next controller in the process.
	 * this will be the default url for next button,
	 * which you can override this with setURLNextButton()
	 */
	public function getURLNextController()
	{
		return 'step7.php';
	}	
}

$objScreen = new step6();