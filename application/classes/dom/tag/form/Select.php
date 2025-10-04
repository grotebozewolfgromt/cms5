<?php
namespace dr\classes\dom\tag\form;

use dr\classes\dom\tag\form\Option;

/**
 * de select tag
 *
 * <select>
 *  <option>optie1</optio>
 *  <option>optie2</option>
 * </select>
 *
 *
 *
 * code example :
 *
 *  $objSelect = new select();
 $objSelect->setName('edtSelect');
 $objOption = new option();
 $objOption->setText('hallo meneer1');
 $objOption->setValue('1');
 $objSelect->appendChild($objOption);
 $objOption = new option();
 $objOption->setText('hallo meneer2');
 $objOption->setValue('2');
 $objSelect->appendChild($objOption);
 $objForm->appendChild($objSelect);
 *
 * of het volgende is sneller:
 *
 * example normal:
 *     $objSelect = new select();
 $objSelect->setName('edtSelect');
 $objOption = new option();
 $objOption->setText('hallo meneer1');
 $objOption->setValue('1');
 $objSelect->appendChild($objOption);
 $objOption = new option();
 $objOption->setText('hallo meneer2');
 $objOption->setValue('2');
 $objSelect->appendChild($objOption);
 $objForm->appendChild($objSelect);
 *
 * example short:
 *
 *  $objSelect = new select();
 $objSelect->setName('edtSelect2');
 $objSelect->addOption(1, 'hallo meneer1');
 $objSelect->addOption(2, 'hallo meneer2');
 $objForm->appendChild($objSelect);
 * 
 * 9 jan 2020: Select(): setSelectedOption() added
 * 27 nov 2021: Select(): setSelectedOption() returns boolean if found or not +
 * 27 nov 2021: Select(): setSelectedOption(): speed increase: if found, loop is terminated
 * 14 nov 2022: Select(): added: setValue($sValue) wrapper for setSelectedOption($sHTMLOptionValue)
 * 23 apr 2024: Select(): added: generateFromArray()
 * 23 apr 2024: Select(): added: generateDaysOfWeek()
 * 23 apr 2024: Select(): added: generateMonths()
 * 23 apr 2024: Select(): added: extra parameter for addOption() to select the option
 * 24 apr 2024: Select(): addOption() parameters switched
 * 24 apr 2024: Select(): fix: generateDaysOfTheWeek() extra parameter for selected day
 * 24 apr 2024: Select(): fix: generateMonths() extra parameter for selected month
 */

class Select extends FormInputAbstract
{


	public function __construct($bIsArray = false, $objParentNode = null)
	{
		parent::__construct($bIsArray, $objParentNode);
		$this->setTagName('select');

        $this->setSourceFormattingIdentForOpenTag(true);
        $this->setSourceFormattingIdentForCloseTag(true);
        $this->setSourceFormattingNewLineAfterOpenTag(true);
        $this->setSourceFormattingNewLineAfterCloseTag(true);
    }


    public function clear()
    {
        $this->removeAllChildNodes();
    }

    /**
     * Add <option> child for <select> combobox
     * 
     * @param string $sValue the value you get when requesting with $_GET[] or $_POST[]
     * @param string $sText the visible part in the combobox
     * @param bool $bIsSelectedValue is this <option> marked as selected?
     * @param bool $bDisabled is this <option> marked as selected?
     */
	public function addOption($sValue, $sText, $bIsSelectedValue = false, $bDisabled = false)
	{
		$objOption = new Option();
		$objOption->setText($sText);
		$objOption->setValue($sValue);
		$objOption->setDisabled($bDisabled);    
        $objOption->setSelected($bIsSelectedValue);
		$this->appendChild($objOption);
	}

    /**
     * converts an array into a <select>-box with <OPTION> childs
     * 
     * 
     * @param array $arrInput 1d array with format: array("lorem", "ipsum", "dolor")
     * @param bool $bOptionValueIsArrayIndex if true: <option value="0">lorem</option>, if false: <option value="lorem">lorem</option>
     * @param mixed $mSelectedValue which option is selected? $bOptionValueIsArrayIndex == true this is compared against array index, $bOptionValueIsArrayIndex == false this is compared against the string value
     */
    public function generateFromArray($arrInput, $bOptionValueIsArrayIndex, $mSelectedValue = '')
    {
        $iSizeArray = 0;
        $iSizeArray = count($arrInput);

        for ($iCounter = 0; $iCounter < $iSizeArray; ++$iCounter)
        {

            //=== determine selected value
            $bIsSelectedValue = false;
            if ($mSelectedValue != '')
            {
                if ($mSelectedValue)
                {
                    if ($bOptionValueIsArrayIndex) //compare against array index
                        $bIsSelectedValue = ($iCounter == $mSelectedValue);
                    else //compare against string value
                        $bIsSelectedValue = ($arrInput[$iCounter] == $mSelectedValue);
                }
            }

            //=== add to <select>
            if ($bOptionValueIsArrayIndex)
                $this->addOption($iCounter, $arrInput[$iCounter], $bIsSelectedValue);
            else
                $this->addOption($arrInput[$iCounter], $arrInput[$iCounter], $bIsSelectedValue);
        }
    }

