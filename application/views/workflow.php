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
                <button class="btn btn-info" onclick="location = baseUrlPath+'workflow/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>

            <?php
            /*$attributes = array('id' => 'form');
            echo form_open($form,$attributes);*/
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_store'); ?></td>
                        <td class="center" style="width:120px;"><?php echo $this->lang->line('column_action'); ?></td>

                    </tr>
                    </thead>

                    <tbody>
                    <?php if (isset($unapproved_list) && $unapproved_list) { ?>
                        <?php foreach ($unapproved_list as $requester) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="selected[]" value="<?php echo $requester['_id']; ?>" />
                            </td>
                            <td class="left"><?php echo $requester['first_name']."  ".$requester['last_name']; ?></td>
                            <td class="left"><?php echo (isset($requester['store']) && !is_null($requester['store']))?$requester['store']:'??'; ?></td>
                            <td class="center">[ <?php echo anchor('workflow/approve/'.$requester['_id'], 'Approve'); ?> ][ <?php echo anchor('workflow/reject/'.$requester['_id'], 'Reject'); ?> ]</td>
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
            echo form_close();?>

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

