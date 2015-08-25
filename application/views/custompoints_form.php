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
                                            <?php echo set_radio('type_custompoint', 'normal', true); ?>>
                                        Normal based
                                    </label>
                                </div>
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="type_custompoint" id="radio_energy" value="energy"
                                            <?php echo set_radio('type_custompoint', 'energy'); ?>>
                                        Energy based
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide" id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_per_user'); ?>:
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_per_user'); ?>"
                                   name="energy_per_user" id="input_energy_per_user"
                                   value="<?php echo isset($per_user) ? $per_user : set_value('energy_per_user'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide"
                                  id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_regen_time'); ?>
                            :
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_regen_time'); ?>"
                                   name="energy_regen_time" id="input_energy_regen_time"
                                   value="<?php echo set_value('energy_regen_time'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide"
                                  id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_decay_per_period'); ?>
                            :
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_decay_per_period'); ?>"
                                   name="energy_decay_per_period" id="input_energy_decay_per_period"
                                   value="<?php echo set_value('energy_decay_per_period'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required hide"
                                  id="energy_required">*</span> <?php echo $this->lang->line('entry_energy_regen_per_period'); ?>
                            :
                        </td>
                        <td>
                            <input type="text"
                                   placeholder="<?php echo $this->lang->line('entry_energy_regen_per_period'); ?>"
                                   name="energy_regen_per_period" id="input_energy_regen_per_period"
                                   value="<?php echo set_value('energy_regen_per_period'); ?>">
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

<script type="text/javascript">
    $(function(){
        $('#tabs a').tabs();

        $('#input_energy_regen_time').timepicker({
            stepHour: 1,
            stepMinute: 10
        });

        var energy_inputs = $('[id^=input_energy_]'),
            energy_required_span = $('[id=energy_required]'),
            custompoint_type_radio = $('input[type=radio][name=type_custompoint]'),
            custompoint_type_radio_value = $('input[type=radio][name=type_custompoint]:checked').val();

        if(custompoint_type_radio_value == 'energy'){
            energy_inputs.attr('disabled',false);
            energy_required_span.show();
        } else {
            energy_inputs.attr('disabled',true);
            energy_required_span.hide();
        }

        $( custompoint_type_radio ).on( "change", function() {
            if ($(this).val() == 'energy') {
                energy_inputs.attr('disabled',false);
                energy_required_span.show();
            } else {
                energy_inputs.attr('disabled',true);
                energy_required_span.hide();
            }
        });
    });
</script>
