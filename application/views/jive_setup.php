<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('jive');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('jive/tokens');?>" style="display: inline;"><?php echo $this->lang->line('tab_tokens'); ?></a>
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
                </table>
            <?php } else { ?>
                Please download below Playbasis add-on package and then have an administrator install it on your Jive community website.
            <?php } ?>

        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->