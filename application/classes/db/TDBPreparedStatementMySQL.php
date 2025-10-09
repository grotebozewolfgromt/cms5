<?php
namespace dr\classes\db;

use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\classes\types\TCurrency;
use dr\classes\types\TFloat;
use dr\classes\types\TInteger;

use dr\classes\dom\tag\TPaginator;
use dr\classes\models\TRecord;
use dr\classes\models\TSysModel;


/**
 * Description of TDBPreparedStatementMySQL
 * 
 * The prepared statement helper class for MySQL
 * 
 * De ownerobject is altijd een TDBConnection
 * 
 * 
MYSQLI_READ_DEFAULT_GROUP

    Read options from the named group from my.cnf or the file specified with MYSQLI_READ_DEFAULT_FILE
MYSQLI_READ_DEFAULT_FILE

    Read options from the named option file instead of from my.cnf
MYSQLI_OPT_CONNECT_TIMEOUT

    Connect timeout in seconds
MYSQLI_OPT_LOCAL_INFILE

    Enables command LOAD LOCAL INFILE
MYSQLI_INIT_COMMAND

    Command to execute when connecting to MySQL server. Will automatically be re-executed when reconnecting.
MYSQLI_CLIENT_SSL

    Use SSL (encrypted protocol). This option should not be set by application programs; it is set internally in the MySQL client library
MYSQLI_CLIENT_COMPRESS

    Use compression protocol
MYSQLI_CLIENT_INTERACTIVE

    Allow interactive_timeout seconds (instead of wait_timeout seconds) of inactivity before closing the connection. The client's session wait_timeout variable will be set to the value of the session interactive_timeout variable.
MYSQLI_CLIENT_IGNORE_SPACE

    Allow spaces after function names. Makes all functions names reserved words.
MYSQLI_CLIENT_NO_SCHEMA

    Don't allow the db_name.tbl_name.col_name syntax.
MYSQLI_CLIENT_MULTI_QUERIES

    Allows multiple semicolon-delimited queries in a single mysqli_query() call.
MYSQLI_STORE_RESULT

    For using buffered resultsets
MYSQLI_USE_RESULT

    For using unbuffered resultsets
MYSQLI_ASSOC

    Columns are returned into the array having the fieldname as the array index.
MYSQLI_NUM

    Columns are returned into the array having an enumerated index.
MYSQLI_BOTH

    Columns are returned into the array having both a numerical index and the fieldname as the associative index.
MYSQLI_NOT_NULL_FLAG

    Indicates that a field is defined as NOT NULL
MYSQLI_PRI_KEY_FLAG

    Field is part of a primary index
MYSQLI_UNIQUE_KEY_FLAG

    Field is part of a unique index.
MYSQLI_MULTIPLE_KEY_FLAG

    Field is part of an index.
MYSQLI_BLOB_FLAG

    Field is defined as BLOB
MYSQLI_UNSIGNED_FLAG

    Field is defined as UNSIGNED
MYSQLI_ZEROFILL_FLAG

    Field is defined as ZEROFILL
MYSQLI_AUTO_INCREMENT_FLAG

    Field is defined as AUTO_INCREMENT
MYSQLI_TIMESTAMP_FLAG

    Field is defined as TIMESTAMP
MYSQLI_SET_FLAG

    Field is defined as SET
MYSQLI_NUM_FLAG

    Field is defined as NUMERIC
MYSQLI_PART_KEY_FLAG

    Field is part of an multi-index
MYSQLI_GROUP_FLAG

    Field is part of GROUP BY
MYSQLI_TYPE_DECIMAL

    Field is defined as DECIMAL
MYSQLI_TYPE_NEWDECIMAL

    Precision math DECIMAL or NUMERIC field (MySQL 5.0.3 and up)
MYSQLI_TYPE_BIT

    Field is defined as BIT (MySQL 5.0.3 and up)
MYSQLI_TYPE_TINY

    Field is defined as TINYINT
MYSQLI_TYPE_SHORT

    Field is defined as SMALLINT
MYSQLI_TYPE_LONG

    Field is defined as INT
MYSQLI_TYPE_FLOAT

    Field is defined as FLOAT
MYSQLI_TYPE_DOUBLE

    Field is defined as DOUBLE
MYSQLI_TYPE_NULL

    Field is defined as DEFAULT NULL
MYSQLI_TYPE_TIMESTAMP

    Field is defined as TIMESTAMP
MYSQLI_TYPE_LONGLONG

    Field is defined as BIGINT
MYSQLI_TYPE_INT24

    Field is defined as MEDIUMINT
MYSQLI_TYPE_DATE

    Field is defined as DATE
MYSQLI_TYPE_TIME

    Field is defined as TIME
MYSQLI_TYPE_DATETIME

    Field is defined as DATETIME
MYSQLI_TYPE_YEAR

    Field is defined as YEAR
MYSQLI_TYPE_NEWDATE

    Field is defined as DATE
MYSQLI_TYPE_INTERVAL

    Field is defined as INTERVAL
MYSQLI_TYPE_ENUM

    Field is defined as ENUM
MYSQLI_TYPE_SET

    Field is defined as SET
MYSQLI_TYPE_TINY_BLOB

    Field is defined as TINYBLOB
MYSQLI_TYPE_MEDIUM_BLOB

    Field is defined as MEDIUMBLOB
MYSQLI_TYPE_LONG_BLOB

    Field is defined as LONGBLOB
MYSQLI_TYPE_BLOB

    Field is defined as BLOB
MYSQLI_TYPE_VAR_STRING

    Field is defined as VARCHAR
MYSQLI_TYPE_STRING

    Field is defined as CHAR or BINARY
MYSQLI_TYPE_CHAR

    Field is defined as TINYINT. For CHAR, see MYSQLI_TYPE_STRING
MYSQLI_TYPE_GEOMETRY

    Field is defined as GEOMETRY
MYSQLI_NEED_DATA

    More data available for bind variable
MYSQLI_NO_DATA

    No more data available for bind variable
MYSQLI_DATA_TRUNCATED

    Data truncation occurred. Available since PHP 5.1.0 and MySQL 5.0.5.
MYSQLI_ENUM_FLAG

    Field is defined as ENUM. Available since PHP 5.3.0.
MYSQLI_BINARY_FLAG

    Field is defined as BINARY. Available since PHP 5.3.0.
MYSQLI_CURSOR_TYPE_FOR_UPDATE

MYSQLI_CURSOR_TYPE_NO_CURSOR

MYSQLI_CURSOR_TYPE_READ_ONLY

MYSQLI_CURSOR_TYPE_SCROLLABLE

MYSQLI_STMT_ATTR_CURSOR_TYPE

MYSQLI_STMT_ATTR_PREFETCH_ROWS

MYSQLI_STMT_ATTR_UPDATE_MAX_LENGTH

MYSQLI_SET_CHARSET_NAME

MYSQLI_REPORT_INDEX

    Report if no index or bad index was used in a query.
MYSQLI_REPORT_ERROR

    Report errors from mysqli function calls.
MYSQLI_REPORT_STRICT

    Throw a mysqli_sql_exception for errors instead of warnings.
MYSQLI_REPORT_ALL

    Set all options on (report all).
MYSQLI_REPORT_OFF

    Turns reporting off.
MYSQLI_DEBUG_TRACE_ENABLED

    Is set to 1 if mysqli_debug() functionality is enabled.
MYSQLI_SERVER_QUERY_NO_GOOD_INDEX_USED

MYSQLI_SERVER_QUERY_NO_INDEX_USED

MYSQLI_REFRESH_GRANT

    Refreshes the grant tables.
MYSQLI_REFRESH_LOG

    Flushes the logs, like executing the FLUSH LOGS SQL statement.
MYSQLI_REFRESH_TABLES

    Flushes the table cache, like executing the FLUSH TABLES SQL statement.
MYSQLI_REFRESH_HOSTS

    Flushes the host cache, like executing the FLUSH HOSTS SQL statement.
MYSQLI_REFRESH_STATUS

    Reset the status variables, like executing the FLUSH STATUS SQL statement.
MYSQLI_REFRESH_THREADS

    Flushes the thread cache.
MYSQLI_REFRESH_SLAVE

    On a slave replication server: resets the master server information, and restarts the slave. Like executing the RESET SLAVE SQL statement.
MYSQLI_REFRESH_MASTER

    On a master replication server: removes the binary log files listed in the binary log index, and truncates the index file. Like executing the RESET MASTER SQL statement.
MYSQLI_TRANS_COR_AND_CHAIN

    Appends "AND CHAIN" to mysqli_commit() or mysqli_rollback().
MYSQLI_TRANS_COR_AND_NO_CHAIN

    Appends "AND NO CHAIN" to mysqli_commit() or mysqli_rollback().
MYSQLI_TRANS_COR_RELEASE

    Appends "RELEASE" to mysqli_commit() or mysqli_rollback().
MYSQLI_TRANS_COR_NO_RELEASE

    Appends "NO RELEASE" to mysqli_commit() or mysqli_rollback().


 * 
 * 
 * 
 * 17 okt 2012: TDBPreparedStatementMySQL: werkt nu
 * 12 feb 2013: TDBPreparedStatementMySQL: decided to emulate, because of performance and binding-issue (binding anly possible with 1 bind_param() statement)
 * 12 feb 2013: TDBPreparedStatementMySQL: emulatie werkt
 * 12 feb 2013: TDBPreparedStatementMySQL: update() werkt nu
 * 10 apr 2013: TDBPreparedStatementMySQL: generateSelect, generateWhere van TDBQueryMySQL gekregen
 * 10 apr 2013: TDBPreparedStatementMySQL: heeft nu de insert, update en delete van TDBQueryMySQL gekregen. de ? vervangen is eruit gehaald
 * 9 mrt 2014: TDBPreparedStatementMySQL: generateSQLWhere() aangepast voor TInteger, TDateTime en TFloat
 * 4 mei 2014: TDBPreparedStatementMySQL: generateSQLWhere() aangepast voor TCurrency en TDecimal
 * 4 mei 2014: TDBPreparedStatementMySQL: parseSQL() aangepast voor TInteger, TFloat, TDecimal en TCurrency
 * 4 mei 2014: TDBPreparedStatementMySQL: insert() aangepast voor CT_DECIMAL en CT_CURRENCY
 * 4 mei 2014: TDBPreparedStatementMySQL: update() aangepast voor CT_DECIMAL en CT_CURRENCY
 * 5 aug 2014: TDBPreparedStatementMySQL: getSafeFieldName() beter gefilterd op SQL injection
 * 5 aug 2014: TDBPreparedStatementMySQL: getSafeDatabaseName() beter gefilterd op SQL injection
 * 5 aug 2014: TDBPreparedStatementMySQL: generateSQLSelect() beter gefilterd op SQL injection sortorder (ASC/DESC)
 * 7 aug 2014: TDBPreparedStatementMySQL: executedbquery() ondersteunt limit
  * 4 apr 2015: TDBPreparedStatementMySQL:optimalisaties door count uit loop te halen
  * 8 apr 2015: TDBPreparedStateMentMySQL: generateSQLCount() aangepast voor tabelnaam voor count (ivm ambigious id fields)
  * 8 apr 2015: TDBPreparedStateMentMySQL: optimalisaties in getSaveFieldname getSafeDatabaseName, getSafeTableName. de filterSQL functie is vervangen door de preg_replace die uit testen 10x sneller blijkt te zijn
  *   8 apr 2015: TDBQueryMySQL en TDBPreparedStateMentMySQL loose check database fields generateSQLSelect()
  *   8 apr 2015: TDBPreparedStateMentMySQL: de functies van TDBQueryMySQL die nog niet geimplementeerd waren zijn nu gekopieerd vanuit TDBQueryMySQL
  * 8 apr 2015: TDBPreparedStatement: verschillende compatibility probleempjes opgelost getInternalStamentObject  vervangen door  getInternalConnObject (statementobject werd niet meer gebruikt)
  * 7 mei 2015: TDBPreparedStateMentMySQL: generateSQLCount() versnelling bij joins, omdat deze dan niet count(*) maar count(idfield) genereert
  * 10 mei 2015: TDBPreparedStateMentMySQL: verschillende functie gebruiken nu de global regex variablen
  * 26 juni 2015:    TDBPreparedStateMentMySQL: aanpasassingen in bool conversie. dit werkt nu middels casts
  * 11 juli 2015: createTable() unique toegevoegd
  * 29 okt 2015: TDBPreparedStateMentMySQL: ondersteuning voor subqeuries in join en where clause
  * 25 apr 2016: createTableFromModel() bugfix: kon niet handelen dat er geen primary keys waren
  * 25 apr 2019: parseSQL() bugfix: if ($arrParams) toegevoegd. gaf fout als geen parameter aanwezig
  * 4 mei 2019: parseSQL() generateSQLWhereModel(): find() van de model gaf sql fout bij boolean. extra boolToInt() toegevoegd 
  * 8 mei 2019: generateSQLSelectModel() alias with AS missed a space
  * 10 mei 2019: bugfix: getSearchableFields  van externe tabllen werd aangeroepen ipv getFieldsSearchable
  * 13 sept 2019: updated: TDBPreparedStatementMySQL->deleteFromDB() returns now false if there where no records deleted, like in case of a record lock
  * 13 sept 2019: TDBPreparedStatementMySQL:  getAffectedRows() removed because it was too mysql specific
  * 10 jan 2020: TDBPreparedStatementMySQL:  generateSQLWhere() en andere methodes aangepast voor support zero timestamp
 * 18jan2020: TDBPreparedStatement en TDBPreparedStatementMySQL geupdate met deleteModel()!!!!
 * 18jan2020: TDBPreparedStatementMySQL updateModel() werkt nu met getWHere ipv de gedetecteerde primary keys
 * 20jan20202: Een snellere versie van TDBPreparedStatementMySQL geprogrammeerd: eerst de array samenstellen en daarna in 1 keer in TSysModel inserten
* 21 jan 2020: TDBPreparedStatementMySQL: deleteFromDBModel() heeft parameter $bCheckAffectedRows gekregen
* 23 jan 2020: TDBPreparedStatementMySQL: generateSelect() ondersteuning for join-inner-table (not always is current table of model assumed)
* 31 okt 2020: TDBPreparedStatementMySQL: find like  aangepast in generateSQLWhereModel() je meot nu expliciet de wildcards opgeven (behalve bij 'sentences')
* 2 dec 2020: TDBPreparedStatementMySQL: added functions: alterTableDrop, alterTableRenameField, alterTableModify, alterTableAdd
* 16 nov 2022: TDBPreparedStatementMySQL: generateSQLSelectModel(): Joins section updated. Now supports tables that have 2 or more references to the same exernal table. For example tblContacts has iDeliveryCountryID and iBillingCountryID both referring to tblCountries
* 18 nov 2022: TDBPreparedStatementMySQL: \n replaced for space( ) for the sake of logfiles.
* 18 nov 2022: TDBPreparedStatementMySQL: logSQL logt nu status (success or failed)
* 26 jul 2023: TDBPreparedStatementMySQL: getSafeDatabaseName() getSafeFieldName() updated not to use REGEX_ALPHANUMERIC_UNDERSCORE for security reasons
* 30 okt 2023: TDBPreparedStatementMySQL: createTableFromModel():  INDEX(field, field) aangemaakt
* 31 okt 2023: TDBPreparedStatementMySQL: createTableFromModel():  INDEX(field), INDEX (field) is now repeated instead of comma'd
* 21 apr 2024: TDBPreparedStatementMySQL: generateSQLColumnsTableManipulation() strlen() werkte niet meer met null in php8
* 22 apr 2024: TDBPreparedStatementMySQL: generateSQLColumnsTableManipulation() betere defaults. vooral voor datums.
* 22 apr 2024: TDBPreparedStatementMySQL: generateSQLColumnsTableManipulation() datums zijn nu null by default, and nullable! dit was voorheen 0 wat problemen gaf in mysql
* 5 dec 2024: TDBPreparedStatementMySQL: generateSQLColumnsTableManipulation() ==> generateSQLColumnsDataManipulation
* 5 dec 2024: TDBPreparedStatementMySQL: aanpassingen blob, binary en ipadres. deze worden standaard geconverteerd naar hexadecimaal
 * 07 jan 2025: TDBPreparedStatementMySQL: updateModel(): IMPORTANT BUGFIX  field value wasn't FILTERED!
 * 07 jan 2025: TDBPreparedStatementMySQL: updateModel(): support for autoincrement field
 * 24 apr 2025: TDBPreparedStatementMySQL: rename: parseSQL() ==> renderSQL()
 * 23 may 2025: TDBPreparedStatementMySQL: generateSQLWhereModel() support for logical operators AND and OR
 * 3 jun 2025: TDBPreparedStatementMySQL: updateField() params extended with comparison operator + functionality for comparison operator
 * 15 aug 2025: TDBPreparedStatementMySQL: support for sql DISTINCT
 * 
 * @author drenirie
 */
