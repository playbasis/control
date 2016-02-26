<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Widget_model extends MY_Model
{
    public function getWidgetSocials($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $results = $this->mongo_db->get("playbasis_socials");

        return $results;
    }

    public function getWidgetSocialsClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        $results = $this->mongo_db->get("playbasis_socials");

        return $results;
    }

    public function getWidgetSocialsSite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        $results = $this->mongo_db->get("playbasis_socials");

        return $results;
    }

    public function updateWidgetSocials($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('provider', $data['provider']);
        $check = $this->mongo_db->get("playbasis_socials");

        $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $date_modified = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        if ($check) {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('provider', utf8_strtolower($data['provider']));

            $this->mongo_db->set('date_added', $date_added);

            $this->mongo_db->set('date_modified', $date_modified);

            if (isset($data['key']) && !is_null($data['key'])) {
                $this->mongo_db->set('key', trim($data['key']));
            }

            if (isset($data['secret']) && !is_null($data['secret'])) {
                $this->mongo_db->set('secret', trim($data['secret']));
            }

            if (isset($data['sort_order']) && !is_null($data['sort_order'])) {
                $this->mongo_db->set('sort_order', trim($data['sort_order']));
            }

            if (isset($data['status']) && !is_null($data['status'])) {
                $this->mongo_db->set('status', $data['status'] === 'true' ? true : false);
            }

            if (isset($data['callback']) && !is_null($data['callback'])) {
                $this->mongo_db->set('callback', trim($data['callback']));
            }

            return $this->mongo_db->update('playbasis_socials');
        } else {
            $data_insert = array(
                'client_id' => new MongoID($data['client_id']),
                'site_id' => new MongoID($data['site_id']),
                'provider' => utf8_strtolower($data['provider']),
                'key' => trim($data['key']),
                'secret' => trim($data['secret']),
                'secret' => trim($data['secret']),
                'callback' => trim($data['callback']),
                'status' => $data['status'] === 'true' ? true : false
            );

            return $this->mongo_db->insert('playbasis_socials', $data_insert);
        }

    }

}

?>