<?php
namespace dr\classes;

/**
 * Description of TConfigFile
 *
 * can read, write and check a php config files.
 * Every line in the config file is in the structure of a php define('key', 'value');
 * Just by including the file into php are the values of the config file read.
 * This is fastest solution because it is native to PHP and safest because
 * it avoids the risk of accidental exposure like with JSON or CSV
 * 
 * 
 *  
 ****************** layout consts array **************
 * name (=key), (default) value, type, description
 *
 ********************** DEFAULTS *********************
 * The default value needs to be the most secure setting possible.
 * This avoids a new install from being vulnerable out-of-the-box, like Wordpress's rediculous gaping security hole: XML-RPC
 * The idea is that the user can enable extra features later when he wants to.
 * 
 * 
 *************
 * 18 nov 2024: created
 * 
 * @author dennis renirie
 */

abstract class TConfigFilePHPConstants
{
	private $arrConsts = null;

	public function __construct()
	{
		$this->arrConsts = $this->defineConstants();
	}

	/**
	 * return value from internal array
	 * 
	 * @return mixed null if not found (or value is null)
	 */
	public function get($sKey)
	{
		foreach ($this->arrConsts as $arrLine)
		{
			if ($arrLine[0] == $sKey)
				return $arrLine[1];
		}

		return null;
	}


	/**
	 * return value from internal array
	 * 
	 * @return bool true or false value, also returns false when not found
	 */
	public function getAsBool($sKey)
	{
		foreach ($this->arrConsts as $arrLine)
		{
			if ($arrLine[0] == $sKey)
				return strToBool($arrLine[1]);
		}

		return false;
	}

	/**
	 * return value from internal array
	 * 
	 * @return int
	 */
	public function getAsInt($sKey)
	{
		foreach ($this->arrConsts as $arrLine)
		{
			if ($arrLine[0] == $sKey)
				return strToInt($arrLine[1]);
		}

		return false;
	}

	/**
	 * sets value in internal array
	 * it overwrites internal values, but does NOT add new ones when it can't find the value
	 * 
	 * @return bool found
	 */
	private function setFull($sKey, $sValue, $iType = TP_STRING, $sDescription = '')
	{
		$iCount = 0;
		$iCount = count($this->arrConsts);

		for ($iLine = 0; $iLine < $iCount; ++$iLine)
		{			
			if ($this->arrConsts[$iLine][0] == $sKey)
			{
				//set value in right type
				if ($iType === TP_BOOL)
					$this->arrConsts[$iLine][1] = strToBool($sValue);
				elseif ($iType === TP_INTEGER)
					$this->arrConsts[$iLine][1] = strToInt($sValue);
				else
					$this->arrConsts[$iLine][1] = $sValue;
				$this->arrConsts[$iLine][2] = $iType;
				$this->arrConsts[$iLine][3] = $sDescription;

				return true;
			}
		}

		return false;
	}


	/**
	 * sets value in internal array
	 * it overwrites internal values, but does NOT add new ones when it can't find the value
	 * 
	 * @param string sKey
	 * @param string sValue
	 * @return bool found
	 */
	public function set($sKey, $sValue)
	{
		$iCount = 0;
		$iCount = count($this->arrConsts);

		for ($iLine = 0; $iLine < $iCount; ++$iLine)
		{			
			if ($this->arrConsts[$iLine][0] == $sKey)
			{
				//set value in right type
				if ($this->arrConsts[$iLine][2] === TP_BOOL)
					$this->arrConsts[$iLine][1] = strToBool($sValue);
				elseif ($this->arrConsts[$iLine][2] === TP_INTEGER)
					$this->arrConsts[$iLine][1] = strToInt($sValue);
				else
					$this->arrConsts[$iLine][1] = $sValue;

				return true;
			}
		}

		return false;
	}

	/**
	 * copies the value from another config file and sets it as value
	 */
	public function setCopy($sKey, TConfigFilePHPConstants $objOtherConfigFile)
	{
		$this->set($sKey, $objOtherConfigFile->get($sKey));
	}

	/**
	 * sets value in internal array
	 * it overwrites internal values, but does NOT add new ones when it can't find the value
	 * 
	 * @param string sKey
	 * @param bool bValue
	 * @return bool found
	 */
	public function setAsBool($sKey, $bValue)	
	{
		return $this->set($sKey, $bValue);
	}

	/**
	 * sets value in internal array
	 * it overwrites internal values, but does NOT add new ones when it can't find the value
	 * 
	 * @param string sKey
	 * @param bool bValue
	 * @return bool found
	 */
	public function setAsInt($sKey, $iValue)	
	{
		return $this->set($sKey, $iValue);
	}

