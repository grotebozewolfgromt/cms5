<?php
namespace dr\classes;


/**
 * Description of TSpamDetector
 *
 * This class is a collection of spam detection algorithms
 * Usable to filter comments on a website or a contact form
 * 
 * this class returns a spam score with getScore().
 * This is a percentage. So 0% is no spam, 100% is definitely spam
 * 
 * you are free to select which specific algorithms you want to use.
 * For example:
 * $objSpam = new TSpamDetector('viagra is delicious');
 * $objSpam->detectBlocked();
 * $objSpam->detectURLs();
 * if ($objSpam->isSpam(70))
 * 	echo "we've got spam!"
 * 
 * The order of the spam detection methods is important for speed.
 * Keep the easier-on-the-cpu methods on top
 * Further detection is skipped when score has reached 100%
 * This especially helps speeding up the detection of large amounts of messages
 * 
 *  
 * @todo letter frequenties overeenkomsten met geblockte woorden / spellingscorrectie??
 * @todo filter op email addressen
 * @todo w*h*a*t*s*a*p*p werkt nog niet
 * @todo negative connotation detection
 * @todo "specialist" contains "cialis"
 * @todo Ÿóųťųвē ĆĚÓ ⓦḧőṛē čāņ şụčķ őņ ṃẙ ḧųģę ĎĪĆĶ ! ヽ(•‿•)ノ
 * @todo þrðmð§m
 * @todo Mêssage me on Têlegram👉@Official_FilmBooth
 * @todo Send a message👆👆👆with the above username on Telè gram💫.
 * @todo all special characters: https://en.wikipedia.org/wiki/A_with_grave_(Cyrillic)
 * @todo implement algo for 'edit distance' between words (pron = porn = 1 edit distance) to detect misspelled spam words. algo: https://www.youtube.com/watch?v=d-Eq6x1yssU
 * 
 * 18 mrt 2022: TSpamDetector: created
 * 18 mrt 2022: TSpamDetector: verbeterd
 * 19 mrt 2022: TSpamDetector: translateChars() toegevoegd
 * 19 mrt 2022: TSpamDetector: badEmoji filter werkt nu
 * 19 mrt 2022: TSpamDetector: detectPunctiation() added
 * 22 mrt 2022: TSpamDetector: speedup: if score is 100 then no further detection is needed and skips detection functions
 * 22 mrt 2022: TSpamDetector: getWords() updated with /r not replaced by space
 * 22 mrt 2022: TSpamDetector: updated
 * 19 jan 2023: TSpamDetector: updated
 * 25 jan 2023: TSpamDetector: added detectNonLatinCharacterSet()
 * 19 may 2023: TSpamDetector: added detection to translateChars 
 * 19 may 2023: TSpamDetector: added detection to detectnumbers 
 * 
* 
 * @author dennis renirie
 */

class TSpamDetector
{
	protected $sDangerousCharacterRLO = "\u{202E}"; //right to left override

	protected $iScore = 0; //the highest reported score so far. if 1 detection method detects 100%, it can't go down
	protected $sSourceText = ''; 
	protected $arrBlockedWords = array(); //a list of words that are not allowed to be used in any way, shape or form. These are weighted. The arraykeyindex is the actual blocked word and the weight is the value
	protected $arrLog = array();//1d array of strings with log why spam is detected (for debug purposes). English only!

	public function __construct($sText = '')
	{
		$this->sSourceText = $sText;
		// $this->arrBlockedWords = $this->getBlockedWordsInit();
		$this->arrBlockedWords = $this->getBlockedWordsWeightedInit();
	}

	public function setText($sText)
	{
		$this->sSourceText = $sText;
	}

	public function getText()
	{
		return $this->sSourceText;
	}

	/**
	 * set a spam score to the internal counter.
	 * only adds when new score is higher than current score.
	 *
	 * @return void
	 */
	private function setNewHighScore($iNewScore)
	{
		if ($iNewScore > $this->iScore)
			$this->iScore = $iNewScore;
		
		if ($this->iScore > 100) //can't exceed 100%
			$this->iScore = 100;
	}

	/**
	 * incease spam likelyhood by $iInceaseAmount
	 * 
	 * for example: incease 10% and current score is 60%
	 * this will add 10% to 60% = 70%
	 *
	 * there is a base score assumed ($iIfScoreHigherThan).
	 * increaseScore(10, 50) adds 10 when base score is 50.
	 * When current score is not 50, it will add 10 to 50 = 60
	 * 
	 * @param int $iInceaseAmount
	 * @param int $iIfScoreHigherThan base score to incease, if spam chance 
	 * @return void
	 */
	private function increaseScore($iInceaseAmount, $iIfScoreHigherThan = 0)
	{
		$iOldScore = $this->iScore;

		if ($this->iScore > $iIfScoreHigherThan)
			$this->iScore+= $iInceaseAmount;
		else
			$this->iScore = $iIfScoreHigherThan + $iInceaseAmount;


		if ($this->iScore > 100) //can't exceed 100%
			$this->iScore = 100;			
		
		$this->arrLog[] = 'Score updated via increaseScore(). Increased:'.$iInceaseAmount.' MinimumScore:'.$iIfScoreHigherThan.' Oldscore: '.$iOldScore.' Newscore: '.$this->iScore;
	}

	/**
	 * clear score and blocked words list (uses default blocked words list)
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->iScore = 0;
		$this->arrBlockedWords = $this->getBlockedWordsWeightedInit();
	}

	/**
	 * return internal array with blocked words
	 *
	 * @return array
	 */	
	public function getBlockedWords()
	{
		return $this->arrBlockedWords;
	}


