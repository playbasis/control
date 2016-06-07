<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rule_model extends MY_Model
{
    public function getActionJigsawList($siteId, $clientId)
    {
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'action_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('status', true);
        if ($clientId) {
            $this->mongo_db->where('site_id', $siteId);
            $this->mongo_db->where('client_id', $clientId);
            $results = $this->mongo_db->get("playbasis_action_to_client");
        } else {
            $results = $this->mongo_db->get("playbasis_action");
        }

        $output = array(
            'error' => 1,
            'success' => false,
            'msg' => 'Error , invalid request format or missing parameter'
        );
        $jigsaw_id = $this->findJigsawId('action', 'ACTION');

        try {
            if (count($results) > 0) {
                foreach ($results as &$rowx) {
                    if (!$clientId) // default action use _id instead
                    {
                        $rowx["action_id"] = $rowx["_id"];
                    }
                    $rowx['specific_id'] = $rowx['action_id'] . "";
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
                    $rowx['dataSet'] = $rowx['init_dataset'];
                    $rowx['id'] = $jigsaw_id;
                    $rowx['category'] = 'ACTION';
                    unset($rowx['action_id']);
                    unset($rowx['init_dataset']);
                    unset($rowx['_id']);
                }
                $output = $results;
            }

        } catch (Exception $e) {
            //Exception stuff
        }

        return $output;
    }

    public function getConditionJigsawList($siteId, $clientId)
    {
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'jigsaw_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('category', 'CONDITION');
        if ($clientId) {
            $this->mongo_db->where('site_id', $siteId);
            $this->mongo_db->where('client_id', $clientId);
            $results = $this->mongo_db->get("playbasis_game_jigsaw_to_client");
        } else {
            $results = $this->mongo_db->get("playbasis_jigsaw");
        }

        $output = array(
            'error' => 1,
            'success' => false,
            'msg' => 'Error , invalid request format or missing parameter'
        );

        try {
            if (count($results) > 0) {
                foreach ($results as &$rowx) {
                    $jigsaw_id = strval($rowx['jigsaw_id']);
                    $rowx['id'] = $jigsaw_id;
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
                    $rowx['dataSet'] = $rowx['init_dataset'];
                    $rowx['specific_id'] = $jigsaw_id; // no specific id for condition so using the same id with jigsaw id.
                    $rowx['category'] = 'CONDITION';
                    unset($rowx['jigsaw_id']);
                    unset($rowx['init_dataset']);
                    unset($rowx['_id']);
                }
                $output = $results;
            }

        } catch (Exception $e) {
            //Exception stuff
        }

        return $output;
    }

    public function getConditionGroupJigsawList($siteId, $clientId)
    {
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'jigsaw_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('category', 'CONDITION_GROUP');
        if ($clientId) {
            $this->mongo_db->where('site_id', $siteId);
            $this->mongo_db->where('client_id', $clientId);
            $results = $this->mongo_db->get("playbasis_game_jigsaw_to_client");
        } else {
            $results = $this->mongo_db->get("playbasis_jigsaw");
        }

        $output = array(
            'error' => 1,
            'success' => false,
            'msg' => 'Error , invalid request format or missing parameter'
        );

        try {
            if (count($results) > 0) {
                foreach ($results as &$rowx) {
                    $jigsaw_id = strval($rowx['jigsaw_id']);
                    $rowx['id'] = $jigsaw_id;
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
                    $rowx['dataSet'] = $rowx['init_dataset'];
                    $rowx['specific_id'] = $jigsaw_id; // no specific id for condition so using the same id with jigsaw id.
                    $rowx['category'] = 'CONDITION_GROUP';
                    unset($rowx['jigsaw_id']);
                    unset($rowx['init_dataset']);
                    unset($rowx['_id']);
                }
                $output = $results;
            }

        } catch (Exception $e) {
            //Exception stuff
        }

        return $output;
    }

    public function getRewardJigsawList($siteId, $clientId)
    {
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'reward_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('status', true);
        if ($clientId) {
            $this->mongo_db->where('site_id', $siteId);
            $this->mongo_db->where('client_id', $clientId);
            $ds = $this->mongo_db->get("playbasis_reward_to_client");
        } else {
            $ds = $this->mongo_db->get("playbasis_reward");
        }

        $output = array(
            'error' => 1,
            'success' => false,
            'msg' => 'Error , invalid request format or missing parameter'
        );
        $jigsaw_id = $this->findJigsawId('reward', 'REWARD');

        try {
            if (count($ds) > 0) {
                foreach ($ds as &$rowx) {
                    $rowx['specific_id'] = $rowx['reward_id'] . "";
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
                    $rowx['dataSet'] = isset($rowx['init_dataset']) ? $rowx['init_dataset'] : null;
                    $rowx['id'] = $jigsaw_id;
                    $rowx['category'] = 'REWARD';
                    unset($rowx['reward_id']);
                    unset($rowx['init_dataset']);
                    unset($rowx['_id']);
                }

                // append custom reward
                if ($clientId) {
                    $this->mongo_db->select(array(
                        'jigsaw_id',
                        'name',
                        'description',
                        'sort_order',
                        'icon',
                        'status',
                        'category',
                        'init_dataset'
                    ));
                    $this->mongo_db->where('site_id', new MongoID($siteId));
                    $this->mongo_db->where('client_id', new MongoID($clientId));
                    $this->mongo_db->where('category', 'REWARD');
                    $this->mongo_db->where('status', true);
                    $this->mongo_db->where('name', 'customPointReward');
                    $this->mongo_db->limit(1);
                    $ds2 = $this->mongo_db->get("playbasis_game_jigsaw_to_client");

                    if (count($ds2) > 0) {
                        $ds2[0]['specific_id'] = $ds2[0]['jigsaw_id'] . "";//'';
                        $ds2[0]['jigsaw_category'] = 'REWARD';
                        $ds2[0]['dataSet'] = $ds2[0]['init_dataset'];
                        $ds2[0]['id'] = $ds2[0]['jigsaw_id'] . "";
                        $ds2[0]['category'] = 'REWARD';

                        unset($ds2[0]['jigsaw_id']);
                        unset($ds2[0]['init_dataset']);
                        unset($ds2[0]['_id']);
                        array_push($ds, $ds2[0]);
                    }
                }

                $output = $ds;
            }

        } catch (Exception $e) {
            //Exception stuff
        }

        return array_merge($output, $this->getGoodsJigsawList($siteId, $clientId));
    }

    public function getGroupJigsawList($siteId, $clientId)
    {
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'jigsaw_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('category', 'GROUP');
        if ($clientId) {
            $this->mongo_db->where('site_id', $siteId);
            $this->mongo_db->where('client_id', $clientId);
            $results = $this->mongo_db->get("playbasis_game_jigsaw_to_client");
        } else {
            $results = $this->mongo_db->get("playbasis_jigsaw");
        }

        $output = array(
            'error' => 1,
            'success' => false,
            'msg' => 'Error , invalid request format or missing parameter'
        );

        try {
            if (count($results) > 0) {
                foreach ($results as &$rowx) {
                    $jigsaw_id = strval($rowx['jigsaw_id']);
                    $rowx['id'] = $jigsaw_id;
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
                    $rowx['dataSet'] = $rowx['init_dataset'];
                    $rowx['specific_id'] = $jigsaw_id; // no specific id for condition so using the same id with jigsaw id.
                    $rowx['category'] = 'GROUP';
                    unset($rowx['jigsaw_id']);
                    unset($rowx['init_dataset']);
                    unset($rowx['_id']);
                }
                $output = $results;
            }

        } catch (Exception $e) {
            //Exception stuff
        }

        return $output;
    }

    public function getFeedbackJigsawList($siteId, $clientId, $emailList, $smsList, $pushList, $webhookList=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $output = array();
        if ($this->getFeatureExistByClientId($clientId, 'email') && !empty($emailList)) {
            $type = 'email';
            $output[] = array(
                '_id' => $type,
                'name' => $type,
                'description' => 'Send ' . $type,
                'sort_order' => 10,
                'status' => 1,
                'specific_id' => $type,
                'dataSet' => array(
                    array(
                        'field_type' => 'hidden',
                        'label' => 'feedback_name',
                        'param_name' => 'feedback_name',
                        'placeholder' => 'feedback_name',
                        'sortOrder' => 0,
                        'tooltips' => 'feedback_name',
                        'value' => 'email',
                    ),
                    array(
                        'field_type' => 'select',
                        'label' => 'Template id',
                        'param_name' => 'template_id',
                        'placeholder' => 'Template id',
                        'sortOrder' => 0,
                        'tooltips' => 'which template to use',
                        'value' => 0,
                        'type' => $type,
                    ),
                    array(
                        'field_type' => 'text',
                        'label' => 'Subject',
                        'param_name' => 'subject',
                        'placeholder' => 'Subject',
                        'sortOrder' => 0,
                        'tooltips' => 'subject',
                        'value' => '',
                    ),
                ),
                'id' => $type,
                'category' => 'FEEDBACK',
            );
        }
        if ($this->getFeatureExistByClientId($clientId, 'sms') && !empty($smsList)) {
            $type = 'sms';
            $output[] = array(
                '_id' => $type,
                'name' => $type,
                'description' => 'Send ' . $type,
                'sort_order' => 10,
                'status' => 1,
                'specific_id' => $type,
                'dataSet' => array(
                    array(
                        'field_type' => 'hidden',
                        'label' => 'feedback_name',
                        'param_name' => 'feedback_name',
                        'placeholder' => 'feedback_name',
                        'sortOrder' => 0,
                        'tooltips' => 'feedback_name',
                        'value' => 'sms',
                    ),
                    array(
                        'field_type' => 'select',
                        'label' => 'Template id',
                        'param_name' => 'template_id',
                        'placeholder' => 'Template id',
                        'sortOrder' => 0,
                        'tooltips' => 'which template to use',
                        'value' => 0,
                        'type' => $type,
                    ),
                ),
                'id' => $type,
                'category' => 'FEEDBACK',
            );
        }

        if ($this->getFeatureExistByClientId($clientId, 'push') && !empty($pushList)) {
            $type = 'push';
            $output[] = array(
                '_id' => $type,
                'name' => $type,
                'description' => 'Send ' . $type,
                'sort_order' => 10,
                'status' => 1,
                'specific_id' => $type,
                'dataSet' => array(
                    array(
                        'field_type' => 'hidden',
                        'label' => 'feedback_name',
                        'param_name' => 'feedback_name',
                        'placeholder' => 'feedback_name',
                        'sortOrder' => 0,
                        'tooltips' => 'feedback_name',
                        'value' => 'push',
                    ),
                    array(
                        'field_type' => 'select',
                        'label' => 'Template id',
                        'param_name' => 'template_id',
                        'placeholder' => 'Template id',
                        'sortOrder' => 0,
                        'tooltips' => 'which template to use',
                        'value' => 0,
                        'type' => $type,
                    ),
                ),
                'id' => $type,
                'category' => 'FEEDBACK',
            );
        }

        if ($this->getFeatureExistByClientId($clientId, 'webhook') && !empty($webhookList)) {
            $type = 'webhook';
            $output[] = array(
                '_id' => $type,
                'name' => $type,
                'description' => 'Send ' . $type,
                'sort_order' => 10,
                'status' => 1,
                'specific_id' => $type,
                'dataSet' => array(
                    array(
                        'field_type' => 'hidden',
                        'label' => 'feedback_name',
                        'param_name' => 'feedback_name',
                        'placeholder' => 'feedback_name',
                        'sortOrder' => 0,
                        'tooltips' => 'feedback_name',
                        'value' => 'webhook',
                    ),
                    array(
                        'field_type' => 'select',
                        'label' => 'Webhook template',
                        'param_name' => 'template_id',
                        'placeholder' => 'Template id',
                        'sortOrder' => 0,
                        'tooltips' => 'which template to use',
                        'value' => 0,
                        'type' => $type,
                    ),
                ),
                'id' => $type,
                'category' => 'FEEDBACK',
            );
        }
        return $output;
    }

    public function getGoodsJigsawList($siteId, $clientId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $output = array();
        if ($this->getFeatureExistByClientId($clientId, 'goods')) {
            $type = 'goods';
            $output[] = array(
                '_id' => $type,
                'name' => $type,
                'description' => 'Reward ' . $type,
                'sort_order' => 10,
                'status' => 1,
                'specific_id' => $type,
                'dataSet' => array(
                    array(
                        'param_name' => 'reward_name',
                        'label' => 'Name',
                        'placeholder' => null,
                        'sortOrder' => 0,
                        'field_type' => 'read_only',
                        'value' => 'goods',
                    ),
                    array(
                        'param_name' => 'item_id',
                        'label' => 'Item id',
                        'placeholder' => 'Item id',
                        'sortOrder' => 0,
                        'field_type' => 'collection-goods',
                        'value' => 0,
                    ),
                    array(
                        'param_name' => 'quantity',
                        'label' => 'Quantity',
                        'placeholder' => 'How many ...',
                        'sortOrder' => 0,
                        'field_type' => 'number',
                        'value' => 1,
                    ),
                ),
                'id' => $type,
                'category' => 'REWARD',
            );
        }
        return $output;
    }

    public function findJigsawId($name, $category)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('category', $category);
        $this->mongo_db->where('status', true);
        $this->mongo_db->limit(1);
        $jigsaw = $this->mongo_db->get("playbasis_jigsaw");
        return strval($jigsaw[0]['_id']);
    }

    /* copy over from "feature_model" */
    public function getFeatureExistByClientId($client_id, $link)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('link', $link);
        return $this->mongo_db->count("playbasis_feature_to_client") > 0;
    }

    public function saveRule($input)
    {
        $response = function ($msg) {
            return array("success" => $msg);
        };
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();

        if ($input['rule_id'] == 'undefined') {
            $res = $this->mongo_db->insert('playbasis_rule', array(
                'client_id' => $input['client_id'] ? new MongoID($input['client_id']) : null,
                'site_id' => $input['site_id'] ? new MongoID($input['site_id']) : null,
                'action_id' => new MongoID($input['action_id']),
                'name' => $input['name'],
                'description' => $input['description'],
                'tags' => $input['tags'],
                'jigsaw_set' => $input['jigsaw_set'],
                'active_status' => (bool)$input['active_status'],
                'date_added' => $d,
                'date_modified' => $d
            ));

            if ($res) {
                return $response(true);
            } else {
                return $response(false);
            }
        } else {
            // check that this rule is from template or not
            $rule = $this->getById($input['rule_id']);
            if ($rule) {
                $this->mongo_db->where('_id', new MongoID($input['rule_id']));
                $this->mongo_db->set('client_id', $input['client_id'] ? new MongoID($input['client_id']) : null);
                $this->mongo_db->set('site_id', $input['site_id'] ? new MongoID($input['site_id']) : null);
                $this->mongo_db->set('action_id', new MongoID($input['action_id']));
                $this->mongo_db->set('name', $input['name']);
                $this->mongo_db->set('description', $input['description']);
                $this->mongo_db->set('tags', $input['tags']);
                $this->mongo_db->set('jigsaw_set', $input['jigsaw_set']);
                $this->mongo_db->set('active_status', (bool)$input['active_status']);
                $this->mongo_db->set('date_modified', $d);
                if (!$this->isSameRules($rule, $input)) {
                    $this->mongo_db->unset_field('clone_id');
                }
                if ($this->mongo_db->update('playbasis_rule')) {
                    return $response(true);
                }
            }
            // save process failed
            return $response(false);
        }
    }

    private function listRulesTemplate()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', null);
        $this->mongo_db->where('site_id', null);
        $this->mongo_db->where('active_status', true);
        return $this->mongo_db->get("playbasis_rule");
    }

    private function findIdByTemplateJigsawId($jigsaw_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('jigsaw_id', intval($jigsaw_id));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_jigsaw");
        return $results ? $results[0]['_id'] : null;
    }

    private function findRewardIdByTemplateRewardName($name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('name', $name);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_reward");
        return $results ? $results[0]['_id'] : null;
    }

    public function copyRulesFromTemplate($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();
        $rules = $this->listRulesTemplate();
        if (!$rules) {
            return false;
        }
        foreach ($rules as &$rule) {
            $rule['client_id'] = $client_id;
            $rule['site_id'] = $site_id;
            $rule['date_added'] = $d;
            $rule['date_modified'] = $d;
            foreach ($rule['jigsaw_set'] as &$each) {
                switch ($each['category']) {
                    case 'ACTION':
                        break;
                    case 'CONDITION':
                        $specific_id = '' . $this->findIdByTemplateJigsawId($each['specific_id']);
                        $each['id'] = $specific_id;
                        $each['specific_id'] = $specific_id;
                        $each['config']['condition_id'] = $specific_id;
                        break;
                    case 'REWARD':
                        $reward_id = '' . $this->findRewardIdByTemplateRewardName($each['name']);
                        $each['specific_id'] = $reward_id;
                        $each['config']['reward_id'] = $reward_id;
                        break;
                    case 'GROUP':
                        $specific_id = '' . $this->findIdByTemplateJigsawId($each['specific_id']);
                        $each['id'] = $specific_id;
                        $each['specific_id'] = $specific_id;
                        $each['config']['group_id'] = $specific_id;
                        foreach ($each['dataSet'][0]['value'] as $i => &$element) {
                            $reward_id = '' . $this->findRewardIdByTemplateRewardName($element['name']);
                            $element['specific_id'] = $reward_id;
                            $element['config']['reward_id'] = $reward_id;
                            $each['config']['group_container'][$i]['reward_id'] = $reward_id;
                        }
                        break;
                    case 'CONDITION_GROUP':
                        $specific_id = '' . $this->findIdByTemplateJigsawId($each['specific_id']);
                        $each['id'] = $specific_id;
                        $each['specific_id'] = $specific_id;
                        $each['config']['group_id'] = $specific_id;
                        foreach ($each['dataSet'][0]['value'] as $i => &$element) {
                            $sub_id = '' . $this->findIdByTemplateJigsawId($element['specific_id']);
                            $element['specific_id'] = $sub_id;
                            $element['config']['reward_id'] = $sub_id;
                            $each['config']['condition_group_container'][$i]['condition_id'] = $sub_id;
                        }
                        break;
                    default:
                        break;
                }
            }
            unset($rule['_id']);
        }
        return $this->mongo_db->batch_insert('playbasis_rule', $rules, array("w" => 0, "j" => false));
    }

    /*
     * Clone Rule from Template to Client's rule
     * template rule cannot duplicate in client table
     * but allow if that rule is edited.
     * @param string $rule_id
     * @param string $client_id
     * @param string $site_id
     * @return array
     */
    public function cloneRule($rule_id, $client_id, $site_id)
    {
        $response = function ($msg) {
            return array("success" => $msg);
        };
        try {
            $rule_obj = new MongoID($rule_id);
        } catch (Exception $e) {
            return $response(false);
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));
        // client must not have this template
        $this->mongo_db->limit(1);
        $is_client_used = $this->mongo_db->get_where("playbasis_rule",
            array(
                "clone_id" => $rule_obj,
                "client_id" => $client_id,
                "site_id" => $site_id
            )
        );
        // get template rule
        if (!$is_client_used) {
            $template = $this->getById($rule_obj);
            if ($template) {
                // move _id to clone_id
                $template["clone_id"] = $template["_id"];
                $template["_id"] = new MongoID();
                // save to client
                $template["client_id"] = $client_id;
                $template["site_id"] = $site_id;
                $template["active_status"] = false;
                $template["date_added"] = new MongoDate();
                $template["date_modified"] = new MongoDate();
                if ($this->mongo_db->insert("playbasis_rule", $template)) {
                    return $response(true);
                }
            }
        }
        // clone process not complete
        return $response(false);
    }

    /*
     * get rule by _id
     * @param string id
     * @return array
     */
    public function getById($id)
    {
        try {
            $id = new MongoID($id);
        } catch (Exception $e) {
            return array();
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $result = $this->mongo_db->get_where("playbasis_rule",
            array("_id" => $id));
        if ($result) {
            return $result[0];
        } else {
            return array();
        }
    }

    public function deleteRule($ruleId, $siteId, $clientId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($ruleId));
        $this->mongo_db->where('site_id', $siteId);
        $this->mongo_db->where('client_id', $clientId);
        $res = $this->mongo_db->delete('playbasis_rule');

        if ($res) {
            return array('success' => true, 'message' => $res);
        } else {
            return array('success' => false);
        }
    }

    function changeRuleState($ruleId, $state, $siteId, $clientId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($ruleId));
        $this->mongo_db->where('site_id', $siteId);
        $this->mongo_db->where('client_id', $clientId);
        $this->mongo_db->set('active_status', (bool)$state);
        $res = $this->mongo_db->update('playbasis_rule');

        if ($res) {
            return array('success' => true, 'other' => $res);
        } else {
            return array('success' => false);
        }
    }

    public function getRuleById($siteId, $clientId, $ruleId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('_id', new MongoID($ruleId));
            $this->mongo_db->where('site_id', $siteId);
            $this->mongo_db->where('client_id', $clientId);
            $results = $this->mongo_db->get("playbasis_rule");
            $ds = $this->unserializeRuleSet($results);

            if (count($ds) > 0) {
                $ds[0]['rule_id'] = $ds[0]['_id'] . "";
                $ds[0]['site_id'] = $ds[0]['site_id'] . "";
                $ds[0]['client_id'] = $ds[0]['client_id'] . "";
                $ds[0]['action_id'] = $ds[0]['action_id'] . "";
                $ds[0]['_id'] = $ds[0]['_id'] . "";
                return $ds[0];
            }
        } catch (Exception $e) {
            //Exception stuff
        }

        return false;
    }

    public function getRulesByCombinationId(
        $siteId,
        $clientId,
        $params = array(
            'actionList' => null,
            'actionNameDict' => array(),
            'conditionList' => null,
            'rewardList' => null
        )
    ) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            '_id',
            'client_id',
            'site_id',
            'action_id',
            'name',
            'description',
            'tags',
            'active_status',
            'jigsaw_set',
            'date_added',
            'date_modified'
        ));
        $this->mongo_db->where('site_id', $siteId);
        $this->mongo_db->where('client_id', $clientId);
        $results = $this->mongo_db->get("playbasis_rule");

        $output = array(
            'error' => 1,
            'success' => false,
            'msg' => 'Error , invalid request format or missing parameter'
        );

        try {
            if (count($results) > 0) {
                /* init */
                $rules = array();
                $usage = $this->countUsage($siteId);
                if ($usage) {
                    foreach ($usage as $each) {
                        $rules[$each['_id']['rule_id']->{'$id'}] = $each['n'];
                    }
                }
                /* main process */
                $output = $results;
                foreach ($output as &$value) {
                    $value['rule_id'] = strval($value["_id"]);
                    $value['client_id'] = strval($value["client_id"]);
                    $value['site_id'] = strval($value["site_id"]);
                    $value['action_id'] = strval($value["action_id"]);
                    $value['action_name'] = array_key_exists(strval($value["action_id"]),
                        $params['actionNameDict']) ? $params['actionNameDict'][strval($value["action_id"])] : $this->getActionName($value['jigsaw_set']) . '<span style="color: red">*</span>';
                    $value['usage'] = array_key_exists($value['rule_id'], $rules) ? $rules[$value['rule_id']] : 0;
                    $value['error'] = implode(', ', $this->checkRuleError($value['jigsaw_set'], $params));
                    unset($value['jigsaw_set']);
                    foreach ($value as $k2 => &$v2) {
                        if ($k2 == "date_added") {
                            $value[$k2] = substr($this->datetimeMongotoReadable($value[$k2]), 0, -8);
                        }
                        if ($k2 == "date_modified") {
                            $value[$k2] = $this->datetimeMongotoReadable($value[$k2]);
                        }
                        if ($k2 == "rule_id") {
                            $value[$k2] = $value["_id"] . "";
                        }
                    }//End for : inner
                }//End for : outter
                /*End : Cut time string off*/
            } else {
                $output['error'] = 2;
                $output['msg'] = 'No data';
            }

        } catch (Exception $e) {
            //Exception stuff
        }

        if (isset($output["date_added"])) {
            $this->vsort($output, "date_added");
        }
        return $output;
    }

    public function countUsage($site_id, $from = null, $to = null)
    {
        $this->set_site_mongodb($site_id);
        $date_added = array();
        if ($from) {
            $date_added['$gt'] = $from;
        }
        if ($to) {
            $date_added['$lt'] = $to;
        }
        $default = array('site_id' => $site_id);
        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);
        $results = $this->mongo_db->aggregate('playbasis_rule_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array('_id' => array('rule_id' => '$rule_id'), 'n' => array('$sum' => '$value'))
                ),
            )
        );
        return $results ? $results['result'] : array();
    }

    public function calculateFrequency($site_id, $from = null, $to = null)
    {
        $this->set_site_mongodb($site_id);
        $date_added = array();
        if ($from) {
            $date_added['$gt'] = $from;
        }
        if ($to) {
            $date_added['$lt'] = $to;
        }
        $default = array('action_log_id' => array('$exists' => 1), 'site_id' => $site_id);
        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);
        $results = $this->mongo_db->aggregate('jigsaw_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$project' => array('action_log_id' => 1, 'rule_id' => 1)
                ),
                array(
                    '$group' => array(
                        '_id' => array('action_log_id' => '$action_log_id', 'rule_id' => '$rule_id'),
                        'n' => array('$sum' => 1)
                    )
                ),
            )
        );
        return $results ? $results['result'] : array();
    }

    public function calculateRuleFrequency($rule_id, $n, $from = null, $to = null)
    {
        $date_added = array();
        if ($from) {
            $date_added['$gt'] = $from;
        }
        if ($to) {
            $date_added['$lt'] = $to;
        }
        $default = array('action_log_id' => array('$exists' => 1), 'rule_id' => $rule_id);
        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);
        $results = $this->mongo_db->aggregate('jigsaw_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$project' => array('action_log_id' => 1)
                ),
                array(
                    '$group' => array('_id' => '$action_log_id', 'n' => array('$sum' => 1))
                ),
                array(
                    '$match' => array('n' => array('$gte' => $n))
                ),
            )
        );
        return $results ? $results['result'] : array();
    }

    public function getLastCalculateFrequencyTime()
    {
        $this->mongo_db->select(array('date_added'));
        $this->mongo_db->order_by(array('date_added' => -1));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('jigsaw_log_precomp');
        return $results ? $results[0]['date_added'] : array();
    }

    private function getActionName($jigsaw_set)
    {
        if (is_array($jigsaw_set)) {
            foreach ($jigsaw_set as $each) {
                switch ($each['category']) {
                    case 'ACTION':
                        return $each['name'];
                        break;
                    case 'CONDITION':
                    case 'REWARD':
                    case 'FEEDBACK':
                        break;
                    default:
                        break;
                }
            }
        }
        return null;
    }

    private function checkRuleError($jigsaw_set, $params)
    {
        $actionList = $params['actionList'];
        $actionNameDict = $params['actionNameDict'];
        $conditionList = $params['conditionList'];
        $rewardList = $params['rewardList'];
        $error = array();
        $is_condition = false; // check if the rule is ending with 'condition' element
        $check_reward = false; // check if the rule should be set to have at least one reward
        if (is_array($jigsaw_set)) {
            foreach ($jigsaw_set as $each) {
                switch ($each['category']) {
                    case 'ACTION':
                        $is_condition = false;
                        if (empty($each['specific_id'])) {
                            $error[] = '[action_id] for ' . $each['name'] . ' is missing';
                        } else {
                            if (!$actionList || !in_array($each['specific_id'], $actionList)) {
                                $error[] = 'action [' . $each['name'] . '] is invalid';
                            } //else if ($actionNameDict && (!array_key_exists($each['specific_id'], $actionNameDict)) || $actionNameDict[$each['specific_id']] !== $each['name']) $error[] = 'action-name ['.$each['name'].'] is invalid';
                            else {
                                if (empty($each['config']['action_id'])) {
                                    $error[] = '[action_id] for ' . $each['config']['action_name'] . ' is missing (config)';
                                } else {
                                    if (!$actionList || !in_array($each['config']['action_id'], $actionList)) {
                                        $error[] = 'action [' . $each['config']['action_name'] . '] is invalid (config)';
                                    }
                                }
                            }
                        }
                        //else if ($actionNameDict && (!array_key_exists($each['config']['action_id'], $actionNameDict)) || $actionNameDict[$each['config']['action_id']] !== $each['config']['action_name']) $error[] = 'action-name ['.$each['config']['action_name'].'] is invalid (config)';
                        break;
                    case 'CONDITION':
                        $is_condition = true;
                        if (empty($each['config']['condition_id'])) {
                            $error[] = '[condition_id] for ' . $each['description'] . ' is missing (config)';
                        } else {
                            if (!$conditionList || !in_array($each['config']['condition_id'], $conditionList)) {
                                $error[] = 'condition-config [' . $each['description'] . '] is invalid (config) [' . $each['config']['condition_id'] . ']';
                            } else {
                                if (empty($each['specific_id'])) {
                                    $error[] = '[condition_id] for ' . $each['description'] . ' is missing';
                                } else {
                                    if (!$conditionList || !in_array($each['config']['condition_id'], $conditionList)) {
                                        $error[] = 'condition-config [' . $each['description'] . '] is invalid [' . $each['config']['condition_id'] . ']';
                                    }
                                }
                            }
                        }
                        break;
                    case 'REWARD':
                        $is_condition = false;
                        $check_reward = true;
                        if (empty($each['specific_id'])) {
                            $error[] = '[reward_id] for ' . $each['config']['reward_name'] . ' is missing';
                        } else {
                            if (!$rewardList || !in_array($each['specific_id'], $rewardList)) {
                                $error[] = 'reward [' . $each['config']['reward_name'] . '] is invalid [' . $each['specific_id'] . ']';
                            }
                        }
                        break;
                    case 'FEEDBACK':
                        $is_condition = false;
                        $check_reward = true;
                        break;
                    case 'GROUP':
                        $is_condition = false;
                        $check_reward = true;
                        // check each entry in "GROUP"
                        foreach ($each['dataSet'] as $data) {
                            if ($data['field_type'] == 'group_container') {
                                // recursive call
                                $error = array_merge($error, $this->checkRuleError($data['value'], $params));
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        if ($is_condition) {
            $error[] = 'because one condition has been set at the end of rule, this condition will be ineffective';
        }
        if (!$check_reward) {
            $error[] = 'there is no any reward configured in the rule';
        }
        return $error;
    }

    private function vsort(&$array, $key)
    {
        $res = array();
        $sort = array();
        reset($array);
        foreach ($array as $ii => $va) {
            if (isset($va[$key])) {
                $sort[$ii] = $va[$key];
            }
        }
        asort($sort);
        foreach ($sort as $ii => $va) {
            $res[$ii] = $array[$ii];
        }
        $array = $res;
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }

    private function unserializeRuleSet($dataSet)
    {
        foreach ($dataSet AS &$rowx) {
            $rowx['date_added'] = $this->datetimeMongotoReadable($rowx['date_added']);
            $rowx['date_modified'] = $this->datetimeMongotoReadable($rowx['date_modified']);
        }
        return $dataSet;
    }

    private function isSameRules($rule1, $rule2)
    {
        if ($rule1["client_id"] != $rule2["client_id"] or
            $rule1["site_id"] != $rule2["site_id"] or
            $rule1["action_id"] != $rule2["action_id"] or
            $rule1["name"] != $rule2["name"] or
            $rule1["description"] != $rule2["description"] or
            $rule1["tags"] != $rule2["tags"] or
            $rule1["jigsaw_set"] != $rule2["jigsaw_set"]
        ) {
            return false;
        } else {
            return true;
        }
    }
}
