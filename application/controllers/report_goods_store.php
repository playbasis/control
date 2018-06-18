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

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'start' => $offset,
            'limit' => $limit,
            'distinct_id' => $filter_goods_distinct,
            'filter_tags' => $filter_tags
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
                $remaining_goods = $goods_data ? array_filter(array_column($goods_data, 'quantity')) : 0;
                $quantity = $goods_data ? sizeof($goods_data) : 0;
                $remaining = $remaining_goods ? sizeof($remaining_goods) : 0;
                $unused_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'status' => 'active'));
                $unused = $unused_data ? sizeof($unused_data) : 0;
                $expired_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'status' => 'expired'));
                $expired = $expired_data ? sizeof($expired_data) : 0;
                $used_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'status' => 'used'));
                $used = $used_data ? sizeof($used_data) : 0;
                $data_row = array(
                    'goods_name' => $result['name'],
                    'group' => $result['is_group'],
                    'batch' => $result['batch_name'],
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'date_expire' => $date_expire,
                    'quantity' => $quantity,
                    'remaining' => $remaining,
                    'granted' => $quantity - $remaining,
                    'unused' => $unused,
                    'used' => $used,
                    'expired' => $expired
                );
                if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true) && isset($result['tags'])) {
                    $searchword = 'PRICE';
                    $price = explode("=RM", implode("", array_filter($result['tags'], function ($var) use ($searchword) {
                        return preg_match("/\b$searchword\b/i", $var);
                    })));
                    $data_row['price'] = isset($price[1]) ? $price[1] : 0;
                    $data_row['total_value'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['quantity']) : 0;
                    $data_row['used_balance'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['used']) : 0;
                }

                $this->data['reports'][] = $data_row;
            } else {
                $goods_data = $this->Goods_model->getAllGoodsByDistinctID($client_id, $site_id, $result['_id']);
                $granted_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'granted')) : array();
                $granted = $granted_data ? sizeof($granted_data) : 0 ;
                $remaining = $granted_data ? $goods_data[0]['quantity'] : 0;
                $unused_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'active')) : array();
                $unused = $unused_data ? sizeof($unused_data) : 0;
                $expired_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'expired')) : array();
                $expired = $expired_data ? sizeof($expired_data) : 0;
                $used_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'used')) : array();
                $used = $used_data ? sizeof($used_data) : 0;
                $data_row = array(
                    'goods_name' => $result['name'],
                    'group' => $result['is_group'],
                    'batch' => array(),
                    'date_start' => isset($result['date_start']) ? array(datetimeMongotoReadable($result['date_start'])) : array(),
                    'date_end' => isset($result['date_expire']) ? array(datetimeMongotoReadable($result['date_expire'])) : array(),
                    'date_expire' => isset($result['date_expired_coupon']) ? array(datetimeMongotoReadable($result['date_expired_coupon'])) : array(),
                    'quantity' => $granted + $remaining,
                    'remaining' => $remaining,
                    'granted' => $granted,
                    'unused' => $unused,
                    'used' => $used,
                    'expired' => $expired
                );
                if(defined('REPORT_CATEGORY_PRICE_DISPLAY') && (REPORT_CATEGORY_PRICE_DISPLAY == true) && isset($result['tags'])) {
                    $searchword = 'PRICE';
                    $price = explode("=RM", implode("", array_filter($result['tags'], function ($var) use ($searchword) {
                        return preg_match("/\b$searchword\b/i", $var);
                    })));
                    $data_row['price'] = isset($price[1]) ? $price[1] : 0;
                    $data_row['total_value'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['quantity']) : 0;
                    $data_row['used_balance'] = isset($price[1]) ? floatval($price[1]) * floatval($data_row['used']) : 0;
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

        $this->data['main'] = 'report_goods_store';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getList($site_id)
    {
        $goods_list = $this->Goods_model->getGroupsList($site_id, array('filter_tags' => 'RM1HOTDEALS'));
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
            'filter_tags' => $filter_tags
        );
        $report_total = 0;

        if ($client_id) {
            $report_total = $this->Report_goods_model->getTotalReportGoodsStore ($data);
        }

        $this->data['reports'] = array();

        $this->load->helper('export_data');

        $exporter = new ExportDataCSV('browser', "GoodsStoreReport_" . date("YmdHis") . ".csv");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_goods_name'),
                $this->lang->line('column_goods_group'),
                $this->lang->line('column_goods_batch'),
                $this->lang->line('column_goods_date_start'),
                $this->lang->line('column_goods_date_end'),
                $this->lang->line('column_goods_date_expire'),
                $this->lang->line('column_goods_unit_price'),
                $this->lang->line('column_goods_quantity'),
                $this->lang->line('column_goods_total_price'),
                $this->lang->line('column_goods_remaining'),
                $this->lang->line('column_goods_granted'),
                $this->lang->line('column_goods_expired'),
                $this->lang->line('column_goods_unused'),
                $this->lang->line('column_goods_used'),
                $this->lang->line('column_goods_balance')
            )
        );

        $data['limit'] = 10000;
        for ($i = 0; $i < $report_total/10000; $i++){
            $data['start'] = ($i * 10000);
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
                    $remaining_goods = $goods_data ? array_filter(array_column($goods_data, 'quantity')) : 0;
                    $quantity = $goods_data ? sizeof($goods_data) : 0;
                    $remaining = $remaining_goods ? sizeof($remaining_goods) : 0;
                    $unused_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'status' => 'active'));
                    $unused = $unused_data ? sizeof($unused_data) : 0;
                    $expired_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'status' => 'expired'));
                    $expired = $expired_data ? sizeof($expired_data) : 0;
                    $used_data = $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'group' => $result['name'], 'status' => 'used'));
                    $used = $used_data ? sizeof($used_data) : 0;
                    $exporter->addRow(array(
                        $result['name'],
                        $result['is_group'] ? "yes" : "no",
                        isset($result['batch_name']) ? implode("\n", $result['batch_name']) : "",
                        isset($date_start) ? implode("\n", $date_start) : "",
                        isset($date_end) ? implode("\n", $date_end) : "",
                        isset($date_expire) ? implode("\n", $date_expire) : "",
                        isset($price[1]) ? $price[1] : 0,
                        $quantity,
                        isset($price[1]) ? floatval($price[1]) * floatval($quantity) : 0,
                        $remaining,
                        $quantity - $remaining,
                        $expired,
                        $unused,
                        $used,
                        isset($price[1]) ? floatval($price[1]) * floatval($used) : 0)
                    );

                } else {
                    $goods_data = $this->Goods_model->getAllGoodsByDistinctID($client_id, $site_id, $result['_id']);
                    $granted_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'granted')) : array();
                    $granted = $granted_data ? sizeof($granted_data) : 0 ;
                    $remaining = $granted_data ? $goods_data[0]['quantity'] : 0;
                    $unused_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'active')) : array();
                    $unused = $unused_data ? sizeof($unused_data) : 0;
                    $expired_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'expired')) : array();
                    $expired = $expired_data ? sizeof($expired_data) : 0;
                    $used_data = $goods_data ? $this->Goods_model->getGoodsLog(array('client_id' => $client_id, 'site_id' => $site_id, 'goods_id' => $goods_data[0]['goods_id'], 'status' => 'used')) : array();
                    $used = $used_data ? sizeof($used_data) : 0;
                    $exporter->addRow(array(
                        $result['name'],
                        $result['is_group'] ? "yes" : "no",
                        isset($result['batch_name'][0]) ? $result['batch_name'][0] : "",
                        isset($result['date_start']) ? datetimeMongotoReadable($result['date_start']) : "",
                        isset($result['date_expire']) ? datetimeMongotoReadable($result['date_expire']) : "",
                        isset($result['date_expired_coupon']) ? datetimeMongotoReadable($result['date_expired_coupon']) : "",
                        isset($price[1]) ? $price[1] : 0,
                        $granted + $remaining,
                        isset($price[1]) ? floatval($price[1]) * floatval($quantity) : 0,
                        $remaining,
                        $granted,
                        $expired,
                        $unused,
                        $used,
                        isset($price[1]) ? floatval($price[1]) * floatval($used) : 0)
                    );
                }
            }
        }
        $exporter->finalize();

    }
}