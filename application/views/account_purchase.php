<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $payment_title; ?></h1>
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
            				<td><?php echo $this->lang->line('form_months'); ?>:</td>
            				<td>
            					<select name = 'month' onchange="calculate(this.value, <?php echo $this->session->userdata('price') ?>); return true;">
						            <option selected='selected' value="">N/A</option>
						            <option value="1">1 Month</option>
						            <option value="2">2 Months</option>
						            <option value="3">3 Months</option>
						            <option value="6">6 Months</option>
						            <option value="12">12 Months</option>
						            <option value="24">24 Months</option>
            					</select>
            				</td>
            			</tr>
			            <tr>
				            <td><span class="required">*</span> <?php echo $this->lang->line('form_credit_to_add'); ?> (USD):</td>
				            <td><input type="text" name="credit" onchange="$('[name=month]').val('')" /></td>
			            </tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_channel'); ?>:</td>
            				<td>
					            <select name = 'channel'>
						            <option selected='selected' value="paypal">PayPal</option>
					            </select>
            				</td>
            			</tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
<script type="text/javascript">
function calculate(val, price) {
	$('[name=credit]').val(val*price)
}
</script>