<?php
// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Localisation\models;

use dr\classes\models\TSysModel;

/**
 * This class represents languages like English, Spanish, Dutch etc.
 * This is represented by the internal acknowledged locale: Country-language
 * 
 * In Belgium(be) French(fr) and Dutch(nl) is spoken, so French is: fe-be and Dutch is: nl-be
 * 
 * The language name is in English by default (just not to overcomplicate things)
 * 
 * THIS CLASS IS CENTRAL FOR THE WHOLE FRAMEWORK!
 * so don't use separate language tables!
 * 
 * you can store languages that are availble on each site in TSysActiveLanguagesPerSite 
 * (don't overload this class, it is supposed to be a 
 * central table for the whole system and everything connected to it)
 * 
 * 18 jun 2025 field isVisible replaced by isFavorite from TSysModel
 */


class TSysLanguages extends TSysModel
{
	const FIELD_LOCALE 			= 'sLocale';
	const FIELD_LANGUAGE 		= 'sLanguage'; //the language name in english
	const FIELD_ISADMINLANGUAGE 	= 'bIsAdminLanguage'; //-> active languages per site are stored in TSysActiveLanguagesPerSite
	// const FIELD_ISSYSTEMDEFAULT = 'bIsSystemDefault';
	// const FIELD_ISVISIBLE 		= 'bIsVisible'; //preventing 400 locales are shown in html select boxes


	public function getLocale()
	{
			return $this->get(TSysLanguages::FIELD_LOCALE);
	}

	public function setLocale($sLocale)
	{
			$this->set(TSysLanguages::FIELD_LOCALE, $sLocale);
	}
	
	public function getLanguage()
	{
			return $this->get(TSysLanguages::FIELD_LANGUAGE);
	}
	
	public function setLanguage($sLanguage)
	{
			$this->set(TSysLanguages::FIELD_LANGUAGE, $sLanguage);
	}
	
	public function getIsCMSLanguage()
	{
			return  $this->get(TSysLanguages::FIELD_ISADMINLANGUAGE);
	}
	
	public function setIsCMSLanguage($bIsCMSLanguage)
	{
			$this->set(TSysLanguages::FIELD_ISADMINLANGUAGE, $bIsCMSLanguage);
	}        
			

	// public function getIsSystemDefault()
	// {
	// 		return  $this->get(TSysLanguages::FIELD_ISSYSTEMDEFAULT);
	// }
	
	// public function setIsSystemDefault($bDefault)
	// {
	// 		$this->set(TSysLanguages::FIELD_ISSYSTEMDEFAULT, $bDefault);
	// }        
	
	// public function getIsVisible()
	// {
	// 		return  $this->get(TSysLanguages::FIELD_ISVISIBLE);
	// }
	
	// public function setIsVisible($bShow)
	// {
	// 		$this->set(TSysLanguages::FIELD_ISVISIBLE, $bShow);
	// }         
	
	/**
	 * 
	 * @return boolean load ok?
	 */
	public function loadFromDBByAdminLanguage()
	{              
			$this->clear();
			$this->find(TSysLanguages::FIELD_ISADMINLANGUAGE, true);
			return $this->loadFromDB();                
	}
	
	/**
	 * 
	 * @param string $sLocale
	 * @return boolean load ok?
	 */
	public function loadFromDBByLocale($sLocale)
	{
			$this->clear();
			$this->find(TSysLanguages::FIELD_LOCALE, $sLocale);
			return $this->loadFromDB();
	}
	
	/**
	 * 
	 * @param string $sLocale
	 * @return boolean load ok?
	 */
	// public function loadFromDBByCMSShown()
	// {
	// 		$this->clear();
	// 		$this->find(TSysLanguages::FIELD_ISVISIBLE, true);
	// 		return $this->loadFromDB();
	// }




