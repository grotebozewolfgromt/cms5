<?php
/**
 * In this library exist only type related functions, such as type conversion
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * 20 juli 2012: lib_types: compareFloat() added
 * 24 okt 2012: lib_types: type definities toegevoegd
 * 25 okt 2012: lib_types: paar kleine wijzingen mbt type conversies. er werd gewerkt met een whitelist (wat fout ging): nu wordt direct geprobeerd te casten
 * 10 apr 2013: lib_types: CT_ types verhuisd van TColumnType naar lib_types
 * 3 mei 2014: lib_types: is32BitInteger toegevoegd
 * 3 mei 2014: lib_types: filterValidNumber toegevoegd
 * 4 mei 2014: lib_types: filterValidNumber opgesplitst in filterValidFloat en filterValidInt. Beide functies maken gebruik van filter_var ipv eigen geschreven filter functie
 * 26 juni 2015: lib_types: performance aanpassingen strToInt() boolToInt() en intToBool()
 * 6 juli 2015: lib_types: REGEX_VALID_CLASSNAME, REGEX_VALID_FUNCTIONNAME toegevoegd 
 * 15 juli 2015: lib_types: REGEX_* constanten helemaal verwijderd. FILTEREXT_SANITIZE_* voor in de plaats gekomen
 * 3 sept 2015: lib_types: join types toegevoegd
 * 28 okt 2015: lib_types: comparison operator IN en NOT IN toegevoegd 
 * 4 april 2019: split lib_types into lib_types and lib_typedef
 * 13 april 2019: lib_types: moved compare float to lib_math
 * 13 nov 2020; lib_types: boolToStr() behaviour changed, it returns '0' or '1' by default instead of 'true' and 'false'
 * 3 nov 2021: lib_types: function is_hex(&$sHexString) added
 * 1 mrt 2022: lib_types: added: issetXXX functions
 * 15 mrt 2024: lib_types: is32BitInteger faster check
 * 15 mrt 2024: lib_types: is32BitInteger ==> isPHP32BitInteger
 * 15 apr 2024: lib_types: strToInt() extra parameter to correct invalid integers
 * 
 * @author Dennis Renirie
 */

//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_file.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');


/**
 * turn defined constant (defined in this library) into a readable string
 * 
 * @param int $iType
 */
function typeToStr($sLibTypesConstant = TP_INTEGER)
{
	
	switch ($sLibTypesConstant)
	{
		case TP_STRING:
			return 'string/longtext (type)';
		case TP_INTEGER:
			return 'integer (type)';
		case TP_BOOLEAN:
			return 'boolean (type)';
		case TP_DOUBLE:
			return 'float/double (type)';
		case TP_BINARY:
			return 'binary (type)';
		case TP_DATETIME:
			return 'datetime (type)';
		case TP_ARRAY:
			return 'array (type)';
		case TP_OBJECT:
			return 'object (type)';
		case TP_DECIMAL:
			return 'decimal (type)'; //technically currency and decimal are the same. We make a distinction because of display and input. an amount wil be displayed and inputted as '1' instead of '1.00', while that would be the desired behavior for money
		case TP_CURRENCY:
			return 'currency (type)'; //technically currency and decimal are the same. We make a distinction because of display and input. an amount wil be displayed and inputted as '1' instead of '1.00', while that would be the desired behavior for money
		case CT_VARCHAR:
			return 'varchar (column type)';			
		case CT_INTEGER32:
			return 'integer (32 bit)(column type)';
		case CT_FLOAT:
			return 'float (32 bit)(column type)';
		case CT_ENUM:
			return 'enum (column type)';					
		default:
			return 'unknown constant';
	}	
}


/**
 * converts a string to a float
 * it's stronger in conversion than a typecast, because it takes account of the
 * dutch decimal separator
 *
 * @param string $sValue
 * @param string $sDecimalSeparator decimaal scheidingsteken
 * @return float
 */
