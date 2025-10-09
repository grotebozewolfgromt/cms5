<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDecimal;

/**
 * represents a <dr-input-number>
 * part of PHP counterpart for web component <dr-input-number>
 * 
 * 
 * @author Dennis Renirie
 * 1 may 2025: DRInputNumber created
 *
 */
class DRInputNumber extends FormInputAbstract
{
	const TYPE_PLAIN = 'plain';//plain has no buttons on the sides;
	const TYPE_BUTTONS = 'buttons';//has + and - buttons on both sides


	public function __construct($objParentNode = null)
	{
		global $objLocalisation;

		parent::__construct($objParentNode);
		$this->setTagName('dr-input-number');

		// $this->setAttribute('disabled', ''); 
		$this->setAttribute('value', '0.0'); 
		$this->setAttribute('precision', 0); //decimal precision. 4 = 4 digits after the decimal separator
		$this->setAttribute('decimalseparator', $objLocalisation->getSetting(TLocalisation::SEPARATOR_DECIMAL)); 
		$this->setAttribute('thousandseparator', $objLocalisation->getSetting(TLocalisation::SEPARATOR_THOUSAND)); 
		$this->setAttributeAsInt('padzero', 0); //pad out how many zeros at the end of the string? This is useful with money: value 6 that pads out 2 zeros to make 6.00
		$this->setAttribute('type', DRInputNumber::TYPE_PLAIN); //type of textbox
		$this->setAttribute('min', '0.0'); //minimum value
		$this->setAttribute('max', '0.0'); //maximum value
		$this->setAttributeAsBool('zerotrashcan', false); //show trashcan in minus-button when value == 1 (in other words: press to remove, like in a webshop)


		//proper includes
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-number'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-number'.DIRECTORY_SEPARATOR.'dr-input-number.js');
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
	 * set decimal precision
	 */
	public function setPrecision($iPrecision)
	{
		$this->setAttributeAsInt('precision', $iPrecision);
	}

	/**
	 * get decimal precision
	 */
	public function getPrecision()
	{
		return $this->getAttributeAsInt('precision');
	}

	/**
	 * set decimal precision
	 * placeholder function for setPrecision
	 */
	public function setDecimalPrecision($iPrecision)
	{
		$this->setPrecision($iPrecision);
	}

	/**
	 * get decimal precision
	 * placeholder function for getPrecision
	 */
	public function getDecimalPrecision()
	{
		return $this->getPrecision();
	}

	/**
	 * set decimal separator
	 */
	public function setDecimalSeparator($sSeparator = '.')
	{
		$this->setAttribute('decimalseparator', $sSeparator);
	}

	/**
	 * get thousand separator
	 */
	public function getThousandSeparator()
	{
		return $this->getAttribute('thousandseparator');
	}

	/**
	 * set thousand separator
	 */
	public function setThousandSeparator($sSeparator = ',')
	{
		$this->setAttribute('thousandseparator', $sSeparator);
	}

	/**
	 * get decimal separator
	 */
	public function getDecimalSeparator()
	{
		return $this->getAttribute('decimalseparator');
	}

	/**
	 * set pad zero.
	 * by padding zeros: you make 6.00 from value 6.
	 * The amount of zeros is determined by the decimal precision
	 * 
	 * @param integer $iNumberOfZerosAfterDecimalSeparator
	 */
	public function setPadZero($iNumberOfZerosAfterDecimalSeparator)
	{
		$this->setAttributeAsInt('padzero', $iNumberOfZerosAfterDecimalSeparator);
	}

	/**
	 * get pad zero.
	 * by padding zeros: you make 6.00 from value 6.
	 * The amount of zeros is determined by the decimal precision
	 * 
	 * @return boolean
	 */
	public function getPadZero()
	{
		return $this->getAttributeAsInt('padzero');
	}		


	/**
	 * set type: DRInputNumber::TYPE_PLAIN or DRInputNumber::TYPE_BUTTONS
	 */
	public function setType($sType = DRInputNumber::TYPE_PLAIN)
	{
		$this->setAttribute('type', $sType);
	}

	/**
	 * get type: DRInputNumber::TYPE_PLAIN or DRInputNumber::TYPE_BUTTONS
	 */
	public function getType()
	{
		return $this->getAttribute('type');
	}	

	/**
	 * set minimum value that is allowed.
	 * This value is set as string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @param string $sMinValue
	 */
	public function setMin($sMinValue)
	{
		$this->setAttribute('min', $sMinValue);
	}

	/**
	 * get minimum value that is allowed.
	 * This value is a string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @return string
	 */
	public function getMin()
	{
		return $this->getAttribute('min');
	}	

	/**
	 * set maximum value that is allowed.
	 * This value is set as string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @param string $sMaxValue
	 */
	public function setMax($sMaxValue)
	{
		$this->setAttribute('max', $sMaxValue);
	}

	/**
	 * get maximum value that is allowed.
	 * This value is a string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @return string
	 */
	public function getMax()
	{
		return $this->getAttribute('max');
	}		

	/**
	 * set if minus icon becomes a trashcan when value == 1
	 * (useful for carts in webshops)
	 * 
	 * @param boolean $bTrashcan
	 */
	public function setZeroTrashcan($bTrashcan)
	{
		if ($bTrashcan)
			$this->setAttribute('zerotrashcan', '');
		else
			$this->removeAttribute('zerotrashcan');
	}

	/**
	 * get if minus icon becomes a trashcan when value == 1
	 * (useful for carts in webshops)
	 *
	 * @return boolean
	 */
	public function getZeroTrashcan()
	{
		if ($this->hasAttribute('zerotrashcan'))
			return $this->getAttributeAsBool('zerotrashcan');
		else
			return false;
	}		

	/**
	 * set value as TDecimal object
	 */
	public function setValueAsTDecimal($objDecimal)
	{
		if ($objDecimal == null)
		{
			$this->setAttribute('value', '0.0');
		}
		else
		{
			$this->setAttribute('value', $objDecimal->getValue());
		}

	}

	/**
	 * get value as TDecimal
	 */
	public function getValueAsTDecimal()
	{
		$objDecimal = new TDecimal('0', $this->getPrecision());
		$objDecimal->setValue( $this->getAttribute('value'));
		return $objDecimal;
	}	
	
}

?>
