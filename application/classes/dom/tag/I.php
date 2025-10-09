<?php
namespace dr\classes\dom\tag;

use dr\classes\dom\tag\Text;

class I extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('i');

		//you probably wanna have some text in the i tag
		//so we create that automatically for you:
		$objText = new Text($objParentNode);
		$this->appendChild($objText);
	}

	public function setText($sText)
	{
		$this->getChildNode(0)->setText($sText);
	}

	public function getText()
	{
		return $this->getChildNode(0)->getText();
	}


}


?>