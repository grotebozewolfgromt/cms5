<?php
namespace dr\classes\db;

use dr\classes\dom\TPaginator;
use dr\classes\models\TRecord;
use dr\classes\models\TSysModel;


/**
 * Description of TDBPreparedStatement
 *
 * Hulpklasse om te werken met prepared statements
 * Deze klasse kan prepared statements debuggen door het te genereren SQL-commando (door de database) weer te geven
 * specifieke database implementatie dient door de childs van deze klasse te geschieden 
 * 
 * Hoe gebruik je de childs van deze klasse ?
 * -vraag aan het connectie object een nieuw prepared statement object op met prepare()
 * -verbind de nodige parameters aan de vraagtekens in het prepared statement met bind()
 * -query uitvoeren met execute()
 * -debuggen is mogelijk met debug(), deze geeft het SQL commando terug
 * -close statement
 * 
 * voorbeeld:
 *  $objConn = $objApplication->getDatabaseConnectionObject();
 *  $objPrep = $objConn->prepare('SELECT FirstName FROM Persons WHERE LastName = ?');
 *  $objPrep->bind('achternaam');
 *  $objPrep->bind('adressen');
 *  $objPrep->bind('arnhem');
 *  $objPrep->executeQuery();
 *  $sSQL = $objPrep->debug();
 *  vardump($sSQL); //--> resultaat op scherm: SELECT achternaam FROM adressen WHERE plaats = arnhem
 * 
 *  $objRst = $objPrep->getResultset();
 *   while (!$objRst->eof())
 *   {
 *       $sVar = $objRst->get('FirstName');
 *       $objRst->moveNext();
 *   }
 *
 *  $objPrep->close();
 * 
 * 
* 12 feb 2013: TDBPreparedStatement: setExecution state verwijderd (zat al in parent)
* 12 feb 2013: TDBPreparedStatement: get en setSQLPreparedStatement toegevoegd
* 9 apr 2015: TDBPreparedStatement: alle functies van TDBQuery zijn opgenomen in deze klasse en erft nu niet meer over van TDBQuery. Dit ivm performance (en het leverde programmeertechnisch geen klap extra op gezien toch overal het prepared statement voor gebruikt moet worden)
* 14 mei 2015: TDBPreparedStatement:  translateComparisonOperator nu als abstracte functie
* 14 mei 2015: TDBPreparedStatement:  getTableAndFieldnameAsFieldname() toegevoegd
* 13 sept 2019: TDBPreparedStatement:  getAffectedRows() removed because it was too mysql specific
 * 18jan2020: TDBPreparedStatement en TDBPreparedStatementMySQL geupdate met deleteModel()!!!!
 * 2 dec 2020: TDBPreparedStatement: added functions: alterTableDrop, alterTableRenameField, alterTableModify, alterTableAdd
 * 24 apr 2025: TDBPreparedStatement: rename: parseSQL() ==> renderSQL()
 * 
 * @author drenirie
 */
abstract class TDBPreparedStatement
{
    private $arrParameters = null; //de array waar alle parameters in opgeslagen worden (index gelijk aan $arrParameterTypes)
    private $arrParameterTypes = null;//de types (integers volgens lib_types) van de paramaters (index gelijk aan $arrParameters)
    private $objInternalStatementAPIObject = null;
    private $objInternalDatabaseAPIObject = null;
    private $objLastResultset = null;
    private $sSQLPreparedStatement = ''; //the SQL (with question marks) of the prepared statement SELECT * FROM tblUsers WHERE id = ? AND city = ?
    private $bExecutionSQLOk = false;
    private $sLastSQLStatement = ''; //Last executed SQL query OR prepared statement
    protected $objConn = null; //conection
                

    
        
    public function  __construct(TDBConnection $objConnectionParent, $sSQLStatement = '')
    {
        $this->setOwnerObject($objConnectionParent);
        $this->sLastSQLStatement = $sSQLStatement;
    }    
    
