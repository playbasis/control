<div id="content" class="span10 plan-price-wrapper">
        
        <?php echo $this->view('partial/plans_table'); ?>
      
        <?php if($this->session->flashdata('fail')){ ?>
                    <div class="content messages half-width">
                        <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                    </div>
                <?php }?>
                <?php if(validation_errors() || isset($message)){?>
                    <div class="content messages half-width">
                        <?php echo validation_errors('<div class="warning">', '</div>');?>
                        <?php if (isset($message) && $message){?>
                            <div class="warning"><?php echo $message;?></div>
                        <?php }?>
                    </div>
                <?php }?>

                <?php $attributes = array('id' => 'form');?>
                <?php echo form_open($form ,$attributes); ?>

                                    <input type="hidden" name="plan" >
                                    <input type="hidden" name="channel" value="<?php echo PAYMENT_CHANNEL_DEFAULT; ?>">
                                
                <?php echo form_close(); ?>
            
</div><!-- #content .span10 -->

