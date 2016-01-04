<div id="content" class="span10 content-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'content'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php } ?>
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
            $attributes = array('id' => 'form', 'class' => 'form-horizontal content-form');
            echo form_open($form, $attributes);
            ?>
            <div class="tabbable">
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active">
                        <a href="#generalContentTab"
                           data-toggle="tab"><?php echo $this->lang->line('tab_general'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="generalContentTab">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label"
                                           for="inputName"><?php echo $this->lang->line('entry_name'); ?><span
                                            class="required">&nbsp;*</span></label>
                                    <div class="controls">
                                        <input type="text" name="name" size="100" id="inputName"
                                               placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                               value="<?php echo isset($name) ? $name : set_value('name'); ?>"/>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="inputDetail">
                                        <?php echo $this->lang->line('entry_detail'); ?><span
                                            class="required">&nbsp;*</span>
                                    </label>
                                    <div class="controls">
                                        <textarea name="detail" id="inputDetail" cols="80" rows="10"
                                                  placeholder="<?php echo $this->lang->line('entry_detail'); ?>">
                                            <?php echo isset($detail) ? $detail : set_value('detail'); ?>
                                        </textarea>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label"><?php echo $this->lang->line('entry_date_range'); ?><span
                                            class="required">&nbsp;*</span>
                                    </label>
                                    <div class="controls">
                                        <span>
                                            <input type="text" class="date" name="date_start" id="date_start" size="50"
                                                   placeholder="<?php echo $this->lang->line('entry_date_start'); ?>"
                                                   value="<?php echo isset($date_start) && $date_start ? date('Y-m-d', strtotime(datetimeMongotoReadable($date_start))) : ''; ?>"/>
                                        </span>
                                        <span>&nbsp;-&nbsp;</span>
                                        <span>
                                            <input type="text" class="date" name="date_end" id="date_end" size="50"
                                                   placeholder="<?php echo $this->lang->line('entry_date_end'); ?>"
                                                   value="<?php echo isset($date_end) && $date_end ? date('Y-m-d', strtotime(datetimeMongotoReadable($date_end))) : ''; ?>"/>
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label"
                                           for="image"><?php echo $this->lang->line('entry_image'); ?>
                                    </label>
                                    <div class="controls">
                                        <div class="image">
                                            <img src="<?php echo $thumb; ?>" alt="" id="thumb"
                                                 onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                                            <input type="hidden" name="image" value="<?php echo $image; ?>"
                                                   id="image"/>
                                            <br/><a
                                                onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label"
                                           for="status"><?php echo $this->lang->line('entry_status'); ?></label>
                                    <div class="controls">
                                        <input type="checkbox" name="status" id="status" data-handle-width="40" <?php echo isset($status) ? ( $status ? "checked" : '') : set_checkbox('status','',true); ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
<link id="base-style" rel="stylesheet" type="text/css"
      href="<?php echo base_url(); ?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css"/>
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/content/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    function image_upload(field, thumb) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="' + baseUrlPath + 'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 200px; height: 100%;" frameborder="no" scrolling="no"></iframe></div>');

        $('#dialog').dialog({
            title: '<?php echo $this->lang->line('text_image_manager'); ?>',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: baseUrlPath + 'filemanager/image?image=' + encodeURIComponent($('#' + field).val()),
                        dataType: 'text',
                        success: function (data) {
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

    CKEDITOR.replace( 'inputDetail' );

    $(function () {
        $("[name='status']").bootstrapSwitch();
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
