<?php
// HTTP
define('HTTP_SERVER', 'http://localhost/stores/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost/stores/');

// DIR
define('DIR_APPLICATION', '/var/www/html/stores/catalog/');
define('DIR_SYSTEM', '/var/www/html/stores/system/');
define('DIR_LANGUAGE', '/var/www/html/stores/catalog/language/');
define('DIR_TEMPLATE', '/var/www/html/stores/catalog/view/theme/');
define('DIR_CONFIG', '/var/www/html/stores/system/config/');
define('DIR_IMAGE', '/var/www/html/stores/image/');
define('DIR_CACHE', '/var/www/html/stores/system/cache/');
define('DIR_DOWNLOAD', '/var/www/html/stores/system/download/');
define('DIR_UPLOAD', '/var/www/html/stores/system/upload/');
define('DIR_UPLOAD_OP', '/var/www/html/stores/system/upload/output/');
define('DIR_MODIFICATION', '/var/www/html/stores/system/modification/');
define('DIR_LOGS', '/var/www/html/stores/system/logs/');

// DB
define('DB_DRIVER', 'mongo');
define('DB_HOSTNAME', '192.168.1.111');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'openshop');
define('DB_PREFIX', 'oc_');

////////////
define('CONFIG_NEW_USER_STATUS', 0); 
define('RUPPE_SIGN', '&#x20B9;');

//////mail/////////
define('MAIL_TO', 'sumit.kumar@aspl.ind.in');
$mail_cc=array('amit.s@akshamaala.com','ap@aspl.ind.in',"hrishabh@aspl.ind.in");
define('MAIL_CC', serialize(array('amit.s@akshamaala.com','ap@aspl.ind.in')));
define('MAIL_BCC', serialize(array('chetan.singh@akshamaala.com','vipin.kumar@aspl.ind.in')));

//sms
/*
define('SMS_USERNAME', 'akashamaala126590');
define('SMS_PASSWORD', '11734');
define('SMS_DISPLAYNAME', 'UNNATI');
define('SMS_HOSTNAME', 'http://sms.akshapp.com/ComposeSMS.aspx');
*/
define('SMS_USERNAME', 'akshamaala4');
define('SMS_PASSWORD', 'akshamaala4');
define('SMS_DISPLAYNAME', 'UNNATI');
define('SMS_HOSTNAME', 'https://www.smscountry.com/SMSCwebservice.asp');
