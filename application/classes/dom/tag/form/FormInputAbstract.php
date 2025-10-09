<?php
namespace dr\classes\dom\tag\form;

use dr\classes\patterns\TObjectList;
use dr\classes\dom\tag\HTMLTag;
use dr\classes\dom\validator\ValidatorAbstract;
use dr\classes\dom\validator\FormInputContents;
use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;

/**
 * parent class for all form input
 * this class gets all values that are submitted
 *
 * this class reads all values from $_POST or $_GET array when getContentsSubmitted() is called
 * 
 * ==== ARRAYS AS FIELD NAMES ====
 * Some fields can be array of fields which php converts automatically to an array.
 * for example the field 'edtQuantity': <input type="text" name="edtQuantity[]">  <--HAS PARENTHESES
 * You can't name the field edtQuantity[] (with the square brackets), because then you can't retrieve the values
 * Therefore the field still needs to be named 'edtQuantity', but in the constructor add TRUE,
 * so this class knows to look for 'edtQuantity' in the $_POST array, 
 * The name when requested with getName() is WITH the square brackets
 * 
 * ==== WARNING ====
 * - characters are automatically filters with a whitelist, blacklist and a parameter of getValueSubmitted()
 * - the whitelist is enabled by default for security reasons
 * - the default parameter of getValueSubmitted() escapes html automatically
 * 
 * ==== BUG FINDING ====
 * - if characters are missing, you need to look at:
 * 		1. the whitelist
 * 		2. the blacklist
 * 		3. a parameter of getValueSubmitted()
 * 
 * 
 * 29 apr 2015 FORMINPUT allow html toegevoegd
 * 8 juli 2015: FORINPUT bugfix in getContentsSubmitted() vergelijk post method
 * 24 apr 2016: FormInputAbstract autofocus toegevoegd
 * 2 mei 2019: setName() heeft extra parameter waarmee tegelijk ook de id geset kan worden
 * 23 jun 2021: getContentsSubmitted() doesn't use prefixes anymore
 * 17 nov 2023: support for arrays
 * 17 nov 2023: BUGFIX: nasty bug! komma in parameter bij filteren for XSS
 * 17 nov 2023: getValueSubmitted() added for fast reading of values
 * 13 mrt 2023: FormInputAbstract: getValueSubmittedInt() added for fast reading of int values
 * 13 mrt 2023: FormInputAbstract: getValueSubmitted() returns null if none of the conditions are met
 * 13 mrt 2023: FormInputAbstract: getContentsSubmitted() deprecated, use getValueSubmitted() instead
 * 15 mrt 2023: FormInputAbstract: getValueSubmittedAsBool() added
 * 17 mei 2024: FormInputAbstract: isArray verwijderd (want deze zit al in de parent)
 * 30 jan 2024: FormInputAbstract: getAllowHTML() verwijderd, parameter van getValueSubmitted() vervangt dit
 * 30 jan 2024: FormInputAbstract: getValueSubmitted() parameter voor filtering. defaults to escaping via htmlspecialchars()
 * 13 aug 2024: FormInputAbstract: added blacklist and whitelist filtering
 */
abstract class FormInputAbstract extends HTMLTag
{
	// protected $objContentsInit = null; //FormInputContents
	// protected $objContentsSubmitted = null; //FormInputContents
	private $objValidators = null;//TObjectlist
	private $bDisabled = false;
	private $bReadOnly = false;
	private $bRequired = false; //in html elements you can specify a 'required'. Not every browser supports this!
	private $bShowValuesOnReloadForm = true; //voor bijvoorbeeld wachtwoorden is het niet wenselijk om deze bij een reload van het formulier weer te geven
	private $iAllowHTML = FILTERXSS_ALLOW_HTML_NONE; //default no HTML allowed
	private $bAutoFocus = false;
	// protected $bIsArray = false; //is this an array of fields? 

	protected $sWhitelist = WHITELIST_SAFE; //character whitelist of characters that are allowed. if empty, it is disabled
	protected $sBlacklist = ''; //character blacklist of characters that are NOT allowed. if empty, it is disabled

	const WHITELIST_DISABLED = '';//empty value disables whitelist
	const BLACKLIST_DISABLED = '';//empty value disables blacklist
	// const WHITELIST_SAFE = WHITELIST_SAFE;
	// const WHITELIST_URL = WHITELIST_URL;//safe whitelist for urls
	// const WHITELIST_URLSLUG = WHITELIST_URLSLUG;//safe whitelist for url slugs => those are almost the same but can't have / and :
	// const WHITELIST_EMAIL = WHITELIST_EMAIL;//safe whitelist for email addresses
	// const WHITELIST_HTML = WHITELIST_HTML;//whitelist for allowing html, but filters MultiByte injections (\)

