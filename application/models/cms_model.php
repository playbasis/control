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

    public function listArticles($data)  // add paging later
    {

        $site = new MongoID($data['site_id']);
        $client = new MongoID($data['client_id']);
        $cms = $this->getCmsInfo($client, $site);
        $info = array(
            'type' => $data['type'],
            'category' => $data['category'],
            'filter[article-category]' => $data['category'],
            'filter[posts_per_page]' => $data['paging'],
            'page' => $data['page']
        );
        if ($cms) {

            $url = $this->config->item('CMS_URL') . $cms['site_slug'] . '/wp-json/posts?type=' . $data['type'] . '&filter[article-category]=' . $data['category'] . '&filter[posts_per_page]=' . $data['paging'] . '&page=' . $data['page'];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt( $ch, CURLOPT_POSTFIELDS, $info);
            //curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            $objects = json_decode($result);
            $response = array();
            foreach ($objects as $object) {
                $temp = array(
                    'id' => $object->ID,
                    'title' => $object->title,
                    'subtitle' => $object->excerpt,
                    'thumbnail' => $object->featured_image,

                );
                array_push($response, $temp);
            }
            return isset($response) != null ? $response : null;
        }
        return null;


    }

    public function getArticleByID($data)  // add paging later
    {

        $site = new MongoID($data['site_id']);
        $client = new MongoID($data['client_id']);
        $cms = $this->getCmsInfo($client, $site);
        if ($cms) {
            $url = $this->config->item('CMS_URL') . $cms['site_slug'] . '/wp-json/posts/' . $data['id'];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt( $ch, CURLOPT_POSTFIELDS, $info);
            //curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            $object = json_decode($result);
            return isset($object) != null ? $object : null;
        }
        return null;


    }

    public function getCmsInfo($client_id, $site_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_cms_site");
        return $results[0] != null ? $results[0] : null;

    }
}