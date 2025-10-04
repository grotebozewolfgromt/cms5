<?php
namespace dr\classes\dom\tag\form;

class InputNumber extends InputText
{
	private $iMin;
	private $iMax;

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode = null);
		$this->setType('number');
	}

	public function getMin()
	{
		return $this->iMin;
	}

	public function setMin($iMin)
	{
		$this->iMin = $iMin;
	}

	public function getMax()
	{
		return $this->iMax;
	}

	public function setMax($iMax)
	{
		$this->iMax = $iMax;
	}

	public function renderHTMLNodeSpecificToInputType()
	{
		$sAttributes = '';
		 
		$sAttributes .= $this->addAttributeToHTML('min', $this->getMin());
		$sAttributes .= $this->addAttributeToHTML('max', $this->getMax());

		return $sAttributes;
	}

}



?>