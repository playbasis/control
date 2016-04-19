<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
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
                <a href="<?php echo site_url('setting');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_security'); ?></a>
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
                        <td><?php echo $this->lang->line('entry_policy') ?>:</td>
                        <td>
                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" class="btn btn-primary <?php echo (isset($password_policy_enable) && $password_policy_enable)?"active":"" ?>" onclick="pass_policy_change(this)" value="true" ><?php echo $this->lang->line('entry_enable') ?></button>
                                <button type="button" class="btn btn-primary <?php echo (isset($password_policy_enable) && $password_policy_enable)?"":"active" ?>" onclick="pass_policy_change(this)" value="false"><?php echo $this->lang->line('entry_disable') ?></button>
                                <input type="hidden" id="pass_policy_id" name="password_policy_enable" value="<?php echo (isset($password_policy_enable) && $password_policy_enable)?"true":"falses" ?>">
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

                </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->

        </div>
    </div>
</div>

<script>
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
</script>