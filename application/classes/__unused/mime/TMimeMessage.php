<?php
namespace dr\classes\mime;


/**
 * Description of TMimePart and TMimeMessage
 * 
 * 
 *
 * Multipurpose Internet Mail Extensions
 * 
 * Een emailbericht kan verschillende Mime-parts bevatten. Dit kan een html tekst zijn, een plain tekst of een attachment
 * 
 * 
 * niet alle types worden nu in deze klasse ondersteund. Mochten ze nodig zijn: kun je ze bijprogrammeren, maar het leek me nutteloos 
 * om deze allemaal nu te integreren
 * 
 * 
 * 
 * In deze mime klassen zijn alle waardes plain opgeslagen, dat wil zeggen dat ze nog niet ge-encrypt zijn. Dat gebeurd pas bij de parse functies
 * Bijvoorbeeld: de attachments zijn letterlijk de inhoud van de file (en nog niet hun base64 decoded versies)
 * 
 * @todo encoding van berichten werkt niet goed, van de parts werkt wel goed
 * @todo klasse testen met verschillende mailclients

 * 
 * 
 * 14 feb 2013: TMime: created
 * 
 * @author drenirie
 */


/**
 * class represents an email message
 */
class TMimeMessage extends TMimeAbstract
{   
    private $arrParts = array();//array of TMime parts    
    private $sBoundary = '';    
    private $sMessageNotSupported = 'This is a message in Mime Format. If you see this, your mail reader does not support Mime format';    
    
    protected static $makeUnique = 0;
    
    
    public function  __construct()
    {
        $this->setBoundary($this->generateBoundary());
        parent::__construct();
    }

    public function __destruct()
    {
       
    }       
    
    /**
     * Check if message needs to be sent as multipart
     * MIME message or if it has only one part.
     *
     * @return bool
     */
    public function isMultiPart()
    {
        return (count($this->arrParts) > 1);
    }    
    
    /**
     * generates a 13 characters long boundary
     * 
     * @return string
     */
    private function generateBoundary()
    {        
        return '=_' . md5(microtime(1) . static::$makeUnique++);
    }
    
    public function setMessageNotSupported($sNotSupported)
    {
        $this->sMessageNotSupported = $sNotSupported;
    }
    
    public function getMessageNotSupported()
    {
        return $this->sMessageNotSupported;
    }
    
    public function getMimeVersion()
    {
        return $this->sMimeVersion;
    }    
    
    public function addPart(TMimePart $objMimePart)
    {
        $this->arrParts[] = $objMimePart;
        
        $this->setContentType(MIME_TYPE_MULTIPART_MIXED); //--> when you add a part it is allways mixed          
    }
    
    public function getParts()
    {
        return $this->arrParts;
    }
    
    public function setBoundary($sBoundary)
    {
        $this->sBoundary = $sBoundary;
    }
    
    public function getBoundary()
    {
        return $this->sBoundary;
    }    

    
    /**
     * Generate MIME-compliant message from the current configuration
     *
     * This can be a multipart message if more than one MIME part was added. If
     * only one part is present, the content of this part is returned. If no
     * part had been added, an empty string is returned.
     *
     * Parts are separated by the mime boundary as defined in Zend_Mime. If
     * {@link setMime()} has been called before this method, the Zend_Mime
     * object set by this call will be used. Otherwise, a new Zend_Mime object
     * is generated and used.
     *
     * @param string $sEOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     * @return string
     */
    public function parseMessage($sEOL = TMime::LINEEND)
    {
        //removed 14 dec2020, because it had no boundary and therefore wasn't displayed in email client
        // if (!$this->isMultiPart()) 
        // {
        //     $part = current($this->arrParts);
        //     $sBody = $part->getContent($sEOL);
        // } 
        // else 
        {
            $sBoundaryLine = TMime::getBoundaryLine($this->getBoundary(),$sEOL);
            $sBody = $this->getMessageNotSupported().$sEOL;

            foreach (array_keys($this->arrParts) as $arrPartHeader) 
            {
                $sBody .= $sBoundaryLine
                       . $this->getPartHeaders($arrPartHeader, $sEOL)
                       . $sEOL
                       . $this->getPartContent($arrPartHeader, $sEOL);

            }

            logdev('====test123456789212');
            logdev($sBody);

            
            $sBody .= TMime::getMimeEnd($this->getBoundary(), $sEOL);
        }

        //encode
        if ($this->getContentTransferEncoding())
            $sBody = TMime::encode($sBody, $this->getContentTransferEncoding(), $sEOL);
        
        return trim($sBody);
    }    
    
