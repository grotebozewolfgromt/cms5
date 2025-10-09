<?php
/**
 * In this library exist only math related functions
 * 
 * 20 juli 2012: conversies voor BTW weg, deze zijn verhuisd naar TCurrency
 * 4 april 2019: compare float moved from lib_types
 * 13 april 2019: getChance() added
 * 13 april 2010: isEqual() added
 * 13 april 2019: compareFloat() moved to here out of lib_types
 * 25 jun 2021: ib_math: isFloatGreaterThan() and isFloatLessThan() added
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * @author Dennis Renirie
 */


//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');

/**
 * random, ook als de range bijv 1..1 is (de functie rand() ondersteund dit niet); voor verdere werking zie standaard functie rand() in PHP
 * @param int $iMin
 * @param int $iMax
 * @return int
 */
function random($iMin, $iMax)
{
        if ($iMin == $iMax)
        {
                $iResult = $iMin;
        }

        if ($iMin > $iMax) //als min en max omgedraaid zijn, dan terugdraaien
        {
                $iTemp = $iMin;
                $iMin = $iMax;
                $iMax = $iTemp;
        }

        if ($iMin < $iMax)
        {
                $iResult = rand($iMin, $iMax);
        }

        return $iResult;
}



/**
 * in the ini sometime says 64M, wich means 64*1024*1024 bytes
 * this function calculates it back to bytes
 * 
 * @param string $sReadableSize
 * @return int size in bytes
 */
function convertReadableBinairySizeToBytes($sReadableSize)
{
    $iMultiplyFactor = substr($sReadableSize, -1);
    $iMultiplyFactor = ($iMultiplyFactor == 'M' ? 1048576 : ($iMultiplyFactor == 'K' ? 1024 : ($iMultiplyFactor == 'G' ? 1073741824 : 1)));

    $iDigits = (int)removeLastChar($sReadableSize);

    return $iDigits * $iMultiplyFactor;
}

/**
 * returns true or false, depending on the odds.
 * How likely this function is to return true is $fPercentage %
 * so, getChance(100) will always return true
 * and getChange(0) will never return true
 * 
 * 
 * @param float $fPercentage percentage of this function returning true
 * @return boolean true or false
 */
function getChance($fPercentage)
{  
    $iRandom = 0;
    $iOdds = 0;
    $fOdds = 0.0;
    $bResult = false;
 
    if(!isEqual($fPercentage, 0.0, 0.1)) //prevent division by zero
    {

        $fOdds = (100 / $fPercentage);
        $iOdds = (int)round( $fOdds , 10, PHP_ROUND_HALF_UP); //chance is 1 in 4 with 25% (100/25 = 4)   
        $iRandom = random(1, $iOdds);

        $bResult = ($iRandom == 1); //it can be any number, but i took 1
    }
    
    return $bResult;
}

/**
 * compare to FLOATS for equality withing a certain range ($fPrecisionEpsilon)
 * isEqual(1.2345, 1.2367, 0.001) returns false
 * isEqual(1.2345, 1.2367, 0.01) returns true
 * 
 * @param float $fFloat1 for example 1.23456789
 * @param float $fFloat2 for example 1.23456780
 * @param float $fPrecisionEpsilon for example 0.00001
 * @return boolean 
 */
function isEqual($fFloat1, $fFloat2, $fPrecisionEpsilon = 0.00001)
{
    if(abs($fFloat1-$fFloat2) < $fPrecisionEpsilon) 
    {
        return true;
    }
    else
        return false;
}

/**
 * compare 2 float values
 * 
 * returns:
 * 1 : $fFloatOne > $fFloatTwo
 * 0 : $fFloatOne == $fFloatTwo
 * -1: $fFloatOne < $fFloatTwo
 * 
 * this function exists because:
 * bccomp is a part of bcmath, and bcmath is a separate module, wich may not be installed on the hosting provider.
 * if it is not installed, you can rewrite this function
 * 
 * @param float $fFloatOne
 * @param float $fFloatTwo
 * @param int $iScale precision in digits after the decimal-separator (2 = 2 digits after decimal separator)
 * @param int 
 */
function compareFloat($fFloatOne, $fFloatTwo, $iScale)
{   
    return bccomp($fFloatOne, $fFloatTwo, $iScale);
}

/**
 * compare floats:
 * is float1 greater than float2?
 * 
 * @param float $fFloatOne
 * @param float $fFloatTwo
 * @param int $iScale precision in digits after the decimal-separator (2 = 2 cijfers achter de komma precisie)
 * @param bool
 */
function isFloatGreaterThan($fFloatOne, $fFloatTwo, $iScale)
{
    return (compareFloat($fFloatOne, $fFloatTwo, $iScale) == 1);
}

/**
 * compare floats:
 * is float1 less than float2?
 * 
 * @param float $fFloatOne
 * @param float $fFloatTwo
 * @param int $iScale precision in digits after the decimal-separator (2 = 2 cijfers achter de komma precisie)
 * @param bool
 */
function isFloatLessThan($fFloatOne, $fFloatTwo, $iScale)
{
    return (compareFloat($fFloatOne, $fFloatTwo, $iScale) == -1);
}



?>
