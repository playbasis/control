<?php $attributes = array('id'=>'form');?>
<?php echo form_open('quest/delete', $attributes);?>
            <table class="list">
                <thead>
                    <tr>
                    <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                    <td class="right" style="width:200px;"><?php echo $this->lang->line('column_quest_name'); ?></td>
                    <td class="right" style="width:50px;"><?php echo $this->lang->line('column_quest_status'); ?></td>
                    <?php if($org_status){?>
                        <td class="right" style="min-width:30px;"><?php echo $this->lang->line('column_organization'); ?></td>
                    <?php }?>
                    <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_quest_tags'); ?></td>
                    <td class="right" style="width:60px;"><?php echo $this->lang->line('column_quest_sort_order'); ?></td>
                    <td class="right" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <?php if($org_status){?>
                            <td></td>
                        <?php }?>
                        <td></td>
                        <td class="right">
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>
                    
                        <?php if(isset($quests)){?>
                            <?php foreach($quests as $quest){?>
                                <tr>
                                    <td style="text-align: center;"><?php if (isset($quest['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $quest['_id']; ?>" checked="checked" />
                                        <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $quest['_id']; ?>" />
                                        <?php } ?></td>
                                    <td class="left"><img src="<?php echo $quest['image']; ?>" alt="" id="quest_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></td>
                                    <td class="right"><?php echo $quest['quest_name']; ?></td>   
                                    <td class="right"><?php echo ($quest['status'])?'Active':'Inactive';?></td>
                                    <?php if($org_status){?>
                                        <td class="right"><?php echo (isset($quest['organize_name']) && !is_null($quest['organize_name']))?$quest['organize_name']:''; ?></td>
                                    <?php }?>
                                    <td class="right"><?php echo (isset($quest['tags']) && $quest['tags']) ? implode(',',$quest['tags']) : null; ?></td>
                                    <td class="right"><?php echo $quest['sort_order'];?></td>
                                    
                                    <td class="right">
                                        <!--<a class="quest_play" href="#" title="Play" data-quest_id="<?php echo $quest["_id"]; ?>"><i class='fa fa-play fa-lg'></i> </a>-->
                                        <?php 
                                            if($client_id){
                                                // echo anchor('quest/update/'.$quest['action_id'], 'Edit');
                                                echo anchor('quest/edit/'.$quest['_id'], "<i class='fa fa-edit fa-lg'></i>",
                                                    array('class'=>'tooltips',
                                                        'title' => 'Edit',
                                                        'data-placement' => 'top'
                                                    ));
                                            }else{
                                                echo anchor('action/edit/'.$quest['_id'], "<i class='fa fa-edit fa-lg'></i>",
                                                    array('class'=>'tooltips',
                                                        'title' => 'Edit',
                                                        'data-placement' => 'top'
                                                    ));
                                            }
                                        ?>

                                        <?php if($client_id){
                                            // echo anchor('action/increase_order/'.$quest['action_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$quest['action_id'], 'style'=>'text-decoration:none'));
                                            echo anchor('action/increase_order/'.$quest['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                        }else{
                                            echo anchor('action/increase_order/'.$quest['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                        }
                                        ?>
                                        <?php if($client_id){
                                            // echo anchor('action/decrease_order/'.$quest['action_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$quest['action_id'], 'style'=>'text-decoration:none'));
                                            echo anchor('action/decrease_order/'.$quest['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                        }else{
                                            echo anchor('action/decrease_order/'.$quest['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                        }
                                        ?>
                                        </td>   
                                </tr>
                            <?php }?>
                        <?php }?>
                    
                </tbody>
            </table>
        <?php echo form_close();?>