<?php
namespace dr\classes\dom\tag;

use dr\classes\dom\tag\Text;

/**
 * <li></li>
 */
class Li extends HTMLTag
{

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('li');

		//you probably wanna have some text in the list item
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