function strToFloat($sValue, $sDecimalSeparator = ',')
{
    
    try
    {
        if ($sValue != '')
        {
            $sValue = str_replace($sDecimalSeparator, '.', $sValue);
            return (float)$sValue;
        }
        else
            return 0.0;
    }
    catch (Exception $objException)
    {
        return 0.0;
    }
}

/**
 * converts a float to a string. you can specify the precision (digits after the
 * floating point separator)
 *
 * @param float $fValue
 * @param int $iNumberDigitsAfterDecimalSeparator aantal cijfers achter te komma
 * @param string $sDecimalSeparator decimaal scheidingsteken
 * @param string $sThousandSeparator duizendtal scheidingsteken
 * @return string
 */
function floatToStr($fValue, $iNumberDigitsAfterDecimalSeparator = 2, $sDecimalSeparator = ',', $sThousandSeparator = '')
{
    $sValue = number_format($fValue, $iNumberDigitsAfterDecimalSeparator, $sDecimalSeparator, $sThousandSeparator);
}

/**
 * convert a boolean to a string
 * 
 * WARNING: 
 * THIS IS NOT LOCALE DEPENDENT. 
 * It will ALWAYS return English text
 * 
 * true becomes 1
 * false becomes 0
 * with bReturnAsText == true
 * true becomes 'true'
 * false becomes 'false'
 *
 * This is a counter version of strToBool()
 * 
 * @param boolean $bValue
 * @param boolean $bReturnAsText
 * @return string
 */
function boolToStr($bValue, $bReturnAsText = false)
{
    if ($bReturnAsText)
    {
        if ($bValue == true)
            return 'true';
        else
            return 'false';
    }
    else
    {
        if ($bValue == true)
            return '1';
        else
            return '0';
    }
}

/**
 * convert a string to a boolean
 *
 * WARNING: 
 * THIS IS NOT LOCALE DEPENDENT. 
 * It NEEDS English text 
 * (like produced with boolToStr())
 * 
 * This is a counter version of boolToStr()
 * 
 * @param string $sValue
 * @return bool
 */
