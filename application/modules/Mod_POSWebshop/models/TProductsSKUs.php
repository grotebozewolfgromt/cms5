<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;


/**
 * TProductsSKUs (Stock Keeping Units)
 * 
 * 9 may 2025: TProductsSKUs: created
 * 
 * @author Dennis Renirie
 */

class TProductsSKUs extends TSysModel
{
	const FIELD_SKUCODE							= 'sSKUCode'; //custom SKU code
	const FIELD_MODELNO							= 'sModelNO'; //model number
	const FIELD_SALESPRICEIMPACTEXCLVAT			= 'dSalesPriceImpactExclVAT'; // the sales price delta of base sales price in Product table. For example: base price = 10.00 and salesprice impact = 2.00 then salesprice for customer = 12.00
	const FIELD_COSTPRICEEXCLVAT				= 'dCostPriceExclVAT'; 
	const FIELD_EOL								= 'bEOL'; //End Of Life
	// const FIELD_ISPRICEDISCOUNTED				= 'bIsPriceDiscounted'; //is product available at discounted price?
	// const FIELD_DISCOUNTSTART					= 'dtDiscountStart';//when does the discounted price start?
	// const FIELD_DISCOUNTEND					= 'dtDiscountEnd';//when does the discounted price end?
	// const FIELD_DISCOUNTIMPACTSALESPRICEINCLVAT	= 'dDiscountImpactPrice';//when does the discounted price end?
	const FIELD_WEIGHTBOXG					= 'iWeightBoxG'; //weight of the box in grams
	const FIELD_WEIGHTPRODUCTG				= 'iWeightProductG'; //weight of the product itself in grams
	const FIELD_DIMENSIONPACKAGEHEIGHTCM	= 'dDimensionPackageHeightCM'; //the height of the packaging in CM
	const FIELD_DIMENSIONPACKAGEWIDTHCM		= 'dDimensionPackageWidthCM'; //the width of the packaging in CM
	const FIELD_DIMENSIONPACKAGEDEPTHCM		= 'dDimensionPackageDepthCM'; //the depth of the packaging in CM
	const FIELD_DIMENSIONPRODUCTHEIGHTCM	= 'dDimensionProductHeightCM'; //the height of the packaging in CM
	const FIELD_DIMENSIONPRODUCTWIDTHCM		= 'dDimensionProductWidthCM'; //the width of the packaging in CM
	const FIELD_DIMENSIONPRODUCTDEPTHCM		= 'dDimensionProductDepthCM'; //the depth of the packaging in CM
	const FIELD_META_VARIANTSDESCRIPTION	= 'sMetaVariantsDescription';//all variants as 1 string combined: i.e. "White - L"
	const FIELD_META_VARIANTVALUE1			= 'sMetaVariantValue1';//custom product-variation value that is cached for easier SQL queries
	const FIELD_META_VARIANTVALUE2			= 'sMetaVariantValue2';//custom product-variation value that is cached for easier SQL queries
	const FIELD_META_VARIANTVALUE3			= 'sMetaVariantValue3';//custom product-variation value that is cached for easier SQL queries
	const FIELD_META_VARIANTVALUE4			= 'sMetaVariantValue4';//custom product-variation value that is cached for easier SQL queries
	const FIELD_META_VARIANTVALUE5			= 'sMetaVariantValue5';//custom product-variation value that is cached for easier SQL queries
	const FIELD_PRODUCTID					= 'iProductID';//product id


	/**
	 * get custom SKU code
	 * 
	 * @return string
	 */
	public function getSKUCode()
	{ 
		return $this->get(TProductsSKUs::FIELD_SKUCODE);
	}
	
	/**
	 * set custom SKU code
	 * 
	 * @param string $sName
	 */
	public function setSKUCode($sCode)
	{
		$this->set(TProductsSKUs::FIELD_SKUCODE, $sCode);
	}        
	
	/**
	 * get model number
	 * 
	 * @return string
	 */
	public function getModelNo()
	{ 
		return $this->get(TProductsSKUs::FIELD_MODELNO);
	}
	
	/**
	 * set model number
	 * 
	 * @param string $sModel
	 */
	public function setModelNo($sModel)
	{
		$this->set(TProductsSKUs::FIELD_MODELNO, $sModel);
	}        
	
