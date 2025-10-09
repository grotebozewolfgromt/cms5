<?php
namespace dr\classes\dom;


use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\Noscript;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\A;
use dr\classes\dom\tag\Ul;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;

/**
 * Description of TPaginator for data pagination
 *
 * With this class you can paginate your results e.a. a database.
 * Example: when you search google, after 10 results you reach the end of the page
 * but when you want to see more results, you can click on a pagenr to see more results
 * Generating that you can click on those pages does this class for you
 * It helps you devide you results into pages and generate HTML code for navigating these pages
 *
 * This class is Arnold Schwarzenegger for your search results: it chops your search
 * results in to pieces/pages.
 *
 * for (database) performance reasons this class restricts the maximum number of pages
 * 
 * LETOP : PAGE INDEX en ITEM INDEX
 * ===========
 * The pageindex starts at 1 (not 0, like arrays in php)!!!!
 * The items start with 0, but are visually represented in the GUI starting with 1!!!!
 *
 *
 * generateHTMLList() generates a list of pages
 * 
 * 24 juli 2012: TPaginator: makeGuiList() rename -> generateHTMLList()
 * 24 okt 2012: TPaginator: added style setting
 * 24 okt 2012: TPaginator: added maximum pages setting
 * 24 okt 2012: TPaginator: rename, geldt nu niet meer alleen voor current page, maar kan ook voor andere pagina's gelden: countItemsOnPage()
 * 24 okt 2012: TPaginator: verschillende aanpassingen, zodat je middels een parameter ook informatie van andere pagina's dan de huidige kunt opvragen
 * 24 okt 2012: TPaginator: getPagesAsArray() verwijderd. mij ontging het nut van deze functie
 * 24 okt 2012: TPaginator: bugfix: next en previousPageNumber
 * 24 okt 2012: TPaginator: werkt nu met variabelen in url die automatisch in de constructor worden gelezen
 * 25 okt 2012: TPaginator: bugfix: undefined index in constructor
 * 25 okt 2012: TPaginator: getPageCount(): takes the maximum number of pages into account
 * 25 okt 2012: TPaginator: generateHTMLList(): shows dots when maximum number of pages is reached
 * 25 okt 2012: TPaginator: getItemRangeFrom(), getItemRangeTo hebben een rename gehad zodat het duidelijker is wat de functie doet
 * 25 okt 2012: TPaginator: getLastPageNumber(): takes the maximum number of pages into account
 * 25 okt 2012: TPaginator: countItemsOnPage(): ging voor de laatste pagina fout (wanneer maximum number of pages overschreden was)
 * 25 okt 2012: TPaginator: init() toegevoegd om de constructor kleiner te maken
 * 25 okt 2012: TPaginator: init(): laad en slaat het aantal items per pagina op in de sessie of cookie (als cookies zijn toegestaan)
 * 05 aug 2014: TPaginator: getOffset() alias toegevoegd
 * 07 aug 2014: TPaginator: getItemRangeFrom() bugfix: foutje in berekening
 * 07 aug 2014: TPaginator: setCurrentPage() bugfix: bij wisselen van items-per-page zou je best wel eens op de laatste pagina kunnen zitten. en ga je dus buiten je resultset. aangepast zodat je nooit hoger dan de laatste pagina kunt ingeven
 * 11 aug 2014: TPaginator: setCurrentPage(), setTotalItemsCount() bugfix: bij wisselen van items-per-page zou je best wel eens op de laatste pagina kunnen zitten. dit werkte niet goed en werden negatieve getallen weergegeven. Dit is opnieuw opgepakt
 * 2 apr 2014: TPaginator: Method_post constante gebruikt
 * 7 mei 2015: TPaginator: nu 'results 1 out of 20' in stead of 'results 0 out of 19' 
 * 7 mei 2015: TPaginator: verschillende bugfixes die aantal items niet goed telden en pagina's.-- verholpen
 * 7 mei 2015: TPaginator: isSecondLastPage() toegevoegd
 * 7 mei 2015: TPaginator: getShowLoadMore() toegevoegd
 * 7 mei 2015: TPaginator: getTextLoadMore() toegevoegd
 * 1 mei 2019: TPaginator: generateHTMLList() extra parameter voor css-class zonder link
 * 1 mei 2019: TPaginator: generateHTMLList() heeft een overhaul gekregen zodat het nu elastissch werkt. en meer dan 10 pagina's aan kan
 * 19 okt 2023: TPaginator: setCurrentPage() bugfix: deed altijd een conversie van strToInt()
 * 19 okt 2023: TPaginator: getCurrentPageFirstItem() added
 * 19 okt 2023: TPaginator: getCurrentPageLastItem() added
 * 26 apr 2025: TPaginator: isLastPage() bugfix. gaf max 8 pagina's terug
 * 
 * 
 *
 * TODO: cache the settings like items-per-page and total search results (hoeft niet iedere keer een query uit te voeren)
 * TODO: verschillende views:
Scrolling style 	Description
All 	Returns every page. This is useful for dropdown menu pagination controls with relatively few pages. In these cases, you want all pages available to the user at once.
Elastic 	A Google-like scrolling style that expands and contracts as a user scrolls through the pages.
Jumping 	As users scroll through, the page number advances to the end of a given range, then starts again at the beginning of the new range.
Sliding 	A Yahoo!-like scrolling style that positions the current page number in the center of the page range, or as close as possible. This is the default style. 
 *
 * @author dennis
 */