function strToBool($sValue)
{
    if (strlen($sValue) > 0)
    {
        if (is_numeric($sValue))
        {
            if ((int)$sValue >= 1)
                return true;
            else
                return false;
        }
        else //non numeric
        {
            if (($sValue == 'true') || ($sValue == 'TRUE'))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    else
        return false;
}

/**
 * convert an int to bool
 *
 * @param int $iValue
 * @return bool
 */
function intToBool($iValue)
{
	return (bool)$iValue;
}

/**
 * convert a boolean to an integer
 *
 * @param bool $bValue
 * @return int
 */
function boolToInt($bValue)
{
	//tests wijzen uit dat (int)$bValue ongeveer even snel is als if()
	
    if ($bValue)
        return 1;
    else
        return 0;
}

/**
 * convert a integer to a string
 *
 * @param int $iValue
 * @return string
 */
function intToStr($iValue)
{
    return (string) $iValue;
}

/**
 * convert a string to an integer
 *
 * @param string $sValue
 * @param boolean $bSanitizeInput ==> is slower but corrects invalid inputs
 * @return int
 */
function strToInt($sValue, $bSanitizeInput = false)
{
    if ($bSanitizeInput)
    {
        $sValue = filterBadCharsWhiteList($sValue, REGEX_NUMERIC, true);

        if (!is_numeric($sValue))
            return 0;
    }

	return (int)$sValue;
}


/**
 * converts string into hexadecimal value
 * 
 * wrapper for bin2hex()
 */
function strToHex($string)
{
    return bin2hex($string);
}

/**
 * converts hexadecimal value to string
 * 
 * wrapper for hex2bin()
 */
function hexToStr($hHexVal)
{
    return hex2bin($hHexVal);
}

/**
 * tegenovergestelde van de functie bin2hex
 * in php is er geen functie die dit doet
 *
 * @param string $sHexString hexadecimale string
 * @return string  binaire string
 */
// if (!function_exists ('hex2bin'))
// {
//     function hex2bin($sHexString)
//     {
//             return pack("H*", $sHexString);
//     }
// }

/**
 * vervanging van array_unique van php, want deze delete alleen de dubbele waardes uit de array, maar de keys blijven hetzelfde
 *
 * @param array $arrData
 * @return array
 */
function arrayunique($arrData)
{
        $arrUnique = null;

        for ($iTeller = 0; $iTeller < count($arrData); $iTeller++) //voor elk element in de array kijken
        {
                $sValue = $arrData[$iTeller];
                $bKomtVoor = false;
                for ($iTeller2 = 0; $iTeller2 < count($arrData); $iTeller2++)
                {
                        if (($arrData[$iTeller2] == $sValue) && ($iTeller != $iTeller2))//als waarde voorkomt in de array (en het is niet zijn eigen waarde)
                                $bKomtVoor = true;
                }

                if (!$bKomtVoor)
                        $arrUnique[] = $sValue;
        }

        return $arrUnique;
}


/**
 * enum() - a function for generating type safe, iterable, singleton enumerations.
 *
 * enum() will create count($args) + 2 classes in the global namespace. It creates
 * an abstract base class with the name $base_class. This class is given static
 * methods with the names of each of the enum values.
 *
 * A class is created for each enum value that extends $base_class. Each of these
 * classes are singletons and contain a single private field: a string containing
 * the name of the class.
 *
 * Finally, an iterator is created (accessible via the ::iterator() method on
 * $base_class). This method returns a singleton iterator for the enum, usable
 * with foreach.
 *
 * C Example:
 *     typedef enum { Male, Female } Gender;
 *     Gender g = Male;
 *     switch (g) {
 *     case Male: printf("it's a dude.\n"); break;
 *     case Female: printf("it's a lady\n"); break;
 *     }
 *
 * PHP Equivalent:
 *     enum('Gender', array('Male', 'Female'));
 *     $g = Gender::Male();
 *     switch ($g) {
 *     case Gender::Male(): echo 'it\'s a dude', PHP_EOL; break;
 *     case Gender::Female(): echo 'it\'s a lady', PHP_EOL; break;
 *     }
 *
 * You can also extend Enums to more specif values. You may have care makes and
 * would like specific models:
 *
 *     enum('CarType', array('Audi', 'BMW', 'Mercedes'));
 *     enum('AudiType extends CarType', array('A4', SR6'));
 *
 * Looping through will only include the immediate decendents of an enum type
 *
 *     php% foreach (CarType::iterator() as $type) { echo $type, ' '; }
 *     => Audi, 'BMW', 'Mercedes'
 *     php% foreach (AudiType::iterator() as $type) { echo $type, ' '; }
 *     => A4, SR6
 *
 * By default, the values of each of the enums are integers 0 through count - 1,
 * like C's default enumeration values. You can however, specify any scalar value,
 * by setting the key of the array of enum values. For example, if you wanted to
 * use the strings 'm' and 'f' as the values for the respective values in
 * the Gender enumeration, you would call enum with the following parameters:
 *
 *     enum('Gender', array('f' => 'Female', 'm' => 'Male'));
 *
 * Values are limited to numeric and string data. If future versions of PHP
 * support array keys of additional data types, enum() automatically support those
 * as well.
 *
 * You can compare by value or class
 *     $g = Gender::male();
 *     $g === Gender::male();   # true
 *     $g === Male::instance(); # true
 *     $g instanceof Gender;    # true
 *     $g instanceof Male;      # true
 *     $g === Gender::Female()  # false
 *
 * And you can use any of classes generated as type hints in function signatures
 *     class Person {
 *         private $gender;
 *         public function __construct(Gender $g) { $this->gender = $g; }
 *         public function gender() { return $this->gender }
 *     }
 *     function for_guys_only(Male $gender) {
 *         // runtime triggers error on instanceof Female
 *     }
 *
 * @author Jonathan Hohle, http://hohle.net
 * @since 5.June.2008
 * @license MIT License
 *
 * @param $base_class enumeration name
 * @param $args array of enum values
 * @return nothing
 */
function enum($base_class, array $args)
{
    $class_parts = preg_split('/\s+/', $base_class);
    $base_class_name = array_shift($class_parts);
    $enums = array();

    foreach ($args as $k => $enum) {
        $static_method = 'public static function ' . $enum .
            '() { return ' . $enum . '::instance(); }';
        $enums[$static_method] = '
            class ' . $enum . ' extends ' . $base_class_name . '{
                private static $instance = null;
                protected $value = "' . addcslashes($k, '\\') . '";
                private function __construct() {}
                private function __clone() {}
                public static function instance() {
                    if (self::$instance === null) { self::$instance = new self(); }
                    return self::$instance;
                }
            }';
    }

    $base_class_declaration = sprintf('
        abstract class %s {
            protected $value = null;
            %s
            public static function iterator() { return %sIterator::instance(); }
            public function value() { return $this->value; }
            public function __toString() { return (string) $this->value; }
        };',
        $base_class,
        implode(PHP_EOL, array_keys($enums)),
        $base_class_name);

    $iterator_declaration = sprintf('
        class %sIterator implements Iterator {
            private static $instance = null;
            private $values = array(\'%s\');
            private function __construct() {}
            private function __clone() {}
            public static function instance() {
                if (self::$instance === null) { self::$instance = new self(); }
                return self::$instance;
            }
            public function current() {
                $value = current($this->values);
                if ($value === false) { return false; }
                return call_user_func(array(\'%s\', $value));
            }
            public function key() { return key($this->values); }
            public function next() {
                next($this->values);
                return $this->current();
            }
            public function rewind() { return reset($this->values); }
            public function valid() { return (bool) $this->current(); }
        };',
        $base_class_name,
        implode('\',\'', $args),
        $base_class_name);

    eval($base_class_declaration);
    eval($iterator_declaration);
    eval(implode(PHP_EOL, $enums));
}

/**
 * Finds whether the given variable is a native binary string
 * ONLY IN PHP VERSIONS < 6.0
 *
 * deze functie wordt toegevoegd als php versie < 6.0 is, want deze functie wordt pas geintroduceerd in 6.0
 *
 * @param mixed $mData 
 * @return bool
 */
if (!function_exists('is_binary'))
{
    function is_binary($mData)
    {
        return (strpos($mData, "\x00") == true);
    }
}


/**
 * tests if this build of php uses 32 bit integers
 */
function isPHP32BitInteger()
{
    return (PHP_INT_MAX == 2147483647);
}

/**
 * sanatizes a string to represent a valid float
 * 
 * returns '0' if $sValue == ''
 * 
 * @param $sValue
 * @return string clean string
 */
function filterValidFloat($sValue)
{
    if ($sValue == '')
        return '0';
    
    $sValue =  filter_var($sValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    if ($sValue == '')
        return '0';
    else
        return $sValue;    
}

/**
 * sanatizes a string to represent a valid int
 *  
 * returns '0' if $sValue == ''
 * 
 * @param $sValue
 * @return string clean string
 */
function filterValidInt($sValue)
{    
    if ($sValue == '') //direct eruit stappen
        return '0';
    
    $sValue = filter_var($sValue, FILTER_SANITIZE_NUMBER_INT);
    
    if ($sValue == '')
        return '0';
    else
        return $sValue;
}

/**
 * clone an array with objects 
 * returns a new array with cloned objects
 * 
 * @author some guy at stackoverflow: http://stackoverflow.com/questions/1532618/is-there-a-function-to-make-a-copy-of-a-php-array-to-another
 * 
 * @param array $array
 * @return array
 */
 function array_clone( array $array ) {
        $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = array_clone( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
} 

/**
 * remove a value from an 1d array
 * 
 * 
 * @param array $arrOrg array to delete value from
 * @param mixed $mValue value to remove
 */
function array_deletevalue($arrOrg, $mValue)
{
    $arrResult = array();
    foreach ($arrOrg as $mElement)
    {
        if ($mElement != $mValue)
            $arrResult[] = $mElement;
    }

    return $arrResult;
}


/**
 * Is string a valid hex value?
 * (compliant with is_int())
 * 
 * is wrapper for ctype_xdigit();
 * please use ctype_xdigit() instead
 *
 * 
 * @param string $sHexString
 * @return boolean
 */
function is_hex(&$sHexString) 
{
    return ctype_xdigit($sHexString);
}

/**
 * checks if variable isset and returns the variable value
 * returns null when variable is NOT set
 * 
 * this function is extremely useful for shortening if-statements, 
 * what otherwise needs 3 lines of code is now possible with just one:
 * if (issetReturn($myVar) == '123') { do your thing here }
 * 
 * @param mixed $mVariable
 * @param mixed $mDefaultValue
 * @return mixed
 */
function issetReturn(&$mVariable, $mDefaultValue = null)
{
    return (isset($mVariable)) ? $mVariable : $mDefaultValue;
}

/**
 * checks if cookie isset and returns cookie value
 * returns null when cookie is NOT set
 * 
 * this function is extremely useful for shortening if-statements, 
 * what otherwise needs 3 lines of code is now possible with just one:
 * if (issetCookieReturn('mycookie') == '123') { do your thing here }
 * 
 * @param string $sCookieName
 * @param mixed $mDefaultValue
 * @return mixed
 */
function issetCookieReturn($sCookieName, $mDefaultValue = null)
{
    return $_COOKIE[$sCookieName] ?? $mDefaultValue;
}

/**
 * checks if session variable isset and returns session value
 * returns null when session is NOT set
 * 
 * this function is extremely useful for shortening if-statements, 
 * what otherwise needs 3 lines of code is now possible with just one:
 * if (issetSessionReturn('mysessionvar') == '123') { do your thing here }
 * 
 * @param string $sSessionVariableName
 * @param mixed $mDefaultValue
 * @return mixed
 */
function issetSessionReturn($sSessionVariableName, $mDefaultValue = null)
{
    return $_SESSION[$sSessionVariableName] ?? $mDefaultValue;
}

/**
 * checks if $_GET variable isset and returns $_GET value
 * returns null when $_GET[] variable is NOT set
 * 
 * this function is extremely useful for shortening if-statements, 
 * what otherwise needs 3 lines of code is now possible with just one:
 * if (issetGETReturn('myparam') == '123') { do your thing here }
 * 
 * @param string $sGETVariableName
 * @param mixed $mDefaultValue
 * @return mixed
 */
function issetGETReturn($sGETVariableName, $mDefaultValue = null)
{
    return $_GET[$sGETVariableName] ?? $mDefaultValue;
}


/**
 * checks if $_POST variable isset and returns $_POST value
 * returns null when $_POST[] variable is NOT set
 * 
 * this function is extremely useful for shortening if-statements, 
 * what otherwise needs 3 lines of code is now possible with just one:
 * if ($sPOSTVariableName('myparam') == '123') { do your thing here }
 * 
 * @param string $sPOSTVariableName
 * @param mixed $mDefaultValue
 * @return mixed
 */
function issetPOSTReturn($sPOSTVariableName, $mDefaultValue = null)
{
    return $_POST[$sPOSTVariableName] ?? $mDefaultValue;
}

/**
 * Copy contents of array to another and cloning objects along the way
 */
function arrayCopy( array $array ) 
{
    $result = array();
    foreach( $array as $key => $val ) {
        if( is_array( $val ) ) {
            $result[$key] = arrayCopy( $val );
        } elseif ( is_object( $val ) ) {
            $result[$key] = clone $val;
        } else {
            $result[$key] = $val;
        }
    }
    return $result;
}


/**
* This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
* 
* @param string $sSize
* @return integer The value in bytes
*/
function convertPHPSizeToBytes($sSize)
{
    //
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix,array('P','T','G','M','K'))){
        return (int)$sSize;  
    } 
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P':
            $iValue *= 1024;
            // Fallthrough intended
        case 'T':
            $iValue *= 1024;
            // Fallthrough intended
        case 'G':
            $iValue *= 1024;
            // Fallthrough intended
        case 'M':
            $iValue *= 1024;
            // Fallthrough intended
        case 'K':
            $iValue *= 1024;
            break;
    }
    return (int)$iValue;
}  


?>
