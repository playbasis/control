<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'campaign'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
                            <span class="required">*</span>
                            <?php echo $this->lang->line('entry_name'); ?>:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                   value="<?php echo isset($name) ? $name : set_value('name'); ?>"/></td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->lang->line('entry_date_start'); ?>:
                        </td>
                        <td>
                            <input type="text" class="date" placeholder="<?php echo $this->lang->line('entry_date_start'); ?>" name="date_start" value="<?php if (isset($date_start) && $date_start && strtotime(datetimeMongotoReadable($date_start))) {echo date('Y-m-d', strtotime(datetimeMongotoReadable($date_start)));} else { echo isset($date_start) && $date_start ? $date_start : ""; } ?>" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->lang->line('entry_date_end'); ?>:
                        </td>
                        <td>
                            <input type="text" class="date" placeholder="<?php echo $this->lang->line('entry_date_end'); ?>" name="date_end" value="<?php if (isset($date_end) && $date_end && strtotime(datetimeMongotoReadable($date_end))) {echo date('Y-m-d', strtotime(datetimeMongotoReadable($date_end)));} else { echo isset($date_end) && $date_end ? $date_end : ""; } ?>" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->lang->line('entry_weight'); ?>:
                        </td>
                        <td>
                            <input type="number"
                                   placeholder="<?php echo $this->lang->line('entry_weight'); ?>"
                                   name="weight" id="input_weight"
                                   value="<?php echo isset($weight) ? $weight : ""; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                        <td><input type="checkbox" name="status" id="status" <?php echo  $status == true ? 'checked' : ''; ?>></td>
                        <td></td>
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
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript" ></script>

<script type="text/javascript">
    $(function(){
        $('#status').bootstrapSwitch();
        $('#status').bootstrapSwitch('size', 'small');
        $('#status').bootstrapSwitch('onColor', 'success');
        $('#status').bootstrapSwitch('offColor', 'danger');
        $('#status').bootstrapSwitch('handleWidth', '70');
        $('#status').bootstrapSwitch('labelWidth', '10');
        $('#status').bootstrapSwitch('onText', 'Enable');
        $('#status').bootstrapSwitch('offText', 'Disable');
        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

    })
</script>