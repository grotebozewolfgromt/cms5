<?php
namespace dr\classes\locale;

use dr\classes\patterns\TObject;
use dr\classes\files\TCSV;
use dr\classes\patterns\TTableRow;

/**
 * Description of TTranslation
 * 
 * translation of language dependent elements
 * 
 * 
 * CONSTRUCTION TRANSLATION ARRAY:
 * Er is gekozen voor een 2d array omdat 1d soms problemen geeft bij vertalingen met meerdere betekenissen.
 * Het engelse woord 'like' bijvoorbeeld, kan 'zoals' betekenen, maar ook 'leuk'. 
 * Daarom kun je een unieke key definieren met een default translation.
 * 
 * Er is een 2d array als volgt opgebouwd
 * $arrTranslation[$sKey]['defaultenglish'] = 'my super english sentence' translation
 * $arrTranslation[$sKey]['translation'] = 'my translation from english' translation
 * $sKey is de key van de translation. 
 * de key en defaultenglish zullen voor 9 van de 10 gevallen hetzelfde zijn:
 * $arrTranslation['press any key to continue']['defaultenglish'] = 'press any key to continue'
 * $arrTranslation['press any key to continue']['translation'] = 'druk op een toets om verder te gaan'
 * maar het bovenstaande voorbeeld (van dubbele betekenissen) zou je een unieke key kunnen definieren voor een specifieke betekenis:
 * $arrTranslation['button_likeperson']['defaultenglish'] = 'like';
 * $arrTranslation['button_likeperson']['translation'] = 'vind leuk';
 * 
 * 
 * 
 * betreffende de includes in deze klasse:
 * ========================================================================================
 * het kan zijn dat de globale $objApplication weg is (bijvoorbeeld in de destructor van Application).
 * Als er gecached is, zijn de klassen niet nodig geweest (dus niet geladen), en daardoor niet beschikbaar bij Application:__destruct.
 * Maar er kunnen geen nieuwe klassen meer ingeladen worden door de autoloader() --> deze heeft daarvoor $objApplication nodig
 * includen zorgt dat de klassen bereikbaar zijn
 * 
 * 
 * 
 * 13 juli 2012: TTranslation created
 * 13 juli 2012: TTranslation functies van TLocale verhuisd naar deze klasse
 * 13 juli 2012: TTranslation read.. en write.. is resp. load.. en save.. geworden. da's logischer
 * 18 juli 2012: $objCSV als class object
 * 18 juli 2012: $objRowKeepAlive als class object
 * 19 juli 2012: elegantere oplossing voor het laden van de cache en de missende klassen: er wordt gewerkt met includes
 * 23 juli 2012: chmod 777 van vertalingsbestand in development environment
 * 24 okt 2012: translate() heeft 4 extra paramenters gekregen voor variabelen
 * 24 okt 2012: translate() bugfix: 2e variabele overschreef de eerste
 * 25 okt 2012: TTranslation: translate() bugfix: als de vertaling nieuw was werden de variabelen niet vervangen door waardes
 * 25 okt 2012: TTranslation: translate() mogelijkheid tot 3e variabele
 * 30 apr 2014: TTranslation: translate() valt niet meer over een lege string invoer (geeft dan lege string ook terug)
 * 28 aug 2015: TTranslation: er wordt bij het laden gecheckt of translation file niet empty is
 * 26 apr 2019: TTranslation: wordt automatisch geladen bij het eerste gebruik om geheugen te besparen
 * 26 apr 2019: TTranslation: csv keys more descriptive
 * 27 apr 2019: TTranslation: bugfix: init() werkte niet goed (op locale variabelen ipv class variablen)
 * 27 apr 2019: TTranslation: bugfix: bij iedere translate() werd file geladen
 * 18 apr 2024: TTranslation: bugfix: translate() trims the result
 * 
 *
 * @author drenirie
 */

//ivm met caching kunnen deze klassen niet geladen zijn, en via autoload niet meer inladen als je in een destructor van een klasse zit
// include_once '../files/TCSV.php'; 
// include_once '../patterns/TTableRow.php';

class TTranslation
{
    private $arrTranslation = array();//2d array first 
    private $bDirty = false;//count from when de file was read, so you can determine if it was changed, when so you have to save it
    private $sFilePath = '';
    private $objCSV = null; //so we have to set all the settings just once
    private $bFileLoaded = false;
    