	public function getBlockedWordsWeightedInit()
	{
		$arrBlockedWords = array();
		$arrBlockedWords['watsapp'] = 100;
		$arrBlockedWords['watsap'] = 100;
		$arrBlockedWords['whatsapp'] = 100;
		$arrBlockedWords['whatsap'] = 100;
		$arrBlockedWords['telegram'] = 100;
		$arrBlockedWords['text message'] = 40;
		$arrBlockedWords['message'] = 20; //message me on telegram
		$arrBlockedWords['text me'] = 40;
		$arrBlockedWords['sms'] = 40;
		$arrBlockedWords['viagra'] = 100;
		$arrBlockedWords['cialis'] = 100; //"specialist" has cialis in it
		$arrBlockedWords['levitra'] = 100;
		$arrBlockedWords['kamagra'] = 100;
		$arrBlockedWords['drugs'] = 100;
		$arrBlockedWords['vitamins'] = 80;
		$arrBlockedWords['supplements'] = 80;
		$arrBlockedWords['meds'] = 90;
		$arrBlockedWords['medication'] = 85;
		$arrBlockedWords['pills'] = 85;
		$arrBlockedWords['FDA'] = 80; //as in "FDA approved"
		$arrBlockedWords['FedEx'] = 60; //as in "failed fedex delivery"
		$arrBlockedWords['trusted'] = 40;//as in "trusted meds"
		$arrBlockedWords['health'] = 40;
		$arrBlockedWords['healt'] = 40;
		$arrBlockedWords['pharmacy'] = 80;
		$arrBlockedWords['parmacy'] = 80;
		$arrBlockedWords['louis vuitton'] = 90;
		$arrBlockedWords['discount'] = 90; //commercial intent
		$arrBlockedWords['immediately'] = 30; //commercial intent
		$arrBlockedWords['instantly'] = 30; //commercial intent
		$arrBlockedWords['demo'] = 30; //commercial intent
		$arrBlockedWords['trial'] = 30; //commercial intent
		$arrBlockedWords['leads'] = 50; //commercial intent
		$arrBlockedWords['unsubscribe'] = 60; //inclines you are subscribed to some sort of mailing list
		$arrBlockedWords['click here'] = 50; //entices to click somewhere on a link
		$arrBlockedWords['suspended'] = 90; //entices to click somewhere on a link because they need to take action now, because something bogus was suspended
		$arrBlockedWords['now'] = 10; //entices to click somewhere on a link because they need to take action now, because something bogus was suspended
		$arrBlockedWords['sale'] = 50; //implies commercial promotion
		$arrBlockedWords['porn'] = 100; //18+
		$arrBlockedWords['pron'] = 5; //18+
		$arrBlockedWords['sex'] = 5; //18+
		$arrBlockedWords['girls'] = 2; //18+
		$arrBlockedWords['casino'] = 70; //promotion for gambling
		$arrBlockedWords['loterij'] = 70; //promotion for gambling
		$arrBlockedWords['lottery'] = 70; //promotion for gambling
		$arrBlockedWords['giveaway'] = 70; //promotion for gambling
		$arrBlockedWords['las vegas'] = 70; //promotion for gambling
		$arrBlockedWords['vegas'] = 10; //promotion for gambling
		$arrBlockedWords['winner'] = 20; //promotion for gambling
		$arrBlockedWords['claim'] = 20; //promotion for gambling (claim your prize)
		$arrBlockedWords['package'] = 20; //promotion for gambling (claim your package)
		$arrBlockedWords['promos'] = 20; //promotion for gambling
		$arrBlockedWords['promosm'] = 50; //promosm (don't know what it is but used a lot for spam)
		$arrBlockedWords['selected'] = 10; //promotion for gambling (selected random winner)
		$arrBlockedWords['random'] = 10; //promotion for gambling (selected random winner)
		$arrBlockedWords['congratulations'] = 20; //promotion for gambling
		$arrBlockedWords['shortlisted'] = 5; //promotion for gambling "congratulations you've been selected amongst our shortlisted winners"
		$arrBlockedWords['prize'] = 30; //promotion for gambling
		$arrBlockedWords['username'] = 30; //promotion for gambling (leave username in comments)
		$arrBlockedWords['visa'] = 70; //selling or extortion intent
		$arrBlockedWords['mastercard'] = 80; //selling or extortion intent
		$arrBlockedWords['amex'] = 65; //selling or extortion intent
		$arrBlockedWords['mastercard'] = 65; //selling or extortion intent
		$arrBlockedWords['amex'] = 65; //selling or extortion intent
		$arrBlockedWords['credit card'] = 60; //selling or extortion intent
		$arrBlockedWords['crypto'] = 100; //selling or extortion intent
		$arrBlockedWords['bitcoin'] = 100; //selling or extortion intent
		$arrBlockedWords['bitcion'] = 100; //selling or extortion intent
		$arrBlockedWords['eth'] = 1; //selling or extortion intent ('eth' can also be in ethernet)
		$arrBlockedWords['wallet'] = 20; //selling or extortion intent
		$arrBlockedWords['promotrades'] = 80; //news@promotrades.com
		$arrBlockedWords['cannabis'] = 80; //
		$arrBlockedWords['weed'] = 80; //
		$arrBlockedWords['grass'] = 20; //
		$arrBlockedWords[' met h'] = 20; //also in 'meth'od
		$arrBlockedWords[' chat gpt '] = 80; 
		$arrBlockedWords[' chatgpt '] = 80; 
		$arrBlockedWords[' openai '] = 80; 
		$arrBlockedWords[' a.i. '] = 80; 
		$arrBlockedWords[' ai '] = 80; 
		$arrBlockedWords['seggs'] = 50; //spells: sex
		$arrBlockedWords['segs'] = 50; //spells: sex
		$arrBlockedWords[' seo '] = 50; //spam to sell fake SEO services
		$arrBlockedWords[' s.e.o. '] = 50; //spam to sell fake SEO services
		$arrBlockedWords[' google '] = 50; //spam to sell fake SEO services

		//profanity
		$arrBlockedWords[' fucking '] = 90; 
		$arrBlockedWords[' fuckin '] = 90; 
		$arrBlockedWords[' asshole '] = 90; 
		$arrBlockedWords[' faggot '] = 90; 
		$arrBlockedWords[' fag '] = 80; 
		$arrBlockedWords[' cunt '] = 80; 
		

		return $arrBlockedWords;
	}

	/**
	 * add a blocked word
	 *
	 * @param string $sOneWord
	 * @return void
	 */
	public function addBlockedWords($sOneWord)
	{
		$this->arrBlockedWords[] = $sOneWord;
	}





