<?php
/* 
 * Installation file
 * Run this file to install the framework
 * 
 * created 20-11-2024
 */
include_once('header.php');
?>

<h1>Archimedes installer for <?php echo $_SERVER['SERVER_NAME']; ?></h1>
Welcome to the installer that installs, updates or uninstalls Archimedes.<br>
This installer is in English, once you've installed Archimedes, you are able to choose a preferred language.<br>

<h2>Install</h2>
Install Archimedes on your webserver.<br>
<br>
After installation you will be asked to remove directory 'installer' to prevent unwanted individuals re-running the installation process or uninstalling the application.<br>
<br>
<br>
<a href="install-step1.php" class="ahrefbutton">Install</a><br>
<br>
<h2>Update</h2>
When new updates come out, most updates will be automatically done via Archimedes when logged in.<br>
Sometimes when updates get more severe, you need to use this function.<br>
Only use this function when explicitly instructed to do so!<br>
<br>
<br>
<a href="update.php" class="ahrefbutton">Update</a><br>
<br>
<br>
<h2>Uninstall</h2>
Removing ALL data from Archimedes and uninstall the software.<br>
<br>
<b>WARNING:</b> By clicking the button below ALL data is removed, this action can not be undone.<br>
<br>
<br>
<a href="uninstall.php" class="ahrefbutton">Uninstall</a>

<?php  include_once('footer.php'); ?>