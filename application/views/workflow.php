<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location =  baseUrlPath+'workflow/create_account'" type="button"><?php echo $this->lang->line('button_create'); ?></button>
                <button class="btn btn-info" type="button" id="delete"><?php echo $this->lang->line('button_delete'); ?></button>
                <?php if ($tab_status == "pending") { ?>
                <button class="btn btn-info" type="button" id="approve"><?php echo $this->lang->line('button_approve'); ?></button>
                <button class="btn btn-info" type="button" id="reject"><?php echo $this->lang->line('button_reject'); ?></button>
                <?php }elseif ($tab_status == "locked") { ?>
                <button class="btn btn-info" type="button" id="unlock"><?php echo $this->lang->line('button_unlock'); ?></button>
                <?php }  ?>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('workflow');?>"          <?php if ($tab_status == "approved") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_approved'); ?></a>
                <a href="<?php echo site_url('workflow/rejected');?>" <?php if ($tab_status == "rejected") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_rejected'); ?></a>
                <a href="<?php echo site_url('workflow/pending');?>"  <?php if ($tab_status == "pending")  { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_pending'); ?>
                    <?php if ($pending_count) { ?>
                    <span class="badge badge-important"><?php echo $pending_count; ?></span>
                    <?php } ?>
                </a>
                <a href="<?php echo site_url('workflow/locked');?>"  <?php if ($tab_status == "locked")  { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_locked'); ?>
                    <?php if (isset($locked_count) && $locked_count) { ?>
                        <span class="badge badge-important"><?php echo $locked_count; ?></span>
                    <?php } ?>
                </a>
            </div>
            <?php if ($error_warning) { ?>
                <div class="warning"><?php echo $error_warning; ?></div>
            <?php } ?>
            <?php if ($success) { ?>
                <div class="success"><?php echo $success; ?></div>
            <?php } ?>

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
            echo form_open($form ,$attributes);
            ?>
            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" id="user_id" name="user_id" value="" />
            <table class="list">
                <thead>
                <tr>
                    <td rowspan="2" width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td rowspan="2" class="left" style="width:100px;"><?php echo $this->lang->line('column_player_id'); ?></td>
                    <td rowspan="2" class="left" style="width:180px;"><?php echo $this->lang->line('column_name'); ?></td>
                    <td rowspan="2" class="left" style="width:180px;"><?php echo $this->lang->line('column_email'); ?></td>
                    <td rowspan="2" class="left" style="width:100px;"><?php echo $this->lang->line('column_tags'); ?></td>
                    <td rowspan="2" class="left" style="width:100px;"><?php echo $this->lang->line('column_phone'); ?></td>
                    <?php if($org_status){?>
                    <td colspan="3" class="center" style="width:200px;"><?php echo $this->lang->line('column_organization'); ?></td>
                    <?php }?>
                    <td rowspan="2" class="right app-col-action" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                </tr>
                <?php if($org_status){?>
                <tr>
                    <td style="text-align: center;"><?php echo $this->lang->line('column_node'); ?></td>
                    <td style="text-align: center;"><?php echo $this->lang->line('column_type'); ?></td>
                    <td style="text-align: center;"><?php echo $this->lang->line('column_role'); ?></td>
                </tr>
                <?php }?>
                </thead>

                <tbody>
                <tr class="filter">
                    <td></td>
                    <td><input type="text" name="filter_id"    placeholder="by ID"    value="<?php echo (isset($_GET['filter_id']) && !is_null($_GET['filter_id']))?$_GET['filter_id']:''; ?>" style="width:80%;" /></td>
                    <td><input type="text" name="filter_name"  placeholder="by name"  value="<?php echo (isset($_GET['filter_name']) && !is_null($_GET['filter_name']))?$_GET['filter_name']:''; ?>" style="width:80%;" /></td>
                    <td><input type="text" name="filter_email" placeholder="by email" value="<?php echo (isset($_GET['filter_email']) && !is_null($_GET['filter_email']))?$_GET['filter_email']:''; ?>" style="width:80%;" /></td>
                    <td><input type="text" name="filter_tag"   placeholder="by tag"   value="<?php echo (isset($_GET['filter_tag']) && !is_null($_GET['filter_tag']))?$_GET['filter_tag']:''; ?>" style="width:80%;" /></td>
                    <td></td>
                    <?php if($org_status){?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php }?>
                    <td class="right">
                        <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                        <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                    </td>
                </tr>

                <?php if (isset($player_list) && $player_list) { ?>
                    <?php foreach ($player_list as $player) { ?>
                    <tr>
                        <td style="text-align: center;"><?php if (isset($player['selected'])) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $player['_id']; ?>" checked="checked" />
                            <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $player['_id']; ?>" />
                            <?php } ?></td>
                        <td class="left"><?php echo $player['cl_player_id']; ?></td>
                        <td class="left"><?php echo $player['first_name']."  ".$player['last_name']; ?></td>
                        <td class="left"><?php echo $player['email']; ?></td>
                        <td class="left"><?php echo isset($player['tags']) ? implode(',',$player['tags']) : ''; ?></td>
                        <td class="left"><?php echo isset($player['phone_number']) ? $player['phone_number'] : ''; ?></td>
                        <?php if($org_status){?>
                        <td class="left"><?php echo (isset($player['organization_node']) && !is_null($player['organization_node']))?$player['organization_node']:''; ?></td>
                        <td class="left"><?php echo (isset($player['organization_type']) && !is_null($player['organization_type']))?$player['organization_type']:''; ?></td>
                        <td class="left"><?php echo (isset($player['organization_role']) && !is_null($player['organization_role']))?$player['organization_role']:''; ?></td>
                        <?php }?>
                        <td class="right app-col-action">
                            <?php if ($tab_status != "locked") { ?>
                            <a href="<?php echo site_url("workflow/edit_account/".$player['_id']) ?>" title="Edit" class="tooltips" data-placement="top"><i class="fa fa-edit fa-lg"></i></a>
                            <a href="javascript:void(0)" onclick="confirmDeletePlayer('<?php echo $player['_id']; ?>')" title="Delete" class="tooltips" data-placement="top"><i class="fa fa-trash fa-lg"></i></a>
                            <?php }?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <?php if ($tab_status == "approved") { ?>
                            <td class="center" colspan="10"><?php echo $this->lang->line('text_no_approved_results'); ?></td>
                        <?php }elseif($tab_status == "rejected"){?>
                            <td class="center" colspan="10"><?php echo $this->lang->line('text_no_rejected_results'); ?></td>
                        <?php }elseif($tab_status == "pending"){?>
                            <td class="center" colspan="10"><?php echo $this->lang->line('text_no_pending_request'); ?></td>
                        <?php }else{?>
                            <td class="center" colspan="10"><?php echo $this->lang->line('text_no_locked_results'); ?></td>
                        <?php }?>
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


<script type="text/javascript">

    function deletePlayer(player_id) {
        //var platform = new Array(platform_id);
        //console.log('start');
        $.ajax({
            url: baseUrlPath+'workflow/pending',
            type: 'POST',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
            success: function() {
                location.href = baseUrlPath+'workflow';
            }
        });

        return false;
    }
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#approve").click(function(e){
            e.preventDefault();
            var checkboxes = document.querySelectorAll('input[name="selected[]"]:checked'), values = [];
            Array.prototype.forEach.call(checkboxes, function(el) {
                values.push(el.value);
            });
            if(values != '') {
                if (confirm("Please confirm to approve the selected account(s)")) {
                    $("#action").val("approve");
                    //alert($("#action").val());
                    $("#form").submit();
                }
            }else{
                alert("Please select account to approve");
            }
        });
        $("#reject").click(function(e){
            e.preventDefault();
            var checkboxes = document.querySelectorAll('input[name="selected[]"]:checked'), values = [];
            Array.prototype.forEach.call(checkboxes, function(el) {
                values.push(el.value);
            });
            if(values != '') {
                if (confirm("Please confirm to reject the selected account(s)")) {
                    $("#action").val("reject");
                    //alert($("#action").val());
                    $("#form").submit();
                }
            }else{
                alert("Please select account to reject");
            }
        });


        $("#delete").click(function(e){
            e.preventDefault();

            var checkboxes = document.querySelectorAll('input[name="selected[]"]:checked'), values = [];
            Array.prototype.forEach.call(checkboxes, function(el) {
                values.push(el.value);
            });
            if(values != '') {
                if(confirm("Are you sure to delete this account?")){
                $("#action").val("delete");
                 //alert($("#action").val());
                $("#form").submit();
                }
            }else{
                alert("Please select account to delete");
            }
        });
        $("#unlock").click(function(e){
            e.preventDefault();

            var checkboxes = document.querySelectorAll('input[name="selected[]"]:checked'), values = [];
            Array.prototype.forEach.call(checkboxes, function(el) {
                values.push(el.value);
            });
            if(values != '') {
                if(confirm("Are you sure to unlock this account?")){
                    $("#action").val("unlock");
                    //alert($("#action").val());
                    $("#form").submit();
                }
            }else{
                alert("Please select account to unlock");
            }
        });

    });

    function confirmDeletePlayer(player_id){

        var decision = confirm('Are you sure to delete this account?');
        if (decision){
            //console.log("yes");
            $("#action").val("delete");
            $("#user_id").val(player_id);
            //alert($("#action").val());
            $("#form").submit();
        }
    }

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>

