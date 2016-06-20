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
                <button class="btn btn-info" onclick="location = baseUrlPath+'sms/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
            <?php //}?>
        </div>
        <div class="content">
            
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('sms');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_general'); ?></a>
                <a href="<?php echo site_url('sms/setup');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
            </div>

            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open('sms/delete',$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="left" ><?php echo $this->lang->line('column_body'); ?></td>
                            <td class="left" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($templates) && $templates) { ?>
                            <?php foreach ($templates as $sms) { ?>
                            <tr <?php if (isset($sms["is_template"]) && $sms["is_template"]) {?> class="sms_template" <?php } ?>>
                                <td style="text-align: center;">
                                    <?php if ($sms['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $sms['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $sms['_id']; ?>" />
                                    <?php } ?>
                                </td>
                                <td class="left"><?php echo $sms['name']; ?></td>
                                <td class="left"><?php echo $sms['body']; ?>  </td>
                                <td class="left"><?php echo ($sms['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo $sms['sort_order']; ?></td>
                                <td class="right">
                                    <?php echo anchor('sms/update/'.$sms['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                        array('class'=>'tooltips',
                                            'title' => 'Edit',
                                            'data-placement' => 'top'
                                        ));
                                    ?>
                                    <?php echo anchor('sms/inscrease_order/'.$sms['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$sms['_id'], 'style'=>'text-decoration:none'));?>
                                    <?php echo anchor('sms/decrease_order/'.$sms['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$sms['_id'], 'style'=>'text-decoration:none' ));?>
                                </td>
                            </tr>
                                <?php } ?>
                            <?php } else { ?>
                        <tr>
                            <td class="center" colspan="6"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->
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

$('.push_down').live("click", function(){

    $.ajax({
        url : baseUrlPath+'sms/increase_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'sms/getListForAjax/';
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
        url : baseUrlPath+'sms/decrease_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'sms/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;
});

</script>

