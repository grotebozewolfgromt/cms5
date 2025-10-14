<?php
/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;

          
    
    
    
    echo '<h2>'.transm(APP_ADMIN_CURRENTMODULE, 'wordlettercounter_title', 'word and letter counter').'</h2>';
    echo transm(APP_ADMIN_CURRENTMODULE, 'wordlettercounter_explanation', 'This tool counts words, letters and characters.');
    echo $objForm->generate()->renderHTMLNode();
    if ($objForm->isFormSubmitted())
    {
        echo '<h2>'.transm(APP_ADMIN_CURRENTMODULE, 'wordlettercounter_result', 'Results').'</h2>';
        echo $sResult;
    }    