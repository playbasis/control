<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/sms/style.css">
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('sms');?>" style="display: inline;"><?php echo $this->lang->line('tab_general'); ?></a>
                    <a href="<?php echo site_url('sms/setup');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                </div>
                <img src="image/twilio_logo.png" width="120"/>

                <?php
                $attributes = array('id' => 'form_sms');
                echo form_open('sms/setup',$attributes);
                ?>
                <table class="form">
                    <tr>
                        <td><span class="required">*</span> Mode :</td>
                        <td>
                            <?php
                            $options = array(
                                "sandbox"=>"sandbox",
                                "prod"=>"production"
                            );
                            echo form_dropdown('sms-mode', $options, set_value('sms-mode')?set_value('sms-mode'):(isset($sms['mode'])?$sms['mode']:'sandbox'));
                            ?>
                            <span class="error"><?php echo form_error('sms-mode'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> Account SID :</td>
                        <td>
                            <?php
                            $siddata = array('name' => 'sms-account_sid','value' => set_value('sms-account_sid')?set_value('sms-account_sid'):(isset($sms['account_sid'])?$sms['account_sid']:''), "placeholder" => 'account_sid', "class"=>"form-control");
                            echo form_input(array_merge(array('id' => 'sms-account_sid'), $siddata));
                            ?>
                            <span class="error"><?php echo form_error('sms-account_sid'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> Auth Token :</td>
                        <td>
                            <?php
                            $tokendata = array('name' => 'sms-auth_token','value' => set_value('sms-auth_token')?set_value('sms-auth_token'):(isset($sms['auth_token'])?$sms['auth_token']:''), "placeholder" => 'auth_token', "class"=>"form-control");
                            echo form_input(array_merge(array('id' => 'sms-auth_token'), $tokendata));
                            ?>
                            <span class="error"><?php echo form_error('sms-auth_token'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> Number :</td>
                        <td>
                            <?php
                            $numdata = array('name' => 'sms-number','value' => set_value('sms-number')?set_value('sms-number'):(isset($sms['number'])?$sms['number']:''), "placeholder" => '+xxxxxxxxxx', "class"=>"form-control");
                            echo form_input(array_merge(array('id' => 'sms-number'), $numdata));
                            ?>
                            <span class="error"><?php echo form_error('sms-number'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> From (Company Name) :</td>
                        <td>
                            <?php
                            $numdata = array('name' => 'sms-name','value' => set_value('sms-name')?set_value('sms-name'):(isset($sms['name'])?$sms['name']:''), "placeholder" => 'Company Name', "class"=>"form-control");
                            echo form_input(array_merge(array('id' => 'sms-name'), $numdata));
                            ?>
                            <span class="error"><?php echo form_error('sms-name'); ?></span>
                        </td>
                    </tr>
                </table>
                <?php
                echo form_submit(array('class' => 'btn btn-info', 'value' => 'save'));
                echo form_close();
                ?>

        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->