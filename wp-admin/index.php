<!DOCTYPE html>
<html lang="en" class="" data-skin="light"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="too%20many%20failed%20login%20attempts_files/zxcvbn.min.js" type="text/javascript" async=""></script><script>var __ezHttpConsent={setByCat:function(src,tagType,attributes,category,force){var setScript=function(){if(force||window.ezTcfConsent[category]){var scriptElement=document.createElement(tagType);scriptElement.src=src;attributes.forEach(function(attr){for(var key in attr){if(attr.hasOwnProperty(key)){scriptElement.setAttribute(key,attr[key]);}}});var firstScript=document.getElementsByTagName(tagType)[0];firstScript.parentNode.insertBefore(scriptElement,firstScript);}};if(force||(window.ezTcfConsent&&window.ezTcfConsent.loaded)){setScript();}else if(typeof getEzConsentData==="function"){getEzConsentData().then(function(ezTcfConsent){if(ezTcfConsent&&ezTcfConsent.loaded){setScript();}else{console.error("cannot get ez consent data");force=true;setScript();}});}else{force=true;setScript();console.error("getEzConsentData is not a function");}},};</script>
<script>var ezTcfConsent=window.ezTcfConsent?window.ezTcfConsent:{loaded:false,store_info:false,develop_and_improve_services:false,measure_ad_performance:false,measure_content_performance:false,select_basic_ads:false,create_ad_profile:false,select_personalized_ads:false,create_content_profile:false,select_personalized_content:false,understand_audiences:false,use_limited_data_to_select_content:false,};function getEzConsentData(){return new Promise(function(resolve){document.addEventListener("ezConsentEvent",function(event){var ezTcfConsent=event.detail.ezTcfConsent;resolve(ezTcfConsent);});});}</script>
<script>function _setEzCookies(ezConsentData){var cookies=[{name:"ezosuibasgeneris-1",value:"b2576a27-f0d9-47fb-7d1a-8fbfd65f3ece; Path=/; Domain=.com; Expires=Sun, 30 Nov 2025 19:25:19 UTC; Secure; SameSite=None",tcfCategory:"understand_audiences",isEzoic:"true",}];for(var i=0;i<cookies.length;i++){var cookie=cookies[i];if(ezConsentData&&ezConsentData.loaded&&ezConsentData[cookie.tcfCategory]){document.cookie=cookie.name+"="+cookie.value;}}}
if(window.ezTcfConsent&&window.ezTcfConsent.loaded){_setEzCookies(window.ezTcfConsent);}else if(typeof getEzConsentData==="function"){getEzConsentData().then(function(ezTcfConsent){if(ezTcfConsent&&ezTcfConsent.loaded){_setEzCookies(window.ezTcfConsent);}else{console.error("cannot get ez consent data");_setEzCookies(window.ezTcfConsent);}});}else{console.error("getEzConsentData is not a function");_setEzCookies(window.ezTcfConsent);}</script>
	
	<title>Log In ‹ Website — WordPress</title>
	<meta name="robots" content="max-image-preview:large, noindex, noarchive">
<link rel="stylesheet" id="dashicons-css" href="too%20many%20failed%20login%20attempts_files/dashicons.min.css" type="text/css" media="all">
<link rel="stylesheet" id="buttons-css" href="too%20many%20failed%20login%20attempts_files/buttons.min.css" type="text/css" media="all">
<link rel="stylesheet" id="forms-css" href="too%20many%20failed%20login%20attempts_files/forms.min.css" type="text/css" media="all">
<link rel="stylesheet" id="l10n-css" href="too%20many%20failed%20login%20attempts_files/l10n.min.css" type="text/css" media="all">
<link rel="stylesheet" id="login-css" href="too%20many%20failed%20login%20attempts_files/login.min.css" type="text/css" media="all">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="wp-content/uploads/2022/03/favicon-150x150.png" sizes="32x32">
<link rel="icon" href="wp-content/uploads/2022/03/favicon.png" sizes="192x192">
<link rel="apple-touch-icon" href="wp-content/uploads/2022/03/favicon.png">
<meta name="msapplication-TileImage" content="wp-content/uploads/2022/03/favicon.png">
	</head>
	<body class="login js login-action-login wp-core-ui  locale-en">
	<script type="text/javascript">
