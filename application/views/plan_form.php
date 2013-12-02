<div id="content" class="span10">

<div class="box">
    <div class="heading">
        <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        <div class="buttons">
            <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
            <button class="btn btn-info" onclick="location = baseUrlPath+'plan'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
        </div>
    </div>
    <div class="content">
        <div id="tabs" class="htabs">
            <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            <a href="#tab-data"><?php echo $this->lang->line('tab_data'); ?></a>
            <a href="#tab-feature"><?php echo $this->lang->line('tab_feature'); ?></a>
            <a href="#tab-reward"><?php echo $this->lang->line('tab_reward'); ?></a>
            <a href="#tab-jigsaw"><?php echo $this->lang->line('tab_jigsaw'); ?></a>
            <a href="#tab-action"><?php echo $this->lang->line('tab_action'); ?></a>

        </div>
        <?php
        if(validation_errors() || isset($message)) {
            ?>
            <div class="content messages half-width">
                <?php
                echo validation_errors('<div class="warning">','</div>');

                if (isset($message) && $message) {
                    ?>
                    <div class="warning"><?php echo $message; ?></div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        $attributes = array('id' => 'form');
        echo form_open($form ,$attributes);
        ?>
            <div id="tab-general">
                <table class="form">
                    <tr>
                        <td><?php echo $this->lang->line('entry_name'); ?></td>
                        <td><input type="text" name="name" value="<?php echo $name; ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_description'); ?></td>
                        <td><input type="text" name="description" value="<?php echo $description; ?>" size="50" /></td>
                    </tr>
                </table>
            </div>
            <div id="tab-data">
                <table class="form">
                    <tr>
                        <td><?php echo $this->lang->line('entry_status'); ?></td>
                        <td><select name="status">
                            <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                            <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                            <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                            <?php } ?>
                        </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_sort_order'); ?></td>
                        <td><input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="1" /></td>
                    </tr>
                </table>
            </div>

            <div id="tab-feature">
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($plan_features) { ?>
                        <?php foreach ($plan_features as $feature) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (in_array($feature['feature_id'], $feature_data)) { ?>
                                <input type="checkbox" name="feature_data[]" value="<?php echo $feature['feature_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="feature_data[]" value="<?php echo $feature['feature_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo $feature['name']; ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="tab-action">
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($plan_actions) { ?>
                        <?php foreach ($plan_actions as $action) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (in_array($action['action_id'], $action_data)) { ?>
                                <input type="checkbox" name="action_data[]" value="<?php echo $action['action_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="action_data[]" value="<?php echo $action['action_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo $action['name']; ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="tab-jigsaw">
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($plan_jigsaws) { ?>
                        <?php foreach ($plan_jigsaws as $jigsaw) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (in_array($jigsaw['jigsaw_id'], $jigsaw_data)) { ?>
                                <input type="checkbox" name="jigsaw_data[]" value="<?php echo $jigsaw['jigsaw_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="jigsaw_data[]" value="<?php echo $jigsaw['jigsaw_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo $jigsaw['name']; ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="tab-reward">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($plan_rewards) { ?>
                        <?php foreach ($plan_rewards as $reward) { ?>
                        <tr>
                            <td class="left"><?php echo $reward['name']; ?></td>
                            <td class="left">
                                <input type="text" name="reward_data[<?php echo $reward['reward_id']; ?>][limit]" value="<?php echo $reward['limit']; ?>" style="width:50px;" />
                                <input type="hidden" name="reward_data[<?php echo $reward['reward_id']; ?>][reward_id]" value="<?php echo $reward['reward_id']; ?>" />
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        <?php
        echo form_close();
        ?>
    </div>
</div>
</div>

<script type="text/javascript"><!--

function addNewDomain() {
    var domain_name = $('input[name=domain_name]').val();
    var site_name = $('input[name=domain_site_name]').val();
    var date_start = $('input[name=domain_start_date]').val();
    var date_expire = $('input[name=domain_expire_date]').val();
    var status = $('select[name=domain_status]').val();
    var plan_id = $('select[name=domain_plan_id]').val();

    $.ajax({
        url: 'index.php?route=client/client/adddomain&token=<?php echo $token; ?>',
        type: 'POST',
        dataType: 'json',
        data: ({'domain_name' : domain_name, 'site_name' : site_name, 'date_start' : date_start, 'date_expire' : date_expire, 'plan_id' : plan_id, 'status' : status, 'client_id' : '<?php echo $client_id; ?>'}),
        success: function(json) {
            var notification = $('#notification');

            if (json['error']) {
                $('#notification').html(json['error']).addClass('warning').show();
            } else {

                $('#notification').html(json['success']).addClass('success').show();
                $('#domains').load('index.php?route=client/client/domain&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>');

            }
        }

    });

    return false;

}

//--></script>

<script type="text/javascript"><!--

function addNewUser() {
    var first_name = $('input[name=user_firstname]').val();
    var last_name = $('input[name=user_lastname]').val();
    var email = $('input[name=user_email]').val();
    var username = $('input[name=user_username]').val();
    var password = $('input[name=user_password]').val();
    var user_group_id = $('select[name=user_group]').val();
    var status = $('select[name=user_status]').val();

    $.ajax({
        url: 'index.php?route=client/client/adduser&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>',
        type: 'POST',
        dataType: 'json',
        data: ({'first_name' : first_name, 'last_name' : last_name, 'email' : email, 'username' : username, 'password' : password, 'user_group_id' : user_group_id, 'status' : status}),
        success: function(json) {
            var notification = $('#notification');

            if (json['error']) {
                $('#notification').html(json['error']).addClass('warning').show();
            } else {

                $('#notification').html(json['success']).addClass('success').show();
                $('#users').load('index.php?route=client/client/users&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>');

            }
        }

    });

    return false;

}

//--></script>

<script type="text/javascript"><!--
$('#users .pagination a').live('click', function() {
    $('#users').fadeIn('slow');

    $('#users').load(this.href);

    $('#users').fadeOut('slow');

    return false;
});

$('#users').load('index.php?route=client/client/users&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>');

//--></script>

<script type="text/javascript"><!--
$('#domains .pagination a').live('click', function() {
    $('#domains').fadeIn('slow');

    $('#domains').load(this.href);

    $('#domains').fadeOut('slow');

    return false;
});

$('#domains').load('index.php?route=client/client/domain&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>');

//--></script>

<script type="text/javascript"><!--

function resetToken(site_id) {

    $.ajax({
        url: 'index.php?route=client/client/reset&token=<?php echo $token; ?>',
        type: 'post',
        data: 'site_id=' + site_id,
        dataType: 'json',
        success: function(json) {
            $('#domains').load('index.php?route=client/client/domain&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>');
        }
    });

    return false;

}

//--></script>

<script type="text/javascript"><!--
function image_upload(field, thumb) {
    $('#dialog').remove();

    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
        title: '<?php echo $text_image_manager; ?>',
        close: function (event, ui) {
            if ($('#' + field).attr('value')) {
                $.ajax({
                    url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
                    dataType: 'text',
                    success: function(data) {
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
                    }
                });
            }
        },
        bgiframe: false,
        width: 800,
        height: 400,
        resizable: false,
        modal: false
    });
};
//--></script>
<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>