<?php
namespace dr\modules\Mod_POSWebshop;

use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_POSWebshop\models\TProductCategories;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TProductsLanguages;
use dr\modules\Mod_POSWebshop\models\TProductsSKUs;
use dr\modules\Mod_POSWebshop\models\TTransactions;
use dr\modules\Mod_POSWebshop\models\TTransactionsLines;
use dr\modules\Mod_POSWebshop\models\TTransactionsTypes;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_POSWebshop\models\TVATClassesCountries;

/**
 * Description of Mod_POSWebshop
 * 
 * Module for (webshop)orders and invoices
 * This modules NEEDS the Contacts module
 * 
 * outstanding invoices = vertaling van nog te betalen facturen
 * past due -> vertaling: betalingstermijn verstreken
 * 
 * 
 * @author drenirie
 */
class Mod_POSWebshop extends TModuleAbstract
{
    const PERM_CAT_TRANSACTIONS = 'transactions';
    const PERM_CAT_TRANSACTIONSTYPES = 'transactions-types';
    const PERM_CAT_VATCLASSES = 'vat-classes';    
    const PERM_CAT_PRODUCTS = 'products';
    const PERM_CAT_PRODUCTCATEGORIES = 'productcategories';
    
    /**
     * returns the type of the module
     * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
     * if you don't know, return MOD_TYPE_REGULAR
     * 
     * @return string
     */    
    public function getModuleType() 
    {
        return TModuleAbstract::MOD_TYPE_REGULAR; 
    }

    /**
     * returns list of instantiated models that are used
     * 
     * THE ORDER IN WHICH IT RETURNS IS IMPORTANT
     * First the dependencies, than the objects itself
     *
     * system modules are done in the system, so they return: array()
     * 
     * @return array 1d with TSysModel objects
     */        
    public function getModelObjects() 
    {
        return array(
            new TVATClasses(),
            new TTransactionsTypes(),            
            new TTransactions(),
            new TTransactionsLines(),
            new TVATClassesCountries(),      
            new TProductCategories(),
            new TProducts(),
            new TProductsSKUs(), 
            new TProductsLanguages()                  
        );
    }

   /**
     * returns the tabsheets for this module
     *
     * dwhen not overridden, it returns index by default
     * 
     * specify array with filename, permission-category, description like this:
     *         return array(
     *                     array('overview_blog.php', Mod_Blog::PERM_CAT_BLOG, 'blog posts', 'explanation about blog posts'),
     *                     array('overview_authors.php', Mod_Blog::PERM_CAT_AUTHORS, 'blog authors', 'explanation about authors for blog posts')
     *                  )
     * 
     * the tab names and descriptions are translated with the transm() function, so don't return translated tabnames and descriptions
     * 
     * @return array
     */   
    public function getTabsheets()
    {
        return array(
            array('list_transactions', Mod_POSWebshop::PERM_CAT_TRANSACTIONS, 'Transactions', 'Manage all invoices and orders'),
            array('list_transactionstypes', Mod_POSWebshop::PERM_CAT_TRANSACTIONS, 'Trans. Types', 'Manage types of transactions'),
            array('list_vatclasses', Mod_POSWebshop::PERM_CAT_VATCLASSES, 'VAT', 'Manage VAT classes'),
            array('list_products', Mod_POSWebshop::PERM_CAT_PRODUCTS, 'Products', 'Manage products.<br><br>A product can have 1 or more SKU\'s (Stock Keeping Units).<br>The SKU\'s of a Tiger T-shirt could be:<ul><li>black L</li><li>black M</li><li>black S</li><li>green L</li><li>green M</li><li>green S</li></ul>'),
            array('list_productcategories', Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES, 'Categories', 'Make a structure to find your products more easily.<br>You can drag and drop a category into another to create a hierarchical structure.')
        );
    } 
    
