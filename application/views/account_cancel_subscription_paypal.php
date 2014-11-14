<div id="content" class="span10">
    
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

    <div class="purchase-summary-wrapper box-gray">

            <h1>Change plan to PLAYBASIS API SUBSCRIPTION BASIC</h1>
            <h2>You'll be charged <strong>$0</strong>/Month </h2>
            <p>Your payment for this purchase is processed securely by <img src="<?php echo base_url();?>image/payment/paypal-newlogo.png"></p>
            <a href="https://www.sandbox.<?php echo PAYPAL_ENV == 'sandbox' ? PAYPAL_ENV.'.' : '' ?>paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=<?php echo PAYPAL_MERCHANT_ID; ?>" class="btn-hero">Change Your Package</a>
    </div> 
</div><!-- #content .span10 -->
