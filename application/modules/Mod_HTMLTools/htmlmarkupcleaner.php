<?php
/**
 * controller index MODULES
 */
    use dr\classes\dom\TPaginator;
    use dr\classes\dom\FormGenerator;
    use dr\classes\dom\validator\Required;
    use dr\classes\dom\tag\Div;
    use dr\classes\dom\tag\form\InputRadio;
    use dr\classes\dom\tag\form\InputCheckbox;
    use dr\classes\dom\tag\form\InputButton;
    use dr\classes\dom\tag\form\InputHidden;
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
    include_once '../../bootstrap_cms_auth.php';
 
    
    $objForm = new FormGenerator('htmlmarkupcleaner', getURLThisScript());
    
    $sFileNameLastConversionRawHtml = 'htmlmarkupcleaner_lastrawhtml.txt';
    $sFileNameLastConversionRichtextHtml = 'htmlmarkupcleaner_lastrichtexthtml.txt';
    $sFileContentsRawHtml = '';
    $sFileContentsRichtextHtml = '';
    if (file_exists($sFileNameLastConversionRawHtml))
    {
        $arrFileLinesRawHtml = loadFromFile($sFileNameLastConversionRawHtml);
        $sFileContentsRawHtml = implode("\n", $arrFileLinesRawHtml);
    }       
    if (file_exists($sFileNameLastConversionRichtextHtml))
    {
        //switched off: we need a clear input, otherwise it pastes in-the-style-of the previous input
       // $arrFileLinesRichtextHtml = loadFromFile($sFileNameLastConversionRichtextHtml);
       // $sFileContentsRichtextHtml = implode("\n", $arrFileLinesRichtextHtml);
    }       
    
    
    //use raw html?
    $objUseRawHtml = new InputRadio();
    $objUseRawHtml->setName('use-raw-or-richtext-html');
    $objUseRawHtml->setID('rdUseRawHTML');
    $objUseRawHtml->setValue('rawhtml');
    if ($objForm->isFormSubmitted())
        $objUseRawHtml->setChecked($objUseRawHtml->getValueSubmitted() == 'rawhtml');
    else
        $objUseRawHtml->setChecked(true);    
    $objForm->add($objUseRawHtml, '', transm(CMS_CURRENTMODULE, 'csv2table_form_field_userawhtml', 'Use raw html'));
    
    
    //input raw HTML
    $objHTMLRawDirty = new Textarea();
    $objHTMLRawDirty->setNameAndID('edtHTMLRawDirty');
    $objHTMLRawDirty->setClass('fullwidthtag');   
    $objHTMLRawDirty->setRequired(true);   
    $objHTMLRawDirty->setAutofocus(true);
    $objHTMLRawDirty->setRows(10);  
    if ($objForm->isFormSubmitted())
        $objHTMLRawDirty->setText($objHTMLRawDirty->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objHTMLRawDirty->setText($sFileContentsRawHtml);
    $objForm->add($objHTMLRawDirty);

    
    //use richtext html?
    $objUseRichtextHtml = new InputRadio();
    $objUseRichtextHtml->setName('use-raw-or-richtext-html');
    $objUseRichtextHtml->setID('rdUseRichtextwHTML');
    $objUseRichtextHtml->setValue('richtexthtml');
    if ($objForm->isFormSubmitted())
        $objUseRichtextHtml->setChecked($objUseRichtextHtml->getValueSubmitted() == 'richtexthtml');
    else
        $objUseRichtextHtml->setChecked(false);    
    $objForm->add($objUseRichtextHtml, '', transm(CMS_CURRENTMODULE, 'csv2table_form_field_userichtexthtml', 'Use rich text html (copy paste from Word, LibreOffice etc)'));    
    
    //input rich text (1 div + 1 hidden)
    $objHTMLRichTextHidden = new InputHidden();//hidden to store html value of the richtexthtml for submission
    $objHTMLRichTextHidden->setNameAndID('edtHTMLRichTextHidden');     
    $objForm->add($objHTMLRichTextHidden);
    
    $objHTMLRichText = new Div();
    $objHTMLRichText->setContentEditable(true);
    $objHTMLRichText->setNameAndID('edtHTMLRichTextDirty');
    $objHTMLRichText->setStyle('height:7em; width:100%; border:solid; border-width:1px; border-color=black; overflow: scroll;');
    if ($objForm->isFormSubmitted())
        $objHTMLRichText->addText($objHTMLRichTextHidden->getValueSubmitted(), false);
    else
        $objHTMLRichText->addText($sFileContentsRichtextHtml, false);    
    $objForm->add($objHTMLRichText);


    
    //format HTML?
    $objFormatHTML = new InputCheckbox();
    $objFormatHTML->setNameAndID('chkFormatHTML');
    if ($objForm->isFormSubmitted())
        $objFormatHTML->setChecked($objFormatHTML->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objFormatHTML->setChecked(true);    
    $objForm->add($objFormatHTML, '', transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_field_formathtml', 'Format HTML code (human readable)'));    
       
    
    //filter unwanted tags and attributes
    $objFilterUnwantedTagsAttributes = new InputCheckbox();
    $objFilterUnwantedTagsAttributes->setNameAndID('chkFilterUnwantedTagsAttributes');
    if ($objForm->isFormSubmitted())
        $objFilterUnwantedTagsAttributes->setChecked($objFilterUnwantedTagsAttributes->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objFilterUnwantedTagsAttributes->setChecked(true);    
    $objForm->add($objFilterUnwantedTagsAttributes, '', transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_field_filterunwantedtagsattributes', 'Filter bad HTML markup tags and attributes'));        
    
    
    //links target="_blank"
    $objLinksTargetBlank = new InputCheckbox();
    $objLinksTargetBlank->setNameAndID('chkLinksTargetBlank');
    if ($objForm->isFormSubmitted())
        $objLinksTargetBlank->setChecked($objLinksTargetBlank->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objLinksTargetBlank->setChecked(true);    
    $objForm->add($objLinksTargetBlank, '', transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_field_linkstargetblank', 'Links: target="_blank"'));        
    
    
    //links rel=nofollow
    $objLinksRelNofollow = new InputCheckbox();
    $objLinksRelNofollow->setNameAndID('chkLinksRelNofollow');
    if ($objForm->isFormSubmitted())
        $objLinksRelNofollow->setChecked($objLinksRelNofollow->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objLinksRelNofollow->setChecked(false);    
    $objForm->add($objLinksRelNofollow, '', transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_field_relnofollow', 'Links: rel="nofollow"'));        
    
    
    //replace </p> by <br>
    $objReplacePByBR = new InputCheckbox();
    $objReplacePByBR->setNameAndID('chkReplacePByBR');
    if ($objForm->isFormSubmitted())
        $objReplacePByBR->setChecked($objReplacePByBR->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool());
    else
        $objReplacePByBR->setChecked(true);    
    $objForm->add($objReplacePByBR, '', transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_field_replacepbybr', 'Replace <p> by <br> tag'));        
    
    
    //submit
    $objSubmit = new InputSubmit();    
    $objSubmit->setValue(transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_button_count', 'Clean my dirty HTML'));
    $objSubmit->setName('btnSubmit');
    $objForm->add($objSubmit, '');    
    
    //copy data from div to hidden form field
    $objForm->getForm()->setOnsubmit("return copyContentEditableToHidden('".$objHTMLRichText->getID()."','".$objHTMLRichTextHidden->getID()."');");
    
    
    //output
    $objHTMLClean = new Textarea();
    if ($objForm->isFormSubmitted())
    {
        saveToFile(explode("\n", $objHTMLRawDirty->getValueSubmitted()), $sFileNameLastConversionRawHtml);
        saveToFile(explode("\n", $objHTMLRichTextHidden->getValueSubmitted()), $sFileNameLastConversionRichtextHtml);

        $sResult = '';
        if ($objUseRawHtml->getValueSubmitted() == 'rawhtml')
            $sResult = $objHTMLRawDirty->getValueSubmitted();
        if ($objUseRichtextHtml->getValueSubmitted() == 'richtexthtml')
            $sResult = $objHTMLRichTextHidden->getValueSubmitted();

        
        $sResult = cleanHTMLMarkup(
                $sResult, 
                $objFilterUnwantedTagsAttributes->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool(), 
                $objFormatHTML->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool(),
                $objReplacePByBR->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool(),
                $objLinksTargetBlank->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool(),
                $objLinksRelNofollow->getContentsSubmitted(Form::METHOD_POST)->getValueAsBool()
                
                );
        $objHTMLClean->setNameAndID('edtHTMLClean');
        $objHTMLClean->setClass('fullwidthtag');   
        $objHTMLClean->setReadOnly(true); 
        $objHTMLRawDirty->setRows(10);   
        $objHTMLClean->setText($sResult);
        $objForm->add($objHTMLClean, '', transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_field_html', 'Output: html'));  
        
        
        $objBtnCopyClipboard = new InputButton();
        $objBtnCopyClipboard->setValue(transm(CMS_CURRENTMODULE, 'htmlmarkupcleaner_form_button_copytoclipboard', 'Copy clean HTML to clipboard'));
        $objBtnCopyClipboard->setName('btnCopyToClipboard');
        $objBtnCopyClipboard->setOnclick("copyToClipboardEditBox('edtHTMLClean')");
        $objForm->add($objBtnCopyClipboard, '');   
        
        
    }


    
    
    
    
    
    
    
    
    
    
    
    

    //===fill tabsheets array (only if you want tabsheets)
    $arrTabsheets = $objCurrentModule->getTabsheets(); 
    
    
    //============ RENDER de templates
 
    
    
    $sTitle = transm(CMS_CURRENTMODULE, CMS_CURRENTMODULE);
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;
    
    
    $sHTMLContentMain = renderTemplate('tpl_htmlmarkupcleaner.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());

    
?>