    public function __clone()
    {
    		if ($objLastResultset)
    			$this->objLastResultset = clone $this->objLastResultset;
    		if ($this->objInternalStatementAPIObject)
    			$this->objInternalStatementAPIObject = clone $this->objInternalStatementAPIObject;
    		if ($this->objInternalDatabaseAPIObject)
    			$this->objInternalDatabaseAPIObject = clone $this->objInternalDatabaseAPIObject;
    		
    }
    
    
    /**
     * @return object parent object
     */
	public function getOwnerObject()
	{
		return $this->objConn;
    }
    
    /**
     * @return object parent object
     */
    public function setOwnerObject($objConn)
    {
    		$this->objConn = $objConn;
    }    
    
    public function getLastSQLQuery()
    {
        return $this->sLastSQLStatement;
    }
    
    public function setLastSQLQuery($sQuery)
    {
        $this->sLastSQLStatement = $sQuery;
    }
    
    /**
     * @param mixed $mSQL can be a string with a SQL statement
     * @param int $iMaxResultCount hoeveel resultaten worden maximaal terug gegeven
     * @param int $iOffset welk record beginnen met tellen
     * @return TDBResultset database resultset, null when error
     */
    abstract public function executeQuery($mSQL = '', $iMaxResultCount = 0, $iOffset = 0);
    
    /**
     * execute a select query (you expect a resultset)
     * The results are stored in the TSysModel object (you supply as parameter)
     * 2 sept 2015: new method in stead of executeQuery() - for using with TSysModel
     * 
     * the result is stored in the TSysModel object
     * 
     * @param TSysModel $objModel
     */
    abstract public function executeSelect(TSysModel &$objModel);
    
    /**
     * execute query: when you expect no resultset i.e. delete, insert, update...
     * 2 sept 2015: new method in stead of executeQuery() - for using with TSysModel
	 * 
     * @param string $sSQL plain sql
     */
    abstract protected function execute($sSQL);
    
    


    /**
     * return a contents of a database as SQL statements
     * 
     * @param boolean $bDropTableIfExists
     * @return string SQL dump contents as string
     */
    abstract public function getBackupDatabase($sDatabase = '', $bDropTableIfExists = true);
   
    
    /**
     * interne databasecomponent extraheren uit TDBConnection klasse
     *
     * @return TDBConnection intern gebruikte database component, of null als niet kan vinden
     */
    protected function getAPIConnObject()
    {
        try
        {
            //uitzoeken of direct teruggeven ? (kan alleen direct teruggeven als het al eens eerder uitgezocht is)
            if ($this->objInternalDatabaseAPIObject == null)
            {
                $objOwner = $this->getOwnerObject();
                if ($objOwner instanceof TDBConnection)
                {
                    return $objOwner->getAPIConnObject();
                }
                else
                {
                    throw new Exception('getAPIObject() : vreemd component gevonden als owner klasse (niet van het type TDBConnection) : '.get_class($objOwner));
                }
            }
            else
                return $this->objInternalDatabaseAPIObject;
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return null;
        }
    }

    /**
     * get the parent connection object. This is the object wich created this query object
     * @return TDBConnection
     */
    public function getConnectionObjectParent()
    {
        return $this->getOwnerObject();
    }

    /**
     * returns if the databasequery was succesfully executed (returns: true) or NOT (returns: false)
     * 
     * @return bool
     */
    public function getExecutionSQLOK()
    {
        return $this->bExecutionSQLOk;
    }
    
    /**
     * sets the SQL execution state on ok
     * this execution state returns if the last databasequery was succesfully executed
     * 
     * @param bool $bOK
     */
    protected function setExecutionSQLOK($bOK)
    {
        $this->bExecutionSQLOk = $bOK;
    }
    
    
    /**
     * DEZE FUNCTIE MOET OVERERFD WORDEN DOOR CHILD KLASSE
     *
     * voor het makkelijk toevoegen van records aan de database
     * @param TSysModel $objModel builder object
     * @return bool toevoegen gelukt ?
     */
    abstract public function insertModel(TSysModel &$objModel);


