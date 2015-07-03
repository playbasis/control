<script type="text/javascript" src="<?php echo base_url();?>javascript/paypal/paypal-button.min.js"></script>
<div id="content" class="span10">

    <div class="purchase-summary-wrapper box-gray">
    		<h1><?php echo $params['modify'] ? 'Change plan to' : 'Upgrade plan to'; ?> <?php echo PRODUCT_NAME; ?> <?php echo $params['plan_name']; ?></h1>
    		<h2>You'll be charged <strong>$<?php echo $params['price']; ?></strong>/Month </h2>
    		<p>Your payment for this purchase is processed securely by <img src="<?php echo base_url();?>image/payment/stripe.png"></p>
            <?php $attributes = array('id' => 'form', 'class' => 'pbf-form text-center');?>
            <?php echo form_open($form, $attributes);?>
            <input type="hidden" name="plan_id" value="<?php echo $params['plan_id']; ?>">
            <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="<?php echo $params['publishable_key']; ?>"
                data-name="<?php echo PRODUCT_NAME; ?>"
                data-description="Subscription to Playbasis platform"
                data-currency="usd"
                data-amount="<?php echo $params['price']*100; ?>"
                data-panel-label="Subscribe"
                >
            </script>
            <?php echo form_close();?>
    </div>

</div>