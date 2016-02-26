<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Editor extends CI_Model
{
    //constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->library('memcached_library');
    }

    //get client
    public function getClient($clientId)
    {
//      $sql = "SELECT `client_id` FROM `playbasis_client` WHERE `client_id` = ?";
//
//      $bindData  = array($clientId);
//      $result = $this->db->query($sql,$bindData);
//
//      return $result->result_array();

        // name for memcached
        $sql = "SELECT `client_id` FROM `playbasis_client` WHERE `client_id` = ?";
        $md5name = md5($sql);
        $table = "playbasis_client";

        $results = $this->memcached_library->get('sql_' . $md5name . "." . $table);

        // gotcha i got result
        if ($results) {
            return $results;
        }


        // so if cannot get any result
        $bindData = array($clientId);
        $result = $this->db->query($sql, $bindData);

        $this->memcached_library->add('sql_' . $md5name . "." . $table, $result->result_array());

        return $result->result_array();
    }

    //get site
    public function getSite($siteId)
    {
//      $sql = "SELECT `site_id` FROM `playbasis_client_site` WHERE `site_id` = ?";
//
//      $bindData = array($siteId);
//      $result = $this->db->query($sql,$bindData);
//
//      return $result->result_array();

        // name for memcached
        $sql = "SELECT `site_id` FROM `playbasis_client_site` WHERE `site_id` = ?";
        $md5name = md5($sql);
        $table = "playbasis_client_site";

        $results = $this->memcached_library->get('sql_' . $md5name . "." . $table);

        // gotcha i got result
        if ($results) {
            return $results;
        }


        // so if cannot get any result
        $bindData = array($siteId);
        $result = $this->db->query($sql, $bindData);

        $this->memcached_library->add('sql_' . $md5name . "." . $table, $result->result_array());

        return $result->result_array();
    }


    //get all rules
    public function getRules($siteId)
    {
//      $sql = "SELECT `rule_id`,`rule_name`,`rule_description`,`active_status` FROM `playbasis_rule` WHERE `site_id` = ?";
//
//      $bindData = array($siteId);
//      $result = $this->db->query($sql,$bindData);
//
//      return $result->result_array();

        // name for memcached
        $sql = "SELECT `rule_id`,`rule_name`,`rule_description`,`active_status` FROM `playbasis_rule` WHERE `site_id` = ?";
        $md5name = md5($sql);
        $table = "playbasis_rule";

        $results = $this->memcached_library->get('sql_' . $md5name . "." . $table);

        // gotcha i got result
        if ($results) {
            return $results;
        }

        // so if cannot get any result
        $bindData = array($siteId);
        $result = $this->db->query($sql, $bindData);

        $this->memcached_library->add('sql_' . $md5name . "." . $table, $result->result_array());

        return $result->result_array();
    }

    //get specific rule
    public function getRule($ruleId)
    {
//      $sql = "SELECT `rule_id`,`rule_name`,`rule_description`,`active_status` FROM `playbasis_rule` WHERE  `rule_id` = ?";
//
//      $bindData = array($ruleId);
//      $result = $this->db->query($sql,$bindData);
//
//      return $result->result_array();

        // name for memcached
        $sql = "SELECT `rule_id`,`rule_name`,`rule_description`,`active_status` FROM `playbasis_rule` WHERE  `rule_id` = ?";
        $md5name = md5($sql);
        $table = "playbasis_rule";

        $results = $this->memcached_library->get('sql_' . $md5name . "." . $table);

        // gotcha i got result
        if ($results) {
            return $results;
        }

        // so if cannot get any result
        $bindData = array($ruleId);
        $result = $this->db->query($sql, $bindData);

        $this->memcached_library->add('sql_' . $md5name . "." . $table, $result->result_array());

        return $result->result_array();
    }

    //add new rule
    public function addRule($data)
    {
        $sql = "INSERT INTO `playbasis_rule` SET `client_id` = ? , `site_id` = ? , `rule_name` = ? , `rule_description` = ? , `date_added` = ? , `date_modified` = ?";

        $bindData = array(
            $data['client_id'],
            $data['site_id'],
            $data['rule_name'],
            isset($data['rule_description']) ? $data['rule_description'] : 'no description',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        );

        $result = $this->db->query($sql, $bindData);

        // clear memcached on this table
        $table = "playbasis_rule";
        $this->memcached_library->update_delete($table);

        //get last rule was added
        return $this->getRule($this->db->insert_id());
    }


    //get jigsaw :: game jigsaw
    public function getJigsaw($ruleId)
    {
//      $sql = "SELECT `jigsaw_set` FROM `playbasis_rule` WHERE `rule_id` =?";
//
//      $bindData = array($ruleId);
//      $result = $this->db->query($sql,$bindData);
//
//      return $result->row_array();

        // name for memcached
        $sql = "SELECT `jigsaw_set` FROM `playbasis_rule` WHERE `rule_id` =?";
        $md5name = md5($sql);
        $table = "playbasis_rule";

        $results = $this->memcached_library->get('sql_' . $md5name . "." . $table);

        // gotcha i got result
        if ($results) {
            return $results;
        }

        // so if cannot get any result
        $bindData = array($ruleId);
        $result = $this->db->query($sql, $bindData);

        $this->memcached_library->add('sql_' . $md5name . "." . $table, $result->row_array());

        return $result->row_array();
    }

    //update rule status
    public function updateRuleStatus($ruleId)
    {
        $sql = "UPDATE `playbasis_rule` SET `active_status` = NOT `active_status` WHERE `rule_id` = ?";

        $bindData = array($ruleId);

        // clear memcached on this table
        $table = "playbasis_rule";
        $this->memcached_library->update_delete($table);

        $result = $this->db->query($sql, $bindData);
    }
}