    /**
     * DEZE FUNCTIE MOET OVERERFD WORDEN DOOR CHILD KLASSE
     * Voor het eenvoudig wijzigen van databaserecords
     *
     * @param TSysModel $objModel builder object
     * @return bool SQL commando gelukt ?
     */
    abstract public function updateModel(TSysModel &$objModel);
    
    /**
     * update one field
	 *
     * @param string $sTable 
     * @param string $sField 
     * @param string $sValue 
     * @param string $sWhereField 
     * @param string $sWhereValue 
     * @param string $sComparisonOperator COMPARISON_OPERATOR_EQUAL_TO
     * @return boolean
     */
    abstract public function updateField($sTable, $sField, $sValue, $sWhereField, $sWhereValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO);

    /**
     * increment the values in one integer-type field
     * 
     * i.e. if you want to insert a record at a certain spot in a table with an order field, 
     * you need to update all order values above a certain order number.
	 *
     * @param string $sTable 
     * @param string $sField 
     * @param string $sWhereField 
     * @param string $iBetweenStart start value
     * @param string $iBetweenEnd 
     * @return boolean
     */
    abstract public function updateFieldIncrementBetween($sTable, $sField, $sWhereField, $iBetweenStart, $iBetweenEnd);

    /**
     * decrement the values in one integer-type field
     * 
     * i.e. if you want to insert a record at a certain spot in a table with an order field, 
     * you need to update all order values above a certain order number.
	 *
     * @param string $sTable 
     * @param string $sField 
     * @param string $sWhereField 
     * @param string $iBetweenStart start value
     * @param string $iBetweenEnd 
     * @return boolean
     */
    abstract public function updateFieldDecrementBetween($sTable, $sField, $sWhereField, $iBetweenStart, $iBetweenEnd);

    /**
     * getting a safe name for the supplied parameter string
     * preventing weird database names to confuse a the SQL code
     * for preventing errors AND SQL injection
     *
     * @param string $sDatabase databasenaam die veilig gemaakt moet worden
     * @return string met veilige databasenaam
     */
    abstract function getSafeDatabaseName($sDatabase);
    
    /**
     * getting a safe name for the supplied parameter string
     * preventing weird tables names to confuse a the SQL code
     * for preventing errors AND SQL injection
     * 
     * @param string $sTable 
     * @return string met veilige tabelnaam
     */
    abstract function getSafeTableName($sTable);

    /**
     * getting a safe name for the supplied parameter string
     * preventing weird columnnames to confuse a the SQL code
     * for preventing errors AND SQL injection
     *
     * @param string $sField columnname in the database table
     * @param string $bStrict for some statements (i.e. COUNT() or 'column1 AS column2') we need to loosen up the check so it will accept COUNT and AS
     * @return string met veilige kolomnaam/field name
     */
    abstract function getSafeFieldName($sField, $bStrict = true);

    /**
     * getting a safe value for the supplied parameter string
     * preventing the confusion of the SQL code
     * for preventing errors AND SQL injection
     *
     * @param string $sValue
     * @param bool $bUseWhiteList Gebruik een wittelijst met goede karakters. Alle karakters buiten deze lijst worden gefilterd
     * @param string $sWhitelist string met toegestane karakters. Karakters die niet op de lijst staan (foute karakters) worden gefilterd
     * @return string veilige veldwaarde voor inbrengen van data in een tabelveld
     */    
    abstract public function getSafeFieldValue($sValue);
            
