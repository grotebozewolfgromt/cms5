<?php
namespace dr\classes\mime;

use dr\classes\patterns\TObject;
/**
 * Description of TMimeAbstract
 * 
 * some generic functions for TMimeMessage and TMimePart
 *
 * @author drenirie
 */
abstract class TMimeAbstract
{
    private $sContentType = MIME_TYPE_OCTETSTREAM;
    private $sCharset;
    private $sContentTransferEncoding = TMime::ENCODING_8BIT;
   
    
    public function  __construct()
    {
       
    }

    public function __destruct()
    {
       
    }        
    
    public function setContentType($sContentType)
    {
        $this->sContentType = $sContentType;
    }
    
    public function getContentType()
    {
        return $this->sContentType;
    }
    
    public function setCharset($sCharset)
    {
        $this->sCharset = $sCharset;
    }
    
    public function getCharset()
    {
        return $this->sCharset;
    }
    
    public function setContentTransferEncoding($sEncoding)
    {
        $this->sContentTransferEncoding = $sEncoding;
    }
    
    public function getContentTransferEncoding()
    {
        return $this->sContentTransferEncoding;
    }
    
    /**
     * Return the headers for this part as a string
     *
     * @param string $sEOL
     * @return String
     */
    public function getHeaders($sEOL = TMime::LINEEND)
    {
        $sStringHeader = '';
        $arrTotalHeader = $this->parseHeadersArray($sEOL);
        foreach ($arrTotalHeader as $arrHeader) 
            $sStringHeader .= $arrHeader[0] . ': ' . $arrHeader[1] . $sEOL;


        return $sStringHeader;
    }  
    
    abstract public function parseHeadersArray($sEOL = TMime::LINEEND);
    
            
    
}

?>
