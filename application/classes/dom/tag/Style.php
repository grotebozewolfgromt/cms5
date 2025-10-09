<?php
namespace dr\classes\dom\tag;

use dr\classes\dom\tag\Text;

/**
 * <style></style>
 */
class Style extends HTMLTag
{
	private $sType = 'text/css';
	private $sMedia = '';

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('style');

		//this tag is only usefull when adding stylesheet information,
		//so we create that automatically for you:
		$objStylesheet = new Text($objParentNode);
		$this->appendChild($objStylesheet);
	}

	/**
	 * setting the type in the head->style tag
	 *
	 * @param string $sType
	 */
	public function setType($sType = 'text/css')
	{
		$this->sType = $sType;
	}

	/**
	 * getting the type in the head->style tag
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->sType;
	}

	/**
	 * setting the media in the head->style tag
	 *
	 * @param string $sMedia
	 */
	public function setMedia($sMedia)
	{
		$this->sMedia = $sMedia;
	}

	/**
	 * getting the media in the head->style tag
	 *
	 * @return string
	 */
	public function getMedia()
	{
		return $this->sMedia;
	}

	/**
	 * setting the stylesheet info
	 *
	 * @param string $sStylesheet
	 */
	public function setStylesheet($sStylesheet)
	{
		$this->getChildNode(0)->setText($sStylesheet);
	}

	/**
	 * getting the stylesheet info
	 *
	 * @return string
	 */
	public function getStylesheet()
	{
		return $this->getChildNode(0)->getText();
	}



	protected function renderChild()
	{
		$sAttributes = '';

		$sAttributes .= $this->addAttributeToHTML('type', $this->getType());
		$sAttributes .= $this->addAttributeToHTML('media', $this->getMedia());

		return $sAttributes;
	}
}


?>
