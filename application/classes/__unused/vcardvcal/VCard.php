<?php
namespace drenirie\framework\classes\vcardvcal;

use drenirie\framework\classes\types\TDateTime;

/**
 * core object for VCard version 4 following RFC 6350 standard
 * more info on standard: https://tools.ietf.org/html/rfc6350
 * 
 * is UTF 8 standard
 * iCalendar data has the MIME content type text/calendar.
 * filenames have ics extension
 * 
 * setFN() is mandatory
 * 
 * @todo add more fields 
 */

class VCard extends VTag
{
	
	const ATTRIBUTE_VERSION 	= 'VERSION';
	const ATTRIBUTE_PRODID 		= 'PRODID';	
	const ATTRIBUTE_FN 			= 'FN';
	const ATTRIBUTE_N 			= 'N';
	const ATTRIBUTE_BDAY 		= 'BDAY';
	const ATTRIBUTE_GENDER		= 'GENDER';
	
	const ATTRIBUTE_EMAIL		= 'EMAIL';
	const ATTRIBUTE_ADR			= 'ADR';//address
	const ATTRIBUTE_TEL			= 'TEL';
	const ATTRIBUTE_URL			= 'URL';

	const ATTRIBUTE_VERSION_VALUE 		= '4.0';
	const ATTRIBUTE_PRODUCTID_VALUE 	= 'DR VCard Module WebApp3';
	const ATTRIBUTE_GENDER_VALUE_MALE 	= 'M'; 
	const ATTRIBUTE_GENDER_VALUE_FEMALE = 'F';
	const ATTRIBUTE_GENDER_VALUE_OTHER 	= 'O';
	const ATTRIBUTE_GENDER_VALUE_NONE	 = 'N';
	const ATTRIBUTE_GENDER_VALUE_UNKNOWN = 'U';


	
	public function __construct()
	{
		$this->setAttribute(VCard::ATTRIBUTE_VERSION, VCard::ATTRIBUTE_VERSION_VALUE);
		$this->setAttribute(VCard::ATTRIBUTE_PRODID, VCard::ATTRIBUTE_PRODUCTID_VALUE);
		$this->setAttribute(VCard::ATTRIBUTE_FN, '');
		
		$this->setMultipleAttributesAllowed(array(
			VCard::ATTRIBUTE_EMAIL,
			VCard::ATTRIBUTE_ADR,
			VCard::ATTRIBUTE_TEL,
			VCard::ATTRIBUTE_URL
		));
	}
	
	public function getName()
	{
		return 'VCARD';
	}
	
	public function renderToString()
	{
		if ($this->getAttribute(VCard::ATTRIBUTE_FN) == '')
			logThis('VCard: attribute FN is mandatory');
		
		return parent::renderToString();
	}
	
	/**
	 * 
	 * @param string $sFilename with .vcf extension
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
		header('Content-Type: text/vcard');
		echo $this->renderToString();
	}
	
		
	/**
	 * 
	 * To specify the formatted text corresponding to the name of
      the object the vCard represents
      This can be the full name of a company or the full name of a person
       
	 * @param string $sValue
	 */
	public function setFN($sValue)
	{
		$this->setAttribute(VCard::ATTRIBUTE_FN, $sValue);
	}
	
	/**
	 * The structured property value corresponds, in
      sequence, to the Family Names (also known as surnames), Given
      Names, Additional Names, Honorific Prefixes, and Honorific
      Suffixes
      
	 * @param array $arrValues
	 */
	public function setN($arrValues)
	{
		$this->setAttribute(VCard::ATTRIBUTE_N, $arrValues);
	}
	
	/**
	 * 
	 * @param string $sLastName
	 * @param string $sFirstName
	 * @param string $sPrefix
	 */
	public function setName($sLastName, $sFirstName = '', $sPrefix = '', $arrTheRest = array())
	{
		$arrNew = array();
		$arrNew[] = $sLastName;
		$arrNew[] = $sFirstName;
		$arrNew[] = $sPrefix;
		array_merge($arrNew, $arrTheRest);
		$this->addAttributeArray(VCard::ATTRIBUTE_N, VCard::ATTRIBUTE_N, $arrNew);
	}
	
	public function setBDay(TDateTime &$objDateTime)
	{
		$this->setAttribute(VCard::ATTRIBUTE_BDAY, $this->dateTimeToStr($objDateTime));
	}
	
	/**
	 * possible values:
	 * ATTRIBUTE_GENDER_VALUE_MALE
	 * ATTRIBUTE_GENDER_VALUE_FEMALE
	 * ATTRIBUTE_GENDER_VALUE_OTHER  
	 * ATTRIBUTE_GENDER_VALUE_NONE
	 * ATTRIBUTE_GENDER_VALUE_UNKNOWN
	* @param string $sValue
	*/
	public function setGender($sValue = VCard::ATTRIBUTE_GENDER_VALUE_UNKNOWN)
	{
		switch ($sValue)
		{
			case VCard::ATTRIBUTE_GENDER_VALUE_MALE:
			case VCard::ATTRIBUTE_GENDER_VALUE_FEMALE:
			case VCard::ATTRIBUTE_GENDER_VALUE_OTHER:
			case VCard::ATTRIBUTE_GENDER_VALUE_NONE:
				break;
			default:
				$sValue = VCard::ATTRIBUTE_GENDER_VALUE_UNKNOWN;
		}
		$this->setAttribute(VCard::ATTRIBUTE_GENDER, $sValue);
	}
}
?>
