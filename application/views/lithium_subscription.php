<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('lithium');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('lithium/event');?>" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('lithium/subscription');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_subscription'); ?></a>
                </div>

            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>

            <?php if (isset($lithium)) { ?>
                <?php echo form_open('lithium/subscription', array('id' => 'form')); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_id'); ?></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_type'); ?></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_token'); ?></td>
                        <td class="center" style="width:100px;"><?php echo $this->lang->line('column_callback'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($subscriptions) && $subscriptions) { ?>
                        <?php foreach ($subscriptions as $subscription) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($subscription['selected']) && $subscription['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $subscription['token']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $subscription['token']; ?>" />
                                    <?php } ?></td>
                                <td class="center"><?php echo $subscription['id']; ?></td>
                                <td class="center"><?php echo $subscription['type']; ?></td>
                                <td class="left"><?php echo $subscription['token']; ?></td>
                                <td class="left"><?php echo $subscription['callback']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="5"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo form_close();?>
            <?php } else { ?>
                Please set up your Lithium community with Playbasis first.
            <?php } ?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->