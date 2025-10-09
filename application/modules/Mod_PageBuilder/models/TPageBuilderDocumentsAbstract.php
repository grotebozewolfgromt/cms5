<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_PageBuilder\models;

use dr\classes\dom\tag\form\Select;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsStatusses;
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;

/**
 * TPageBuilderDocumentsAbstract
 * This class represents an editable HTML document
 * This does not nessarily always mean a webpage, it can be something else.
 * A TPageBuilderDocumentsAbstract can be for example: a blog post, or an email, an invoice, a webpage
 * 
 * the page builder consists of 4 types of information: a document, containers, blocks, elements
 * DOCUMENT
 *   |- SKELETON
 * 	     |- STRUCTURE (layout in columns)
 *             |- BLOCK
 *                  |- ELEMENT
 * The individual pagebuilder module take care of blocks, containers and elements
 * 
 * 
 * for each language there is a different page!
 * 
 * 
 * ==============
 * HTML RENDERED DATA vs DATA
 * ==============
 * DATA:
 * The data is the internal data. This structure can be different per pagebuilder. 
 * This can be HTML, XML, JSON whatever floats your boat/
 * This data needs to be easy to work with for the pagebuilder.
 * 
 * RENDERED HTML DATA:
 * From the data from the pagebuilder we can render HTML. 
 * This rendered HTML is stored in a separate field as cache.
 * This way we can make the page rendering on the front-end quicker 
 * (because we don't have to render it, just query the database)
 * 
 * 
 * @author Dennis Renirie
 * 25 apr 2024: TPageBuilderDocumentsAbstract created
 * 18 mei 2024: TPageBuilderDocumentsAbstract: rename nameinternal -> title
 * 18 mei 2024: TPageBuilderDocumentsAbstract: data field added
 */

abstract class TPageBuilderDocumentsAbstract extends TSysModel
{
	const FIELD_NAMEINTERNAL			= 'sNameInternal'; //just for the users' own reference, so they know what it is. language unaware
	const FIELD_DATA					= 'sData'; //the actual data that the pagebuilder can process (of which eventually the actual HTML can be rendered)
	const FIELD_RENDEREDHTML			= 'sRenderedHTML'; //page rendered in full html by Javascript. This is basically a cache to prevent a page from needing to be rerendered on every pageload
	const FIELD_NEEDSRENDER				= 'bNeedsRender'; //DATA-field has changed without rending the HTMLRENDERED-field. This boolean indicates that the html cache needs to be rendered. This can happen if something has changed outside the pagebuilder (for example links are mass edited).
	const FIELD_MODULEID				= 'iModuleID';//what module (=pagebuilder) was it made with
	const FIELD_MODULEVERSIONNUMBER		= 'iModuleVersionNumber';//what version of the module was it made with
	const FIELD_AUTHORUSERID			= 'iAuthorUserID';//user id of the author
	const FIELD_STATUSID				= 'iStatusID';//status id from TPageBuilderDocumentStatusses
	const FIELD_META_MODULENAMEINTERNAL	= 'sMetaModuleNameInternal';//when you deinstall the pagebuilder you will loose also the name of the module, you never know which pagebuilder made the document when you want to install it again
	const FIELD_META_MODULENAMENICE		= 'sMetaModuleNameNice';//just a nice name (when you deinstall the pagebuilder you will loose also the nice name)
	const FIELD_NEEDSWORK				= 'bNeedsWork';//indicator for the user that the document isn't finished yet
	const FIELD_NOTESINTERNAL			= 'sNotesInternal'; //internal notes about the document for users. i.e. what is left to do, what needs to be changed, instructions for other authors, used resources etc

