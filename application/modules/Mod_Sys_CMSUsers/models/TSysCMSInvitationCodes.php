<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\classes\models\TSysModel;
use dr\classes\models\TSysRedemptionCodesAbstract;

/**
 * Invitation codes for cms accounts
 * Only users with a redemption code can create an account
 * 
 * created 4 maart 2022
 * 4 mrt 2022: TCMSInvitationCodes: 
 */

class TSysCMSInvitationCodes extends TSysRedemptionCodesAbstract
{

	/**
	 * the child has to inherit this
	 *
	 * @return string name of database table
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysCMSInvitationCodes';
	}
} 
?>