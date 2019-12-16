<?php
$url=  "https://".$_SERVER['HTTP_HOST'];

if (strpos($url, 'unnati') == false) {
    exit();
}
// HTTP
define('HTTP_SERVER', $url.'/stores/pos/');
define('HTTP_CATALOG', $url.'/stores/');

// HTTPS
define('HTTPS_SERVER', $url.'/stores/pos/');
define('HTTPS_CATALOG', $url.'/stores/');

// DIR
define('DIR_APPLICATION', '/var/www/html/unnati/stores/pos/');
define('DIR_SYSTEM', '/var/www/html/unnati/stores/system/');
define('DIR_LANGUAGE', '/var/www/html/unnati/stores/pos/language/');
define('DIR_TEMPLATE', '/var/www/html/unnati/stores/pos/view/');
define('DIR_CONFIG', '/var/www/html/unnati/stores/system/config/');
define('DIR_IMAGE', '/var/www/html/unnati/stores/image/');
define('DIR_CACHE', '/var/www/html/unnati/stores/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/unnati/stores/system/download/');
define('DIR_UPLOAD', '/var/www/html/unnati/stores/system/upload/');
define('DIR_LOGS', '/var/www/html/unnati/stores/system/logs/');
define('DIR_MODIFICATION', '/var/www/html/unnati/stores/system/modification/');
define('DIR_CATALOG', '/var/www/html/unnati/stores/catalog/');

// DB
define('DB_DRIVER', 'mongo');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'shop');
define('DB_PASSWORD', 'shopakshamaala');
define('DB_DATABASE', 'openshop');
define('DB_PREFIX', 'oc_');
define('RUPPE_SIGN', '&#x20b9;');
define('RS_SIGN', 'Rs.');
define('please_wait_span_display','<span id="please_wait_span" style="margin-top: 73px;display: none; text-align: center; margin-right: 0px;" class="loading_text">Please wait. Please do not close your browser or click back button ..</span>');
//define('RUPPE_SIGN', '&#x20B9;');
//sms
define('SMS_USERNAME', 'akashamaala126590');
define('SMS_PASSWORD', '11734');
define('SMS_DISPLAYNAME', 'UNNATI');
define('SMS_HOSTNAME', 'http://sms.akshapp.com/ComposeSMS.aspx');

define('OFFICE_ADDRESS', '<strong>Akshamaala Solutions Pvt. Ltd</strong><br />
 C-84/A, 1<sup>st</sup> Floor, Sector-8, Noida, Uttar Pradesh 201301<br/>
 Ph: +91 120-4040160, <u>accounts@unnati.world</u>, <u>www.unnati.world</u> <br />
 PAN No: AAICA9806D<br />
 CIN No: U72200DL2010PTC209266<br />
 GSTN : 09AAICA9806D1ZM ');
