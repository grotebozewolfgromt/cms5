<?php
namespace dr\classes\dom\tag\form;


/**
 * <input type="radio">
 *
 * LET OP: getLabel/setLabel genereert GEEN resultaat.
 * Dit zijn hulpvariabelen voor het generen een formulier.
 * Wil je een label hebben voor dit object, zul je deze zelf handmatig aan moeten maken
 *
 *     $objHidden = new InputRadio();
 $objHidden->setName('edtRadio');
 $objHidden->setID('edtRadio1');
 $objHidden->setValue(100);
 $objForm->appendChild($objHidden);
 */
class InputRadio extends InputAbstract
{
	private $sLabel = '';
	private $bChecked = false;

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('radio');
	}

	/**
	 * getting the text behind a radiobox
	 * You have to create the label object manually!!!
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->sLabel();
	}

	/**
	 * setting the text behind a radiobox
	 * You have to create the label object manually!!!
	 *
	 * @param string $sText
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
	 * @return <type>
	 */
	public function getChecked()
	{
		return $this->bChecked;
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