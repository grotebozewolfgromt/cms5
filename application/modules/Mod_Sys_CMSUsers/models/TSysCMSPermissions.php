<?php

/** 
 * TSysCMSPermissions created 10 jan 2020
 * 
 * 
 */

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\classes\models\TSysModel;
use dr\classes\models\TSysPermissionsAbstract;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\modules\Mod_Sys_Modules\models\TSysModules;

/**
 * Description of TSysCMSPermissions
 * 
 * the permissions of users in the cms
 *
 * @author drenirie
 */
class TSysCMSPermissions extends TSysPermissionsAbstract
{
    const FIELD_USERROLEID     = 'iUserRoleID';
    
            
    public function getUserRoleID()
    {
        return $this->get(TSysCMSPermissions::FIELD_USERROLEID);
    }
    
    public function setUserRoleID($iID)
    {
        $this->set(TSysCMSPermissions::FIELD_USERROLEID, $iID);
    }

    
    public static function getTable()
    {
        return APP_DB_TABLEPREFIX.'SysCMSPermissions';
    }
     

    /**
     * defines the fields in the tables
     * i.e. types, default values, enum values, referenced tables etc
    */
    public function defineTable()
    {
        parent::defineTable();
                
        //role id		
        $this->setFieldDefaultValue(TSysCMSPermissions::FIELD_USERROLEID, 0);
        $this->setFieldType(TSysCMSPermissions::FIELD_USERROLEID, CT_INTEGER64);
        $this->setFieldLength(TSysCMSPermissions::FIELD_USERROLEID, 0);
        $this->setFieldDecimalPrecision(TSysCMSPermissions::FIELD_USERROLEID, 0);
        $this->setFieldPrimaryKey(TSysCMSPermissions::FIELD_USERROLEID, false);
        $this->setFieldNullable(TSysCMSPermissions::FIELD_USERROLEID, false);
        $this->setFieldEnumValues(TSysCMSPermissions::FIELD_USERROLEID, null);
        $this->setFieldUnique(TSysCMSPermissions::FIELD_USERROLEID, false);
        $this->setFieldIndexed(TSysCMSPermissions::FIELD_USERROLEID, true);
        $this->setFieldFulltext(TSysCMSPermissions::FIELD_USERROLEID, false);
        $this->setFieldForeignKeyClass(TSysCMSPermissions::FIELD_USERROLEID, TSysCMSUsersRoles::class);
        $this->setFieldForeignKeyTable(TSysCMSPermissions::FIELD_USERROLEID, TSysCMSUsersRoles::getTable());
        $this->setFieldForeignKeyField(TSysCMSPermissions::FIELD_USERROLEID, TSysModel::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysCMSPermissions::FIELD_USERROLEID);
        $this->setFieldForeignKeyActionOnUpdate(TSysCMSPermissions::FIELD_USERROLEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysCMSPermissions::FIELD_USERROLEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE); //when user-role is deleted, also permissions are deleted
        $this->setFieldAutoIncrement(TSysCMSPermissions::FIELD_USERROLEID, false);
        $this->setFieldUnsigned(TSysCMSPermissions::FIELD_USERROLEID, true);
        $this->setFieldEncryptionDisabled(TSysCMSPermissions::FIELD_USERROLEID);        
    }
    

    /**
     * erf deze functie over om je eigen checksum te maken voor je tabel.
     * je berekent deze de belangrijkste velden te pakken, wat strings toe te
     * voegen en alles vervolgens de coderen met een hash algoritme
     * zoals met sha1 (geen md5, gezien deze makkelijk te breken is)
     * de checksum mag maar maximaal 50 karakters lang zijn
     * 
     *
     * BELANGRIJK: je mag NOOIT het getID() en getChecksum()-field meenemen in
     * je checksum berekening (id wordt pas toegekend na de save in de database,
     * dus is nog niet bekend ten tijde van het checksum berekenen)
     *
     * @return string
    */
    public function getChecksumUncrypted()
    {
        return 'pdfL_kdl1'.$this->get(TSysPermissionsAbstract::FIELD_RESOURCE).'_qwwd1'.$this->get(TSysPermissionsAbstract::FIELD_RESOURCE).boolToInt($this->getAllowed()).boolToInt($this->getUserRoleID()).'e03-jajaikwileenkoekje';
    }

