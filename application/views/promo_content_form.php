<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'promo_content'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php } ?>
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
            <?php
            if (validation_errors() || isset($message)) {
                ?>
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
                <?php
            }
            $attributes = array('id' => 'form');
            echo form_open($form, $attributes);
            ?>
            <div id="tab-general">
                <table class="form">
                    <tbody>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_name'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                   value="<?php echo isset($name) ? $name : set_value('name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_description'); ?>&nbsp;:
                        </td>
                        <td>
                            <textarea name="description" rows="4"
                                      placeholder="<?php echo $this->lang->line('entry_description'); ?>"><?php echo isset($description) ? $description : set_value('description'); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                                <span
                                    class="required">*&nbsp;</span><?php echo $this->lang->line('entry_date_range'); ?>&nbsp;:
                        </td>
                        <td>
                            <span>
                                <input type="text" class="date" name="date_start" id="date_start" size="50"
                                       placeholder="<?php echo $this->lang->line('entry_date_start'); ?>"
                                       value="<?php echo isset($date_start) && $date_start ? date('Y-m-d',
                                           strtotime(datetimeMongotoReadable($date_start))) : ''; ?>"/>
                            </span>
                            <span>&nbsp;-&nbsp;</span>
                            <span>
                                <input type="text" class="date" name="date_end" id="date_end" size="50"
                                       placeholder="<?php echo $this->lang->line('entry_date_end'); ?>"
                                       value="<?php echo isset($date_end) && $date_end ? date('Y-m-d',
                                           strtotime(datetimeMongotoReadable($date_end))) : ''; ?>"/>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_image'); ?>&nbsp;:</td>
                        <td valign="top">
                            <div class="image">
                                <img src="<?php echo $thumb; ?>" alt="" id="thumb"
                                     onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image"/>
                                <br/><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_status'); ?>&nbsp;:
                        </td>
                        <td>
                            <div class="control-group">
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="status" id="radio_status_enable" value="enable"
                                            <?php echo $status == true ? "checked=\"checked\"" : set_radio('status',
                                                'enable', true); ?>>
                                        <?php echo $this->lang->line('entry_status_enable'); ?>
                                    </label>
                                </div>
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="status" id="radio_status_disabled" value="disable"
                                            <?php echo $status == false ? "checked=\"checked\"" : set_radio('status',
                                                'disable'); ?>>
                                        <?php echo $this->lang->line('entry_status_disable'); ?>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
<link id="base-style" rel="stylesheet" type="text/css"
      href="<?php echo base_url(); ?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css"/>
<script type="text/javascript"
        src="<?php echo base_url(); ?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
    function image_upload(field, thumb) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+baseUrlPath+'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 200px; height: 100%;" frameborder="no" scrolling="no"></iframe></div>');

        $('#dialog').dialog({
            title: '<?php echo $this->lang->line('text_image_manager'); ?>',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: baseUrlPath+'filemanager/image?image=' + encodeURIComponent($('#' + field).val()),
                        dataType: 'text',
                        success: function(data) {
                            $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                        }
                    });
                }
            },
            bgiframe: false,
            width: 200,
            height: 100,
            resizable: false,
            modal: false
        });
    }

    var startDateTextBox = $('#date_start');
    var endDateTextBox = $('#date_end');

    $(function () {
        $('#tabs a').tabs();

        startDateTextBox.datepicker({
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datepicker('setDate', testStartDate);
                }
                else {
                    endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
            }
        });
        endDateTextBox.datepicker({
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datepicker('setDate', testEndDate);
                }
                else {
                    startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
            }
        });

    });
</script>
