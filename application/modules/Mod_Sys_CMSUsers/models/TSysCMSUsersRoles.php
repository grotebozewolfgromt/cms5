<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\classes\models\TSysUsersRolesAbstract;

/**
 * Description of TSysCMSUsers
 *
 * 16 nov 2022: TSysCMSUsersGroups renamed to TSysCMSUsersRoles
 * 
 * 
 * @author drenirie
 */
class TSysCMSUsersRoles extends TSysUsersRolesAbstract
{
    const ROLENAME_DEFAULT_ADMINISTRATORS       = 'God'; //describes the role better than admin (to avoid confusion with "account admin")
    const ROLENAME_DEFAULT_HUMANS               = 'Humans'; //account holders are called "account administrators", basically my clients. the idea: account administrators can invite ROLENAME_DEFAULT_ACCOUNTADMIN and ROLENAME_DEFAULT_ACCOUNTUSERS, but not ROLENAME_DEFAULT_ADMINISTRATORS
    const ROLENAME_DEFAULT_BASICACCOUNTADMIN    = 'BASIC account admin'; //account holders are called "account administrators", basically my clients. the idea: account administrators can invite ROLENAME_DEFAULT_ACCOUNTADMIN and ROLENAME_DEFAULT_ACCOUNTUSERS, but not ROLENAME_DEFAULT_ADMINISTRATORS
    // const ROLENAME_DEFAULT_ADVANCEDACCOUNTADMIN = 'ADVANCED account admin'; //account holders are called "account administrators", basically my clients. the idea: account administrators can invite ROLENAME_DEFAULT_ACCOUNTADMIN and ROLENAME_DEFAULT_ACCOUNTUSERS, but not ROLENAME_DEFAULT_ADMINISTRATORS
    // const ROLENAME_DEFAULT_PROACCOUNTADMIN      = 'PRO account admin'; //account holders are called "account administrators", basically my clients. the idea: account administrators can invite ROLENAME_DEFAULT_ACCOUNTADMIN and ROLENAME_DEFAULT_ACCOUNTUSERS, but not ROLENAME_DEFAULT_ADMINISTRATORS
    // const ROLENAME_DEFAULT_ACCOUNTUSERS         = 'Account user'; //account holders can create regular users
    const ROLENAME_DEFAULT_ANONYMOUS            = 'Anonymous'; //users that are not logged in into the system
    const ROLENAME_DEFAULT_DEMO                 = 'Demo users'; //potential users that can log in to see the functionality of the CMS and websites. This role is not added on installation, but on cronjob (in function handleCronjob() of module) when demo mode is enabled
    
    
    public static function getTable()
    {
        return APP_DB_TABLEPREFIX.'SysCMSUsersRoles';
    }
    
     /**
     * additions to the install procedure
     * 
     * @param array $arrPreviousDependenciesModelClasses
     */
    public function install($arrPreviousDependenciesModelClasses = null)
    {
        $bSuccess = true;
        $bSuccess = parent::install($arrPreviousDependenciesModelClasses);

        //==check if at least one user exists, if not, then add it
        $this->newQuery();
        $this->clear();    
        $this->limitOne(); //we need just one record to be returned
        if (!$this->loadFromDB())
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loadFromDB() failed in install()');
            return false;
        }

        //==if no usergroups exists, add a default one
        if($this->count() == 0)
        {         
            //adding admin
            $this->clear();
            $this->newRecord();
            $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_ADMINISTRATORS); //default username            
            $this->setDescription('Has all permissions in the system');    
            $this->setMaxUsersInAccount(-1); //for security reasons don't allow any (extra) users
            $this->setIsSystemRole(true);
            if (!$this->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_ADMINISTRATORS);
                return false;
            }

            //adding account holder for BASIC account
            $this->clear();
            $this->newRecord();
            $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_HUMANS); //default username            
            $this->setDescription('Have less permissions than God'); 
            $this->setMaxUsersInAccount(1);
            $this->setIsSystemRole(false); //--> be careful, because it will be the default account
            if (!$this->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_HUMANS);
                return false;
            }                   
            
            //adding account holder for BASIC account
            // $this->clear();
            // $this->newRecord();
            // $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_BASICACCOUNTADMIN); //default username            
            // $this->setDescription('BASIC Account holder (=my customer)'); 
            // $this->setMaxUsersInAccount(1);
            // $this->setIsSystemRole(false); //--> be careful, because it will be the default account
            // if (!$this->saveToDB())
            // {
            //     logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_BASICACCOUNTADMIN);
            //     return false;
            // }       

            //the role last added, is now the new DEFAULT role
            //update new-user-register-default-groupid setting
            setSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_MEMBERSHIP_NEWUSER_ROLEID, $this->getID(), false);

            //adding account holder for ADVANCED account
            // $this->clear();
            // $this->newRecord();
            // $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_ADVANCEDACCOUNTADMIN); //default username            
            // $this->setDescription('ADVANCED Account holder (=my customer)'); 
            // $this->setMaxUsersInAccount(5);
            // $this->setIsSystemRole(false);
            // if (!$this->saveToDB())
            // {
            //     logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_ADVANCEDACCOUNTADMIN);
            //     return false;
            // }       


            //adding account holder for PRO account
            // $this->clear();
            // $this->newRecord();
            // $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_PROACCOUNTADMIN); //default username            
            // $this->setDescription('PRO Account holder (=my customer)'); 
            // $this->setMaxUsersInAccount(0);            
            // $this->setIsSystemRole(false);
            // if (!$this->saveToDB())
            // {
            //     logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_PROACCOUNTADMIN);
            //     return false;
            // }                

            //adding basic user
            // $this->clear();
            // $this->newRecord();
            // $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_ACCOUNTUSERS); //default username           
            // $this->setDescription('Basic user (customer can create these accounts)');  
            // $this->setMaxUsersInAccount(-1);            
            // $this->setIsSystemRole(false);            
            // if (!$this->saveToDB())
            // {
            //     logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_ACCOUNTUSERS);
            //     return false;
            // }       

            //adding anonymous
            $this->clear();
            $this->newRecord();
            $this->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_ANONYMOUS); //default username           
            $this->setDescription('Unidentified users (users that are not logged in)');  
            $this->setMaxUsersInAccount(-1);//since 0 means unlimited, for security reasons -1
            $this->setIsAnonymous(true);
            $this->setIsSystemRole(true);
            if (!$this->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsersRoles::ROLENAME_DEFAULT_ANONYMOUS);
                return false;
            }                

        }                
        

        return $bSuccess;
    }       
    
  
}
