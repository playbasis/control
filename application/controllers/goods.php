<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Goods extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('goods_model');
        $this->load->model('player_model');
        $this->load->model('store_org_model');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function index_get($goodsId = 0)
    {
        /* process group */
        $results = $this->goods_model->getGroupsAggregate($this->validToken['site_id']);
        $ids = array();
        $group_name = array();
        $org_id_list = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }
        /* find my goods */
        $player_id = $this->input->get('player_id');
        if ($player_id !== false) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
            ));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
            $myGoods = $this->player_model->getGoods($pb_player_id, $this->site_id);
            $m = $this->mapByGoodsId($myGoods);

            $org_list = $this->store_org_model->retrieveNodeByPBPlayerID($this->client_id, $this->site_id,
                $pb_player_id);

            if (is_array($org_list)) {
                foreach ($org_list as $node) {
                    $org_info = $this->store_org_model->getOrgInfoOfNode($this->client_id, $this->site_id,
                        $node['node_id']);
                    $a = array((string)$org_info[0]['organize'] => isset($node['roles']) ? $node['roles'] : array());
                    $org_id_list = array_merge($org_id_list, $a);
                }
            }

        }
        /* main */
        if ($goodsId) // given specified goods_id
        {
            $goods['goods'] = $this->goods_model->getGoods(array_merge($this->validToken, array(
                'goods_id' => new MongoId($goodsId)
            )));

            // return an error if
            // 1. good id is set organize and player_id is not in that organize
            // Or 2. organize role is set and player role is not matched
            if (isset($goods['goods']['organize_id'])) {
                if ((!array_key_exists((string)$goods['goods']['organize_id'], $org_id_list)
                    || ((isset($goods['goods']['organize_role']) && $goods['goods']['organize_role'] != "")
                        && !array_key_exists($goods['goods']['organize_role'],
                            $org_id_list[(string)$goods['goods']['organize_id']])))
                ) {
                    $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
                }
                $org = $this->store_org_model->retrieveOrganizeById($this->client_id, $this->site_id,
                    $goods['goods']['organize_id']);
                $goods['goods']['organize'] = $org['name'];
                unset($goods['goods']['organize_id']);
            }

            $goods['goods']['is_group'] = array_key_exists('group', $goods['goods']);
            unset($goods['goods']['code']);
            if ($goods['goods']['is_group']) {
                $group = $goods['goods']['group'];
                $goods['goods']['name'] = $goods['goods']['group'];
                foreach ($group_name as $each) {
                    if ($each['group'] == $group) {
                        $goods['goods']['quantity'] = $each['quantity'];
                        break;
                    }
                }
                if ($player_id !== false) {
                    $goods['amount'] = isset($m[$group]) ? $m[$group]['amount'] : 0;
                    if(isset($m[$group]['code']) && $goods['amount'] > 0) $goods['goods']['code'] = $m[$group]['code'];
                }
            } else {
                if ($player_id !== false) {
                    $goods['amount'] = isset($m[$goodsId]) ? $m[$goodsId]['amount'] : 0;
                    if(isset($m[$goodsId]['code'])  && $goods['amount'] > 0) $goods['goods']['code'] = $m[$goodsId]['code'];
                }
            }

            $this->response($this->resp->setRespond($goods), 200);
        } else // list all
        {
            $data = $this->validToken;

            if ($this->input->get('tags')){
                $data['tags'] = explode(',', $this->input->get('tags'));
            }

            if ($this->input->get('selected_field')){
                $data['selected_field'] = explode(',', $this->input->get('selected_field'));
                foreach ($data['selected_field'] as $index => $field){
                    if(!$field){
                        unset($data['selected_field'][$index]);
                    }
                }
                $data['selected_field'] = array_values($data['selected_field']);
            }

            if ($this->input->get('active_filter') == "true"){
                if(!$this->input->get('date_start') && ! $this->input->get('date_end')){
                    $data['date_start'] = new MongoDate();
                }
            }

            if ($this->input->get('date_start')){
                $data['date_start'] = new MongoDate(strtotime($this->input->get('date_start')));
            }

            if ($this->input->get('date_end')){
                if (strpos($this->input->get('date_end'), ':') !== false) {
                    $data['date_end'] = new MongoDate(strtotime($this->input->get('date_end')));
                } else {
                    $data['date_end'] = new MongoDate(strtotime($this->input->get('date_end') . " 23:59:59"));
                }
            }

            $data['offset'] = ($this->input->get('offset')) ? $this->input->get('offset') : 0;
            $data['limit'] = ($this->input->get('limit')) ? $this->input->get('limit') : null;
            if ($data['limit'] > 500) {
                $data['limit'] = 500;
            }

            $goodsList['goods_list'] = $this->goods_model->getAllGoods($data, $ids);
            if (is_array($goodsList['goods_list'])) {
                foreach ($goodsList['goods_list'] as $key => &$goods) {
                    $goods_id = $goods['_id'];
                    $is_group = array_key_exists('group', $goods);
                    unset($goods['code']);
                    if ($is_group) {
                        $goods['is_group'] = true;
                        $goods['name'] = $group_name[$goods_id]['group'];
                        $goods['quantity'] = $group_name[$goods_id]['quantity'];
                        if ($player_id !== false) {
                            $goods['amount'] = isset($m[$goods['name']]) ? $m[$goods['name']]['amount'] : 0;
                            if(isset($m[$goods['name']]['code'])  && $goods['amount'] > 0) $goods['code'] = $m[$goods['name']]['code'];
                        }
                    } else {
                        if ($player_id !== false) {
                            $goods['amount'] = isset($m[$goods['goods_id']]) ? $m[$goods['goods_id']]['amount'] : 0;
                            if(isset($m[$goods['name']]['code'])  && $goods['amount'] > 0) $goods['code'] = $m[$goods['name']]['code'];
                        }
                    }
                    unset($goods['_id']);
                    
                    // unset the result if
                    // 1. good id is set organize and player_id is not in that organize
                    // Or 2. organize role is set and player role is not matched
                    if (isset($goods['organize_id'])) {
                        if ((!array_key_exists((string)$goods['organize_id'], $org_id_list)
                            || ((isset($goods['organize_role']) && $goods['organize_role'] != "")
                                && !array_key_exists($goods['organize_role'],
                                    $org_id_list[(string)$goods['organize_id']]))
                        )
                        ) {
                            unset($goodsList['goods_list'][$key]);
                        } else {
                            $org = $this->store_org_model->retrieveOrganizeById($this->client_id, $this->site_id,
                                $goods['organize_id']);
                            $goods['organize'] = $org['name'];
                            unset($goods['organize_id']);
                        }
                    }
                }
            }
            $goodsList['goods_list'] = array_values($goodsList['goods_list']); // sort array just in case there were unset
            $this->response($this->resp->setRespond($goodsList), 200);
        }
    }

    public function sponsor_get($goodsId = 0)
    {
        $validToken_ad = array('client_id' => null, 'site_id' => null);
        /* process group */
        $results = $this->goods_model->getGroupsAggregate($validToken_ad['site_id']);
        $ids = array();
        $group_name = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }
        /* find my goods */
        $player_id = $this->input->get('player_id');
        if ($player_id !== false) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
            ));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
            $myGoods = $this->player_model->getGoods($pb_player_id, $this->site_id);
            $m = $this->mapByGoodsId($myGoods);
        }
        /* main */
        if ($goodsId) // given specified goods_id
        {
            $goods['goods'] = $this->goods_model->getGoods(array_merge($validToken_ad, array(
                'goods_id' => new MongoId($goodsId)
            )));
            $goods['goods']['is_group'] = array_key_exists('group', $goods['goods']);
            if ($goods['goods']['is_group']) {
                $group = $goods['goods']['group'];
                foreach ($group_name as $each) {
                    if ($each['group'] == $group) {
                        $goods['goods']['quantity'] = $each['quantity'];
                        break;
                    }
                }
                if ($player_id !== false) {
                    $goods['amount'] = isset($m[$group]) ? $m[$group]['amount'] : 0;
                }
            } else {
                if ($player_id !== false) {
                    $goods['amount'] = isset($m[$goods['goods_id']]) ? $m[$goods['goods_id']]['amount'] : 0;
                }
            }
            $this->response($this->resp->setRespond($goods), 200);
        } else // list all
        {
            $goodsList['goods_list'] = $this->goods_model->getAllGoods($validToken_ad, $ids);
            if (is_array($goodsList['goods_list'])) {
                foreach ($goodsList['goods_list'] as &$goods) {
                    $goods_id = $goods['_id'];
                    $is_group = array_key_exists('group', $goods);
                    if ($is_group) {
                        $goods['is_group'] = true;
                        $goods['name'] = $group_name[$goods_id]['group'];
                        $goods['quantity'] = $group_name[$goods_id]['quantity'];
                        if ($player_id !== false) {
                            $goods['amount'] = isset($m[$goods['name']]) ? $m[$goods['name']]['amount'] : 0;
                        }
                    } else {
                        if ($player_id !== false) {
                            $goods['amount'] = isset($m[$goods['goods_id']]) ? $m[$goods['goods_id']]['amount'] : 0;
                        }
                    }
                    unset($goods['_id']);
                    $goods['code'] = null;
                }
            }
            $this->response($this->resp->setRespond($goodsList), 200);
        }
    }

    public function personalizedSponsor_get()
    {
        $validToken_ad = array('client_id' => null, 'site_id' => null);
        /* check required 'player_id' */
        $required = $this->input->checkParam(array(
            'player_id',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $cl_player_id = $this->input->get('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        /* process group */
        $results = $this->goods_model->getGroupsAggregate($validToken_ad['site_id']);
        $ids = array();
        $group_name = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }
        /* goods list */
        $goodsList = $this->goods_model->getAllGoods($validToken_ad, $ids);
        $goods['goods'] = $this->recommend($pb_player_id, $goodsList);
        $goods['goods']['is_group'] = array_key_exists('group', $goods['goods']);
        if ($goods['goods']['is_group']) {
            $group = $goods['goods']['group'];
            foreach ($group_name as $each) {
                if ($each['group'] == $group) {
                    $goods['goods']['quantity'] = $each['quantity'];
                    break;
                }
            }
        }
        $this->response($this->resp->setRespond($goods), 200);
    }

    private function recommend($pb_player_id, $goodsList)
    {
        if (!$goodsList) {
            return array();
        }
        /* TODO: integrate machine learning algorithm instead of randomly picking a goods */
        $idx = rand(0, count($goodsList) - 1);
        return $this->goods_model->getGoods(array_merge(array('client_id' => null, 'site_id' => null), array(
            'goods_id' => new MongoId($goodsList[$idx]['goods_id'])
        )));
    }

    private function mapByGoodsId($goodsList)
    {
        $ret = array();
        foreach ($goodsList as $goods) {
            $key = isset($goods['group']) ? $goods['group'] : $goods['goods_id'];
            if (!isset($ret[$key])) {
                $ret[$key] = $goods;
                if(isset($goods['group'])){
                    if($goods['amount'] > 0){
                        $ret[$key]['code'] = array($goods['code']);
                    } else {
                        $ret[$key]['code'] = array();
                    }
                } else {
                    if($goods['amount'] > 0){
                        $ret[$key]['code'] = $goods['code'];
                    } else {
                        $ret[$key]['code'] = "";
                    }
                }
            } else {
                $ret[$key]['amount'] += $goods['amount'];
                if((isset($goods['group'])) && ($goods['amount'] > 0)) $ret[$key]['code'][] = $goods['code'];
            }
        }
        return $ret;
    }

    private function convert_mongo_object(&$item, $key)
    {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else {
                if (get_class($item) === 'MongoDate') {
                    $item = datetimeMongotoReadable($item);
                }
            }
        }
    }
}

?>