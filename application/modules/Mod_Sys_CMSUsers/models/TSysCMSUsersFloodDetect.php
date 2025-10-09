<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\classes\models\TSysModel;
use dr\classes\models\TSysUsersFloodDetectAbstract;

/**
 * @created 16 jan 2020 drenirie
 */

class TSysCMSUsersFloodDetect extends TSysUsersFloodDetectAbstract
{
	/**
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysCMSUsersFloodDetect';
	}
	

} 
?>