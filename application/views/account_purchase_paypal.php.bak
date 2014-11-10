<script type="text/javascript" src="<?php echo base_url();?>javascript/paypal/paypal-button.min.js"></script>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'account/subscribe'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
            	<div id="tab-general">
            		<table class="form">
			            <tr>
				            <td><?php echo $this->lang->line('form_channel'); ?>:</td>
				            <td>
					            <select name="channel" disabled>
						            <option selected="selected" value="<?php echo PAYMENT_CHANNEL_PAYPAL; ?>"><?php echo $this->lang->line('text_paypal'); ?></option>
					            </select>
				            </td>
			            </tr>
			            <tr>
				            <?php $plan = $this->session->userdata('plan'); ?>
				            <td><?php echo $this->lang->line('form_price'); ?> (USD):</td>
				            <td><input type="text" value="<?php echo $params['price']; ?>" disabled /></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_product'); ?>:</td>
				            <td><input type="text" value="<?php echo PRODUCT_NAME; ?>" disabled /></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_effective_date'); ?>:</td>
				            <td><input type="text" value="<?php echo date('d M Y'); ?>" disabled /></td>
			            </tr>
			            <tr>
				            <td>
			            <script src="https://www.paypalobjects.com/js/external/paypal-button.min.js?merchant=<?php echo PAYPAL_MERCHANT_ID; ?>"
			                    data-button="subscribe"
			                    data-name="<?php echo PRODUCT_NAME; ?>"
			                    data-amount="<?php echo $params['price']; ?>"
			                    data-currency="USD"
			                    data-recurrence="1"
			                    data-period="M"
			                    data-custom="<?php echo $this->session->userdata('client_id')->{'$id'}.','.$params['plan_id']->{'$id'}; ?>"
			                    data-cancel_return="<?php echo base_url(); ?><?php echo (index_page() == '')? '' : index_page()."/"; ?>account/subscribe"
			                    data-return="<?php echo base_url(); ?><?php echo (index_page() == '')? '' : index_page()."/"; ?>account/paypal_completed"
			                    data-callback="<?php echo $params['callback']; ?>"
			                    data-env="<?php echo PAYPAL_ENV; ?>"
			            ></script>
				            </td>
				            <td>&nbsp;<?php echo $params['modify'] ? 'Your selected plan will be effective at the next billing cycle' : ''; ?></td>
			            </tr>
            		</table>
            	</div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
<script type="text/javascript"><!--
	$(document).ready(function(){
		/* enable recurring payment */
		$('<input>').attr({
			type: 'hidden',
			name: 'src', // enable flag, and, if enable, keep recurring until subscribers cancel their subscriptions
			value: 1
		}).appendTo('form');
		$('<input>').attr({
			type: 'hidden',
			name: 'sra', // reattempt on failure
			value: 1
		}).appendTo('form');
		/* enable trial period */
		<?php if ($params['trial_days'] > 0) { ?>
		$('<input>').attr({
			type: 'hidden',
			name: 'a1', // price
			value: 0
		}).appendTo('form');
		$('<input>').attr({
			type: 'hidden',
			name: 'p1', // duration
			value: <?php echo $params['trial_days']; ?> <!-- allowed value: 1-90 -->
		}).appendTo('form');
		$('<input>').attr({
			type: 'hidden',
			name: 't1', // unit of duration
			value: 'D'
		}).appendTo('form');
		<?php } ?>
		/* enable upgrade/downgrade */
		$('<input>').attr({
			type: 'hidden',
			name: 'modify', // modification behavior
			value: <?php echo $params['modify'] ? PAYPAL_MODIFY_CURRENT_SUBSCRIPTION_ONLY : PAYPAL_MODIFY_NEW_SUBSCRIPTION_ONLY; ?>
		}).appendTo('form');
	});
//--></script>