    /**
     * Deze functie is een hulpfunctie voor de database specifieke implementatie
     * van de functie getSafeFieldValue().
     * De getSafeFieldValue() kan deze functie gebruiken voor de whitelist functionaliteit
     *
     * @param string $sValue onveilige input voor een tabelveld
     * @param string $sWhitelist string met toegestane karakters. Karakters die niet op de lijst staan (foute karakters) worden gefilterd
     * @return string veilige veldwaarde voor inbrengen van data in een tabelveld
     */
//     protected function getSafeFieldValueUsingWhiteList($sValue, $sWhitelist = null)
//     {
//         return filterBadCharsWhiteList($sValue, $sWhitelist);
//     }
 
    
    /**
     * the querybuilder-less function
     * @param string $sTable
     * @param string $sField
     * @param mixed $mHasValue can be of different types (string, integer etc)
     * @param string $sComparisonOperator
     * @param string $sField2, you can specify a second field
     * @param mixed $mHasValue2 can be of different types (string, integer etc), you can specify a second value
     * @param string $sComparisonOperator2, you can specify a second operator
     */
    abstract public function deleteFromDB($sTable, $sField, $mHasValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $sField2 = '', $mHasValue2 = '', $sComparisonOperator2 = COMPARISON_OPERATOR_EQUAL_TO);
   

     /**
     * delete from database according to where conditions given in TSysModel
     * 
     * @param TSysModel $objModel
     * @param boolean $bYesISpecifiedAWhereInMyModel to prevent you from deleting the contents of the whole table by accident
     * @return boolean ok= true
     */
    abstract public function deleteModel(&$objModel, $bYesISpecifiedAWhereInMyModel);
    

    
    /**
     * return SQL select-statement from TSysModel query builder
     * 2 sept 2015: alternative for TDBSQLBuilder  wich replaces generateSQLSelect() and generateSQLCount
     * 
     * this function can generate een count statement for the select query (handy for the paginator class)
     * 
     * @param TSysModel $objModel 
     * @return string sql statement, null if error
     */
    abstract public function generateSQLSelectModel(TSysModel &$objModel);
    
    

    
    

    /**
     * verkrijgen van het id van het laatst toegevoegde record
     * WERKT ALLEEN VOORDAT COMMIT IS UITGEVOERD!
     * (werkt alleen met auto increment id's)
     *
     * @return int id of the last record added to the database table
     */
     abstract public function getLastInsertID();

    /**
     * het aantal records waarop de laatste het laatste SQL commando van toepassing is geweest
     * alleen van toepassing op het updaten van een tabel (INSERT, UPDATE commando's)
     *
     * @return int number of rows
     */
     //abstract public function getAffectedRows();        --> removed because its a typical mysql function
         
     /**
      * verwijderen van een VOLLEDIGE tabel uit de database
      * @param sting $sTableName
      * @return bool drop succesfull ?
      */
     abstract public function dropTable($sTableName);

     /**
      * verwijderen van een VOLLEDIGE database
      * @param sting $sDatabase
      * @return bool drop succesfull ?
      */
     abstract public function dropDatabase($sDatabase);
     
     
     /**
      * Opvragen van laatste ID van een database-tabel en hier 1 bij op tellen
      * dit is te gebruiken voor het makkelijk genereren van een nieuwe ID voor
      * een volgende record in de database tabel
      *
      * @param string $sTable
      * @param string $sIDFieldName
      * @return integer het ID van een database tabel + 1
      */
     abstract public function getIDPlus1($sTable, $sIDFieldName);

     /**
      * verkrijgen van de veldnamen van een databasetabel
      *
      * @param string $sTable
      * @return array of strings with fields
      */
     abstract public function getFieldsInTable($sTable);

     /**
      * verkrijgen van de tabellen in de huidige database
      * @return array 1d array met namen van tabellen, null wanneer er iets mis ging
      */
     abstract public function getTablesInDatabase($sDatabase = '');

     /**
      * creating a database table wich is defined in TDBTable
      *
      * @param TSysModel $objModel
      * @return bool success?
      */
     abstract public function createTableFromModel(TSysModel &$objModel); //test with new TSysModel object
     
     /**
      * creates a database 
      * 
      * @param string $sDatabaseName
      * @return boolean
      */
     abstract public function createDatabase($sDatabaseName);
     
