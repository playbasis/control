<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
	                <?php $free_flag = ($plan['price'] == 0); ?>
	                <?php if ($free_flag) { ?> You are in a free package, please <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_subscribe'); ?></button><?php } ?>
                    <?php if (!$free_flag && $client['trial_flag']) { ?> You are in a trial period, please <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_subscribe'); ?></button><?php } ?>
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
				            <td><?php echo $this->lang->line('form_name'); ?>:</td>
				            <td><?php echo $client['first_name'].' '.$client['last_name']; ?></td>
			            </tr>
            			<tr>
            				<td><?php echo $this->lang->line('form_company'); ?>:</td>
            				<td><?php echo $client['company']; ?></td>
            			</tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_email'); ?>:</td>
				            <td><?php echo $client['email']; ?></td>
			            </tr>
            			<!--tr>
            				<td><?php echo $this->lang->line('form_status'); ?>:</td>
            				<td>
            					<select disabled>
            					<?php if($client['status'] || set_value('status')){?>
            						<option selected="selected" value="1">Enabled</option>
            					<?php }else{?>
            						<option selected="selected" value="0">Disabled</option>
            					<?php }?>
            					</select>
            				</td>
            			</tr-->
			            <tr>
				            <td><?php echo $this->lang->line('form_date_added'); ?>:</td>
				            <td><?php echo date('d M Y', $client['date_added']); ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_plan_name'); ?>:</td>
				            <td><?php echo $plan['name']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_plan_price'); ?>:</td>
				            <td><?php echo $plan['price']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_plan_registration'); ?>:</td>
				            <td><?php echo date('d M Y', $plan['registration_date_modified']); ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_trial_flag'); ?>:</td>
				            <td><?php echo $client['trial_flag'] ? 'TRUE' : 'FALSE'; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_trial_days'); ?>:</td>
				            <td><?php echo $client['trial_days']; ?></td>
			            </tr>
			            <tr>
				            <td><?php echo $this->lang->line('form_trial_remaining_days'); ?>:</td>
				            <td><?php echo $client['trial_remaining_days']; ?></td>
			            </tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
