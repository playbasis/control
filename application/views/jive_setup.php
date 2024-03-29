<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('jive');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('jive/place');?>" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('jive/event');?>" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('jive/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
                </div>

            <?php if (isset($jive)) { ?>
                <table class="form">
                    <tr>
                        <td>Client ID :</td>
                        <td><?php echo $jive['jive_client_id']; ?></td>
                    </tr>
                    <tr>
                        <td>Client Secret :</td>
                        <td><?php echo $jive['jive_client_secret']; ?></td>
                    </tr>
                    <tr>
                        <td>Jive URL :</td>
                        <td><?php echo $jive['jive_url']; ?></td>
                    </tr>
                    <tr>
                        <td>Token :</td>
                        <td><?php echo isset($jive['token']) ? $jive['token']['access_token'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td>Token Expires :</td>
                        <td><?php echo isset($jive['token']) ? date('d M Y H:i:s', $jive['token']['date_expire']->sec) : 'N/A'; ?></td>
                    </tr>
                </table>
                <?php if (!isset($jive['token'])) { ?>
                Please click the button below to authorize Playbasis a new token <br>
                <a href="<?php echo $jive['jive_url'].'/oauth2/authorize?client_id='.$jive['jive_client_id'].'&response_type=code'; ?>" class="btn btn-success btn-mini">Authorize</a>
                <?php } ?>
            <?php } else { ?>
                Please download below Playbasis add-on package and then have an administrator install it on your Jive community website.
                <a href="<?php echo site_url('jive/download');?>" class="btn btn-success btn-mini">Download</a>
            <?php } ?>

        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->