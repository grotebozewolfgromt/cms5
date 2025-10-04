<?php
namespace dr\classes\dom;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\FormInput;
use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\Text;
use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\classes\types\TCurrency;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputDatetimelocal;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\form\InputHidden;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\validator\Onlynumeric;
use dr\classes\dom\tag\form\Textarea;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\tag\HTMLTag;
use dr\classes\dom\tag\form\InputRadio;
use dr\classes\dom\tag\form\InputTel;
use dr\classes\dom\tag\form\Label;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\Script;
use dr\classes\dom\tag\Ul;
use dr\classes\dom\tag\webcomponents\DRIconInfo;
use ErrorException;
use Exception;

/**
 * Description of FormGenerator
 *
 * This class generates HTML forms.
 * 
 * Why use this class?
 * - increased programming speed for forms (because of automatic input validation and filtering)
 * - extra security against invalid/dirty user input
 * - auto fills submitted values on form submit, when there is an input error and the form is reloaded
 * - Anti Cross-Site Request Forgery token is standard implemented
  *   the DOM classes of this framework. You have to allow HTML explicitly for each FormInputAbstract child class
 *   This saves time and is super secure, because you don't have to think about XSS.
 *  
 * This class allows to add input elements to a form and add a validator.
 * I.e. when you add an integer validator, this class will check for an integer value
 * and adds an appropriate error message above the field when input is not correct
 *
 * The easiest way to use this class is to show fields and save values in the same .php script
 * (instead of having 1 .php file for showing and 1 .php file for saving)
 *  
 * This class can pull data from TSysModel and make an input form for most of the fields
 * 
 * 
 **********************************************************
 * ANTI CSRF (Cross Site Request Forgery)
 **********************************************************
 * Random tokens are generated, hashed and inserted in the form
 * These are stored in the session.
 * Only if the token in the form and in the session match, we know 
 * that the request is coming from this form generator.
 * 
 * the session contains an array with tokens.
 * I choose for an array, because you could technically have multiple
 * tabs open for the same site which is a 'legal' thing to do.
 * But this allows us also to restrict the amount of tokens given out 
 * (in other words: how often is the form requested but not submitted).
 * If this maximum is exceeded, it might be a brute force attack or flood attack
 * 
 * 
 * 
 **********************************************************
 * RECAPTCHA V3
 **********************************************************
 * This class includes the functionality to use Recaptcha v3 for each form.
 * By default the recaptcha is DISABLED!
 * You enable it with setRecaptchaV3Use(true);
 * 
 * Recaptcha V3 validation is attached to the submit button rather than a form element (Recapcha V2)
 * This class adds a tiny js script with a function (named after the id of the form) for the callback triggered by the recaptcha
 * 
 * How to use need-to-knows:
 * -you need to include the google js library with setRecaptchaAPIInclude(true). But include only 1 per page if you have 2 or more FormGenerator objects!!! 
 * -you need to make your submit button into a recaptchav3 button with makeRecaptchaV3SubmitButton() ==> this needs to be a regular button instead of a submit button
 * 
 * 
 * 
 **********************************************************
 * HONEYPOT
 **********************************************************
 * This class uses a honeypot to 'catch' bots.
 * A honeypot field looks like a normal text field that a user can fill in,
 * however this field is not shown to a human user (hidden with js/css).
 * We count on it that a bot fills out this field, so we know it is spam
 * 
 * We are deliberately vague with fieldnames, css class names and javascript function names in order NOT to tip off spammers
 * 
 * with a simple javascript, you can hide the parent elements of the honeypot
        const objNodes = document.getElementsByClassName("letitbee");
        let iNodeCount = objNodes.length;
        for (i = 0; i < iNodeCount; i++) 
        {
            // objNodes[i].parentElement.style.visibility = 'hidden';
            objNodes[i].parentElement.style.height = 0; //otherwise it still takes up space
            objNodes[i].parentElement.style.overflow = 'hidden';
        } 
 * 
 * 
 **********************************************************
 * CODE EXAMPLE:
 **********************************************************
 * $this->objFormLogin = new FormGenerator('frmLogin', 'samescript.php');
 * 
 *       $this->objEdtUsername = new InputText();
 *       $this->objEdtUsername->setNameAndID('edtUsername');
 *       $this->objEdtUsername->setClass('input_type_text');                 
 *       $this->objEdtUsername->setRequired(true); 
 *       $this->objEdtUsername->setMaxLength(255);    
 *       $objValidator = new Maximumlength(transg('form_error_maxlengthexceeded', 'The maximumlength [length] of this field is exceeded', 'length', '100'), 100);
 *       $this->objEdtUsername->addValidator($objValidator);        
 *       $objValidator = new Required(transcms('form_error_requiredfield', 'This is a required field'));
 *       $this->objEdtUsername->addValidator($objValidator);       
 *       $objValidator = new Characterwhitelist(transcms('form_error_charactersnotallowed', 'One or more characters are not allowed'), TAuthenticationSystemAbstract::CHARSALLOWED_FORMFIELDS);
 *       $this->objEdtUsername->addValidator($objValidator);
 *       $this->objFormLogin->add($this->objEdtUsername, '', transg('loginform_field_username', 'username')); 
 *   
 * if ($this->objFormLogin->isValid()) 
 * 	echo "valid";
 * if ($this->objFormLogin->isFormSubmitted())
 *  echo "form submitted";
 * 
 * echo $this->objFormLogin->generate()->renderHTMLNode(); //generate() returns HTML DOM Form object found in classes/dom/tag/form
 * 
 **********************************************************
 * 
 *
 *
 * @TODO: check op unieke naam in formulier
 * @TODO: ja / nee element (met radioboxen)
 * @TODO: hints weergeven achter invulboxen
 * @TODO: mogelijkheid dat er nogmaals (na submitten van formulier) gevraagd wordt aan de gebruiker of alle ingevulde waardes juist zijn (dit gebeurd server side, als ok, dan wordt pas formulier in database opgeslagen)
 * @TODO: om-en-om kleuren regels in tabel
 * @TODO: extra check op request url (domein moet hetzelfde zijn bij request and submit)
 * 
 * 
 * 4 juli: abstracte klasse weg. de manier van gebruiken wordt anders. het wordt een hulpklasse geen klasse die je over moet erven
 * 4 juli: add() heeft parameter 'required' minder, omdat deze nu uit het html attribuut gelezen wordt
 * 4 juli: constructor geen parameter meer over submit
 * 4 juli: constructor set de form
 * 4 juli: action en method paramaters uit generate()
 * 4 juli: met method_exist de niet-input-type html objecten ondervangen 1,2
 * 5 juli: form->setMethod() aanroep
 * 5 juli: FormGenerator: bugfix: add(): in het 'for' attribuut van labels, werd geen rekening gehouden met de nameprefix. nu wel, waardoor het functioneert
 * 3 mrt 2014: FormGenerator: add() heeft nu geen verplichte description parameter meer
 * 3 mrt 2014: FormGenerator: generate() aanpassingen dat ingevulde waardes worden overgenomen in het nieuwe formulier bij foute invoer van de gebruiker, zodat deze niet alles opnieuw hoeft in te voeren
 * 11 mrt 2014: FormGenerator: generate() parameter toegevoegd die foutmeldinglijst bovenin beeld in en uitschakelt, standaard gedrag is nu : uitgeschakeld (voorheen ingeschakeld)
 * 2 apr 2015: FormGenerator: __construct default autocomplete false ipv true
 * 2 apr 2015: FormGenerator: checkFormNames en formids unique wordt alleen toegepast op forminput, niet op andere html componenten
 * 2 apr 2015: FormGenerator: oude code verwijderd
 * 2 apr 2015: FormGenerator: add() marked as required field is nu standaard false
  * 7 apr 2015: FormGenerator: optimalisaties door count buiten loop te zetten
  * 29 aug 2015: FormGenerator: unieke formnames worden niet meer gecheckt voor radio boxes
  * 30 mrt 2016: FormGenerator: add() parameters omgedraaid
   * 30 mrt 2016: FormGenerator: add() extra parameter voor het niet weergeven van de description
  * 30 mrt 2016: FormGenerator: constanten voor css klassen
  * 30 mrt 2016: FormGenerator: de formsections werken nu eindelijk met mapping!
  * 31 mrt 2016: FormGenerator: constanten voor input types:CSS_CLASS_INPUTTEXT
  * 31 mrt 2016: FormGenerator: voor checkboxen en radioboxes wordt de description gebruikt, die ACHTER de box geplaatst wordt. Dit ipv de postdescription die veelal kleiner is en alleen dient om hints te geven
  * 1 apr 2016: FormGenerator: de map array structuur veranderd waardoor nu behalve secties ook andere eigenschappen opgeslagen kunnen worden
    * 1 apr 2016: FormGenerator: now supports a mapping with deleted fields
    * 20 apr 2016: FormGenerator: wijzigingen aan populate(), modelToForm() en formToModel() zodat foreign keys values EN formulier-fouten van gebruikers ondersteund worden
    * * 20 apr 2016: FormGenerator: bugfixes en wijzigingen aan populate(), modelToForm() en formToModel()  
    * 24 apr 2016: FormGenerator: generate() : verwijderd: if ((!$bSubmitted) || ($bErrorsOnForm)) 
    * 2 mei 2019: FormGenerator: setSubmittedValuesAsValues() : toegevoegd
    * 8 jan 2020: FormGenerator: setSubmittedValuesAsValues() : exception for buttons and the form method (_GET or _POST) is used to retrieve values (only post was supported before, because it was the default value)
 * 8 jan 2020: FormGenerator: geen check meer op ids bij toevoegen in form generator
 * 9 jan 2020: FormGenerator: setSubmittedValuesAsValues(): uitzondering voor select boxen toegevoegd
 * 9 jan 2020: FormGenerator: generate(): restyle: fouten worden niet meer bovenaan form weergegeven, maar bij de velden zelf, in plaats daarvan een generieke foutmelding bovenin form, deze hebben we voor het cms weggelaten ivm de standaard foutmelding van het cms
 * 14 jan 2020: FormGenerator: CSS_CLASS_FORMDESCRIPTIONPOST toegevoegd for checkboxes
 * 1 okt 2021: FormGenerator: Cross-Site Request Forgery token implemented. nu Standaard geintegreerd
 * 1 okt 2021: FormGenerator: Cross-Site Request Forgery tokens counted and if exceeded: error + no form
 * 1 okt 2021: FormGenerator: Cross-Site Request Forgery token is gesalt
 * 2 okt 2021: FormGenerator: generate() SESSIONARRAYKEY_ANTICSRFTOKEN was not checked for existence
 * 2 okt 2021: FormGenerator: support voor flood detection. maar er wordt nog geen foutmelding gegeven of slowdown gedaan
 * 2 okt 2021: FormGenerator: flood detection errors shown, errormessage on top of form deleted
 * 2 okt 2021: FormGenerator: getFormStatus() added
 * 2 okt 2021: FormGenerator: hoop bugfixes ivm CSRF token
 * 20 jan 2023: FormGenerator: recapcha3 functionality added
 * 16 nov 2023: FormGenerator: anti CSRF token naar constructor
 * 16 nov 2023: FormGenerator: secs between submits and formshow worden nu op een andere plek gecheckt ivm custom forms
 * 16 nov 2023: FormGenerator: secs between formshow en submit werkt nu
 * 15 aug 2023: FormGenerator: support for info icon
 * 15 aug 2023: FormGenerator: added: addQuick()
 * 
 * @author dennis renirie
 */
