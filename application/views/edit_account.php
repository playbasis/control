<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $this->lang->line('text_edit_account'); ?></h1>
	        <div class="buttons">
	            <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
	            <button class="btn btn-info" onclick="location = baseUrlPath+'dashboard'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
	        </div>
        </div><!-- .header -->
        <div class="content">
        <?php if($this->session->flashdata('no_changes')){ ?>
            <div class="content messages half-width">
            <div class="warning"><?php echo $this->session->flashdata('no_changes'); ?></div>
            </div>
        <?php }?>
        <?php if($this->session->flashdata('success')){ ?>
            <div class="content messages half-width">
            <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
            </div>
        <?php }?>
        <?php if(validation_errors() || isset($message)) {?>
        	<div class="content messages half-width">
        		<?php echo validation_errors("<div class='warning'>","</div>")?>
				<?php if (isset($message) && $message) {?>
					<div class="warning"><?php echo $message; ?></div>
				<?php }?>
        	</div>
        <?php }?>
        <?php $attributes = array('id' => 'form');?>
	        <?php echo form_open($form, $attributes)?>
	        	<table class="form">
	        		<tr>
	                    <td><?php echo $this->lang->line('form_firstname'); ?></td>
	                    <td><?php echo $user_info['firstname'];?></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_lastname'); ?></td>
	                    <td><?php echo $user_info['lastname'];?></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_email'); ?></td>
	                    <td><?php echo $user_info['email'];?></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_user_group'); ?></td>
	                    <td><?php echo $usergroup_name;?></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_password'); ?></td>
	                    <td><input type="password" name = "password" size="100"/></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_confirm_password'); ?></td>
	                    <td><input type="password" name="password_confirm" size="100"/></td>
	                </tr>
	                <tr>
                        <td><?php echo $this->lang->line('form_image_profile'); ?>:</td>
                        <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" class="thumbprofile" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                            <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                            <br /><a onclick="image_upload_profile('image', 'thumbprofile');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('.thumbprofile').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                    </tr>
	        	</table>
	        <?php echo form_close();?>
        </div>
    </div><!-- .box -->
</div><!-- #content .span10 -->


<script type="text/javascript"><!--
function image_upload_profile(field, thumb) {
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
                        $('.' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');

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