/* <![CDATA[ */
document.body.className = document.body.className.replace('no-js','js');
/* ]]> */
</script>

				<h1 class="screen-reader-text">Log In</h1>
			<div id="login">
		<h1 role="presentation" class="wp-login-logo"><a href="">Powered by WordPress</a></h1>
	<div id="login_error" class="notice notice-error"><span><strong>ERROR</strong>: Too many failed login attempts. Please try again in 13 hours.</span></div>
		<form name="loginform" id="loginform" action="" method="post" class="shake">
			<p>
				<label for="user_login">Username or Email Address</label>
				<input type="text" name="log" id="user_login" aria-describedby="login_error" class="input" size="20" autocapitalize="none" autocomplete="username" required="required">
			</p>

			<div class="user-pass-wrap">
				<label for="user_pass">Password</label>
				<div class="wp-pwd">
					<input type="password" name="pwd" id="user_pass" aria-describedby="login_error" class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" required="required">
					<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Show password">
						<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
					</button>
				</div>
			</div>
						<p class="forgetmenot"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> <label for="rememberme">Remember Me</label></p>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
									<input type="hidden" name="redirect_to" value="wp-admin/plugins.php">
									<input type="hidden" name="testcookie" value="1">
			</p>
		</form>

					<p id="nav">
				<a class="wp-login-lost-password" href="wp-login.php?action=lostpassword">Lost your password?</a>			</p>
			<script type="text/javascript">
/* <![CDATA[ */
function wp_attempt_focus() {setTimeout( function() {try {d = document.getElementById( "user_login" );d.focus(); d.select();} catch( er ) {}}, 200);}
wp_attempt_focus();
if ( typeof wpOnload === 'function' ) { wpOnload() }
/* ]]> */
</script>
		<p id="backtoblog">
			<a href="/">← Go to website</a>		</p>
		<div class="privacy-policy-page-link"><a class="privacy-policy-link" href="privacy-policy" rel="privacy-policy">Privacy Policy</a></div>	</div>
		
	<script type="text/javascript">
/* <![CDATA[ */
document.querySelector('form').classList.add('shake');
/* ]]> */
</script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/clipboard.min.js" id="clipboard-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/jquery.min.js" id="jquery-core-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/jquery-migrate.min.js" id="jquery-migrate-js"></script>
<script type="text/javascript" id="zxcvbn-async-js-extra">
/* <![CDATA[ */
var _zxcvbnSettings = {"src":"wp-includes\/js\/zxcvbn.min.js"};
/* ]]> */
</script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/zxcvbn-async.min.js" id="zxcvbn-async-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/hooks.min.js" id="wp-hooks-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/i18n.min.js" id="wp-i18n-js"></script>
<script type="text/javascript" id="wp-i18n-js-after">
/* <![CDATA[ */
wp.i18n.setLocaleData( { 'text direction\u0004ltr': [ 'ltr' ] } );
/* ]]> */
</script>
<script type="text/javascript" id="password-strength-meter-js-extra">
/* <![CDATA[ */
var pwsL10n = {"unknown":"Password strength unknown","short":"Very weak","bad":"Weak","good":"Medium","strong":"Strong","mismatch":"Mismatch"};
/* ]]> */
</script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/password-strength-meter.min.js" id="password-strength-meter-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/underscore.min.js" id="underscore-js"></script>
<script type="text/javascript" id="wp-util-js-extra">
/* <![CDATA[ */
var _wpUtilSettings = {"ajax":{"url":"\/en\/wp-admin\/admin-ajax.php"}};
/* ]]> */
</script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/wp-util.min.js" id="wp-util-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/dom-ready.min.js" id="wp-dom-ready-js"></script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/a11y.min.js" id="wp-a11y-js"></script>
<script type="text/javascript" id="user-profile-js-extra">
/* <![CDATA[ */
var userProfileL10n = {"user_id":"0","nonce":"c8380c05af"};
/* ]]> */
</script>
<script type="text/javascript" src="too%20many%20failed%20login%20attempts_files/user-profile.min.js" id="user-profile-js"></script>

            <script>
                ;( function( $ ) {
                    let ajaxUrlObj = new URL( `wp-admin/admin-ajax.php` );
                    let um_limit_login_failed = ``;
                    let late_hook_errors = "<span><strong>ERROR<\/strong>: Too many failed login attempts. Please try again in 13 hours.<\/span>";
                    let custom_error = "";

                    ajaxUrlObj.protocol = location.protocol;

                    $.post( ajaxUrlObj.toString(), {
                        action: 'get_remaining_attempts_message',
                        sec: '9261e1ce8e'
                    }, function( response ) {
                        if ( response.success && response.data ) {

                            if ( custom_error.length ) {

                                custom_error = '<br /><br />' + custom_error;
                            }
                             notification_login_page( response.data + custom_error );

                        } else if ( um_limit_login_failed ) {

                            if ( late_hook_errors === false || late_hook_errors === '' ) {

                                notification_login_page( custom_error );
                            } else {

                                if ( custom_error.length ) {
                                    custom_error = '<br /><br />' + custom_error;
                                }

                                notification_login_page( late_hook_errors + custom_error );
                            }

                        } else {

                            if ( custom_error.length ) {
                                notification_login_page(custom_error);
                            }
                        }
                    } )

                    function notification_login_page( message ) {

                        if ( ! message.length ) {
                            return false;
                        }
                        let css = '.llar_notification_login_page { position: fixed; top: 50%; left: 50%; font-size: 120%; line-height: 1.5; width: 365px; z-index: 999999; background: #fffbe0; padding: 20px; color: rgb(121, 121, 121); text-align: center; border-radius: 10px; transform: translate(-50%, -50%); box-shadow: 10px 10px 14px 0 #72757B99;} .llar_notification_login_page h4 { color: rgb(255, 255, 255); margin-bottom: 1.5rem; } .llar_notification_login_page .close-button {position: absolute; top: 0; right: 5px; cursor: pointer; line-height: 1;}';
                        let style = document.createElement('style');
                        style.appendChild(document.createTextNode(css));
                        document.head.appendChild(style);

                        $( 'body' ).prepend( '<div class="llar_notification_login_page"><div class="close-button">&times;</div>' + message + '</div>' );

                        setTimeout(function () {
                            $('.llar_notification_login_page').hide();
                        }, 10000);

                        $('.llar_notification_login_page').on( 'click', '.close-button', function () {
                            $('.llar_notification_login_page').hide();
                        });

                        $( 'body' ).on('click', function(event) {
                            if (!$(event.target).closest('.llar_notification_login_page').length) {
                                $('.llar_notification_login_page').hide();
                            }
                        });
                    }

                } )(jQuery)
            </script>
			<script data-cfasync="false">function _emitEzConsentEvent(){var customEvent=new CustomEvent("ezConsentEvent",{detail:{ezTcfConsent:window.ezTcfConsent},bubbles:true,cancelable:true,});document.dispatchEvent(customEvent);}