	/**
	 * get cost price excluding VAT
	 * 
	 * @return TDecimal
	 */
	public function getCostPriceExclVAT()
	{
		return $this->get(TProductsSKUs::FIELD_COSTPRICEEXCLVAT);
	}
	
	/**
	 * set cost price excluding VAT
	 * 
	 * @param TDecimal $objAmount
	 */
	public function setCostPriceExclVAT($objAmount)
	{
		$this->set(TProductsSKUs::FIELD_COSTPRICEEXCLVAT, $objAmount);
	}     

	/**
	 * get sales price impact excluding VAT
	 * 
	 * @return TDecimal
	 */
	public function getSalesPriceImpactExclVAT()
	{
		return $this->get(TProductsSKUs::FIELD_SALESPRICEIMPACTEXCLVAT);
	}
	
	/**
	 * set cost price excluding VAT
	 * 
	 * @param TDecimal $objCurrency
	 */
	public function setSalesPriceImpactExclVAT($objCurrency)
	{
		$this->set(TProductsSKUs::FIELD_SALESPRICEIMPACTEXCLVAT, $objCurrency);
	}     

	/**
	 * get End Of Life
	 * 
	 * @return boolean
	 */
	public function getEOL()
	{
		return $this->get(TProductsSKUs::FIELD_EOL);
	}
	
	/**
	 * set End Of Life
	 * 
	 * @param boolean $bEOL
	 */
	public function setEOL($bEOL)
	{
		$this->set(TProductsSKUs::FIELD_EOL, $bEOL);
	}   

	// /**
	//  * get if product is available for a discounted price
	//  * 
	//  * @return boolean
	//  */
	// public function getIsPriceDiscounted()
	// {
	// 	return $this->get(TProductsSKUs::FIELD_ISPRICEDISCOUNTED);
	// }	

	// /**
	//  * set if product is available for a discounted price
	//  * 
	//  * @param boolean $bOnSale
	//  */
	// public function setIsPriceDiscounted($bOnSale)
	// {
	// 	$this->set(TProductsSKUs::FIELD_ISPRICEDISCOUNTED, $bOnSale);
	// }    

	// /**
	//  * get start date of sale
	//  * 
	//  * @return TDateTime
	//  */
	// public function getDiscountStart()
	// {
	// 	return $this->get(TProductsSKUs::FIELD_DISCOUNTSTART);
	// }	

	// /**
	//  * set start date of sale
	//  * 
	//  * @param TDateTime $dtDate
	//  */
	// public function setDiscountStart($dtDate)
	// {
	// 	$this->set(TProductsSKUs::FIELD_DISCOUNTSTART, $dtDate);
	// }  

	// /**
	//  * get end date of sale
	//  * 
	//  * @return TDateTime
	//  */
	// public function getDiscountEnd()
	// {
	// 	return $this->get(TProductsSKUs::FIELD_DISCOUNTEND);
	// }	

	// /**
	//  * set end date of sale
	//  * 
	//  * @param TDateTime $dtDate
	//  */
	// public function setDiscountEnd($dtDate)
	// {
	// 	$this->set(TProductsSKUs::FIELD_DISCOUNTEND, $dtDate);
	// }    
	
	// /**
	//  * get sales price including VAT when product is available at discounted price
	//  * 
	//  * @return TDecimal
	//  */
	// public function getDiscountSalesPriceInclVAT()
	// {
	// 	return $this->get(TProductsSKUs::FIELD_DISCOUNTSALESPRICEINCLVAT);
	// }	


	// /**
	//  * set sales price including VAT when product is available at discounted price
	//  * 
	//  * @param TDecimal $objCurrency
	//  */
	// public function setDiscountSalesPriceInclVAT($objCurrency)
	// {
	// 	$this->set(TProductsSKUs::FIELD_DISCOUNTSALESPRICEINCLVAT, $objCurrency);
	// }    
		
	/**
	 * get the weight of the box in grams
	 * 
	 * @return integer
	 */
	public function getWeightBoxG()
	{
		return $this->get(TProductsSKUs::FIELD_WEIGHTBOXG);
	}	

	/**
	 * set the weight of the box in grams
	 * 
	 * @param integer $iWeightInGrams
	 */
	public function setWeightBoxG($iWeightInGrams)
	{
		$this->set(TProductsSKUs::FIELD_WEIGHTBOXG, $iWeightInGrams);
	}    
	
	 
	//======================================= BOX DIMENSIONS & WEIGHT =======================

