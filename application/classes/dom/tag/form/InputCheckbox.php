<?php
namespace dr\classes\dom\tag\form;

/**
 * <input type="checkbox">
 *
 * LET OP: getLabel/setLabel genereert GEEN resultaat.
 * Dit zijn hulpvariabelen voor het generen een formulier.
 * Wil je een label hebben voor dit object, zul je deze zelf handmatig aan moeten maken
 *
 $objHidden = new InputCheckbox();
 $objHidden->setName('edtCheckbox');
 $objHidden->setValue(100);
 $objForm->appendChild($objHidden);
 * *
 */
class InputCheckbox extends InputAbstract
{
	private $sLabel = '';
	private $bChecked = false;

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('checkbox');
		$this->setValue(1); //default
	}

	/**
	 * getting the text behind a checkbox
	 * You have to create the label object manually!!!
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->sLabel;
	}

	/**
	 * setting the text behind a checkbox
	 * You have to create the label object manually!!!
	 *
	 *  @param string $sText
	 */
	public function setLabel($sLabel)
	{
		$this->sLabel = $sLabel;
	}

	/**
	 * check the checkbox
	 *
	 * @param bool $bChecked
	 */
	public function setChecked($bChecked)
	{
		 
		if (is_bool($bChecked))
		{

			$this->bChecked = $bChecked;

		}
	}

	/**
	 * get if the checkbox is checked
	 *
	 * @return bool
	 */
	public function getChecked()
	{
		return $this->bChecked;
	}

	public function getXMLNodeSpecificToInputType_OLD(DOMElement $objXMLElement)
	{
		//checked toevoegen
		if ( $this->getChecked() )
			$objXMLElement->setAttribute('checked', 'yes');
	}

	public function renderHTMLNodeSpecificToInputType()
	{
		$sAttributes = '';

		if ( $this->getChecked() )
			$sAttributes .= $this->addAttributeToHTML('checked', 'yes');

		return $sAttributes;
		 
	}
}

?>