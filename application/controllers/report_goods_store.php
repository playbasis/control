<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_goods_store extends MY_Controller
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

        $this->getGoodsList(0, site_url('report_goods_store/page'));

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

        $this->getGoodsList($offset, site_url('report_goods_store/page'));
    }

    public function goods_store_filter()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getGoodsList(0, site_url('report_goods_store/page'));
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

        $filter_tags = null;
        $filter_goods_distinct = array();
        $filter_goods_data = array();

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
                $currentDate = strtotime($filter_date_end);
                $futureDate = $currentDate + ("86399");
                $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            }
        } else {
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
        }

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

        if ($this->input->get('status')){
            $filter_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_status;
            if($filter_status == "all" ){
                $status = null;
            } else {
                $status = $filter_status == "disable" ? false : true;
            }
        } else {
            $filter_status = null;
            $status = true;
        }

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'start' => $offset,
            'limit' => $limit,
            'distinct_id' => $filter_goods_distinct,
            'filter_tags' => $filter_tags,
            'filter_status' => $status
        );

        $report_total = 0;

        $results = array();

        if ($client_id) {
            $report_total = $this->Report_goods_model->getTotalReportGoodsStore($data);
            $results = $this->Report_goods_model->getReportGoodsStore($data);
        }

        $this->data['reports'] = array();

        foreach ($results as $result) {
            if($result['is_group']){
                $date_start = array();
                $date_end = array();
                $date_expire = array();
                $goods_data = $this->Goods_model->getAllGoodsByDistinctID($client_id, $site_id, $result['_id']);
                if (is_array($result['batch_name']))foreach ($result['batch_name'] as $key => $batch) {
                    $goods_batch = $this->Goods_model->checkBatchNameExistInClient($result['name'], array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $batch));
                    array_push($date_start, isset($goods_batch['date_start']) && $goods_batch['date_start'] ? datetimeMongotoReadable($goods_batch['date_start']) : "N/A" );
                    array_push($date_end, isset($goods_batch['date_expire']) && $goods_batch['date_expire'] ? datetimeMongotoReadable($goods_batch['date_expire']) : "N/A");
                    array_push($date_expire, isset($goods_batch['date_expired_coupon']) && $goods_batch['date_expired_coupon'] ? datetimeMongotoReadable($goods_batch['date_expired_coupon']) : "N/A");
                }
                $quantity = $goods_data ? sizeof($goods_data) : 0;
                $remaining_goods = $goods_data ? array_filter(array_column($goods_data, 'quantity')) : 0;
                $remaining = $remaining_goods ? sizeof($remaining_goods) : 0;

                $goods_period_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'date_start' => $filter_date_start, 'date_end' => $filter_date_end));
                $goods_total_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name']));
                $active_array = array();
                $used_array = array();
                $expire_array = array();
                $total_active_array = array();
                $total_used_array = array();
                $total_expire_array = array();
                foreach ($goods_total_data as $key => &$v){
                    if(array_key_exists('receiver_id', $v)){
                        unset($goods_total_data[$key]);
                        continue;
                    }
                    if(array_key_exists('status', $v)){
                        if($v['status'] == 'used'){
                            array_push($total_used_array, $v);
                        } else {
                            $d = new MongoDate();
                            $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                            if($d > $e){
                                array_push($total_expire_array, $v);
                            }
                            else{
                                array_push($total_active_array, $v);
                            }
                        }
                    } else {
                        $d = new MongoDate();
                        $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                        if($d > $e){
                            array_push($total_expire_array, $v);
                        }
                        else{
                            array_push($total_active_array, $v);
                        }
                    }
                }
                foreach ($goods_period_data as $key => &$v){
                    if(array_key_exists('receiver_id', $v)){
                        unset($goods_period_data[$key]);
                        continue;
                    }
                    if(array_key_exists('status', $v)){
                        if($v['status'] = 'used'){
                            array_push($used_array, $v);
                        } else {
                            $d = new MongoDate();
                            $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                            if($d > $e){
                                array_push($expire_array, $v);
                            }
                            else{
                                array_push($active_array, $v);
                            }
                        }
                    } else {
                        $d = new MongoDate();
                        $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                        if($d > $e){
                            array_push($expire_array, $v);
                        }
                        else{
                            array_push($active_array, $v);
                        }
                    }
                }

                $data_row = array(
                    'goods_name' => $result['name'],
                    'group' => $result['is_group'],
                    'batch' => $result['batch_name'],
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'date_expire' => $date_expire,
                    'quantity' => $quantity,
                    'remaining' => $remaining,
                    'granted' => sizeof($goods_period_data),
                    'unused' => sizeof($active_array),
                    'used' => sizeof($used_array),
                    'expired' => sizeof($expire_array),
                    'total_granted' => sizeof($goods_total_data),
                    'total_unused' => sizeof($total_active_array),
                    'total_used' => sizeof($total_used_array),
                    'total_expired' => sizeof($total_expire_array),
                );
                if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true) && isset($result['tags'])) {
                    $searchword = 'PRICE';
                    $price = explode("=RM", implode("", array_filter($result['tags'], function ($var) use ($searchword) {
                        return preg_match("/\b$searchword\b/i", $var);
                    })));
                    $data_row['price'] = isset($price[1]) ? $price[1] : 0;
                    $data_row['total_value'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['quantity']) : 0;
                    $data_row['used_balance'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['used']) : 0;
                    $data_row['total_used_balance'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['total_used']) : 0;
                }

                $this->data['reports'][] = $data_row;
            } else {
                $goods_data = $this->Goods_model->getAllGoodsByDistinctID($client_id, $site_id, $result['_id']);
                $goods_period_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'date_start' => $filter_date_start, 'date_end' => $filter_date_end)) : array();
                $goods_total_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'])): array();
                $remaining = $goods_data ? $goods_data[0]['quantity'] : 0;
                $active_array = array();
                $used_array = array();
                $expire_array = array();
                $total_active_array = array();
                $total_used_array = array();
                $total_expire_array = array();
                foreach ($goods_total_data as $key => &$v){
                    if(array_key_exists('receiver_id', $v)){
                        unset($goods_total_data[$key]);
                        continue;
                    }
                    $d = new MongoDate();
                    $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                    if($d > $e){
                        array_push($total_expire_array, $v);
                    } else {
                        if(array_key_exists('status', $v)){
                            if($v['status'] = 'used'){
                                array_push($total_used_array, $v);
                            } else {
                                array_push($total_active_array, $v);
                            }
                        } else {
                            array_push($total_active_array, $v);
                        }
                    }
                }
                foreach ($goods_period_data as $key => &$v){
                    if(array_key_exists('receiver_id', $v)){
                        unset($goods_period_data[$key]);
                        continue;
                    }
                    $d = new MongoDate();
                    $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                    if($d > $e){
                        array_push($expire_array, $v);
                    } else {
                        if(array_key_exists('status', $v)){
                            if($v['status'] = 'used'){
                                array_push($used_array, $v);
                            } else {
                                array_push($active_array, $v);
                            }
                        } else {
                            array_push($active_array, $v);
                        }
                    }
                }
                
                $data_row = array(
                    'goods_name' => $result['name'],
                    'group' => $result['is_group'],
                    'batch' => array(),
                    'date_start' => isset($result['date_start']) ? array(datetimeMongotoReadable($result['date_start'])) : array(),
                    'date_end' => isset($result['date_expire']) ? array(datetimeMongotoReadable($result['date_expire'])) : array(),
                    'date_expire' => isset($result['date_expired_coupon']) ? array(datetimeMongotoReadable($result['date_expired_coupon'])) : array(),
                    'quantity' => sizeof($goods_total_data) + $remaining,
                    'remaining' => $remaining,
                    'granted' => sizeof($goods_period_data),
                    'unused' => sizeof($active_array),
                    'used' => sizeof($used_array),
                    'expired' => sizeof($expire_array),
                    'total_granted' => sizeof($goods_total_data),
                    'total_unused' => sizeof($total_active_array),
                    'total_used' => sizeof($total_used_array),
                    'total_expired' => sizeof($total_expire_array),
                );
                if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true) && isset($result['tags'])) {
                    $searchword = 'PRICE';
                    $price = explode("=RM", implode("", array_filter($result['tags'], function ($var) use ($searchword) {
                        return preg_match("/\b$searchword\b/i", $var);
                    })));
                    $data_row['price'] = isset($price[1]) ? $price[1] : 0;
                    $data_row['total_value'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['quantity']) : 0;
                    $data_row['used_balance'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['used']) : 0;
                    $data_row['total_used_balance'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['total_used']) : 0;
                }

                $this->data['reports'][] = $data_row;
            }
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
        $this->data['filter_tags'] = $filter_tags;
        $this->data['filter_goods_id'] = $filter_goods_id;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;
        $this->data['filter_status'] = $filter_status;

        $this->data['main'] = 'report_goods_store';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function convert_mongo_date(&$item, $key)
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

        if ($this->User_model->hasPermission('access', 'report/action') && $this->Feature_model->getFeatureExistByClientId($client_id, 'report/action')
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
            $parameter_url .= "&date_expire=" . $filter_date_end;
            if(strpos($filter_date_end, '00:00:00')){
                $currentDate = strtotime($filter_date_end);
                $futureDate = $currentDate + ("86399");
                $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            }
        } else {
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
        }

        if ($this->input->get('status')){
            $filter_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_status;
            if($filter_status == "all" ){
                $status = null;
            } else {
                $status = $filter_status == "disable" ? false : true;
            }
        } else {
            $status = true;
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
        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'distinct_id' => $filter_goods_distinct,
            'filter_tags' => $filter_tags,
            'filter_status' => $status
        );
        $report_total = 0;

        if ($client_id) {
            $report_total = $this->Report_goods_model->getTotalReportGoodsStore ($data);
        }

        $this->data['reports'] = array();

        $this->load->helper('export_data');
        $exporter = new ExportDataCSV('browser', "GoodsStoreReport_" . date("YmdHis") .".csv");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_goods_name'),
                $this->lang->line('column_goods_group'),
                $this->lang->line('column_goods_unit_price'),
                $this->lang->line('column_goods_quantity'),
                $this->lang->line('column_goods_total_price'),
                $this->lang->line('column_goods_granted'),
                $this->lang->line('column_goods_expired'),
                $this->lang->line('column_goods_unused'),
                $this->lang->line('column_goods_used'),
                $this->lang->line('column_goods_balance'),
                'total_'.$this->lang->line('column_goods_granted'),
                'total_'.$this->lang->line('column_goods_expired'),
                'total_'.$this->lang->line('column_goods_unused'),
                'total_'.$this->lang->line('column_goods_used'),
                'total_'.$this->lang->line('column_goods_balance'),
                $this->lang->line('column_goods_remaining'),
                $this->lang->line('column_goods_batch'),
                $this->lang->line('column_goods_date_start'),
                $this->lang->line('column_goods_date_end'),
                $this->lang->line('column_goods_date_expire'),
            )
        );

        $data['limit'] = 100;
        for ($i = 0; $i < $report_total/100; $i++){
            $data['start'] = ($i * 100);
            $results = $this->Report_goods_model->getReportGoodsStore($data);
            foreach ($results as $result) {
                if(isset($result['tags'])){
                    $searchword = 'PRICE';
                    $price = explode("=RM", implode("", array_filter($result['tags'], function ($var) use ($searchword) {
                        return preg_match("/\b$searchword\b/i", $var);
                    })));
                }
                if($result['is_group']){
                    $date_start = array();
                    $date_end = array();
                    $date_expire = array();
                    $goods_data = $this->Goods_model->getAllGoodsByDistinctID($client_id, $site_id, $result['_id']);
                    if (is_array($result['batch_name'])) foreach ($result['batch_name'] as $key => $batch) {
                        $goods_batch = $this->Goods_model->checkBatchNameExistInClient($result['name'], array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $batch));
                        array_push($date_start, isset($goods_batch['date_start']) && $goods_batch['date_start'] ? datetimeMongotoReadable($goods_batch['date_start']) : "" );
                        array_push($date_end, isset($goods_batch['date_expire']) && $goods_batch['date_expire'] ? datetimeMongotoReadable($goods_batch['date_expire']) : "");
                        array_push($date_expire, isset($goods_batch['date_expired_coupon']) && $goods_batch['date_expired_coupon'] ? datetimeMongotoReadable($goods_batch['date_expired_coupon']) : "");
                    }
                    $quantity = $goods_data ? sizeof($goods_data) : 0;
                    $remaining_goods = $goods_data ? array_filter(array_column($goods_data, 'quantity')) : 0;
                    $remaining = $remaining_goods ? sizeof($remaining_goods) : 0;

                    $goods_period_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'date_start' => $filter_date_start, 'date_end' => $filter_date_end));
                    $goods_total_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name']));

                    $active_array = array();
                    $used_array = array();
                    $expire_array = array();
                    $total_active_array = array();
                    $total_used_array = array();
                    $total_expire_array = array();
                    foreach ($goods_total_data as $key => &$v){
                        if(array_key_exists('receiver_id', $v)){
                            unset($goods_total_data[$key]);
                            continue;
                        }
                        $d = new MongoDate();
                        $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                        if($d > $e){
                            array_push($total_expire_array, $v);
                        } else {
                            if(array_key_exists('status', $v)){
                                if($v['status'] = 'used'){
                                    array_push($total_used_array, $v);
                                } else {
                                    array_push($total_active_array, $v);
                                }
                            } else {
                                array_push($total_active_array, $v);
                            }
                        }
                    }
                    foreach ($goods_period_data as $key => &$v){
                        if(array_key_exists('receiver_id', $v)){
                            unset($goods_period_data[$key]);
                            continue;
                        }
                        $d = new MongoDate();
                        $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                        if($d > $e){
                            array_push($expire_array, $v);
                        } else {
                            if(array_key_exists('status', $v)){
                                if($v['status'] = 'used'){
                                    array_push($used_array, $v);
                                } else {
                                    array_push($active_array, $v);
                                }
                            } else {
                                array_push($active_array, $v);
                            }
                        }
                    }
                    $exporter->addRow(array(
                        $result['name'],
                        $result['is_group'] ? "yes" : "no",
                        isset($price[1]) ? $price[1] : 0,
                        $quantity,
                        isset($price[1]) ? floatval($price[1]) * floatval($quantity) : 0,
                        sizeof($goods_period_data),
                        sizeof($expire_array),
                        sizeof($active_array),
                        sizeof($used_array),
                        isset($price[1]) ? floatval($price[1]) * floatval(sizeof($used_array)) : 0,
                        sizeof($goods_total_data),
                        sizeof($total_expire_array),
                        sizeof($total_active_array),
                        sizeof($total_used_array),
                        isset($price[1]) ? floatval($price[1]) * floatval(sizeof($total_used_array)) : 0,
                        $remaining,
                        isset($result['batch_name']) ? implode("\n", $result['batch_name']) : "",
                        isset($date_start) ? implode("\n", $date_start) : "",
                        isset($date_end) ? implode("\n", $date_end) : "",
                        isset($date_expire) ? implode("\n", $date_expire) : "")
                    );

                } else {
                    $goods_data = $this->Goods_model->getAllGoodsByDistinctID($client_id, $site_id, $result['_id']);
                    $goods_period_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'date_start' => $filter_date_start, 'date_end' => $filter_date_end)) : array();
                    $goods_total_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'])): array();
                    $remaining = $goods_data ? $goods_data[0]['quantity'] : 0;
                    $active_array = array();
                    $used_array = array();
                    $expire_array = array();
                    $total_active_array = array();
                    $total_used_array = array();
                    $total_expire_array = array();
                    foreach ($goods_total_data as $key => &$v){
                        if(array_key_exists('receiver_id', $v)){
                            unset($goods_total_data[$key]);
                            continue;
                        }
                        if(array_key_exists('status', $v)){
                            if($v['status'] == 'used'){
                                array_push($total_used_array, $v);
                            } else {
                                $d = new MongoDate();
                                $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                                if($d > $e){
                                    array_push($total_expire_array, $v);
                                }
                                else{
                                    array_push($total_active_array, $v);
                                }
                            }
                        } else {
                            $d = new MongoDate();
                            $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                            if($d > $e){
                                array_push($total_expire_array, $v);
                            }
                            else{
                                array_push($total_active_array, $v);
                            }
                        }
                    }
                    foreach ($goods_period_data as $key => &$v){
                        if(array_key_exists('receiver_id', $v)){
                            unset($goods_period_data[$key]);
                            continue;
                        }
                        if(array_key_exists('status', $v)){
                            if($v['status'] = 'used'){
                                array_push($used_array, $v);
                            } else {
                                $d = new MongoDate();
                                $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                                if($d > $e){
                                    array_push($expire_array, $v);
                                }
                                else{
                                    array_push($active_array, $v);
                                }
                            }
                        } else {
                            $d = new MongoDate();
                            $e = isset($v['date_expire']) ? $v['date_expire']: $d;
                            if($d > $e){
                                array_push($expire_array, $v);
                            }
                            else{
                                array_push($active_array, $v);
                            }
                        }
                    }

                    $exporter->addRow(array(
                        $result['name'],
                        $result['is_group'] ? "yes" : "no",
                        isset($price[1]) ? $price[1] : 0,
                        sizeof($goods_total_data) + $remaining,
                        isset($price[1]) ? floatval($price[1]) * floatval(sizeof($goods_total_data) + $remaining) : 0,
                        sizeof($goods_period_data),
                        sizeof($expire_array),
                        sizeof($active_array),
                        sizeof($used_array),
                        isset($price[1]) ? floatval($price[1]) * floatval(sizeof($used_array)) : 0,
                        sizeof($goods_total_data),
                        sizeof($total_expire_array),
                        sizeof($total_active_array),
                        sizeof($total_used_array),
                        isset($price[1]) ? floatval($price[1]) * floatval(sizeof($total_used_array)) : 0,
                        $remaining,
                        isset($result['batch_name'][0]) ? $result['batch_name'][0] : "",
                        isset($result['date_start']) ? datetimeMongotoReadable($result['date_start']) : "",
                        isset($result['date_expire']) ? datetimeMongotoReadable($result['date_expire']) : "",
                        isset($result['date_expired_coupon']) ? datetimeMongotoReadable($result['date_expired_coupon']) : "")
                    );
                }
            }
        }
        $exporter->finalize();
    }
}