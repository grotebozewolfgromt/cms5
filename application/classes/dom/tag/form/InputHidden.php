<?php
namespace dr\classes\dom\tag\form;

/**
 * <input type="hidden">
 *
 *     $objHidden = new InputHidden();
 $objHidden->setName('edtHidden');
 $objHidden->setValue(100);
 $objForm->appendChild($objHidden);
 */
class InputHidden extends InputAbstract
{
	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('hidden');
	}



	public function renderHTMLNodeSpecificToInputType()
	{

	}
}

?>