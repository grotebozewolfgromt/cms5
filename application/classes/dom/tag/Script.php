<?php
namespace dr\classes\dom\tag;

use dr\classes\dom\tag\Text;

class Script extends HTMLTag
{
	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('script');

		//you probably wanna have some text in the i tag
		//so we create that automatically for you:
		$objText = new Text();
		$this->appendChild($objText);
	}

        /**
         * set text of script
         * @param string $sScript
         */
	public function setText($sScript)
	{
		$this->getChildNode(0)->setText($sScript, false);
	}
        
        /**
         * alias for setText
         * @param string $sScript
         */
	public function setScript($sScript)
	{
		$this->getChildNode(0)->setText($sScript, false);
	}        

	public function getText()
	{
		return $this->getChildNode(0)->getText();
	}

        /**
         * alias for getText()
         * 
         * @return string
         */
        public function getScript()
        {
                return $this->getChildNode(0)->getText();
        }
        

}

?>