    const ARRKEY_DEFAULTENGLISH 	= 'defaultenglish';
    const ARRKEY_TRANSLATION 		= 'translation';
    
    const CSV_HEADER_KEY            = 'UNIQUE ID KEY';
    const CSV_HEADER_DEFAULTENGLISH = 'DEFAULT ENGLISH';
    const CSV_HEADER_TRANSLATION 	= 'TRANSLATION FOREIGN LANGUAGE';
    		
    
    public function __construct()
    {
        $this->init();
    }
    
    public function __destruct()
    {
        if ($this->sFilePath != '')
            $this->saveTranslationFile();      
    }    
    
    /**
     * initionalizes the object and resets/removes all existing data
     */
    public function init()
    {
        $this->bFileLoaded = false;
        $this->sFilePath = '';
        $this->bDirty = false;
        
        if ($this->arrTranslation)
            unset($this->arrTranslation);
        $this->arrTranslation = array();
        
        if ($this->objCSV)
            unset($this->objCSV);

        $this->objCSV = new TCSV();
        $this->objCSV->setSettings(TCSV::MSEXCELSETTINGS);
        $this->objCSV->setFirstLineIsHeader(true);
        $this->objCSV->setHeader(array(TTranslation::CSV_HEADER_KEY, TTranslation::CSV_HEADER_DEFAULTENGLISH, TTranslation::CSV_HEADER_TRANSLATION));        
    }
    

    /**
     * set file path of translation file
     * @param string $sFilePath path of the translation file
     */
    public function setFileName($sFilePath)
    {
        $this->sFilePath = $sFilePath;
    }
    
    /**
     * return path of translation file 
     * 
     * @return string
     */
    public function getFileName()
    {
        return $this->sFilePath;
    }

    /**
     * internal generic function to read language files
     * 
     * @param string $sFileName
     */
    public function loadTranslationFile($sFileName = '')
    {
        //als geen parameter dan interne filepath pakken
        if ($sFileName == '')
            $sFileName = $this->sFilePath;
        else//als wel parameter dan interne filepath overschrijven
            $this->sFilePath = $sFileName;
  
        if (is_file($sFileName))
        {
            if ($this->objCSV->loadFromFile($sFileName))
            {
            	if ($this->objCSV->count() > 0)
            	{
                    $sKeyColName 	= $this->objCSV->getColumnName(0); //eerste kolom is de key
                    $sDefEngColName = $this->objCSV->getColumnName(1); //tweede kolom is default english
                    $sTransColName 	= $this->objCSV->getColumnName(2); //derde kolom is de vertaling

                    //vardump($sOriginalColName);
                    //vardump($sTransColName);

                    for ($iLineCounter = 0; $iLineCounter < $this->objCSV->count(); $iLineCounter++)
                    {
                        /* @var $objRow TTableRow */
                        $objRow = $this->objCSV->get($iLineCounter);                    
                        //echo 'whoie-=-=';
                        //vardump($objRow);
                        //echo 'asdfasf-===';


    //                     $this->arrTranslation[$objRow->get($sKeyColName)][TTranslation::ARRKEY_DEFAULTENGLISH] = $objRow->get($sKeyColName);
                        $this->arrTranslation[$objRow->get($sKeyColName)][TTranslation::ARRKEY_DEFAULTENGLISH] = $objRow->get($sDefEngColName);
                        $this->arrTranslation[$objRow->get($sKeyColName)][TTranslation::ARRKEY_TRANSLATION] = $objRow->get($sTransColName);

                        //echo '$objRow->get(:'.$objRow->get($sOriginalColName).'<br>';
                        //echo '$objRow->get(:'.$objRow->get($sTransColName).'<br><br>';

                    }
                    
            	}//count > 0
            }            
        }

        $this->bFileLoaded = true; //statemen is hier omdat anders in een lege file iedere translation opnieuw wordt geladen wat kan leiden tot traagheid
        
        //to spare memory, we clean the CSV
        $this->objCSV->clear();
        
        $this->bDirty = false; //to determine differences
    }
    
