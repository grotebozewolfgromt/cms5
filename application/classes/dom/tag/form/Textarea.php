<?php
namespace dr\classes\dom\tag\form;

use dr\classes\dom\tag\Text;

/*
 * <textarea></textarea>
 *
 *     $objHidden = new textarea();
 $objHidden->setName('edtMoreLines');
 $objHidden->setText(100);
 $objForm->appendChild($objHidden);

 * 14 nov 2022: Textarea(): added: setValue() and getValue() wrappers for setText() and getText()
 */
class Textarea extends FormInputAbstract
{
	private $iRows = 2;
	private $iCols = 20;
	private $sOnChange = '';

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setTagName('textarea');

		//according to the w3c standards: every textarea needs text
		//so we create that automatically for you:
		$objText = new Text();
		$this->appendChild($objText);
	}



	/**
	 * getting the text of a text area
	 * @return string
	 */
	public function getText()
	{
		$this->setText($this->getValue());//synchroniseren met getContents
		return $this->getChildNode(0)->getText();
	}

	/**
	 * setting the text of a text area
	 * @param string $sText
	 */
	public function setText($sText)
	{
		// $this->setValue($sText); //synchroniseren met getContents --> gives endless loop
		$this->getChildNode(0)->setText($sText);
	}

	/**
	* wrapper for getText()
	* @return string
	*/
	public function getValue()
	{
		return $this->getText();
	}

	/**
	 * wrapper for setText();
	 * @param string $sText
	 */
	public function setValue($sText)
	{
		$this->setText($sText);
	}	


	/**
	 * save the contents of a textarea to a file
	 *
	 * @param string $sFile path of the filename to save.
	 * @return boolean save succesfull ?
	 */
	public function saveToFile($sFile)
	{
		$sFileContents = $this->getText();
		$arrContents = strToArr($sFileContents, $config_files_slineendingcharacter);
		return saveToFile($arrContents, $sFile);
	}

	public function loadFromFile($sFile)
	{
		$arrFileContents = loadFromFile($sFile);
		$sFileContents = arrToStr($arrFileContents, $config_files_slineendingcharacter);
		$this->setText($sFileContents);
	}

	/**
	 * getting the cols of a text area
	 * @return int
	 */
	public function getCols()
	{
		return $this->iCols;
	}

	/**
	 * setting the cols of a text area
	 * @param int $iCols
	 */
	public function setCols($iCols)
	{
		$this->iCols = $iCols;
	}

	/**
	 * getting the rows of a text area
	 * @return int
	 */
	public function getRows()
	{
		return $this->iRows;
	}

	/**
	 * setting the rows of a text area
	 * @param int $iRows
	 */
	public function setRows($iRows)
	{
		$this->iRows = $iRows;
	}


	protected function renderChild()
	{
		$sAttributes = '';

		//cols toevoegen
		if (strlen($this->getCols()) > 0)
			$sAttributes .= $this->addAttributeToHTML('cols', $this->getCols());

		//cols toevoegen
		if (strlen($this->getRows()) > 0)
			$sAttributes .= $this->addAttributeToHTML('rows', $this->getRows());

		return $sAttributes;
	}
}


?>