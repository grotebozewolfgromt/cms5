<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\classes\models\TSysUsersHistoryAbstract;

/**
 * Description of TSysCMSUsersSessions
 *
 * @author drenirie
 */
class TSysCMSUsersHistory extends TSysUsersHistoryAbstract
{
     /**
     * for the function defineTable() we need a TSysUsersAbstract instantiated
     * object to define the database tables
     * 
     * @return TSysUsersAbstract user object
     */
    protected function getNewUsersModel()
    {
        return new TSysCMSUsers();
    }

    public static function getTable() 
    {
        return APP_DB_TABLEPREFIX.'SysCMSUsersHistory';
    }


}
