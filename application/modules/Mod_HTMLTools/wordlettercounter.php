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
    include_once '../../bootstrap_cms_auth.php';
 
    
    $objForm = new FormGenerator('wordlettercounter', getURLThisScript());
    
    $sFileNameLastConversion = 'wordlettercounter_lastcount.txt';
    $sFileContents = '';
    if (file_exists($sFileNameLastConversion))
    {
        $arrFileLines = loadFromFile($sFileNameLastConversion);
        $sFileContents = implode("\n", $arrFileLines);
    }       
    
    //input
    $objPlainText = new Textarea();
    $objPlainText->setNameAndID('edtPlainText');
    $objPlainText->setClass('fullwidthtag');   
    $objPlainText->setRequired(true);   
    $objPlainText->setAutofocus(true);
    $objPlainText->setRows(10);  
    $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
    $objPlainText->addValidator($objValidator);
    if ($objForm->isFormSubmitted())
        $objPlainText->setText($objPlainText->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW));
    else
        $objPlainText->setText($sFileContents);
    $objForm->add($objPlainText, '', transm(CMS_CURRENTMODULE, 'wordlettercounter_form_field_plaintext', 'Input: text (html is allowed but filtered for count)'));

        
    
    //submit
    $objSubmit = new InputSubmit();    
    $objSubmit->setValue(transm(CMS_CURRENTMODULE, 'wordlettercounter_form_button_count', 'count'));
    $objSubmit->setName('btnSubmit');
    $objForm->add($objSubmit, '');    
    
    //output
    $sResult = '';
    if ($objForm->isFormSubmitted())
    {
        saveToFile(explode("\n", $objPlainText->getValueSubmitted()), $sFileNameLastConversion);
        
        $sContent = $objPlainText->getValueSubmitted();
        $sContent = strip_tags($sContent);
        $sContent = str_replace('.', ' ', $sContent);
        $sContent = str_replace('?', ' ', $sContent);
        $sContent = str_replace('\'', ' ', $sContent);
        $sContent = str_replace('"', ' ', $sContent);
        $sContent = str_replace("\n", ' ', $sContent);
        $sContent = str_replace("\r", '', $sContent);
        $sContent = str_replace("\t", ' ', $sContent);
        $sContent = str_replace("(", '', $sContent);
        $sContent = str_replace(")", '', $sContent);
        $sContent = str_replace("-", '', $sContent);
        $sContent = str_replace("+", '', $sContent);
        $sContent = str_replace("=", '', $sContent);
        $sContent = str_replace("@", '', $sContent);
        $sContent = str_replace("!", '', $sContent);
        $sContent = str_replace("%", '', $sContent);
        $sContent = str_replace("*", '', $sContent);
        $sContent = str_replace("/", '', $sContent);
        $sContent = str_replace("|", '', $sContent);
        $sContent = str_replace("'", '', $sContent);
        $sContent = str_replace("`", '', $sContent);
        $sContent = str_replace('  ', ' ', $sContent);
//        $sContent = filterBadCharsWhiteList($sContent, REGEX_ALPHABETICAL.REGEX_NUMERIC.' ');
        $arrWords = explode(' ', $sContent);        

        //prevent empty array items (sometimes the last item is empty)
        $iCountWords = 0;
        foreach($arrWords as $sWord)
        {
            if (strlen($sWord) > 0)
                $iCountWords++;
        }

        $sResult.= 'Words: '.$iCountWords.'<br>'; 
        $sResult.= 'Characters: '.strlen($sContent).'<br>';
        
        $iLetters = strlen($sContent);
        $iLetters -= substr_count($sContent, ' ');
        $sResult.= 'Letters: '.$iLetters.' (chars - reading symbols)<br>';
        
        $arrLines = explode("\n", $objPlainText->getValueSubmitted());
        $sResult.= 'Lines: '.count($arrLines).' <br>';
        $sResult.= '<br>';
        $sResult.= 'processed:<br>'.$sContent.'';
        
    }


    
    
    
    
    
    
    
    
    
    
    
    

    //===fill tabsheets array (only if you want tabsheets)
    $arrTabsheets = $objCurrentModule->getTabsheets(); 
    
    
    //============ RENDER de templates
 
    
    
    $sTitle = transm(CMS_CURRENTMODULE, CMS_CURRENTMODULE);
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;
    
    
    $sHTMLContentMain = renderTemplate('tpl_wordlettercounter.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());

    
?>