    /**
     * Create and return the array of headers for this MIME part
     *
     * @param string $sEOL
     * @return array
     */
    public function parseHeadersArray($sEOL = TMime::LINEEND)
    {
        $arrHeader = array();

        //compiling content type
        $sContentType = $this->getContentType();
        if (stristr($this->getContentType(), 'text')) //only add when type = text
        {
            if ($this->getCharset()) 
                $sContentType .= '; charset=' . $this->getCharset();
        }
        
        if ($this->getBoundary()) 
            $sContentType .= ';' . $sEOL . " boundary=\"" . $this->getBoundary() . '"';

        $arrHeader[] = array('Content-Type', $sContentType);
        //END --- compiling content type

        
        if ($this->getContentTransferEncoding()) 
        {
            $arrHeader[] =  array('Content-Transfer-Encoding',$this->getContentTransferEncoding());
        }
        

        
        return $arrHeader;
    }    
    
    
    /**
     * generate message body
     * 
     * @return string
     */
//    protected function parseBodySpecific() 
//    {
//        $sBody = '';
//        
//       
//        //als er parts zijn
//        if (count($this->arrParts) > 0)
//        {
//            //de content als part toevoegen, omdat je deze anders bij een multipart-mixed niet meer terugziet
//            if ($this->getContent() != '')
//            {
//                $objPart = new TMimePart();
//                $objPart->setCharset($this->getCharset());
//                $objPart->setContentTransferEncoding($this->getContentTransferEncoding());
//                $objPart->setContentType($this->getContentType());
//                $objPart->setContent($this->getClone());
//                $this->addPart($objPart);
//            }
//            
//            //als de client mime niet kan lezen
//            $sBody .= $this->getMessageNotSupported().TMimeAbstract_old::EOL;
//            if ($this->getContent() != '') //als er content is dan deze ook plaintext nog even weergeven
//            {
//                $sBody .= 'Your message is displayed below:'.TMimeAbstract_old::EOL;
//                $sBody .= '*********************************'.TMimeAbstract_old::EOL;
//                $sBody .= $this->getContent().TMimeAbstract_old::EOL;
//            }
//           
//            
//            //parts langslopen
//            foreach ($this->arrParts as $objMimePart)
//            {                
//                $sBody .= '--'.$this->getBoundary().TMimeAbstract_old::EOL;
//                $sBody .= $objMimePart->parseHeader().TMimeAbstract_old::EOL;
////                $sBody .= TMimeAbstract::EOL; //lege regel scheidt header van body
//                $sBody .= $objMimePart->parseBody().TMimeAbstract_old::EOL;
//            }
//            
//            $sBody .= '--'.$this->getBoundary().'--'; //afsluiting dus geen EOL
//        }
//        else //als er geen parts zijn, maar alleen content dan deze weergeven
//            $sBody .= $this->getContent();            
//        
//        return $sBody;
//    }

    /**
     * generate mime message header
     */
//    protected function parseHeaderSpecific() 
//    {
//        $sHeader = '';
//        
//        $sHeader .= TMimeMessage::MIME_VERSION.': '.$this->getMimeVersion().TMimeAbstract_old::EOL;
//        
//        //content type, charset boundary then an EOL -->Many mail user agents also send messages with the file name in the name parameter of the content-type header instead of the filename parameter of the content-disposition header. This practice is discouraged ��� the file name should be specified either through just the filename parameter, or through both the filename and the name parameters
//        $sHeader .= TMimeAbstract_old::CONTENT_TYPE.': '.$this->getContentType();
//        if ($this->getCharset() != '')
//            $sHeader .= '; '.TMimeAbstract_old::CHARSET.'='.$this->getCharset();
//        if ($this->getBoundary() != '')        
//            $sHeader .= '; '.TMimeAbstract_old::BOUNDARY.'='.$this->getBoundary();                
////        $sHeader .= TMimeAbstract::EOL;
//        
//        
//        return $sHeader;
//    }