class TPaginator
{    
    private $iTotalItemsCount = 0;
    private $iCurrentPage = 1; //the first page is page 1 (not 0)
    private $iItemsPerPage = 100;
    private $iScrollingStyle = TPaginator::STYLE_PREVIOUSNEXT;    
    private $iMaximumNumberOfPagesLimit = 8; //maximum of X pages. If we have 1 million records, you probably don't want to view them all so we stop searching after X pages (so the database query can be limited to X pages of Y results-per-page)
    private $sGETVariableNamePageNumber = 'page'; //de $_GET[] variabele die in de constructor wordt uitgelezen
    private $sPOSTVariableNameResultsPerPage = 'resultsperpage';//de $_POST[] variabele die in de constructor wordt uitgelezen
    private $sSESSIONVariableNameResultsPerPage = 'TPaginator_results-per-page'; //de $_SESSION[] variabele waar het aantal resultaten per pagina worden opgeslagen (sessie wordt gebruikt als cookies niet mogen)
    private $sCOOKIEVariableNameResultsPerPage = 'TPaginator_results-per-page'; //de $_COOKIE[] variabele waar het aantal resultaten per pagina worden opgeslagen (sessie wordt gebruikt als cookies niet mogen)

    //STYLE_PREVIOUSNEXT: shows only 'previous' and 'next' pages
    //this style is the most database-friendly and fastest, because it doesn't need to know the total number of pages (so no extra database query). 
    //Er wordt altijd 1 extra record geladen dan wordt weergegeven. Zo weet je dat er meer resultaten zijn dan de huidige pagina (maar je weet niet hoeveel resultaten in totaal, je weet alleen dat er MEER resultaten zijn dan de huidige)
    //PREVIOUSNEXT LET OP!!: zorg dat je uit de database altijd 1 record MEER opvraagt (en set met setTotalItemsCount()) dan het aantal items per pagina (getItemCountPerPage()), omdat je ALLEEN dan weet of er nog meer records zijn die met NEXT opgevraagd kunnen worden
    const STYLE_PREVIOUSNEXT = 0; //default, facebook-style -> it only shows the previous and next pages. 
    
    //STYLE_NUMBEREDPAGES: shows numbered pages - most used and user friendly, google-style
    //This uses 2 database queries (1 for the data and 1 for the total count of the pages)
    //the total pagecount will be limited to a maximum ($this->getMaximumNumberOfPages) due to performance issues
    const STYLE_NUMBEREDPAGES = 1; //most used and user friendly, google-style
    
    //STYLE_LOADMORE: shows 'load more' when more pages are present
    //This uses 2 database queries (1 for the data and 1 for the total count of the pages)
    //the total pagecount will be limited to a maximum ($this->getMaximumNumberOfPages) due to performance issues
    const STYLE_LOADMORE = 2; 
    
    
    public function __construct($iScrollStyle = TPaginator::STYLE_NUMBEREDPAGES)
    {    		
        $this->setCurrentPage($this->getFirstPageNumber(true));
        $this->setStyle($iScrollStyle); //set default scrolling style
        $this->init();
    }
    
    public function getGETVariableNamePageNumber()
    {
    		return $this->sGETVariableNamePageNumber;
    }
    
    public function setGETVariableNamePageNumber($sVarName)
    {
    		$this->sGETVariableNamePageNumber = $sVarName;
    		$this->processGETPOSTArray();
    }
    
