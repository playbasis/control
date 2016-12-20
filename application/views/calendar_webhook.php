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
                    <a href="<?php echo site_url('calendar');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('calendar/place');?>" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('calendar/webhook');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
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

            <?php if (isset($calendar)) { ?>
                <?php echo form_open('calendar/webhook', array('id' => 'form')); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="center" style="width:10px;"><?php echo $this->lang->line('column_id'); ?></td>
                        <td class="center" style="width:10px;"><?php echo $this->lang->line('column_channel_id'); ?></td>
                        <td class="center" style="width:10px;"><?php echo $this->lang->line('column_resource_id'); ?></td>
                        <td class="center" style="width:10px;"><?php echo $this->lang->line('column_resource_uri'); ?></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_callback'); ?></td>
                        <td class="center" style="width:10px;"><?php echo $this->lang->line('column_date_expire'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($webhooks) && $webhooks) { ?>
                        <?php foreach ($webhooks as $webhook) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($webhook['selected']) && $webhook['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $webhook['resource_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $webhook['resource_id']; ?>" />
                                    <?php } ?></td>
                                <td class="left"><?php echo $webhook['calendar_id']; ?></td>
                                <td class="left"><?php echo $webhook['channel_id']; ?></td>
                                <td class="left"><?php echo $webhook['resource_id']; ?></td>
                                <td class="left"><?php echo $webhook['resource_uri']; ?></td>
                                <td class="left"><?php echo $webhook['callback_url']; ?></td>
                                <td class="left"><?php echo $webhook['date_expire']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="7"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo form_close();?>
            <?php } else { ?>
                Please set up your Google account with Playbasis first.
            <?php } ?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

<script type="text/javascript">
    $(document).ready(function(){
        $('[rel=popover]').popover({
            html: true
        });
    });
    $('body').on('click', function (e) {
        $('[rel="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
</script>