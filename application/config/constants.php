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

define('EMAIL_DEBUG_MODE', true);

define('FREE_PLAN', '5428f2df998040b0458b45f2'); // free plan
define('DEFAULT_PLAN_PRICE', 0); // default is free package
define('DEFAULT_TRIAL_DAYS', 0); // default is having no trial period
define('PAYMENT_CHANNEL_PAYPAL', 'PayPal');
define('PAYPAL_ENV', '');
define('PAYPAL_IPN_VERIFIED', 'VERIFIED');
define('PAYPAL_IPN_INVALID', 'INVALID');
/* https://www.paypal.com/cgi-bin/webscr?cmd=p/acc/ipn-subscriptions-outside */
define('PAYPAL_TXN_TYPE_SUBSCR_SIGNUP', 'subscr_signup');
define('PAYPAL_TXN_TYPE_SUBSCR_CANCEL', 'subscr_cancel');
define('PAYPAL_TXN_TYPE_SUBSCR_MODIFY', 'subscr_modify');
define('PAYPAL_TXN_TYPE_SUBSCR_FAILED', 'subscr_failed');
define('PAYPAL_TXN_TYPE_SUBSCR_PAYMNT', 'subscr_payment');
define('PAYPAL_TXN_TYPE_SUBSCR_EOT', 'subscr_eot');
/* https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=xpt/Help/popup/StatusTypes */
define('PAYPAL_PAYMENT_STATUS_COMPLETED', 'Completed'); // Money has been successfully sent to the recipient
define('PAYPAL_PAYMENT_STATUS_CANCELED', 'Canceled'); // The sender canceled this payment
define('PAYPAL_PAYMENT_STATUS_DENIED', 'Denied'); // The recipient chose not to accept this payment
define('PAYPAL_PAYMENT_STATUS_HELD', 'Held'); // Money is being temporarily held. The sender may be disputing this payment, or the payment may be under review by PayPal
define('PAYPAL_PAYMENT_STATUS_PENDING', 'Pending'); // This payment is being processed. Allow up to 4 days for it to complete
define('PAYPAL_PAYMENT_STATUS_RETURNED', 'Returned'); // Money was returned to the sender because the payment was unclaimed for 30 days
define('PAYPAL_PAYMENT_STATUS_UNCLAIMED', 'Unclaimed'); // The recipient hasn't yet accepted this payment
define('GRACE_PERIOD_IN_DAYS', 5);

define('EMAIL_FROM', 'no-reply@playbasis.com');
define('EMAIL_TYPE_USER', 'user');
define('EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS', 'notifyInactiveClients');
define('EMAIL_TYPE_NOTIFY_FREE_ACTIVE_CLIENTS', 'notifyFreeActiveClientsToSubscribe');
define('EMAIL_TYPE_NOTIFY_NEAR_LIMIT_USAGE', 'notifyNearLimitUsage');
define('EMAIL_TYPE_REMIND_TO_SETUP_SUBSCRIPTION', 'remindClientsToSetupSubscription');
define('EMAIL_TYPE_REMIND_END_OF_TRIAL_PERIOD', 'remindClientsEndOfTrialPeriod');
define('EMAIL_TYPE_NOTIFY_API_ACCESS_SHUTDOWN_PERIOD', 'notifyClientsShutdownAPI');
define('EMAIL_TYPE_CLIENT_REGISTRATION', 'listClientRegistration');
define('EMAIL_TYPE_NOTIFY_CLIENT_SETUP_MOBILE', 'notifyClientsToSetupMobile');
define('EMAIL_TYPE_REPORT', 'report');
define('EMAIL_BCC_PLAYBASIS_EMAIL', 'pascal@playbasis.com');

define('SMS_TYPE_REDEEM_GOODS', 'redeemGoods');

define('CACHE_ADAPTER', 'file');
//define('CACHE_ADAPTER', 'memcached');
define('CACHE_KEY_VERSION', 'version-api');
define('CACHE_TTL_IN_SEC', 10*60);

define('FULLCONTACT_API', 'https://api.fullcontact.com');
define('FULLCONTACT_API_KEY', '8f10cefa2030457a');
define('FULLCONTACT_RATE_LIMIT', 1); // per sec
define('FULLCONTACT_CALLBACK_URL', 'https://api.pbapp.net/notification/%s');
define('FULLCONTACT_REQUEST_OK', 200);
define('FULLCONTACT_REQUEST_WEBHOOK_ACCEPTED', 202);
define('FULLCONTACT_USER_AGENT', 'FullContact');

define('JIVE_USER_AGENT', 'Jive SBS');

define('COMPLETE_MISSION_ACTION', 'complete-mission');
define('COMPLETE_QUEST_ACTION', 'complete-quest');
define('COMPLETE_QUIZ_ACTION', 'complete-quiz');

define('DEMO_SITE_ID', '52ea1eac8d8c89401c0000e5');
/* End of file constants.php */
/* Location: ./application/config/constants.php */