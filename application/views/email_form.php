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
                <button class="btn btn-info" onclick="location = baseUrlPath+'email'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }?>

            <div id="tabs" class="htabs">
                <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
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
                                <td>
                                    <div class="row">
                                        <div class="span9">
                                            <textarea name="body" id="body"><?php echo isset($body) ? $body : set_value('body'); ?></textarea>    
                                        </div>
                                        <div class="span3">
                                            <h4> Code</h4>
                                            <table cellpadding="5">
                                                <tbody>
                                                    <tr>
                                                        <td width="50%" align="right"><small>Player First Name:</small></td>
                                                        <td>{{first_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player Last Name:</small></td>
                                                        <td>{{last_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player ID:</small></td>
                                                        <td>{{cl_player_id}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player Email:</small></td>
                                                        <td>{{email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player Phone:</small></td>
                                                        <td>{{phone_number}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player Referral Code:</small></td>
                                                        <td>{{code}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" align="right"><small>Player (2) First Name:</small></td>
                                                        <td>{{first_name-2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player (2) Last Name:</small></td>
                                                        <td>{{last_name-2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player (2) ID:</small></td>
                                                        <td>{{cl_player_id-2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player (2) Email:</small></td>
                                                        <td>{{email-2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player (2) Phone:</small></td>
                                                        <td>{{phone_number-2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Player (2) Referral Code:</small></td>
                                                        <td>{{code-2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><small>Coupon Code:</small></td>
                                                        <td>{{coupon}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                        
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_sort_order'); ?>:</td>
                                <td><input type="text" name="sort_order" value="<?php echo isset($sort_order) ? $sort_order : set_value('sort_order'); ?>" size="1" /></td>
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
    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
    CKEDITOR.replace('body', {
        filebrowserImageBrowseUrl: 'mediamanager/dialog/'
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

