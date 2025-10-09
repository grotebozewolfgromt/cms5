<?php
namespace dr\classes\dom\tag\form;

class InputColor extends InputText
{
	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('color');
	}

}
?>