	const FIELD_WEBSITEID				= 'iWebsiteID'; //id of the website --> dont need it, because it can also be an email or invoice
	const FIELD_URLSLUG					= 'sURLSlug'; //example.com/blog/slug
	const FIELD_CANONICALURL			= 'sCanonicalURL'; // helps webmasters prevent duplicate content issues in search engine optimization by specifying the "canonical" or "preferred" version of a web page. see RFC 6596
	const FIELD_301REDIRECTURL			= 's301RedirectURL'; //error code 301 permanently moved to this url. content not rendered, just redirected to another url. if empty, no redirect
	const FIELD_HTML_TITLE				= 'sHTMLTitle'; //what is in the <title> tag
	const FIELD_HTML_METADESCRIPTION	= 'sHTMLMetaDescription'; //what is in the <meta name="description" content=""> tag
	const FIELD_PASSWORD				= 'sPassword'; //2-way encrypted password to access the page on the website (for password protected pages). When empty ('') page is not password protected. The goal is not to have a cryptographically secure password (hence the 2-way encryption), just a first line of defense against search engine indexing and crawling bots. Preferred to combine it with the unlisted-status
	const FIELD_PUBLISHDATE				= 'dtPublishDate';//publish date to schedule posts in advance.
	const FIELD_VISIBILITY				= 'enumVisibility';//visibility

	const ENUM_VISIBILITY_PRIVATE		= 'private';
	const ENUM_VISIBILITY_UNLISTED		= 'unlisted';
	const ENUM_VISIBILITY_PUBLIC		= 'public';

	const ENCRYPTION_PASSWORD_PASSPHRASE = 'wdsdf4-2%ds#=wer23rwefss'; //passphrase for the encryption algo

