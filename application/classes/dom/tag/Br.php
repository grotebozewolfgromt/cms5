<?php
namespace dr\classes\dom\tag;


/**

 */
class Br extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('br');

	}


	
	protected function renderChild()
	{
		$sAttributes = '';
	
	
		return $sAttributes;
	}	
}

?>
