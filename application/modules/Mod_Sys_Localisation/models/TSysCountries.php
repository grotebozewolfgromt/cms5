<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Localisation\models;

use dr\classes\models\TSysModel;


/**
 * countries and country codes according to ISO 3166 (https://www.iban.com/country-codes)
 * 
 * The countryname is in English by default (just not to overcomplicate things)
 * 
 * THIS CLASS IS USED THROUGHOUT THE WHOLE FRAMEWORK!
 * 
 * created 1 maart 2022
 * 2 mrt 2022: TSysCountries: isDefault field added
 * 17 mrt 2022: TSysCountries: iseuropeanunion field added
 * 19 okt 2023: TSysCountries: loadFromDBByISO2() added
 * 30 apr 2024: TSysCountries: rename FIELD_ISEUROPEANUNION ==> FIELD_ISEEA
 */

class TSysCountries extends TSysModel
{
	const FIELD_COUNTRYNAME 		= 'sCountryName'; //countryname in english, i.e. The Netherlands
	const FIELD_COUNTRYCODEPHONE	= 'iCountryCodePhone'; //phone number country code
	const FIELD_ISO2 				= 'sISO2'; //alpha iso 2 digit code, i.e. NL
	const FIELD_ISO3 				= 'sISO3'; //alpha iso 3 code, i.e. NLD
	const FIELD_ISEEA 				= 'bIsEEA';	//boolean: is this country in european econimic area and thus part of the single market?
	const FIELD_ISUNKNOWN			= 'bIsUnknown'; //represents an unknown country. When you have a list of countries, it might be useful to have by default selected: 'unknown'
	
	/**
	 * get name of the country
	 * 
	 * @return string
	 */
	public function getCountryName()
	{
		return $this->get(TSysCountries::FIELD_COUNTRYNAME);
	}

	
	/**
	 * set name of the country
	 * 
	 * @param string $sCountry
	 */
	public function setCountryName($sCountry)
	{
		$this->set(TSysCountries::FIELD_COUNTRYNAME, $sCountry);
	}        
	
	/**
	 * get country code of phone number
	 * 
	 * @return string
	 */
	public function getCountryCodePhone()
	{
		return $this->get(TSysCountries::FIELD_COUNTRYCODEPHONE);
	}

	
	/**
	 * set country code of phone number
	 * 
	 * @param int $iCountryCode
	 */
	public function setCountryCodePhone($iCountryCode)
	{
		$this->set(TSysCountries::FIELD_COUNTRYCODEPHONE, $iCountryCode);
	}     

	/**
	 * get alpha 2 code
	 * 
	 * @return string
	 */
	public function getISO2()
	{
		return $this->get(TSysCountries::FIELD_ISO2);
	}

	/**
	 * set alpha 2 code
	 * 
	 * @param string $sURL
	 */
	public function setISO2($sCode)
	{
		$this->set(TSysCountries::FIELD_ISO2, $sCode);
	}           

	
	/**
	 * get alpha 3 code
	 * 
	 * @return string
	 */
	public function getISO3()
	{
		return $this->get(TSysCountries::FIELD_ISO3);
	}

	/**
	 * set alpha 3 code
	 * 
	 * @param string $sURL
	 */
	public function setISO3($sCode)
	{
		$this->set(TSysCountries::FIELD_ISO3, $sCode);
	}   


	public function getIsUnknown()
	{
		return  $this->get(TSysCountries::FIELD_ISUNKNOWN);
	}
	
	public function setIsUnknown($bUnknown)
	{
		$this->set(TSysCountries::FIELD_ISUNKNOWN, $bUnknown);
	} 	

	/**
	 * Loads the country that is marked as representing an unknown country
	 * 
	 * @return boolean load ok?
	 */
	public function loadFromDBByIsUnknown()
	{
		$this->clear();
		$this->find(TSysCountries::FIELD_ISUNKNOWN, true);
		$this->limitOne();
		return $this->loadFromDB();
	}	

	/**
	 * 
	 * @param string $sISO2CountryCode
	 * @return boolean load ok?
	 */
	public function loadFromDBByISO2($sISO2CountryCode)
	{
		$this->clear();
		$this->find(TSysCountries::FIELD_ISO2, $sISO2CountryCode);
		return $this->loadFromDB();
	}		

