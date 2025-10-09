<?php
namespace dr\classes\dom\tag;

/**
 * <ul></ul>
 */
class Ul extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('ul');
	}

	
	public function addListItem($sText)
	{
		$objLI = new Li($this->objParentNode);
		$objLI->setText($sText);
		$this->appendChild($objLI);
	}
}
?>