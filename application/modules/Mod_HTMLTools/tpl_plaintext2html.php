<?php
/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;

          
    
    
    
    echo '<h2>'.transm(CMS_CURRENTMODULE, 'plaintext2html_title', 'plain text to html').'</h2>';
    echo transm(CMS_CURRENTMODULE, 'plaintext2html_explanation', 'With this tool you can convert plain text to html layout');
    echo $objForm->generate()->renderHTMLNode();
    if ($objForm->isFormSubmitted())
    {
        echo '<h2>'.transm(CMS_CURRENTMODULE, 'list2ul_result', 'result in HTML:').'</h2>';
        echo $sResult;
        
        echo '<h2>html markup clan</h2>';
        echo cleanHTMLMarkup($sResult);
    }    