	/**
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);

		//the netherlands
		if (!$this->recordExistsTableDB(TSysCountries::FIELD_ISO2, 'NL'))
		{
			$this->newRecord();
			$this->set(TSysCountries::FIELD_COUNTRYNAME, 'Netherlands (the)'); //the csv above has tab-chars in it, trim them
			$this->set(TSysCountries::FIELD_COUNTRYCODEPHONE, 31); 
			$this->set(TSysCountries::FIELD_ISO2, 'NL');
			$this->set(TSysCountries::FIELD_ISO3, 'NLD');
			$this->set(TSysCountries::FIELD_ISEEA, true);
			$this->set(TSysCountries::FIELD_ISDEFAULT, true);
			$this->set(TSysCountries::FIELD_ISFAVORITE, true);
			$this->set(TSysCountries::FIELD_ISUNKNOWN, false);

			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving default country on install');
				$bSuccess = false;
			}
		}

		//unknown country
		if (!$this->recordExistsTableDB(TSysCountries::FIELD_ISO2, '--'))
		{		
			$this->newRecord();
			$this->set(TSysCountries::FIELD_COUNTRYNAME, '[unknown]'); //the csv above has tab-chars in it, trim them
			$this->set(TSysCountries::FIELD_COUNTRYCODEPHONE, 0); 
			$this->set(TSysCountries::FIELD_ISO2, '--');
			$this->set(TSysCountries::FIELD_ISO3, '---');
			$this->set(TSysCountries::FIELD_ISEEA, false);
			$this->set(TSysCountries::FIELD_ISDEFAULT, false);
			$this->set(TSysCountries::FIELD_ISUNKNOWN, true);

			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving unknown country on install');
				$bSuccess = false;
			}
		}

		return $bSuccess;
	}

	/**
	 * generate data to insert into database
	 * This is called after install()	 
	 * this is done because if installation takes too long/broken off/canceled, the likelyhood of the database being corrupt is lower (data propagation is less important than install)
	 *
	 * DIFFERENCE: install VS propagate 
	 * - install installs the minimum necessary data for database to function 
	 * - propagate installs convenient data
	 * 
	 * for example: languages
	 * You want to install at a minimum 1 language (=install), 
	 * but it is convenient to have all languages in the world in the database (=propagate)
	 * 
	 * PLEASE OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
	 * 
	 * @return bool true = propagation succeeded, false = error
	 */
	public function propagateData()
	{	
		$sCSV = "Afghanistan,AF,AFG,0,0
		Albania,AL,ALB,0,0
		Algeria,DZ,DZA,0,0
		American Samoa,AS,ASM,0,0
		Andorra,AD,AND,0,0
		Angola,AO,AGO,0,0
		Anguilla,AI,AIA,0,0
		Antarctica,AQ,ATA,0,0
		Antigua and Barbuda,AG,ATG,0,0
		Argentina,AR,ARG,0,0
		Armenia,AM,ARM,0,0
		Aruba,AW,ABW,0,0
		Australia,AU,AUS,0,61
		Austria,AT,AUT,1,43
		Azerbaijan,AZ,AZE,0,0
		Bahamas (the),BS,BHS,0,0
		Bahrain,BH,BHR,0,0
		Bangladesh,BD,BGD,0,0
		Barbados,BB,BRB,0,0
		Belarus,BY,BLR,0,0
		Belgium,BE,BEL,1,32
		Belize,BZ,BLZ,0,0
		Benin,BJ,BEN,0,0
		Bermuda,BM,BMU,0,0
		Bhutan,BT,BTN,0,0
		Bolivia (Plurinational State of),BO,BOL,0,0
		Bonaire and Sint Eustatius and Saba,BQ,BES,0,0
		Bosnia and Herzegovina,BA,BIH,0,0
		Botswana,BW,BWA,0,0
		Bouvet Island,BV,BVT,0,0
		Brazil,BR,BRA,0,0
		British Indian Ocean Territory (the),IO,IOT,0,0
		Brunei Darussalam,BN,BRN,0,0
		Bulgaria,BG,BGR,1,0
		Burkina Faso,BF,BFA,0,0
		Burundi,BI,BDI,0,0
		Cabo Verde,CV,CPV,0,0
		Cambodia,KH,KHM,0,0
		Cameroon,CM,CMR,0,0
		Canada,CA,CAN,0,0
		Cayman Islands (the),KY,CYM,0,0
		Central African Republic (the),CF,CAF,0,0
		Chad,TD,TCD,0,0
		Chile,CL,CHL,0,0
		China,CN,CHN,0,86
		Christmas Island,CX,CXR,0,0
		Cocos (Keeling) Islands (the),CC,CCK,0,0
		Colombia,CO,COL,0,0
		Comoros (the),KM,COM,0,0
		Congo (the Democratic Republic of the),CD,COD,0,0
		Congo (the),CG,COG,0,0
		Cook Islands (the),CK,COK,0,0
		Costa Rica,CR,CRI,0,0
		Croatia,HR,HRV,1,0
		Cuba,CU,CUB,0,0
		Curaçao,CW,CUW,0,599
		Cyprus,CY,CYP,1,357
		Czech Republic,CZ,CZE,1,420
		Côte d'Ivoire,CI,CIV,0,0
		Denmark,DK,DNK,1,45
		Djibouti,DJ,DJI,0,0
		Dominica,DM,DMA,0,0
		Dominican Republic (the),DO,DOM,0,0
		Ecuador,EC,ECU,0,0
		Egypt,EG,EGY,0,20
		El Salvador,SV,SLV,0,0
		Equatorial Guinea,GQ,GNQ,0,0
		Eritrea,ER,ERI,0,0
		Estonia,EE,EST,1,372
		Eswatini,SZ,SWZ,0,0
		Ethiopia,ET,ETH,0,0
		Falkland Islands (the) [Malvinas],FK,FLK,0,0
		Faroe Islands (the),FO,FRO,0,0
		Fiji,FJ,FJI,0,0
		Finland,FI,FIN,1,358
		France,FR,FRA,1,33
		French Guiana,GF,GUF,0,0
		French Polynesia,PF,PYF,0,0
		French Southern Territories (the),TF,ATF,0,0
		Gabon,GA,GAB,0,0
		Gambia (the),GM,GMB,0,0
		Georgia,GE,GEO,0,0
		Germany,DE,DEU,1,49
		Ghana,GH,GHA,0,0
		Gibraltar,GI,GIB,0,350
		Greece,GR,GRC,1,30
		Greenland,GL,GRL,0,299
		Grenada,GD,GRD,0,0
		Guadeloupe,GP,GLP,0,0
		Guam,GU,GUM,0,0
		Guatemala,GT,GTM,0,0
		Guernsey,GG,GGY,0,0
		Guinea,GN,GIN,0,0
		Guinea-Bissau,GW,GNB,0,0
		Guyana,GY,GUY,0,0
		Haiti,HT,HTI,0,0
		Heard Island and McDonald Islands,HM,HMD,0,0
		Holy See (the),VA,VAT,0,0
		Honduras,HN,HND,0,0
		Hong Kong,HK,HKG,0,0
		Hungary,HU,HUN,1,0
		Iceland,IS,ISL,0,354
		India,IN,IND,0,91
		Indonesia,ID,IDN,0,62
		Iran (Islamic Republic of),IR,IRN,0,0
		Iraq,IQ,IRQ,0,0
		Ireland,IE,IRL,1,353
		Isle of Man,IM,IMN,0,0
		Israel,IL,ISR,0,0
		Italy,IT,ITA,1,39
		Jamaica,JM,JAM,0,0
		Japan,JP,JPN,0,81
		Jersey,JE,JEY,0,0
		Jordan,JO,JOR,0,0
		Kazakhstan,KZ,KAZ,0,0
		Kenya,KE,KEN,0,0
		Kiribati,KI,KIR,0,0
		Korea (the Democratic People's Republic of),KP,PRK,0,0
		Korea (the Republic of),KR,KOR,0,0
		Kuwait,KW,KWT,0,0
		Kyrgyzstan,KG,KGZ,0,0
		Lao People's Democratic Republic (the),LA,LAO,0,0
		Latvia,LV,LVA,1,371
		Lebanon,LB,LBN,0,0
		Lesotho,LS,LSO,0,0
		Liberia,LR,LBR,0,0
		Libya,LY,LBY,0,0
		Liechtenstein,LI,LIE,0,0
		Lithuania,LT,LTU,1,0
		Luxembourg,LU,LUX,1,0
		Macao,MO,MAC,0,0
		Madagascar,MG,MDG,0,0
		Malawi,MW,MWI,0,0
		Malaysia,MY,MYS,0,0
		Maldives,MV,MDV,0,0
		Mali,ML,MLI,0,0
		Malta,MT,MLT,1,0
		Marshall Islands (the),MH,MHL,0,0
		Martinique,MQ,MTQ,0,0
		Mauritania,MR,MRT,0,0
		Mauritius,MU,MUS,0,0
		Mayotte,YT,MYT,0,0
		Mexico,MX,MEX,0,0
		Micronesia (Federated States of),FM,FSM,0,0
		Moldova (the Republic of),MD,MDA,0,0
		Monaco,MC,MCO,0,0
		Mongolia,MN,MNG,0,0
		Montenegro,ME,MNE,0,0
		Montserrat,MS,MSR,0,0
		Morocco,MA,MAR,0,0
		Mozambique,MZ,MOZ,0,0
		Myanmar,MM,MMR,0,0
		Namibia,NA,NAM,0,0
		Nauru,NR,NRU,0,0
		Nepal,NP,NPL,0,0
		New Caledonia,NC,NCL,0,0
		New Zealand,NZ,NZL,0,64
		Nicaragua,NI,NIC,0,0
		Niger (the),NE,NER,0,0
		Nigeria,NG,NGA,0,0
		Niue,NU,NIU,0,0
		Norfolk Island,NF,NFK,0,0
		Northern Mariana Islands (the),MP,MNP,0,0
		Norway,NO,NOR,0,47
		Oman,OM,OMN,0,0
		Pakistan,PK,PAK,0,0
		Palau,PW,PLW,0,0
		Palestine (State of),PS,PSE,0,0
		Panama,PA,PAN,0,0
		Papua New Guinea,PG,PNG,0,0
		Paraguay,PY,PRY,0,0
		Peru,PE,PER,0,0
		Philippines (the),PH,PHL,0,0
		Pitcairn,PN,PCN,0,0
		Poland,PL,POL,1,48
		Portugal,PT,PRT,1,351
		Puerto Rico,PR,PRI,0,0
		Qatar,QA,QAT,0,0
		Republic of North Macedonia,MK,MKD,0,0
		Romania,RO,ROU,1,40
		Russian Federation (the),RU,RUS,0,7
		Rwanda,RW,RWA,0,0
		Réunion,RE,REU,0,0
		Saint Barthélemy,BL,BLM,0,0
		Saint Helena and Ascension and Tristan da Cunha,SH,SHN,0,0
		Saint Kitts and Nevis,KN,KNA,0,0
		Saint Lucia,LC,LCA,0,0
		Saint Martin (French part),MF,MAF,0,0
		Saint Pierre and Miquelon,PM,SPM,0,0
		Saint Vincent and the Grenadines,VC,VCT,0,0
		Samoa,WS,WSM,0,0
		San Marino,SM,SMR,0,0
		Sao Tome and Principe,ST,STP,0,0
		Saudi Arabia,SA,SAU,0,0
		Senegal,SN,SEN,0,0
		Serbia,RS,SRB,0,0
		Seychelles,SC,SYC,0,0
		Sierra Leone,SL,SLE,0,0
		Singapore,SG,SGP,0,65
		Sint Maarten (Dutch part),SX,SXM,0,1
		Slovakia,SK,SVK,0,421
		Slovenia,SI,SVN,0,386
		Solomon Islands,SB,SLB,0,0
		Somalia,SO,SOM,0,0
		South Africa,ZA,ZAF,0,0
		South Georgia and the South Sandwich Islands,GS,SGS,0,0
		South Sudan,SS,SSD,0,0
		Spain,ES,ESP,1,34
		Sri Lanka,LK,LKA,0,0
		Sudan (the),SD,SDN,0,0
		Suriname,SR,SUR,0,497
		Svalbard and Jan Mayen,SJ,SJM,0,47
		Sweden,SE,SWE,1,46
		Switzerland,CH,CHE,0,0
		Syrian Arab Republic,SY,SYR,0,0
		Taiwan (Province of China),TW,TWN,0,0
		Tajikistan,TJ,TJK,0,0
		Tanzania (United Republic of),TZ,TZA,0,0
		Thailand,TH,THA,0,0
		Timor-Leste,TL,TLS,0,0
		Togo,TG,TGO,0,0
		Tokelau,TK,TKL,0,0
		Tonga,TO,TON,0,0
		Trinidad and Tobago,TT,TTO,0,0
		Tunisia,TN,TUN,0,0
		Turkey,TR,TUR,0,0
		Turkmenistan,TM,TKM,0,0
		Turks and Caicos Islands (the),TC,TCA,0,0
		Tuvalu,TV,TUV,0,0
		Uganda,UG,UGA,0,0
		Ukraine,UA,UKR,0,0
		United Arab Emirates (the),AE,ARE,0,0
		United Kingdom of Great Britain and Northern Ireland (the),GB,GBR,0,0
		United States Minor Outlying Islands (the),UM,UMI,0,0
		United States of America (the),US,USA,0,0
		Uruguay,UY,URY,0,0
		Uzbekistan,UZ,UZB,0,0
		Vanuatu,VU,VUT,0,0
		Venezuela (Bolivarian Republic of),VE,VEN,0,0
		Viet Nam,VN,VNM,0,0
		Virgin Islands (British),VG,VGB,0,0
		Virgin Islands (U.S.),VI,VIR,0,0
		Wallis and Futuna,WF,WLF,0,0
		Western Sahara,EH,ESH,0,0
		Yemen,YE,YEM,0,0
		Zambia,ZM,ZMB,0,0
		Zimbabwe,ZW,ZWE,0,0
		Åland Islands,AX,ALA,0,0";
	

		$arrLines = explode("\n", $sCSV);
		$bSuccess = true;		
		foreach ($arrLines as $sLine)
		{
			$arrColumns = explode(',', $sLine);

			if (!$this->recordExistsTableDB(TSysCountries::FIELD_ISO2, $arrColumns[1]))
			{
				$this->newRecord();
				$this->set(TSysCountries::FIELD_COUNTRYNAME, trim($arrColumns[0])); //the csv above has tab-chars in it, trim them
				$this->set(TSysCountries::FIELD_ISO2, $arrColumns[1]);
				$this->set(TSysCountries::FIELD_ISO3, $arrColumns[2]);
				if ($arrColumns[3] == '1') //europe
					$this->set(TSysCountries::FIELD_ISEEA, true);
				if ($arrColumns[4] !== '0') //country code phone
					$this->set(TSysCountries::FIELD_COUNTRYCODEPHONE, $arrColumns[4]);					
				
				// if ($arrColumns[1] == APP_LOCATION_DEFAULT)
				// {
				// 	$this->set(TSysCountries::FIELD_ISSYSTEMDEFAULT, true);
				// }

				if (!$this->saveToDB())
				{
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving language on install: '. $arrColumns[1]);
					$bSuccess = false;
				}
			}
		}

		
		return $bSuccess;
	}


	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
         * 
	 * initialize values
	 */
	public function initRecord()
	{}
		
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//country name
		$this->setFieldDefaultsVarChar(TSysCountries::FIELD_COUNTRYNAME);	
		$this->setFieldUnique(TSysCountries::FIELD_COUNTRYNAME, true);		
		$this->setFieldIndexed(TSysCountries::FIELD_COUNTRYNAME, false);//it is already unique
		$this->setFieldFulltext(TSysCountries::FIELD_COUNTRYNAME, true);			
		
		//european union?
		$this->setFieldDefaultsInteger(TSysCountries::FIELD_COUNTRYCODEPHONE);		

		//alpha 2 code								
		$this->setFieldDefaultsVarChar(TSysCountries::FIELD_ISO2, 2);		
		$this->setFieldUnique(TSysCountries::FIELD_ISO2, true);		
		$this->setFieldIndexed(TSysCountries::FIELD_ISO2, false); //it is already unique
		$this->setFieldFulltext(TSysCountries::FIELD_ISO2, true);

		//alpha 3 code	
		$this->setFieldDefaultsVarChar(TSysCountries::FIELD_ISO3, 3);			
		$this->setFieldUnique(TSysCountries::FIELD_ISO3, true);
		$this->setFieldIndexed(TSysCountries::FIELD_ISO3, false); //it is already unique
		$this->setFieldFulltext(TSysCountries::FIELD_ISO3, true); 					

		//european union?
		$this->setFieldDefaultsBoolean(TSysCountries::FIELD_ISEEA);

		//unknown
		$this->setFieldDefaultsBoolean(TSysCountries::FIELD_ISUNKNOWN);
	}
	
	
	/**
	 * returns an array with fields that are publicly viewable
	 * sometimes (for security reasons the password-field for example) you dont want to display all table fields to the user
	 *
	 * i.e. it can be used for searchqueries, sorting, filters or exports
	 *
	 * @return array function returns array WITHOUT tablename
	*/
	public function getFieldsPublic()
	{
		return array(TSysCountries::FIELD_COUNTRYNAME, TSysCountries::FIELD_ISO2, TSysCountries::FIELD_ISO3, TSysCountries::FIELD_ISDEFAULT, TSysCountries::FIELD_ISEEA);
	}
	
