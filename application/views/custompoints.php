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
            <?php //if($user_group_id != $setting_group_id){ ?>
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'custompoints/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
            <?php //}?>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open('custompoints/delete',$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($custompoints)) { ?>
                            <?php foreach ($custompoints as $cs) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if ($client_id){?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $cs['reward_id']; ?>" />
                                    <?php }?>
                                </td>
                                <td class="left"><?php echo $cs['name']; ?></td>
                                <td class="right">
                                    <?php if(!$client_id){?>
                                        [ <?php echo anchor('custompoints/update/'.$cs['reward_id'], 'Edit'); ?> ]
                                    <?php }else{?>
                                        [ <?php echo anchor('custompoints/update/'.$cs['reward_id'], 'Edit'); ?> ]
                                    <?php }?>
                                </td>
                            </tr>
                                <?php } ?>
                            <?php } else { ?>
                        <tr>
                            <td class="center" colspan="8"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->
            <!--
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                <?php //if($pagination_links != ''){?>
                        <?php //echo $pagination_links;?>
                <?php //}?>
                </ul>
            </div>
            -->
        </div>
    </div>
</div>

<script type="text/javascript">

$('.push_down').live("click", function(){

    $.ajax({
        url : baseUrlPath+'badge/increase_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'badge/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;

});
</script>


<script type="text/javascript">
$('.push_up').live("click", function(){
    $.ajax({
        url : baseUrlPath+'badge/decrease_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'badge/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;
});

</script>