	/**
	 * get the height of the box in centimeter
	 * 
	 * @return TDecimal
	 */
	public function getDimensionPackageHeightCM()
	{
		return $this->get(TProductsSKUs::FIELD_DIMENSIONPACKAGEHEIGHTCM);
	}	


	/**
	 * set the height of the box in centimeter
	 * 
	 * @param TDecimal $objCentimeters
	 */
	public function setDimensionPackageHeightCM($objCentimeters)
	{
		$this->set(TProductsSKUs::FIELD_DIMENSIONPACKAGEHEIGHTCM, $objCentimeters);
	}    
		
	/**
	 * get the width of the box in centimeter
	 * 
	 * @return TDecimal
	 */
	public function getDimensionPackageWidthCM()
	{
		return $this->get(TProductsSKUs::FIELD_DIMENSIONPACKAGEWIDTHCM);
	}	


	/**
	 * set the width of the box in centimeter
	 * 
	 * @param TDecimal $objCentimeters
	 */
	public function setDimensionPackageWidthCM($objCentimeters)
	{
		$this->set(TProductsSKUs::FIELD_DIMENSIONPACKAGEWIDTHCM, $objCentimeters);
	}

	/**
	 * get the depth of the box in centimeter
	 * 
	 * @return TDecimal
	 */
	public function getDimensionPackageDepthCM()
	{
		return $this->get(TProductsSKUs::FIELD_DIMENSIONPACKAGEDEPTHCM);
	}	

	/**
	 * set the depth of the box in centimeter
	 * 
	 * @param TDecimal $objCentimeters
	 */
	public function setDimensionPackageDepthCM($objCentimeters)
	{
		$this->set(TProductsSKUs::FIELD_DIMENSIONPACKAGEDEPTHCM, $objCentimeters);
	}




	//======================================= PRODUCT DIMENSIONS & WEIGHT =======================

	/**
	 * get the weight of the product itself in grams
	 * 
	 * @return integer
	 */
	public function getWeightProductG()
	{
		return $this->get(TProductsSKUs::FIELD_WEIGHTPRODUCTG);
	}	

	
	/**
	 * set the weight of the product itself in grams
	 * 
	 * @param integer $iWeightInGrams
	 */
	public function setWeightProductG($iWeightInGrams)
	{
		$this->set(TProductsSKUs::FIELD_WEIGHTPRODUCTG, $iWeightInGrams);
	}  


	/**
	 * get the height of the product in centimeter
	 * 
	 * @return TDecimal
	 */
	public function getDimensionProductHeightCM()
	{
		return $this->get(TProductsSKUs::FIELD_DIMENSIONPRODUCTHEIGHTCM);
	}	


	/**
	 * set the height of the product in centimeter
	 * 
	 * @param TDecimal $objCentimeters
	 */
	public function setDimensionProductHeightCM($objCentimeters)
	{
		$this->set(TProductsSKUs::FIELD_DIMENSIONPRODUCTHEIGHTCM, $objCentimeters);
	}    
		
	/**
	 * get the width of the product in centimeter
	 * 
	 * @return TDecimal
	 */
	public function getDimensionProductWidthCM()
	{
		return $this->get(TProductsSKUs::FIELD_DIMENSIONPRODUCTWIDTHCM);
	}	


	/**
	 * set the width of the product in centimeter
	 * 
	 * @param TDecimal $objCentimeters
	 */
	public function setDimensionProductWidthCM($objCentimeters)
	{
		$this->set(TProductsSKUs::FIELD_DIMENSIONPRODUCTWIDTHCM, $objCentimeters);
	}

	/**
	 * get the depth of the product in centimeter
	 * 
	 * @return TDecimal
	 */
	public function getDimensionProductrDepthCM()
	{
		return $this->get(TProductsSKUs::FIELD_DIMENSIONPRODUCTDEPTHCM);
	}	

	/**
	 * set the depth of the product in centimeter
	 * 
	 * @param TDecimal $objCentimeters
	 */
	public function setDimensionProductDepthCM($objCentimeters)
	{
		$this->set(TProductsSKUs::FIELD_DIMENSIONPRODUCTDEPTHCM, $objCentimeters);
	}

