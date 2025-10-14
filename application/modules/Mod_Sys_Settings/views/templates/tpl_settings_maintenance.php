<?php

    use dr\modules\Mod_Sys_Settings\Mod_Sys_Settings; 
 
    includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js');
?>


<h2><?php echo transcms('settings_cronjob_h2', 'Cron job'); ?>
    <dr-icon-info>
        <?php echo transm(APP_ADMIN_CURRENTMODULE, 'settings_cronjob_iconinfo_text', 'A cronjob performs maintenance on regular basis (once a day is preferred) to keep this system running fast and smooth.<br>Things that happen are for example: emptying caches, deleting old users.<br>It also performs necessary actions that need to happen without user input.<br>For example: checking for intrusion, sending notification emails, sending invoices to clients etc.<br><br>Examples:<br>', 'url', getURLCMSCronjob());?>
        <div style="border:1px; border-style:solid; border-color: #1ea9ffff; background-color: #059fff23; padding: 5px;">
            <b>Linux (execute 5am every day):</b><br>
            crontab -e<br>
            0 5 * * * wget <?php echo getURLCMSCronjob(); ?>
        </div>
        <div style="border:1px; border-style:solid; border-color: #1ea9ffff; background-color: #059fff23; padding: 5px;">
            <b>Windows:</b><br>
            <a href="https://phoenixnap.com/kb/cron-job-windows" target="_blank">Tutorial</a>
        </div>    
    </dr-icon-info>
</h2>

<?php echo transm(APP_ADMIN_CURRENTMODULE, 'settings_cronjob_explanation', 'Point the Linux cronjob manager or Windows task scheduler to url:<br><br><b>[url]</b><br>', 'url', getURLCMSCronjob()); ?>
<?php echo transm(APP_ADMIN_CURRENTMODULE, 'settings_cronjob_executemanuallyexplanation', 'You can trigger the Cronjob also manually with the button below:');?><br>
<?php
    if (auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_MAINTENANCE, Mod_Sys_Settings::PERM_OP_EXECUTE))    
    {
        ?>
            <input type="button" class="button_normal" onclick="openInNewTab('<?php echo getURLCMSCronjob(); ?>')" value="<?php echo transcms('settings_execute_cronjobnow', 'Execute cron job'); ?>" style="width: 200px;">
        <?php
    }
?>
<?php     
    echo transm(APP_ADMIN_CURRENTMODULE, 'settings_cronjob_lastexecution', 'Last execution:').' '.date($objAuthenticationSystem->getUsers()->getDateFormatLong().' '.$objAuthenticationSystem->getUsers()->getTimeFormatLong(), APP_CRONJOB_LASTEXECUTED);
    if (APP_CRONJOB_LASTEXECUTED + DAY_IN_SECS < time())
        echo transm(APP_ADMIN_CURRENTMODULE, 'settings_cronjob_pleaseexecute', '<br>Recommendation: Run CronJob ASAP! The last one ran more than 24 hours ago.');
?>

<h2>
    <?php echo transcms('settings_installer_h2', 'Installer'); ?>
    <dr-icon-info>
        <?php echo transm(APP_ADMIN_CURRENTMODULE, 'settings_installer_explanation', 'The installer installs, updates and removes the installation of [applicationname].<br>To use the installer, it needs to be enabled and you need a password to prevent others from using it.<br><br><b>Disable the installer when you don\'t use it to prevent malicious actors manipulating and removing data from the system.</b>', 'applicationname', APP_APPLICATIONNAME);?>
    </dr-icon-info>
</h2>
<?php 
    if ($bInstallerEnabled)
        $sTransEnabled = transm(APP_ADMIN_CURRENTMODULE, 'settings_installer_enabled_true', 'enabled');
    else
        $sTransEnabled = transm(APP_ADMIN_CURRENTMODULE, 'settings_installer_enabled_false', 'disabled');
    echo transm(APP_ADMIN_CURRENTMODULE, 'settings_installer_enabled', 'Status: [value]', 'value', $sTransEnabled);?><br>
<?php echo transm(APP_ADMIN_CURRENTMODULE, 'settings_installer_password', 'Password: [value]', 'value', APP_INSTALLER_PASSWORD);?><br>
<?php
    //enable/disable
    if (auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_MAINTENANCE, Mod_Sys_Settings::PERM_OP_EXECUTE))    
    {
        if ($bInstallerEnabled)
        {
            ?><input type="button" class="button_normal" onclick="window.location = '<?php echo addVariableToURL(APP_URLTHISSCRIPT, 'enableinstaller', 0); ?>'" value="<?php echo transcms('settings_button_disableinstaller', 'Disable installer'); ?>" style="width: 200px;"><?php
        }
        else
        {
            ?><input type="button" class="button_normal" onclick="window.location = '<?php echo addVariableToURL(APP_URLTHISSCRIPT, 'enableinstaller', 1); ?>'" value="<?php echo transcms('settings_button_enableinstaller', 'Enable installer'); ?>" style="width: 200px;"><?php            
        }
    }

    //run installer
    if (auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_MAINTENANCE, Mod_Sys_Settings::PERM_OP_EXECUTE) && $bInstallerEnabled)    
    {
        ?><input type="button" class="button_normal" onclick="openInNewTab('<?php echo APP_URL_ADMIN_INSTALLERSCRIPT; ?>')" value="<?php echo transcms('settings_execute_installer', 'Start installer'); ?>" style="width: 200px;"><?php
    }

    echo transm(APP_ADMIN_CURRENTMODULE, 'settings_installer_message_disablwhennotuse', 'Disable the installer for security reasons when you are not actively using it.<br>You can always enable it when you actually need it.').'<br>';
?>