	/**
	 * use the auto-added id-field ?
	 * @return bool
	*/
	public function getTableUseIDField()
	{
		return true;
	}
	
	
	/**
	 * use the auto-added date-changed & date-created field ?
	 * @return bool
	*/
	public function getTableUseDateCreatedChangedField()
	{
		return false;
	}
	
	
	/**
	 * use the checksum field ?
	 * @return bool
	*/
	public function getTableUseChecksumField()
	{
		return true;
	}
	
	/**
	 * order field to switch order between records
	*/
	public function getTableUseOrderField()
	{
		return false;
	}
	
	/**
	 * use checkout for locking file for editing
	*/
	public function getTableUseCheckout()
	{
		return false;
	}
		
	/**
	 * use record locking to prevent record editing
	*/
	public function getTableUseLock()
	{
		return false;
	}            
	
        
	/**
	 * use image in your record?
    * if you don't want a small and large version, use this one
	*/
	public function getTableUseImageFile()
	{
		return false;
	}
        
        
	/**
	 * opvragen of records fysiek uit de databasetabel verwijderd moeten worden
	 *
	 * returnwaarde interpretatie:
	 * true = fysiek verwijderen uit tabel
	 * false = record-hidden-veld gebruiken om bij te houden of je het record kan zien in overzichten
	 *
	 * @return bool moeten records fysiek verwijderd worden ?
	*/
	public function getTablePhysicalDeleteRecord()
	{
		return true;
	}
	
	
	
	
	/**
	 * type of primary key field
	 *
	 * @return integer with constant CT_AUTOINCREMENT or CT_INTEGER32 or something else that is not recommendable
	*/
	public function getTableIDFieldType()
	{
		return CT_AUTOINCREMENT;
	}
	
	
	/**
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysCountries';
	}
	
	
	
	/**
	 * OVERSCHRIJF DOOR CHILD KLASSE ALS NODIG
	 *
	 * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
	 * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 *
	 *
	 * return '??? - functie niet overschreven door child klasse';
	*/
	public function getDisplayRecordShort()
	{
		$sCountryCode = '';
		$sCountryCode = strval($this->get(TSysCountries::FIELD_COUNTRYCODEPHONE));
		
		if ($sCountryCode == '0') //ignore code 0
			$sCountryCode = '';

		if (strlen($sCountryCode) > 0)	
			$sCountryCode = ' (+'.$sCountryCode.')';

		return $this->get(TSysCountries::FIELD_COUNTRYNAME).$sCountryCode;
	}
	
	
	/**
	 * erf deze functie over om je eigen checksum te maken voor je tabel.
	 * je berekent deze de belangrijkste velden te pakken, wat strings toe te
	 * voegen en alles vervolgens de coderen met een hash algoritme
	 * zoals met sha1 (geen md5, gezien deze makkelijk te breken is)
	 * de checksum mag maar maximaal 50 karakters lang zijn
	 *
	 * BELANGRIJK: je mag NOOIT het getID() en getChecksum()-field meenemen in
	 * je checksum berekening (id wordt pas toegekend na de save in de database,
	 * dus is nog niet bekend ten tijde van het checksum berekenen)
	 *
	 * @return string
	*/
	public function getChecksumUncrypted()
	{
		return 'winkelbediende'.$this->get(TSysCountries::FIELD_COUNTRYNAME).'proletarisch'.$this->get(TSysCountries::FIELD_ISO2).''.$this->get(TSysCountries::FIELD_ISO3).'winkelen'.$this->get(TSysCountries::FIELD_COUNTRYNAME).'dat-is-dus-stelen'.boolToStr($this->get(TSysCountries::FIELD_ISDEFAULT));
	}
	
	
	/**
	 * DEZE FUNCTIE MOET OVERGEERFD WORDEN DOOR DE CHILD KLASSE
	 *
	 * checken of alle benodigde waardes om op te slaan wel aanwezig zijn
	 *
	 * @return bool true=ok, false=not ok
	*/
	public function areValuesValid()
	{     
		return true;
	}
	
