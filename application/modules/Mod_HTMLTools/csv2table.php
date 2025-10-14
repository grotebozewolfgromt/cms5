<?php
/**
 * controller index MODULES
 */
    use dr\classes\dom\TPaginator;
    use dr\classes\dom\FormGenerator;
    use dr\classes\dom\validator\TRequired;
    use dr\classes\dom\tag\Div;
    use dr\classes\dom\tag\form\InputCheckbox;
    use dr\classes\dom\tag\form\InputButton;
    use dr\classes\dom\tag\form\InputText;
    use dr\classes\dom\tag\form\InputRadio;
    use dr\classes\dom\tag\form\Form;
    use dr\classes\dom\tag\Text;
    use dr\classes\dom\tag\form\Textarea;
    use dr\classes\dom\tag\form\InputSubmit;
    use dr\classes\dom\tag\form\Select;
    use dr\classes\dom\tag\form\Option;

    use dr\modules\Mod_Sys_Modules\models\TSysModules;
    use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
    use dr\classes\models\TSysModel;
    use dr\classes\controllers\TCRUDListController;
use dr\classes\dom\tag\form\FormInputAbstract;

    //session started in bootstrap
    include_once '../../bootstrap_admin_auth.php';
 
    
    $objForm = new FormGenerator('htmlgenerator', getURLThisScript());
       
    $sFileNameLastConversion = 'csv2table_lastconversion.txt';
    $sFileContents = '';
    if (file_exists($sFileNameLastConversion))
    {
        $arrFileLines = loadFromFile($sFileNameLastConversion);
        $sFileContents = implode("\n", $arrFileLines);
    }
    
    //input
    $objEdtCSV = new Textarea();

    $objEdtCSV->setNameAndID('edtCSV');
    $objEdtCSV->setClass('fullwidthtag');   
    $objEdtCSV->setRequired(true);   
    $objEdtCSV->setAutofocus(true);
    $objEdtCSV->setRows(10);  
    $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
    $objEdtCSV->addValidator($objValidator);
    if ($objForm->isFormSubmitted())
        $objEdtCSV->setText($objEdtCSV->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objEdtCSV->setText($sFileContents);
    $objForm->add($objEdtCSV, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_plaintext', 'Input: CSV'));
    
    //use custom value separator
    $objUseCustomSeparator = new InputRadio();
    $objUseCustomSeparator->setName('separatorvalueradiogroup');
    $objUseCustomSeparator->setID('rdUseCustomValueSeparator');
    $objUseCustomSeparator->setValue('customvalue');
    if ($objForm->isFormSubmitted())
        $objUseCustomSeparator->setChecked($objUseCustomSeparator->getValueSubmitted() == 'customvalue');
    else
        $objUseCustomSeparator->setChecked(false);    
    $objForm->add($objUseCustomSeparator, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_usecustomseparator', 'Use custom separator'));
    
    
    //the content of the value separator
    $objCustomValueSeparator = new InputText();
    $objCustomValueSeparator->setNameAndID('edtValueSeparator');  
    if ($objForm->isFormSubmitted())
        $objCustomValueSeparator->setValue($objCustomValueSeparator->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objCustomValueSeparator->setValue(',');
    $objForm->add($objCustomValueSeparator, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_valueseparator', 'Value separator'));
       
    //use enter-sepator
    $objUseNewlineSeparator = new InputRadio();
    $objUseNewlineSeparator->setName('separatorvalueradiogroup');
    $objUseNewlineSeparator->setID('rdUseNewlineValueSeparator');
    $objUseNewlineSeparator->setValue('newlinevalue');
    if ($objForm->isFormSubmitted())
        $objUseNewlineSeparator->setChecked($objUseNewlineSeparator->getValueSubmitted() == 'newlinevalue');
    else
        $objUseNewlineSeparator->setChecked(false);    
    $objForm->add($objUseNewlineSeparator, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_useenterseparator', 'Use NewLine-character separator (\\n)'));    
    
        //how many colums in table (the enter character separates not only rows, but also columns
    $objNoCols = new InputText();
    $objNoCols->setNameAndID('edtNoCols');   
    if ($objForm->isFormSubmitted())
        $objNoCols->setValue($objNoCols->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objNoCols->setValue('2');
    $objForm->add($objNoCols, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_numberorfolumns', 'Number of columns in table (when enter character is also used as column separator)'));
    
    
    //use tab-sepator
    $objUseTabSeparator = new InputRadio();
    $objUseTabSeparator->setName('separatorvalueradiogroup');
    $objUseTabSeparator->setID('rdUseTabValueSeparator');
    $objUseTabSeparator->setValue('tabvalue');
    if ($objForm->isFormSubmitted())
        $objUseTabSeparator->setChecked($objUseTabSeparator->getValueSubmitted() == 'tabvalue');
    else
        $objUseTabSeparator->setChecked(true);    
    $objForm->add($objUseTabSeparator, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_usetabseparator', 'Use tab-character separator (\\t)'));       
    
    
    //first line is header
    $objChkFirstLineHeader = new InputCheckbox();
    $objChkFirstLineHeader->setNameAndID('chkFirstLineHeader');
    if ($objForm->isFormSubmitted())
        $objChkFirstLineHeader->setChecked($objChkFirstLineHeader->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objChkFirstLineHeader->setChecked(false);
    $objForm->add($objChkFirstLineHeader, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_firstlineistableheader', 'First line is table header (horizontal)'));

    //first value is header
    $objChkFirstValueHeader = new InputCheckbox();
    $objChkFirstValueHeader->setNameAndID('chkFirstColHeader');
    if ($objForm->isFormSubmitted())
        $objChkFirstValueHeader->setChecked($objChkFirstValueHeader->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objChkFirstValueHeader->setChecked(false);
    $objForm->add($objChkFirstValueHeader, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_firstvalueistableheader', 'First value is table header (vertical)'));
    
        
    
    //determine separator
    $sValueSeparator = "\n"; //default
   
    
    if ($objUseCustomSeparator->getValueSubmitted() == 'customvalue')
        $sValueSeparator = $objCustomValueSeparator->getValueSubmitted();        
    if ($objUseNewlineSeparator->getValueSubmitted() == 'newlinevalue')
        $sValueSeparator = "\n";    
    if ($objUseTabSeparator->getValueSubmitted() == 'tabvalue')
        $sValueSeparator = "\t";    
    
    //submit
    $objSubmit = new InputSubmit();    
    $objSubmit->setValue(transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_button_convert', 'convert'));
    $objSubmit->setName('btnSubmit');
    $objForm->add($objSubmit, '');    




    
    
    //output
    $objHTML = new Textarea();
    $arrLines = array();
    if ($objForm->isFormSubmitted())
    {
        saveToFile(explode("\n", $objEdtCSV->getValueSubmitted()), $sFileNameLastConversion);        
        
        //becasue the newline character separates both columns and rows, we need to separate the cols from the rows
        if ($sValueSeparator == "\n") 
        {
            $arrTempVals = explode("\n", $objEdtCSV->getValueSubmitted());            
            $iTempCounter = 0;
            $sTempLine = '';
            
            for ($iCounter = 0;$iCounter < count($arrTempVals); $iCounter++)
            {
                $sTempLine.= $arrTempVals[$iCounter];
                if (($iCounter % $objNoCols->getContentsSubmitted(Form::METHOD_POST)->getValueAsInt()) != 0)
                {
                    
                    $arrLines[] = $sTempLine;
                    $sTempLine = '';
                }       
                else
                    $sTempLine.= "\n"; //only add if other values will follow (otherwise we have a \n too much, which in turn results in an extra column later)
            }
            
            unset($arrTempVals);
            unset($sTempVal);
        }
        else        
            $arrLines = explode("\n", $objEdtCSV->getValueSubmitted());
        
        $sResult = '<table>'."\n";
        $bFirstLine = $objChkFirstLineHeader->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool();
        foreach($arrLines as $sLine)
        {
            if (strlen(trimAll($sLine)) > 0) //prevent empty lines
            {
                $arrValuesOnLine = explode($sValueSeparator, $sLine);
                
                if ($bFirstLine)
                {
                    $sResult.= "\t".'<thead>'."\n";  
                    $sResult.= "\t\t".'<tr>'."\n"."\t\t";                        
                    foreach($arrValuesOnLine as $sValue)
                    {
                        $sResult.= "\t".'<th>'.trimAll($sValue).'</th>';            
                    }
                    $sResult.= "\n\t\t".'</tr>'."\n";    
                    $sResult.= "\t".'</thead>'."\n";  
                    $sResult.= "\t".'<tbody>'."\n";  

                    $bFirstLine = false;
                }
                else
                {                  
                    $sResult.= "\t\t".'<tr>'."\n"."\t";                        
                    for ($iCounter = 0;$iCounter < count($arrValuesOnLine); $iCounter++)
                    {
                        //is first value on line AND is header
                        if (
                                (($iCounter % $objNoCols->getContentsSubmitted(Form::METHOD_POST)->getValueAsInt()) == 0) 
                                && 
                                ($objChkFirstValueHeader->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool())
                            )
                        {
                            $sResult.= "\t\t".'<th>'.trimAll($arrValuesOnLine[$iCounter]).'</th>';  
                        }
                        else
                            $sResult.= "\t\t".'<td>'.trimAll($arrValuesOnLine[$iCounter]).'</td>';            
                    }
                    $sResult.= "\n\t\t".'</tr>'."\n";    
                               
                }

                 
            }
        }
        $sResult.= "\t".'</tbody>'."\n"; 
        $sResult.= '</table>'."\n";
        $objHTML->setNameAndID('edtHTML');
        $objHTML->setClass('fullwidthtag');   
        $objHTML->setReadOnly(true); 
        $objEdtCSV->setRows(10);   
        $objHTML->setText($sResult);
        $objForm->add($objHTML, '', transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_field_html', 'Output: html'));  
        
        
        $objBtnCopyClipboard = new InputButton();
        $objBtnCopyClipboard->setValue(transm(APP_ADMIN_CURRENTMODULE, 'csv2table_form_button_copytoclipboard', 'Copy HTML to clipboard'));
        $objBtnCopyClipboard->setName('btnCopyToClipboard');
        $objBtnCopyClipboard->setOnclick("copyToClipboardEditBox('edtHTML')");
        $objForm->add($objBtnCopyClipboard, '');   
        
        
    }


    
    
    
    
    
    
    
    
    
    
    
    

    //===fill tabsheets array (only if you want tabsheets)
    $arrTabsheets = $objCurrentModule->getTabsheets(); 
    
    
    //============ RENDER de templates
 
    
    
    $sTitle = transm(APP_ADMIN_CURRENTMODULE, APP_ADMIN_CURRENTMODULE);
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;
    
    
    $sHTMLContentMain = renderTemplate('tpl_csv2table.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());

    
?>