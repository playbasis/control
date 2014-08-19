<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                    You are in a trial period, please <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_subscribe'); ?></button>
            </div><!-- .buttons -->
        </div><!-- .heading -->
        <div class="content">
        	<?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
            <?php if(validation_errors() || isset($message)){?>
            	<div class="content messages half-width">
            		<?php echo validation_errors('<div class="warning">', '</div>');?>
            		<?php if (isset($message) && $message){?>
            			<div class="warning"><?php //echo $message;?></div>
            		<?php }?>
            	</div>
            <?php }?>
            <?php $attributes = array('id' => 'form');?>
            <?php echo form_open($form, $attributes);?>
            	<div id="tab-general">
            		<table class="form">
			            <tr>
				            <td><?php echo $this->lang->line('form_name'); ?>:</td>
				            <td><?php echo $client['first_name'].' '.$client['last_name']; ?></td>
			            </tr>
            			<tr>
            				<td><?php echo $this->lang->line('form_company'); ?>:</td>
            				<td><?php echo $client['company']; ?></td>
            			</tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_email'); ?>:</td>
				            <td><?php echo $client['email']; ?></td>
			            </tr>
            			<tr>
            				<td><?php echo $this->lang->line('form_status'); ?>:</td>
            				<td>
            					<select name = 'status' disabled>
            					<?php if($client['status'] || set_value('status')){?>
            						<option selected='selected' value="1">Enabled</option>
            						<option value="0">Disabled</option>
            					<?php }else{?>
                                    <option value="1">Enabled</option>
            						<option selected='selected' value="0">Disabled</option>
            					<?php }?>
            					</select>
            				</td>
            			</tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_plan_name'); ?>:</td>
				            <td><?php echo $plan['name']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_plan_price'); ?>:</td>
				            <td><?php echo $plan['price']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_plan_registration'); ?>:</td>
				            <td><?php echo date('d M Y', $plan['registration_date_modified']); ?></td>
			            </tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
