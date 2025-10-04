
<h1><?php echo $sTitle ?></h1>
<?php 
    //success messages in box
    if ($objAuthenticationSystem->getMessageNormal())
    {
        echo $objAuthenticationSystem->getMessageNormal();
    }                                    


    if ($objFormCreateAccount) //it can be null because of too many login attempts or form submitted
    {
        echo transcms('createaccount_message_entercredentials', 'Enter your credentials to be associated with your account').'<br>';
        echo '<br>';

        echo $objFormCreateAccount->generate()->renderHTMLNode();
    }

?><br>
<a href="<?php echo $sURLBackToLogin; ?>"><?php echo transcms('createaccount_link_backtologin', 'Back to login page'); ?></a>

