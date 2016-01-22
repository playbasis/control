<table id="user-list" class="list">
    <thead>
    <tr>
        <td class="left"><?php echo $this->lang->line('column_title'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_username'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_group'); ?></td>
        <td class="right"><?php echo $this->lang->line('column_status'); ?></td>
        <td></td>
    </tr>
    </thead>
    <?php $user_row = 0; ?>
    <?php foreach ($users as $user) { ?>
        <tbody id="user-row<?php echo $user_row; ?>">
        <tr>
            <td class="left"><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></td>
            <td class="left"><?php echo anchor('user/update/'.$user['user_id'], $user['username']); ?></td>
            <td class="left">
                <select name="user_value[<?php echo $user_row; ?>][user_group_id]">
                    <?php if ($groups) { ?>
                        <?php if($this->session->userdata('client_id')){?>
                            <?php foreach ($groups as $group) { ?>
                                <?php if($group['name']=='User'||$group['name']=='Admin'){?> 
                                    <?php if ($group['_id']==$user['user_group_id']) { ?>
                                        <option value="<?php echo $group['_id']; ?>" selected="selected"><?php echo $group['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $group['_id']; ?>"><?php echo $group['name']; ?></option>
                                    <?php } ?>
                                <?php }?>
                            <?php } ?>
                        <?php }else{?>
                            <?php foreach ($groups as $group) { ?>
                                <?php if ($group['_id']==$user['user_group_id']) { ?>
                                    <option value="<?php echo $group['_id']; ?>" selected="selected"><?php echo $group['name']; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $group['_id']; ?>"><?php echo $group['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php }?>
                    <?php } ?>
                </select>
            </td>
            <td class="right"><select name="user_value[<?php echo $user_row; ?>][status]">
                    <?php if ($user['status']==1) { ?>
                        <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                        <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                    <?php } else { ?>
                        <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                        <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                    <?php } ?>
                </select></td>
            <td class="left">
                <a onclick="removeUser('<?php echo $user['user_id']; ?>');" class="button"><span><?php echo $this->lang->line('button_remove'); ?></span></a>
                <input type="hidden" name="user_value[<?php echo $user_row; ?>][user_id]" value="<?php echo $user['user_id']; ?>" />
                <input type="hidden" name="user_value[<?php echo $user_row; ?>][client_id]" value="<?php echo $user['client_id']; ?>" />
            </td>
        </tr>
        </tbody>
        <?php $user_row++; ?>
    <?php } ?>
    <tfoot>
    <tr>
        <td colspan="4"></td>
        <td class="left"></td>
    </tr>
    </tfoot>
</table>

<script type="text/javascript">
    function removeUser(user_id) {

        if (!confirm('<?php echo $text_confirm; ?>')) {
            return false;
        } else {

            $.ajax({
                url: baseUrlPath+'user/delete_ajax',
                type: 'POST',
                dataType: 'json',
                data: ({'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','user_id' : user_id, 'client_id' : '<?php echo $list_client_id; ?>'}),
                success: function(json) {
                    var notification = $('#notification');

                    if (json['error']) {

                        $('#notification').html(json['error']).addClass('warning').show();

                    } else {

                        $('#notification').html(json['success']).addClass('success').show();
                        //location.reload(true);
                        $('#users').load(baseUrlPath+'client/users?client_id=<?php echo $list_client_id; ?>');
                    }
                }

            });

        }

        return false;

    }
</script>