	/**
	 * get meta variants description
	 * i.e. "White - L"
	 * 
	 * @return string
	 */
	public function getMetaVariantsDescription()
	{
		return $this->get(TProductsSKUs::FIELD_META_VARIANTSDESCRIPTION);
	}	

	/**
	 * set meta variants description
	 * i.e. "White - L"
	 * 
	 * @param string $sValue
	 */
	public function setMetaVariantsDescription($sValue)
	{
		$this->set(TProductsSKUs::FIELD_META_VARIANTSDESCRIPTION, $sValue);
	}

	/**
	 * get meta value 1
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @return string
	 */
	public function getMetaVariantValue1()
	{
		return $this->get(TProductsSKUs::FIELD_META_VARIANTVALUE1);
	}	

	/**
	 * set meta value 1
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @param string $sValue
	 */
	public function setMetaVariantValue1($sValue)
	{
		$this->set(TProductsSKUs::FIELD_META_VARIANTVALUE1, $sValue);
	}

	/**
	 * get meta value 2
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @return string
	 */
	public function getMetaVariantValue2()
	{
		return $this->get(TProductsSKUs::FIELD_META_VARIANTVALUE2);
	}	

	/**
	 * set meta value 1
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @param string $sValue
	 */
	public function setMetaVariantValue2($sValue)
	{
		$this->set(TProductsSKUs::FIELD_META_VARIANTVALUE2, $sValue);
	}

	/**
	 * get meta value 3
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @return string
	 */
	public function getMetaVariantValue3()
	{
		return $this->get(TProductsSKUs::FIELD_META_VARIANTVALUE3);
	}	

	/**
	 * set meta value 3
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @param string $sValue
	 */
	public function setMetaVariantValue3($sValue)
	{
		$this->set(TProductsSKUs::FIELD_META_VARIANTVALUE3, $sValue);
	}

	/**
	 * get meta value 4
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @return string
	 */
	public function getMetaVariantValue4()
	{
		return $this->get(TProductsSKUs::FIELD_META_VARIANTVALUE4);
	}	

	/**
	 * set meta value 4
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @param string $sValue
	 */
	public function setMetaVariantValue4($sValue)
	{
		$this->set(TProductsSKUs::FIELD_META_VARIANTVALUE4, $sValue);
	}

	/**
	 * get meta value 5
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @return string
	 */
	public function getMetaVariantValue5()
	{
		return $this->get(TProductsSKUs::FIELD_META_VARIANTVALUE5);
	}	

	/**
	 * set meta value 5
	 * 
	 * custom product-variation value that is cached for easier SQL queries
	 * 
	 * @param string $sValue
	 */
	public function setMetaVariantValue5($sValue)
	{
		$this->set(TProductsSKUs::FIELD_META_VARIANTVALUE5, $sValue);
	}


	/**
	 * get product id
	 * 
	 * @return integer
	 */
	public function getProductID()
	{
		return $this->get(TProductsSKUs::FIELD_PRODUCTID);
	}	

	/**
	 * set product id
	 * 
	 * @param integer $iID
	 */
	public function setProductID($iID)
	{
		$this->set(TProductsSKUs::FIELD_PRODUCTID, $iID);
	}








	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
     * 
	 * initialize values
	 */
	public function initRecord()
	{
		$this->setSKUCode("new ".date("Y-m-d H:i:s")); //preventing empty code being written to database resulting in duplicate name when this happened before
	}
		
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//SKU code
		$this->setFieldDefaultValue(TProductsSKUs::FIELD_SKUCODE, '');
		$this->setFieldType(TProductsSKUs::FIELD_SKUCODE, CT_VARCHAR);
		$this->setFieldLength(TProductsSKUs::FIELD_SKUCODE, 50);
		$this->setFieldDecimalPrecision(TProductsSKUs::FIELD_SKUCODE, 0);
		$this->setFieldPrimaryKey(TProductsSKUs::FIELD_SKUCODE, false);
		$this->setFieldNullable(TProductsSKUs::FIELD_SKUCODE, false);
		$this->setFieldEnumValues(TProductsSKUs::FIELD_SKUCODE, null);
		$this->setFieldUnique(TProductsSKUs::FIELD_SKUCODE, true); 
		$this->setFieldIndexed(TProductsSKUs::FIELD_SKUCODE, true); 
		$this->setFieldFulltext(TProductsSKUs::FIELD_SKUCODE, true); 
		$this->setFieldForeignKeyClass(TProductsSKUs::FIELD_SKUCODE, null);
		$this->setFieldForeignKeyTable(TProductsSKUs::FIELD_SKUCODE, null);
		$this->setFieldForeignKeyField(TProductsSKUs::FIELD_SKUCODE, null);
		$this->setFieldForeignKeyJoin(TProductsSKUs::FIELD_SKUCODE);
		$this->setFieldForeignKeyActionOnUpdate(TProductsSKUs::FIELD_SKUCODE, null);
		$this->setFieldForeignKeyActionOnDelete(TProductsSKUs::FIELD_SKUCODE, null); 
		$this->setFieldAutoIncrement(TProductsSKUs::FIELD_SKUCODE, false);
		$this->setFieldUnsigned(TProductsSKUs::FIELD_SKUCODE, false);
        $this->setFieldEncryptionDisabled(TProductsSKUs::FIELD_SKUCODE);

