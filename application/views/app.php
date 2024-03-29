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
                <?php if( ($plan_limit_app !== null &&  $total_app >= $plan_limit_app) || ($plan_limit_platform !== null && $total_platform >= $plan_limit_platform)){ ?>
                    <button class="btn btn-default disabled" disabled type="button"><?php echo $this->lang->line('button_add_app'); ?></button>
                <?php }else{ ?>
                    <button class="btn btn-info" onclick="location = baseUrlPath+'app/add'" type="button"><?php echo $this->lang->line('button_add_app'); ?></button>
                <?php } ?>
                <?php if( $plan_limit_platform !== null && $total_platform >= $plan_limit_platform){ ?>
                    <button class="btn btn-default disabled" disabled type="button">Add Platform</button>
                <?php }else{ ?>
                    <button class="btn btn-info" onclick="location='<?php echo site_url("app/add_platform/".$site_id); ?>'" type="button">Add Platform</button>
                <?php } ?>
                <button class="btn btn-info" onclick="location='<?php echo site_url("app/edit_app/".$site_id); ?>'" type="button">Edit App</button>
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
                <?php if (isset($site_list)) { ?>
                    <?php foreach ($site_list as $site) {
                    if ($site['site_id'] == $site_id) {
                    ?>
                    <table class="list app-table">
                        <tbody>
                            <tr class="app-table-label">
                                <td style="text-align: center;" width="7">
                                    <input type="checkbox" name="app_selected[]" value="<?php echo $site['site_id']; ?>" onclick="$(this).parent().parent().parent().parent().find('input[name*=\'selected\']').attr('checked', this.checked);">
                                </td>
                                <td class="center" width="10%">
                                    Platform
                                </td>
                                <td class="center" width="30%">
                                    Platform Url
                                </td>
                                <td class="center" width="15%">
                                    Api Key
                                </td>
                                <td class="center" width="30%">
                                    Api Secret
                                </td>
                                <td class="center" width="5%">
                                    Status
                                </td>
                                <td class="right app-col-action" width="5%">
                                    Action
                                </td>
                            </tr>
                            <?php
                            foreach($site["apps"] as $app){
                            ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="platform_selected[]" value="<?php echo $app['_id']; ?>">
                                </td>
                                <td class="left app-col-platform">
                                    <?php
                                    if($app['platform'] == 'web'){
                                        $aicon = 'fa-desktop';
                                        $aname = 'Web Site';
                                    }elseif($app['platform'] == 'ios'){
                                        $aicon = 'fa-apple';
                                        $aname = 'IOS';
                                    }elseif($app['platform'] == 'android'){
                                        $aicon = 'fa-android';
                                        $aname = 'Android';
                                    }
                                    ?>
                                    <i class="fa <?php echo $aicon; ?> fa-lg"></i> <?php echo $aname; ?>
                                </td>
                                <td >
                                    <?php
                                    if($app['platform'] == 'web'){
                                        $asite = $app['data']['site_url'];
                                    }elseif($app['platform'] == 'ios'){
                                        $asite = $app['data']['ios_bundle_id'];
                                    }elseif($app['platform'] == 'android'){
                                        $asite = $app['data']['android_package_name'];
                                    }
                                    ?>
                                    <?php echo $asite; ?>
                                </td>
                                <td >
                                    <?php echo $app['api_key']; ?>
                                </td>
                                <td >
                                    <?php echo $app['api_secret']; ?>
                                    <a href="javascript:void(0)" onclick="confirmationReset('<?php echo $app['_id']; ?>')" title="Reset Api Secret" class="tooltips" data-placement="right"><i class="fa fa-repeat fa-lg"></i></a>
                                </td>
                                <td class="right">
                                    <?php if ($app['status']==1) { ?>
                                        <?php echo $this->lang->line('text_enabled'); ?>
                                    <?php } else { ?>
                                        <?php echo $this->lang->line('text_disabled'); ?>
                                    <?php } ?>
                                </td>
                                <td class="right app-col-action">
                                    <a href="<?php echo site_url("app/platform_edit/".$app['_id']) ?>" title="Edit" class="tooltips" data-placement="top"><i class="fa fa-edit fa-lg"></i></a>
                                    <a href="javascript:void(0)" onclick="confirmationDelete('<?php echo $app['_id']; ?>')" title="Delete" class="tooltips" data-placement="top"><i class="fa fa-trash fa-lg"></i></a>
                                </td>

                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php }} ?>
                <?php }else{

                    } ?>
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

<script type="text/javascript">
    function resetSecret(platform_id) {
        $.ajax({
            url: baseUrlPath+'app/reset',
            type: 'POST',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','platform_id': platform_id},
            dataType: 'json',
            success: function(json) {
                if(json.success){
                    location.href = baseUrlPath+'app';
                }
            }
        });

        return false;
    }

    function deletePlatform(platform_id) {
        var platform = new Array(platform_id);
        console.log('start');
        $.ajax({
            url: baseUrlPath+'app/delete',
            type: 'POST',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>',platform_selected: platform},
            success: function() {
                location.href = baseUrlPath+'app';
            }
        });

        return false;
    }
</script>

<script type="text/javascript">
    function confirmationReset(platform_id){
        var decision = confirm('Are you sure to reset the secret key ?');
        if (decision){
            resetSecret(platform_id);
        }
    }

    function confirmationDelete(platform_id){
        var decision = confirm('Are you sure to delete ?');
        if (decision){
            deletePlatform(platform_id);
        }
    }
</script>
