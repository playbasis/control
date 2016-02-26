<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CMS_model extends MY_Model
{

    public function createCMS($data)
    {
        $site_name = strtolower($data['site_name']);
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('site_name', $site_name);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_cms_site");

        if ($results) {
            $value = $results[0];
            if ($value['site_id'] != $data["site_id"]) {

                if ($value['site_name'] == $site_name) {
                    $site_name = $site_name . "-1";
                } else {
                    $temp = str_split($site_name, '-');
                    $number = $temp[count($temp) - 1];
                    $number++;
                    $site_name = $site_name . "-" . $number;
                }

            }

        } else {
            $site_name = str_replace(' ', '-', $site_name);
        }


        $info = array(
            'role' => 'editor',
            'site_slug' => $site_name,
            'site_name' => $data['site_name'],
            'site_id' => $data['site_id'],
            'client_id' => $data['client_id'],
            'admin_name' => $data['user_name'],
            'admin_id' => $data['user_id'],
            'admin_email' => $data['user_email'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
        );


        $url = 'https://cms.pbapp.net/create-site';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $object = json_decode($result);

        if ($object->success) {
            $this->set_site_mongodb($data['site_id']);
            $response = $object->response;
            $data_insert = array(
                'client_id' => $data['client_id'],
                'site_id' => $data['site_id'],
                'site_slug' => $site_name,
                'site_name' => $data['site_name'],
                'cms_site_id' => $response->cms_site_id,
                'cms_admin_id' => $response->cms_admin_id,
                'user_admin_id' => $data['user_id'],
                'date_created' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
            );
            $this->mongo_db->insert('playbasis_cms_site', $data_insert);
        }

    }

    public function listCmsByClientId($client_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('client_id', $client_id);
        $results = $this->mongo_db->get("playbasis_cms_site");
        return $results;
    }

    public function getCmsInfo($client_id, $site_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_cms_site");
        return isset($results[0]) != null ? $results[0] : null;

    }

    public function getCmsBySiteSlug($site_slug)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->where('site_slug', $site_slug);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_cms_site");
        return isset($results[0]) != null ? $results[0] : null;
    }

    public function updateUserPermission($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $data_insert = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'site_slug' => $data['site_slag'],
            'user_id' => $data['user_id'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'date_created' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
        );
        $this->mongo_db->insert('playbasis_cms_user', $data_insert);

    }

    public function getUserRole($client_id, $site_id, $user_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'user_id' => $user_id
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_cms_user");
        return isset($results[0]) != null ? $results[0] : null;

    }

    public function getTotalUserBySite($client_id, $site_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
        ));
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

        $user_data = $this->mongo_db->count("playbasis_cms_user");

        return $user_data;
    }

    public function getUserBySite($filter)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where(array(
            'client_id' => $filter['client_id'],
            'site_id' => $filter['site_id']
        ));
        $results = $this->mongo_db->get("playbasis_cms_user");
        return $results != null ? $results : null;

    }


}

?>