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
                <button class="btn btn-info" onclick="location = baseUrlPath+'app/add'" type="button"><?php echo $this->lang->line('button_add_app'); ?></button>
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
            echo form_open('app/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_platform'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_key'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_secret'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($domain_list)) { ?>
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

                                <a href="" ><?php echo $domain['site_id']; ?></a>
                            </td>

                                <?php
                                foreach($domain["apps"] as $app){
                                ?>
                                <td class="left">
                                <?php echo $app['platform']; ?>
                                </td>
                                <td class="left">
                                    <?php echo $app['api_key']; ?>
                                </td>
                                <td class="left">
                                    <?php echo $app['api_secret']; ?>
                                </td>
                                <td class="right">
                                    <?php if ($app['status']==1) { ?>
                                        <?php echo $this->lang->line('text_enabled'); ?>
                                    <?php } else { ?>
                                        <?php echo $this->lang->line('text_disabled'); ?>
                                    <?php } ?>
                                </td>
                                <td class="left">
                                    <a href="<?php echo site_url("app/platform_edit/".$app['_id']) ?>" >Edit</a>
                                </td>
                                <?php
                                }
                                ?>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="4"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            ?>


            <table class="list app-table">
                <thead>
                <tr>
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$(this).parent().parent().parent().parent().find('input[name*=\'selected\']').attr('checked', this.checked);"></td>
                    <td class="left" colspan="5"><h3>APP NAME</h3> <button class="btn btn-info btn-mini" onclick="location = baseUrlPath+'app/add'" type="button">Add Platform</button></td>
                </tr>
                </thead>
                <tbody>
                    <tr class="app-table-label">
                        <td style="text-align: center;">
                            
                        </td>
                        <td class="left">
                            Platform
                        </td>
                        <td >
                            Api Key
                        </td>
                        <td >
                            Api Secret
                        </td>
                        <td class="right">
                            Status
                        </td>
                        <td class="right app-col-action">
                            Action
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" name="selected[]" value="54609911d4139e702bd63af4">
                        </td>
                        <td class="left app-col-platform">
                            <i class="fa fa-desktop fa-lg"></i> Web Site
                        </td>
                        <td >
                            1495118408
                        </td>
                        <td >
                            9aafb37ee5df01c42117c5788759daa4 <a href="javascript:void(0)" onclick="alert('Reset Secret')" title="Reset Api Secret" class="tooltips" data-placement="right"><i class="fa fa-repeat fa-lg"></i></a>
                        </td>
                        <td class="right">
                            Enabled
                        </td>
                        <td class="right app-col-action">
                            <a href="javascript:void(0)" title="Edit" class="tooltips" data-placement="top"><i class="fa fa-edit fa-lg"></i></a>
                            <a href="javascript:void(0)" title="Delete" class="tooltips" data-placement="top"><i class="fa fa-trash fa-lg"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" name="selected[]" value="54609911d4139e702bd63af4">
                        </td>
                        <td class="left app-col-platform">
                            <i class="fa fa-apple fa-lg"></i> iOS
                        </td>
                        <td >
                            1495118408
                        </td>
                        <td >
                             9aafb37ee5df01c42117c5788759daa4 <a href="javascript:void(0)" onclick="alert('Reset Secret')" title="Reset Api Secret" class="tooltips" data-placement="right"><i class="fa fa-repeat fa-lg"></i></a>
                        </td>
                        <td class="right">
                            Enabled
                        </td>
                        <td class="right app-col-action">
                            <a href="javascript:void(0)" title="Edit" class="tooltips" data-placement="top"><i class="fa fa-edit fa-lg"></i></a>
                            <a href="javascript:void(0)" title="Delete" class="tooltips" data-placement="top"><i class="fa fa-trash fa-lg"></i></a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" name="selected[]" value="54609911d4139e702bd63af4">
                        </td>
                        <td class="left app-col-platform">
                            <i class="fa fa-android fa-lg"></i> Android
                        </td>
                        <td >
                            1495118408
                        </td>
                        <td >
                             9aafb37ee5df01c42117c5788759daa4 <a href="javascript:void(0)" onclick="alert('Reset Secret')" title="Reset Api Secret" class="tooltips" data-placement="right"><i class="fa fa-repeat fa-lg"></i></a>
                        </td>
                        <td class="right">
                            Enabled
                        </td>
                        <td class="right app-col-action">
                            <a href="javascript:void(0)" title="Edit" class="tooltips" data-placement="top"><i class="fa fa-edit fa-lg"></i></a>
                            <a href="javascript:void(0)" title="Delete" class="tooltips" data-placement="top"><i class="fa fa-trash fa-lg"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>


            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <?php echo $pagination_links; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--

function resetSecret(site_id) {
    $.ajax({
        url: baseUrlPath+'app/reset',
        type: 'POST',
        data: 'site_id=' + site_id,
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
