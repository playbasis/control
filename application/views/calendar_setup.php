<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <?php if (!isset($calendar)) { ?>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_upload'); ?></button>
                <?php } ?>
            </div>
        </div>
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('calendar');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('calendar/place');?>" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('calendar/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
                </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
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
            echo form_open_multipart($form ,$attributes);
            ?>
            <?php if (isset($calendar)) { ?>
                <table class="form">
                    <tr>
                        <td>Client ID :</td>
                        <td><?php echo $calendar['google_client_id']; ?></td>
                    </tr>
                    <tr>
                        <td>Client Secret :</td>
                        <td><?php echo $calendar['google_client_secret']; ?></td>
                    </tr>
                    <tr>
                        <td>Google URL :</td>
                        <td><?php echo $calendar['google_url']; ?></td>
                    </tr>
                    <tr>
                        <td>Token :</td>
                        <td><?php echo isset($calendar['token']) ? $calendar['token']['access_token'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td>Token Expires :</td>
                        <td><?php echo isset($calendar['token']) ? date('d M Y H:i:s', $calendar['token']['date_expire']->sec) : 'N/A'; ?></td>
                    </tr>
                </table>
                <?php if (!isset($calendar['token'])) { ?>
                Please click the button below to authorize Playbasis <br>
                <a href="<?php echo $calendar['google_url'].'?client_id='.$calendar['google_client_id'].'&response_type=code&scope=https://www.googleapis.com/auth/calendar.readonly&redirect_uri='.base_url().'calendar/authorize&access_type=offline'; ?>" class="btn btn-success btn-mini">Authorize</a>
                <?php } ?>
            <?php } else { ?>
                Please upload your "client_secret.json" using the form below.
                <table class="form">
                    <tr>
                        <td><span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>:</td>
                        <td><input id="file" type="file" name="file" size="100" /></td>
                    </tr>
                </table>
            <?php } ?>
            <?php echo form_close(); ?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->