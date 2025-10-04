<?php
namespace dr\classes\models;

use dr\classes\models\TSysModel;

/**
 * IP Address Geolocation.
 * This class gives a geolocation for an ip addres.
 * This is not a TSysModel based class
 * 
 * When NO ip adress is supplied, the current address is assumed
 * 
 * The current version is based on GeoPlugin webservice
 * https://www.geoplugin.com/webservices/php (which in its turn uses maxmind)
 * if this service stops working, replace the 'insides' of this class
 * to provide the same interface to the application
 * 
 * created 7 maart 2022 by dennis renirie
 * 08 mrt 2022: TIPGeoLocation: inconsistentie met TSysModel weg gewerkt, loadFromDB() is verwijderd
 * 19 oct 2023: TIPGeoLocation: loadFromDBByIP default parameter is '' en gebruikt interne IP adress
 * 19 oct 2023: TIPGeoLocation: getCountryIDloadFromDB default parameter is '' en gebruikt interne IP adress
 * 
 * @todo regard output of GeoPlugin as dirty. If geoplugin gets a hack and returns malicious code. filter output of this class for SQL injection and XSS and weird characters
 */
class TIPGeoLocation
{
	private $sIPAddress = '';

	private $sCountryName = '';
	private $sCountryCode = ''; //2 digit iso country code
	private $sLongitude = 0; //don't want to deal with floats, so I made it a string
	private $sLatitude = 0; //don't want to deal with floats, so I made it a string
	private $sCityName = '';
	private $sRegionName = '';
	private $sRegionCode = '';
	private $sTimeZone = '';

	public function __construct()
	{
		$this->sIPAddress = $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * ip address to lookup
	 *
	 * @param string $sIP
	 * @return bool
	 */
	public function setIPAddress($sIP)
	{
		$this->sIPAddress = $sIP;
	}


	/**
	 * return city name
	 *
	 * @return string
	 */
	public function getIPAddress()
	{
		return $this->sIPAddress;
	}

	/**
	 * return city name
	 *
	 * @return string
	 */
	public function getCityName()
	{
		return $this->sCityName;
	}


	/**
	 * return region code
	 *
	 * @return string
	 */
	public function getRegionCode()
	{
		return $this->sRegionCode;
	}	

	/**
	 * return region name
	 *
	 * @return string
	 */
	public function getRegionName()
	{
		return $this->sRegionName;
	}	

	/**
	 * returns ISO 2 character countrycode
	 *
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->sCountryCode;
	}		

	/**
	 * return country name
	 *
	 * @return string
	 */
	public function getCountryName()
	{
		return $this->sCountryName;
	}

	/**
	 * return longitude
	 * 
	 * @return string
	 */
	public function getLongitude()
	{
		return $this->sLongitude;
	}	


	/**
	 * return latitude
	 * 
	 * @return string
	 */
	public function getLatitude()
	{
		return $this->sLatitude;
	}	

	/**
	 * return timezone
	 * 
	 * @return string
	 */
	public function getTimeZone()
	{
		return $this->sTimeZone;
	}	

	/**
	* loads external website to retreive IP addres
	*/
	public function loadFromDBByIP($sIPAddress = '')
	{
		if ($sIPAddress == '')
			$sIPAddress = $this->sIPAddress;

		$mWebPageContent = file_get_contents('http://www.geoplugin.net/php.gp?ip='.$sIPAddress);

		if ($mWebPageContent)
		{
			$arrResultset = unserialize($mWebPageContent);
			
			/* array format :
				array (
				'geoplugin_request' => '31.20.85.155',
				'geoplugin_status' => 200,
				'geoplugin_delay' => '1ms',
				'geoplugin_credit' => 'Some of the returned data includes GeoLite data created by MaxMind, available from http://www.maxmind.com.',
				'geoplugin_city' => 'Weert',
				'geoplugin_region' => 'Limburg',
				'geoplugin_regionCode' => 'LI',
				'geoplugin_regionName' => 'Limburg',
				'geoplugin_areaCode' => '',
				'geoplugin_dmaCode' => '',
				'geoplugin_countryCode' => 'NL',
				'geoplugin_countryName' => 'Netherlands',
				'geoplugin_inEU' => 1,
				'geoplugin_euVATrate' => 21,
				'geoplugin_continentCode' => 'EU',
				'geoplugin_continentName' => 'Europe',
				'geoplugin_latitude' => '51.2357',
				'geoplugin_longitude' => '5.7341',
				'geoplugin_locationAccuracyRadius' => '10',
				'geoplugin_timezone' => 'Europe/Amsterdam',
				'geoplugin_currencyCode' => 'EUR',
				'geoplugin_currencySymbol' => '€',
				'geoplugin_currencySymbol_UTF8' => '€',
				'geoplugin_currencyConverter' => '0.9134',
				)

			*/

			$this->sCityName = $arrResultset['geoplugin_city'];
			$this->sRegionCode = $arrResultset['geoplugin_regionCode'];
			$this->sRegionName = $arrResultset['geoplugin_regionName'];
			$this->sCountryCode = $arrResultset['geoplugin_countryCode'];
			$this->sCountryName = $arrResultset['geoplugin_countryName'];
			$this->sLongitude = $arrResultset['geoplugin_longitude'];
			$this->sLatitude = $arrResultset['geoplugin_latitude'];
			$this->sTimeZone = $arrResultset['geoplugin_timezone'];

			//@todo filter malicious output from geoplugin for SQL injection, XSS, length and weird chars
			
		}
		else
			return false;

		return true;	
	}

	/**
	 * looks up countryid in database based on 
	 * 
	 * WARNING:
	 * This function does NOT lookup ip addresses in database.
	 * If you want to lookup an ip, you need to call loadFromDBByIP() first!!!
	 * 
	 * @return int if false, it failed
	 */
	public function getCountryIDloadFromDB()
	{
		$iCountryID = 0;

		$objCountries = new TSysCountries();
        if (!$objCountries->loadFromDBByISO2($this->getCountryCode()))
			return false;
        $iCountryID = $objCountries->getID();
        unset($objCountries);  

		return $iCountryID;
	}

}

?>