	/**
	 * for the automatic database table upgrade system to work this function
	 * returns the version number of this class
	 * The update system can compare the version of the database with the Business Logic
	 *
	 * default with no updates = 0
	 * first update = 1, second 2 etc
	 *
	 * @return int
	*/
	public function getVersion()
	{
		return 0;
	}
	
	/**
	 * update the table in the database
	 * (may have been changes to fieldnames, fields added or removed etc)
	 *
	 * @param int $iFromVersion upgrade vanaf welke versie ?
	 * @return bool is alles goed gegaan ? true = ok (of er is geen upgrade gedaan)
	*/
	protected function refactorDBTable($iFromVersion)
	{
		return true;
	}	
        
	/**
	 * use a second id that has no follow-up numbers?
	 */
	public function getTableUseRandomID()
	{
		return false;
	}        
	
	/**
	 * is randomid field a primary key?
	 */        
	public function getTableUseRandomIDAsPrimaryKey()
	{
		return false;
	}       
        
	/**
	 * use a third character-based id that has no logically follow-up numbers?
	 * 
	 * a tertiary unique key (uniqueid) can be useful for security reasons like login sessions: you don't want to _POST the follow up numbers in url
	 */
	public function getTableUseUniqueID()
	{
		return false;
	}

	/**
	 * use a random string id that has no logically follow-up numbers
	 * 
	 * this is used to produce human readable identifiers
	 * @return bool
	 */
	public function getTableUseNiceID()
	{
		return false;
	}	
		
	/**
	 * is this model a translation model?
	 *
	 * @return bool is this model a translation model?
	 */
	public function getTableUseTranslationLanguageID()
	{
		return false;
	}        

	/**
	 * Want to use the 'isdefault' field in database table?
	 * Returning true allows 1 record to be the default record in a table
	 * This is useful for creating records with foreign fields without user interference OR 
	 * selecting records in GUI elements like comboboxes
	 * 
	 * example: select the default language in a combobox
	 * 
	 * @return bool
	 */
	public function getTableUseIsDefault()
	{
		return true;
	}	

	/**
	 * can a record be favorited by the user?
	 *
	 * @return bool
	 */
	public function getTableUseIsFavorite()
	{
		return true;
	}	

	/**
	 * can record be transcanned?
	 * Trashcan is an extra step in for deleting a record
	 *
	 * @return bool
	 */
	public function getTableUseTrashcan()
	{
		return false;
	}	

	/**
	 * use a field for search keywords?
	 * (also known als tags or labels)
	 *
	 * @return bool
	 */
	public function getTableUseSearchKeywords()
	{
		return false;
	}		
} 
?>