<?php

// HTML CONSTANTS
define("HTML_RESET", "<div class='page_reset'>&nbsp;</div>");

// URL CONSTANTS
define("CLASSES_PATH", "./");
define("VENDORS", "./vendor/");
define("PAGES", "./views/");
define("HEADER", "./static/header.php");
define("FOOTER", "./static/footer.php");
define("STYLES", "./");
define("MAINMENU", "./static/mainmenu.php");
define("IMAGES", "./content/images/");
define("GALLERY", IMAGES . "gallery/");
define("OVERLAY", IMAGES . "overlay/");
define("TITLES", "./titles/");
define("SCRIPTS", "./");

// DATE FORMATS
define("DATE_DISPLAY", "l, jS F Y");
define("DATETIME_DISPLAY", "l, jS F Y - H:i");
define("SHORT_DATE", "Y-m-d");
define("SHORT_DISPLAY_DATE", "d/m/Y");
define("TIME_FORMAT", "H:i:s");
define("TIMEINDEX", "U");
define("FULL_DATETIME", "d-m-Y H:i:s");
define("MYSQL_DATETIME", "Y-m-d H:i:s");

// COOKIE CONSTANTS
define("COOKIE_EXPIRE", -3600);
define("COOKIE_UNIQUE_EXPIRE", 2592000);
define("COOKIE_UNIQUE_NAME", "Unique Visitor");
define("COOKIE_COOKIES_NAME", "Accept Cookies");
define("COOKIE_COOKIES_EXPIRE", 2592000);

// TEXT
define("PAGE_TITLE", "Welcome to Project ODIN");
define("TITLE_SEPARATOR"," | ");

// MYSQL PARAMETERS - test site
//define("MYSQL_USERNAME", "igeekcou_site");
//define("MYSQL_PASSWORD", 'M@riners2021');
//define("MYSQL_DATABASE", "igeekcou_wapw");

// MYSQL PARAMETERS - non WAPW Site
define("MYSQL_USERNAME", "igeekcou_site");
define("MYSQL_PASSWORD", 'M@riners2021');
define("MYSQL_DATABASE", "igeekcou_maindb");

// MYSQL PARAMETERS - Live Site
//define("MYSQL_USERNAME", "weddinga_main");
//define("MYSQL_PASSWORD", 'P!nkL1pstick');
//define("MYSQL_DATABASE", "weddinga_wapw");

// MYSQL PARAMETERS - SHARED
define("MYSQL_HOST", "localhost");
define("MYSQL_PORT", "3306");

// FEATURES ON OR OFF
define("GEOLOCATION", TRUE);
define("GEOFENCING", TRUE); // fence the site so only UK, Europe and US are allowed.  Requires GEOLOCATION set to True
define("ANONYMOUSBROWSING", FALSE); // allows unknown browsers and platforms to view the site.  If set to false, Bots are still allowed.
define("BROWSER_DATA", TRUE); // turns on the collection of browser data.

// EMAIL ADDRESSES
//define("EMAIL_CONTACT", "contact@weddingandpartywishes.co.uk");
define("EMAIL_CONTACT", "info@weddingandpartywishes.co.uk");
//define("EMAIL_CONTACT", "john@westerman.me.uk");
define("EMAIL_INFO", "info@weddingandpartywishes.co.uk");

//GOOGLE CAPTCHA
define("SITE_KEY", "6Lc8mBccAAAAAAS3sMb5QIBcIgwz6eEoIDdp-N_M");
define("SECRET_KEY", "6Lc8mBccAAAAAJoHo0Qz8sQXSDrs3oqtykLeLkRN");
