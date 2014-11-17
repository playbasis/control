<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
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
            	<?php $plan = $this->session->userdata('plan'); ?>
            	<div id="tab-general">
            		<table class="form">
			            <tr>
				            <td><A HREF="https://www.<?php echo PAYPAL_ENV == 'sandbox' ? PAYPAL_ENV.'.' : '' ?>paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=<?php echo PAYPAL_MERCHANT_ID; ?>" target="_blank"><IMG BORDER="0" SRC="https://www.paypalobjects.com/en_US/i/btn/btn_unsubscribe_LG.gif"></A></td>
				            <td>&nbsp;</td>
			            </tr>
            		</table>
            	</div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
