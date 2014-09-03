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
			            <form method="post" action="https://www.<?php echo PAYPAL_ENV == 'sandbox' ? PAYPAL_ENV.'.' : '' ?>paypal.com/cgi-bin/webscr" class="paypal-button" target="_top">
				            <div class="hide" id="errorBox"></div>
				            <input type="hidden" name="button" value="subscribe">
				            <input type="hidden" name="item_name" value="<?php echo PRODUCT_NAME; ?>">
				            <input type="hidden" name="currency_code" value="USD">
				            <input type="hidden" name="a3" value="<?php echo $params['price']; ?>">
				            <input type="hidden" name="p3" value="1">
				            <input type="hidden" name="t3" value="M">
				            <input type="hidden" name="custom" value="<?php echo $this->session->userdata('client_id')->{'$id'}.','.$params['plan_id']->{'$id'}; ?>">
				            <input type="hidden" name="cancel_return" value="<?php echo base_url(); ?><?php echo (index_page() == '')? '' : index_page()."/"; ?>account/subscribe">
				            <input type="hidden" name="return" value="<?php echo base_url(); ?><?php echo (index_page() == '')? '' : index_page()."/"; ?>account/paypal_completed">
				            <input type="hidden" name="notify_url" value="<?php echo $params['callback']; ?>">
				            <input type="hidden" name="cmd" value="_xclick-subscriptions">
				            <input type="hidden" name="business" value="<?php echo PAYPAL_MERCHANT_ID; ?>">
				            <input type="hidden" name="bn" value="JavaScriptButton_subscribe">
				            <button type="submit" class="paypal-button large">Subscribe</button>
				            <input type="hidden" name="src" value="1">
				            <input type="hidden" name="sra" value="1">
				            <?php if ($params['trial_days'] > 0) { ?>
				            <input type="hidden" name="a1" value="0">
				            <input type="hidden" name="p1" value="<?php echo $params['trial_days']; ?>">
				            <input type="hidden" name="t1" value="D">
				            <?php } ?>
				            <?php if ($params['trial2_days'] > 0) { ?>
				            <input type="hidden" name="a2" value="<?php echo $params['trial2_price']; ?>">
				            <input type="hidden" name="p2" value="<?php echo $params['trial2_days']; ?>">
				            <input type="hidden" name="t2" value="D">
				            <?php } ?>
				            <input type="hidden" name="modify" value="<?php echo $params['modify'] ? PAYPAL_MODIFY_CURRENT_SUBSCRIPTION_ONLY : PAYPAL_MODIFY_NEW_SUBSCRIPTION_ONLY; ?>">
			            </form>
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
            		</table>
            	</div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
