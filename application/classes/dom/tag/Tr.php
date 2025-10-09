<?php
namespace dr\classes\dom\tag;

/**
 * <tr></tr>
 */
class Tr extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('tr');
	}


}

?>