<?php
namespace dr\classes\dom\tag;

/**
 * <ol></ol>
 */
class Ol extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('ol');
	}

	
	public function addListItem($sText)
	{
		$objLI = new THTMLCodeGen_li($this->objParentNode);
		$objLI->setText($sText);
		$this->appendChild($objLI);
	}
}
?>