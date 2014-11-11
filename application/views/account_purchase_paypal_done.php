<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
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
            <div class="purchase-summary-wrapper box-gray">
                <h1>Congratulation!</h1>
                <h2>Your current package is</h2>
                <h1><?php echo PRODUCT_NAME; ?> <?php echo $params['plan_name']; ?></h1>
                <a href="<?php echo current_url();?>" class="btn-hero">Go to Dashboard</a>
            </div>
            
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
