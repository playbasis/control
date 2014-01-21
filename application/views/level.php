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
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'level/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
        <?php if($this->session->flashdata('success')){ ?>
            <div class="content messages half-width">
            <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
            </div>
        <?php }?>
            <?php
            $attributes = array('id' => 'form');
            echo form_open('level/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="right" style="width:50px;"><?php echo $this->lang->line('column_level'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_exp'); ?></td>
                        <td class="left" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($levels) { ?>
                        <?php foreach ($levels as $level) { ?>
                        <tr>
                            <td style="text-align: center;"><?php if ($level['selected']) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $level['level_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $level['level_id']; ?>" />
                                <?php } ?></td>
                            <td class="right"><?php echo $level['level']; ?></td>
                            <td class="left"><?php echo $level['title']; ?></td>
                            <td class="right"><?php echo $level['exp']; ?></td>
                            <td class="left"><?php echo $level['status']; ?></td>
                            <td class="right">
                                [ <?php echo anchor('level/update/'.$level['level_id'], 'Edit'); ?> ]
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
            <?php echo form_close();?>
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <?php if($pagination_links != ''){?>
                        <?php echo $pagination_links;?>
                    <?php }?>
                </ul>
            </div>
        </div>
    </div>
</div>