	/**
	 * some characters have other meanings
	 * translate characters that visually look like another character and 
	 * is therefore human readable.
	 * 
	 * example 1:
	 * viăgra
	 * example 2: 
	 * cia1is
	 * 
	 * other examples:
	 * i = l / l = 1 / 0 = O / * = o (p*rn) / E = 3
	 * @return string
	 */
	private function translateChars($sInput)
	{
		$sFiltered = $sInput;

		//===filter input
		//accents
		$sFiltered = strtolower(collateString($this->sSourceText));
		
		
		//punctuations (like fake slashes)
		$sVisual = array('⁄', '∕', '／', '⧸', '⫽', '⫻', '⧵',  '⧹'  ,'⑊',     '﹨', '＼');
		$sReal   = array('/', '/', '/', '/' , '//', '//', '\\', '\\', '\\\\', '\\', '\\');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  


		//other visual characters //🇻-🇮-🇦-🇬-🇷-🇦 𝐩𝓻Ỗ𝓂Ø𝓈Ｍ https://en.wikipedia.org/wiki/Letterlike_Symbols
		$sVisual = array('1', '0', '3', '4', '*', '[', '|', '@', '$', '€', '§');
		$sReal   = array('l', 'o', 'e', 'a', 'o', 'l', 'l', 'a', 's', 'e', 's');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		//https://en.wikipedia.org/wiki/A_with_grave_(Cyrillic) 
		//@TODO => other cyrillic letters too
		$sVisual = array('А', 'а', 'А́','а́', 'А̀', 'а̀', 'А̂');
		$sReal   = array('a', 'a', 'a', 'a', 'A', 'a', 'a');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		// https://en.wikipedia.org/wiki/Letterlike_Symbols
		$sVisual = array('ℂ', '℃', '℄', '℈', '℉', 'ℋ', 'ℌ', 'ℍ', 'ℎ', 'ℏ', 'ℐ', 'ℑ', 'ℒ', 'ℓ', 'ℕ', '№', '℗', '℘', 'ℙ', 'ℚ', 'ℛ', 'ℜ', 'ℝ', '℞', '℟', '™', '℣', 'ℤ', 'Ω', '℧', 'ℨ', 'K', 'Å', 'ℬ', 'ℭ', '℮', 'ℯ', 'ℰ', 'ℱ', 'ℳ', 'ℴ', 'ℵ', 'ℼ', 'ℽ', 'ℾ', 'ℿ', '⅀', 'ⅅ', 'ⅆ', 'ⅇ', 'ⅈ', 'ⅉ', '⅊', '⅌');
		$sReal   =  array('c', 'c', 'l', 'e',  'f', 'h',  'h', 'h',  'h', 'h', 'i', 'i', 'l', 'l', 'n', 'n', 'p',  'p', 'p', 'q',  'r', 'r', 'r',  'r', 'r', 'tm', 'v', 'z', 'o', 'u', '3', 'k', 'a', 'b',  'c', 'e', 'e', 'e', 'f', 'm',  'o', 'k', 'n', 'y', 'r', 'n', 'e', 'd', 'd', 'e', 'i', 'j', 'p', 'p');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  


		//https://www.fancy-letters.com/symbolic.html ==> tot de glitchy letters
		//𝕒𝕓𝕔𝕕𝕖𝕗𝕘𝕙𝕚𝕛𝕜𝕝𝕞𝕟𝕠𝕡𝕢𝕣𝕤𝕥𝕦𝕧𝕨𝕩𝕪𝕫𝔸𝔹ℂ𝔻𝔼𝔽𝔾ℍ𝕀𝕁𝕂𝕃𝕄ℕ𝕆ℙℚℝ𝕊𝕋𝕌𝕍𝕎𝕏𝕐ℤ
		//a̷b̷c̷d̷e̷f̷g̷h̷i̷j̷k̷l̷m̷n̷o̷p̷q̷r̷s̷t̷u̷v̷w̷x̷y̷z̷A̷B̷C̷D̷E̷F̷G̷H̷I̷J̷K̷L̷M̷N̷O̷P̷Q̷R̷S̷T̷U̷V̷W̷X̷Y̷Z̷
		//🅰🅱🅲🅳🅴🅵🅶🅷🅸🅹🅺🅻🅼🅽🅾🅿🆀🆁🆂🆃🆄🆅🆆🆇🆈🆉
		//ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘQʀꜱᴛᴜᴠᴡxʏᴢᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘQʀꜱᴛᴜᴠᴡxʏᴢ
		//𝓪𝓫𝓬𝓭𝓮𝓯𝓰𝓱𝓲𝓳𝓴𝓵𝓶𝓷𝓸𝓹𝓺𝓻𝓼𝓽𝓾𝓿𝔀𝔁𝔂𝔃𝓐𝓑𝓒𝓓𝓔𝓕𝓖𝓗𝓘𝓙𝓚𝓛𝓜𝓝𝓞𝓟𝓠𝓡𝓢𝓣𝓤𝓥𝓦𝓧𝓨𝓩
		//ǟɮƈɖɛʄɢɦɨʝӄʟʍռօքզʀֆȶʊʋաӼʏʐǟɮƈɖɛʄɢɦɨʝӄʟʍռօքզʀֆȶʊʋաӼʏʐ
		//🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉
		//ДБCDΞFGHIJҜLMИФPǪЯSΓЦVЩЖУZДБCDΞFGHIJҜLMИФPǪЯSΓЦVЩЖУZ
		//𝔞𝔟𝔠𝔡𝔢𝔣𝔤𝔥𝔦𝔧𝔨𝔩𝔪𝔫𝔬𝔭𝔮𝔯𝔰𝔱𝔲𝔳𝔴𝔵𝔶𝔷𝔄𝔅ℭ𝔇𝔈𝔉𝔊ℌℑ𝔍𝔎𝔏𝔐𝔑𝔒𝔓𝔔ℜ𝔖𝔗𝔘𝔙𝔚𝔛𝔜ℨ
		//𝖆𝖇𝖈𝖉𝖊𝖋𝖌𝖍𝖎𝖏𝖐𝖑𝖒𝖓𝖔𝖕𝖖𝖗𝖘𝖙𝖚𝖛𝖜𝖝𝖞𝖟𝕬𝕭𝕮𝕯𝕰𝕱𝕲𝕳𝕴𝕵𝕶𝕷𝕸𝕹𝕺𝕻𝕼𝕽𝕾𝕿𝖀𝖁𝖂𝖃𝖄𝖅
		//𝒶𝒷𝒸𝒹𝑒𝒻𝑔𝒽𝒾𝒿𝓀𝓁𝓂𝓃𝑜𝓅𝓆𝓇𝓈𝓉𝓊𝓋𝓌𝓍𝓎𝓏𝒜𝐵𝒞𝒟𝐸𝐹𝒢𝐻𝐼𝒥𝒦𝐿𝑀𝒩𝒪𝒫𝒬𝑅𝒮𝒯𝒰𝒱𝒲𝒳𝒴𝒵
		//𝘢𝘣𝘤𝘥𝘦𝘧𝘨𝘩𝘪𝘫𝘬𝘭𝘮𝘯𝘰𝘱𝘲𝘳𝘴𝘵𝘶𝘷𝘸𝘹𝘺𝘻𝘈𝘉𝘊𝘋𝘌𝘍𝘎𝘏𝘐𝘑𝘒𝘓𝘔𝘕𝘖𝘗𝘘𝘙𝘚𝘛𝘜𝘝𝘞𝘟𝘠𝘡
		//𝙖𝙗𝙘𝙙𝙚𝙛𝙜𝙝𝙞𝙟𝙠𝙡𝙢𝙣𝙤𝙥𝙦𝙧𝙨𝙩𝙪𝙫𝙬𝙭𝙮𝙯𝘼𝘽𝘾𝘿𝙀𝙁𝙂𝙃𝙄𝙅𝙆𝙇𝙈𝙉𝙊𝙋𝙌𝙍𝙎𝙏𝙐𝙑𝙒𝙓𝙔𝙕
		//𝚊𝚋𝚌𝚍𝚎𝚏𝚐𝚑𝚒𝚓𝚔𝚕𝚖𝚗𝚘𝚙𝚚𝚛𝚜𝚝𝚞𝚟𝚠𝚡𝚢𝚣𝙰𝙱𝙲𝙳𝙴𝙵𝙶𝙷𝙸𝙹𝙺𝙻𝙼𝙽𝙾𝙿𝚀𝚁𝚂𝚃𝚄𝚅𝚆𝚇𝚈𝚉
		//ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩⒶⒷⒸⒹⒺⒻⒼⒽⒾⒿⓀⓁⓂⓃⓄⓅⓆⓇⓈⓉⓊⓋⓌⓍⓎⓏ
		//🅐🅑🅒🅓🅔🅕🅖🅗🅘🅙🅚🅛🅜🅝🅞🅟🅠🅡🅢🅣🅤🅥🅦🅧🅨🅩
		//ᗩᗷᑕᗪᗴᖴǤᕼᎥᒎᛕᒪᗰᑎᗝᑭɊᖇᔕ丅ᑌᐯᗯ᙭Ƴ乙ᗩᗷᑕᗪᗴᖴǤᕼᎥᒎᛕᒪᗰᑎᗝᑭɊᖇᔕ丅ᑌᐯᗯ᙭Ƴ乙
		//ａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺ
		//𝐚𝐛𝐜𝐝𝐞𝐟𝐠𝐡𝐢𝐣𝐤𝐥𝐦𝐧𝐨𝐩𝐪𝐫𝐬𝐭𝐮𝐯𝐰𝐱𝐲𝐳𝐀𝐁𝐂𝐃𝐄𝐅𝐆𝐇𝐈𝐉𝐊𝐋𝐌𝐍𝐎𝐏𝐐𝐑𝐒𝐓𝐔𝐕𝐖𝐗𝐘𝐙
		//Z⅄XMΛ∩⊥SᴚΌԀONW˥⋊ſIH⅁ℲƎᗡƆᙠ∀zʎxʍʌnʇsɹbdouɯlʞɾıɥɓɟǝpɔqɐ --> upside down
		//ɐqɔpǝɟƃɥᴉɾʞlɯuodbɹsʇnʌʍxʎzɐqɔpǝɟƃɥᴉɾʞlɯuodbɹsʇnʌʍxʎz ==> upside down in abc order
		//ƸYXWVUTꙄЯỌꟼOͶM⅃⋊ႱIHᎮꟻƎᗡƆᙠAƹʏxwvuƚꙅɿpqoᴎm|ʞꞁiʜǫᎸɘbɔdɒ
		//卂乃匚ᗪ乇千Ꮆ卄丨ﾌҜㄥ爪几ㄖ卩Ɋ尺丂ㄒㄩᐯ山乂ㄚ乙卂乃匚ᗪ乇千Ꮆ卄丨ﾌҜㄥ爪几ㄖ卩Ɋ尺丂ㄒㄩᐯ山乂ㄚ乙
		//ﾑ乃ᄃり乇ｷムんﾉﾌズﾚﾶ刀のｱゐ尺丂ｲひ√Wﾒﾘ乙ﾑ乃ᄃり乇ｷムんﾉﾌズﾚﾶ刀のｱゐ尺丂ｲひ√Wﾒﾘ乙
		//คც८ძ૯Բ૭ҺɿʆқՆɱՈ૦ƿҩՐς੮υ౮ω૪עઽคც८ძ૯Բ૭ҺɿʆқՆɱՈ૦ƿҩՐς੮υ౮ω૪עઽ
		//абcдёfgнїjкгѫпѳpфя$тцѵщжчзАБCДЄFGHЇJКГѪЙѲPФЯ$TЦѴШЖЧЗ
		//ДБCDΞFGHIJҜLMИФPǪЯSΓЦVЩЖУZДБCDΞFGHIJҜLMИФPǪЯSΓЦVЩЖУZ
		//αßςdεƒghïյκﾚmη⊕pΩrš†u∀ωxψzαßςdεƒghïյκﾚmη⊕pΩrš†u∀ωxψz
		//ΔβĆĐ€₣ǤĦƗĴҜŁΜŇØƤΩŘŞŦỮVŴЖ¥ŽΔβĆĐ€₣ǤĦƗĴҜŁΜŇØƤΩŘŞŦỮVŴЖ¥Ž
		//ꍏꌃꉓꀸꍟꎇꁅꃅꀤꀭꀘ꒒ꂵꈤꂦꉣꆰꋪꌗ꓄ꀎꃴꅏꊼꌩꁴꍏꌃꉓꀸꍟꎇꁅꃅꀤꀭꀘ꒒ꂵꈤꂦꉣꆰꋪꌗ꓄ꀎꃴꅏꊼꌩꁴ
		//ꋬꃳꉔ꒯ꏂꊰꍌꁝ꒐꒻ꀘ꒒ꂵꋊꄲꉣꆰꋪꇙ꓄꒤꒦ꅐꉧꌦꁴꋬꃳꉔ꒯ꏂꊰꍌꁝ꒐꒻ꀘ꒒ꂵꋊꄲꉣꆰꋪꇙ꓄꒤꒦ꅐꉧꌦꁴ
		//ᵃᵇᶜᵈᵉᶠᵍʰⁱʲᵏˡᵐⁿᵒᵖqʳˢᵗᵘᵛʷˣʸᶻᴬᴮᶜᴰᴱᶠᴳᴴᴵᴶᴷᴸᴹᴺᴼᴾQᴿˢᵀᵁⱽᵂˣʸᶻ
		//ₐ𝚋𝚌𝚍ₑfg𝓱ᵢⱼ𝓴ᄂᗰ𝚗ₒ𝐩qᵣ𝘴𝚝ᵤᵥwₓyzₐBCDₑFGHᵢⱼKLMNₒPQᵣSTᵤᵥWₓYZ
		//ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘQʀꜱᴛᴜᴠᴡxʏᴢᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘQʀꜱᴛᴜᴠᴡxʏᴢ
		//a̶b̶c̶d̶e̶f̶g̶h̶i̶j̶k̶l̶m̶n̶o̶p̶q̶r̶s̶t̶u̶v̶w̶x̶y̶z̶A̶B̶C̶D̶E̶F̶G̶H̶I̶J̶K̶L̶M̶N̶O̶P̶Q̶R̶S̶T̶U̶V̶W̶X̶Y̶Z̶
		//a̴b̴c̴d̴e̴f̴g̴h̴i̴j̴k̴l̴m̴n̴o̴p̴q̴r̴s̴t̴u̴v̴w̴x̴y̴z̴A̴B̴C̴D̴E̴F̴G̴H̴I̴J̴K̴L̴M̴N̴O̴P̴Q̴R̴S̴T̴U̴V̴W̴X̴Y̴Z̴
		//𝚊̷𝚋̷𝚌̷𝚍̷𝚎̷𝚏̷𝚐̷𝚑̷𝚒̷𝚓̷𝚔̷𝚕̷𝚖̷𝚗̷𝚘̷𝚙̷𝚚̷𝚛̷𝚜̷𝚝̷𝚞̷𝚟̷𝚠̷𝚡̷𝚢̷𝚣̷𝙰̷𝙱̷𝙲̷𝙳̷𝙴̷𝙵̷𝙶̷𝙷̷𝙸̷𝙹̷𝙺̷𝙻̷𝙼̷𝙽̷𝙾̷𝙿̷𝚀̷𝚁̷𝚂̷𝚃̷𝚄̷𝚅̷𝚆̷𝚇̷𝚈̷𝚉̷
		//a̷b̷c̷d̷e̷f̷g̷h̷i̷j̷k̷l̷m̷n̷o̷p̷q̷r̷s̷t̷u̷v̷w̷x̷y̷z̷A̷B̷C̷D̷E̷F̷G̷H̷I̷J̷K̷L̷M̷N̷O̷P̷Q̷R̷S̷T̷U̷V̷W̷X̷Y̷Z̷
		//a̲b̲c̲d̲e̲f̲g̲h̲i̲j̲k̲l̲m̲n̲o̲p̲q̲r̲s̲t̲u̲v̲w̲x̲y̲z̲A̲B̲C̲D̲E̲F̲G̲H̲I̲J̲K̲L̲M̲N̲O̲P̲Q̲R̲S̲T̲U̲V̲W̲X̲Y̲Z̲
		//a̳b̳c̳d̳e̳f̳g̳h̳i̳j̳k̳l̳m̳n̳o̳p̳q̳r̳s̳t̳u̳v̳w̳x̳y̳z̳A̳B̳C̳D̳E̳F̳G̳H̳I̳J̳K̳L̳M̳N̳O̳P̳Q̳R̳S̳T̳U̳V̳W̳X̳Y̳Z̳
		//a͢b͢c͢d͢e͢f͢g͢h͢i͢j͢k͢l͢m͢n͢o͢p͢q͢r͢s͢t͢u͢v͢w͢x͢y͢z͢A͢B͢C͢D͢E͢F͢G͢H͢I͢J͢K͢L͢M͢N͢O͢P͢Q͢R͢S͢T͢U͢V͢W͢X͢Y͢Z͢
		//ค๒ς๔єŦﻮђเןкɭ๓ภ๏קợгรՇยשฬאץչค๒ς๔єŦﻮђเןкɭ๓ภ๏קợгรՇยשฬאץչ
		//αႦƈԃҽϝɠԋιʝƙʅɱɳσρϙɾʂƚυʋɯxყȥABCDEFGHIJKLMNOPQRSTUVWXYZ
		//ǟɮƈɖɛʄɢɦɨʝӄʟʍռօքզʀֆȶʊʋաӼʏʐǟɮƈɖɛʄɢɦɨʝӄʟʍռօքզʀֆȶʊʋաӼʏʐ
		//ᏗᏰፈᎴᏋᎦᎶᏂᎥᏠᏦᏝᎷᏁᎧᎮᎤᏒᏕᏖᏬᏉᏇጀᎩፚᏗᏰፈᎴᏋᎦᎶᏂᎥᏠᏦᏝᎷᏁᎧᎮᎤᏒᏕᏖᏬᏉᏇጀᎩፚ
		//ąცƈɖɛʄɠɧıʝƙƖɱŋơ℘զཞʂɬų۷ῳҳყʑąცƈɖɛʄɠɧıʝƙƖɱŋơ℘զཞʂɬų۷ῳҳყʑ
		//ค๖¢໓ēfງhiวkl๓ຖ໐p๑rŞtนงຟxฯຊค๖¢໓ēfງhiวkl๓ຖ໐p๑rŞtนงຟxฯຊ
		//αზƈԃҽϝɠԋιʝƙʅɱɳσρϙɾʂƚυʋɯxყȥABCDEFGHIJKLMNOPQRSTUVWXYZ
		//ĂβČĎĔŦĞĤĨĴĶĹМŃŐРQŔŚŤÚVŴЖŶŹĂβČĎĔŦĞĤĨĴĶĹМŃŐРQŔŚŤÚVŴЖŶŹ
		//ΛϦㄈÐƐFƓнɪﾌҚŁ௱ЛØþҨ尺らŤЦƔƜχϤẔΛϦㄈÐƐFƓнɪﾌҚŁ௱ЛØþҨ尺らŤЦƔƜχϤẔ
		//ƛƁƇƊЄƑƓӇƖʆƘԼMƝƠƤƢƦƧƬƲƔƜҲƳȤƛƁƇƊЄƑƓӇƖʆƘԼMƝƠƤƢƦƧƬƲƔƜҲƳȤ
		//ԹՅՇԺȝԲԳɧɿʝƙʅʍՌԾρφՐՏԵՄעաՃՎՀԹՅՇԺȝԲԳɧɿʝƙʅʍՌԾρφՐՏԵՄעաՃՎՀ
		//αɓ૮∂εƒɠɦเʝҡℓɱɳσρφ૨รƭµѵωאყƶαɓ૮∂εƒɠɦเʝҡℓɱɳσρφ૨รƭµѵωאყƶ
		//მჩეძპfცhἶქκlოῆõρგΓჰནυὗwჯყɀმჩეძპfცhἶქκlოῆõρგΓჰནυὗwჯყɀ
		//άвςȡέғģħίјķĻмήόρqŕşţùνώxчžάвςȡέғģħίјķĻмήόρqŕşţùνώxчž
		//a҉b҉c҉d҉e҉f҉g҉h҉i҉j҉k҉l҉m҉n҉o҉p҉q҉r҉s҉t҉u҉v҉w҉x҉y҉z҉A҉B҉C҉D҉E҉F҉G҉H҉I҉J҉K҉L҉M҉N҉O҉P҉Q҉R҉S҉T҉U҉V҉W҉X҉Y҉Z҉
		//a̼b̼c̼d̼e̼f̼g̼h̼i̼j̼k̼l̼m̼n̼o̼p̼q̼r̼s̼t̼u̼v̼w̼x̼y̼z̼A̼B̼C̼D̼E̼F̼G̼H̼I̼J̼K̼L̼M̼N̼O̼P̼Q̼R̼S̼T̼U̼V̼W̼X̼Y̼Z̼
		//a͆b͆c͆d͆e͆f͆g͆h͆i͆j͆k͆l͆m͆n͆o͆p͆q͆r͆s͆t͆u͆v͆w͆x͆y͆z͆A͆B͆C͆D͆E͆F͆G͆H͆I͆J͆K͆L͆M͆N͆O͆P͆Q͆R͆S͆T͆U͆V͆W͆X͆Y͆Z͆
		//a̺b̺c̺d̺e̺f̺g̺h̺i̺j̺k̺l̺m̺n̺o̺p̺q̺r̺s̺t̺u̺v̺w̺x̺y̺z̺A̺B̺C̺D̺E̺F̺G̺H̺I̺J̺K̺L̺M̺N̺O̺P̺Q̺R̺S̺T̺U̺V̺W̺X̺Y̺Z̺
		//a͙b͙c͙d͙e͙f͙g͙h͙i͙j͙k͙l͙m͙n͙o͙p͙q͙r͙s͙t͙u͙v͙w͙x͙y͙z͙A͙B͙C͙D͙E͙F͙G͙H͙I͙J͙K͙L͙M͙N͙O͙P͙Q͙R͙S͙T͙U͙V͙W͙X͙Y͙Z͙
		//a̟b̟c̟d̟e̟f̟g̟h̟i̟j̟k̟l̟m̟n̟o̟p̟q̟r̟s̟t̟u̟v̟w̟x̟y̟z̟A̟B̟C̟D̟E̟F̟G̟H̟I̟J̟K̟L̟M̟N̟O̟P̟Q̟R̟S̟T̟U̟V̟W̟X̟Y̟Z̟
		//a͎b͎c͎d͎e͎f͎g͎h͎i͎j͎k͎l͎m͎n͎o͎p͎q͎r͎s͎t͎u͎v͎w͎x͎y͎z͎A͎B͎C͎D͎E͎F͎G͎H͎I͎J͎K͎L͎M͎N͎O͎P͎Q͎R͎S͎T͎U͎V͎W͎X͎Y͎Z͎
		//a͓̽b͓̽c͓̽d͓̽e͓̽f͓̽g͓̽h͓̽i͓̽j͓̽k͓̽l͓̽m͓̽n͓̽o͓̽p͓̽q͓̽r͓̽s͓̽t͓̽u͓̽v͓̽w͓̽x͓̽y͓̽z͓̽A͓̽B͓̽C͓̽D͓̽E͓̽F͓̽G͓̽H͓̽I͓̽J͓̽K͓̽L͓̽M͓̽N͓̽O͓̽P͓̽Q͓̽R͓̽S͓̽T͓̽U͓̽V͓̽W͓̽X͓̽Y͓̽Z͓̽
		//a̾b̾c̾d̾e̾f̾g̾h̾i̾j̾k̾l̾m̾n̾o̾p̾q̾r̾s̾t̾u̾v̾w̾x̾y̾z̾A̾B̾C̾D̾E̾F̾G̾H̾I̾J̾K̾L̾M̾N̾O̾P̾Q̾R̾S̾T̾U̾V̾W̾X̾Y̾Z̾
		//åb̊c̊d̊e̊f̊g̊h̊i̊j̊k̊l̊m̊n̊o̊p̊q̊r̊s̊t̊ův̊ẘx̊ẙz̊ÅB̊C̊D̊E̊F̊G̊H̊I̊J̊K̊L̊M̊N̊O̊P̊Q̊R̊S̊T̊ŮV̊W̊X̊Y̊Z̊
		//a͛b͛c͛d͛e͛f͛g͛h͛i͛j͛k͛l͛m͛n͛o͛p͛q͛r͛s͛t͛u͛v͛w͛x͛y͛z͛A͛B͛C͛D͛E͛F͛G͛H͛I͛J͛K͛L͛M͛N͛O͛P͛Q͛R͛S͛T͛U͛V͛W͛X͛Y͛Z͛
		//a⃣b⃣c⃣d⃣e⃣f⃣g⃣h⃣i⃣j⃣k⃣l⃣m⃣n⃣o⃣p⃣q⃣r⃣s⃣t⃣u⃣v⃣w⃣x⃣y⃣z⃣A⃣B⃣C⃣D⃣E⃣F⃣G⃣H⃣I⃣J⃣K⃣L⃣M⃣N⃣O⃣P⃣Q⃣R⃣S⃣T⃣U⃣V⃣W⃣X⃣Y⃣Z⃣
		//₳฿₵ĐɆ₣₲ⱧłJ₭Ⱡ₥₦Ø₱QⱤ₴₮ɄV₩ӾɎⱫ₳฿₵ĐɆ₣₲ⱧłJ₭Ⱡ₥₦Ø₱QⱤ₴₮ɄV₩ӾɎⱫ
		// 🆊 🆋 🆌 🆍 🆏 
		// 🅊 🅋 🅌 🅍 🅎 🅏 
		// 🆘 🆔 🆒 🆓 🆕 🆖 🆗 🆙 🆚 🆎 🆑 ⛝ ❎
		// 🄐 🄑 🄒 🄓 🄔 🄕 🄖 🄗 🄘 🄙 🄚 🄛 🄜 🄝 🄞 🄟 🄠 🄡 🄢 🄣 🄤 🄥 🄦 🄧 🄨 🄩 
		// ⒜ ⒝ ⒞ ⒟ ⒠ ⒡ ⒢ ⒣ ⒤ ⒥ ⒦ ⒧ ⒨ ⒩ ⒪ ⒫ ⒬ ⒭ ⒮ ⒯ ⒰ ⒱ ⒲ ⒳ ⒴ ⒵ 
		// Ⓐ Ⓑ Ⓒ Ⓓ Ⓔ Ⓕ Ⓖ Ⓗ Ⓘ Ⓙ Ⓚ Ⓛ Ⓜ Ⓝ Ⓞ Ⓟ Ⓠ Ⓡ Ⓢ Ⓣ Ⓤ Ⓥ Ⓦ Ⓧ Ⓨ Ⓩ 

		//define characters
		$arrTranslate = array
		(
			'🇦' => 'a',
			'🇧' => 'b',
			'🇨' => 'c',
			'🇩' => 'd',
			'🇪' => 'e',
			'🇫' => 'f',
			'🇬' => 'g',
			'🇭' => 'h',
			'🇮' => 'i',
			'🇯' => 'j',
			'🇰' => 'k',
			'🇱' => 'l',
			'🇲' => 'm',
			'🇳' => 'n',
			'🇴' => 'o',
			'🇵' => 'p',
			'🇶' => 'q',
			'🇷' => 'r',
			'🇸' => 's',
			'🇹' => 't',
			'🇺' => 'u',
			'🇻' => 'v',
			'🇼' => 'w',
			'🇽' => 'x',
			'🇾' => 'y',
			'🇿' => 'z',
			'𝐩' => 'p',
			'𝓻' => 'r',
			'Ỗ' => 'o',
			'𝓂' => 'm',
			'Ø' => 'o',
			'𝓈' => 's',
			'Ｍ' => 'm',
		);


		//do the translation
		$arrKeys = array_keys($arrTranslate);
		$iLenTrans = count($arrTranslate);
		for ($iIndex = 0; $iIndex < $iLenTrans; ++$iIndex)
		{
			$sFiltered = str_replace($arrKeys[$iIndex], $arrTranslate[$arrKeys[$iIndex]], $sFiltered);
		}


		return $sFiltered;
	}

