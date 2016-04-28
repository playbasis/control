<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'import'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php } ?>
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
            <?php
            if (validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">', '</div>');

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
            echo form_open($form, $attributes);
            ?>
            <div id="tab-general">
                <table class="form">
                    <tbody>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_name'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                   value="<?php echo isset($name) ? $name : set_value('name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_hosttype'); ?>&nbsp;:</td>
                        <td>
                            <span class="dropdown">
                                <select id="hostType" class="span3"  name ="host_type">
                                    <option label="HTTPS" value="HTTPS" <?php echo $host_type == "HTTPS"?"selected":""?>>
                                    <option label="FTP"   value="FTP"   <?php echo $host_type == "FTP"?"selected":""?>>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_hostname'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="host_name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_hostname'); ?>"
                                   value="<?php echo isset($host_name) ? $host_name : set_value('host_name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_filename'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="file_name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_filename'); ?>"
                                   value="<?php echo isset($file_name) ? $file_name : set_value('file_name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>&nbsp;</span><?php echo $this->lang->line('entry_port'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="port" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_port'); ?>"
                                   value="<?php echo isset($port) ? $port : set_value('port'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span&nbsp;</span><?php echo $this->lang->line('entry_username'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="user_name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_username'); ?>"
                                   value="<?php echo isset($user_name) ? $user_name : set_value('user_name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>&nbsp;</span><?php echo $this->lang->line('entry_password'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="password" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_password'); ?>"
                                   value="<?php echo isset($password) ? $password : set_value('password'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_import_type'); ?>&nbsp;:</td>
                        <td>
                            <span class="dropdown">
                                <select id="importType" class="span3"  name ="import_type">
                                    <option label="Player"         value="player"      <?php echo $import_type =="player"?"selected":""?>>
                                    <option label="Transaction"    value="transaction" <?php echo $import_type =="transaction"?"selected":""?>>
                                    <option label="Store organize" value="storeorg"    <?php echo $import_type =="storeorg"?"selected":""?>>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_occur'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="number" name="routine" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_occur'); ?>"
                                   value="<?php echo isset($routine) ? $routine : set_value('routine'); ?>"/>
                        </td>
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