     /**
      * checks if database table exists
      * (useful for automatically creating database tables)
      * 
      * @param string $sTableName
      * @return boolean TRUE = table exists
      */
     abstract public function tableExists($sTableName);
     
    
    /**
     * set internal statement object (api object, can be any object)
     * 
     * @param object $objStatObj
     */
    protected function setAPIStatementObject($objStatObj)
    {
        $this->objInternalStatementAPIObject = $objStatObj;
    }
    
    /**
     * return internal statement object (api object, can be any object)
     * 
     * @return object
     */
    protected function getAPIStatementObject()
    {
        return $this->objInternalStatementAPIObject;
    }

    /**
     * sets the SQL (with question marks) wich is needed to produce the full SQL statement
     * @param string $sSQL
     */
    protected function setSQLPreparedStatement($sSQL)
    {
        $this->sSQLPreparedStatement = $sSQL;
    }
    
    /**
     * sets the SQL (with question marks) wich is needed to produce the full SQL statement
     * @return string
     */
    protected function getSQLPreparedStatement()
    {
        return $this->sSQLPreparedStatement;
    }
    
    /**
     * return last generated resultset
     * 
     * @return TDBResulset
     */
    public function getResultset()
    {
        return $this->objLastResultset;
    }

    /**
     * set the last resulset
     * 
     * @param TDBResultset $objRst
     */
    protected function setResultset($objRst)
    {
        $this->objLastResultset = $objRst;
    }    
    
    /**
     * return parameters that will be bound to the question mark-characters
     */
    public function getParameters()
    {
        return $this->arrParameters;
    }
    
    /**
     *  return array of types (integers volgens lib_types)
     * 
     * @return array
     */
    public function getParameterTypes()
    {
        return $this->arrParameterTypes;
    }
    
    /**
     * adds parameter to internal parameter list
     * (no database action involved)
     * 
     * 
     * @param string $sParameter
     */
    protected function addParameter($sParameter, $ctType = CT_VARCHAR)
    {
        $this->arrParameters[] = $sParameter;
        $this->arrParameterTypes[] = $ctType;
    }
    
    /**
     * deletes all bound parameters
     */
    public function clearParameters()
    {
        $this->arrParameters = array();
        $this->arrParameterTypes = array();
    }

    /**
     * count parameters
     * @return int
     */
    abstract public function paramCount();
    
    
    /**
     * bind parameter to next question mark in SQL command
     * function calls database API and adds parameter to internal array
     * @param int $ctType integer wich defines type (defined in TDBTableColumn)
     * @param mixed $mParameter parameter you want to add
     * 
     * THE CHILD CLASS HAS TO CALL addParameter() IN THIS FUNCTION TO ADD PARAMETERS TO INTERNAL ARRAY!!! OTHERWISE DEBUGGING WON'T WORK!
     */
    abstract public function bind($mParameter, $ctType = CT_VARCHAR);
    
    /**
     * close prepared statement
     */
    abstract public function close();
             
    /**
     * prepare SQL statement
     * @param string $sPreparedStatement
     */
    abstract public function prepare($sPreparedStatement);
    
    
    /**
     * this function tries to parse the prepared SQL into a valid SQL query
     * 1) when prepared statements are emulated: this function produces the real sql
     * 2) when using real prepated statements: this function can be used for the debug function
     * 
     * @return string parsed SQL
     */
    abstract protected function renderSQL($sSQLStatementWithPlaceholders = '');

    
    /**
     * translate the comparison operators form lib_types to database specific operators
     * 
     * @param string $sComparisonOperator
     */
    abstract public function translateComparisonOperator($sComparisonOperator);

