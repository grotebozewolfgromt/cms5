<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\types\TDateTime;

/**
 * represents a <dr-input-datetime>
 * part of PHP counterpart for web component <dr-input-datetime>
 * 
 * 
 * EXAMPLE: create box in: populate()
 *         	$this->objDTLastContact = new DRInputDateTime();
 * 			$this->objDTLastContact->setNameAndID('dtLastContact');
 * 			$this->objDTLastContact->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
 * 			$this->objDTLastContact->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
 * 			$this->objDTLastContact->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
 * 			$this->objDTLastContact->setAllowEmptyDateTime(true);
 * 			$this->getFormGenerator()->add($this->objDTLastContact, $sFormSectionMisc, transm($this->getModule(), 'form_field_last_contact', 'Last contact')); 
 * 
 * EXAMPLE: read from box in: viewToModel();
 *         $this->getModel()->set(TSysContacts::FIELD_FIRSTCONTACT, $this->objDTFirstContact->getValueSubmittedAsTDateTimeISO());
 * 
 * EXAMPLE: set value in box in: modelToView()
 *         $this->objDTLastContact->setValueAsTDateTime($this->getModel()->get(TSysContacts::FIELD_LASTCONTACT));
 * 
 * @author Dennis Renirie
 * 5 apr 2025: DRInputDateTime created
 * 6 apr 2025: DRInputDateTime actual read and write values to box
 * 1 may 2025: DRInputDateTime getValueAsTDateTime() returned nothing
 *
 */
