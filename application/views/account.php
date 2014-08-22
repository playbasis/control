<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
	                <?php if ($plan['free_flag']) { ?> You are in a free package, please <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_subscribe'); ?></button><?php } ?>
	                <?php if ($plan['paid_flag'] && !$client['date_billing']) { ?> Before start using our service, please <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_update_payment_detail'); ?></button><?php } ?>
	                <?php if ($client['date_billing'] /* TODO: check that current plan is not the maximum paid plan by price */) { ?><a href="<?php echo base_url();?>account/upgrade_plan" class="btn btn-primary"><?php echo $this->lang->line('button_upgrade'); ?></a><?php } ?>
	                <?php if ($client['date_billing'] /* TODO: check that current plan is not the minimum paid plan by price */) { ?><a href="<?php echo base_url();?>account/downgrade_plan" class="btn btn-primary"><?php echo $this->lang->line('button_downgrade'); ?></a><?php } ?>
	                <?php if ($client['date_billing'] && time() < $client['date_billing']) { ?><a href="<?php echo base_url();?>account/cancel_subscription" class="btn btn-primary"><?php echo $this->lang->line('button_cancel_subscription'); ?></a><?php } ?><!-- can cancel within a trial period without penalty -->
	            <br>
	            <!--
	            // if free (plan-price-0), offer them a choice to +upgrade<br>
	            // if trial period has not ended, offer them a choice to quickly +subscribe now<br>
	            // if trial period has ended (+email-bill-0), and does not subscribe yet, daily alert them that they have only 5 days for registration before we really block API +subscribe +email-reminder-to-subscribe<br>
	            // if trial period has ended (+email-bill-0), and does not subscribe yet, and it has passed 5-day grace period, warning them that we already block API, please ASAP +subscribe<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and it is not at the billing date yet, offer +upgrade-choice<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and it is near billing date, offer +upgrade-choice +email-invoice<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and it is at billing date, and payment fail (+email, +email-Playbasis), daily alert them that they have only 5 days for fixing before we really block API +fix-fail-payment +email-reminder-to-fix<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and it is at billing date, and payment fail (+email, +email-Playbasis), and it has passed 5-day grace period, warning them that we already block API (+email), please ASAP +fix-fail-payment<br>
	            // if trial period has ended (+email-bill-0), has subscribed, payment success (+email-congrat, +email-Playbasis), offer +upgrade-choice<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and the contract is about to expire, alert them to +extend +email-warning<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and the contract has expired (+email), daily alert them that they have only 5 days for registration before we really block API +subscribe +email-reminder-to-subscribe<br>
	            // if trial period has ended (+email-bill-0), has subscribed, and the contract has expired (+email), and it has passed 5-day grace period, warning them that we already block API, please ASAP +subscribe<br>
	            -->
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
				            <td><?php echo $this->lang->line('text_name'); ?>:</td>
				            <td><?php echo $client['first_name'].' '.$client['last_name']; ?></td>
			            </tr>
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
				                <select disabled>
				                    <option selected="selected"><?php echo $client['valid'] ? $this->lang->line('text_enabled') : $this->lang->line('text_disabled'); ?></option>
				                </select>
				            </td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('text_valid_api'); ?>:</td>
				            <td>
					            <?php $start = ($client['date_start'] ? date('d M Y', $client['date_start']) : ''); ?>
					            <?php $end = ($client['date_expire'] ? date('d M Y', $client['date_expire']) : ''); ?>
					            <?php if ($start && $end) echo $start.' - '.$end; ?>
					            <?php if (!$start && $end) echo 'Until '.$end; ?>
					            <?php if ($start && !$end) echo 'From '.$start; ?>
					            <?php if (!$start && !$end) echo $this->lang->line('text_unlimited'); ?>
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
				            <?php $days_used = $plan['trial_total_days'] - $client['trial_remaining_days']; ?>
				            <?php if ($days_used < 0) $days_used = 0; ?>
				            <?php if ($days_used > $plan['trial_total_days']) $days_used = $plan['trial_total_days']; ?>
			            <tr>
				            <td><?php echo $this->lang->line('text_trial'); ?>:</td>
				            <?php if (!$client['date_billing']) { ?>
				            <td><?php echo $this->lang->line('text_trial_not_begin'); ?></td>
				            <?php } else { ?>
				            <td><?php echo $client['trial_remaining_days'] >= 0 ? $this->lang->line('text_yes') : $this->lang->line('text_no'); ?> <?php echo '('.$days_used.'/'.$plan['trial_total_days'].')'; ?></td>
				            <?php } ?>
			            </tr>
				            <?php if ($client['date_billing'] && $client['trial_remaining_days'] >= 0) { ?>
			            <tr>
				            <td><?php echo $this->lang->line('text_trial_remaining_days'); ?>:</td>
				            <td><?php echo $client['trial_remaining_days']; ?></td>
			            </tr>
				            <?php } ?>
			            <?php } ?>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
