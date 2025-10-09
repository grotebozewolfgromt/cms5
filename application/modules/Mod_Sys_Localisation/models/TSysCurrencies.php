<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Localisation\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDecimal;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

/**
 * currencies according to ISO 4217 standard (https://en.wikipedia.org/wiki/ISO_4217)
 * 
 * The currencyname is in English by default (just not to overcomplicate things)
 * 
 * THIS CLASS IS USED THROUGHOUT THE WHOLE FRAMEWORK!
 * 
 * created 20 october 2023
 * 20 oct 2023: TSysCurrencies: created
 * 25 oct 2023: TSysCurrencies: minor units renamed to decimal precision for consistency with the rest of the framework
 * 
 * @author Dennis Renirie
 */

class TSysCurrencies extends TSysModel
{
	const FIELD_CURRENCYNAME 		= 'sCurrencyName'; //currency name in english, i.e. Netherlands Antillean guilder
	const FIELD_CURRENCYSYMBOL 		= 'sCurrencySymbol'; //currency symbol i.e. €
	const FIELD_ISOALPHABETIC 		= 'sISOAlphabetic'; //alhabetical 3 letter ISO code, i.e. EUR
	const FIELD_ISONUMERIC 			= 'iISONumeric'; //mumerical ISO code, i.e. 973
	const FIELD_DECIMALPRECISION 	= 'iDecimalPrecision'; //official name: minor unit, the number of digits after decimal separator (currency decimals)
	// const FIELD_ISSYSTEMDEFAULT 	= 'bIsSystemDefault';	//boolean: is this the default currency?
	// const FIELD_ISVISIBLE 			= 'bIsVisible'; //preventing 400 locales are shown in html select boxes
	const FIELD_EXCHANGERATE 		= 'dExchangeRate'; //exchange rate relative to the default currency
	
	/**
	 * get name of the currency
	 * 
	 * @return string
	 */
	public function getCurrencyName()
	{
		return $this->get(TSysCurrencies::FIELD_CURRENCYNAME);
	}

	
	/**
	 * set name of the currency
	 * 
	 * @param string $sCurrency
	 */
	public function setCurrencyName($sCurrency)
	{
		$this->set(TSysCurrencies::FIELD_CURRENCYNAME, $sCurrency);
	}        
	
	/**
	 * get name of the currency
	 * 
	 * @return string
	 */
	public function getCurrencySymbol()
	{
		return $this->get(TSysCurrencies::FIELD_CURRENCYSYMBOL);
	}

	
	/**
	 * set name of the currency
	 * 
	 * @param string $sCurrency
	 */
	public function setCurrencySymbol($sSymbol)
	{
		$this->set(TSysCurrencies::FIELD_CURRENCYSYMBOL, $sSymbol);
	} 

	/**
	 * get alphabetical 3 digit ISO code
	 * 
	 * @return string
	 */
	public function getISOAlphabetical()
	{
		return $this->get(TSysCurrencies::FIELD_ISOALPHABETIC);
	}

	/**
	 * set alphabetical 3 digit ISO code
	 * 
	 * @param string $sCode
	 */
	public function setISOAlphabetical($sCode)
	{
		$this->set(TSysCurrencies::FIELD_ISOALPHABETIC, $sCode);
	}           

	
	/**
	 * get numeric ISO code
	 * 
	 * @return int
	 */
	public function getISONumeric()
	{
		return $this->get(TSysCurrencies::FIELD_ISONUMERIC);
	}

	/**
	 * set numeric ISO code
	 * 
	 * @param int $iCode
	 */
	public function setISONumeric($iCode)
	{
		$this->set(TSysCurrencies::FIELD_ISONUMERIC, $iCode);
	}   

	/**
	 * get number of digits after decimal separator
	 * (currency decimals)
	 * 
	 * @return int
	 */
	public function getDecimalPrecision()
	{
		return $this->get(TSysCurrencies::FIELD_DECIMALPRECISION);
	}

	/**
	 * set number of digits after decimal separator 
	 * (currency decimals)
	 * 
	 * @param int $iCode
	 */
	public function setDecimalPrecision($iCode)
	{
		$this->set(TSysCurrencies::FIELD_DECIMALPRECISION, $iCode);
	}  	


	// public function getIsDefault()
	// {
	// 	return  $this->get(TSysCurrencies::FIELD_ISSYSTEMDEFAULT);
	// }
	
