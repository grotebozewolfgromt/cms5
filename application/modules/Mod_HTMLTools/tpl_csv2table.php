<?php
/**
 * Overview module: sys_modules
 */
    use dr\classes\models\TSysModel;

          
    
    
    
    echo '<h2>'.transm(APP_ADMIN_CURRENTMODULE, 'csv2table_title', 'CSV to table ').'</h2>';
    echo transm(APP_ADMIN_CURRENTMODULE, 'csv2table_explanation', 'With this tool you can convert plain text CSV (comma separated values) from Excel for example to html table');
    echo $objForm->generate()->renderHTMLNode();
    if ($objForm->isFormSubmitted())
    {
        echo '<h2>'.transm(APP_ADMIN_CURRENTMODULE, 'csv2table_result', 'result in HTML:').'</h2>';
        echo $sResult;
    }    