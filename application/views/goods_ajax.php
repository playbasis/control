<?php
$attributes = array('id' => 'form');
echo form_open('goods/delete',$attributes);
?>
    <table class="list">
        <thead>
        <tr>
            <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
            <?php if($org_status){?>
                <td class="left" style="min-width:30px;"><?php echo $this->lang->line('column_organization'); ?></td>
            <?php }?>
            <?php if(!$client_id){?>
                <td class="left"><?php echo $this->lang->line('column_owner'); ?></td>
            <?php }?>
            <?php if($client_id){?>
                <td class="left" style="width:40px;"><?php echo $this->lang->line('column_is_group'); ?></td>
            <?php }?>
            <td class="left" style="width:50px;"><?php echo $this->lang->line('column_quantity'); ?></td>
            <td class="left" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
            <td class="left" style="width:65px;"><?php echo $this->lang->line('column_date_start'); ?></td>
            <td class="left" style="width:65px;"><?php echo $this->lang->line('column_date_end'); ?></td>
            <td class="right" style="width:60px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
            <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
            <td class="right" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
        </tr>
        </thead>
        <tr class="filter">
            <td></td>
            <td></td>
            <td class="right" ><input style="width:95%;" title="filter_goods" type="text" name="filter_goods" value="<?php echo isset($_GET['filter_goods']) ? $_GET['filter_goods'] : "" ?>"/></td>
            <?php if ($org_status) { ?>
                <td></td>
            <?php } if (!$client_id) {?>
                <td></td>
            <?php } if ($client_id) { ?>
                <td>
                    <select name="filter_group" style="width:95%">
                        <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $this->lang->line('text_yes'); ?></option>
                            <option value="0"><?php echo $this->lang->line('text_no'); ?></option>
                        <?php } else { ?>
                            <option value="1"><?php echo $this->lang->line('text_yes'); ?></option>
                            <option value="0" selected="selected"><?php echo $this->lang->line('text_no'); ?></option>
                        <?php } ?>
                    </select>
                </td>
            <?php } ?>
            <td></td>
            <td>
                <select name="filter_status" style="width:95%">
                    <?php if ($status) { ?>
                        <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                        <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                    <?php } else { ?>
                        <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                        <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                    <?php } ?>
                </select>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="right" ><input style="width:95%;" title="filter_tags" type="text" name="filter_tags" value="<?php echo isset($_GET['filter_tags']) ? $_GET['filter_tags'] : "" ?>"/></td>
            <td class="right">
                <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
            </td>
        </tr>
        <tbody>
        <?php if (isset($goods_list)) { ?>
            <?php foreach ($goods_list as $goods) { ?>
                <tr>
                    <td style="text-align: center;">
                        <?php if($client_id){?>
                            <?php if(!(isset($goods['sponsor']) && $goods['sponsor'])){?>
                                <?php if ($goods['selected']) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $goods['goods_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $goods['goods_id']; ?>" />
                                <?php } ?>
                            <?php } ?>
                        <?php }else{ ?>
                            <?php if ($goods['selected']) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $goods['goods_id']; ?>" checked="checked" />
                            <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $goods['goods_id']; ?>" />
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td class="left"><div class="image"><img src="<?php echo $goods['image']; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></div></td>
                    <td class="left"><?php echo $goods['name']; ?></td>
                    <?php if($org_status){?>
                        <td class="left"><?php echo (isset($goods['organize_name']) && !is_null($goods['organize_name']))?$goods['organize_name']:''; ?></td>
                    <?php }?>
                    <?php if(!$client_id){?>
                        <td class="left"><?php echo ($goods['is_public'])?"Public":"Private"; ?></td>
                    <?php }?>
                    <?php if($client_id){?>
                        <td class="left"><?php echo ($goods['is_group'])?"Yes":""; ?></td>
                    <?php }?>
                    <td class="right"><?php echo (isset($goods['quantity']) && !is_null($goods['quantity']))?$goods['quantity']:'Unlimited'; ?></td>
                    <td class="left"><?php echo ($goods['status'])? "Enabled" : "Disabled"; ?></td>
                    <td class="left"><?php if (isset($goods['date_start']) && $goods['date_start'] && strtotime(datetimeMongotoReadable($goods['date_start']))) {echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($goods['date_start'])));} else { echo ""; }?></td>
                    <td class="left"><?php if (isset($goods['date_end']) && $goods['date_end'] && strtotime(datetimeMongotoReadable($goods['date_end']))) {echo date('Y-m-d H:i:s', strtotime(datetimeMongotoReadable($goods['date_end'])));} else { echo ""; }?></td>
                    <td class="right"><?php echo $goods['sort_order']; ?></td>
                    <td class="right" style="word-wrap:break-word;"><?php echo (isset($goods['tags']) && $goods['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $goods['tags']).'</span>' : null); ?></td>
                    <td class="right">
                        <?php
                            if((!$client_id) || (!(isset($goods['sponsor']) && $goods['sponsor']))) {
                                echo anchor('goods/update/' . $goods['goods_id'], "<i class='fa fa-edit fa-lg''></i>",
                                    array('class'=>'tooltips',
                                        'title' => 'Edit',
                                        'data-placement' => 'top'
                                    ));
                            }
                        ?>
                        <?php echo anchor('goods/increase_order/'.$goods['goods_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$goods['goods_id'], 'style'=>'text-decoration:none'));?>
                        <?php echo anchor('goods/decrease_order/'.$goods['goods_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$goods['goods_id'], 'style'=>'text-decoration:none' ));?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td class="center" colspan="<?php echo !$client_id ? 9 : 8; ?>"><?php echo $this->lang->line('text_no_results'); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php
echo form_close();?>