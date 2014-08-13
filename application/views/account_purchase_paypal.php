<script type="text/javascript" src="<?php echo base_url();?>javascript/paypal/paypal-button.min.js"></script>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $order_title; ?></h1>
            <div class="buttons">
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
				            <td><?php echo $this->lang->line('form_channel'); ?>:</td>
				            <td>
					            <select name = 'channel' disabled>
						            <option selected='selected' value="paypal">PayPal</option>
					            </select>
				            </td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_outstanding_amount'); ?> (USD):</td>
				            <td><input type="text" value="<?php echo $this->session->userdata('credit'); ?>" disabled /></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_product'); ?> (USD):</td>
				            <td><input type="text" value="Playbasis Platform Service" disabled /></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_transaction_date'); ?> (USD):</td>
				            <td><input type="text" value="<?php echo date('d M Y'); ?>" disabled /></td>
			            </tr>
			            <script async="async" src="https://www.paypalobjects.com/js/external/paypal-button.min.js?merchant=pechpras-facilitator@playbasis.com"
			                    data-button="buynow"
			                    data-name="Playbasis Platform"
			                    data-quantity="1"
			                    data-amount="<?php echo $this->session->userdata('credit'); ?>"
			                    data-currency="USD"
			                    data-callback="<?php echo base_url();?><?php echo (index_page() == '')? '' : index_page()."/" ?>account/payment_notification"
			                    data-env="sandbox"
				        ></script>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
