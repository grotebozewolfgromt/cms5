<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\classes\models\TSysIPBlackWhitelistAbstract;
use dr\classes\models\TSysModel;


/**
 * @created 16 jan 2020 drenirie
 */

class TSysCMSLoginIPBlackWhitelist extends TSysIPBlackWhitelistAbstract
{
	
	/**
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysCMSLoginIPBlackWhitelist';
	}


	/**
	 * Returns whether the whitelist is enabled or not
	 * A whitelist is not desireable in every situation, for example logging in into a webshop: everybody must be able to log in
	 */
	public function getWhitelistEnabled()
	{
		return APP_CMS_LOGINONLYWHITELISTEDIPS;
	}


	
} 
?>