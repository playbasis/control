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
define('PAYMENT_CHANNEL_STRIPE', 'Stripe');
//define('STRIPE_API_KEY', 'sk_test_8ChxEiUQyzeiN7OgnnFDBBYG'); // Test
define('STRIPE_API_KEY', 'sk_live_5sqDh7hQ5VSGbGqSOPN9aDJa'); // Live
//define('STRIPE_PUBLISHABLE_KEY', 'pk_test_1dekH9esZmjybutm3r76RIhG'); // Test
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_d0EvPVufD6uYONkk9ZLziEfp'); // Live
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
/* https://stripe.com/docs/api#event_types */
define('PLAN_CREATED', 'plan.created');
define('PLAN_UPDATED', 'plan.updated');
define('PLAN_DELETED', 'plan.deleted');
define('CUSTOMER_CREATED', 'customer.created');
define('CUSTOMER_UPDATED', 'customer.updated');
define('CUSTOMER_DELETED', 'customer.deleted');
define('SOURCE_CREATED', 'customer.source.created');
define('SOURCE_UPDATED', 'customer.source.updated');
define('SOURCE_DELETED', 'customer.source.deleted');
define('INVOICE_CREATED', 'invoice.created');
define('INVOICE_UPDATED', 'invoice.updated');
define('INVOICE_PAYMENT_SUCCEEDED', 'invoice.payment_succeeded');
define('INVOICE_PAYMENT_FAILED', 'invoice.payment_failed');
define('CHARGE_SUCCEEDED', 'charge.succeeded');
define('CHARGE_FAILED', 'charge.failed');
define('SUBSCRIPTION_CREATED', 'customer.subscription.created');
define('SUBSCRIPTION_UPDATED', 'customer.subscription.updated');
define('SUBSCRIPTION_DELETED', 'customer.subscription.deleted');
define('SUBSCRIPTION_TRIAL_WILL_END', 'customer.subscription.trial_will_end');
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

define('LITHIUM_USER_AGENT', 'Jakarta Commons-HttpClient/3.1');

define('ACTION_COMPLETE_MISSION', 'complete-mission');
define('ACTION_COMPLETE_QUEST', 'complete-quest');
define('ACTION_COMPLETE_QUIZ', 'complete-quiz');
define('ACTION_REGISTER', 'register');
define('ACTION_LOGIN', 'login');
define('ACTION_LOGOUT', 'logout');
define('ACTION_INVITE', 'invite');
define('ACTION_INVITED', 'invited');

define('EXEC_BACKGROUND', true);

define('GOOGLE_USER_AGENT', 'APIs-Google');
define('STRIPE_USER_AGENT', 'Stripe');

define('DEMO_SITE_ID', '52ea1eac8d8c89401c0000e5');

define('GECKO_API_KEY', '3b28853ec6792fb3cc0e94ad891d1659');
define('GECKO_URL', 'https://push.geckoboard.com/v1/send/');

define('DATE_FREE_ACCOUNT_SHOULD_SETUP_MOBILE', '2015-06-01');

define('PLAYER_AUTH_SESSION_TIMEOUT', 1440); //1440 secs

define('SMS_VERIFICATION_TIMEOUT_IN_SECONDS', 300); //1440 secs
define('SMS_VERIFICATION_CODE_LENGTH', 6);

define('RETURN_LIMIT_FOR_RANK', 20);

define('S3_IMAGE', 'http://elasticbeanstalk-ap-southeast-1-007834438823.s3.amazonaws.com/');
define('S3_CONTENT_FOLDER','user_content/');
define('S3_DATA_FOLDER','data/');
define('DIR_IMAGE', FCPATH.'images/');
define('THUMBNAIL_FOLDER','cache/');
define('POSTFIX_NUMERIC_PARAM','_numeric');

define('MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH', 80);
define('MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT', 80);
define('MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH', 240);
define('MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT', 240);

define('TOKEN_CLIENT_EXPIRE', (3 * 24 * 3600)); // 3 days
define('TOKEN_PLAYER_EXPIRE', (3 * 24 * 3600)); // 3 days

define('DEBUG_KEY', 'playbasisthailand');
/* End of file constants.php */
/* Location: ./application/config/constants.php */