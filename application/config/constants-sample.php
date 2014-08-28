<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('CAPTCHA_PUBLIC_KEY', '6LcPOPgSAAAAAFELsbPGvNeEByjWavQNk1f7ZLSY');
define('CAPTCHA_PRIVATE_KEY', '6LcPOPgSAAAAAIH3-5uY9DFrXpkTiBoTPsWuasGK');

define('S3_IMAGE', 'http://images.pbapp.net/');
define('DIR_IMAGE', 'C:\\Program Files (x86)\\Ampps\\www\\control\\image\\');
//define('DIR_IMAGE', './control/image/');

define('DEFAULT_PLAN_PRICE', 0); // default is free package
define('DEFAULT_PLAN_DISPLAY', false); // default is to not display the plan
define('DEFAULT_TRIAL_DAYS', 0); // default is having no trial period
define('DEFAULT_LIMIT_NUM_PLAYERS', 1000); // default limit number of player for a plan
define('MAX_ALLOWED_TRIAL_DAYS', 90); // this is limited by PayPal
define('PAYMENT_CHANNEL_PAYPAL', 'PayPal');
define('PAYPAL_MERCHANT_ID', 'pechpras-facilitator@playbasis.com');
define('PAYPAL_MODIFY_NEW_SUBSCRIPTION_ONLY', 0);
define('PAYPAL_MODIFY_EITHER_NEW_SUBSCRIPTION_OR_MODIFY', 1);
define('PAYPAL_MODIFY_CURRENT_SUBSCRIPTION_ONLY', 2);
define('PAYPAL_ENV', 'sandbox');
define('PRODUCT_NAME', 'Playbasis API Subscription');
define('PURCHASE_SUBSCRIBE', 0);
define('PURCHASE_UPGRADE', 1);
define('PURCHASE_DOWNGRADE', 2);
define('FOREVER', 100); // number of years our system used for representing an unlimited value (for example, free package has no "date_expire")

/* End of file constants.php */
/* Location: ./application/config/constants.php */