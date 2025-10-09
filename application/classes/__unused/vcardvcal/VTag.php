<?php
namespace drenirie\framework\classes\vcardvcal;

use drenirie\framework\classes\types\TDateTime;

/**
 * Parent class for iCal (VCalendar) and vCard format
 * Every tag of the VCalendar or VCard format has to inherit this class
 * 
 * 
 * example tag:
 * BEGIN:VEVENT
UID:uid1@example.com
DTSTAMP:19970714T170000Z
ORGANIZER;CN=John Doe:MAILTO:john.doe@example.com
DTSTART:19970714T170000Z
DTEND:19970715T035959Z
SUMMARY:Bastille Day Party
END:VEVENT

 * in this example:
 * Tag: VEVENT
 * Attribute: UID
 * value of UID: uid1@example.com
 * @author dennisrenirie
 *
 *
 * @todo: wil nu naar //array in array: $arrProperties['TEL'][] = array(ARRAYKEY_VALUE =>'+3161234567', ARRAYKEY_PARAMSATTR => 'type=INTERNET;type=HOMEtype=pref'))
 */

abstract class VTag
{
	const NL = "\r\n";
	
	private $arrAttributes = array();//array in array with attributes and their values, key is the attribute name
	private $arrChildrenTags = array();//you can nest tags in each other
	
	private $arrMultipleAttributesAllowed = array();
	
	protected function setMultipleAttributesAllowed($arrMultProp)
	{
		$this->arrMultipleAttributesAllowed;
	} 
	
	public function renderToString()
	{
		$sReturn = '';

		$sReturn .= 'BEGIN:'.$this->splitOctets('BEGIN', $this->getName()).VTag::NL;
		
		//render attributes
		$arrAttributes = array_keys($this->arrAttributes);
		foreach ($arrAttributes as $sAttributeName)
		{
			$arrAttributeSubArray = $this->arrAttributes[$sAttributeName];
					
			$arrKeys = array_keys($arrAttributeSubArray);
			foreach ($arrKeys as $sKey)
			{							
				$sReturn .= $sKey.':'.$this->splitOctets($sKey, $arrAttributeSubArray[$sKey]).VTag::NL;
			}
		}
		
		//render children
		foreach ($this->arrChildrenTags as $objChild)
		{
			$sReturn .= $objChild->renderToString();
		}
		
		$sReturn .= 'END:'.$this->splitOctets('END', $this->getName()).VTag::NL;
		
		return $sReturn;
	}
	
	abstract public function getName();
	
	
	/**
	 * set attribute (attribute will be replaced if it exists)
	 *  
	 * @param string $sAttribute i.e. TEL
	 * @param string $sValue i.e. +3161234567
	 */
	protected function setAttribute($sAttribute, $sValue)
	{		
		$this->arrAttributes[$sAttribute][$sAttribute] = $this->escape($sValue);
	}
	

	/**
	 * get value of an attribute
	 *  
	 * @param string $sAttribute
	 * @param string $sKey when empty $sAttribute is assumed
	 */
	protected function getAttribute($sAttribute, $sKey = '')
	{
		if ($sKey == '')
			$sKey = $sAttribute; 
			
		return $this->arrAttributes[$sAttribute][$sKey];
	} 
	
	/**
	 * the attribute and key kan be the same, but sometimes i.e. a telephone can have more  than one specification
	 * TEL;type=WORK;type=VOICE;type=pref:+3161234567 
	 * 
	 * @param string $sElement i.e. TEL
	 * @param string $sKey i.e. TEL;type=WORK;type=VOICE;type=pref:
	 * @param string $sValue i.e. +3161234567
	 */
	protected function addAttribute($sAttribute, $sKey, $sValue)
	{
		if (!in_array($sAttribute, $this->arrMultipleAttributesAllowed) && isset($this->arrAttributes[$sAttribute]))
			logThis('VTag: You can only set '.$sElement.' once');
		else
			$this->arrAttributes[$sAttribute][$sKey] = $this->escape($sValue);		
	}
	
	/**
	 * same as addAttribute() but you can add a array for multiple values
	 * 
	 * @param string $sAttribute
	 * @param string $sKey
	 * @param mixed $mValue string or array
	 */
	protected function addAttributeArray($sAttribute, $sKey, $mValue)
	{
		if (!in_array($sAttribute, $this->arrMultipleAttributesAllowed) && isset($this->arrAttributes[$sAttribute]))
			logThis('VTag: You can only set '.$sElement.' once');
		else
		{
			//adding and escaping chars for array
			if (is_array($mValue))
			{	
				$iCount = count($mValue);
				for ($iIndex = 0; $iIndex < $iCount; $iIndex++)
					$mValue[$iIndex] = $this->escape($mValue[$iIndex]);
				
				$sValue = implode(';', $mValue);
				$this->arrAttributes[$sAttribute][$sKey] = $sValue;
			}
			else //adding and escaping chars for string
				$this->arrAttributes[$sAttribute][$sKey] = $this->escape($sValue);
		}
	}
	
	protected function addTag(VTag &$objTag)
	{
		$this->arrChildrenTags[] = $objTag;
	}
	
