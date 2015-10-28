<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CMS_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('mongo_db');
    }
    public function listArticles($type,$category,$site_id,$client_id)  // add paging later
    {

        $site = new MongoID($site_id);
        $client = new MongoID($client_id);
        $cms = $this->getCmsInfo($client,$site);
        $info = array(
            'type'=> $type,
            'filter[article-category]' => $category
        );
        if($cms)
        {
            $url = 'https://cms.pbapp.net/'.$cms['site_name'].'/wp-json/posts?type='.$type.'&filter[article-category]='.$category;
            $ch = curl_init( $url );
            curl_setopt( $ch, CURLOPT_URL, $url);
            //curl_setopt( $ch, CURLOPT_POSTFIELDS, $info);
            //curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt( $ch, CURLOPT_HEADER, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec( $ch );
            $object = json_decode($result);
            if($object->success)
            {
                $response = $object->response;
                return $response;
            }
        }
        return null;


    }
    public function getCmsInfo($client_id,$site_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_cms_site");
        return $results[0] != null ? $results[0] : null;

    }
}