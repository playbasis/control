<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class  MY_Controller  extends  CI_Controller  {

    function MY_Controller ()  {
        parent::__construct();
        if ($this->session->userdata('user_id')) {
            setcookie("client_id", $this->session->userdata('client_id'));
            setcookie("site_id", $this->session->userdata('site_id'));
        } else {
            setcookie("client_id", null);
            setcookie("site_id", null);
        }
    }

    function render_page($view) {

        $this->load->model('User_model');
        $this->load->model('Domain_model');
        $this->load->model('Feature_model');

        $lang = get_lang($this->session, $this->config);

        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['text_affiliate'] = $this->lang->line('text_affiliate');
        $this->data['text_attribute'] = $this->lang->line('text_attribute');
        $this->data['text_attribute_group'] = $this->lang->line('text_attribute_group');
        $this->data['text_backup'] = $this->lang->line('text_backup');
        $this->data['text_banner'] = $this->lang->line('text_banner');
        $this->data['text_catalog'] = $this->lang->line('text_catalog');
        $this->data['text_category'] = $this->lang->line('text_category');
        $this->data['text_confirm'] = $this->lang->line('text_confirm');
        $this->data['text_retry'] = $this->lang->line('text_retry');
        $this->data['text_country'] = $this->lang->line('text_country');
        $this->data['text_coupon'] = $this->lang->line('text_coupon');
        $this->data['text_currency'] = $this->lang->line('text_currency');
        $this->data['text_customer'] = $this->lang->line('text_customer');
        $this->data['text_customer_group'] = $this->lang->line('text_customer_group');
        $this->data['text_customer_blacklist'] = $this->lang->line('text_customer_blacklist');
        $this->data['text_sale'] = $this->lang->line('text_sale');
        $this->data['text_design'] = $this->lang->line('text_design');
        $this->data['text_documentation'] = $this->lang->line('text_documentation');
        $this->data['text_download'] = $this->lang->line('text_download');
        $this->data['text_error_log'] = $this->lang->line('text_error_log');
        $this->data['text_extension'] = $this->lang->line('text_extension');
        $this->data['text_feed'] = $this->lang->line('text_feed');
        $this->data['text_front'] = $this->lang->line('text_front');
        $this->data['text_geo_zone'] = $this->lang->line('text_geo_zone');
        $this->data['text_dashboard'] = $this->lang->line('text_dashboard');
        $this->data['text_help'] = $this->lang->line('text_help');
        $this->data['text_information'] = $this->lang->line('text_information');
        $this->data['text_language'] = $this->lang->line('text_language');
        $this->data['text_layout'] = $this->lang->line('text_layout');
        $this->data['text_localisation'] = $this->lang->line('text_localisation');
        $this->data['text_logout'] = $this->lang->line('text_logout');
        $this->data['text_contact'] = $this->lang->line('text_contact');
        $this->data['text_manufacturer'] = $this->lang->line('text_manufacturer');
        $this->data['text_module'] = $this->lang->line('text_module');
        $this->data['text_option'] = $this->lang->line('text_option');
        $this->data['text_order'] = $this->lang->line('text_order');
        $this->data['text_order_status'] = $this->lang->line('text_order_status');
        $this->data['text_opencart'] = $this->lang->line('text_opencart');
        $this->data['text_payment'] = $this->lang->line('text_payment');
        $this->data['text_product'] = $this->lang->line('text_product');
        $this->data['text_reports'] = $this->lang->line('text_reports');
        $this->data['text_report_sale_order'] = $this->lang->line('text_report_sale_order');
        $this->data['text_report_sale_tax'] = $this->lang->line('text_report_sale_tax');
        $this->data['text_report_sale_shipping'] = $this->lang->line('text_report_sale_shipping');
        $this->data['text_report_sale_return'] = $this->lang->line('text_report_sale_return');
        $this->data['text_report_sale_coupon'] = $this->lang->line('text_report_sale_coupon');
        $this->data['text_report_product_viewed'] = $this->lang->line('text_report_product_viewed');
        $this->data['text_report_product_purchased'] = $this->lang->line('text_report_product_purchased');
        $this->data['text_report_customer_online'] = $this->lang->line('text_report_customer_online');
        $this->data['text_report_customer_order'] = $this->lang->line('text_report_customer_order');
        $this->data['text_report_customer_reward'] = $this->lang->line('text_report_customer_reward');
        $this->data['text_report_customer_credit'] = $this->lang->line('text_report_customer_credit');
        $this->data['text_report_affiliate_commission'] = $this->lang->line('text_report_affiliate_commission');
        $this->data['text_report_sale_return'] = $this->lang->line('text_report_sale_return');
        $this->data['text_report_product_purchased'] = $this->lang->line('text_report_product_purchased');
        $this->data['text_report_product_viewed'] = $this->lang->line('text_report_product_viewed');
        $this->data['text_report_customer_order'] = $this->lang->line('text_report_customer_order');
        $this->data['text_review'] = $this->lang->line('text_review');
        $this->data['text_return'] = $this->lang->line('text_return');
        $this->data['text_return_action'] = $this->lang->line('text_return_action');
        $this->data['text_return_reason'] = $this->lang->line('text_return_reason');
        $this->data['text_return_status'] = $this->lang->line('text_return_status');
        $this->data['text_support'] = $this->lang->line('text_support');
        $this->data['text_shipping'] = $this->lang->line('text_shipping');
        $this->data['text_setting'] = $this->lang->line('text_setting');
        $this->data['text_stock_status'] = $this->lang->line('text_stock_status');
        $this->data['text_system'] = $this->lang->line('text_system');
        $this->data['text_tax'] = $this->lang->line('text_tax');
        $this->data['text_tax_class'] = $this->lang->line('text_tax_class');
        $this->data['text_tax_rate'] = $this->lang->line('text_tax_rate');
        $this->data['text_total'] = $this->lang->line('text_total');
        $this->data['text_user'] = $this->lang->line('text_user');
        $this->data['text_user_group'] = $this->lang->line('text_user_group');
        $this->data['text_users'] = $this->lang->line('text_users');
        $this->data['text_voucher'] = $this->lang->line('text_voucher');
        $this->data['text_voucher_theme'] = $this->lang->line('text_voucher_theme');
        $this->data['text_weight_class'] = $this->lang->line('text_weight_class');
        $this->data['text_length_class'] = $this->lang->line('text_length_class');
        $this->data['text_zone'] = $this->lang->line('text_zone');

        $this->data['text_player'] = $this->lang->line('text_player');
        $this->data['text_badge'] = $this->lang->line('text_badge');
        $this->data['text_collection'] = $this->lang->line('text_collection');
        $this->data['text_client'] = $this->lang->line('text_client');
        $this->data['text_admin'] = $this->lang->line('text_admin');
        $this->data['text_criteria'] = $this->lang->line('text_criteria');
        $this->data['text_level'] = $this->lang->line('text_level');
        $this->data['text_plan'] = $this->lang->line('text_plan');
        $this->data['text_basic_rule'] = $this->lang->line('text_basic_rule');

        $this->data['lang'] = $lang['folder'];
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();
        $this->data['domain'] = '';
        $this->data['domain_name'] = '';
        $this->data['check_domain_exists'] = true;


        $features = array();

        if($this->session->userdata('user_id')){

            if($this->session->userdata('multi_login') != $this->User_model->getMultiLogin($this->session->userdata('user_id'))){
                $this->User_model->logout();
                $this->session->set_flashdata('multi_login', 'You have been logged out due to concurrent login');
                redirect('login', 'refresh');
            }

            $this->data['username'] = $this->User_model->getUserName();

            $userInfo = $this->User_model->getUserInfo($this->session->userdata('user_id'));

            if(isset($userInfo['image'])){
                $this->data['thumbprofile'] = S3_IMAGE.$userInfo['image'];
            }else{
                $this->data['thumbprofile'] = '';
            }


            if($this->data['site_id']){


                $this->data['domain'] = $this->Domain_model->getDomain($this->data['site_id']);
                $this->data['domain_name'] = $this->data['domain'];

                $temp = array('client_id'=>$this->data['client_id'], 'site_id'=>$this->data['site_id']);
                
                // $this->data['domain_all'] = $this->Domain_model->getDomainsByClientId($temp);
                $allDomains = $this->Domain_model->getDomainsByClientId($temp);
                $activeDomains = array();

                foreach ($allDomains as $aDomain){
                    if($aDomain['status']){
                        $activeDomains[] = $aDomain;    
                    }
                }
                $this->data['domain_all'] = $activeDomains;
                // var_dump($this->data['domain_all']);

                $features = $this->Feature_model->getFeatureBySiteId($this->User_model->getClientId(), $this->User_model->getSiteId());
                //var_dump($features);

                
                foreach ($features as $value) {
                    if($this->User_model->hasPermission('access', strtolower(implode("_",explode(" ", $value['link']))))){
                        $this->data['features'][] = array(
                            'feature_id' => $value['_id'],
                            'name' => $value['name'],
                            'icon' => $value['icon'],
                            'link' =>$value['link']
                        );
                    }                        
                }

                // foreach ($features as $value) {
                //     $this->data['features'][] = array(
                //         'feature_id' => $value['_id'],
                //         'name' => $value['name'],
                //         'icon' => $value['icon'],
                //         'link' =>$value['link']
                //     );
                // }
            }else{
                /*if($this->data['client_id']){
                    $this->data['check_domain_exists'] = false;
                }else{
                    $features = $this->Feature_model->getFeatures();    
                    foreach ($features as $value) {
                        $this->data['features'][] = array(
                            'feature_id' => $value['_id'],
                            'name' => $value['name'],
                            'icon' => $value['icon'],
                            'link' =>$value['link']
                        );
                    }
                }*/

                // super admin
                
                //var_dump($features);
            }
        }

        if ($this->data['check_domain_exists']){
            $this->load->vars($this->data);
            $this->load->view($view);
        }else{
            $this->load->vars($this->data);
            $this->load->view('no_domain');
        }
        
    }

}
