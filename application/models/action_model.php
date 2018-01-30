<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function action_model_index_id($obj)
{
    return $obj['_id'];
}

class Action_model extends MY_Model
{
    public function getAction($action_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($action_id));
        $results = $this->mongo_db->get("playbasis_action");

        return $results ? $results[0] : null;
    }

    public function getActions($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
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
        // $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_action");

        return $results;
    }

    public function getTotalActions()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $results = $this->mongo_db->count("playbasis_action");

        return $results;
    }

    public function getActionSiteInfo($action_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('action_id', new MongoID($action_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $results = $this->mongo_db->get("playbasis_action_to_client");

        return $results ? $results[0] : null;
    }

    public function getActionsClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'action_id',
            'name',
            'description',
            'icon',
            'color',
            'sort_order',
            'status',
            'date_added',
            'date_modified'
        ));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
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

        $results = $this->mongo_db->get("playbasis_action_to_client");

        $actions = array();
        $tmp = array();
        foreach ($results as $result) {
            if (!in_array($result['action_id'], $tmp)) {
                $a = $result;
                $actions[] = $a;
                $tmp[] = $result['action_id'];
            }
        }

        return $actions;
    }

    //dupicate function just count on getActionClient
    /*public function getTotalActionClient($data) {

        $this->mongo_db->select(array('action_id','name','description','icon','color','sort_order','status'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".preg_quote(utf8_strtolower($data['filter_name']))."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->get("playbasis_action_to_client");

        $actions = array();
        $tmp = array();
        foreach ($results as $result) {
            if (!in_array($result['action_id'], $tmp)) {
                $a = $result;
                $actions[] = $a;
                $tmp[] = $result['action_id'];
            }
        }

        return count($actions);
    }*/

    public function getActionsSite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'action_id',
            'name',
            'description',
            'icon',
            'color',
            'sort_order',
            'status',
            'date_added',
            'date_modified'
        ));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
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

        return $this->mongo_db->get("playbasis_action_to_client");
    }

    public function getTotalActionsSite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        return $this->mongo_db->count("playbasis_action_to_client");
    }

    public function getTotalActionReport($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('cl_player_id', $data['username']);
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (isset($data['action_id']) && !empty($data['action_id'])) {
            $this->mongo_db->where_in('action_id', $data['action_id']);
        }

        $results = $this->mongo_db->count("playbasis_validated_action_log");

        return $results;

    }

    public function getActionReport($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('cl_player_id', $data['username']);
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime($data['date_start'])),
                                                       '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        if (isset($data['action_id']) && !empty($data['action_id'])) {
            $this->mongo_db->where_in('action_id', $data['action_id']);
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
        $results = $this->mongo_db->get("playbasis_validated_action_log");

        return $results;

    }

    public function getAllIcons()
    {

        $handle = fopen(base_url('stylesheet/custom/font-awesome.css'), 'r');
        $all_icons = array();

        if ($handle) {
            while (($line = fgets($handle)) != false) {

                $temp = explode(":before", $line);
                if (count($temp) > 1) {
                    $all_icons[] = substr($temp[0], 1);
                }
            }
        } else {
            echo "File of the font-awesome.css not found";
        }

        sort($all_icons);
        return $all_icons;
    }

    public function addAction($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $date_modified = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $init_dataset = $this->processActionDataSet(isset($data['init_dataset']) ? $data['init_dataset'] : null);

        $data_insert = array(
            'name' => utf8_strtolower($data['name']),
            'description' => $data['description'],
            'icon' => $data['icon'],
            'color' => $data['color'],
            'sort_order' => (int)$data['sort_order'],
            'status' => (bool)$data['status'],
            'init_dataset' => $init_dataset,
            'date_added' => $date_added,
            'date_modified' => $date_modified
        );

        return $this->mongo_db->insert('playbasis_action', $data_insert);

    }

    public function addActionToClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $date_modified = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $init_dataset = $this->processActionDataSet(isset($data['init_dataset']) ? $data['init_dataset'] : null);

        $data_insert = array(
            'action_id' => new MongoID($data['action_id']),
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'name' => utf8_strtolower($data['name']),
            'description' => $data['description'],
            'icon' => $data['icon'],
            'color' => $data['color'],
            'init_dataset' => $init_dataset,
            'sort_order' => (int)$data['sort_order'],
            'status' => (bool)$data['status'],
            'date_added' => $date_added,
            'date_modified' => $date_modified,
            'is_custom' => true
        );

        return $this->mongo_db->insert('playbasis_action_to_client', $data_insert);
    }

    public function delete($action_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($action_id));
        $this->mongo_db->delete('playbasis_action');

    }

    public function deleteActionClient($action_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($action_id));
        $this->mongo_db->delete('playbasis_action_to_client');
    }

    public function editAction($action_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($action_id));

        if (isset($data['name']) && !is_null($data['name'])) {
            $this->mongo_db->set('name', utf8_strtolower($data['name']));
        }

        if (isset($data['description']) && !is_null($data['description'])) {
            $this->mongo_db->set('description', $data['description']);
        }

        if (isset($data['sort_order']) && !is_null($data['sort_order'])) {
            $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        }

        if (isset($data['status']) && !is_null($data['status'])) {
            $this->mongo_db->set('status', (bool)$data['status']);
        }

        if (isset($data['icon']) && !is_null($data['icon'])) {
            $this->mongo_db->set('icon', $data['icon']);
        }

        if (isset($data['color']) && !is_null($data['color'])) {
            $this->mongo_db->set('color', $data['color']);
        }

        $temp = $this->processActionDataSet($data['init_dataset']);

        $this->mongo_db->set('init_dataset', $temp);

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        return $this->mongo_db->update('playbasis_action');

    }

    public function editActionToClient($action_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('action_id', new MongoID($action_id));

        if (isset($data['client_id']) && !is_null($data['client_id'])) {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        }

        if (isset($data['name']) && !is_null($data['name'])) {
            $this->mongo_db->set('name', utf8_strtolower($data['name']));
        }

        if (isset($data['description']) && !is_null($data['description'])) {
            $this->mongo_db->set('description', $data['description']);
        }

        if (isset($data['sort_order']) && !is_null($data['sort_order'])) {
            $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        }

        if (isset($data['status']) && !is_null($data['status'])) {
            $this->mongo_db->set('status', (bool)$data['status']);
        }

        if (isset($data['icon']) && !is_null($data['icon'])) {
            $this->mongo_db->set('icon', $data['icon']);
        }

        if (isset($data['color']) && !is_null($data['color'])) {
            $this->mongo_db->set('color', $data['color']);
        }

        $temp = $this->processActionDataSet($data['init_dataset']);

        $this->mongo_db->set('init_dataset', $temp);

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        if (isset($data['client_id']) && !is_null($data['client_id'])) {
            $update = $this->mongo_db->update('playbasis_action_to_client');
        } else {
            $update = $this->mongo_db->update_all('playbasis_action_to_client');
        }


        // update rule engine //
        $this->mongo_db->where(array('jigsaw_set.specific_id' => $action_id));
        if (isset($data['name']) && !is_null($data['name'])) {
            $this->mongo_db->set(array('jigsaw_set.$.name' => utf8_strtolower($data['name'])));
            $this->mongo_db->set(array('jigsaw_set.$.config.action_name' => utf8_strtolower($data['name'])));
        }
        if (isset($data['description']) && !is_null($data['description'])) {
            $this->mongo_db->set(array('jigsaw_set.$.description' => utf8_strtolower($data['description'])));
        }
        $this->mongo_db->update_all('playbasis_rule');
        // end update rule engine //

        return $update;
    }

    public function checkActionExists($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', utf8_strtolower($data['name']));
        $result = $this->mongo_db->get('playbasis_action');

        return $result ? $result[0] : null;
    }

    public function checkActionClientExists($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', utf8_strtolower($data['name']));
        $this->mongo_db->where('site_id', $this->session->userdata('site_id'));
        $result = $this->mongo_db->get('playbasis_action_to_client');

        return $result ? $result[0] : null;
    }

    public function increaseOrderByOne($action_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($action_id));
        $theAction = $this->mongo_db->get('playbasis_action');

        $currentSort = $theAction[0]['sort_order'];

        $newSort = $currentSort + 1;

        $this->mongo_db->where('_id', new MongoID($action_id));
        $this->mongo_db->set('sort_order', $newSort);
        $this->mongo_db->update('playbasis_action');

    }


    public function increaseOrderByOneClient($action_id, $client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('action_id', new MongoId($action_id));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $theAction = $this->mongo_db->get('playbasis_action_to_client');

        $currentSort = $theAction[0]['sort_order'];

        $newSort = $currentSort + 1;

        $this->mongo_db->where('action_id', new MongoID($action_id));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->set('sort_order', $newSort);
        $this->mongo_db->update('playbasis_action_to_client');

    }

    public function decreaseOrderByOne($action_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($action_id));
        $theAction = $this->mongo_db->get('playbasis_action');

        $currentSort = $theAction[0]['sort_order'];

        if ($currentSort != 0) {
            $newSort = $currentSort - 1;

            $this->mongo_db->where('_id', new MongoID($action_id));
            $this->mongo_db->set('sort_order', $newSort);
            $this->mongo_db->update('playbasis_action');
        }
    }

    public function decreaseOrderByOneClient($action_id, $client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('action_id', new MongoId($action_id));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $theAction = $this->mongo_db->get('playbasis_action_to_client');

        $currentSort = $theAction[0]['sort_order'];

        if ($currentSort != 0) {
            $newSort = $currentSort - 1;

            $this->mongo_db->where('action_id', new MongoID($action_id));
            $this->mongo_db->where('client_id', new MongoId($client_id));
            $this->mongo_db->set('sort_order', $newSort);
            $this->mongo_db->update('playbasis_action_to_client');
        }
    }

    public function getActionsForDownload($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['username']) && $data['username'] != null) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->where('username', $regex);
            $player_info = $this->mongo_db->get('playbasis_player');
            $player_id = ($player_info) ? $player_info[0]['_id'] : null;
        }


        if (isset($data['site_id']) && $data['site_id'] != null) {
            $this->mongo_db->where('site_id', $data['site_id']);

        }

        if (isset($data['client_id']) && $data['client_id'] != null) {
            $this->mongo_db->where('client_id', $data['client_id']);

        }

        if (isset($player_id) && $player_id != null) {
            $this->mongo_db->where('pb_player_id', $player_id);
            echo $data['client_id'];
        }

        if (isset($data['action_id']) && $data['action_id'] != null) {
            $this->mongo_db->where('action_id', $data['action_id']);
        }

        $results = $this->mongo_db->get('playbasis_action_log');

        return $results;

    }

    /*
     * Get all actions from playbasis_action
     * @param bool $includeDisable
     * @return array
     */
    public function getAllActions($includeDisable = false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if ($includeDisable) {
            $this->mongo_db->where('status', true);
        }
        return $this->mongo_db->get("playbasis_action");
    }

    private function processActionDataSet($init_dataset)
    {
        if (isset($init_dataset) && !is_null($init_dataset)) {
            foreach ($init_dataset as $key => $data_set) {
                $temp[$key] = (object)array(
                    'param_name' => $data_set['param_name'],
                    'label' => isset($data_set['label']) ? $data_set['label'] : '',
                    'placeholder' => '',
                    'sortOrder' => $key,
                    'field_type' => 'text',
                    'value' => '',
                    'required' => isset($data_set['required']) ? $data_set['required'] : false
                );
            }
        } else {
            $temp[0] = (object)array(
                'param_name' => 'url',
                'label' => 'URL or filter String',
                'placeholder' => '',
                'sortOrder' => 0,
                'field_type' => 'text',
                'value' => '',
                'required' => false
            );
        }
        return $temp;
    }

    public function listActions($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name', 'icon'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        $result = $this->mongo_db->get('playbasis_action_to_client');
        if (!$result) {
            $result = array();
        }
        return $result;
    }

    public function actionLogPerAction($data, $action_name, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $action_id = $this->findAction(array_merge($data, array('action_name' => $action_name)));
        if (!$action_id) {
            return array();
        }
        $match = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'action_id' => $action_id,
        );
        if (($from || $to) && !isset($match['date_added'])) {
            $match['date_added'] = array();
        }
        if ($from) {
            $match['date_added']['$gte'] = new MongoDate(strtotime($from . ' 00:00:00'));
        }
        if ($to) {
            $match['date_added']['$lte'] = new MongoDate(strtotime($to . ' 23:59:59'));
        }
        $_result = $this->mongo_db->aggregate('playbasis_player_dau', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => '$date_added', 'value' => array('$sum' => '$count'))
            ),
        ));
        $_result = $_result ? $_result['result'] : array();
        $result = array();
        if (is_array($_result)) {
            foreach ($_result as $key => $value) {
                array_push($result, array('_id' => date('Y-m-d', $value['_id']->sec), 'value' => $value['value']));
            }
        }
        usort($result, 'cmp_id');
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 'SKIP'));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 'SKIP'));
        }
        return $result;
    }

    public function findAction($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('action_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => strtolower($data['action_name'])
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_action_to_client');
        return $result ? $result[0]['action_id'] : array();
    }
}

function cmp_id($a, $b)
{
    if ($a['_id'] == $b['_id']) {
        return 0;
    }
    return ($a['_id'] < $b['_id']) ? -1 : 1;
}

?>
