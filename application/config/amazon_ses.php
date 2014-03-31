<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the Amazon Simple Email Service library
 *
 * @see ../libraries/Amazon_ses.php
 */
// Amazon credentials
$config['amazon_ses_secret_key'] = 'dKWWcZvO8QnOIge2mWGVhn0vC4JwYD6a5Xbxeuto';
$config['amazon_ses_access_key'] = 'AKIAJHDIP22PG7RAODHA';

// Adresses
$config['amazon_ses_from'] = '';
$config['amazon_ses_from_name'] = '';
$config['amazon_ses_reply_to'] = '';

// Path to certificate to verify SSL connection (i.e. 'certs/cacert.pem') 
$config['amazon_ses_cert_path'] = 'application/certs/cacert.pem';

// Charset to be used, for example UTF-8, ISO-8859-1 or Shift_JIS. The SMTP
// protocol uses 7-bit ASCII by default
$config['amazon_ses_charset'] = 'UTF-8';