		//model number
        $this->setFieldCopyProps(TProductsSKUs::FIELD_MODELNO, TProductsSKUs::FIELD_SKUCODE);
		$this->setFieldUnique(TProductsSKUs::FIELD_MODELNO, false); 
		$this->setFieldIndexed(TProductsSKUs::FIELD_MODELNO, true); 
		$this->setFieldFulltext(TProductsSKUs::FIELD_MODELNO, true); 

		//cost price excl vat
		$this->setFieldDefaultsTDecimalCurrency(TProductsSKUs::FIELD_COSTPRICEEXCLVAT);
		
		//sales price impact
		$this->setFieldDefaultsTDecimalCurrency(TProductsSKUs::FIELD_SALESPRICEIMPACTEXCLVAT);


		//End of Life
		$this->setFieldDefaultValue(TProductsSKUs::FIELD_EOL, false);
		$this->setFieldType(TProductsSKUs::FIELD_EOL, CT_BOOL);
		$this->setFieldLength(TProductsSKUs::FIELD_EOL, 1);
		$this->setFieldDecimalPrecision(TProductsSKUs::FIELD_EOL, 0);
		$this->setFieldPrimaryKey(TProductsSKUs::FIELD_EOL, false);
		$this->setFieldNullable(TProductsSKUs::FIELD_EOL, false);
		$this->setFieldEnumValues(TProductsSKUs::FIELD_EOL, null);
		$this->setFieldUnique(TProductsSKUs::FIELD_EOL, false); 
		$this->setFieldIndexed(TProductsSKUs::FIELD_EOL, false); 
		$this->setFieldFulltext(TProductsSKUs::FIELD_EOL, false); 
		$this->setFieldForeignKeyClass(TProductsSKUs::FIELD_EOL, null);
		$this->setFieldForeignKeyTable(TProductsSKUs::FIELD_EOL, null);
		$this->setFieldForeignKeyField(TProductsSKUs::FIELD_EOL, null);
		$this->setFieldForeignKeyJoin(TProductsSKUs::FIELD_EOL);
		$this->setFieldForeignKeyActionOnUpdate(TProductsSKUs::FIELD_EOL, null);
		$this->setFieldForeignKeyActionOnDelete(TProductsSKUs::FIELD_EOL, null); 
		$this->setFieldAutoIncrement(TProductsSKUs::FIELD_EOL, false);
		$this->setFieldUnsigned(TProductsSKUs::FIELD_EOL, false);
        $this->setFieldEncryptionDisabled(TProductsSKUs::FIELD_EOL);		

		// //discounted sales price
		// $this->setFieldCopyProps(TProductsSKUs::FIELD_ISPRICEDISCOUNTED, TProductsSKUs::FIELD_EOL);

