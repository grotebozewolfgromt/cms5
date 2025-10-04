# IDEAS


## speed optimization ideas
* try catch statements verminderen
* TCSV platslaan en interne array gebruiken
* TModel: inbouwen loadFromSession(id) om data sneller te krijgen
* translations cachen in sessie
* localisation object cachen in sessie
* localisation settings laden uit json file (=sneller??)
* language files als json opslaan en laden (=sneller??)
* in FormInputAbstract de validators is een TObjectlist ipv array
* websites worden geladen in bootstrap_cms_auth. (alleen nodig voor selectbox aan linkerkant scherm)

## security ideas
* lijst tonen van authentication logs (uit database) met o.a. login attempts en failed-login-attempts etc, met direct de mogelijkheid om ip te blocken, user te blocken. mogelijkheid om te filteren op failed-login-attempts

## functionality ideas
* change record order dmv dragging-and-dropping
* color as database type

## install ideas
* SET SQL_MODE='ALLOW_INVALID_DATES' either on install, or check on install (SELECT @@GLOBAL.sql_mode;)
* uncaught mysqli exception afvangen
* connection failed bij niet bestaande sql server NOG VOOR INSTALLATIE!
* instructies in eerste scherm over: pas config file aan
