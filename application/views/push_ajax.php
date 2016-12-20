<?php
                $attributes = array('id' => 'form');
                echo form_open('push/delete',$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="left" ><?php echo $this->lang->line('column_body'); ?></td>
                            <td class="left" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($templates) && $templates) { ?>
                            <?php foreach ($templates as $each) { ?>
                            <tr <?php if (isset($each["is_template"]) && $each["is_template"]) {?> class="push_template" <?php } ?>>
                                <td style="text-align: center;">
                                    <?php if ($each['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $each['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $each['_id']; ?>" />
                                    <?php } ?>
                                </td>
                                <td class="left"><?php echo $each['name']; ?></td>
                                <td class="left"><?php echo $each['body']; ?>  </td>
                                <td class="left"><?php echo ($each['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo $each['sort_order']; ?></td>
                                <td class="right">

                                    <?php echo anchor('push/update/'.$each['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                        array('class'=>'tooltips',
                                            'title' => 'Edit',
                                            'data-placement' => 'top'
                                        ));
                                    ?>
                                    <?php echo anchor('push/inscrease_order/'.$each['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$each['_id'], 'style'=>'text-decoration:none'));?>
                                    <?php echo anchor('push/decrease_order/'.$each['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$each['_id'], 'style'=>'text-decoration:none' ));?>
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