		// //discount start
		// $this->setFieldDefaultValue(TProductsSKUs::FIELD_DISCOUNTSTART, 0);
		// $this->setFieldType(TProductsSKUs::FIELD_DISCOUNTSTART, CT_DATETIME);
		// $this->setFieldLength(TProductsSKUs::FIELD_DISCOUNTSTART, 0);
		// $this->setFieldDecimalPrecision(TProductsSKUs::FIELD_DISCOUNTSTART, 0);
		// $this->setFieldPrimaryKey(TProductsSKUs::FIELD_DISCOUNTSTART, false);
		// $this->setFieldNullable(TProductsSKUs::FIELD_DISCOUNTSTART, false);
		// $this->setFieldEnumValues(TProductsSKUs::FIELD_DISCOUNTSTART, null);
		// $this->setFieldUnique(TProductsSKUs::FIELD_DISCOUNTSTART, false); 
		// $this->setFieldIndexed(TProductsSKUs::FIELD_DISCOUNTSTART, false); 
		// $this->setFieldFulltext(TProductsSKUs::FIELD_DISCOUNTSTART, false); 
		// $this->setFieldForeignKeyClass(TProductsSKUs::FIELD_DISCOUNTSTART, null);
		// $this->setFieldForeignKeyTable(TProductsSKUs::FIELD_DISCOUNTSTART, null);
		// $this->setFieldForeignKeyField(TProductsSKUs::FIELD_DISCOUNTSTART, null);
		// $this->setFieldForeignKeyJoin(TProductsSKUs::FIELD_DISCOUNTSTART);
		// $this->setFieldForeignKeyActionOnUpdate(TProductsSKUs::FIELD_DISCOUNTSTART, null);
		// $this->setFieldForeignKeyActionOnDelete(TProductsSKUs::FIELD_DISCOUNTSTART, null); 
		// $this->setFieldAutoIncrement(TProductsSKUs::FIELD_DISCOUNTSTART, false);
		// $this->setFieldUnsigned(TProductsSKUs::FIELD_DISCOUNTSTART, false);
        // $this->setFieldEncryptionDisabled(TProductsSKUs::FIELD_DISCOUNTSTART);		
		
		// //discount end
		// $this->setFieldCopyProps(TProductsSKUs::FIELD_DISCOUNTEND, TProductsSKUs::FIELD_DISCOUNTSTART);

		// //discounted sales price
		// $this->setFieldDefaultsTDecimalCurrency(TProductsSKUs::FIELD_DISCOUNTSALESPRICEINCLVAT);

		//weight box
		$this->setFieldDefaultValue(TProductsSKUs::FIELD_WEIGHTBOXG, 0);
		$this->setFieldType(TProductsSKUs::FIELD_WEIGHTBOXG, CT_INTEGER32);
		$this->setFieldLength(TProductsSKUs::FIELD_WEIGHTBOXG, 0);
		$this->setFieldDecimalPrecision(TProductsSKUs::FIELD_WEIGHTBOXG, 0);
		$this->setFieldPrimaryKey(TProductsSKUs::FIELD_WEIGHTBOXG, false);
		$this->setFieldNullable(TProductsSKUs::FIELD_WEIGHTBOXG, false);
		$this->setFieldEnumValues(TProductsSKUs::FIELD_WEIGHTBOXG, null);
		$this->setFieldUnique(TProductsSKUs::FIELD_WEIGHTBOXG, false); 
		$this->setFieldIndexed(TProductsSKUs::FIELD_WEIGHTBOXG, false); 
		$this->setFieldFulltext(TProductsSKUs::FIELD_WEIGHTBOXG, false); 
		$this->setFieldForeignKeyClass(TProductsSKUs::FIELD_WEIGHTBOXG, null);
		$this->setFieldForeignKeyTable(TProductsSKUs::FIELD_WEIGHTBOXG, null);
		$this->setFieldForeignKeyField(TProductsSKUs::FIELD_WEIGHTBOXG, null);
		$this->setFieldForeignKeyJoin(TProductsSKUs::FIELD_WEIGHTBOXG);
		$this->setFieldForeignKeyActionOnUpdate(TProductsSKUs::FIELD_WEIGHTBOXG, null);
		$this->setFieldForeignKeyActionOnDelete(TProductsSKUs::FIELD_WEIGHTBOXG, null); 
		$this->setFieldAutoIncrement(TProductsSKUs::FIELD_WEIGHTBOXG, false);
		$this->setFieldUnsigned(TProductsSKUs::FIELD_WEIGHTBOXG, false);
        $this->setFieldEncryptionDisabled(TProductsSKUs::FIELD_WEIGHTBOXG);	
		
		//weight product
		$this->setFieldCopyProps(TProductsSKUs::FIELD_WEIGHTPRODUCTG, TProductsSKUs::FIELD_WEIGHTBOXG);

