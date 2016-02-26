<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan_model extends MY_Model
{
    public function getPlan($plan_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_plan");

        return $results ? $results[0] : null;
    }

    public function getPlanById($plan_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', $plan_id);
        $results = $this->mongo_db->get("playbasis_plan");
        return $results ? $results[0] : null;
    }

    public function getPlans($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

        $plans = $this->mongo_db->get("playbasis_plan");

        return $plans;
    }

    public function getAvailablePlans()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->order_by(array('name' => 1));
        $plans = $this->mongo_db->get("playbasis_plan");

        foreach ($plans as $key => $plan) {
            $subscribers = array();
            $this->mongo_db->select(array('client_id'));
            $this->mongo_db->where('plan_id', $plan['_id']);
            $records = $this->mongo_db->get('playbasis_permission');
            // one "client_id" can have several "site_id"s within the same "plan_id", so we do the manual count
            // current version always counts a client regardless of the status of the client
            if ($records) {
                foreach ($records as $record) {
                    $subscribers[$record['client_id']->{'$id'}] = true;
                }
            }
            $number_of_subscribers = count($subscribers);
            $planLimit = !empty($plan['limit_num_client']) ? $plan['limit_num_client'] : null;
            // check if the plan has reached the maximum limit #clients that can subscribe to this plan
            if ($planLimit && $number_of_subscribers >= $planLimit) {
                unset($plans[$key]); // if true, then the plan is not available
            }
        }
        return $plans;
    }

    public function getTotalPlans($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $total = $this->mongo_db->count("playbasis_plan");

        return $total;
    }

    public function getClientByPlan($plan_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('plan_id', new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_permission");

        return $results;
    }

    public function getClientByPlanOnlyClient($plan_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('client_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('plan_id', new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_permission");

        $client_id_list = array();
        foreach ($results as $r) {
            $client_id_list[$r['client_id'] . ""] = $r;
        }
        return $client_id_list;
    }

    public function getFeatures($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

        $feaures_data = $this->mongo_db->get("playbasis_feature");

        return $feaures_data;
    }

    public function getActions($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

        $actions_data = $this->mongo_db->get("playbasis_action");

        return $actions_data;
    }

    public function getJigsaws($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

        $jigsaws_data = $this->mongo_db->get("playbasis_jigsaw");

        return $jigsaws_data;
    }

    public function getRewards($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

        $rewards_data = $this->mongo_db->get("playbasis_reward");

        return $rewards_data;
    }

    public function addPlan($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['sort_order'])) {
            $sort_order = (int)$data['sort_order'];
        } else {
            $sort_order = 0;
        }

        $dinsert = array(
            'name' => $data['name'] | '',
            'description' => $data['description'] | '',
            'price' => intval($data['price']),
            'display' => (bool)$data['display'],
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'status' => (bool)$data['status'],
            'sort_order' => $sort_order
        );
        if (isset($data['limit_num_client']) && !empty($data['limit_num_client'])) {
            $dinsert['limit_num_client'] = new MongoInt32($data['limit_num_client']);
        } else {
            $dinsert['limit_num_client'] = null;
        }

        if (isset($data['feature_data'])) {
            $feature = array();
            foreach ($data['feature_data'] as $feature_value) {
                array_push($feature, new MongoId($feature_value));
            }
            $dinsert['feature_to_plan'] = $feature;
        }
        if (isset($data['action_data'])) {
            $action = array();
            foreach ($data['action_data'] as $action_value) {
                array_push($action, new MongoId($action_value));
            }
            $dinsert['action_to_plan'] = $action;
        }
        if (isset($data['jigsaw_data'])) {
            $jigsaw = array();
            foreach ($data['jigsaw_data'] as $jigsaw_value) {
                array_push($jigsaw, new MongoId($jigsaw_value));
            }
            $dinsert['jigsaw_to_plan'] = $jigsaw;
        }
        if (isset($data['reward_data'])) {
            $reward = array();
            foreach ($data['reward_data'] as $reward_value => $value) {
                $arr_val = array(
                    'reward_id' => new MongoId($value['reward_id']),
                    'limit' => (isset($value['limit']) && $value['limit'] !== '') ? new MongoInt32($value['limit']) : null
                );
                array_push($reward, $arr_val);
            }
            $dinsert['reward_to_plan'] = $reward;
        }
        if (isset($data['limit_noti'])) {
            $limit_noti = array();
            foreach ($data['limit_noti'] as $key => $value) {
                $limit_noti[$key] = ($value['limit'] != null && $value['limit'] !== '' ? intval($value['limit']) : null);
            }
            $dinsert['limit_notifications'] = $limit_noti;
        }
        if (isset($data['limit_others'])) {
            $limit_others = array();
            foreach ($data['limit_others'] as $key => $value) {
                $limit_others[$key] = ($value['limit'] != null && $value['limit'] !== '' ? intval($value['limit']) : null);
            }
            $dinsert['limit_others'] = $limit_others;
        }
        if (isset($data['limit_widget'])) {
            $limit_widget = array();
            foreach ($data['limit_widget'] as $key => $value) {
                $limit_widget[$key] = ($value['limit'] != null && $value['limit'] !== '' ? true : false);
            }
            $dinsert['limit_widget'] = $limit_widget;
        }
        if (isset($data['limit_cms'])) {
            $limit_cms = array();
            foreach ($data['limit_cms'] as $key => $value) {
                $limit_cms[$key] = ($value['limit'] != null && $value['limit'] !== '' ? true : false);
            }
            $dinsert['limit_cms'] = $limit_cms;
        }
        if (isset($data['limit_req'])) {
            $limit_req = array();
            for ($i = 0; $i < sizeof($data['limit_req']); $i++) {
                $item = $data['limit_req'][$i];
                if (!$item['field']) {
                    continue;
                }
                // strip only first path of the api and lowercase
                $item['field'] = strtolower(preg_replace(
                    "/(\w+)\/.*/", '${1}',
                    $item['field']));
                if (substr($item['field'], 0, 1) != "/") {
                    $item['field'] = "/" . $item['field'];
                }
                $limit_req[$item['field']] = ($item['limit'] != null && $item['limit'] !== '' ? intval($item['limit']) : null);
            }
            $dinsert['limit_requests'] = $limit_req;
        }
        return $this->mongo_db->insert('playbasis_plan', $dinsert);
    }

    public function editPlan($plan_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($plan_id));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('price', intval($data['price']));
        $this->mongo_db->set('display', (bool)$data['display']);
        $this->mongo_db->set('limit_num_client',
            !empty($data['limit_num_client']) ? new MongoInt32($data['limit_num_client']) : null);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        if (isset($data['feature_data'])) {
            $feature = array();
            foreach ($data['feature_data'] as $feature_value) {
                array_push($feature, new MongoId($feature_value));
            }
            $this->mongo_db->set('feature_to_plan', $feature);
        } else {
            $feature = array();
            $this->mongo_db->set('feature_to_plan', $feature);
        }
        if (isset($data['action_data'])) {
            $action = array();
            foreach ($data['action_data'] as $action_value) {
                array_push($action, new MongoId($action_value));
            }
            $this->mongo_db->set('action_to_plan', $action);
        } else {
            $action = array();
            $this->mongo_db->set('action_to_plan', $action);
        }
        if (isset($data['jigsaw_data'])) {
            $jigsaw = array();
            foreach ($data['jigsaw_data'] as $jigsaw_value) {
                array_push($jigsaw, new MongoId($jigsaw_value));
            }
            $this->mongo_db->set('jigsaw_to_plan', $jigsaw);
        } else {
            $jigsaw = array();
            $this->mongo_db->set('jigsaw_to_plan', $jigsaw);
        }
        if (isset($data['reward_data'])) {
            $reward = array();
            foreach ($data['reward_data'] as $reward_value => $value) {
                $arr_val = array(
                    'reward_id' => new MongoId($value['reward_id']),
                    'limit' => (isset($value['limit']) && $value['limit'] != '') ? new MongoInt32($value['limit']) : null
                );
                array_push($reward, $arr_val);
            }
            $this->mongo_db->set('reward_to_plan', $reward);
        }
        if (isset($data['limit_noti'])) {
            $limit_noti = array();
            foreach ($data['limit_noti'] as $key => $value) {
                $limit_noti[$key] = ($value['limit'] != null && $value['limit'] !== '' ? intval($value['limit']) : null);
            }
            $this->mongo_db->set('limit_notifications', $limit_noti);
        }
        if (isset($data['limit_others'])) {
            $limit_others = array();
            foreach ($data['limit_others'] as $key => $value) {
                $limit_others[$key] = ($value['limit'] != null && $value['limit'] !== '' ? intval($value['limit']) : null);
            }
            $this->mongo_db->set('limit_others', $limit_others);
        }
        if (isset($data['limit_widget'])) {
            $limit_widget = array();
            foreach ($data['limit_widget'] as $key => $value) {
                $limit_widget[$key] = ($value['limit'] != null && $value['limit'] !== '' ? true : false);
            }
            $this->mongo_db->set('limit_widget', $limit_widget);
        }
        if (isset($data['limit_cms'])) {
            $limit_cms = array();
            foreach ($data['limit_cms'] as $key => $value) {
                $limit_cms[$key] = ($value['limit'] != null && $value['limit'] !== '' ? true : false);
            }
            $this->mongo_db->set('limit_cms', $limit_cms);
        }
        if (isset($data['limit_req'])) {
            $limit_req = array();
            for ($i = 0; $i < sizeof($data['limit_req']); $i++) {
                $item = $data['limit_req'][$i];
                if (!$item['field']) {
                    continue;
                }
                // strip only first path of the api and lowercase
                $item['field'] = strtolower(preg_replace(
                    "/(\w+)\/.*/", '${1}',
                    $item['field']));
                if (substr($item['field'], 0, 1) != "/") {
                    $item['field'] = "/" . $item['field'];
                }
                $limit_req[$item['field']] = ($item['limit'] != null && $item['limit'] !== '' ? intval($item['limit']) : null);
            }
            $this->mongo_db->set('limit_requests', $limit_req);
        }

        $this->mongo_db->update('playbasis_plan');

    }

    public function deletePlan($plan_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($plan_id));
        $this->mongo_db->delete('playbasis_plan');

    }

    public function getPlanID($name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', $name);
        $results = $this->mongo_db->get('playbasis_plan');
        return $results ? $results[0]['_id'] : null;
    }

    public function getPlanByName($name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', $name);
        $results = $this->mongo_db->get('playbasis_plan');
        return $results ? $results[0] : null;
    }

    public function getPlanTrialDays($name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', $name);
        $results = $this->mongo_db->get('playbasis_plan');

        return $results && isset($results[0]['limit_others']['trial']) ? $results[0]['limit_others']['trial'] : null;
    }

    public function getDisplayedPlans()
    {
        $ret = array();
        $plans = $this->listDisplayPlans();
        if ($plans) {
            foreach ($plans as $plan) {
                array_push($ret, $plan['_id']->{'$id'});
            }
        }
        return $ret;
    }

    public function checkPlanExistsByName($plan_name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name'));
        $plan_names = $this->mongo_db->get('playbasis_plan');

        foreach ($plan_names as $plan) {
            if (trim(strtolower($plan['name'])) == strtolower($plan_name)) {
                return true;
            }
        }

        return false;

    }

    /**
     * Return Permission Display Widget by Plan ID
     * @param plan_id string
     * @return array | null
     */
    public function getPlanDisplayWidget($plan_id)
    {
        $this->mongo_db->select(array('limit_widget'));
        $this->mongo_db->where(array(
            '_id' => $plan_id,
        ));
        $res = $this->mongo_db->get('playbasis_plan');
        if ($res) {
            $res = $res[0];
            return isset($res['limit_widget']) ? $res['limit_widget'] : null;
        } else {
            throw new Exception("getPlanLimitById plan_id not found");
        }
    }

    /**
     * Return Permission Display CMS by Plan ID
     * @param plan_id string
     * @return array | null
     */
    public function getPlanDisplayCMS($plan_id)
    {
        $this->mongo_db->select(array('limit_cms'));
        $this->mongo_db->where(array(
            '_id' => $plan_id,
        ));
        $res = $this->mongo_db->get('playbasis_plan');
        if ($res) {
            $res = $res[0];
            return isset($res['limit_cms']) ? $res['limit_cms'] : null;
        } else {
            throw new Exception("getPlanLimitById plan_id not found");
        }
    }

    /**
     * Return Permission limitation by Plan ID
     * in particular type and field
     * e.g. notifications email
     * @param string $plan_id
     * @param string $type "notifications"|"others"|"requests"
     * @param string $field
     * @return array|int|null
     * @throws Exception
     */
    public function getPlanLimitById($plan_id, $type, $field)
    {
        // wrong type
        if ($type != "notifications" && $type != "requests" && $type != "others") {
            throw new Exception("WRONG_TYPE");
        }

        $this->mongo_db->where(array(
            '_id' => $plan_id,
        ));
        $res = $this->mongo_db->get('playbasis_plan');
        if ($res) {
            $res = $res[0];
            $limit = 'limit_' . $type;  // mongodb_field
            if (is_array($field)) {
                $result = array();
                for ($i = 0; $i < sizeof($field); $i++) {  // get multiple limits
                    if (isset($res[$limit]) &&
                        isset($res[$limit][$field[$i]])
                    ) {
                        $result[$field[$i]] = $res[$limit][$field[$i]];
                    } else {
                        $result[$field[$i]] = null;
                    }
                }
                return $result;
            }
            if (isset($res[$limit]) &&
                isset($res[$limit][$field])
            ) {
                return $res[$limit][$field];
            } else { // this plan does not set this limitation
                return null;
            }
        } else {
            throw new Exception("getPlanLimitById plan_id not found");
        }
    }

    public function listDisplayPlans()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('display', true);
        $this->mongo_db->order_by(array('price' => 1));
        $results = $this->mongo_db->get("playbasis_plan");
        return $results;
    }
}

?>
