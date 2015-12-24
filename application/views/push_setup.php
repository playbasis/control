<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/sms/style.css">
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('push');?>" style="display: inline;"><?php echo $this->lang->line('tab_template'); ?></a>
                    <a href="<?php echo site_url('push/ios');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_ios'); ?></a>
                    <a href="<?php echo site_url('push/android');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_android'); ?></a>
                </div>

                <?php
                $attributes = array('id' => 'form_push');
                echo form_open('push/ios',$attributes);
                ?>
                <table class="form">
                    <tr>
                        <td><span class="required">*</span> Environment :</td>
                        <td>
                            <?php
                            $options = array(
                                "sandbox" => "sandbox",
                                "prod" => "production"
                            );
                            echo form_dropdown('push-env', $options, set_value('push-env')?set_value('push-env'):(isset($push['env'])?$push['env']:'sandbox'));
                            ?>
                            <span class="error"><?php echo form_error('push-env'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> Provider Certificate :</td>
                        <td>
                            <?php
                            $certificate = array('name' => 'push-certificate','value' => set_value('push-certificate')?set_value('push-certificate'):(isset($push['certificate'])?$push['certificate']:''), "placeholder" => 'certificate', "class"=>"form-control");
                            echo form_textarea(array_merge(array('id' => 'push-certificate'), $certificate), '', ' style="min-width:400px;"');
                            ?>
                            <span class="error"><?php echo form_error('push-certificate'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> Certificate Passphrase :</td>
                        <td>
                            <?php
                            $password = array('name' => 'push-password','value' => set_value('push-password')?set_value('push-password'):(isset($push['password'])?$push['password']:''), "placeholder" => 'password', "class"=>"form-control");
                            echo form_password(array_merge(array('id' => 'push-password'), $password));
                            ?>
                            <span class="error"><?php echo form_error('push-password'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> Root Certification Authority :</td>
                        <td>
                            <?php
                            $ca = array('name' => 'push-ca','value' => set_value('push-ca')?set_value('push-ca'):(isset($push['ca'])?$push['ca']:''), "placeholder" => 'ca', "class"=>"form-control");
                            echo form_textarea(array_merge(array('id' => 'push-ca'), $ca), '', ' style="min-width:400px;"');
                            ?>
                            <span class="error"><?php echo form_error('push-ca'); ?></span>
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