    /**
     * create permissions for a module
     * for all usergroups
     * these permissions are all set to ALLOWED
     * 
     * @param string $sInternalModuleName (can also be 'system' or something else)     
     * @return bool succes??
     */
    public function createPermissionsForModule($sInternalModuleName)
    {
        /* the uncommented code below works, but is retired because of reusable code is easier to maintain
        //instantiate module class and ask for permissions
        $sTempModClass = getModuleFullNamespaceClass($sInternalModuleName);
        $objCurrentModule = new $sTempModClass; 
        $arrPermissions = $objCurrentModule->getPermissions();
        $arrIndexesPerm = array_keys($arrPermissions);
        $arrOperations = array();

        //EXTRA check if module exists on disk: for security reasons (injection, gain access to certain parts of the cms by inserting stuff into the database)
        $arrTempMods = getModuleFolders();
        if (!in_array($sInternalModuleName, $arrTempMods))
        {
            error_log(__CLASS__.'createPermissionsForModule(): can\'t find module on disk. abort creation of permission for security reasons');
            return false;
        }


        $this->newQuery();
        $this->clear();

        $objUsergroups = new TSysCMSUsersRoles();
        $objUsergroups->limit(0);//get all
        if (!$objUsergroups->loadFromDB())
            return false;

        //loops all groups (that rhimes)
        while($objUsergroups->next())
        {
            //add permission for general access to a module
            $this->newRecord();
            $this->setUserRoleID($objUsergroups->getID());
            $this->setResource(getAuthResourceString($sInternalModuleName, AUTH_CATEGORY_MODULEACCESS, AUTH_OPERATION_MODULEACCESS));
            $this->setAllowed($bDefaultAllowed); 

            //add permissions from the ->getPermissions() array of a module
            //loop categories
            foreach ($arrIndexesPerm as $sCategory)
            {
                //loop operations
                $arrOperations = $arrPermissions[$sCategory];            
                foreach ($arrOperations as $sOperation)
                {
                    $this->newRecord();
                    $this->setUserRoleID($objUsergroups->getID());
                    $this->setResource(getAuthResourceString($sInternalModuleName, $sCategory, $sOperation));
                    $this->setAllowed($bDefaultAllowed);
                }
            }
        }

        return $this->saveToDBAll(true, true);
        */





        $arrModFolders = array();
        $arrModFolders = getModuleFolders();
        $objModBL = null;
        $sModFullClassBL = '';
        $arrPermModBL = array();
        $bModFound = false;

        //for every module:
        //finding permissions that are in the business logic but NOT in the database and add them to database
        foreach ($arrModFolders as $sModBL)
        {
            if ($sInternalModuleName == $sModBL) //extra check to see if the supplied module name exists in the directory (a file exist would be vulnerable to directory)
            {
                $bModFound = true;
                $sModFullClassBL = getModuleFullNamespaceClass($sModBL);
                $objModBL = new $sModFullClassBL;
                $arrPermModBL = $objModBL->getPermissions();

                if (!$this->createPermDBForPermArrBLInternal($sModBL, $arrPermModBL))
                    return false;                
            }

        }   


        return $bModFound;
    }

