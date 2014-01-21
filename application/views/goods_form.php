<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'goods'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
                                <td><input type="text" name="name" size="100" value="<?php echo isset($name) ? $name :  set_value('name'); ?>" />
                                </td>
                            </tr>
                            <tr>    
                                <?php if(!$client_id && !$name){?>
                                    <td><span class="required">*</span> <?php echo $this->lang->line('entry_for_client'); ?>:</td>
                                    <td>
                                        <select name="client_id">
                                            <?php if(isset($to_clients)){?>
                                            <option value = 'all_clients'>All Clients</option>
                                                <?php foreach($to_clients as $client){?>
                                                    <option value ="<?php echo $client['_id']?>"><?php echo $client['company'] ? $client['company'] : $client['first_name']." ".$client['last_name'];?></option>
                                                <?php }?>
                                            <?php }?>
                                        </select>
                                    </td>
                                <?php }?>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_description'); ?>:</td>
                                <td><textarea name="description" id="description"><?php echo isset($description) ? $description : set_value('description'); ?></textarea></td>
                            </tr>
                        </table>

                </div>
                <div id="tab-data">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_quantity'); ?>:</td>
                            <td><input type="text" name="quantity" value="<?php echo isset($quantity) ? $quantity : set_value('quantity'); ?>" size="5" /></td>
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
                        <tr>
                            <td><?php echo $this->lang->line('entry_redeem_with'); ?>:</td>
                            <td>
                                <div class="well" style="max-width: 400px;">
                                    <button id="point-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>
                                    <div class="point">
                                        <div class="goods-panel">
                                            <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                            <input type="text" name="point" size="100" class="orange" value="<?php echo isset($point) ? $point :  set_value('point'); ?>" />
                                        </div>
                                    </div>
                                    <?php
                                    if($badge_list){
                                        ?>
                                        <br>
                                        <button id="badge-entry" type="button" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>
                                        <div class="badges">
                                            <div class="goods-panel">
                                            <?php
                                            foreach($badge_list as $badge){
                                                ?>
                                                <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" />
                                                <input type="text" name="reward_badge['<?php echo $badge['_id']; ?>']" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="<?php echo set_value('reward_badge['.$badge['_id'].']'); ?>" /><br/>
                                            <?php
                                            }
                                            ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php if($client_id){?>
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" id="client_id" />
                    <input type="hidden" name="site_id" value="<?php echo $site_id; ?>" id="site_id" />
                <?php }?>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--

CKEDITOR.replace('description', {
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
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
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

$(document).ready(function(){
    $(".point").hide();
    $(".badges").hide();
    $("#point-entry").click(function() {$(".point").toggle()});
    $("#badge-entry").click(function() {$(".badges").toggle()});
});

//--></script>