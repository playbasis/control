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
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_url'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_url'); ?>"
                                   value="<?php echo isset($url) ? $url : set_value('url'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_port'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_port'); ?>"
                                   value="<?php echo isset($port) ? $port : set_value('port'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_username'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_username'); ?>"
                                   value="<?php echo isset($username) ? $username : set_value('username'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_password'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_password'); ?>"
                                   value="<?php echo isset($password) ? $password : set_value('password'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_action'); ?>&nbsp;:</td>
                        <td>
                            <select id="org_list" class="span3"  name ="selected_org" onchange="org_change(this)">
                                <option label="None" value="" <?php echo isset($selected_org)?"":"selected"?>>
                                    <?php foreach ($org_lists as $key => $org){?>
                                <option label="<?php echo $org['name'] ?> " value="<?php echo $org['_id'] ?>" <?php echo $selected_org==$org['_id']?"selected":""?>>
                                    <?php } ?>

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_occur'); ?>:</td>
                        <td>
                            <div class="control-group">
                                <div class="btn-group" data-toggle="buttons-radio" >
                                    <button type="button" class="btn btn-primary <?php echo (isset($occur_once) && $occur_once)?"active":"" ?>" onclick="occurence_change(this)" value="once">Once at</button>
                                    <button type="button" class="btn btn-primary <?php echo (isset($occur_once) && $occur_once)?"":"active" ?>" onclick="occurence_change(this)" value="repeat">Repeat Until</button>
                                    <input type="hidden" id="occurence_id" name="occur_once" value="<?php echo (isset($occur_once) && $occur_once)?"true":"false" ?>">
                                </div>
                            </div>
                            <span>
                                <input type="text" class="date" name="month" id="monthpicker" size="50"
                                       placeholder="<?php echo $this->lang->line('entry_month'); ?>"
                                       value="<?php echo isset($month) && $month ? date('Y-m',
                                           strtotime(datetimeMongotoReadable($month))) : ''; ?>"/>
                            </span>
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


<link id="base-style" rel="stylesheet" type="text/css"
      href="<?php echo base_url(); ?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css"/>
<script type="text/javascript"
        src="<?php echo base_url(); ?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">

</script>
