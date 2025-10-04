<?php
namespace dr\classes\dom\tag\form;

class InputRange extends InputNumber
{
	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('range');
	}

}

?>