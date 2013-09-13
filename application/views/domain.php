<div id="content" class="span10">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div class="content">
            <?php
            $attributes = array('id' => 'form');
            echo form_open('domain/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <!--td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td-->
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_start'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_expire'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <?php if ($user_group_id==$setting_group_id) { ?>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($domain_list) { ?>
                        <?php foreach ($domain_list as $domain) { ?>
                        <tr>
                            <td class="left">
                                <?php echo $domain['domain_name']; ?> [ <a href="domain/domain" class="button_reset_token" onclick="resetSecret(<?php echo $domain['site_id']; ?>);" ><?php echo $this->lang->line('text_reset_token'); ?></a> ]
                                <br /><span class="help">Keys:</span> <?php echo $domain['keys']; ?>
                                <br /><span class="help">Secret:</span> <?php echo $domain['secret']; ?>
                            </td>
                            <td class="right">
                                <?php if (strtotime($domain['date_start'])) { ?>
                                <?php echo date('Y-m-d', strtotime($domain['date_start'])); ?>
                                <?php } else { ?>
                                -
                                <?php } ?>
                            </td>
                            <td class="right">
                                <?php if (strtotime($domain['date_expire'])) { ?>
                                <?php echo date('Y-m-d', strtotime($domain['date_expire'])); ?>
                                <?php } else { ?>
                                -
                                <?php } ?>
                            </td>
                            <td class="right">
                                <?php if ($domain['status']==1) { ?>
                                <?php echo $this->lang->line('text_enabled'); ?>
                                <?php } else { ?>
                                <?php echo $this->lang->line('text_disabled'); ?>
                                <?php } ?>
                            </td>
                            <?php if ($user_group_id==$setting_group_id) { ?>
                            <td class="right">
                                <?php foreach ($domain['action'] as $action) { ?>
                                <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a>
                                <?php } ?></td>
                            <?php } ?>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            ?>
            <div class="pagination"><?php echo $pagination_links; ?></div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--

function resetSecret(site_id) {
    //console.log(site_id);

    $.ajax({
        url: 'domain/reset',
        type: 'POST',
        data: 'site_id=' + site_id,
        dataType: 'json',
        success: function(json) {
            // console.log(json);
            location.href = 'domain/domain';
        }
    });

    return false;
}

//--></script>