    private function init()
    {

        if (isset($_SESSION[$this->sSESSIONVariableNameResultsPerPage]))
        {
            $sSessionVal = (int)$_SESSION[$this->sSESSIONVariableNameResultsPerPage];
            if (is_numeric($sSessionVal))
            {
                if ((int)$sSessionVal > 100) //maximum van 100 forceren
                   $this->setItemCountPerPage(100);
                else
                   $this->setItemCountPerPage((int)$sSessionVal);
            }
        }            


        $this->processGETPOSTArray();        
    }
    
    private function processGETPOSTArray()
    {
        //get en post variabelen verwerken
        if (array_key_exists($this->sGETVariableNamePageNumber, $_GET))
            $this->setCurrentPage($_GET[$this->sGETVariableNamePageNumber]); //instellen in paginator
        if (array_key_exists($this->sPOSTVariableNameResultsPerPage, $_POST))
        {
            //instellen in paginator
            $this->setItemCountPerPage($_POST[$this->sPOSTVariableNameResultsPerPage]);
        
            //opslaan
            if (is_numeric($_POST[$this->sPOSTVariableNameResultsPerPage])) //safety precautions
            {
                if (APP_COOKIES_ALLOWED) //opslaan in cookies
                {
                    setcookie($this->sCOOKIEVariableNameResultsPerPage, $_POST[$this->sPOSTVariableNameResultsPerPage], time() + YEAR_IN_SECS);//1 jaar opslaan
                }
                else //opslaan in session
                {
                    $_SESSION[$this->sSESSIONVariableNameResultsPerPage] = $_POST[$this->sPOSTVariableNameResultsPerPage];
                }
            }
        }
    }
    
    /**
     * set the paginator style, 
     * use the class constants as parameter (i.e. TPaginator::STYLE_NUMBEREDPAGES)
     * 
     * @param int $iScrollStyle
     */
    public function setStyle($iScrollStyle)
    {
        $this->iScrollingStyle = $iScrollStyle;
    }
    
    /**
     * returns the current paginator style.
     * it returns an integer wich you can match with the class constants (i.e. TPaginator::STYLE_NUMBEREDPAGES)
     * 
     * @return int
     */
    public function getStyle()
    {
        return $this->iScrollingStyle;
    }
    
    /**
     * you can change the maximum number of pages, so the result won't be endless
     * 
     * @param int $iLimit
     */
    public function setMaximumNumberOfPages($iLimit)
    {
       $this->iMaximumNumberOfPagesLimit = $iLimit; 
    }
    
    /**
     * get the limit of the maximum number of pages that will be shown
     * 
     * @return int
     */
    public function getMaximumNumberOfPages()
    {
       return $this->iMaximumNumberOfPagesLimit;
    }    
    
    /**
     * sets the total results to de
     * @param int $iResults
     */
    public function setTotalItemsCount($iResults)
    {
        if (is_numeric($iResults))
        {
            $this->iTotalItemsCount = strToInt($iResults);
            
            //het kan zo zijn dat als je het totaal set, dat je buiten de pagina-aantal gaat
            //bijvoorbeeld: je zit op pagina 7 van 10 items per pagina, maar als je switcht naar 100 items per pagina bestaat pagina 7 helemaal niet
            if ($this->getCurrentPage() > $this->getPageCount(true))
                $this->setCurrentPage ($this->getLastPageNumber ());
            
        }
        else
            $this->iTotalItemsCount = 0;
    }

    public function getTotalItemsCount()
    {
        return $this->iTotalItemsCount;
    }

    /**
     * sets the current page
     * 
     * first set the total number of results before setting the current page
     * 
     * 
     * @param int $iPageNumber
     */
    public function setCurrentPage($iPageNumber)
    {
        
        if (!is_numeric($iPageNumber))
            $iPageNumber = $this->getFirstPageNumber(true);

        
        if ($iPageNumber < $this->getFirstPageNumber(true))
            $iPageNumber = $this->getFirstPageNumber(true);


        
        //het kan zijn dat het totaal (nog) niet is opgegeven, dus checken we niet
        //dit kan het geval zijn :
        //1) doordat de current pagina in de constructor van de paginator wordt geset
        //2) er geen totalen worden opgegeven bij de previous-next styl
        if ($this->getTotalItemsCount() > 0)
        {                      
            if ($iPageNumber > $this->getLastPageNumber())
                $iPageNumber = $this->getLastPageNumber();
        }
        
        
        $this->iCurrentPage = $iPageNumber;
    }

