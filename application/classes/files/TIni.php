<?php
namespace dr\classes\files;


/**
 * Description of TIni
 *
 * create and manage an ini file
 * for i.e. language purposes or configuration purposes
 *
 *
 * 22 jun 2015: created an entirely new class (wich replaced the old TIni) based on the php native parse_ini_file) for more speed (more than 10 times)
 * 15 april 2016: getFilePath + sectionExists toegevoegd
 * 26 april 2019: bugje waardoor items niet automatisch werden toegevoegd $arrIni = array() gedrclareerd
 * 
 * @author dennis renirie
 */
class TIni
{
    private $arrIni = array();//associative array a produced by parse_ini_file()
    private $arrIni_unchanged = array();//copy of $arrIni to determine if the ini file has changed
    private $bDirty = false;//register is changes happened 
    private $bSaveOnDestruction = false;
    private $sFileNameOpened = '';//the filename wich you used to open the file

    public function __construct($bSaveOnDestruction = false)
    {
        $this->bSaveOnDestruction = $bSaveOnDestruction;
    }

    public function __destruct()
    {
        if ($this->bSaveOnDestruction)
        	$this->saveToFile(); //checks if is dirty
    }



    /**
     * get if changes made to the ini file since last load
     * 
     * @return bool
     */
    public function getDirty()
    {
        return $this->bDirty;
    }
    
    public function setDirty($bDirty = true)
    {
    	$this->bDirty = $bDirty;
    }

    /**
     * read an ini file
     * @param string $sFileName path to ini file
     * @return bool
     */
    public function loadFromFile($sFileName)
    {
    	$this->sFileNameOpened = $sFileName;
	    $this->arrIni = parse_ini_file($sFileName, true); //process sections
	    $this->arrIni_unchanged = arrayCopy($this->arrIni); //make a copy of arrIni so we can determine later if it is changed		
        
        if ($this->arrIni)
            return true;
        else
            return false;
    }

    /**
     * empty the ini contents
     */
    public function clear()
    {
        unset($this->arrIni);
        $this->arrIni =  null;
    }

    /**
     * save contents to ini file
     * @param string $sFileName path to ini file - if empty it uses the filename you used to open the ini file
     * @return bool
     */
    public function saveToFile($sFileName = '') 
    {
    	
		if ($sFileName == '')
			$sFileName = $this->sFileNameOpened;
		
		if ($this->getDirty ()) {
			//tracepoint('savetodisk:');
			
		
			$this->arrIni_unchanged = arrayCopy($this->arrIni);
			$has_sections = true;
			$content = '';
			if ($has_sections) {
				foreach ( $this->arrIni as $key => $elem ) {
					$content .= '[' . $key . "]\n";
					foreach ( $elem as $key2 => $elem2 ) {
						if (is_array ( $elem2 )) {
							for($i = 0; $i < count ( $elem2 ); $i ++) {
								$content .= $key2 . '[] = "' . $elem2 [$i] . "\"\n";
							}
						} else if ($elem2 == '')
							$content .= $key2 . " = \n";
						else
							$content .= $key2 . ' = "' . $elem2 . "\"\n";
					}
					$content .= "\n";
				}
			} else {
				foreach ( $this->arrIni as $key => $elem ) {
					if (is_array ( $elem )) {
						for($i = 0; $i < count ( $elem ); $i ++) {
							$content .= $key . '[] = "' . $elem [$i] . "\"\n";
						}
					} else if ($elem == '')
						$content .= $key . " = \n";
					else
						$content .= $key . ' = "' . $elem . "\"\n";
				}
			}
			
			if (! $handle = fopen ( $sFileName, 'w' )) {
				return false;
			}
			
			$success = fwrite ( $handle, $content );
			fclose ( $handle );
			
			return $success;
		} else
			return true;
    }    
    
    
    /**
     * save to contents to the session array, so you dont have to load and interpret the
     * contents of the ini file everytime you need it (you can read it once and
     * use the session for later use).
     * Use an ID to identify this instance of TIni_old
     *
     * @param string $sID
     */
    public function loadFromSession($sID)
    {
        $this->arrIni = $_SESSION['TIni_'.$sID];
        $this->arrIni_unchanged = arrayCopy($this->arrIni);
    }

    /**
     * save the contents from the session array, so you dont have to load and interpret the
     * contents of the ini file every time you need it (you can read once and
     * use the session for later use).
     * Use an ID to identify this instance of TIni_old
     *
     * @param string $sID
     */
    public function saveToSession($sID)
    {
    	if ($this->getDirty())
    	{
    		$this->arrIni_unchanged = arrayCopy($this->arrIni);
        	$_SESSION['TIni_'.$sID] = $this->arrIni;        	
    	}
    }

    /**
     * read a variable from a section in the ini file
     *
     * @param string $sSection the section in the inifile -> the one between brackets: [mysection]
     * @param string $sVariable the variable in the section -> the one before the =-sign: thisone = somevalue
     * @param string $sDefaultValue the default value if the variable doesn't exists in the section
     * @return string
     */
    public function read($sSection, $sVariable, $sDefaultValue = '')
    {

	    	//als sectie niet bestaat
	    	if (is_array($this->arrIni))
	    	{
//tracepoint('slakkislo')                    ;   	
		    	if ( (!array_key_exists($sSection, $this->arrIni)) )
		    	{    		
		//     		tracepoint('bestaatniet sectie');
		    		$this->write($sSection, $sVariable, $sDefaultValue, false);
		    		return $sDefaultValue;
		    	}
	    	}
	
	    	//als variabele niet bestaat
	    	if (is_array($this->arrIni[$sSection]))
	    	{
		    	if ( (!array_key_exists($sVariable, $this->arrIni[$sSection])) )
		    	{
		//     		tracepoint('bestaat niet var');
		    		$this->write($sSection, $sVariable, $sDefaultValue, false);
		    		return $sDefaultValue;    		
		    	}
	    	}
    	    	
		return $this->arrIni[$sSection][$sVariable];
    }

    /**
     * write a variable to a section of the ini file
     *
     * @param string $sSection
     * @param string $sVariable
     * @param string $sValue
     * @param boolean $bOnlySetDirtyIfValueIsDifferent default TRUE (sets dirty-state ALWAYS to true), on FALSE it skips the resource expensive check for looking up the value in the array and comparing it to $sValue
     */
    public function write($sSection, $sVariable, $sValue, $bOnlySetDirtyIfValueIsDifferent = true)
    {
//    	if ($bOnlySetDirtyIfValueIsDifferent)
//     	{ 
// 	    	if ( (!array_key_exists($sSection, $this->arrIni)) ) //resource intensive
// 	    	{    	
// 		    	if ( (!array_key_exists($sVariable, $this->arrIni[$sSection])) ) //resource intensive
// 		    	{
// 		    		if ($this->arrIni[$sSection][$sVariable] != $sValue)
// 	    				$this->setDirty();	    		 
// 		    	}
// 	    	}
//     	}
//     	else
    		$this->setDirty();    	    	

		$this->arrIni[$sSection][$sVariable] = $sValue;
    }
    
    /**
     * does section exist in config file?
     * 
     * @param string $sSection
     * @return boolean true exists, false it doesnt
     */
    public function sectionExists($sSection)
    {
    	return array_key_exists($sSection, $this->arrIni);
    }
    
    /**
     * return the path of the opened file 
     * @return string
     */
    public function getFilePath()
    {
    	return $this->sFileNameOpened;
    }
}


?>
