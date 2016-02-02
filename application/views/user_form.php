<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title_user; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'user'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
            
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
                            <!--<tr>
                                <td><span class="required">*</span> <?php //echo $this->lang->line('form_username'); ?></td>
                                <td><input type="text" name="username" size="100" value="<?php //echo isset($user['username']) ? $user['username'] :  set_value('username'); ?>" /></td>
                            </tr>-->
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_email'); ?>:</td>
                                <td><input type="email" name="email" size="100" value="<?php echo isset($user['email']) ? $user['email'] :  set_value('email'); ?>" class="tooltips" data-placement="right" title="Email address is used to log into the system"/></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_firstname'); ?>:</td>
                                <td><input type="text" name="firstname" size="100" value="<?php echo isset($user['firstname']) ? $user['firstname'] :  set_value('firstname'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_lastname'); ?>:</td>
                                <td><input type="text" name="lastname" size="100" value="<?php echo isset($user['lastname']) ? $user['lastname'] :  set_value('lastname'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_user_group'); ?>:</td>
                                <td>
                                    <select name ="user_group">

                                    <?php $group_chk = false?>
                                    <?php if($user_groups){?>
                                        <?php foreach($user_groups as $user_group){?>
                                            <?php if($user_group['name'] != "Top Administrator"){?>
                                            <?php }?>
                                            <?php if((isset($user['user_group_id']) && $user['user_group_id']==$user_group['_id'])|| $user_group['_id'] == set_value('user_group')){?>
                                                <option value = "<?php echo $user_group['_id'];?>" selected><?php echo $user_group['name'];?></option>
                                                <?php $group_chk = true ?>
                                            <?php }else{?>
                                                <option value = "<?php echo $user_group['_id'];?>"><?php echo $user_group['name'];?></option>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>
                                    <?php if(!(isset($is_admin_groups) && $is_admin_groups==true)){ ?>
                                        <?php if($group_chk){ ?>
                                            <option value="" ><?php echo $this->lang->line('text_default_admin'); ?></option>
                                        <?php } else { ?>
                                            <option value="" selected="selected"><?php echo $this->lang->line('text_default_admin'); ?></option>
                                        <?php } ?>
                                    <?php } ?>


                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_password'); ?>:</td>
                                <td><input type="password" name="password" size="100" /></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_confirm_password'); ?>:</td>
                                <td><input type="password" name="confirm_password" size="100" /></td>
                            </tr>
                            <?php if(!$this->session->userdata('client_id')){?>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('form_status'); ?>:</td>
                                <td>
                                    <select name ="status">
                                        <?php if($user['status']||set_value('status')==1){?>
                                            <option value = 1>Enabled</option>
                                            <option value = 0>Disabled</option>
                                        <?php }else{ ?>
                                            <option value =0>Disabled</option>
                                            <option value =1>Enabled</option>
                                        <?php }?>    
                                    </select>
                                </td>
                            </tr>
                            <?php }?>
                            <!--<tr>
                                <td><span class="required">*</span> <?php //echo $this->lang->line('form_status'); ?></td>
                                <td>
                                    <select name ="status">
                                        <?php //if($user['status']){?>
                                            <option value = 1>Enabled</option>
                                            <option value = 0>Disabled</option>
                                        <?php //}else{ ?>
                                            <option value =0>Disabled</option>
                                            <option value =1>Enabled</option>
                                        <?php //}?>    
                                    </select>
                                </td>
                            </tr>-->
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
$('#languages a').tabs();
//--></script>