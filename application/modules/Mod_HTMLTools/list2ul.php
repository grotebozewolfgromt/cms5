<?php
/**
 * controller index MODULES
 */
    use dr\classes\dom\TPaginator;
    use dr\classes\dom\FormGenerator;
    use dr\classes\dom\validator\TRequired;
    use dr\classes\dom\tag\Div;
    use dr\classes\dom\tag\form\InputCheckbox;
    use dr\classes\dom\tag\form\InputRadio;
    use dr\classes\dom\tag\form\InputButton;
    use dr\classes\dom\tag\form\InputText;
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
      
    //session started in bootstrap
    include_once '../../bootstrap_admin_auth.php';
 
    
    $objForm = new FormGenerator('htmlgenerator', getURLThisScript());
           
    $sFileNameLastConversion = 'list2ul_lastconversion.txt';
    $sFileContents = '';
    if (file_exists($sFileNameLastConversion))
    {
        $arrFileLines = loadFromFile($sFileNameLastConversion);
        $sFileContents = implode("\n", $arrFileLines);
    }    
//var_dump(substr_count($sFileContents, "\r"));
    
    //input
    $objPlainText = new Textarea();
    $objPlainText->setNameAndID('edtPlainText');
    $objPlainText->setClass('fullwidthtag');   
    $objPlainText->setRequired(true);   
    $objPlainText->setRows(10);  
    $objPlainText->setAutofocus(true);
    $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
    $objPlainText->addValidator($objValidator);    
    if ($objForm->isFormSubmitted())
        $objPlainText->setText($objPlainText->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objPlainText->setText($sFileContents);
    $objForm->add($objPlainText, '', transm(CMS_CURRENTMODULE, 'list2ul_form_field_plaintext', 'Input: plain text (every item on a new line)'));

    //method: new line
    $objRdMethodNL = new InputRadio();
    $objRdMethodNL->setName('rdMethod');
    $objRdMethodNL->setID('rdMethodNL');
    $objRdMethodNL->setValue('newline');
    if ($objForm->isFormSubmitted())
        $objRdMethodNL->setChecked($objRdMethodNL->getValueSubmitted() == 'newline');
    else
        $objRdMethodNL->setChecked(true);
    $objForm->add($objRdMethodNL, '', transm(CMS_CURRENTMODULE, 'plaintext2html_form_field_method_newline', 'every line is a list item'));

    
    //method: bullet points
    $objRdMethodBullet = new InputRadio();
    $objRdMethodBullet->setName('rdMethod');
    $objRdMethodBullet->setID('rdMethodBullet');
    $objRdMethodBullet->setValue('bullet');
    if ($objForm->isFormSubmitted())
        $objRdMethodBullet->setChecked($objRdMethodBullet->getValueSubmitted() == 'bullet');
    else
        $objRdMethodBullet->setChecked(false);    
    $objForm->add($objRdMethodBullet, '', transm(CMS_CURRENTMODULE, 'plaintext2html_form_field_method_bulletppints', 'every bullet point is a list item'));    

    //bullet character
    $objBulletChar = new InputText();
    $objBulletChar->setNameAndID('edtBulletChar'); 
    $objBulletChar->setRequired(true);   
    $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
    $objBulletChar->addValidator($objValidator);    
    if ($objForm->isFormSubmitted())
        $objBulletChar->setValue($objBulletChar->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objBulletChar->setValue('•');
    $objForm->add($objBulletChar, '', transm(CMS_CURRENTMODULE, 'list2ul_form_field_bulletcharacter', 'Bullet character that marks a new item'));
    
    
    
    //submit
    $objSubmit = new InputSubmit();    
    $objSubmit->setValue(transm(CMS_CURRENTMODULE, 'list2ul_form_button_convert', 'convert'));
    $objSubmit->setName('btnSubmit');
    $objForm->add($objSubmit, '');    
    
    //output
    $objHTML = new Textarea();
    if ($objForm->isFormSubmitted())
    {
        saveToFile(explode("\n", $objPlainText->getValueSubmitted()), $sFileNameLastConversion);
        
        //converting baby
        $sResult = '<ul>'."\n";
        
        if ($objRdMethodBullet->getValueSubmitted() == 'newline')
            $arrListItems = explode("\n", $objPlainText->getValueSubmitted());
        if ($objRdMethodBullet->getValueSubmitted() == 'bullet')
            $arrListItems = explode($objBulletChar->getValueSubmitted(), $objPlainText->getValueSubmitted());
        
        foreach ($arrListItems as $sListItem)
        {
            if (strlen($sListItem) > 0) //avoid empty lines
            {
                $sResult .= "\t".'<li>'. trimAll($sListItem).'</li>'."\n";
            }
                
        }
        $sResult .= '</ul>'."\n";        
        
        $objHTML->setNameAndID('edtHTML');
        $objHTML->setClass('fullwidthtag');   
        $objHTML->setReadOnly(true); 
        $objPlainText->setRows(10);   
        $objHTML->setText($sResult);
        $objForm->add($objHTML, '', transm(CMS_CURRENTMODULE, 'list2ul_form_field_html', 'Output: html'));  
        
        
        $objBtnCopyClipboard = new InputButton();
        $objBtnCopyClipboard->setValue(transm(CMS_CURRENTMODULE, 'list2ul_form_button_copytoclipboard', 'Copy HTML to clipboard'));
        $objBtnCopyClipboard->setName('btnCopyToClipboard');
        $objBtnCopyClipboard->setOnclick("copyToClipboardEditBox('edtHTML')");
        $objForm->add($objBtnCopyClipboard, '');            
    }


    
    
    
    
    
    
    
    
    
    
    
    

    //===fill tabsheets array (only if you want tabsheets)
    $arrTabsheets = $objCurrentModule->getTabsheets(); 
    
    
    //============ RENDER de templates
 
    
    
    $sTitle = transm(CMS_CURRENTMODULE, CMS_CURRENTMODULE);
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;
    
    
    $sHTMLContentMain = renderTemplate('tpl_list2ul.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());

    
?>