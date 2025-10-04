<?php
namespace dr\classes\dom\tag\form;

/**
 * <input type="button">
 *
 *     $objSubmit = new InputButton();
 $objSubmit->setName('btnSubmit');
 $objSubmit->setValue('verstuur de hele rommel');
 $objForm->appendChild($objSubmit);
 */
class InputButton extends InputAbstract
{
	private $sDataSitekey = ''; //used by google recaptcha
	private $sDataCallback = ''; //used by google recaptcha
	private $sDataAction = ''; //used by google recaptcha

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray);
		$this->setType('button');
	}



	public function renderHTMLNodeSpecificToInputType()
	{
		$sAttributes = '';

		//data-sitekey toevoegen
		if ($this->getDataSitekey() != '')
		{
			$sAttributes .= $this->addAttributeToHTML('data-sitekey', $this->getDataSitekey());
		}

		//data-callback toevoegen
		if ($this->getDataCallback() != '')
		{
			$sAttributes .= $this->addAttributeToHTML('data-callback', $this->getDataCallback());
		}

		//data-action toevoegen
		if ($this->getDataAction() != '')
		{
			$sAttributes .= $this->addAttributeToHTML('data-action', $this->getDataAction());
		}


		return $sAttributes;
	}

	/**
	 * sets data-sitekey tag 
	 * (used by google recaptcha)
	 *
	 * @param string $sSitekey
	 * @return void
	 */
	public function setDataSitekey($sSitekey)
	{
		$this->sDataSitekey = $sSitekey;
	}


	/**
	 * gets data-sitekey tag
	 * (used by google recaptcha)
	 *
	 * @return string
	 */
	public function getDataSitekey()
	{
		return $this->sDataSitekey;
	}


	/**
	 * sets data-callback tag 
	 * (used by google recaptcha)
	 *
	 * @param string $sCallback
	 * @return void
	 */
	public function setDataCallback($sCallback)
	{
		$this->sDataCallback = $sCallback;
	}


	/**
	 * gets data-callback tag
	 * (used by google recaptcha)
	 *
	 * @return string
	 */
	public function getDataCallback()
	{
		return $this->sDataCallback;
	}
	
	/**
	 * sets data-action tag 
	 * (used by google recaptcha)
	 *
	 * @param string $sAction
	 * @return void
	 */
	public function setDataAction($sAction)
	{
		$this->sDataAction = $sAction;
	}


	/**
	 * gets data-action tag
	 * (used by google recaptcha)
	 *
	 * @return string
	 */
	public function getDataAction()
	{
		return $this->sDataAction;
	}	


}
?>