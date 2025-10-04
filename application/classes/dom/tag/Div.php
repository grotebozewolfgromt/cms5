<?php
namespace dr\classes\dom\tag;

class Div extends HTMLTag
{
    
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('div');
		$this->setSourceFormattingIdentForOpenTag(true);
		$this->setSourceFormattingIdentForCloseTag(false);
		$this->setSourceFormattingNewLineAfterOpenTag(true);
		$this->setSourceFormattingNewLineAfterCloseTag(true);
	}


}
?>