<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location =  baseUrlPath+'quest/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#questImportModal').modal({'backdrop': true});$('#file-import').val('');" type="button"><?php echo $this->lang->line('button_import'); ?></button>
                <button class="btn btn-info" onclick="quest_export();" type="button"><?php echo $this->lang->line('button_export'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>

            </div>
        </div><!-- .heading -->
        <div class="content">
        <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="actions">
        <?php $attributes = array('id'=>'form');?>
        <?php echo form_open('quest/delete', $attributes);?>
            <table id="questTable" class="list">
                <thead>
                    <tr>
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_quest_name'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_quest_status'); ?></td>
                    <?php if($org_status){?>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_organization'); ?></td>
                    <?php }?>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_quest_tags'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_quest_sort_order'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <?php if($org_status){?>
                        <td></td>
                        <?php }?>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>
                    
                    <?php if(isset($quests) && $quests){?>
                        <?php foreach($quests as $quest){?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($quest['selected'])) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $quest['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $quest['_id']; ?>" />
                                    <?php } ?></td>
                                <td class="left"><img src="<?php echo $quest['image']; ?>" alt="" id="quest_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></td>
                                <td class="right"><?php echo $quest['quest_name']; ?> <?php if (!empty($quest['error'])) { ?><span class="red"><a herf="javascript:void(0)" class="error-icon" title="<?php echo $quest['error']; ?>" data-toggle="tooltip"><i class="icon-warning-sign" ></i></a></span><?php } ?></td>
                                <td class="right"><?php echo ($quest['status'])?'Active':'Inactive';?></td>
                                <?php if($org_status){?>
                                    <td class="right"><?php echo (isset($quest['organize_name']) && !is_null($quest['organize_name']))?$quest['organize_name']:''; ?></td>
                                <?php }?>
                                <td class="right"><?php echo (isset($quest['tags']) && $quest['tags']) ? implode(',',$quest['tags']) : null; ?></td>
                                <td class="right"><?php echo $quest['sort_order'];?></td>
                                <td class="right">

                                    <a class="quest_play" href="#" title="Play" data-quest_id="<?php echo $quest["_id"]; ?>"><i class='fa fa-play fa-lg'></i></a>
                                    <?php if($client_id){
                                        // echo anchor('quest/update/'.$quest['action_id'], 'Edit');
                                        echo anchor('quest/edit/'.$quest['_id'], "<i class='fa fa-edit fa-lg'></i>",
                                            array('class'=>'tooltips',
                                                'title' => 'Edit',
                                                'data-placement' => 'top'
                                            ));
                                    }else{
                                        echo anchor('action/edit/'.$quest['_id'], "<i class='fa fa-edit fa-lg'></i>",
                                            array('class'=>'tooltips',
                                                'title' => 'Edit',
                                                'data-placement' => 'top'
                                            ));
                                    }
                                    ?>

                                    <?php if($client_id){
                                        // echo anchor('action/increase_order/'.$quest['action_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$quest['action_id'], 'style'=>'text-decoration:none'));
                                        echo anchor('action/increase_order/'.$quest['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                    }else{
                                        echo anchor('action/increase_order/'.$quest['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                    }
                                    ?>
                                    <?php if($client_id){
                                        // echo anchor('action/decrease_order/'.$quest['action_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$quest['action_id'], 'style'=>'text-decoration:none'));
                                        echo anchor('action/decrease_order/'.$quest['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                    }else{
                                        echo anchor('action/decrease_order/'.$quest['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$quest['_id'], 'style'=>'text-decoration:none'));
                                    }
                                    ?>
                                    </td>
                            </tr>
                        <?php }?>
                    <?php }else{?>
                            <tr>
                                <td class="center" colspan="8">
                                    No quest
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
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

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
<div id="questImportModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="questImportLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="questImportLabel">Import Quest</h3>
    </div>
    <div class="modal-body">
        <br>
        &emsp;<span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>&emsp;:&emsp;
        <input id="file-import" type="file" size="100" value=""/>
        <br>&emsp;
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" onclick="quest_import();" type="button"><?php echo $this->lang->line('button_import'); ?></button>
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
    window.location.replace(baseUrlPath+'quest');
});

function quest_import() {
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
                        var array_quests =  JSON.stringify(json);
                        var import_status = false;

                        $.ajax({
                            url: baseUrlPath+'quest/ImportQuest',
                            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','array_quests': array_quests},
                            type:'POST',
                            // dataType:'json',
                            beforeSend:function(){
                                $('#questImportModal').modal('hide');
                                progressDialog.show('Importing quest(s) ...');
                            },
                            success:function(data){

                                if($.parseJSON(data).status=='success'){
                                    if($.parseJSON(data).results!=null) {
                                        //var msg = "Organization name below are not found in this site<br>".fontcolor( '570420' ).bold();
                                        var msg = "";
                                        for (var k in $.parseJSON(data).results){
                                            if ($.parseJSON(data).results.hasOwnProperty(k)) {
                                                msg += "Organization name \'"+($.parseJSON(data).results[k]).bold() + "\' is not found \(Quest : " + k.bold() +"\)<br>";
                                            }
                                        }
                                        $('#successModal .modal-body').html(msg.fontcolor( 'D53A3A' ));
                                        $('#successModal').find('#myModalLabel').html("Success with some warning below !".fontcolor( '12984C' ));

                                    }else{
                                        $('#successModal .modal-body').html('Import quest(s) successfully');
                                        $('#successModal').find('#myModalLabel').html("Success !".fontcolor( '12984C' ));

                                    }
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
                                dialogMsg = 'Unable to import quest(s) to server,\n Please try again later';

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

function quest_export() {
    var array_quests = new Array();
    $("input:checkbox[name=selected[]]:checked").each(function(){
        array_quests.push($(this).val());
    });
    if(array_quests.length == 0){
        preventUnusual.message('Please select at least 1 quest to export!');
    }
    else{
        var export_status = false;

        $.ajax({
            url: baseUrlPath+'quest/exportQuest',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','array_quests': array_quests},
            type:'POST',
            // dataType:'json',
            beforeSend:function(){
                progressDialog.show('Exporting quest(s) ...');
            },
            success:function(data){

                if(($.parseJSON(data)).success==false) {
                    dialogMsg = ($.parseJSON(data)).msg;
                }else {
                    if (($.parseJSON(data))) {
                        var output_data = "text/json;charset=utf-8," + encodeURIComponent(JSON.stringify($.parseJSON(data),null, 2));
                        link = document.createElement('a');
                        link.setAttribute('href', 'data:' + output_data);
                        link.setAttribute('download', 'quests.json');
                        link.click();
                        export_status = true;
                        dialogMsg = 'Export quest(s) successfully';
                    }
                    else {
                        dialogMsg = 'Unable to export(s) quest from server ';
                    }
                }

            },
            error:function(){
                //console.log('on error')
                dialogMsg = 'Unable to export quest(s) from server,\n Please try again later';

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
    url = baseUrlPath+'quest';

    var filter_name = $('input[name=\'filter_name\']').attr('value');

    if (filter_name) {
        url += '?filter_name=' + encodeURIComponent(filter_name);
    }

    location = url;
}
//--></script>

<script type="text/javascript">
    $('input[name=\'filter_name\']').live("focus", function (event) {
        $(this).autocomplete({
            delay: 0,
            source: function(request, response) {
                $.ajax({
                    url: baseUrlPath+'quest/autocomplete?filter_name=' +  encodeURIComponent(request.term),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            return {
                                label: item.name,
                                name: item.name
                            }
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('input[name=\'filter_name\']').val(ui.item.name);

                return false;
            },
            focus: function(event, ui) {
                return false;
            }
        });
    });
</script>


<script type = "text/javascript">

$( ".push_down" ).live( "click", function() {
  
    $.ajax({
        url: baseUrlPath+'quest/increase_order/'+$(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        var getListForAjax = 'quest/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;
});

</script>

<script type = "text/javascript">

$( ".push_up" ).live( "click", function() {
  
    $.ajax({
        url: baseUrlPath+'quest/decrease_order/'+$(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        var getListForAjax = 'quest/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;
});

// play quest
$(".quest_play").click(function() {
    var id = $(this).data("quest_id");
    var that = $(this);
    $.ajax({
        url: baseUrlPath + "quest/playQuest/" + id,
            type:'GET',
            beforeSend: function() {
                $(".icon-ok").remove();
                $(".icon-remove").remove();
                that.parent().prepend("<div class='small progress spinner'><div>Loading...</div></div>");
            },
            success:function(data){
                $(".spinner").remove();
                var j = JSON.parse(data);
                if (j["success"]) {
                    that.parent().prepend("<i class='icon-ok' style='font-size: 3em'></i>");
                } else {
                    that.parent().prepend("<i class='icon-remove' style='font-size: 3em'></i>");
                }
            }
    });
});

</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
        $("#clear_filter").hide();
    <?php }else{?>
        $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        window.location.replace(baseUrlPath+'quest');
    }
</script>

<script>
$(document).ready(function() {
	$('.error-icon').tooltip();
});
</script>

<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/blackdrop/blackdrop.css" />