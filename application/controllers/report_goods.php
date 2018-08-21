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
            $filter_username = null;
        }

        $filter_tags = null;
        $filter_goods_distinct = array();
        $filter_goods_data = array();
        if ($this->input->get('tags')) {
            $filter_tags = $this->input->get('tags');
            $parameter_url .= "&tags=" . $filter_tags;
        }
        if ($this->input->get('goods_id')){
            $filter_goods_id = $this->input->get('goods_id');
            $parameter_url .= "&goods_id=" . $filter_goods_id;
            $filter_goods_id = explode(',', $filter_goods_id);
            foreach ($filter_goods_id as $value){
                $goods_data = $this->Goods_model->getGoodsOfClientPrivate($value);
                $filter_goods_data[] = $goods_data;
                if(isset($goods_data['distinct_id'])){
                    $filter_goods_distinct[] = $goods_data['distinct_id'];
                }

            }
        } else {
            $filter_goods_id = array();
        }
        $goods_list = array();
        $group = array();
        $goods = array();

        if($filter_goods_distinct || $filter_tags){
            $goods_list = $this->Goods_model->getGroupsList($site_id,  $filter_array = array(
                'distinct_id' => $filter_goods_distinct,
                'filter_tags' => $filter_tags
            ));
            $goods_distinct = $goods_list;
        } else {
            $goods_distinct = $this->Goods_model->getGroupsList($site_id);
        }

        foreach ($goods_list as $list){
            if($list['is_group']){
                $group[] = $list['name'];
            } else {
                $index = array_search($list['_id'], array_column($filter_goods_data, 'distinct_id'));
                if($index){
                    $goods[] = $filter_goods_data[$index]['goods_id'];
                } else {
                    $goods_detail = $this->Goods_model->getGoodsByDistinctID($client_id, $site_id, $list['_id']);
                    $goods[] = $goods_detail['goods_id'];
                }
            }
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

            $goods_name = isset($result['group']) && $result['group'] ? $result['group'] : $result['goods_name'];
            $index = array_search($goods_name,array_column($goods_distinct, 'name'));
            if(is_numeric($index)){
                $tags = isset($goods_distinct[$index]['tags']) ? $goods_distinct[$index]['tags'] : null;
            } else {
                $tags = null;
            }

            $data_row = array(
                'cl_player_id' => isset($result['cl_player_id']) ? $result['cl_player_id'] : null,
                'date_added' => $date_added ,
                'date_expire' => $date_expire,
                'date_used' => isset($result['status']) && $result['status'] == 'used' ? $date_modified : null,
                'date_gifted' => isset($result['status']) && $result['status'] == 'sender' ? $date_modified : null,
                'goods_name' => $goods_name,
                'code' => isset($result['code']) ? $result['code'] : null,
                'tags' => $tags,
                'value' => $result['amount'],
                'status' => $status
            );
            if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true) && isset($goods_distinct[$index]['tags'])) {
                $searchword = 'CAT';
                $category = explode("=", implode("", array_filter($goods_distinct[$index]['tags'], function ($var) use ($searchword) {
                    return preg_match("/\b$searchword\b/i", $var);
                })));
                $data_row['category'] = isset($category[1]) ? $category[1] : "";
                $searchword = 'PRICE';
                $price = explode("=", implode("", array_filter($goods_distinct[$index]['tags'], function ($var) use ($searchword) {
                    return preg_match("/\b$searchword\b/i", $var);
                })));
                $data_row['price'] = isset($price[1]) ? $price[1] : "";
            }
            $this->data['reports'][] = $data_row;
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
        } else {
            $filter_username = '';
        }
        
        $filter_tags = null;
        $filter_goods_distinct = array();
        $filter_goods_data = array();
        if ($this->input->get('tags')) {
            $filter_tags = $this->input->get('tags');
        }
        if ($this->input->get('goods_id')){
            $filter_goods_id = $this->input->get('goods_id');
            $filter_goods_id = explode(',', $filter_goods_id);
            foreach ($filter_goods_id as $value){
                $goods_data = $this->Goods_model->getGoodsOfClientPrivate($value);
                $filter_goods_data[] = $goods_data;
                if(isset($goods_data['distinct_id'])){
                    $filter_goods_distinct[] = $goods_data['distinct_id'];
                }

            }
        }
        $goods_list = array();
        $group = array();
        $goods = array();

        if($filter_goods_distinct || $filter_tags){
            $goods_list = $this->Goods_model->getGroupsList($site_id,  $filter_array = array(
                'distinct_id' => $filter_goods_distinct,
                'filter_tags' => $filter_tags
            ));
            $goods_distinct = $goods_list;
        } else {
            $goods_distinct = $this->Goods_model->getGroupsList($site_id);
        }

        foreach ($goods_list as $list){
            if($list['is_group']){
                $group[] = $list['name'];
            } else {
                $index = array_search($list['_id'], array_column($filter_goods_data, 'distinct_id'));
                if($index){
                    $goods[] = $filter_goods_data[$index]['goods_id'];
                } else {
                    $goods_detail = $this->Goods_model->getGoodsByDistinctID($client_id, $site_id, $list['_id']);
                    $goods[] = $goods_detail['goods_id'];
                }
            }
        }

        if ($this->input->get('status')) {
            $filter_goods_status = $this->input->get('status');
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

        if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true)) {
            $exporter->addRow(array(
                    $this->lang->line('column_player_id'),
                    $this->lang->line('column_goods_name'),
                    $this->lang->line('column_goods_code'),
                    $this->lang->line('column_tags'),
                    $this->lang->line('column_category'),
                    $this->lang->line('column_price'),
                    $this->lang->line('column_goods_amount'),
                    $this->lang->line('column_status'),
                    $this->lang->line('column_date_added'),
                    $this->lang->line('column_date_used'),
                    $this->lang->line('column_date_gifted'),
                    $this->lang->line('column_date_expire')
                )
            );
        } else {
            $exporter->addRow(array(
                    $this->lang->line('column_player_id'),
                    $this->lang->line('column_goods_name'),
                    $this->lang->line('column_goods_code'),
                    $this->lang->line('column_tags'),
                    $this->lang->line('column_goods_amount'),
                    $this->lang->line('column_status'),
                    $this->lang->line('column_date_added'),
                    $this->lang->line('column_date_used'),
                    $this->lang->line('column_date_gifted'),
                    $this->lang->line('column_date_expire')
                )
            );
        }

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
                $goods_name = isset($result['group']) && $result['group'] ? $result['group'] : $result['goods_name'];
                $index = array_search($goods_name,array_column($goods_distinct, 'name'));
                $tags = isset($goods_distinct[$index]['tags']) ? implode(',', $goods_distinct[$index]['tags']) : null;
                if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true)) {
                    $searchword = 'CAT';
                    $category = explode("=", implode("", array_filter($goods_distinct[$index]['tags'], function($var) use ($searchword) { return preg_match("/\b$searchword\b/i", $var); })));
                    $category = isset($category[1]) ? $category[1] : "";
                    $searchword = 'PRICE';
                    $price = explode("=", implode("", array_filter($goods_distinct[$index]['tags'], function($var) use ($searchword) { return preg_match("/\b$searchword\b/i", $var); })));
                    $price = isset($price[1]) ? $price[1] : "";
                    $exporter->addRow($data_row = array(
                        isset($result['cl_player_id']) ? $result['cl_player_id'] : null,
                        $goods_name,
                        isset($result['code']) ? $result['code'] : null,
                        $tags,
                        $category,
                        $price,
                        $result['amount'],
                        $status,
                        $date_added,
                        isset($result['status']) && $result['status'] == 'used' ? $date_modified : null,
                        isset($result['status']) && $result['status'] == 'sender' ? $date_modified : null,
                        $date_expire
                    ));
                } else {
                    $exporter->addRow($data_row = array(
                        isset($result['cl_player_id']) ? $result['cl_player_id'] : null,
                        $goods_name,
                        isset($result['code']) ? $result['code'] : null,
                        $tags,
                        $result['amount'],
                        $status,
                        $date_added,
                        isset($result['status']) && $result['status'] == 'used' ? $date_modified : null,
                        isset($result['status']) && $result['status'] == 'sender' ? $date_modified : null,
                        $date_expire
                    ));
                }

            }
        }
        $exporter->finalize();

    }
}