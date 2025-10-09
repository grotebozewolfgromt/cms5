<?php
namespace dr\classes\dom\tag;

/**
 * klasse voor alle (platte) tekstuele zaken
 * LET OP: De klasse is anders dan andere!
 *
 *
 */
class Text extends HTMLTag
{
	private $sText = '';

	/**
	 * setting the text for the textnode
	 * (all special characters are converted to html characters)
	 *
	 * @param string $sText
	 * @param string $bConvertToHTMLSpecialChars = true it will be converted, false it will be literally the text as you supplied
	 */
	public function setText($sText, $bConvertToHTMLSpecialChars = true)
	{
		if ($bConvertToHTMLSpecialChars)
			$sText = htmlspecialchars($sText);
		$this->sText = $sText;
	}

	/**
	 * setting the text for the textnode literally
	 * (no special character conversion)
	 *
	 * @param string $sText
	 */
	public function setTextUnsafe($sText)
	{
		$this->sText = $sText;
	}

	/**
	 * getting the text for the text node
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->sText;
	}


	public function renderHTMLNode($iLevel = 0)
	{
		return $this->getText();
	}
}


?>