class TDBPreparedStatementMySQL extends TDBPreparedStatement
{
    const TIMESTAMP_ZERO_FORMAT = '0000-00-00 00:00:00';
    const REGEXALPHANUMERIC = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890'; //we can use REGEX_ALPHANUMERIC, but to prevent bugs: if we change something about the regex global, it will cause a HUGE security risk

    /**
     * prepare a SQL statement
     * 
     * @param string $sPreparedStatement
     */
    public function prepare($sPreparedStatement = '')
    {
        if ($this->getOwnerObject() instanceof TDBConnection)
        {
            $objAPIConn = $this->getOwnerObject()->getAPIConnObject();        
            
            $this->clearParameters();
            $this->setSQLPreparedStatement($sPreparedStatement);
            
            //limit toevoegen
            //let op: de limit kan 1 of 2 parameters hebben: 
            //1 parameter: is dat het aantal records wat terug moet komen
            //2 parameters: is de EERSTE de offset en de TWEEDE het aantal records wat terug moet komen --> DAT IS DUS EEN ANDERE VOLGORDE ALS DE PARAMETERS VAN DEZE FUNCTIE!!
//             if (is_numeric($iMaxResultCount) && is_numeric($iOffset)) //injection voorkomen
//             {
//                 //
//                 if (($iMaxResultCount > 0) || ($iOffset > 0)) 
//                     $sSQL .= ' LIMIT ';

//                 if ($iOffset > 0)
//                     $sSQL .= $iOffset.',';

//                 if ($iMaxResultCount > 0)
//                     $sSQL .= $iMaxResultCount;

//             }
//              removed because of emulation            
//            $this->setSQLPreparedStatement($sPreparedStatement);
//            
//            $objAPIStatement = $objAPIConn->prepare($sPreparedStatement);        
//            $this->setAPIStatementObject($objAPIStatement);
//
//            if(!$objAPIStatement) //als fout opgetreden
//            {
//                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objAPIConn->error.' : '.$sPreparedStatement, $this);
//                error_log($objAPIConn->error.' : '.$sPreparedStatement);
//            }                  
        }
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'prepare(): owner object is NOT of type TDBConnection', $this);
    }


    /**
     * bind parameters to question marks
     * 
     * 
     * @param int $ctType integer wich defines type, default string
     * @param mixed $mParameter parameter you want to add
     */
    public function bind($mParameter, $ctType = CT_VARCHAR)
    {
//        $objAPI = $this->getAPIStatementObject();
        
//        if ($objAPI != null)
//        {
            $this->addParameter($mParameter, $ctType);                            
//        }
//        else
//            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'bind(): API object is null, maybe an SQL error in your prepared statement ? Or maybe you didnt call prepare() before bind()', $this);
    }
    
    /**
     * count parameters
     * @return int
     */
    public function paramCount()
    {
//        $objAPI = $this->getAPIStatementObject();
//        return $objAPI->param_count;
        return count($this->getParameters());
    }
        
    /**
     * execute query
     * if a resultset is returned it's in TDBQuery-format, else null (i.e. database table creation)
     * returns also null when SQL syntax query (errors will be logged)
     * 
     * @param mixed $mSQL can be a string with a SQL statement, or '' when you set the prepared statement already by prepare
     * @param int $iMaxResultCount
     * @param int $iOffset
     * @return TDBResultset null when error
     */
    public function executeQuery($sSQL = '', $iMaxResultCount = 0, $iOffset = 0)
    {              
        //connect
        if (!$this->objConn->isConnected())
            $this->objConn->connect();

        //after connect            
        $objMySQLAPI = $this->getAPIConnObject(); 


        //SQL maken
        if ($sSQL != '')
            $this->setSQLPreparedStatement($sSQL);
        $sSQL = $this->renderSQL();
        $this->setLastSQLQuery($sSQL);//laatste query updaten:
        
        
        //limit toevoegen
        //let op: de limit kan 1 of 2 parameters hebben: 
        //1 parameter: is dat het aantal records wat terug moet komen
        //2 parameters: is de EERSTE de offset en de TWEEDE het aantal records wat terug moet komen --> DAT IS DUS EEN ANDERE VOLGORDE ALS DE PARAMETERS VAN DEZE FUNCTIE!!
        if (is_numeric($iMaxResultCount) && is_numeric($iOffset)) //injection voorkomen
        {
            //
            if (($iMaxResultCount > 0) || ($iOffset > 0)) 
                $sSQL .= ' LIMIT ';
            
            if ($iOffset > 0)
                $sSQL .= $iOffset.',';
            
            if ($iMaxResultCount > 0)
                $sSQL .= $iMaxResultCount;
            
        }                 
        
        
        // logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL);
        if (APP_DEBUGMODE)
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'upcoming query to execute');

        if ($objTempMySQLResult = $objMySQLAPI->query($sSQL)) 
        {                               
            $this->setExecutionSQLOK(true);
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'success');
            
            if (is_object($objTempMySQLResult)) //returns a resultset or not ?
            {
                $objReturnedResultset = new TDBResultsetMySQL($this);

                $iRecordcount = 0;
                //while($arrRow = $objTempMySQLResult->fetch_assoc()) 
                while($arrRow = $objTempMySQLResult->fetch_array(MYSQLI_BOTH)) 
                {
                    $objReturnedResultset->addAssoc($arrRow);
                    ++$iRecordcount;
                }

                //extra info over de velden
                $arrInfoFields = $objTempMySQLResult->fetch_fields();
                foreach ($arrInfoFields as $mFieldInfo) 
                {
                    
                    $objReturnedResultset->addColumnName($mFieldInfo->name);
                    
                    //translating mysql types to our own
                    switch ($mFieldInfo->type)
                    {
                        case(MYSQLI_TYPE_DECIMAL):
                            $objReturnedResultset ->addColumnType(CT_DECIMAL);
                            break;
                        case(MYSQLI_TYPE_NEWDECIMAL):
                            $objReturnedResultset ->addColumnType(CT_DECIMAL);
                            break;
                        case(MYSQLI_TYPE_BIT):
                            $objReturnedResultset ->addColumnType(CT_BOOL);
                            break;
                        case(MYSQLI_TYPE_TINY):
                            $objReturnedResultset ->addColumnType(CT_INTEGER);
                            break;
                        case(MYSQLI_TYPE_SHORT):
                            $objReturnedResultset ->addColumnType(CT_INTEGER);
                            break;
                        case(MYSQLI_TYPE_LONG):
                            $objReturnedResultset ->addColumnType(CT_INTEGER);
                            break;
                        case(MYSQLI_TYPE_FLOAT):
                            $objReturnedResultset ->addColumnType(CT_FLOAT);
                            break;
                        case(MYSQLI_TYPE_DOUBLE):
                            $objReturnedResultset ->addColumnType(CT_DOUBLE);
                            break;
                        case(MYSQLI_TYPE_NULL):
                            $objReturnedResultset ->addColumnType(CT_VARCHAR);
                            break;                            
                        case(MYSQLI_TYPE_TIMESTAMP):
                            $objReturnedResultset ->addColumnType(CT_INTEGER64);
                            break;                             
                        case(MYSQLI_TYPE_LONGLONG):
                            $objReturnedResultset ->addColumnType(CT_INTEGER64);
                            break;                             
                        case(MYSQLI_TYPE_INT24):
                            $objReturnedResultset ->addColumnType(CT_INTEGER);
                            break;                             
                        case(MYSQLI_TYPE_TIME):
                            $objReturnedResultset ->addColumnType(CT_DATETIME);
                            break;                             
                        case(MYSQLI_TYPE_DATETIME):
                            $objReturnedResultset ->addColumnType(CT_DATETIME);
                            break;                             
                        case(MYSQLI_TYPE_YEAR):
                            $objReturnedResultset ->addColumnType(CT_INTEGER);
                            break;                                     
                        case(MYSQLI_TYPE_NEWDATE):
                            $objReturnedResultset ->addColumnType(CT_DATETIME);
                            break;        
                        case(MYSQLI_TYPE_INTERVAL):
                            $objReturnedResultset ->addColumnType(CT_DATETIME);
                            break;                         
                        case(MYSQLI_TYPE_ENUM):
                            $objReturnedResultset ->addColumnType(CT_ENUM);
                            break;                         
                        case(MYSQLI_TYPE_SET):
                            $objReturnedResultset ->addColumnType(CT_VARCHAR);
                            break;                         
                        case(MYSQLI_TYPE_TINY_BLOB):
                            $objReturnedResultset ->addColumnType(CT_BLOB);
                            break;                         
                        case(MYSQLI_TYPE_MEDIUM_BLOB):
                            $objReturnedResultset ->addColumnType(CT_BLOB);
                            break;                         
                        case(MYSQLI_TYPE_LONG_BLOB):
                            $objReturnedResultset ->addColumnType(CT_BLOB);
                            break;                         
                        case(MYSQLI_TYPE_BLOB):
                            $objReturnedResultset ->addColumnType(CT_BLOB);
                            break;                         
                        case(MYSQLI_TYPE_VAR_STRING):
                            $objReturnedResultset ->addColumnType(CT_VARCHAR);
                            break;                         
                        case(MYSQLI_TYPE_STRING):
                            $objReturnedResultset ->addColumnType(CT_VARCHAR);
                            break;                         
                        case(MYSQLI_TYPE_CHAR):
                            $objReturnedResultset ->addColumnType(CT_VARCHAR);
                            break;                                               
                    }
                    
                }                
                
                $objTempMySQLResult->close();/* free result set */
                

                $this->setResultset($objReturnedResultset);                
                return $objReturnedResultset;
            }
            else
            {
                $this->setResultset(null);
                return null; //return no resultset
            }

        }        
        else
        {
            $this->setExecutionSQLOK(false);
            $this->setResultset(null);

            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQLAPI->error.' : '.$sSQL);
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'FAILED !!!');
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQLAPI->error, 'ERRORMSG');    

            return null; //return no resultset
        }
        
        

        
    }
    
    /**
     * execute query: when you expect no resultset i.e. delete, insert, update...
     * 2 sept 2015: new method in stead of executeQuery() - for using with TSysModel
     * 
     * @param string $sSQL plain sql
     */
    protected function execute($sSQL = '')
    {   	
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();

            //after connect            
            $objMySQLAPI = $this->getAPIConnObject();    

	    	//SQL maken
	    	if ($sSQL != '')
	    		$this->setSQLPreparedStatement($sSQL);
	    	$sSQL = $this->renderSQL();
	    	$this->setLastSQLQuery($sSQL);//laatste query updaten:
  	    	
	    	
	    	// logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL);
            if (APP_DEBUGMODE)
                logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'upcoming query to execute');            

	    	if ($objMySQLAPI->query($sSQL) !== false)
	    	{
	    		$this->setExecutionSQLOK(true);
                logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'success');
	    		return true;
	    	}    	
	    	else
	    	{
	    		$this->setExecutionSQLOK(false);
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQLAPI->error.' : '.$sSQL);
                logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'FAILED !!!');
                logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQLAPI->error, 'ERRORMSG');    
	    		return false;
	    	}
    }
    
    /**
     * execute a select query (you expect a resultset)
     * The results are stored in the TSysModel object (you supply as parameter)
     * 2 sept 2015: new method in stead of executeQuery() - for using with TSysModel
     * 
     * the result is stored in the TSysModel object
     * 
     * @param TSysModel $objModel
     */
    public function executeSelect(TSysModel &$objModel)
    {	
        //connect
        if (!$this->objConn->isConnected())
            $this->objConn->connect();        

        //after connect            
        $objMySQLAPI = $this->getAPIConnObject(); 

	    //constructing SELECT SQL query if TSysModel object
    	$sSQL = $this->generateSQLSelectModel($objModel);
		if (!$sSQL)//on error, returns null
    	{
            $this->setResultset(null);
            return false; //return no resultset
    	}	    	
	    	
	    	
        //make SQL 
        if ($sSQL != '')
            $this->setSQLPreparedStatement($sSQL);
        $sSQL = $this->renderSQL();
        $this->setLastSQLQuery($sSQL);//update last query	    	 	
    	
	    	
        // logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL);  
        
        if (APP_DEBUGMODE)
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'upcoming query to execute');           

        if ($objTempMySQLResult = $objMySQLAPI->query($sSQL))
        {
            $this->setExecutionSQLOK(true);
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, ''.$sSQL, 'success');    
        
            if (is_object($objTempMySQLResult)) //returns a resultset or not ?
            {
                $arrInfoFields = $objTempMySQLResult->fetch_fields();

                //new internal array for TSysModel 20 jan 2020: VERSION 3
                
                $arrModelRows = array();
                $arrModelRow = array();
                $iCountRows = $objTempMySQLResult->num_rows;
                $iCountCols = $objTempMySQLResult->field_count;
                $iColType = 0;
                
                //structure array: $arrData[tablename][fieldname][value ..]
                $sModelArrayKeyValue = TSysModel::DATA_VALUE;
                $sModelArrayKeyFieldnameOriginal = TSysModel::DATA_FIELDNAMEORIGINAL;
                $sModelArrayKeyTablename = TSysModel::DATA_TABLENAME;
                $sModelArrayKeyTablenameOriginal = TSysModel::DATA_TABLENAMEORIGINAL;
                
                $sModelFieldNew = TSysModel::FIELD_SYS_NEW;
                $sModelFieldDirty = TSysModel::FIELD_SYS_DIRTY;
                
                $sFieldValue = '';
                $sFieldName = '';
                $sFieldNameOriginal = '';
                $sTableName = '';
                $sTableNameOriginal = '';
                $sTableNameEmptyIfSameAsModel = ''; //is table, except if its the current table from TSysModel --> this makes searching for the default table in TSysModel faster
                $sTableNameFromModel = $objModel::getTable();
                
                $objFieldInfo = null;

                for ($iRowIndex = 0; $iRowIndex < $iCountRows; $iRowIndex++)
                {
                    $arrMySQLRow = $objTempMySQLResult->fetch_row();
                    
                    //going through fields and replace by special types like dates and decimals
                    $iColIndex = -1;

                    foreach ($arrInfoFields as $objFieldInfo) //by accident the fieldinfo order equals the column index order
                    {
                        $iColIndex++;
                        $iColType = $objFieldInfo->type;


                        switch ($iColType)
                        {
                            case(MYSQLI_TYPE_DECIMAL):
                                $sFieldValue = new TDecimal($arrMySQLRow[$iColIndex], $objFieldInfo->decimals);
                                break;
                            case(MYSQLI_TYPE_NEWDECIMAL):
                                $sFieldValue =  new TDecimal($arrMySQLRow[$iColIndex], $objFieldInfo->decimals);
                                break;
                            //case(MYSQLI_TYPE_BIT):
                            //	break;
                            case(MYSQLI_TYPE_TINY): //also CT_BOOLEAN
                                if ($objModel->getFieldType($objFieldInfo->name) == CT_BOOL)
                                    $sFieldValue = (bool)$arrMySQLRow[$iColIndex];
                                else
                                    $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_SHORT):
                                $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_LONG):
                                $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_FLOAT):
                                $sFieldValue = (float)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_DOUBLE):
                                $sFieldValue = (double)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_NULL):
                                $sFieldValue = null;
                                break;
                            case(MYSQLI_TYPE_TIMESTAMP):
                                $iTimeStamp = 0;                                                             
                                if (($arrMySQLRow[$iColIndex] != null) && ($arrMySQLRow[$iColIndex] != TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT))
                                {
                                    $arrDT = date_parse_from_format('Y-m-d H:i:s' , $arrMySQLRow[$iColIndex]);                                                                    
                                    if ($arrDT['warning_count'] == 0)
                                        $iTimeStamp = mktime($arrDT['hour'], $arrDT['minute'], $arrDT['second'], $arrDT['month'], $arrDT['day'], $arrDT['year']);                                                                    
                                }
                                $sFieldValue = new TDateTime($iTimeStamp);            						                                                                
                                break;
                            case(MYSQLI_TYPE_LONGLONG):
                                $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_INT24):
                                $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;
                            //case(MYSQLI_TYPE_TIME):
                            //	break;	    							
                            case(MYSQLI_TYPE_DATETIME):
                                $iTimeStamp = 0; 
                                if (($arrMySQLRow[$iColIndex] != null) && ($arrMySQLRow[$iColIndex] != TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT))
                                {
                                    $arrDT = date_parse_from_format('Y-m-d H:i:s' , $arrMySQLRow[$iColIndex]);	    							   							
                                    if ($arrDT['warning_count'] == 0)
                                        $iTimeStamp = mktime($arrDT['hour'], $arrDT['minute'], $arrDT['second'], $arrDT['month'], $arrDT['day'], $arrDT['year']);
                                }
                                $sFieldValue = new TDateTime($iTimeStamp);            						
                                break;
                            case(MYSQLI_TYPE_YEAR):
                                $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;
                            case(MYSQLI_TYPE_VAR_STRING):  //https://www.php.net/manual/en/mysqli.constants.php: Field is defined as CHAR or BINARY.                         
                                if (($objFieldInfo->flags & MYSQLI_BINARY_FLAG) == MYSQLI_BINARY_FLAG) //https://www.php.net/manual/en/mysqli.constants.php: Field is defined as BINARY.  bitwise flag comparison. but the flag is sometimes binary on string fields like date
                                {
                                    if ($objModel->getFieldType($objFieldInfo->name) == CT_IPADDRESS)
                                        $sFieldValue = inet_ntop($arrMySQLRow[$iColIndex]);
                                    else
                                        $sFieldValue = $arrMySQLRow[$iColIndex];
                                }
                                else
                                    $sFieldValue = $arrMySQLRow[$iColIndex];
                                break;  
                            case(MYSQLI_TYPE_YEAR):
                                $sFieldValue = (int)$arrMySQLRow[$iColIndex];
                                break;                                                              
                            //case(MYSQLI_TYPE_NEWDATE):
                            //	break;
                            //case(MYSQLI_TYPE_INTERVAL):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_ENUM):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_SET):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_TINY_BLOB):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_MEDIUM_BLOB):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_LONG_BLOB):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_BLOB):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_VAR_STRING):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_STRING):
                            //	
                            //	break;
                            //case(MYSQLI_TYPE_CHAR):
                            //	
                            //	break;
                            default:
                                $sFieldValue = $arrMySQLRow[$iColIndex];//default
                            //alle string achtige types hoeven niet expliciet geconverteerd te worden, ze staan al goed in de array
                        }//einde switch
                        
                                            
                        $sFieldName = $objFieldInfo->name;
                        $sFieldNameOriginal = $objFieldInfo->orgname;
                        $sTableName = $objFieldInfo->table;
                        $sTableNameOriginal = $objFieldInfo->orgtable;                                                
                        if ($sTableName == $sTableNameFromModel)
                            $sTableNameEmptyIfSameAsModel = '';
                        else
                            $sTableNameEmptyIfSameAsModel = $sTableName;
                        $arrModelRow[$sTableNameEmptyIfSameAsModel][$sFieldName][$sModelArrayKeyValue] = $sFieldValue;
                        $arrModelRow[$sTableNameEmptyIfSameAsModel][$sFieldName][$sModelArrayKeyFieldnameOriginal] = $sFieldNameOriginal;                                                
                        $arrModelRow[$sTableNameEmptyIfSameAsModel][$sFieldName][$sModelArrayKeyTablename] = $sTableName;
                        $arrModelRow[$sTableNameEmptyIfSameAsModel][$sFieldName][$sModelArrayKeyTablenameOriginal] = $sTableNameOriginal;
                                            
                                            
                    }//end: for fields
                                    
                    //flag: new
                    $arrModelRow[''][$sModelFieldNew][$sModelArrayKeyValue] = false;
                    $arrModelRow[''][$sModelFieldNew][$sModelArrayKeyFieldnameOriginal] = $sModelFieldNew;
                    $arrModelRow[''][$sModelFieldNew][$sModelArrayKeyTablenameOriginal] = $sTableNameFromModel;
                    
                    //flag: dirty
                    $arrModelRow[''][$sModelFieldDirty][$sModelArrayKeyValue] = false;
                    $arrModelRow[''][$sModelFieldDirty][$sModelArrayKeyFieldnameOriginal] = $sModelFieldDirty;
                    $arrModelRow[''][$sModelFieldDirty][$sModelArrayKeyTablenameOriginal] = $sTableNameFromModel;
                                                            
                    $arrModelRows[] = $arrModelRow;
                                    
                }//end: for rows  

                $objModel->setInternalDataNew($arrModelRows);
                                        
                $objModel->resetRecordPointer();
                $objTempMySQLResult->close();/* free result set */
        
                $this->setResultset(null);
                return true; //everything was ok
            }
            else
            {
                $objModel->resetRecordPointer();
                $this->setResultset(null);
                return true;  //everything was ok
            }
        
        }
        else
        {
            $this->setExecutionSQLOK(false);
            $this->setResultset(null);
        
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQLAPI->error.' : '.$sSQL, $this);
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sSQL, 'FAILED !!!');    
            logSQL(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQLAPI->error, 'ERRORMSG');    

            return false; //not ok
        }    	
    }
        
    
    /**
     * close statement
     */
    public function close()
    {
        //$this->getAPIStatementObject()->close();
    }
        
    

    
    /**
     * the querybuilder-less function
     * 
     * @param string $sTable
     * @param string $sField, table field
     * @param mixed $mHasValue can be of different types (string, integer etc)
     * @param string $sComparisonOperator
     * @param string $sField2, you can specify a second field 
     * @param mixed $mHasValue2 can be of different types (string, integer etc), you can specify a second value
     * @param string $sComparisonOperator2, you can specify a second operator
     */
    public function deleteFromDB($sTable, $sField, $mHasValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $sField2 = '', $mHasValue2 = '', $sComparisonOperator2 = COMPARISON_OPERATOR_EQUAL_TO)
    {
        try
        {
            //SQL maken
            $sSQL  = 'DELETE FROM '.$this->getSafeTableName($sTable).' ';	    		
            $sSQL .= 'WHERE '.$this->getSafeFieldName($sField).' '.$this->translateComparisonOperator($sComparisonOperator).' \''.$this->getSafeFieldValue($mHasValue).'\'';
                    
            if ($sField2 != '') //use second field??
            {
                $sSQL .= 'AND '.$this->getSafeFieldName($sField2).' '.$this->translateComparisonOperator($sComparisonOperator2).' \''.$this->getSafeFieldValue($mHasValue2).'\'';
            }
            
            if (!$this->execute($sSQL))
                return false; //sql error
                
            $objStat = $this->getAPIConnObject();
            return ($objStat->affected_rows > 0);//determine if delete was successful by the amount of affected rows

        }
        catch (Exception $objException)
        {
            $this->setExecutionSQLOK(false);
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }    		
    }
    
    
    /**
     * delete from database according to where conditions given in TSysModel
     * 
     * @param TSysModel $objModel
     * @param boolean $bYesISpecifiedAWhereInMyModel to prevent you from deleting the contents of the whole table by accident
     * @param boolean $bCheckAffectedRows if true this function checks if any records were deleted
     * @return boolean ok= true 
     */
    public function deleteModel(&$objModel, $bYesISpecifiedAWhereInMyModel, $bCheckAffectedRows = false)
    {
        if ($bYesISpecifiedAWhereInMyModel)
        {
            try
            {
                //make SQL 
                $sSQL  = 'DELETE FROM '.$this->getSafeTableName($objModel::getTable()).' ';	    		
                $sSQL .= $this->generateSQLWhereModel($objModel);

                if (!$this->execute($sSQL))
                    return false; //sql error

                if ($bCheckAffectedRows)
                {
                    $objStat = $this->getAPIConnObject();
                    return ($objStat->affected_rows > 0);//determine if delete was successful by the amount of affected rows
                }
                else
                    return true;

            }
            catch (Exception $objException)
            {
                $this->setExecutionSQLOK(false);
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
                return false;
            }   
        }
        else
            error_log(__CLASS__.' '.__FUNCTION__.': you didnt specify a WHERE in your model');
        
        return false;
    }

    /**
     * creates a database with default utf collation
     * @param type $sDatabaseName 
     */    
    public function createDatabase($sDatabaseName)
    {
        try
        {
            $sSQL =  'CREATE DATABASE '.$this->getSafeDatabaseName($sDatabaseName).' ';
            $sSQL .= 'DEFAULT CHARACTER SET utf8'.' ';
            $sSQL .= 'DEFAULT COLLATE utf8_general_ci;'.' ';

            $this->executeQuery($sSQL);
            return $this->getExecutionSQLOK();
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }    
    }



    /**
     * does database schema exist ?
    * 
    * @param string $sSchema
    */
    public function databaseExists($sSchema)
    {
        if ($sSchema == '')
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Database schema parameter is empty');
            return false;
        }

        try
        {
            //connect
            if (!$this->objConn->isConnected())
            $this->objConn->connect();
        
            //after connect            
            $objMySQL = $this->getAPIConnObject();

            //create SQL
            $sSQL = 'SELECT SCHEMA_NAME
                    FROM INFORMATION_SCHEMA.SCHEMATA
                    WHERE SCHEMA_NAME = \''.$this->getSafeDatabaseName ($sSchema).'\'';


            //execute
            if ($objTempMySQLResult = $objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                if (is_object($objTempMySQLResult)) //returns a resultset or not ?
                {                   
                    while($objTempMySQLResult->fetch_array(MYSQLI_NUM))
                    {
                        $objTempMySQLResult->close();/* free result set */
                        return true;                        
                    }

                    return false;
                }
                else
                {
                    return false; //return no resultset
                }

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return false; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }  
    }

    /**
     * creating a database table wich is defined in TSysModel with utf 8 collation
     *
     * 
     * @param TSysModel $objModel
     * @return bool success?
     */
    public function createTableFromModel(TSysModel &$objModel)
    {
	    	$objConnection = $this->getConnectionObjectParent();
	    	$arrSQLColumns = array();
	    	$arrColumnNames = $objModel->getFieldsDefined();
	    	
	    	try
	    	{
	    		$sSQL = 'CREATE TABLE '.$objConnection->getPreparedStatement()->getSafeTableName($objModel::getTable()).' (';

	    		//==== LOOP COLUMNS
	    		foreach ($arrColumnNames as $sColumnName)
	    		{    			              
                    $arrSQLColumns[] = $this->generateSQLColumnsDataRefactor($objModel, $sColumnName);
	    		} //END: LOOP COLUMNS
	    
	    		//==== PRIMARY KEYS
	    		$arrPrimKeys = array();
	    		$bPrimKeyExists = false;
	    		$sPrimKey = 'PRIMARY KEY (';
	    		foreach ($arrColumnNames as $sColumnName)
	    		{
	    			//$objCurrCol = $objDBTable->getColumn($sColumnName);
	    			if ($objModel->getFieldPrimaryKey($sColumnName))
	    			{
	    				$arrPrimKeys[] = $this->getSafeFieldName($sColumnName);
	    				$bPrimKeyExists = true;
	    			}
	    		}
	    		$sPrimKey .= implode(', ', $arrPrimKeys);
	    		$sPrimKey .= ') ';
	    		if ($bPrimKeyExists) //only add if primary keys are present
	    			$arrSQLColumns[] .= $sPrimKey;
	    		//END: PRIMARY KEYS
	    

	    		//==== INDICES
	    		$arrIndexCols = array();
	    		$bIndexColExists = false;
	    		$sIndexCol = '';
	    		foreach ($arrColumnNames as $sColumnName)
	    		{	    			
	    			if ($objModel->getFieldIndexed($sColumnName))
	    			{
	    				$arrIndexCols[] = 'INDEX ('.$this->getSafeFieldName($sColumnName).')';
	    				$bIndexColExists = true;
	    			}
	    		}
	    		$sIndexCol .= implode(', ', $arrIndexCols);
	    		if ($bIndexColExists) //only add if indexed columns are present
	    			$arrSQLColumns[] .= $sIndexCol;
	    		//END: INDICES

	    		//==== FULLTEXT columns
                //we place a fulltext-index on the column itself, and one on all columns
                //be aware that the order of fields is important. 
                //if they are created in a certain order, they need to be queried in the exact same order!
	    		$arrFulltextColsAll = array();
	    		$bFulltextColExists = false;
                $arrColumnNamesSearchFields = $objModel->getFieldsSearchable();//we take the order of the search fields to avoid any order conflicts when querying
                $arrColumnNamesSearchFields = array_unique($arrColumnNamesSearchFields); //filter duplicates to prevent error
	    		foreach ($arrColumnNamesSearchFields as $sColumnName)
	    		{	    			
	    			if ($objModel->getFieldFulltext($sColumnName))
	    			{
	    				$arrSQLColumns[] = 'FULLTEXT ('.$this->getSafeFieldName($sColumnName).')'; //index on each column individually
	    				$arrFulltextColsAll[] = $this->getSafeFieldName($sColumnName); //collect for all column-index
	    				$bFulltextColExists = true;
	    			}
	    		}	    		
	    		if ($bFulltextColExists) //only add if indexed columns are present
                {
                    $arrSQLColumns[] = 'FULLTEXT ('.implode(', ', $arrFulltextColsAll).')'; //index on all columns together
                }
	    		//END: FULLTEXT columns
                                

	    
	    		//==== FOREIGN KEYS
	    		foreach ($arrColumnNames as $sColumnName)
	    		{
	    			if (($objModel->getFieldForeignKeyTable($sColumnName) != '') && ($objModel->getFieldForeignKeyField($sColumnName) != ''))
	    			{
	    				//on update action
	    				$sOnUpdate = '';
	    				switch ($objModel->getFieldForeignKeyActionOnUpdate($sColumnName))
	    				{
	    					case TSysModel::FOREIGNKEY_REFERENCE_NOACTION:
	    						$sOnUpdate = ' ON UPDATE NO ACTION';
	    						break;
	    					case TSysModel::FOREIGNKEY_REFERENCE_SETNULL:
	    						$sOnUpdate = ' ON UPDATE SET NULL';
	    						break;
	    					case TSysModel::FOREIGNKEY_REFERENCE_RESTRICT:
	    						$sOnUpdate = ' ON UPDATE RESTRICT';
	    						break;
	    					case TSysModel::FOREIGNKEY_REFERENCE_CASCADE:
	    						$sOnUpdate = ' ON UPDATE CASCADE';
	    						break;
	    				}
	    
	    				//on delete action
	    				$sOnDelete = '';
	    				switch ($objModel->getFieldForeignKeyActionOnDelete($sColumnName))
	    				{
	    					case TSysModel::FOREIGNKEY_REFERENCE_NOACTION:
	    						$sOnDelete = ' ON DELETE NO ACTION';
	    						break;
	    					case TSysModel::FOREIGNKEY_REFERENCE_SETNULL:
	    						$sOnDelete = ' ON DELETE SET NULL';
	    						break;
	    					case TSysModel::FOREIGNKEY_REFERENCE_RESTRICT:
	    						$sOnDelete = ' ON DELETE RESTRICT';
	    						break;
	    					case TSysModel::FOREIGNKEY_REFERENCE_CASCADE:
	    						$sOnDelete = ' ON DELETE CASCADE';
	    						break;
	    				}
	    
	    				$arrSQLColumns[] = 'FOREIGN KEY ('.$this->getSafeFieldName($sColumnName).') REFERENCES '.$this->getSafeFieldName($objModel->getFieldForeignKeyTable($sColumnName). '('.$objModel->getFieldForeignKeyField($sColumnName).')'.$sOnUpdate.$sOnDelete, false);
	    			}
	    		}
	    		//END FOREIGN KEYS
	    		 
	    		 
	    
	    
	    		//add columns to SQL query
	    		$sSQL .= implode(", ", $arrSQLColumns);
	    
	    
	    		$sSQL .= ') ';
	    		$sSQL .= 'ENGINE=InnoDB ';
	    		$sSQL .= 'COLLATE=utf8_general_ci ';
	    		$sSQL .= 'COMMENT=\'created:'.date('d-m-Y H:i').'\'';
	    
	    		//query execute
	    		$this->executeQuery($sSQL);
	    		return $this->getExecutionSQLOK();
	    	}
	    	catch (\Exception $objException)
	    	{
	    		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException->getMessage());
	    		return false;
	    	}
    }

    
     /**
      * verwijderen van een VOLLEDIGE database
      * @param sting $sDatabase
      * @return bool drop succesfull ?
      */    
    public function dropDatabase($sDatabase)
    {
         try
         {
            $this->executeQuery('DROP DATABASE '.$this->getSafeTableName($sDatabase).'');
            return $this->getExecutionSQLOK();
         }
         catch (Exception $objException)
         {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
         }        
    }

      /**
      * verwijderen van een VOLLEDIGE tabel uit de database
      * 
      * @return bool drop successfull
      * @param sting $sTableName
      */
    public function dropTable($sTableName)
    {
         try
         {
            $this->executeQuery('DROP TABLE '.$this->getSafeTableName($sTableName).'');
            return $this->getExecutionSQLOK();
         }
         catch (Exception $objException)
         {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
         }        
    }

    //it's too mysql specific
