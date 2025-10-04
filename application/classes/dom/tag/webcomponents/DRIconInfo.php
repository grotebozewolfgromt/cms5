<?php
namespace dr\classes\dom\tag\webcomponents;


use dr\classes\dom\tag\HTMLTag;


/**
 * represents a <dr-icon-info>
 * part of PHP counterpart for web component <dr-icon-info>
 * 
 * 
 * @author Dennis Renirie
 * 15 aug 2025: DRIconInfo created
 *
 */
class DRIconInfo extends HTMLTag
{

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-icon-info');

		//proper includes
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js');
	}

	/**
	 * alias for setInnerHTML
	 */
	public function setInfo($sInfoHTML)
	{
		$this->setInnerHTML($sInfoHTML);
	}
}

?>
