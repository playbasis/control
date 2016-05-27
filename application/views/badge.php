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
                <button class="btn btn-info" onclick="location = baseUrlPath+'badge/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
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
            <div class="tabbable">
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active">
                        <a href="#generalItemTab"
                           data-toggle="tab" onclick="item_tab(this);"><?php echo $this->lang->line('tab_item'); ?></a>
                    </li>
                    <li>
                        <a href="#categoryItemTab"
                           data-toggle="tab" onclick="categoty_tab(this);"><?php echo $this->lang->line('tab_category'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="generalItemTab">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="actions">
                                    <?php
                                    $attributes = array('id' => 'form');
                                    echo form_open('badge/delete',$attributes);
                                    ?>
                                        <table class="list">
                                            <thead>
                                            <tr>
                                                <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                                                <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                                                <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                                                <?php if(!$client_id){?>
                                                    <td class="left"><?php echo $this->lang->line('column_owner'); ?></td>
                                                <?php }?>
                                                <td class="right" style="width:100px;"><?php echo $this->lang->line('column_category'); ?></td>
                                                <td class="right" style="width:50px;"><?php echo $this->lang->line('column_peruser'); ?></td>
                                                <td class="right" style="width:50px;"><?php echo $this->lang->line('column_quantity'); ?></td>
                                                <td class="right" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                                                <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                                                <td class="right" style="width:100px;"><?php echo $this->lang->line('column_tags'); ?></td>
                                                <td class="right" style="width:150px;"><?php echo $this->lang->line('column_action'); ?></td>
                                            </tr>
                                            </thead>
                                            <tr class="filter">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="right"><input title="category" type="text" name="filter_category" value="<?php echo isset($_GET['filter_category']) ? $_GET['filter_category'] : "" ?>"/></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="right">
                                                    <a onclick="clear_filter();" class="button"
                                                       id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                                                    <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                                                </td>
                                            </tr>
                                            <tbody>
                                                <?php if (isset($badges) && $badges) { ?>
                                                <?php foreach ($badges as $badge) { ?>
                                                <tr <?php if (isset($badge["is_template"]) && $badge["is_template"]) {?> class="badge_template" <?php } ?>>
                                                    <td style="text-align: center;">
                                                    <?php if (!$client_id){?>
                                                        <?php if ($badge['selected']) { ?>
                                                            <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" checked="checked" />
                                                        <?php } else { ?>
                                                            <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
                                                        <?php } ?>
                                                    <?php }else{?>
                                                        <?php if(!(isset($badge['sponsor']) && $badge['sponsor'])){?>
                                                        <?php if ($badge['selected']) { ?>
                                                            <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" checked="checked" />
                                                        <?php } else { ?>
                                                            <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
                                                        <?php } ?>
                                                        <?php }?>
                                                    <?php }?>
                                                    </td>
                                                    <td class="left"><div class="image"><img src="<?php echo $badge['image']; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></div></td>
                                                    <td class="left"><?php echo $badge['name']; ?></td>
                                                    <?php if(!$client_id){?>
                                                        <td class="left"><?php echo ($badge['is_public'])?"Public":"Private"; ?></td>
                                                    <?php }?>
                                                    <td class="right"><?php echo (isset($badge['category']) && !empty($badge['category'])) ? $badge['category'] : ''; ?></td>
                                                    <td class="right"><?php echo (isset($badge['per_user']) && !is_null($badge['per_user'])) ? $badge['per_user'] : 'Unlimited'; ?></td>
                                                    <td class="right"><?php echo $badge['quantity']; ?></td>
                                                    <td class="left"><?php echo ($badge['status'])? "Enabled" : "Disabled"; ?></td>
                                                    <td class="right"><?php echo $badge['sort_order']; ?></td>
                                                    <td class="right"><?php echo (((isset($badge['tags'])) && $badge['tags'])? implode($badge['tags'],',') : null); ?></td>
                                                    <td class="right">
                                                        <?php if(!$client_id){?>
                                                            [ <?php echo anchor('badge/update/'.$badge['badge_id'], 'Edit'); ?> ]
                                                        <?php }else{?>
                                                            <?php if(!(isset($badge['sponsor']) && $badge['sponsor'])){?>
                                                                [ <?php echo anchor('badge/update/'.$badge['badge_id'], 'Edit'); ?> ]
                                                            <?php }?>
                                                        <?php }?>
                                                        <?php echo anchor('badge/increase_order/'.$badge['badge_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$badge['badge_id'], 'style'=>'text-decoration:none'));?>
                                                        <?php echo anchor('badge/decrease_order/'.$badge['badge_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$badge['badge_id'], 'style'=>'text-decoration:none' ));?>
                                                    </td>
                                                </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                            <tr>
                                                <td class="center" colspan="<?php echo !$client_id ? 11 : 10; ?>"><?php echo $this->lang->line('text_no_results'); ?></td>
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
                    <div class="tab-pane fade in" id="categoryItemTab">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="categoryItemToolbar">
                                    <button id="remove" class="btn btn-danger" disabled>
                                        <i class="fa fa-remove"></i> Delete
                                    </button>
                                    <a href="#formCategoryModal" id="add" role="button"
                                       class="btn btn-info add-category" data-toggle="modal"><i class="fa fa-plus"></i>
                                        Add</a>
                                </div>
                                <table id="categoryItemTable"
                                       data-height="600"
                                       data-toolbar="#categoryItemToolbar"
                                       data-search="true"
                                       data-show-refresh="true"
                                       data-id-field="_id"
                                       data-pagination="true"
                                       data-side-pagination="server"
                                       data-url="<?php echo site_url(); ?>/badge/category/"
                                       data-response-handler="categoryResponseHandler">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

<div class="modal hide" id="categoryErrorDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
        <h1>Error</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-times"></i>&nbsp;<span id="category_error_message"></span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="category-error-dialog-close">Close</button>
    </div>
</div>

<link id="base-style" rel="stylesheet" type="text/css"
      href="<?php echo base_url(); ?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css"/>
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-table.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>javascript/content/ckeditor/ckeditor.js"></script>
<script type="text/javascript">

    var $formCategoryModal = $('#formCategoryModal'),
        $waitDialog = $('#pleaseWaitDialog'),
        $savedDialog = $('#savedDialog'),
        $categoryItemTable = $('#categoryItemTable'),
        $categoryItemToolbarRemove = $('#categoryItemToolbar').find('#remove'),
        categorySelections = [],
        $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html(),
        $categoryErrorDialog = $('#categoryErrorDialog');

    <?php if (!isset($_GET['filter_category'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'badge');
    }
    function filter() {
        url = baseUrlPath + 'badge';

        var filter_category = $('input[name=\'filter_category\']').attr('value');

        if (filter_category) {
            url += '?filter_category=' + encodeURIComponent(filter_category);
        }

        location = url;
    }

    function initCategoryTable() {
        $categoryItemTable.bootstrapTable({
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
            $categoryItemTable.bootstrapTable('resetView');
        }, 200);
        $categoryItemTable.on('check.bs.table uncheck.bs.table ' +
            'check-all.bs.table uncheck-all.bs.table', function () {
            $categoryItemToolbarRemove.prop('disabled', !$categoryItemTable.bootstrapTable('getSelections').length);
            // save your data, here just save the current page
            categorySelections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });
        $categoryItemToolbarRemove.click(function (e) {
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
                    $categoryItemTable.bootstrapTable('remove', {
                        field: '_id',
                        values: ids
                    });
                    $categoryItemTable.bootstrapTable('resetView');
                })
                .fail(function () {
                    console.log("Error!");
                });
            $categoryItemToolbarRemove.prop('disabled', true);
        });
    }

    function categoryFormatSelection(category) {
        return category.name;
    }

    function getIdSelections() {
        return $.map($categoryItemTable.bootstrapTable('getSelections'), function (row) {
            return row._id;
        });
    }

    function categoryResponseHandler(res) {
        $.each(res.rows, function (i, row) {
            row.state = $.inArray(row._id, categorySelections) !== -1;
        });
        return res;
    }

    function operateFormatter(value, row, index) {
        return [
            '<a class="edit-category" title="Edit">',
            '<i class="fa fa-edit fa-2x"></i>',
            '</a>  ',
            '<a class="remove-category" href="javascript:void(0)" title="Remove">',
            '<i class="fa fa-remove fa-2x"></i>',
            '</a>'
        ].join('');
    }

    function resetModalForm() {
        var $categoryForm = $('form.category-form');
        $categoryForm.trigger("reset");
        $categoryForm.find('#category-id').val('');
    }

    function submitModalForm() {
        // todo: Add client validation here!
        var categoryId = $formCategoryModal.find("#category-id").val() || "",
            formData = $('form.category-form').serialize();

        $.ajax({
            type: "POST",
            url: baseUrlPath + "badge/category/" + categoryId,
            data: formData,
            beforeSend: function (xhr) {
                $formCategoryModal.modal('hide');
                $waitDialog.modal();
            }
        })
            .done(function (data) {
                $waitDialog.modal('hide');
                $categoryItemTable.bootstrapTable('refresh');
                //var result = JSON.parse(data).rows;
                //$categoryContentTable.bootstrapTable('append', result);
            })
            .fail(function (xhr, textStatus, errorThrown) {
                if(JSON.parse(xhr.responseText).status == "error") {
                    $('form.category-form').trigger("reset");
                    alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
                }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                    $waitDialog.modal('hide');
                    $categoryErrorDialog.find("#category_error_message").text("Category name is already exist!");
                    $categoryErrorDialog.modal();
                }
            })
            .always(function () {
                //$('form.category-form').trigger("reset");
                $waitDialog.modal('hide');
            });
    }

    function editModalForm(data) {
        resetModalForm();
        $('#formCategoryModalLabel').html("Edit category");
        $formCategoryModal.find("#category-id").val(data._id);
        $formCategoryModal.find("#category-name").val(data.name);
    }

    function categoty_tab(obj) {

        document.getElementById('insert_button').style.visibility = 'hidden';
        document.getElementById('delete_button').style.visibility = 'hidden';
    }

    function item_tab(obj) {

        document.getElementById('insert_button').style.visibility = 'visible';
        document.getElementById('delete_button').style.visibility = 'visible';
    }

    window.operateEvents = {
        'click .edit-category': function (e, value, row, index) {
            //console.log('You click edit action, row: ' + JSON.stringify(row));
            editModalForm(row);
            $formCategoryModal.modal('show');
        },
        'click .remove-category': function (e, value, row, index) {
            //console.log("REMOVE NODE");
            $.ajax({
                type: "POST",
                url: baseUrlPath + 'badge/category/' + row._id,
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','action': "delete"}
            })
                .done(function (msg) {
//                    console.log("Entry removed: " + JSON.parse(msg).status);
                    $categoryItemTable.bootstrapTable('remove', {
                        field: '_id',
                        values: [row._id]
                    });
                })
                .fail(function () {
                    console.log("Error!");
                });
        }
    };

    function preventEnterKeyDown() {
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    }
    $(function () {
        initCategoryTable();

        preventEnterKeyDown();
    });

    $("[data-toggle]")
        .filter("[href='#formCategoryModal'],[data-target='#formCategoryModal']")
        .on('click', function (e) {
            resetModalForm();
            if ($(this).hasClass('add-category'))
                $('#formCategoryModalLabel').html("Add new category");
        });

    $('#page-render')
        .on('click', 'button#category-modal-submit', submitModalForm)
        .on('click', '#addNewCategoryLink', function () {
            $('#mainTab').find('a[href="#categoryItemTab"]').tab('show');
            resetModalForm();
            $formCategoryModal.modal('show');
        })
        .on('click', 'button#category-error-dialog-close', function () {
            $categoryErrorDialog.modal('hide');
            $formCategoryModal.modal('show');
        });
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

