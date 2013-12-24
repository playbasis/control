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
	        	</table>
	        <?php echo form_close();?>
        </div>
    </div><!-- .box -->
</div><!-- #content .span10 -->