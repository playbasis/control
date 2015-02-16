<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'email'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }?>
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
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_name'); ?>:</td>
                                <td><input type="text" name="name" size="100" value="<?php echo isset($name) ? $name :  set_value('name'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_body'); ?>:</td>
                                <td><textarea name="body" id="body"><?php echo isset($description) ? $description : set_value('body'); ?></textarea></td>
                            </tr>
                        </table>

                </div>
                <div id="tab-data">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_quantity'); ?>:</td>
                            <td><input type="text" name="quantity" value="<?php echo isset($quantity) ? $quantity : set_value('quantity'); ?>" size="5" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_stackable'); ?>:</td>
                            <td><select name="stackable">
                                <?php if ($stackable || set_value('stackable')==1) { ?>
                                <?php //if (set_value('stackable')==1) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_substract'); ?>:</td>
                            <td><select name="substract">
                                <?php if ($substract || set_value('substract')==1) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_sort_order'); ?>:</td>
                            <td><input type="text" name="sort_order" value="<?php echo isset($sort_order) ? $sort_order : set_value('sort_order'); ?>" size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_claim'); ?></td>
                            <td>
                                <select name="claim">
                                    <?php if ($claim || set_value('claim')==1) { ?>
                                        <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                        <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                        <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_redeem'); ?></td>
                            <td>
                                <select name="redeem">
                                    <?php if ($redeem || set_value('redeem')==1) { ?>
                                        <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                        <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                        <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_status'); ?></td>
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
                    </table>
                </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--

CKEDITOR.replace('body', {
    filebrowserBrowseUrl: baseUrlPath+'filemanager',
    filebrowserImageBrowseUrl: baseUrlPath+'filemanager',
    filebrowserFlashBrowseUrl: baseUrlPath+'filemanager',
    filebrowserUploadUrl: baseUrlPath+'filemanager',
    filebrowserImageUploadUrl: baseUrlPath+'filemanager',
    filebrowserFlashUploadUrl: baseUrlPath+'filemanager'
});

//--></script>
<script type="text/javascript"><!--
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
};
//--></script>
<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>


<script type="text/javascript">

$("#sponsor").hover(function (){

});

</script>
