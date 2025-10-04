<?php
namespace dr\classes\dom\tag\form;

/**
 * parent class for input types: text, password, checkbox en radio
 * <input type="x">
 * the contents if X in the example above depends on child class
 * 
 * 23 jun 2021: InputAbstract: setValueSubmitted() added
 */
abstract class InputAbstract extends FormInputAbstract
{
	private $sType = '';
	private $iSize = '';
	private $iMaxLength = '';
	private $sInitValue = '';

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setTagName('input');

		$this->setSourceFormattingIdentForCloseTag(false);
		$this->setSourceFormattingNewLineAfterOpenTag(false);
		$this->setSourceFormattingNewLineAfterCloseTag(false);
		$this->setSourceFormattingIdentForOpenTag(false);
	}

	/**
	 * setting input type
	 * @param string $sType
	 */
	public function setType($sType)
	{
		$this->sType = $sType;
	}

	/**
	 * get input type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->sType;
	}

	/**
	 * setting input value
	 * @param string $sValue
	 */
	public function setValue($sValue)
	{
		$this->sInitValue = $sValue;
	}


	/**
	 * same as setValue(), but it takes the input of the submitted $_GET or $_POST
	 * 
	 * @param $sFormMethod read the $_POST or $_GET array? use constant Form::METHOD_POST
	 * @return void
	 */
	public function setValueSubmitted($sFormMethod = Form::METHOD_POST)
	{
		$this->setValue($this->getValueSubmitted($sFormMethod));
	}


	/**
	 * get input value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->sInitValue;
	}
	
	/**
	 * setting size of box in chars (maxlength is something else)
	 * @param int $iValue
	 */
	public function setSize($iValue)
	{
		$this->iSize = $iValue;
	}
	
	/**
	 * get size of box in chars (maxlength is something else)
	 *
	 * @return int
	 */
	public function getSize()
	{
		return $this->iSize;
	}	
	
	/**
	 * setting size (length of input)
	 * @param int $iValue
	 */
	public function setMaxLength($iValue)
	{
		$this->iMaxLength = $iValue;
	}
	
	/**
	 * get maxlength
	 *
	 * @return int
	 */
	public function getMaxLength()
	{
		return $this->iMaxLength;
	}

	

	protected function renderChild()
	{
		$sAttributes = parent::renderChild();

		$sAttributes .= $this->addAttributeToHTML('type', $this->getType());
		$sAttributes .= $this->addAttributeToHTML('value', $this->getValue());
		$sAttributes .= $this->addAttributeToHTML('size', $this->getSize());
		$sAttributes .= $this->addAttributeToHTML('maxlength', $this->getMaxLength());

		$sAttributes .= $this->renderHTMLNodeSpecificToInputType();

		return $sAttributes;
	}

	abstract public function renderHTMLNodeSpecificToInputType();

}

?>