	const GETVALUESUBMITTED_RETURN_RAW = 0; //unsafe
	const GETVALUESUBMITTED_RETURN_HTMLESCAPED = 1; //===> DEFAULT !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	const GETVALUESUBMITTED_RETURN_ALLOWHTMLMARKUP = 2;
	const GETVALUESUBMITTED_RETURN_FILTERXSS = 3;
	const GETVALUESUBMITTED_RETURN_ALLOWHTMLSVG = 4;


	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setIsArray($bIsArray);		
		// $this->objContentsInit = new FormInputContents($this);
		// $this->objContentsSubmitted = new FormInputContents($this);
		$this->objValidators = new TObjectList();

		
		// $this->bIsArray = $bIsArray;
	}

	public function  __destruct()
	{
		unset($this->objContentsInit);
		unset($this->objContentsSubmitted);
		unset($this->objValidators);
	}
        
	/**
	 * getting the name of this HTML tag
	 * example: frmContact in  <form name="frmContact">
	 *
	 * overloaded from parent
	 * 
	 * @return string
	 */
	public function getName($bWithParentheses = true)
	{
		// if ($bWithParentheses)
		// {
		// 	if ($this->bIsArray)
		// 	{
		// 		return parent::getName().'[]';		
		// 	}
		// }

		return parent::getName();
	}
	
	/**
	 * when form loads the field will be automatically focussed
	 * 
	 * @param boolean $bAutoFocus
	 */
	public function setAutofocus($bAutoFocus = true)
	{
		$this->bAutoFocus = $bAutoFocus;
	}
	
	/**
	 * when form loads the field will be automatically focussed
	 *
	 * @param boolean $bAutoFocus
	 */	
	public function getAutofocus()
	{
		return $this->bAutoFocus;
	}
	
	/**
	 * set if field is allowed to contain html code
	 * (for preventing cross site scripting)
	 * 
	 * @param int $iAllowHTML  i.e. FILTERXSS_ALLOW_HTML_NONE 
	 */
	// public function setAllowHTML($iAllowHTML = FILTERXSS_ALLOW_HTML_NONE)
	// {
	// 	$this->iAllowHTML = $iAllowHTML;
	// }
	
	
	/**
	 * get if field is allowed to contain html code
	 * (for preventing cross site scripting)
	 *
	 * @return int i.e. FILTERXSS_ALLOW_HTML_NONE 
	 */	
	// public function getAllowHTML()
	// {
	// 	return $this->iAllowHTML;
	// }
	
	/**
	 * set if values are displayed on reload form
	 * (voor wachtwoorden is het bijvoorbeeld onwenselijk om deze opnieuw weer te geven omdat ze dat plat in de html tekst staan)
	 *
	 * @param boolean $bDontShow
	 */
	public function setShowValuesOnReloadForm($bShowValues)
	{
		$this->bShowValuesOnReloadForm = $bShowValues;
	}

	/**
	 * get if values are displayed on reload form
	 * (voor wachtwoorden is het bijvoorbeeld onwenselijk om deze opnieuw weer te geven omdat ze dat plat in de html tekst staan)
	 *
	 * @return boolean
	 */
	public function getShowValuesOnReloadForm()
	{
		return $this->bShowValuesOnReloadForm;
	}

                

	/**
	 * getting the contents object
	 * this object contains the initial values form the form
	 * 
     * @deprecated
	 * @return FormInputContents
	 */
	/*
	public function getContentsInit()
	{
		return $this->objContentsInit;
	}
		*/

	/**
	 * getting the contents object
	 * this object contains the values when the form is submitted
	 * 
	 * 
	 * @deprecated replacement : getValueSubmitted() which purely returns the value
	 * @param $sFormMethod read the $_POST or $_GET array? use constant Form::METHOD_POST
	 * @return FormInputContents
	 */
	/*
	public function getContentsSubmitted($sFormMethod = Form::METHOD_POST)
	{
		$sFieldName = '';
		$sFieldName = $this->getName(false); //we want the name without parentheses
		

		//read the values from the proper array
		if ($sFormMethod == Form::METHOD_POST)
		{
			if (isset($_POST[$sFieldName]))  //only when exists at all
				$this->objContentsSubmitted->setValue(filterXSS($_POST[$sFieldName], $this->getAllowHTML()));

			if (isset($_FILES[$sFieldName])) //only when exists at all
				$this->objContentsSubmitted->setFileArray($_FILES[$sFieldName]);
		}
		elseif ($sFormMethod == Form::METHOD_GET)
		{
			if (isset($_GET[$sFieldName]))
				$this->objContentsSubmitted->setValue(filterXSS($_GET[$sFieldName], $this->getAllowHTML()));
		}

		return $this->objContentsSubmitted;
	}
	*/

	/**
	 * This function returns the (string) value of $_GET, $_POST or $_FILES 
	 * it defaults to returning a value escaped with htmlspecialchars()
	 * 
	 * replacement for getContentsSubmitted() which returns a superfluous object
	 * 
	 * WARNING: 
	 * the value is filtered by iReturnValueFilter paramater AND the blacklist AND the whitelist
	 * 
	 * @param string $sFormMethod POST or GET?
	 * @param int $iReturnValueFilter how to return FormInputAbstract::GETVALUESUBMITTED_RETURN_HTMLESCAPED
	 * @return mixed string or binary ($_FILES)
	 */
	public function getValueSubmitted($sFormMethod = Form::METHOD_POST, $iReturnValueFilter = FormInputAbstract::GETVALUESUBMITTED_RETURN_HTMLESCAPED)
	{
		$sFieldName = '';
		$sFieldName = $this->getName(false); //we want the name without parentheses
		
		//filter whitelist
		if ($this->sWhitelist !== '') //enabled whitelist
		{
			if ($sFormMethod == Form::METHOD_POST)
			{
				if (isset($_POST[$sFieldName])) 
				{
					$sBefore = $_POST[$sFieldName];
					$_POST[$sFieldName] = filterBadCharsWhiteListLiteral($_POST[$sFieldName], $this->sWhitelist);
					$sAfter = $_POST[$sFieldName];

					if ($sBefore !== $sAfter)
					{
						logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'filtered: BEFORE:'.$sBefore);
						logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'filtered: AFTER:'.$sAfter);
					}
				}
			}
			elseif ($sFormMethod == Form::METHOD_GET)
			{
				if (isset($_GET[$sFieldName])) 
					$_GET[$sFieldName] = filterBadCharsWhiteListLiteral($_GET[$sFieldName], $this->sWhitelist);				
			}

		}

		//filter blacklist
		if ($this->sBlacklist !== '') //enabled blacklist
		{
			if ($sFormMethod == Form::METHOD_POST)
			{
				if (isset($_POST[$sFieldName]))
					$_POST[$sFieldName] = filterBadCharsBlackList($_POST[$sFieldName], $this->sWhitelist);
			}
			elseif ($sFormMethod == Form::METHOD_GET)
			{
				if (isset($_GET[$sFieldName]))
					$_GET[$sFieldName] = filterBadCharsBlackList($_GET[$sFieldName], $this->sWhitelist);				
			}
		}

		//read the values from the proper array
		if ($sFormMethod == Form::METHOD_POST)
		{
			if (isset($_POST[$sFieldName]))  //only when exists at all
			{
				switch ($iReturnValueFilter)
				{
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW:
						return $_POST[$sFieldName];
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_ALLOWHTMLMARKUP:
						return filterXSS($_POST[$sFieldName], FILTERXSS_ALLOW_HTML_MARKUP);
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_FILTERXSS:
						return filterXSS($_POST[$sFieldName], FILTERXSS_ALLOW_HTML_FILTEREDXSS);
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_ALLOWHTMLSVG:
						return purifyHTMLSVG($_POST[$sFieldName]);
					default: // defaults to FormInputAbstract::GETVALUESUBMITTED_RETURN_HTMLESCAPED
						return htmlspecialchars($_POST[$sFieldName]);
				}				
			}

			if (isset($_FILES[$sFieldName])) //only when exists at all
				return $_FILES[$sFieldName]; //not a string to return
		}
		elseif ($sFormMethod == Form::METHOD_GET)
		{
			if (isset($_GET[$sFieldName]))
			{
				switch ($iReturnValueFilter)
				{
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_RAW:
						return $_GET[$sFieldName];
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_ALLOWHTMLMARKUP:
						return filterXSS($_GET[$sFieldName], FILTERXSS_ALLOW_HTML_MARKUP);
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_FILTERXSS:
						return filterXSS($_GET[$sFieldName], FILTERXSS_ALLOW_HTML_FILTEREDXSS);
					case FormInputAbstract::GETVALUESUBMITTED_RETURN_ALLOWHTMLSVG:
						return purifyHTMLSVG($_GET[$sFieldName]);
					default: // defaults to FormInputAbstract::GETVALUESUBMITTED_RETURN_HTMLESCAPED
						return htmlspecialchars($_GET[$sFieldName]);
				}	
			}
				
		}

		return null;		
	}


	/**
	 * This function returns the casted int value of the $_GET, $_POST 
	 * $_FILES returns 0
	 * 
	 * @return int
	 */
	public function getValueSubmittedAsInt($sFormMethod = Form::METHOD_POST)
	{
		$sFieldName = '';
		$sFieldName = $this->getName(false); //we want the name without parentheses
		

		//read the values from the proper array
		if ($sFormMethod == Form::METHOD_POST)
		{
			if (isset($_POST[$sFieldName]))  //only when exists at all
				return strToInt($_POST[$sFieldName]);

			if (isset($_FILES[$sFieldName])) //only when exists at all
				return 0;//can't exist because it is binary instead of int, so return 0
		}
		elseif ($sFormMethod == Form::METHOD_GET)
		{
			if (isset($_GET[$sFieldName]))
				return strToInt($_GET[$sFieldName]);
		}

		return 0;		
	}	

	/**
	 * This function returns the casted int value of the $_GET, $_POST 
	 * $_FILES returns 0
	 * 
	 * @return int
	 */
	public function getValueSubmittedAsBool($sFormMethod = Form::METHOD_POST)
	{
		return intToBool($this->getValueSubmittedAsInt($sFormMethod));
	}		

	/**
	 * This function returns a TDateTime() object, even if input == ""
	 * The value that is read from $_POST or $_GET in ISO8601 format 
	 * (see: https://en.wikipedia.org/wiki/ISO_8601)
	 * 
	 * @return TDateTime
	 */
	public function getValueSubmittedAsTDateTimeISO($sFormMethod = Form::METHOD_POST)
	{
		$objDateTime = new TDateTime();
		$objDateTime->setZero();
		$sFieldName = '';
		$sFieldName = $this->getName(false); //we want the name without parentheses
		
		//read the values from the proper array
		if ($sFormMethod == Form::METHOD_POST)
		{
			if (isset($_POST[$sFieldName]))  //only when exists at all
			{
				if ($_POST[$sFieldName] != "")
					$objDateTime->setISOString($_POST[$sFieldName]);
				return $objDateTime;
			}

			if (isset($_FILES[$sFieldName])) //only when exists at all
				return $objDateTime;//can't exist because it is binary instead of int, so return 0
		}
		elseif ($sFormMethod == Form::METHOD_GET)
		{
			if (isset($_GET[$sFieldName]))
			{
				if ($_GET[$sFieldName] != "")
					$objDateTime->setISOString($_GET[$sFieldName]);
				return $objDateTime;
			}
		}

		return $objDateTime; //should not be able to get here	
	}	

	/**
	 * This function returns a TDecimal() object, even if input == ""
	 * 
	 * @return TDecimal
	 */
	public function getValueSubmittedAsTDecimal($sFormMethod = Form::METHOD_POST, $iPrecision = 0)
	{
		$objDecimal = new TDecimal('0.0', $iPrecision);
		$sFieldName = '';
		$sFieldName = $this->getName(false); //we want the name without parentheses

		if ($sFormMethod == Form::METHOD_POST)
		{
			if (isset($_POST[$sFieldName]))  //only when exists at all
			{
				if ($_POST[$sFieldName] != "")
					$objDecimal->setValue($_POST[$sFieldName]);
				return $objDecimal;
			}

			if (isset($_FILES[$sFieldName])) //only when exists at all
			{
				if ($_FILES[$sFieldName] != "")
					$objDecimal->setValue($_POST[$sFieldName]);
				return $objDecimal;
			}
		}
		elseif ($sFormMethod == Form::METHOD_GET)
		{
			if (isset($_GET[$sFieldName]))
			{
				if ($_GET[$sFieldName] != "")
					$objDecimal->setValue($_GET[$sFieldName]);
				return $objDecimal;
			}
		}

		return $objDecimal; //should not be able to get here	
	}		

	/**
	 * This function returns if the $_GET or $_POST post is empty
	 * 
	 * @return int
	 */
	public function getValueSubmittedEmpty($sFormMethod = Form::METHOD_POST)
	{
		$sValue = '';
		$sValue = $this->getValueSubmitted($sFormMethod);

		return (strlen($sValue) == 0);
	}			

	/**
	 * add a formvalidator object to this element.
	 * When submitting the form, the form generator (FormGenerator)
	 * checks the content of the input value by requesting the added validators.
	 * 
	 * bare in mind that standard no html is allowed
	 *
	 * @param ValidatorAbstract $objValidator validator object
	 */
	public function addValidator(ValidatorAbstract $objValidator)
	{
		$this->objValidators->add($objValidator);
	}

	/**
	 * return the number of added validators
	 *
	 * @return int
	 */
	public function countValidators()
	{
		return $this->objValidators->count();
	}

	/**
	 * requesting a validator by supplying the index of the added validator
	 *
	 * @param int $iValidatorIndex index of the validator
	 * @return ValidatorAbstract
	 */
	public function getValidator($iValidatorIndex)
	{
		return $this->objValidators->get($iValidatorIndex);
	}

	/**
	 * set object disabled (or not)
	 *
	 * @param bool $bDisabled
	 */
	public function setDisabled($bDisabled)
	{
		if (is_bool($bDisabled))
		{
			$this->bDisabled = $bDisabled;
		}
	}

	/**
	 * is object disabled ?
	 *
	 * @return bool
	 */
	public function getDisabled()
	{
		return $this->bDisabled;
	}

	/**
	 * set object read only (or not)
	 *
	 * @param bool $bReadOnly
	 */
	public function setReadOnly($bReadOnly)
	{
		if (is_bool($bReadOnly))
		{
			$this->bReadOnly = $bReadOnly;
		}
	}

	/**
	 * is object read only
	 *
	 * @return bool
	 */
	public function getReadOnly()
	{
		return $this->bReadOnly;
	}

	/**
	 * setting the required property for the HTML tag
	 * bijvoorbeeld:  <input type="text" required="required">
	 *
	 * @param boolean $bRequired
	 */
	public function setRequired($bRequired)
	{
		$this->bRequired = $bRequired;
	}

	/**
	 * getting the required property of this HTML tag
	 * bijvoorbeeld: <input type="text" required="required">
	 *
	 * @return bool
	 */
	public function getRequired()
	{
		return $this->bRequired;
	}

	/**
	 * specific attributes for this element
	 * @param DOMElement $objXMLElement
	 */