    /**
     * returns the SQL string as will be parsed by the database
     * WORKS ONLY IN DEVELOPMENT ENVIRONMENT! (uses APP_DEBUGMODE)
     * 
     * This function needs be be considered as a usefull help, not as a perfect debug functionality!
     * This function only replaces the questionmarks. 
     * If an API adds extra restrictions (such as the mysqli API: ONLY values, NO field names and NO table names, ONLY select, insert and update statements), this function doesn't take that into account
     * so the debug function can generate valid SQL code, while the API rejects it!
     * 
     * in some databases
     * 
     * @return string SQL command (if in development mode, else null)
     */
    public function debug($sSQLStatementWithPlaceholders = '')
    {

        if (APP_DEBUGMODE)
        {
            if ($sSQLStatementWithPlaceholders == '')
            {
                $sGeneratedSQL = $this->renderSQL($this->getSQLPreparedStatement());
                $sSQLStatementWithPlaceholders = $this->getSQLPreparedStatement();
            }
            else
                $sGeneratedSQL = $this->renderSQL($sSQLStatementWithPlaceholders);
            
            
            //kijken of het aantal vraagtekens overeenkomt met het aantal gebonden (bind) parameters:
            $iQuestMarksCount = substr_count($sSQLStatementWithPlaceholders, '?');
            if (count($this->getParameters()) != $iQuestMarksCount)
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'the number of question marks ('.$iQuestMarksCount.') isn\'t the same as the number of bound parameters ('.count($this->getParameters()).')', $this);

            
            echo $sGeneratedSQL.' (WARNING: query can be correct and still generate an error, because some APIs dont accept every position of the question mark. i.e. WHERE clause)';
        }
        else
            null;
    }


    
    /**
     * sometimes you need the tablename and fieldname as just 1 unique fieldname for a query
     * but is the table always separated from the fieldname with a dot? 
     * 
     * overload this function if you specific database uses another notation than
     * table.fieldname
     * 
     * @param string $sFieldname
     * @param string $sTable
     */
    public function concatenateTableAndFieldname($sTable, $sFieldname) 
    {
    		return $sTable.'.'.$sFieldname;    
    }
    
    /**
     * function returns the fieldname from a concatenated table and fieldname
     * (opposite of concatenateTableAndFieldname())
     *  
     * @param string $sFieldnameWithTableName
     */
    public function deconcatenateTableAndFieldname($sFieldnameWithTableName)
    {
    		$arrTableFieldname = explode('.', $sFieldnameWithTableName);
    		if (count($arrTableFieldname) == 2) //als er 2 elementen in zitten is de eerste de tabel, tweede de veldnaam
    			return $arrTableFieldname[1];
    		else //anders alles terug geven (we weten niet wat het is), waarschijnlijk zit er geen tabelnaam in
    			return $sFieldnameWithTableName;
    }
    

    /**
     * add column to database table
     * 
     * this function uses the defined table of TSysModel to change the values to
     * $sFieldName must be a field defined in TSysModel
     * 
     * @param TSysModel $objModel
     * @param string $sFieldname (defined in TSysModel)
     * @return bool
     */
    abstract public function alterTableAdd(&$objModel, $sFieldname);



    /**
     * change column in database table
     * 
     * this function uses the defined table of TSysModel to change the values to
     * $sFieldName must be a field defined in TSysModel
     * 
     * @param TSysModel $objModel
     * @param string $sFieldname  (defined in TSysModel)
     * @return bool
     */
    abstract public function alterTableModify(&$objModel, $sFieldname);


    /**
     * rename column in database table
     * this function uses the defined table of TSysModel to change the values to.
     * $sNewFieldName must be a field defined in TSysModel
     *
     * @param TSysModel $objModel
     * @param string $sOldFieldName old name of the column
     * @param string $sNewFieldName new name of the column (defined in TSysModel)
     * @return bool
     */
    abstract public function alterTableRenameField(&$objModel, $sOldFieldName, $sNewFieldName);


    /**
     * drop column from database table
     *
     * @param string $sTable
     * @param string $sFieldname
     * @return bool
     */
    abstract public function alterTableDrop($sTable, $sFieldname);

}
?>
