<?php
namespace dr\classes\dom\tag;

/**
 * <title></title>
 */
class Title extends HTMLTag
{
	private $sTitle;

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('title');
	}

	/**
	 * setting the text in the title tag
	 *
	 * @param string $sTitle
	 */
	public function setTitle($sTitle)
	{
		$this->sTitle = $sTitle;
	}

	/**
	 * getting the title tag
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->sTitle;
	}

	protected function renderChild()
	{
		$this->addText($this->getTitle());
	}

}

?>