	/**
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);

		//check if exists already

		if (!$this->recordExistsTableDB(TSysLanguages::FIELD_LOCALE, APP_LOCALE_DEFAULT))
		{
			$this->newRecord();
			$this->set(TSysLanguages::FIELD_LOCALE, APP_LOCALE_DEFAULT);
			$this->set(TSysLanguages::FIELD_LANGUAGE, 'English');
			$this->set(TSysLanguages::FIELD_ISADMINLANGUAGE, true);
			$this->set(TSysLanguages::FIELD_ISDEFAULT, true);
			$this->set(TSysLanguages::FIELD_ISFAVORITE, true);
			
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving language on data propagation: '. $arrColumns[1]);
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
		$sCSV = 'bs,Bosnian
		ee-TG,Ewe (Togo)
		ms,Malay
		kam-KE,Kamba (Kenya)
		mt,Maltese
		ha,Hausa
		es-HN,Spanish (Honduras)
		ml-IN,Malayalam (India)
		ro-MD,Romanian (Moldova)
		kab-DZ,Kabyle (Algeria)
		he,Hebrew
		es-CO,Spanish (Colombia)
		my,Burmese
		es-PA,Spanish (Panama)
		az-Latn,Azerbaijani (Latin)
		mer,Meru
		en-NZ,English (New Zealand)
		xog-UG,Soga (Uganda)
		sg,Sango
		fr-GP,French (Guadeloupe)
		sr-Cyrl-BA,Serbian (Cyrillic- Bosnia and Herzegovina)
		hi,Hindi
		fil-PH,Filipino (Philippines)
		lt-LT,Lithuanian (Lithuania)
		si,Sinhala
		en-MT,English (Malta)
		si-LK,Sinhala (Sri Lanka)
		luo-KE,Luo (Kenya)
		it-CH,Italian (Switzerland)
		teo,Teso
		mfe,Morisyen
		sk,Slovak
		uz-Cyrl-UZ,Uzbek (Cyrillic- Uzbekistan)
		sl,Slovenian
		rm-CH,Romansh (Switzerland)
		az-Cyrl-AZ,Azerbaijani (Cyrillic- Azerbaijan)
		fr-GQ,French (Equatorial Guinea)
		kde,Makonde
		sn,Shona
		cgg-UG,Chiga (Uganda)
		so,Somali
		fr-RW,French (Rwanda)
		es-SV,Spanish (El Salvador)
		mas-TZ,Masai (Tanzania)
		en-MU,English (Mauritius)
		sq,Albanian
		hr,Croatian
		sr,Serbian
		en-PH,English (Philippines)
		ca,Catalan
		hu,Hungarian
		mk-MK,Macedonian (Macedonia)
		fr-TD,French (Chad)
		nb,Norwegian Bokmål
		sv,Swedish
		kln-KE,Kalenjin (Kenya)
		sw,Swahili
		nd,North Ndebele
		sr-Latn,Serbian (Latin)
		el-GR,Greek (Greece)
		hy,Armenian
		ne,Nepali
		el-CY,Greek (Cyprus)
		es-CR,Spanish (Costa Rica)
		fo-FO,Faroese (Faroe Islands)
		pa-Arab-PK,Punjabi (Arabic- Pakistan)
		seh,Sena
		ar-YE,Arabic (Yemen)
		ja-JP,Japanese (Japan)
		ur-PK,Urdu (Pakistan)
		pa-Guru,Punjabi (Gurmukhi)
		gl-ES,Galician (Spain)
		zh-Hant-HK,Chinese (Traditional Han- Hong Kong SAR China)
		ar-EG,Arabic (Egypt)
		nl,Dutch
		th-TH,Thai (Thailand)
		es-PE,Spanish (Peru)
		fr-KM,French (Comoros)
		nn,Norwegian Nynorsk
		kk-Cyrl-KZ,Kazakh (Cyrillic- Kazakhstan)
		kea,Kabuverdianu
		lv-LV,Latvian (Latvia)
		kln,Kalenjin
		tzm-Latn,Central Morocco Tamazight (Latin)
		yo,Yoruba
		gsw-CH,Swiss German (Switzerland)
		ha-Latn-GH,Hausa (Latin- Ghana)
		is-IS,Icelandic (Iceland)
		pt-BR,Portuguese (Brazil)
		cs,Czech
		en-PK,English (Pakistan)
		fa-IR,Persian (Iran)
		zh-Hans-SG,Chinese (Simplified Han- Singapore)
		luo,Luo
		ta,Tamil
		fr-TG,French (Togo)
		kde-TZ,Makonde (Tanzania)
		mr-IN,Marathi (India)
		ar-SA,Arabic (Saudi Arabia)
		ka-GE,Georgian (Georgia)
		mfe-MU,Morisyen (Mauritius)
		id,Indonesian
		fr-LU,French (Luxembourg)
		de-LU,German (Luxembourg)
		ru-MD,Russian (Moldova)
		cy,Welsh
		te,Telugu
		bg-BG,Bulgarian (Bulgaria)
		shi-Latn,Tachelhit (Latin)
		ig,Igbo
		ses,Koyraboro Senni
		ii,Sichuan Yi
		es-BO,Spanish (Bolivia)
		th,Thai
		ko-KR,Korean (South Korea)
		ti,Tigrinya
		it-IT,Italian (Italy)
		shi-Latn-MA,Tachelhit (Latin- Morocco)
		pt-MZ,Portuguese (Mozambique)
		ff-SN,Fulah (Senegal)
		haw,Hawaiian
		zh-Hans,Chinese (Simplified Han)
		so-KE,Somali (Kenya)
		bn-IN,Bengali (India)
		en-UM,English (U.S. Minor Outlying Islands)
		to,Tonga
		id-ID,Indonesian (Indonesia)
		uz-Cyrl,Uzbek (Cyrillic)
		en-GU,English (Guam)
		es-EC,Spanish (Ecuador)
		en-US-POSIX,English (United States- Computer)
		sr-Latn-BA,Serbian (Latin-Bosnia and Herzegovina)
		is,Icelandic
		luy,Luyia
		tr,Turkish
		en-NA,English (Namibia)
		it,Italian
		da,Danish
		bo-IN,Tibetan (India)
		vun-TZ,Vunjo (Tanzania)
		ar-SD,Arabic (Sudan)
		uz-Latn-UZ,Uzbek (Latin- Uzbekistan)
		az-Latn-AZ,Azerbaijani (Latin- Azerbaijan)
		de,German
		es-GQ,Spanish (Equatorial Guinea)
		ta-IN,Tamil (India)
		de-DE,German (Germany)
		fr-FR,French (France)
		rof-TZ,Rombo (Tanzania)
		ar-LY,Arabic (Libya)
		en-BW,English (Botswana)
		asa,Asu
		zh,Chinese
		ha-Latn,Hausa (Latin)
		fr-NE,French (Niger)
		es-MX,Spanish (Mexico)
		bem-ZM,Bemba (Zambia)
		zh-Hans-CN,Chinese (Simplified Han- China)
		bn-BD,Bengali (Bangladesh)
		pt-GW,Portuguese (Guinea-Bissau)
		om,Oromo
		jmc,Machame
		de-AT,German (Austria)
		kk-Cyrl,Kazakh (Cyrillic)
		sw-TZ,Swahili (Tanzania)
		ar-OM,Arabic (Oman)
		et-EE,Estonian (Estonia)
		or,Oriya
		da-DK,Danish (Denmark)
		ro-RO,Romanian (Romania)
		zh-Hant,Chinese (Traditional Han)
		bm-ML,Bambara (Mali)
		ja,Japanese
		fr-CA,French (Canada)
		naq,Nama
		zu,Zulu
		en-IE,English (Ireland)
		ar-MA,Arabic (Morocco)
		es-GT,Spanish (Guatemala)
		uz-Arab-AF,Uzbek (Arabic- Afghanistan)
		en-AS,English (American Samoa)
		bs-BA,Bosnian (Bosnia and Herzegovina)
		am-ET,Amharic (Ethiopia)
		ar-TN,Arabic (Tunisia)
		haw-US,Hawaiian (United States)
		ar-JO,Arabic (Jordan)
		fa-AF,Persian (Afghanistan)
		uz-Latn,Uzbek (Latin)
		en-BZ,English (Belize)
		nyn-UG,Nyankole (Uganda)
		ebu-KE,Embu (Kenya)
		te-IN,Telugu (India)
		cy-GB,Welsh (United Kingdom)
		uk,Ukrainian
		nyn,Nyankole
		en-JM,English (Jamaica)
		en-US,English (United States)
		fil,Filipino
		ar-KW,Arabic (Kuwait)
		af-ZA,Afrikaans (South Africa)
		en-CA,English (Canada)
		fr-DJ,French (Djibouti)
		ti-ER,Tigrinya (Eritrea)
		ig-NG,Igbo (Nigeria)
		en-AU,English (Australia)
		ur,Urdu
		fr-MC,French (Monaco)
		pt-PT,Portuguese (Portugal)
		pa,Punjabi
		es-419,Spanish (Latin America)
		fr-CD,French (Congo - Kinshasa)
		en-SG,English (Singapore)
		bo-CN,Tibetan (China)
		kn-IN,Kannada (India)
		sr-Cyrl-RS,Serbian (Cyrillic, Serbia)
		lg-UG,Ganda (Uganda)
		gu-IN,Gujarati (India)
		ee,Ewe
		nd-ZW,North Ndebele (Zimbabwe)
		bem,Bemba
		uz,Uzbek
		sw-KE,Swahili (Kenya)
		sq-AL,Albanian (Albania)
		hr-HR,Croatian (Croatia)
		mas-KE,Masai (Kenya)
		el,Greek
		ti-ET,Tigrinya (Ethiopia)
		es-AR,Spanish (Argentina)
		pl,Polish
		eo,Esperanto
		shi,Tachelhit
		kok,Konkani
		fr-CF,French (Central African Republic)
		fr-RE,French (Réunion)
		mas,Masai
		rof,Rombo
		ru-UA,Russian (Ukraine)
		yo-NG,Yoruba (Nigeria)
		dav-KE,Taita (Kenya)
		gv-GB,Manx (United Kingdom)
		pa-Arab,Punjabi (Arabic)
		es,Spanish
		teo-UG,Teso (Uganda)
		ps,Pashto
		es-PR,Spanish (Puerto Rico)
		fr-MF,French (Saint Martin)
		et,Estonian
		pt,Portuguese
		eu,Basque
		ka,Georgian
		rwk-TZ,Rwa (Tanzania)
		nb-NO,Norwegian Bokmål (Norway)
		fr-CG,French (Congo - Brazzaville)
		cgg,Chiga
		zh-Hant-TW,Chinese (Traditional Han- Taiwan)
		sr-Cyrl-ME,Serbian (Cyrillic- Montenegro)
		lag,Langi
		ses-ML,Koyraboro Senni (Mali)
		en-ZW,English (Zimbabwe)
		ak-GH,Akan (Ghana)
		vi-VN,Vietnamese (Vietnam)
		sv-FI,Swedish (Finland)
		to-TO,Tonga (Tonga)
		fr-MG,French (Madagascar)
		fr-GA,French (Gabon)
		fr-CH,French (Switzerland)
		de-CH,German (Switzerland)
		es-US,Spanish (United States)
		ki,Kikuyu
		my-MM,Burmese (Myanmar [Burma])
		vi,Vietnamese
		ar-QA,Arabic (Qatar)
		ga-IE,Irish (Ireland)
		rwk,Rwa
		bez,Bena
		ee-GH,Ewe (Ghana)
		kk,Kazakh
		as-IN,Assamese (India)
		ca-ES,Catalan (Spain)
		kl,Kalaallisut
		fr-SN,French (Senegal)
		ne-IN,Nepali (India)
		km,Khmer
		ms-BN,Malay (Brunei)
		ar-LB,Arabic (Lebanon)
		ta-LK,Tamil (Sri Lanka)
		kn,Kannada
		ur-IN,Urdu (India)
		fr-CI,French (Côte d’Ivoire)
		ko,Korean
		ha-Latn-NG,Hausa (Latin, Nigeria)
		sg-CF,Sango (Central African Republic)
		om-ET,Oromo (Ethiopia)
		zh-Hant-MO,Chinese (Traditional Han- Macau SAR China)
		uk-UA,Ukrainian (Ukraine)
		fa,Persian
		mt-MT,Maltese (Malta)
		ki-KE,Kikuyu (Kenya)
		luy-KE,Luyia (Kenya)
		kw,Cornish
		pa-Guru-IN,Punjabi (Gurmukhi- India)
		en-IN,English (India)
		kab,Kabyle
		ar-IQ,Arabic (Iraq)
		ff,Fulah
		en-TT,English (Trinidad and Tobago)
		bez-TZ,Bena (Tanzania)
		es-NI,Spanish (Nicaragua)
		uz-Arab,Uzbek (Arabic)
		ne-NP,Nepali (Nepal)
		fi,Finnish
		khq,Koyra Chiini
		gsw,Swiss German
		zh-Hans-MO,Chinese (Simplified Han- Macau SAR China)
		en-MH,English (Marshall Islands)
		hu-HU,Hungarian (Hungary)
		en-GB,English (United Kingdom)
		fr-BE,French (Belgium)
		de-BE,German (Belgium)
		saq,Samburu
		be-BY,Belarusian (Belarus)
		sl-SI,Slovenian (Slovenia)
		fo,Faroese
		fr,French
		xog,Soga
		fr-BF,French (Burkina Faso)
		tzm,Central Morocco Tamazight
		sk-SK,Slovak (Slovakia)
		fr-ML,French (Mali)
		he-IL,Hebrew (Israel)
		ru-RU,Russian (Russia)
		fr-CM,French (Cameroon)
		teo-KE,Teso (Kenya)
		seh-MZ,Sena (Mozambique)
		kl-GL,Kalaallisut (Greenland)
		fi-FI,Finnish (Finland)
		kam,Kamba
		es-ES,Spanish (Spain)
		af,Afrikaans
		asa-TZ,Asu (Tanzania)
		cs-CZ,Czech (Czech Republic)
		tr-TR,Turkish (Turkey)
		es-PY,Spanish (Paraguay)
		tzm-Latn-MA,Central Morocco Tamazight (Latin- Morocco)
		lg,Ganda
		ebu,Embu
		en-HK,English (Hong Kong SAR China)
		nl-NL,Dutch (Netherlands)
		en-BE,English (Belgium)
		ms-MY,Malay (Malaysia)
		es-UY,Spanish (Uruguay)
		ar-BH,Arabic (Bahrain)
		kw-GB,Cornish (United Kingdom)
		ak,Akan
		chr,Cherokee
		dav,Taita
		lag-TZ,Langi (Tanzania)
		am,Amharic
		so-DJ,Somali (Djibouti)
		shi-Tfng-MA,Tachelhit (Tifinagh - Morocco)
		sr-Latn-ME,Serbian (Latin - Montenegro)
		sn-ZW,Shona (Zimbabwe)
		or-IN,Oriya (India)
		ar,Arabic
		as,Assamese
		fr-BI,French (Burundi)
		jmc-TZ,Machame (Tanzania)
		chr-US,Cherokee (United States)
		eu-ES,Basque (Spain)
		saq-KE,Samburu (Kenya)
		vun,Vunjo
		lt,Lithuanian
		naq-NA,Nama (Namibia)
		ga,Irish
		af-NA,Afrikaans (Namibia)
		kea-CV,Kabuverdianu (Cape Verde)
		es-DO,Spanish (Dominican Republic)
		lv,Latvian
		kok-IN,Konkani (India)
		de-LI,German (Liechtenstein)
		fr-BJ,French (Benin)
		az,Azerbaijani
		guz-KE,Gusii (Kenya)
		rw-RW,Kinyarwanda (Rwanda)
		mg-MG,Malagasy (Madagascar)
		km-KH,Khmer (Cambodia)
		gl,Galician
		shi-Tfng,Tachelhit (Tifinagh)
		ar-AE,Arabic (United Arab Emirates)
		fr-MQ,French (Martinique)
		rm,Romansh
		sv-SE,Swedish (Sweden)
		az-Cyrl,Azerbaijani (Cyrillic)
		ro,Romanian
		so-ET,Somali (Ethiopia)
		en-ZA,English (South Africa)
		ii-CN,Sichuan Yi (China)
		fr-BL,French (Saint Barthélemy)
		hi-IN,Hindi (India)
		gu,Gujarati
		mer-KE,Meru (Kenya)
		nn-NO,Norwegian Nynorsk (Norway)
		gv,Manx
		ru,Russian
		ar-DZ,Arabic (Algeria)
		ar-SY,Arabic (Syria)
		en-MP,English (Northern Mariana Islands)
		nl-BE,Dutch (Belgium)
		rw,Kinyarwanda
		be,Belarusian
		en-VI,English (U.S. Virgin Islands)
		es-CL,Spanish (Chile)
		bg,Bulgarian
		mg,Malagasy
		hy-AM,Armenian (Armenia)
		zu-ZA,Zulu (South Africa)
		guz,Gusii
		mk,Macedonian
		es-VE,Spanish (Venezuela)
		ml,Malayalam
		bm,Bambara
		khq-ML,Koyra Chiini (Mali)
		bn,Bengali
		ps-AF,Pashto (Afghanistan)
		so-SO,Somali (Somalia)
		sr-Cyrl,Serbian (Cyrillic)
		pl-PL,Polish (Poland)
		fr-GN,French (Guinea)
		bo,Tibetan
		om-KE,Oromo (Kenya)';		
			
		$arrLines = explode("\n", $sCSV);
		$bSuccess = true;


		foreach ($arrLines as $sLine)
		{
			$arrColumns = explode(',', $sLine);

			if (!$this->recordExistsTableDB(TSysLanguages::FIELD_LOCALE, trim($arrColumns[0])))
			{
				$this->newRecord();
				$this->set(TSysLanguages::FIELD_LOCALE, trim($arrColumns[0]));
				$this->set(TSysLanguages::FIELD_LANGUAGE, $arrColumns[1]);
				// if ($arrColumns[0] == APP_LOCALE_DEFAULT)
				// {
				// 	$this->set(TSysLanguages::FIELD_ISADMINLANGUAGE, true);
				// 	$this->set(TSysLanguages::FIELD_ISSYSTEMDEFAULT, true);
				// 	$this->set(TSysLanguages::FIELD_ISVISIBLE, true);
				// }
				
				if (!$this->saveToDB())
				{
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving default language on data propagation');
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
            
		//locale
		$this->setFieldDefaultValue(TSysLanguages::FIELD_LOCALE, '');
		$this->setFieldType(TSysLanguages::FIELD_LOCALE, CT_VARCHAR);
		$this->setFieldLength(TSysLanguages::FIELD_LOCALE, 11);
		$this->setFieldDecimalPrecision(TSysLanguages::FIELD_LOCALE, 0);
		$this->setFieldPrimaryKey(TSysLanguages::FIELD_LOCALE, false);
		$this->setFieldNullable(TSysLanguages::FIELD_LOCALE, false);
		$this->setFieldEnumValues(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldUnique(TSysLanguages::FIELD_LOCALE, true);
		$this->setFieldIndexed(TSysLanguages::FIELD_LOCALE, false); //it is already unique
		$this->setFieldFulltext(TSysLanguages::FIELD_LOCALE, true); 
		$this->setFieldForeignKeyClass(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldForeignKeyTable(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldForeignKeyField(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldForeignKeyJoin(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysLanguages::FIELD_LOCALE, null);
		$this->setFieldAutoIncrement(TSysLanguages::FIELD_LOCALE, false);
		$this->setFieldUnsigned(TSysLanguages::FIELD_LOCALE, false);
        $this->setFieldEncryptionDisabled(TSysLanguages::FIELD_LOCALE);		
		
		
		//language description
		$this->setFieldCopyProps(TSysLanguages::FIELD_LANGUAGE, TSysLanguages::FIELD_LOCALE);
		$this->setFieldLength(TSysLanguages::FIELD_LANGUAGE, 100);
		$this->setFieldUnique(TSysLanguages::FIELD_LANGUAGE, true);
		$this->setFieldIndexed(TSysLanguages::FIELD_LANGUAGE, true);
		$this->setFieldFulltext(TSysLanguages::FIELD_LANGUAGE, true);
		
		//active language
		$this->setFieldDefaultValue(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldType(TSysLanguages::FIELD_ISADMINLANGUAGE, CT_BOOL);
		$this->setFieldLength(TSysLanguages::FIELD_ISADMINLANGUAGE, 0);
		$this->setFieldDecimalPrecision(TSysLanguages::FIELD_ISADMINLANGUAGE, 0);
		$this->setFieldPrimaryKey(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldNullable(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldEnumValues(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldUnique(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldIndexed(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldFulltext(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldForeignKeyClass(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldForeignKeyTable(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldForeignKeyField(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldForeignKeyJoin(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysLanguages::FIELD_ISADMINLANGUAGE, null);
		$this->setFieldAutoIncrement(TSysLanguages::FIELD_ISADMINLANGUAGE, false);
		$this->setFieldUnsigned(TSysLanguages::FIELD_ISADMINLANGUAGE, false);	
        $this->setFieldEncryptionDisabled(TSysLanguages::FIELD_ISADMINLANGUAGE);				
                
		//system default
		// $this->setFieldCopyProps(TSysLanguages::FIELD_ISSYSTEMDEFAULT, TSysLanguages::FIELD_ISADMINLANGUAGE);
                
		//shown in cms selectboxes
		// $this->setFieldCopyProps(TSysLanguages::FIELD_ISVISIBLE, TSysLanguages::FIELD_ISADMINLANGUAGE);
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
		return array(TSysLanguages::FIELD_LANGUAGE, TSysLanguages::FIELD_LOCALE, TSysLanguages::FIELD_ISADMINLANGUAGE, TSysLanguages::FIELD_ISDEFAULT, TSysLanguages::FIELD_ISFAVORITE, TSysModel::FIELD_POSITION);
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
		return true;
	}
        
	/**
	 * use locking file for editing
	*/
	public function getTableUseLock()
	{
		return true;
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
		return false;
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
		return APP_DB_TABLEPREFIX.'SysLanguages';
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
		return $this->get(TSysLanguages::FIELD_LANGUAGE).' ('.$this->get(TSysLanguages::FIELD_LOCALE).')';
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
		return 'pablo'.$this->get(TSysLanguages::FIELD_LOCALE).'picasso'.$this->get(TSysLanguages::FIELD_LOCALE).''.$this->get(TSysLanguages::FIELD_LANGUAGE).'maaktrare'.$this->get(TSysLanguages::FIELD_LANGUAGE).'schilderijen';
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