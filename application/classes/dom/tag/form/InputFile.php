<?php
namespace dr\classes\dom\tag\form;

/**
 * <input type="file">
 *
 * LET OP: set the enctype of the form object to be able to upload files
 * 
 * 28 oct 2020: InputFile added 'multiple'
 */
class InputFile extends InputAbstract
{
	private $bMultiple = false;

	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setType('file');
	}


	public function renderHTMLNodeSpecificToInputType()
	{
		$sAttributes = '';

		if ( $this->getMultiple() )
			$sAttributes .= $this->addAttributeToHTML('multiple', 'yes');

		return $sAttributes;
		 
	}	

	/**
	 * set 'multiple' for files (so you can select multiple files instead of one)
	 *
	 * @param bool $bMultiple
	 */
	public function setMultiple($bMultiple)
	{
		 
		if (is_bool($bMultiple))
		{

			$this->bMultiple = $bMultiple;

		}
	}

	/**
	 * get 'multiple' for files (so you can select multiple files instead of one)
	 *
	 * @return bool
	 */
	public function getMultiple()
	{
		return $this->bMultiple;
	}	
}
?>