		//box height
		$this->setFieldDefaultsTDecimal(TProductsSKUs::FIELD_DIMENSIONPACKAGEHEIGHTCM, 10,4);
		//box width
		$this->setFieldDefaultsTDecimal(TProductsSKUs::FIELD_DIMENSIONPACKAGEWIDTHCM, 10,4);
		//box depth
		$this->setFieldDefaultsTDecimal(TProductsSKUs::FIELD_DIMENSIONPACKAGEDEPTHCM, 10,4);

		//product height
		$this->setFieldDefaultsTDecimal(TProductsSKUs::FIELD_DIMENSIONPRODUCTHEIGHTCM, 10,4);
		//product width
		$this->setFieldDefaultsTDecimal(TProductsSKUs::FIELD_DIMENSIONPRODUCTWIDTHCM, 10,4);
		//product depth
		$this->setFieldDefaultsTDecimal(TProductsSKUs::FIELD_DIMENSIONPRODUCTDEPTHCM, 10,4);

		//meta variants description i.e. "White - L"
		$this->setFieldDefaultsVarChar(TProductsSKUs::FIELD_META_VARIANTSDESCRIPTION, 100);

		//meta variant value 1
		$this->setFieldDefaultValue(TProductsSKUs::FIELD_META_VARIANTVALUE1, '');
		$this->setFieldType(TProductsSKUs::FIELD_META_VARIANTVALUE1, CT_VARCHAR);
		$this->setFieldLength(TProductsSKUs::FIELD_META_VARIANTVALUE1, 50);
		$this->setFieldDecimalPrecision(TProductsSKUs::FIELD_META_VARIANTVALUE1, 0);
		$this->setFieldPrimaryKey(TProductsSKUs::FIELD_META_VARIANTVALUE1, false);
		$this->setFieldNullable(TProductsSKUs::FIELD_META_VARIANTVALUE1, false);
		$this->setFieldEnumValues(TProductsSKUs::FIELD_META_VARIANTVALUE1, null);
		$this->setFieldUnique(TProductsSKUs::FIELD_META_VARIANTVALUE1, false); 
		$this->setFieldIndexed(TProductsSKUs::FIELD_META_VARIANTVALUE1, false); 
		$this->setFieldFulltext(TProductsSKUs::FIELD_META_VARIANTVALUE1, false); 
		$this->setFieldForeignKeyClass(TProductsSKUs::FIELD_META_VARIANTVALUE1, null);
		$this->setFieldForeignKeyTable(TProductsSKUs::FIELD_META_VARIANTVALUE1, null);
		$this->setFieldForeignKeyField(TProductsSKUs::FIELD_META_VARIANTVALUE1, null);
		$this->setFieldForeignKeyJoin(TProductsSKUs::FIELD_META_VARIANTVALUE1);
		$this->setFieldForeignKeyActionOnUpdate(TProductsSKUs::FIELD_META_VARIANTVALUE1, null);
		$this->setFieldForeignKeyActionOnDelete(TProductsSKUs::FIELD_META_VARIANTVALUE1, null); 
		$this->setFieldAutoIncrement(TProductsSKUs::FIELD_META_VARIANTVALUE1, false);
		$this->setFieldUnsigned(TProductsSKUs::FIELD_META_VARIANTVALUE1, false);
        $this->setFieldEncryptionDisabled(TProductsSKUs::FIELD_META_VARIANTVALUE1);

		//meta variant value 2
		$this->setFieldCopyProps(TProductsSKUs::FIELD_META_VARIANTVALUE2, TProductsSKUs::FIELD_META_VARIANTVALUE1);
		//meta variant value 3
		$this->setFieldCopyProps(TProductsSKUs::FIELD_META_VARIANTVALUE3, TProductsSKUs::FIELD_META_VARIANTVALUE1);
		//meta variant value 4
		$this->setFieldCopyProps(TProductsSKUs::FIELD_META_VARIANTVALUE4, TProductsSKUs::FIELD_META_VARIANTVALUE1);
		//meta variant value 5
		$this->setFieldCopyProps(TProductsSKUs::FIELD_META_VARIANTVALUE5, TProductsSKUs::FIELD_META_VARIANTVALUE1);


