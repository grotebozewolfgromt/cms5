
           
<h1><?php echo $sTitle ?></h1>
<?php
    //can be null if password email is sent
    if ($objFormPasswordRecover) //it can be null because of too many login attempts
    {
        echo transcms('passwordrecover_message_enterpassword', 'Please enter your new password').'<br>';
        echo '<br>';

        echo $objFormPasswordRecover->generate()->renderHTMLNode();
    }
    else
    {
        if ($objAuthenticationSystem->getMessageNormal() != '')
            echo $objAuthenticationSystem->getMessageNormal().'<br>';
        if ($objAuthenticationSystem->getMessageError() != '')
            echo $objAuthenticationSystem->getMessageError().'<br>';        
    }    
?>
<a href="<?php echo $sURLBackToLogin; ?>"><?php echo transcms('passwordrecover_link_backtologin', 'Back to login page'); ?></a>

   



