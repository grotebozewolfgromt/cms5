<?php
/**
 * In this library exist the functions that didn't fit in any other libraries
 * "The misfits and the rebels" :)
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * 11 juli 2012: isUTF8String () toegevoegd
 * 25 juli 2021: lib_misc: preventTimingAttack() toegevoegd
 * 25 juli 2021: lib_misc: preventTimingAttack() supports larger than 1 second
 * 
 * @author Dennis Renirie
 */

//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');

/**
 * no-operation, in other words, do nothing.
 * Sometimes if-statements are more readable when you use noop()
 * if ($blabla == true)
 *     noop()
 * else
 *     doSomething()
 */
function noop()
{
}

/**
 * retrieve the next sortorder by passing the previous sortorder
 * (i.e. for sorting database columns)
 * 
 * @param string $sCurrentSortOrder sortorders from lib_type (SORT_ORDER_NONE, SORT_ORDER_ASC, SORT_ORDER_DESC)
 * @return string (SORT_ORDER_NONE, SORT_ORDER_ASC, SORT_ORDER_DESC)
 */
function getNextSortOrder($sCurrentSortOrder = SORT_ORDER_NONE)
{
	switch ($sCurrentSortOrder)
	{
		case SORT_ORDER_ASCENDING:
			return SORT_ORDER_DESCENDING;
		case SORT_ORDER_DESCENDING:
			return SORT_ORDER_NONE;					
		default: //SORT_ORDER_NONE or something else we dont accept
			return SORT_ORDER_ASCENDING; 
	}
}


/**
 * avoid a time attack by waiting
 * 
 * In every situation you want to do checks and then 'return' out of a function as soon as you know that 
 * the result of the function will never change.
 * This in order to save system resources.
 * i.e. When you are in a loop to find a certain value, you want to go out of the loop as soon 
 * as you found the value and dont want to complete the whole loop.
 * 
 * When it comes to security, this is not always desireable, since the time that it takes to do the checks
 * can give away valuable information to an attacker.
 * You can still do all the checks and hog up system resources to prevent this from happening, 
 * or you can wait and don't waste system resources.
 * This is waiting is what this function does for you
 * 
 * The maximum waiting time is 1 second, the times are less accurate (due to OS restrictions)
 * 
 * @param int $iMinWaitingTimeMS the number of MilliSeconds that this function should always wait
 * @param int $iMaxWaitingTimeMS the maximum number of MilliSeconds that this function should always wait
 * @return void
 */
function preventTimingAttack($iMinWaitingTimeMS = 0, $iMaxWaitingTimeMS = 100)
{
    $iWaitMS = 0;
    
    //prevent max from being bigger than min
    if ($iMaxWaitingTimeMS > $iMinWaitingTimeMS)
        $iMinWaitingTimeMS = 0;

    //calculate milliseconds wait
    $iWaitMS = random_int($iMinWaitingTimeMS, $iMaxWaitingTimeMS);

    //Note from php.net: Values larger than 1000000 (i.e. sleeping for more than a second) may not be supported by the operating system. Use sleep() instead. 
    if ($iWaitMS > 1000) //1000 instead of 1000000, because we are working with Milliseconds, instead of Microseconds
		sleep((int)($iWaitMS /1000));//sleep() is in seconds, but we lose precision
	else        
	    usleep($iWaitMS * 1000);//usleep() is in microseconds
}



?>
