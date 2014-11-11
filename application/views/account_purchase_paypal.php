<script type="text/javascript" src="<?php echo base_url();?>javascript/paypal/paypal-button.min.js"></script>
<div id="content" class="span10">

    <div class="purchase-summary-wrapper box-gray">
    		<h1>Upgrade to <?php echo PRODUCT_NAME; ?> <?php echo $params['plan_name']; ?></h1>
    		<h2>You'll be charged <strong>$<?php echo $params['price']; ?></strong>/Month </h2>
    		<p>Your payment for this purchase is processed securely by <img src="<?php echo base_url();?>image/payment/paypal-newlogo.png"></p>
    		<!--p>Enjoy 30 days trial. Start to pay on <?php echo date('d M Y'); ?></p-->
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
    		<!-- <a href="" class="btn-hero">Upgrade Your Package</a> -->
    </div>

    

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
			value: <?php echo $params['trial_days']; ?> //allowed value: 1-90
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

		$('form button[type=submit]').text('Upgrade Your Package')
	});
//--></script>

<style type="text/css">
	.paypal-button button.large{
		font-size: 16px;
		font-weight: 600;
		line-height: normal;
		white-space: normal;
		text-align: center;
		color: #FFF;
		text-shadow: 0px -1px #BF4C1B;
		box-shadow: 0px 1px #BF4C1B;
		background: linear-gradient(#F26023, #ED500E) repeat scroll 0% 0% #F26023;
		width: auto;
		border: medium none;
		padding: 10px 20px;
		border-radius: 5px;
		text-transform: uppercase;
	}
	.paypal-button button.large:hover{
		color: #fff;
		text-decoration: none;
		outline: medium none;
		background: linear-gradient(#FF5B16, #FE5B17) repeat scroll 0% 0% #FF5B16;
	}
	.paypal-button button:after,
	.paypal-button button:before{
		display: none;
	}
	.paypal-button button{
		font-style: normal;
	}
</style>
