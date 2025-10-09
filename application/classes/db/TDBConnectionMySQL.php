<?php
namespace dr\classes\db;


/**
 * specifieke database interpretatie voor mysql
 *
 * 24 sept 2012: TDBConnectionMySQL: basisopzet met mysqli ipv Zend met PDO
 * 24 sept 2012: TDBConnectionMySQL: character set is automatically set to utf8 upon connection
 * 9 mrt 2014: TDBConnectionMySQL: nasty bugsies, we hates them... na de commit werd de autocommit niet terug gezet
 * 9 jan 2020: TDBConnectionMySQL: forgot to include the port on connecting
 * 
 * @author dennis renirie
 */
class TDBConnectionMySQL extends TDBConnection
{
    protected $bIsConnected = false;

    public function __construct()
    {
        $this->setErrorReporting(TDBConnection::REPORT_ERROR_OFF); //default off

        //the nulls is what MySQLi wants as values-not-set value
        $this->setHost(null);//default is null when no host selected
        $this->setUsername(null);//default is null when no user
        $this->setPassword(null);//default is null when no password
        $this->setDatabaseName(null);//default is null when no database selected
        $this->setPort(null);//default is null when no port is submitted
    }

    public function connect()
    {
        //the nulls is what MySQLi wants as values-not-set value
        $sHost = null;
        $sUser = null;
        $sPass = null;
        $sDB = null;
        $sPort = null;

        //ensure we use default values null and not ''
        if ($this->getHost() !== '')
            $sHost = $this->getHost();
        if ($this->getUsername() !== '')
            $sUser = $this->getUsername();
        if ($this->getPassword() !== '')
            $sPass = $this->getPassword();
        if ($this->getDatabaseName() !== '')
            $sDB = $this->getDatabaseName();
        if ($this->getPort() !== '')
            $sPort = $this->getPort();

        try
        {       

            /* @ is used to suppress warnings, see https://www.php.net/manual/en/mysqli.connect-errno.php */
            $objMySQL = @new \mysqli($sHost, $sUser, $sPass, $sDB, $sPort);
                              
            if ($objMySQL->connect_errno)
            {
                $this->bIsConnected = false;
                return false;
            }

            $this->bIsConnected = true;
            $this->setInternalDatabaseObject($objMySQL);
            
            if (!$objMySQL->set_charset('utf8')) /* change character set to utf8 */
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Error loading character set utf8: '.$objMySQL->error, $this);
            

            
            //extra instelling
//            $objQuery = $this->getQuery();
//            $objQuery->executeQuery('SET sql_safe_updates=0');
//            if (!$objQuery->getExecutionSQLOK()) 
//            {
//               logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Error setting sql update option'.$objMySQL->error, $this);     
//            }
            return true;
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }

    }

    /**
     * het uitvoeren van een sql query
     *
     * @param string $sSQL - SQL query om uit te voeren
     * @param int $iResultsPerPage - max aantal resultaten die query mag teruggeven)
     * @param int $iPage - start met recordnr (voor paginate functie)
     * @return TDBResultset object
     */
//     public function executeQuery($sSQL, $iResultsPerPage = 0, $iPage = 0)
//     {
//         try
//         {
//             $objQuery = new TDBQueryMySQL($this);
//             $objRst = $objQuery->executeQuery($sSQL, $iResultsPerPage, $iPage);
//             return $objRst;
//         }
//         catch (Exception $objException)
//         {
//             error($objException, $this);
//             error_log($objException->getMessage());
//             return null;
//         }
//     }

     /**
     * het verkrijgen van een query object
     *
     * @return TDBQuery object
     */
    public function getQuery()
    {
    		return new TDBPreparedStatementMySQL($this);
//         return new TDBQueryMySQL_OLD($this);
    }

    public function disconnect()
    {
        $this->bIsConnected = false;
        $objMySQL = $this->getAPIConnObject();
        $objMySQL->close();        
    }

    public function isConnected()
    {
        return $this->bIsConnected;
    }

    /**
     * make a SQL prepared statement
     * 
     * @param string $sPreparedStatement
     * @return TDBPreparedStatementMySQL
     */
    public function prepare($sPreparedStatement)
    {
        $objPreparedStatement = new TDBPreparedStatementMySQL($this);
        $objPreparedStatement->prepare($sPreparedStatement);
        return $objPreparedStatement;
    }
    
    /**
     * commit a database transaction and return to autocommit state
     */    
    public function commit()
    {
//        logDev('commit');
        
        try
        {
            $objMySQL = $this->getAPIConnObject();
            $bAutoCommit = $objMySQL->autocommit(true);
            $bCommit = $objMySQL->commit();
            
            return ($bAutoCommit && $bCommit);
        }
        catch (Exception $objException)
        {	
        		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);            
            return false;
        }  
    }    
    
    
    /**
     * rollback a database transaction and return to autocommit state
     */    
    public function rollback()
    {
//        logDev('rollback');

        try
        {
            $objMySQL = $this->getAPIConnObject();
            $bRollback = $objMySQL->rollback();
            $bAutoCommit = $objMySQL->autocommit(true);
            
            
            return ($bRollback && $bAutoCommit);            
            
//            //rollback
//            $this->executeQuery('ROLLBACK');
//            if ($this->getExecutionSQLOK())
//            {
//                //autocommit weer terugzetten
//                $this->executeQuery('SET autocommit=1');                                /
//                return $this->getExecutionSQLOK();
//            }
//            else
//            {
//                //ook als rollback mislukt is, dan autocommit terugzetten
//                $this->executeQuery('SET autocommit=1');
//                return false;
//            }
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }            
    }
    
    /**
     * disable autocommit and starting a database transaction
     */
    public function startTransaction()
    {
//        logDev('starttransaction');
        
        try
        {
            $objMySQL = $this->getAPIConnObject();
            return $objMySQL->autocommit(false);
            
//            //eerst autocommit uit
//            $this->executeQuery('SET autocommit=0');
//            if ($this->getExecutionSQLOK())
//            {
//                //database transactie starten
//                $this->executeQuery('START TRANSACTION');
//                
//                //als start transactie niet lukt, dan terug naar autocommit
//                if (!$this->getExecutionSQLOK())
//                {
//                    $this->executeQuery('SET autocommit=1');
//                    return false;
//                }
//                
//                return $this->getExecutionSQLOK();
//            }
//            else
//                return false;
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }          
    }

    /**
     * get a new prepared statement
     * 
     * @return TDBPreparedStatementMySQL
     */
    public function getPreparedStatement()
    {
        return new TDBPreparedStatementMySQL($this);
    }


}
?>