    /**
     * get number of current page
     */
    public function getCurrentPage()
    {
        return $this->iCurrentPage;
    }

    /**
     * get the number of the FIRST item on the current page
     * So you can show the text: 'Showing [FIRST] to [LAST] of 431 entries'
     */
    public function getCurrentPageFirstItem()
    {
        return ($this->iCurrentPage - 1) * $this->iItemsPerPage + 1;
    }

    /**
     * get the number of the LAST item on the current page
     * So you can show the text: 'Showing [FIRST] to [LAST] of 431 entries'
     */
    public function getCurrentPageLastItem()
    {
        $iCalcLast = $this->iCurrentPage * $this->iItemsPerPage;
        
        //last page is exception: show total items count
        if ($this->getTotalItemsCount() < $iCalcLast) 
            return $this->getTotalItemsCount();
            
        return $iCalcLast;
    }    


    /**
     * sets how many items should be displayed per page
     * @param int $iItemsPerPage (if not a valid numerical value, then nothing will change)
     */
    public function setItemCountPerPage($iItemsPerPage)
    {
        if (is_int($iItemsPerPage))
            $this->iItemsPerPage = $iItemsPerPage;
        else
            $this->iItemsPerPage = strToInt($iItemsPerPage);
    }

    /**
     * gets how many items should be displayed in a page
     * @return int
     */
    public function getItemCountPerPage()
    {
        return $this->iItemsPerPage;
    }

    /**
     * get the total pages
     * 
     * does NOT work with STYLE_PREVIOUSNEXT (omdat je het totaal van de resultaten niet weet, je weet alleen of er nog meer resultaten zijn)
     * 
     * @param bool $bShowRealPageCount the real page count can exceed the maximum number of pages (TRUE == can exceed max, FALSE == maximum number of pages taken into account)
     * @return int
     */
    public function getPageCount($bShowRealPageCount = false)
    {
        $iRealPageCount = ceil($this->getTotalItemsCount() / $this->getItemCountPerPage());

        if (($iRealPageCount > $this->getMaximumNumberOfPages()) && (!$bShowRealPageCount)) //chop of the results if there are more pages than allowed AND you want not the real result
            return $this->getMaximumNumberOfPages();
        else
            return $iRealPageCount;            
    }

    /**
     * get number of next page
     * 
     * if you want to know the next of the last page, it will return the last page 
     * (so it will always output valid pagenumbers)
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return type
     */
    public function getNextPageNumber($iPageNo = 0)
    {
        
        if ($iPageNo == 0)
            $iPageNo = $this->getCurrentPage();

        if ($iPageNo == $this->getLastPageNumber(true)) //voorkomen dat je ooit 'valse' pagina nummers terug geeft
            return $iPageNo;
        else
            return $iPageNo + 1;
    }

    /**
     * get number of previous page
     *      
     * if you want to know the previous of the first page, it will return the first page 
     * (so it will always output valid pagenumbers)
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return type
     */    
    public function getPreviousPageNumber($iPageNo = 0)
    {
        if ($iPageNo == 0)
            $iPageNo = $this->getCurrentPage();
        
        if ($iPageNo == $this->getFirstPageNumber(true)) //voorkomen dat je ooit 'valse' pagina nummers terug geeft
            return $iPageNo;
        else
            return $iPageNo - 1;
    }

    /**
     * is the current page the last page ?
     * Then you know that you don't want to display the text 'NEXT' where a user can click on
     * @return bool
     */
    public function isLastPage()
    {
        return ($this->getLastPageNumber(true) == $this->getCurrentPage());
    }
    
    /**
     * is the current page the second last (een-na-laatste) page  ?
     * @return bool
     */
    public function isSecondLastPage()
    {
        if ($this->isLastPage())
                return false;

        return ($this->getLastPageNumber()-1 == $this->getCurrentPage());
    }    

    /**
     * is this the first page ?
     * Then you know that you don't want to display the text 'PREVIOUS' where a user can click on
     * @return bool
     */
    public function isFirstPage()
    {
        return ($this->getFirstPageNumber(true) == $this->getCurrentPage());
    }

    /**
     * return the first page number
     * this can be the offset or 1 (depending on $bShowRealPageCount)
     * 
     * @param boolean $bShowRealPageCount
     * @return int
     */
    public function getFirstPageNumber($bShowRealPageCount = false)
    {
        if ($bShowRealPageCount)
            return 1;
        else 
            return $this->getPageOffset();                    
    }

