<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('jive');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('jive/event');?>" style="display: inline;"><?php echo $this->lang->line('tab_event'); ?></a>
                    <a href="<?php echo site_url('jive/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_subscription'); ?></a>
                </div>

            <?php if (isset($lithium)) { ?>
                <table class="form">
                    <tr>
                        <td>Lithium URL :</td>
                        <td><?php echo $lithium['lithium_url']; ?></td>
                    </tr>
                    <tr>
                        <td>Client ID :</td>
                        <td><?php echo $lithium['lithium_client_id']; ?></td>
                    </tr>
                    <tr>
                        <td>Client Secret :</td>
                        <td><?php echo $lithium['lithium_client_secret']; ?></td>
                    </tr>
                    <tr>
                        <td>Token :</td>
                        <td><?php echo isset($lithium['token']) ? $lithium['token']['access_token'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td>Token Expires :</td>
                        <td><?php echo isset($lithium['token']) ? date('d M Y H:i:s', $lithium['token']['date_expire']->sec) : 'N/A'; ?></td>
                    </tr>
                </table>
                <?php if (!isset($lithium['token'])) { ?>
                Please click the button below to authorize Playbasis a new token <br>
                <a href="<?php echo $lithium['lithium_url'].'/oauth2/authorize?client_id='.$lithium['lithium_client_id'].'&response_type=code'; ?>" class="btn btn-success btn-mini">Authorize</a>
                <?php } ?>
            <?php } else { ?>
                Please provide information about your Lithium setup.
                <table class="form">
                    <tr>
                        <td>Lithium URL :</td>
                        <td><?php echo $lithium['lithium_url']; ?></td>
                    </tr>
                    <tr>
                        <td>Client ID :</td>
                        <td><?php echo $lithium['lithium_client_id']; ?></td>
                    </tr>
                    <tr>
                        <td>Client Secret :</td>
                        <td><?php echo $lithium['lithium_client_secret']; ?></td>
                    </tr>
                    <tr>
                        <td>Token :</td>
                        <td><?php echo isset($lithium['token']) ? $lithium['token']['access_token'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td>Token Expires :</td>
                        <td><?php echo isset($lithium['token']) ? date('d M Y H:i:s', $lithium['token']['date_expire']->sec) : 'N/A'; ?></td>
                    </tr>
                </table>
            <?php } ?>

        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->