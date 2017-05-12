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
                <button class="btn btn-info" onclick="<?php if($is_group){ ?> fromcheck() <?php }else{ ?> $('#form').submit(); <?php } ?>" type="button"><?php echo $this->lang->line('button_save'); ?></button>
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
                            <?php if ($client_id && $is_group) { ?>
                            <tr>
                                <td><?php if ($is_import) { ?><span class="required">*</span>  <?php } ?><?php echo $this->lang->line('entry_file'); ?>:</td>
                                <td><input id="file" type="file" name="file" size="100" /></td>
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
                                    <input type="text" name="code" value="<?php echo isset($code) ? $code : set_value('code'); ?>" size="5" class="tooltips" data-placement="right" title="Code for reddem or do something"/>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($client_id) { ?>
                            <tr>
                                <td><?php echo $this->lang->line('entry_tags'); ?>:</td>
                                <td>
                                    <input type="text" class="tags" name="tags" value="<?php echo isset($tags) ? implode(',',$tags) : set_value('tags'); ?>" size="5" class="tooltips" data-placement="right" title="Tag(s) input"/>
                                </td>
                            </tr>
                            <?php } ?>
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
                                <td><span class="required">*</span> <?php echo $this->lang->line('column_param'); ?>:</td>
                                <td>
                                    <div class="row-fluid">
                                        <table class="table table-bordered" id="new-branches-table">
                                            <thead>
                                            <th><?php echo $this->lang->line('column_key'); ?></th>
                                            <th><?php echo $this->lang->line('column_value'); ?></th>
                                            </thead>
                                            <tbody>
                                            <?php if(isset($custom_param) && is_array($custom_param) ){?>
                                                <?php foreach($custom_param as $key => $param){?>
                                                    <tr>
                                                        <td><input type="text" name="<?php echo "custom_param[".$key."][key]" ?>"
                                                                   value="<?php echo isset($param['key']) ? $param['key']: set_value('parameter'); ?>"
                                                        </td>
                                                        <td><input type="text" name="<?php echo "custom_param[".$key."][value]" ?>"
                                                                   value="<?php echo isset($param['value']) ? $param['value']: set_value('parameter'); ?>"
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            <?php }?>
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
                        <?php if (isset($members)) { ?>
                            <div class="member_wrapper">
                                <table id="members" class="display form no-footer" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('entry_system_id'); ?></th>
                                        <th><?php echo $this->lang->line('entry_name'); ?></th>
                                        <th><?php echo $this->lang->line('entry_code'); ?></th>
                                        <th><?php echo $this->lang->line('entry_expire_date_coupon'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($members)){
                                        $count = 0;
                                        foreach ($members as $member) { ?>
                                            <tr class="<?php echo (++$count%2 ? "odd" : "even") ?>">
                                                <td><?php echo $member['goods_id']->{'$id'}; ?></td>
                                                <td><?php echo $member['name']; ?></td>
                                                <td><?php echo isset($member['code']) ? $member['code'] : ''; ?></td>
                                                <td align="center"><?php echo isset($member['date_expired_coupon']) ? $member['date_expired_coupon'] : ''; ?></td>
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
                                <div class="well" style="max-width: 400px;">
                                    <button id="point-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>
                                    <div class="point">
                                        <div class="goods-panel">
                                            <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                            <input type="text" name="reward_point" size="100" class="orange tooltips" value="<?php echo isset($reward_point) ? $reward_point :  set_value('reward_point'); ?>" data-placement="right" title="The number of Points needed to redeem the Goods"/>
                                        </div>
                                    </div>
                                    <div id="badge-panel">
                                    <?php
                                    if($badge_list){
                                    ?>
                                        <br>
                                        <button id="badge-entry" type="button" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>
                                        <div class="badges">
                                            <div class="goods-panel">
                                                <?php
                                                foreach($badge_list as $badge){
                                                    ?>
                                                    <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                    <input type="text" name="reward_badge[<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?> tooltips" size="100" value="<?php if(set_value('reward_badge['.$badge['badge_id'].']')){
                                                        echo set_value('reward_badge['.$badge['badge_id'].']');
                                                    }else{
                                                        if($reward_badge){
                                                            foreach($reward_badge as $rbk => $rb){
                                                                if($rbk == $badge['badge_id']){
                                                                    echo $rb;
                                                                    continue;
                                                                }
                                                            }
                                                        }
                                                    } ?>" data-placement="right" title="The number of Badges needed to redeem the Goods"/><br/>
                                                <?php
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
                                        <button id="reward-entry" type="button" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_custom_point'); ?></button>
                                        <div class="rewards">
                                            <div class="goods-panel">
                                                <?php
                                                foreach($point_list as $point){
                                                    ?>
                                                    <?php echo $point['name']; ?>
                                                    <input type="text" name="reward_reward[<?php echo $point['reward_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="<?php if(set_value('reward_reward['.$point['reward_id'].']')){
                                                        echo set_value('reward_reward['.$point['reward_id'].']');
                                                    }else{
                                                        if($reward_reward){
                                                            foreach($reward_reward as $rbk => $rb){
                                                                if($rbk == $point['reward_id']){
                                                                    echo $rb;
                                                                    continue;
                                                                }
                                                            }
                                                        }
                                                    } ?>" /><br/>
                                                <?php
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
        </div>
    </div>
</div>

<div id="newParam_emptyElement" class="hide invisible">
    <table>
        <tr>
            <td><input type="text" name="custom_param[{{id}}][key]" value=""></td>
            <td><input type="text" name="custom_param[{{id}}][value]" value=""></td>
        </tr>
    </table>
</div>


<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>

<script type="text/javascript"><!--
    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
    CKEDITOR.replace('description', {
        filebrowserImageBrowseUrl: 'mediamanager/dialog/'
    });
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
            if(file.size < 2097152) { // 2MB (this size is in bytes)
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
            var page = $(this).attr("data-page");

            $('.member_wrapper').append('<div class="backgrund-load"><div class="loading-img"><img src="<?php echo base_url();?>image/white_loading.gif" /></div></div>');

            $(".backgrund-load").css({"width": $("#members").width(), "height": $("#members").height(), "top": $("#members").height()*(-1)});
            $(".loading-img").css({"top": ($("#members").height()/3)});

            $.ajax({
                type: "GET",
                url: baseUrlPath+"goods/getGoodsGroupAjax/<?php echo $goods_id; ?>",
                data: { page: page },
                dataType: "html"
            }).done(function( data ) {
                $('.member_wrapper').html(data);

                pagination_click();
            });

        });
    }

</script>
<?php } ?>

<script type="text/javascript">

    function downloadFile(){
        distinct_id = "<?php echo $distinct_id?>";
        location = baseUrlPath+'goods/getWhitelistFile?distinct_id='+distinct_id;

    }

    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

</script>