    /**
     * return last page number
     * 
     * does NOT work with STYLE_PREVIOUSNEXT (omdat je het totaal van de resultaten niet weet, je weet alleen of er nog meer resultaten zijn)
     * 
     * @param bool $bShowRealPageCount the real page count can exceed the maximum number of pages (TRUE == can exceed max, FALSE == maximum number of pages taken into account)
     * @return int
     */
    public function getLastPageNumber($bShowRealPageCount = false)
    {
        return $this->getPageCount($bShowRealPageCount);
    }
    
    /**
     * for the 'elastic' effect we calculate the page offset
     * when on current page = 8 then the pageoffset = 1, page 9 the pageoffset is 2
     * 
     * PREVIOUS 2 3 4 5 6 7 (8) 9 10 NEXT
     * 
     * @return int
     */
    public function getPageOffset()
    {
        $iPageOffset = 0;
        
        $iPageOffset = $this->getCurrentPage() - floor($this->getMaximumNumberOfPages() / 2) ; // divided by 2 because we want the current pgage to be somewhere in the middle of the pages
        
        if ($iPageOffset < 1)
            $iPageOffset = 1;
        
        return $iPageOffset;
    }

    /**
     * make a HTML unordered list
     * you can supply an url with the pagenumber. When you supply the text '[PAGE]'
     * in the url it will be replaced with the page number
     *
     * when $sUrlPAGEBetweenSquareBrackets == '' the current script page will be assumed
     * 
     * @param string $sCSSClassSelected the css class of the LI node that is selected
     * @param string $sClassNameNoLink the css class of the LI node that has no link (i.e. the 'PREVIOUS' on the first page)
     * @param string $sUrlPAGEBetweenSquareBrackets de url waar de links naar moeten verwijzen --> de pagina moet tussen vierkante haken staan als variabele [PAGE], bijvoorbeeld: www.mijnsite.nl/nieuws/pagina/[PAGE]/nieuws.html
     * @return ul
     */
    public function generateHTMLList($sCSSClassSelected = 'selected', $sClassNameNoLink = 'nolink', $sUrlPAGEBetweenSquareBrackets = '', $bAddPageNumbersToURLS = true)
    {
        $objUL = new Ul();
        
        
        if ($sUrlPAGEBetweenSquareBrackets == '')
        {
            $sUrlPAGEBetweenSquareBrackets = APP_URLTHISSCRIPT;
            $bAddPageNumbersToURLS = true;    //just te be sure
        }
        
        //first page
//        $sURL = str_replace('[PAGE]', $this->getFirstPageNumber(true), $sUrlPAGEBetweenSquareBrackets);
//        $objA = new a();
//        $objA->setHref($sURL);
//        $objA->setText($sFirstButtonText);
//
//        $objLI = new li();
//        $objLI->appendChild($objA);
//        $objUL->appendChild($objLI);

        //previous page
        if ($this->getCurrentPage() > $this->getFirstPageNumber(true))
        {
            $sURL = str_replace('[PAGE]', $this->getPreviousPageNumber(), $sUrlPAGEBetweenSquareBrackets);                                    
            if ($bAddPageNumbersToURLS)
                $sURL = addVariableToURL ($sURL, $this->sGETVariableNamePageNumber, $this->getPreviousPageNumber());
            
                
            $objA = new A();
            $objA->setHref($sURL);
            $objA->setText(transg('previous_results_paginator', 'Previous'));
            $objA->setTitle(transg('Previous [x] results (page [y])', '', 'x', $this->getItemCountPerPage(), 'y', $this->getPreviousPageNumber()));
            

            $objLI = new Li();
            $objLI->appendChild($objA);
            $objUL->appendChild($objLI);
        }
        else //no link when on the first page
        {
            $objLI = new Li();
            $objLI->setClass($sClassNameNoLink);
            $objLI->addText(transg('previous_results_paginator', 'Previous'));
            $objUL->appendChild($objLI);            
        }


        //numbered pages style
        if (($this->getStyle() == TPaginator::STYLE_NUMBEREDPAGES))
        {
            //individual pages
            $bIsLastPages = false; //did we reach the end of the pages?
            $bIsFirstPages = false;   //are this the first pages?
            $iStartOnPage = 0; //default non-existing page
            $iEndOnPage = 0;
            
            $iStartOnPage = $this->getPageOffset();            
            $iEndOnPage = $iStartOnPage + $this->getMaximumNumberOfPages() -1;
            if (($iStartOnPage + $this->getMaximumNumberOfPages()) > $this->getLastPageNumber(true)) //if we reach the end, prevent that we show pages beyond the last page
            {
                $bIsLastPages = true;
                $iStartOnPage = $this->getLastPageNumber(true) - $this->getMaximumNumberOfPages();
                if ($iStartOnPage <= 0)//it must be 1 or higher ($iMaximumNumberOfPagesLimit can calculate to a negative page number when the number of pages is lower than the page limit)
                    $iStartOnPage = 1;
                $iEndOnPage = $this->getLastPageNumber(true);
            }

            // show ... in the beginning and first page number
            if ($iStartOnPage > 2)
            {
                $bIsFirstPages = true;

                //show the FIRST page that exists
                $sURL = str_replace('[PAGE]', $this->getFirstPageNumber(true), $sUrlPAGEBetweenSquareBrackets);
                if ($bAddPageNumbersToURLS)
                    $sURL = addVariableToURL ($sURL, $this->sGETVariableNamePageNumber, $this->getFirstPageNumber(true));

                $objA = new A();
                $objA->setHref($sURL);
                $objA->setText($this->getFirstPageNumber(true));
                $objA->setTitle(transg('Go to the first page'));

                $objLI = new Li();
                $objLI->appendChild($objA);
                $objUL->appendChild($objLI);  
                
                if ($iStartOnPage != 2) //prevent that ... is showed when page 2 is displayed of the most right
                {
                    $objLI = new Li();
                    $objLI->setClass($sClassNameNoLink);
                    $objLI->addText('...');
                    $objUL->appendChild($objLI);         
                }
                     
            }
            
            
            
            for ($iPageCounter = $iStartOnPage; $iPageCounter <= $iEndOnPage; $iPageCounter++)
            {
               
                $sURL = str_replace('[PAGE]', $iPageCounter, $sUrlPAGEBetweenSquareBrackets);
                if ($bAddPageNumbersToURLS)
                    $sURL = addVariableToURL ($sURL, $this->sGETVariableNamePageNumber, $iPageCounter);


                $objA = new A();
                $objA->setHref($sURL);
                $objA->setText($iPageCounter);
                $objA->setTitle(transg('Results [x] - [y]', '', 'x',$this->getItemRangeFrom($iPageCounter) + 1, 'y', $this->getItemRangeTo($iPageCounter) + 1));//weergave items (begint intern op 0 op het scherm op 1)

                $objLI = new Li();
                if ($iPageCounter == $this->getCurrentPage())//als het de huidige pagina is
                    $objLI->setClass($sCSSClassSelected);                                        
                $objLI->appendChild($objA);
                $objUL->appendChild($objLI);  
                    
            }

            //if more results than the maximum number of pages can handle we show an extra <li> with 3 dots
            if ($this->getPageCount(true) > $this->getMaximumNumberOfPages())
            {
                if (!$bIsLastPages)
                {
                    if ($iEndOnPage != $this->getLastPageNumber(true) - 1) //prevent that ... is showed when page 43 is displayed when we have 44 pages
                    {
                        $objLI = new Li();
                        $objLI->setClass($sClassNameNoLink);
                        $objLI->addText('...');
                        $objUL->appendChild($objLI);         
                    }

                    
                    //show the last page that exists
                    $sURL = str_replace('[PAGE]', $this->getLastPageNumber(true), $sUrlPAGEBetweenSquareBrackets);
                    if ($bAddPageNumbersToURLS)
                        $sURL = addVariableToURL ($sURL, $this->sGETVariableNamePageNumber, $this->getLastPageNumber(true));
                
                    $objA = new A();
                    $objA->setHref($sURL);
                    $objA->setText($this->getLastPageNumber(true));
                    $objA->setTitle(transg('Go to the last page'));//weergave items (begint intern op 0 op het scherm op 1)

                    $objLI = new Li();
                    $objLI->appendChild($objA);
                    $objUL->appendChild($objLI);                       
                }
            }
            
            
            
        }
        
        
        
        //next page
        $bShowLinkNext = true; //default
        if (($this->getStyle() == TPaginator::STYLE_NUMBEREDPAGES))
            $bShowLinkNext = $this->getCurrentPage() < $this->getLastPageNumber(true);
        if (($this->getStyle() == TPaginator::STYLE_PREVIOUSNEXT))
            $bShowLinkNext = $this->getTotalItemsCount() > $this->getItemCountPerPage();
        
        if ($bShowLinkNext)
        {
            $sURL = str_replace('[PAGE]', $this->getNextPageNumber(), $sUrlPAGEBetweenSquareBrackets);
            if ($bAddPageNumbersToURLS)
                $sURL = addVariableToURL ($sURL, $this->sGETVariableNamePageNumber, $this->getNextPageNumber());

            $objA = new A();
            $objA->setHref($sURL);            
            $objA->setText(transg('next_results_paginator', 'Next'));
            $objA->setTitle(transg('Next [x] results (page [y])', '', 'x', $this->getItemCountPerPage(), 'y', $this->getNextPageNumber()));
            
            $objLI = new Li();
            $objLI->appendChild($objA);
            $objUL->appendChild($objLI);
        }
        else //no link when on the last page
        {
            $objLI = new Li();
            $objLI->setClass($sClassNameNoLink);            
            $objLI->addText(transg('next_results_paginator', 'Next'));
            $objUL->appendChild($objLI);               
        }
        

        //last page
//        $sURL = str_replace('[PAGE]', $this->getLastPageNumber(), $sUrlPAGEBetweenSquareBrackets);
//        $objA = new a();
//        $objA->setHref($sURL);
//        $objA->setText($sLastButtonText);
//
//        $objLI = new li();
//        $objLI->appendChild($objA);
//        $objUL->appendChild($objLI);
        

        return $objUL;
    }


