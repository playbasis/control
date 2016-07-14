<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-primary btn-lg" onclick="game_validation();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-primary btn-lg" onclick="location = baseUrlPath+'game'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>

        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('game');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_farm'); ?></a>
            </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php
            if(validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            $attributes = array('id' => 'form' , 'class' => 'form-horizontal game-form');
            echo form_open_multipart($form ,$attributes);
            ?>
            <div id="actions">
                <table class="form">
                    <input type="hidden" name="name" size="100" value="Farm">
                    <tr>
                        <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                        <td> <img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                            <input type="hidden" name="image" value="<?php echo $image; ?>" id="image"/>
                                <br/>
                                <a onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                        <td><input type="checkbox" name="status" id="status"" checked></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_template'); ?>:</td>
                        <td><input type="text" name="template" size="100" value="">&nbsp;
                            <a href="javascript:void(0)" class="btn btn-primary template-setting-btn btn-lg">Template Setting</a>
                        </td>
                        <td></td>
                    </tr>
                </table>
                <div id="table-world">
                    <div class="world-head-wrapper text-center">
                        <a href="javascript:void(0)" class="btn open-world-btn btn-lg">Open All</a>
                        <a href="javascript:void(0)" class="btn close-world-btn btn-lg">Close All</a>
                        <a href="javascript:void(0)" class="btn btn-primary add-world-btn btn-lg">+ New World</a>
                    </div><br>
                    <div class="world-wrapper">

                    </div><!-- .world-wrapper -->
                </div>
            <?php echo form_close();?>
            </div><!-- #actions -->
        </div>
    </div>
</div>

<div class="modal hide" id="savedDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h1>Data Saved</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-save"></i>&nbsp;<span>Data has been saved!</span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1>Please Wait</h1>
    </div>
    <div class="modal-body">
        <div class="offset5 ">
            <i class="fa fa-spinner fa-spin fa-5x"></i>
        </div>
    </div>
</div>

<div id="pleaseWaitSpanDiv" class="hide">
    <span id="pleaseWaitSpan"><i class="fa fa-spinner fa-spin"></i></span>
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

<div id="formItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formItemModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formItemModalLabel">Item</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal item-form')); ?>
            <div class="row-fluid">
                <input type="hidden" name="item_world_id" id="item_world_id">
                <input type="hidden" name="item_row" id="item_row">
                <input type="hidden" name="item_column" id="item_column">
                <div class="control-group">
                    <label for="item_id" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_item_name'); ?></label>
                    <div class="controls">
                        <input type="text" name="item_id" id="item_id" placeholder="<?php echo $this->lang->line('entry_item_name'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="item_harvest" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_item_harvest'); ?></label>
                    <div class="controls">
                        <input type="number" name="item_harvest" id="item_harvest" min="1" placeholder="<?php echo $this->lang->line('entry_item_harvest'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="item_deduct" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_item_deduct'); ?></label>
                    <div class="controls">
                        <input type="number" name="item_deduct" id="item_deduct" min="1" placeholder="<?php echo $this->lang->line('entry_item_deduct'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="item-desc" class="control-label"><?php echo $this->lang->line('entry_item_description'); ?></label>
                    <div class="controls">
                        <textarea name="item-desc" id="item-desc" rows="5" placeholder="<?php echo $this->lang->line('entry_item_description') ?>"></textarea>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="item-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
    </div>
</div>


<div id="formTemplateModal" class="modal hide fade"  tabindex="-1" role="dialog" aria-labelledby="formTemplateModalLabel" aria-hidden="true" style="width: 700px">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formTemplateModalLabel">Template</h3>
    </div>
    <div class="modal-body" id="formTemplateModal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal template-form')); ?>
            <div class="row-fluid">
                <div class="template-wrapper span12">
                    <div class="template-nav well span3" style="height: 300px">
                        <ul class="nav nav-tabs nav-stacked" id="template_nav"></ul>
                    </div>
                    <div class="span9 well" id ="template_body" style="height: 300px">
                        <div class="control-group">
                            <label for="template_name" class="control-label span3"><span class="required">*</span><?php echo $this->lang->line('entry_template_name'); ?></label>
                            <div class="controls span6">
                                <input type="text" name="template_name" id="template_name" placeholder="<?php echo $this->lang->line('entry_template_name'); ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="template_weight" class="control-label span3"><span class="required">*</span><?php echo $this->lang->line('entry_template_weight'); ?></label>
                            <div class="controls span6">
                                <input type="number" name="template_weight" id="template_weight" min="1" placeholder="<?php echo $this->lang->line('entry_template_weight'); ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="template_start" class="control-label span3"><span class="required">*</span><?php echo $this->lang->line('entry_template_start'); ?></label>
                            <div class="controls span6">
                                <input type="date" name="template_start" id="template_start" min="1" placeholder="<?php echo $this->lang->line('entry_template_start'); ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="template_end" class="control-label span3"><span class="required">*</span><?php echo $this->lang->line('entry_template_end'); ?></label>
                            <div class="controls span6">
                                <input type="date" name="template_end" id="template_end" min="1" placeholder="<?php echo $this->lang->line('entry_template_end'); ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="template_status" class="control-label span3"><?php echo $this->lang->line('entry_template_status'); ?></label>
                            <div class="controls span6">
                                <input type="checkbox" name="template_status" id="template_status" >
                            </div>
                        </div>
                    </div>
                </div><!-- .world-wrapper -->
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="template-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
    </div>
</div>


<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript" ></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<script>

    $(function(){
        $('#status').bootstrapSwitch();
        $("#status").bootstrapSwitch('size', 'small');
        $("#status").bootstrapSwitch('onColor', 'success');
        $("#status").bootstrapSwitch('offColor', 'danger');
        $("#status").bootstrapSwitch('handleWidth', '70');
        $("#status").bootstrapSwitch('labelWidth', '10');
        $("#status").bootstrapSwitch('onText', 'Enable');
        $("#status").bootstrapSwitch('offText', 'Disable');
        $('#template_status').bootstrapSwitch();
        $("#template_status").bootstrapSwitch('size', 'small');
        $("#template_status").bootstrapSwitch('onColor', 'success');
        $("#template_status").bootstrapSwitch('offColor', 'danger');
        $("#template_status").bootstrapSwitch('handleWidth', '70');
        $("#template_status").bootstrapSwitch('labelWidth', '10');
        $("#template_status").bootstrapSwitch('onText', 'Enable');
        $("#template_status").bootstrapSwitch('offText', 'Disable');
    });

    var countWorldId = 0,
        templateData = [],
        $waitDialog = $('#pleaseWaitDialog'),
        $savedDialog = $('#savedDialog'),
        $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html();

    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined) {
                $('#errorModal').find('#myModalLabel').html(title);
            }else{
                $('#errorModal').find('#myModalLabel').html("Warning !");
            }
            $('#errorModal').removeClass('hide');
            $('#errorModal').removeClass('in');
            $('#errorModal').modal();
            $('#errorModal .modal-body').html(msg);
        }
    }

    function game_validation(){
        var dialogMsg = "",
            check_level = false;

        for (var i=1; i<= countWorldId; i++) {
            var $world_name = $('#worlds_'+ i +'_name').val(),
                $world_level = $('#worlds_'+ i +'_level').val(),
                $world_width = $('#worlds_'+ i +'_world_width').val(),
                $world_height = $('#worlds_'+ i +'_world_height').val();

            if ($world_name == "") dialogMsg += '- Name is require, Please select name of world '+ i +'<br>';
            for (var j=i+1; j<= countWorldId; j++){
                var $world_name_temp = $('#worlds_'+ j +'_name').val(),
                    $world_level_temp = $('#worlds_'+ j +'_level').val();
                if ($world_name == $world_name_temp) dialogMsg += '- Name is required unique value, Name of world '+ i + ' is same as world ' + j + '<br>';
                if ($world_level == $world_level_temp) dialogMsg += '- Level is required unique value, Level of world '+ i + ' is same as world ' + j + '<br>';
            }
            if ($world_level == 1) check_level = true;
            if ($world_level < 0) dialogMsg += '- Level is require at least 1<br>';
            if ($world_width < 0) dialogMsg += '- Width is require at least 1<br>';
            if ($world_height < 0) dialogMsg += '- Height is require at least 1<br>';
        }

        if(!check_level) dialogMsg += '- Level 1 is require<br>';
        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
            var formData = $('form.game-form').serialize();
            $.ajax({
                type: "POST",
                url: baseUrlPath + "game/edit/",
                data: formData,
                beforeSend: function (xhr) {
                    $waitDialog.modal();
                }
            }).done(function (data) {
                $waitDialog.modal('hide');
            }).fail(function (xhr, textStatus, errorThrown) {
                if(JSON.parse(xhr.responseText).status == "error") {
                    $('form.game-form').trigger("reset");
                    alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
                }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                    $waitDialog.modal('hide');

                }
            }).always(function () {
                $waitDialog.modal('hide');
            });
        }
    }

    function image_upload(field, thumb) {
        var $mm_Modal = $('#mmModal');

        if ($mm_Modal.length !== 0) $mm_Modal.remove();

        var frameSrc = baseUrlPath + "mediamanager/dialog?field=" + encodeURIComponent(field);
        var mm_modal_str = "";
        mm_modal_str += "<div id=\"mmModal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\">";
        mm_modal_str += " <div class=\"modal-body\">";
        mm_modal_str += "   <iframe src=\"" + frameSrc + "\" style=\"position:absolute; zoom:0.60\" width=\"99.6%\" height=\"99.6%\" frameborder=\"0\"><\/iframe>";
        mm_modal_str += " <\/div>";
        mm_modal_str += "<\/div>";

        $mm_Modal = $(mm_modal_str);
        $('#page-render').append($mm_Modal);

        $mm_Modal.modal('show');

        $mm_Modal.on('hidden', function () {
            var $field = $(field);
            if ($field.attr('value')) {
                $.ajax({
                    url: baseUrlPath + 'mediamanager/image?image=' + encodeURIComponent($field.val()),
                    dataType: 'text',
                    success: function (data) {
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                    }
                });
            }
        });
    }

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });

    //======================== World ========================
    $('.world-item-wrapper').each(function () {
        countWorldId++;
    })

    $('.open-world-btn').click(function () {
        $('.world-item-wrapper>.box-content').show();
    })
    $('.close-world-btn').click(function () {
        $('.world-item-wrapper>.box-content').hide();
    })

    $('.add-world-btn').click(function () {
        countWorldId++;
        var itemWorldId = countWorldId;
        var itemWorldHtml = '<div class="world-item-wrapper" data-world-id="'+ itemWorldId +'">\
                                <div class="box-header box-world-header overflow-visible" style="height: 30px;">\
                                    <h2><img src="<?php echo base_url();?>image/default-image.png" width="30"> World</h2>\
                                    <div class="box-icon">\
                                        <a href="javascript:void(0)" class="btn btn-danger right remove-world-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
                                        <span class="break"></span>\
                                        <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                                    </div>\
                                </div>\
                                <div class="box-content clearfix">\
                                    <div class="row-fluid">\
                                        <div class="span12 well" style="min-height:500px">\
                                            <div class="span6">\
                                            <table class="form">\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_name"); ?>:</td>\
                                                    <td><input type="text" name="worlds['+itemWorldId+'][world_name]" id="worlds_'+itemWorldId+'_name" size="100" value=""></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_level"); ?>:</td>\
                                                    <td><input type="number" name="worlds['+itemWorldId+'][world_level]" id="worlds_'+itemWorldId+'_level" size="100" min="1" value="1"></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_image"); ?>:</td>\
                                                    <td> <img src="<?php echo $thumb; ?>" alt="" id="world_'+itemWorldId+'_thumb" onerror="$(this).attr("src","<?php echo base_url(); ?>image/default-image.png");"/>\
                                                        <input type="hidden" name="worlds['+itemWorldId+'][world_image]" value="<?php echo $image; ?>" id="world_'+itemWorldId+'_image"/>\
                                                        <br/>\
                                                        <a onclick="image_upload(\'#world_'+itemWorldId+'_image\', \'world_'+itemWorldId+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
                                                        <a onclick="$(\'world_'+itemWorldId+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'world_'+itemWorldId+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a>\
                                                    </td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line('entry_category'); ?>:</td>\
                                                    <td><input type="text" name="worlds['+itemWorldId+'][world_category]" id="worlds_'+itemWorldId+'_category" size="100" value=""></td>\
                                                    <td></td>\
                                                </tr>\
                                            </table>\
                                            </div>\
                                            <div class="span6">\
                                            <table class="form">\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_width"); ?>:</td>\
                                                    <td><input type="number" name=worlds['+itemWorldId+'][world_width] id="worlds_'+itemWorldId+'_world_width" size="100" min="1" value="1" onchange="add_thumbnail('+itemWorldId+')"></td>\
                                                    <td><input type="hidden" id="worlds_'+itemWorldId+'_world_width_temp" size="100" value="0"></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_height"); ?>:</td>\
                                                    <td><input type="number" name=worlds['+itemWorldId+'][world_height] id="worlds_'+itemWorldId+'_world_height" size="100" min="1" value="1" onchange="add_thumbnail('+itemWorldId+')"></td>\
                                                    <td><input type="hidden" id="worlds_'+itemWorldId+'_world_height_temp" size="100" value="0"></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_description"); ?>:</td>\
                                                    <td><textarea name="worlds['+itemWorldId+'][world_description]" rows="4"></textarea></td>\
                                                    <td></td>\
                                                </tr>\
                                            </table>\
                                            </div>\
                                            <div class="span12">Item:</div>\
                                            <div class="well" id="worlds_'+itemWorldId+'_thumbnails_grids" style="overflow-y:scroll; overflow-x:scroll; height:500px; width:98%;"></div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>';

        $('.world-item-wrapper>.box-content').slideUp();
        $('.world-wrapper').append(itemWorldHtml);
        init_world_event(itemWorldId);
        add_thumbnail(itemWorldId);
    });

    $('.template-setting-btn').click(function () {

        $.ajax({
            url : baseUrlPath+ "badge/category",
            dataType: "json"
        }).done(function(data) {
            $('#template_nav').empty();
            for(var i=0; i< data.total; i++){
                if(data.rows[i] != undefined){
                    var templateHtml = '<li id="tab-'+i+'" ><a onclick="set_template('+i+')" data-toggle="tab" data-interest="'+i+'">'+data.rows[i].name+'</a></li>';
                    $('#template_nav').append(templateHtml);
                }
            }
            templateData = data.rows;
            if(!data.total){
                var templateHtml = '<li id="tab-0" ><a onclick="set_template(0);" data-toggle="tab" data-interest="'+0+'">Default</a></li>';
                $('#template_nav').append(templateHtml);
            } else {
                document.getElementById('template_name').value =  templateData[0].name;
            }
            var templateHtml = '<li id="tab-end" ><a onclick="set_template(99)" data-toggle="tab" data-interest="'+99+'">+Template</a></li>';
            $('#template_nav').append(templateHtml);
            $('#tab-0').addClass("active");

        });
        $('#formTemplateModal').modal('show');
    });

    function init_world_event(id) {

        $('.world-item-wrapper .box-world-header').unbind().bind('click', function (data) {
            var $target = $(this).next('.box-content');

            if ($target.is(':visible')) $('i', $(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i', $(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.remove-world-btn').unbind().bind('click', function (data) {
            var $target = $(this).parent().parent().parent();

            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
                init_world_event(id);
            }
        });


        $inputCategory = $('#worlds_'+id+'_category');
        $inputCategory.select2({
            width: '220px',
            allowClear: true,
            placeholder: "Select category",
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "badge/category",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        search: term, // search term
                    };
                },
                results: function (data, page) {
                    for(var i=1; i <= countWorldId; i++){
                        for(var j=0; j< data.total; j++){
                            if(data.rows[j] != undefined){
                                if($('#worlds_'+i+'_category').val() == data.rows[j]._id){
                                    data.rows.splice(j, 1);
                                    data.total--;
                                }
                            }
                        }
                    }
                    return {results: data.rows};
                },
                cache: true
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== ""){
                    $.ajax(baseUrlPath + "badge/category/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $inputCategory.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                    }).always(function () {
                        $inputCategory.parent().parent().parent().find("#pleaseWaitSpan").remove();
                    });
                }
            },
            formatResult: categoryFormatResult,
            formatSelection: categoryFormatSelection,
        });
    }

    function add_thumbnail(world_id){
        var $world_widths = $('#worlds_'+ world_id +'_world_width'),
            $world_widths_temp = $('#worlds_'+ world_id +'_world_width_temp'),
            $world_heights = $('#worlds_'+ world_id +'_world_height'),
            $world_heights_temp = $('#worlds_'+ world_id +'_world_height_temp'),
            $thumbnails_grids = $('#worlds_'+ world_id +'_thumbnails_grids');

        if($world_heights.val() > $world_heights_temp.val() || $world_widths.val() > $world_widths_temp.val()){
            for(var i =0;i<$world_heights.val();i++){
                var myElemi = document.getElementById('thumbnails_grid_'+ world_id +'_'+ i);
                if (myElemi === null) $thumbnails_grids.append('<ul id="thumbnails_grid_'+ world_id +'_'+ i +'" class="thumbnails">');
                for(var j=0;j<$world_widths.val();j++){
                    var myElemj = document.getElementById('thumbnails_grid_'+ world_id +'_' + i + '_' + j);
                    if (myElemj === null) $('#thumbnails_grid_'+ world_id +'_'+ i).append('<li id="thumbnails_grid_'+ world_id +'_' + i + '_' + j + '">\
                                                    <div class="thumbnail tooltips" data-placement="top" title="['+i+']['+j+']" style="width:120px;height:120px;">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_id] id="worlds_'+world_id+'_item_id_' + i + '_' + j + '" size="100" value="">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_harvest] id="worlds_'+world_id+'_item_harvest_' + i + '_' + j + '" size="100" value="1">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_deduct] id="worlds_'+world_id+'_item_deduct_' + i + '_' + j + '" size="100" value="1">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_description] id="worlds_'+world_id+'_item_description_' + i + '_' + j + '" size="100" value="">\
                                                        <i class="fa fa-plus-circle fa-5x fa-align-center" onclick="showItemModalForm('+world_id+','+i+','+j+')" class="tooltips" data-placement="top" title="['+i+']['+j+']" id="add" style="padding: 30px" aria-hidden="true"></i>\
                                                    </div>\
                                                 </li>');
                }
                if (myElemi === null) $thumbnails_grids.append('</ul>');
            }
            if($world_heights.val() > $world_heights_temp.val()) document.getElementById('worlds_'+world_id+'_world_height_temp').value = $world_heights.val();
            if($world_widths.val() > $world_widths_temp.val()) document.getElementById('worlds_'+world_id+'_world_width_temp').value = $world_widths.val();
        } else {
            for(var i =0; i<$world_heights_temp.val(); i++){
                for(var j=0; j<$world_widths_temp.val(); j++){
                    if(i > ($world_heights.val()-1) || j > ($world_widths.val()-1)){
                        $('#thumbnails_grid_'+ world_id +'_' + i +'_'+ j).remove();
                    }
                }
            }
            if($world_heights.val() < $world_heights_temp.val()) document.getElementById('worlds_'+world_id+'_world_height_temp').value = $world_heights.val();
            if($world_widths.val() < $world_widths_temp.val()) document.getElementById('worlds_'+world_id+'_world_width_temp').value = $world_widths.val();
        }
    }

    $('#page-render').on('click', 'button#template-modal-submit', submitTemplateModalForm);
    $('#page-render').on('click', 'button#item-modal-submit', submitItemModalForm);

    function set_template(index) {
        var previous_tab_id = $("#template_nav .active").attr('id');
        if((index != 99) && (templateData.length != 0)){
            document.getElementById('template_name').value =  templateData[index].name? templateData[index].name : "";
            document.getElementById('template_weight').value = templateData[index].weight ? templateData[index].weight : "";
            document.getElementById('template_start').value = templateData[index].start ? templateData[index].start: "";
            document.getElementById('template_end').value = templateData[index].end ? templateData[index].end : "";
            var check = templateData[index].status ? templateData[index].status : false;
            $('#template_status').attr('checked', check);
            $("#template_status").bootstrapSwitch('state', check);
        }
        else{
            document.getElementById('template_name').value = "";
            document.getElementById('template_weight').value = "";
            document.getElementById('template_start').value = "";
            document.getElementById('template_end').value = "";
            $('#template_status').attr('checked', false);
            $("#template_status").bootstrapSwitch('state', false);
        }

    }

    function submitTemplateModalForm() {
        var template_id = $("#template_nav .active").attr('id'),
            template_name = $('#template_name').val(),
            template_weight = $('#template_weight').val(),
            template_start = $('#template_start').val(),
            template_end = $('#template_end').val();

        var dialogMsg = "";

        if(template_id == "tab-end"){
            for(var i=0; i< templateData.length; i++){
                if(template_name == templateData[i].name) dialogMsg += '- Template Name is required uniuqe name<br>';
            }
        }

        if (template_name == "") dialogMsg += '- Template Name is required, Please set template name<br>';
        if (template_weight < 0) dialogMsg += '- Level is required at least 1<br>';
        if (template_start > template_end) dialogMsg += '- Date start should be less than date end<br>';

        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
           /* var formData = $('form.game-form').serialize();
            $.ajax({
                type: "POST",
                url: baseUrlPath + "game/edit/",
                data: formData,
                beforeSend: function (xhr) {
                    $waitDialog.modal();
                }
            }).done(function (data) {
                $waitDialog.modal('hide');
            }).fail(function (xhr, textStatus, errorThrown) {
                if(JSON.parse(xhr.responseText).status == "error") {
                    $('form.game-form').trigger("reset");
                    alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
                }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                    $waitDialog.modal('hide');

                }
            }).always(function () {
                $waitDialog.modal('hide');
            });*/
        }

    }

    function submitItemModalForm() {
        var world_id   = $('#item_world_id').val(),
            row    = $('#item_row').val(),
            column    = $('#item_column').val(),
            item_id = $('#item_id').val(),
            item_harvest = $('#item_harvest').val(),
            item_deduct  = $('#item_deduct').val(),
            dialogMsg = "";

        if(item_id == "") dialogMsg += '- Please select Item <br>';
        if(item_harvest < 1) dialogMsg += '- Days to harvest is require at least 1 day <br>';
        if(item_deduct < 1) dialogMsg += '- Days to die is require at least 1 day <br>';
        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
            $('#formItemModal').modal('hide');

            document.getElementById('worlds_'+ world_id +'_item_id_' + row + '_' + column).value =  item_id;
            document.getElementById('worlds_'+ world_id +'_item_harvest_' + row + '_' + column).value =  item_harvest;
            document.getElementById('worlds_'+ world_id +'_item_deduct_' + row + '_' + column).value =  item_deduct;
            document.getElementById('worlds_'+ world_id +'_item_description_' + row + '_' + column).value =  $('#item-desc').getText();

            document.getElementById('item_id').value = "";
            document.getElementById('item_harvest').value = "";
            document.getElementById('item_deduct').value = "";
            document.getElementById('item-desc').value = "";
        }
    }

    function showItemModalForm(world_id,row,column) {
        $('#formItemModal').modal('show');
        var $world_category = $('#worlds_'+world_id+'_category');
        var $world_width = $('#worlds_'+ world_id +'_world_width');
        var $world_height = $('#worlds_'+ world_id +'_world_height');
        document.getElementById('worlds_'+world_id+'_world_width').disabled = false;
        document.getElementById('worlds_'+world_id+'_world_height').disabled = false;
        document.getElementById('item_world_id').value = world_id;
        document.getElementById('item_row').value = row;
        document.getElementById('item_column').value = column;
        document.getElementById('item_id').value = $('#worlds_'+ world_id +'_item_id_'+ row +'_' + column).val();
        document.getElementById('item_harvest').value = $('#worlds_'+ world_id +'_item_harvest_'+ row +'_' + column).val();
        document.getElementById('item_deduct').value  = $('#worlds_'+ world_id +'_item_deduct_' + row +'_' + column).val();
        if ($('#worlds_'+ world_id +'_item_desc_'+ row +'_' + column).val() != null){
            document.getElementById('item-desc').value = $('#worlds_'+ world_id +'_item_desc_'+ row +'_' + column).val();
        }

        if ($world_category.val() != null){
            $inputCategory = $('#item_id');
            $inputCategory.select2({
                width: '220px',
                allowClear: true,
                placeholder: "Select Item",
                minimumInputLength: 0,
                id: function (data) {
                    return data._id;
                },
                ajax: {
                    url: baseUrlPath + "badge/items?filter_category=" + $world_category.val(),
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (term, page) {
                        return {
                            search: term, // search term
                        };
                    },
                    results: function (data, page) {
                        for(var i=0; i < Number($world_height.val()); i++){
                            for(var j=0; j< Number($world_width.val()); j++){
                                for(var k=0; k< data.total; k++){
                                    if(data.rows[k] != undefined){
                                        if($('#worlds_'+world_id+'_item_id_' + i + '_' + j).val() == data.rows[k]._id){
                                            data.rows.splice(k, 1);
                                            data.total--;
                                        }
                                    }
                                }
                            }
                        }
                        return {results: data.rows};
                    },
                    cache: true
                },
                initSelection: function (element, callback) {
                    var id = $(element).val();
                    if (id !== ""){
                        $.ajax(baseUrlPath + "badge/items/" + id, {
                            dataType: "json",
                            beforeSend: function (xhr) {
                                $inputCategory.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                            }
                        }).done(function (data) {
                            if (typeof data != "undefined")
                                callback(data);
                        }).always(function () {
                            $inputCategory.parent().parent().parent().find("#pleaseWaitSpan").remove();
                        });
                    }
                },
                formatResult: categoryFormatResult,
                formatSelection: categoryFormatSelection,
            });
        }
        else{
            alert("please select category");
            $('#formItemModal').modal('hide');
        }
    }

    function categoryFormatResult(category) {
        return '<div class="row-fluid">' +
                    '<div>' + category.name + '</div>'
               '</div>';
    }

    function categoryFormatSelection(category) {
        return category.name;
    }

</script>