class FormGenerator
{
	//array:formcontent keys
	const ARRAYKEY_FORMCONTENT_DOMELEMENTS                  = 'domelements';//array of 1 or more objects
	const ARRAYKEY_FORMCONTENT_DESCRIPTION                  = 'description';//string description before element
	const ARRAYKEY_FORMCONTENT_ICONINFO                		 = 'iconinfo';//string with text for the information-icon
	const ARRAYKEY_FORMCONTENT_DESCRIPTIONVISIBLE           = 'descriptionvisible';//boolean discription has to be displayed? default is yes
	const ARRAYKEY_FORMCONTENT_POSTDESCRIPTION              = 'postdescription';//string descriptions after element
	const ARRAYKEY_FORMCONTENT_REQUIRED                     = 'required';//boolean required field to submit
//	const ARRAYKEY_FORMCONTENT_FORMSECTIONCUSTOMCSSCLASS	= 'formsectioncustomcssclass';//you can define a custom css class (besides that of CSS_CLASS_FORMSECTION)
	
	//array: map keys: map fields to db
	const ARRAYKEY_MAP_FORMSECTION		= 'formsection'; //string: to wich section is the field
	const ARRAYKEY_MAP_READONLY			= 'readonly'; //boolean: field readonly, default = false
	const ARRAYKEY_MAP_DELETED 			= 'deleted'; //boolean: dont use field in form
	
	const SESSIONARRAYKEY_ANTICSRFTOKEN = 'sACSRFT';//Session Array Key for Anti Cross-Site Request Forgery Token
	const MAX_ANTICSRFTOKENS			= 50; //how many anti-CSRF tokens can the session hold? Above this number, this class deletes the oldest token when creating a new one
	const ANTICSRFTOKENS_SALTSEPARATOR	= '::'; 


	const SESSIONARRAYKEY_LASTFORMSHOW_TIMESTAMP = 'formgenerator-lastformshow-timestamp'; //when was the last time the form was SHOWN (aka: in not-submitted state)? Used for flood protection
	const SESSIONARRAYKEY_LASTFORMSUBMIT_TIMESTAMP = 'formgenerator-lastformsubmit-timestamp'; //when was the last time the form was SUBMITTED? Used for flood protection

	const SECTION_DEFAULT 				= 'default'; //just so we can detect the defaultsection (no text is displayed on the form if section == SECTION_DEFAULT)
	

	const CSS_CLASS_FORMSECTION						= 'formsection';
	const CSS_CLASS_FORMSECTIONHEADER 				= 'formsection-header';
	const CSS_CLASS_FORMSECTIONLINE					= 'formsection-line';
	const CSS_CLASS_FORMSECTIONLINEERROR			= 'formsection-line-error'; //div with error (we can color border it red)
	const CSS_CLASS_FORMSECTIONLINETOPERRORMESSAGE  = 'formsection-line-toperrormessage'; //div with error message on top of the page: "correct errors you dumbo!"
	const CSS_CLASS_FORMDESCRIPTION					= 'form-description';
	const CSS_CLASS_FORMDESCRIPTIONPOST				= 'form-description-post';//description after (post) the element - chekcboxes and radioboxes have description behind element , not in front
	// const CSS_CLASS_FORMDESCRIPTIONERROR            = 'form-description-error'; //if the description has an error (we can color it red)
	const CSS_CLASS_FORMPOSTDESCRIPTION				= 'form-postdescription';
    const CSS_CLASS_FORMSECTIONERRORLIST            = 'formsection-line-errorlist'; //the list per line with all the errors
	
	const CSS_CLASS_INPUTTEXT						= 'input_type_text';
	const CSS_CLASS_INPUTCHECK						= 'input_type_check';
	const CSS_CLASS_INPUTSELECT						= 'input_type_select';
	const CSS_CLASS_INPUTTEXTAREA					= 'input_type_textarea';
	// const CSS_CLASS_HONEYPOT						= 'letitbee';
	

	CONST FORMSTATUS_INIT							= 0; //can be no values, values from database or gone back from FORMSTATUS_VERIFYINPUT
	CONST FORMSTATUS_SUBMITTED_WITHERRORS			= 1;
	//CONST FORMSTATUS_VERIFYINPUT					= 2; -->@todo implement (skipped if user doesnt verify)
	CONST FORMSTATUS_SUBMITTED_OK					= 3;


// 	private $objHTMLFormInputObjects = null;
// 	private $objHTMLLabels = null; // de tekst voor (edtbox) of achter (checkbox) het input element getoond wordt
	
    private $arrFormContent = array();//dimensional array with structure: $arrFormContent['sectionname'][lineindex][ARRAYKEY_DOMELEMENTS][elementindex]
    private $arrElementIDs = array();//1d array with objects from $arrFormContent, but with key (=obj->getID) and object combination
    private $objForm = null;
    private $arrMappedHTMLElements = array();//2d array with mapped html-element-ids to sectionnames; arraystructure: $arrMap['elementid'][ARRAYKEY_MAP_SECTION] = 'sectionname'; the structure of the array looks redundant, but that way we dont have to do resource intensive loops to find elements for a formsection 
    //private $arrSectionMapper = array();//key is the htmlid the value is the sectionname 
    
    private $objInputTypeHiddenToDetectFormSubmitted = null; //InputHidden. we use a hidden input field to detect if a form is submitted or not. The function getIsFormSubmitted() uses this field.
    private $objInputTypeHiddenAntiCSRFToken = null; //InputHidden. CRSRF = Cross-Site Request Forgery.
    private $arrCustomCSSClassesFormSections= array(); //1d array['sectionname'] = 'mycustomclass'
	