    /**
     * make a HTML select box with page numbers
     *
     * @return select
     */
    public function generateHTMLSelect()
    {
        $objSelect = new Select();

        //pages
        for ($iTeller = 1; $iTeller <= $this->getPageCount(); $iTeller++ )
        {
            $objOption = new option();
            $objOption->setValue($iTeller);
            $objOption->setText($iTeller);
            $objSelect->appendChild($objOption);
        }


        return $objSelect;
    }

    /**
     * make a HTML select box with page numbers
     *
     * @param bool $bAddJavascriptToRefreshPage do you want to add javascript so this page does a refresh when another option is selected?
     * @return select
     */
    public function generateHTMLFormWithSELECTResultsPerPage($bAddJavascriptToRefreshPage = true)
    {
        $arrResultsPerPage = array(10, 20, 50, 100, 250, 500);
        
        
        $sUID = 'ResultsPerPage'.uniqid();  //de kans verkleinen dat je 2 paginators op 1 pagina gebruikt en daardoor geen unieke html id's hebt
        
        $objForm = new Form();
        $objForm->setName($sUID);
        $objForm->setID($sUID);
        $objForm->setMethod(Form::METHOD_POST);        
        $objForm->setAction(APP_URLTHISSCRIPT);        
        
        $objSelect = new Select();
        $objSelect->setName($this->sPOSTVariableNameResultsPerPage);
            
        if ($bAddJavascriptToRefreshPage)
        {            
            $objSelect->setOnchange('document.getElementById("'.$objForm->getID().'").submit();');
        }
        
        //add number of results
        foreach ($arrResultsPerPage as $sRPP)
        {               
            $objOption = new Option();
            $objOption->setValue($sRPP);
            $objOption->setText($sRPP);            
            
            if ($sRPP == $this->getItemCountPerPage())
                $objOption->setSelected(true);
            
            $objSelect->appendChild($objOption);
        }
        
        $objForm->appendChild($objSelect);        
        
        $objText = new Text();
        $objText->setText(' '.transg('results per page'));
        $objForm->appendChild($objText);
        
        //noscript toevoegen voor als er geen javascript is
        $objNoScript = new Noscript();
        $objSubmit = new InputSubmit();
        $objSubmit->setValue(transg('Change'));     
        $objNoScript->appendChild($objSubmit);
        $objForm->appendChild($objNoScript);
        
        return $objForm;
        
        
    }
    
    
    /**
     * get (translated) text 'Results X of Y'
     * i.e. 'Results 1 - 10'
     * 
     * @param int $iPageNo pagenumber you want to know the text of, 0 ==  current page
     * @return string
     */
    public function getTextResultsXOfY($iPageNo = 0)
    {
        return transg('Results [x] - [y]', '', 'x',$this->getItemRangeFrom($iPageNo) + 1, 'y', $this->getItemRangeTo($iPageNo) + 1);
    }
    
