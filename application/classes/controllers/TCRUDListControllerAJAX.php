<?php
namespace dr\classes\controllers;

/**
 * Description of TCRUDOverviewControllerAJAX
 * Create Read Update Delete:
 * A controller for a record list overview, but now in AJAX style
 * 
 * the goal of this class is to keep it lightweight due to OOP performance issues 
 * in PHP, so no parent class (it needs to be as flat as possible when it comes to parent classes depth)
 * 
 * @author drenirie
 * 
 * CONVERT non-AJAX ==> AJAX controller:
 * =========
 * 1. add defineDBQuery()
 * 2. remove execute()
 * 3. in getTemplatePathgetTemplatePath(): change:  'tpl_modellist.php' => 'tpl_modellistajax.php' 
 * 4. in defineDBQuery(): add $this to before array: arrTableColumnsShow => $this->arrTableColumnsShow
 * 
 * created 28 feb 2025
 * 28 feb 2025 - TCRUDListControllerAJAX created
 * 14 may 2025 - TCRUDListControllerAJAX defineDBQuery() return value is not array but integer of join levels
 */

use dr\classes\models\TSysModel;
use dr\classes\dom\TPaginator;
    
use dr\classes\dom\tag\webcomponents\DRDBFilters;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\Option;
use dr\classes\models\TTreeModel;
use dr\classes\types\TDateTime;

abstract class TCRUDListControllerAJAX
{
    protected $objModel = null;
    private $sModule = '';
    
    private $objPaginator = null;
    private $bUseBulkDelete = true;
    //private $bUseBulkCheckout = true; --> can be retrieved from model
    //private $bUseBulkLock = true;--> can be retrieved from model
    protected $objDBFilters = null;
    protected $arrTableColumnsShow = array(); //defines the columns of database shown
    private $bSuccessChangePosition = true;//error state of changing positions. is always true, even there if positions aren't changed. It will become false when a position change happened and lead to an error
    
    const FIELD_QUICKSEARCH = 'edtQuickSearch';

    // const ACTION_VARIABLE_RENDERVIEW = 'renderview';
    // const ACTION_VALUE_RENDERVIEW_HTMLPAGE = 'htmlpage'; //default this is assumed
    // const ACTION_VALUE_RENDERVIEW_JSONDATA = 'jsondata';
    const ACTION_VARIABLE_FILTERS = 'filters'; //$_POST['filters']

    const CSS_CLASS_DISPLAYMOBILE = 'column-display-on-mobile';
    const CSS_CLASS_DISPLAYDESKTOP = 'column-display-on-desktop';
    

    //==== JSON constants ====

    //fields in JSON response:
    // const JSON_TABLERESPONSE_MESSAGE                        = 'message'; //general message, like: "Please correct input"
    // const JSON_TABLERESPONSE_ERRORCODE                      = 'errorcode'; //number of the error
    const JSON_TABLERESPONSE_NEWREQUESTURL                  = 'newrequesturl'; //if you want to do a new request, here is the latest url with sortorder etc included
    const JSON_TABLERESPONSE_PAGINATOR                      = 'paginator'; //paginator js object with isfirstpage, islastpage etc
    const JSON_TABLERESPONSE_PAGINATOR_ISFIRSTPAGE          = 'isfirstpage'; //either true or false wether there is a PREVIOUS page in paginator available
    const JSON_TABLERESPONSE_PAGINATOR_ISLASTPAGE           = 'islastpage'; //either true or false wether there is a NEXT page in paginator available
    const JSON_TABLERESPONSE_PAGINATOR_PREVIOUSPAGENUMBER   = 'previouspagenumber'; //number of the PREVIOUS page
    const JSON_TABLERESPONSE_PAGINATOR_NEXTPAGENUMBER       = 'nextpagenumber'; //number of the NEXT page
    const JSON_TABLERESPONSE_PAGINATOR_CURRENTPAGE          = 'currentpage'; //current page number (starting with 0)
    const JSON_TABLERESPONSE_PAGINATOR_TOTALRESULTS         = 'totalresults'; //total number of unpaginated results from query
    const JSON_TABLERESPONSE_PAGINATOR_RECORDSPERPAGE       = 'recordsperpage'; //number of records displayed per page
    const JSON_TABLERESPONSE_PAGINATOR_PAGECOUNT            = 'pagecount'; //number of pages with results
    const JSON_TABLERESPONSE_TABLEHEADER                    = 'tableheader'; //the table with database data
    const JSON_TABLERESPONSE_TABLEBODY                      = 'tablebody'; //the table with database data
    const JSON_TABLERESPONSE_ROW_CSSCLASS                   = 'cssclassrow'; //css class of row in table
    const JSON_TABLERESPONSE_COL_CHECKBOX                   = 'checkbox'; //the id (integer64)of the current record in database (needed for reordering record positions)
    const JSON_TABLERESPONSE_COL_VALUEONMOBILE              = 'value-on-mobile'; //the id (integer64)of the current record in database (needed for reordering record positions)
    const JSON_TABLERESPONSE_COL_EDIT                       = 'edit'; //the id (integer64)of the current record in database (needed for reordering record positions)
    const JSON_TABLERESPONSE_ROW_RECORDID                   = 'recordid'; //the id (integer64)of the current record in database (needed for reordering record positions)
    const JSON_TABLERESPONSE_ROW_RANDOMID                   = 'randomid'; //the random (integer64) id of the current record in database
    const JSON_TABLERESPONSE_ROW_UNIQUEID                   = 'uniqueid'; //the unique (string) id of the current record in database
    // const JSON_TABLERESPONSE_ROW_DRAGGABLE                  = 'draggablerow'; //is row draggable (true or false)
    const JSON_TABLERESPONSE_CELL_CSSCLASS                  = 'cssclass'; //css class of cell in table
    const JSON_TABLERESPONSE_CELL_VALUE                     = 'value';  //contents of the cell in table
    
    //JSON error codes
    // const JSONAK_RESPONSE_OK = 0;
    // const JSONAK_RESPONSE_ERRORCODE_UNKNOWN = 999;
    const JSON_ERRORCODE_MOVEFAILED = 1;
    
    /**
     * 
     * @param TSysModel $objModel
     */
    public function  __construct()
    {               
        $this->sModule = APP_ADMIN_CURRENTMODULE;
        $this->objModel = $this->getNewModel();

        $this->objPaginator = new TPaginator(TPaginator::STYLE_NUMBEREDPAGES);   
        $this->objPaginator->setItemCountPerPage(getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE));
    
        $this->objDBFilters = new DRDBFilters();

        // includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'style.css');        
        includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js');
        // includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'style.css');        
        includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'dr-paginator.js');
        includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-drag-drop'.DIRECTORY_SEPARATOR.'dr-drag-drop.js');

        //check permissions
        if (!auth(APP_ADMIN_CURRENTMODULE, $this->getAuthorisationCategory(), AUTH_OPERATION_VIEW))
        {
            showAccessDenied(transcms('message_noaccess_viewrecords', 'you don\'t have permission to view these records'));
            die();
        }


        //first destructive manipulation edits
        $this->executeChangePositionUpDown(); //old method: 1 up and 1 down (doesn't work with TTreeModel)
        $this->executeChangePositionRecord(); //new method: drag and drop everywhere
        $this->executeBulkActions();
        $this->objModel->newQuery();//reset query

        //define columns and execute database query
        $mAutoJoinDefinedTablesLevels = 0;
        $mAutoJoinDefinedTablesLevels = $this->defineDBQuery(); //calls defineDBQuery() in child controller
        if ($mAutoJoinDefinedTablesLevels === null) //if no number is returned
            $mAutoJoinDefinedTablesLevels = 0;

        $this->executeDB($mAutoJoinDefinedTablesLevels);


        //determine output
        if (isset($_GET[ACTION_VARIABLE_RENDERVIEW]))
        {
            switch ($_GET[ACTION_VARIABLE_RENDERVIEW])
            {
                case ACTION_VALUE_RENDERVIEW_JSONDATA:
                    $this->renderJSONTable(true);
                    break;
                default:
                    $this->renderHTML();
            }
        }
        else //default without url parameter
        {
            $this->renderHTML();            
        }
        

        
    }

    /**
     * render to screen
     *
     * @return void
     */
    protected function renderHTML()
    {
        global $objCurrentModule;
        $arrVars = array();
        $sSortOrder = '';
        $sTempOutput = '';
        $sResultOutput = '';

        includeJS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'modellist.js');

        //variables for the template
        $arrVars['sTitle'] = $this->getTitle();
        $arrVars['sHTMLTitle'] = $arrVars['sTitle'];
        $arrVars['sHTMLMetaDescription'] = $arrVars['sTitle'];
        $arrVars['objCRUD'] = $this;
        $arrVars['objPaginator'] = $this->getPaginator();
        $arrVars['objDBFilters'] = $this->getDBFilters();
        $arrVars['objSelectBulkActions'] = $this->getBulkSelect();
        $arrVars['sURLDetailPage'] = $this->getDetailPageURL();
        $arrVars['arrTabsheets'] = $objCurrentModule->getTabsheets();        
        $arrVars['bNoRecordsToDisplay'] = false;
        $arrVars['arrQBSort'] = $this->objModel->getQBSort();//array(TSysModel::QB_SORTINDEX_TABLE => $sTable, TSysModel::QB_SORTINDEX_FIELD => $sField, TSysModel::QB_SORTINDEX_ORDER => $sSortOrder);                            \                                 
        $arrVars['arrTableColumnsShow'] = $this->arrTableColumnsShow;
        $arrVars['objModel'] = $this->objModel;
        $arrVars['sJSONTable'] = $this->renderJSONTable(false);
        $arrVars['bAllowedCreateNew'] = auth(APP_ADMIN_CURRENTMODULE, $this->getAuthorisationCategory(), AUTH_OPERATION_CREATE);

        
        //json variables template
        $arrVars['sVarfilters'] = TCRUDListControllerAJAX::ACTION_VARIABLE_FILTERS;
        $arrVars['sVarRenderView'] = ACTION_VARIABLE_RENDERVIEW;
        $arrVars['sValRenderViewJSONData'] = ACTION_VALUE_RENDERVIEW_JSONDATA;
        $arrVars['sValRenderViewHTMLPage'] = ACTION_VALUE_RENDERVIEW_HTMLPAGE;
    

        if ($this->objModel != null)
        {
            $this->objModel->resetRecordPointer();
            if ($this->objModel->count() == 0)
                $arrVars['bNoRecordsToDisplay'] = true;
        }
        $arrVars = array_merge($GLOBALS, $arrVars); //ORDER OF PARAMETERS IS IMPORTANT -> we pick $GLOBALS as base and overwrite them with the variables from execute()
    
        //render template
        if ($this->getTemplatePath() != '') //only render if exists
            $sTempOutput = renderTemplate($this->getTemplatePath(), $arrVars); //add content template to the variables for the skin                        
        else
            $sTempOutput = '';

        $arrVars['sHTMLContentMain'] = $sTempOutput;
        $sResultOutput = $sTempOutput;            

        //render skin
        if ($this->getSkinPath() != '') //only render if exists    
            $sTempOutput = renderTemplate($this->getSkinPath(), $arrVars);    
        else
            $sTempOutput = '';
        $sResultOutput = $sTempOutput;             


        //output to screen
        echo $sResultOutput;
    }

    /**
     * render to json data
     * 
     * @param boolean $bEchoResult echos result to screen
     * @return array JSON array
     */
    protected function renderJSONTable($bEchoResult = true)
    {        
        $arrJSONResponse = array();
        $sResult = ''; //fattened  $arrJSONResponse
        $objPaginator = $this->objPaginator;
        $arrTableBody = array();
        $arrTableHeader = array();
        $arrRow = array();
        $objModel = $this->objModel;
        $arrTableColumnsShow = array();
        $arrQBSort = $objModel->getQBSort();
        $arrTableColumnsShow = $this->arrTableColumnsShow;
        $bInstanceOfTree = false;
        $bInstanceOfTree = ($objModel instanceof TTreeModel);
        $iIdentSpaces = 0;//number of identation spaces when it is a tree
        $sIdentSpaces = '';//identation spaces string
        // $iIdentCounter = 0; //identation spaces loop index


        //==== DEFAULTS ===
        $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = JSONAK_RESPONSE_OK;
        $arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = '';
        $arrJSONResponse[JSONAK_RESPONSE_HALTONERROR] = true;
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_NEWREQUESTURL] = addVariableToURL(APP_URLTHISSCRIPT, ACTION_VARIABLE_RENDERVIEW, ACTION_VALUE_RENDERVIEW_JSONDATA);
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR] = array(
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_ISFIRSTPAGE => false,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_ISLASTPAGE => false,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_PREVIOUSPAGENUMBER => 0,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_NEXTPAGENUMBER => 0,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_CURRENTPAGE => 0,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_TOTALRESULTS => 0,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_RECORDSPERPAGE => 0,
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_PAGECOUNT => 0
        );
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEBODY] = $arrTableBody;
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEHEADER] = $arrTableHeader;
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_CSSCLASS] = '';
        // $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_DRAGGABLE] = ''; //default not draggable
        
        include_once APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php'; //getNextSortOrder

        //==== CHECK MOVE ERROR ====
        if (!$this->bSuccessChangePosition)
        {
            $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TCRUDListControllerAJAX::JSON_ERRORCODE_MOVEFAILED;
            $arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('message_error_moverecordposition_failed', 'Moving record(s) failed');
            $arrJSONResponse[JSONAK_RESPONSE_HALTONERROR] = false; //don't halt, refresh table so the user has the latest representation of what is in the database
        }

    
        //==== PAGINATOR ====
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR] = array(
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_ISFIRSTPAGE => $objPaginator->isFirstPage(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_ISLASTPAGE => $objPaginator->isLastPage(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_PREVIOUSPAGENUMBER => $objPaginator->getPreviousPageNumber(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_NEXTPAGENUMBER => $objPaginator->getNextPageNumber(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_CURRENTPAGE => $objPaginator->getCurrentPage(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_TOTALRESULTS => $objPaginator->getTotalItemsCount(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_RECORDSPERPAGE => $objPaginator->getItemCountPerPage(),
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_PAGINATOR_PAGECOUNT => $objPaginator->getPageCount(true)
        );

        //==== TABLE ====
        //==== TABLE: HEADER ====

        /**
         * we work with sort column indexes in stead of sort column names to pass through the visible url
         * to prevent messing with the column names
         */
        $sColumnHead = '';
        $sURL = '';
        $arrQBSelect = array(); // to determine on which column to sort
        $arrQBSelect = $objModel->getQBSelectFrom();   
        $iCountQBSelect = count($arrQBSelect);
        $iSortColIndex = 0; //index in $arrQBSelect
        // $arrQBSelectRow = array();

        //COLUMN: checkbox: select all
        $sColumnHead = '<input type="checkbox" name="chkCheckAll" onClick="toggleAllCheckboxes(this, \''.BULKACTION_VARIABLE_CHECKBOX_RECORDID.'[]\')">';
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEHEADER][] = array(
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnHead, 
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => ''
        );

        //COLUMN: column for mobile only
        $sColumnHead = transcms('column-display-on-mobile-header', 'Record');
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEHEADER][] = array(
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnHead, 
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => TCRUDListControllerAJAX::CSS_CLASS_DISPLAYMOBILE
        );        
        
        //COLUMN: going through every database column
        foreach ($arrTableColumnsShow as $arrColumn)
        {     
            $iSortColIndex = 0;                       
            $sTableName = $arrColumn[0];
            $sColumnName = $arrColumn[1];
            $sColumnHead = $arrColumn[2];                        
            $sCSSClass = TCRUDListControllerAJAX::CSS_CLASS_DISPLAYDESKTOP; //add css class  showOnDesktop - some columns you want to show on mobile AND desktop (like the sort up-down);
            $sSortOrder = '';
            if (isset($_GET[ACTION_VARIABLE_SORTORDER]))
                $sSortOrder = $_GET[ACTION_VARIABLE_SORTORDER];

            //figure out the index of the sortcolumn
            for ($iSCICounter = 0; $iSCICounter < $iCountQBSelect; $iSCICounter++)
            {
                if ($arrQBSelect[$iSCICounter][TSysModel::QB_SELECTINDEX_FIELD] == $sColumnName)
                {
                    if (($arrQBSelect[$iSCICounter][TSysModel::QB_SELECTINDEX_TABLE] == $sTableName) || $sTableName == '') //tablenames of the current TSysModel are empty (and later replaced by the real tablename)
                    {
                        $iSortColIndex = $iSCICounter;
                        $iSCICounter = $iCountQBSelect;//jump out of for loop, we found the sort column
                    }
                }
            }

            //only add link when NOT an encrypted field
            $bAddLinkToHead = true;
            if ($objModel->getFieldsDefinedExists($sColumnName))
                $bAddLinkToHead = ($objModel->getFieldEncryptionDisabled($sColumnName));

            if ($bAddLinkToHead)
            {
                $sURL = APP_URLTHISSCRIPT;
                $sURL = addVariableToURL($sURL, ACTION_VARIABLE_SORTCOLUMNINDEX, $iSortColIndex);//sort on column INDEX!!
                $sURL = addVariableToURL($sURL, ACTION_VARIABLE_SORTORDER, getNextSortOrder($sSortOrder));//sort order
                $sColumnHead = '<span onmousedown="changeSortOrder(this, \''.$sURL.'\')" style="cursor:pointer;">'.$sColumnHead.'</span>';   
            }

            //sort order arrows
            $bAddedSpinner = false;
            foreach($arrQBSort as $arrQBSortItem) //technically there can be multiple rows sorted in model, although not supported (yet) in GUI, because via the url is currently only sort column passed
            {
                if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == $sColumnName) //is actually sorted on this column in database?
                {
                    $sCSSClass = '';

                    if ($sSortOrder == SORT_ORDER_ASCENDING)
                    {
                        // $sColumnHead.='<img src="'.APP_URL_CMS_IMAGES.'/icon-sortasc16x16.png">';
                        $sColumnHead.='<dr-icon-spinner><svg class="iconchangefill" style="margin-left: 5px; margin-top:13px;" enable-background="new 0 0 32 32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" ><g id="background"><rect fill="none" height="32" width="32"/></g><g id="arrow_x5F_down"><polygon points="2.002,10 16.001,24 30.002,10  "/></g></svg></dr-icon-spinner>';    
                        $bAddedSpinner = true;
                    }
                    elseif ($sSortOrder == SORT_ORDER_DESCENDING)
                    {
                        // $sColumnHead.='<img src="'.APP_URL_CMS_IMAGES.'/icon-sortdesc16x16.png">';
                        $sColumnHead.='<dr-icon-spinner><svg class="iconchangefill" style="margin-left: 5px" enable-background="new 0 0 32 32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="backbord"><rect fill="none" height="32" width="32"/></g><g id="arrow_x5F_up"><polygon points="30,22 16.001,8 2.001,22  "/></g></svg></dr-icon-spinner>';
                        $bAddedSpinner = true;
                    }   
                }
                
            }   

            if ($bAddedSpinner === false)
            {
                $sColumnHead.='<dr-icon-spinner></dr-icon-spinner>';
            }   
                 

            /* <th class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnHead; ?></th> */
            $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEHEADER][] = array(
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnHead, 
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => $sCSSClass
            );
        }

        //COLUMN: "edit" icon space
        $sColumnHead = "&nbsp;";
        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEHEADER][] = array(
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnHead, 
            TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => ''
        );             
    

        //==== TABLE: BODY ====
        //caching most used translations
        $sTranslatedBooleanYes = '';
        $sTranslatedBooleanYes = transcms('boolean_yes', 'yes');    
        $sTranslatedBooleanNo = '';
        $sTranslatedBooleanNo = transcms('boolean_no', 'no');
        $sTranslatedMoveOneUp = '';
        $sTranslatedMoveOneUp = transcms('recordlist_move_up', 'move record up');
        $sTranslatedMoveOneDown = '';
        $sTranslatedMoveOneDown = transcms('recordlist_move_down', 'move record down');
        $sTranslatedEdit = '';
        $sTranslatedEdit = transcms('recordlist_edit', 'edit record');
        $sTranslatedTranslate = ''; //translate icon
        $sTranslatedTranslate = transcms('recordlist_translate', 'translate record to other language');//translate icon

        $bShowTranslateIcon = $objModel->getTableUseTranslationLanguageID();

        //value of te ID field (because getID() doesn't always exist, it can be getRandomID() or getUniqueID())
        $iIDValue = 0; //default
        $bEditAllowed = false;
        $bEditAllowed = auth(APP_ADMIN_CURRENTMODULE, $this->getAuthorisationCategory(), AUTH_OPERATION_CHANGE);
        $sURLDetailPage = $this->getDetailPageURL();
        $bIsTreeStructure = $objModel instanceof TTreeModel;

        //ONLY allow up and down if sorted on iOrder
        $bAllowedChangePosition = false;
        $arrQBSortItem = array();
        if (count($arrQBSort) > 0) 
        {
            $arrQBSortItem = $arrQBSort[0]; ///we only have to know if the first column in QBSort is iOrder (more columns doesn't matter, because if it isn't the first sort column, you don't see anything of moving up or down )
            if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == TSysModel::FIELD_POSITION) //is actually sorted on iOrder?
            {
                if (auth(APP_ADMIN_CURRENTMODULE, $this->getAuthorisationCategory(), AUTH_OPERATION_CHANGEPOSITION)) //if also allowed by authentication, then it is allowed to show
                {
                    $bAllowedChangePosition = true;
                }
            }
        }        

        $objModel->resetRecordPointer();
        while($objModel->next())
        {
            $arrRow = array();

            //WHOLE ROW
            if ($objModel->getTableUseIDField())
                $arrRow[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_RECORDID] = $objModel->getID();
            if ($objModel->getTableUseRandomID())
                if ($objModel->getTableUseRandomIDAsPrimaryKey())
                    $arrRow[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_RANDOMID] = $objModel->getRandomID();
            // if ($objModel->getTableUseUniqueID())
            //     $arrRow[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_UNIQUEID] = $objModel->getUniqueID();

            if ($bAllowedChangePosition)
            {
                $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_CSSCLASS] = 'draggable';
                if ($bIsTreeStructure)
                    $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_CSSCLASS] .= ' droppable';
                // $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_ROW_DRAGGABLE] = 'true';
            }


            //value of te ID field (because getID() doesn't always exist, it can be getRandomID() or getUniqueID())
            $iIDValue = 0; //default fallback
            if ($objModel->getTableUseIDField())
            {
                $iIDValue = $objModel->getID();
            }
            else //looking for alternatives to the ID field
            {
                if ($objModel->getTableUseRandomID()) //test FIRST because it is integer based (counting is faster)
                    $iIDValue = $objModel->getRandomID();
                elseif ($objModel->getTableUseUniqueID()) //test LAST because it is character based (counting is slower)
                    $iIDValue = $objModel->getUniqueID(); 
            }

            //edit alowed?
            $bEditAllowedThisRecord = true;
            $bEditAllowedThisRecord = $bEditAllowed; //temp only for this record so we can change the privilges based on locks and checkout

            //checkout
            $bRecordCheckedOut = false;
            if ($objModel->getTableUseCheckout())
            {
                $objDateCheckoutExpire = null;
                $objDateCheckoutExpire = $objModel->getCheckoutExpires();
                if ($objDateCheckoutExpire->isInTheFuture())
                {
                    $bRecordCheckedOut = true;
                    $bEditAllowedThisRecord = false;
                }
            }

            //lock
            $bRecordLocked = false;
            if ($objModel->getTableUseLock())
            {
                if ($objModel->getLocked())
                {
                    $bRecordLocked = true;                            
                    $bEditAllowedThisRecord = false;
                }
            }        
            

            //COLUMN: checkbox
            $sColumnValue = '<dr-icon-spinner><input type="checkbox" name="'.BULKACTION_VARIABLE_CHECKBOX_RECORDID.'[]" value="'.$iIDValue.'" onchange="toggleRowColorCheckboxClick(this)"><dr-icon-spinner>';
            $arrRow[TCRUDListControllerAJAX::JSON_TABLERESPONSE_COL_CHECKBOX] = array(
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => '',
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnValue
            );

            //COLUMN: data shown only on mobile
            $sColumnValue = $objModel->getDisplayRecordShort();
            $arrRow[TCRUDListControllerAJAX::JSON_TABLERESPONSE_COL_VALUEONMOBILE] = array(
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => TCRUDListControllerAJAX::CSS_CLASS_DISPLAYMOBILE,
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnValue
            );            

            //COLUMN: going through every database column
            foreach ($arrTableColumnsShow as $arrColumn)
            {
                $sTableName = $arrColumn[0];
                $sColumnName = $arrColumn[1];
                $sColumnNiceName = $arrColumn[2];
                $sCSSClass = TCRUDListControllerAJAX::CSS_CLASS_DISPLAYDESKTOP; //add css class  showOnDesktop - some columns you want to show on mobile AND desktop (like the sort up-down);                                
                $sColumnValue = '';
                $iColType = $objModel->getFieldType($sColumnName);
                $sSortOrder = '';
                
                foreach($arrQBSort as $arrQBSortItem) //technically there can be multiple rows sorted in model, although not supported (yet) in GUI, because via the url is currently only sort column passed
                {
                    if ($arrQBSortItem[TSysModel::QB_SORTINDEX_FIELD] == $sColumnName) //is actually sorted on this column in database?
                        $sSortOrder = $arrQBSortItem[TSysModel::QB_SORTINDEX_ORDER];
                }                            
        
                switch ($iColType)
                {
                        case TP_DATETIME:                                                
                                $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getDateTimeAsString(); 
                                break;
                        case TP_BOOL:  
                                if ($objModel->get($sColumnName, $sTableName, true))
                                    $sColumnValue = '<svg class="iconchangefill" viewBox="6 6 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M10.5858 13.4142L7.75735 10.5858L6.34314 12L10.5858 16.2427L17.6568 9.1716L16.2426 7.75739L10.5858 13.4142Z" fill="currentColor"/></svg>';
                                else
                                    $sColumnValue = '&nbsp;';
                                break;
                        case TP_COLOR:  
                            $sColumnValue = '<div class="color" style="height:10px;width:40px; background-color: #'.$objModel->get($sColumnName, $sTableName, true).'"></div>';
                            break;                                                    
                        case TP_CURRENCY:
                                $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getValueFormatted();
                                break;
                        case TP_DECIMAL:
                                $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getValueFormatted();
                                break;
                        case TP_FLOAT:
                                $sColumnValue = $objModel->get($sColumnName, $sTableName, true)->getAsString();
                                break;
                        case CT_IPADDRESS:
                                $sColumnValue = $objModel->get($sColumnName, $sTableName);
                                break;
                        default: 
                                $sColumnValue = $objModel->get($sColumnName, $sTableName, true);
                }            

                //identation when it is a tree
                if ($bInstanceOfTree)
                {
                    //ident the name field
                    if (($sColumnName === $objModel->getDisplayRecordColumn()) && ($sTableName === $objModel->getDisplayRecordTable() || ($sTableName === '')))
                    {
                        $iIdentSpaces = $objModel->get(TTreeModel::FIELD_META_DEPTHLEVEL, $sTableName, false);
                        $sIdentSpaces = '<span class="treeinvisibleident">'.generateChars('-', $iIdentSpaces * 3).'</span>'; //3 spaces
                        $sColumnValue = $sIdentSpaces.' '.$sColumnValue;
                    }
                }

                //COLUMN: favorite
                if ($sColumnName == TSysModel::FIELD_ISFAVORITE)
                {
                    if ($objModel->getIsFavorite())
                        $sColumnValue = '<svg  class="iconchangefill" version="1.1" viewBox="-2 -2 19 19" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M8.612,2.347L8,2.997l-0.612-0.65c-1.69-1.795-4.43-1.795-6.12,0c-1.69,1.795-1.69,4.706,0,6.502l0.612,0.65L8,16  l6.12-6.502l0.612-0.65c1.69-1.795,1.69-4.706,0-6.502C13.042,0.551,10.302,0.551,8.612,2.347z"/></svg>';                
                    else
                        $sColumnValue = ' ';
                }

                //COLUMN: CHANGE POSITION/ORDER 
                /*
                if ($sColumnName == TSysModel::FIELD_POSITION)
                {
                    $sCSSClass = "";
                    $bOrderOneUpAllowed = $bOrderOneUpDownAllowed; //per item can be disabled if it is the first or last item
                    $bOrderOneDownAllowed = $bOrderOneUpDownAllowed; //per item can be disabled if it is the first or last item
                    
                    if (!$bEditAllowedThisRecord) //record can be locked or checked out 
                    {
                        $bOrderOneUpAllowed = false;
                        $bOrderOneDownAllowed = false;
                    }
                    
                    $sURL = APP_URLTHISSCRIPT;
                    $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $iIDValue);//move record id
                    $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUPDOWN, ACTION_VALUE_ORDERONEUPDOWN);//Change Order 1 record
                    $sURL = addVariableToURL($sURL, ACTION_VARIABLE_SORTORDER, $sSortOrder);//Change Order 1 record                                    
                    $sURL = addVariableToURL($sURL, ACTION_VARIABLE_RENDERVIEW, ACTION_VALUE_RENDERVIEW_JSONDATA);
                    
                    //not allowed if it's first item on first page
                    if ($objPaginator->isFirstPage())
                    {
                        if ($objModel->isFirstRecord())
                            $bOrderOneUpAllowed = false;
                    }
                    
                    //not allowed if it's the last item on the last page
                    if ($objPaginator->isLastPage())
                    {
                        if ($objModel->isLastRecord())
                            $bOrderOneDownAllowed = false;                                        
                    }                                    
                                        
                    //up arrow
                    if ($bOrderOneUpAllowed)
                    {
                        $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUP, ACTION_VALUE_ORDERONEUP); //move one up
                        // $sColumnValue = '<a href="'.$sURL.'"><svg class="iconchangefill" alt="'.$sTranslatedMoveOneUp.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,64.5 64.5,256.5 176.5,256.5 176.5,448.5 336.5,448.5 336.5,256.5 448.5,256.5 "/></svg></a>';                                        
                        $sColumnValue = '<dr-icon-spinner><svg onmousedown="changeOrderRecord(this, \''.$sURL.'\');" style="cursor:pointer;" class="iconchangefill" alt="'.$sTranslatedMoveOneUp.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,64.5 64.5,256.5 176.5,256.5 176.5,448.5 336.5,448.5 336.5,256.5 448.5,256.5 "/></svg></dr-icon-spinner>';                                        
                    }
                    else
                    {
                        $sColumnValue = '<svg class="iconchangefill" style="opacity: 0.5" alt="'.$sTranslatedMoveOneUp.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,64.5 64.5,256.5 176.5,256.5 176.5,448.5 336.5,448.5 336.5,256.5 448.5,256.5 "/></svg>';
                    }
                    
                    
                    //down arrow
                    if ($bOrderOneDownAllowed)
                    {
                        $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ORDERONEUP, ACTION_VALUE_ORDERONEDOWN); //move one down
                        // $sColumnValue.= '<a href="'.$sURL.'"><svg class="iconchangefill" alt="'.$sTranslatedMoveOneDown.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,448.5 448.5,256.5 336.5,256.5 336.5,64.5 176.5,64.5 176.5,256.5 64.5,256.5 "/></svg></a>';                                        
                        $sColumnValue.= '<dr-icon-spinner><svg onmousedown="changeOrderRecord(this, \''.$sURL.'\');" style="cursor:pointer;" class="iconchangefill" alt="'.$sTranslatedMoveOneDown.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,448.5 448.5,256.5 336.5,256.5 336.5,64.5 176.5,64.5 176.5,256.5 64.5,256.5 "/></svg></dr-icon-spinner>';                                        
                    }
                    else
                    {
                        $sColumnValue.= '<svg class="iconchangefill" style="opacity: 0.5" alt="'.$sTranslatedMoveOneDown.'" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><polygon points="256.5,448.5 448.5,256.5 336.5,256.5 336.5,64.5 176.5,64.5 176.5,256.5 64.5,256.5 "/></svg>';
                    }                                    

                } 
                */
                if ($sColumnName == TSysModel::FIELD_POSITION)
                {  
                    $sColumnValue = '<svg viewBox="0 0 24 24"  xmlns="http://www.w3.org/2000/svg"><path d="M15.5 17C16.3284 17 17 17.6716 17 18.5C17 19.3284 16.3284 20 15.5 20C14.6716 20 14 19.3284 14 18.5C14 17.6716 14.6716 17 15.5 17ZM8.5 17C9.32843 17 10 17.6716 10 18.5C10 19.3284 9.32843 20 8.5 20C7.67157 20 7 19.3284 7 18.5C7 17.6716 7.67157 17 8.5 17ZM15.5 10C16.3284 10 17 10.6716 17 11.5C17 12.3284 16.3284 13 15.5 13C14.6716 13 14 12.3284 14 11.5C14 10.6716 14.6716 10 15.5 10ZM8.5 10C9.32843 10 10 10.6716 10 11.5C10 12.3284 9.32843 13 8.5 13C7.67157 13 7 12.3284 7 11.5C7 10.6716 7.67157 10 8.5 10ZM15.5 3C16.3284 3 17 3.67157 17 4.5C17 5.32843 16.3284 6 15.5 6C14.6716 6 14 5.32843 14 4.5C14 3.67157 14.6716 3 15.5 3ZM8.5 3C9.32843 3 10 3.67157 10 4.5C10 5.32843 9.32843 6 8.5 6C7.67157 6 7 5.32843 7 4.5C7 3.67157 7.67157 3 8.5 3Z" class="iconchangefill" /></svg>';
                }                
                
                /*     <td  class="<?php if ($bCSSClassTDShowOnDesktop){ echo 'column-display-on-desktop';} ?>"><?php echo $sColumnValue ?></td> */                
                $arrRow[$sColumnNiceName] = array(
                    TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => $sCSSClass,
                    TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnValue
                );
                
            }

            //COLUMN: EDIT
            $sColumnValue = '';
            
            //checkout
            if ($bRecordCheckedOut)
                $sColumnValue = '<img src="'.APP_URL_CMS_IMAGES.'/icon-checkout-locked32x32.png" alt="'.transcms('recordlist_record_checkedout', 'Record CHECKED OUT by [source], not available for editing', 'source', $objModel->getCheckoutSource()) .'">';

            //lock
            if ($bRecordLocked)
                $sColumnValue = '<img src="'.APP_URL_CMS_IMAGES.'/icon-lock-closed32x32.png" alt="'.transcms('recordlist_record_locked', 'Record LOCKED by [source], not available for editing','source', $objModel->getLockedSource()).'">';
            
            //edit-icon
            if (isset($sURLDetailPage) && $bEditAllowedThisRecord)
            {   
                $sURL = '';
                if (isset($sURLDetailPage))
                {
                    if ($objModel->getTableUseRandomIDAsPrimaryKey())
                        $sURL.= addVariableToURL($sURLDetailPage, ACTION_VARIABLE_UNIQUEID, $objModel->getRandomID()); //I choose not to use addvariableToID() because of speed
                    
                    if ($objModel->getTableUseIDField())
                        $sURL.= addVariableToURL($sURLDetailPage, ACTION_VARIABLE_ID, $iIDValue); //I choose not to use addvariableToID() because of speed
                }

                $sColumnValue.= '<dr-icon-spinner><svg onmousedown="onEditRecordClick(this, \''.$sURL.'\');" style="cursor:pointer;" class="iconchangefill" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M11.6768 4.38395L15.6128 8.31995L7.57485 16.3586C7.36976 16.5636 7.12302 16.7212 6.85215 16.821L6.68687 16.8739L2.6319 17.9798C2.28531 18.0743 1.96524 17.7857 2.00279 17.4452L2.01796 17.3658L3.12386 13.3109C3.20017 13.031 3.33624 12.7718 3.52191 12.5508L3.63917 12.4229L11.6768 4.38395ZM13.245 2.81706C14.3318 1.73025 16.0939 1.73025 17.1807 2.81706C18.2222 3.85858 18.2656 5.52026 17.3109 6.61346L17.1807 6.75273L16.3198 7.61295L12.3838 3.67695L13.245 2.81706Z" /></svg></dr-icon-spinner>';

            }
            
            //translate
            if ($bShowTranslateIcon)
            {
                if (isset($sURLTranslatePage))
                    $sColumnValue.= '<a href="'.$sURLTranslatePage.'?'.ACTION_VARIABLE_ID.'='.$iIDValue.'">';
                $sColumnValue.= '<img src="'.APP_URL_CMS_IMAGES.'/icon-translate32x32.png" alt="'.$sTranslatedTranslate.'">';
                if (isset($sURLTranslatePage))
                    $sColumnValue.= '</a>';          
            }
             

            $arrRow[TCRUDListControllerAJAX::JSON_TABLERESPONSE_COL_EDIT] = array(
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_CSSCLASS => '',
                TCRUDListControllerAJAX::JSON_TABLERESPONSE_CELL_VALUE => $sColumnValue
            );
       



            //cleanup
            unset($objDateCheckoutExpire);    

            //add row
            $arrTableBody[] = $arrRow;
        }

        $arrJSONResponse[TCRUDListControllerAJAX::JSON_TABLERESPONSE_TABLEBODY] = $arrTableBody;
        $sResult = json_encode($arrJSONResponse);

        if ($bEchoResult)
        {
            header(JSONAK_RESPONSE_HEADER);
            echo $sResult;
        }
    
        return $sResult;
    }

    
    /**
     * set model object
     * @param TSysModel $objModel
     */
    public function setModel($objModel)
    {
        $this->objModel = $objModel;
    }
    
    /**
     * get module object
     * @return TSysModel
     */
    public function getModel()
    {       
        return $this->objModel;
    }
    

    /**
     * set authorisation category for auth() function
     */
    public function setAuthorisationCategory($sAuthorisationCategory)
    {
        $this->sAuthorisationCategory = $sAuthorisationCategory;
    }
    

    /**
     * set model object
     * @param TSysModel $objModel
     */
    public function setModule($sModule)
    {
        $this->sModule = $sModule;
    }
    
    /**
     * get module object
     * @return TSysModel
     */
    public function getModule()
    {
        return $this->sModule;
    }
    
    public function getPaginator()
    {
        return $this->objPaginator;
    }

    public function getDBFilters()
    {
        return $this->objDBFilters;
    }
  
    /**
     * use bulk delete?
     * 
     * @return boolean
     */
    public function getUseBulkDelete()
    {
        return $this->bUseBulkDelete;        
    }
    
    /**
     * use bulk delete ?
     * 
     * @param boolean $bDelete
     */
    public function setUseBulkDelete($bDelete)
    {
        $this->bUseBulkDelete = $bDelete;    
    }
          

    /**
     * returns the html select tag with all the bulk items
     * 
     * @return Select
     */
    public function getBulkSelect()
    {
        $objSelectBulkActions = new Select();
        $objSelectBulkActions->setNameAndID(BULKACTION_VARIABLE_SELECT_ACTION);

        $objNone = new Option();
        $objNone->setValue('');
        $objNone->setText(transcms('modellist_bulkactions_title', '[select action]'));
        $objSelectBulkActions->appendChild($objNone);

        if ($this->bUseBulkDelete)
        {
            $objDelete = new Option();
            $objDelete->setValue(BULKACTION_VALUE_DELETE);
            $objDelete->setText(transcms('modellist_bulkactions_delete', 'delete'));
            if (auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_DELETE))
                $objSelectBulkActions->appendChild($objDelete);
            unset($objDelete);
        }

        if ($this->objModel->getTableUseCheckout() && auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_CHECKINOUT))
        {
            $objCheckout = new Option();
            $objCheckout->setValue(BULKACTION_VALUE_CHECKOUT);
            $objCheckout->setText(transcms('modellist_bulkactions_checkout', 'lock auto (triggers check-out)'));
            $objSelectBulkActions->appendChild($objCheckout);    
            unset($objCheckout);    

            $objCheckin = new Option();
            $objCheckin->setValue(BULKACTION_VALUE_CHECKIN);
            $objCheckin->setText(transcms('modellist_bulkactions_checkin', 'unlock auto (triggers check-in)'));
            $objSelectBulkActions->appendChild($objCheckin);    
            unset($objCheckin);
        }

        if ($this->objModel->getTableUseLock() && auth($this->sModule,$this->getAuthorisationCategory(), AUTH_OPERATION_LOCKUNLOCK))
        {
            $objLock = new Option();
            $objLock->setValue(BULKACTION_VALUE_LOCK);
            $objLock->setText(transcms('modellist_bulkactions_lock', 'lock manual'));
            $objSelectBulkActions->appendChild($objLock);    
            unset($objLock);

            $objUnlock = new Option();
            $objUnlock->setValue(BULKACTION_VALUE_UNLOCK);
            $objUnlock->setText(transcms('modellist_bulkactions_unlock', 'unlock manual'));
            $objSelectBulkActions->appendChild($objUnlock);    
            unset($objUnlock);     
        }
        
        return $objSelectBulkActions;
    }
    
    /**
     * execute things like delete, lock etc.
     * 
     * @param mixed $mAutoJoinDefinedTablesLevels -1=unlimited, 0=none, false=0; 1=1level, true=1level
     */
    public function executeDB($mAutoJoinDefinedTablesLevels = 0)
    {
        //fieldname in table that is used for counting pages for paginator. Often ID field
        $sCountFieldPaginator = $this->objModel::FIELD_ID; //default
        if ($this->objModel->getTableUseIDField() === false) //looking for alternatives to the ID field
        {
            if ($this->objModel->getTableUseRandomID()) //test FIRST because it is integer based (counting is faster)
                $sCountFieldPaginator = $this->objModel::FIELD_RANDOMID;
            elseif ($this->objModel->getTableUseUniqueID()) //test LAST because it is character based (counting is slower)
                $sCountFieldPaginator = $this->objModel::FIELD_UNIQUEID; 
        }

        //then record selection and display
        $this->executeFilters();
        $this->executeSortColumns();        

        //actually execute
        $this->objModel->loadFromDB($mAutoJoinDefinedTablesLevels, $this->objPaginator, $sCountFieldPaginator);
    }
    
    /**
     * Change order records 1 position up or down (switch 1 place with record above or below)
     */
    private function executeChangePositionUpDown()
    {
        if (isset($_GET[ACTION_VARIABLE_ORDERONEUPDOWN]) && isset($_GET[ACTION_VARIABLE_ID]))    
        {        
            if ($_GET[ACTION_VARIABLE_ORDERONEUPDOWN] == ACTION_VALUE_ORDERONEUPDOWN)
            {
                if (auth($this->getModule(), $this->getAuthorisationCategory(), AUTH_OPERATION_CHANGEPOSITION))
                {
                    $this->objModel->positionChangeOneUpDownDB($_GET[ACTION_VARIABLE_ID], $_GET[ACTION_VALUE_ORDERONEUPDOWN], $_GET[ACTION_VARIABLE_SORTORDER]);
                }
            }
        } 
    }
    
    /**
     * change order of records by dragging and dropping them
     */
    private function executeChangePositionRecord()
    {
        // if (isset($_GET[ACTION_VARIABLE_CHANGEPOSITION]) && isset($_GET[ACTION_VARIABLE_ID]))    
        // {        
        //     if ($_GET[ACTION_VARIABLE_CHANGEPOSITION] == ACTION_VALUE_CHANGEPOSITION)
        //     {
        //         if (auth($this->getModule(), $this->getAuthorisationCategory(), AUTH_OPERATION_CHANGEPOSITION))
        //         {                    
        //             if (isset($_GET[ACTION_VARIABLE_CHANGEPOSITION_ONID])) //dropping ON element (in tree)
        //             {
        //                 if (!$this->objModel->moveToParentNodeDB($_GET[ACTION_VARIABLE_ID], $_GET[ACTION_VARIABLE_CHANGEPOSITION_ONID]))
        //                 {
        //                     logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'executeChangePositionRecord(): position change failed. drop ON element');        
        //                     $this->bSuccessChangePosition = false;
        //                 }
        //             }
        //             else //dropping AFTER element
        //             {
        //                 if (!$this->objModel->positionChangeDB($_GET[ACTION_VARIABLE_ID], $_GET[ACTION_VARIABLE_CHANGEPOSITION_AFTERID]))
        //                 {
        //                     logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'executeChangePositionRecord(): position change failed. drop AFTER element');        
        //                     $this->bSuccessChangePosition = false;                            
        //                 }
        //             }
                    
        //         }
        //         else
        //         {
        //             logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'executeChangePositionRecord(): auth() failed for changing position');
        //             $this->bSuccessChangePosition = false;
        //         }
        //     }
        // } 


        if (!isset($_GET[ACTION_VARIABLE_CHANGEPOSITION]) || !isset($_GET[ACTION_VARIABLE_ID]))    
            return;

        if ($_GET[ACTION_VARIABLE_CHANGEPOSITION] != ACTION_VALUE_CHANGEPOSITION)
            return;

        //authorize
        if (!auth($this->getModule(), $this->getAuthorisationCategory(), AUTH_OPERATION_CHANGEPOSITION))
        {    
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'executeChangePositionRecord(): auth() failed for changing position');
            $this->bSuccessChangePosition = false;
            return;                
        }

        //what kind of drop?
        if (isset($_GET[ACTION_VARIABLE_CHANGEPOSITION_ONID])) //dropping ON element (in tree)
        {
            if (!$this->objModel->moveToParentNodeDB($_GET[ACTION_VARIABLE_ID], $_GET[ACTION_VARIABLE_CHANGEPOSITION_ONID]))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'executeChangePositionRecord(): position change failed. drop ON element');        
                $this->bSuccessChangePosition = false;
            }
        }
        else //dropping AFTER element
        {
            if (!$this->objModel->positionChangeDB($_GET[ACTION_VARIABLE_ID], $_GET[ACTION_VARIABLE_CHANGEPOSITION_AFTERID]))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'executeChangePositionRecord(): position change failed. drop AFTER element');        
                $this->bSuccessChangePosition = false;                            
            }
        }
                 
    }

    /**
     * execute filters
     */
    private function executeFilters()
    {
        if ($this->objDBFilters)
        {
            $this->objDBFilters->readJSON(true, TCRUDListControllerAJAX::ACTION_VARIABLE_FILTERS);
            $this->objDBFilters->createDBQuery($this->objModel);
        }
    }
    
    /**
     * sort
     */
    private function executeSortColumns()
    {
       
        $arrQBSelect = array();
        $arrQBSelect = $this->objModel->getQBSelectFrom();
        
        if (!$arrQBSelect) //select can be empty if no fields are defined via $objModel->selectFrom();
        {
            error_log ('column sorting doesnt work because no fields are defined via $objModel->select(). (fields are generated by TSysModel after this function is called). Error thrown in '.__METHOD__);
        }
        else //$arrQBSelect exists
        {
            if (isset($_GET[ACTION_VARIABLE_SORTCOLUMNINDEX])) //sort-column-index
            {
                $iSortColIndex = $_GET[ACTION_VARIABLE_SORTCOLUMNINDEX];
                $sSortOrder = '';
                $sSortColumn = '';
                $sSortTable = '';
                if (is_numeric($iSortColIndex)) //prevent injection
                {
                    if ($iSortColIndex < count($arrQBSelect)) //prevent indexes outside the scope of the array
                    {
                        $sSortColumn = $arrQBSelect[$iSortColIndex][TSysModel::QB_SELECTINDEX_FIELD];
                        $sSortTable = $arrQBSelect[$iSortColIndex][TSysModel::QB_SELECTINDEX_TABLE];
                    }
                }
                if (!isset($_GET[ACTION_VARIABLE_SORTORDER]))
                    $sSortOrder = SORT_ORDER_NONE;
                else
                    $sSortOrder = $_GET[ACTION_VARIABLE_SORTORDER];

                $this->objModel->sort($sSortColumn, $sSortOrder, $sSortTable);
            }
            else //no sort order selected? then pick sortorder column (if it exists)
            {
                if ($this->objModel->getTableUseOrderField())
                {
                    $this->objModel->sort(TSysModel::FIELD_POSITION);
                }
            }        
        }
    }
    
    /**
     * execute bulk actions
     */
    protected function executeBulkActions()
    {
        global $objAuthenticationSystem;
        $bBulkSuccess = false;

        // if (isset($_POST[BULKACTION_VARIABLE_CHECKBOX_RECORDID]))
        if (isset($_POST[BULKACTION_VARIABLE_SELECT_ACTION]) && isset($_POST[BULKACTION_VARIABLE_CHECKBOX_RECORDID]))
        {    
            // $iCountIDs = count($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID]);

            foreach($_POST[BULKACTION_VARIABLE_CHECKBOX_RECORDID] as $mID)
            {
                $bValidID = false;
                $bValidID = is_numeric($mID); //if it is numeric, it is always valid

                //check uniqueid for validity
                if (!$bValidID)
                    if ($this->objModel->getTableUseUniqueID())
                        if (isUniqueidRealValid($mID))
                            $bValidID = true;

                if ($bValidID)
                {
                    //delete action?
                    if ($this->bUseBulkDelete)
                    {
                        if ($_POST[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_DELETE)
                        {
                            if (auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_DELETE))
                            {
                                $this->objModel->clear(true);

                                if ($this->objModel->getTableUseIDField())
                                    $this->objModel->findID($mID);
                                elseif ($this->objModel->getTableUseRandomID())
                                    $this->objModel->findRandomID($mID);
                                elseif ($this->objModel->getTableUseUniqueIDField())
                                    $this->objModel->findUniqueID($mID);

                                if ($this->objModel->getTableUseLock())
                                    $this->objModel->find(TSysModel::FIELD_LOCKED, false);

                                if ($this->objModel->getTableUseCheckout())
                                {
                                    //@todo check if record is checked-out or checkout-date expired
                                }
                                    

                                if ($this->objModel->deleteFromDB(true, true))
                                    $bBulkSuccess = true;
                                else
                                    error_log('delete record with id '.$mID.' failed for '.$this->objModel::getTable());
                            }
                            else
                                error_log('auth() failed for bulk deleting records');
                        }
                    }

                    //checkin and checkout
                    if ($this->objModel->getTableUseCheckout())
                    {
                        //checkOUT action?
                        if ($_POST[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_CHECKOUT)
                        {
                            if (auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_CHECKINOUT))
                            {
                                //@todo support for randomid + uniqueid
                                if ($this->objModel->checkoutNowDB($mID, $this->sModule.': records overview screen by user: '.$objAuthenticationSystem->getUsers()->getUsername()))
                                    $bBulkSuccess = true;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'checkout with id '.$mID.' failed for '.$this->objModel::getTable());
                            }

                        }  

                        //checkIN action?
                        if ($_POST[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_CHECKIN)
                        {
                            if (auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_CHECKINOUT))
                            {
                                //@todo support for randomid + uniqueid
                                if ($this->objModel->checkinNowDB($mID))
                                    $bBulkSuccess = true;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'checkin with id '.$mID.' failed for '.$this->objModel::getTable());
                            }

                        }     
                    }


                    //lock and unlock
                    if ($this->objModel->getTableUseLock())
                    {
                        //lock action?
                        if ($_POST[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_LOCK)
                        {
                            if (auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_LOCKUNLOCK))
                            {
                                //@todo support for randomid + uniqueid
                                if ($this->objModel->lockNowDB($mID, $this->sModule.': records overview screen by user: '.$objAuthenticationSystem->getUsers()->getUsername()))
                                    $bBulkSuccess = true;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'locking with id '.$iID.' failed for '.$this->objModel::getTable());
                            }

                        }               

                        //unlock action?
                        if ($_POST[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_UNLOCK)
                        {
                            if (auth($this->sModule, $this->getAuthorisationCategory(), AUTH_OPERATION_LOCKUNLOCK))
                            {
                                //@todo support for randomid + uniqueid
                                if ($this->objModel->unlockNowDB($mID))
                                    $bBulkSuccess = true;
                                else
                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'unlocking with id '.$iID.' failed for '.$this->objModel->getTable());
                            }

                        }                  
                    }
                }
                else
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'ID "'. $mID. '" is not valid');
            }

            // $sRefURL = '';
            // $sRefURL = removeVariableFromURL(getURLThisScript(), BULKACTION_VARIABLE_SELECT_ACTION);
            // $sRefURL = removeVariableFromURL($sRefURL, urlencode(BULKACTION_VARIABLE_CHECKBOX_RECORDID.'[]'));

            // if ($bBulkSuccess)
            // {
            //     if ($_POST[BULKACTION_VARIABLE_SELECT_ACTION] == '')                
            //         $sRefURL = addVariableToURL ($sRefURL, 'cmsmessage', transcms('overview_bulkactions_empty', 'not selected a bulk action'));
            //     else
            //         $sRefURL = addVariableToURL ($sRefURL, 'cmsmessage', transcms('overview_bulkactions_success', 'bulk actions completed succesfully'));
            // }
            // else
            //     $sRefURL = addVariableToURL ($sRefURL, 'cmserror', transcms('overview_bulkactions_error', 'execution bulk actions: FAILED!'));
            
            // header('Location: '.$sRefURL);
        }        
    }


    /*****************************************
     * 
     *  ABSTRACT FUNCTIONS
     * 
     *****************************************/

  
    /**
     * defines database query to execute
     * 
     * @return integer how many levels of tables to auto join: -1=unlimited, 0=none; 1=1level
     */
    abstract public function defineDBQuery();


    /**
     * return path of the page template
     *
     * @return string
     */
    abstract public function getTemplatePath();

    /**
     * return path of the skin template
     * 
     * return '' if no skin
     *
     * @return string
     */
    abstract public function getSkinPath();

    /**
     * return new TSysModel object
     * 
     * @return TSysModel;
     */
    abstract public function getNewModel();

    /**
     * return permission category 
     * =class constant of module class
     * 
     * for example: Mod_Sys_CMSUsers::PERM_CAT_USERS
     *
     * @return string
     */
    abstract public function getAuthorisationCategory();

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    abstract public function getDetailPageURL();

    /**
     * return page title
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "instellingen"
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * show tabsheets on top?
     *
     * @return bool
     */
    abstract public function showTabs();    
}
    

