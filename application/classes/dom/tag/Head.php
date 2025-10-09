<?php
namespace dr\classes\dom\tag;

/**
 * <head></head>
 */
class Head extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('head');

		//according to the w3c standards: every head tag needs to have a title tag,
		//so we create that automatically for you:
		$objTitle = new title($objParentNode);
		$this->appendChild($objTitle);
	}

	/**
	 * verkrijg title object
	 *
	 * @return title
	 */
	public function getTitle()
	{
		return $this->getChildNode(0);
	}

}

?>