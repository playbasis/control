<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'sms'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
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
            echo form_open($form ,$attributes);
            ?>
                <div id="tab-general">
                        <table class="form">
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_lithium_url'); ?>:</td>
                                <td><input type="text" name="lithium_url" size="100" value="<?php echo isset($lithium_url) ? $lithium_url :  set_value('lithium_url'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_lithium_username'); ?>:</td>
                                <td><input type="text" name="lithium_username" value="<?php echo isset($lithium_username) ? $lithium_username : set_value('lithium_username'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_lithium_password'); ?>:</td>
                                <td><input type="password" name="lithium_password" value="<?php echo isset($lithium_password) ? $lithium_password : set_value('lithium_password'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_http_auth_username'); ?>:</td>
                                <td><input type="text" name="http_auth_username" value="<?php echo isset($http_auth_username) ? $http_auth_username : set_value('http_auth_username'); ?>" /></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_http_auth_password'); ?>:</td>
                                <td><input type="password" name="http_auth_password" value="<?php echo isset($http_auth_password) ? $http_auth_password : set_value('http_auth_password'); ?>" /></td>
                            </tr>
                        </table>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
