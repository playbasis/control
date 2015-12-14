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
                <!-- <button class="btn btn-info" onclick="location = baseUrlPath+'workflow/approveconfirm'" type="submit"><?php echo $this->lang->line('button_approve'); ?></button>
                -->
                <button class="btn btn-info" type="button" id="approve"><?php echo $this->lang->line('button_approve'); ?></button>
                <button class="btn btn-info" type="button" id="reject"><?php echo $this->lang->line('button_reject'); ?></button>
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
            echo form_open('workflow/pending' ,$attributes);
            ?>
            <table class="list">
                <thead>
                <tr>
                    <input type="hidden" id="action" name="action" value="" />
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_store'); ?></td>
                    <td class="center" style="width:120px;"><?php echo $this->lang->line('column_action'); ?></td>

                </tr>
                </thead>

                <tbody>
                <?php if (isset($player_list) && $player_list) { ?>
                    <?php foreach ($player_list as $player) { ?>
                    <tr>
                        <td style="text-align: center;"><?php if (isset($player['selected'])) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $player['_id']; ?>" checked="checked" />
                            <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $player['_id']; ?>" />
                            <?php } ?></td>
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
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $("#approve").click(function(e){
            e.preventDefault();
            $("#action").val("approve");
            //alert($("#action").val());
            $("#form").submit();
        });
        $("#reject").click(function(e){
            e.preventDefault();
            $("#action").val("reject");
            //alert($("#action").val());
            $("#form").submit();
        });
    });
</script>


