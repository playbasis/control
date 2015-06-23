<?php

date_default_timezone_set('Asia/Bangkok');
class Push_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function initial($data)
    {


        // Instantiate a new ApnsPHP_Push object
        /*$push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
            APPPATH.'libraries/ApnsPHP/Certificates/apple_push_notification_production.pem'
        );


        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
            APPPATH.'libraries/ApnsPHP/Certificates/push_development.pem',
        );*/
        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            APPPATH.'libraries/ApnsPHP/Certificates/push_production.pem'
        );
        // Set the Provider Certificate passphrase
                $push->setProviderCertificatePassphrase('playbasis');
        // Set the Root Certificate Autority to verify the Apple remote peer
                $push->setRootCertificationAuthority(APPPATH.'libraries/ApnsPHP/Certificates/Entrust_Root_Certification_Authority.pem');
        // Connect to the Apple Push Notification Service
                $push->connect();
        // Instantiate a new Message with a single recipient
                $message = new ApnsPHP_Message($data['device_token']);

        // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
        // over a ApnsPHP_Message object retrieved with the getErrors() message.
                $message->setCustomIdentifier("Playbasis-Notification");

        // Set badge icon to "3"
                $message->setBadge($data['badge_number']);

        // Set a simple welcome text
                $message->setText($data['messages']);

        // Play the default sound
                $message->setSound();

        // Set a custom property
                //$message->setCustomProperty('acme2', array('bang', 'whiz'));
                $message->setCustomProperty('DataInfo', $data['data']);

        // Set another custom property
                //$message->setCustomProperty('acme3', array('bing', 'bong'));

        // Set the expiry value to 30 seconds
                $message->setExpiry(30);

        // Add the message to the message queue
                $push->add($message);

        // Send all messages in the message queue
                $push->send();

        // Disconnect from the Apple Push Notification Service
                $push->disconnect();

        // Examine the error message container
                $aErrorQueue = $push->getErrors();
                if (!empty($aErrorQueue)) {
                    var_dump($aErrorQueue);

                }
    }

    public function server($data)
    {

        $server = new ApnsPHP_Push_Server(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            APPPATH.'libraries/ApnsPHP/Certificates/push_production.pem'
        );

        // $push->setProviderCertificatePassphrase('test');
            $server->setProviderCertificatePassphrase('playbasis');

        // Set the Root Certificate Autority to verify the Apple remote peer
            $server->setRootCertificationAuthority(APPPATH.'libraries/ApnsPHP/Certificates/Entrust_Root_Certification_Authority.pem');

        // Set the number of concurrent processes
            $server->setProcesses(2);

        // Starts the server forking the new processes
            $server->start();

        // Main loop...
        $i = 1;
        while ($server->run()) {

            // Check the error queue
            $aErrorQueue = $server->getErrors();
            if (!empty($aErrorQueue)) {
                // Do somethings with this error messages...
                var_dump($aErrorQueue);
            }

            // Send 10 messages
            if ($i <= 10) {
                // Instantiate a new Message with a single recipient
                $message = new ApnsPHP_Message($this.$data['device_token']);
                $message->setCustomIdentifier("Playbasis-Notification-Production");
                $message->setText($this.$data['messages']);
                // Set badge icon to "i"
                $message->setBadge($i);
                $message->setCustomProperty('DataInfo', $data['data']);

                // Add the message to the message queue
                $server->add($message);

                $i++;
            }

            // Sleep a little...
            usleep(200000);
        }
    }
}