<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_confirm'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'account'" type="button"><?php echo $this->lang->line('button_close'); ?></button>
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
            	<input type="hidden" name="channel" value="<?php echo PAYMENT_CHANNEL_PAYPAL; ?>" />
            	<?php $plan = $this->session->userdata('plan'); ?>
            	<div id="tab-general">
            		<table class="form">
			            <tr>
				            <td><span class="required">*</span> <?php echo $this->lang->line('form_package'); ?>:</td>
				            <td>
					            <select disabled>>
						            <option selected="selected" value="<?php echo $plan['_id']->{'$id'}; ?>"><?php echo $plan['name'].' ($'.$plan['price'].')'; ?></option>
					            </select>
				            </td>
			            </tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_months'); ?>:</td>
            				<td>
					            <select disabled>
						            <option selected="selected"><?php echo MONTHS_PER_PLAN; ?></option>
					            </select>
            				</td>
            			</tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_channel'); ?>:</td>
            				<td>
					            <select disabled>
						            <option selected="selected" value="<?php echo PAYMENT_CHANNEL_PAYPAL; ?>"><?php echo $this->lang->line('text_paypal'); ?></option>
					            </select>
            				</td>
            			</tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