	/**
	 * is there a literal match between the (optionally filtered/corrected input) and the blocked words?
	 * 
	 *
	 * @param string $sInput
	 * @return int weight
	 */
	private function isBlockedListMatch($sInput)
	{
		$iWeight = 0;
		$arrKeys = array_keys($this->arrBlockedWords);

		foreach($arrKeys as $sKey)
		{		
			if (stripos($sInput, $sKey) !== false)//if exists
			{
				$iWeight+=$this->arrBlockedWords[$sKey];
				$this->arrLog[] = '-- isBlockedListMatch(): Match detected because of blocked word "'.$sKey.'" (weight: '.$this->arrBlockedWords[$sKey].'%)';
			}

			//quit detection if score is already 100 (speeds up detection process)
			if ($iWeight >= 100)
				return $iWeight;
		}

		return $iWeight;
	}	

	/**
	 * return spam score
	 * 100% is definitely spam, 0% no spam
	 * 
	 * @return float spam score in percentage. 
	 */
	public function getScore()
	{
		return $this->iScore;
	}

	/**
	 * compares spam score with threshold
	 *
	 * @param int $iSpamThreshold percentage (0-100)
	 * @return boolean
	 */
	public function isSpam($iSpamThreshold = 60)
	{
		return ($this->iScore >= $iSpamThreshold);
	}

