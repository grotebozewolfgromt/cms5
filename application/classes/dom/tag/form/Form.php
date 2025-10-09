<?php
namespace dr\classes\dom\tag\form;

use dr\classes\patterns\TObjectList;
use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\dom\tag\HTMLTag;

/**
 * additional classes for generating a HTML form
 *
 * Let op: alle input types zijn aparte klassen die van input (=abstract) overerven
 *
 * Met deze klasse kun je de verstuurde waardes met dezelfde klassen/objecten ook weer opvangen:
 *     $objText = new InputText();
    $objText->setName('text');
    $objText->setValue(100);
    echo 'waarde na submitten:'.$objText->getValueSubmitted();
    $objForm->appendChild($objText);
 *
 * 
 * 3 juli 2012: input_button toegevoegd
 * 
 * 4 juli 2012: FORMINPUT: required 
 * 4 juli 2012: FORMINPUT: xmlnode: required, readonly en disabled toegevoegd
 * 
 * 5 juli 2012: setMethod() parameter aangepast
 * 25 okt 2012: select,text, textarea: het  onchange event toegevoegd
 * 
 * 3 mrt 2014: input: renderHTMLNodeSpecificToInputType werd niet uitgevoerd, dit is verholpen 
 * 3 mrt 2014: FORMINPUT: $bShowValuesOnReloadForm toegevoegd
 * 18 aug 2014: FORMINPUT: html5 types toegevoegd
 * 2 apr 2015: form: constanten post en get toegevoegd die voor setMethod() gebruikt worden
 * 
 * @author Dennis Renirie
 *
 * TODO: validators toevoegen: check op file (of file meegegeven), check file-extensie, check op maximale grootte bestand
 * TODO: bij getContentsSubmitted alle bestandseigenschappen op kunnen vragen en opslaan op schijf
 * TODO: bij het maken van een tabel met inputboxen, labels maken voor alle input zaken (ervoor) en checbox/radiobox erachter
 * TODO: checked voor radio en checkboxen kunnen setten
 * TODO: maxlength attribuut voor edtboxen text en password
 * TODO: getContents setten van waardes werkt niet voor de niet-type=input tags (option, select en textarea)
 * TODO: function names corresponderen met html-tag-attributen de prefix setAtt geven
 */



/**
 * <form></form>
 *
     $objForm = new form();
    $objForm->setAction('index.php');
    $objForm->setMethod();
 * *
 */
class Form extends HTMLTag
{
    private $sAction = '';
    private $sMethod = 'post'; //post = invisible; get = visible in url
    private $sEnctype = ''; //for sending files
    private $bAutocomplete = true; //html autocomplete = on
    private $sOnSubmit = '';
    
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';

    const ENCTYPE_MULTIPART_FORMDATA = 'multipart/form-data';

    public function __construct($objParentNode = null)
    {
        parent::__construct($objParentNode);
        $this->setTagName('form');
    }

    /**
     * setting action
     * @param string $sAction
     */
    public function setAction($sAction)
    {
        $this->sAction = $sAction;
    }

    /**
     * get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->sAction;
    }

    /**
     * @param bool
     */
    public function setAutocomplete($bAutocomplete)
    {
        $this->bAutocomplete = $bAutocomplete;
    }
    
    /**
     * 
     * @return bool
     */
    public function getAutocomplete()
    {
        return $this->bAutocomplete;
    }
   
    /**
     * setting form method property
     * @param int $bMethodPost true=post, false=get
     */
    public function setMethod($sMethod = form::METHOD_POST)
    {
        $this->sMethod = $sMethod;            
    }

    /**
     * get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->sMethod;
    }

    /**
     * set encryption type of the form: I know only one: multipart/form-data so thats the default parameter value
     * @param string $sEnctype
     */
    public function setEnctype($sEnctype = FORM::ENCTYPE_MULTIPART_FORMDATA)
    {
        $this->sEnctype = $sEnctype;
    }

    public function getEnctype()
    {
        return $this->sEnctype;
    }
    
    public function setOnsubmit($sOnSubmitEvent)
    {
            $this->sOnSubmit = $sOnSubmitEvent;
    }

    public function getOnsubmit()
    {
            return $this->sOnSubmit;
    }    


    protected function renderChild()
    {
        $sAttributes = '';        
        

        //method toevoegen
        if (strlen($this->getMethod()) > 0 )
        {
            if (($this->getMethod() == 'post') || ($this->getMethod() == 'get'))
                $sAttributes .= $this->addAttributeToHTML('method', $this->getMethod()); 
            else
                logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, '$this->getMethod() is not _GET nor _POST');
        }
        
        $sAttributes .= $this->addAttributeToHTML('enctype', $this->getEnctype()); 
        $sAttributes .= $this->addAttributeToHTML('action', $this->getAction()); 
        if ($this->getAutocomplete())
            $sAttributes .= $this->addAttributeToHTML('autocomplete', 'on'); 
        else
            $sAttributes .= $this->addAttributeToHTML('autocomplete', 'off'); 
        $sAttributes .= $this->addAttributeToHTML('onsubmit', $this->getOnsubmit()); 
        
        
        return $sAttributes;        
    }
}





?>
