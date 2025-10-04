<?php

namespace drenirie\framework\classes\vcardvcal;

use drenirie\framework\classes\types\TDateTime;

/**
 * Alarm object for VEvent
 * 
 * IMPORTANT NOTE:
 * some calendars may ignore the alarm when it is loaded from an url
 *
 * 
 */

class VAlarm extends VTag
{
	const ATTRIBUTE_ACTION 			= 'ACTION';
	const ATTRIBUTE_TRIGGER 		= 'TRIGGER';
	const ATTRIBUTE_DESCRIPTION 	= 'DESCRIPTION';
	
	const ATTRIBUTE_ACTION_VALUE_DISPLAY = 'DISPLAY';
	
	/**
	 * when you want to display a message you have to set action to ATTRIBUTE_ACTION_VALUE_DISPLAY
	 * @param string $sAction
	 */
	public function setAction($sAction = VAlarm::ATTRIBUTE_ACTION_VALUE_DISPLAY)
	{
		$this->setAttribute(VAlarm::ATTRIBUTE_ACTION, $sAction);	
	} 
	
	
	/**
	 * display this message on trigger of the alarm.
	 * dont forget to set the trigger ;-)
	 * 
	 * when you set this discription the action is automatically set to VAlarm::ATTRIBUTE_ACTION_VALUE_DISPLAY
	 * 
	 * @param string $sDescription
	 */
	public function setDescription($sDescription)
	{
		$this->setAttribute(VAlarm::ATTRIBUTE_DESCRIPTION, $sDescription);
		$this->setAction(VAlarm::ATTRIBUTE_ACTION_VALUE_DISPLAY);//just to be sure that the right action is used, otherwise nothing will be displayed		
	}
	
	/**
	 * set duration/interval for trigger
	 * 
	 * dont forget to set the description ;-)
	 * and dont forget the invert for the time interval if you want to display it before the event starts
	 * 
	 * @param \DateInterval $objInterval
	 */
	public function setTrigger(\DateInterval $objInterval)
	{
		$this->setAttribute(VAlarm::ATTRIBUTE_TRIGGER, $this->intervalToStr($objInterval));
	}
	
	

	public function getName()
	{
		return 'VALARM';
	}
}

?>