    /**
     * get (translated) text 'Results X of Y (out of Z)'
     * i.e. 'Results 1 - 10'
     * 
     * @param int $iPageNo pagenumber you want to know the text of, 0 ==  current page
     * @return string
     */
    public function getTextResultsXOfYOutOfZ($iPageNo = 0)
    {
    		return transg('Results [x] - [y] (out of [z])','', 'x',$this->getItemRangeFrom($iPageNo) + 1, 'y', $this->getItemRangeTo($iPageNo) + 1, 'z', $this->getTotalItemsCount());
    }    
    
    /**
     * finds out on wich page the item is displayed 
     * als 5 records per pagina, dan record 1 t/m 5 op de eerste pagina, 6 op de tweede
     *
     * @param int $iItemNumber
     * @return int 0=invalid input
     */
    public function getItemOnPage($iItemNumber)
    {
        //-1 omdat het eerste item op index 0 zit en niet op index 1
        //+firstpagenumber omdat de eerste paginanummer 1 is en niet 0
        if (is_numeric($iItemNumber))
        {
            $iPage = floor(($iItemNumber -1) / $this->getItemCountPerPage())+$this->getFirstPageNumber(true);

            if ($iPage > $this->getPageCount()) // als boven het maximum uit stijgt (dat kan eigenlijk dus niet)
                return $this->getPageCount();
            else
                return $iPage;
        }
        else
            return 0;
    }

