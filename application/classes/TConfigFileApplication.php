<?php
namespace dr\classes;

/**
 * Description of TConfigFile
 *
 * reading, writing and checking the config file for the framework
 * 
 * 
 * for the config files:
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

class TConfigFileApplication extends TConfigFilePHPConstants
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
			array('APP_DEBUGMODE', false, TP_BOOL, 'Enables developer mode for testing and debugging. WARNING: it reveals sensitive system information and slows down the application. DO NOT ENABLE IN LIVE DEPLOYMENT ENVIROMENT WITH CUSTOMERS!!!'),
			array('APP_DEMOMODE', false, TP_BOOL, 'The demo mode restricts access to certain parts of the CMS and websites to show potential users its functionality. The cronjob will set and reset sample data. To enable demo mode: set value to true and run cronjob'),
			array('APP_MAINTENANCEMODE', false, TP_BOOL, '//Disables application when system being worked on, like updating'),
			array('APP_PEPPER', '', TP_STRING, 'NEEDS TO CHANGE ON EVERY SYSTEM INSTALL!!! NEEDS TO BE DIFFERENT FOR EVERY SYSTEM for security reasons!!! Used for peppering passwords and checksums (as opposed to salting a password) - the longer, the safer. Don\t change when system is already installed, you will invalidate all passwords and checksums!'),
			array('APP_CRONJOBID', '', TP_STRING, 'NEEDS TO CHANGE ON EVERY SYSTEM INSTALL!!! NEEDS TO BE DIFFERENT FOR EVERY SYSTEM for security reasons!!! Prevents dos attacks on the system by executing many cronjobs (everybody knows the url once the know the system). The cronjob id needs to be supplied as parameter in order to execute the cron job'),
			array('APP_CRONJOB_LASTEXECUTED', 0, TP_INTEGER, 'Timestamp of the last execution of the cronjob'),			
			array('APP_CRONJOB_ISEXECUTING', false, TP_BOOL, 'Is CronJob currently being executed? true=executing. Prevents concurrency problems when multiple cronjobs being started when 1 is busy'),
			array('APP_INSTALLER_PASSWORD', '', TP_STRING, 'NEEDS TO CHANGE ON EVERY SYSTEM INSTALL!!! NEEDS TO BE DIFFERENT FOR EVERY SYSTEM for security reasons!!! Password for running install/upgrade/uninstall scripts like installer/install.php. This prevents accidental and unauthorised removal when the installer is not removed. This password is stored in plain text for a reason, because you need to be able to humanly read it and input it in the install scripts'),
			array('APP_INSTALLER_PASSWORD_TRIES', 0, TP_INTEGER, 'The amount of times that the installpassword is tried by user. When it exceeds 3, the installer blocks completely, until you lower this number. This to prevent brute-force cracking the install password. By design, it only blocks the installer, not the CMS or website (otherwise a hacker can block the whole system just by inputting 3 false passwords)'),
			array('APP_INSTALLER_ENABLED', true, TP_BOOL, 'You can only use the installer when it is enabled.'),
			array('APP_CACHE_CLEARONRUN', false, TP_BOOL, 'Sometimes its annoying when you deal with cached files (especially when developing). With this option you can clear the cache on every run so you dont have to do it manually every time'),			
			array('APP_DB_HOST', 'localhost', TP_STRING, 'Host server of database'),
			array('APP_DB_USER', 'root', TP_STRING, 'Username of database user. Default = root or admin, but everybody knows it\'s root or admin, so change it when you use default.'),
			array('APP_DB_PASSWORD', 'root', TP_STRING, 'Password of database user. Default = root or admin, but everybody knows it\'s root or admin, so change it when you use default.'),
			array('APP_DB_DATABASE', 'cms5', TP_STRING, 'Schema/database to use on database server'),
			array('APP_DB_TABLEPREFIX', 'tbl', TP_STRING, 'Database table prefix. This allows you do install multiple frameworks in 1 database when you change the table prefix'),
			array('APP_DB_CONNECTIONCLASS', 'dr\classes\db\TDBConnectionMySQL', TP_STRING, 'Class responsible for handling database connections. Every database brand has its own class (mysql, ms sql, postgresql). Default for MySQL: dr\classes\db\TDBConnectionMySQL'),
			array('APP_DB_PORT', 3306, TP_INTEGER, 'Port on which the database server is reachable. Default for MySQL/MariaDB is 3306 or 3307'),
			array('APP_GOOGLE_RECAPTCHAV2_SITEKEY', '', TP_STRING, 'Googles recaptchaV2 SiteKey. Copy from Google Recaptcha site console'),
			array('APP_GOOGLE_RECAPTCHAV2_SECRETKEY', '', TP_STRING, 'Googles recaptchaV2 SecretKey. Copy from Google Recaptcha site console'),
			array('APP_GOOGLE_RECAPTCHAV2_ENABLE', false, TP_BOOL, 'Use recaptchaV2?'),
			array('APP_GOOGLE_RECAPTCHAV3_SITEKEY', '', TP_STRING, 'Googles recaptchaV3 SiteKey. Copy from Google Recaptcha site console'),
			array('APP_GOOGLE_RECAPTCHAV3_SECRETKEY', '', TP_STRING, 'Googles recaptchaV3 SecretKey. Copy from Google Recaptcha site console'),
			array('APP_GOOGLE_RECAPTCHAV3_ENABLE', false, TP_BOOL, 'Use recaptchaV3?'),			
			array('APP_GOOGLEAPI_KEY', '', TP_STRING, 'Google API key, used to connecting to Google services'),			
			array('APP_GOOGLEAPI_CLIENTID', '', TP_STRING, 'Google Client ID for Google API'),			
			array('APP_GOOGLEAPI_CLIENTSECRET', '', TP_STRING, 'Google Client Secret for Google API'),			
			array('APP_GOOGLEAPI_SCOPES', '', TP_STRING, 'CSV (comma separated). Client scopes (what do you want to get access to as application). '),			
			array('APP_EMAIL_ADMIN', '', TP_STRING, 'Administrator email address that receives notifications from websites'),			
			array('APP_ISHTTPS', true, TP_BOOL, 'Is the website HTTPS (=true) or HTTP (=false)?'),
			array('APP_PATH_DOMAIN_CMS', 'localhost', TP_STRING, 'Almost the same as APP_URL_CMS, but it only contains the domain name. This is separate from APP_PATH_WWW because sometimes you only need the domain and its faster to define it instead of figuring it out wasting CPU cycles'),
			array('APP_URL_CMS', '', TP_STRING, 'Local path of the CMS without trailing slash (/). This is stored in the framework config file, because it is the same for all connected websites'),			
			array('APP_PATH_UPLOADS', '', TP_STRING, 'Local path of all user uploads without trailing slash (/) or backslash (\). This is the centralized place where all the user uploads are stored for all websites like a CDN (because we can only upload files to ONE server for multiple sites). THIS IS CASE SENSIVE ALSO ON WINDOWS!!! DRIVE LETTERS ARE UPPERCASE!'),			
			array('APP_URL_UPLOADS', '', TP_STRING, 'URL where all uploads are stored without trailing slash (/). This is the centralized place where all the user uploads are stored for all websites like a CDN (because we can only upload files to ONE server for multiple sites)'),			
			array('APP_CMS_MODULESDIR', 'modules', TP_STRING, 'Name of the modules directory'),			
			array('APP_CMS_APPLICATIONNAME', 'Archimedes CMS', TP_STRING, 'Name of application that the CMS respresents'),			
			array('APP_VERSION', '5.0.0.0', TP_STRING, 'Version number of the framework'),			
			array('APP_CMS_ANYONECANREGISTERACCOUNT', false, TP_BOOL, 'this option SUPERSEDES the option in the settings screen. It can be switched off here for security reasons: When you would get into the database you could check this and do EVERYTHING with the whole system (because you can add a user).'),			
			array('APP_CMS_ENABLESIGNINWITHGOOGLE', false, TP_BOOL, 'Allow Google login for CMS'),			
			array('APP_CMS_SHOWWEBSITESINNAVIGATION', true, TP_BOOL, 'enable or disable the visibility websites in header and menu on left. This disabled makes it a webapp instead of a CMS'),			
			array('APP_CMS_SAVESUCCESSNOTIFICATION', true, TP_BOOL, 'show notification when record saved successfully'),			
			array('APP_CMS_FIRSTPAGECONTROLLER', 'loginform', TP_STRING, 'The name of the controller that represents the first page of the CMS. When you type in the URL it needs to go to the first page of the CMS.'),			
			array('APP_CMS_RATELIMITER_ENABLE', true, TP_BOOL, 'CMS rate limiter: enabled or not'),			
			array('APP_CMS_RATELIMITER_REQUESTS', 28800, TP_INTEGER, 'CMS rate limiter: [X] requests allowed per [Y] seconds. This is the X (Y: see setting below). Default: 1 request per second for 8 hours per ip address: 60*60*8=28800'),			
			array('APP_CMS_RATELIMITER_PERSECS', 86400, TP_INTEGER, 'CMS rate limiter: [X] requests allowed per [Y] seconds. This is the Y (X: see setting above). Default: 8 hours usage per day'),			
			array('APP_CMS_LOGINONLYWHITELISTEDIPS', true, TP_BOOL, 'Login only allowed by whitelisted IP addresses. Default is true because it is the most secure'),			
			array('APP_CMS_STEALTHMODEBLACKWHITELISTEDIPS', true, TP_BOOL, 'In stealth mode: all blacklisted and non-whitelisted IP addresses are served 404 errors instead of 401 errors. This prevents automated directory enumeration. Default is true because it is the most secure, but you might think that your website is down when you try to login from a non-whitelisted or blacklisted IP address'),
			array('APP_CMS_THEME', 'default', TP_STRING, 'The directory name of the CMS theme in themes directory. THIS IS CASE SENSIVE ALSO ON WINDOWS!!! DRIVE LETTERS ARE UPPERCASE!'),
			array('APP_PATH_WEBSITE_VIEWS_THEMES', '', TP_STRING, 'Local path of all website-themes without trailing slash (/) or backslash (\). This is the centralized place where all website-themes are stored for all websites like a CDN (because we can only upload files to ONE server for multiple sites)'),			
			array('APP_URL_WEBSITE_VIEWS_THEMES', '', TP_STRING, 'URL where all website-themes are stored without trailing slash (/). This is the centralized place where all the user website-themes are stored for all websites like a CDN (because we can only upload files to ONE server for multiple sites)'),			
			array('APP_CMS_PRINTING_ZPL_ENABLE', false, TP_BOOL, 'Enable printing for zebra network printers'),			
			array('APP_CMS_PRINTING_ZPL_PRINTER1_NAME', 'First printer', TP_STRING, 'The name of the first printer so the user can recognize it'),			
			array('APP_CMS_PRINTING_ZPL_PRINTER1_IPADDRESS', 'localhost', TP_STRING, 'The IP address of the first Zebra network printer'),			
			array('APP_CMS_PRINTING_ESCPOS_ENABLE', false, TP_BOOL, 'Enable printing for ESC/POS network printers'),			
			array('APP_CMS_PRINTING_ESCPOS_PRINTER1_NAME', 'First printer', TP_STRING, 'The name of the first printer so the user can recognize it'),			
			array('APP_CMS_PRINTING_ESCPOS_PRINTER1_IPADDRESS', 'localhost', TP_STRING, 'The IP address of the first ESC/POS network printer'),			
			array('APP_CMS_PRINTING_ESCPOS_PRINTER1_PORT', 9100, TP_INTEGER, 'The tcp/ip port of the first ESC/POS network printer'),			
			array('APP_CMS_IMAGE_RESIZE_MAX_WIDTHPX', 3840, TP_INTEGER, 'Resolution (width) for automatic resizing an image to maximum-size format'),			
			array('APP_CMS_IMAGE_RESIZE_MAX_HEIGHTPX', 3840, TP_INTEGER, 'Resolution (height) for automatic resizing an image to maximum-size format'),			
			array('APP_CMS_IMAGE_RESIZE_LARGE_WIDTHPX', 1024, TP_INTEGER, 'Resolution (width) for automatic resizing an image to large-size format'),			
			array('APP_CMS_IMAGE_RESIZE_LARGE_HEIGHTPX', 1024, TP_INTEGER, 'Resolution (height) for automatic resizing an image to large-size format'),			
			array('APP_CMS_IMAGE_RESIZE_MEDIUM_WIDTHPX', 500, TP_INTEGER, 'Resolution (width) for automatic resizing an image to medium-size format'),			
			array('APP_CMS_IMAGE_RESIZE_MEDIUM_HEIGHTPX', 500, TP_INTEGER, 'Resolution (height) for automatic resizing an image to medium-size format'),			
			array('APP_CMS_IMAGE_RESIZE_THUMBNAIL_WIDTHPX', 150, TP_INTEGER, 'Resolution (width) for automatic resizing an image to thumbnail format'),			
			array('APP_CMS_IMAGE_RESIZE_THUMBNAIL_HEIGHTPX', 150, TP_INTEGER, 'Resolution (height) for automatic resizing an image to thumbnail format'),			
			array('APP_DATAPROTECTION_CONTACTS_SEARCHFIELDS', '', TP_STRING, 'Which encrypted database fields of contacts table should be included in unencrypted search field. Values separated by comma'),
			array('APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS', 3650, TP_INTEGER, 'Anonymize data after how many days of last contact.'),
			array('APP_DATAPROTECTION_CONTACTS_ENCRYPT_LASTNAME', true, TP_BOOL, 'Encrypt table column last name?'),
			array('APP_DATAPROTECTION_CONTACTS_ENCRYPT_ADDRESS', true, TP_BOOL, 'Encrypt table columns with address data?'),
			array('APP_DATAPROTECTION_CONTACTS_ENCRYPT_POSTALZIP', true, TP_BOOL, 'Encrypt table columns with postal /zip codes?'),
			array('APP_DATAPROTECTION_CONTACTS_ENCRYPT_PHONENUMBER', true, TP_BOOL, 'Encrypt table columns with phone numbers?'),
			array('APP_DATAPROTECTION_CONTACTS_ENCRYPT_EMAILADDRESS', true, TP_BOOL, 'Encrypt table columns with email addresses?'),
			array('APP_FORMS_LIMITSHOWSUBMIT_SECS', 2, TP_INTEGER, 'The time between when forms show and when they can be submitted. 2=user needs to wait at least 2 seconds. Prevents rapid form spamming.'),
			array('APP_FORMS_LIMITSUBMITS_SECS', 5, TP_INTEGER, 'The time between when forms are submitted. 5=user needs to wait at least 5 seconds between submitting forms. Prevents rapid form spamming.'),
		);
	}

}
