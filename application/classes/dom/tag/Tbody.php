<?php
namespace dr\classes\dom\tag;


/**
 * <tbody></tbody>
 */
class TBody extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('tbody');
	}


	/**
	 * adds a row to the table
	 *
	 * @param array $arrColumnValues array met waardes voor in de rij
	 * @param string $sCSSClass css klasse
	 */
	public function addRow($arrColumnValues, $sCSSClass = '')
	{
		$objTR = new tr();
		$objTR->setClass($sCSSClass);

		foreach ($arrColumnValues as $sCol)
		{
			$objCol = new td();
			$objText = new text();
			$objText->setText($sCol);
			$objCol->appendChild($objText);

			$objTR->appendChild($objCol);
		}

		$this->appendChild($objTR);
	}

}

?>