<?php

    //display: error
    echo '<div style="color:red;font-weight:bolder; display:block;margin-bottom:1em;">'.$sMessageError.'</div>';
    //display: success
    echo '<div style="color:red;font-weight:bolder">'.$sMessageSuccess.'</div>';

    
    if ($objForm) //can be null after submitted or on spam
        echo $objForm->generate()->renderHTMLNode();

?>