//    private function getAffectedRows()
//    {     
//        $objStat = $this->getAPIConnObject();
//        return $objStat->affected_rows;        
//    }
    
    /**
     * return a contents of a database as SQL statements
     *
     * @param boolean $bDropTableIfExists
     * @return string SQL dump contents as string
     */    
    public function getBackupDatabase($sDatabase = '', $bDropTableIfExists = true)
    {
        $sSQL = "/* ===================================\n";
        $objDate = new TDateTime();               
        $sSQL .= transg('file created').' : '.$objDate->getDateTimeAsString()."\n";
        $sSQL .= transg('dump of database : ');
        if ($sDatabase == '')
            $sSQL .= $this->getConnectionObjectParent()->getDatabaseName()."\n";
        else
            $sSQL .= $sDatabase."\n";
        $sSQL.= "===================================*/\n\n";
        
        $arrTables = $this->getTablesInDatabase($sDatabase);
        
        foreach ($arrTables as $sTable)
        {
            $sSQL.= "\n\n/* contents of table ".$this->getSafeTableName($sTable)." */\n";
            if ($bDropTableIfExists)
                $sSQL.= 'DROP TABLE IF EXISTS '.$this->getSafeTableName($sTable).";\n";
                        
            //table createn
            $objRstCreateTable = $this->executeQuery('SHOW CREATE TABLE '.$this->getSafeTableName($sTable)); 
            $sSQL.= $objRstCreateTable->get('Create Table').";\n\n";
            
            //fields (kolomkop) ophalen
            $sFields = '';
            $arrFields = $this->getFieldsInTable($sTable);
            $iCountFields = count($arrFields);
            for ($iFieldCounter = 0; $iFieldCounter < $iCountFields; $iFieldCounter++)
            {
                if ($iFieldCounter != 0)
                {
                    $sFields.= ', ';
                }
                
                $sFields.= $this->getSafeFieldName($arrFields[$iFieldCounter]);
            }
            
            //data ophalen
            $sValues = '';
            $objRstData = $this->executeQuery('SELECT * FROM '.$this->getSafeTableName($sTable));
            while(!$objRstData->eof())
            {
                $sValues = '';
                
                $iCountFields = count($arrFields);
                for ($iFieldCounter = 0; $iFieldCounter < $iCountFields; $iFieldCounter++)  
                {
                    if ($iFieldCounter != 0)
                        $sValues.= ', ';
                                    
                    $sValues.= '\''.$objRstData->get($arrFields[$iFieldCounter]).'\'';                    
                }

                //SQL maken
                $sSQL .= 'INSERT INTO '.$this->getSafeTableName($sTable).' ('.$sFields.') ';
                $sSQL .= 'VALUES ('.$sValues.");";                
                                
                $objRstData->next();
            }
            
        }
        
        return $sSQL;         
    }

    /**
     * verkrijgen van de veldnamen van een databasetabel
     *
     * @param string $sTable
     * @return array of strings with fields
     */    
    public function getFieldsInTable($sTable)
    {
        try
        {
            $arrFields = null;
            
            $sSQL  = 'SHOW COLUMNS FROM '.$this->getSafeTableName($sTable);    
            $objRst = $this->executeQuery($sSQL);            
            if (is_object($objRst))
            {
                while(!$objRst->eof())
                {
                    $arrFields[] = $objRst->get('Field');
                    $objRst->next();
                }
                
                return $arrFields;
            }
            else
                return null;
        }
        catch (Exception $objException)
        {
            $this->setExecutionSQLOK(false);
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return null;
        }           
    }

    /**
     * Opvragen van laatste ID van een database-tabel en hier 1 bij op tellen
     * dit is te gebruiken voor het makkelijk genereren van een nieuwe ID voor
     * een volgende record in de database tabel
     *
     * @param string $sTable
     * @param string $sIDFieldName
     * @return integer het ID van een database tabel + 1
     */    
    public function getIDPlus1($sTable, $sIDFieldName)
    {
        $iID = 1;

        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();                 

            //after connect            
            $objMySQL = $this->getAPIConnObject();    

            $sSQL = 'SELECT MAX('.$this->getSafeFieldName($sIDFieldName).') AS i_maxid FROM '.$this->getSafeTableName($sTable);
            

            if ($objTempMySQLResult = $objMySQL->query($sSQL)) 
            {            
                
                if($objTempMySQLResult->num_rows > 0) 
                {
                    $this->setExecutionSQLOK(true);
                    

                    while($arrRow = $objTempMySQLResult->fetch_assoc()) 
                    {
                        $iID = (int)$arrRow['i_maxid'];                        
                        $iID++;            
                        $objTempMySQLResult->close();/* free result set */
                        return $iID;
                    }
                }
                else
                {
                    $objTempMySQLResult->close();/* free result set */
                    $this->setExecutionSQLOK(false);
                }

            }
            else 
            {
                $this->setExecutionSQLOK(false);
            }
            

        }
        catch (Exception $objException)
        {
            $this->setExecutionSQLOK(false);
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return 1;
        }

        return $iID;        
    }

    public function getLastInsertID()
    {
        $objStat = $this->getAPIConnObject();
        return $objStat->insert_id;
    }

    /**
     * getting a safe name for the supplied parameter string
     * preventing weird database names to confuse a the SQL code
     * for preventing errors AND SQL injection
     *
     * @param string $sDatabase databasenaam die veilig gemaakt moet worden
     * @return string met veilige databasenaam
     */    
    public function getSafeDatabaseName($sDatabase)
    {
        //$objMySQL = $this->getAPIConnObject();        
        //return $objMySQL->real_escape_string($sDatabase);   
        return preg_replace( '/[^'.TDBPreparedStatementMySQL::REGEXALPHANUMERIC.'_]/', '', $sDatabase);          
    }

    /**
     * getting a safe name for the supplied parameter string
     * preventing weird columnnames to confuse a the SQL code
     * for preventing errors AND SQL injection
     *
     * @param string $sField columnname in the database table
     * @param string $bStrict for some statements (i.e. COUNT() or 'column1 AS column2') we need to loosen up the check so it will accept COUNT and AS 
     * @return string met veilige kolomnaam/field name
     */    
    public function getSafeFieldName($sField, $bStrict = true)
    {
       
//        $objMySQL = $this->getAPIConnObject();                
//        return $objMySQL->real_escape_string($sField);   
		if ($bStrict)
			$sFilter = TDBPreparedStatementMySQL::REGEXALPHANUMERIC.'_';
		else  //loose
			$sFilter = TDBPreparedStatementMySQL::REGEXALPHANUMERIC.'_'.'\. ()*';
        return preg_replace( '/[^'.$sFilter.']/', '', $sField);        
    }

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
    public function getSafeFieldValue($sValue)
    {
        $objMySQL = $this->getAPIConnObject();  //---> WARNING: can be null when there is no connection
        if ($sValue == null)  //real_escape_string: passing null to parameter #1 ($string) of type string is deprecated
            return null;
    
        return $objMySQL->real_escape_string($sValue);       
    }    
    

    
    /**
     * getting a safe name for the supplied parameter string
     * preventing weird tables names to confuse a the SQL code
     * for preventing errors AND SQL injection
     *
     * @param string $sTable
     * @return string met veilige tabelnaam
     */    
    public function getSafeTableName($sTable)
    {       
        return preg_replace( '/[^'.REGEX_ALPHANUMERIC_UNDERSCORE.']/', '', $sTable); 
    }

    /**
     * verkrijgen van de tabellen in de huidige database
     * @param string $sDatabase (optioneel) de database waarvan tabellen opgevraagd moeten worden
     * @return array 1d array met namen van tabellen, null wanneer er iets mis ging
     */
    public function getTablesInDatabase($sDatabase = '')
    {
        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();                
            
            //after connect            
            $objMySQL = $this->getAPIConnObject();    

            //create sql
            $sSQL = 'SHOW TABLES';
            if ($sDatabase != '')
                $sSQL .= 'FROM '.$this->getSafeDatabaseName ($sDatabase);

            //execute
            if ($objTempMySQLResult = $objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                if (is_object($objTempMySQLResult)) //returns a resultset or not ?
                {
                    $arrTables = null;
                    
                    while($arrRow = $objTempMySQLResult->fetch_array(MYSQLI_NUM))
                    {
                        $arrTables[] = $arrRow[0];
                    }

                    $objTempMySQLResult->close();/* free result set */

                    return $arrTables;
                }
                else
                {
                    return null; //return no resultset
                }

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return null; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return null;
        }                  
    }

    
    
    /**
     * generate insertSQL (and execute in db) from a model to insert records in database
     * 
     * @param TSysModel $objModel
     * @return boolean
     */
    public function insertModel(TSysModel &$objModel)
    {
        try
        {   
            $arrFieldnamesDefined = array(); //init values is faster
            $arrFieldnamesDefined = $objModel->getFieldsDefined();
            $arrFieldnamesInsert = array();//we need to filter for auto increment values, therefore a second array to fill
            $arrFieldvaluesInsert = array(); 
            $iCountFields = count($arrFieldnamesDefined);
        		
            for ($iIndex = 0; $iIndex < $iCountFields; ++$iIndex)
            {
            		$sFieldName = $arrFieldnamesDefined[$iIndex];
            	
            		//only if not auto increment field (because then the database has to do it)
            		if (!$objModel->getFieldAutoIncrement($sFieldName))
            		{       
            			$mValue = $objModel->get($sFieldName);

            		if ($mValue instanceof TSysModel)
					    $mValue = $mValue->get($objModel->getFieldForeignKeyField($sFieldName));//getting value of referenced object            				
            				
            				
	                //type aware insert, this prevents also sql injection
	                switch ($objModel->getFieldType($sFieldName))
	                {
                                case CT_BINARY:
                                case CT_BLOB:
                                case CT_HEX:
                                case CT_COLOR:                                    
                                        $arrFieldvaluesInsert[] = 'x\''.bin2hex($mValue).'\''; //the x before the value is needed for mysql to see it as a hexadecimal                  
                                        break;
                                case CT_IPADDRESS:
                                        $arrFieldvaluesInsert[] = 'x\''.bin2hex(inet_pton($mValue)).'\''; //the x before the value is needed for mysql to see it as a hexadecimal                  
                                        break;
                                case CT_BOOL:
                                        $arrFieldvaluesInsert[] = (int)$mValue;
                                        break;
                                case CT_DATETIME:
                                        $sDateTimeInDB = '';
                                        $sDateTimeInDB = TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT; //default to prevent injection by inputting a string as a date for example
                                        if ($mValue instanceof TDateTime)
                                        {
                                                if (!$mValue->isZero()) //if not zero convert to date, if zero fall back to default TIMESTAMP_ZERO_FORMAT
                                                    $sDateTimeInDB = date('Y-m-d H:i:s', $mValue->getTimestamp());		                		 
                                        }
                                        elseif (is_int($mValue)) //also supporting unix timestamp
                                                $sDateTimeInDB = date('Y-m-d H:i:s', $mValue);
                                        $arrFieldvaluesInsert[] = '\''.$sDateTimeInDB.'\'';
                                        break;
                                case CT_DOUBLE:
                                case CT_FLOAT:
                                        $arrFieldvaluesInsert[] = (float)$mValue;
                                        break;		                		
                                case CT_INTEGER32:
                                case CT_INTEGER64:
                                        $arrFieldvaluesInsert[] = (int)$mValue;
                                        break;		                		
                                case CT_DECIMAL:
                                case CT_CURRENCY:
                                        if ($mValue instanceof TDecimal)
                                            $arrFieldvaluesInsert[] = '\''.$mValue->getValue().'\'';
                                        else 
                                        {
                                            $arrFieldvaluesInsert[] = '\'0\'';
                                            error_log('update db: field "'.$sFieldName.'" is not of type TDecimal. Value is "'.$mValue.'". 0 is assumed for safety reasons');
                                        }
                                        break;                                                 		                			                
                                default:
                                        $arrFieldvaluesInsert[] = '\''.$this->getSafeFieldValue($mValue).'\'';
	                }//eind: switch
            			
	                //replace with safe fieldname (to prevent injection) -->we can only do this after the insert because
	                $arrFieldnamesInsert[] = $this->getSafeFieldName($arrFieldnamesDefined[$iIndex]);
            		}//eind: if autoincrement            
            }
            
            //glue the arrays to string separated with comma's
            $sFieldNames = implode(', ',$arrFieldnamesInsert);
            $sFielValues = implode(', ',$arrFieldvaluesInsert);
            
            //SQL maken
            $sSQL  = 'INSERT INTO '.$this->getSafeTableName($objModel::getTable()).' ('.$sFieldNames.") ";
            $sSQL .= 'VALUES ('.$sFielValues.')';
        
            $this->execute($sSQL);
            return $this->getExecutionSQLOK();
        }
        catch (Exception $objException)
        {
            $this->setExecutionSQLOK(false);
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }           
    }

    
    /**
     * updating records by supplying a model 
     * 
     * @param TSysModel $objModel
     * @return boolean
     */
    public function updateModel(TSysModel &$objModel)
    {
        try
        {            
            $arrFieldnamesDefined = array(); //init values is faster
            $arrFieldnamesDefined = $objModel->getFieldsDefined();
            $arrFieldsUpdate = array();//we need to filter for auto increment values, therefore a second array to fill
            $iCountFields = count($arrFieldnamesDefined);
	        	
            for ($iFieldCounter = 0; $iFieldCounter < $iCountFields; $iFieldCounter++)
            {
                $sFieldName = $arrFieldnamesDefined[$iFieldCounter];

                $mValue = $objModel->get($sFieldName);

                if ($mValue instanceof TSysModel)
                        $mValue = $mValue->get($objModel->getFieldForeignKeyField($sFieldName));//getting value of referenced object


                //type aware insert, this prevents also sql injection
                switch ($objModel->getFieldType($sFieldName))
                {
                    case CT_BINARY:
                    case CT_BLOB:
                    case CT_HEX:
                    case CT_COLOR:                              
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = x\''.bin2hex($mValue).'\'';                       
                        break;
                    case CT_IPADDRESS:
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = x\''.bin2hex(inet_pton($mValue)).'\'';                       
                        break;
                    case CT_BOOL:
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = '.(int)$mValue.'';
                        break;
                    case CT_DATETIME:
                        $sDateTimeInDB = '';
                        $sDateTimeInDB = TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT; //default to prevent injection by inputting a string as a date for example
                        if ($mValue instanceof TDateTime)
                        {
                            if (!$mValue->isZero()) //if not zero convert to date, if zero fall back to default TIMESTAMP_ZERO_FORMAT
                                $sDateTimeInDB = date('Y-m-d H:i:s', $mValue->getTimestamp());		                		 
                        }
                        elseif (is_int($mValue)) //also supporting unix timestamp
                                $sDateTimeInDB = date('Y-m-d H:i:s', $mValue);
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = \''.$sDateTimeInDB.'\'';
                        break;                                
                    case CT_DOUBLE:
                    case CT_FLOAT:
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = '.(float)$mValue.'';
                        break;                    	 
                    case CT_AUTOINCREMENT:
                    case CT_INTEGER32:
                    case CT_INTEGER64:
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = '.(int)$mValue.'';
                        break;
                    case CT_DECIMAL:
                    case CT_CURRENCY:
                        if ($mValue instanceof TDecimal)
                            $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = \''.$mValue->getValue().'\'';
                        else 
                        {
                            $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = \'0\'';
                            error_log('update db: field "'.$sFieldName.'" is not of type TDecimal. Value is "'.$mValue.'". 0 is assumed for safety reasons');
                        }
                        break;                                                 
                    default:
                        $arrFieldsUpdate[] = $this->getSafeFieldName($sFieldName).' = \''.$this->getSafeFieldValue($mValue).'\'';                       
                }//eind: switch      

            }//eind: for index
            
            
            //glue the arrays to string separated with comma's
            $sFieldsUpdate = implode(', ',$arrFieldsUpdate);            
            
            //SQL maken
            $sSQL  = 'UPDATE '.$this->getSafeTableName($objModel::getTable()).' ';
            $sSQL .= 'SET '.$sFieldsUpdate.' ';
            $sSQL .= $this->generateSQLWhereModel($objModel);
                                          
//vardump(APP_DEBUGMODE,'wattepetatte2update:2:'.$sSQL);   

            return $this->execute($sSQL);            
        }
        catch (Exception $objException)
        {
            $this->setExecutionSQLOK(false);
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }          
    }

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
    public function updateField($sTable, $sField, $sValue, $sWhereField, $sWhereValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO)
    {
    	$sSQL  = 'UPDATE '.$this->getSafeTableName($sTable).' ';
    	$sSQL .= 'SET '.$this->getSafeFieldName($sField).' = "'.$this->getSafeFieldValue($sValue).'" '.' ';        
    	$sSQL .= 'WHERE '.$this->getSafeFieldName($sWhereField).' '.$this->translateComparisonOperator($sComparisonOperator).' "'.$this->getSafeFieldValue($sWhereValue).'"';
    	
    	return $this->execute($sSQL);
    	 
    }

    /**
     * increment the values in one integer-type field
     * 
     * i.e. if you want to insert a record at a certain spot in a table with an order field, 
     * you need to update all order values above a certain order number.
	 *
     * @param string $sTable 
     * @param string $sIncrField field to increment
     * @param string $sWhereField field on which to apply the SQL 'WHERE' filter
     * @param string $iBetweenStart start value
     * @param string $iBetweenEnd end value
     * @return boolean
     */
    public function updateFieldIncrementBetween($sTable, $sIncrField, $sWhereField, $iBetweenStart, $iBetweenEnd)
    {
        if (!is_numeric($iBetweenStart))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'iBetweenStart parameter is not numeric');
            return false;
        }
        if (!is_numeric($iBetweenEnd))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'iBetweenEnd parameter is not numeric');
            return false;
        }

        //BETWEEN works only when the lowest value is on the left side in the query
        if ($iBetweenEnd < $iBetweenStart)
        {
            $iSwap = 0;
            $iSwap = $iBetweenEnd;
            $iBetweenEnd = $iBetweenStart;
            $iBetweenStart = $iSwap;
        }

    	$sSQL  = 'UPDATE '.$this->getSafeTableName($sTable).' ';
    	$sSQL .= 'SET '.$this->getSafeFieldName($sIncrField).' = '.$this->getSafeFieldName($sIncrField).' + 1 ';        
    	$sSQL .= 'WHERE '.$this->getSafeFieldName($sWhereField).' BETWEEN '.$iBetweenStart.' AND '.$iBetweenEnd . ';';
    	
    	return $this->execute($sSQL);
    }

    /**
     * decrement the values in one integer-type field
     * 
     * i.e. if you want to insert a record at a certain spot in a table with an order field, 
     * you need to update all order values above a certain order number.
	 *
     * @param string $sTable 
     * @param string $sDecrField 
     * @param string $sWhereField 
     * @param string $iBetweenStart start value
     * @param string $iBetweenEnd 
     * @return boolean
     */
    public function updateFieldDecrementBetween($sTable, $sDecrField, $sWhereField, $iBetweenStart, $iBetweenEnd)
    {
        if (!is_numeric($iBetweenStart))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'iBetweenStart parameter is not numeric');
            return false;
        }
        if (!is_numeric($iBetweenEnd))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'iBetweenEnd parameter is not numeric');
            return false;
        }

        //BETWEEN works only when the lowest value is on the left side in the query
        if ($iBetweenEnd < $iBetweenStart)
        {
            $iSwap = 0;
            $iSwap = $iBetweenEnd;
            $iBetweenEnd = $iBetweenStart;
            $iBetweenStart = $iSwap;
        }

    	$sSQL  = 'UPDATE '.$this->getSafeTableName($sTable).' ';
    	$sSQL .= 'SET '.$this->getSafeFieldName($sDecrField).' = '.$this->getSafeFieldName($sDecrField).' - 1 ';        
    	$sSQL .= 'WHERE '.$this->getSafeFieldName($sWhereField).' BETWEEN '.$iBetweenStart.' AND '.$iBetweenEnd . ';';
    	
    	return $this->execute($sSQL);
    }    
    
    /**
     * parse SQL statement
     * this gets rid of the prepared statement question marks (?)
     */
    protected function renderSQL($sSQLStatementWithPlaceholders = '')        
    {
        if ($sSQLStatementWithPlaceholders == '')
            $sGeneratedSQL = $this->getSQLPreparedStatement();
        else
            $sGeneratedSQL = $sSQLStatementWithPlaceholders;
        
        $arrParams = $this->getParameters();
        $arrParamTypes = $this->getParameterTypes();

        if ($sGeneratedSQL != null)
        {
            if ($arrParams) //are there parameters??
            {
                $iCountParams = count($arrParams);
                for ($iParamIndex = 0; $iParamIndex < $iCountParams; $iParamIndex++)                
                {
                    $mParam = $arrParams[$iParamIndex];
                    $iType = $arrParamTypes[$iParamIndex];
                    //$sGeneratedSQL = str_replace('?', $sParam, $sGeneratedSQL); --> ik kan geen replace doen omdat alle vraagtekens dan in 1 keer vervangen worden, dat willen we niet

                    /**
                     * er zijn 4 types voor prepared statements:
                     * i - integer
                     * d - double
                     * s - string
                     * b - binary
                     */                    
                    switch ($iType)
                    {
                        case CT_BOOL:
                            $mParam = (int)$mParam;
                            break;     
                        case CT_INTEGER32:
                            if (!is_int($mParam)) 
                            {
                                if ($mParam instanceof TInteger)
                                    $mParam = (int)$mParam->getValue();

                                if (is_numeric($mParam))
                                    $mParam = (int)$mParam;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type integer");
                            }
                            break;
                       case CT_INTEGER64:                      
                            if (!is_int($mParam)) 
                            {
                                if ($mParam instanceof TInteger)
                                    $mParam = (int)$mParam->getValue();

                                if (is_numeric($mParam))
                                    $mParam = (int)$mParam;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type 64 bits integer");
                            }
                            break;
                        case CT_DOUBLE:
                            if (!is_double($mParam)) 
                            {
                                if ($mParam instanceof TFloat)
                                    $mParam = (double)$mParam->getValue();

                                if (is_numeric($mParam))
                                    $mParam = (double)$mParam;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type double");
                            }
                            break;
                        case CT_FLOAT:
                            if (!is_float($mParam)) 
                            {
                                if ($mParam instanceof TFloat)
                                    $mParam = (float)$mParam->getValue();                            

                                if (is_numeric($mParam))
                                    $mParam = (float)$mParam;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type float");
                            }
                            break;
                        case TP_DATETIME:
                            if ($mParam instanceof TDateTime)
                            {
                                if ($mParam->isZero())
                                    $mParam = '\''.TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT.'\'';
                                else
                                    $mParam = '\''.date('Y-m-d H:i:s', $mParam->getTimestamp()).'\'';
                            }
                            elseif(is_int($mParam)) //support unix timestamp
                            {
                                $mParam = '\''.date('Y-m-d H:i:s', $mParam).'\'';
                            }
                            else
                                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type TDateTime or int");                            
                            break;                                     
                        case TP_BINARY:
                        case TP_BLOB:
                        case CT_HEX:
                        case CT_COLOR:                            
                            $mParam = 'x\''.bin2hex($mParam).'\''; //x before value is needed for MySQL to see it as a hexadecimal
                            break;     
                        case CT_IPADDRESS:
                            $mParam = 'x\''.bin2hex(inet_pton($mParam)).'\''; //x before value is needed for MySQL to see it as a hexadecimal
                            break;     
                        case TP_DECIMAL:
                            if ($mParam instanceof TDecimal)
                                $mParam = $mParam->getValue();
                            else
                                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type TDecimal");                            
                            break;            
                        case TP_CURRENCY:
                            if ($mParam instanceof TCurrency)
                                $mParam = $mParam->getValue();
                            else
                                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, "'$mParam' is not of type TCurrency");                            
                            break;                       
                        default: //default is string, die wordt ge-escaped
                            $mParam = '\''.$this->getSafeFieldValue($mParam).'\''; 
                    }

                    $iPos = strpos($sGeneratedSQL, '?'); //positie vraagteken

                    if ($iPos) //als het vraagteken nog voorkomt
                    {
                        $sPre = substr($sGeneratedSQL, 0, $iPos);//alles voor het vraagteken pakken
                        $sPost = substr($sGeneratedSQL, $iPos+1, strlen($sGeneratedSQL)-1); //alles na het vraagteken pakken

                        $sGeneratedSQL = $sPre.$mParam.$sPost; //alles aan elkaar plakken
                    }                     
                    //vardump($sParam);
                } //end for
            } //end if ($arrParams)

            return $sGeneratedSQL;
        }
        else
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'the SQL statement to prepare is null. So can\'t do anything', $this);
            return null;
        }
        
    }

    
    /**
     * return SQL select-statement from TSysModel query builder
     * 2 sept 2015: alternative for TDBSQLBuilder  wich replaces generateSQLSelect() and generateSQLCount
     *
     * this function can generate een count statement for the select query (handy for the paginator class)
     *
     * @param TSysModel $objModel
     * @param boolean $bCountStatement generate a count statement (true) in stead of select statement (false)
     * @return string sql statement, null if error
     */
    public function generateSQLSelectModel(TSysModel &$objModel)
    {
    	$arrSelect = array(); //init
    	$arrJoin = array();//init
        global $objDBConnection;
	
        //====first make sure we have a database connection, otherwise the API-connection-mysql object == null and we can't do $this->getSafeFieldName()
        if (!$objDBConnection->isConnected())
            $objDBConnection->connect();


        //===SELECT and FROM
        if (!$objModel->getQBSelectFrom()) //als niets opgegevens dan SELECT *
        {
        	logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'generateSQLSelectModel(): objModel->getQBSelectFrom is empty');
        	return '';
        }        
    	$arrSelect = $objModel->getQBSelectFrom();
        $arrJoin = $objModel->getQBJoin();
    	        	
    	       
        if (count($arrSelect) > 0)
        { 
            	$arrSelectFields = array();
            	$arrSelectTables = array();
            
            	foreach($arrSelect as $arrSelectRow)
            	{
            		$sSQLAliasPostFix = '';
            		if ($arrSelectRow[TSysModel::QB_SELECTINDEX_ALIAS] != '') 
            			$sSQLAliasPostFix = ' AS '.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_ALIAS]);
            		$sSQLTablePrefix = $this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_TABLE]).'.';
      		
            		
            		//just the normal 'plain' fields like: SELECT id, name FROM addres
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_FIELD]))
           				$arrSelectFields[] = $sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_FIELD]).$sSQLAliasPostFix;
            			           		            		
            		//count fields
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_COUNTFIELD]))
            			$arrSelectFields[] = 'COUNT('.$sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_COUNTFIELD]).') '.$sSQLAliasPostFix;         			
            		
            		//countdistinct
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_COUNTDISTINCTFIELD]))
            			$arrSelectFields[] = 'COUNT(DISTINCT '.$sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_COUNTDISTINCTFIELD]).') '.$sSQLAliasPostFix;         			

            		//distinct
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_DISTINCTFIELD]))
            			$arrSelectFields[] = 'DISTINCT '.$sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_DISTINCTFIELD]).' '.$sSQLAliasPostFix;         			
            		            				
            		//average
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_AVGFIELD]))
            			$arrSelectFields[] = 'AVG('.$sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_AVGFIELD]).') '.$sSQLAliasPostFix;

            		//minimum
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_MINFIELD]))
	              		$arrSelectFields[] = 'MIN('.$sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_MINFIELD]).') '.$sSQLAliasPostFix;

            		//maximum
            		if (isset($arrSelectRow[TSysModel::QB_SELECTINDEX_MAXFIELD]))
              			$arrSelectFields[] = 'MAX('.$sSQLTablePrefix.$this->getSafeFieldName($arrSelectRow[TSysModel::QB_SELECTINDEX_MAXFIELD]).') '.$sSQLAliasPostFix;
          		
            		
            		//oh yeah, dont forget the tables            		
            		//filter joins (those are not nessasary directly in FROM clause, because they are added in JOIN clause)
            		$bJoinExists = false; 
            		foreach ($arrJoin as $arrJoinRow)
            		{
            			if (isset($arrJoinRow[TSysModel::QB_JOININDEX_TABLEEXTERNAL]))
            				if ($arrJoinRow[TSysModel::QB_JOININDEX_TABLEEXTERNAL] == $arrSelectRow[TSysModel::QB_SELECTINDEX_TABLE])
            					$bJoinExists = true;
            		}
            		if (!$bJoinExists)
            			$arrSelectTables[] = $this->getSafeTableName($arrSelectRow[TSysModel::QB_SELECTINDEX_TABLE]);
            		
            	}//eind: foreach
            
            	
            	$arrSelectTables = array_unique($arrSelectTables);   //remove doubles

            	$sSQL = 'SELECT '.implode(', ', $arrSelectFields).' ';
				$sSQL.= 'FROM '.implode(', ', $arrSelectTables).' '; 
             	//$sSQL.= 'FROM '.$objModel::getTable().' ';
            						
				unset($arrSelectFields);
				//unset($arrSelectTables);
        }//eind if count > 0
        	    
        unset($arrSelect);       

        
        //===JOINS (inner, outer, left, right)
        if ($arrJoin)
        {
            $sInternalTable = '';

            //prevent sql error: Not unique table/alias: 'tblBEDEVSysCountries' regarding the join statement :
            //Example: 
            //SELECT tblBEDEVSysCMSUserAccounts.i_id FROM tblBEDEVSysCMSUserAccounts 
            //INNER JOIN tblBEDEVSysContacts ON tblBEDEVSysCMSUserAccounts.iContactID = tblBEDEVSysContacts.i_id 
            //INNER JOIN tblBEDEVSysCountries ON tblBEDEVSysContacts.iBillingCountryID = tblBEDEVSysCountries.i_id  <== Country1
            //INNER JOIN tblBEDEVSysCountries ON tblBEDEVSysContacts.iDeliveryCountryID = tblBEDEVSysCountries.i_id <== Country2
            //$arrJoinedTables is a registration of the join-tables we used, so we can give the double ones an alias to avoid dupicate error messages from the mysql
            //layout array: $arrJoinedTables['tablename'] = 5; (5 is the number of times the same table is already joined)
            $arrJoinedTables = array(); 

	        foreach ($arrJoin as $arrJoinRow)
    	    {
                //make sure we have in internal table (internal table can be the table of the model object or an external model object)
                if (isset($arrJoinRow[TSysModel::QB_JOININDEX_TABLEINTERNAL]))
                {
                    if ($arrJoinRow[TSysModel::QB_JOININDEX_TABLEINTERNAL] == '') //allow an empty table set, then table of current model is assumed
                        $sInternalTable = $objModel::getTable();
                    else
                        $sInternalTable = $arrJoinRow[TSysModel::QB_JOININDEX_TABLEINTERNAL];
                }
                else //internal table not set
                {
                    $sInternalTable = $objModel::getTable();
                }


    	    	switch ($arrJoinRow[TSysModel::QB_JOININDEX_TYPE])
    	    	{
    	    		case JOIN_OUTER:
    	    			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'mysql doesnt support outer joins');
    	    			break;
    	    		case JOIN_LEFT:
    	    			$sSQL.= 'LEFT JOIN';
    	    			break;
    	    		case JOIN_RIGHT:
    	    			$sSQL.= 'RIGHT JOIN';
    	    			break;
    	    		default:
    	    			$sSQL.= 'INNER JOIN';
    	    	}
    	    	
    	    	//====regular table (instead of subquery)
    	    	if (isset($arrJoinRow[TSysModel::QB_JOININDEX_TABLEEXTERNAL]))
                {
                    $sExternalTable = '';
                    $sExternalTableAlias = '';
                    $sExternalTableAliasWithAS = '';// ' AS ' part
                    $sExternalTable = $this->getSafeTableName($arrJoinRow[TSysModel::QB_JOININDEX_TABLEEXTERNAL]);

                    //updating $arrJoinedTables administration
                    if(array_key_exists($sExternalTable, $arrJoinedTables))
                        $arrJoinedTables[$sExternalTable] = $arrJoinedTables[$sExternalTable] +1;
                    else
                        $arrJoinedTables[$sExternalTable] = 1;

                    //if table is joined more than once, then add alias with postfix
                    if ($arrJoinedTables[$sExternalTable] > 1) //assign alias
                    {
                        $sExternalTableAlias = $sExternalTable.$arrJoinedTables[$sExternalTable]; //postfix is the amount of tables that exist
                        $sExternalTableAliasWithAS = $sExternalTable.' AS '. $sExternalTableAlias;
                    }
                    else//no alias
                    {
                        $sExternalTableAlias = $sExternalTable;
                        $sExternalTableAliasWithAS = $sExternalTableAlias;
                    }


	    			$sSQL.= ' '. $sExternalTableAliasWithAS.' ON '.$this->getSafeTableName($sInternalTable).'.'.$this->getSafeFieldName($arrJoinRow[TSysModel::QB_JOININDEX_FIELDINTERNAL]).' = '.$sExternalTableAlias.'.'.$this->getSafeFieldName($arrJoinRow[TSysModel::QB_JOININDEX_FIELDEXTERNAL]).' ';
                }

    	    	//====subquery
    	    	if (isset($arrJoinRow[TSysModel::QB_JOININDEX_SUBQUERYOBJECT]))
    	    		$sSQL.= ' ('.$this->generateSQLSelectModel($arrJoinRow[TSysModel::QB_JOININDEX_SUBQUERYOBJECT]).') AS '.$this->getSafeTableName($arrJoinRow[TSysModel::QB_JOININDEX_SUBQUERYTABLEALIAS]).' ON '.$this->getSafeTableName($sInternalTable).'.'.$this->getSafeFieldName($arrJoinRow[TSysModel::QB_JOININDEX_FIELDINTERNAL]).' = '.$this->getSafeTableName($arrJoinRow[TSysModel::QB_JOININDEX_SUBQUERYTABLEALIAS]).'.'.$this->getSafeFieldName($arrJoinRow[TSysModel::QB_JOININDEX_FIELDEXTERNAL]).' ';
    	    	
            }        	
        }
        unset($arrJoin);

        
        //===WHERE
        $sSQL .= $this->generateSQLWhereModel($objModel);

        
        //===ORDERBY
        $arrOrderBy = array();//init
        $arrOrderBy = $objModel->getQBSort();
        if ($arrOrderBy)
        {         
        	if (count($arrOrderBy) > 0)
        	{ 
	            $arrOrderByComponents = array();
	            foreach ($arrOrderBy as $arrOrderByRow)
	            {
	            	if ($arrOrderByRow[TSysModel::QB_SORTINDEX_ORDER] == SORT_ORDER_ASCENDING)
	            		$arrOrderByComponents[] = $this->getSafeTableName($arrOrderByRow[TSysModel::QB_SORTINDEX_TABLE]).'.'.$this->getSafeFieldName($arrOrderByRow[TSysModel::QB_SORTINDEX_FIELD]).' ASC';
	            	if ($arrOrderByRow[TSysModel::QB_SORTINDEX_ORDER] == SORT_ORDER_DESCENDING)
	            		$arrOrderByComponents[] = $this->getSafeTableName($arrOrderByRow[TSysModel::QB_SORTINDEX_TABLE]).'.'.$this->getSafeFieldName($arrOrderByRow[TSysModel::QB_SORTINDEX_FIELD]).' DESC';	            	
	            	//er kan ook een sortorder_none opgegeven zijn
	            }
	            if ($arrOrderByComponents)
	            	$sSQL.= 'ORDER BY '.implode(', ', $arrOrderByComponents).' ';
	            unset($arrOrderByComponents);	            
        	}
        }         
        unset($arrOrderBy);   

        
        //===LIMIT 
        //let op: de limit kan 1 of 2 parameters hebben:
        //1 parameter: is dat het aantal records wat terug moet komen
        //2 parameters: is de EERSTE de offset en de TWEEDE het aantal records wat terug moet komen --> DAT IS DUS EEN ANDERE VOLGORDE ALS DE PARAMETERS VAN DEZE FUNCTIE!!
        
        //limit: init
        $arrLimit = array();
        $arrLimit = $objModel->getQBLimit();
        
        if (!is_numeric($arrLimit[TSysModel::QB_LIMITINDEX_COUNT]))
        	$arrLimit[TSysModel::QB_LIMITINDEX_COUNT] = 0;
        if ($arrLimit[TSysModel::QB_LIMITINDEX_COUNT] == 0) //if count is zero, replace it with default count
        	$arrLimit[TSysModel::QB_LIMITINDEX_COUNT] = TSysModel::QB_LIMITCOUNTDEFAULT;
        
        if (!is_numeric($arrLimit[TSysModel::QB_LIMITINDEX_OFFSET]))
        	$arrLimit[TSysModel::QB_LIMITINDEX_OFFSET] = 0;
        
        //limit: construct sql
        if (($arrLimit[TSysModel::QB_LIMITINDEX_COUNT] > 0) || ($arrLimit[TSysModel::QB_LIMITINDEX_OFFSET] > 0))
       		$sSQL .= 'LIMIT ';
        
       	if ($arrLimit[TSysModel::QB_LIMITINDEX_OFFSET] > 0)
       		$sSQL .= $arrLimit[TSysModel::QB_LIMITINDEX_OFFSET].',';
        
       	if ($arrLimit[TSysModel::QB_LIMITINDEX_COUNT] > 0)
       		$sSQL .= $arrLimit[TSysModel::QB_LIMITINDEX_COUNT];
        
        
        return $sSQL;
        
    }

   


    /**
     * generate WHERE part of sql-statement
     * 4 sept 2015: replacement of the TDBSQLBuilder function
     * 
     * @param TSysModel $objModel
     * @return string
     */
    private function generateSQLWhereModel(TSysModel &$objModel)
    {
    	$arrWhereFields = array();
        $arrWhereFields = $objModel->getQBFind();//the where fields
        $arrWhereConditions = array();//the different WHERE components are stored here, wich are later on glued together with ' AND '
        $iConditionCounter = 0;
        global $objDBConnection;

        //====first make sure we have a database connection, otherwise the API-connection-mysql object == null and we can't do $this->getSafeFieldName()
        if (!$objDBConnection->isConnected())
            $objDBConnection->connect();
        

        //====WHERE (just normal where conditions)
        if (count($arrWhereFields) > 0) //only when there are items in array
        {
            $iConditionCounter = 0;
            foreach ($arrWhereFields as $arrWhereRow)
            {           	
           		$sWhereCondition = '';
           		
                if ($iConditionCounter > 0) //skip first item
                {
                    //we always default to LOGICAL_OPERATOR_AND except when its LOGICAL_OPERATOR_OR
                    if ($arrWhereRow[TSysModel::QB_FINDINDEX_LOGICALOPERATOR] == LOGICAL_OPERATOR_OR)
                        $sWhereCondition = ' OR ';
                    else 
                        $sWhereCondition = ' AND ';
                }


           		if (isset($arrWhereRow[TSysModel::QB_FINDINDEX_TABLE])) //table exists? ->subqueries can maybe have no table specified
           			$sWhereCondition .= $this->getSafeTableName($arrWhereRow[TSysModel::QB_FINDINDEX_TABLE]).'.';           		 
           		$sWhereCondition .= $this->getSafeFieldName($arrWhereRow[TSysModel::QB_FINDINDEX_FIELD]).' '.$this->translateComparisonOperator($arrWhereRow[TSysModel::QB_FINDINDEX_COMPARISONOPERATOR]).' ';
           	
           		if (isset($arrWhereRow[TSysModel::QB_FINDINDEX_SUBQUERYOBJECT]))//subquery?
           		{           			
           			$sWhereCondition .= '('.$this->generateSQLSelectModel($arrWhereRow[TSysModel::QB_FINDINDEX_SUBQUERYOBJECT]).')';
           		}
           		else//non subquery
           		{
           		
	           		if ($arrWhereRow[TSysModel::QB_FINDINDEX_COMPARISONOPERATOR] == COMPARISON_OPERATOR_IS)
	           		{
	           			$sIsValue = '';
	           			$sIsValue = $arrWhereRow[TSysModel::QB_FINDINDEX_VALUE];
	           			
	           			switch ($sIsValue)
	           			{
	           				case COMPARISON_OPERATOR_IS_VALUE_NOTNULL:
	           					$sWhereCondition.= 'NOT NULL';
	           					break;
	           				case COMPARISON_OPERATOR_IS_VALUE_NULL:
	           					$sWhereCondition.= 'NULL';
	           					break;
	           				default:
	           					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'generateSQLWhereModel(): "is" "'.$sIsValue.'" value not recognised', $this);
	           					return '';
	           			}
	           		}
	           		else 
                    {
                        if ($arrWhereRow[TSysModel::QB_FINDINDEX_COMPARISONOPERATOR] == COMPARISON_OPERATOR_BETWEEN)
                        {
                            $sWhereCondition .= $this->generateSQLWhereValue($objModel, $arrWhereRow, $sWhereCondition, TSysModel::QB_FINDINDEX_VALUE).
                                                ' AND '.
                                                $this->generateSQLWhereValue($objModel, $arrWhereRow, $sWhereCondition, TSysModel::QB_FINDINDEX_VALUEEND);
                        }
                        else
                        {
                            // $this->generateSQLWhereValue($objModel, $arrWhereRow, $sWhereCondition);
                            $sWhereCondition .= $this->generateSQLWhereValue($objModel, $arrWhereRow, $sWhereCondition, TSysModel::QB_FINDINDEX_VALUE);
                        }
                    }
           		}//end: else non-subquery
           		
                $arrWhereConditions[] = $sWhereCondition;
                ++$iConditionCounter;
            }//end: foreach                
        }//end: if count > 0

        //==== QUICKSEARCH
        $sSQLQuickSearch = '';
        $sSQLQuickSearch = $this->generateSQLWhereQuicksearch($objModel);
        if ($sSQLQuickSearch != '')
        {
            if ($iConditionCounter > 0) //when it's not the first condition
                $sSQLQuickSearch = ' AND '.$sSQLQuickSearch;//add quicksearch as sub-where by adding brackets. This way you can set for example a website id (normal search) and search only within that website with quicksearch

            $arrWhereConditions[] = $sSQLQuickSearch;
        }

		//==== WHERE						
		if ($arrWhereConditions)
			return 'WHERE '.implode('', $arrWhereConditions).' ';
		else
			return '';
			
    }

    /**
     * used by generateSQLWhereModel().
     * 
     * Used for a normal comparison operator once (EQUALS, GREATER THAN etc)
     * But for "BETWEEN" it is used twice (once for each value)
     * 
     * @return string column value with quotes (or not) and proper converted ints, dates, ipaddresses, booleans
     */
    private function generateSQLWhereValue(TSysModel &$objModel, array &$arrWhereRow, string &$sWhereCondition, string $sArrValueIndex = TSysModel::QB_FINDINDEX_VALUE)
    {
        
        $iType = 0;
        $iType = $objModel->getFieldType($arrWhereRow[TSysModel::QB_FINDINDEX_FIELD]);
        /*
        $sReturnValue = '';

        //type aware insert, this prevents also sql injection
        switch ($iType)
        {
            case CT_BLOB:
            case CT_BINARY:
            case CT_HEX:
            case CT_COLOR:
                $sReturnValue .= 'x\''.bin2hex($arrWhereRow[$sArrValueIndex]).'\''; //the x before the value is needed for mysql to see it as a hexadecimal                                      
                break;
            case CT_IPADDRESS:        
                $sReturnValue .= 'x\''.bin2hex(inet_pton($arrWhereRow[$sArrValueIndex])).'\''; //the x before the value is needed for mysql to see it as a hexadecimal                  
                break; 
            case CT_BOOL:
                $sReturnValue .= ''.boolToInt($this->getSafeFieldValue($arrWhereRow[$sArrValueIndex])).'';
                break;
            case CT_DATETIME:
                if ($arrWhereRow[$sArrValueIndex] instanceof TDateTime)
                {
                    if ($arrWhereRow[$sArrValueIndex]->isZero())
                        $sReturnValue .= '\''.TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT.'\'';                                                       
                    else
                        $sReturnValue .= '\''.date('Y-m-d H:i:s', $arrWhereRow[$sArrValueIndex]->getTimestamp()).'\'';                                                       
                }
                elseif (is_int($arrWhereRow[$sArrValueIndex])) //support unix timestamp
                {
                    $sReturnValue .= '\''.date('Y-m-d H:i:s', $arrWhereRow[$sArrValueIndex]).'\'';                                                       
                }
                        
                break;     
            case CT_DOUBLE:
            case CT_FLOAT:
                if (is_numeric($arrWhereRow[$sArrValueIndex]))
                    $sReturnValue .= ''.$arrWhereRow[$sArrValueIndex].'';                       
                if ($arrWhereRow[$sArrValueIndex] instanceof TFloat)
                    $sReturnValue .= ''.$arrWhereRow[$sArrValueIndex]->getValue().'';                                                                               
                break;  
            case CT_AUTOINCREMENT:
            case CT_INTEGER32:
            case CT_INTEGER64:
                if (is_numeric($arrWhereRow[$sArrValueIndex]))
                    $sReturnValue .= ''.$arrWhereRow[$sArrValueIndex].'';                       
                if ($arrWhereRow[$sArrValueIndex] instanceof TInteger)
                    $sReturnValue .= ''.$arrWhereRow[$sArrValueIndex]->getValue().'';                                                                               
                break;  
            case CT_DECIMAL:       
            case CT_CURRENCY:
                if (is_numeric($arrWhereRow[$sArrValueIndex]))
                    $sReturnValue .= ''.$arrWhereRow[$sArrValueIndex].'';                       
                if ($arrWhereRow[$sArrValueIndex] instanceof TDecimal)
                    $sReturnValue .= ''.$arrWhereRow[$sArrValueIndex]->getValue().'';                                                                               
                break;     
            default:
                $sReturnValue .= '\''.$this->getSafeFieldValue($arrWhereRow[$sArrValueIndex]).'\'';                       
        }  //end: case    
        
        return $sReturnValue;
        */

        return $this->generateSQLWhereValue2($arrWhereRow[$sArrValueIndex], $iType);
    }

    /**
     * returns field value with or without quotes, depending on the data-type
     * 
     * improved version of generateSQLWhereValue() that is more generic, and therefore also usable by quicksearch
     * this used by generateSQLWhereModel().
     * 
     * @param mixed $mFieldValue string or object
     * @return string column value with quotes (or not) and proper converted ints, dates, ipaddresses, booleans
     */
    private function generateSQLWhereValue2($mFieldValue, $iType)
    {
        $sReturnValue = '';

        //type aware insert, this prevents also sql injection
        switch ($iType)
        {
            case CT_BLOB:
            case CT_BINARY:
            case CT_HEX:
            case CT_COLOR:
                $sReturnValue .= 'x\''.bin2hex($mFieldValue).'\''; //the x before the value is needed for mysql to see it as a hexadecimal                                      
                break;
            case CT_IPADDRESS:        
                $sReturnValue .= 'x\''.bin2hex(inet_pton($mFieldValue)).'\''; //the x before the value is needed for mysql to see it as a hexadecimal                  
                break; 
            case CT_BOOL:
                $sReturnValue .= ''.boolToInt($this->getSafeFieldValue($mFieldValue)).'';
                break;
            case CT_DATETIME:
                if ($mFieldValue instanceof TDateTime)
                {
                    if ($mFieldValue->isZero())
                        $sReturnValue .= '\''.TDBPreparedStatementMySQL::TIMESTAMP_ZERO_FORMAT.'\'';                                                       
                    else
                        $sReturnValue .= '\''.date('Y-m-d H:i:s', $mFieldValue->getTimestamp()).'\'';                                                       
                }
                elseif (is_int($mFieldValue)) //support unix timestamp
                {
                    $sReturnValue .= '\''.date('Y-m-d H:i:s', $mFieldValue).'\'';                                                       
                }
                        
                break;     
            case CT_DOUBLE:
            case CT_FLOAT:
                if (is_numeric($mFieldValue))
                    $sReturnValue .= ''.$mFieldValue.'';                       
                if ($mFieldValue instanceof TFloat)
                    $sReturnValue .= ''.$mFieldValue->getValue().'';                                                                               
                break;  
            case CT_AUTOINCREMENT:
            case CT_INTEGER32:
            case CT_INTEGER64:
                if (is_numeric($mFieldValue))
                    $sReturnValue .= ''.$mFieldValue.'';                       
                if ($mFieldValue instanceof TInteger)
                    $sReturnValue .= ''.$mFieldValue->getValue().'';                                                                               
                break;  
            case CT_DECIMAL:       
            case CT_CURRENCY:
                if (is_numeric($mFieldValue))
                    $sReturnValue .= ''.$mFieldValue.'';                       
                if ($mFieldValue instanceof TDecimal)
                    $sReturnValue .= ''.$mFieldValue->getValue().'';                                                                               
                break;     
            default:
                $sReturnValue .= '\''.$this->getSafeFieldValue($mFieldValue).'\'';                       
        }  //end: case    
        
        return $sReturnValue;
    }

    /**
     * used by generateSQLWhereModel():
     * generates where conditions for quicksearch query
     * 
     * PSEUDO CODE:
     * loops quicksearches (you can add multiple)
     *      each quicksearch loops fields
     *          for each field looks at data type and index-type:
     *              1. varchar,longtext: fulltext                                   -> add to match-fulltext array
     *                  if uniqueid or niceid search                                -> add where x="y"
     *              2. varchar, longtext: no fulltext                               -> add like x="%y%"
     *              3. integer32, integer64, autoincrement, decimal, currency:      -> add where x=y
     * 
     * WARNING: 
     * in 1 'match()' can only be fields from the same table
     * 
     * EXAMPLE SQL:
     * SELECT 
     *      posts.id, title, body 
     *   FROM 
     *      posts 
     *   INNER JOIN postcategories
     *        ON posts.categoryid = postcategories.id
     *    WHERE 
     *      posts.id = 7
     *    OR 
     *      MATCH (posts.title, posts.body) AGAINST ('integrity data')
     *    OR
     *      MATCH (postcategories.name) AGAINST ('integrity data') 
     * 
     * @return array with where conditions
     */    
    private function generateSQLWhereQuicksearch(TSysModel &$objModel)
    {   
        //declare + init
        $arrQuicksearches = array();
        $iCountQuicksearches = 0;
        $sAddToSQL = '';
        $sValue = '';
        $iJoinDepth = 0;
        $arrQuicksearchEntry = array();
        
        $arrQuicksearches = $objModel->getQBFindQuickSearch();

        //assemble query
        $iCountQuicksearches = count($arrQuicksearches);
        for ($iQSIndex = 0; $iQSIndex < $iCountQuicksearches; ++$iQSIndex)
        {
            $arrQuicksearchEntry = $arrQuicksearches[$iQSIndex];
            $sValue = $this->getSafeFieldValue($arrQuicksearchEntry[TSysModel::QB_FINDQUICKSEARCH_VALUE]);
            $iJoinDepth = $arrQuicksearchEntry[TSysModel::QB_FINDQUICKSEARCH_JOINDEPTH];
            
            if ($iQSIndex > 0)
                $sAddToSQL.= ' AND ';
            $sAddToSQL.= '('.$this->generateSQLWhereQuicksearchTable($objModel, $sValue, $iJoinDepth).')';
        }
        
        return $sAddToSQL;
    }

    /**
     * generate search sql query for each joined table
     */
    private function generateSQLWhereQuicksearchTable(TSysModel &$objModel, &$sValue, $iJoinDepth = 0)    
    {
        //declare + init
        $iCountFieldsInTable = 0;
        $arrFieldsInTable = array();
        $arrMatch = array();
        $sTable = '';
        $arrWhereConditionsLocal = array();
        $sAddToSQL = '';

        $arrFieldsInTable = $objModel->getFieldsSearchable(); //note: we follow the order of getFieldsSearchable(). This is important for fulltext fields, otherwise we can't use them
        $arrMatch = array();
        $sTable = $this->getSafeTableName($objModel::getTable());

        $iCountFieldsInTable = count($arrFieldsInTable);
        for ($iIndexFieldsInTable = 0; $iIndexFieldsInTable < $iCountFieldsInTable; ++$iIndexFieldsInTable) //for each field in table
        {
            $sField = $this->getSafeFieldName($arrFieldsInTable[$iIndexFieldsInTable]);
            $iFieldType = $objModel->getFieldType($sField);

            switch($iFieldType)
            {
                case CT_VARCHAR: 
                case CT_LONGTEXT: //assumed you can't add 'unique' or 'indexed' indices for LONGTEXT
                    if (strlen($sValue) > 2) //at least 3 characters in order to search (otherwise you can severely stress the database)
                    {
                        if ($objModel->getFieldFulltext($sField)) //add to match-fulltext array
                        {
                            $arrMatch[] = $sField;
                        }

                        //even though fields can have a fulltext, we also include the normal compare with 'is' (=) and like.
                        if (($sField === TSysModel::FIELD_UNIQUEID) || ($sField === TSysModel::FIELD_NICEID)) //add where x="y"
                        {
                            $arrWhereConditionsLocal[] = $sTable.'.'.$sField.' = "'.$sValue.'"';
                        }
                        else //add like x=%y%
                        {
                            $arrWhereConditionsLocal[] = $sTable.'.'.$sField.' LIKE "%'.$sValue.'%"';
                        }
                    }
                    break;
                case CT_INTEGER32:
                case CT_INTEGER64:
                case CT_AUTOINCREMENT:
                case CT_DECIMAL:
                case CT_CURRENCY:
                    if (is_numeric($sValue)) //useless to check numerical fields when value is not numeric
                    {
                        $arrWhereConditionsLocal[] = $sTable.'.'.$sField.' = '.$sValue.'';
                    }
                    break;         
            }
        }

        //==== RENDER 'MATCH'-part
        //be aware that the index must be placed on all columns in that same exact order.
        //so, if you have 3 columns: the index must be placed on those exact 3 columns together and exactly in that order
        //if not: mysql will produce a sql error
        $iCountMatch = count($arrMatch);
        if ($iCountMatch > 0)
        {
            $sMatchSQL = 'MATCH (';
            for ($iMatchIndex = 0; $iMatchIndex < $iCountMatch; ++$iMatchIndex)
            {
                if ($iMatchIndex > 0)
                    $sMatchSQL.= ', ';

                $sMatchSQL.= $sTable.'.'.$arrMatch[$iMatchIndex];
            }
            $sMatchSQL.= ') AGAINST ("'.$sValue.'")';

            $arrWhereConditionsLocal[] = $sMatchSQL;
        }

        //construct SQL
        $sAddToSQL = implode(' OR ', $arrWhereConditionsLocal);


        //@todo recursive call generateSQLWhereQuicksearchTable() for joined tables. De iJoinDepth zit al in parameter

        return $sAddToSQL;
    }
    
     /**
      * does database tabel exist ?
      * 
      * @param string $sTableName
      */
     public function tableExists($sTableName)
     {
        if ($sTableName == '')
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Table name parameter is empty');
            return false;
        }

        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();

            //after connect            
            $objMySQL = $this->getAPIConnObject();      

            //sql maken
            $sSQL = 'SHOW TABLES LIKE \''.$this->getSafeTableName ($sTableName).'\'';              
            
            //uitvoeren
            if ($objTempMySQLResult = $objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                if (is_object($objTempMySQLResult)) //returns a resultset or not ?
                {
                    while($objTempMySQLResult->fetch_array(MYSQLI_NUM))
                    {
                        $objTempMySQLResult->close();/* free result set */
                        return true;                        
                    }

                    return false;
                }
                else
                {
                    return false; //return no resultset
                }

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return false; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }          
     }

     /**
      * vertalen libtype operatoren naar mysql
      * @see \drenirie\framework\classes\db\TDBPreparedStatement::translateComparisonOperator()
      */
     public function translateComparisonOperator($sComparisonOperator)
     {     	
     	switch ($sComparisonOperator) 
     	{ //in verband met performance op volgorde wanneer deze het snelst voor zal komen
     		case COMPARISON_OPERATOR_EQUAL_TO:
     			return '=';
            case COMPARISON_OPERATOR_NOT_EQUAL_TO:
                return '!=';
     		case COMPARISON_OPERATOR_IS: //for comparing NULL's
     			return 'IS';     			
     		case COMPARISON_OPERATOR_LIKE:
     			return 'LIKE';     			
     		case COMPARISON_OPERATOR_NOT_LIKE:
     			return 'NOT LIKE';     			
     		case COMPARISON_OPERATOR_BETWEEN:
     			return 'BETWEEN';     			
     		case COMPARISON_OPERATOR_LESS_THAN:
     			return '<';
    		case COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO:
    			return '<=';
    		case COMPARISON_OPERATOR_GREATER_THAN:
    			return '>';
    		case COMPARISON_OPERATOR_GREATER_THAN_OR_EQUAL_TO:
    			return '>=';
    		default:
    			return '=';//fallback     			
     	}     	     	
     	
     }

     

    /**
     * add column to database table
     * 
     * this function uses the defined table of TSysModel to change the values to
     * $sFieldName must be a field defined in TSysModel
     * 
     * @param TSysModel $objModel
     * @param string $sFieldName  (defined in TSysModel)
     * @return bool
     */
    public function alterTableAdd(&$objModel, $sFieldName)
    {
        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();

            //after connect            
            $objMySQL = $this->getAPIConnObject();     

            //make sql
            $sSQL = 'ALTER TABLE '.$this->getSafeTableName($objModel::getTable()).' '.
                    'ADD COLUMN '.$this->generateSQLColumnsDataRefactor($objModel, $sFieldName);                    
               

            //execute sql
            if ($objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                return true;

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return false; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }          
    }


    /**
     * change column in database table
     * this function uses the defined table of TSysModel to change the values to
     * $sFieldName must be a field defined in TSysModel
     *
     * @param TSysModel $objModel
     * @param string $sFieldName  (defined in TSysModel)
     * @return bool
     */
    public function alterTableModify(&$objModel, $sFieldName)
    {
        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();                    
            
            //after connect
            $objMySQL = $this->getAPIConnObject();   

            //make sql
            $sSQL = 'ALTER TABLE '.$this->getSafeTableName($objModel::getTable()).' '.
                    'MODIFY '.$this->generateSQLColumnsDataRefactor($objModel, $sFieldName);                                     

            //execute sql
            if ($objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                return true;

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return false; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }               
    }    


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
    public function alterTableRenameField(&$objModel, $sOldFieldName, $sNewFieldName)
    {
        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();   
            
            //after connect
            $objMySQL = $this->getAPIConnObject();   

            //make sql
            $sSQL = 'ALTER TABLE '.$this->getSafeTableName($objModel::getTable()).' '.
                    'CHANGE COLUMN '.$this->getSafeFieldName($sOldFieldName).' '.$this->generateSQLColumnsDataRefactor($objModel, $sNewFieldName);                    

            //execute sql
            if ($objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                return true;

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return false; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }               
    }    





    /**
     * drop column from database table
     *
     * @param string $sTableName
     * @param string $sFieldName (defined in TSysModel)
     * @return bool
     */
    public function alterTableDrop($sTableName, $sFieldName)
    {
        try
        {
            //connect
            if (!$this->objConn->isConnected())
                $this->objConn->connect();

            //after connect
            $objMySQL = $this->getAPIConnObject();              

            //make sql
            $sSQL = 'ALTER TABLE '.$this->getSafeTableName($sTableName).' '.
                    'DROP COLUMN '.$this->getSafeFieldName($sFieldName);

            if ($sTableName == '')
                 throw new Exception('alterTableDrop(): Table name is empty');

            //execute sql
            if ($objMySQL->query($sSQL)) 
            {                               
                $this->setExecutionSQLOK(true);

                return true;

            }        
            else
            {
                $this->setExecutionSQLOK(false);

                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objMySQL->error.' : '.$sSQL, $this);
                error_log($objMySQL->error.' : '.$sSQL);

                return false; //return no resultset
            }            
            

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }          
    }

     

    /**
     * creates SQL for refactoring a database table
     * in the my sql query for creating a table, altering or adding a column we need to specify the column section
     * This is the same for all 3 cases, so we let only 1 function handle this
     * 
     * @return string sql
     */
    private function generateSQLColumnsDataRefactor(&$objModel, $sColumnName)
    {
        // $sColumnNameSafe = '';
        // $sColumnNameSafe = $this->getConnectionObjectParent()->getPreparedStatement()->getSafeFieldName($sColumnName); //filteren voor de zekerheid
        global $objDBConnection;

        //====first make sure we have a database connection, otherwise the API-connection-mysql object == null and we can't do $this->getSafeFieldName()
        if (!$objDBConnection->isConnected())
            $objDBConnection->connect();


        //alle waardes in de switch resetten
        $sType = '';
        $sLength = '';
        $sAutoIncrement = '';
        $sUnsigned = '';
        $sUnique = '';
        $sDefault = '';
        $bAllowOverwriteDefault = true; //allowed to overwrite the default defined database-default?
        $sNullable = ' NULL'; //null can be omitted according to mysql doc. However practice showed that NULL can ONLY be omitted for the first TIMESTAMP column (!!), not second (a bug??), otherwise error        
        $bAllowOverwriteNullable = true; //allowed to overwrite the default defined database-nullable?
    
        //depending on type, set properties
        switch ($objModel->getFieldType($sColumnName, TSysModel::FI_TYPE))
        {
            case CT_BLOB:
                $sType = 'LONGBLOB';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT NULL';                   
                break;
            case CT_BOOL:
                $sType = 'TINYINT';
                $sLength = '(1)';
                $sUnsigned = ' UNSIGNED';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0';                
                break;
            case CT_DATETIME:
                $sType = 'TIMESTAMP';
                $bAllowOverwriteDefault = false;
                $sDefault = ' DEFAULT NULL';
                $bAllowOverwriteNullable = false;
                $sNullable = ' NULL';
                break;
            case CT_DOUBLE:
                $sType = 'DOUBLE';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0.0';                       
                break;
            case CT_ENUM :
                $sType = 'ENUM(';
                $arrEnumValues = $objModel->getFieldEnumValues($sColumnName);
                $sLastEnumValue = $arrEnumValues[count($arrEnumValues)-1];
                foreach ($arrEnumValues as $sEnumValue)
                {
                    if (is_numeric($sEnumValue)) //haakjes?
                        $sType .= $sEnumValue;
                    else
                        $sType .= "'".$sEnumValue."'";

                    if ($sEnumValue != $sLastEnumValue) //komma?
                        $sType .= ',';
                }
                $sType .= ')';
                break;
            case CT_FLOAT:
                $sType = 'FLOAT';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0.0';                       
                break;
            case CT_INTEGER32:
                $sType = 'INT';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0';                       
                break;
            case CT_INTEGER64:
                $sType = 'BIGINT';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0';                   
                break;
            case CT_LONGTEXT:
                $sType = 'LONGTEXT';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT NULL';                   
                break;
            case CT_DECIMAL:
                $sType = 'DECIMAL';
                $sLength = '('.$objModel->getFieldLength($sColumnName).', '.$objModel->getFieldDecimalPrecision($sColumnName).')';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0.0';                   
                break;
            case CT_CURRENCY:
                $sType = 'DECIMAL';
                $sLength = '(13,4)'; //--> auto assumed, doesn't care about field length
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT 0.0';                   
                break;
            case CT_AUTOINCREMENT:
                $sType = 'BIGINT';
                $sAutoIncrement = ' AUTO_INCREMENT';
                $bAllowOverwriteDefault = true;             
                break;
            case CT_BINARY:
                $sType = 'VARBINARY';
                $sLength = '('.$objModel->getFieldLength($sColumnName).')';                
                $bAllowOverwriteDefault = true;             
                break;
            case CT_IPADDRESS:
                $sType = 'VARBINARY';
                $sLength = '(16)'; //--> auto assumed, doesn't care about field length                
                $bAllowOverwriteDefault = true;             
                break;
            case CT_HEX:
                $sType = 'VARCHAR';
                $sLength = '('.$objModel->getFieldLength($sColumnName).')';   
                $bAllowOverwriteDefault = true;             
                break;
            case CT_COLOR:
                $sType = 'VARCHAR';
                $sLength = '(8)'; //--> auto assumed, doesn't care about field length                
                $bAllowOverwriteDefault = true;             
                break;                
            default :
                $sType = 'VARCHAR';
                $sLength = '('.$objModel->getFieldLength($sColumnName).')';
                $bAllowOverwriteDefault = true;
                $sDefault = ' DEFAULT NULL';                       
        }//END switch


        //not null        
        if ($bAllowOverwriteNullable)
        {
            if ((!$objModel->getFieldNullable($sColumnName)) && (!$objModel->getFieldPrimaryKey($sColumnName)))
            {
                $sNullable = ' NOT NULL';
                $bAllowOverwriteDefault = true;
                $sDefault = ''; //prevent: not nullable being default null
            }
        }

        //default
        if ($bAllowOverwriteDefault)
        {
            if ($objModel->getFieldDefaultValue($sColumnName) !== null)
            {
                if (strlen($objModel->getFieldDefaultValue($sColumnName)) > 0)
                {
                    if (is_numeric($objModel->getFieldDefaultValue($sColumnName)))
                        $sDefault = ' DEFAULT '.$objModel->getFieldDefaultValue($sColumnName);
                    else
                        $sDefault = " DEFAULT '".$this->getSafeFieldValue($objModel->getFieldDefaultValue($sColumnName))."'";
                }
            }
        }
        
        //unique
        if ($objModel->getFieldUnique($sColumnName) && (!$objModel->getFieldPrimaryKey($sColumnName)))
            $sUnique = ' UNIQUE';
            

        //sql statement maken
        return $sColumnName.' '.$sType.$sLength.$sUnsigned.$sNullable.$sAutoIncrement.$sDefault.$sUnique;

    }

}



?>
