<?php
namespace dr\classes\dom\tag\form;

/**
 * <input type="submit">
 *
 *     $objSubmit = new InputSubmit();
 $objSubmit->setName('btnSubmit');
 $objSubmit->setValue('verstuur de hele rommel');
 $objForm->appendChild($objSubmit);
 */
class InputSubmit extends InputAbstract
{

        
	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('submit');
	}
        
        

	public function renderHTMLNodeSpecificToInputType()
	{
//		$sAttributes = '';
//
//
//		return $sAttributes;
	}

}
?>