<?php
namespace dr\classes\dom\tag;


/**
 * <html></html>
 */
class Html extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('html');

		//according to the w3c standards: every html tag needs to have a head,
		//so we create that automatically for you:
		$objHead = new head($objParentNode);
		$this->appendChild($objHead);

		//according to the w3c standards: every html tag needs to have a body,
		//so we create that automatically for you:
		$objBODY = new body($objParentNode);
		$this->appendChild($objBODY);
	}

	/**
	 * returns the head object of the html tag
	 *
	 * @return head head object
	 */
	public function getHead()
	{
		return $this->getChildNode(0);
	}

	/**
	 * returns the body object of the html tag
	 *
	 * @return body body object
	 */
	public function getBody()
	{
		return $this->getChildNode(1);
	}



	/**
	 * return HTML5 code as string
	 *
	 * @return string
	 */
	public function renderHTML()
	{
		$sHTML = '<!DOCTYPE html>'."\n";
		$sHTML .= $this->renderHTMLNode();

		return $sHTML;
	}

	/**
	 * save rendered result to file
	 * @param string $sFileName path of the file
	 * @return bool file save success ?
	 */
	public function saveToFile($sFileName)
	{
		$sRendered = $this->renderHTML();
		return saveToFileString($sRendered, $sFileName);
	}



}

?>