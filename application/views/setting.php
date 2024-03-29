<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'setting'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>

        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('setting');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
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
            echo form_open_multipart($form ,$attributes);
            ?>
            <div id="actions">
                <table class="form">
                    <tr>
                        <td><?php echo $this->lang->line('entry_app_status') ?>:</td>
                        <td>
                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" class="btn btn-primary <?php echo (isset($app_status) && $app_status)?"active":"" ?>" onclick="app_status_change(this)" value="true" ><?php echo $this->lang->line('entry_enable') ?></button>
                                <button type="button" class="btn btn-primary <?php echo (isset($app_status) && $app_status)?"":"active" ?>" onclick="app_status_change(this)" value="false"><?php echo $this->lang->line('entry_disable') ?></button>
                                <input type="hidden" id="app_status_id" name="app_status" value="<?php echo (isset($app_status) && $app_status)?"true":"false" ?>">
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td><?php echo $this->lang->line('entry_app_period'); ?>:</td>
                        <td>
                            <input type="text" class="date" id="app_date_start_id" name="app_period[date_start]" value="<?php echo isset($app_period['date_start']) ? date('Y-m-d', strtotime(datetimeMongotoReadable($app_period['date_start']))):''; ?>" size="50" /> to
                            <input type="text" class="date" id="app_date_end_id" name="app_period[date_end]"  value="<?php echo isset($app_period['date_end']) ? date('Y-m-d', strtotime(datetimeMongotoReadable($app_period['date_end']))):''; ?>" size="50" />
                        </td>
                    </tr>

                    <tr>
                        <td><?php echo $this->lang->line('entry_policy') ?>:</td>
                        <td>
                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" class="btn btn-primary <?php echo (isset($password_policy_enable) && $password_policy_enable)?"active":"" ?>" onclick="pass_policy_change(this)" value="true" ><?php echo $this->lang->line('entry_enable') ?></button>
                                <button type="button" class="btn btn-primary <?php echo (isset($password_policy_enable) && $password_policy_enable)?"":"active" ?>" onclick="pass_policy_change(this)" value="false"><?php echo $this->lang->line('entry_disable') ?></button>
                                <input type="hidden" id="pass_policy_id" name="password_policy_enable" value="<?php echo (isset($password_policy_enable) && $password_policy_enable)?"true":"false" ?>">
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td><?php echo $this->lang->line('entry_min_char'); ?>:</td>
                        <td> <input type="text" name="password_policy[min_char]" class="span3" id="min_char"
                                    placeholder="<?php echo $this->lang->line('placeholder_min_char'); ?>"
                                    value="<?php echo (isset($password_policy['min_char']) && $password_policy['min_char'] > 0) ? $password_policy['min_char'] : ''; ?>"/></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->lang->line('entry_alphabet'); ?>:</td>
                        <td><input type="checkbox" id="alphabet" name="password_policy[alphabet]" <?php echo (isset($password_policy['alphabet']) && $password_policy['alphabet']) ? "checked" : ''; ?>></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_numeric'); ?>:</td>
                        <td><input type="checkbox" id="numeric" name="password_policy[numeric]" <?php echo (isset($password_policy['numeric']) && $password_policy['numeric']) ? "checked" : ''; ?>></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_user_in_password'); ?>:</td>
                        <td><input type="checkbox" id="user_in_password" name="password_policy[user_in_password]" <?php echo (isset($password_policy['user_in_password']) && $password_policy['user_in_password']) ? "checked" : ''; ?>></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_timeout'); ?>:</td>
                        <td>
                            <div class="dropup">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo (isset($timeout))?$timeout:"Forever" ?>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                    <?php foreach ($timeout_list as $timeout){?>
                                    <li><a><?php echo $timeout?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <input type="hidden" id="timeout" name="timeout" value="<?php echo (isset($timeout))?$timeout:"Forever" ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_max_retries'); ?>:</td>
                        <td> <input type="text" name="max_retries" class="span3" id="max_retries"
                                    placeholder="<?php echo $this->lang->line('placeholder_max_retries'); ?>"
                                    value="<?php echo (isset($max_retries) && $max_retries > 0) ? $max_retries : ''; ?>"/></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_email_verification'); ?>:</td>
                        <td><input type="checkbox" id="email_verification_enable" name="email_verification_enable" <?php echo (isset($email_verification_enable) && $email_verification_enable) ? "checked" : ''; ?>></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->lang->line('entry_player_authentication'); ?>:</td>
                        <td><input type="checkbox" id="player_authentication_enable" name="player_authentication_enable" <?php echo (isset($player_authentication_enable) && $player_authentication_enable) ? "checked" : ''; ?>></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->lang->line('entry_goods_alert_sms'); ?>:</td>
                        <td>
                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" class="btn btn-primary <?php echo ($goods_alert_enabled)?"active":"" ?>" onclick="goods_alert_change(this)" value="true" ><?php echo $this->lang->line('entry_enable') ?></button>
                                <button type="button" class="btn btn-primary <?php echo ($goods_alert_enabled)?"":"active" ?>" onclick="goods_alert_change(this)" value="false"><?php echo $this->lang->line('entry_disable') ?></button>
                                <input type="hidden" id="goods_alert_id" name="goods_alert_enabled" value="<?php echo ($goods_alert_enabled)?"true":"false" ?>">
                            </div>

                        </td>
                    </tr>
                    <tr id="select_user_to_alert" style="display: none;">
                        <td><?php echo $this->lang->line('entry_goods_alert_users'); ?>:</td>
                        <td>
                            <select class="chosen-select" multiple id="goods_alert_users" name="goods_alert_users[]" >
                            <?php foreach($goods_alert_users as $user){?>
                                <option <?php echo $user['alert_active']? 'selected' :''; ?> value="<?php echo $user['_id']; ?>" data="<?php echo $user['_id']?>"><?php echo $user['firstname']." ".$user['lastname'];?></option>
                            <?php } ?>
                            </select>
                        </td>
                    </tr>

                </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->

        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>javascript/bootstrap/combodate.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link id="bootstrap-style2" href="<?php echo base_url();?>javascript/bootstrap/chosen.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/chosen.jquery.min.js"></script>

<style type="text/css">

    .chosen-container .chosen-drop {
        border-bottom: 0;
        border-top: 1px solid #aaa;
        top: auto;
        bottom: 30px;
    }

</style>

<script type="text/javascript">
    $("#goods_alert_users").chosen({max_selected_options: 40});
    var filter_id = document.getElementById("goods_alert_users_chosen")
    filter_id.style.width = "400px";
    filter_id.children[0].children[0].children[0].style.width = "200px";
</script>

<script type="text/javascript">
    $(function(){

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

        $('.timelimit').combodate({
            firstItem: 'name', //show 'hour' and 'minute' string at first item of dropdown
            minuteStep: 1
        });
    })

    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

</script>

<script>

    $(document).ready(function() {
        //hidden input
        var pass_policy = document.getElementById('pass_policy_id');

        // text input
        var min_char = document.getElementById('min_char');

        //checkbox
        var alphabet = document.getElementById('alphabet');
        var numeric = document.getElementById('numeric');
        var user_in_password = document.getElementById('user_in_password');

        if (pass_policy.value == "true")
        {
            min_char.disabled = false;
            alphabet.disabled = false;
            numeric.disabled = false;
            user_in_password.disabled = false;
        }
        else{
            min_char.disabled = true;
            alphabet.disabled = true;
            numeric.disabled = true;
            user_in_password.disabled = true;

        }

        //hidden input
        var app_status_id = document.getElementById('app_status_id');

        // text input
        var app_date_start_id = document.getElementById('app_date_start_id');
        var app_date_end_id = document.getElementById('app_date_end_id');

        if (app_status_id.value == "true")
        {
            //app_status_id.value = true;
            app_date_start_id.disabled = false;
            app_date_end_id.disabled = false;
        }
        else{
            //app_status_id.value = false;
            app_date_start_id.disabled = true;
            app_date_end_id.disabled = true;
        }

        var goods_alert_id = document.getElementById('goods_alert_id');
        var select_user_to_alert = document.getElementById('select_user_to_alert');

        if (goods_alert_id.value == "true")
        {
            select_user_to_alert.style.display = "table-row";
        }
        else{
            select_user_to_alert.style.display = "none";
        }
    })

    $(function(){
        $(".dropdown-menu li a").click(function(){
            var timeout = document.getElementById('timeout');
            $("#dropdownMenu2").html($(this).text() + '&nbsp;<span class="caret"></span>');
            $("#dropdownMenu2").val($(this).text());
            timeout.value = $(this).text();
            console.log($(this).text());

        });

    });
    $(this).toggleClass("active");
    function pass_policy_change(elem){
        //hidden input
        var pass_policy = document.getElementById('pass_policy_id');

        // text input
        var min_char = document.getElementById('min_char');

        //checkbox
        var alphabet = document.getElementById('alphabet');
        var numeric = document.getElementById('numeric');
        var user_in_password = document.getElementById('user_in_password');

        if (elem.value == "true")
        {
            pass_policy.value = true;
            min_char.disabled = false;
            alphabet.disabled = false;
            numeric.disabled = false;
            user_in_password.disabled = false;
        }
        else{
            pass_policy.value = false;
            min_char.disabled = true;
            alphabet.disabled = true;
            numeric.disabled = true;
            user_in_password.disabled = true;

        }

    }

    function app_status_change(elem){
        //hidden input
        var app_status_id = document.getElementById('app_status_id');

        // text input
        var app_date_start_id = document.getElementById('app_date_start_id');
        var app_date_end_id = document.getElementById('app_date_end_id');

        if (elem.value == "true")
        {
            app_status_id.value = true;
            app_date_start_id.disabled = false;
            app_date_end_id.disabled = false;
        }
        else{
            app_status_id.value = false;
            app_date_start_id.disabled = true;
            app_date_end_id.disabled = true;
        }
    }

    function goods_alert_change(elem){
        //hidden input
        var goods_alert_id = document.getElementById('goods_alert_id');
        var select_user_to_alert = document.getElementById('select_user_to_alert');

        if (elem.value == "true")
        {
            goods_alert_id.value = true;
            select_user_to_alert.style.display = "table-row";
        }
        else{
            goods_alert_id.value = false;
            select_user_to_alert.style.display = "none";
        }
    }

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>