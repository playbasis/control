<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';

class Push_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tool/utility', 'utility');
        $this->load->library('mongo_db');
    }

    public function getTemplate($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $results = $this->mongo_db->get("playbasis_push_to_client");
        return $results ? $results[0] : null;
    }

    public function getTemplateByName($site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->count("playbasis_push_to_client");
    }

    public function getTemplateIDByName($site_id, $name, $getInfo = false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_push_to_client");

        if($getInfo){
            return $results ? $results[0] : null;
        }else {
            return $results ? $results[0]['_id'] . "" : null;
        }
    }

    public function listTemplatesBySiteId($site_id, $data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );
        $order = 1;
        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        }
        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        return $this->mongo_db->get("playbasis_push_to_client");
    }

    public function getTotalTemplatesBySiteId($site_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        return $this->mongo_db->count("playbasis_push_to_client");
    }

    public function addTemplate($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $dt = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        return $this->mongo_db->insert('playbasis_push_to_client', array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order'] | 1,
            'date_modified' => $dt,
            'date_added' => $dt,
            'name' => $data['name'] | '',
            'body' => $data['body'] | '',
            'deleted' => false,
        ));
    }

    public function editTemplate($template_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get("playbasis_push_to_client");
        if (!$templates) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set("name", $data["name"]);
        $this->mongo_db->set('client_id', new MongoID($data['client_id']));
        $this->mongo_db->set('site_id', new MongoID($data['site_id']));
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('body', $data['body']);
        $this->mongo_db->set("date_modified", new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_push_to_client');
        return true;
    }

    public function deleteTemplate($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get("playbasis_push_to_client");
        if (!$templates) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_push_to_client');
        return true;
    }

    public function increaseSortOrder($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get('playbasis_push_to_client');
        if (!$templates) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set('sort_order', $templates[0]['sort_order'] + 1);
        $this->mongo_db->update('playbasis_push_to_client');
        return true;
    }

    public function decreaseSortOrder($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get('playbasis_push_to_client');
        if (!$templates || $templates[0]['sort_order'] <= 0) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set('sort_order', $templates[0]['sort_order'] - 1);
        $this->mongo_db->update('playbasis_push_to_client');
        return true;
    }

    public function getIosSetup($client_id = null, $site_id = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_push_ios");
        return $results ? $results[0] : null;
    }

    public function updateIos($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $client_id = isset($data['client_id']) && !empty($data['client_id']) ? new MongoId($data['client_id']) : null;
        $site_id = isset($data['site_id']) && !empty($data['site_id']) ? new MongoId($data['site_id']) : null;
        $env = isset($data['env']) && !empty($data['env']) ? $data['env'] : null;
        $d = new MongoDate();
        if ($this->getIosSetup($client_id, $site_id)) {
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->set('env', $data['push-env']);
            $this->mongo_db->set('certificate', $data['push-certificate']);
            $this->mongo_db->set('password', $data['push-password']);
            $this->mongo_db->set('ca', $data['push-ca']);
            $this->mongo_db->set('date_modified', $d);
            $this->mongo_db->update('playbasis_push_ios');
        } else {
            $this->mongo_db->insert('playbasis_push_ios', array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'env' => $data['push-env'],
                'certificate' => $data['push-certificate'],
                'password' => $data['push-password'],
                'ca' => $data['push-ca'],
                'date_modified' => $d,
                'date_added' => $d
            ));
        }
    }

    public function getAndroidSetup($client_id = null, $site_id = null)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_push_android");
        return $results ? $results[0] : null;
    }

    public function updateAndroid($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $client_id = isset($data['client_id']) && !empty($data['client_id']) ? new MongoId($data['client_id']) : null;
        $site_id = isset($data['site_id']) && !empty($data['site_id']) ? new MongoId($data['site_id']) : null;
        $d = new MongoDate();
        if ($this->getAndroidSetup($client_id, $site_id)) {
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->set('api_key', $data['push-key']);
            $this->mongo_db->set('sender_id', $data['push-sender']);
            $this->mongo_db->set('date_modified', $d);
            $this->mongo_db->update('playbasis_push_android');
        } else {
            $this->mongo_db->insert('playbasis_push_android', array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'api_key' => $data['push-key'],
                'sender_id' => $data['push-sender'],
                'date_modified' => $d,
                'date_added' => $d
            ));
        }
    }

    public function getTemplateById($site_id, $template_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', new MongoId($template_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get('playbasis_push_to_client');
        return $results ? $results[0] : null;
    }

    public function initial($data, $type = null)
    {
        $type = strtolower($type);
        switch ($type) {
            case "ios":
                $setup = $this->getIosSetup($data['data']['client_id'], $data['data']['site_id']);
                if (!$setup) {
                    break;
                } // suppress the error for now

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
                $logger = new ApnsPHP_Log_Hidden();
                $push->setLogger($logger);
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
                $message->setCustomProperty('dataInfo', $data['data']);

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
                $setup = $this->getAndroidSetup($data['data']['client_id'], $data['data']['site_id']);
                if (!$setup) {
                    break;
                } // suppress the error for now

                $api_access_key = $setup['api_key'];


                //define( 'API_ACCESS_KEY', 'AIzaSyCeCZPwysyiPnP4A-PWKFiSgz_QbWYPFtE' );
                $registrationIds = array($data['device_token']);
                $msg = array
                (
                    'message' => $data['messages'],
                    'title'   => 'Playbasis API',
                    //'subtitle'    => $data['subtitle'],
                    //'tickerText'  => $data['description'],
                    'badge' => $data['badge_number'],
                    'vibrate' => 1,
                    'sound' => 1,
                    'largeIcon' => 'large_icon',
                    'smallIcon' => 'small_icon',
                    'dataInfo' => $data['data']
                );

                $fields = array
                (
                    'registration_ids' => $registrationIds,
                    'data' => $msg
                );

                $headers = array
                (
                    'Authorization: key=' . $api_access_key,
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                //echo $result;
                break;

            default:
                throw new Exception("Unsupported device type: " . $type);
                break;
        }
    }
}