	/**
	 * returns spam detection array log (for easier debugging)
	 *
	 * @return array
	 */
	public function getLog()
	{
		return $this->arrLog;
	}


	/**
	 * return array of words
	 *
	 * @param string $sInput
	 * @return array
	 */
	private function getWords($sInput)
	{
		$arrWords = array();

		$sInput = str_replace("\n", ' ', $sInput);//make new-line a space, so we can explode
		$sInput = str_replace("\r", '', $sInput);//remove windows cariage return for new lines
		$sInput = str_replace('.', '', $sInput);//remove .
		$sInput = str_replace(',', '', $sInput);//remove ,
		$sInput = str_replace(';', '', $sInput);//remove ;
		$sInput = str_replace('-', '', $sInput);//remove -


		$arrWords = explode(' ', $sInput);

		return $arrWords;
	}


	/**
	 * the easiest detection is just to look for 1 on 1 matches with blocked words
	 * removed because detectBlockedNonAlphabetFiltered() does the same, but more advanced
	 *
	 * @return void
	 */
	// public function detectBlockedLiteral()
	// {

	// 	$sFiltered = '';
	// 	$iWeight = 0;

	// 	$sFiltered = $this->translateChars($this->sSourceText);
	// 	$iWeight = $this->isBlockedListMatch($sFiltered);

