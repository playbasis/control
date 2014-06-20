

<div id="content" >
	<div class = "box" style = "position: relative; max-width: 400px; margin:0 auto;">
		
		<div class="heading">
			<h1><?php echo $heading_forgot_password;?></h1>
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
							<br/>
							Please provide your email, we will send you a link via email to reset your password.
							<br/>
							<br/>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_email');?>: </td>
							<td><input type = "text" name="email" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['email'];}?>" class="tooltips" data-placement="bottom" title="Email address is used to log into the system"></td>
						</tr>
					</table>
				</div>
					
					<p style="float:left"><a href="<?php echo base_url();?>" class="btn-not-login" id="cancel">Cancel</a>

				
				<p style="float:right"><a onclick="$('#form').submit();" class="button btn-not-login" id="submit">Submit</a></p>

				
			<?php echo form_close();?>
		</div><!-- .content-->
	</div><!-- .box -->
</div><!-- #content -->