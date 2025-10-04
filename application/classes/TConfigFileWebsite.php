<?php
namespace dr\classes;

/**
 * Description of TConfigFile
 *
 * reading, writing and checking the config file for a website
 * 
 * 
 * for the config file of the framework:
 * Every host has a different config file
 * This way it is easy to copy the whole framework to another server to work on it without changing config files
 * especially useful in for development server vs deployment server.
 * When distributing the framework to clients, you can just delete de development config file, 
 * instead of having to dive into the config file and change all the values

 *************
 * 18 nov 2024: created
 * 
 * @author dennis renirie
 */

class TConfigFileWebsite extends TConfigFilePHPConstants
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
			array('WEBSITE_PATH_DOMAIN', 'localhost', TP_STRING, 'Almost the same as APP_PATH_WWW, but it only contains the domain name. This is separate from APP_PATH_WWW because sometimes you only need the domain and its faster to define it instead of figuring it out wasting CPU cycles'),
			array('WEBSITE_PATH_LOCAL', '', TP_STRING, 'Root path CMS WITHOUT trailing slash (/) or backslash (\)'),			
			array('WEBSITE_PATH_WWW', 'http://localhost', TP_STRING, 'Root URL WITHOUT trailing slash (/)'),			
			array('WEBSITE_DB_SITEID', 1, TP_INTEGER, 'The ID of the current host in the database (table: websites). By default ID=1 is assumed because its the first website that is created'),
			array('WEBSITE_THEME', 'default', TP_STRING, 'The directory name of the website theme in themes directory'),
		);
	}

}
