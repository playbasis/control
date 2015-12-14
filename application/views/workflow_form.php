<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'workflow'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
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
                        <td><?php echo $this->lang->line('form_username'); ?>:</td>
                        <td><input class="span5" type="text" name="username"  value="<?php echo isset($requester['username']) ? $requester['username'] :  set_value('username'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_password'); ?>:</td>
                        <td><input class="span5" type="password" name="password"  value="<?php echo isset($requester['password']) ? $requester['password'] :  set_value('password'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_confirm_password'); ?>:</td>
                        <td><input class="span5" type="password" name="confirm_password"  value="<?php echo isset($requester['password']) ? $requester['password'] :  set_value('password'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_id'); ?>:</td>
                        <td><input class="span5" type="text" name="cl_player_id"  value="<?php echo isset($requester['cl_player_id']) ? $requester['cl_player_id'] :  set_value('cl_player_id'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_firstname'); ?>:</td>
                        <td><input class="span5" type="text" name="first_name"  value="<?php echo isset($requester['first_name']) ? $requester['first_name'] :  set_value('first_name'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_lastname'); ?>:</td>
                        <td><input class="span5" type="text" name="last_name"  value="<?php echo isset($requester['last_name']) ? $requester['last_name'] :  set_value('last_name'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_email'); ?>:</td>
                        <td><input class="span5" type="email" name="email" size="100" value="<?php echo isset($requester['email']) ? $requester['email'] :  set_value('email'); ?>" class="tooltips" data-placement="right" title="Email address is used to log into the system"/></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_approve'); ?>:</td>

                        <td>
                            <select name="approved" class="span5" >
                                <option value="approved" <?php if ($requester['approved'] == "approved") { ?>selected<?php }?>>Approved</option>
                                <option value="rejected" <?php if ($requester['approved'] == "rejected") { ?>selected<?php }?>>Rejected</option>
                                <option value="pending"  <?php if ($requester['approved'] == "pending")  { ?>selected<?php }?>>Pending</option>
                            </select>
                        </td>
                    </tr>


                </table>
            </div>

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

