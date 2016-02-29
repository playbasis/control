<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push_model extends MY_Model
{
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
            $this->mongo_db->where('env', $data['push-env']);
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
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
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
}