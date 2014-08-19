<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $subscribe_title; ?></h1>
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
					            <select name="price">
						            <option value="29">Starter ($29)</option>
						            <option selected="selected" value="99">Standard ($99)</option>
					            </select>
				            </td>
			            </tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_months'); ?>:</td>
            				<td>
            					<select name="months">
						            <option value="6">6 Months</option>
						            <option selected="selected" value="12">12 Months</option>
						            <option value="24">24 Months</option>
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
