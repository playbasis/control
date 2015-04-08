<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('jive');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('jive/place');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('jive/event');?>" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('jive/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
                </div>

            <?php if (isset($jive)) { ?>
                <?php echo form_open('jive/place', array('id' => 'form')); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_type'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_description'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_total_followers'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_total_views'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_creator'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($places) { ?>
                        <?php foreach ($places as $place) { ?>
                            <?php $place = (array) $place; ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($place['selected']) && $place['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $place['placeID']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $place['placeID']; ?>" />
                                    <?php } ?></td>
                                <td class="left"><?php echo $place['type']; ?></td>
                                <td class="left"><?php echo $place['name']; ?></td>
                                <td class="right"><?php echo $place['description']; ?></td>
                                <td class="right"><?php echo $place['followerCount']; ?></td>
                                <td class="right"><?php echo $place['viewCount']; ?></td>
                                <td class="right"><?php echo isset($place['creator']) ? $place['creator']->displayName : ''; ?></td>
                                <td class="right"><?php echo ($place['status']!=='Active')? $this->lang->line('text_disabled') : $this->lang->line('text_enabled'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="9"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo form_close();?>
            <?php } else { ?>
                Please set up your Jive community with Playbasis first.
            <?php } ?>

        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->