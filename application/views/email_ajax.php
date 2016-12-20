<?php
                $attributes = array('id' => 'form');
                echo form_open('email/delete',$attributes);
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
                            <?php foreach ($templates as $email) { ?>
                            <tr <?php if (isset($email["is_template"]) && $email["is_template"]) {?> class="email_template" <?php } ?>>
                                <td style="text-align: center;">
                                    <?php if ($email['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $email['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $email['_id']; ?>" />
                                    <?php } ?>
                                </td>
                                <td class="left"><?php echo $email['name']; ?></td>
                                <td class="left"><?php echo $email['body']; ?>  </td>
                                <td class="left"><?php echo ($email['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo $email['sort_order']; ?></td>
                                <td class="right">

                                    <?php echo anchor('email/update/'.$email['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                        array('class'=>'tooltips',
                                            'title' => 'Edit',
                                            'data-placement' => 'top'
                                        ));
                                    ?>
                                    <?php echo anchor('email/increase_order/'.$email['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$email['_id'], 'style'=>'text-decoration:none'));?>
                                    <?php echo anchor('email/decrease_order/'.$email['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$email['_id'], 'style'=>'text-decoration:none' ));?>
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