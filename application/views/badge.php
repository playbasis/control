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

                <?php //if ($user_group_id == 1) { ?>
                <button class="btn btn-info" onclick="location = '<?php echo $this->lang->line('insert'); ?>'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <?php //if ($user_group_id == 1) { ?>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
                <?php //} ?>

            </div>
        </div>
        <div class="content">
            <?php if ($user_group_id==$setting_group_id) { ?>
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
                            <td class="left"><?php echo $badge['status']; ?></td>
                            <td class="right"><?php echo $badge['sort_order']; ?></td>
                            <td class="right"><?php foreach ($badge['action'] as $action) { ?>
                                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                                <?php } ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            } else {
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
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($badges)) { ?>
                        <?php foreach ($badges as $badge) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
                            </td>
                            <td class="left"><div class="image"><img src="<?php echo $badge['image']; ?>" alt="" id="thumb" /></div></td>
                            <td class="left"><?php echo $badge['name']; ?></td>
                            <td class="right"><?php echo $badge['quantity']; ?></td>
                            <td class="right">
                                [ <a href="<?php echo $badge['href']; ?>">Edit</a> ]
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            }
            ?>
        </div>
    </div>
</div>