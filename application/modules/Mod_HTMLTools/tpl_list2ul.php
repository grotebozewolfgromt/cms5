<?php
/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;

    
    echo '<h2>'.transm(CMS_CURRENTMODULE, 'list2ul_title', 'plain text list to html ul list').'</h2>';
    echo transm(CMS_CURRENTMODULE, 'list2ul_explanation', 'With this tool you can convert plain text list to a html list with the &lt;ul&gt; and &lt;li&gt; tags.<br>Each line is one item in the list');
    echo $objForm->generate()->renderHTMLNode();
    if ($objForm->isFormSubmitted())
    {
        echo '<h2>'.transm(CMS_CURRENTMODULE, 'list2ul_result', 'result in HTML:').'</h2>';
        echo $sResult;
    }