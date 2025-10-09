<?php
define('CONFIG_DEBUG', false); //Enables developer mode for testing and debugging. WARNING: it reveals sensitive system information and slows down the application. DO NOT ENABLE IN LIVE ENVIROMENT WITH CUSTOMERS
define('CONFIG_TESTINT', 3); //integer test
define('CONFIG_EMAILADMIN', 'email@dexxterclark.com'); //Email address of the administrator for the site. This email address receives notifications about the status of the website
define('CONFIG_FRAMEWORKINSTALLPASSWORD', 'beerput'); //password for running install/upgrade/uninstall scripts like installer/install.php?confirm=123456 . This prevents accidental and unauthorised removal
?>
