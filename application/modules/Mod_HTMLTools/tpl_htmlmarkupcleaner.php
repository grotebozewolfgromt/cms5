<?php
/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;

          
    
    
    
    echo '<h2>'.transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_title', 'HTML markup cleaner').'</h2>';
    echo transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_explanation', 'Some tools like browser HTML generators and word processors can generate so much poop that interferes with the CSS of a site.<br>This tools cleans the HTML for you.<br>Finally some well deserved rest at night!');
    echo $objForm->generate()->renderHTMLNode();
    if ($objForm->isFormSubmitted())
    {
        echo '<h2>'.transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_result', 'Clean HTML').'</h2>';
        echo $sResult;
    }    