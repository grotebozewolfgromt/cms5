<?php
namespace dr\classes\dom\validator;

use dr\classes\dom\tag\form\FormInputAbstract;


/**
 * this class is for manipulating or requesting values from the form input
 *
 * 6 juli 2015: FORMINPUTCONTENTS: performance improvement bij casts
 *
 * @deprecated use field->getValueSubmitted() instead
 */
class FormInputContentsOLD
{
	private $sValue = '';
	private $arrFiles = array();//a copy of the $_FILES array
	private $objParentFormInputAbstract = null; //object type: FormInputAbstract

	/**
	 * Undocumented function
	 *
	 * @param [type] $objParent
	 */
	public function __construct(FormInputAbstract &$objParent)
	{
		$objParentFormInputAbstract = $objParent;
	}

	/**
	 * setting the contents of an input item on a form
	 * @param string $sValue
	*/
	public function setValue($sValue)
	{
		$this->sValue = $sValue;
	}

	/**
	 * setting a value as boolean
	 *
	 * @param bool $bValue
	 */
	public function setValueAsBool($bValue)
	{
		$this->setValue(intToStr(boolToInt($bValue)));
	}

	/**
	 * setting a value as integer
	 * @param int $iValue
	 */
	public function setValueAsInt($iValue)
	{
		$this->setValue(intToStr($iValue));
	}

	/**
	 * setting a value as float
	 * @param float $fValue
	 */
	public function setValueAsFloat($fValue)
	{
		$this->setValue(intToFloat($fValue));
	}

	/**
	 * setting the contents of an input item on a form
	 * @return string
	 */
	public function getValue()
	{
		if (count($this->arrFiles) > 0) //als er een file geupload is, deze als waarde setten
			if (strlen($this->sValue) == 0)
				$this->setValue($this->getFileName());

		return $this->sValue;
	}


	/**
	 * alias for getValue
	 * @return string
	 */
	public function getValueAsString()
	{
		return $this->getValue();
	}

	/**
	 * getting a float value as string in a readable format
	 * use this for displaying money amounts
	 *
	 * @return string
	 */
	public function getValueAsStringFloatValue($iNumberDigitsAfterDecimalSeparator)
	{
		return floatToStr(strToFloat($this->getValue()), $iNumberDigitsAfterDecimalSeparator);
	}

	/**
	 * get value as floating point value (can be real or double too)
	 * @return float
	 */
	public function getValueAsFloat()
	{
		return strToFloat($this->getValue());
	}

	/**
	 * getValue as an boolean
	 * @return bool
	 */
	public function getValueAsBool()
	{
		return (bool)( (int)$this->getValue() );
	}

	/**
	 * getValue as an integer
	 * @return int
	 */
	public function getValueAsInt()
	{
		return (int)$this->getValue();
	}

	/**
	 * getting the string-length of the current value
	 *
	 * @return int
	 */
	public function getLength()
	{
		return strlen($this->getValue());
	}

	/**
	 * is the value a numeric value ?
	 * @return bool
	 */
	public function isNumeric()
	{
		return is_numeric($this->getValue());
	}

	/**
	 * is the value set/is the value empty ?
	 * @return bool
	 */
	public function isEmpty()
	{
		return ($this->getLength() == 0);
	}

	/**
	 * checks if the value could be a valid integer
	 * @return bool
	 */
	public function isValidInt()
	{
		$bValid = true;

		try
		{
			$iTemp = (int)$this->getValue();
			return true;
		}
		catch (Exception $objException)
		{
			return false;
		}
	}

	/**
	 * checks if the value could be a valid bool
	 * @return bool
	 */
	public function isValidBool()
	{
		$bValid = true;

		try
		{
			$bTemp = (bool)$this->getValue();
			return true;
		}
		catch (Exception $objException)
		{
			return false;
		}
	}

	/**
	 * checks if the value could be a valid float
	 * @return bool
	 */
	public function isValidFloat()
	{
		$bValid = true;

		try
		{
			$fTemp = (float)$this->getValue();
			return true;
		}
		catch (Exception $objException)
		{
			return false;
		}
	}

	/**
	 * sets the files-array (contents of the $_FILES[] array)
	 * this is done by the getContentsSubmitted() function of FORMINPUT
	 * SO, DON'T USE THIS FUNCTION, IT'S AUTOMATICALLY DONE FOR YOU
	 * @param array $arrFiles
	 */
	public function setFileArray($arrFiles)
	{
		$this->arrFiles = $arrFiles;
	}

	/**
	 * get the original name of the file
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->arrFiles['name'];
	}

	/**
	 * get the extension of the original file
	 *
	 * @return string
	 */
	public function getFileExtension()
	{
		return getFileExtension($this->getFileName());
	}

	/**
	 * get the filetype of the uploaded file, for example application/octet-stream
	 *
	 * @return string
	 */
	public function getFileType()
	{
		return $this->arrFiles['type'];
	}

	/**
	 * get the path where the file is temporary stored
	 *
	 * @return string
	 */
	public function getFileTempFileName()
	{
		return $this->arrFiles['tmp_name'];
	}

	/**
	 * get errors, if any
	 *
	 * @return string
	 */
	public function getFileError()
	{
		return $this->arrFiles['error'];
	}

	/**
	 * get the file size in bytes
	 *
	 * @return int
	 */
	public function getFileSize()
	{
		return $this->arrFiles['size'];
	}

	/**
	 * get the file size in kilobytes
	 *
	 * @return float
	 */
	public function getFileSizeKB()
	{
		return $this->getFileSize() / 1024;
	}
	public function getFileSizeKBRounded()
	{
		return round($this->getFileSizeKB);
	}

	/**
	 * get the file size in megabytes
	 *
	 * @return float
	 */
	public function getFileSizeMB()
	{
		return $this->getFileSizeKB() / 1024;
	}
	public function getFileSizeMBRounded()
	{
		return round($this->getFileSizeMB());
	}

	/**
	 * save the oploaded file to a location, i.e. /usr/dennis/mypic.jpg
	 *
	 * @param string $sNewFilePath
	 */
	public function saveFileToLocation($sNewFilePath)
	{
		try
		{
			move_uploaded_file($this->getFileTempFileName(), $sNewFilePath);
		}
		catch (Exception $objEx)
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objEx, $this);
		}
	}

	/**
	 * does the same as saveFileToLocation, but preserves the original filename
	 * @param string $sDirectory the directory including a slash after the directory name
	 */
	public function saveFileToDirectory($sDirectoryWithSlash)
	{
		$this->saveFileToLocation($sDirectoryWithSlash.$this->getFileName());
	}
}

?>