	/**
	 * get internal description
	 * 
	 * @return string
	 */
	public function getNameInternal()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL);
	}

	
	/**
	 * set internal description
	 * 
	 * @param string $sName
	 */
	public function setNameInternal($sName)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, $sName);
	}    

	/**
	 * get the internal data that the pagebuilder works with
	 * 
	 * @return string
	 */
	public function getData()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_DATA);
	}

	
	/**
	 * set the internal data that the pagebuilder works with
	 * 
	 * @param string $sData
	 */
	public function setData($sData)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_DATA, $sData);
	}   	
	

	/**
	 * get rendered HTML
	 * 
	 * @return string
	 */
	public function getRenderedHTML()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML);
	}

	
	/**
	 * set rendered HTML
	 * 
	 * @param string $sHTML
	 */
	public function setRenderedHTML($sHTML)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, $sHTML);
	}        
	

	/**
	 * get internal module name responsible for editing this page
	 * 
	 * @return int
	 */
	public function getModuleID()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_MODULEID);
	}

	
	/**
	 * get internal module name responsible for editing this page
	 * 
	 * @param int $iID
	 */
	public function setModuleID($iID)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_MODULEID, $iID);
	}   		



	/**
	 * get internal module name responsible for editing this page
	 * 
	 * @return int
	 */
	public function getModuleVersionNumber()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER);
	}

	
	/**
	 * get internal module name responsible for editing this page
	 * 
	 * @param int $iVersion
	 */
	public function setModuleVersionNumber($iVersion)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, $iVersion);
	}   		


	/**
	 * get user id of the author
	 * 
	 * @return int
	 */
	public function getAuthorUserID()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_AUTHORUSERID);
	}

	
	/**
	 * get internal module name responsible for editing this page
	 * 
	 * @param int $iUserID
	 */
	public function setAuthorUserID($iUserID)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_AUTHORUSERID, $iUserID);
	}   		

	/**
	 * get status id
	 * 
	 * @return int
	 */
	public function getStatusID()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_STATUSID);
	}

	
	/**
	 * set status id
	 * 
	 * @param int $iStatusID
	 */
	public function setStatusID($iStatusID)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_STATUSID, $iStatusID);
	} 	

	/**
	 * get meta pagebuilder module name internal
	 * 
	 * when you would deinstall the pagebuilder, there is no way to know with which module the page was created
	 * this field is just to give the user a reminder of which pagebuilder it was, so they can install it again if needed
	 * 
	 * @return string
	 */
	public function getMetaModuleNameInternal()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL);
	}
	
	/**
	 * set meta pagebuilder module name internal
	 * 
	 * when you would deinstall the pagebuilder, there is no way to know with which module the page was created
	 * this field is just to give the user a reminder of which pagebuilder it was, so they can install it again if needed
	 * 
	 * @param string $sModule
	 */
	public function setMetaModuleNameInternal($sModule)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL, $sModule);
	}    	

	/**
	 * get meta pagebuilder module name internal
	 * 
	 * when you would deinstall the pagebuilder, there is no way to know with which module the page was created
	 * this field is just to give the user a reminder of which pagebuilder it was, so they can install it again if needed
	 * 
	 * @return string
	 */
	public function getMetaModuleNameNice()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMENICE);
	}
	
	/**
	 * set meta pagebuilder module name nice
	 * 
	 * when you would deinstall the pagebuilder, there is no way to know with which module the page was created
	 * this field is just to give the user a reminder of which pagebuilder it was, so they can install it again if needed
	 * 
	 * @param string $sModule
	 */
	public function setMetaModuleNameNice($sModule)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMENICE, $sModule);
	} 


	/**
	 * get needs to rerender the page?
	 * 
	 * DATA-field has changed without rending the HTMLRENDERED-field. 
	 * This boolean indicates that the html cache needs to be rendered. 
	 * This can happen if something has changed outside the pagebuilder 
	 * (for example links are mass edited).
	 * 
	 * @return bool
	 */
	public function getNeedsRender()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER);
	}

	
	/**
	 * set needs to rerender the page?
	 * 
	 * DATA-field has changed without rending the HTMLRENDERED-field. 
	 * This boolean indicates that the html cache needs to be rendered. 
	 * This can happen if something has changed outside the pagebuilder 
	 * (for example links are mass edited).
	 * 
	 * @param bool $bRender
	 */
	public function setNeedsRender($bRender)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, $bRender);
	} 		

	/**
	 * get needs work?
	 * This allows the user to indicate this document needs some work
	 * 
	 * @return bool
	 */
	public function getNeedsWork()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_NEEDSWORK);
	}

	
	/**
	 * set needs work
	 * This allows the user to indicate this document needs some work
	 * 
	 * @param bool $bWork
	 */
	public function setNeedsWork($bWork)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_NEEDSWORK, $bWork);
	} 	


	/**
	 * get internal notes
	 * 
	 * internal notes about the document for users. 
	 * i.e. what is left to do, what needs to be changed, 
	 * instructions for other authors, used resources etc
	 * 
	 * @return string
	 */
	public function getNotesInternal()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_NOTESINTERNAL);
	}

	
	/**
	 * set needs work
	 * 
	 * internal notes about the document for users. 
	 * i.e. what is left to do, what needs to be changed, 
	 * instructions for other authors, used resources etc
	 * 
	 * @param string $sNotes
	 */
	public function setNotesInternal($sNotes)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_NOTESINTERNAL, $sNotes);
	} 		

	/**
	 * get website id
	 * 
	 * @return int
	 */
	public function getWebsiteID()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_WEBSITEID);
	}
	
	/**
	 * set website id
	 * 
	 * @param int $iID
	 */
	public function setWebsiteID($iWebsiteID)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_WEBSITEID, $iWebsiteID);
	}      

	/**
	 * get url slug: example.com/blog/slug
	 * 
	 * @return string
	 */
	public function getURLSlug()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_URLSLUG);
	}
	
	/**
	 * set url slug: example.com/blog/slug
	 * 
	 * @param string $sSlug
	 */
	public function setURLSlug($sSlug)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, $sSlug);
	}   

	/**
	 * get canonical url --> needs to be FULL url
	 * 
	 * A canonical link element is an HTML element that helps webmasters prevent duplicate content issues 
	 * in search engine optimization by specifying the "canonical" or "preferred" version of a web page. 
	 * It is described in RFC 6596
	 * 
	 * @return string
	 */
	public function getCanonicalURL()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL);
	}
	
	/**
	 * set canonical url --> needs to be FULL url
	 * 
	 * A canonical link element is an HTML element that helps webmasters prevent duplicate content issues 
	 * in search engine optimization by specifying the "canonical" or "preferred" version of a web page. 
	 * It is described in RFC 6596
	 * 
	 * @param string $sURL
	 */
	public function setCanonicalURL($sURL)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, $sURL);
	}   	
   
	/**
	 * get url that the 301-permanently-moved error refers to in header
	 * empty means: no 301 error
	 * 
	 * @return string
	 */
	public function get301RedirectURL()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_301REDIRECTURL);
	}
	
	/**
	 * set url that the 301-permanently-moved error refers to in header
	 * empty means: no 301 error
	 * 
	 * @param string $sURL
	 */
	public function set301RedirectURL($sURL)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_301REDIRECTURL, $sURL);
	}   	

	/**
	 * get HTML title
	 * 
	 * @return string
	 */
	public function getHTMLTitle()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE);
	}

	/**
	 * set HTML title
	 * what is in the <title> tag
	 * 
	 * @param string $sURL
	 */
	public function setHTMLTitle($sCode)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, $sCode);
	}           

	/**
	 * get HTML meta description content
	 * what is in the <meta name="description" content="bla bla"> tag
	 * 
	 * @return string
	 */
	public function getHTMLMetaDescription()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_HTML_METADESCRIPTION);
	}

	/**
	 * set HTML meta description content
	 * what is in the <meta name="description" content="bla bla"> tag
	 * 
	 * @param string $sDescription
	 */
	public function setHTMLMetaDescription($sDescription)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_HTML_METADESCRIPTION, $sDescription);
	}     	

	/**
	 * get password for document
	 * 
	 * 2-way encrypted password to access the page on the website (for password protected pages). 
	 * When empty ('') page is not password protected. 
	 * The goal is not to have a cryptographically secure password (hence the 2-way encryption), 
	 * just a first line of defense against search engine indexing and crawling bots. 
	 * Preferred to combine it with the unlisted-status
	 * 
	 * @return string
	 */
	public function getPassword()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, '', true);
	}

	/**
	 * get password for document
	 * 
	 * 2-way encrypted password to access the page on the website (for password protected pages). 
	 * When empty ('') page is not password protected. 
	 * The goal is not to have a cryptographically secure password (hence the 2-way encryption), 
	 * just a first line of defense against search engine indexing and crawling bots. 
	 * Preferred to combine it with the unlisted-status
	 * 
	 * @param string $sDescription
	 */
	public function setPassword($sPassword)
	{
		$this->set(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, $sPassword, '', true);
	}     	
 	
    /**
	 * set publish date
	 * 
	 * For example: This allows you to schedule a blog post to release next week.
	 * 
	 * when the datetime object is null, which means: never change status, which publishes a page immediately to a site
     * 
     * @param TDateTime $objDateTime when null then an invalid date (timestamp 0) will be set
     */
    public function setPublishDate($objDateTime = null)
    {
        $this->setTDateTime(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, $objDateTime);
    }        

    /**
	 * get publish date
	 * 
	 * For example: This allows you to schedule a blog post to release next week.
	 * 
     * when the datetime object is null, which means: never change status, which publishes a page immediately to a site
     * 
     * @return TDateTime
     */
    public function getPublishDate()
    {
        return $this->get(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE);
    }   


	/**
	 * get visibility
	 * 
	 * equals ENUM_VISIBILITY_PRIVATE, ENUM_VISIBILITY_UNLISTED or ENUM_VISIBILITY_PUBLIC
	 * 
	 * @return string
	 */
	public function getVisibility()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY);
	}

	/**
	 * set visibility
	 * 
	 * ENUM_VISIBILITY_PRIVATE, ENUM_VISIBILITY_UNLISTED or ENUM_VISIBILITY_PUBLIC
	 * 
	 * @param string $sVibility default ENUM_VISIBILITY_PRIVATE
	 */
	public function setVisibility($sVibility = TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE)
	{
		//check validy parameter
		if ($sVibility !== TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE &&
			$sVibility !== TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_UNLISTED &&
			$sVibility !== TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PUBLIC)
		{
			$sVibility = TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE;
		}

		$this->set(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, $sVibility);
	}    

    /**
     * make a HTML dropdown select-box with items from visibility
     * translates items into language
	 * 
	 * example
	 * <select name="visibility" id="visibility">
     *   <option value="private">Private</option>
     *   <option value="unlisted">Unlisted</option>
     *   <option value="public">Public</option>
	 * </select>
	 * 
     * @param mixed $sValueSelectedItem this value is selected in dropdown select-box --> this can be $this->getVisibility(), but can also be value from a <form></form>
     * @param Select $objHTMLSelectbox (if null then a new object is returned from this function)
     */
    public function generateHTMLSelectVisibility($sValueSelectedItem = TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE, 
									&$objHTMLSelectbox = null)
	{
		if ($objHTMLSelectbox == null)
			$objHTMLSelectbox = new Select();
		$objHTMLSelectbox->clear();
		$objHTMLSelectbox->addOption(TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE, transg('TPageBuilderDocumentsAbstract_visibility_private', 'Private'));
		$objHTMLSelectbox->addOption(TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_UNLISTED, transg('TPageBuilderDocumentsAbstract_visibility_unlisted', 'Unlisted'));
		$objHTMLSelectbox->addOption(TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PUBLIC, transg('TPageBuilderDocumentsAbstract_visibility_public', 'Public'));
		$objHTMLSelectbox->setSelectedOption($sValueSelectedItem);
		return $objHTMLSelectbox;
	}


	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
         * 
	 * initialize values
	 */
	public function initRecord()
	{}
	
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{		
		//internal name
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, '');
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, CT_VARCHAR);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, 100);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, false);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, true);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL);									

		//data
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_DATA, '');
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_DATA, CT_LONGTEXT);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_DATA, 0);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_DATA, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_DATA, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_DATA, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_DATA);	

		//rendered html cache
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, '');
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, CT_LONGTEXT);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, 0); //we don't know length
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, true);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, false); 
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, true); 
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML);	

        //module id
        $this->setFieldDefaultsIntegerForeignKey(TPageBuilderDocumentsAbstract::FIELD_MODULEID, TSysModules::class, TSysModules::getTable(), TSysModules::FIELD_ID);
        $this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_MODULEID, TSysModel::FOREIGNKEY_REFERENCE_NOACTION); //do not delete page when module is deleted
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_MODULEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE); //do not update page when module is deleted		

		//module version
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, 0);
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, CT_INTEGER32);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, 0); 
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false); 
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false); 
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER);	
		
        //user id author
        $this->setFieldDefaultsIntegerForeignKey(TPageBuilderDocumentsAbstract::FIELD_AUTHORUSERID, TSysCMSUsers::class, TSysCMSUsers::getTable(), TSysCMSUsers::FIELD_ID);
        $this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_AUTHORUSERID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT); //do not delete page when user is deleted
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_AUTHORUSERID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT); //do not update page when user is deleted		
		
        //status
        $this->setFieldDefaultsIntegerForeignKey(TPageBuilderDocumentsAbstract::FIELD_STATUSID, TPageBuilderDocumentsStatusses::class, TPageBuilderDocumentsStatusses::getTable(), TPageBuilderDocumentsStatusses::FIELD_ID);
        $this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_STATUSID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT); //do not delete page when status is deleted
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_STATUSID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT); //do not update page when status is deleted		

		//module internal name
		$this->setFieldCopyProps(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL, TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL, 100);

		//module name nice
		$this->setFieldCopyProps(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMENICE, TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMENICE, 100);

		//needs rerender: bool
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, CT_BOOL);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, 1);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER);	
		
		//needs work: bool
		$this->setFieldCopyProps(TPageBuilderDocumentsAbstract::FIELD_NEEDSWORK, TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER);
		
		//internal notes
		$this->setFieldCopyProps(TPageBuilderDocumentsAbstract::FIELD_NOTESINTERNAL, TPageBuilderDocumentsAbstract::FIELD_DATA);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_NOTESINTERNAL, true);		

        //website id
        $this->setFieldDefaultsIntegerForeignKey(TPageBuilderDocumentsAbstract::FIELD_WEBSITEID, TSysWebsites::class, TSysWebsites::getTable(), TSysWebsites::FIELD_ID);
        $this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_WEBSITEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE); //delete page when website is deleted
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_WEBSITEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE); //update page when website is deleted		

		//url slug
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, '');
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, CT_VARCHAR);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, 100);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, true);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, true);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, true);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_URLSLUG, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_URLSLUG);									

		//canonical
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, '');
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, CT_VARCHAR);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, 255);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, true);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, false);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, true);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL);			

		//301 redirect url
		$this->setFieldCopyProps(TPageBuilderDocumentsAbstract::FIELD_301REDIRECTURL, TPageBuilderDocumentsAbstract::FIELD_CANONICALURL);

		//html: title
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, CT_VARCHAR);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, 100);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, false);	
        $this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE);						

		//html: <meta name="description" content=""> tag
		$this->setFieldCopyProps(TPageBuilderDocumentsAbstract::FIELD_HTML_METADESCRIPTION, TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, 255);				

		//password
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, '');
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, CT_LONGTEXT);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, 0);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, true);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, false); 
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, false); 
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, false); 
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, false);
		$this->setFieldEncryptionCypher(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TPageBuilderDocumentsAbstract::FIELD_PASSWORD, TPageBuilderDocumentsAbstract::ENCRYPTION_PASSWORD_PASSPHRASE);			                          

		//schedule date
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, CT_DATETIME);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, 0);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE);	
		
		//visibility enum
		$this->setFieldDefaultValue(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE);
		$this->setFieldType(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, CT_ENUM);
		$this->setFieldLength(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, 100);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, false);
		$this->setFieldNullable(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, array(TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PRIVATE, TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_UNLISTED, TPageBuilderDocumentsAbstract::ENUM_VISIBILITY_PUBLIC));
		$this->setFieldUnique(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, false);
		$this->setFieldIndexed(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, true);
		$this->setFieldFulltext(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, false);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY, false);	
        $this->setFieldEncryptionDisabled(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY);			
	}
	
	/**
	 * erf deze functie over om je eigen checksum te maken voor je tabel.
	 * je berekent deze de belangrijkste velden te pakken, wat strings toe te
	 * voegen en alles vervolgens de coderen met een hash algoritme
	 * zoals met sha1 (geen md5, gezien deze makkelijk te breken is)
	 * de checksum mag maar maximaal 50 karakters lang zijn
	 *
	 * BELANGRIJK: je mag NOOIT het getID() en getChecksum()-field meenemen in
	 * je checksum berekening (id wordt pas toegekend na de save in de database,
	 * dus is nog niet bekend ten tijde van het checksum berekenen)
	 *
	 * @return string
	*/
	public function getChecksumUncrypted()
	{
		return 'whapsfe'.
		$this->get(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_DATA).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_RENDEREDHTML).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_MODULEVERSIONNUMBER).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL).
		boolToInt($this->get(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER)).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_NOTESINTERNAL).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_AUTHORUSERID).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE).
		boolToInt($this->get(TPageBuilderDocumentsAbstract::FIELD_NEEDSRENDER)).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_301REDIRECTURL).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_CANONICALURL).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_URLSLUG).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_WEBSITEID).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_PASSWORD).
		$this->get(TPageBuilderDocumentsAbstract::FIELD_VISIBILITY);		
	}

	/**
	 * returns an array with fields that are publicly viewable
	 * sometimes (for security reasons the password-field for example) you dont want to display all table fields to the user
	 *
	 * i.e. it can be used for searchqueries, sorting, filters or exports
	 *
	 * @return array function returns array WITHOUT tablename
	*/
	public function getFieldsPublic()
	{
		return array(
			TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL, 
			TPageBuilderDocumentsAbstract::FIELD_URLSLUG, 
			TPageBuilderDocumentsAbstract::FIELD_CANONICALURL, 
			TPageBuilderDocumentsAbstract::FIELD_301REDIRECTURL, 
			TPageBuilderDocumentsAbstract::FIELD_HTML_TITLE, 
			TPageBuilderDocumentsAbstract::FIELD_HTML_METADESCRIPTION,
			TPageBuilderDocumentsAbstract::FIELD_PUBLISHDATE,
			TPageBuilderDocumentsAbstract::FIELD_VISIBILITY
		);
	}	
	
	/**
	 * use the auto-added id-field ?
	 * @return bool
	*/
	public function getTableUseIDField()
	{
		return true;
	}
	
	
	/**
	 * use the auto-added date-changed & date-created field ?
	 * @return bool
	*/
	public function getTableUseDateCreatedChangedField()
	{
		return true;
	}
	
	
	/**
	 * use the checksum field ?
	 * @return bool
	*/
	public function getTableUseChecksumField()
	{
		return true;
	}
	
	/**
	 * order field to switch order between records
	*/
	public function getTableUseOrderField()
	{
		return false;
	}
	
	/**
	 * use checkout for locking file for editing
	*/
	public function getTableUseCheckout()
	{
		return true;
	}
		
	/**
	 * use record locking to prevent record editing
	*/
	public function getTableUseLock()
	{
		return true;
	}            
	
        
	/**
	 * use image in your record?
    * if you don't want a small and large version, use this one
	*/
	public function getTableUseImageFile()
	{
		return false;
	}
        
        
	/**
	 * opvragen of records fysiek uit de databasetabel verwijderd moeten worden
	 *
	 * returnwaarde interpretatie:
	 * true = fysiek verwijderen uit tabel
	 * false = record-hidden-veld gebruiken om bij te houden of je het record kan zien in overzichten
	 *
	 * @return bool moeten records fysiek verwijderd worden ?
	*/
	public function getTablePhysicalDeleteRecord()
	{
		return true;
	}
	
	
	/**
	 * type of primary key field
	 *
	 * @return integer with constant CT_AUTOINCREMENT or CT_INTEGER32 or something else that is not recommendable
	*/
	public function getTableIDFieldType()
	{
		return CT_AUTOINCREMENT;
	}
		

	/**
	 * use a second id that has no follow-up numbers?
	 */
	public function getTableUseRandomID()
	{
		return false;
	}        
	
	/**
	 * is randomid field a primary key?
	 */        
	public function getTableUseRandomIDAsPrimaryKey()
	{
		return false;
	}       
        
	/**
	 * use a third character-based id that has no logically follow-up numbers?
	 * 
	 * a tertiary unique key (uniqueid) can be useful for security reasons like login sessions: you don't want to _POST the follow up numbers in url
	 */
	public function getTableUseUniqueID()
	{
		return false;
	} 

	
	/**
	 * use a random string id that has no logically follow-up numbers
	 * 
	 * this is used to produce human readable identifiers
	 * @return bool
	 */
	public function getTableUseNiceID()
	{
		return false;
	}	
		
	/**
	 * OVERSCHRIJF DOOR CHILD KLASSE ALS NODIG
	 *
	 * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
	 * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 *
	 *
	 * return '??? - functie niet overschreven door child klasse';
	*/
	public function getDisplayRecordShort()
	{
		return $this->get(TPageBuilderDocumentsAbstract::FIELD_NAMEINTERNAL);
	}
	
		
	/**
	 * DEZE FUNCTIE MOET OVERGEERFD WORDEN DOOR DE CHILD KLASSE
	 *
	 * checken of alle benodigde waardes om op te slaan wel aanwezig zijn
	 *
	 * @return bool true=ok, false=not ok
	*/
	public function areValuesValid()
	{     

		return true;
	}
	
	/**
	 * for the automatic database table upgrade system to work this function
	 * returns the version number of this class
	 * The update system can compare the version of the database with the Business Logic
	 *
	 * default with no updates = 0
	 * first update = 1, second 2 etc
	 *
	 * @return int
	*/
	public function getVersion()
	{
		return 0;
	}
	
	/**
	 * update the table in the database
	 * (may have been changes to fieldnames, fields added or removed etc)
	 *
	 * @param int $iFromVersion upgrade vanaf welke versie ?
	 * @return bool is alles goed gegaan ? true = ok (of er is geen upgrade gedaan)
	*/
	protected function refactorDBTable($iFromVersion)
	{
		return true;
	}	

	/**
	 * is this model a translation model?
	 *
	 * @return bool is this model a translation model?
	 */
	public function getTableUseTranslationLanguageID()
	{
		return true;
	}    

	/**
	 * Want to use the 'isdefault' field in database table?
	 * Returning true allows 1 record to be the default record in a table
	 * This is useful for creating records with foreign fields without user interference OR 
	 * selecting records in GUI elements like comboboxes
	 * 
	 * example: select the default language in a combobox
	 * 
	 * @return bool
	 */
	public function getTableUseIsDefault()
	{
		return false;
	}

	/**
	 * can a record be favorited by the user?
	 *
	 * @return bool
	 */
	public function getTableUseIsFavorite()
	{
		return false;
	}	

	/**
	 * can record be transcanned?
	 * Trashcan is an extra step in for deleting a record
	 *
	 * @return bool
	 */
	public function getTableUseTrashcan()
	{
		return false;
	}	

	/**
	 * use a field for search keywords?
	 * (also known als tags or labels)
	 *
	 * @return bool
	 */
	public function getTableUseSearchKeywords()
	{
		return false;
	}		

} 
?>