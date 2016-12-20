<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_watch'); ?></button>
            </div>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('jive');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('jive/place');?>" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('jive/event');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('jive/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
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
                <?php echo form_open('jive/event', array('id' => 'form')); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_type'); ?></td>
                        <td class="center" style="width:100px;"><?php echo $this->lang->line('column_description'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($events) && $events) { ?>
                        <?php foreach ($events as $event) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($event['selected']) && $event['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $event['id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $event['id']; ?>" />
                                    <?php } ?></td>
                                <td class="center"><?php echo $event['type']; ?></td>
                                <td class="left"><?php echo $event['description']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="3"><?php echo $this->lang->line('text_no_results'); ?></td>
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