	private $iSecsMinBetweenFormShowAndSubmit = 2; //minimum amount of seconds between form show and submit; default 2 seconds
	private $iSecsMinBetweenFormSubmits = 5; //minimum amount of seconds between form submits; default 2 seconds

	private $bRecaptchaV3Use = false; //use recaptcha v3
	private $bRecaptchaAPIInclude = true; //include recaptcha JS api. when you have 2 form generators on a page, you only want to include 1


    /**
     * constructor
     * 
     * @param string $sFormName
     * @param string $sTextSubmitButton
     * @param boolean $bAutocomplete
     */
    public function  __construct($sFormID, $sSubmitToUrlAction, $bAutocomplete = false)
    {
//         $this->objHTMLFormInputObjects = new TObjectlist();
//         $this->objHTMLLabels = new TObjectlist();
              
        
        //create form
        $this->objForm = new Form();
        $this->objForm->setName($sFormID);
        $this->objForm->setID($sFormID);
        $this->objForm->setEnctype(); //anders kun je geen bestanden versturen
        $this->objForm->setMethod(Form::METHOD_POST); //anders kun je geen bestanden versturen
        $this->objForm->setAction($sSubmitToUrlAction); //action setten
        $this->objForm->setAutocomplete($bAutocomplete); //action setten
        
        //create hidden field to detect if form is submitted
        $this->objInputTypeHiddenToDetectFormSubmitted = new InputHidden();
        $this->objInputTypeHiddenToDetectFormSubmitted->setValue('1234567890'); //is checked later if this value is numeric
        $this->objInputTypeHiddenToDetectFormSubmitted->setName('hdSbmd');
        $this->objForm->appendChild($this->objInputTypeHiddenToDetectFormSubmitted);

		//create hidden field with anti-CSRF token
        $this->objInputTypeHiddenAntiCSRFToken = new InputHidden();
		$this->objInputTypeHiddenAntiCSRFToken->setValue($this->generateAndRegisterAntiCSRFToken()); //always set new token (even if form is valid)
        $this->objInputTypeHiddenAntiCSRFToken->setNameAndID(ACTION_VARIABLE_ANTICSRFTOKEN); //Anti-Csrf-Token
        $this->objForm->appendChild($this->objInputTypeHiddenAntiCSRFToken);


		//defaults form spamming prevention
		$this->setSecsMinBetweenFormShowAndSubmit(APP_FORMS_LIMITSHOWSUBMIT_SECS);
		$this->setSecsMinBetweenFormSubmits(APP_FORMS_LIMITSUBMITS_SECS);

    }


    public function __destruct()
    {
		//====register timestamps for flood detection
		//this can't be done in the constructor, because it needs to be done after the checks.
		//(otherwise the checks will check what was set in the constructor, instead of the last time the form showed)
		//we can't do this in the generate(), because this function isn't called on custom forms
		if ($this->isFormSubmitted())
		{
			$_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSUBMIT_TIMESTAMP] = time();
		}
		else
		{
			$_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSHOW_TIMESTAMP] = time();
		}


        unset($this->objHTMLFormInputObjects);
        unset($this->objHTMLLabels);
        unset($this->objInputTypeHiddenToDetectFormSubmitted);
        unset($this->objInputTypeHiddenAntiCSRFToken);
    }


	/**
	 * Returns the hidden field that detects if a form is submitted
	 * 
	 * This is useful if you want to use a custom form, 
	 * but want to use the functions that the form generator offers
	 * (like checking for errors for example)
	 * 
	 * @return InputHidden
	 */
	public function getFormSubmittedDOMElement()
	{
		return $this->objInputTypeHiddenToDetectFormSubmitted;
	}

	/**
	 * Returns the hidden field that detects Cross Site Request Forgery (CSRF)
	 * 
	 * This is useful if you want to use a custom form, 
	 * but want to use the functions that the form generator offers
	 * (like checking for errors for example)
	 * 
	 * @return InputHidden
	 */
	public function getCSRFTokenDOMElement()
	{
		return $this->objInputTypeHiddenAntiCSRFToken;
	}	

	public function setHoneyPotFieldName($sName)
	{
		$this->objInputTypeTextHoneyPot->setName($sName);
	}

	public function getHoneyPotFieldName()
	{
		return $this->objInputTypeTextHoneyPot->getName();
	}

	/**
	 * does nothing when 
	 */
	public function setRecaptchaV3Use($bInclude)
	{
		$this->bRecaptchaV3Use = $bInclude;
	}

	/**
	 * 
	 */
	public function getRecaptchaV3Use()
	{
		return $this->bRecaptchaV3Use;
	}


	/**
	 * set include recapcha JS API?
	 * Only include 1 per page (if a page has 2 or more form generators)
	 */
	public function setRecaptchaAPIInclude($bInclude)
	{
		$this->bRecaptchaAPIInclude = $bInclude;
	}

	/**
	 * set include recapcha JS API?
	 * Only include 1 per page (if a page has 2 or more form generators)
	 */
	public function getRecaptchaAPIInclude()
	{
		return $this->bRecaptchaAPIInclude;
	}

	/**
	 * get status of the form.
	 * 
	 * This function returns 1 of the states:
	 * FormGenerator::FORMSTATUS_INIT
	 * FormGenerator::FORMSTATUS_SUBMITTED_WITHERRORS
	 * FormGenerator::FORMSTATUS_SUBMITTED_OK
	 *
	 * @return integer 
	 */
	public function getFormStatus()
	{
		if ($this->isFormSubmitted()) //least expensive operation (system resource wise)
		{
			if ($this->isValid()) //expensive operation (system resource wise)
				return FormGenerator::FORMSTATUS_SUBMITTED_OK;
			else
				return FormGenerator::FORMSTATUS_SUBMITTED_WITHERRORS;
		}
		else
			return FormGenerator::FORMSTATUS_INIT;
	}

    /**
     * assign a custom CSS class to a section with name $sSection
     * 
     * @param string $sSection
     */
    public function assignCSSClassSection($sSection, $sCustomCSSClass) 
    {
        $this->arrCustomCSSClassesFormSections[$sSection] = $sCustomCSSClass;
    }
    
    /**
     * map an html element to a formsection
     * 
     * @param string $sHTMLElementID
     * @param string $sSectionName
     */
