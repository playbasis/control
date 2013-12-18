<div id="content" class="span10">

    <!-- Messages to display -->
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="image/user.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location =  baseUrlPath+'user/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php
            $attributes = array('id' => 'form');
            echo form_open('user/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left" style="width:72px;"><?php echo $this->lang->line('column_username'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_added'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <td class="right"><a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a></td>
                    </tr>

                    <?php if (isset($users)) { ?>
                        <?php foreach ($users as $user) { ?>
                        <tr>
                            <td style="text-align: center;"><?php if (isset($user['selected'])) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $user['_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $user['_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo $user['username']; ?></td>
                            <td class="left"><?php echo ($user['status'])? "Enabled" : "Disabled"; ?></td>
                            <td class="right"><?php echo datetimeMongotoReadable($user['date_added']); ?></td>
                            <td class="right">[ <?php echo anchor('user/update/'.$user['_id'], 'Edit'); ?> ]</td>
                        </tr>
                            <?php } ?>
                    <?php } else { ?>
                    <tr>
                        <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            ?>

            <div class="pagination">
            	<?php 
                if(!isset($_GET['filter_name'])){
                    echo $this->pagination->create_links();    
                }
            	?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--
function filter() {
    url = baseUrlPath+'user';

    var filter_name = $('input[name=\'filter_name\']').attr('value');

    if (filter_name) {
        url += '?filter_name=' + encodeURIComponent(filter_name);
    }

    location = url;
}
//--></script>

<script type="text/javascript">
$('input[name=\'filter_name\']').autocomplete({
    delay: 0,
    source: function(request, response) {
        $.ajax({
            url: baseUrlPath+'user/autocomplete?filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                response($.map(json, function(item) {
                    return {
                        label: item.username,
                        username: item.username
                    }
                }));
                console.log(response);
            }
        });
    },
    select: function(event, ui) {
        $('input[name=\'filter_name\']').val(ui.item.username);

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
});
</script>