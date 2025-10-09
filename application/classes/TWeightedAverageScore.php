<?php
namespace dr\classes;


/**
 * Description of TWeightedAverageScore
 *
 * With this class you can calculate an average score that is weighted
 * 
 * 
 * example 1:
 * ->addScore(100, 1) //score 100 with weighting factor 1
 * ->addScore(50,1) //score 50 with weighting factor 1
 * ->calculateScore() //will return 75
 * 
 * example 2:
 * ->addScore(100, 1) //score 100 with weighting factor 1
 * ->addScore(50,1) //score 50 with weighting factor 1
 * ->addScore(0,1) //score 0 with weighting factor 1
 * ->calculateScore() //will return 50
 * 
 * example 3:
 * $objScore->addScore(200, 1);
 * $objScore->addScore(100, 2);
 * $objScore->addScore(0, 1);
 * ->calculateScore() //will return 100
 * 
 * example 4:
 * $objScore->addScore(100, 2); //2 parts 100
 * $objScore->addScore(50, 1); //1 part 50 
 * vardump($objScore->calculateScore()); //will return 83.3 (2*100 + 1*50 = 250 / 3 = 83.3)
 * 
 * 
 * application example 1:
 * you want to calculate the spam-likelyhood of text,
 * based on different factors like literal string matches (factor 10), character frequencies (factor 2) etc
 * 
 * 
 * application example 2:
 * for a keyword research tool, you can say that a keyword is given
 * a score(=percentage) but certain percentages weigh heavier than others
 * The subscribers for a channel has weighting factor 1
 * but the amount of views on a channel has weighting factor 10
 * 
 * 14 mrt 2022: TWeightedAverageScore: created
 * 18 mrt 2022: TWeightedAverageScore: clear() added
 * 18 mrt 2022: TWeightedAverageScore: calculateScore() updated: prevent division by 0
* 
 * @author dennis renirie
 */

class TWeightedAverageScore
{
	const ARRKEYINDEX_SCORE = 0;//array key index
	const ARRKEYINDEX_WEIGHT = 1;

	private $arrScores = array(); //2d array, on every item an array with
	private $fTotalWeight = 0.0;//float that calculates the total weight, that is also the division factor when calculating the total

	public function __construct()
	{

	}

	/**
	 * reset class
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->arrScores = array();
		$this->fTotalWeight = 0.0;
	}

	/**
	 * add score
	 *
	 * @param float $fScore
	 * @param float $fWeightFactor
	 * @param bool $bOnlyAddWhenHigherThanCurrentScore to prevent a score from going down
	 * @return void
	 */
	public function addScore($fScore, $fWeightFactor = 1.0, $bOnlyAddWhenHigherThanCurrentScore = false)
	{
		$bAdd = true;
		$fCalcScore = 0.0;

		if ($bOnlyAddWhenHigherThanCurrentScore)
		{
			$fCalcScore = $this->calculateScore();
			if (round($fScore, 6) <= round($fCalcScore, 6)) //6 digit precision
				$bAdd = false;
		}

		if ($bAdd)
		{
			$this->arrScores[] = array(
				TWeightedAverageScore::ARRKEYINDEX_SCORE => $fScore,
				TWeightedAverageScore::ARRKEYINDEX_WEIGHT => $fWeightFactor
			);
			$this->fTotalWeight += $fWeightFactor;
		}
	}

	/**
	 * calculate the total of the weighted scores
	 *
	 * @return float
	 */
	public function calculateScore()
	{
		$fTotalScore = 0.0;
		$iCountScoreArr = 0;
		$iCountScoreArr = count($this->arrScores);
		for ($iIndex = 0; $iIndex < $iCountScoreArr; ++$iIndex)
		{
			$fTotalScore += $this->arrScores[$iIndex][TWeightedAverageScore::ARRKEYINDEX_SCORE] * $this->arrScores[$iIndex][TWeightedAverageScore::ARRKEYINDEX_WEIGHT];		
		}

		//prevent division by zero due to no scores added
		if ($iCountScoreArr == 0) 
			return 0.0;
		if (round($this->fTotalWeight, 1) == 0.0)
			return 0.0;


		return ($fTotalScore / $this->fTotalWeight);
	}
}
