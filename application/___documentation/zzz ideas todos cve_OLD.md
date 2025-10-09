# IDEAS


## wat heeft een verbouwing van site naar cms5 nodig?
-libraries toevoegen:
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

-tabllen overzetten:
	-tblBEDEVLivestreamSubmissions
	-tblBEDEVSiteContent
	-tblBEDEVWeblog
	-tblBEDEVWeblogTags
-siteid niet vergeten

## TODOs
SHORT TERM TODOS:

URL validator controleren op latin chars
email validator controleren op latin chars

detailscherm accounts --> WERKT, maar moet users openen en nieuwe user in account createn
checkbox bij user: create new account for user (on save first time)
optie: invite creeren die naar user verstuurd wordt, die in hetzelfde account terecht komt
account holders moeten users kunnen toevoegen in het instellingen scherm
//lierelei no verwijderen omdat het aantal inlogs opgehoogd is
bij loginsessies land vermelden


aparte tabel met producten waar de maandelijkse, jaarlijkse or eenmalige fees in staan met koppeling naar roles (default is anonymous)
geen gebruikers toe kunnen voegen aan anonymous role

	-rename 'overview' -> 'list' voor controllers
	-amerikaanse notatie van tijd in tijdboxen
	-het recoveren van een password werkt niet. het lijkt aan de email token te liggen, die niet overeenkomt met de token in de database
	-createn account lukt niet omdat voor password: special chars niet worden geaccepteerd, maar wel vereist zijn. waarschijnlijk filterXSS functie in getContentsSubmitted() in FormInputAbstract
	-TOEVOEGEN: logincontroller: preventTimingAttack() als login goed
	-cookie path in logincontroller setten op www_path/subdir van cms
	-ip spoofing protection; persistent-ip-address: 
		bij inlogform weergeven: token uitgeven in cookie: 
		cookie en ip adres opslaan in db table. 
		bij inloggen moet cookie token kloppen met ip, en token invalidaten
		op deze manier kunnen alleen 'goedgekeurde' ip's inloggen --> voor 'remind me' inloggen
		in loginsessions is ip opgeslagen: deze controleren met cookie
	-invalidate the form token after it has been used, otherwise an attacker can reuse it
	-eigen password verify function schrijven met pepper
	-eigen password verify function vervangen in logincontroller
	-tmodel: validateChecksum & calculateChecksum: pepper via hash_hmac gebruiken
	-min watchtwoord lengte van 10 chars afdwingen
	-tabelchecksums vastleggen in TTableVersions
	-checken cookies: als je toegang hebt tot de database, kun je dan via cookie inloggen?? 
		-> salting and pepering login token in cookie
		-> extra token gebruiken voor verificatie (ipv encrypted in db, ook encrypted in db)
	-check in TSysModel om definetablefouten op te sporen (omdat deze heel moeilijk te vinden zijn)
	-login via google
	-formgenerator: 
		-> als error op form dan preventTimingAttack(), slow down the attacker
		->extra check op request url (domein moet hetzelfde zijn) 
		->honeypot
		->recaptcha support (verplaatsen vanuit logincontroller)
		->hdSubmitted verwijderen en isSubmitted() checkt op existence hdCTRF token
	-formgenerator flood detection: https://www.hashbangcode.com/article/drupal-9-integrating-flood-protection-forms
	-field voor seed/salt aanmaken (in login controller zit al een seed veld -> renamen), die je kan gebruiken om toe te voegen aan passwords en usernames
	-usersessions
		-hashen (+salt) ip addressen (kan salten? wordt daarop gezocht?)
	-cmsusersflooddetect: 
		-ip adres hashen (kan niet salten, want er wordt op gezocht)
	-html purifyer schrijven (cleanHTMLMarkup kopieren). en deze aanroepen in filterXSS()
	-user settings, checken of user de settings saved van eigen account ipv ander account https://www.youtube.com/watch?v=rloqMGcPMkI
	-.php extensie verwijderen voor bestanden buiten het CMS (misschien zelfs in het cms). alle file urls worden gereroute naar .php variant in .htaccess
	-mail component, filteren injection headers:  op dubbelepunt(:) en foutmelding boolean possible injection attack
	-tijden (time between submits, time between show and submit) van formulieren in logincontroller instellen: voor inloggen, recovery, create account
	-generateSubmitButton() + generateCancelButton() met onClick() event om de button en de rest van de form te disablen (om dubbele submission te voorkomen)
	-naast een systemwide pepper, een pepper per class dat inheritance afgedwongen wordt via TSysModel
	-ipv generateWord voor cookie identification uniqueid gebruiken
	-als er een randomID() is in TSysModel dan randomid gebruiken in crudcontrollers ivm veiligheid
	=kan ik hier wat mee voor de ip land check: to guy who wrote clientIPToHex() function - you reinvented so many wheels. bin2hex(inet_pton($ip)) will do the same thing.
	-dark mode: alle kleur informatie naar aparte CSS verplaatsen (colorscheme.css), dan is het heel makkelijk om een andere te kiezen en zo een dark mode te implementeren

 problemen/todos:
TODO BELANGRIJK: bij inloggen checken of de request komt van de huidige server (om te voorkomen dat een andere computer probeert in te loggen ipv huidige php server). Cors. https://reqbin.com/req/php/yxgi232e/get-request-with-cors-headers
TODO BELANGRIJK: forms: checken of de request komt van de huidige server (om te voorkomen dat een andere computer probeert formulier te submitten ipv huidige php server). cors: https://reqbin.com/req/php/yxgi232e/get-request-with-cors-headers
https://youtu.be/mL8EuL7jSbg?t=720 checken of cookie is geset als httpOnly (anders kan javascript cookie lezen)
 todo: Strongpassword hint heeft kan geen html tags tags aan voor error. dit ivm meerdere rules
 todo: inloggen werkt niet meer als je een preg_escape doet in badcharswhitelist and badcharsblacklist
todo: countries module (dan kunnen we dat in select-box weergeven bij registreren account)
todo: general html-blocks builder maken die je voor webpaginas, blogs, emails etc kunt overerven hergebruiken met extra componenten
todo: config file per host (kun je makkelijk hele cms directory vervangen)
todo: column in db: ipadres-hash ipv plain ipadres (ivm hack, dan heb je ips van gebruikers die tot unieke persoon te herleiden zijn)
todo: auto expire van een login sessie
todo: login: regenerate sessie bij handleLogin()
todo: loginhistorie (userid, 'ip', 'fingerprint', date)
todo: backups maken (ook in cronjob en bij uninstall)
todo: background uploading files/images
-ip locatie database en locatie aan login sessies toevoegen in user detail scherm (niet in db ivm gdpr)
-de detailsave crud controller checkt niet of record is gelocked of checked out: handleNewEditRecord()
-als quicksearch en dan bulk action, de quicksearch wordt gewist. de bulkacties zijn een _GET ipv _POST
-er staan cms waarden zoals constanten als cms_languages in de bootstrap v/h framework ipv cms
-@todo tab selection doesn't work when url parameter exists
-there is a running issue automatically logging out: this has to do with the browserfingerprint which returns sometimes a different fingerprint. The debugLog() logs the generated fingerprints
-refresh tokens inbouwen in logincontroller (iedere 1 minuut of iedere page refresh een nieuwe token krijgen van de server. nadenken of in url of in cookie meegeven)


## speed optimization ideas
* try catch statements verminderen
* TCSV platslaan en interne array gebruiken
* TModel: inbouwen loadFromSession(id) om data sneller te krijgen
* translations cachen in sessie
* localisation object cachen in sessie
* localisation settings laden uit json file (=sneller??)

## ideas
* render geeft string terug. parameter toevoegen render(param)

