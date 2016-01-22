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
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'domain/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if ($this->session->flashdata("fail")): ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata("fail"); ?></div>
                </div>
            <?php endif; ?>
            <?php
            $attributes = array('id' => 'form');
            echo form_open('domain/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="left" style="width:100px;"><?php echo $this->lang->line('column_key'); ?></td>
                        <td class="left" style="width:300px;"><?php echo $this->lang->line('column_secret'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($domain_list) && $domain_list) { ?>
                        <?php foreach ($domain_list as $domain) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if ($domain['selected']) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $domain['site_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $domain['site_id']; ?>" />
                                <?php } ?>
                            </td>
                            <td class="left">
                                <?php echo $domain['domain_name']; ?>
                            </td>
                            <td class="left">
                                <?php echo $domain['keys']; ?>
                            </td>
                            <td class="left">
                                <?php echo $domain['secret']; ?> [ <?php echo anchor('domain', $this->lang->line('text_reset_secret'), array('onclick' => "confirmation('".$domain['site_id']."'); return false;")); ?> ]
                            </td>
                            <td class="right">
                                <?php if ($domain['status']==1) { ?>
                                <?php echo $this->lang->line('text_enabled'); ?>
                                <?php } else { ?>
                                <?php echo $this->lang->line('text_disabled'); ?>
                                <?php } ?>
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="5"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            ?>
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--

function resetSecret(site_id) {
    $.ajax({
        url: baseUrlPath+'domain/reset',
        type: 'POST',
        data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','site_id':site_id},
        dataType: 'json',
        success: function(json) {
            if(json.success){
                location.href = baseUrlPath+'domain';
            }
        }
    });

    return false;
}

//--></script>

<script type="text/javascript">
    function confirmation(site_id){
        var decision = confirm('Are you sure you want to reset the key?');
        if (decision){
            resetSecret(site_id);
        }
    }
</script>
