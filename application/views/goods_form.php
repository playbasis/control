<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<div id="content" class="span10">
    <?php if ($this->session->flashdata('success')) { ?>
        <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" id="submit_button" onclick="<?php if($is_group){ ?> fromcheck() <?php }else{ ?> $('#form').submit(); <?php } ?>" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'<?php echo $this->session->flashdata('refer_page') ? $this->session->flashdata('refer_page') : $refer_page; ?>'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }?>
            <div id="tabs" class="htabs">
                <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
                <?php if ($is_group && isset($group)) { ?>
                <a href="#tab-coupon"><?php echo $this->lang->line('tab_coupon'); ?></a>
                <?php } ?>
                <a href="#tab-data"><?php echo $this->lang->line('tab_data'); ?></a>
                <a href="#tab-redeem"><?php echo $this->lang->line('tab_redeem'); ?></a>
            </div>
            <?php
            if(validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            $attributes = array('id' => 'form');
            echo form_open_multipart($form ,$attributes);
            ?>
                <div id="tab-general">
                    <table class="form">
                        <input type="hidden" name="refer_page" value="<?php echo $this->session->flashdata('refer_page') ? $this->session->flashdata('refer_page') : $refer_page; ?>" id="refer_page" />
                        <tr>
                            <td><span class="required">*</span> <?php echo $this->lang->line($is_group ? 'entry_group' : 'entry_name'); ?>:</td>
                            <td><input type="text" name="name" size="100" value="<?php echo $is_group ? (isset($group) ? $group : set_value('group')) : (isset($name) ? $name : set_value('name')); ?>" /></td>
                        </tr>
                        <?php if ($client_id && $is_group && !isset($group)) { ?>
                        <tr>
                            <td><?php if ($is_import) { ?><span class="required">*</span><?php } ?><?php echo $this->lang->line('entry_file'); ?>:</td>
                            <td>
                                <input id="file" type="file" name="file" size="100" />
                                <a onclick="showDemo()" title="Show file example" class="tooltips" data-placement="top"><i class="fa fa-file-text-o fa-lg"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <?php if(!$client_id && !$name){?>
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_for_client'); ?>:</td>
                                <td>
                                    <select id="client-choose" name="admin_client_id">
                                        <?php if(isset($to_clients)){?>
                                        <option value = 'all_clients'>All Clients</option>
                                            <?php foreach($to_clients as $client){?>
                                                <option value ="<?php echo $client['_id']?>"><?php echo $client['company'] ? $client['company'] : $client['first_name']." ".$client['last_name'];?></option>
                                            <?php }?>
                                        <?php }?>
                                    </select>
                                </td>
                            <?php }?>
                        </tr>
                        <?php if(!$client_id){?>
                            <tr>
                                <td><?php echo $this->lang->line('entry_sponsor'); ?>:</td>
                                <td>
                                    <input type="checkbox" name="sponsor" value = 1 <?php echo ($sponsor)?'checked':'unchecked'?> class="tooltips" data-placement="right" title="Sponsor badge cannot be modified by clients"/>
                                </td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_description'); ?>:</td>
                            <td><textarea name="description" id="description"><?php echo isset($description) ? $description : set_value('description'); ?></textarea></td>
                        </tr>
                        <?php if (!$is_group) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_code'); ?>:</td>
                            <td>
                                <input type="text" name="code" value="<?php echo isset($code) ? $code : set_value('code'); ?>" size="5" class="tooltips" data-placement="right" title="Code for redeem or do something"/>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($client_id) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_tags'); ?>:</td>
                            <td>
                                <input type="text" class="tags" name="tags" value="<?php echo isset($tags) ? implode(',',$tags) : set_value('tags'); ?>" size="5" class="tooltips" data-placement="right" title="Tag(s) input" style="width: 600px;" />
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if (!$is_group) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_start_date'); ?>:</td>
                            <td>
                                <input type="text" class="date" name="date_start" placeholder="date start reward coupon"value="<?php if ($date_start && strtotime(datetimeMongotoReadable($date_start))) {echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($date_start)));} else { echo $date_start; } ?>" size="50" />
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_expire_date'); ?>:</td>
                            <td>
                                <input type="text" class="date" name="date_expire" placeholder="date end to reward coupon" value="<?php if ($date_expire && strtotime(datetimeMongotoReadable($date_expire))) { echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($date_expire))); } else { echo $date_expire; } ?>" size="50" />
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($is_group) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_days_expire'); ?>:</td>
                            <td>
                                <input type="number" name="days_expire" placeholder="day to expire after get coupon" value="<?php echo isset($days_expire) ? $days_expire :""; ?>" size="50" />
                            </td>
                        </tr>
                        <?php } else { ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_date_expire'); ?>:</td>
                            <td>
                                <input type="text" class="date"  name="date_expired_coupon" placeholder="date to expire coupon" value="<?php if (isset($date_expired_coupon) && $date_expired_coupon && strtotime(datetimeMongotoReadable($date_expired_coupon))) { echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($date_expired_coupon))); } else { echo isset($date_expired_coupon) ? $date_expired_coupon :""; } ?>" size="50" />
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->line('column_param'); ?>:</td>
                            <td>
                                <div class="row-fluid">
                                    <table class="table table-bordered" id="new-branches-table">
                                        <thead>
                                        <th><?php echo $this->lang->line('column_key'); ?></th>
                                        <th><?php echo $this->lang->line('column_value'); ?></th>
                                        </thead>
                                        <tbody>
                                        <?php if(isset($custom_param) && is_array($custom_param) ){?>
                                            <?php foreach($custom_param as $key => $param){
                                                if(strpos( $param['key'], POSTFIX_NUMERIC_PARAM ) == false){?>
                                                <tr id="param_<?php echo "custom_param[".$key."][key]" ?>">
                                                    <td><input type="text" name="<?php echo "custom_param[".$key."][key]" ?>" id="<?php echo "custom_param[".$key."][key]" ?>"
                                                               value="<?php echo isset($param['key']) ? $param['key']: set_value('parameter'); ?>"
                                                    </td>
                                                    <td><input type="text" name="<?php echo "custom_param[".$key."][value]" ?>" id="<?php echo "custom_param[".$key."][value]" ?>"
                                                               value="<?php echo isset($param['value']) ? $param['value']: set_value('parameter'); ?>"
                                                          <div>
                                                            <input class="chk_custom_hid" type="checkbox" name="<?php echo "custom_param[".$key."][hidden]" ?>" value="" <?php echo !isset($param['hidden']) || (isset($param['hidden']) && $param['hidden']) ? 'checked' : ''; ?> id="<?php echo "custom_param[".$key."]['hidden']" ?>" style="margin: 0px 0px 2px 15px;">
                                                            <span>Show in table</span>
                                                            <button type="button" onclick="deleteCustomParam('<?php echo "custom_param[".$key."][key]" ?>','<?php echo "custom_param[".$key."][value]" ?>');" style="background:transparent;border:none;outline:none;float:right;font-size:30px;" ><span class="icon-remove" style="color: red;"></span></button>
                                                          </div>
                                                    </td>
                                                </tr>
                                            <?php }
                                            }
                                        }?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="text-align: center">
                                                    <div class="row-fluid">
                                                        <div class="offset3 span3">
                                                            <a class="btn btn-primary btn-block" id="add" onclick="createParameterRow()"><i class="fa fa-plus"></i>&nbsp;Add</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tab-data">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
                        <?php if (!$is_group) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_quantity'); ?>:</td>
                            <td><input type="text" name="quantity" value="<?php echo isset($quantity) ? $quantity : set_value('quantity'); ?>" size="5" class="tooltips" data-placement="right" title="Number of Goods to be redeemed, if left blank it is unlimited"/></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_per_user'); ?>:</td>
                            <td>
                                <input type="checkbox" name="per_user_include_inactive" id="per_user_include_inactive" value=true <?php echo $per_user_include_inactive ? "checked":""?> /> <?php echo $this->lang->line('entry_per_user_include_inactive'); ?>

                                <br><input type="text" name="per_user" value="<?php echo isset($per_user) ? $per_user : set_value('per_user'); ?>" size="5" class="tooltips" data-placement="right" title="Number of Goods that a user can redeem, if left blank it is unlimited"/>
                                                  </td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_sort_order'); ?>:</td>
                            <td><input type="text" name="sort_order" value="<?php echo isset($sort_order) ? $sort_order : set_value('sort_order'); ?>" size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                            <td><select name="status">
                                    <?php if ($status) { ?>
                                        <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                        <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                        <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                    <?php } ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_whitelist'); ?>:</td>
                            <td>
                                <input type="checkbox" name="whitelist_enable" id="whitelist_enable" value=true <?php echo $whitelist_enable ? "checked":""?> /> <?php echo $this->lang->line('entry_whitelist_enable'); ?><br>

                                <p name="whitelist_file_name" id="whitelist_file_name" >
                                       <?php echo isset($whitelist_file_name) && $whitelist_file_name ? $whitelist_file_name."&nbsp;&nbsp;<a onclick=\"downloadFile()\" title=\"Download files\" class=\"tooltips\" data-placement=\"top\"><i class=\"fa fa-file-text-o fa-lg\"></i></a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ''; ?>
                                        <input type="file" id="whitelist_file" name="whitelist_file" size="20" /></p>
                            </td>

                        </tr>
                        <?php if($org_status){?>
                        <tr>
                            <td><?php echo $this->lang->line('entry_organize_name'); ?>:
                            </td>
                            <td>
                                <input type="checkbox" name="global_goods" id="global_goods" value=true <?php echo isset($organize_id)?"":"checked"?> /> <?php echo $this->lang->line('entry_global_goods'); ?>

                                <br>Type : <input type='hidden' name="organize_id" id="organize_id" style="width:220px;" value="<?php echo isset($organize_id) ? $organize_id : set_value('organize_id'); ?>">

                                <br>Role : <input type="text" name="organize_role" id="organize_role" value="<?php echo isset($organize_role) ? $organize_role : set_value('organize_role'); ?>" size="1" />
                            </td>

                        </tr>
                        <?php }?>
                    </table>
                </div>
                <div id="tab-redeem">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_redeem_with'); ?>:</td>
                            <td>
                                <div class="well">
                                    <button id="point-entry" type="button" style="max-width: 400px;" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>
                                    <div class="point">
                                        <div class="well">
                                            <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                            <input type="text" name="reward_point" size="100" class="orange tooltips" value="<?php echo isset($reward_point) ? $reward_point :  set_value('reward_point'); ?>" data-placement="right" title="The number of Points needed to redeem the Goods"/>
                                        </div>
                                    </div>
                                    <div id="badge-panel">
                                        <?php
                                        if($badge_list){
                                        ?>
                                            <br>
                                            <button id="badge-entry" type="button" style="max-width: 400px;" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>
                                            <div class="badges">
                                                <div id="redeem_badge_table" class="well ">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#formAddBadge" id="editBadge" ><i class="fa fa-plus"></i> Add</button>
                                                    <br>
                                                <?php
                                                foreach($badge_list as $badge){
                                                    if(array_key_exists($badge['badge_id']."",$reward_badge)) {
                                                            ?>
                                                            <div id="<?php echo $badge['badge_id']; ?>">
                                                                <img id="Img_Badge" height="50" width="50" data-toggle="tooltip" data-placement="left" title="<?php echo $badge['name']; ?>" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png'); "/>
                                                                <input id="valueBadge_<?php echo $badge['badge_id']; ?>" placeholder="<?php echo $badge['name']; ?>" type="text" name="reward_badge[<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="<?php echo $reward_badge[$badge['badge_id'].""]?>"/>
                                                                <button type="button" onclick="deleteBadge('<?php echo $badge['badge_id']; ?>');" style="background: transparent; border: none; outline: none;" ><span class="icon-remove" style="color: red;"></span></button><br/>
                                                            </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                </div>
                                             </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <div id="reward-panel">
                                    <?php
                                    if($point_list){
                                    ?>
                                        <br>
                                        <button id="reward-entry" type="button" style="max-width: 400px;" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_custom_point'); ?></button>
                                        <div class="rewards">
                                            <div id="redeem_custom_reward_table" class="well ">
                                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#formAddCurrency" id="editReward" ><i class="fa fa-plus"></i> Add</button>
                                                <br>
                                            <?php
                                            foreach($point_list as $point){
                                                if(array_key_exists($point['reward_id']."",$reward_reward)) {
                                                    ?>
                                                        <div id="<?php echo $point['reward_id']; ?>">
                                                            <span class="label label-primary"><?php echo $point['name']; ?></span>
                                                            <input id="valueCurrency_<?php echo $point['reward_id']; ?>" type="number" name="reward_reward[<?php echo $point['reward_id']; ?>]" class="<?php echo alternator('green', 'yellow', 'blue'); ?>" size="100" value="<?php echo $reward_reward[$point['reward_id'].""] ?>"/>
                                                            <button type="button" onclick="deleteCurrency('<?php echo $point['reward_id']; ?>');" style="background: transparent; border: none; outline: none;" ><span class="icon-remove" style="color: red;"></span></button><br/>
                                                        </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php
            echo form_close();
            ?>
            <div id="tab-coupon">
                <table>
                    <?php if ($client_id && $is_group && isset($group)) { ?>
                        <tr>
                            <td><?php if ($is_import) { ?><span class="required">*</span><?php } ?><?php echo $this->lang->line('entry_file'); ?>:</td>
                            <td>
                                <?php echo form_open_multipart($form ,array('id' => 'form_coupon')); ?>
                                <input id="file" type="file" name="file" size="100" />
                                <a onclick="showDemo()" title="Show file example" class="tooltips" data-placement="top"><i class="fa fa-file-text-o fa-lg"></i></a>
                                <button class="btn btn-info" type="button" onclick="uploadCoupon();"><?php echo $this->lang->line('button_upload'); ?></button>
                                <?php echo form_close(); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <?php if (isset($members)) { ?>
                    <div class="member_wrapper">
                        <table id="members" class="display form no-footer" cellspacing="0" border="1" width="100%">
                            <thead>
                            <tr>
                                <th style="width:100px;"><?php echo $this->lang->line('entry_goods_id'); ?></th>
                                <th style="width:80px;"><?php echo $this->lang->line('entry_batch_name'); ?></th>
                                <th style="width:200px;"><?php echo $this->lang->line('entry_name'); ?></th>
                                <th style="width:200px;"><?php echo $this->lang->line('entry_code'); ?></th>
                                <th style="width:120px;"><?php echo $this->lang->line('entry_start_date'); ?></th>
                                <th style="width:120px;"><?php echo $this->lang->line('entry_expire_date'); ?></th>
                                <th style="width:120px;"><?php echo $this->lang->line('entry_expire_date_coupon'); ?></th>
                                <th style="width:30px;"><?php echo $this->lang->line('entry_action'); ?></th>
                            </tr>
                            <tr class="filter">
                                <td class="left" ><input style="width:150px;" title="filter_goods" type="text" id="filter_goods" name="filter_goods" value="<?php echo isset($_GET['filter_goods']) ? $_GET['filter_goods'] : "" ?>"/></td>
                                <td class="left" >
                                    <select id="filter_batch" name="filter_batch" style="width:80px;">
                                        <option value=""></option>
                                    <?php foreach ($members_batch as $batch) {?>
                                        <option value="<?php echo $batch;?>" <?php echo isset($_GET['filter_batch']) && $_GET['filter_batch'] == $batch? 'selected' : "" ?>><?php echo $batch;?></option>
                                    <?php } ?>
                                    </select>
                                </td>
                                <td class="left" ><input style="width:180px;" title="filter_coupon_name" type="text" id="filter_coupon_name" name="filter_coupon_name" value="<?php echo isset($_GET['filter_coupon_name']) ? $_GET['filter_coupon_name'] : "" ?>"/></td>
                                <td class="left" ><input style="width:180px;" title="filter_voucher_code" type="text" id="filter_voucher_code" name="filter_voucher_code" value="<?php echo isset($_GET['filter_voucher_code']) ? $_GET['filter_voucher_code'] : "" ?>"/></td>
                                <td class="left" ><input style="width:120px;" type="text" class="date" id="filter_date_start" name="filter_date_start" value="<?php echo isset($_GET['filter_date_start']) ? $_GET['filter_date_start'] : "" ?>" /></td>
                                <td class="left" ><input style="width:120px;" type="text" class="date" id="filter_date_end" name="filter_date_end" value="<?php echo isset($_GET['filter_date_end']) ? $_GET['filter_date_end'] : "" ?>"  /></td>
                                <td class="left" ><input style="width:120px;" type="text" class="date" id="filter_date_expire" name="filter_date_expire" value="<?php echo isset($_GET['filter_date_expire']) ? $_GET['filter_date_expire'] : "" ?>"  /></td>
                                <td style="width:90px;">
                                    <a onclick="filter();" class="button"><i class='fa fa-filter fa-lg' title="Filter"></i></a>
                                    <a onclick="update_table();" class="button" id="clear_filter"><i class='fa fa-refresh fa-lg' title="Clear Filter"></i></a>
                                    <?php if (is_array($members) && isset($members[0]['goods_id'])){ ?>
                                        <a onclick="delete_filtered_coupon('<?php echo $members[0]['goods_id']->{'$id'}?>',);" class="button" id="delete_filtered" title="Delete All Match Filtered"><i class='fa fa-trash fa-lg' title="Delete All Match Filtered"></i></a>
                                        <a onclick="downloadCoupon();" class="button"><i class='fa fa-download fa-lg' title="Download Coupon"></i></a>
                                    <?php } ?>
                                </td>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (is_array($members)){
                                $count = 0;
                                foreach ($members as $index => $member) { ?>
                                    <tr class="<?php echo (++$count%2 ? "odd" : "even") ?>">
                                        <td style="width:120px;"><?php echo $member['goods_id']->{'$id'}; ?></td>
                                        <td style="width:80px;"><?php echo isset($member['batch_name']) ? $member['batch_name'] : 'default'; ?></td>
                                        <td style="width:200px;"><?php echo $member['name']; ?></td>
                                        <td style="width:200px;"><?php echo isset($member['code']) ? $member['code'] : ''; ?></td>
                                        <td style="width:80px;" align="center"><?php echo isset($member['date_start']) ? $member['date_start'] : ""; ?></td>
                                        <td style="width:80px;" align="center"><?php echo isset($member['date_expire']) ? $member['date_expire'] : ""; ?></td>
                                        <td style="width:80px;" align="center"><?php echo isset($member['date_expired_coupon']) ? $member['date_expired_coupon'] : ''; ?></td>
                                        <td align="center">
                                            <a onclick="showCouponModalForm('<?php echo $member['goods_id']->{'$id'}?>',
                                                '<?php echo isset($member['batch_name']) ? $member['batch_name'] : 'default'; ?>',
                                                '<?php echo strpos($member['name'], "'" ) ? implode('xposss', explode("'", $member['name'])) : $member['name']; ?>',
                                                '<?php echo isset($member['code']) ? $member['code'] : ''; ?>',
                                                '<?php echo isset($member['date_start']) ? $member['date_start'] : ""; ?>',
                                                '<?php echo isset($member['date_expire']) ? $member['date_expire'] : ""; ?>',
                                                '<?php echo isset($member['date_expired_coupon']) ? $member['date_expired_coupon'] : ""; ?>'
                                                );" class="button" title="Edit"><i class='fa fa-edit fa-lg' title="Edit"></i></a>
                                            <a onclick="delete_coupon('<?php echo $member['goods_id']->{'$id'}?>');" class="button" title="Delete"><i class='fa fa-times fa-lg' title="Delete"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <div id="members_info" class="paging_info" role="status" aria-live="polite">Showing 1 to <?php echo $members_current_total_page; ?> of <?php echo $members_total; ?> entries</div>
                        <div class="paging_simple_numbers" id="members_paginate">
                                    <span>
                                        <?php echo $total_page; ?>
                                    </span>
                        </div>
                    </div>
                    <div class="hide" id="member_current">1</div>
                    <div class="hide" id="member_order"></div>
                    <div class="hide" id="member_sort"></div>

                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div id="newParam_emptyElement" class="hide invisible">
    <table>
        <tr id="param_custom_param[{{id}}][key]">
            <td><input type="text" name="custom_param[{{id}}][key]" value=""></td>
            <td><input type="text" name="custom_param[{{id}}][value]" value="">
                <input type="checkbox" name="custom_param[{{id}}][hidden]" id="" checked style="margin: 0px 0px 2px 15px;">
                <span>Show in table</span>
                <button type="button" onclick="deleteCustomParam('custom_param[{{id}}][key]','custom_param[{{id}}][value]');" style="background:transparent;border:none;outline:none;float:right;font-size:30px;" ><span class="icon-remove" style="color: red;"></span></button>
            </td>
        </tr>
    </table>
</div>

<div id="formDemoModal" class="modal hide fade"   tabindex="-1" role="dialog" aria-labelledby="formDemoModalLabel"  aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formDemoModalLabel">File demo</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <div class="row-fluid">

                <table  id="example-table" border="2"></table>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary"  onclick='downloadCSV();'><i class="">&nbsp;</i>Download</button>

        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

    </div>
</div>

<div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1>Please Wait</h1>
    </div>
    <div class="modal-body">
        <div class="offset5 ">
            <i class="fa fa-spinner fa-spin fa-5x"></i>
        </div>
    </div>
</div>

<div id="pleaseWaitSpanDiv" class="hide">
    <span id="pleaseWaitSpan"><i class="fa fa-spinner fa-spin"></i></span>
</div>

<div id="formCouponModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formCouponModalLabel" aria-hidden="true"">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="formCouponModalLabel">Coupon</h3>
</div>
<div class="modal-body" style="max-height: 100%;">
    <div class="container-fluid">
        <?php echo form_open(null, array('class' => 'form-horizontal Coupon-form')); ?>
        <table class="form">
            <tr>
                <td><?php echo $this->lang->line('entry_goods_id'); ?>:</td>
                <td>
                    <input type="text" id="coupon_id" name="coupon_id" size="100" value="" disabled/>
                </td>

            </tr>
            <tr>
                <td><?php echo $this->lang->line('entry_batch_name'); ?>:</td>
                <td>
                    <input type="text" id="coupon_batch_name" name="coupon_batch_name" size="100" value="" />
                    <input type="checkbox" id="coupon_check_batch_name" name="coupon_check_batch_name">
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('entry_name'); ?>:</td>
                <td>
                    <input type="text" id="coupon_name" name="coupon_name" size="100" value="" />
                    <input type="checkbox" id="coupon_check_name" name="coupon_check_name">
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('entry_code'); ?>:</td>
                <td>
                    <input type="text" id="coupon_code" name="coupon_code" size="100" value="" />
                    <input type="checkbox" id="coupon_check_code" name="coupon_check_code">
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('entry_start_date'); ?>:</td>
                <td>
                    <input type="text" class="date" id="coupon_date_start" name="coupon_date_start" placeholder="date start reward coupon"value="" size="50" />
                    <input type="checkbox" id="coupon_check_date_start" name="coupon_check_date_start">
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('entry_expire_date'); ?>:</td>
                <td>
                    <input type="text" class="date" id="coupon_date_expire" name="coupon_date_expire" placeholder="date end to reward coupon" value="" size="50" />
                    <input type="checkbox" id="coupon_check_date_expire" name="coupon_check_date_expire">
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('entry_date_expire'); ?>:</td>
                <td>
                    <input type="text" class="date"  id="coupon_date_expired_coupon" name="coupon_date_expired_coupon" placeholder="date to expire coupon" value="" size="50" />
                    <input type="checkbox" id="coupon_check_date_expired_coupon" name="coupon_check_date_expired_coupon">
                </td>
            </tr>
        </table>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="modal-footer">
    <div>
        <p align="center" style="color:red"><?php echo $this->lang->line('warning_check'); ?></p>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" onclick="edit_coupon()" id="coupon-modal-submit"><i class="fa fa-plus">&nbsp;</i>Edit</button>
        <button class="btn btn-primary hide" onclick="edit_filtered_coupon()" id="coupon-modal-filter-submit"><i class="fa fa-plus">&nbsp;</i>Edit All Coupon Matching Filtered</button>
    </div>
</div>
</div>

<div class="modal hide" id="pleaseWaitRewardDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1>Please Wait</h1>
    </div>
    <div class="modal-body">
        <div class="offset5 ">
            <i class="fa fa-spinner fa-spin fa-5x"></i>
        </div>
    </div>
</div>

<div id="formAddCurrency" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formAddCurrencyLabel" aria-hidden="true" style="max-width: 800px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formAddCurrencyLabel">Add currency</h3>
    </div>
    <div class="modal-body" style="height: 300px;">

        <div align="center">
                            <label class="text-info" type="text" style="text-align: center"><h2>Currency  <span class="icon-search"></span></h2></label><br>
                            <select class="chosen-select" multiple id="redeem_add_currency" name="redeem_add_currency" >
                                <?php foreach ($point_list as $br){?>
                                <option value="<?php echo $br['reward_id']?>" data="<?php echo $br['name']?>"><?php echo $br['name'];?></option>
                                <?php }?>
                            </select>
        </div>

    </div>
    <div class="modal-footer">
        <div>
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" id="test">Close</button>
            <button class="btn btn-primary" onclick="listCurrency()" id="listRewardButton">Add</button>
        </div>
    </div>
</div>

<div id="formAddBadge" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formAddBadgeLabel" aria-hidden="true" style="max-width: 800px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formAddBadgeLabel">Add Badge</h3>
    </div>
    <div class="modal-body" style="height: 300px;">
        <div align="center">
            <label class="text-info" type="text" style="text-align: center"><h2>Badge  <span class="icon-search"></span></h2></label><br>
            <select class="chosen-select" multiple id="add_badge" name="add_badge" >
                <?php
                foreach($badge_list as $badge){
                    ?>
                    <option value="<?php echo $badge['badge_id']; ?>" data="<?php echo $badge['name']?>"><?php echo $badge['name'];?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <div>
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" id="test">Close</button>
            <button class="btn btn-primary" onclick="listBadge('<?php echo $badge['image']; ?>')" id="listRewardButton">Add</button>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo base_url(); ?>javascript/import/d3.v3.min.js"></script>
<link id="bootstrap-style2" href="<?php echo base_url();?>javascript/bootstrap/chosen.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/chosen.jquery.min.js"></script>


<script type="text/javascript">
    $("#redeem_add_currency").chosen({max_selected_options: 9});
    var filter_id = document.getElementById("redeem_add_currency_chosen")
    filter_id.style.width = "400px";
    filter_id.children[0].children[0].children[0].style.width = "200px";
</script>

<script type="text/javascript">
    $("#add_badge").chosen({max_selected_options: 9});
    var filter_id = document.getElementById("add_badge_chosen");
    filter_id.style.width = "400px";
    filter_id.children[0].children[0].children[0].style.width = "200px";
</script>

<script type="text/javascript">
    function getById(id) {
        if(document.getElementsByName(id).length){
            return false;
        }else {
            return true;
        }
    }

    function listBadge(Img) {
        var badgeId = $('select[name=\'add_badge\']').val();
        var getInputName = document.getElementById("add_badge");
        var color = "'green', 'yellow', 'blue'" ;
        var URL = "https://images.pbapp.net/"+Img;
        var onError = $('#Img_Badge').attr("onerror");
        for (i = 0; i < badgeId.length; i++) {
            if(getById("reward_badge[" + badgeId[i] + "]")){
                var text = '<div id="'+badgeId[i]+'">\
                              <img height="50" width="50" data-toggle="tooltip" data-placement="left" title="'+getInputName.selectedOptions[i].text+'" src='+URL+' onerror='+onError+' />\
                              <input type="text" id="valueBad_'+badgeId[i]+'" placeholder="'+getInputName.selectedOptions[i].text+'" name="reward_badge['+badgeId[i]+']" class="alternator('+color+');" size="100" value=""/>\
                              <button type="button" onclick="deleteBadge('+"'"+badgeId[i]+"'"+')" style="background: transparent; border: none; outline: none;" ><span class="icon-remove" style="color: red;"></span></button><br/>\
                            </div>';
                $('#redeem_badge_table').append(text);
                $('[data-toggle="tooltip"]').tooltip();
            } else if(!getById("reward_badge[" + badgeId[i] + "]")){
                document.getElementById(badgeId[i]).style.display = 'inline';
            }
        }
        $("#formAddBadge").modal("hide");
    }
    function deleteBadge(badgeId) {
        document.getElementById(badgeId).style.display = 'none';
        document.getElementById("valueBadge_" + badgeId).value = null;
    }
</script>


<script type="text/javascript">
    function getById(id) {
        if(document.getElementsByName(id).length){
            return false;
        }else {
            return true;
        }
    }

    function listCurrency() {
        var reward_id = $('select[name=\'redeem_add_currency\']').val();
        var tagSelect = document.getElementById("redeem_add_currency");
        var color = "'green', 'yellow', 'blue'" ;
        for (idx = 0; idx < reward_id.length; idx++) {
            if(getById("reward_reward[" + reward_id[idx] + "]")){
                var txt = '<div id="'+reward_id[idx]+'">\
                             <span class="label label-primary">'+tagSelect.selectedOptions[idx].text+'</span>\
                             <input id="valueCurrency_'+reward_id [idx]+'" type="number" name="reward_reward['+reward_id[idx]+']" class="alternator('+color+');" size="100" value="">\
                             <button type="button" onclick="deleteCurrency('+"'"+reward_id[idx]+"'"+')" style="background: transparent; border: none; outline: none;" ><span class="icon-remove" style="color: red;"></span></button><br/>\
                           </div>';
                $('#redeem_custom_reward_table').append(txt);
            } else if(!getById("reward_reward[" + reward_id[idx] + "]")){
                document.getElementById(reward_id[idx]).style.display = 'inline';
            }
        }
        $("#formAddCurrency").modal("hide");
    }
    function deleteCurrency(rewardId) {
        var a = $('input[id=\'valueCurrency_'+rewardId+'\']').val();
                document.getElementById(rewardId).style.display = 'none';
                document.getElementById("valueCurrency_" + rewardId).value = null;
    }
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });

    function deleteCustomParam(IdKey,IdValue) {
        document.getElementById("param_"+IdKey).style.display = 'none';
        document.getElementById(IdKey).value = null;
        document.getElementById(IdValue).value = null;
    }
</script>

<script type="text/javascript"><!--
    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
    CKEDITOR.replace('description', {
        filebrowserImageBrowseUrl: 'mediamanager/dialog/'
    });
    $("#clear_filter").hide();
    $("#delete_filtered").hide();
//--></script>
<script type="text/javascript">
    $(function(){
        $('.date').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: "HH:mm:ss"});
    })
</script>
<script type="text/javascript"><!--
    function image_upload(field, thumb) {
        var $mm_Modal = $('#mmModal');

        if ($mm_Modal.length !== 0) $mm_Modal.remove();

        var frameSrc = baseUrlPath + "mediamanager/dialog?field=" + encodeURIComponent(field);
        var mm_modal_str = "";
        mm_modal_str += "<div id=\"mmModal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\">";
        mm_modal_str += " <div class=\"modal-body\">";
        mm_modal_str += "   <iframe src=\"" + frameSrc + "\" style=\"position:absolute; zoom:0.60\" width=\"99.6%\" height=\"99.6%\" frameborder=\"0\"><\/iframe>";
        mm_modal_str += " <\/div>";
        mm_modal_str += "<\/div>";

        $mm_Modal = $(mm_modal_str);
        $('#page-render').append($mm_Modal);

        $mm_Modal.modal('show');

        $mm_Modal.on('hidden', function () {
            var $field = $(field);
            if ($field.attr('value')) {
                $.ajax({
                    url: baseUrlPath + 'mediamanager/image?image=' + encodeURIComponent($field.val()),
                    dataType: 'text',
                    success: function (data) {
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                    }
                });
            }
        });
    }
//--></script>
<script type="text/javascript"><!--
$('#tabs a').tabs();
    $('#tabs a').click(function(){
        if ($(this)[0].hash == "#tab-coupon"){
            $('#submit_button').addClass('hide');
        } else {
            $('#submit_button').removeClass('hide');
        }
    });
    var $waitDialog = $('#pleaseWaitDialog');
    var filter_goods = "";
    var filter_batch = "";
    var filter_coupon_name = "";
    var filter_voucher_code = "";
    var filter_date_start = "";
    var filter_date_end = "";
    var filter_date_expire = "";

    function showCouponModalForm(coupon_id,batch,coupon_name,coupon_code,coupon_date_start,coupon_date_expire,coupon_date_expire_coupon) {
        var substring = "xposss";
        if (coupon_name.indexOf(substring) !== -1){
            coupon_name = coupon_name.replace("xposss", "'");
        }
        document.getElementById('coupon_id').value =  coupon_id;
        document.getElementById('coupon_batch_name').value =  batch;
        document.getElementById('coupon_name').value =  coupon_name;
        document.getElementById('coupon_code').value =  coupon_code;
        document.getElementById('coupon_date_start').value =  coupon_date_start;
        document.getElementById('coupon_date_expire').value =  coupon_date_expire;
        document.getElementById('coupon_date_expired_coupon').value =  coupon_date_expire_coupon;

        document.getElementById('coupon_check_batch_name').checked =  false;
        document.getElementById('coupon_check_name').checked =  false;
        document.getElementById('coupon_check_code').checked =  false;
        document.getElementById('coupon_check_date_start').checked =  false;
        document.getElementById('coupon_check_date_expire').checked =  false;
        document.getElementById('coupon_check_date_expired_coupon').checked =  false;
        $('#formCouponModal').modal('show');
    }

    function edit_coupon() {
        
        var goods_id = $('#coupon_id').val();
        var formData = $('form.Coupon-form').serialize();
        $.ajax({
            type: "POST",
            url: baseUrlPath + "goods/updateGoodsFromAjax/"+goods_id,
            data: formData,
            timeout: 3000,
            beforeSend: function (xhr) {
                $('#formCouponModal').modal('hide');
                $waitDialog.modal('show');
            }
        }).done(function (data) {
            $waitDialog.modal('hide');
            update_table();
        }).fail(function (xhr, textStatus, errorThrown) {
            $waitDialog.modal('hide');
            alert('Edit error: ' + errorThrown + '. Please contact Playbasis!');
        }).always(function () {
            $waitDialog.modal('hide');
        });
        
    }

    function edit_filtered_coupon() {
        var goods_id = $('#coupon_id').val();
        var formData = $('form.Coupon-form').serialize();
        var url = baseUrlPath + "goods/updateGoodsFromAjax/"+goods_id+"?";
        if (filter_goods) {
            url += '&filter_goods=' + encodeURIComponent(filter_goods);
        }
        if (filter_batch) {
            url += '&filter_batch=' + encodeURIComponent(filter_batch);
        }
        if (filter_coupon_name) {
            url += '&filter_coupon_name=' + encodeURIComponent(filter_coupon_name);
        }
        if (filter_voucher_code) {
            url += '&filter_voucher_code=' + encodeURIComponent(filter_voucher_code);
        }
        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }
        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }
        if (filter_date_expire) {
            url += '&filter_date_expire=' + encodeURIComponent(filter_date_expire);
        }
        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            timeout: 3000,
            beforeSend: function (xhr) {
                $('#formCouponModal').modal('hide');
                $waitDialog.modal('show');
            }
        }).done(function (data) {
            $waitDialog.modal('hide');
            update_table();
        }).fail(function (xhr, textStatus, errorThrown) {
            $waitDialog.modal('hide');
            alert('Edit error: ' + errorThrown + '. Please contact Playbasis!');
        }).always(function () {
            $waitDialog.modal('hide');
        });
    }

    function delete_coupon(goods_id) {
        $.ajax({
            type: "POST",
            url: baseUrlPath + "goods/deleteGoodsFromAjax/"+goods_id,
            data:  {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
            timeout: 3000,
            beforeSend: function (xhr) {
                $waitDialog.modal('show');
            }
        }).done(function (data) {
            $waitDialog.modal('hide');
            if($.parseJSON(data).status == 'deleted'){
                window.location= baseUrlPath + "goods";
            }
            if($.parseJSON(data).status == 'success'){
                update_table();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            $waitDialog.modal('hide');
            alert('Delete error: ' + errorThrown + '. Please contact Playbasis!');
        }).always(function () {
            $waitDialog.modal('hide');
        });

    }

    function delete_filtered_coupon(goods_id) {
        var url = baseUrlPath + "goods/deleteGoodsFromAjax/"+goods_id+"?";
        if (filter_goods) {
            url += '&filter_goods=' + encodeURIComponent(filter_goods);
        }
        if (filter_batch) {
            url += '&filter_batch=' + encodeURIComponent(filter_batch);
        }
        if (filter_coupon_name) {
            url += '&filter_coupon_name=' + encodeURIComponent(filter_coupon_name);
        }
        if (filter_voucher_code) {
            url += '&filter_voucher_code=' + encodeURIComponent(filter_voucher_code);
        }
        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }
        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }
        if (filter_date_expire) {
            url += '&filter_date_expire=' + encodeURIComponent(filter_date_expire);
        }
        $.ajax({
            type: "POST",
            url: url,
            data:  {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
            timeout: 3000,
            beforeSend: function (xhr) {
                $waitDialog.modal('show');
            }
        }).done(function (data) {
            $waitDialog.modal('hide');
            if($.parseJSON(data).status == 'deleted'){
                window.location= baseUrlPath + "goods";
            }
            if($.parseJSON(data).status == 'success'){
                update_table();
            }

        }).fail(function (xhr, textStatus, errorThrown) {
            $waitDialog.modal('hide');
            alert('Delete error: ' + errorThrown + '. Please contact Playbasis!');
        }).always(function () {
            $waitDialog.modal('hide');
        });

    }

    function filter() {
        var url = baseUrlPath+"goods/getGoodsGroupAjax/<?php echo $goods_id; ?>?";

        filter_goods = $('input[name=\'filter_goods\']').attr('value');
        filter_batch = $('select[name=\'filter_batch\']').attr('value');
        filter_coupon_name = $('input[name=\'filter_coupon_name\']').attr('value');
        filter_voucher_code = $('input[name=\'filter_voucher_code\']').attr('value');
        filter_date_start = $('select[name=\'filter_date_start\']').attr('value');
        filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
        filter_date_expire = $('input[name=\'filter_date_expire\']').attr('value');


        if (filter_goods) {
            url += '&filter_goods=' + encodeURIComponent(filter_goods);
        }
        if (filter_batch) {
            url += '&filter_batch=' + encodeURIComponent(filter_batch);
        }
        if (filter_coupon_name) {
            url += '&filter_coupon_name=' + encodeURIComponent(filter_coupon_name);
        }
        if (filter_voucher_code) {
            url += '&filter_voucher_code=' + encodeURIComponent(filter_voucher_code);
        }
        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }
        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }
        if (filter_date_expire) {
            url += '&filter_date_expire=' + encodeURIComponent(filter_date_expire);
        }

        $('.member_wrapper').append('<div class="backgrund-load"><div class="loading-img"><img src="<?php echo base_url();?>image/white_loading.gif" /></div></div>');
        $(".backgrund-load").css({"width": $("#members").width(), "height": $("#members").height(), "top": $("#members").height()*(-1)});
        $(".loading-img").css({"top": ($("#members").height()/3)});

        $.ajax({
            type: "GET",
            url: url,
            data: { page: 1 },
            dataType: "html"
        }).done(function( data ) {
            $('.member_wrapper').html(data);

            pagination_click();
            if(filter_goods || filter_batch || filter_coupon_name || filter_voucher_code || filter_date_start || filter_date_end || filter_date_expire){
                $("#clear_filter").show();
                $("#coupon-modal-filter-submit").show();
                $("#delete_filtered").show();
            } else {
                $("#clear_filter").hide();
                $("#coupon-modal-filter-submit").hide();
                $("#delete_filtered").hide();
            }
        });

    }

    function uploadCoupon(){
        var file = document.getElementById('file').files[0];
        var paras = document.getElementsByClassName('messages');
        while(paras[0]) {
            paras[0].parentNode.removeChild(paras[0]);
        }

        if(file){
            if(file.size < 4194304) { // 4MB (this size is in bytes)
                var formData = new FormData($('#form_coupon')[0]);
                $.ajax({
                    type: "POST",
                    url: baseUrlPath + "goods/upload/<?php echo $goods_id; ?>",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function (xhr) {
                        $waitDialog.modal('show');
                    }
                })
                .done(function (data) {
                    $(".content").prepend('<div class="content messages half-width"><div class="success"><?php echo $this->lang->line('text_uploaded'); ?></div> </div>');
                    $waitDialog.modal('hide');
                    update_table();
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
                    $waitDialog.modal('hide');
                })
            } else {
                //Prevent default and display error
                $(".content").prepend('<div class="content messages half-width"><div class="warning"><?php echo $this->lang->line('error_file_too_large'); ?></div> </div>');
                $waitDialog.modal('hide');
            }
        }else{
            $(".content").prepend('<div class="content messages half-width"><div class="warning"><?php echo $this->lang->line('error_file'); ?></div> </div>');
            $waitDialog.modal('hide');
        }
    };

    var $organizeParent = $("#organize_id");

    function organizeFormatResult(organize) {
        return '<div class="row-fluid">' +
            '<div>' + organize.name /*+
            '<small class="text-muted">&nbsp;(' + organize.description +
            ')</small></div></div>'*/;
    }

    function organizeFormatSelection(organize) {
        return organize.name;
    }
    var globalNewIndex = <?php echo isset($custom_param) ?count($custom_param):0; ?>;
    function createParameterRow(numToCreate){
        numToCreate = typeof numToCreate !== 'undefined' ? numToCreate : 1;

        if ($.isNumeric(numToCreate) && numToCreate > 0) {
            for (idx = 0; idx < numToCreate; idx++) {
                var tableRowHTML = $('#newParam_emptyElement').find('tbody').html();
                var newIndex = globalNewIndex;

                tableRowHTML = tableRowHTML.replace(new RegExp('{{id}}', 'g'), newIndex);

                $('#new-branches-table').find('tbody').append(tableRowHTML);

                globalNewIndex++;
            }
            //$(":not(div .bootstrap-switch-container)>input[name^='newBranches'][name$='[status]']:not([name*='id'])").bootstrapSwitch();
        }
    };

$(document).ready(function(){
    $(".point").hide();
    $(".badges").hide();
    $(".rewards").hide();
    $("#point-entry").on('click', function() {$(".point").toggle()});
    $("#badge-entry").on('click', function() {$(".badges").toggle()});
    $("#reward-entry").on('click', function() {$(".rewards").toggle()});

    $organizeParent.select2({
        placeholder: "Search for an organize name",
        allowClear: false,
        minimumInputLength: 0,
        id: function (data) {
            return data._id;   
        },
        ajax: {
            url: baseUrlPath + "store_org/organize/",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    search: term, // search term
                };
            },
            results: function (data, page) {
                return {results: data.rows};
            },
            cache: true
        },
        initSelection: function (element, callback) {
            var id = $(element).val();
            if (id !== "") {
                $.ajax(baseUrlPath + "store_org/organize/" + id, {
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $organizeParent
                            .select2('enable', false);
                    }
                }).done(function (data) {
                    if (typeof data != "undefined")
                        callback(data);
                }).always(function () {
                    $organizeParent
                        .select2('enable', true);
                });
            }
        },
        formatResult: organizeFormatResult,
        formatSelection: organizeFormatSelection,

    });

    $("#whitelist_enable").change(function(e){
        e.preventDefault();
        if (document.getElementById('whitelist_enable').checked) {
            //alert("checked");
            document.getElementById("whitelist_file").disabled = false;
            document.getElementById("whitelist_file_name").style.display = "inline";
        } else {
            document.getElementById("whitelist_file").value = null;
            document.getElementById("whitelist_file").disabled = true;
            document.getElementById("whitelist_file_name").style.display = "none";

        }
    });

    if (document.getElementById('whitelist_enable').checked) {
        //alert("checked");
        document.getElementById("whitelist_file").disabled = false;
        document.getElementById("whitelist_file_name").style.display = "inline";
    } else {
        document.getElementById("whitelist_file").disabled = true;
        document.getElementById("whitelist_file_name").style.display = "none";
    }

    <?php if($org_status){?>
        $("#global_goods").change(function(e){
            e.preventDefault();
            if (document.getElementById('global_goods').checked) {
                //alert("checked");
                $organizeParent.select2('enable', false);
                $organizeParent.select2('val', null);
                document.getElementById("organize_role").value = null;
                document.getElementById("organize_role").disabled = true;
            } else {
                $organizeParent.select2('enable', true);
                document.getElementById("organize_role").disabled = false;
            }
        });

        if (document.getElementById('global_goods').checked) {
            //alert("checked");
            $organizeParent.select2('enable', false);
            document.getElementById("organize_role").disabled = true;
        } else {
            $organizeParent.select2('enable', true);
            document.getElementById("organize_role").disabled = false;
        }
    <?php } ?>
});

//--></script>



<?php if(!$client_id && !$name){?>
<script type="text/javascript"><!--

    $(document).ready(function(){
        $("#client-choose").change(function() {
            var c = $(this).val();
            $("#badge-panel").html("");
            $("#reward-panel").html("");
            if(c != "all_clients"){
                $.ajax({
                    url: baseUrlPath+'goods/getBadgeForGoods',
                    data: { client_id: c },
                    context: document.body
                }).done(function(data) {
                    $("#badge-panel").html(data);
                    $(".badges").hide();
                });
                $.ajax({
                    url: baseUrlPath+'goods/getCustomForGoods',
                    data: { client_id: c },
                    context: document.body
                }).done(function(data) {
                    $("#reward-panel").html(data);
                    $(".rewards").hide();
                });
            }
        });

    });
    
    //--></script>
<?php } ?>
<?php if ($is_group) { ?>
<script>
    function fromcheck(){
        var file = document.getElementById('file').files[0];

        if(file){
            if(file.size < 4194304) { // 4MB (this size is in bytes)
                //Submit form
                $('#form').submit();
            } else {
                //Prevent default and display error
                $(".content").prepend('<div class="content messages half-width"><div class="warning"><?php echo $this->lang->line('error_file_too_large'); ?></div> </div>');
            }
        }else{
            <?php if ($is_import) { ?>
            $(".content").prepend('<div class="content messages half-width"><div class="warning"><?php echo $this->lang->line('error_file'); ?></div> </div>');
            <?php } else { ?>
            $('#form').submit();
            <?php } ?>
        }
    }

    pagination_click();

    function pagination_click(){
        $('.paginate_button').click(function(){
            var url = baseUrlPath+"goods/getGoodsGroupAjax/<?php echo $goods_id; ?>?";

            if (filter_goods) {
                url += '&filter_goods=' + encodeURIComponent(filter_goods);
            }
            if (filter_batch) {
                url += '&filter_batch=' + encodeURIComponent(filter_batch);
            }
            if (filter_coupon_name) {
                url += '&filter_coupon_name=' + encodeURIComponent(filter_coupon_name);
            }
            if (filter_voucher_code) {
                url += '&filter_voucher_code=' + encodeURIComponent(filter_voucher_code);
            }
            if (filter_date_start) {
                url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
            }
            if (filter_date_end) {
                url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
            }
            if (filter_date_expire) {
                url += '&filter_date_expire=' + encodeURIComponent(filter_date_expire);
            }
            var page = $(this).attr("data-page");

            $('.member_wrapper').append('<div class="backgrund-load"><div class="loading-img"><img src="<?php echo base_url();?>image/white_loading.gif" /></div></div>');

            $(".backgrund-load").css({"width": $("#members").width(), "height": $("#members").height(), "top": $("#members").height()*(-1)});
            $(".loading-img").css({"top": ($("#members").height()/3)});

            $.ajax({
                type: "GET",
                url: url,
                data: { page: page },
                dataType: "html"
            }).done(function( data ) {
                $('.member_wrapper').html(data);

                pagination_click();
                if(filter_goods || filter_batch || filter_coupon_name || filter_voucher_code || filter_date_start || filter_date_end || filter_date_expire){
                    $("#clear_filter").show();
                    $("#coupon-modal-filter-submit").show();
                    $("#delete_filtered").show();
                } else {
                    $("#clear_filter").hide();
                    $("#coupon-modal-filter-submit").hide();
                    $("#delete_filtered").hide();
                }
            });

        });
    }

    function update_table(){

        $('.member_wrapper').append('<div class="backgrund-load"><div class="loading-img"><img src="<?php echo base_url();?>image/white_loading.gif" /></div></div>');

        $(".backgrund-load").css({"width": $("#members").width(), "height": $("#members").height(), "top": $("#members").height()*(-1)});
        $(".loading-img").css({"top": ($("#members").height()/3)});

        $.ajax({
            type: "GET",
            url: baseUrlPath+"goods/getGoodsGroupAjax/<?php echo $goods_id; ?>",
            data: { page: 1 },
            dataType: "html"
        }).done(function( data ) {
            $('.member_wrapper').html(data);
            pagination_click();
            filter_goods = "";
            filter_batch = "";
            filter_coupon_name = "";
            filter_voucher_code = "";
            filter_date_start = "";
            filter_date_end = "";
            filter_date_expire = "";
            $("#clear_filter").hide();
            $("#coupon-modal-filter-submit").hide();
            $("#delete_filtered").hide();
        });

    }

    function showDemo(){
        $('#formDemoModalLabel').html("File demo for goods group importing");
        filename = "goods-example.csv";

        $("#example-table").empty();

        d3.text("<?php echo base_url();?>image/import/"+filename, function(data) {
            var parsedCSV = d3.csv.parseRows(data);
            csvData = data;

            var container = d3.select('#example-table')

                .selectAll("tr")
                .data(parsedCSV).enter()
                .append("tr")

                .selectAll("td")
                .data(function(d) { return d; }).enter()
                .append("td")
                .text(function(d) { return d; });
        });


        $('#formDemoModal').modal('show');
    }

    function downloadCSV() {
        var data, link;

        var csv = csvData;
        if (csv == null) return;

        if (!csv.match(/^data:text\/csv/i)) {
            csv = 'data:text/csv;charset=utf-8,' + csv;
        }
        data = encodeURI(csv);

        link = document.createElement('a');
        link.setAttribute('href', data);
        link.setAttribute('download', filename);
        link.click();
    }

    function downloadCoupon() {
        var url = baseUrlPath+"goods/downloadGoodsGroup/<?php echo $goods_id; ?>?";

        if (filter_goods) {
            url += '&filter_goods=' + encodeURIComponent(filter_goods);
        }
        if (filter_batch) {
            url += '&filter_batch=' + encodeURIComponent(filter_batch);
        }
        if (filter_coupon_name) {
            url += '&filter_coupon_name=' + encodeURIComponent(filter_coupon_name);
        }
        if (filter_voucher_code) {
            url += '&filter_voucher_code=' + encodeURIComponent(filter_voucher_code);
        }
        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }
        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }
        if (filter_date_expire) {
            url += '&filter_date_expire=' + encodeURIComponent(filter_date_expire);
        }
        location = url;

    }
</script>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });
    <?php if(isset($distinct_id)) {?>
    function downloadFile() {
        distinct_id = "<?php echo $distinct_id?>";
        location = baseUrlPath + 'goods/getWhitelistFile?distinct_id=' + distinct_id;
    }
    <?php }?>
</script>