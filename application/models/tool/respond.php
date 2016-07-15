<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Respond extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => CACHE_ADAPTER));
    }

    public function setRespond($data = array())
    {
        $respondData = array();
        $respondData['success'] = true;
        $respondData['error_code'] = '0000';
        $respondData['message'] = 'Success';
        $respondData['response'] = $data;
        $respondData['timestamp'] = (int)time();
        $respondData['time'] = date('r e');
        $version = $this->cache->get(CACHE_KEY_VERSION);
        if ($version === false) {
            $str = @file_get_contents('./pom.xml');
            $xml = new SimpleXMLElement($str);
            $version = (string)$xml->version;
            $obj = explode('-', $version);
            $version = $obj[0];
            $this->cache->save(CACHE_KEY_VERSION, $version, CACHE_TTL_IN_SEC);
        }
        $respondData['version'] = $version;
        return $respondData;
    }
}

?>