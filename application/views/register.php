

<div id="content" >
	<div class = "box" style = "position: relative; max-width: 750px; margin:0 auto;">
		
		<div class="heading">
			<h1><img src="<?php echo base_url('image/user-group.png')?>" alt="" /><?php echo $heading_title_register;?></h1>
		</div><!-- .heading -->
		<div class="content" >
			<?php if(validation_errors() || isset($message)) {?>
                <div class="content messages half-width">
                    <?php echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
            <?php }?>
			<?php $attributes = array('id' => 'form');?>
			<?php echo form_open_multipart($form, $attributes);?>
				<div id="pg1">
					<table class="form">
							
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#0C96CD;float:left;margin:10px;"class="circle">
							User Details
						</div>
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#38393D;float:left;margin:10px;"class="circle">
							Domain Details
						</div>
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#38393D;float:left;margin:10px;"class="circle">
							Company Logo
						</div>	
							
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_firstname');?>: </td>
							<td><input type = "text" name="firstname" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['firstname'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_lastname');?>: </td>
							<td><input type = "text" name="lastname" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['lastname'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_email');?>: </td>
							<td><input type = "text" name="email" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['email'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_username');?>: </td>
							<td><input type = "text" name="username" size="50" value ="<?php if(isset($temp_fields)){echo $temp_fields['username'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_password');?>: </td>
							<td><input type = "password" name="password" size="50" value = ""></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_confirm_password');?>: </td>
							<td><input type = "password" name="password_confirm" size="50" value =""></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_user_group');?>:</td>
							<td>
								<select name="user_group">
									<?php if(isset($user_groups)){?>
										<?php foreach($user_groups as $user_group){?>
											<option value="<?php echo $user_group['_id'];?>"><?php echo $user_group['name'];?></option>
										<?php }?>
									<?php }?>
								</select>
							</td>
						</tr>

					</table>
				</div>
				<div id="pg2">
					
					<table class="form">
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#38393D;float:left;margin:10px;"class="circle">
							User Details
						</div>
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#0C96CD;float:left;margin:10px;"class="circle">
							Domain Details
						</div>
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#38393D;float:left;margin:10px;"class="circle">
							Company Logo
						</div>	
						
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_domain');?>:</td>
							<td><input type = "text" name="domain" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['domain'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_site');?>:</td>
							<td><input type = "text" name="site" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['site'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_limit_users');?>:</td>
							<td><input type="text" name="limit_users" size="50" value="10"/></td>
						</tr>
					</table>
				</div>
				<div id="pg3">
					<table class="form">
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#38393D;float:left;margin:10px;"class="circle">
							User Details
						</div>
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#38393D;float:left;margin:10px;"class="circle">
							Domain Details
						</div>
						<div style = "width:120px;height:120px;border-radius:60px;color:#fff;line-height:120px;text-align:center;background:#0C96CD;float:left;margin:10px;"class="circle">
							Company Logo
						</div>	
						<tr>
                            <td><?php echo $this->lang->line('form_logo_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
					</table>
				</div>
					
					<p style="float:left"><a href="<?php echo base_url();?>" id="cancel">Cancel</a>
					<p style="float:right"><a id="next_pg2">Next <i class="icon-chevron-right"></i></a> </p>
					<p style="float:left"><a id="prev_pg1"><i class="icon-chevron-left"></i> Previous</a></p>
					<p style="float:right"><a id="next_pg3">Next <i class="icon-chevron-right"></i></a></p>
					<p style="float:left"><a id="prev_pg2"><i class="icon-chevron-left"></i> Previous</a></p>
				
				<p style="float:right"><a onclick="$('#form').submit();" class="button" id="submit">Register</a></p>

				
			<?php echo form_close();?>
		</div><!-- .content-->
	</div><!-- .box -->
</div><!-- #content -->

<script type="text/javascript"><!--

$(document).ready(function(){
	$("#pg2").hide();
	$("#pg3").hide();
	$("#prev_pg1").hide();
	$("#prev_pg2").hide();
	$("#next_pg3").hide();
	$("#submit").hide();
});

$('#next_pg2').click(function(){
	
	$("#pg2").show();
	$("#pg1").hide();
	$("#prev_pg1").show();
	$("#next_pg3").show();
	$("#next_pg2").hide();
	$("#cancel").hide();
});

$('#prev_pg1').click(function(){
	$("#pg1").show();
	$("#pg2").hide();
	$("#next_pg2").show();
	$("#prev_pg1").hide();
	$("#next_pg3").hide();
	$("#cancel").show();
});

$('#next_pg3').click(function(){
	$("#pg3").show();
	$("#pg2").hide();
	$("#pg1").hide();
	$("#next_pg1").hide();
	$("#prev_pg1").hide();
	$("#prev_pg2").show();
	$("#next_pg3").hide();
	$("#submit").show();
});

$('#prev_pg2').click(function(){
	$("#pg3").hide();
	$("#pg2").show();
	$("#prev_pg2").hide();
	$("#prev_pg1").show();
	$("#next_pg3").show();
	$("#submit").hide();
})

//--></script>

<script type="text/javascript"><!--
function image_upload(field, thumb) {
    $('#dialog').remove();

    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+baseUrlPath+'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

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
        width: 800,
        height: 400,
        resizable: false,
        modal: false
    });
};
//--></script>