    /**
     * returns the menu items for this module (shown on the left side of the screen in the CMS)
     * the items from this function are stored in TSysCMSMenu on module installation.
     * in TSysCMSMenu the user is able to edit, remove, move position these menu items
     * 
     * you can return and empty array when you don't want extra items under the module in the menu
     *
     * specify array with filename, permission-category, name, svg-icon like this:
     *         return array (
     *                      array   (
     *                                  AK_CMSMENUITEM_CONTROLLER => 'list_blog.php',
     *                                  AK_CMSMENUITEM_PERMISSIONCATEGORY =>  Mod_Blog::PERM_CAT_BLOG,
     *                                  AK_CMSMENUITEM_NAMEDEFAULT => 'blog posts',
     *                                  AK_CMSMENUITEM_SVGICON => '<svg></circle></svg>',
     *                                  AK_CMSMENUITEM_ISVISIBLEMENU => true,
     *                                  AK_CMSMENUITEM_ISVISIBLETOOLBAR => false,
     *                              ), 
     *                      array   (
     *                                  AK_CMSMENUITEM_CONTROLLER => 'list_authors.php',
     *                                  AK_CMSMENUITEM_PERMISSIONCATEGORY =>  Mod_Blog::PERM_CAT_AUTHORS,
     *                                  AK_CMSMENUITEM_NAMEDEFAULT => 'blog authors',
     *                                  AK_CMSMENUITEM_SVGICON => '<svg></circle></svg>'
     *                                  AK_CMSMENUITEM_ISVISIBLEMENU => true,
     *                                  AK_CMSMENUITEM_ISVISIBLETOOLBAR => false,
     *                              ), 
     *                      );
     *          
     * 
     * the tab names and descriptions are translated with the transm() function, so don't return translated tabnames and descriptions
     * 
     * @return array
     */ 
    public function getMenuItems()
    {
        return array (
            array   (
                        TModuleAbstract::AK_CMSMENUITEM_CONTROLLER => 'list_transactions',
                        TModuleAbstract::AK_CMSMENUITEM_PERMISSIONCATEGORY => Mod_POSWebshop::PERM_CAT_TRANSACTIONS,
                        TModuleAbstract::AK_CMSMENUITEM_NAMEDEFAULT => 'transactions',
                        TModuleAbstract::AK_CMSMENUITEM_SVGICON => '',
                        TModuleAbstract::AK_CMSMENUITEM_ISVISIBLEMENU => true,
                        TModuleAbstract::AK_CMSMENUITEM_ISVISIBLETOOLBAR => false,
                    ), 
            array   (
                        TModuleAbstract::AK_CMSMENUITEM_CONTROLLER => 'list_products',
                        TModuleAbstract::AK_CMSMENUITEM_PERMISSIONCATEGORY =>  Mod_POSWebshop::PERM_CAT_PRODUCTS,
                        TModuleAbstract::AK_CMSMENUITEM_NAMEDEFAULT => 'products',
                        TModuleAbstract::AK_CMSMENUITEM_SVGICON => '',
                        TModuleAbstract::AK_CMSMENUITEM_ISVISIBLEMENU => true,
                        TModuleAbstract::AK_CMSMENUITEM_ISVISIBLETOOLBAR => false,
                    ), 
                );
    }  



    public function getCategoryDefault()
    {
        return TModuleAbstract::CATEGORYDEFAULT_POS;
    }

    /**
     * handles cron job
     *
     * @return bool
     */
    public function handleCronJob() 
    {
        return true;
    }

