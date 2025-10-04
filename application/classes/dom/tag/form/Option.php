<?php
namespace dr\classes\dom\tag\form;

use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\HTMLTag;

/**
 * hulpklasse voor de select tag
 *
 * <select>
 *  <option>optie1</optio>
 *  <option>optie2</option>
 * </select>
 * 
 * 11 mrt 2022: form\Option added disabled field
 * 10 mei 2024: form\Option value="" will always render regardless if empty
 */
class Option extends HTMLTag
{
	private $sValue = '';
	private $bSelected = false;
	private $bDisabled = false;	

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setTagName('option');

        $this->setSourceFormattingIdentForOpenTag(true);
        $this->setSourceFormattingIdentForCloseTag(false);
        $this->setSourceFormattingNewLineAfterOpenTag(false);
        $this->setSourceFormattingNewLineAfterCloseTag(true);

		//according to the w3c standards: every option needs text
		//so we create that automatically for you:
		$objText = new Text();
		$this->appendChild($objText);
	}

	/**
	 * setting input value
	 * @param string $sType
	 */
	public function setValue($sValue)
	{
		$this->sValue = $sValue;
	}

	/**
	 * get input value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->sValue;
	}


	/**
	 * gets if option is selected in select-box
	 * @return bool
	 */
	public function getSelected()
	{
		return $this->bSelected;
	}

	/**
	 * set if option is selected in select box
	 * @param bool $bSelected
	 */
	public function setSelected($bSelected)
	{
		if (is_bool($bSelected))
		{
			$this->bSelected = $bSelected;
		}
	}

	/**
	 * getting the text of a text area
	 * @return string
	 */
	public function getText()
	{
		return $this->getChildNode(0)->getText();
	}

	/**
	 * setting the text of a text area
	 * @param string $sText
	 */
	public function setText($sText)
	{
		$this->getChildNode(0)->setText($sText);
	}

// 	public function getXMLNodeSpecificToNode_OLD(DOMElement $objXMLElement)
// 	{
// 		//value toevoegen
// 		if (strlen($this->getValue()) > 0 )
// 			$objXMLElement->setAttribute('value', $this->getValue());

// 		//selected ?
// 		if ($this->getSelected())
// 			$objXMLElement->setAttribute('selected', 'true');
// 	}

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

	protected function renderChild()
	{
		$sAttributes = '';

		//selected ?
		if ($this->getSelected())
			$sAttributes .= $this->addAttributeToHTML('selected', 'true');

		//disabled toevoegen
		if ($this->getDisabled())
			$sAttributes .= $this->addAttributeToHTML('disabled', 'disabled');

		$sAttributes .= $this->addAttributeToHTML('value', $this->getValue(), true);

		return $sAttributes;
	}

}

?>