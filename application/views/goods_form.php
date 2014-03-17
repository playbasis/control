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
            <div id="tabs" class="htabs">
                <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
                <a href="#tab-data"><?php echo $this->lang->line('tab_data'); ?></a>
                <a href="#tab-redeem"><?php echo $this->lang->line('tab_redeem'); ?></a>
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
                                <td><input type="text" name="name" size="100" value="<?php echo isset($name) ? $name :  set_value('name'); ?>" />
                                </td>
                            </tr>
                            <tr>    
                                <?php if(!$client_id && !$name){?>
                                    <td><span class="required">*</span> <?php echo $this->lang->line('entry_for_client'); ?>:</td>
                                    <td>
                                        <select id="client-choose" name="admin_client_id">
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
                            <?php if(!$client_id){?>
                                <tr>
                                    <td><?php echo $this->lang->line('entry_sponsor'); ?>:</td>
                                    <td>
                                        <input type="checkbox" name="sponsor" value = 1 <?php echo ($sponsor)?'checked':'unchecked'?> class="tooltips" data-placement="right" title="Sponsor badge cannot be modified by clients"/>
                                    </td>
                                </tr>
                            <?php }?>
                            <tr>
                                <td><?php echo $this->lang->line('entry_description'); ?>:</td>
                                <td><textarea name="description" id="description"><?php echo isset($description) ? $description : set_value('description'); ?></textarea></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_start_date'); ?>:</td>
                                <td>
                                    <input type="text" class="date" name="date_start" value="<?php if (strtotime(datetimeMongotoReadable($date_start))) {echo date('Y-m-d', strtotime(datetimeMongotoReadable($date_start)));} else { echo $date_start; } ?>" size="50" />
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_expire_date'); ?>:</td>
                                <td>
                                    <input type="text" class="date" name="date_expire" value="<?php if (strtotime(datetimeMongotoReadable($date_expire))) { echo date('Y-m-d', strtotime(datetimeMongotoReadable($date_expire))); } else { echo $date_expire; } ?>" size="50" />
                                </td>
                            </tr>
                        </table>

                </div>
                <div id="tab-data">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="<?php echo base_url();?>image/default-image.png" />
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
                    </table>
                </div>
                <div id="tab-redeem">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_redeem_with'); ?>:</td>
                            <td>
                                <div class="well" style="max-width: 400px;">
                                    <button id="point-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>
                                    <div class="point">
                                        <div class="goods-panel">
                                            <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                            <input type="text" name="reward_point" size="100" class="orange tooltips" value="<?php echo isset($reward_point) ? $reward_point :  set_value('reward_point'); ?>" data-placement="right" title="The number of Points needed to redeem the Goods"/>
                                        </div>
                                    </div>
                                    <div id="badge-panel">
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
                                                    <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="<?php echo base_url();?>image/default-image.png" />
                                                    <input type="text" name="reward_badge[<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?> tooltips" size="100" value="<?php if(set_value('reward_badge['.$badge['badge_id'].']')){
                                                        echo set_value('reward_badge['.$badge['badge_id'].']');
                                                    }else{
                                                        if($reward_badge){
                                                            foreach($reward_badge as $rbk => $rb){
                                                                if($rbk == $badge['badge_id']){
                                                                    echo $rb;
                                                                    continue;
                                                                }
                                                            }
                                                        }
                                                    } ?>" data-placement="right" title="The number of Badges needed to redeem the Goods"/><br/>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    </div>
                                    <div id="reward-panel">
                                    <?php
                                    if($point_list){
                                    ?>
                                        <br>
                                        <button id="reward-entry" type="button" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_rewards'); ?></button>
                                        <div class="rewards">
                                            <div class="goods-panel">
                                                <?php
                                                foreach($point_list as $point){
                                                    ?>
                                                    <?php echo $point['name']; ?>
                                                    <input type="text" name="reward_reward[<?php echo $point['reward_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="<?php if(set_value('reward_reward['.$point['reward_id'].']')){
                                                        echo set_value('reward_reward['.$point['reward_id'].']');
                                                    }else{
                                                        if($reward_reward){
                                                            foreach($reward_reward as $rbk => $rb){
                                                                if($rbk == $point['reward_id']){
                                                                    echo $rb;
                                                                    continue;
                                                                }
                                                            }
                                                        }
                                                    } ?>" /><br/>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    </div>
                                </div>
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
<script type="text/javascript">
    $(function(){

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

    })
</script>
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
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="<?php echo base_url();?>image/default-image.png" />');
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
    $(".rewards").hide();
    $("#point-entry").live('click', function() {$(".point").toggle()});
    $("#badge-entry").live('click', function() {$(".badges").toggle()});
    $("#reward-entry").live('click', function() {$(".rewards").toggle()});
});

//--></script>
<?php if(!$client_id && !$name){?>
<script type="text/javascript"><!--

    $(document).ready(function(){
        $("#client-choose").change(function() {
            var c = $(this).val();
            $("#badge-panel").html("");
            $("#reward-panel").html("");
            if(c != "all_clients"){
                $.ajax({
                    url: baseUrlPath+'goods/getBadgeForGoods',
                    data: { client_id: c },
                    context: document.body
                }).done(function(data) {
                    $("#badge-panel").html(data);
                    $(".badges").hide();
                });
                $.ajax({
                    url: baseUrlPath+'goods/getCustomForGoods',
                    data: { client_id: c },
                    context: document.body
                }).done(function(data) {
                    $("#reward-panel").html(data);
                    $(".rewards").hide();
                });
            }
        });
    });
    
    //--></script>
<?php } ?>