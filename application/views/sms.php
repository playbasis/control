<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
            <label class="col-md-4" >
                <img src="image/twilio_logo.png" width="120"/>
            <div class="twilio-config">
                <?php
                $attributes = array('id' => 'form_sms');
                echo form_open('sms',$attributes);
                ?>
                <div class="form-group">
                    <label class="col-md-4" >
                        Mode :
                    <?php
                    $modedata = array('name' => 'sms-mode','value' => set_value('sms-mode')?set_value('sms-mode'):(isset($sms['mode'])?$sms['mode']:'sandbox'), "placeholder" => 'Mode', "class"=>"form-control");
                    echo form_input(array_merge(array('id' => 'sms-mode'), $modedata));
                    ?>
                    </label>
                    <span class="error"><?php echo form_error('sms-mode'); ?></span>
                </div>
                <div class="form-group">
                    <label class="col-md-4" >
                        Account SID :
                    <?php
                    $siddata = array('name' => 'sms-account_sid','value' => set_value('sms-account_sid')?set_value('sms-account_sid'):(isset($sms['account_sid'])?$sms['account_sid']:''), "placeholder" => 'account_sid', "class"=>"form-control");
                    echo form_input(array_merge(array('id' => 'sms-account_sid'), $siddata));
                    ?>
                    </label>
                    <span class="error"><?php echo form_error('sms-account_sid'); ?></span>
                </div>
                <div class="form-group">
                    <label class="col-md-4" >
                        Auth Token :
                    <?php
                    $tokendata = array('name' => 'sms-auth_token','value' => set_value('sms-auth_token')?set_value('sms-auth_token'):(isset($sms['auth_token'])?$sms['auth_token']:''), "placeholder" => 'auth_token', "class"=>"form-control");
                    echo form_input(array_merge(array('id' => 'sms-auth_token'), $tokendata));
                    ?>
                    </label>
                    <span class="error"><?php echo form_error('sms-auth_token'); ?></span>
                </div>
                <div class="form-group">
                    <label class="col-md-4" >
                        Number :
                    <?php
                    $numdata = array('name' => 'sms-number','value' => set_value('sms-number')?set_value('sms-number'):(isset($sms['number'])?$sms['number']:''), "placeholder" => '+xxxxxxxxxx', "class"=>"form-control");
                    echo form_input(array_merge(array('id' => 'sms-number'), $numdata));
                    ?>
                    </label>
                    <span class="error"><?php echo form_error('sms-number'); ?></span>
                </div>
                <?php
                echo form_submit(array('class' => 'btn btn-info', 'value' => 'save'));
                echo form_close();
                ?>
            </div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->