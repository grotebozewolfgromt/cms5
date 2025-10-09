<?php 
/**
 * this email is sent when a user creates an account outside the cms.
 * they tried to create an account with an email address that already exists.
 * Instead of giving an error, we email the user to say that he can recover his password
 */
?>
<h1><?php echo transcms('createaccount_email_emailaddressexists_header', 'An account already exists'); ?></h1>
<?php echo transcms('createaccount_email_emailaddressexists_body_emailaddressalreadyindatabase', 'You (or someone else) tried to create a [applicationname] account for this email address, we didn\'t create the account, because we have already have an account associated with this email address.', 'applicationname', $sApplicationName); ?><br>
<br>
<?php echo transcms('createaccount_email_emailaddressexist_body_clicklinktoactivate', 'If you\'ve lost your password, you can request a password reset by clicking on the link below:'); ?><br>
<br>
<a href="<?php echo $sURLResetPassword ?>"><?php echo transcms('createaccount_email_emailaddressexist_button_resetpassword', 'Reset password'); ?></a><br>
<br>
<?php echo transcms('createaccount_email_emailaddressexist_body_dontwanttoresetpassword', 'If you don\'t want to reset your password, you can ignore this email and log in with your current credentials via the link below:'); ?><br>
<br>
<a href="<?php echo $sURLLogin ?>"><?php echo transcms('createaccount_email_emailaddressexist_button_loginhere', 'Log in here'); ?></a><br>
<br>
<?php echo transcms('createaccount_email_footer_close', 'With kind regards,'); ?><br>
<?php echo transcms('createaccount_email_footer_name', 'The team of [applicationname]', 'applicationname', $sApplicationName); ?><br>

