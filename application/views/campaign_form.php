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
                            <?php echo $this->lang->line('entry_image'); ?>:
                        </td>
                        <td valign="top">
                            <div class="image">
                                <img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" /><br/>
                                <a onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?>
                                </a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');">
                                    <?php echo $this->lang->line('text_clear'); ?></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->lang->line('entry_date_start'); ?>:
                        </td>
                        <td>
                            <input type="text" class="date" placeholder="<?php echo $this->lang->line('entry_date_start'); ?>" name="date_start" value="<?php if (isset($date_start) && $date_start && strtotime(datetimeMongotoReadable($date_start))) {echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($date_start)));} else { echo isset($date_start) && $date_start ? $date_start : ""; } ?>" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->lang->line('entry_date_end'); ?>:
                        </td>
                        <td>
                            <input type="text" class="date" placeholder="<?php echo $this->lang->line('entry_date_end'); ?>" name="date_end" value="<?php if (isset($date_end) && $date_end && strtotime(datetimeMongotoReadable($date_end))) {echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($date_end)));} else { echo isset($date_end) && $date_end ? $date_end : ""; } ?>" size="50" />
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
        $('.date').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: "HH:mm:ss"});
    });

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
</script>