<?php
namespace dr\classes\dom\tag;

/**
 * <td></td>
 */
class Td extends HTMLTag
{
	private $iColspan = 1;

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('td');

		$this->setSourceFormattingIdentForCloseTag(false);
		$this->setSourceFormattingNewLineAfterOpenTag(false);
	}

	/**
	 * get the colspan of the TD
	 * @return int
	 */
	public function getColSpan()
	{
		return $this->iColspan;
	}

	/**
	 * set the colspan of the td
	 *
	 * @param int $iSpan
	 */
	public function setColSpan($iSpan)
	{
		if (is_numeric($iSpan))
		{
			$this->iColspan = $iSpan;
		}
	}

// 	public function getXMLNodeSpecificToNode_OLD(DOMElement $objXMLElement)
// 	{
// 		//colspan toevoegen
// 		if ($this->getColSpan() > 1)
// 		{
// 			$objXMLElement->setAttribute('colspan', $this->getColSpan());
// 		}
// 	}

	protected function renderChild()
	{
		$sAttributes = '';

		//colspan toevoegen
		if ($this->getColSpan() > 1)
		{
			$sAttributes .= $this->addAttributeToHTML('colspan', $this->getColSpan());
		}

		return $sAttributes;
	}
}

?>