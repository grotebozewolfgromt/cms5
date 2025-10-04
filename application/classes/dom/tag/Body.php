<?php
namespace dr\classes\dom\tag;

/**
 * <body></body>
 */
class Body extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('body');
	}




}
?>