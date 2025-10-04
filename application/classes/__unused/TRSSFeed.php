<?php
namespace dr\classes\files;

use dr\classes\patterns\TObject;
use dr\classes\patterns\TObjectList;

/**
 * Description of TRSSFeed
 *
 * with this class you can create an rss feed using the xml dom element classes of php
 * according to the RSS 2.0 specficat
 *
 * TODO: copyright/ auteur
 * TODO: loadFromFile: lezen van een feed (functionaliteit in zend)
 * TODO: laatste 10/20/100 resultaten renderen (niet alles)]
 * TODO: saveToFile
 *
 * @author dennis renirie
 */
class TRSSFeed
{
    private $objItems = null; //TObjectlist met instanties van TRSSFeedItem
    private $sTitle = '';
    private $sLink = '';
    private $sDescription = '';



    public function __construct()
    {
        $this->objItems = new TObjectlist();
    }

    public function __destruct()
    {
        unset($this->objItems);
    }

    /**
     * add an RSS item to the list
     * @param TRSSFeedItem $objItem
     */
    public function add(TRSSFeedItem $objItem)
    {
        $this->objItems->add($objItem);
    }

    /**
     * count the number of items in the itemlist
     * @return int
     */
    public function count()
    {
        return $this->objItems->count();
    }

    public function setTitle($sTitle)
    {
        $this->sTitle = $sTitle;
    }

    public function getTitle()
    {
        return $this->sTitle;
    }

    public function setDescription($sDescription)
    {
        $this->sDescription = $sDescription;
    }

    public function getDescription()
    {
        return $this->sDescription;
    }

    public function setLink($sLink)
    {
        $this->sLink = $sLink;
    }

    public function getLink()
    {
        return $this->sLink;
    }

    /**
     * renders the RSS feed and returns result as a string
     *
     * @param string $sURLOfTheXMLFile for compliance with W3C you need to supply the location of the feed in the feed itself
     * @return string
     */
    public function renderRSS20($sURLOfTheXMLFile = '')
    {
        $objXMLDoc = new DOMDocument();
        $objXMLDoc->formatOutput = true;

        //rss element
        $objRSSElement = $objXMLDoc->createElement('rss');
        $objRSSElement->setAttribute('version', '2.0');
        $objRSSElement->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom'); //-->voor W3C compliance
        $objXMLDoc->appendChild($objRSSElement);

        //1 single channel element (allways)
        $objChannel = $objXMLDoc->createElement('channel');
        $objRSSElement->appendChild( $objChannel );

        //atom rotzooi -->voor W3C compliance
        if ($sURLOfTheXMLFile != '')
        {
            $objAtomLink = $objXMLDoc->createElement('atom:link');
            $objAtomLink->setAttribute('href', $sURLOfTheXMLFile);
            $objAtomLink->setAttribute('rel', 'self');
            $objAtomLink->setAttribute('type', 'application/rss+xml');
            $objChannel->appendChild( $objAtomLink );
        }


        //title
        $objTitle = $objXMLDoc->createElement('title');
        $objXMLTitleText = $objXMLDoc->createTextNode($this->getTitle());
        $objTitle->appendChild( $objXMLTitleText );
        $objChannel->appendChild( $objTitle );

        //link
        $objLink = $objXMLDoc->createElement('link');
        $objXMLLinkText = $objXMLDoc->createTextNode($this->getLink());
        $objLink->appendChild( $objXMLLinkText );
        $objChannel->appendChild( $objLink );

        //description
        $objDescription = $objXMLDoc->createElement('description');
        $objXMLDescriptionText = $objXMLDoc->createTextNode($this->getDescription());
        $objDescription->appendChild( $objXMLDescriptionText );
        $objChannel->appendChild( $objDescription );

        //Publication date
        $objXMLPubDate = $objXMLDoc->createElement('pubDate');
        $objXMLPubDateText = $objXMLDoc->createTextNode(date('r', now()));
        $objXMLPubDate->appendChild( $objXMLPubDateText );
        $objChannel->appendChild( $objXMLPubDate );


        //walk through al the feeditems
        for ($iTeller = 0; $iTeller < $this->count(); $iTeller++)
        {
            //item oproepen
            $objRSSFeedItem = $this->get($iTeller);
            $objRSSFeedItem->renderRSS20($objXMLDoc, $objChannel);
        }

        //renderen
        $sReturn = $objXMLDoc->saveXML();
        
        unset($objXMLDoc);

        return $sReturn;
    }

    /**
     * get the TRSSFeedItem object of index
     * 
     * @param index $iIndex index of the feed item
     * @return TRSSFeedItem
     */
    public function get($iIndex)
    {
        return $this->objItems->get($iIndex);
    }
}

class TRSSFeedItem
{
    private $sTitle = '';
    private $sLink = '';
    private $sDescription = '';
    private $sGUID = '';
    private $iPubDate = 0;

    public function __construct()
    {
        $this->setPubDate(now());
    }

    public function setTitle($sTitle)
    {
        $this->sTitle = $sTitle;
    }

    public function getTitle()
    {
        return $this->sTitle;
    }

    public function setDescription($sDescription)
    {
        $this->sDescription = $sDescription;
    }

    public function getDescription()
    {
        return $this->sDescription;
    }

    public function setLink($sLink)
    {
        $this->sLink = $sLink;
    }

    public function getLink()
    {
        return $this->sLink;
    }

    public function setGUID($sGUID)
    {
        $this->sGUID = $sGUID;
    }

    public function getGUID()
    {
        return $this->sGUID;
    }

    /**
     * get the unix timestamp of the publication date
     * @return int
     */
    public function getPubDate()
    {
        return $this->iPubDate;
    }

    /**
     * set publication date by supplying a unix timestamp
     * @param int $iUnixTimeStamp
     */
    public function setPubDate($iUnixTimeStamp)
    {
        $this->iPubDate = $iUnixTimeStamp;
    }

    public function renderRSS20(DOMDocument $objXMLDoc, DOMElement $objChannel)
    {
        //new item in channel
        $objXMLItem = $objXMLDoc->createElement('item');
        $objChannel->appendChild( $objXMLItem );

        //subitems aan item toevoegen
        //title
        $objXMLTitle = $objXMLDoc->createElement('title');
        $objXMLTitleText = $objXMLDoc->createTextNode($this->getTitle());
        $objXMLTitle->appendChild( $objXMLTitleText );
        $objXMLItem->appendChild( $objXMLTitle );

        //description
        $objXMLDescription = $objXMLDoc->createElement('description');
        $objXMLDescriptionText = $objXMLDoc->createTextNode($this->getDescription());
        $objXMLDescription->appendChild( $objXMLDescriptionText );
        $objXMLItem->appendChild( $objXMLDescription );

        //link
        $objXMLLink = $objXMLDoc->createElement('link');
        $objXMLLinkText = $objXMLDoc->createTextNode($this->getLink());
        $objXMLLink->appendChild( $objXMLLinkText );
        $objXMLItem->appendChild( $objXMLLink );

        //GUID
        $objXMLGUID = $objXMLDoc->createElement('guid');
        $objXMLGUIDText = $objXMLDoc->createTextNode($this->getGUID());
        $objXMLGUID->appendChild( $objXMLGUIDText );
        $objXMLItem->appendChild( $objXMLGUID );

        //Publication date
        $objXMLPubDate = $objXMLDoc->createElement('pubDate');
        $objXMLPubDateText = $objXMLDoc->createTextNode(date('r', $this->getPubDate()));
        $objXMLPubDate->appendChild( $objXMLPubDateText );
        $objXMLItem->appendChild( $objXMLPubDate );

    }
}
?>
