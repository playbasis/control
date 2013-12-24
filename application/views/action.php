<div id="content" class="span10">
    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <?php
            if($user_group_id != $setting_group_id){
                ?>
                <div class="buttons">
                    <button class="btn btn-info" onclick="location = baseUrlPath+'action/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                    <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php
            $attributes = array('id' => 'form');
            echo form_open('action/delete',$attributes);
            ?>
            <table class="list">
                <thead>
                <tr class="filter">
                    <td></td>
                    <td></td>
                    <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                    <td></td>
                    <td></td>
                    <td class="right"><a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a></td>
                </tr>
                <tr>
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                    <td class="left" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($actions)) { ?>
                    <?php foreach ($actions as $action) { ?>
                        <tr>
                            <td style="text-align: center;"><?php if ($action['selected']) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><div class="image"><img src="<?php echo $action['icon']; ?>" alt="" id="thumb" /></div></td>
                            <td class="left"><?php echo $action['name']; ?></td>
                            <td class="left"><?php echo ($action['status'])? "Enabled" : "Disabled"; ?></td>
                            <td class="right"><?php echo $action['sort_order']; ?></td>
                            <td class="right">
                                [ <?php echo anchor('action/update/'.$action['_id'], 'Edit'); ?> ]
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
            echo form_close();

            if($pagination_links != ''){
                echo $pagination_links;
            }
            ?>
        </div>
    </div>
</div>