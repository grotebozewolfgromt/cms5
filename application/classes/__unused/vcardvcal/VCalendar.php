<?php
namespace drenirie\framework\classes\vcardvcal;

/**
 * core object for iCalendar following RFC 2445 standard
 * more info on standard: https://tools.ietf.org/html/rfc2445
 * 
 * is UTF 8 standard
 * iCalendar data has the MIME content type text/calendar.
 * filenames have ics extension
 * 
 * 
 * 
 */

class VCalendar extends VTag
{
	const ATTRIBUTE_VERSION 	= 'VERSION';
	const ATTRIBUTE_PRODID 		= 'PRODID';
		
	const ATTRIBUTE_VERSION_VALUE = '2.0';
	const ATTRIBUTE_PRODID_VALUE 	= 'DR VCalendar Module WebApp3';
	
	public function __construct()
	{
		$this->setAttribute(VCalendar::ATTRIBUTE_VERSION, VCalendar::ATTRIBUTE_VERSION_VALUE);
		$this->setAttribute(VCalendar::ATTRIBUTE_PRODID, VCalendar::ATTRIBUTE_PRODID_VALUE);
	}
	
	public function getName()
	{
		return 'VCALENDAR';
	}
	
	public function addEvent(VEvent &$objEvent)
	{
		$this->addTag($objEvent);
	}
	
	/**
	 * 
	 * @param string $sFilename with .icf extension
	 * @return boolean
	 */
	public function renderToFile($sFilename)
	{
		return (file_put_contents($sFilename, $this->renderToString()) == true);
	}
	
	/**
	 * render calendar
	 */
	public function renderToScreen()
	{
		header('Content-Type: text/calendar');
		echo $this->renderToString();
	}
	

}
?>
