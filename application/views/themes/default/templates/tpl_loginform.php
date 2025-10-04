<?php 
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

?>            


     
<h1><?php echo $sTitle ?></h1>
<?php 
    if (APP_DEMOMODE)
    {
        echo '<div id="logindemomessage">';
        echo '<b>'.transcms('loginform_demo_message_thisisdemo','This is a demo version.').'</b><br>';
        echo transcms('loginform_demo_message_youcanloginwith','You can login with:').'<br>';
        echo transcms('loginform_demo_message_username','User name: [username]', 'username', TSysCMSUsers::USERNAME_DEFAULT_DEMO).'<br>';
        echo transcms('loginform_demo_message_password','Password: [password]', 'password', TSysCMSUsers::PASSWORD_DEFAULT_DEMO).'<br>';
        echo '</div>';
    }
?>
<?php
    if ($objFormLogin) //it can be null because of too many login attempts
    {
        echo $objFormLogin->generate()->renderHTMLNode();
    }
    else
        echo transg('loginform_logindisabled', 'Login disabled');

    if ($objAuthenticationSystem->getUseSigninWithGoogle())
        echo '<a href="'.$objAuthenticationSystem->getURLGoogleAuth().'">'.transcms('loginform_signinwithgoogle_button','Sign In with Google').'</a>';

    if ($bShowPasswordRecoverLink)
        echo '<div id="loginlostpassword"><a href="'.$objAuthenticationSystem->getURLPasswordRecoverEnterEmail().'">'.transcms('loginform_lostpassword_link','I forgot my password').'</a></div>';

    if ($bShowCreateAccountLink)                            
        echo '<div id="logincreateaccount"><a href="'.$objAuthenticationSystem->getURLCreateAccountEnterCredentials().'">'.transcms('loginform_createaccount_link','Create a new account').'</a></div>';

?>

<div id="logincookiesettings">
    <a href="#" data-cc="c-settings"><?php echo transcms('loginform_cookiesettings_link','Cookie settings')?></a>
</div>
<div id="logindarkmode">                    
    <a href="#" onclick="event.preventDefault(); toggleDarkLightMode()"><?php echo transcms('loginform_darkmode', 'Dark/light Mode');?></a>                    
</div>
<div id="serviceavailable">
    <?php
        echo transcms('login_serviceonlyavailable_countries', 'This service is available in:');
        echo '<br>';
        while ($objPermCountries->next())
        {
            if (!$objPermCountries->isFirstRecord())
                echo ', ';                                
            echo $objPermCountries->get(TSysCountries::FIELD_COUNTRYNAME, TSysCountries::getTable());
        }
    ?>
<div>

 
                
