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
                    <a href="<?php echo site_url('jive');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('jive/place');?>" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('jive/event');?>" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('jive/webhook');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
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

            <?php if (isset($jive)) { ?>
                <?php echo form_open('jive/webhook'.(isset($offset) ? '/'.$offset : ''), array('id' => 'form')); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_type'); ?></td>
                        <td class="center" style="width:100px;"><?php echo $this->lang->line('column_description'); ?></td>
                        <td class="center" style="width:100px;"><?php echo $this->lang->line('column_callback'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($webhooks) && $webhooks) { ?>
                        <?php foreach ($webhooks as $webhook) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($webhook['selected']) && $webhook['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $webhook['webhookID']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $webhook['webhookID']; ?>" />
                                    <?php } ?></td>
                                <td class="center"><?php echo $webhook['events']; ?></td>
                                <td class="left"><?php echo $webhook['object']; ?></td>
                                <td class="left"><?php echo $webhook['callback']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="4"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo form_close();?>
            <?php } else { ?>
                Please set up your Jive community with Playbasis first.
            <?php } ?>
        </div><!-- .content -->
        <?php if (isset($jive)) { ?>
        <div class="pagination">
            <ul class='ul_rule_pagination_container'>
                <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                <?php echo $pagination_links; ?>
            </ul>
        </div>
        <?php } ?>
    </div><!-- .box -->
</div><!-- #content .span10 -->