class DRInputDateTime extends FormInputAbstract
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-input-datetime');

		$this->setAttribute('phpformat', 'Y-m-d H:i'); //default ISO 8601
		$this->setAttribute('firstdayofweek', 0); //0=sunday, 1=monday

		//translations
		$this->setAttribute('transday', transg(TRANS_DAY_SHORTHAND_KEY, TRANS_DAY_SHORTHAND_VALUE));
		$this->setAttribute('transmonth', transg(TRANS_MONTH_SHORTHAND_KEY, TRANS_MONTH_SHORTHAND_VALUE));
		$this->setAttribute('transyear', transg(TRANS_YEAR_SHORTHAND_KEY, TRANS_YEAR_SHORTHAND_VALUE));
		$this->setAttribute('transjanuary', transg(TRANS_MONTH_JANUARY_SHORT_KEY, TRANS_MONTH_JANUARY_SHORT_VALUE));
		$this->setAttribute('transfebruary', transg(TRANS_MONTH_FEBRUARY_SHORT_KEY, TRANS_MONTH_FEBRUARY_SHORT_VALUE));
		$this->setAttribute('transmarch', transg(TRANS_MONTH_MARCH_SHORT_KEY, TRANS_MONTH_MARCH_SHORT_VALUE));
		$this->setAttribute('transapril', transg(TRANS_MONTH_APRIL_SHORT_KEY, TRANS_MONTH_APRIL_SHORT_VALUE));
		$this->setAttribute('transmay', transg(TRANS_MONTH_MAY_SHORT_KEY, TRANS_MONTH_MAY_SHORT_VALUE));
		$this->setAttribute('transjune', transg(TRANS_MONTH_JUNE_SHORT_KEY, TRANS_MONTH_JUNE_SHORT_VALUE));
		$this->setAttribute('transjuly', transg(TRANS_MONTH_JULY_SHORT_KEY, TRANS_MONTH_JULY_SHORT_VALUE));
		$this->setAttribute('transaugust', transg(TRANS_MONTH_AUGUST_SHORT_KEY, TRANS_MONTH_AUGUST_SHORT_VALUE));
		$this->setAttribute('transseptember', transg(TRANS_MONTH_SEPTEMBER_SHORT_KEY, TRANS_MONTH_SEPTEMBER_SHORT_VALUE));
		$this->setAttribute('transoctober', transg(TRANS_MONTH_OCTOBER_SHORT_KEY, TRANS_MONTH_OCTOBER_SHORT_VALUE));
		$this->setAttribute('transnovember', transg(TRANS_MONTH_NOVEMBER_SHORT_KEY, TRANS_MONTH_NOVEMBER_SHORT_VALUE));
		$this->setAttribute('transdecember', transg(TRANS_MONTH_DECEMBER_SHORT_KEY, TRANS_MONTH_DECEMBER_SHORT_VALUE));
		$this->setAttribute('transmonday', transg(TRANS_WEEKDAY_MONDAY_SHORT_KEY, TRANS_WEEKDAY_MONDAY_SHORT_VALUE));
		$this->setAttribute('transtuesday', transg(TRANS_WEEKDAY_TUESDAY_SHORT_KEY, TRANS_WEEKDAY_TUESDAY_SHORT_VALUE));
		$this->setAttribute('transwednesday', transg(TRANS_WEEKDAY_WEDNESDAY_SHORT_KEY, TRANS_WEEKDAY_WEDNESDAY_SHORT_VALUE));
		$this->setAttribute('transthursday', transg(TRANS_WEEKDAY_THURSDAY_SHORT_KEY, TRANS_WEEKDAY_THURSDAY_SHORT_VALUE));
		$this->setAttribute('transfriday', transg(TRANS_WEEKDAY_FRIDAY_SHORT_KEY, TRANS_WEEKDAY_FRIDAY_SHORT_VALUE));
		$this->setAttribute('transsaturday', transg(TRANS_WEEKDAY_SATURDAY_SHORT_KEY, TRANS_WEEKDAY_SATURDAY_SHORT_VALUE));
		$this->setAttribute('transsunday', transg(TRANS_WEEKDAY_SUNDAY_SHORT_KEY, TRANS_WEEKDAY_SUNDAY_SHORT_VALUE));
		$this->setAttribute('transyesterday', transg(TRANS_DAY_YESTERDAY_KEY_FULL, TRANS_DAY_YESTERDAY_VALUE_FULL));
		$this->setAttribute('transtoday', transg(TRANS_DAY_TODAY_KEY_FULL, TRANS_DAY_TODAY_VALUE_FULL));
		$this->setAttribute('transtomorrow', transg(TRANS_DAY_TOMORROW_KEY_FULL, TRANS_DAY_TOMORROW_VALUE_FULL));
		$this->setAttribute('transam', transg(TRANS_AMPM_AM_KEY, TRANS_AMPM_AM_VALUE));
		$this->setAttribute('transpm', transg(TRANS_AMPM_PM_KEY, TRANS_AMPM_PM_VALUE));
		$this->setAttribute('transampm', transg(TRANS_AMPM_PM_SHORTHAND_KEY, TRANS_AMPM_PM_SHORTHAND_VALUE)); //placeholder meaning am or pm
		$this->setAttribute('transhours', transg(TRANS_HOUR_SHORTHAND_KEY, TRANS_HOUR_SHORTHAND_VALUE));
		$this->setAttribute('transminutes', transg(TRANS_MINUTE_SHORTHAND_KEY, TRANS_MINUTE_SHORTHAND_VALUE));
		$this->setAttribute('transseconds', transg(TRANS_SECOND_SHORTHAND_KEY, TRANS_SECOND_SHORTHAND_VALUE));
		$this->setAttribute('transnow', transg(TRANS_TIME_NOW_KEY_FULL, TRANS_TIME_NOW_VALUE_FULL));

		//proper includes
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-date'.DIRECTORY_SEPARATOR.'style.css');
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-time'.DIRECTORY_SEPARATOR.'style.css');
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-datetime'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-date'.DIRECTORY_SEPARATOR.'dr-input-date.js');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-time'.DIRECTORY_SEPARATOR.'dr-input-time.js');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-datetime'.DIRECTORY_SEPARATOR.'dr-input-datetime.js');

	}



	/**
	 * set php date format
	 */
	public function setPHPDateFormat($sDateTimeFormat)
	{
		$this->setAttribute('phpdateformat', $sDateTimeFormat);
	}

	/**
	 * get php date format
	 */
	public function getPHPDateFormat()
	{
		return $this->getAttribute('phpdateformat');
	}

	/**
	 * set empty date allowed
	 */
	public function setAllowEmptyDateTime($bAllowed)
	{
		if ($bAllowed)
			$this->setAttribute('allowemptydatetime', '');
		else
			$this->removeAttribute('allowemptydatetime');
	}

	/**
	 * set empty date allowed
	 */
	public function getAllowEmptyDateTime()
	{
		if ($this->hasAttribute('allowemptydatetime'))
			return $this->getAttributeAsBool('allowemptydatetime');
		else
			return false;
	}	

	/**
	 * set php time format
	 */
	public function setPHPTimeFormat($sDateTimeFormat)
	{
		$this->setAttribute('phptimeformat', $sDateTimeFormat);
	}

	/**
	 * get php time format
	 */
	public function getPHPTimeFormat()
	{
		return $this->getAttribute('phptimeformat');
	}

	/**
	 * set php date format
	 */
	public function setFirstDayOfWeek($iFirstDayWeek)
	{
		$this->setAttributeAsInt('firstdayofweek', $iFirstDayWeek);
	}

	/**
	 * get php date format
	 */
	public function getFirstDayOfWeek()
	{
		return $this->getAttributeAsInt('firstdayofweek');
	}


	/**
	 * set datetime value as ISO 8601
	 */
	public function setValue($sValue)
	{
		$this->setAttribute('value', $sValue);
	}

	/**
	 * get datetime value as ISO 8601
	 */
	public function getValue()
	{
		return $this->getAttribute('value');
	}

	/**
	 * set value as TDateTime object
	 */
	public function setValueAsTDateTime($objDateTime)
	{
		if ($objDateTime == null)
		{
			$this->setAttribute('value', '');
		}
		else
		{
			if ($objDateTime->isEmpty())
				$this->setAttribute('value', '');
			else
				$this->setAttribute('value', $objDateTime->getISOString());
		}

	}

	/**
	 * get datetime as TDateTime
	 */
	public function getValueAsTDateTime()
	{
		$objDateTime = new TDateTime();
		$objDateTime->setISOString($this->getAttribute('value'));
		return $objDateTime;
	}	
	
}

?>
