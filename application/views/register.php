

<div id="content" >
	<div class = "box" style = "position: relative; max-width: 750px; margin:0 auto;">
		
		<div class="heading">
			<h1><img src="<?php echo base_url('image/user-group.png')?>" alt="" /><?php echo $heading_title_register;?></h1>
		</div><!-- .heading -->
		<div class="content" >
			<?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
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
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_email');?>: </td>
							<td><input type = "text" name="email" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['email'];}?>" class="tooltips" data-placement="right" title="Your Email address is used to log into the system"></td>
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
							<td><span class="required">*</span> <?php echo $this->lang->line('form_firstname');?>: </td>
							<td><input type = "text" name="firstname" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['firstname'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_lastname');?>: </td>
							<td><input type = "text" name="lastname" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['lastname'];}?>"></td>
						</tr>
						
						<!--<tr>
							<td><span class="required">*</span> <?php //echo $this->lang->line('form_username');?>: </td>
							<td><input type = "text" name="username" size="50" value ="<?php //if(isset($temp_fields)){echo $temp_fields['username'];}?>"></td>
						</tr>-->
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_domain');?>:</td>
							<td><input type = "text" name="domain_name" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['domain_name'];}?>" class="tooltips" data-placement="right" title="Your domain name (example: www.playbasis.com)"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_site');?>:</td>
							<td><input type = "text" name="site_name" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['site_name'];}?>" class="tooltips" data-placement="right" title="Your Site name (example: Playbasis)"></td>
						</tr>
					</table>
				</div>
					
					<p style="float:left"><a href="<?php echo base_url();?>" id="cancel">Cancel</a>

				
				<p style="float:right"><a onclick="$('#form').submit();" class="button" id="submit">Register</a></p>

				
			<?php echo form_close();?>
		</div><!-- .content-->
	</div><!-- .box -->
</div><!-- #content -->