	// 	if ($iWeight > 0)
	// 	{
	// 		$this->arrLog[] = 'detectLiteral(): Spam detected because of blocked words with a total weight of '.$iWeight. '%';
	// 		$this->increaseScore($iWeight, 40); 
	// 	}
	// }

	/**
	 * detect urls
	 *
	 * 
	 * @return void
	 */
	public function detectURLs()
	{
		//don't need detection
		if ($this->iScore == 100)
			return;
		if (strlen($this->sSourceText) == 0)
			return;
	
		//declarations and inits		
		$sFiltered = '';
		$sReplaceText = '[spam-detection-remove-url]';

		//METHOD 1: PROTOCOL DETECT = 100% spam
			$sFiltered = filterURL($this->sSourceText, $sReplaceText, true, false);
			if (stripos($sFiltered, $sReplaceText) !== false) //if exists
			{
				$this->arrLog[] = '-- detectURLs(): Spam detected because of url (method: protocol detect): add 70% spam score';
				$this->increaseScore(70); //add 70% spam chance
			}		

		//METHOD 2: TLD DETECT
			$sFiltered = filterURL($this->sSourceText, $sReplaceText, false, true);
			if (stripos($sFiltered, $sReplaceText) !== false) //if exists
			{
				$this->arrLog[] = '-- detectURLs(): Spam detected because of url (method: TLD detect): add 10% to score';
				$this->increaseScore(10,30); //add 10% spam chance 
			}
	}

	/**
	 * detect blocked words based with non-alfabetical characters filtered
	 * things like "Via`gra-" will be detected
	 *
	 * @return void
	 */
	public function detectBlocked()
	{
		//don't need detection
		if ($this->iScore == 100)
			return;
		if (strlen($this->sSourceText) == 0)
			return;
	
		//declarations and inits
		$sFiltered = '';
		$iWeight = 0;

		//filter chars on characters that visually the same 1=l 0=O
		$sFiltered = $this->translateChars($this->sSourceText);

		//filter all characters that are not alfabetical, numeric or a space
		$sFiltered = preg_replace('/[^a-zA-Z0-9 ]/', '', $sFiltered);

		//see if we can find matches in the blocked list
		$iWeight = $this->isBlockedListMatch($sFiltered);
		if ($iWeight > 0)
		{
			$this->arrLog[] = '-- detectBlocked(): Spam detected because of blocked words with a total weight of '.$iWeight. '%';
			$this->increaseScore($iWeight, 0);
		}

	}


