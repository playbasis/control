<?php

/*
 * Sample bootstrap file.
 */

// Include the composer autoloader
if(!file_exists(__DIR__ .'/vendor/autoload.php')) {
	echo "The 'vendor' folder is missing. You must run 'composer update --no-dev' to resolve application dependencies.\nPlease see the README for more information.\n";
	exit(1);
}


require __DIR__ . '/vendor/autoload.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Invoice;
use PayPal\Api\MerchantInfo;
use PayPal\Api\BillingInfo;
use PayPal\Api\InvoiceItem;
use PayPal\Api\Phone;
use PayPal\Api\Address;
use PayPal\Api\Currency;
use PayPal\Api\PaymentTerm;
use PayPal\Api\ShippingInfo;

class PaypalHelper
{
    /**
     * Helper method for getting an APIContext for all calls
     *
     * @return PayPal\Rest\ApiContext
     */
    public function getApiContext() {

        // ### Api context
        // Use an ApiContext object to authenticate 
        // API calls. The clientId and clientSecret for the 
        // OAuthTokenCredential class can be retrieved from 
        // developer.paypal.com

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                'EBWKjlELKMYqRNQ6sYvFo64FtaRLRR5BdHEESmha49TM',
                'EO422dn3gQLgDbuwqTjzrFgFtaRLRR5BdHEESmha49TM'
            )
        );

        // #### SDK configuration

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file 
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'FINE'
            )
        );

    /*
    // Register the sdk_config.ini file in current directory
    // as the configuration source.
    if(!defined("PP_CONFIG_PATH")) {
        define("PP_CONFIG_PATH", __DIR__);
    }
     */

        return $apiContext;
    }

    public function createInvoice($email='', $item='') { // TODO
        $invoice = new Invoice();
        $invoice
            ->setMerchantInfo(new MerchantInfo())
            ->setBillingInfo(array(new BillingInfo()))
            ->setItems(array(new InvoiceItem()))
            ->setNote("Medical Invoice 16 Jul, 2013 PST") // TODO
            ->setPaymentTerm(new PaymentTerm())
            ->setShippingInfo(new ShippingInfo());

        $invoice->getMerchantInfo() // TODO
            ->setEmail("ammbot@gmail.com")
            ->setFirstName("Dennis")
            ->setLastName("Doctor")
            ->setbusinessName("Medical Professionals, LLC")
            ->setPhone(new Phone())
            ->setAddress(new Address());

        $invoice->getMerchantInfo()->getPhone() // TODO
            ->setCountryCode("001")
            ->setNationalNumber("5032141716");

        $invoice->getMerchantInfo()->getAddress() // TODO
            ->setLine1("1234 Main St.")
            ->setCity("Portland")
            ->setState("OR")
            ->setPostalCode("97217")
            ->setCountryCode("US");

        $billing = $invoice->getBillingInfo(); // TODO
        $billing[0]
            ->setEmail("example@example.com");

        $items = $invoice->getItems(); // TODO
        $items[0]
            ->setName("Sutures")
            ->setQuantity(100)
            ->setUnitPrice(new Currency());

        $items[0]->getUnitPrice() // TODO
            ->setCurrency("USD")
            ->setValue(5);

        $invoice->getPaymentTerm() // TODO DUE DATE
            ->setTermType("NET_45");

        $invoice->getShippingInfo() // TODO
            ->setFirstName("Sally")
            ->setLastName("Patient")
            ->setBusinessName("Not applicable")
            ->setPhone(new Phone())
            ->setAddress(new Address());

        $invoice->getShippingInfo()->getPhone() // TODO
            ->setCountryCode("001")
            ->setNationalNumber("5039871234");

        $invoice->getShippingInfo()->getAddress() // TODO
            ->setLine1("1234 Main St.")
            ->setCity("Portland")
            ->setState("OR")
            ->setPostalCode("97217")
            ->setCountryCode("US");

        print(var_dump($invoice->toArray()));

        try {
            $invoice->create(PaypalHelper::getApiContext());
        } catch (PayPal\Exception\PPConnectionException $ex) {
            echo "Exception: " . $ex->getMessage() . PHP_EOL;
            var_dump($ex->getData());
            exit(1);
        }

    }
}