// 	public function getXMLNodeSpecificToNode_OLD(DOMElement $objXMLElement)
// 	{
// 		//required toevoegen
// 		if ($this->getRequired() == true)
// 			$objXMLElement->setAttribute('required', 'required');

// 		//readonly toevoegen
// 		if ($this->getRequired() == true)
// 			$objXMLElement->setAttribute('readonly', 'readonly');

// 		//disabled toevoegen
// 		if ($this->getRequired() == true)
// 			$objXMLElement->setAttribute('disabled', 'disabled');


// 	}


	protected function renderChild()
	{
		$sAttributes = '';

		//required toevoegen
		if ($this->getRequired())
			$sAttributes .= $this->addAttributeToHTML('required', 'required');

		//readonly toevoegen
		if ($this->getReadOnly())
			$sAttributes .= $this->addAttributeToHTML('readonly', 'readonly');

		//disabled toevoegen
		if ($this->getDisabled())
			$sAttributes .= $this->addAttributeToHTML('disabled', 'disabled');
		
		//autofocus toevoegen
		if ($this->getAutofocus())
			$sAttributes .= $this->addAttributeToHTML('autofocus', 'true');
					
		 

		return $sAttributes;
	}

	/**
	 * set blacklist characters
	 * to disable blacklist just set empty string setBlacklist('') 
	 */
	public function setBlacklist($sCharactersOnBlacklist)
	{
		$this->sBlacklist = $sCharactersOnBlacklist;
	}

	/**
	 * get blacklist characters
	 * if returns empty string, the blacklist is disabled
	 * 
	 * @return string
	 */
	public function getBlacklist()
	{
		return $this->sBlacklist;
	}

	/**
	 * set whitelist characters
	 * to disable whitelist just set empty string setWhitelist('') 
	 */
	public function setWhitelist($sWhitelistCharacters)
	{
		$this->sWhitelist = $sWhitelistCharacters;
	}

	/**
	 * get whitelist characters
	 * if returns empty string, the whitelist is disabled
	 * 
	 * @return string
	 */
	public function getWhitelist()
	{
		return $this->sWhitelist;
	}	
}


?>