    /**
     * internal generic function to write language files
     * 
     * @param string $sFileName optional
     */
    public function saveTranslationFile($sFileName = '')
    {
          
        //als geen parameter dan interne filepath pakken
        if ($sFileName == '')
            $sFileName = $this->sFilePath;
        else//als wel parameter dan interne filepath overschrijven
            $this->sFilePath = $sFileName;
        
        
        //to be sure: we clear the CSV
        $this->objCSV->clear();

        //now we fill the CSV with data
        if ($this->arrTranslation != null)
        {
        
            if ($this->bDirty)
            {
//             	tracepoint('save: '.$sFileName);            	
                foreach(array_keys($this->arrTranslation) as $sKey) 
                {
                    $objRow = new TTableRow;

                    $objRow->add($sKey);
                    $objRow->add($this->arrTranslation[$sKey][TTranslation::ARRKEY_DEFAULTENGLISH]);                    
                    $objRow->add($this->arrTranslation[$sKey][TTranslation::ARRKEY_TRANSLATION]);
               
                    $this->objCSV->add($objRow);                    
                }

                $this->objCSV->saveToFile($sFileName);
                
                //change rights for development environment
                // if(APP_DEBUGMODE) --> removed 15-3-2024 chmod gave errors
                //     chmod ($sFileName, 0777);

                unset($this->objCSV);  
                
                $this->bDirty = false;//reset dirty
            }
            
        }
        

    }    
    
    /**
     * get the translation 
     * if translation not found it returns $sOriginalText
     * 
     * when you want to use variables, enclose them by square brackets []
     * 
     * example translate('Page [x] from [y]', 'x', '2', 'y', 3);
     * will translate to string : 'Page 2 from 3'
     * 
     * ik heb besloten om NIET TVariableValue te gebruiken voor de variabalen
     * omdat je dan eerst de klasse moet instantieren, en dat programmeert kut
     * nu kun je in 1 programmeerregel de vertaling krijgen
     * 
     * how to use?
     * translate('this is my life'); --> unique key = 'this is my life'
     * for ambiguous translation use:
     * translate('button_likeperson', 'like'); --> unique key 'button_likeperson'
     * 
     * @param string $sUniqueKey the lookup key can be the default english translation
     * @param string $sDefaultEnglishTranslation when '' $sUniqueTranslationKey is assumed
     * @param string $sVariable1
     * @param string $sValue1
     * @param string $sVariable2
     * @param string $sValue2
     * @return string
     */  
    public function translate($sUniqueKey, $sDefaultEnglishTranslation = '', $sVariable1 = '', $sValue1 = '', $sVariable2 = '', $sValue2 = '', $sVariable3 = '', $sValue3 = '')
    {
        $sTranslation = '';
        
        
        if (!$this->bFileLoaded)//load translation file if translation is empty
        {
            $this->loadTranslationFile();
        }
        
        if ($sUniqueKey == '')//een string zonder inhoud geeft een fout in de hash. gezien een lege string toch niet vertaald hoeft te worden wordt deze leeg terug gegeven
            return '';
        if ($sDefaultEnglishTranslation == '')
        	$sDefaultEnglishTranslation = $sUniqueKey;
        
        //if translation exists, return it
        if (array_key_exists($sUniqueKey,$this->arrTranslation))
        {
// tracepoint('existsdog: '.$sUniqueKey).        	
            $sTranslation = $this->arrTranslation[$sUniqueKey][TTranslation::ARRKEY_TRANSLATION];
        }
        else//if translation NOT exists add it
        {
        	$this->arrTranslation[$sUniqueKey][TTranslation::ARRKEY_DEFAULTENGLISH] = $sDefaultEnglishTranslation;
            $this->arrTranslation[$sUniqueKey][TTranslation::ARRKEY_TRANSLATION] = $sDefaultEnglishTranslation;
            $this->bDirty = true;
// tracepoint('downdirty:' .$sUniqueKey);
// vardump($this->bDirty);            
            $sTranslation = $sDefaultEnglishTranslation;        
        }
        
        
        //variabelen vervangen door waardes
        if ($sVariable1 != '')
        {
            if($sValue1 === null)
                $sValue1 = '[[nothing]]';
            $sTranslation = str_replace('['.$sVariable1.']', $sValue1, $sTranslation);
        }    
        if ($sVariable2 != '')
        {
            if($sValue2 === null)
                $sValue2 = '[[nothing]]';
            $sTranslation = str_replace('['.$sVariable2.']', $sValue2, $sTranslation);
        }           
        if ($sVariable3 != '')
        {
            if($sValue3 === null)
                $sValue3 = '[[nothing]]';
            $sTranslation = str_replace('['.$sVariable3.']', $sValue3, $sTranslation);
        }           
        
        return trim($sTranslation); //we need to add trim because sometimes it adds \n or a dead space, which leads to problems with javascript
    }
    

   
    
}

?>
