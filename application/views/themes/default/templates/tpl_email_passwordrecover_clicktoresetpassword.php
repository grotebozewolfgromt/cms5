<?php 
/**
 * this email is sent when a user wants to reset a password
 * in the email is a link that leads back to the cms to choose another password
 */
?>
<h1><?php echo transcms('passwordrecover_email_clicktoreset_header_yourequestedpassword', 'Forgot your password?'); ?></h1>
<?php echo transcms('passwordrecover_email_clicktoreset_body_requestedfor', 'We got a request to change the [applicationname] password for user with username: [username].', 'username', $sUserName, 'applicationname', $sApplicationName); ?><br>
<?php echo transcms('passwordrecover_email_clicktoreset_body_clicklinktoreset', 'Click the link below to reset your password.'); ?><br>
<?php echo transcms('passwordrecover_email_clicktoreset_body_linkvalid60minutes', 'This link is only valid for 60 minutes after this email is sent.'); ?><br>
<br>
<a href="<?php echo $sURLPasswordRecover ?>"><?php echo transcms('passwordrecover_email_clicktoreset_button_reset', 'Reset my password now'); ?></a><br>
<br>
<?php echo transcms('passwordrecover_email_clicktoreset_body_dontwanttochange', 'If you don\'t want to change your password, you can ignore this email'); ?><br>
<br>
<?php echo transcms('passwordrecover_email_clicktoreset_body_cantchangeifnotactivated', 'If you didn\'t activate your account yet, you need to activate it first before your can change your password.'); ?><br>
<br>
<?php echo transcms('passwordrecover_email_footer_close', 'With kind regards,'); ?><br>
<?php echo transcms('passwordrecover_email_footer_name', 'The team of [applicationname]', 'applicationname', $sApplicationName); ?><br>


