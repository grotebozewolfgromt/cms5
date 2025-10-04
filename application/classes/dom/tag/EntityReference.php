<?php
namespace dr\classes\dom\tag;


/**
 * klasse voor speciale html karakters, zoals &nbsp; etc. Deze worden door de standaard xml klassen automatisch ge-escaped
 *
 * LET OP: De klasse is anders dan andere!
 *
 *
 */
class EntityReference extends HTMLTag
{
	private $sText = '';

	/**
	 * setting the text for the textnode
	 *
	 * @param string $sText
	 */
	public function setEntity($sText)
	{
		$this->sText = $sText;
	}

	/**
	 * getting the text for the text node
	 *
	 * @return string
	 */
	public function getEntity()
	{
		return $this->sText;
	}



}


?>