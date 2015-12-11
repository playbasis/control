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
                <button class="btn btn-info" onclick="location = baseUrlPath+'workflow/addaccount'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <?php if ($tab_status == "approved") { ?>
                <a href="<?php echo site_url('workflow');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_approved'); ?></a>
                <a href="<?php echo site_url('workflow/rejected');?>" style="display: inline;"><?php echo $this->lang->line('tab_rejected'); ?></a>
                <a href="<?php echo site_url('workflow/pending');?>" style="display: inline;"><?php echo $this->lang->line('tab_pending'); ?></a>
                <?php } elseif($tab_status == "rejected"){ ?>
                <a href="<?php echo site_url('workflow');?>" style="display: inline;"><?php echo $this->lang->line('tab_approved'); ?></a>
                <a href="<?php echo site_url('workflow/rejected');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_rejected'); ?></a>
                <a href="<?php echo site_url('workflow/pending');?>" style="display: inline;"><?php echo $this->lang->line('tab_pending'); ?></a>
                <?php } else{ ?>
                <a href="<?php echo site_url('workflow');?>" style="display: inline;"><?php echo $this->lang->line('tab_approved'); ?></a>
                <a href="<?php echo site_url('workflow/rejected');?>" style="display: inline;"><?php echo $this->lang->line('tab_rejected'); ?></a>
                <a href="<?php echo site_url('workflow/pending');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_pending'); ?></a>
                <?php }  ?>
            </div>

            <table class="list">
                <thead>
                <tr>
                    <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_store'); ?></td>
                    <td class="center" style="width:120px;"><?php echo $this->lang->line('column_action'); ?></td>

                </tr>
                </thead>

                <tbody>
                <?php if (isset($player_list) && $player_list) { ?>
                    <?php foreach ($player_list as $player) { ?>
                    <tr>
                        <td class="left"><?php echo $player['first_name']."  ".$player['last_name']; ?></td>
                        <td class="left"><?php echo (isset($player['store']) && !is_null($player['store']))?$player['store']:'??'; ?></td>
                        <td class="center">[ <?php echo anchor('workflow/approveconfirm/'.$player['_id'], 'Approve'); ?> ][ <?php echo anchor('workflow/rejectconfirm/'.$player['_id'], 'Reject'); ?> ]</td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="center" colspan="9"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>


        </div>
    </div>
</div>

<script type="text/javascript">

$('.push_down').live("click", function(){

    $.ajax({
        url : baseUrlPath+'goods/increase_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'goods/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#goods').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;

});
</script>


<script type="text/javascript">
$('.push_up').live("click", function(){
    $.ajax({
        url : baseUrlPath+'goods/decrease_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'goods/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#goods').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;
});

</script>

