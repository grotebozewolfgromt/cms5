<?php
namespace dr\classes\dom\tag\form;

class InputEmail extends InputText
{
	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setType('email');
	}

}
?>