<script type="text/javascript"><!--
    function filter() {

        url = baseUrlPath+'workflow';

        <?php if($tab_status == "rejected"){?>
            url += '/rejected';
        <?php }elseif($tab_status == "pending"){?>
            url += '/pending';
        <?php }elseif($tab_status == "locked"){?>
            url += '/locked';
        <?php }?>

        var filter_name = $('input[name=\'filter_name\']').attr('value');
        var filter_id = $('input[name=\'filter_id\']').attr('value');
        var filter_email = $('input[name=\'filter_email\']').attr('value');
        var filter_tag = $('input[name=\'filter_tag\']').attr('value');

        url += '?filter_id=' + encodeURIComponent(filter_id)+'&filter_name=' + encodeURIComponent(filter_name)+'&filter_email=' + encodeURIComponent(filter_email)+'&filter_tag=' + encodeURIComponent(filter_tag);

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])&&!isset($_GET['filter_id'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        <?php if($tab_status == "rejected"){?>
        window.location.replace(baseUrlPath+'workflow/rejected');
        <?php }elseif($tab_status == "pending"){?>
        window.location.replace(baseUrlPath+'workflow/pending');
        <?php }elseif($tab_status == "locked"){?>
        window.location.replace(baseUrlPath+'workflow/locked');
        <?php }elseif($tab_status == "approved"){?>
        window.location.replace(baseUrlPath+'workflow');
        <?php }?>

    }
</script>


