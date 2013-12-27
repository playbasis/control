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
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_password');?>: </td>
							<td><input type = "password" name="password" size="50" value = ""></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_confirm_password');?>: </td>
							<td><input type = "password" name="password_confirm" size="50" value =""></td>
						</tr>			
					</table>
				</div>
					
					<p style="float:left"><a href="<?php echo base_url();?>" id="cancel">Cancel</a>

				
				<p style="float:right"><a onclick="$('#form').submit();" class="button" id="submit">Register</a></p>

				
			<?php echo form_close();?>
		</div><!-- .content-->
	</div><!-- .box -->
</div><!-- #content -->