    /**
     * counts the number of items on a page
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return int
     */
    public function countItemsOnPage($iPageNo = 0)
    {
        if ($iPageNo == 0)
            $iPageNo = $this->getCurrentPage();
        
        if ($iPageNo == $this->getLastPageNumber(true))
        {
            $iLastFullPage = $this->getPreviousPageNumber($iPageNo);
            $iAantalItemsAlGehad = ($this->getItemCountPerPage() * $iLastFullPage);
            $iOverschot = $this->getTotalItemsCount() - $iAantalItemsAlGehad;
            
            return $iOverschot;
        }
        else
            return $this->getItemCountPerPage();
    }

    /**
     * on page $iPageNo which is the FIRST item on that page ?
     * Its the X in : 'displaying results X - Y'
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return int
     */
    public function getItemRangeFrom($iPageNo = 0)
    {
        if ($iPageNo == 0)
            $iPageNo = $this->getCurrentPage();
            
        return ( $this->getItemCountPerPage() * ($iPageNo-1) );
    }
    
    /**
     * the database class talks about offset
     * so this is a handy alias
     * this is the ITEMS offset (not the pages offset)
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return int
     */
    public function getOffset($iPageNo = 0)
    {
        return $this->getItemRangeFrom($iPageNo);
    }


    /**
     * on page $iPageNo which is the LAST item on that page ?
     * Its the Y in : 'displaying results X - Y'
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return int
     */
    public function getItemRangeTo($iPageNo = 0)
    {
        if ($iPageNo == 0)
            $iPageNo = $this->getCurrentPage();
    
        if (($iPageNo == 1) && ($this->getPageCount() == 1)) //uitzondering als eerste en enige pagina is
            return $this->getTotalItemsCount()-1;
    		
        return $this->getItemRangeFrom($iPageNo) + $this->countItemsOnPage($iPageNo)-1;
    }

    /**
     * for the STYLE_LOADMORE
     *
     * determines if the text 'Show more' should be displayed
     * (bij de eerste en enige pagina niet, en op de laatste pagina ook niet)
     * 
     * @param int $iPageNo page number you want to know, 0 = currentpage
     * @return bool true=show;false=dont show
     */
    public function getShowLoadMore($iPageNo = 0)
    {
        if ($iPageNo == 0)
            $iPageNo = $this->getCurrentPage();    
                    
        //bij eerste en enige pagina
        if (($iPageNo == 1) && ($this->getPageCount() == 1)) 
            return false;
        
        //bij laatste pagina
        if ($this->isLastPage())
            return false;
        
        return true;
    }    
    
    /**
     * for the STYLE_LOADMORE
     * 
     * returns language aware text 'load next 30 results' (depending on the getItemCountPerPage setting)
     * 
     * @return string
     */
    public function getTextLoadMore()
    {
        if ($this->isSecondLastPage())
        {
            $iItemsOnNextPage = $this->countItemsOnPage($this->getCurrentPage() + 1);
            return transg('Load last [nextresultcount] results', '', 'nextresultcount', $iItemsOnNextPage);    			
        }
        else
        { 
            return transg('Load next [nextresultcount] results', '', 'nextresultcount', $this->getItemCountPerPage());
        }    		
    }
    
    
}
?>