    /**
     * delete permissions for a module
     * for all userroles
     *
     * @return bool
     */
    public function deletePermissionsForModule($sInternalModuleName)
    {
        //EXTRA check if module exists on disk: for security reasons (injection, directory traversal, gain access to certain parts etc...)
        $arrTempMods = getModuleFolders();//we use getModuleFolders instead of file_exists to prevent directory traversal
        if (!in_array($sInternalModuleName, $arrTempMods))
        {
            error_log(__CLASS__.'deletePermissionsForModule(): can\'t find module on disk. abort creation of permission for security reasons');
            return false;
        }


        $objPerm = new TSysCMSPermissions();
        //$objPerm->findLike(TSysCMSPermissions::FIELD_RESOURCE, $sInternalModuleName.AUTH_RESOURCESEPARATOR.'%'); --> 16-4-2025 replaced by line below
        $objPerm->find(TSysCMSPermissions::FIELD_RESOURCE, $sInternalModuleName.AUTH_RESOURCESEPARATOR.'%', COMPARISON_OPERATOR_LIKE, '', TP_STRING); 

        // $objPerm->deleteFromDB(true);
        if (!$objPerm->deleteFromDB(true))
            return false;        

        return true;
    }

    /**
     * create permissions for a usergroup
     * for all modules
     * these permissions are all set to ALLOWED
     * 
     * @param int $iUsergroupID
     * @param boolean $bDefaultAllowed true = allow all; false = disallow all
     * @return void
     */
    public function createPermissionsForUsergroup($iUsergroupID, $bDefaultAllowed = true)
    {

        $objPerm = new TSysCMSPermissions();
        $arrPermissions = array();
        $objCurrentModule = null;
        $arrIndexesPerm = array();
        $arrOperations = array();

        if (!is_numeric($iUsergroupID)) //prevent injection
            $iUsergroupID = 0;

        //request modules from datbase (those are the ones that are installed)
        $objModsDB = new TSysModules();
        $objModsDB->select(array(TSysModules::FIELD_ID, TSysModules::FIELD_NAMEINTERNAL));
        $objModsDB->limit(0); //all modules
        $objModsDB->loadFromDB();


         //loop all modules in database 
        while ($objModsDB->next())
        {
            //instantiate Module Business Logic class
            $sTempModClass = getModuleFullNamespaceClass($objModsDB->getNameInternal());
            $objCurrentModule = new $sTempModClass; 
            $arrPermissions = $objCurrentModule->getPermissions();
            $arrIndexesPerm = array_keys($arrPermissions);

            //request permission categories
            foreach ($arrIndexesPerm as $sCategory)
            {
                //request operations
                $arrOperations = $arrPermissions[$sCategory];            
                foreach ($arrOperations as $sOperation)
                {
                    //create db record
                    $objPerm->newRecord();
                    $objPerm->setResource(getAuthResourceString($objModsDB->get(TSysModules::FIELD_NAMEINTERNAL),$sCategory, $sOperation));
                    $objPerm->setUserRoleID($iUsergroupID);
                    $objPerm->setAllowed($bDefaultAllowed);
                }
            }
        }

        return $objPerm->saveToDBAll(true, true);
    }

    /**
     * delete all permissions for a certain user group
     *
     * @param int $iUsergroupID
     * @return bool
     */
    public function deletePermissionsForUsergroup($iUsergroupID)
    {
        if (is_numeric($iUsergroupID))
        {
            $objPerm = new TSysCMSPermissions();
            $objPerm->find(TSysCMSPermissions::FIELD_USERROLEID, $iUsergroupID);
            if (!$objPerm->deleteFromDB(true))
                return false;        
        }
        else    
        {
            error_log('deletePermissionsForUsergroup(): usergroupid is not numeric: '.$iUsergroupID);
            return false;
        }

        return true;    
    }    
    

    /**
     * the only time we need to delete permissions for the cms is 
     * when we uninstall the framework.
     * When we uninstall the framework, we delete the database table, 
     * so implementing this method is useless
     */
    //public function deletePermissionsForCMS(){}

