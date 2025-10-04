<?php
namespace drenirie\framework\classes\vcardvcal;

use drenirie\framework\classes\types\TDateTime;

/**
 * event object for VCalendar class
 * 
 * @todo add more fields
 */

class VEvent extends VTag
{
	const ATTRIBUTE_UID 	= 'UID';
	const ATTRIBUTE_DTSTART = 'DTSTART';
	const ATTRIBUTE_DTEND 	= 'DTEND';
	const ATTRIBUTE_SUMMARY = 'SUMMARY';
	const ATTRIBUTE_TRANSP	= 'TRANSP';//tranparancy: OPAQUE (default) or TRANSPARENT
	const ATTRIBUTE_URL		= 'URL';
	const ATTRIBUTE_DESCRIPTION = 'DESCRIPTION';
	
	const ATTRIBUTE_TRANSP_VALUE_OPAQUE = 'OPAQUE'; //default ->Blocks or opaque on busy time searches.
	const ATTRIBUTE_TRANSP_VALUE_TRANSPARANT = 'TRANSPARENT';//->Transparent on busy time searches.
	
	public function getName()
	{
		return 'VEVENT';
	}
	
	public function setUID($sValue)
	{
		$this->setAttribute(VEvent::ATTRIBUTE_UID, $sValue);
	}
	
	public function setDTStart(TDateTime &$objDate)
	{
		$this->setAttribute(VEvent::ATTRIBUTE_DTSTART, $this->dateTimeToStr($objDate));
	}
	
	public function setDTEnd(TDateTime &$objDate)
	{
		$this->setAttribute(VEvent::ATTRIBUTE_DTEND, $this->dateTimeToStr($objDate));
	}	
	
	public function setSummary($sValue)
	{
		$this->setAttribute(VEvent::ATTRIBUTE_SUMMARY, $sValue);
	}
		
	/**
	 * set transparacy
	 * This property defines whether an event is transparent or not
   	 * to busy time searches.
   	 * if $sValue is not ATTRIBUTE_TRANSP_VALUE_TRANSPARANT or ATTRIBUTE_TRANSP_VALUE_OPAQUE
   	 * then ATTRIBUTE_TRANSP_VALUE_OPAQUE is assumed
   	 * 
   	 * transvalue = "OPAQUE"      ;Blocks or opaque on busy time searches.
                / "TRANSPARENT" ;Transparent on busy time searches.
        ;Default value is OPAQUE
	 * @param string $sValue
	 */
	public function setTransp($sValue = VEvent::ATTRIBUTE_TRANSP_VALUE_OPAQUE)
	{
		if (($sValue != VEvent::ATTRIBUTE_TRANSP_VALUE_OPAQUE) && ($sValue != VEvent::ATTRIBUTE_TRANSP_VALUE_TRANSPARANT))
			$sValue = ATTRIBUTE_TRANSP_VALUE_OPAQUE;
		 
		$this->setAttribute(VEvent::ATTRIBUTE_TRANSP, $sValue);
	}
	
	public function setURL($sValue)
	{
		$this->setAttribute(VEvent::ATTRIBUTE_URL, $sValue);
	}
	
	public function setDescription($sValue)
	{
		$this->setAttribute(VEvent::ATTRIBUTE_DESCRIPTION, $sValue);
	}
	
	public function addAlarm(VAlarm &$objAlarm)
	{
		$this->addTag($objAlarm);
	}
}
?>
