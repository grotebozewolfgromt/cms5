<?php
namespace dr\classes\dom\tag;

class Svg extends HTMLTag
{
        
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('svg');
	}


}
?>