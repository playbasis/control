<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'level'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a><a href="#tab-data"><?php echo $this->lang->line('tab_data'); ?></a></div>
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
                            <td><?php echo $this->lang->line('entry_name'); ?>:</td>
                            <td><input type="text" name="level_title" value="<?php echo isset($level_title) ? $level_title :  set_value('level_title'); ?>" size="50" /></td>
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
                        <tr>
                            <td><span class="required">*</span> <?php echo $this->lang->line('entry_exp'); ?>:</td>
                            <td><input type="text" name="exp" value="<?php echo isset($exp) ? $exp : set_value('exp'); ?>"  /></td>
                        </tr>
                        <tr>
                            <td><span class="required">*</span> <?php echo $this->lang->line('entry_level'); ?>:</td>
                            <td><input type="text" name="level" value="<?php echo isset($level) ? $level : set_value('level'); ?>"  /></td>
                        </tr>
                        <!--<tr>
                            <td><?php //echo $this->lang->line('entry_sort_order'); ?>:</td>
                            <td><input type="text" name="sort_order" value="<?php //echo isset($sort_order) ? $sort_order : set_value('sort_order'); ?>" size="1" /></td>
                        </tr>-->
                        <tr>
                            <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                            <td><select name="status">
                                <?php if ($status || $status != set_value('status')) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                    </table>
                </div>

            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" id="client_id" />
            <input type="hidden" name="site_id" value="<?php echo $site_id; ?>" id="site_id" />

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

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
$('#languages a').tabs();
//--></script>