(function(window,document){function _setAllEzConsentTrue(){window.ezTcfConsent.loaded=true;window.ezTcfConsent.store_info=true;window.ezTcfConsent.develop_and_improve_services=true;window.ezTcfConsent.measure_ad_performance=true;window.ezTcfConsent.measure_content_performance=true;window.ezTcfConsent.select_basic_ads=true;window.ezTcfConsent.create_ad_profile=true;window.ezTcfConsent.select_personalized_ads=true;window.ezTcfConsent.create_content_profile=true;window.ezTcfConsent.select_personalized_content=true;window.ezTcfConsent.understand_audiences=true;window.ezTcfConsent.use_limited_data_to_select_content=true;window.ezTcfConsent.select_personalized_content=true;}
function _clearEzConsentCookie(){document.cookie="ezCMPCookieConsent=tcf2;Domain=.com;Path=/;expires=Thu, 01 Jan 1970 00:00:00 GMT";}
_clearEzConsentCookie();if(typeof window.__tcfapi!=="undefined"){window.ezgconsent=false;var amazonHasRun=false;function _ezAllowed(tcdata,purpose){return(tcdata.purpose.consents[purpose]||tcdata.purpose.legitimateInterests[purpose]);}
function _reloadAds(){if(typeof window.ezorefgsl==="function"&&typeof window.ezslots==="object"){if(typeof __ezapsFetchBids=="function"&&amazonHasRun===false){ezapsFetchBids(__ezaps);if(typeof __ezapsVideo!="undefined"){ezapsFetchBids(__ezapsVideo,"video");}
amazonHasRun=true;}
var slots=[];for(var i=0;i<window.ezslots.length;i++){if(window[window.ezslots[i]]&&typeof window[window.ezslots[i]]==="object"){slots.push(window[window.ezslots[i]]);}else{setTimeout(_reloadAds,100);return false;}}
for(var i=0;i<slots.length;i++){window.ezorefgsl(slots[i]);}}else if(!window.ezadtimeoutset){window.ezadtimeoutset=true;setTimeout(_reloadAds,100);}}
function _handleConsentDecision(tcdata){window.ezTcfConsent.loaded=true;if(!tcdata.vendor.consents["347"]&&!tcdata.vendor.legitimateInterests["347"]){window._emitEzConsentEvent();return;}
window.ezTcfConsent.store_info=_ezAllowed(tcdata,"1");window.ezTcfConsent.develop_and_improve_services=_ezAllowed(tcdata,"10");window.ezTcfConsent.measure_content_performance=_ezAllowed(tcdata,"8");window.ezTcfConsent.select_basic_ads=_ezAllowed(tcdata,"2");window.ezTcfConsent.create_ad_profile=_ezAllowed(tcdata,"3");window.ezTcfConsent.select_personalized_ads=_ezAllowed(tcdata,"4");window.ezTcfConsent.create_content_profile=_ezAllowed(tcdata,"5");window.ezTcfConsent.measure_ad_performance=_ezAllowed(tcdata,"7");window.ezTcfConsent.use_limited_data_to_select_content=_ezAllowed(tcdata,"11");window.ezTcfConsent.select_personalized_content=_ezAllowed(tcdata,"6");window.ezTcfConsent.understand_audiences=_ezAllowed(tcdata,"9");window._emitEzConsentEvent();}
function _handleGoogleConsentV2(tcdata){if(!tcdata||!tcdata.purpose||!tcdata.purpose.consents){return;}
var googConsentV2={};if(tcdata.purpose.consents[1]){googConsentV2.ad_storage='granted';googConsentV2.analytics_storage='granted';}
if(tcdata.purpose.consents[3]&&tcdata.purpose.consents[4]){googConsentV2.ad_personalization='granted';}
if(tcdata.purpose.consents[1]&&tcdata.purpose.consents[7]){googConsentV2.ad_user_data='granted';}
if(googConsentV2.analytics_storage=='denied'){gtag('set','url_passthrough',true);}
gtag('consent','update',googConsentV2);}
__tcfapi("addEventListener",2,function(tcdata,success){if(!success||!tcdata){window._emitEzConsentEvent();return;}
if(!tcdata.gdprApplies){_setAllEzConsentTrue();window._emitEzConsentEvent();return;}
if(tcdata.eventStatus==="useractioncomplete"||tcdata.eventStatus==="tcloaded"){if(typeof gtag!='undefined'){_handleGoogleConsentV2(tcdata);}
_handleConsentDecision(tcdata);if(tcdata.purpose.consents["1"]===true&&tcdata.vendor.consents["755"]!==false){window.ezgconsent=true;(adsbygoogle=window.adsbygoogle||[]).pauseAdRequests=0;_reloadAds();}else{_reloadAds();}
if(window.__ezconsent){__ezconsent.setEzoicConsentSettings(ezConsentCategories);}
__tcfapi("removeEventListener",2,function(success){return null;},tcdata.listenerId);if(!(tcdata.purpose.consents["1"]===true&&_ezAllowed(tcdata,"2")&&_ezAllowed(tcdata,"3")&&_ezAllowed(tcdata,"4"))){if(typeof __ez=="object"&&typeof __ez.bit=="object"&&typeof window["_ezaq"]=="object"&&typeof window["_ezaq"]["page_view_id"]=="string"){__ez.bit.Add(window["_ezaq"]["page_view_id"],[new __ezDotData("non_personalized_ads",true),]);}}}});}else{_setAllEzConsentTrue();window._emitEzConsentEvent();}})(window,document);</script>
	
	<p id="a11y-speak-intro-text" class="a11y-speak-intro-text" style="position: absolute;margin: -1px;padding: 0;height: 1px;width: 1px;overflow: hidden;clip: rect(1px, 1px, 1px, 1px);-webkit-clip-path: inset(50%);clip-path: inset(50%);border: 0;word-wrap: normal !important;" hidden="hidden">Notifications</p><div id="a11y-speak-assertive" class="a11y-speak-region" style="position: absolute;margin: -1px;padding: 0;height: 1px;width: 1px;overflow: hidden;clip: rect(1px, 1px, 1px, 1px);-webkit-clip-path: inset(50%);clip-path: inset(50%);border: 0;word-wrap: normal !important;" aria-live="assertive" aria-relevant="additions text" aria-atomic="true"></div><div id="a11y-speak-polite" class="a11y-speak-region" style="position: absolute;margin: -1px;padding: 0;height: 1px;width: 1px;overflow: hidden;clip: rect(1px, 1px, 1px, 1px);-webkit-clip-path: inset(50%);clip-path: inset(50%);border: 0;word-wrap: normal !important;" aria-live="polite" aria-relevant="additions text" aria-atomic="true"></div></body></html>