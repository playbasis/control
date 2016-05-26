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
                            <input type="text" class="tags" name="tags" value="<?php echo !empty($tags) ? implode($tags,',') : set_value('tags'); ?>"
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

        var energy_inputs = $('[id^=input_energy_]'),
            energy_required_span = $('[id=energy_required]'),
            custompoint_type_radio = $('input[type=radio][name=type_custompoint]'),
            custompoint_type_radio_value = $('input[type=radio][name=type_custompoint]:checked').val();

        if(custompoint_type_radio_value != "normal"){
            energy_inputs.attr('disabled',false);
            energy_required_span.show();
        } else {
            energy_inputs.attr('disabled',true);
            energy_required_span.hide();
        }

        $( custompoint_type_radio ).on( "change", function() {
            if ($(this).val() != 'normal') {
                energy_inputs.attr('disabled',false);
                energy_required_span.show();
            } else {
                energy_inputs.attr('disabled',true);
                energy_required_span.hide();
            }
        });
    });
</script>