    /**
     * Get the headers of a given part as an array
     *
     * @param int $partnum
     * @return array
     */
    public function getPartHeadersArray($partnum)
    {
        return $this->arrParts[$partnum]->getHeadersArray();
    }

    /**
     * Get the headers of a given part as a string
     *
     * @param int $partnum
     * @param string $EOL
     * @return string
     */
    public function getPartHeaders($partnum, $EOL = TMime::LINEEND)
    {
        return $this->arrParts[$partnum]->getHeaders($EOL);
    }

    /**
     * Get the (encoded) content of a given part as a string
     *
     * @param int $partnum
     * @param string $EOL
     * @return string
     */
    public function getPartContent($partnum, $EOL = TMime::LINEEND)
    {
        return $this->arrParts[$partnum]->getContent($EOL);
    }

    /**
     * Explode MIME multipart string into separate parts
     *
     * Parts consist of the header and the body of each MIME part.
     *
     * @param string $body
     * @param string $boundary
     * @throws Exception\RuntimeException
     * @return array
     */
    protected static function _disassembleMime($body, $boundary)
    {
        $start  = 0;
        $res    = array();
        // find every mime part limiter and cut out the
        // string before it.
        // the part before the first boundary string is discarded:
        $p = strpos($body, '--' . $boundary."\n", $start);
        if ($p === false) {
            // no parts found!
            return array();
        }

        // position after first boundary line
        $start = $p + 3 + strlen($boundary);

        while (($p = strpos($body, '--' . $boundary . "\n", $start)) !== false) {
            $res[] = substr($body, $start, $p-$start);
            $start = $p + 3 + strlen($boundary);
        }

        // no more parts, find end boundary
        $p = strpos($body, '--' . $boundary . '--', $start);
        if ($p===false) {
            throw new Exception\RuntimeException('Not a valid Mime Message: End Missing');
        }

        // the remaining part also needs to be parsed:
        $res[] = substr($body, $start, $p-$start);
        return $res;
    }

    /**
     * Decodes a MIME encoded string and returns a Zend_Mime_Message object with
     * all the MIME parts set according to the given string
     *
     * @param string $message
     * @param string $boundary
     * @param string $EOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     * @throws Exception\RuntimeException
     * @return \Zend\Mime\Message
     */
//    public static function createFromMessage($message, $boundary, $EOL = Mime::LINEEND)
//    {
//        $parts = Decode::splitMessageStruct($message, $boundary, $EOL);
//
//        $res = new static();
//        foreach ($parts as $part) {
//            // now we build a new MimePart for the current Message Part:
//            $newPart = new Part($part['body']);
//            foreach ($part['header'] as $header) {
//                /** @var \Zend\Mail\Header\HeaderInterface $header */
//                /**
//                 * @todo check for characterset and filename
//                 */
//
//                $fieldName  = $header->getFieldName();
//                $fieldValue = $header->getFieldValue();
//                switch (strtolower($fieldName)) {
//                    case 'content-type':
//                        $newPart->type = $fieldValue;
//                        break;
//                    case 'content-transfer-encoding':
//                        $newPart->encoding = $fieldValue;
//                        break;
//                    case 'content-id':
//                        $newPart->id = trim($fieldValue,'<>');
//                        break;
//                    case 'content-disposition':
//                        $newPart->disposition = $fieldValue;
//                        break;
//                    case 'content-description':
//                        $newPart->description = $fieldValue;
//                        break;
//                    case 'content-location':
//                        $newPart->location = $fieldValue;
//                        break;
//                    case 'content-language':
//                        $newPart->language = $fieldValue;
//                        break;
//                    default:
//                        throw new Exception\RuntimeException('Unknown header ignored for MimePart:' . $fieldName);
//                }
//            }
//            $res->addPart($newPart);
//        }
//
//        return $res;
//    }

  
}


    



?>
