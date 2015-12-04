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
            echo form_open('push/android',$attributes);
            ?>
            <table class="form">
                <tr>
                    <td><span class="required">*</span> API Key :</td>
                    <td>
                        <?php
                        $api_key = array('name' => 'push-key','value' => set_value('push-key')?set_value('push-key'):(isset($push['api_key'])?$push['api_key']:''), "placeholder" => 'api_key', "class"=>"form-control");
                        echo form_textarea(array_merge(array('id' => 'push-key'), $api_key));
                        ?>
                        <span class="error"><?php echo form_error('push-key'); ?></span>
                    </td>
                </tr>
                <tr>
                    <td><span class="required">*</span> Sender ID :</td>
                    <td>
                        <?php
                        $sender_id = array('name' => 'push-sender','value' => set_value('push-sender')?set_value('push-sender'):(isset($push['sender_id'])?$push['sender_id']:''), "placeholder" => 'sender_id', "class"=>"form-control");
                        echo form_textarea(array_merge(array('id' => 'push-sender'), $sender_id), '', ' style="min-width:400px;"');
                        ?>
                        <span class="error"><?php echo form_error('push-sender'); ?></span>
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