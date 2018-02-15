<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_goods extends MY_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Goods_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("report", $lang['folder']);
    }

    public function index()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getGoodsList(0, site_url('report_goods/page'));

    }

    public function page($offset = 0)
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getGoodsList($offset, site_url('report_goods/page'));
    }

    public function goods_filter()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getGoodsList(0, site_url('report_goods/page'));
    }

    public function getGoodsList($offset, $url)
    {
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        $this->load->library('pagination');

        $this->load->model('Report_goods_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $date = date("Y-m-d", strtotime("-7 days"));
            $previousDate = strtotime($date);
            $filter_date_start = date("Y-m-d H:i:s", $previousDate);
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            $parameter_url .= "&date_expire=" . $filter_date_end;

            if(strpos($filter_date_end, '00:00:00')){
                //--> This will enable to search on the day until the time 23:59:59
                $currentDate = strtotime($filter_date_end);
                $futureDate = $currentDate + ("86399");
                $filter_date_end = date("Y-m-d H:i:s", $futureDate);
                //--> end*/
            }
        } else {
            //--> This will enable to search on the current day until the time 23:59:59
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end
        }

        $UTC_7 = new DateTimeZone("Asia/Bangkok");

        if ($this->input->get('time_zone')){
            $filter_time_zone = $this->input->get('time_zone');
            $parameter_url .= "&time_zone=" . urlencode($filter_time_zone);
            $newTZ = new DateTimeZone($filter_time_zone);
            $date_start = new DateTime( $filter_date_start, $newTZ);
            $date_start->setTimezone($UTC_7);
            $filter_date_start2 = $date_start->format("Y-m-d H:i:s");;

            $date_end = new DateTime( $filter_date_end, $newTZ);
            $date_end->setTimezone($UTC_7);
            $filter_date_end2 = $date_end->format("Y-m-d H:i:s");
        } else {
            $filter_time_zone = "Asia/Bangkok";
            $newTZ = new DateTimeZone($filter_time_zone);
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=" . $filter_username;
        } else {
            $filter_username = '';
        }
        $goodsList = array();
        if ($this->input->get('tags')) {
            $filter_tags = $this->input->get('tags');
            $parameter_url .= "&tags=" . $filter_tags;
            $opts = array('client_id' => $client_id, 'site_id' => $site_id);
            $opts['tags'] = explode(',', $filter_tags);
            $group_list = $this->Goods_model->getGroupsList($site_id, array('filter_group' => true));;
            $in_goods = array();
            foreach ($group_list as $group_name){
                $goods_group_id =  $this->Goods_model->getGoodsIDByName($client_id, $site_id, "", $group_name['name'], false);
                array_push($in_goods, new MongoId($goods_group_id));
            }
            $opts['specific'] = array('$or' => array(array("group" => array('$exists' => false ) ), array("goods_id" => array('$in' => $in_goods ) ) ));
            $goodsList = $this->Goods_model->getAllGoods($opts);
            foreach ($goodsList as &$val){
                $val = $val['goods_id'];
            }
        } else {
            $filter_tags = '';
        }

        $goods = array();
        $group = array();
        if ($this->input->get('goods_id')) {
            $filter_goods_id = $this->input->get('goods_id');
            $parameter_url .= "&goods_id=" . $filter_goods_id;
            $filter_goods_id = explode(',', $filter_goods_id);
            foreach ($filter_goods_id as $value){
                if($this->input->get('tags') && $goodsList){
                    $match =  array_search($value, $goodsList);
                    if(!is_null($match) && $match !== false){
                        $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($value);
                        if(array_key_exists('group', $goods_detail)){
                            array_push($group, $goods_detail['group']);
                        } else {
                            array_push($goods, $goods_detail['goods_id']);
                        }
                    }
                } else {
                    $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($value);
                    if(array_key_exists('group', $goods_detail)){
                        array_push($group, $goods_detail['group']);
                    } else {
                        array_push($goods, $goods_detail['goods_id']);
                    }
                }
            }
        } else {
            if($goodsList){
                foreach ($goodsList as $v){
                    $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($v);
                    if(array_key_exists('group', $goods_detail)){
                        array_push($group, $goods_detail['group']);
                    } else {
                        array_push($goods, $goods_detail['goods_id']);
                    }
                }
            }
            $filter_goods_id = array();
        }

        if ($this->input->get('status')) {
            $filter_goods_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_goods_status;
            if ($filter_goods_status === "all" ) $filter_goods_status = null;
        } else {
            $filter_goods_status = null;
        }

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'goods_id' => $goods,
            'status' => $filter_goods_status,
            'group' => $group,
            'start' => $offset,
            'limit' => $limit
        );

        $report_total = 0;

        $results = array();

        if ($client_id) {
            $report_total = $this->Report_goods_model->getTotalReportGoods($data);
            $results = $this->Report_goods_model->getReportGoods($data);
        }

        $this->data['time_zone'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $this->data['reports'] = array();

        foreach ($results as $result) {
            $date_expire = null;
            $status =  isset($result['status']) ? $result['status'] : 'active';
            $currentYMD = date("Y-m-d");
            $currentTime = strtotime($currentYMD." " . date('H:i:s', time()) );
            if ($this->input->get('time_zone')){
                $date_added = new DateTime(datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");

                $date_modified = new DateTime(datetimeMongotoReadable($result['date_modified']), $UTC_7);
                $date_modified->setTimezone($newTZ);
                $date_modified = $date_modified->format("Y-m-d H:i:s");

                if(isset($result['date_expire']) && $result['date_expire']){
                    $date_expire = new DateTime(datetimeMongotoReadable($result['date_expire']), $UTC_7);
                    $date_expire->setTimezone($newTZ);
                    $date_expire = $date_expire->format("Y-m-d H:i:s");
                    $expireTime = strtotime($date_expire);
                    $status = $currentTime > $expireTime && $status != 'used' ? 'expire' : $status;
                }
            }else{
                $date_added = datetimeMongotoReadable($result['date_added']);
                $date_modified = datetimeMongotoReadable($result['date_modified']);
                if(isset($result['date_expire']) && $result['date_expire']){
                    $date_expire = datetimeMongotoReadable($result['date_expire']);
                    $expireTime = strtotime($date_expire);
                    $status = $currentTime > $expireTime && $status != 'used' ? 'expire' : $status;
                }
            }

            $this->data['reports'][] = array(
                'cl_player_id' => isset($result['cl_player_id']) ? $result['cl_player_id'] : null,
                'date_added' => $date_added ,
                'date_expire' => $date_expire,
                'date_used' => isset($result['status']) && $result['status'] == 'used' ? $date_modified : null,
                'date_gifted' => isset($result['status']) && $result['status'] == 'sender' ? $date_modified : null,
                'goods_name' => isset($result['group']) && $result['group'] ? $result['group'] : $result['goods_name'],
                'code' => isset($result['code']) ? $result['code'] : null,
                'value' => $result['amount'],
                'status' => $status
            );
        }

        $this->data['goods_available'] = array();

        if ($client_id) {
            $allGoodsAvailable = $this->getList($site_id);
            $all_goods = array();
            foreach ($allGoodsAvailable as $goods) {
                $is_group = array_key_exists('group', $goods);
                $all_goods[] = array(
                    '_id' => $goods['goods_id']->{'$id'},
                    'name' => $goods[$is_group ? 'group' : 'name']
                );
            }
            $this->data['goods_available'] = $all_goods;
        }

        $config['base_url'] = $url . $parameter_url;

        $config['total_rows'] = $report_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;
        $config['page_query_string'] = true;

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = "<li class='page_index_nav next'>";
        $config['next_tag_close'] = "</li>";

        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = "<li class='page_index_nav prev'>";
        $config['prev_tag_close'] = "</li>";

        $config['num_tag_open'] = '<li class="page_index_number">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page_index_number active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page_index_nav next">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page_index_nav prev">';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        $this->data['pagination_total_pages'] = ceil(floatval($config["total_rows"]) / $config["per_page"]);
        $this->data['pagination_total_rows'] = $config["total_rows"];

        $this->data['filter_time_zone'] = $filter_time_zone;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;
        // --> end
        $this->data['filter_username'] = $filter_username;
        $this->data['filter_tags'] = $filter_tags;
        $this->data['filter_goods_id'] = $filter_goods_id;
        $this->data['filter_status'] = $filter_goods_status;
        $this->data['main'] = 'report_goods';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getList($site_id)
    {
        $goods_list = $this->Goods_model->getGroupsList($site_id);
        $goods_data = array();
        foreach ($goods_list as $goods_name){
            if($goods_name['is_group']){
                $goods_detail =  $this->Goods_model->getGoodsByName($this->session->userdata('client_id'), $this->session->userdata('site_id'), "", $goods_name['name'],false);
            } else {
                $goods_detail =  $this->Goods_model->getGoodsByName($this->session->userdata('client_id'), $this->session->userdata('site_id'), $goods_name['name'], null,false);
            }
            if(!is_null($goods_detail)){
                array_push($goods_data, $goods_detail);
            }
        }

        return $goods_data;
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'report/action') && $this->Feature_model->getFeatureExistByClientId($client_id, 'report/action')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function actionDownload()
    {
        $parameter_url = "?t=" . rand();
        $this->load->model('Report_goods_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $date = date("Y-m-d", strtotime("-7 days"));
            $previousDate = strtotime($date);
            $filter_date_start = date("Y-m-d H:i:s", $previousDate);
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            if(strpos($filter_date_end, '00:00:00')){
                //--> This will enable to search on the day until the time 23:59:59
                $currentDate = strtotime($filter_date_end);
                $futureDate = $currentDate + ("86399");
                $filter_date_end = date("Y-m-d H:i:s", $futureDate);
                //--> end*/
            }
        } else {
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
        }

        $UTC_7 = new DateTimeZone("Asia/Bangkok");
        if ($this->input->get('time_zone')){

            $filter_time_zone = $this->input->get('time_zone');
            $newTZ = new DateTimeZone($filter_time_zone);
            $date_start = new DateTime( $filter_date_start, $newTZ);
            $date_start->setTimezone($UTC_7);
            $filter_date_start2 = $date_start->format("Y-m-d H:i:s");;

            $date_end = new DateTime( $filter_date_end, $newTZ);
            $date_end->setTimezone($UTC_7);
            $filter_date_end2 = $date_end->format("Y-m-d H:i:s");
        } else {
            $filter_time_zone = "Asia/Bangkok";
            $newTZ = new DateTimeZone($filter_time_zone);
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=" . $filter_username;
        } else {
            $filter_username = '';
        }

        $goodsList = array();
        if ($this->input->get('tags')) {
            $filter_tags = $this->input->get('tags');
            $parameter_url .= "&tags=" . $filter_tags;
            $opts = array('client_id' => $client_id, 'site_id' => $site_id);
            $opts['tags'] = explode(',', $filter_tags);
            $group_list = $this->Goods_model->getGroupsList($site_id, array('filter_group' => true));;
            $in_goods = array();
            foreach ($group_list as $group_name){
                $goods_group_id =  $this->Goods_model->getGoodsIDByName($client_id, $site_id, "", $group_name['name'], false);
                array_push($in_goods, new MongoId($goods_group_id));
            }
            $opts['specific'] = array('$or' => array(array("group" => array('$exists' => false ) ), array("goods_id" => array('$in' => $in_goods ) ) ));
            $goodsList = $this->Goods_model->getAllGoods($opts);
            foreach ($goodsList as &$val){
                $val = $val['goods_id'];
            }
        } else {
            $filter_tags = '';
        }

        $goods = array();
        $group = array();
        if ($this->input->get('goods_id')) {
            $filter_goods_id = $this->input->get('goods_id');
            $parameter_url .= "&goods_id=" . $filter_goods_id;
            $filter_goods_id = explode(',', $filter_goods_id);
            foreach ($filter_goods_id as $value){
                if($this->input->get('tags') && $goodsList){
                    $match =  array_search($value, $goodsList);
                    if(!is_null($match) && $match !== false){
                        $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($value);
                        if(array_key_exists('group', $goods_detail)){
                            array_push($group, $goods_detail['group']);
                        } else {
                            array_push($goods, $goods_detail['goods_id']);
                        }
                    }
                } else {
                    $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($value);
                    if(array_key_exists('group', $goods_detail)){
                        array_push($group, $goods_detail['group']);
                    } else {
                        array_push($goods, $goods_detail['goods_id']);
                    }
                }
            }
        } else {
            if($goodsList){
                foreach ($goodsList as $v){
                    $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($v);
                    if(array_key_exists('group', $goods_detail)){
                        array_push($group, $goods_detail['group']);
                    } else {
                        array_push($goods, $goods_detail['goods_id']);
                    }
                }
            }
            $filter_goods_id = '';
        }

        if ($this->input->get('status')) {
            $filter_goods_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_goods_status;
            if ($filter_goods_status === "all") $filter_goods_status = null;
        } else {
            $filter_goods_status = null;
        }

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'goods_id' => $goods,
            'status' => $filter_goods_status,
            'group' => $group,
        );
        $report_total = 0;

        if ($client_id) {
            $report_total = $this->Report_goods_model->getTotalReportGoods($data);
        }

        $this->data['reports'] = array();

        $this->load->helper('export_data');

        $exporter = new ExportDataExcel('browser', "GoodsReport_" . date("YmdHis") . ".xls");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_player_id'),
                $this->lang->line('column_goods_name'),
                $this->lang->line('column_goods_code'),
                $this->lang->line('column_goods_amount'),
                $this->lang->line('column_status'),
                $this->lang->line('column_date_added'),
                $this->lang->line('column_date_used'),
                $this->lang->line('column_date_gifted'),
                $this->lang->line('column_date_expire')
            )
        );
        $data['limit'] = 10000;
        for ($i = 0; $i < $report_total/10000; $i++){
            $data['start'] = ($i * 10000);
            $results = $this->Report_goods_model->getReportGoods($data);
            foreach ($results as $result) {
                $date_expire = null;
                $status =  isset($result['status']) ? $result['status'] : 'active';
                $currentYMD = date("Y-m-d");
                $currentTime = strtotime($currentYMD." " . date('H:i:s', time()) );
                if ($this->input->get('time_zone')){
                    $date_added = new DateTime(datetimeMongotoReadable($result['date_added']), $UTC_7);
                    $date_added->setTimezone($newTZ);
                    $date_added = $date_added->format("Y-m-d H:i:s");

                    $date_modified = new DateTime(datetimeMongotoReadable($result['date_modified']), $UTC_7);
                    $date_modified->setTimezone($newTZ);
                    $date_modified = $date_modified->format("Y-m-d H:i:s");

                    if(isset($result['date_expire']) && $result['date_expire']){
                        $date_expire = new DateTime(datetimeMongotoReadable($result['date_expire']), $UTC_7);
                        $date_expire->setTimezone($newTZ);
                        $date_expire = $date_expire->format("Y-m-d H:i:s");
                        $expireTime = strtotime($date_expire);
                        $status = $currentTime > $expireTime && $status != 'used'? 'expire' : $status;
                    }
                }else{
                    $date_added = datetimeMongotoReadable($result['date_added']);
                    $date_modified = datetimeMongotoReadable($result['date_modified']);
                    if(isset($result['date_expire']) && $result['date_expire']){
                        $date_expire = datetimeMongotoReadable($result['date_expire']);
                        $expireTime = strtotime($date_expire);
                        $status = $currentTime > $expireTime && $status != 'used'? 'expire' : $status;
                    }
                }

                $exporter->addRow(array(
                        isset($result['cl_player_id']) ? $result['cl_player_id'] : null,
                        isset($result['group']) && $result['group'] ? $result['group'] : $result['goods_name'],
                        isset($result['code']) ? $result['code'] : null,
                        $result['amount'],
                        $status,
                        $date_added,
                        isset($result['status']) && $result['status'] == 'used' ? $date_modified : null,
                        isset($result['status']) && $result['status'] == 'sender' ? $date_modified : null,
                        $date_expire
                    )
                );
            }
        }
        $exporter->finalize();

    }
}