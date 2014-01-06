<?php
                $attributes = array('id' => 'form');
                echo form_open('badge/delete',$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="left" style="width:50px;"><?php echo $this->lang->line('column_quantity'); ?></td>
                            <td class="left" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($badges)) { ?>
                            <?php foreach ($badges as $badge) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if ($badge['selected']) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
                                    <?php } ?></td>
                                <td class="left"><div class="image"><img src="<?php echo $badge['image']; ?>" alt="" id="thumb" /></div></td>
                                <td class="left"><?php echo $badge['name']; ?></td>
                                <td class="right"><?php echo $badge['quantity']; ?></td>
                                <td class="left"><?php echo ($badge['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo $badge['sort_order']; ?></td>
                                <td class="right">
                                    [ <?php echo anchor('badge/update/'.$badge['badge_id'], 'Edit'); ?> ]
                                    <?php echo anchor('badge/increase_order/'.$badge['badge_id'], 'Push down', array('class'=>'push_down', 'alt'=>$badge['badge_id']));?>
                                    <?php echo anchor('badge/decrease_order/'.$badge['badge_id'], 'Push up', array('class'=>'push_up', 'alt'=>$badge['badge_id'] ));?>
                                </td>
                            </tr>
                                <?php } ?>
                            <?php } else { ?>
                        <tr>
                            <td class="center" colspan="8"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                echo form_close();?>