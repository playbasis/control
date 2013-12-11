<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'client'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
                <a href="#tab-data"><?php echo $this->lang->line('tab_data'); ?></a>
                <!-- <a href="#tab-address"><?php echo $this->lang->line('tab_address'); ?></a> -->
                <?php if ($list_client_id.""!=0) { ?><a href="#tab-user"><?php echo $this->lang->line('tab_user'); ?></a><?php } ?>
                <?php if ($list_client_id.""!=0) { ?><a href="#tab-domain"><?php echo $this->lang->line('tab_domain'); ?></a><?php } ?>
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
                            <td><?php echo $this->lang->line('entry_firstname'); ?></td>
                            <td><input type="text" name="first_name" value="<?php echo $first_name; ?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_lastname'); ?></td>
                            <td><input type="text" name="last_name" value="<?php echo $last_name; ?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_email'); ?></td>
                            <td><input type="text" name="email" value="<?php echo $email; ?>"size="50"  /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_mobile'); ?></td>
                            <td><input type="text" name="mobile" value="<?php echo $mobile; ?>" size="50" /></td>
                        </tr>
                    </table>
                </div>
                <div id="tab-data">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_image'); ?></td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
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
                    </table>
                </div>
                <div id="tab-address"></div>
                <?php if ($list_client_id.""!=0) { ?>
                <div id="tab-user">
                    <table class="form">
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_firstname'); ?></td>
                            <td><input type="text" name="user_firstname" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_lastname'); ?></td>
                            <td><input type="text" name="user_lastname" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_email'); ?></td>
                            <td><input type="text" name="user_email" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_username'); ?></td>
                            <td><input type="text" name="user_username" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_password'); ?></td>
                            <td><input type="password" name="user_password" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_group'); ?></td>
                            <td>
                                <select name="user_group">
                                    <option value="0" selected="selected"><?php echo $this->lang->line('text_select'); ?></option>
                                    <?php if ($groups) { ?>
                                    <?php foreach ($groups as $group) { ?>
                                        <option value="<?php echo $group['_id']; ?>"><?php echo $group['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_status'); ?></td>
                            <td>
                                <select name="user_status">
                                    <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><a onclick="addNewUser();" class="button"><span><?php echo $this->lang->line('button_add_user'); ?></span></a></td>
                        </tr>
                    </table>

                    <iframe id="users" frameborder="0" style="min-height: 400px; width: 100%;" height="100%" width="100%" src="<?php echo base_url();?><?php echo (index_page() == '')? '' : index_page()."/" ?>client/users?client_id=<?php echo $list_client_id; ?>">

                    </iframe>

                </div>
                <?php } ?>
                <?php if ($list_client_id.""!=0) { ?>
                <div id="tab-domain">
                    <table class="form">
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_domain_name'); ?></td>
                            <td><input type="text" name="domain_name" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_site_name'); ?></td>
                            <td><input type="text" name="domain_site_name" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_start_date'); ?></td>
                            <td><input type="text" class="date" name="domain_start_date" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_expire_date'); ?></td>
                            <td><input type="text" class="date" name="domain_expire_date" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('limit_users'); ?></td>
                            <td><input type="text" name="limit_users" value="" size="50" /></td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_plan'); ?></td>
                            <td>
                                <select name="domain_plan_id">
                                    <option value="0" selected="selected"><?php echo $this->lang->line('text_select'); ?></option>
                                    <?php if ($plan_data) { ?>
                                    <?php foreach ($plan_data as $plan) { ?>
                                        <option value="<?php echo $plan['_id']; ?>"><?php echo $plan['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="require">*</span> <?php echo $this->lang->line('entry_status'); ?></td>
                            <td>
                                <select name="domain_status">
                                    <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><a onclick="addNewDomain();" class="button"><span><?php echo $this->lang->line('button_add_domain'); ?></span></a></td>
                        </tr>
                    </table>

                    <iframe id="domains" frameborder="0" style="min-height: 400px; width: 100%;" height="100%" width="100%" src="<?php echo base_url();?><?php echo (index_page() == '')? '' : index_page()."/" ?>client/domain?client_id=<?php echo $list_client_id; ?>">

                    </iframe>
                </div>
                <?php } ?>
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
    var limit_users = $('input[name=limit_users]').val();
    var status = $('select[name=domain_status]').val();
    var plan_id = $('select[name=domain_plan_id]').val();

    $.ajax({
        url: baseUrlPath+'domain/insert_ajax',
        type: 'POST',
        dataType: 'json',
        data: ({'domain_name' : domain_name, 'site_name' : site_name, 'date_start' : date_start, 'date_expire' : date_expire, 'limit_users' : limit_users, 'plan_id' : plan_id, 'status' : status, 'client_id' : '<?php echo $list_client_id; ?>'}),
        success: function(json) {
            var notification = $('#notification');

            if (json['error']) {
                $('#notification').html(json['error']).addClass('warning').show();
            } else {

                $('#notification').html(json['success']).addClass('success').show();

                $('iframe').each(function() {
                    this.contentWindow.location.reload(true);
                });
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
        url: baseUrlPath+'user/insert_ajax',
        type: 'POST',
        dataType: 'json',
        data: ({'firstname' : first_name, 'lastname' : last_name, 'email' : email, 'username' : username, 'password' : password, 'user_group' : user_group_id, 'status' : status, client_id : '<?php echo $list_client_id; ?>'}),
        success: function(json) {
            var notification = $('#notification');

            if (json['error']) {
                $('#notification').html(json['error']).addClass('warning').show();
            } else {

                $('#notification').html(json['success']).addClass('success').show();

                $('iframe').each(function() {
                    this.contentWindow.location.reload(true);
                });
            }
        }

    });

    return false;

}

//--></script>

<script type="text/javascript"><!--
/*$('#users .pagination a').live('click', function() {
    $('#users').fadeIn('slow');

    $('#users').load(this.href);

    $('#users').fadeOut('slow');

    return false;
});*/

//$('#users').load(baseUrlPath+'client/users?client_id=<?php echo $list_client_id; ?>');

//--></script>

<script type="text/javascript"><!--
/*$('#domains .pagination a').live('click', function() {
    $('#domains').fadeIn('slow');

    $('#domains').load(this.href);

    $('#domains').fadeOut('slow');

    return false;
});*/

//$('#domains').load(baseUrlPath+'client/domain?client_id=<?php echo $list_client_id; ?>');

//--></script>

<script type="text/javascript"><!--

function resetToken(site_id) {

    $.ajax({
        url: baseUrlPath+'domain/reset',
        type: 'post',
        data: 'site_id=' + site_id,
        dataType: 'json',
        success: function(json) {
            $('iframe').each(function() {
                this.contentWindow.location.reload(true);
            });
        }
    });

    return false;

}

//--></script>

<script type="text/javascript"><!--
function image_upload(field, thumb) {
    $('#dialog').remove();

    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+baseUrlPath+'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
        title: '<?php echo $this->lang->line('text_image_manager'); ?>',
        close: function (event, ui) {
            if ($('#' + field).attr('value')) {
                $.ajax({
                    url: baseUrlPath+'filemanager/image?image=' + encodeURIComponent($('#' + field).val()),
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
<script type="text/javascript">
    $(function(){

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

    })
</script>