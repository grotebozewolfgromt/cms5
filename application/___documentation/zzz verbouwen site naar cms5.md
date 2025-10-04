## wat heeft een verbouwing van site naar cms5 nodig?
# libraries toevoegen:
 	-index.php
	include_once(GLOBAL_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php'); (voor redirectHTTPS
	include_once(GLOBAL_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php'); (voor hoursLeftOffer())

	-book-tube
	include_once(GLOBAL_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php'); (voor hoursLeftOffer())
	-blog.php
	include_once(GLOBAL_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_inet.php');		
	-blogdetail.php
	include_once(GLOBAL_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_inet.php');    
	include_once(GLOBAL_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php');    

# tabellen overzetten:
	-tblBEDEVLivestreamSubmissions
	-tblBEDEVSiteContent
	-tblBEDEVWeblog
	-tblBEDEVWeblogTags
	-siteid niet vergeten

# GLOBALS
GLOBAL_PATH_DOMAIN => WEBSITE_PATH_DOMAIN
GLOBAL_PATH_LOCAL => WEBSITE_PATH_LOCAL
GLOBAL_PATH_WWW => WEBSITE_PATH_WWW
GLOBAL_DB_SITEID => WEBSITE_DB_SITEID