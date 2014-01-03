<?php $attributes = array('id'=>'form');?>
        <?php echo form_open('action/delete', $attributes);?>
            <table class="list">
                <thead>
                    <tr>
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_added'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_order'); ?></td>
                    <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right"><a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a></td>
                    </tr>
                    
                        <?php if(isset($actions)){?>
                            <?php foreach($actions as $action){?>
                                <tr>
                                    <td style="text-align: center;"><?php if (isset($action['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" checked="checked" />
                                        <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" />
                                        <?php } ?></td>
                                    <td class="left"><?php echo "<i style='color:grey' class='".$action['icon']." icon-4x'></i>"; ?></td>
                                    <td class="left"><?php echo $action['name']; ?></td>
                                    <td class="right"><?php echo datetimeMongotoReadable($action['date_added']); ?></td>
                                    <td class="right"><?php echo ($action['status'])? "Enabled" : "Disabled"; ?></td>
                                    <td class="right"><?php echo $action['sort_order'];?></td>
                                    <td class="right">[ <?php if($client_id){
                                            echo anchor('action/update/'.$action['action_id'], 'Edit');
                                        }else{
                                            echo anchor('action/update/'.$action['_id'], 'Edit');
                                        }
                                        ?> ]

                                        <?php if($client_id){

                                            echo anchor('action/increase_order/'.$action['action_id'], 'Push Down', array('class'=>'push_down', 'alt'=>$action['action_id']));
                                        }else{
                                            echo anchor('action/increase_order/'.$action['_id'], 'Push Down', array('class'=>'push_down', 'alt'=>$action['_id']));
                                        }
                                        ?>
                                        <?php if($client_id){
                                            echo anchor('action/decrease_order/'.$action['action_id'], 'Push Up', array('class'=>'push_up', 'alt'=>$action['action_id']));
                                        }else{
                                            echo anchor('action/decrease_order/'.$action['_id'], 'Push Up', array('class'=>'push_up', 'alt'=>$action['_id']));
                                        }
                                        ?>
                                        </td>   

                                </tr>
                            <?php }?>
                        <?php }?>
                    
                </tbody>
            </table>
        <?php echo form_close();?>