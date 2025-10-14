<?php
namespace dr\classes\controllers;

// use dr\modules\modules\TControllerAbstract;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of dashboard
 *
 * This is the first page shown to users with a summary of different stuff
 * interesting to the user.
 * 
 * @author dennis renirie
 * 18 apr 2024: dashboard created
 *
 */

class dashboard extends TControllerAbstract
{

    /**
     * This function adds EARLY BINDING variables to template, which are cached (if cache enabled)
     * (see description on top of this class for more info)
     * 
     * this is the plain-old-fashioned way of doing php with regular php variables etc.
     *
     * executes the things you want to cache
     * this function is ONLY called on a cache miss 
     * (if caching enabled, if NOT enabled it's ALWAYS called).
     * This function generates content for the cache file and for displaying on-screen
     * 
     * this function is executed BEFORE bindVarsLate(), because it's early binding
     * 
     * @return array with variables, use: "return get_defined_vars();" to use all variables declared in the execute() function
     */
    public function bindVarsEarly()
    {
        $sTitle = transcms('home_title', '[applicationname] Dashboard', 'applicationname', APP_CMS_APPLICATIONNAME);
        $sHTMLTitle = transcms('home_htmltitle', '[applicationname] Dashboard', 'applicationname', APP_CMS_APPLICATIONNAME);
        $sHTMLMetaDescription = transcms('home_htmlmetadescription', '[applicationname] Dashboard', APP_CMS_APPLICATIONNAME);

        return get_defined_vars();
    }

    /**
     * This function adds LATE BINDING variables to template which are NOT cached 
     * (for more info: see description on top of this class)
     * 
     * executes the things you always want to execute, even on a cache miss
     * bindVarsEarly() is executed first, then bindVarsLate()
     *  
     * These variables that aren't resolved by php in the cache file
     * This way you can add dynamic php code to an otherwise cached page
     * 
     * These late binding variables need to be in the following format in the template: [variablename]
     * (Otherwise PHP will resolve variables in thecachefile with the format: $variablename)
     * 
     * This function is executed AFTER bindVarsEarly()
     * 
     * @return array with variables, use: "return get_defined_vars();" to use all variables declared in the execute() function
     */
    public function bindVarsLate()
    {
        return get_defined_vars();
    }


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_dashboard.php';
    }

    /**
     * return path of the skin template
     * 
     * return '' if no skin
     *
     * @return string
     */
    public function getSkinPath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php';
    }


}