	/**
	 * detect suggestive emojis that try users to do something like texting 
	 * a whatsapp number
	 * 
	 *
	 * @return void
	 */
	public function detectBadEmojis()
	{
		//don't need detection
		if ($this->iScore == 100)
			return;
		if (strlen($this->sSourceText) == 0)
			return;
	
		//declarations and inits
		$arrEmojis = array();
		$iCountEmojis = 0;
		$iMatches = 0;
		$fPercentageMatches = 0;

		//pointing emojis to a phone number, website, email address
		$arrEmojis[] = '👈';
		$arrEmojis[] = '👉';
		$arrEmojis[] = '☝️';
		$arrEmojis[] = '👇';
		$arrEmojis[] = '☝️';
		$arrEmojis[] = '👈🏻';
		$arrEmojis[] = '👉🏻';
		$arrEmojis[] = '👆🏻';
		$arrEmojis[] = '👇🏻';
		$arrEmojis[] = '☝🏻';
		$arrEmojis[] = '👈🏼';
		$arrEmojis[] = '👉🏼';
		$arrEmojis[] = '👆🏼';
		$arrEmojis[] = '👈🏼';
		$arrEmojis[] = '👇🏼';
		$arrEmojis[] = '👈🏽';
		$arrEmojis[] = '👉🏽';
		$arrEmojis[] = '👆🏽';
		$arrEmojis[] = '👇🏽';
		$arrEmojis[] = '☝🏽';
		$arrEmojis[] = '👈🏾';
		$arrEmojis[] = '👉🏾';
		$arrEmojis[] = '👆🏾';
		$arrEmojis[] = '👇🏾';
		$arrEmojis[] = '☝🏾';
		$arrEmojis[] = '👈🏿';
		$arrEmojis[] = '👉🏿';
		$arrEmojis[] = '👆🏿';
		$arrEmojis[] = '👇🏿';
		$arrEmojis[] = '☝🏿';
		//arrow emojis pointing at an email address, phone number etc
		$arrEmojis[] = '→';
		$arrEmojis[] = '⇒';
		$arrEmojis[] = '⟹';
		$arrEmojis[] = '⇨';
		$arrEmojis[] = '⇾';
		$arrEmojis[] = '➾';
		$arrEmojis[] = '⇢';
		$arrEmojis[] = '☛';
		$arrEmojis[] = '☞';
		$arrEmojis[] = '➔';
		$arrEmojis[] = '➜';
		$arrEmojis[] = '➙';
		$arrEmojis[] = '➛';
		$arrEmojis[] = '➝';
		$arrEmojis[] = '➞';
		$arrEmojis[] = '⬆';
		$arrEmojis[] = '↗';
		$arrEmojis[] = '➡';
		$arrEmojis[] = '↘';
		$arrEmojis[] = '⬇';
		$arrEmojis[] = '↙';
		$arrEmojis[] = '⬅';
		$arrEmojis[] = '↖';
		$arrEmojis[] = '↕';
		$arrEmojis[] = '↔';
		$arrEmojis[] = '↩';
		$arrEmojis[] = '↪';
		$arrEmojis[] = '⤴';
		$arrEmojis[] = '⤵';
		$arrEmojis[] = '▶';
		$arrEmojis[] = '◀';
		$arrEmojis[] = '🔼';
		$arrEmojis[] = '🔽';
		$arrEmojis[] = '⏫';
		$arrEmojis[] = '⏬';
		$arrEmojis[] = '⏪';
		$arrEmojis[] = '⏩';
		$arrEmojis[] = '⏏';
		$arrEmojis[] = '🆗';
		$arrEmojis[] = '🆙';
		$arrEmojis[] = '🆕';
		$arrEmojis[] = '🆓';
		$arrEmojis[] = '🔝';
		$arrEmojis[] = '🔛';
		$arrEmojis[] = '🔙';
		$arrEmojis[] = '🔚';
		//emojis for email
		$arrEmojis[] = '✉️';
		$arrEmojis[] = '✉';
		$arrEmojis[] = '📩';
		$arrEmojis[] = '📨';
		$arrEmojis[] = '📧';
		$arrEmojis[] = '📪';
		$arrEmojis[] = '📫';
		$arrEmojis[] = '📬';
		$arrEmojis[] = '📭';
		//misc
		$arrEmojis[] = '💲';
		$arrEmojis[] = '✔️';
		$arrEmojis[] = '✅';
		$arrEmojis[] = '💱';
		$arrEmojis[] = '🏆'; //money, prize, winner
		$arrEmojis[] = '🥇'; //money, prize, winner
		$arrEmojis[] = '🏅'; //money, prize, winner
		$arrEmojis[] = '🎖'; //money, prize, winner
		$arrEmojis[] = '🤑'; //money, prize, winner
		$arrEmojis[] = '🎁 '; //money, prize, winner, gift
		$arrEmojis[] = '📦';//money, prize, winner, gift
		// $arrEmojis[] = '💱';
		// $arrEmojis[] = '💱';
		// $arrEmojis[] = '💱';

		
		//count number of emojis in source text
		$iCountEmojis = count($arrEmojis);
		for ($iIndex = 0; $iIndex < $iCountEmojis; ++$iIndex)
		{
			$iMatches += substr_count($this->sSourceText, $arrEmojis[$iIndex]);
		}
		
		if ($iMatches > 0)
		{
			//look at percentage of emojis used
			$fPercentageMatches = round(($iMatches / strlen($this->sSourceText) * 100 ), 4); //matches / totallengthtext * 100
			// if ($fPercentageMatches > 3)
			{
				$this->arrLog[] = '-- detectBadEmojis(): Spam detected because of % of emojis compared to textlength is '.$fPercentageMatches.'%. added ('.$fPercentageMatches.' * 4)% weight';			
				$this->increaseScore(($fPercentageMatches * 5), 60);
			}

			//always increase based on amount of emojis used
			$this->arrLog[] = '-- detectBadEmojis(): Spam detected because of emojis. '.$iMatches.' emojis found. Weight 10% * matches = '.($iMatches * 10). '%';
			$this->increaseScore($iMatches * 10, 50);

		} 


		//dangerous unicode characters
		//right-to-left override character
		if (strrpos($this->sSourceText, $this->sDangerousCharacterRLO) !== false)
		{
			$this->arrLog[] = '-- detectBadEmojis(): Spam detected because of Right-to-left-override character.';
			$this->increaseScore(100);
		}
	
	}