	// public function setIsDefault($bDefault)
	// {
	// 	$this->set(TSysCurrencies::FIELD_ISSYSTEMDEFAULT, $bDefault);
	// } 	


	// public function getIsVisible()
	// {
	// 	return  $this->get(TSysCurrencies::FIELD_ISVISIBLE);
	// }
	
	// public function setIsVisible($bVisible)
	// {
	// 	$this->set(TSysCurrencies::FIELD_ISVISIBLE, $bVisible);
	// } 		

	/**
	 * @return TDecimal
	 */
	public function getExchangeRate()
	{
		return  $this->get(TSysCurrencies::FIELD_EXCHANGERATE);
	}
	
	/**
	 * @param TDecimal $objDecimal
	 */
	public function setExchangeRate($objDecimal)
	{
		$this->set(TSysCurrencies::FIELD_EXCHANGERATE, $objDecimal);
	} 		


	/**
	 * load default currency
	 * 
	 * @return boolean load ok?
	 */
	// public function loadFromDBByIsDefault()
	// {
	// 	$this->clear();
	// 	$this->find(TSysCurrencies::FIELD_ISSYSTEMDEFAULT, true);
	// 	return $this->loadFromDB();
	// }	


	/**
	 * load currency by ISO alphabetical code
	 * 
	 * @param string $sISOCode
	 * @return boolean load ok?
	 */
	public function loadFromDBByISOAlphabetical($sISOCode)
	{
		$this->clear();
		$this->find(TSysCurrencies::FIELD_ISOALPHABETIC, $sISOCode);
		return $this->loadFromDB();
	}		