    /**
     * Generate a language aware <select> with all days of the week.
     * 
     * <option value=1>monday</option>
     * <option value=2>tuesday</option>
     * <option value=0>sunday</option>^
     * 
     * @param int $iSelectedDay 0=sunday, 1=tuesday, ... 6=sunday
     */
    public function generateDaysOfTheWeek($iSelectedDay = 0)
    {
        $arrDays = array();

        //we first put days in array so we can offset the <option value=X>
        $arrDays[] = transg(TRANS_WEEKDAY_SUNDAY_FULL_KEY, TRANS_WEEKDAY_SUNDAY_FULL_VALUE);        
        $arrDays[] = transg(TRANS_WEEKDAY_MONDAY_FULL_KEY, TRANS_WEEKDAY_MONDAY_FULL_VALUE);
        $arrDays[] = transg(TRANS_WEEKDAY_TUESDAY_FULL_KEY, TRANS_WEEKDAY_TUESDAY_FULL_VALUE);
        $arrDays[] = transg(TRANS_WEEKDAY_WEDNESDAY_FULL_KEY, TRANS_WEEKDAY_WEDNESDAY_FULL_VALUE);
        $arrDays[] = transg(TRANS_WEEKDAY_THURSDAY_FULL_KEY, TRANS_WEEKDAY_THURSDAY_FULL_VALUE);
        $arrDays[] = transg(TRANS_WEEKDAY_FRIDAY_FULL_KEY, TRANS_WEEKDAY_FRIDAY_FULL_VALUE);
        $arrDays[] = transg(TRANS_WEEKDAY_SATURDAY_FULL_KEY, TRANS_WEEKDAY_SATURDAY_FULL_VALUE);

        for ($iIndex = 0; $iIndex < 7; ++$iIndex)
        {
            $this->addOption($iIndex, $arrDays[$iIndex], ($iIndex == $iSelectedDay));
        }        
    }


    /**
     * Generate a language aware <select> with all months in a year.
     * 
     * <option value=1>january</option>
     * <option value=2>february</option>
     * <option value=12>december</option>
     * 
     * @param int $iSelectedMonth which month is selected? 1=january, 12=december
     */
    public function generateMonths($iSelectedMonth = 0)
    {
        $this->addOption(1, transg(TRANS_MONTH_JANUARY_FULL_KEY, TRANS_MONTH_JANUARY_FULL_VALUE),     ($iSelectedMonth == 1));
        $this->addOption(2, transg(TRANS_MONTH_FEBRUARY_FULL_KEY, TRANS_MONTH_FEBRUARY_FULL_VALUE),   ($iSelectedMonth == 2));
        $this->addOption(3, transg(TRANS_MONTH_MARCH_FULL_KEY, TRANS_MONTH_MARCH_FULL_VALUE),         ($iSelectedMonth == 3));
        $this->addOption(4, transg(TRANS_MONTH_APRIL_FULL_KEY, TRANS_MONTH_APRIL_FULL_VALUE),         ($iSelectedMonth == 4));
        $this->addOption(5, transg(TRANS_MONTH_MAY_FULL_KEY, TRANS_MONTH_MAY_FULL_VALUE),             ($iSelectedMonth == 5));
        $this->addOption(6, transg(TRANS_MONTH_JUNE_FULL_KEY, TRANS_MONTH_JUNE_FULL_VALUE),           ($iSelectedMonth == 6));
        $this->addOption(7, transg(TRANS_MONTH_JULY_FULL_KEY, TRANS_MONTH_JULY_FULL_VALUE),           ($iSelectedMonth == 7));
        $this->addOption(8, transg(TRANS_MONTH_AUGUST_FULL_KEY, TRANS_MONTH_AUGUST_FULL_VALUE),       ($iSelectedMonth == 8));
        $this->addOption(9, transg(TRANS_MONTH_SEPTEMBER_FULL_KEY, TRANS_MONTH_SEPTEMBER_FULL_VALUE), ($iSelectedMonth == 9));
        $this->addOption(10, transg(TRANS_MONTH_OCTOBER_FULL_KEY, TRANS_MONTH_OCTOBER_FULL_VALUE),    ($iSelectedMonth == 10));
        $this->addOption(11, transg(TRANS_MONTH_NOVEMBER_FULL_KEY, TRANS_MONTH_NOVEMBER_FULL_VALUE),  ($iSelectedMonth == 11));
        $this->addOption(12, transg(TRANS_MONTH_DECEMBER_FULL_KEY, TRANS_MONTH_DECEMBER_FULL_VALUE),  ($iSelectedMonth == 12));
    }    

    /**
     * set Option() with $sHTMLOptionValue as selected="true"
     * It looks at child nodes and sets the child node as selected that 
     * has the value $sHTMLOptionValue assigned
     * 
     * FROR EXAMPLE
     * in this scenario:
     * <select name="selFirstName">
     *  <option value="1">john</optio>
     *  <option value="2">harry</option>
     *  <option value="3">mary</option>
     *  <option value="4">stefany</option>
     * </select>
     * 
     * when calling $objSelect->setSelectedOption(3)
     * this function will set Mary as selected
     * <select name="selFirstName">
     *  <option value="1">john</optio>
     *  <option value="2">harry</option>
     *  <option value="3" selected="true">mary</option>
     *  <option value="4">stefany</option>
     * </select>
     * 
     * 
     * @param string $sHTMLOptionValue
     * @param bool found yes or no
     */
    public function setSelectedOption($sHTMLOptionValue)
    {
        $objNode = null;
        $iCountNodes = 0;
        $iCountNodes = $this->countChildNodes();
    
        //going through all nodes in search of the Option node with value == $sHTMLOptionValue
        for ($iIndex = 0; $iIndex < $iCountNodes; $iIndex++) 
        {
            $objNode = $this->getChildNode($iIndex);
            if ($objNode instanceof Option)
            {
                //found node with right value?
                if ($objNode->getValue() == $sHTMLOptionValue)
                {
                    $objNode->setSelected(true); 
                    return true;                          
                }
                else //else don't select
                {
                    $objNode->setSelected(false);
                }
            }
        }

        return false;
    }


    /**
     * wrapper for setSelectedOption() to be consistent with all the other html elements
     */
    public function setValue($sValue)
    {
        return $this->setSelectedOption($sValue);
    }

  
}


?>