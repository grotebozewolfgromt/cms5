<?php
namespace dr\classes\dom\tag;


class Img extends HTMLTag
{
	private $sSrc = '';
	private $sAlt = '';

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('img');

		$this->setSourceFormattingIdentForCloseTag(false);
		$this->setSourceFormattingNewLineAfterOpenTag(false);
		$this->setSourceFormattingNewLineAfterCloseTag(false);
		$this->setSourceFormattingIdentForOpenTag(false);
	}

	public function setSrc($sText)
	{
		$this->sSrc = $sText;
	}

	public function getSrc()
	{
		return $this->sSrc;
	}

	public function setAlt($sText)
	{
		$this->sAlt = $sText;
	}

	public function getAlt()
	{
		return $this->sAlt;
	}



	protected function renderChild()
	{
		$sAttributes = '';

		$sAttributes .= $this->addAttributeToHTML('src', $this->getSrc(), true); //verplichte attributen
		$sAttributes .= $this->addAttributeToHTML('alt', $this->getAlt(), true); //verplicht

		return $sAttributes;

	}
}


?>