		//product id
		$this->setFieldDefaultValue(TProductsSKUs::FIELD_PRODUCTID, 0);
		$this->setFieldType(TProductsSKUs::FIELD_PRODUCTID, CT_INTEGER64);
		$this->setFieldLength(TProductsSKUs::FIELD_PRODUCTID, 0);
		$this->setFieldDecimalPrecision(TProductsSKUs::FIELD_PRODUCTID, 0);
		$this->setFieldPrimaryKey(TProductsSKUs::FIELD_PRODUCTID, false);
		$this->setFieldNullable(TProductsSKUs::FIELD_PRODUCTID, false);
		$this->setFieldEnumValues(TProductsSKUs::FIELD_PRODUCTID, null);
		$this->setFieldUnique(TProductsSKUs::FIELD_PRODUCTID, false); 
		$this->setFieldIndexed(TProductsSKUs::FIELD_PRODUCTID, false); 
		$this->setFieldFulltext(TProductsSKUs::FIELD_PRODUCTID, false); 
		$this->setFieldForeignKeyClass(TProductsSKUs::FIELD_PRODUCTID, TProducts::class);
		$this->setFieldForeignKeyTable(TProductsSKUs::FIELD_PRODUCTID, TProducts::getTable());
		$this->setFieldForeignKeyField(TProductsSKUs::FIELD_PRODUCTID, TProducts::FIELD_ID);
		$this->setFieldForeignKeyJoin(TProductsSKUs::FIELD_PRODUCTID);
		$this->setFieldForeignKeyActionOnUpdate(TProductsSKUs::FIELD_PRODUCTID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TProductsSKUs::FIELD_PRODUCTID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TProductsSKUs::FIELD_PRODUCTID, false);
		$this->setFieldUnsigned(TProductsSKUs::FIELD_PRODUCTID, false);
        $this->setFieldEncryptionDisabled(TProductsSKUs::FIELD_PRODUCTID);				
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
		return array(TProductsSKUs::FIELD_SKUCODE, 
			TProductsSKUs::FIELD_MODELNO, 
			TProductsSKUs::FIELD_COSTPRICEEXCLVAT,
			TProductsSKUs::FIELD_EOL,
			TProductsSKUs::FIELD_WEIGHTBOXG,
			TProductsSKUs::FIELD_WEIGHTPRODUCTG,
			TProductsSKUs::FIELD_DIMENSIONPACKAGEHEIGHTCM,
			TProductsSKUs::FIELD_DIMENSIONPACKAGEWIDTHCM,
			TProductsSKUs::FIELD_DIMENSIONPACKAGEDEPTHCM,
			TProductsSKUs::FIELD_DIMENSIONPRODUCTHEIGHTCM,
			TProductsSKUs::FIELD_DIMENSIONPRODUCTWIDTHCM,
			TProductsSKUs::FIELD_DIMENSIONPRODUCTDEPTHCM,
			TProductsSKUs::FIELD_META_VARIANTVALUE1,
			TProductsSKUs::FIELD_META_VARIANTVALUE2,
			TProductsSKUs::FIELD_META_VARIANTVALUE3,
			TProductsSKUs::FIELD_META_VARIANTVALUE4,
			TProductsSKUs::FIELD_META_VARIANTVALUE5,
			TProductsSKUs::FIELD_PRODUCTID
					);
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
		return true;
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
		return false;
	}
	
	/**
	 * use checkout for locking file for editing
	*/
	public function getTableUseCheckout()
	{
		return true;
	}
		
	/**
	 * use record locking to prevent record editing
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
		return APP_DB_TABLEPREFIX.'ProductsSKUs';
	}
	
	
	
	/**
	 * OVERRIDE BY CHILD CLASS IF necessary
	 *
	 * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
	 * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 *
	 *
	 * return '??? - functie niet overschreven door child klasse';
	*/
	public function getDisplayRecordShort()
	{
		return $this->get(TProductsSKUs::FIELD_SKUCODE) . ' '. $this->get(TProductsSKUs::FIELD_MODELNO);
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
		return '2345sdf234tdxsffguyh6'.
			$this->get(TProductsSKUs::FIELD_SKUCODE).
			$this->get(TProductsSKUs::FIELD_MODELNO).
			'23w4rasdf_ASDfasdf3fiuj';			
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
		return false;
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