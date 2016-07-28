<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'badge'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }?>
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a><a href="#tab-data"><?php echo $this->lang->line('tab_data'); ?></a></div>
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
            $attributes = array('id' => 'form');
            echo form_open($form ,$attributes);
            ?>
                <div id="tab-general">
                        <table class="form">
                            <tr>
                                <td><span class="required">*</span> <?php echo $this->lang->line('entry_name'); ?>:</td>
                                <td><input type="text" name="name" size="100" value="<?php echo isset($name) ? $name :  set_value('name'); ?>" />
                            </td>
                            </tr>
                            <tr>    
                                <?php if(!$client_id && !$name){?>
                                    <td><span class="required">*</span> <?php echo $this->lang->line('entry_for_client'); ?>:</td>
                                    <td>
                                        <select name="admin_client_id">
                                            <?php if(isset($to_clients)){?>
                                            <option value = 'all_clients'>All Clients</option>
                                                <?php foreach($to_clients as $client){?>
                                                    <?php if(trim($client['company'])=="" || !isset($client['company'])){?>
                                                        <option value ="<?php echo $client['_id']?>"><?php echo $client['first_name']." ".$client['last_name'];?></option>
                                                    <?php }else{?>
                                                    <option value ="<?php echo $client['_id']?>"><?php echo $client['company'];?></option>
                                                    <?php }?>
                                                <?php }?>
                                            <?php }?>
                                        </select>
                                    </td>
                                <?php }?>
                            </tr>
                            <?php if(!$client_id){?>
                            <tr>
                                <td><?php echo $this->lang->line('entry_sponsor'); ?>:</td>
                                <td>
                                    <input type="checkbox" name="sponsor" value = 1 <?php echo ($sponsor)?'checked':'unchecked'?> class="tooltips" data-placement="right" title="Sponsor badge cannot be modified by clients"/>
                                </td>
                            </tr>
                            <?php }?>
                            <tr>
                                <td><?php echo $this->lang->line('entry_hint'); ?>:</td>
                                <td><textarea name="hint" cols="40" rows="5"><?php echo isset($hint) ? $hint : set_value('hint'); ?></textarea></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_description'); ?>:</td>
                                <td><textarea name="description" id="description"><?php echo isset($description) ? $description : set_value('description'); ?></textarea></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->lang->line('entry_tags'); ?>:</td>
                                <td>
                                    <input type="text" class="tags" name="tags" value="<?php echo !empty($tags) ? implode($tags,',') : set_value('tags'); ?>" size="5" class="tooltips" data-placement="right" title="Tag(s) input"/>
                                </td>
                            </tr>
                        </table>

                </div>
                <div id="tab-data">
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_category'); ?>:</td>
                            <td><input type='text' name="category" id="inputCategory" value="<?php echo isset($category) ? $category : set_value('category'); ?>" size="5" style="width: 220px; height: 30px" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_quantity'); ?>:</td>
                            <td><input type="text" name="quantity" value="<?php echo isset($quantity) ? $quantity : set_value('quantity'); ?>" size="5" class="tooltips" data-placement="right" title="if left blank it is unlimited"/></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_per_user'); ?>:</td>
                            <td><input type="text" name="per_user" value="<?php echo isset($per_user) ? $per_user : set_value('per_user'); ?>" size="5" class="tooltips" data-placement="right" title="Number of reward that a user can get, if left blank it is unlimited"/></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_stackable'); ?>:</td>
                            <td><select name="stackable">
                                <?php if ($stackable || set_value('stackable')==1) { ?>
                                <?php //if (set_value('stackable')==1) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_substract'); ?>:</td>
                            <td><select name="substract">
                                <?php if ($substract || set_value('substract')==1) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_sort_order'); ?>:</td>
                            <td><input type="text" name="sort_order" value="<?php echo isset($sort_order) ? $sort_order : set_value('sort_order'); ?>" size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                            <td><select name="status">
                                <?php if ($status) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_auto_notify'); ?>:</td>
                            <td><select name="auto_notify">
                                <?php if ($auto_notify) { ?>
                                    <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                        </tr>
                    </table>
                </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<div id="formCategoryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formCategoryModalLabel"
     aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formCategoryModalLabel">Category</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal category-form')); ?>
            <div class="row-fluid">
                <input type="hidden" name="category-id" id="category-id">

                <div class="control-group">
                    <label for="category-name"
                           class="control-label"><?php echo $this->lang->line('entry_category_name'); ?></label>

                    <div class="controls">
                        <input type="text" name="category-name" id="category-name"
                               placeholder="<?php echo $this->lang->line('entry_category_name'); ?>">
                    </div>
                </div>

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="category-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
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
<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">

<script type="text/javascript"><!--
    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
    CKEDITOR.replace('description', {
        filebrowserImageBrowseUrl: 'mediamanager/dialog/'
    });
    //--></script>
<script type="text/javascript"><!--
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

//--></script>
<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>

<script type="text/javascript">
    $inputCategory = $('#inputCategory');
    $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html();
    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });
    $inputCategory.select2({
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
                return {results: data.rows};
            },
            cache: true
        },
        initSelection: function (element, callback) {
            var id = $(element).val();
            if (id !== "") {
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
    function initCategoryTable() {
        $categoryContentTable.bootstrapTable({
            columns: [
                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle'
                }, {
                    title: 'Category Name',
                    field: 'name',
                    align: 'center',
                    valign: 'middle',
                    sortable: true
                }, {
                    field: 'operate',
                    title: 'Item Operate',
                    align: 'center',
                    events: operateEvents,
                    formatter: operateFormatter
                }
            ]
        });
        // sometimes footer render error.
        setTimeout(function () {
            $categoryContentTable.bootstrapTable('resetView');
        }, 200);
        $categoryContentTable.on('check.bs.table uncheck.bs.table ' +
            'check-all.bs.table uncheck-all.bs.table', function () {
            $categoryContentToolbarRemove.prop('disabled', !$categoryContentTable.bootstrapTable('getSelections').length);
            // save your data, here just save the current page
            categorySelections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });
        $categoryContentToolbarRemove.click(function (e) {
            e.preventDefault();
            var ids = getIdSelections();
            console.log("id selected", ids);
            $.ajax({
                type: "POST",
                url: baseUrlPath + 'badge/category/',
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','id': ids, 'action': "delete"}
            })
                .done(function (msg) {
                    //console.log("Entry removed: " + JSON.parse(msg).status);
                    $categoryContentTable.bootstrapTable('remove', {
                        field: '_id',
                        values: ids
                    });
                    $categoryContentTable.bootstrapTable('resetView');
                })
                .fail(function () {
                    console.log("Error!");
                });
            $categoryContentToolbarRemove.prop('disabled', true);
        });
    }
    function categoryFormatResult(category) {
        return '<div class="row-fluid">' +
            '<div>' + category.name +
            '</div></div>';
    }

    function categoryFormatSelection(category) {
        return category.name;
    }

    function getIdSelections() {
        return $.map($categoryContentTable.bootstrapTable('getSelections'), function (row) {
            return row._id;
        });
    }

    initCategoryTable();
</script>
