<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_model extends MY_Model
{
    public function getTemplate($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $results = $this->mongo_db->get("playbasis_sms_to_client");
        return $results ? $results[0] : null;
    }

    public function getTemplateByName($site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->count("playbasis_sms_to_client");
    }

    public function getTemplateIDByName($site_id, $name, $getInfo = false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_sms_to_client");

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
        return $this->mongo_db->get("playbasis_sms_to_client");
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
        return $this->mongo_db->count("playbasis_sms_to_client");
    }

    public function addTemplate($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $dt = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        return $this->mongo_db->insert('playbasis_sms_to_client', array(
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
        $templates = $this->mongo_db->get("playbasis_sms_to_client");
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
        $this->mongo_db->update('playbasis_sms_to_client');
        return true;
    }

    public function deleteTemplate($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get("playbasis_sms_to_client");
        if (!$templates) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_sms_to_client');
        return true;
    }

    public function increaseSortOrder($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get('playbasis_sms_to_client');
        if (!$templates) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set('sort_order', $templates[0]['sort_order'] + 1);
        $this->mongo_db->update('playbasis_sms_to_client');
        return true;
    }

    public function decreaseSortOrder($template_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($template_id));
        $templates = $this->mongo_db->get('playbasis_sms_to_client');
        if (!$templates || $templates[0]['sort_order'] <= 0) {
            return false;
        }

        $this->mongo_db->where('_id', new MongoID($template_id));
        $this->mongo_db->set('sort_order', $templates[0]['sort_order'] - 1);
        $this->mongo_db->update('playbasis_sms_to_client');
        return true;
    }

    public function getSMSClient($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $results = $this->mongo_db->get("playbasis_sms");

        return $results ? $results[0] : null;
    }

    public function getSMSMaster()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', null);
        $results = $this->mongo_db->get("playbasis_sms");

        return $results ? $results[0] : null;
    }

    public function updateSMS($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['client_id'])) {
            if ($this->getSMSClient($data['client_id'])) {
                $this->mongo_db->where('client_id', new MongoID($data['client_id']));
                $this->mongo_db->set('mode', $data['sms-mode']);
                $this->mongo_db->set('account_sid', $data['sms-account_sid']);
                $this->mongo_db->set('auth_token', $data['sms-auth_token']);
                $this->mongo_db->set('number', $data['sms-number']);
                $this->mongo_db->set('name', $data['sms-name']);
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                $this->mongo_db->update('playbasis_sms');
            } else {
                $this->mongo_db->insert('playbasis_sms', array(
                    'client_id' => new MongoID($data['client_id']),
                    'mode' => $data['sms-mode'],
                    'account_sid' => $data['sms-account_sid'],
                    'auth_token' => $data['sms-auth_token'],
                    'number' => $data['sms-number'],
                    'name' => $data['sms-name'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                ));
            }
        } else {
            if ($this->getSMSMaster()) {
                $this->mongo_db->where('client_id', null);
                $this->mongo_db->set('mode', $data['sms-mode']);
                $this->mongo_db->set('account_sid', $data['sms-account_sid']);
                $this->mongo_db->set('auth_token', $data['sms-auth_token']);
                $this->mongo_db->set('number', $data['sms-number']);
                $this->mongo_db->set('name', $data['sms-name']);
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                $this->mongo_db->update('playbasis_sms');
            } else {
                $this->mongo_db->insert('playbasis_sms', array(
                    'client_id' => null,
                    'mode' => $data['sms-mode'],
                    'account_sid' => $data['sms-account_sid'],
                    'auth_token' => $data['sms-auth_token'],
                    'number' => $data['sms-number'],
                    'name' => $data['sms-name'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                ));
            }
        }
    }

    public function log($client_id, $site_id, $type, $from, $to, $message, $response)
    {
        $mongoDate = new MongoDate(time());
        $this->set_site_mongodb($this->session->userdata('site_id'));
        return $this->mongo_db->insert('playbasis_sms_log', array(
            'type' => $type,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'from' => $from,
            'to' => $to,
            'message' => $message,
            'response' => $response,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate,
        ));
    }
}

?>