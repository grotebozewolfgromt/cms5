    
<h1><?php echo $sTitle ?></h1>
<?php
    if ($objFormPasswordRecover) //it can be null because of too many login attempts or form submitted
    {
        echo transcms('passwordrecover_message_enteremailaccount', 'Enter the emailaddress that is associated with your account').'<br>';
        echo transcms('passwordrecover_message_enteremailinstructrions', 'We\'ll email you instructions on how to reset your password').'<br>';
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
?><br>
<a href="<?php echo $sURLBackToLogin; ?>"><?php echo transcms('passwordrecover_link_backtologin', 'Back to login page'); ?></a>
       