	/**
	 * little helper for the ISO 8601 datetime notation
	 */
	protected function dateTimeToStr(TDateTime &$objDateTime)
	{
		return $objDateTime->format('Ymd\THis');
	}
	
	/**
	 * according to the standard wich is speaking of 'duration' instead of interval
	 * @param \DateInterval $objInterval
	 */
	protected function intervalToStr(\DateInterval &$objInterval)
	{
		/*
		 *   public 'y' => int 0
  public 'm' => int 8
  public 'd' => int 0
  public 'h' => int 0
  public 'i' => int 0
  public 's' => int 0
  public 'weekday' => int 0
  public 'weekday_behavior' => int 0
  public 'first_last_day_of' => int 0
  public 'invert' => int 0
  public 'days' => boolean false
  public 'special_type' => int 0
  public 'special_amount' => int 0
  public 'have_weekday_relative' => int 0
  public 'have_special_relative' => int 0
		 */		
		
		$sYear = '';
		$sMonth = '';
		$sDay = '';		
		$sHour = '';
		$sMinute = '';
		$sSecond = '';

// 		if ($objInterval->y > 0) //year
// 			$sYear = $objInterval->y.'Y';		
// 		if ($objInterval->m > 0) //month
// 			$sDay = $objInterval->m.'';
	
		if ($objInterval->d > 0) //days
			$sDay = $objInterval->d.'D';		
		if ($objInterval->h > 0) //hours
			$sHour = $objInterval->h.'H';
		if ($objInterval->i > 0) //minutes
			$sMinute = $objInterval->i.'M';
		if ($objInterval->s > 0) //seconds
			$sSecond = $objInterval->s.'S';

		$sTime = '';
		if (($objInterval->h > 0) || ($objInterval->i) || ($objInterval->s) )
		{
			$sTime = 'T'.$sHour.$sMinute.$sSecond;
		}				
	
		return $objInterval->format('%rP'.$sTime);//P & T are literals
	}
	
	/**
	 * lines cannot be longer than 75 octets
	 *
	 * https://gist.github.com/hugowetterberg/81747
	 */
	private function splitOctets($preamble, $value) {
		mb_internal_encoding("UTF-8");
		
		$value = trim($value);
		$value = strip_tags($value);
		$value = preg_replace('/\n+/', ' ', $value);
		$value = preg_replace('/\s{2,}/', ' ', $value);
		$preamble_len = strlen($preamble);
		$lines = array();
		while (strlen($value)>(75-$preamble_len)) {
			$space = (75-$preamble_len);
			$mbcc = $space;
			while ($mbcc) {
				$line = mb_substr($value, 0, $mbcc);
				$oct = strlen($line);
				if ($oct > $space) {
					$mbcc -= $oct-$space;
				}
				else {
					$lines[] = $line;
					$preamble_len = 1; // Still take the tab into account
					$value = mb_substr($value, $mbcc);
					break;
				}
			}
		}
		if (!empty($value)) {
			$lines[] = $value;
		}
		return join($lines, "\n ");
	}

	/**
	 * escaping all meaningful characters in a descent way
	 * (reverse escaping with unescape())
	 * 
	 * 
   Some properties may contain one or more values delimited by a COMMA
   character (U+002C).  Therefore, a COMMA character in a value MUST be
   escaped with a BACKSLASH character (U+005C), even for properties that
   don't allow multiple instances (for consistency).

   Some properties (e.g., N and ADR) comprise multiple fields delimited
   by a SEMICOLON character (U+003B).  Therefore, a SEMICOLON in a field
   of such a "compound" property MUST be escaped with a BACKSLASH
   character.  SEMICOLON characters in non-compound properties MAY be
   escaped.  On input, an escaped SEMICOLON character is never a field
   separator.  An unescaped SEMICOLON character may be a field
   separator, depending on the property in which it appears.

   Furthermore, some fields of compound properties may contain a list of
   values delimited by a COMMA character.  Therefore, a COMMA character
   in one of a field's values MUST be escaped with a BACKSLASH
   character, even for fields that don't allow multiple values (for
   consistency).  Compound properties allowing multiple instances MUST
   NOT be encoded in a single content line.

   Finally, BACKSLASH characters in values MUST be escaped with a
   BACKSLASH character.  NEWLINE (U+000A) characters in values MUST be
   encoded by two characters: a BACKSLASH followed by either an 'n'
   (U+006E) or an 'N' (U+004E).
	 * 
	 * @param string $sInput
	 * @return string
	 */
	public function escape($sInput)
	{
		//$sInput = str_ireplace('\\', '\\\\', $sInput);		
		$sInput = str_ireplace(',', '\,', $sInput);
		$sInput = str_ireplace(';', '\;', $sInput);
// 		$sInput = str_ireplace(':', '\:', $sInput);

		
		return $sInput;
	}
	
	/**
	 * unescaping all meaningful characters in a descent way
	 *
	 * @param string $sInput
	 * @return string
	 */
	public function unescape($sInput)
	{
		$sInput = str_ireplace('\,', ',', $sInput);
		$sInput = str_ireplace('\;', ';', $sInput);
// 		$sInput = str_ireplace('\:', ':', $sInput);
		$sInput = str_ireplace('\\\\', '\\', $sInput);
		
		return $sInput;
	}	
}
?>