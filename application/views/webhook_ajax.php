<?php
$attributes = array('id' => 'form');
echo form_open('webhook/delete',$attributes);
?>
    <table class="list">
        <thead>
        <tr>
            <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            <td class="left" style="width:200px;"><?php echo $this->lang->line('column_name'); ?></td>
            <td class="left"><?php echo $this->lang->line('column_url'); ?></td>
            <td class="left" ><?php echo $this->lang->line('column_body'); ?></td>
            <td class="left" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templates) && $templates) { ?>
            <?php foreach ($templates as $webhook) { ?>
                <tr <?php if (isset($webhook["is_template"]) && $webhook["is_template"]) {?> class="sms_template" <?php } ?>>
                    <td style="text-align: center;">
                        <?php if ($webhook['selected']) { ?>
                            <input type="checkbox" name="selected[]" value="<?php echo $webhook['_id']; ?>" checked="checked" />
                        <?php } else { ?>
                            <input type="checkbox" name="selected[]" value="<?php echo $webhook['_id']; ?>" />
                        <?php } ?>
                    </td>
                    <td class="left"><?php echo $webhook['name']; ?></td>
                    <td class="left"><?php echo $webhook['url']; ?>  </td>
                    <td class="left"><?php echo $webhook['body']; ?>  </td>
                    <td class="left"><?php echo ($webhook['status'])? "Enabled" : "Disabled"; ?></td>
                    <td class="right"><?php echo $webhook['sort_order']; ?></td>
                    <td class="right">

                        [ <?php echo anchor('webhook/update/'.$webhook['_id'], 'Edit'); ?> ]
                        <?php echo anchor('webhook/inscrease_order/'.$webhook['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$webhook['_id'], 'style'=>'text-decoration:none'));?>
                        <?php echo anchor('webhook/decrease_order/'.$webhook['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$webhook['_id'], 'style'=>'text-decoration:none' ));?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td class="center" colspan="6"><?php echo $this->lang->line('text_no_results'); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php
echo form_close();?>