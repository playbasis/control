<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class  MY_Controller  extends  CI_Controller  {

    function MY_Controller ()  {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Client_model');
        $this->load->model('Player_model');
        $this->load->model('App_model');
        $this->load->model('Feature_model');
        $this->load->model('Plan_model');

        if ($this->session->userdata('user_id')) {
            $this->input->set_cookie("client_id", $this->session->userdata('client_id'));
            $this->input->set_cookie("site_id", $this->session->userdata('site_id'));
            if ($this->input->get('site_id')) {
                $this->User_model->updateSiteId($this->input->get('site_id'));
                $this->User_model->set_last_app();
            }
        } else {
            $this->input->set_cookie("client_id", null);
            $this->input->set_cookie("site_id", null);
        }

    }

    function render_page($view) {


        $lang = get_lang($this->session, $this->config);

        if (!array_key_exists('heading_title', $this->data)) $this->data['heading_title'] = $this->lang->line('heading_title');
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
        $this->data['site'] = '';
        $this->data['site_name'] = '';
        $this->data['check_site_exists'] = true;

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

                $this->data['site'] = $this->App_model->getApp($this->data['site_id']);
                $this->data['site_name'] = $this->data['site'];

                $temp = array('client_id'=>$this->data['client_id'], 'site_id'=>$this->data['site_id']);

                $allSites = $this->App_model->getAppsByClientId($temp);
                $activeSites = array();

                foreach ($allSites as $aSite){
                    if($aSite['status']){
                        $activeSites[] = $aSite;    
                    }
                }
                $this->data['site_all'] = $activeSites;
                // var_dump($this->data['site_all']);

                $features = $this->Feature_model->getFeatureBySiteId($this->User_model->getClientId(), $this->User_model->getSiteId());
//                var_dump($features);
                
                foreach ($features as $value) {
                    if($this->User_model->hasPermission('access', strtolower(implode("_",explode(" ", $value['link']))))){
                        if(isset($value['type'])){
                            $this->data['features'][$value['type']][] = array(
                                'feature_id' => $value['_id'],
                                'name' => $value['name'],
                                'icon' => $value['icon'],
                                'link' =>$value['link']
                            );
                        }else{
                            $this->data['features']['others'][] = array(
                                'feature_id' => $value['_id'],
                                'name' => $value['name'],
                                'icon' => $value['icon'],
                                'link' =>$value['link']
                            );
                        }
                    }
                }
                $user_plan = $this->User_model->getPlan();
                if (!array_key_exists('price', $user_plan)) $user_plan['price'] = DEFAULT_PLAN_PRICE;
                $this->data['user_plan'] = $user_plan;
                /*if(isset($user_plan['limit_notifications']) && is_null($user_plan['limit_notifications']['sms'])){
                    $this->data['features'][] = array(
                        'feature_id' => new MongoId(),
                        'name' => 'Sms (old)',
                        'icon' => 'fa-mail-forward',
                        'link' => 'sms'
                    );
                }*/
                $client = $this->Client_model->getClient($this->data['client_id']);
                $this->data['account'] = $this->set_account($user_plan, $client);

                $player_limit = $this->App_model->getPlanLimitById(
                    $this->data['site_id'],
                    $user_plan["_id"],
                    "others",
                    "player"
                );
                $usersCount = $this->Player_model->getTotalPlayers($this->data['site_id'], $this->data['client_id']);
                $this->data['check_limit'] = array(
                    'limit_user' => $player_limit,
                    'total' => $usersCount
                );
            }else{
                if($this->data['client_id']){
                    // check to see if there is an associated plan, otherwise we output error no site
                    $user_plan = $this->User_model->getPlan();
                    if (!array_key_exists('price', $user_plan)) $user_plan['price'] = DEFAULT_PLAN_PRICE;
                    $this->data['user_plan'] = $user_plan;
                    if (!empty($user_plan)) {
                        if (array_key_exists('feature_to_plan', $user_plan)) foreach ($user_plan['feature_to_plan'] as $feature_id) {
                            $value = $this->Feature_model->getFeature($feature_id);
                            if($this->User_model->hasPermission('access', strtolower(implode("_",explode(" ", $value['link']))))){
                                $this->data['features']['others'][] = array(
                                    'feature_id' => $value['_id'],
                                    'name' => $value['name'],
                                    'icon' => $value['icon'],
                                    'link' =>$value['link']
                                );
                            }
                        }

                        /*if(isset($user_plan['limit_notifications']) && is_null($user_plan['limit_notifications']['sms'])){
                            $this->data['features'][] = array(
                                'feature_id' => new MongoId(),
                                'name' => 'Sms (old)',
                                'icon' => 'fa-mail-forward',
                                'link' => 'sms'
                            );
                        }*/
                    } else {
                        $this->data['check_site_exists'] = false;
                    }
                    $client = $this->Client_model->getClient($this->data['client_id']);
                    $this->data['account'] = $this->set_account($user_plan, $client);
                }else{
                    // super admin
                    $features = $this->Feature_model->getFeatures();    
                    foreach ($features as $value) {
                        if($this->User_model->hasPermission('access', strtolower(implode("_",explode(" ", $value['link']))))){
                            $this->data['features']['others'][] = array(
                                'feature_id' => $value['_id'],
                                'name' => $value['name'],
                                'icon' => $value['icon'],
                                'link' =>$value['link']
                            );
                        }
                    }

                    $this->data['features']['others'][] = array(
                        'feature_id' => new MongoId(),
                        'name' => 'Sms',
                        'icon' => 'fa-mail-forward',
                        'link' => 'sms/setup'
                    );
                }
                //var_dump($features);
            }
        }

        if ($this->data['check_site_exists']){
            $this->load->vars($this->data);
            $this->load->view($view);
        }else{
            $this->load->vars($this->data);
            $this->load->view('no_site');
        }
    }

    private function set_account($user_plan, $client) {
        $is_free_plan = $user_plan['_id'] == FREE_PLAN;
        $is_plan_price_gt_0 = $user_plan['price'] > 0;
        $is_displayed_plan = in_array($user_plan['_id'], $this->Plan_model->getDisplayedPlans());
        $client_has_date_billing = array_key_exists('date_billing', $client);
        return array(
            'is_free_plan' => $is_free_plan,
            'is_paid_plan' => $is_plan_price_gt_0,
            'is_pro_plan_pending' => !$is_free_plan && $is_plan_price_gt_0 && $is_displayed_plan && !$client_has_date_billing,
            'is_pro_plan_paid' => !$is_free_plan && $is_plan_price_gt_0 && $is_displayed_plan && $client_has_date_billing,
            'is_enterprise_plan_without_paypal' => !$is_free_plan && !$is_plan_price_gt_0,
            'is_enterprise_plan_with_paypal_pending' => !$is_free_plan && $is_plan_price_gt_0 && !$is_displayed_plan && !$client_has_date_billing,
            'is_enterprise_plan_with_paypal_paid' => !$is_free_plan && $is_plan_price_gt_0 && !$is_displayed_plan && $client_has_date_billing,
            'is_already_subscribed' => $client_has_date_billing,
            'is_something_wrong' => !$is_free_plan && $is_displayed_plan && !$is_plan_price_gt_0,
        );
    }
}
