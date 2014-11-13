<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
	                <?php if ($plan['free_flag']) { ?> You are in a free package, please <a href="<?php echo current_url();?>/subscribe" class="btn btn-primary"><?php echo $this->lang->line('button_subscribe'); ?></a><?php } ?>
	                <?php if ($plan['paid_flag'] && !$client['date_billing']) { ?> Before start using our service, please <a href="<?php echo current_url();?>/subscribe" class="btn btn-primary"><?php echo $this->lang->line('button_setup_payment_detail'); ?></a><?php } ?>
	                <?php if ($client['date_billing']) { ?>
	                Please stay with your current plan for at least 6 months before
	                <a href="<?php echo current_url();?>/upgrade" class="btn btn-primary"><?php echo $this->lang->line('button_upgrade'); ?></a>
	                <a href="<?php echo current_url();?>/downgrade" class="btn btn-primary"><?php echo $this->lang->line('button_downgrade'); ?></a>
	                <a href="<?php echo current_url();?>/cancel_subscription" class="btn btn-primary"><?php echo $this->lang->line('button_cancel_subscription'); ?></a>
	                <?php } ?>
	            <br>
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
            	<div id="tab-general">
            		<table class="form">
			            <tr>
				            <td><?php echo $this->lang->line('text_company'); ?>:</td>
				            <td><?php echo $client['company']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_email'); ?>:</td>
				            <td><?php echo $client['email']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_date_added'); ?>:</td>
				            <td><?php echo date('d M Y', $client['date_added']); ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_valid'); ?>:</td>
				            <td>
                                <span class="label <?php echo $client['valid'] ? "label-success" : "label-important"; ?>"><?php echo $client['valid'] ? $this->lang->line('text_enabled') : $this->lang->line('text_disabled'); ?></span>
				            </td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_valid_api'); ?>:</td>
				            <td>
				            <?php if ($client['valid']) { ?>
				                <?php $start = ($client['date_start'] ? date('d M Y', $client['date_start']) : ''); ?>
				                <?php $end = ($client['date_expire'] ? date('d M Y', $client['date_expire']) : ''); ?>
				                <?php if ($start && $end) echo $start.' - '.$end; ?>
				                <?php if (!$start && $end) echo 'Until '.$end; ?>
				                <?php if ($start && !$end) echo 'From '.$start; ?>
				                <?php if (!$start && !$end) echo $this->lang->line('text_unlimited'); ?>
				            <?php } else { ?>
				                <?php echo $this->lang->line('text_not_available'); ?>
				            <?php } ?>
				            </td>
			            </tr>
			            <?php if ($plan['paid_flag']) { ?>
			            <tr>
				            <td>&nbsp;</td>
				            <td>&nbsp;</td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_plan_name'); ?>:</td>
				            <td><?php echo $plan['name']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_plan_price'); ?>:</td>
				            <td><?php echo $plan['price']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_plan_subscription_date'); ?>:</td>
				            <td><?php echo date('d M Y', $plan['date_modified']); ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_billing_date'); ?>:</td>
				            <td><?php echo $client['date_billing'] ? date('d M Y', $client['date_billing']) : $this->lang->line('text_not_available'); ?></td>
			            </tr>
			            <?php if ($plan['trial_total_days'] > 0) { ?>
				            <?php $days_used = $plan['trial_total_days'] - $client['trial_remaining_days']; ?>
				            <?php if ($days_used < 0) $days_used = 0; ?>
				            <?php if ($days_used > $plan['trial_total_days']) $days_used = $plan['trial_total_days']; ?>
			            <tr>
				            <td><?php echo $this->lang->line('text_trial'); ?>:</td>
				            <?php if (!$client['date_billing']) { ?>
				            <td><?php echo $this->lang->line('text_trial_not_begin'); ?> <?php echo '('.$plan['trial_total_days'].' days)'; ?></td>
				            <?php } else { ?>
				            <td><?php echo $client['trial_remaining_days'] >= 0 ? $this->lang->line('text_yes') : $this->lang->line('text_no'); ?> <?php echo '('.$days_used.'/'.$plan['trial_total_days'].')'; ?></td>
				            <?php } ?>
			            </tr>
			            <?php } ?>
				            <?php if ($client['date_billing'] && $client['trial_remaining_days'] >= 0) { ?>
			            <tr>
				            <td><?php echo $this->lang->line('text_trial_remaining_days'); ?>:</td>
				            <td><?php echo $client['trial_remaining_days']; ?></td>
			            </tr>
				            <?php } ?>
			            <?php } ?>
            		</table>
            	</div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
