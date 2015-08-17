<?php
function find_template($data, $type, $template_id)
{
    if (isset($data['feedbacks']) && array_key_exists($type, $data['feedbacks'])) {
        foreach ($data['feedbacks'][$type] as $_template_id => $val) {
            if ($_template_id == $template_id) {
                return $val;
            }
        }
    }
    return false;
} ?>
<link type='text/css' rel="stylesheet" href="<?php echo base_url(); ?>stylesheet/luckydraw/style.css">
<link type='text/css' rel='stylesheet'
      href='<?php echo base_url(); ?>stylesheet/custom/jquery-ui-timepicker-addon.css'/>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/md5.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/mongoid.js"></script>

<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form_luckydraw').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'luckydraw'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="#tab-luckydraw-info"><?php echo $this->lang->line('tab_info'); ?></a>
                <a href="#tab-luckydraw-rewards"><?php echo $this->lang->line('tab_rewards'); ?></a>
            </div>

            <?php if (validation_errors() || isset($message)) { ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">', '</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
            <?php } ?>
            <?php
            if (isset($luckydraw) && empty($luckydraw)) {
                unset($luckydraw);
            }

            $qndata = array(
                "name" => "name",
                "id" => 'luckydraw_name',
                "value" => isset($luckydraw) ? $luckydraw["name"] : '',
                "placeholder" => $this->lang->line('luckydraw_name'),
                "class" => "form-control"
            );
            $qddata = array(
                'name' => 'description',
                'id' => 'luckydraw_desc',
                'value' => isset($luckydraw) ? $luckydraw['description'] : '',
                "placeholder" => $this->lang->line('luckydraw_description'),
                "class" => "form-control",
                "rows" => 3
            );


            $attributes = array('id' => 'form_luckydraw');
            if (isset($luckydraw['_id'])) {
                echo form_open_multipart('luckydraw/edit/' . $luckydraw['_id'] . "", $attributes);
            } else {
                echo form_open_multipart('luckydraw/insert', $attributes);
            }
            ?>
            <div id="tab-luckydraw-info">
                <div class="span12">
                    <table class="form">
                        <tbody>
                        <tr>
                            <td>
                                <span
                                    class="required">* </span><?php echo $this->lang->line('entry_name'); ?>
                                :
                            </td>
                            <td>
                                <?php
                                echo form_input($qndata);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('entry_desc'); ?> :
                            </td>
                            <td>
                                <?php
                                echo form_textarea($qddata);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span
                                    class="required">* </span><?php echo $this->lang->line('entry_date_start'); ?>
                                :
                            </td>
                            <td>
                                <input type="text" class="date" name="date_start" id="date_start"
                                       value="<?php echo isset($luckydraw) && isset($luckydraw['date_start']) && $luckydraw['date_start'] ? date('Y-m-d H:i',
                                           strtotime(datetimeMongotoReadable($luckydraw['date_start']))) : ''; ?>"
                                       size="50"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span
                                    class="required">* </span><?php echo $this->lang->line('entry_date_end'); ?>
                                :
                            </td>
                            <td>
                                <input type="text" class="date" name="date_end" id="date_end"
                                       value="<?php echo isset($luckydraw) && isset($luckydraw['date_end']) && $luckydraw['date_end'] ? date('Y-m-d H:i',
                                           strtotime(datetimeMongotoReadable($luckydraw['date_end']))) : ''; ?>"
                                       size="50"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="required">* </span><?php echo $this->lang->line('entry_part_method'); ?> :
                            </td>
                            <td>
                                <label class="radio" style="display: block;">
                                    <input type="radio" name="participate_method" id="part_method_ask"
                                           value="ask_to_join" <?php echo isset($luckydraw) && isset($luckydraw['participate_method']) && $luckydraw['participate_method'] ? 'checked' : ''; ?>>
                                    <?php echo $this->lang->line('entry_part_method_ask'); ?>
                                </label>
                                <label class="radio" style="display: block;">
                                    <input type="radio" name="participate_method" id="part_method_active"
                                           value="active_users_only" <?php echo isset($luckydraw) && isset($luckydraw['participate_method']) && $luckydraw['participate_method'] == false ? 'checked' : ''; ?>>
                                    <?php echo $this->lang->line('entry_part_method_active'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('entry_banner_upload'); ?> :
                            </td>
                            <td>
                                <?php echo $this->lang->line('entry_banner_advertise'); ?><button class="btn btn-small" type="button"><?php echo $this->lang->line('entry_banner_upload_button'); ?></button>
                                <?php echo $this->lang->line('entry_banner_winner'); ?><button class="btn btn-small" type="button"><?php echo $this->lang->line('entry_banner_upload_button'); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-luckydraw-rewards">
                <div class="reward-head-wrapper">
                    <a href="javascript:void(0)" class="btn open-reward-btn btn-lg">Open All</a>
                    <a href="javascript:void(0)" class="btn close-reward-btn btn-lg">Close All</a>
                    <a href="javascript:void(0)" class="btn btn-primary add-reward-btn btn-lg">Add Reward</a>
                </div>

                <div class="rewards-wrapper">
                    <?php
                    if (isset($luckydraw['rewards']) && $luckydraw['rewards']) {
                        foreach ($luckydraw['rewards'] as $reward) {

                            // convert from mongoid obj to just id
                            $reward['ld_reward_id'] = $reward['ld_reward_id'] . "";

                            $custom_user_set = array();
                            $badge_user_set = array();
                            $point_user_set = array();
                            $exp_user_set = array();

                            if(isset($reward["details"])){
                                foreach($reward["details"] as $reward_key => $reward_value){
                                    if($reward_key == "custom"){
                                        $custom_user_set = $reward_value;
                                    }
                                    if($reward_key == "badge"){
                                        $badge_user_set = $reward_value;
                                    }
                                    if($reward_key == "exp"){
                                        $exp_user_set = $reward_value;
                                    }
                                    if($reward_key == "point"){
                                        $point_user_set = $reward_value;
                                    }
                                }
                            }

                            $reward_ranking_data = array(
                                "name" => "luckydraw[rewards][" . $reward['ld_reward_id'] . "][ranking]",
                                "id" => 'luckydraw_reward_ranking',
                                "value" => isset($reward["ranking"]) ? $reward["ranking"] : '',
                                "placeholder" => $this->lang->line('entry_reward_ranking'),
                                "class" => "form-control"
                            );

                            $reward_quantity_data = array(
                                "name" => "luckydraw[rewards][" . $reward['ld_reward_id'] . "][qty]",
                                "id" => 'luckydraw_reward_quantity',
                                "value" => isset($reward["qty"]) ? $reward["qty"] : '',
                                "placeholder" => $this->lang->line('entry_reward_quantity'),
                                "class" => "form-control"
                            );
                            ?>
                            <div class="reward-item-wrapper" data-ld-reward-id="<?php echo $reward['ld_reward_id']; ?>">
                                <div class="box-header box-reward-header overflow-visible">
                                    <h2><img src="<?php echo base_url(); ?>image/default-image.png" width="50">Reward
                                        for ranking# <?php echo $reward['ranking']; ?>
                                    </h2>

                                    <div class="box-icon">
                                        <a href="javascript:void(0)" class="btn btn-danger right remove-reward-btn">Delete </a>
                                        <span class="break"></span>
                                        <a href="javaScript:void(0)"><i class="icon-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-content clearfix" style="display: none;">
                                    <div class="span6">
                                        <table class="form">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?php echo $this->lang->line("entry_reward_ranking") ?> :
                                                </td>
                                                <td>
                                                    <?php echo form_input($reward_ranking_data); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?php echo $this->lang->line("entry_reward_quantity") ?> :
                                                </td>
                                                <td>
                                                    <?php echo form_input($reward_quantity_data); ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="span6">
                                        <table class="form">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <p class="text-center">
                                                        <?php echo $this->lang->line('entry_rewards'); ?>
                                                    </p>

                                                    <div class="reward">
                                                        <div class="btn-group-wrap" style="text-align: center;">
                                                            <div class="btn-group center">
                                                                <button id="exp-entry"
                                                                        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_exp'); ?></button>
                                                                <button id="point-entry"
                                                                        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_point'); ?></button>
                                                                <button id="badge-entry"
                                                                        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_badge'); ?></button>
                                                                <button id="reward-entry"
                                                                        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_custom_point'); ?></button>
                                                            </div>
                                                        </div>
                                                        <div class="exp" id="exp">
                                                            <div class="row-fluid"
                                                                 style="padding-top: 10px; padding-bottom: 10px;">
                                                                <div class="span2 offset1">
                                                                        <span
                                                                            class="label label-primary"><?php echo $this->lang->line('entry_exp'); ?></span>
                                                                </div>
                                                                <div class="span6">
                                                                    <input type="text"
                                                                           name="luckydraw[rewards][<?php echo $reward['ld_reward_id']; ?>][details][exp][value]"
                                                                           class="orange tooltips"
                                                                           value="<?php echo $exp_user_set? $exp_user_set["value"] : ''; ?>" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="point" id="point">
                                                            <div class="row-fluid"
                                                                 style="padding-top: 10px; padding-bottom: 10px">
                                                                <div class="span2 offset1">
                                                                    <span
                                                                        class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                                                </div>
                                                                <div class="span6">
                                                                    <input type="text"
                                                                           name="luckydraw[rewards][<?php echo $reward['ld_reward_id']; ?>][details][point][value]"
                                                                           class="orange tooltips"
                                                                           value="<?php echo $point_user_set ? $point_user_set["value"] : ''; ?>" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="badges" id="badges">
                                                            <?php
                                                            if ($badge_list) {
                                                                foreach ($badge_list as $badge) {
                                                                    ?>
                                                                    <div class="row-fluid"
                                                                         style="padding-top: 10px; padding-bottom: 10px;">
                                                                        <div class="span2 offset1">
                                                                            <img height="50" width="50"
                                                                                 src="<?php echo S3_IMAGE . $badge['image']; ?>"
                                                                                 onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                                                                        </div>
                                                                        <?php
                                                                        $user_b = "";
                                                                        foreach($badge_user_set as $b){
                                                                            if($b["badge_id"] == $badge['badge_id']){
                                                                                $user_b = $b["badge_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <div class="span6">
                                                                            <input type="text"
                                                                                   name="luckydraw[rewards][<?php echo $reward['ld_reward_id']; ?>][details][badge][<?php echo $badge['badge_id']; ?>]"
                                                                                   class="<?php echo alternator('green',
                                                                                       'yellow',
                                                                                       'blue'); ?> tooltips"
                                                                                   size="100"
                                                                                   value="<?php echo $user_b; ?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                        <div id="rewards" class="rewards">
                                                            <?php if ($point_list) { ?>
                                                                <div class="row-fluid"
                                                                     style="padding-top: 10px; padding-bottom: 10px">
                                                                    <div class="span2 offset1">
                                                                        <?php
                                                                        foreach ($point_list as $point) {
                                                                        ?>
                                                                        <span
                                                                            class="label label-primary"><?php echo $point['name']; ?></span>
                                                                        <?php
                                                                        $user_c = "";
                                                                        foreach($custom_user_set as $c){
                                                                            if($c["custom_id"] == $point['reward_id']){
                                                                                $user_c = $c["custom_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="span6">
                                                                        <input type="text"
                                                                               name="luckydraw[rewards][<?php echo $reward['ld_reward_id']; ?>][details][custom][<?php echo $point['reward_id']; ?>] ?>]"
                                                                               class="<?php echo alternator('green',
                                                                                   'yellow', 'blue'); ?>" size="100"
                                                                               value="<?php echo $user_c; ?>"/>
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/luckydraw/luckydraw.js"></script>
<script type="text/javascript"
        src="<?php echo base_url(); ?>javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/rule_editor/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
        var startDateTextBox = $('#date_start');
        var endDateTextBox = $('#date_end');

        startDateTextBox.datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat: 'HH:mm',
            addSliderAccess: true,
            sliderAccessArgs: {touchonly: false},
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datetimepicker('getDate');
                    var testEndDate = endDateTextBox.datetimepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datetimepicker('setDate', testStartDate);
                }
                else {
                    endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate'));
            }
        });
        endDateTextBox.datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat: 'HH:mm',
            addSliderAccess: true,
            sliderAccessArgs: {touchonly: false},
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datetimepicker('getDate');
                    var testEndDate = endDateTextBox.datetimepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datetimepicker('setDate', testEndDate);
                }
                else {
                    startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate'));
            }
        });

        $(".exp").hide();
        $(".point").hide();
        $(".badges").hide();
        $(".rewards").hide();
        $("#exp-entry").live('click', function (e) {
            e.preventDefault();
            $(this).closest(".reward").find(".exp").toggle();
        });
        $("#point-entry").live('click', function (e) {
            e.preventDefault();
            $(this).closest(".reward").find(".point").toggle();
        });
        $("#badge-entry").live('click', function (e) {
            e.preventDefault();
            $(this).closest(".reward").find(".badges").toggle();
        });
        $("#reward-entry").live('click', function (e) {
            e.preventDefault();
            $(this).closest(".reward").find(".rewards").toggle();
        });

        $('#tabs a').tabs();

        init_sub_remove_event('.remove-reward-btn', 2);

        init_reward_event();

        $('.open-reward-btn').click(function () {
            $('.reward-item-wrapper>.box-content').show();
        });
        $('.close-reward-btn').click(function () {
            $('.reward-item-wrapper>.box-content').hide();
        });

    });

    function init_sub_remove_event(obj_click, num_of_parent) {
        $(obj_click).unbind().bind('click', function (data) {

            var $target = $(this);

            for (var i = 0; i < num_of_parent; i++) {
                $target = $target.parent();
            }

            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
            }
        });
    }

    $('.add-reward-btn').click(function(){
        countRewards = mongoIDjs();

        var rewardHtml = '<div class="reward-item-wrapper" data-ld-reward-id="' + countRewards + '">\
            <div class="box-header box-reward-header overflow-visible">\
            <h2><img src="<?php echo base_url(); ?>image/default-image.png" width="50">Reward</h2>\
            <div class="box-icon">\
            <a href="javascript:void(0)" class="btn btn-danger right remove-reward-btn">Delete </a>\
            <span class="break"></span>\
            <a href="javaScript:void(0)"><i class="icon-chevron-up"></i></a>\
        </div>\
        </div>\
        <div class="box-content clearfix" style="display: none;">\
            <div class="span6">\
            <table class="form">\
            <tbody>\
            <tr>\
            <td>\
        <?php echo $this->lang->line("entry_reward_ranking") ?> :\
        </td>\
        <td>\
        <input type="text" name="luckydraw[rewards][' + countRewards + '][ranking]" placeholder="<?php echo $this->lang->line('entry_reward_ranking'); ?>" class="orange tooltips" value="" />\
        </td>\
        </tr>\
        <tr>\
        <td>\
        <?php echo $this->lang->line("entry_reward_quantity") ?> :\
        </td>\
        <td>\
        <input type="text" name="luckydraw[rewards][' + countRewards + '][qty]" placeholder="<?php echo $this->lang->line('entry_reward_quantity'); ?>" class="orange tooltips" value="" />\
        </td>\
        </tr>\
        </tbody>\
        </table>\
        </div>\
        <div class="span6">\
            <table class="form">\
            <tbody>\
            <tr>\
            <td>\
            <p class="text-center">\
            <?php echo $this->lang->line('entry_rewards'); ?>\
            </p>\
            <div class="reward">\
            <div class="btn-group-wrap" style="text-align: center;">\
            <div class="btn-group center">\
            <button id="exp-entry"\
        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_exp'); ?></button>\
            <button id="point-entry"\
        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_point'); ?></button>\
            <button id="badge-entry"\
        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_badge'); ?></button>\
            <button id="reward-entry"\
        class="btn btn-info btn-large"><?php echo $this->lang->line('entry_custom_point'); ?></button>\
            </div>\
            </div>\
            <div class="exp hide" id="exp">\
            <div class="row-fluid"\
        style="padding-top: 10px; padding-bottom: 10px;">\
            <div class="span2 offset1">\
            <span\
        class="label label-primary"><?php echo $this->lang->line('entry_exp'); ?></span>\
            </div>\
            <div class="span6">\
            <input type="text"\
        name="luckydraw[rewards][' + countRewards + '][details][exp][value]"\
        class="orange tooltips" />\
            </div>\
            </div>\
            </div>\
            <div class="point hide" id="point">\
            <div class="row-fluid"\
        style="padding-top: 10px; padding-bottom: 10px">\
            <div class="span2 offset1">\
            <span\
        class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>\
            </div>\
            <div class="span6">\
            <input type="text"\
        name="luckydraw[rewards][' + countRewards + '][details][point][value]"\
        class="orange tooltips" />\
            </div>\
            </div>\
            </div>\
            <div class="badges hide" id="badges">\
            <?php if ($badge_list) { foreach ($badge_list as $badge) { ?>\
            <div class="row-fluid"\
        style="padding-top: 10px; padding-bottom: 10px;">\
            <div class="span2 offset1">\
            <img height="50" width="50"\
        src="<?php echo S3_IMAGE . $badge['image']; ?>"\
        onerror="$(this).attr(\'src\',\'<?php echo base_url(); ?>image/default-image.png\');"/>\
            </div>\
            <?php $user_b = ""; foreach($badge_user_set as $b){ if($b["badge_id"] == $badge['badge_id']){ $user_b = $b["badge_value"]; break; } } ?>\
            <div class="span6">\
            <input type="text"\
        name="luckydraw[rewards][' + countRewards + '][details][badge][<?php echo $badge['badge_id']; ?>]"\
        class="<?php echo alternator('green','yellow','blue'); ?> tooltips"\
        size="100" />\
            </div>\
            </div>\
            <?php } } ?>\
            </div>\
            <div id="rewards" class="rewards hide">\
            <?php if ($point_list) { ?>\
            <div class="row-fluid"\
        style="padding-top: 10px; padding-bottom: 10px">\
            <div class="span2 offset1">\
            <?php
            foreach ($point_list as $point) {
            ?>\
            <span\
        class="label label-primary"><?php echo $point['name']; ?></span>\
            <?php
            $user_c = "";
            foreach($custom_user_set as $c){
                if($c["custom_id"] == $point['reward_id']){
                    $user_c = $c["custom_value"];
                    break;
                }
            }
            ?>\
            </div>\
            <div class="span6">\
            <input type="text"\
        name="luckydraw[rewards][' + countRewards + '][details][custom][<?php echo $point['reward_id']; ?>] ?>]"\
        class="<?php echo alternator('green', 'yellow', 'blue'); ?>" size="100" />\
            <?php } ?>\
            </div>\
            </div>\
            <?php } ?>\
            </div>\
            </div>\
            </td>\
            </tr>\
            </tbody>\
            </table>\
            </div>\
            </div>\
            </div>';

        $('.rewards-wrapper').append(rewardHtml);

        var element_position = $('.reward-item-wrapper[data-ld-reward-id="'+countRewards+'"] ').offset();
        $("html, body").animate({scrollTop:(element_position.top-20)}, 600);

        init_reward_event();
    });

    function init_reward_event() {

        $('.reward-item-wrapper .box-reward-header').unbind().bind('click', function (data) {
            var $target = $(this).next('.box-content');

            if ($target.is(':visible')) $('i', $(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i', $(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.remove-reward-btn').unbind().bind('click', function (data) {
            var $target = $(this).parent().parent().parent();

            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
                init_reward_event()
            }
        });
    }

</script>
<script type="text/javascript">
    $(function () {

    })
</script>