<?php
namespace dr\classes\dom\tag;


/**
 * <a></a>
 *
 *     $objA = new a();
 * $objA->setHref('http://www.av.com');
 * $objA->setTarget('_blank');
 * $objA->setText('ga naar google');
 * 
 * @author Dennis Renirie
 * 17 apr 2024 updated to attribute method
 *
 */
class A extends HTMLTag
{
	const TARGET_BLANK = '_blank';

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('a');

		//you probably wanna have some text in the a tag
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

	public function setHref($sHref)
	{
		$this->setAttribute('href', $sHref);
	}

	public function getHref()
	{
		return $this->getAttribute('href');
	}

	public function setTarget($sTarget = A::TARGET_BLANK)
	{
		$this->setAttribute('target', $sTarget);
	}

	public function getTarget()
	{
		return $this->getAttribute('target');
	}

	public function setTitle($sTitle)
	{
		$this->setAttribute('title', $sTitle);
	}

	public function getTitle()
	{
		return $this->getAttribute('title');
	}

}

?>
