<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $channel_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_purchase'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'account'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
				            <td><span class="required">*</span> <?php echo $this->lang->line('form_package'); ?>:</td>
				            <td>
					            <?php $plan = $this->session->userdata('plan'); ?>
					            <?php $free_flag = ($plan['price'] == 0); ?>
					            <select name="plan" <?php if (!$free_flag) echo "readonly"; ?>>
						            <?php if ($free_flag) { ?>
							            <?php if ($plans) foreach ($plans as $plan) { ?>
								            <?php if (array_key_exists('price', $plan) && $plan['price'] > 0) { ?>
						            <option value="<?php echo $plan['_id']->{'$id'}; ?>"><?php echo $plan['name'].' ($'.$plan['price'].')'; ?></option>
								            <?php } ?>
						                <?php } ?>
						            <?php } else { ?>
						            <option selected="selected" value="<?php echo $plan['_id']->{'$id'}; ?>"><?php echo $plan['name'].' ($'.$plan['price'].')'; ?></option>
						            <?php } ?>
					            </select>
				            </td>
			            </tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_months'); ?>:</td>
            				<td>
            					<select name="months" readonly>
						            <option selected="selected" value="12">12 Months</option>
            					</select>
            				</td>
            			</tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_channel'); ?>:</td>
            				<td>
					            <select name="channel">
						            <option selected="selected" value="paypal">PayPal</option>
					            </select>
            				</td>
            			</tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
