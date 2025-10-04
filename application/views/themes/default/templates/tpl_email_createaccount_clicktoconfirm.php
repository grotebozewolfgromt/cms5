<?php 
/**
 * this email is sent when a user creates an account outside the cms.
 * in the email is a link that leads back to the cms to activate the account
 */
?>
<h1><?php echo transcms('createaccount_email_clicktoactivate_header_confirmaccount', 'Activate your account'); ?></h1>
<?php echo transcms('createaccount_email_clicktoactivate_body_pleaseconfirm', 'Please activate your [applicationname] account for username: [username].', 'username', $sUserName, 'applicationname', $sApplicationName); ?><br>
<?php echo transcms('createaccount_email_clicktoactivate_body_clicklinktoactivate', 'In order to be able to log in, click on the link below.'); ?><br>
<?php echo transcms('createaccount_email_clicktoactivate_body_linkvalid60minutes', 'This link is only valid for 60 minutes after this email is sent.'); ?><br>
<br>
<a href="<?php echo $sURLCreateAccountEmailConfirm ?>"><?php echo transcms('createaccount_email_clicktoactivate_button_activate', 'Activate account'); ?></a><br>
<br>
<?php echo transcms('createaccount_email_clicktoactivate_body_dontwanttoactivate', 'If you don\'t want to activate the account, you can ignore this email, your account will be automatically deleted.'); ?><br>
<br>
<?php echo transcms('createaccount_email_footer_close', 'With kind regards,'); ?><br>
<?php echo transcms('createaccount_email_footer_name', 'The team of [applicationname]', 'applicationname', $sApplicationName); ?><br>

