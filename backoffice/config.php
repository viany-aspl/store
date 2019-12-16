<?php
$url=  "http://".$_SERVER['HTTP_HOST'];

if (strpos($url, 'unnati') == false) {
    //exit();
}

// HTTP
define('HTTP_SERVER', $url.'/stores/backoffice/');
define('HTTP_CATALOG', $url.'/stores/');

// HTTPS
define('HTTPS_SERVER', $url.'/stores/backoffice/');
define('HTTPS_CATALOG', $url.'/stores/');

// DIR
define('DIR_APPLICATION', '/var/www/html/stores/backoffice/');
define('DIR_SYSTEM', '/var/www/html/stores/system/');
define('DIR_LANGUAGE', '/var/www/html/stores/backoffice/language/');
define('DIR_TEMPLATE', '/var/www/html/stores/backoffice/view/template/');
define('DIR_CONFIG', '/var/www/html/stores/system/config/');
define('DIR_IMAGE', '/var/www/html/stores/image/');
define('DIR_CACHE', '/var/www/html/stores/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/stores/system/download/');
define('DIR_UPLOAD', '/var/www/html/stores/system/upload/');
define('DIR_LOGS', '/var/www/html/stores/system/logs/');
define('DIR_MODIFICATION', '/var/www/html/stores/system/modification/');
define('DIR_CATALOG', '/var/www/html/stores/catalog/');

// DB
define('DB_DRIVER', 'mongo');
define('DB_HOSTNAME', '192.168.1.111');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'openshop');
define('DB_PREFIX', 'oc_');


//sms
define('SMS_USERNAME', 'akshamaala4');
define('SMS_PASSWORD', 'akshamaala4');
define('SMS_DISPLAYNAME', 'UNNATI');
define('SMS_HOSTNAME', 'https://www.smscountry.com/SMSCwebservice.asp');

define('OFFICE_ADDRESS', '<strong>Akshamaala Solutions Pvt. Ltd</strong><br />
 C-84/A, 1<sup>st</sup> Floor, Sector-8, Noida, Uttar Pradesh 201301<br/>
 Ph: +91 120-4040160, <u>accounts@unnati.world</u>, <u>www.unnati.world</u> <br />
 PAN No: AAICA9806D<br />
 CIN No: U72200DL2010PTC209266<br />
 GSTN : 09AAICA9806D1ZM ');