	/**
	 * load values from config file to internal array
	 * 
     * @param string $sConfigFilePath path of the config file
	 * @return bool did load succeed?
	 */
	public function loadFile($sConfigFilePath)
	{
		$sLine = '';
		$iLPosKey = 0;
		$iRPosKey = 0;
		$iLPosValue = 0;
		$iRPosValue = 0;
		$iLPosValueQuote = 0;
		$iRPosValueQuote = 0;
		$sKey = '';
		$sValue = '';
		$fpFile = 0;//pointer

		if (!file_exists($sConfigFilePath))
			return false;

		$fpFile = fopen($sConfigFilePath, 'r') or error_log(__FILE__.' loadfile(): cant open/create file '.$sConfigFilePath); //we use error log because the config file might not be read yet

		while(!feof($fpFile)) 
		{
			$sLine = fgets($fpFile);
			if (startswith($sLine, 'define'))
			{
				//====KEY
					$sKey = '';
					//search for position of first '
					$iLPosKey = strpos($sLine, '\'');
					//search for position of second '
					$iRPosKey = strpos($sLine, '\'', $iLPosKey+1);
					//substring
					$sKey = substr($sLine, $iLPosKey+1, $iRPosKey-$iLPosKey-1);

				//===VALUE
					$sValue = '';
					//search for ,
					$iLPosValue = strpos($sLine, ',');
					//search for position )
					$iRPosValue = strpos($sLine, ')', $iLPosValue);
					//substring
					$sValue = substr($sLine, $iLPosValue+1, $iRPosValue-$iLPosValue-1);
					$sValue = ltrim($sValue);
					//filter string quotes '' --> overwrites $sValue
					$iLPosValueQuote = strpos($sValue, '\'');
					if ($iLPosValueQuote !== false)
					{
						$iRPosValueQuote = strpos($sValue, '\'', $iLPosValueQuote+1);
						if ($iRPosValueQuote !== false)
							$sValue = substr($sValue, $iLPosValueQuote+1, $iRPosValueQuote-$iLPosValueQuote-1);
					}
				

				$this->set($sKey, $sValue);
			}
		}		
		
		fclose($fpFile);
		return true;
	}

	/**
	 * checks current live active config for errors by looking at the internal array.
	 * it checks for missing constants and misconfigured types
	 * 
	 * @return array string-array with errors. if count(check()) == 0 then there are no errors
	 */
	public function checkLive()
	{
		$arrErrors = array();

		//body
		foreach ($this->arrConsts as $arrLine)
		{
			if (defined($arrLine[0]))
			{
				if ($arrLine[2] === TP_BOOL)
				{
					if (!is_bool($arrLine[1]))
						$arrErrors[] = 'The value of constant "'.$arrLine[0].'" is not a boolean, while it is defined as one';
				}

				if ($arrLine[2] === TP_INTEGER)
				{
					if (!is_int($arrLine[1]))
						$arrErrors[] = 'The value of constant "'.$arrLine[0].'" is not an integer, while it is defined as one';
				}

				if ($arrLine[2] === TP_STRING)
				{
					if (!is_string($arrLine[1]))
						$arrErrors[] = 'The value of constant "'.$arrLine[0].'" is not a string, while it is defined as one';
				}
			}
			else
				$arrErrors[] = 'Constant "'.$arrLine[0].'" is NOT defined';
		}	
		
		return $arrErrors;
	}

	/**
	 * checks internal array for errors
	 * it checks for misconfigured types
	 * 
	 * @return array string-array with errors. if count(check()) == 0 then there are no errors
	 */
	public function checkInternal()
	{
		$arrErrors = array();

		//body
		foreach ($this->arrConsts as $arrLine)
		{
			if ($arrLine[2] === TP_BOOL)
			{
				if (!is_bool($arrLine[1]))
					$arrErrors[] = 'The value of constant "'.$arrLine[0].'" is not a boolean, while it is defined as one';
			}

			if ($arrLine[2] === TP_INTEGER)
			{
				if (!is_int($arrLine[1]))
					$arrErrors[] = 'The value of constant "'.$arrLine[0].'" is not an integer, while it is defined as one';
			}

			if ($arrLine[2] === TP_STRING)
			{
				if (!is_string($arrLine[1]))
					$arrErrors[] = 'The value of constant "'.$arrLine[0].'" is not a string, while it is defined as one';
			}
		}	
		
		return $arrErrors;
	}

	/**
	 * save values in current array to config file
	 * 
     * @param string $sConfigFilePath path of the config file
	 * @return bool success or not
	 */
	public function saveFile($sConfigFilePath)
	{
		$fpFile = fopen($sConfigFilePath, 'w') or error_log(__FILE__.' savefile(): cant open/create file '.$sConfigFilePath); //we use error log because the config file might not be read yet
		if ($fpFile === false)
			return false;

		//header
		fwrite($fpFile, '<?php'."\n");

		//body
		foreach ($this->arrConsts as $arrLine)
		{
			if ($arrLine[2] === TP_BOOL)
				fwrite($fpFile, 'define(\''.$arrLine[0].'\', '.boolToStr($arrLine[1], true).'); //'.$arrLine[3]."\n");
			elseif ($arrLine[2] === TP_INTEGER)
				fwrite($fpFile, 'define(\''.$arrLine[0].'\', '.$arrLine[1].'); //'.$arrLine[3]."\n");
			else
				fwrite($fpFile, 'define(\''.$arrLine[0].'\', \''.$arrLine[1].'\'); //'.$arrLine[3]."\n");
		}

		//footer
		fwrite($fpFile, '?>'."\n");
	
		if (fclose($fpFile) === false)
			return false;

		return true;
	}



	/*********************** ABSTRACT FUNCTIONS ****************/

	/**
	 * define 2d array with PHP constants
	 * 
	 * layout:
	 * indexed array with on every line: 
	 * name (=key), (default) value, type, description
	 */
	abstract protected function defineConstants();

}
