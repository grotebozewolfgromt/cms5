var LOREM_IPSUM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
var sURLPrivacyPolicy = 'https://www.dexxterclark.com/privacy-policy?nocookie=1';
var sURLContact = 'https://www.dexxterclark.com/business-inquiries';

// obtain cookieconsent plugin
var cc = initCookieConsent();

// run plugin with config object
cc.run({
    current_lang: 'en',
    autoclear_cookies: true,                    // default: false
    cookie_name: 'cookieconsent',             // default: 'cc_cookie'
    cookie_expiration: 365,                     // default: 182
    page_scripts: true,                         // default: false
    force_consent: true,                        // default: false

    // auto_language: null,                     // default: null; could also be 'browser' or 'document'
    // autorun: true,                           // default: true
    // delay: 0,                                // default: 0
    // hide_from_bots: false,                   // default: false
    // remove_cookie_tables: false              // default: false
    // cookie_domain: location.hostname,        // default: current domain
    // cookie_path: '/',                        // default: root
    // cookie_same_site: 'Lax',
    // use_rfc_cookie: false,                   // default: false
    // revision: 0,                             // default: 0

    gui_options: {
        consent_modal: {
            layout: 'cloud',                    // box,cloud,bar
            position: 'bottom center',          // bottom,middle,top + left,right,center
            transition: 'slide'                 // zoom,slide
        },
        settings_modal: {
            layout: 'bar',                      // box,bar
            position: 'left',                   // right,left (available only if bar layout selected)
            transition: 'slide'                 // zoom,slide
        }
    },

    onFirstAction: function(){
        // console.log('onFirstAction fired');
    },

    onAccept: function (cookie) {
        // console.log('onAccept fired!')
    },

    onChange: function (cookie, changed_preferences) {
        // console.log('onChange fired!');

        // If analytics category is disabled => disable google analytics
        if (!cc.allowedCategory('analytics')) {
            typeof gtag === 'function' && gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    },

    languages: {
        'en': {
            consent_modal: {
                title: 'This website uses cookies',
                description: 'We use cookies for security, analytics and supporting our business model.<br>We use data and cookies for personalised advertising via Google Ads and mailinglist marketing popup messages via Mailerlite.<br><br>By clicking “Accept Cookies”, you agree to storing cookies on your device.<br><a href="'+sURLPrivacyPolicy+'" class="cc-link">Privacy policy</a>',
                primary_btn: {
                    text: 'Accept Cookies',
                    role: 'accept_all'      //'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Preferences',
                    role: 'settings'       //'settings' or 'accept_necessary'
                },
                revision_message: '<br><br> Dear user, terms and conditions have changed since the last time you visisted!'
            },
            settings_modal: {
                title: 'Cookie settings',
                save_settings_btn: 'Save current selection',
                accept_all_btn: 'Accept all',
                reject_all_btn: 'Reject all',
                close_btn_label: 'Close',
                cookie_table_headers: [
                    {col1: 'Name'},
                    {col2: 'Domain'},
                    {col3: 'Expiration'}
                ],
                blocks: [
                    {
                        title: 'Cookie usage',
                        description: 'We use cookies for security, analytics and supporting our business model.<br><a href="'+sURLPrivacyPolicy+'" class="cc-link">Privacy Policy</a>.'
                    }, {
                        title: 'Strictly necessary cookies',
                        description: 'Some elements on our website require cookies to function properly.<br><br>We use Google\'s Recaptcha to prevent automated requests by bots.<br>This is used to prevent large amount of spam emails.<br>We use Sellfy to sell our products',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true  //cookie categories with readonly=true are all treated as "necessary cookies"
                        },
                        cookie_table: [
                            {
                                col1: 'PHPSESSID',
                                col2: 'this site',
                                col3: 'Temporary session cookie.<br>Session identifyer to ensure persistency between page loads to improve user experience.<br>For example temporily storing form values so the user doesn\'t have to fill out all values again on an input error'                             
                            },
                            {
                                col1: 'cms5sTXXX',
                                col2: 'this site',
                                col3: '2 months.<br>Identifying user credentials'                             
                            },                
                            {
                                col1: 'sid',
                                col2: 'this site',
                                col3: '2 months.<br>Remembering a site preference of a user'                             
                            },                                           
                            {
                                col1: 'cookieconsent',
                                col2: 'this site',
                                col3: '1 year.<br>Remembers the cookie settings of this dialog: which cookies do you allow?'                             
                            },
                            {
                                col1: '_GRECAPTCHA',
                                col2: 'google.com',
                                col3: '1 month.<br>This cookie is set by Google to help protect websites from spam and abuse. ',
                            },
                        ]                        


                    }, {
                        title: 'Analytics & Performance cookies',
                        description: 'We use Google Analytics to track site performance and user behavior, so we can optimize user experience and track marketing performance to support our business model.<br>A description of <a href="https://policies.google.com/technologies/cookies" target="_blank">Google cookies you can find here</a>',
                        toggle: {
                            value: 'analytics',
                            enabled: false,
                            readonly: false
                        },
                        cookie_table: [
                            {
                                col1: '_dc_gtm_UA-xxxxxxxx',
                                col2: 'this site via google.com',
                                col3: 'Google Tag Manager.<br>This cookie is associated with sites using Google Tag Manager to load other scripts and code into a page. The end of the name is a unique number which is also an identifier for an associated Google Analytics account.'
                            },                            
                            {
                                col1: '_ga',
                                col2: 'this site via google.com',
                                col3: '1 year. This cookie is associated with Google Analytics and is used to distinguish users. '
                            },
                            {
                                col1: '_gid',
                                col2: 'this site via google.com',
                                col3: '1 day. This cookie is associated with Google Analytics and is used to distinguish users'
                            },
                            {
                                col1: '_gat',
                                col2: 'this site via google.com',
                                col3: '1 year. This cookie is associated with Google Analytics and used to throttle the request rate - limiting the collection of data on high traffic sites. '
                            },                            
                            {
                                col1: '_gat_UA- ',
                                col2: 'this site via google.com',
                                col3: 'This Google Analytics cookie is set to collect information how visitors use the website. '
                            }
                        ]
                    }, {
                        title: 'Targeting & Advertising cookies',
                        description: 'To support our business model, we use Google Ads for advertisements on this site and Mailerlite to collect email names and email addresses.<br>More information on <a href="https://support.google.com/admanager/answer/2839090?hl=en" target="_blank">google\'s cookie policy</a><br>A description of <a href="https://policies.google.com/technologies/cookies" target="_blank">Google cookies you can find here</a>.<br>More info on <a href="https://www.mailerlite.com/legal/cookie-policy">Mailerlite\'s cookie policy</a>',
                        toggle: {
                            value: 'targeting',
                            enabled: false,
                            readonly: false,
                            reload: 'on_disable'            // New option in v2.4, check readme.md
                        },
                        cookie_table: [
                            {
                                col1: 'mailerlite:webform:shown:x',               
                                col2: 'mailerlite.com',
                                col3: 'Persistent. This cookie is set by MailerLite and is used to determine if website visitor has seen pop-up or not, and when to show the pop-up again regarding your behavior settings. '
                            },
                            {
                                col1: 'IDE',               
                                col2: 'doubleclick.net',
                                col3: 'This cookie is set by DoubleClick and is used for serving targeted advertisements that are relevant to you across the web.'
                            },                            
                            {
                                col1: 'NID',               
                                col2: 'google',
                                col3: 'This Google cookie is used to help optimize ads on Google properties, like Google Search.'
                            },
                            {
                                col1: '__Secure_3PSIDCC',               
                                col2: 'google',
                                col3: 'This Google cookie is used to build groups of users to target advertising.'
                            },
                            {
                                col1: '__Secure-3PAPISID',               
                                col2: 'google',
                                col3: 'This Google cookie is used to build groups of users to target advertising.'
                            },
                            {
                                col1: '__Secure-3PSID',               
                                col2: 'google',
                                col3: 'This Google cookie is used to build groups of users to target advertising.'
                            },
                            {
                                col1: 'SIDCC',               
                                col2: 'google',
                                col3: 'This Google cookie is used as security measure to protect users data from unauthorised access.'
                            },
                            {
                                col1: 'SSID',               
                                col2: 'google',
                                col3: 'This Google cookie stores the preferences and other information of the user.'
                            },
                            {
                                col1: 'HSID',               
                                col2: 'google',
                                col3: 'This Google cookie contains digitally signed and encrypted records of a user’s Google Account ID and most recent sign-in time. The combination with SID cookie allows to block many types of attack, such as attempts to steal the content of forms submitted in Google services.'
                            },
                            {
                                col1: 'SID',               
                                col2: 'google',
                                col3: 'This Google cookie contains digitally signed and encrypted records of a user’s Google Account ID and most recent sign-in time. The combination with HSID cookie allows to block many types of attack, such as attempts to steal the content of forms submitted in Google services.'
                            },
                            {
                                col1: 'APISID',               
                                col2: 'google',
                                col3: 'This Google cookie is used to personalize ads on websites based on recent searches and interactions.'
                            },
                            {
                                col1: 'SAPISID',               
                                col2: 'google',
                                col3: 'This Google cookie is used to collect visitor information for videos hosted by YouTube.'
                            },
                            {
                                col1: '1P_JAR',               
                                col2: 'google',
                                col3: 'This Google cookie is used to display personalized advertisements on Google sites, based on recent searches and previous interactions.'
                            },
                            {
                                col1: 'OTZ',               
                                col2: 'google',
                                col3: 'This cookie is used by Google Analytics to provide an aggregate analysis of website visitors.'
                            },
                            {
                                col1: 'VISITOR_INFO1_LIVE',               
                                col2: 'youtube',
                                col3: 'This cookie is set by YouTube and is used as a unique identifier to track viewing of videos.'
                            },
                            {
                                col1: 'GPS',               
                                col2: 'youtube',
                                col3: 'This cookie is set by YouTube and is used to store location data.'
                            },
                            {
                                col1: 'YSC',               
                                col2: 'youtube',
                                col3: 'This cookie is set by YouTube and is used to register a unique ID to keep statistics of what videos from YouTube the user has seen.'
                            },
                            {
                                col1: 'CONSENT',               
                                col2: 'youtube',
                                col3: 'This cookie is set by Youtube for marketing purposes.'
                            },
                            {
                                col1: '_uetvid',               
                                col2: 'google tag manager',
                                col3: 'This Google Tag Manager cookie is used to track visitors on multiple websites, in order to present relevant advertisement based on the visitors preferences.'
                            },
                            {
                                col1: '_gcl_au',               
                                col2: 'google tag manager',
                                col3: 'This cookie is set by Google Tag Manager to store and track conversions.'
                            }
                        ]
                        
                    }, {
                        title: 'More information',
                        description: 'Please ' + ' <a class="cc-link" href="'+sURLContact+'" target="_blank">Contact me</a> when information is false, incomplete or unclear.',
                    }
                ]
            }
        }
    }
});