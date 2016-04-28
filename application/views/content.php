<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10 content-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" id="insert_button" onclick="location =  baseUrlPath+'content/insert'"
                        type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" id="delete_button" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('success')) { ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php } ?>

            <div class="tabbable">
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active">
                        <a href="#generalContentTab"
                           data-toggle="tab" onclick="content_tab(this);"><?php echo $this->lang->line('tab_content'); ?></a>
                    </li>
                    <li>
                        <a href="#categoryContentTab"
                           data-toggle="tab" onclick="categoty_tab(this);"><?php echo $this->lang->line('tab_category'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="generalContentTab">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="contents">
                                    <?php $attributes = array('id' => 'form'); ?>
                                    <?php echo form_open('content/delete', $attributes); ?>
                                    <table class="list">
                                        <thead>
                                        <tr>
                                            <td width="1" style="text-align: center;">
                                                <input type="checkbox"
                                                       onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
                                            </td>
                                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                                            <td class="right" style="width:80px;"><?php echo $this->lang->line('column_category'); ?></td>
                                            <td class="right"
                                                style="width:100px;"><?php echo $this->lang->line('column_date_range'); ?></td>
                                            <td class="right" style="width:60px;"><?php echo $this->lang->line('column_status'); ?></td>
                                            <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="filter">
                                            <td></td>
                                            <td><input title="name" type="text" name="filter_title" value="<?php echo isset($_GET['title']) ? $_GET['title'] : "" ?>" style="width:50%;"/></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="right">
                                                <a onclick="clear_filter();" class="button"
                                                   id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                                                <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                                            </td>
                                        </tr>

                                        <?php if (isset($contents) && $contents) { ?>
                                            <?php foreach ($contents as $content) { ?>
                                                <tr>
                                                    <td style="text-align: center;"><?php if (isset($content['selected'])) { ?>
                                                            <input type="checkbox" name="selected[]"
                                                                   value="<?php echo $content['_id']; ?>" checked="checked"/>
                                                        <?php } else { ?>
                                                            <input type="checkbox" name="selected[]"
                                                                   value="<?php echo $content['_id']; ?>"/>
                                                        <?php } ?></td>
                                                    <td class="right"><?php echo $content['title']; ?> <?php if (!empty($content['error'])) { ?>
                                                            <span class="red"><a herf="javascript:void(0)" class="error-icon"
                                                                                 title="<?php echo $content['error']; ?>"
                                                                                 data-toggle="tooltip"><i class="icon-warning-sign"></i></a>
                                                            </span><?php } ?></td>
                                                    <td class="right"><?php echo isset($content['category']['name']) ? $content['category']['name'] : ""; ?></td>
                                                    <td class="right"><?php echo dateMongotoReadable($content['date_start']); ?>&nbsp;-&nbsp;<?php echo dateMongotoReadable($content['date_end']); ?></td>
                                                    <td class="right"><?php echo isset($content['status']) ? ( $content['status'] ? "Enable" : "Disabled") : "N/A"; ?></td>
                                                    <td class="right">
                                                        <?php if ($push_feature_existed) { ?>
                                                            <span>[ <?php echo anchor('#confirmModal', 'Send push',
                                                                    array(
                                                                        'class' => 'open-confirmModal',
                                                                        'title' => 'Send push notification to all players',
                                                                        'data-toggle' => 'modal',
                                                                        'data-target' => '#confirmModal',
                                                                        'data-id' => $content['_id'],
                                                                    )); ?> ]</span>
                                                        <?php } ?>
                                                        <span>[ <?php echo anchor('content/update/' . $content['_id'], 'Edit'); ?> ]</span>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="6" class="center">
                                                    <?php echo $this->lang->line('text_empty_content'); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        </tbody>
                                    </table>
                                    <?php echo form_close(); ?>
                                </div>
                                <div class="pagination">
                                    <ul class='ul_rule_pagination_container'>
                                        <li class="page_index_number active"><a>Total Records:</a></li>
                                        <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                                        <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?>
                                                Pages)</a></li>
                                        <?php echo $pagination_links; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="categoryContentTab">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="categoryContentToolbar">
                                    <button id="remove" class="btn btn-danger" disabled>
                                        <i class="fa fa-remove"></i> Delete
                                    </button>
                                    <a href="#formCategoryModal" id="add" role="button"
                                       class="btn btn-info add-category" data-toggle="modal"><i class="fa fa-plus"></i>
                                        Add</a>
                                </div>
                                <table id="categoryContentTable"
                                       data-height="600"
                                       data-toolbar="#categoryContentToolbar"
                                       data-search="true"
                                       data-show-refresh="true"
                                       data-id-field="_id"
                                       data-pagination="true"
                                       data-side-pagination="server"
                                       data-url="<?php echo site_url(); ?>/content/category/"
                                       data-response-handler="categoryResponseHandler">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

    var $formCategoryModal = $('#formCategoryModal'),
        $waitDialog = $('#pleaseWaitDialog'),
        $savedDialog = $('#savedDialog'),
        $categoryContentTable = $('#categoryContentTable'),
        $categoryContentToolbarRemove = $('#categoryContentToolbar').find('#remove'),
        categorySelections = [],
        $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html();

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
                    url: baseUrlPath + 'content/category/',
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
                url: baseUrlPath + "content/category/" + categoryId,
                data: formData,
                beforeSend: function (xhr) {
                    $formCategoryModal.modal('hide');
                    $waitDialog.modal();
                }
            })
            .done(function (data) {
                $waitDialog.modal('hide');
                $categoryContentTable.bootstrapTable('refresh');
                //var result = JSON.parse(data).rows;
                //$categoryContentTable.bootstrapTable('append', result);
            })
            .fail(function (xhr, textStatus, errorThrown) {
                alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
            })
            .always(function () {
                $('form.category-form').trigger("reset");
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

    function content_tab(obj) {

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
                    url: baseUrlPath + 'content/category/' + row._id,
                    data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','action': "delete"}
                })
                .done(function (msg) {
//                    console.log("Entry removed: " + JSON.parse(msg).status);
                    $categoryContentTable.bootstrapTable('remove', {
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
            $('#mainTab').find('a[href="#categoryContentTab"]').tab('show');
            resetModalForm();
            $formCategoryModal.modal('show');
        });

</script>


<?php if ($push_feature_existed) { ?>
    <div class="modal hide fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true"
         aria-labelledby="confirmModalLabel">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="confirmModalLabel">Send push to players</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to send push notification for this content to all players?</p>
            <input type="hidden" name="contentId" id="contentId" value=""/>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
            <a href="#" class="btn btn-primary" id="confirmPush">Confirm</a>
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

    <div class="modal hide" id="sentDialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Push notification sent</h3>
        </div>
        <div class="modal-body">
            <div>
                <span><i class="fa fa-send"></i>&nbsp;Push notification has been sent!</span>&nbsp;<span id="devices_sent"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>
<?php } ?>
<script type="text/javascript"><!--
    function filter() {
        url = baseUrlPath + 'content';

        var filter_title = $('input[name=\'filter_title\']').attr('value');

        if (filter_title) {
            url += '?title=' + encodeURIComponent(filter_title);
        }

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['title'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'content');
    }

    <?php if ($push_feature_existed) { ?>
    var $waitDialog = $('#pleaseWaitDialog'),
        $confirmModalDialog = $('#confirmModal'),
        $sentDialog = $('#sentDialog');

    $(document)
        .on("click", ".open-confirmModal", function () {
            var contentId = $(this).data('id');
            $(".modal-body #contentId").val(contentId);
        })
        .on("click", "#confirmPush", function(){
            var contentId = $(".modal-body #contentId").val();
            var request = $.ajax({
                url: baseUrlPath + "content/push/" + contentId,
                type: "POST",
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
                dataType: "json",
                beforeSend: function (xhr) {
                    $confirmModalDialog.modal('hide');
                    $waitDialog.modal();
                }
            });

            request.done(function (data, textStatus, jqXHR ) {
                $waitDialog.modal('hide');
                var resp = JSON.parse(jqXHR.responseText);
                if (typeof resp !== "undefined")
                    if (resp.hasOwnProperty("devices"))
                        $sentDialog.find("#devices_sent").text("(" + resp.devices + " Devices)");
                $sentDialog.modal();
            });

            request.fail(function( jqXHR, textStatus ) {
                alert(JSON.parse(jqXHR.responseText).message + ' \n\nPlease contact Playbasis!');
            });

            request.always(function(){
                $waitDialog.modal('hide');
            });
        });
    <?php } ?>

    Pace.on("done", function(){
        $(".cover").fadeOut(1000);
    });
</script>