//     public function mapToSection($sHTMLElementID, $sSectionName)
//     {
//     	$this->arrMappedHTMLElements[$sHTMLElementID] = $sSectionName;	
//     }
    
    /**
     * set new map for html elements wich are mapped to sections
     * @param array $arrNewMap; structure: $arrMappedHTMLElements['htmlelementid'][ARRAYKEY_MAP_FORMSECTION] = 'sectionname'
     */
    public function setMap($arrNewMap)
    {
    	unset($this->arrMappedHTMLElements);
    	$this->arrMappedHTMLElements = $arrNewMap;
    }

	/**
	 * how many seconds the user has to wait at least between submissions?
	 *
	 * @param int $iSecs
	 * @return void
	 */
	public function setSecsMinBetweenFormSubmits($iSecs)
	{
		$this->iSecsMinBetweenFormSubmits = $iSecs;
	}


	/**
	 * how many seconds the user has to wait at least between the form is shown 
	 * and submitted?
	 * 
	 * It would be weird for a long form to be filled out in 2 seconds,
	 * in that case make it longer than 2 seconds.
	 * But a loginform (=short) and can be auto-filled out by the browser or password
	 * manager, which is a complete legit action to do
	 *
	 * @param int $iSecs
	 * @return void
	 */
	public function setSecsMinBetweenFormShowAndSubmit($iSecs)
	{
		$this->iSecsMinBetweenFormShowAndSubmit = $iSecs;
	}


    /**
     * with populate()
     * 
     * @param TSysModel $objModel
     * @return string
     */
    public function getValueIDField(TSysModel $objModel)
    {
    	if ($this->getForm()->getMethod() == Form::METHOD_POST)
    		return $_POST[$objModel::getTable().TSysModel::FIELD_ID];
    	if ($this->getForm()->getMethod() == Form::METHOD_GET)
    		return $_GET[$objModel::getTable().TSysModel::FIELD_ID];    	 
    }
    
    /**
     * returns if  the form is submitted (the user hit the submit button or submits via Javascript)
     * @return bool
     */
    public function isFormSubmitted()
    {    	
		//We could look for the submit button, but this will fail if the form is submitted via javascript
		//instead we use a hidden field that always exists in a form to detect if the form is submitted
        
		return (is_numeric($this->objInputTypeHiddenToDetectFormSubmitted->getValueSubmitted()));
    }



	/**
	 * returns the first element on the form that is of class $sDOMClassName
	 * 
	 * @todo test function. function written, but never tested
	 * 
	 * @return HTMLTag or null
	 */
	private function getDOMElementOfType($sDOMClassName)
	{
		$arrFormContent = $this->arrFormContent;
        $arrSectionKeys = array_keys($arrFormContent);//new keys (these can be changed by the mapping)
        foreach ($arrSectionKeys as $sSectionName)
        {
        	$arrSection = $arrFormContent[$sSectionName];
       	
			foreach ($arrSection as $arrFormLine)
			{
				$arrElementsOnLine = $arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_DOMELEMENTS];	

				//detect if specific elements are present on this form-line
				foreach ($arrElementsOnLine as $objDOMElement)
				{
					if ($objDOMElement instanceof $$sDOMClassName)
					{
						return $objDOMElement;
					}
				}
			}
		}

		return null;
	}




	
    /**
     * are all values on the form valid?
     * 
     * checks on errors on the form by requesting all added form validators
     * 
     * @return bool true = all value are valid, false = errors on form
     */
    public function isValid()
    {    	
    	$iCountVal = 0;
    	
		//check Cross Site Request Forgery token
		if (!$this->isAntiCSRFTokenValid())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'anti Cross Site Request Forgery (CSRF) detection invalidated form validation');			
			return false;
		}

		//check recaptcha v3
		if (!$this->isRecaptchaV3Valid())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'recaptcha3 invalidated form validation');
			return false;
		}

		//check flood
		if ($this->isFloodDetectedBetweenFormSubmits() || $this->isFloodDetectedBetweenShowAndSubmitForm())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'flood detection invalidated form validation');
			return false;
		}

		//checking elements on form
    	foreach ($this->arrElementIDs as $objDOMElement)
    	{    		
    		if (method_exists($objDOMElement,'countValidators'))
    		{		
    			$iCountVal = $objDOMElement->countValidators();
    			for ($iValidatorTeller = 0; $iValidatorTeller < $iCountVal; $iValidatorTeller++)
    			{
    				$objValidator = $objDOMElement->getValidator($iValidatorTeller);
    				if (!$objValidator->isValid($objDOMElement->getValueSubmitted()))
					{
						//vardumpdie($objDOMElement->getName(), 'liop');
						logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'field '.$objDOMElement->getName().' wasn\'t valid. Error message: '.$objValidator->getErrorMessage());
    					return false;
					}
    			}
    		}//end: if exists countvalidars
    		
    	}
    	
    	return true;    
    }

    /**
     * handle all the stuff for the recaptcha v3
     *
     * returns also true when recaptcha is switched off
     * @return boolean true = everything ok , false = recaptcha failed
     */
    private function isRecaptchaV3Valid()
    {
        $sFormResponseRecapthca = '';
        $sGoogleResponse = '';
        $arrGoogleResponse = array();

        if ($this->getRecaptchaV3Use() && APP_GOOGLE_RECAPTCHAV3_ENABLE)
        {
            $sFormResponseRecapthca = $_POST['g-recaptcha-response'];
            $sURL = 'https://www.google.com/recaptcha/api/siteverify';
            $sURL = addVariableToURL($sURL, 'secret', APP_GOOGLE_RECAPTCHAV3_SECRETKEY);
            $sURL = addVariableToURL($sURL, 'response', $sFormResponseRecapthca);
            $sGoogleResponse = file_get_contents($sURL);
            $arrGoogleResponse = json_decode($sGoogleResponse, true);

            if ($arrGoogleResponse)
            {
                if ($arrGoogleResponse['success'] === true)
                {
                    //score is beteen 0.0 to 1.0 (0=bot, 1=human)
                    if (isFloatGreaterThan($arrGoogleResponse['score'], 0.7, 1))
                    {
                        logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Recaptcha: SUCCESS!');
                        return true;
                    }
                    else
					{
						logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Recaptcha: didnt meet threshold, score'.$arrGoogleResponse['score']);
                        return false;
					}
                }
                else
				{
					logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Recaptcha: Google response NOT success (=error).');
                    return false;
				}
            }
            else
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Recaptcha: NO Google response');
                return false;
			}
            
        }//no recaptcha use, then everything is honkydory
        return true;
    }

	/**
	 * make a regular submit button into a submit button that uses Recaptcha V3
	 * if $this->getRecaptchaV3Use() == false than nothing happens
	 * 
	 * CAUTION: $objButton needs to be InputButton not InputSubmit!
	 * 
	 * @param object $objSubmitButton 
	 */
	public function makeRecaptchaV3SubmitButton(&$objButton)
	{		
		if ($this->getRecaptchaV3Use())
		{
			if (is_a($objButton, InputButton::class))
			{
				$sJSFunctionNameCallback = '';
				$sJSFunctionNameCallback = 'onSubmitRecaptchaV3'.sanitizeJavascriptFunctionName($this->objForm->getID());

				if ($objButton->getClass()) //only add class when it is defined (because of the space in the class definition)
					$objButton->setClass('g-recaptcha '.$objButton->getClass());
				else
					$objButton->setClass('g-recaptcha');
				$objButton->setDataSitekey(APP_GOOGLE_RECAPTCHAV3_SITEKEY);
				$objButton->setDataCallback($sJSFunctionNameCallback);
				$objButton->setDataAction('submit');

				//add js function for the callback
				$objScript = new Script();
				$objScript->setText('function '.$sJSFunctionNameCallback.'(token) 
				{
					//alert(token);
					document.getElementById("'.$this->objForm->getID().'").submit();
				}'); 
				$this->objForm->appendChild($objScript);	
				
				//add recaptcha API
				if ($this->getRecaptchaAPIInclude())
				{
					?><script src="https://www.google.com/recaptcha/api.js"></script><?php
				}
			}
			else
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.':'.__FUNCTION__.':'.__LINE__, '$objButton parameter is not of type InputButton, it should be because recaptachaV3 uses a InputButton instead of a InputSubmit');
			}
	
		}
	}

    /**
     * add 1 line with 1 dom element 
     * if you want to add checkboxes or radioboxes, you can use this function with a description and a post description  
     * 
     * new (and correct way) to add stuff
     * 
     * @param HTMLTag $objDOMElement
     * @param string $sSection section in form (empty is default section)
     * @param string $sDescription description at beginning of line (before element)
     * @param boolean $bDescriptionVisible display discription or not (handy for forms where you dont have descriptions, i.e. loginforms for the username, password and checkboxes below)
     * @param string $sPostDescription descript at the end of the line (after element)
     * @param boolean $bMarkAsRequiredField required field
     */
    public function add(HTMLTag $objDOMElement, $sSection = '', $sDescription = '', $bDescriptionVisible = true, $sPostDescription = '', $bMarkAsRequiredField = false, $sTextInfoIcon = '')
    {
    	if ($sSection == '')
    		$sSection = FormGenerator::SECTION_DEFAULT;
    		
    	$arrTemp = array();
    		
    	$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_DOMELEMENTS] = array($objDOMElement);
        $arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTION] = $sDescription;
        $arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTIONVISIBLE] = $bDescriptionVisible;
        $arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_POSTDESCRIPTION] = $sPostDescription;
        $arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_REQUIRED] = $bMarkAsRequiredField;
        $arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_ICONINFO] = $sTextInfoIcon;
