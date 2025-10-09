<?php
namespace dr\classes\dom\tag\form;


/**
 * <input type="text">
 *
 *     $objHidden = new InputText();
 $objHidden->setName('edtText');
 $objHidden->setValue(100);
 $objForm->appendChild($objHidden);
 */
class InputText extends InputAbstract
{
	private $sPlaceHolder = ''; //new in html5

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('text');
	}

	public function setPlaceholder($sPlaceholder)
	{
		$this->sPlaceHolder = $sPlaceholder;
	}

	public function getPlaceholder()
	{
		return $this->sPlaceHolder;
	}


	public function getXMLNodeSpecificToInputType_OLD(DOMElement $objXMLElement)
	{
	}

	public function renderHTMLNodeSpecificToInputType()
	{	
		$sAttributes = '';
		 
		$sAttributes .= $this->addAttributeToHTML('placeholder', $this->getPlaceholder());

		return $sAttributes;
	}

}

?>