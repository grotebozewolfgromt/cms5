<?php
namespace dr\modules\Mod_PageBuilder\models;

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

class TConfigFilePagebuilder extends TConfigFilePHPConstants
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
			array('PAGEBUILDER_TEST', '', TP_STRING, 'Just a test config'),
			array('PAGEBUILDER_NAME', '', TP_STRING, 'Just a test config'),
			array('PAGEBUILDER_ISDEFAULT', true, TP_BOOL, 'Just a test config'),
		);
	}

}
