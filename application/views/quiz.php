<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location =  baseUrlPath+'quiz/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#quizImportModal').modal({'backdrop': true});$('#file-import').val('');" type="button"><?php echo $this->lang->line('button_import'); ?></button>
                <button class="btn btn-info" onclick="quiz_export();" type="button"><?php echo $this->lang->line('button_export'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div><!-- .heading -->
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="quizs">
                <?php $attributes = array('id'=>'form');?>
                <?php echo form_open('quiz/delete', $attributes);?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="right" style="min-width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:50px;"><?php echo $this->lang->line('column_weight'); ?></td>
                        <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
                        <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_date_start'); ?></td>
                        <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_date_end'); ?></td>
                        <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_date_added'); ?></td>
                        <td class="right" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:80%;" /></td>
                        <td>
                            <select name="filter_status" style="width:95%">
                                <?php if (isset($_GET['filter_status']) && $_GET['filter_status'] == 'enable') { ?>
                                    <option value="">All</option>
                                    <option value="enable" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="disable" ><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } elseif (isset($_GET['filter_status']) && $_GET['filter_status'] == 'disable') { ?>
                                    <option value="">All</option>
                                    <option value="enable"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="disable" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                    <option value="" selected="selected">All</option>
                                    <option value="enable"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="disable"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select name="sort_order" style="width:95%">
                                <?php if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'asc') { ?>
                                    <option value="" disabled>Sort</option>
                                    <option value="asc" selected="selected"><?php echo $this->lang->line('asc'); ?></option>
                                    <option value="desc" ><?php echo $this->lang->line('desc'); ?></option>
                                <?php } elseif (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') { ?>
                                    <option value="" disabled>Sort</option>
                                    <option value="asc"><?php echo $this->lang->line('asc'); ?></option>
                                    <option value="desc" selected="selected"><?php echo $this->lang->line('desc'); ?></option>
                                <?php } else { ?>
                                    <option value="" disabled selected="selected">Sort</option>
                                    <option value="asc"><?php echo $this->lang->line('asc'); ?></option>
                                    <option value="desc"><?php echo $this->lang->line('desc'); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="text" name="filter_tags" value="" style="width:80%;" /></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
<!--                            -->
                        </td>
                    </tr>

                    <?php if(isset($quizs) && $quizs){?>
                        <?php foreach($quizs as $quiz){?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($quiz['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $quiz['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $quiz['_id']; ?>" />
                                    <?php } ?></td>
                                <td class="right"><?php echo $quiz['name']; ?> <?php if (!empty($quiz['error'])) { ?><span class="red"><a herf="javascript:void(0)" class="error-icon" title="<?php echo $quiz['error']; ?>" data-toggle="tooltip"><i class="icon-warning-sign" ></i></a></span><?php } ?></td>
                                <td class="right"><?php echo ($quiz['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo ($quiz['weight'])? $quiz['weight'] : "1"; ?></td>
                                <td class="right" style="word-wrap:break-word;"><?php echo (isset($quiz['tags']) && $quiz['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $quiz['tags']).'</span>' : null); ?></td>
                                <td class="right"><?php echo datetimeMongotoReadable($quiz['date_start']) ? datetimeMongotoReadable($quiz['date_start']) : ""; ?></td>
                                <td class="right"><?php echo datetimeMongotoReadable($quiz['date_expire']) ? datetimeMongotoReadable($quiz['date_expire']) : ""; ?></td>
                                <td class="right"><?php echo datetimeMongotoReadable($quiz['date_added']); ?></td>
                                <td class="right">
                                    <?php
                                        echo anchor('quiz/edit/'.$quiz['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                            array('class'=>'tooltips',
                                                'title' => 'Edit',
                                                'data-placement' => 'top'
                                            ));
                                    ?>
                                </td>

                            </tr>
                        <?php }
                            }else{
                        ?>
                    <tr>
                        <td class="center" colspan="9">
                        No quiz
                        </td>
                    </tr>
                    <?php }?>

                    </tbody>
                </table>
                <?php echo form_close();?>
            </div>
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
<div id="quizImportModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="quizImportLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="quizImportLabel">Import Quiz</h3>
    </div>
    <div class="modal-body">
        <br>
        &emsp;<span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>&emsp;:&emsp;
        <input id="file-import" type="file" size="100" value=""/>
        <br>&emsp;
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" onclick="quiz_import();" type="button"><?php echo $this->lang->line('button_import'); ?></button>
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
        window.location.replace(baseUrlPath+'quiz');
    });

    function quiz_import() {
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
                            var array_quizs =  JSON.stringify(json);
                            var import_status = false;

                            $.ajax({
                                url: baseUrlPath+'quiz/ImportQuiz',
                                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','array_quizs': array_quizs},
                                type:'POST',
                                // dataType:'json',
                                beforeSend:function(){
                                    $('#quizImportModal').modal('hide');
                                    progressDialog.show('Importing quiz ...');
                                },
                                success:function(data){

                                    if($.parseJSON(data).status=='success'){

                                            $('#successModal .modal-body').html('Import quiz(s) successfully');
                                            $('#successModal').find('#myModalLabel').html("Success !".fontcolor( '12984C' ));


                                        import_status = true;


                                    }
                                    else if($.parseJSON(data).status=='fail') {
                                        var msg = "";
                                        for (var k in $.parseJSON(data).results){
                                            if ($.parseJSON(data).results.hasOwnProperty(k)) {
                                                msg += k.fontcolor( '570420' ).bold() + " : " + $.parseJSON(data).results[k] +"<br>";
                                            }
                                        }

                                        preventUnusual.message(msg,'Error to import, The following item(s) are not found in this site.');

                                    }else{
                                        preventUnusual.message($.parseJSON(data).msg);

                                    }

                                },
                                error:function(){
                                    //console.log('on error')
                                    dialogMsg = 'Unable to import quiz to server,\n Please try again later';

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

    function quiz_export() {
        var array_quizs = new Array();
        $("input:checkbox[name=selected[]]:checked").each(function(){
            array_quizs.push($(this).val());
        });
        if(array_quizs.length == 0){
            preventUnusual.message('Please select at least 1 quiz to export!');
        }
        else{
            var export_status = false;

            $.ajax({
                url: baseUrlPath+'quiz/exportQuiz',
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','array_quizs': array_quizs},
                type:'POST',
                // dataType:'json',
                beforeSend:function(){
                    progressDialog.show('Exporting quiz(s) ...');
                },
                success:function(data){

                    if(($.parseJSON(data)).success==false) {
                        dialogMsg = ($.parseJSON(data)).msg;
                    }else {
                        if (($.parseJSON(data))) {
                            var output_data = "text/json;charset=utf-8," + encodeURIComponent(JSON.stringify($.parseJSON(data),null, 2));
                            link = document.createElement('a');
                            link.setAttribute('href', 'data:' + output_data);
                            link.setAttribute('download', 'quiz.json');
                            link.click();
                            export_status = true;
                            dialogMsg = 'Export quiz(s) successfully';
                        }
                        else {
                            dialogMsg = 'Unable to export quiz(s) from server ';
                        }
                    }

                },
                error:function(){
                    //console.log('on error')
                    dialogMsg = 'Unable to export quiz(s) from server,\n Please try again later';

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

</script>


<script type="text/javascript"><!--
    function filter() {
        url = baseUrlPath+'quiz?';

        var filter_name = $('input[name=\'filter_name\']').attr('value');
        var filter_status = $('select[name=\'filter_status\']').attr('value');
        var filter_tags = $('input[name=\'filter_tags\']').attr('value');
        var sort_order = $('select[name=\'sort_order\']').attr('value');
        if (filter_name) {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
        if (filter_status) {
            url += '&filter_status=' + encodeURIComponent(filter_status);
        }
        if (sort_order) {
            url += '&sort_order=' + encodeURIComponent(sort_order);
        }
        if (filter_tags) {
            url += '&filter_tags=' + encodeURIComponent(filter_tags);
        }

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        window.location.replace(baseUrlPath+'quiz');
    }
</script>

<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/blackdrop/blackdrop.css" />