	/**
	 * detects numbers, which are often phone numbers for calls or whatsapp
	 *
	 * @return void
	 */
	public function detectNumbers()
	{
		//don't need detection
		if ($this->iScore == 100)
			return;
		if (strlen($this->sSourceText) == 0)
			return;

		//declarations and inits
		$sTrimmedWord = '';
		$arrWords = array();
		$sFiltered = $this->sSourceText;
		
		//'translate' emoticons to digits
		$sFiltered = str_replace('0️⃣', '0', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('1️⃣', '1', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('2️⃣', '2', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('3️⃣', '3', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('4️⃣', '4', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('5️⃣', '5', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('6️⃣', '6', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('7️⃣', '7', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('8️⃣', '8', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('9️⃣', '9', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('🔟', '10', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('🎱', '8', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⓪', '0', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('①', '1', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('②', '2', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('③', '3', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('④', '4', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑤', '5', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑤', '6', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑦', '7', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑧', '8', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑨', '9', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑩', '10', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑪', '11', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑫', '12', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑬', '13', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑭', '14', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑮', '15', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑯', '16', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑰', '17', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑱', '18', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑲', '19', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('⑳', '20', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉑', '21', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉒', '22', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉓', '23', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉔', '24', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉕', '25', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉖', '26', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉗', '27', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉘', '28', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉙', '29', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉚', '30', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉛', '31', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉜', '32', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉝', '33', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉞', '34', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㉟', '35', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊱', '36', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊲', '37', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊳', '38', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊴', '39', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊵', '40', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊶', '41', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊷', '42', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊸', '43', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊹', '44', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊺', '45', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊻', '46', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊼', '47', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊽', '48', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊾', '49', $sFiltered);//remove numbered emojis
		$sFiltered = str_replace('㊿', '50', $sFiltered);//remove numbered emojis
		

		//white circled numbers
		$sVisual = array('🄋', '➀', '➁', '➂', '➃', '➄', '➅', '➆' ,'➇', '➈', '➉');
		$sReal   = array('0', '1',  '2',  '3' , '4', '5', '6',  '7', '8', '9', '10');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		//black circled numbers
		$sVisual = array('⓿', '❶', '❷', '❸',  '❹', '❺', '❻', '❼' ,'❽', '❾', '❿',  '⓫',  '⓬',  '⓭',  '⓮',  '⓯',  '⓰',  '⓱',  '⓲',  '⓳',  '⓴');
		$sReal   = array('0', '1',  '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		
		//black circled numbers sans serif
		$sVisual = array('🄌', '➊', '➋', '➌', '➍', '➎', '➏', '➐' ,'➑', '➒', '➓');
		$sReal   = array('0', '1',  '2',  '3' , '4', '5', '6',  '7', '8', '9', '10');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  


		//Circled Numbers on Black Square
		$sVisual = array('㉈',  '㉉', '㉊',  '㉋', '㉌', '㉍', '㉎', '㉏');
		$sReal   = array('10', '20', '30', '40', '50', '60', '70', '80');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		//Double Circled Number
		$sVisual = array('⓵', '⓶', '⓷', '⓸', '⓹', '⓺', '⓻' ,'⓼', '⓽', '⓾');
		$sReal   = array('1',  '2',  '3' , '4', '5',  '6',  '7',  '8',  '9', '10');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  


		//numbers with period
		$sVisual = array('🄀', '⒈', '⒉', '⒊', '⒋', '⒌', '⒍', '⒎' ,'⒏', '⒐', '⒑',  '⒒',  '⒓',  '⒔',  '⒕',  '⒖',  '⒗', '⒘', '⒙',  '⒚', '⒛');
		$sReal   = array('0', '1',  '2', '3',  '4', '5',  '6',  '7', '8',  '9', '10', '11', '12',  '13',  '14',  '15', '16', '17', '18', '19', '20');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		
		//Parenthesized
		$sVisual = array('⑴', '⑵', '⑶', '⑷', '⑸', '⑹', ' ⑺', '⑻' ,'⑼', '⑽', '⑾', '⑿',  '⒀',  '⒁',  '⒂',  '⒃', '⒄', '⒅', '⒆', '⒇');
		$sReal   = array('1',  '2',  '3',  '4', '5',  '6',  '7',  '8',  '9',  '10', '11', '12',  '13',  '14',  '15', '16', '17', '18', '19', '20');
		$sFiltered = str_replace($sVisual, $sReal, $sFiltered);	  

		// ㊀ ㊁ ㊂ ㊃ ㊄ ㊅ ㊆ ㊇ ㊈ ㊉  korean/jap/chinese


		//check if individual words are numeric
		$arrWords = $this->getWords($this->sSourceText);
		foreach ($arrWords as $sWord)
		{
			$sTrimmedWord = trimAll($sWord);
			
			if (strlen($sTrimmedWord) > 5) //only longer digits: phone numbers are generally larger than 5 characters. Because numbers can occur in a very legit texts
			{
				if (is_numeric($sTrimmedWord))
				{
					$this->arrLog[] = '-- detectNumbers(): Spam detected because of number '.$sTrimmedWord.'. add score 10%';
					$this->increaseScore(10,40);
				}
			}
		}
	}

	/**
	 * detects abuse of punctiation marks and other not alfabetical characters (like /////// or ------)
	 * 
	 * 
	 * 
	 * 
 	* ,.....====== thank you very much for uploading  this video .=======..

                                      \\\\\\\  thumbs up       \\\\\\\\\ 

                     ,.im sharing this video to my community...............
	 *
	 * @return void
	 */
	public function detectPunctuation()
	{
		//don't need detection
		if ($this->iScore == 100)
			return;
		if (strlen($this->sSourceText) == 0)
			return;
	
		//declarations and inits
		$sFiltered = $this->sSourceText;
		$fPercentagePunct = 0.0;
		$fPercentageExcl = 0.0;


		//METHOD 1: COUNTING EXCLAMATION MARKS (!)

			$fPercentageExcl = round((substr_count($sFiltered, '!') / strlen($this->sSourceText) * 100 ), 4); //matches / totallengthtext * 100
			if ($fPercentageExcl > 1.0)
			{
				$this->arrLog[] = '-- detectPunctiation(): Spam detected because of '.$fPercentageExcl.'% of exclamation-marks used compared to textlength ('.strlen($this->sSourceText).'). added '.$fPercentageExcl.'*10% weight';			
				$this->increaseScore($fPercentageExcl*10,0);
			}

		//METHOD 2: PERCENTAGE OF NON-ALPHABETICAL

			//filter all characters that are not alfabetical, numeric or a space
			$sFiltered = preg_replace('/[a-zA-Z0-9 ]/', '', $sFiltered); //only non alphabet characters
			$fPercentagePunct = round((strlen($sFiltered) / strlen($this->sSourceText) * 100 ), 4); //matches / totallengthtext * 100

			//more than 4% punctuation is probably spam
			if ($fPercentagePunct > 6.5)
			{
				$this->arrLog[] = '-- detectPunctuation(): Spam detected because of '.$fPercentagePunct.'% of punction-marks used compared to textlength ('.strlen($this->sSourceText).'). added '.$fPercentagePunct.'*2% weight';			
				$this->increaseScore($fPercentagePunct*2,50);
			}


	}

	/**
	 * detect the overusage of uppercase characters (nl: hoofdletters)
	 * 
	 *
	 * @return void
	 */
	public function detectCAPITALS()
	{
		//don't need detection
		if ($this->iScore == 100)
			return;
		if (strlen($this->sSourceText) == 0)
			return;
	
		//declarations and inits		
		$sFiltered = $this->sSourceText;
		$fPercentageCaps = 0;
		$arrWords = array();
		$iCountWords = 0;
		$iUpcaseWords = 0;

		//===METHOD 1: PERCENTAGE CHARS CAPITALS

			//filter all characters that are not alfabetical, numeric or a space
			$sFiltered = preg_replace('/[^A-Z]/', '', $sFiltered); //only non alphabet characters
			$fPercentageCaps = round((strlen($sFiltered) / strlen($this->sSourceText) * 100 ), 4); //matches / totallengthtext * 100

			//more than 4% UPPERCASE chars is probably spam
			if ($fPercentageCaps > 4.5)
			{
				$this->arrLog[] = '-- detectCapitals(): Spam detected because of '.$fPercentageCaps.'% of uppercase characters used compared to textlength ('.strlen($this->sSourceText).'). added '.$fPercentageCaps.'*10% weight';			
				$this->increaseScore($fPercentageCaps*10,30);
			}

		//====METHOD 2: PERCENTAGE WORDS IN CAPITALS

			$arrWords = $this->getWords($this->sSourceText);
			$iCountWords = count($arrWords);


			//detecting number of words in all-caps only
			foreach ($arrWords as $sWord)
			{
				if (ctype_upper($sWord))
					++$iUpcaseWords;
			}
			$fPercentageCaps = round(($iUpcaseWords / $iCountWords * 100 ), 4); 

			//more than 4% UPPERCASE words is probably spam
			if ($fPercentageCaps > 4.5)
			{
				$this->arrLog[] = '-- detectCapitals(): Spam detected because of '.$fPercentageCaps.'% of uppercase words used compared to total amount of words ('.$iCountWords .'). added '.$fPercentageCaps.'*10% weight';			
				$this->increaseScore($fPercentageCaps*10,30);
			}

	}

	/**
	 * detect if the characters used are non-latin
	 * to prevent for example cyrilic unicode characters looking like latin characters
	 * which can lead to a IDN homograph attack
	 * 
	 * @param bool $bIncreaseScoreByRatio true=the ratio of wrong chacters is added as score; false=1 detected = 100% spam
	 */
	public function detectNonLatinCharacterSet($bIncreaseScoreByRatio = false)
	{
		$sFiltered = '';
		$iOrgLen = 0;
		$iFilteredLen = 0;
		$iDiffOrgFilteredLen = 0;
		$iPercBadChars = 0;

		//don't need detection
		if ($this->iScore == 100)
			return;		

		$sFiltered = preg_replace( '/[^'.REGEX_TEXT_NORMAL.']/', '', $this->sSourceText);
		$iOrgLen = strlen($this->sSourceText);
		$iFilteredLen = strlen($sFiltered);
		$iDiffOrgFilteredLen = $iOrgLen - $iFilteredLen;

		if ($bIncreaseScoreByRatio)
		{
			if ($iDiffOrgFilteredLen > 0) //prevent division by zero
			{
				$iPercBadChars = (($iOrgLen / $iDiffOrgFilteredLen) * 100);
				$this->arrLog[] = '-- detectNonLatinCharacterSet(): Spam detected because of '.$iDiffOrgFilteredLen.' non latin characters (out of '.$iOrgLen.'). Added '.$iPercBadChars.'% to score';
				$this->increaseScore($iPercBadChars, 0);
			}
		}
		else //1 detected = 100% spam
		{
			if ($iDiffOrgFilteredLen > 0)
			{
				$this->arrLog[] = '-- detectNonLatinCharacterSet(): Spam detected because of '.$iDiffOrgFilteredLen.' non latin characters (out of '.$iOrgLen.'). added 100% to score (1 bad char = 100%)';
				$this->increaseScore(100,0);
			}
		}
	}

}
