<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_purchase'); ?></button>
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
            	<?php $myplan = $this->session->userdata('plan'); ?>
            	<?php $free_flag = ($myplan['price'] <= 0); ?>
            	<?php $upgrade_downgrade_flag = in_array($mode, array(PURCHASE_UPGRADE, PURCHASE_DOWNGRADE)); ?>
            	<?php $allow_plan_selection_flag = ($free_flag || $upgrade_downgrade_flag); ?>
            	<input type="hidden" name="months" value="<?php echo $months; ?>" />
            	<?php if (!$allow_plan_selection_flag) { ?>
            	<input type="hidden" name="plan" value="<?php echo $myplan['_id']->{'$id'}; ?>" />
            	<?php } ?>
            	<div id="tab-general">
            		<table class="form">
			            <tr>
				            <td><span class="required">*</span> <?php echo $this->lang->line('form_package'); ?>:</td>
				            <td>
					            <select <?php if ($allow_plan_selection_flag) echo 'name="plan"'; else echo "disabled"; ?>>
						            <?php if ($allow_plan_selection_flag) { ?>
						                      <?php if ($plans) foreach ($plans as $plan) {
						                          if (array_key_exists('price', $plan) && $plan['price'] > 0) { // we display only plans with price > 0 (also discard plans without price)
						                              switch ($mode) {
						                              case PURCHASE_UPGRADE:
						                                  if ($plan['price'] >= $myplan['price']) {
						            ?><option value="<?php echo $plan['_id']->{'$id'}; ?>"><?php echo $plan['name'].' ($'.$plan['price'].')'; ?></option><?php
						                                  }
						                                  break;
						                              case PURCHASE_DOWNGRADE:
						                                  if ($plan['price'] <= $myplan['price']) {
						            ?><option value="<?php echo $plan['_id']->{'$id'}; ?>"><?php echo $plan['name'].' ($'.$plan['price'].')'; ?></option><?php
						                                  }
						                                  break;
						                              default:
						            ?><option value="<?php echo $plan['_id']->{'$id'}; ?>"><?php echo $plan['name'].' ($'.$plan['price'].')'; ?></option><?php
						                                  break;
						                              }
						                          }
						                      }
						            ?>
						            <?php } else { ?>
						            <option selected="selected" value="<?php echo $myplan['_id']->{'$id'}; ?>"><?php echo $myplan['name'].' ($'.$myplan['price'].')'; ?></option>
						            <?php } ?>
					            </select>
				            </td>
			            </tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_months'); ?>:</td>
            				<td>
					            <select disabled>
						            <option selected="selected"><?php echo $months; ?></option>
					            </select>
            				</td>
            			</tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_channel'); ?>:</td>
            				<td>
					            <select name="channel">
						            <option selected="selected" value="<?php echo PAYMENT_CHANNEL_PAYPAL; ?>"><?php echo $this->lang->line('text_paypal'); ?></option>
					            </select>
            				</td>
            			</tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->