    /**
     * update the permissions table
     * for example: you added or deleted permissions to/from the 
     * array of a module in phpcode.
     * These permissions won't add/delete itself automagically to/from
     * the database.
     * This function will take care of that for you
     * 
     * This function:
     * -deletes permissions in database if they are not in de code
     * -adds permissions to database if they are in the code but not in database
     * 
     * 
     * this function is called in the cronjob() function of the CMSUsers module
     *
     * @return bool
     */
    public function updatePermissions()
    {
        $arrModFolders = array();
        $arrModFolders = getModuleFolders();
        $objModBL = null;
        $sModFullClassBL = '';
        $arrPermModBL = array();

        //==== CREATING PERMISSIONS ====
        //for every module:
        //finding permissions that are in the business logic but NOT in the database and add them to database
        foreach ($arrModFolders as $sModBL)
        {
            $sModFullClassBL = getModuleFullNamespaceClass($sModBL);
            $objModBL = new $sModFullClassBL;
            $arrPermModBL = $objModBL->getPermissions();

            if ($this->createPermDBForPermArrBLInternal($sModBL, $arrPermModBL))
                logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'created permissions in database for module '.$sModBL.' (if they didn\'t exist already)<br>');
            else
                logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__,'creating permissions in database for module '.$sModBL.' FAILED!!<br>');

        }      
        
        //adding permissions that exists in CMS
        if ($this->createPermDBForPermArrBLInternal(AUTH_MODULE_CMS, getPermissionsCMS()))
            logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'created permissions in database for CMS (if they didn\'t exist already)<br>');
        else
            logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'creating permissions in database for CMS FAILED!!<br>');        


        //==== DELETING PERMISSIONS ====
        //finding permissions that are in the database but not in the business and delete them from database
        //for every database permissions, check if exists in modules or cms, if not, delete from db
        $arrResource = array();
        $objPermDB = new TSysCMSPermissions();
        $objPermTempDelDB = new TSysCMSPermissions(); //we use a differen object to delete db entries
        $objPermDB->limit(0);//all permissions
        $objPermDB->loadFromDB();
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Looking for outdated permissions ...<br>');
        while($objPermDB->next())
        {
            //for modules
            foreach ($arrModFolders as $sModBL)
            {
                $arrResource = getAuthResourceArray($objPermDB->getResource());            

                if (
                    ($arrResource['operation'] != AUTH_OPERATION_MODULEACCESS) &&
                    ($arrResource['module'] == $sModBL)
                    ) //is right module? don't look for the moduleaccess thingy --> that doesn't exist in the mod->getPermissions()
                {
                    $sModFullClassBL = getModuleFullNamespaceClass($sModBL);
                    $objModBL = new $sModFullClassBL;
                    $arrPermModBL = $objModBL->getPermissions();
     
                    if (!$this->isInPermissionsArrBL($arrPermModBL, $arrResource))
                    {
                        error_log('DELETING PERMISSION for module '.$sModBL.': '.$objPermDB->getResource().' because it is in the database, but isn\'t returned by '.$sModBL.'->getPermissions() (anymore)');
                        $objPermTempDelDB->clear();
                        $objPermTempDelDB->findID($objPermDB->getID());
                        $objPermTempDelDB->deleteFromDB(true);
                        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'deleted permission(s) for module '.$sModBL.'<br>');
                    }
                    else
                    {
                        // echo 'found permission '.$sModBL.' for '.$objPermDB->getResource().' for usergroupid '.$objPermDB->getUserRoleID().'<br>';                        
                    }   
                }
            }            

            //for CMS
            $arrResource = getAuthResourceArray($objPermDB->getResource());            
            if ($arrResource['module'] == AUTH_MODULE_CMS)
            {
                $arrPermModBL = getPermissionsCMS();
                if (!$this->isInPermissionsArrBL($arrPermModBL, $arrResource))
                {
                    error_log('DELETING PERMISSION for CMS: '.$objPermDB->getResource().' because it is in the database, but isn\'t returned by getPermissionsCMS() (anymore)');
                    $objPermTempDelDB->clear();
                    $objPermTempDelDB->findID($objPermDB->getID());
                    $objPermTempDelDB->deleteFromDB(true);
                    logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'deleted permission(s) for CMS<br>');
                }
                else
                {
                    // echo 'found permission CMS for '.$objPermDB->getResource().' for usergroupid '.$objPermDB->getUserRoleID().'<br>';                        
                }
            }
        }

        unset($objPermDB);
        unset($objPermTempDelDB);

        return true;
    }


 



    /**
     * create permissions in database for the supplied permissions-array from business logic of a module/cms
     * (helper function for internal use)
     * this function checks if permission already exists and adds it if not
     * this applies to all roles
     * 
     * @param string $sModule this is the module-part of the auth() resource-string, so this can also be CMS!
     * @param array $arrPermBL business logic array with permissions (from CMS or module)
     */
    private function createPermDBForPermArrBLInternal($sModule, $arrPermBL)
    {
        $bDefaultAllowed = false;
        $arrPermModBLKeys = array();
        $arrPermOpBL = array();
        $objUsergroups = new TSysCMSUsersRoles();

        $objUsergroups->limit(0);//no limit
        $objUsergroups->loadFromDB();
        

    
        
        $arrPermModBLKeys = array_keys($arrPermBL);


 
        //==== add permission for general access to a module
        if ($sModule != AUTH_MODULE_CMS) //only for modules, not cms
        {
            $objUsergroups->resetRecordPointer();  

            while($objUsergroups->next()) //looping usergroups (=userroles)
            {                
                $bDefaultAllowed = false;

                //allow default = true on system roles, but not on anonymous user
                if ($objUsergroups->getIsSystemRole())
                    $bDefaultAllowed = true;
                
                //allow default = false on anonymous roles (anonymous roles are also system roles, so explicitly making it false)
                if ($objUsergroups->getIsAnonymous())
                    $bDefaultAllowed = false;

                if (!$this->addPermToDBIfNotExistsInternal($sModule, AUTH_CATEGORY_MODULEACCESS, AUTH_OPERATION_MODULEACCESS, $objUsergroups->getID(), $bDefaultAllowed))
                    return false;
            }
        }



        //==== looping all permission categories of a module
        foreach ($arrPermModBLKeys as $sCatPermBL)
        {
            $arrPermOpBL = $arrPermBL[$sCatPermBL];            

            //looping the operations in a category
            foreach ($arrPermOpBL as $sOpBL)
            {
                $objUsergroups->resetRecordPointer();

                //looping usergroups
                while($objUsergroups->next())
                {
                    $bDefaultAllowed = false;

                    //allow default = true on system roles, but not on anonymous user
                    if ($objUsergroups->getIsSystemRole())
                        $bDefaultAllowed = true;
                    
                    //allow default = false on anonymous roles (anonymous roles are also system roles, so explicitly making it false)
                    if ($objUsergroups->getIsAnonymous())
                        $bDefaultAllowed = false;
    
                    if (!$this->addPermToDBIfNotExistsInternal($sModule, $sCatPermBL, $sOpBL, $objUsergroups->getID(), $bDefaultAllowed))
                        return false;
                }
            }
        }        

        
        unset($objUsergroups);
        return true;
    }

    /**
     * function used by createPermDBForPermArrBLInternal
     * to add permissions to database if they don't exist
     */
    private function addPermToDBIfNotExistsInternal($sModule, $sCategory, $sOperation, $iUsergroupID, $bDefaultAllowed = true)
    {
        $sResource = '';
        $sResource = getAuthResourceString($sModule, $sCategory, $sOperation);

        $objPermDB = new TSysCMSPermissions();
        $objPermDB->select(array(TSysModel::FIELD_ID));
        $objPermDB->find(TSysCMSPermissions::FIELD_RESOURCE, $sResource);
        $objPermDB->find(TSysCMSPermissions::FIELD_USERROLEID, $iUsergroupID);
        if (!$objPermDB->loadFromDB())
            return false;

        //if not exists in database, add it
        if ($objPermDB->count() == 0)
        {           
            $objPermDB->clear();
            $objPermDB->setResource($sResource);
            $objPermDB->setUserRoleID($iUsergroupID);
            $objPermDB->setAllowed($bDefaultAllowed);
            if (!$objPermDB->saveToDB())
                return false;
        }

        return true;
    }  

    /**
     * sets permission in database
     */
    private function setPermissionInDB($sModule, $sCategory, $sOperation, $iUsergroupID, $bAllowed = true)
    {
        $sResource = '';
        $sResource = getAuthResourceString($sModule, $sCategory, $sOperation);

        $objPermDB = new TSysCMSPermissions();
        $objPermDB->find(TSysCMSPermissions::FIELD_RESOURCE, $sResource);
        $objPermDB->find(TSysCMSPermissions::FIELD_USERROLEID, $iUsergroupID);
        if (!$objPermDB->loadFromDB())
            return false;

        //if not exists in database, add it
        if ($objPermDB->count() == 0)
           $objPermDB->newRecord();
 
        $objPermDB->setResource($sResource);
        $objPermDB->setUserRoleID($iUsergroupID);
        $objPermDB->setAllowed($bAllowed);
        if (!$objPermDB->saveToDB())
            return false;
        
        return true;
    }  



    /**
     * Does a  authorise resource exist in Business Logic permissions array from module or cms
     * @param array $arrPermBL the array from mod->getPermissions() and getPermissionsCMS()
     * @param array $arrResourceDB the resource from the database exploded into an array with getAuthResourceArray()
     */
    private function isInPermissionsArrBL(&$arrPermBL, &$arrResourceDB)
    {
        $arrPermModBLKeys = array();
        $arrPermModBLKeys = array_keys($arrPermBL);

        foreach ($arrPermModBLKeys as $arrPermModBLKey)
        {
            if ($arrPermModBLKey == $arrResourceDB['category'])
            {
                $arrCatContent = $arrPermBL[$arrPermModBLKey]; //category contents of a permission
                foreach ($arrCatContent as $sPermBL)
                {
                    if ($arrResourceDB['operation']  == $sPermBL)
                        return true;
                }
            }
        }

        return false;
    }


    /**
     * set the permissions for the demo role by looking at the modules
     * called in Mod_Sys_CMSUsers
     */
    public function setPermissionsDemoMode(TSysCMSUsersRoles $objDemoRole)
    {
        //declare+ init
        $bSuccess = true;
        $arrPermBL = array();
        $objModules = null;
        $objModBL = null;
        $bAtLeastOneOperationAllowed = false; //if a module has no operations allowed, I deny module access


        //=== loop all modules
        $objModules = new TSysModules();
        $objModules->loadFromDB();
        while ($objModules->next())
        {
            $bAtLeastOneOperationAllowed = false;
            $sModFullClassBL = getModuleFullNamespaceClass($objModules->getNameInternal());
            $objModBL = new $sModFullClassBL;
            $arrPermBL = $objModBL->getPermissionsDemoModeAllowed();

            //==== looping all permission categories (cat) of a module
            $arrPermModBLKeys = array_keys($arrPermBL);
            foreach ($arrPermModBLKeys as $sCatPermBL)
            {
                $arrPermOpBL = $arrPermBL[$sCatPermBL];            

                //==== looping the operations (op) in a category
                foreach ($arrPermOpBL as $sOpBL)
                {
                    $bAtLeastOneOperationAllowed = true;
                    if (!$this->setPermissionInDB($objModules->getNameInternal(), $sCatPermBL, $sOpBL, $objDemoRole->getID(), true))
                        $bSuccess = false;
                } //end: operations loop
            } //end: category loop   

            //module access itself
            if (!$this->setPermissionInDB($objModules->getNameInternal(), AUTH_CATEGORY_MODULEACCESS, AUTH_OPERATION_MODULEACCESS, $objDemoRole->getID(), $bAtLeastOneOperationAllowed))
                $bSuccess = false;            
        }   //end: module loop

        return $bSuccess;
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

        if (!$this->createPermDBForPermArrBLInternal(AUTH_MODULE_CMS, getPermissionsCMS()))
            return false;

        return $bSuccess;
    }
 
}
