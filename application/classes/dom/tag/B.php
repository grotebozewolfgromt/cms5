<?php
namespace dr\classes\dom\tag;

use dr\classes\dom\tag\Text;

class B extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('b');

		//you probably wanna have some text in the b tag
		//so we create that automatically for you:
		$objText = new Text();
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