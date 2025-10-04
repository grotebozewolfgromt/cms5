
<?php 
    if (isset($_GET[GETARRAYKEY_CMSMESSAGE_SUCCESS]))
    {
        echo '<script>';

        if (is_array($_GET[GETARRAYKEY_CMSMESSAGE_SUCCESS]))
        {
            foreach($_GET[GETARRAYKEY_CMSMESSAGE_SUCCESS] as $sMessage);
            {
                ?>sendNotification('<?php echo transcms('notification_title_success', 'Success'); ?>', '<?php echo $sMessage; ?>', 'success'); <?php
            }
        }
        else
        {
            ?>sendNotification('<?php echo transcms('notification_title_success', 'Success'); ?>', '<?php echo $_GET[GETARRAYKEY_CMSMESSAGE_SUCCESS]; ?>', 'success'); <?php
        }

        echo '</script>';
    }

    if (isset($_GET[GETARRAYKEY_CMSMESSAGE_ERROR]))
    {
        echo '<script>';

        if (is_array($_GET[GETARRAYKEY_CMSMESSAGE_ERROR]))
        {
            foreach($_GET[GETARRAYKEY_CMSMESSAGE_ERROR] as $sMessage);
            {
                ?>sendNotification('<?php echo transcms('notification_title_error', 'Error'); ?>', '<?php echo $sMessage; ?>', 'error');<?php
            }
        }
        else
        {
            ?>sendNotification('<?php echo transcms('notification_title_error', 'Error'); ?>', '<?php echo $_GET[GETARRAYKEY_CMSMESSAGE_ERROR]?>', 'error');<?php
        }

        echo '</script>';
    }
    
    if (isset($_GET[GETARRAYKEY_CMSMESSAGE_NOTIFICATION]))
    {
        echo '<script>';

        if (is_array($_GET[GETARRAYKEY_CMSMESSAGE_NOTIFICATION]))
        {
            foreach($_GET[GETARRAYKEY_CMSMESSAGE_NOTIFICATION] as $sMessage);
            {
                ?>sendNotification('<?php echo transcms('notification_title_notification', 'Notification'); ?>', '<?php echo $sMessage; ?>', 'notification');<?php
            }
        }
        else
        {
            ?>sendNotification('<?php echo transcms('notification_title_notification', 'Notification'); ?>', '<?php echo $_GET[GETARRAYKEY_CMSMESSAGE_NOTIFICATION]; ?>', 'notification');<?php
        }

        echo '</script>';    
    }           
    
    //notifications from login controller
    if (isset($objAuthenticationSystem))
    {
        if ($objAuthenticationSystem)
        {
            if ($objAuthenticationSystem->getMessageNormal())
            {
                ?>
                <script>
                    sendNotification('<?php echo transcms('notification_title_notification', 'Notification'); ?>', '<?php echo $objAuthenticationSystem->getMessageNormal(); ?>', 'notification');
                </script>
                <?php
            }    

            if ($objAuthenticationSystem->getMessageError())
            {
                ?>
                <script>
                    sendNotification('<?php echo transcms('notification_title_error', 'Error'); ?>', '<?php echo $objAuthenticationSystem->getMessageError(); ?>', 'error');
                </script>
                <?php
            }            
        }
    }
    


?>
