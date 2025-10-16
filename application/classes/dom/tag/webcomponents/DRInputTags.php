<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDecimal;

/**
 * represents a <dr-input-tags>
 * part of PHP counterpart for web component <dr-input-tags>
 * 
 * WARNING:
 * 
 * @author Dennis Renirie
 * 15 okt 2025: DRInputTags created
 *
 */
class DRInputTags extends FormInputAbstract
{

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-input-tags');

		// $this->setAttribute('disabled', ''); 
		// $this->setAttribute('value', ''); 
		$this->setAttribute('whitelist', WHITELIST_SAFE); //assume safe whitelist as default

		//translations
		$this->setAttribute('placeholder', transg('dr-input-tags_placeholder', 'Input value and press Enter'));

		//proper includes
		// includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-tags'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-tags'.DIRECTORY_SEPARATOR.'dr-input-tags.js');
	}

	/**
	 * set disabled
	 */
	public function setDisabled($bDisabled)
	{
		if ($bDisabled)
			$this->setAttribute('disabled', '');
		else
			$this->removeAttribute('disabled');
	}

	/**
	 * get disabled
	 * 
	 * @return boolean
	 */
	public function getDisabled()
	{
		if ($this->hasAttribute('disabled'))
			return $this->getAttributeAsBool('disabled');
		else
			return false;
	}	

	/**
	 * set value with a string where the decimal separator is ALWAYS a dot (.)
	 */
	public function setValue($sValue)
	{
		$this->setAttribute('value', $sValue);
	}

	/**
	 * get value. the return value is a string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->getAttribute('value');
	}

	/**
	 * set placeholder text like: "select an item"
	 */
	public function setPlaceholder($sPlaceholder)
	{
		$this->setAttribute('placeholder', $sPlaceholder);
	}

	/**
	 * get placeholder text like "Select an item"
	 */
	public function getPlaceholder()
	{
		return $this->getAttribute('placeholder');
	}


	/**
	 * set maximum length
	 */
	public function setMaxLength($iValue)
	{
		$this->setAttribute('maxlength', $iValue);
	}

	/**
	 * get maximum length
	 * 
	 * @return integer
	 */
	public function getMaxLength()
	{
		return $this->getAttribute('maxlength');
	}	

	/**
	 * set minimum length
	 */
	public function setMinLength($iValue)
	{
		$this->setAttributeAsInt('minlength', $iValue);
	}

	/**
	 * get minimum length
	 * 
	 * @return integer
	 */
	public function getMinLength()
	{
		return $this->getAttributeAsInt('minlength');
	}	
	

	/**
	 * set show char
	 */
	public function setShowCharCounter($bShow)
	{
		if ($bShow)
			$this->setAttribute('charcounter', '');
		else
			$this->removeAttribute('charcounter');
	}

	/**
	 * get show charcounter
	 * 
	 * @return boolean
	 */
	public function getShowCharCounter()
	{
		if ($this->hasAttribute('charcounter'))
			return $this->getAttributeAsBool('charcounter');
		else
			return false;
	}		

	// /**
	//  * set blacklist characters
	//  * empty string parameter disables the blacklist
	//  */
	// public function setBlacklist($sBlacklistCharacters)
	// {
	// 	$this->setAttribute('blacklist', $sBlacklistCharacters);
	// 	parent::setBlacklist($sBlacklistCharacters);
	// }

	// /**
	//  * get blacklist characters
	//  * if empty, blacklist is disabled
	//  * 
	//  * @return string
	//  */
	// public function getBlacklist()
	// {
	// 	return $this->getAttribute('blacklist');
	// }

	/**
	 * set whitelist characters
	 * empty string parameter disables the whitelist
	 */
	public function setWhitelist($sWhitelistCharacters)
	{
		$this->setAttribute('whitelist', str_replace('"', '&quot;', $sWhitelistCharacters));
		// $this->setAttribute('whitelist', $sWhitelistCharacters);
		parent::setWhitelist($sWhitelistCharacters);
	}

	/**
	 * get whitelist characters
	 * if empty, whitelist is disabled
	 * 
	 * @return string
	 */
	public function getWhitelist()
	{
		return $this->getAttribute('whitelist');
	}


}

?>
