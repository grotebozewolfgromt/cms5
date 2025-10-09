        
<h1><?php echo $sTitle ?></h1>
<?php
    if ($objAuthenticationSystem->getMessageNormal())
    {
        echo $objAuthenticationSystem->getMessageNormal();
    }
    if ($objAuthenticationSystem->getMessageError())
    {
        echo $objAuthenticationSystem->getMessageError();
    }   

?><br>

<a href="<?php echo $sURLBackToLogin; ?>"><?php echo transcms('createaccount_link_backtologin', 'Back to login page'); ?></a>