    /**
     * return permissions array
     *
     * @return array
     */
    public function getPermissions()
    {
        return array(
            Mod_POSWebshop::PERM_CAT_TRANSACTIONS => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT
                                            ),
            Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_CHANGEORDER
                                            ),     
            Mod_POSWebshop::PERM_CAT_VATCLASSES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT,
                                                TModuleAbstract::PERM_OP_CHANGEORDER
                                            ),     
            Mod_POSWebshop::PERM_CAT_PRODUCTS => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT,
                                            ),                                            
            Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT,
                                                TModuleAbstract::PERM_OP_CHANGEORDER,
                                            ),                                                                                                                             
            ) ;    
          
    }     

    /**
     * the permissions of a module that are allowed in the demo mode
     * This function returns the same array structure as getPermissions(),
     * BUT has ONLY the permissions in it that are allowed in the demo mode.
     * 
     * The easiest way to create this function is to copy/paste the array of getPermissions()
     * and delete the items you dont need
     * 
     * example with a module to register 'books' and 'authors':
     * return array(
     *       TModuleAbstract::PERM_CAT_BOOKS => array (TModuleAbstract::PERM_OP_VIEW,
     *                                                 // TModuleAbstract::PERM_OP_DELETE, dont allow deletion
     *                                                 TModuleAbstract::PERM_OP_CHANGE,
     *                                                 TModuleAbstract::PERM_OP_CREATE,
     *                                                 TModuleAbstract::PERM_OP_LOCKUNLOCK,
     *                                                 TModuleAbstract::PERM_OP_CHECKINOUT)
     *       TModuleAbstract::PERM_CAT_AUTHORS => array (TModuleAbstract::PERM_OP_VIEW,
     *                                                // TModuleAbstract::PERM_OP_DELETE, dont delete
     *                                                 //TModuleAbstract::PERM_OP_CHANGE dont change
     *                                                  )
     *      ) ;
     * 
     * 
     * @return array 2d
     */
    public function getPermissionsDemoModeAllowed()
    {
        return array(
            Mod_POSWebshop::PERM_CAT_TRANSACTIONS => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT
                                            ),
            Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_CHANGEORDER
                                            ),     
            Mod_POSWebshop::PERM_CAT_VATCLASSES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_CHANGEORDER
                                            ),     
            Mod_POSWebshop::PERM_CAT_PRODUCTS => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                            ),                                            
            Mod_POSWebshop::PERM_CAT_PRODUCTCATEGORIES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_CHANGEORDER,
                                            ),                                                                                                                             
            ) ;     
    }


    /**
     * get the default (non-internal) name for the module.
     * This is de DEFAULT ENGLISH translation as it is passed to the
     * transm() function
     *
     * @return void
     */
    public function getNameDefault()
    {
        return 'POS + Webshop';
    }      

   /**
     * return an array with all settings for the cms
     *
     * this will return an array in this format:
     *         return array(
     *       SETTINGS_CMS_MEMBERSHIP_ANYONECANREGISTER => array ('0', TP_BOOL) //default, type
     *       );   
     * 
     * @return array
     */
    public function getSettingsEntries()
    {
        return array();
    }    

    /**
     * who made it?
     * @return string
     */
    public function getAuthor()
    {
        return 'Dennis Renirie';
    }

    /**
     * versi0n 1,2,3 etc needed for database refactoring.
     * when you are doing a database structur change, increment the version number by 1
     * (we use integers for fast, easy and reliable comparing between version numbers)
     * 
     * @return int
     */
    public function getVersion()
    {
         return 1;
    }

    /**
     * returns the url to the settings page in the cms
     * when '' is returned the setting screen is assumed not to exist
     * 
     * @return string
     */
    public function getURLSettingsCMS()
    {
        return 'settings';
    }


    /**
     * is module visible in CMS menus?
     *
     * @return boolean 
     */
    public function isVisibleCMS()
    {
        return true;
    }


    /**
     * is module visible in menus in the frontend of the site?
     *
     * @return boolean 
     */
    public function isVisibleFrontEnd()
    {
        return false;
    }  
    
    /**
     * returns the url of the website of the author
     * when '' is returned the site is assumed not to exist
     * 
     * @return string
     */
    public function getURLAuthor()
    {
        return 'https://www.dexxterclark.com';
    }

    /**
     * returns the url of the support part of the website of the author
     * when '' is returned the url is assumed not to exist
     * 
     * @return string
     */
    public function getURLSupport()
    {
        return 'https://www.dexxterclark.com/business-inquiries';
    }    

    /**
     * returns svg icon
     * 
     * example: return '<svg><path="blabla"></svg>';
     * 
     * please override this to return your own icon
     * 
     * @return string
     */
    public function getIconSVG()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>';
    }      

    /**
     * returns a subdirectory for the uploadfilemanager to put files in
     * return '' when root upload dir is ok, or you don't want to use the uploadfilemanager
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return 'poswebshop';
    }       
}
