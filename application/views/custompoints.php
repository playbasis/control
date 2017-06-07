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
                <button class="btn btn-info" onclick="$('#customPointImportModal').modal({'backdrop': true});$('#file-import').val('');" type="button"><?php echo $this->lang->line('button_import'); ?></button>
                <button class="btn btn-info" onclick="custompoint_export();" type="button"><?php echo $this->lang->line('button_export'); ?></button>
                <button class="btn btn-info" onclick="custompoint_delete()" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
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
                            <td width="10" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="center" style="width:60px;"><?php echo $this->lang->line('column_type'); ?></td>
                            <td class="center" style="width:60px;"><?php echo $this->lang->line('column_quantity'); ?></td>
                            <td class="center" style="width:60px;"><?php echo $this->lang->line('column_peruser'); ?></td>
                            <td class="center" style="width:100px;"><?php echo $this->lang->line('column_pending'); ?></td>
                            <td class="center" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td class="center" style="width:50px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($custompoints) && $custompoints) { ?>
                            <?php foreach ($custompoints as $cs) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if ($client_id){?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $cs['reward_id']; ?>" />
                                    <?php }?>
                                </td>
                                <td class="left"><?php echo $cs['name']; ?></td>
                                <td class="left"><?php echo isset($cs['type']) ? $cs['type'] : ""; ?></td>
                                <td class="left"><?php echo isset($cs['quantity']) ? $cs['quantity'] : "Unlimited"; ?></td>
                                <td class="left"><?php echo isset($cs['per_user']) ? $cs['per_user'] : "Unlimited"; ?></td>
                                <td class="left"><?php echo isset($cs['pending']) && $cs['pending'] === 'on' ? "true" : "false"; ?></td>
                                <td class="right" style="word-wrap:break-word;"><?php echo (isset($cs['tags']) && $cs['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $cs['tags']).'</span>' : null); ?></td>
                                <td class="center">
                                    <?php
                                        echo anchor('custompoints/update/'.$cs['reward_id'], "<i class='fa fa-edit fa-lg''></i>",
                                                    array('class'=>'tooltips',
                                                        'title' => 'Edit',
                                                        'data-placement' => 'top'
                                                    ));
                                    ?>
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


<!-- Error Modal -->
<div id="errorModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:100000">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Warning !</h3>
    </div>
    <div class="modal-body red">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:100000">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Warning !</h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<!-- Import Modal -->
<div id="customPointImportModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="customPointImportLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="customPointImportLabel">Import Currency</h3>
    </div>
    <div class="modal-body">
        <br>
        &emsp;<span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>&emsp;:&emsp;
        <input id="file-import" type="file" size="100" value=""/>
        <br>&emsp;
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" onclick="custompoint_import();" type="button"><?php echo $this->lang->line('button_import'); ?></button>
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<style type="text/css">
    .modal {
        width: 40%;
        margin-left:-20%;
    }
</style>

<script type="text/javascript">

    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined) {
                $('#errorModal').find('#myModalLabel').html(title);
            }else{
                $('#errorModal').find('#myModalLabel').html("Warning !");
            }
            $('#errorModal').modal({'backdrop': true});
            $('#errorModal .modal-body').html(msg);
        }
    }

    var progressDialog = (function($){
        var obj = {};
        obj.show = function(text){

            $('body').prepend('<div class="custom_blackdrop"><img src="./image/white_loading.gif" /><br><span>'+text+'</span></div>');
        }

        obj.hide = function(){

            setTimeout(function(){
                $('.custom_blackdrop').remove();
            },1000)
        }

        return obj;
    }(jQuery))

    $("#successModal").on("hidden.bs.modal", function () {
        window.location.replace(baseUrlPath+'custompoints');
    });

    function custompoint_import() {
        var myfile = document.getElementById("file-import");

        if(myfile.files[0] != undefined){
            //
            //var textType = 'text/csv';

            if (myfile.value.match(/\.json/gi)==".json") {
                var file = myfile.files[0];
                var reader = new FileReader();
                reader.readAsText(file);
                reader.onload = (function (theFile) {
                    return function (e) {
                        try {
                            json = JSON.parse(e.target.result);
                            var array_custompoints =  JSON.stringify(json);
                            var import_status = false;

                            $.ajax({
                                url: baseUrlPath+'custompoints/importCustompoint',
                                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','array_custompoints': array_custompoints},
                                type:'POST',
                                // dataType:'json',
                                beforeSend:function(){
                                    $('#customPointImportModal').modal('hide');
                                    progressDialog.show('Importing Currency ...');
                                },
                                success:function(data){

                                    if($.parseJSON(data).status=='success'){

                                        $('#successModal .modal-body').html('Import Currency(s) successfully');
                                        $('#successModal').find('#myModalLabel').html("Success !".fontcolor( '12984C' ));


                                        import_status = true;


                                    }
                                    else if($.parseJSON(data).status=='fail') {
                                        var msg = "";
                                        for (var k in $.parseJSON(data).results){
                                            if ($.parseJSON(data).results.hasOwnProperty(k)) {
                                                msg += $.parseJSON(data).results[k] +"<br>";
                                            }
                                        }

                                        preventUnusual.message(msg,'Error to import!!! The following Currency(s) are already exist in this site');

                                    }else{
                                        preventUnusual.message($.parseJSON(data).msg);

                                    }

                                },
                                error:function(){
                                    //console.log('on error')
                                    dialogMsg = 'Unable to import Currency to server,\n Please try again later';

                                },
                                complete:function(){
                                    //console.log('on complete')
                                    progressDialog.hide();

                                    if(import_status){

                                        $('#successModal').modal({'backdrop': true});

                                    }
                                }
                            });
                        } catch (ex) {

                            preventUnusual.message('Error when trying to parse json : ' + ex+'<br><br><br>');
                        }
                    }
                })(file);
                reader.onerror = function() {
                    preventUnusual.message('Unable to read ' + file.fileName+'<br><br><br>');
                };


            } else {
                preventUnusual.message('File type is invalid! ( only JSON file is supported)<br><br><br>');
            }

        }else{
            preventUnusual.message('Please choose a file to execute!<br><br><br>');
        }
    }

    function custompoint_export() {
        var array_custompoints = new Array();
        $("input:checkbox[name=selected[]]:checked").each(function(){
            array_custompoints.push($(this).val());
        });
        if(array_custompoints.length == 0){
            preventUnusual.message('Please select at least 1 Currency to export!');
        }
        else{
            var export_status = false;

            $.ajax({
                url: baseUrlPath+'custompoints/exportCustompoint',
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','array_custompoints': array_custompoints},
                type:'POST',
                // dataType:'json',
                beforeSend:function(){
                    progressDialog.show('Exporting Currency(s) ...');
                },
                success:function(data){

                    if(($.parseJSON(data)).success==false) {
                        dialogMsg = ($.parseJSON(data)).msg;
                    }else {
                        if (($.parseJSON(data))) {
                            var output_data = "text/json;charset=utf-8," + encodeURIComponent(JSON.stringify($.parseJSON(data),null, 2));
                            link = document.createElement('a');
                            link.setAttribute('href', 'data:' + output_data);
                            link.setAttribute('download', 'custompoints.json');
                            link.click();
                            export_status = true;
                            dialogMsg = 'Export Currency(s) successfully';
                        }
                        else {
                            dialogMsg = 'Unable to export Currency(s) from server ';
                        }
                    }

                },
                error:function(){
                    //console.log('on error')
                    dialogMsg = 'Unable to export Currency(s) from server,\n Please try again later';

                },
                complete:function(){
                    //console.log('on complete')
                    progressDialog.hide();

                    if(export_status){
                        preventUnusual.message(dialogMsg.fontcolor( '010040' ), "Success !".fontcolor( '12984C' ));

                    }else{
                        preventUnusual.message(dialogMsg, "Error !!!");
                    }
                }
            });
        }
        return true;
    }

    function custompoint_delete() {
        var array_custompoints = new Array();
        $("input:checkbox[name=selected[]]:checked").each(function(){
            array_custompoints.push($(this).val());
        });
        if(array_custompoints.length == 0){
            preventUnusual.message('Please select at least 1 Currency to delete!');
        }
        else{
            $('#form').submit();
        }
        return true;
    }

</script>

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

<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/blackdrop/blackdrop.css" />

