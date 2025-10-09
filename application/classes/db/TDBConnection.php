<?php
namespace dr\classes\db;

use dr\classes\patterns\TObject;

/**
 * Description of TDBConnection
 *
 * @author Dennis Renirie
 *
 * Opzetten van een database connectie
 */
abstract class TDBConnection
{
    protected $iErrorReportLevel = TDBConnection::REPORT_ERROR_OFF;
    protected $sHost = 'localhost';
    protected $sUsername = '';
    protected $sPassword = '';
    protected $sDatabaseName = '';
    protected $iPort = 0;

    protected $objInternalDatabaseAPIConnectionObject = null; //specifieke klasse van het gebruikte database connectie component

    const REPORT_ERROR_OFF = 0;
    const REPORT_ERROR_ON = 1;
    const REPORT_ERROR_ALL = 2;


    /**
     * reportlevel errors REPORT_ERROR_OFF or REPORT_ERROR_ALL
     */
    public function setErrorReporting($iReportLevel = TDBConnection::REPORT_ERROR_OFF)
    {
        $this->iErrorReportLevel = $iReportLevel;

        if ($iReportLevel == TDBConnection::REPORT_ERROR_OFF)
            mysqli_report(MYSQLI_REPORT_OFF);
        if ($iReportLevel == TDBConnection::REPORT_ERROR_ON)
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        if ($iReportLevel == TDBConnection::REPORT_ERROR_ALL)
            mysqli_report(MYSQLI_REPORT_ALL);
    }

    public function setHost($sHost)
    {
        $this->sHost = $sHost;
    }

    public function setUsername($sUsername)
    {
        $this->sUsername = $sUsername;
    }

    public function setPassword($sPassword)
    {
        $this->sPassword = $sPassword;
    }

    public function setDatabaseName($sDatabaseName)
    {
        $this->sDatabaseName = $sDatabaseName;
    }

    public function setPort($iPort)
    {
        $this->iPort = $iPort;
    }

    public function getHost()
    {
        return $this->sHost;
    }

    public function getUsername()
    {
        return $this->sUsername;
    }

    public function getPassword()
    {
        return $this->sPassword;
    }

    public function getDatabaseName()
    {
        return $this->sDatabaseName;
    }

    public function getPort()
    {
        return $this->iPort;
    }


    /**
     * @param string $sSQL
     * @param int $iMaxResultCount - limiteer het aantal terug te geven records
     * @param int $iOffset - start met recordnr (voor paginate functie)
     * @return TDBResultset object
     */
//     abstract public function executeQuery($sSQL, $iMaxResultCount = 0, $iOffset = 0);

    /**
     * @return TDBQuery object
     */
    abstract public function getQuery();
    
    /**
     * @return TDBPreparedStatement
     */
    abstract public function getPreparedStatement();

  
    
    /**
     * verkrijgen van gebruikte database component
     * @return object -> gebruikte databaseobject
     */
    public function getAPIConnObject()
    {
        return $this->objInternalDatabaseAPIConnectionObject;
    }

    /**
     * Het instellen van het interne databaseobject
     * @param object -> het interne databaseobject
     */
    public function setInternalDatabaseObject($objDatabaseObject)
    {
        $this->objInternalDatabaseAPIConnectionObject = $objDatabaseObject;
    }

    //abstracte functies - functies die per se overerfd moeten worden
    abstract public function connect();
    abstract public function disconnect();
    abstract public function isConnected();
    abstract public function prepare($sPreparedStatement); //returns prepared statement object (TDBPreparedStatement child)
    
    /**
     * commit a database transaction and return to autocommit state
     */
    abstract public function commit();

    /**
     * rollback a database transaction and return to autocommit state
     */
    abstract public function rollback();

    /**
     * disable autocommit and starting a database transaction
     */
    abstract public function startTransaction();

    /**
     * bogus function for calling startTransaction
     */
    public function beginTransaction()
    {
        $this->startTransaction();
    }
    
    
}
?>
