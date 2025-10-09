<?php
namespace dr\classes\mime;

/**
 * Description of TMimePart
 *
 * @author drenirie

 * class represents an Mime-part in a Mime-message (TMimeMessage)
 */
class TMimePart extends TMimeAbstract
{
    
    //values
//    private $sID;
    private $sContentDisposition;//will be 'attachment' for attachments    
    private $sFileName;//will contain a file name  (i.e. 'gnome.jpeg') for attachments
//    private $sDescription;
//    private $sLocation;
//    private $sLanguage;    
    private $mContent;
    protected $isStream = false;    
    
    
    private $iCreationDateAttachmentTimeStamp = 0;
    private $iModificationDateAttachmentTimeStamp = 0;
    private $iReadDateAttachmentTimeStamp = 0;
    
       
    
    public function  __construct($mContent = null)
    {
        parent::__construct();
        
        if ($mContent)
            $this->setContent ($mContent);   
    }   
    
    
    public function setContent($mContent)
    {
        $this->mContent = $mContent;
        if (is_resource($mContent)) {
            $this->isStream = true;
        }        
        
    }
    
    
    
    /**
     * check if this part can be read as a stream.
     * if true, getEncodedStream can be called, otherwise
     * only getContent can be used to fetch the encoded
     * content of the part
     *
     * @return bool
     */
    public function isStream()
    {
      return $this->isStream;
    }    
    
    /**
     * if this was created with a stream, return a filtered stream for
     * reading the content. very useful for large file attachments.
     *
     * @param string $EOL
     * @return stream
     * @throws Exception\RuntimeException if not a stream or unable to append filter
     */
    public function getEncodedStream($EOL = Mime::LINEEND)
    {
        if (!$this->isStream) {
            throw new Exception\RuntimeException('Attempt to get a stream from a string part');
        }

        //stream_filter_remove(); // ??? is that right?
        switch ($this->encoding) {
            case TMime::ENCODING_QUOTEDPRINTABLE:
                $filter = stream_filter_append(
                    $this->content,
                    'convert.quoted-printable-encode',
                    STREAM_FILTER_READ,
                    array(
                        'line-length'      => 76,
                        'line-break-chars' => $EOL
                    )
                );
                if (!is_resource($filter)) {
                    throw new Exception\RuntimeException('Failed to append quoted-printable filter');
                }
                break;
            case TMime::ENCODING_BASE64:
                $filter = stream_filter_append(
                    $this->content,
                    'convert.base64-encode',
                    STREAM_FILTER_READ,
                    array(
                        'line-length'      => 76,
                        'line-break-chars' => $EOL
                    )
                );
                if (!is_resource($filter)) {
                    throw new Exception\RuntimeException('Failed to append base64 filter');
                }
                break;
            default:
        }
        return $this->content;
    }    

    
    /**
     * Get the Content of the current Mime Part in the given encoding.
     *
     * @param string $EOL
     * @return string
     */
    public function getContent($EOL = TMime::LINEEND)
    {
        if ($this->isStream) {
            return stream_get_contents($this->getEncodedStream($EOL));
        }
        
        return TMime::encode($this->mContent, $this->getContentTransferEncoding(), $EOL);
    }    
    
    /**
     * Get the RAW unencoded content from this part
     * @return string
     */
    public function getRawContent()
    {
        if ($this->isStream) {
            return stream_get_contents($this->getContent());
        }
        return $this->mContent;
    }    
    
    /**
     * set timestamp
     * 
     * @param int $iTimeStamp
     */
    public function setCreationDateAttachment($iTimeStamp)
    {
        $this->iCreationDateAttachmentTimeStamp = $iTimeStamp;
    }
    
    public function getCreationDateAttachment()
    {
        return $this->iCreationDateAttachmentTimeStamp;
    }
    
    /**
     * set timestamp
     * 
     * @param int $iTimeStamp
     */
    public function setModificationDateAttachment($iTimeStamp)
    {
        $this->iModificationDateAttachmentTimeStamp = $iTimeStamp;
    }
    
    public function getModificationDateAttachment()
    {
        return $this->iModificationDateAttachmentTimeStamp;
    }
    
    /**
     * set timestamp
     * 
     * @param int $iTimeStamp
     */
    public function setReadDateAttachment($iTimeStamp)
    {
        $this->iReadDateAttachmentTimeStamp = $iTimeStamp;       
    }
    
    public function getReadDateAttachment()
    {
        return $this->iReadDateAttachmentTimeStamp;
    }
    
    public function setContentDisposition($sDisposition)
    {
        $this->sContentDisposition = $sDisposition;
        $this->setCharset('');//when attachment is added, no charset. this created unnecessary overhead it the already chunky attachment
    }
    
    public function getContentDisposition()
    {
        return $this->sContentDisposition;
    }
    
    public function setFilename($sFilename)
    {
        $this->sFileName = $sFilename;
    }
    
    public function getFilename()
    {
        return $this->sFileName;
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
        if (stristr($this->getContentType(), 'text')) //alleen van het type text een charset toevoegen
        {
            if ($this->getCharset()) 
            {
                $sContentType .= '; charset=' . $this->getCharset();
            }
        }

        $arrHeader[] = array('Content-Type', $sContentType);
        //END --- compiling content type

        
        if ($this->getContentTransferEncoding()) 
        {
            $arrHeader[] =  array('Content-Transfer-Encoding',$this->getContentTransferEncoding());
        }

        
        
        
        
//        if ($this->id) 
//        {
//            $arrHeader[]  = array('Content-ID', '<' . $this->id . '>');
//        }

        if ($this->getContentDisposition()) 
        {
            $sDisposition = $this->getContentDisposition();
            if ($this->getFilename()) 
            {
                $sDisposition .= '; filename="' . $this->getFilename() . '"';
            }
            
            //add dates
            if (is_numeric($this->getCreationDateAttachment()))
                if ($this->getCreationDateAttachment() > 0)
                    $sDisposition .= ';'.$sEOL.'creation-date='.date('r', $this->getCreationDateAttachment());             
            if (is_numeric($this->getModificationDateAttachment()))
                if ($this->getModificationDateAttachment() > 0)
                    $sDisposition .= ';'.$sEOL.'modification-date='.date('r', $this->getModificationDateAttachment());             
            if (is_numeric($this->getReadDateAttachment()))
                if ($this->getReadDateAttachment() > 0)
                    $sDisposition .= ';'.$sEOL.'read-date='.date('r', $this->getReadDateAttachment());             
            
            $arrHeader[] = array('Content-Disposition', $sDisposition);
        }

//        if ($this->description) {
//            $arrHeader[] = array('Content-Description', $this->description);
//        }
//
//        if ($this->location) {
//            $arrHeader[] = array('Content-Location', $this->location);
//        }
//
//        if ($this->language) {
//            $arrHeader[] = array('Content-Language', $this->language);
//        }

        return $arrHeader;
    }    
    
             
    
}

?>
