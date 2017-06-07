<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'custompoints'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }?>
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a></div>
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
            echo form_open($form ,$attributes);
            ?>
            <div id="tab-general">
                <table class="form">
                    <tr>
                        <td>
                            <span class="required">*</span> <?php echo $this->lang->line('entry_name'); ?>:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                   value="<?php echo isset($name) ? $name : set_value('name'); ?>"/></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*</span> <?php echo $this->lang->line('entry_type'); ?>:
                        </td>
                        <td>
                            <div class="control-group">
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="type_custompoint" id="radio_normal" value="normal"
                                            <?php echo $type == "normal" ? "checked=\"checked\"" : set_radio('type_custompoint',
                                                'normal', true); ?>>
                                        <?php echo $this->lang->line('entry_energy_normal_based'); ?>
                                    </label>
                                </div>
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="type_custompoint" id="radio_gain" value="gain"
                                            <?php echo $type == "gain" ? "checked=\"checked\"" : set_radio('type_custompoint',
                                                'gain'); ?>>
                                        <?php echo $this->lang->line('entry_energy_gain_based'); ?>
                                    </label>
                                </div>
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="type_custompoint" id="radio_loss" value="loss"
                                            <?php echo $type == "loss" ? "checked=\"checked\"" : set_radio('type_custompoint',
                                                'loss'); ?>>
                                        <?php echo $this->lang->line('entry_energy_loss_based'); ?>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span id="quantity_required"></span> <?php echo $this->lang->line('entry_quantity'); ?>:
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_quantity'); ?>"
                                   name="quantity" id="input_quantity"
                                   value="<?php echo isset($quantity) ? $quantity : set_value('quantity'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span id="per_user"></span> <?php echo $this->lang->line('entry_per_user'); ?>:
                        </td>
                        <td>
                            <input type="checkbox" name="per_user_include_deducted" id="per_user_include_deducted" value=true <?php echo $per_user_include_deducted ? "checked":""?> /> <?php echo $this->lang->line('entry_per_user_include_deducted'); ?>
                            <br><input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_per_user'); ?>"
                                   name="per_user" id="input_per_user"
                                   value="<?php echo isset($per_user) ? $per_user : set_value('per_user'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span id="limit_per_day_span"></span> <?php echo $this->lang->line('entry_limit_per_day'); ?>:
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_limit_per_day_placeholder'); ?>"
                                   name="limit_per_day" id="input_limit_per_day"
                                   value="<?php echo isset($limit_per_day) ? $limit_per_day : set_value('limit_per_day'); ?>">
                            Start time :
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_limit_start_time'); ?>"
                                   name="limit_start_time" id="input_limit_start_time"
                                   value="<?php echo isset($limit_start_time) ? $limit_start_time : set_value('limit_start_time'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span id="pending"></span> <?php echo $this->lang->line('entry_pending'); ?>:
                        </td>
                        <td>
                            <input type="checkbox" id="pending" name="pending" <?php echo (isset($pending) && $pending) ? "checked" : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide" id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_maximum'); ?>:
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_maximum'); ?>"
                                   name="energy_maximum" id="input_energy_maximum"
                                   value="<?php echo isset($maximum) ? $maximum : set_value('energy_maximum'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide"
                                  id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_changing_period'); ?>
                            :
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_changing_period'); ?>"
                                   name="energy_changing_period" id="input_energy_changing_period"
                                   value="<?php echo isset($changing_period) ? $changing_period : set_value('energy_changing_period'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide"
                                  id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_changing_per_period'); ?>
                            :
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_changing_per_period'); ?>"
                                   name="energy_changing_per_period" id="input_energy_changing_per_period"
                                   value="<?php echo isset($changing_per_period) ? $changing_per_period : set_value('energy_changing_per_period'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_tags'); ?>:</td>
                        <td>
                            <input type="text" class="tags" name="tags" value="<?php echo !empty($tags) ? implode(',',$tags) : set_value('tags'); ?>"
                                   size="5" class="tooltips" data-placement="right" title="Tag(s) input"/>
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
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">


<script type="text/javascript">
    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });


    $(function(){
        $('#tabs a').tabs();

        $('#input_energy_changing_period').timepicker({
            stepHour: 1,
            stepMinute: 10
        });

        $('#input_limit_start_time').timepicker({
            stepHour: 1,
            stepMinute: 10
        });

        var energy_inputs = $('[id^=input_energy_]'),
            quantity_input = $('[id=input_quantity]'),
            quantity_span = $('[id=quantity_required]'),
            per_user = $('[id=input_per_user]'),
            limit_per_day = $('[id=input_limit_per_day]'),
            limit_start_time = $('[id=input_limit_start_time]'),
            energy_required_span = $('[id=energy_required]'),
            custompoint_type_radio = $('input[type=radio][name=type_custompoint]'),
            custompoint_type_radio_value = $('input[type=radio][name=type_custompoint]:checked').val();

        if(custompoint_type_radio_value != "normal"){
            quantity_input.attr('disabled',true);
            limit_per_day.attr('disabled',true);
            per_user.attr('disabled',true);
            limit_start_time.attr('disabled',true);
            quantity_span.hide();
            energy_inputs.attr('disabled',false);
            energy_required_span.show();
        } else {
            quantity_input.attr('disabled',false);
            limit_per_day.attr('disabled',false);
            per_user.attr('disabled',false);
            limit_start_time.attr('disabled',false);
            quantity_span.show();
            energy_inputs.attr('disabled',true);
            energy_required_span.hide();
        }

        $( custompoint_type_radio ).on( "change", function() {
            if ($(this).val() != 'normal') {
                quantity_input.attr('disabled',true);
                limit_per_day.attr('disabled',true);
                per_user.attr('disabled',true);
                limit_start_time.attr('disabled',true);
                quantity_span.hide();
                energy_inputs.attr('disabled',false);
                energy_required_span.show();
            } else {
                quantity_input.attr('disabled',false);
                limit_per_day.attr('disabled',false);
                per_user.attr('disabled',false);
                limit_start_time.attr('disabled',false);
                quantity_span.show();
                energy_inputs.attr('disabled',true);
                energy_required_span.hide();
            }
        });
    });

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>
