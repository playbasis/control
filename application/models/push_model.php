<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Bangkok');
class Push_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tool/utility', 'utility');
        $this->load->library('mongo_db');
    }

    public function initial($data,$type=null)
    {
        $type = strtolower($type);
        switch ($type) {
            case "ios":
                $setup = $this->getIosSetup();
                if (!$setup) break; // suppress the error for now

                $f_cert = tmpfile();
                $f_ca = tmpfile();

                $environment = $setup['env'] == 'prod' ? ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION : ApnsPHP_Abstract::ENVIRONMENT_SANDBOX;
                $certificate = $this->utility->var2file($f_cert, $setup['certificate']);
                $password = $setup['password'];
                $ca = $this->utility->var2file($f_ca, $setup['ca']);

                $push = new ApnsPHP_Push($environment, $certificate);

                // Instantiate a new ApnsPHP_Push object
                /*$push = new ApnsPHP_Push(
                    ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
                    APPPATH.'libraries/ApnsPHP/Certificates/apple_push_notification_production.pem'
                );
                */
                /*
                $push = new ApnsPHP_Push(
                    ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
                    APPPATH.'libraries/ApnsPHP/Certificates/push_development.pem'
                );
        /*      */
                /*$push = new ApnsPHP_Push(
                    ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
                    APPPATH.'libraries/ApnsPHP/Certificates/push_production.pem'
                );*/

                // Set the Provider Certificate passphrase
                //$push->setProviderCertificatePassphrase('playbasis');
                $push->setProviderCertificatePassphrase($password);
                // Set the Root Certificate Autority to verify the Apple remote peer
                //$push->setRootCertificationAuthority(APPPATH.'libraries/ApnsPHP/Certificates/Entrust_Root_Certification_Authority.pem');
                $push->setRootCertificationAuthority($ca);
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

                fclose($f_cert);
                fclose($f_ca);

                break;

            case "android":
                $setup = $this->getAndroidSetup();
                if (!$setup) break; // suppress the error for now

                $api_access_key = $setup['api_key'];


                //define( 'API_ACCESS_KEY', 'AIzaSyCeCZPwysyiPnP4A-PWKFiSgz_QbWYPFtE' );
                $registrationIds = $data['device_token'];
                $msg = array
                (
                    'message' 	=> $data['messages'],
                    //'title'		=> $data['title'],
                    //'subtitle'	=> $data['subtitle'],
                    //'tickerText'	=> $data['description'],
                    'badge' => $data['badge_number'],
                    'vibrate'	=> 1,
                    'sound'		=> 1,
                    'largeIcon'	=> 'large_icon',
                    'smallIcon'	=> 'small_icon',
                    'dataInfo', $data['data']
                );

                $fields = array
                (
                    'registration_ids' 	=> $registrationIds,
                    'data'			=> $msg
                );

                $headers = array
                (
                    'Authorization: key=' . $api_access_key,
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                $result = curl_exec($ch );
                curl_close( $ch );
                //echo $result;
                break;

            default:
                throw new Exception("Unsupported device type: ".$type);
                break;
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

    public function getIosSetup($client_id=null, $site_id=null) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('client_id', $client_id);
        $results = $this->mongo_db->get("playbasis_push_ios");
        return $results ? $results[0] : null;
    }

    public function getAndroidSetup($client_id=null, $site_id=null)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('client_id', $client_id);
        $results = $this->mongo_db->get("playbasis_push_android");
        return $results ? $results[0] : null;
    }

    public function getTemplateById($site_id, $template_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', new MongoId($template_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get('playbasis_push_to_client');
        return $results ? $results[0] : null;
    }

    public function listDevice($pb_player_id)
    {
        $this->mongo_db->select(null);
        $this->mongo_db->where(array(
            'pb_player_id' => new MongoId($pb_player_id),
        ));
        return $this->mongo_db->get('playbasis_player_device');
    }
}