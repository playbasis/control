<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('calendar');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('calendar/place');?>" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('calendar/event');?>" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('calendar/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
                </div>

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
                        <td>Jive URL :</td>
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
                Please click the button below to authorize Playbasis a new token <br>
                <a href="<?php echo $calendar['google_url'].'/oauth2/authorize?client_id='.$calendar['google_client_id'].'&response_type=code'; ?>" class="btn btn-success btn-mini">Authorize</a>
                <?php } ?>
            <?php } else { ?>
                Please download below Playbasis add-on package and then have an administrator install it on your Jive community website.
                <a href="<?php echo site_url('calendar/download');?>" class="btn btn-success btn-mini">Download</a>
            <?php } ?>

        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->