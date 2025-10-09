<?php
namespace dr\modules\Mod_Dev\models;

use dr\classes\TConfigFilePHPConstants;

/**
 * Description of TConfigFilePagebuilder
 *
 * reading, writing and checking the config file for pagebuilder
 * 
 * 
 *************
 * 03 jan 2025: created
 * 
 * @author dennis renirie
 */

class TConfigFileDev extends TConfigFilePHPConstants
{

	/**
	 * define 2d array with PHP constants
	 * 
	 * layout:
	 * indexed array with on every line: 
	 * name (=key), (default) value, type, description
	 */
	protected function defineConstants()
	{
		return array(
			array('DEV_TEST', '', TP_STRING, 'Just a test config'),
			array('DEV_NAME', '', TP_STRING, 'Just a test config'),
			array('DEV_ISDEFAULT', true, TP_BOOL, 'Just a test config'),
		);
	}

}