//        $arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_REQUIRED] = $bMarkAsRequiredField;

        $this->arrFormContent[$sSection][] = $arrTemp;
        unset($arrTemp);

        if (!$this->existsElement($objDOMElement->getID()))
            $this->arrElementIDs[$objDOMElement->getID()] = $objDOMElement;
    }

	/**
	 * easy-to-use alias for add() with a gazillion parameters
	 */
	public function addQuick(HTMLTag $objDOMElement, $sSection = '', $sDescription = '', $sTextInfoIcon = '')
	{
		$this->add($objDOMElement, $sSection, $sDescription, true, '', false, $sTextInfoIcon);
	}
    
    /**
     * add array of elements to form for 1 line (ie. 3 editboxes after one another)
     * if you want to add checkboxes or radioboxes, you have to include the label in
     * the $arrDomElements
     *
     * new (and correct way) to add stuff
     *
     * @param array $arrDOMElements
     * @param string $sSection section in form (empty is default section) 
     * @param string $sDescription description at beginning of line (before element)
     * @param boolean $bDiscriptionVisible display discription or not (handy for forms where you dont have descriptions, i.e. loginforms for the username, password and checkboxes below)
     * @param string $sPostDescription descript at the end of the line (after element)
     * @param boolean $bMarkAsRequiredField required field
     */
    public function addArray($arrDOMElements, $sSection = '', $sDescription = '', $bDiscriptionVisible = true, $sPostDescription = '', $bMarkAsRequiredField = false, $sTextInfoIcon = '')
    {
    	if ($sSection == '')
    		$sSection = FormGenerator::SECTION_DEFAULT;
    
    	if ($arrDOMElements)
    	{
    		$arrTemp = array();
    
    		$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_DOMELEMENTS] = $arrDOMElements;
    		$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTION] = $sDescription;
    		$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTIONVISIBLE] = $bDiscriptionVisible;
    		$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_POSTDESCRIPTION] = $sPostDescription;
    		$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_REQUIRED] = $bMarkAsRequiredField;
    		$arrTemp[FormGenerator::ARRAYKEY_FORMCONTENT_ICONINFO] = $sTextInfoIcon;
    
    		$this->arrFormContent[$sSection][] = $arrTemp;
    		unset($arrTemp);
    	}
    	
    	foreach ($arrDOMElements as $objDomElement)
    	{
    		if (!$this->existsElement($objDomElement->getID()))
    			$this->arrElementIDs[$objDomElement->getID()] = $objDomElement;
    	}
    }
    
    
    /**
     * creating the form, returns form object
     * @return Form
     */
    public function generate()
    {      
		//declarations  
		$bSubmitted = false;
		$bIsAntiCSRFTokenValid = false;
		$bErrorsOnForm = false;
		$arrFormContent = array();
		$arrMap = array();
		$objForm = null;	

		$iFormStatus = 0; //declared as int: default: FormGenerator::FORMSTATUS_INIT;

		//inits
        $objForm = $this->objForm;
        $arrFormContent = $this->arrFormContent;
        $arrMap = $this->arrMappedHTMLElements;
		$iFormStatus = $this->getFormStatus();
		$bErrorsOnForm = ($iFormStatus == FormGenerator::FORMSTATUS_SUBMITTED_WITHERRORS);        

		//====flood detection: 

			//register timestamps (needs to be done AFTER checks)
			//$_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSHOW_TIMESTAMP] = time(); //the form is always shown, with or without it is being submitted. --> moved to destructor because this function isn't called on custom forms


			
		//====Anti Cross-Site Request Forgery token		
			$bIsAntiCSRFTokenValid = $this->isAntiCSRFTokenValid(); 

			//create Cross-Site Request Forgery token	
			//$this->objInputTypeHiddenAntiCSRFToken->setValue($this->generateAndRegisterAntiCSRFToken());  --> done in constructor


        //====begin remapping sections 
			//(html-elementid's can be mapped to sections)
			//this mapping loop will assign the right elements to the right formsections
			//map array structure: $arrMap[elementid] = section
			if ($arrMap) //if mapping exists, if not dont waste systemresources
			{        
				$arrMappedFormContent = array();
				$arrSectionKeys = array_keys($arrFormContent);
				
				foreach ($arrSectionKeys as $sOldSectionName) //every section
				{
					$arrOldSection = $arrFormContent[$sOldSectionName];
					foreach ($arrOldSection as $arrFormLine) //every line
					{        	
						$sNewSection = '';
						foreach($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_DOMELEMENTS] as $objDOMElement) //every element in line
						{
							//if element-id exists in map, then add section to new mapped array
							if(array_key_exists($objDOMElement->getID(), $arrMap))
							{
								//does section element exist?
								if (array_key_exists(FormGenerator::ARRAYKEY_MAP_FORMSECTION, $arrMap[$objDOMElement->getID()])) 
								{
									$sNewSection = $arrMap[$objDOMElement->getID()][FormGenerator::ARRAYKEY_MAP_FORMSECTION];
									$arrMappedFormContent[$sNewSection][] = $arrFormLine;
								}
							}
						}      
						
						//if not exist in map, add to default section
						if ($sNewSection == '')
							$arrMappedFormContent[FormGenerator::SECTION_DEFAULT][] = $arrFormLine; 
					}
				}
				unset($arrFormContent);
				$arrFormContent = $arrMappedFormContent;
			}
        //end mapping
        

		//====error section on top of form
		$objDivSection = new Div();
		$objDivSection->setClass(FormGenerator::CSS_CLASS_FORMSECTION);
		//$objDivSection->addText(transg('form_input_errorstopform', 'Sorry, errors on form')); --> no text, this is done by the class caller
		if ($bErrorsOnForm) //only add to form when errors
			$objForm->appendChild($objDivSection);   
	
		$objUL = new Ul();
		$objUL->setClass(FormGenerator::CSS_CLASS_FORMSECTIONERRORLIST);
		$objDivSection->appendChild($objUL);


		//====CSRF token errors

			//error if anti Cross-Site Request Forgery token failed
			if ((!$bIsAntiCSRFTokenValid) && ($bSubmitted))
			{
				$objDivSection = new Div();
				$objDivSection->setClass(FormGenerator::CSS_CLASS_FORMSECTION.' '.FormGenerator::CSS_CLASS_FORMSECTIONERRORLIST);
				$objDivSection->addText(transg('form_input_error_anti-csrftoken-invalid', 'Form invalid. The form most likely expired: you waited too long with submitting input. Try submitting again!'));
				$objForm->appendChild($objDivSection);   
			}

		

		//====flood detection
			if ($this->isFloodDetectedBetweenShowAndSubmitForm())
				$objUL->addListItem(transg('form_input_error_flooddetected_formshowandsubmit', 'You submitted the form too quick. Wait at least [secs] seconds before trying again.', 'secs', $this->iSecsMinBetweenFormShowAndSubmit));

			if ($this->isFloodDetectedBetweenFormSubmits())
				$objUL->addListItem(transg('form_input_error_flooddetected_formsubmits', 'You submitted too many forms in a short timespan. Wait at least [secs] seconds before trying again.', 'secs', $this->iSecsMinBetweenFormSubmits));


        //===add all objects from internal objectlist to form
        $iCountSections = count($arrFormContent);
        $arrSectionKeys = array_keys($arrFormContent);//new keys (these can be changed by the mapping)
        foreach ($arrSectionKeys as $sSectionName)
        {
        	$arrSection = $arrFormContent[$sSectionName];
        	
        	
        	$objDivSection = new Div();
                if (array_key_exists($sSectionName, $this->arrCustomCSSClassesFormSections)) //if exists, add custom class
                    $objDivSection->setClass(FormGenerator::CSS_CLASS_FORMSECTION.' '.$this->arrCustomCSSClassesFormSections[$sSectionName]);
                else
                    $objDivSection->setClass(FormGenerator::CSS_CLASS_FORMSECTION);
        	$objForm->appendChild($objDivSection);        
        	

        	//header: new section
        	//only header when more than one section, and if not default section
        	if ($iCountSections > 1) 
        	{
        		$objDivHeader = new Div();
        		$objDivHeader->setClass(FormGenerator::CSS_CLASS_FORMSECTIONHEADER);
        		$objTempText = new Text();
                        if ($sSectionName != FormGenerator::SECTION_DEFAULT)
                            $objTempText->setText($sSectionName);
        		$objDivHeader->appendChild($objTempText);
        		$objDivSection->appendChild($objDivHeader);
        	}

        	
        	
            //====if NOT submitted yet, or there are errors
           // if ((!$bSubmitted) || ($bErrorsOnForm)) --> 24-apr-2016 verwijderd, omdat het loginform na een foute inlog geen nieuwe invulvelden gaf
            //{            	
            	
            	foreach ($arrSection as $arrFormLine)
            	{
            		$arrElementsOnLine = $arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_DOMELEMENTS];
            		$iCountElementsOnLine = count($arrElementsOnLine);
           		
            		//detect if specific elements are present on this form-line
            		$bCheckboxRadioBoxOnLine = false;
            		$iCountDeletedElementsOnLine = 0; //--> if all elements are deleted, dont show the line, to detect that: we count the deleted items
            		foreach ($arrElementsOnLine as $objDOMElement)
            		{
            			//look for checkbox or radiobox
            			if (($objDOMElement instanceof InputCheckbox) || ($objDOMElement instanceof InputRadio))
            				$bCheckboxRadioBoxOnLine = true;
            			
            			//look for deleted elements (and count them, so we know later on if we have to display the line)
            			if ($arrMap)
            				if(array_key_exists($objDOMElement->getID(), $arrMap))
            					if (array_key_exists(FormGenerator::ARRAYKEY_MAP_DELETED, $arrMap[$objDOMElement->getID()]))
            						if ($arrMap[$objDOMElement->getID()][FormGenerator::ARRAYKEY_MAP_DELETED])
            							++$iCountDeletedElementsOnLine;
            		}
            		
            		
            		//detect errors			
            		$bErrorsDetectedOnLine= false;//declare
            		if ($bErrorsOnForm)
            		{
						//errors for every element on the form
            			foreach($arrElementsOnLine as $objDOMElement)
            			{
            				if (method_exists($objDOMElement,'countValidators'))
            				{
            					$iCountVal = $objDOMElement->countValidators();
            					for ($iValidatorTeller = 0; $iValidatorTeller < $iCountVal; $iValidatorTeller++)
            					{
            						$objValidator = $objDOMElement->getValidator($iValidatorTeller);
            						if (!$objValidator->isValid($objDOMElement->getValueSubmitted()))
            						{
            							$bErrorsDetectedOnLine = true;
            						}
            					}
            				}
            			}
            		}		
            		            		
            		
            		//start with a new line
            		$objDivLine = new Div();
            		$objDivLine->setClass(FormGenerator::CSS_CLASS_FORMSECTIONLINE);// we may add error class later,  for checkboxes we overwrite class
            		
            		
            		
					//add errors --> always add the <ul>, but we don't have anything in it (this is so we can add errors AJAX when onChange() and when saving AJAX
					$objUL = new Ul();
					$objUL->setClass(FormGenerator::CSS_CLASS_FORMSECTIONERRORLIST);
				
					if ($bErrorsDetectedOnLine) 
					{
						foreach($arrElementsOnLine as $objDOMElement)
						{
							if (method_exists($objDOMElement,'countValidators'))
							{
								$iCountVal = $objDOMElement->countValidators();
								for ($iValidatorTeller = 0; $iValidatorTeller < $iCountVal; $iValidatorTeller++)
								{
									$objValidator = $objDOMElement->getValidator($iValidatorTeller);
									if (!$objValidator->isValid($objDOMElement->getValueSubmitted()))
									{
										$objUL->addListItem($objValidator->getErrorMessage());
									}
								}
							}
					
						}            
					}									
					$objDivLine->appendChild($objUL);    

					

            		//label with description 
            		//label 'FOR' property only when elements exist
            		if ($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTIONVISIBLE])
            		{
	            		$objLabel = new Label();
                        $objLabel->setClass(FormGenerator::CSS_CLASS_FORMDESCRIPTION);// we may add error class later
	            		if ($bErrorsDetectedOnLine)
                        {
	            			$objDivLine->setClass($objDivLine->getClass().' '.FormGenerator::CSS_CLASS_FORMSECTIONLINEERROR);	            			
                        }
                                
	            		if ($iCountElementsOnLine > 0)
	            		{            			
	            			$objFirstDom = $arrElementsOnLine[0];
	            			$objLabel->setFor($objFirstDom->getID());            			
	            		} 
	            		
	            		//add text, exception for checkboxes en radio buttons (text behind item, instead of in front)
						if (!$bCheckboxRadioBoxOnLine)
						{
							$objLabel->setText($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTION]);
						}
	            		

						//add i-icon with more information
						if ($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_ICONINFO] !== '')
						{
							$objIconInfo = new DRIconInfo();
							$objIconInfo->setInfo($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_ICONINFO]);
							$objLabel->appendChild($objIconInfo);
							unset($objIconInfo);
						}


	            		$objDivLine->appendChild($objLabel);
	            		unset($objLabel);
            		}
            		        			
					

            		
            		//elements on the form-line
	                foreach($arrElementsOnLine as $objDOMElement)
	                {
	                	$bAddToLine = true;
	                	
	                	//extra functions
	                	if ($arrMap)
	                	{
	        				if(array_key_exists($objDOMElement->getID(), $arrMap))
	        				{
	        					//detect readonly fields	        					
	        					if (array_key_exists(FormGenerator::ARRAYKEY_MAP_READONLY, $arrMap[$objDOMElement->getID()]))
		        					$objDOMElement->setReadOnly($arrMap[$objDOMElement->getID()][FormGenerator::ARRAYKEY_MAP_READONLY]);
	        					
	        					//detect deleted fields
	        					//if (array_key_exists(FormGenerator::ARRAYKEY_MAP_DELETED, $arrMap[$objDOMElement->getID()]))
	        					//	$bAddToLine = !($arrMap[$objDOMElement->getID()][FormGenerator::ARRAYKEY_MAP_DELETED]);		        						 
	        				}
	                	}

	                	if ($bAddToLine)
	                		$objDivLine->appendChild($objDOMElement);
	                }
	                
	                //description for checkboxes and radio boxes
	                if ($bCheckboxRadioBoxOnLine)
	                {	                	
		                $objLabel = new Label();
		                $objLabel->setClass(FormGenerator::CSS_CLASS_FORMDESCRIPTIONPOST);
		                if ($iCountElementsOnLine > 0)
		                {
		                	$objFirstDom = $arrElementsOnLine[0];
		                	$objLabel->setFor($objFirstDom->getID());
		                }		                 
						$objLabel->setText($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_DESCRIPTION]);		                 
	                	$objDivLine->appendChild($objLabel);
	                	unset($objLabel);
	                }
	                
	                //post description
	                if (strlen($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_POSTDESCRIPTION]) > 0)
	                {
	                	$objLblPostDescription = new Label();
	                	$objLblPostDescription->setClass(FormGenerator::CSS_CLASS_FORMPOSTDESCRIPTION);
	                	$objLblPostDescription->addText($arrFormLine[FormGenerator::ARRAYKEY_FORMCONTENT_POSTDESCRIPTION]);
	                    if ($iCountElementsOnLine > 0)
            			{            			
            				$objFirstDom = $arrElementsOnLine[0];
            				$objLblPostDescription->setFor($objFirstDom->getID());            			
            			} 	                	
	                	$objDivLine->appendChild($objLblPostDescription);
	                }                       

	                if ($iCountElementsOnLine > $iCountDeletedElementsOnLine) //als alle elementen gedelete zijn, heeft het weinig zin om de line toe te voegen
	                	$objDivSection->appendChild($objDivLine);
            	}//end: foreach form-line
          // }//end: if !bSubmitted: first time --> 24 apr2016 verwijderd
        }//end: foreach sections


        return $objForm;
    }


    /**
     * array with contents of the form
     * 
     * @return array
     */
    public function getContents()
    {
    	return $this->arrFormContent;
    }


 
    /**
     * return internal html form object
     * @return Form
     */
    public function getForm()
    {
    	return $this->objForm;
    }
    
    /**
     * 
     * @param string $sHTMLID
     * @return HTMLTag
     */
    public function getElement($sHTMLID)
    {
    	return $this->arrElementIDs[$sHTMLID];
    }

	/**
	 * returns all internal elements
	 */
	public function getElements()
	{
		return $this->arrElementIDs;
	}
    
    /**
     * is the object ID (html <input type="text" id="THIS ID">)  used in the form?
     * 
     * @param string $sHTMLID
     * @param bool $bIgnoreEmptyHTMLID == true-> if $sHTMLID == '' this function returns false, otherwise it does a check if the empty ID exists in the form
     * @return bool
     */
    public function existsElement($sHTMLID, $bIgnoreEmptyHTMLID = false)
    {
        if ($bIgnoreEmptyHTMLID)
        {
            if ($sHTMLID == '')
                return false;
        }
        
    	return array_key_exists($sHTMLID, $this->arrElementIDs);
    }
    
    public function merge(FormGenerator &$objForm)
    {
    	//check for double id's
    	$arrKeysThis = array_keys($this->arrElementIDs);    	
    	foreach ($arrKeysThis as $sKey)
    	{
    		if (array_key_exists($sKey, $objForm))
    			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'merge(): key "'.$sKey.'" already exists in other form, therefore cannot merge without overriding values', $this);
    	}
    	
    	$this->arrElementIDs = array_merge($this->arrElementIDs, $objForm->arrElementIDs);
		$this->arrFormContent = array_merge($this->arrFormContent, $objForm->arrFormContent);    	
    }
    
    /**
     * add dom elements for a model to internal array structure
     * 
     * to use the generator, use generate() after populate(), this will make the form
     * populate is used by modelToView() and viewToModel() (there is some redudancy if you load records foreign tables etc)
     * 
     * @param TSysModel $objModel
     * @param array $arrFields optional: generate only html-objects for this fields, otherwise $objModel->getFieldsDefined() is assumed
     * @param boolean $bLoadForeignValuesFromDB : true will load all values from foreign key fields
     */
    public function populate(TSysModel &$objModel, $arrFields = array(), $bLoadForeignValuesFromDB = false)
    {    	
    	if (!$arrFields)
    		$arrFields = $objModel->getFieldsDefined();
    	$sTable = $objModel::getTable();
    	 
    	foreach ($arrFields as $sFieldName)
    	{
    		$objNewDomElement = null;
    		$bSkipField = false;
    		$sTransDescription = transg('form_'.get_class_short($this).'_'.$sFieldName, $sFieldName);
    		$sID = $sTable.$sFieldName;
    		$sName = $sID;
    	
    		if ($sFieldName == TSysModel::FIELD_ID) //id field is hidden
    		{
    			$objNewDomElement = new InputText();
    			$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTTEXT);
    			$objNewDomElement->setID($sID);
    			$objNewDomElement->setName($sName);
    			$objNewDomElement->setReadOnly(true);
    			$this->add($objNewDomElement, '',  $sTransDescription);
    			$bSkipField = true;
    		}
    		elseif (($objModel->getFieldForeignKeyTable($sFieldName)) && ($objModel->getFieldForeignKeyField($sFieldName)))//foreign keyed table
    		{
    			$objNewDomElement = new Select();
    			$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTSELECT);
    			$objNewDomElement->setID($sID);
    			$objNewDomElement->setName($sName);
    			    			
    			if ($bLoadForeignValuesFromDB) //load all foreign key values
    			{
    				$sForeignClass = '';
    				$sForeignClass = $objModel->getFieldForeignKeyClass($sFieldName);
    				$objForeignModel = new $sForeignClass;
    				if ($objForeignModel->getTableUseOrderField())
    					$objForeignModel->sort(TSysModel::FIELD_POSITION);
   					$objForeignModel->loadFromDB();
   					while ($objForeignModel->next())
   					{
   						$objOption = new Option();
   						$objOption->setValue($objForeignModel->get(TSysModel::FIELD_ID));
   						$objOption->setText($objForeignModel->getDisplayRecordShort());
   						//$objOption->setSelected($objModel->get($sFieldName) === $objForeignModel->get(TSysModel::FIELD_ID));//selected --> kunt m nog niet selecteren record is nog niet geladen
   						$objNewDomElement->appendChild($objOption);
   					}
   					unset($objForeignModel);    					     					
    			}    			
    			
    			$this->add($objNewDomElement, '',  $sTransDescription);
    			$bSkipField = true;
    		}
    		elseif ($sFieldName == TSysModel::FIELD_CHECKOUTEXPIRES)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_CHECKSUMENCRYPTED)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_POSITION)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_RECORDCHANGED)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_RECORDCREATED)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_RECORDHIDDEN)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_SYS_DIRTY)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_SYS_NEW)
    			$bSkipField = true;
   		 
    		 
    		if (!$bSkipField)//skip field
    		{
    	
    			$iType = $objModel->getFieldType($sFieldName);
    			switch($iType)
    			{
    				// case CT_DATETIME:
    				// 	$objNewDomElement = new InputDatetimelocal();
    				// 	$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTTEXT);
    				// 	$objNewDomElement->setID($sID);
    				// 	$objNewDomElement->setName($sName);
    				// 	$this->add($objNewDomElement, '', $sTransDescription);
    				// 	break;
    				case CT_BOOL:
    						
    					$objNewDomElement = new InputCheckbox();
    					$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTCHECK);
    					$objNewDomElement->setValue(1);
    					$objNewDomElement->setID($sID);
    					$objNewDomElement->setName($sName);
    						
    					$this->add($objNewDomElement, '', $sTransDescription);
    					break;
    				case CT_INTEGER32:
    				case CT_INTEGER64:
    					$objNewDomElement = new InputNumber();
    					$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTTEXT);
    					if ($objModel->getFieldNullable($sFieldName))
    						$objNewDomElement->addValidator(new Onlynumericorempty(transg('detailmodel_onlynumericorempty', 'Only numeric values or empty allowed')));
    					else
    						$objNewDomElement->addValidator(new Onlynumeric(transg('detailmodel_onlynumeric', 'Only numeric values allowed')));
    					$objNewDomElement->setID($sID);
    					$objNewDomElement->setName($sName);
    					$this->add($objNewDomElement, '', $sTransDescription);
    					 
    					break;
    				case CT_DECIMAL:
    				case CT_CURRENCY:
    					$objNewDomElement = new InputNumber();
    					$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTTEXT);
    					if ($objModel->getFieldNullable($sFieldName))
    						$objNewDomElement->addValidator(new Onlynumeric(transg('detailmodel_onlynumericorempty', 'Only numeric values or empty allowed')));
    					else
    						$objNewDomElement->addValidator(new Onlynumeric(transg('detailmodel_onlynumeric', 'Only numeric values allowed')));
    					$objNewDomElement->setID($sID);
    					$objNewDomElement->setName($sName);
    					$this->add($objNewDomElement, '', $sTransDescription);
    					 
    					break;
    				case CT_ENUM:
    					$objNewDomElement = new Select();
    					$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTSELECT);
    					$objNewDomElement->setID($sID);
    					$objNewDomElement->setName($sName);
    					$this->add($objNewDomElement, '', $sTransDescription);
    					 
    					break;
    				case CT_LONGTEXT:
    					$objNewDomElement = new Textarea();
    					$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTTEXTAREA);
    					$objNewDomElement->setID($sID);
    					$objNewDomElement->setName($sName);
    					$this->add($objNewDomElement, '', $sTransDescription);
    					break;
    				default: //string
    					$iMaxLength = 0;
    					$iMaxLength = $objModel->getFieldLength($sFieldName);
    	
    					$objNewDomElement = new InputText();
    					$objNewDomElement->setMaxLength($iMaxLength);
    					$objNewDomElement->setClass(FormGenerator::CSS_CLASS_INPUTTEXT);
    					$objNewDomElement->addValidator(new Maximumlength($iMaxLength));
    					$objNewDomElement->setID($sID);
    					$objNewDomElement->setName($sName);
    					$this->add($objNewDomElement, '', $sTransDescription);
    					 
    			}
    			 
    		}//eind: skipfield
    	
    	
    	}//EIND: foreach field
    	    	
    }
    
	/**	
	 * transfers values of this to model $objModel
	 * 
	 * 
     * @param TSysModel $objModel
     * @param array $arrFields optional: generate only html-objects for this fields, otherwise $objModel->getFieldsDefined() is assumed
	 */
    public function viewToModel(TSysModel &$objModel, $arrFields = array())
    {    	
            $sTable = $objModel::getTable();
            if (!$arrFields)
                    $arrFields = $objModel->getFieldsDefined();

            $iType = 0;	


            $iID = $this->getElement($sTable.TSysModel::FIELD_ID)->getValueSubmitted();
            if (!is_numeric($iID))//preventing injection
                            $iID = TSysModel::FIELD_ID_VALUE_DEFAULT;


            //walk through all fields
            foreach($arrFields as $sFieldName)
            {
                    $sHTMLID = $sTable.$sFieldName;

                    //only fields in form
                    if ($this->existsElement($sHTMLID))
                    {
                            $iType = $objModel->getFieldType($sFieldName);
                            switch ($iType)
                            {
                                    case CT_DATETIME:
                                            $objDateTime = new TDateTime();
                                                    $objDateTime->setDateTimeAsString($this->getElement($sHTMLID)->getValueSubmitted());
                                                    $objModel->set($sFieldName, $objDateTime);
                                                    break;
                                            case CT_BOOL:
                                                    $objModel->set($sFieldName, $this->getElement($sHTMLID)->getValueSubmittedAsBool());
                                                    break;
                                    case CT_INTEGER32:
                                    case CT_INTEGER64:
                                            $objModel->set($sFieldName, $this->getElement($sHTMLID)->getValueSubmittedAsInt());
                                            break;
                                    case CT_DECIMAL:
                                                    $objDec = new TDecimal();
                                                    $objDec->setValueFormatted($this->getElement($sHTMLID)->getValueSubmitted());
                                                    $objModel->set($sFieldName, $objDec);
                                                    break;
                                    case CT_ENUM:
                                    case CT_LONGTEXT:
                                    default://string
                                            $objModel->set($sFieldName, $this->getElement($sHTMLID)->getValueSubmitted());
                            }//switch
                    }//array_key_exists
            }//foreach arrFields
    	
    }
    
    /**
     * after you use popuplate(), use this function to transfer values from model to the fields in the form 
     *
     * @param TSysModel $objModel
     * @param array $arrFields optional: generate only html-objects for this fields, otherwise $objModel->getFieldsDefined() is assumed
     */    
    public function modelToView(TSysModel &$objModel, $arrFields = array())
    {
    	if (!$arrFields)
    		$arrFields = $objModel->getFieldsDefined();
    	$sTable = $objModel::getTable();
    	
    	//$this->populate($objModel, $arrFields); --> is al gebeurd als het goed is
    	
    	$iType = 0;
    	//$objDOMElement = null;
    	if ($objModel->count() == 0) //defaults if nothing is loaded (aka its a new record)
    		$objModel->newRecord();
    	
    	//treatment for special fields
    	foreach ($arrFields as $sFieldName)
    	{
    		$bSkipField = false;
    		$objDOMElement = null;
    		 
    		if (($objModel->getFieldForeignKeyTable($sFieldName)) && ($objModel->getFieldForeignKeyField($sFieldName)))//foreign keyed table --> select the correct value
    		{
    			$objDOMElement = $this->getElement($sTable.$sFieldName);
    			
    			//searching for the right option in the select-box (pull-down-box)
    			while($objNode = $objDOMElement->getNextChildNode())
    				if ($objNode->getValue() === $objModel->get($sFieldName))
    					$objNode->setSelected(true);//selected    			
    			
    			$bSkipField = true;
    		}
    		elseif ($sFieldName == TSysModel::FIELD_CHECKOUTEXPIRES)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_CHECKSUMENCRYPTED)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_POSITION)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_RECORDCHANGED)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_RECORDCREATED)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_RECORDHIDDEN)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_SYS_DIRTY)
    			$bSkipField = true;
    		elseif ($sFieldName == TSysModel::FIELD_SYS_NEW)
    			$bSkipField = true;
    			
    			
    		if (!$bSkipField)
    		{    	
    			$objDOMElement = $this->getElement($sTable.$sFieldName);
    			
    			$iType = $objModel->getFieldType($sFieldName);
    			switch($iType)
    			{
    				case CT_DATETIME:
    					// 		   					vardump($sFieldName, 'getto');
    					// 		   					vardump($objDOMElement->setValue($objModel->get($sFieldName)));
    					$objDOMElement->setValue($objModel->get($sFieldName)->getDateTimeAsString());
    					break;
    				case CT_BOOL:
    					$objDOMElement->setChecked($objModel->get($sFieldName));
    					break;
    				case CT_INTEGER32:
    				case CT_INTEGER64:
    					$objDOMElement->setValue($objModel->get($sFieldName));
    					break;
    				case CT_DECIMAL:
    				case CT_CURRENCY:
    					$objDOMElement->setValue($objModel->get($sFieldName)->getValueFormatted());
    					break;
    				case CT_ENUM:
    					$arrOpts = $objModel->getFieldEnumValues($sFieldName);
    					foreach ($arrOpts as $sOption)
    					{
    						$objOption = new Option();
    						$objOption->setValue($sOption);
    						$objOption->setText(transg('detailmodel_'.$sTable.$sFieldName.'_'.$sOption, $sOption));
    						$objOption->setSelected($objModel->get($sFieldName) === $sOption);//selected
    						$objDOMElement->appendChild($objOption);
    					}
    					break;
    				case CT_LONGTEXT:
    					$objDOMElement->setText($objModel->get($sFieldName));
    					break;
    				default: //string
    					$objDOMElement->setValue($objModel->get($sFieldName));
    			}//case
    		}//skipfield
    	}//foreach fields    	
    }
    
    
    /**
     * copy over the submitted values as (actual) values of all the elements on the form generator
     * 
     * this replaces all the lines:
     * $objEditLocale->setValue($objEditLocale->getValueSubmitted());
     * by doing it automatically
     * 
     * @important: there can be elements missing you need to add
     * 
     */
    public function setSubmittedValuesAsValues()
    {
        $objCurrElement = null;

		//copy over Anti CSRF token
		$this->objInputTypeHiddenAntiCSRFToken->setValue($this->objInputTypeHiddenAntiCSRFToken->getValueSubmitted());

        //go through all fields
        foreach($this->arrElementIDs as $objCurrElement)
        {
            $bCallSetValue = true; //call ->setValue()?
            
            //EXCEPTION FOR BUTTONS
            //after form submission only the value of the button that submitted the form is in the _GET or _POST array,
            //the rest of the buttons has no value, so if you re-set the value of the button, it sets and empty value, 
            //so the buttons have no text anymore
            //so we need an exception for the value setting buttons (input type button and input type submit)
            if (($objCurrElement instanceof InputSubmit) || ($objCurrElement instanceof InputButton)) //only set value if not button
                $bCallSetValue = false;

            //EXCEPTION FOR CHECKBOX
            if($objCurrElement instanceof InputCheckbox)
            {
                $objCurrElement->setValue(1);
                $objCurrElement->setChecked($objCurrElement->getContentsSubmitted($this->getForm()->getMethod())->getValueAsBool());
                $bCallSetValue = false;
            }
            
            //EXCEPTION FOR SELECT (html comboboxes)
            if($objCurrElement instanceof Select)
            {
                $objCurrElement->setSelectedOption($objCurrElement->getContentsSubmitted($this->getForm()->getMethod())->getValue());
                $bCallSetValue = false;
            }

            //add here other exceptions
            //...
            
            
            //call ->setValue() on html node
            if ($bCallSetValue)
                $objCurrElement->setValue($objCurrElement->getContentsSubmitted($this->getForm()->getMethod())->getValue());  
        }        
    }

    /**
     * generates a new form token and saves it in the session.
     * You can check if a formtoken is valid with this function: isAntiCSRFTokenValid()
     *
     * @return string token
     */
    public function generateAndRegisterAntiCSRFToken()
    {
        $sNewToken = '';
        $sNewSalt = '';
        
		$sNewToken = generatePassword(10, 50);
        $sNewSalt = md5(generateRandomString(10, 20, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*(){}?;_+=.,^ '));        
        $sNewToken = $sNewSalt.FormGenerator::ANTICSRFTOKENS_SALTSEPARATOR.sha1($sNewToken.$sNewSalt); //I choose SHA1 because it is fast and every result is the same, so we can find it fast in the array (in $_SESSION)

        if (isset($_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN]))
            $_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN][] = $sNewToken;
        else
            $_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN] = array($sNewToken);


		//for every new token, remove an old one that was good
		if ($this->isAntiCSRFTokenValid())
			$_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN] = array_deletevalue($_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN], $this->objInputTypeHiddenAntiCSRFToken->getValueSubmitted());


		//remove old tokens
		//we keep only the most recent X amount of tokens (X = FormGenerator::MAX_ANTICSRFTOKENS)
		if (count($_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN]) > FormGenerator::MAX_ANTICSRFTOKENS)
		{
			$arrTemp = array();
			$arrTemp = $_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN];
			array_shift($arrTemp);
			$_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN] = $arrTemp;
		}


        return $sNewToken;
    }

	/**
	 * checks if Cross-Site Request Forgery token is valid
	 *
	 * @return boolean
	 */
	public function isAntiCSRFTokenValid()
	{
		//check existence Cross-Site Request Forgery token in session. if not present, it is a forgery
		if (isset($_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN]))
		{	
			//token doesn't exist
			if (!in_array($this->objInputTypeHiddenAntiCSRFToken->getValueSubmitted(), $_SESSION[FormGenerator::SESSIONARRAYKEY_ANTICSRFTOKEN]))
				return false;
		}

		return true;
	}

	/**
	 * detect flood on time between form show and form submit
	 *
	 * You can't circumvent this check by deleting the session (unlike isFloodDetectedBetweenFormSubmits()) 
	 * because the anti-CSRF token forces the form show and submit to be in the 
	 * same session in order to be able to submit the form at all
	 * 
	 * @return boolean
	 */
	public function isFloodDetectedBetweenShowAndSubmitForm()
	{
		$iTimeNow = 0;
		$iTimeShow = 0;
		$iSecsDifference = 0;

		$iTimeNow = time();
		if (isset($_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSHOW_TIMESTAMP]))
			$iTimeShow = $_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSHOW_TIMESTAMP];
		
		$iSecsDifference = $iTimeNow - $iTimeShow;
		return ($iSecsDifference < $this->iSecsMinBetweenFormShowAndSubmit);
	}

	/**
	 * detect flood on form submits
	 * 
	 * it possible to circumvent this check by changing session id
	 *
	 * @return boolean
	 */
	public function isFloodDetectedBetweenFormSubmits()
	{
		$iTimeNow = 0;
		$iTimePreviousSubmit = 0;
		$iSecsDifference = 0;

		$iTimeNow = time();
		if (isset($_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSUBMIT_TIMESTAMP]))
			$iTimePreviousSubmit = $_SESSION[FormGenerator::SESSIONARRAYKEY_LASTFORMSUBMIT_TIMESTAMP];

		$iSecsDifference = $iTimeNow - $iTimePreviousSubmit;
		return ($iSecsDifference < $this->iSecsMinBetweenFormSubmits);
	}

}




?>