	/**
	 * this function creates table in database and calls all foreign key classes to do the same
	 *
	 * the $arrPreviousDependenciesModelClasses prevents a endless loop by storing all the classnames that are already installed
	 *
	 * @param array $arrPreviousDependenciesModelClasses with classnames.
	 * @return bool success?
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);

		//Euro,€,EUR,978,2,1
		if (!$this->recordExistsTableDB(TSysCurrencies::FIELD_CURRENCYNAME, 'Euro'))
		{
			$this->newRecord();
			$this->set(TSysCurrencies::FIELD_CURRENCYNAME, 'Euro');
			$this->set(TSysCurrencies::FIELD_CURRENCYSYMBOL, '€'); 
			$this->set(TSysCurrencies::FIELD_ISOALPHABETIC, 'EUR');
			$this->set(TSysCurrencies::FIELD_ISONUMERIC, 978);
			$this->set(TSysCurrencies::FIELD_DECIMALPRECISION, 2);
			$this->set(TSysCurrencies::FIELD_ISDEFAULT, true);
			$this->set(TSysCurrencies::FIELD_ISFAVORITE, true);
			$this->set(TSysCurrencies::FIELD_EXCHANGERATE, new TDecimal('1', 4));
			

			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving currencies on install: '. $arrColumns[0]);
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
		$sCSV = "United States Dollar,$,USD,840,2,0
		Pound Sterling,£,GBP,826,2,0";
	
		/*
		$sCSVByAI = "Afghan afghani, ؋, AFN, 971, 2
Albanian lek, L, ALL, 008, 2
Algerian dinar, DA, DZD, 012, 2
Angolan kwanza, Kz, AOA, 973, 2
Argentine peso, $, ARS, 032, 2
Armenian dram, ֏, AMD, 051, 2
Aruban florin, ƒ, AWG, 533, 2
Australian dollar, $, AUD, 036, 2
Azerbaijani manat, ₼, AZN, 944, 2
Bahamian dollar, $, BSD, 044, 2
Bahraini dinar, BD, BHD, 048, 3
Bangladeshi taka, ৳, BDT, 050, 2
Barbadian dollar, $, BBD, 052, 2
Belarusian ruble, Br, BYN, 933, 2
Belize dollar, BZ$, BZD, 084, 2
Bermudian dollar, $, BMD, 060, 2
Bhutanese ngultrum, Nu., BTN, 064, 2
Bolivian boliviano, Bs., BOB, 068, 2
Bosnia and Herzegovina convertible mark, KM, BAM, 977, 2
Botswana pula, P, BWP, 072, 2
Brazilian real, R$, BRL, 986, 2
Brunei dollar, B$, BND, 096, 2
Bulgarian lev, лв, BGN, 975, 2
Burundian franc, FBu, BIF, 108, 0
Cabo Verdean escudo, $, CVE, 132, 2
Cambodian riel, ៛, KHR, 116, 2
Canadian dollar, $, CAD, 124, 2
Cayman Islands dollar, $, KYD, 136, 2
Central African CFA franc, CFA, XAF, 950, 0
Chilean peso, $, CLP, 152, 0
Chinese yuan, ¥, CNY, 156, 2
Colombian peso, $, COP, 170, 2
Comorian franc, CF, KMF, 174, 0
Congolese franc, FC, CDF, 976, 2
Costa Rican colón, ₡, CRC, 188, 2
Croatian kuna, kn, HRK, 191, 2
Cuban peso, $, CUP, 192, 2
Czech koruna, Kč, CZK, 203, 2
Danish krone, kr, DKK, 208, 2
Djiboutian franc, Fdj, DJF, 262, 0
Dominican peso, $, DOP, 214, 2
East Caribbean dollar, EC$, XCD, 951, 2
Egyptian pound, £, EGP, 818, 2
Eritrean nakfa, Nfk, ERN, 232, 2
Estonian kroon, kr, EEK, 233, 2
Ethiopian birr, Br, ETB, 230, 2
Fijian dollar, $, FJD, 242, 2
Gambian dalasi, D, GMD, 270, 2
Georgian lari, ₾, GEL, 981, 2
Ghanaian cedi, ₵, GHS, 936, 2
Gibraltar pound, £, GIP, 292, 2
Guatemalan quetzal, Q, GTQ, 320, 2
Guinean franc, FG, GNF, 324, 0
Guyanaese dollar, $, GYD, 328, 2
Haitian gourde, G, HTG, 332, 2
Honduran lempira, L, HNL, 340, 2
Hong Kong dollar, $, HKD, 344, 2
Hungarian forint, Ft, HUF, 348, 2
Icelandic króna, kr, ISK, 352, 0
Indian rupee, ₹, INR, 356, 2
Indonesian rupiah, Rp, IDR, 360, 2
Iranian rial, ﷼, IRR, 364, 2
Iraqi dinar, ع.د, IQD, 368, 3
Israeli new shekel, ₪, ILS, 376, 2
Jamaican dollar, $, JMD, 388, 2
Japanese yen, ¥, JPY, 392, 0
Jordanian dinar, د.ا, JOD, 400, 3
Kazakhstani tenge, ₸, KZT, 398, 2
Kenyan shilling, Sh, KES, 404, 2
Kuwaiti dinar, د.ك, KWD, 414, 3
Kyrgyzstani som, с, KGS, 417, 2
Lao kip, ₭, LAK, 418, 2
Latvian lats, Ls, LVL, 428, 2
Lebanese pound, ل.ل, LBP, 422, 2
Lesotho loti, L, LSL, 426, 2
Liberian dollar, $, LRD, 430, 2
Libyan dinar, ل.د, LYD, 434, 3
Lithuanian litas, Lt, LTL, 440, 2
Macanese pataca, P, MOP, 446, 2
Macedonian denar, ден, MKD, 807, 2
Malagasy ariary, Ar, MGA, 969, 2
Malawian kwacha, MK, MWK, 454, 2
Malaysian ringgit, RM, MYR, 458, 2
Maldivian rufiyaa, Rf, MVR, 462, 2
Mauritanian ouguiya, UM, MRU, 929, 2
Mauritian rupee, ₨, MUR, 480, 2
Mexican peso, $, MXN, 484, 2
Moldovan leu, L, MDL, 498, 2
Mongolian tögrög, ₮, MNT, 496, 2
Moroccan dirham, د.م., MAD, 504, 2
Mozambican metical, MT, MZN, 943, 2
Myanmar kyat, K, MMK, 104, 2
Namibian dollar, $, NAD, 516, 2
Nepalese rupee, ₨, NPR, 524, 2
Netherlands Antillean guilder, ƒ, ANG, 532, 2
New Zealand dollar, $, NZD, 554, 2
Nicaraguan córdoba, C$, NIO, 558, 2
Nigerian naira, ₦, NGN, 566, 2
North Korean won, ₩, KPW, 408, 2
Norwegian krone, kr, NOK, 578, 2
Omani rial, ر.ع., OMR, 512, 3
Pakistani rupee, ₨, PKR, 586, 2
Panamanian balboa, B/., PAB, 590, 2
Papua New Guinean kina, K, PGK, 598, 2
Paraguayan guaraní, ₲, PYG, 600, 0
Peruvian sol, S/, PEN, 604, 2
Philippine peso, ₱, PHP, 608, 2
Polish złoty, zł, PLN, 985, 2
Qatari riyal, ر.ق, QAR, 634, 2
Romanian leu, lei, RON, 946, 2
Russian ruble, ₽, RUB, 643, 2
Rwandan franc, FRw, RWF, 646, 0
Saint Helena pound, £, SHP, 654, 2
Samoan tālā, T, WST, 882, 2
São Tomé and Príncipe dobra, Db, STN, 930, 2
Saudi riyal, ر.س, SAR, 682, 2
Serbian dinar, дин., RSD, 941, 2
Seychellois rupee, ₨, SCR, 690, 2
Sierra Leonean leone, Le, SLL, 694, 2
Singapore dollar, $, SGD, 702, 2
Solomon Islands dollar, $, SBD, 090, 2
Somali shilling, Sh, SOS, 706, 2
South African rand, R, ZAR, 710, 2
South Korean won, ₩, KRW, 410, 0
South Sudanese pound, £, SSP, 728, 2
Sri Lankan rupee, ₨, LKR, 144, 2
Sudanese pound, ج.س, SDG, 938, 2
Surinamese dollar, $, SRD, 968, 2
Swazi lilangeni, L, SZL, 748, 2
Swedish krona, kr, SEK, 752, 2
Swiss franc, Fr., CHF, 756, 2
Syrian pound, £, SYP, 760, 2
Tajikistani somoni, ЅМ, TJS, 972, 2
Tanzanian shilling, Sh, TZS, 834, 2
Thai baht, ฿, THB, 764, 2
Tongan paʻanga, T$, TOP, 776, 2
Trinidad and Tobago dollar, $, TTD, 780, 2
Tunisian dinar, د.ت, TND, 788, 3
Turkish lira, ₺, TRY, 949, 2
Turkmenistani manat, m, TMT, 934, 2
Ugandan shilling, Sh, UGX, 800, 2
Ukrainian hryvnia, ₴, UAH, 980, 2
United Arab Emirates dirham, د.إ, AED, 784, 2
United States dollar, $, USD, 840, 2
Uruguayan peso, $, UYU, 858, 2
Uzbekistani soʻm, soʻm, UZS, 860, 2
Vanuatu vatu, Vt, VUV, 548, 0
Venezuelan bolívar, Bs., VES, 928, 2
Vietnamese đồng, ₫, VND, 704, 1
Yemeni rial, ﷼, YER, 886, 2
Zambian kwacha, ZK, ZMW, 967, 2
Zimbabwean dollar, $, ZWL, 932, 2";*/

	
		$arrLines = explode("\n", $sCSV);
		$bSuccess = true;	
		foreach ($arrLines as $sLine)
		{
			$arrColumns = explode(',', $sLine);

			if (!$this->recordExistsTableDB(TSysCurrencies::FIELD_ISONUMERIC, $arrColumns[3]))
			{
				$this->newRecord();
				$this->set(TSysCurrencies::FIELD_CURRENCYNAME, trim($arrColumns[0])); //the csv above has tab-chars in it, trim them
				$this->set(TSysCurrencies::FIELD_CURRENCYSYMBOL, $arrColumns[1]); 
				$this->set(TSysCurrencies::FIELD_ISOALPHABETIC, $arrColumns[2]);
				$this->set(TSysCurrencies::FIELD_ISONUMERIC, $arrColumns[3]);
				$this->set(TSysCurrencies::FIELD_DECIMALPRECISION, $arrColumns[4]);
				if ($arrColumns[5] == '1')
					$this->set(TSysCurrencies::FIELD_ISDEFAULT, true);
				$this->set(TSysCurrencies::FIELD_ISFAVORITE, true);
				$this->set(TSysCurrencies::FIELD_EXCHANGERATE, new TDecimal('1', 4));

				if (!$this->saveToDB())
				{
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving default currency on install');
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
		//currency name
		$this->setFieldDefaultValue(TSysCurrencies::FIELD_CURRENCYNAME, '');
		$this->setFieldType(TSysCurrencies::FIELD_CURRENCYNAME, CT_VARCHAR);
		$this->setFieldLength(TSysCurrencies::FIELD_CURRENCYNAME, 100);
		$this->setFieldDecimalPrecision(TSysCurrencies::FIELD_CURRENCYNAME, 0);
		$this->setFieldPrimaryKey(TSysCurrencies::FIELD_CURRENCYNAME, false);
		$this->setFieldNullable(TSysCurrencies::FIELD_CURRENCYNAME, false);
		$this->setFieldEnumValues(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldUnique(TSysCurrencies::FIELD_CURRENCYNAME, true);
		$this->setFieldIndexed(TSysCurrencies::FIELD_CURRENCYNAME, false); //already unique
		$this->setFieldFulltext(TSysCurrencies::FIELD_CURRENCYNAME, true);
		$this->setFieldForeignKeyClass(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldForeignKeyTable(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldForeignKeyField(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldForeignKeyJoin(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysCurrencies::FIELD_CURRENCYNAME, null);
		$this->setFieldAutoIncrement(TSysCurrencies::FIELD_CURRENCYNAME, false);
		$this->setFieldUnsigned(TSysCurrencies::FIELD_CURRENCYNAME, false);
		$this->setFieldEncryptionDisabled(TSysCurrencies::FIELD_CURRENCYNAME);									


		//currency symbol
		$this->setFieldDefaultValue(TSysCurrencies::FIELD_CURRENCYSYMBOL, '');
		$this->setFieldType(TSysCurrencies::FIELD_CURRENCYSYMBOL, CT_VARCHAR);
		$this->setFieldLength(TSysCurrencies::FIELD_CURRENCYSYMBOL, 5);
		$this->setFieldDecimalPrecision(TSysCurrencies::FIELD_CURRENCYSYMBOL, 0);
		$this->setFieldPrimaryKey(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldNullable(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldEnumValues(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldUnique(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldIndexed(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldFulltext(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldForeignKeyClass(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldForeignKeyTable(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldForeignKeyField(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldForeignKeyJoin(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldForeignKeyActionOnDelete(TSysCurrencies::FIELD_CURRENCYSYMBOL, null);
		$this->setFieldAutoIncrement(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldUnsigned(TSysCurrencies::FIELD_CURRENCYSYMBOL, false);
		$this->setFieldEncryptionDisabled(TSysCurrencies::FIELD_CURRENCYSYMBOL);	

		
		//iso alphabetic code
		$this->setFieldDefaultValue(TSysCurrencies::FIELD_ISOALPHABETIC, '');
		$this->setFieldType(TSysCurrencies::FIELD_ISOALPHABETIC, CT_VARCHAR);
		$this->setFieldLength(TSysCurrencies::FIELD_ISOALPHABETIC, 3);
		$this->setFieldDecimalPrecision(TSysCurrencies::FIELD_ISOALPHABETIC, 0);
		$this->setFieldPrimaryKey(TSysCurrencies::FIELD_ISOALPHABETIC, false);
		$this->setFieldNullable(TSysCurrencies::FIELD_ISOALPHABETIC, false);
		$this->setFieldEnumValues(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldUnique(TSysCurrencies::FIELD_ISOALPHABETIC, true);
		$this->setFieldIndexed(TSysCurrencies::FIELD_ISOALPHABETIC, false); //it is already unique
		$this->setFieldFulltext(TSysCurrencies::FIELD_ISOALPHABETIC, true); 
		$this->setFieldForeignKeyClass(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldForeignKeyTable(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldForeignKeyField(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldForeignKeyJoin(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldForeignKeyActionOnDelete(TSysCurrencies::FIELD_ISOALPHABETIC, null);
		$this->setFieldAutoIncrement(TSysCurrencies::FIELD_ISOALPHABETIC, false);
		$this->setFieldUnsigned(TSysCurrencies::FIELD_ISOALPHABETIC, false);
		$this->setFieldEncryptionDisabled(TSysCurrencies::FIELD_ISOALPHABETIC);									


		//iso numeric code
		$this->setFieldDefaultValue(TSysCurrencies::FIELD_ISONUMERIC, 0);
		$this->setFieldType(TSysCurrencies::FIELD_ISONUMERIC, CT_INTEGER32);
		$this->setFieldLength(TSysCurrencies::FIELD_ISONUMERIC, 0);
		$this->setFieldDecimalPrecision(TSysCurrencies::FIELD_ISONUMERIC, 0);
		$this->setFieldPrimaryKey(TSysCurrencies::FIELD_ISONUMERIC, false);
		$this->setFieldNullable(TSysCurrencies::FIELD_ISONUMERIC, false);
		$this->setFieldEnumValues(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldUnique(TSysCurrencies::FIELD_ISONUMERIC, true);
		$this->setFieldIndexed(TSysCurrencies::FIELD_ISONUMERIC, false); //it is already unique
		$this->setFieldFulltext(TSysCurrencies::FIELD_ISONUMERIC, false); //it is already unique
		$this->setFieldForeignKeyClass(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldForeignKeyTable(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldForeignKeyField(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldForeignKeyJoin(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldForeignKeyActionOnDelete(TSysCurrencies::FIELD_ISONUMERIC, null);
		$this->setFieldAutoIncrement(TSysCurrencies::FIELD_ISONUMERIC, false);
		$this->setFieldUnsigned(TSysCurrencies::FIELD_ISONUMERIC, true);
		$this->setFieldEncryptionDisabled(TSysCurrencies::FIELD_ISONUMERIC);	


		//decimal precision, aka minor unit: number of digits after decimal separator
		$this->setFieldDefaultValue(TSysCurrencies::FIELD_DECIMALPRECISION, 0);
		$this->setFieldType(TSysCurrencies::FIELD_DECIMALPRECISION, CT_INTEGER32);
		$this->setFieldLength(TSysCurrencies::FIELD_DECIMALPRECISION, 0);
		$this->setFieldDecimalPrecision(TSysCurrencies::FIELD_DECIMALPRECISION, 0);
		$this->setFieldPrimaryKey(TSysCurrencies::FIELD_DECIMALPRECISION, false);
		$this->setFieldNullable(TSysCurrencies::FIELD_DECIMALPRECISION, false);
		$this->setFieldEnumValues(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldUnique(TSysCurrencies::FIELD_DECIMALPRECISION, false);
		$this->setFieldIndexed(TSysCurrencies::FIELD_DECIMALPRECISION, false);
		$this->setFieldFulltext(TSysCurrencies::FIELD_DECIMALPRECISION, false);
		$this->setFieldForeignKeyClass(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldForeignKeyTable(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldForeignKeyField(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldForeignKeyJoin(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldForeignKeyActionOnDelete(TSysCurrencies::FIELD_DECIMALPRECISION, null);
		$this->setFieldAutoIncrement(TSysCurrencies::FIELD_DECIMALPRECISION, false);
		$this->setFieldUnsigned(TSysCurrencies::FIELD_DECIMALPRECISION, true);
		$this->setFieldEncryptionDisabled(TSysCurrencies::FIELD_DECIMALPRECISION);					


		//exchange rate
		$this->setFieldDefaultsTDecimal(TSysCurrencies::FIELD_EXCHANGERATE, 10, 4);
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
		return array(TSysCurrencies::FIELD_CURRENCYNAME, TSysCurrencies::FIELD_CURRENCYSYMBOL, TSysCurrencies::FIELD_ISOALPHABETIC, TSysCurrencies::FIELD_ISONUMERIC, TSysCurrencies::FIELD_DECIMALPRECISION, TSysCurrencies::FIELD_ISDEFAULT, TSysCurrencies::FIELD_ISFAVORITE);
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
		return true;
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
		return APP_DB_TABLEPREFIX.'SysCurrencies';
	}
	
	
	
	/**
	 * OVERRIDE BY CHILD CLASS IF necessary
	 *
	 * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
	 * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 *
	 *
	 * return '??? - functie niet overschreven door child klasse';
	*/
	public function getDisplayRecordShort()
	{
		return $this->get(TSysCurrencies::FIELD_ISOALPHABETIC).' - '.$this->get(TSysCurrencies::FIELD_CURRENCYSYMBOL.'');
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
		return 'gekkiegerrit'.$this->get(TSysCurrencies::FIELD_ISOALPHABETIC).'isnietgek'.$this->get(TSysCurrencies::FIELD_ISONUMERIC).boolToStr($this->get(TSysCountries::FIELD_ISFAVORITE));
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