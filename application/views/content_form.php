<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10 content-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'content'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php } ?>
            <?php
            if (validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">', '</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            $attributes = array('id' => 'form', 'class' => 'form-horizontal content-form');
            echo form_open($form, $attributes);
            ?>

            <div class="control-group">
                <label class="control-label"
                       for="inputTitle"><?php echo $this->lang->line('entry_title'); ?><span
                        class="required">&nbsp;*</span></label>
                <div class="controls">
                    <input type="text" name="title" size="100" id="inputTitle"
                           placeholder="<?php echo $this->lang->line('entry_title'); ?>"
                           value="<?php echo isset($title) ? $title : set_value('$title'); ?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputSummary">
                    <?php echo $this->lang->line('entry_summary'); ?><span
                        class="required">&nbsp;*</span>
                </label>
                <div class="controls">
                    <textarea name="summary" id="inputSummary" cols="80" rows="5" style="width: 70%;"
                              placeholder="<?php echo $this->lang->line('entry_summary'); ?>"><?php echo isset($summary) ? $summary : set_value('summary'); ?></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputDetail">
                    <?php echo $this->lang->line('entry_detail'); ?><span
                        class="required">&nbsp;*</span>
                </label>
                <div class="controls">
                    <textarea name="detail" id="inputDetail" cols="80" rows="20"
                              placeholder="<?php echo $this->lang->line('entry_detail'); ?>"><?php echo isset($detail) ? $detail : set_value('detail'); ?></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo $this->lang->line('entry_date_range'); ?><span
                        class="required">&nbsp;*</span>
                </label>
                <div class="controls">
                    <span>
                        <input type="text" class="date" name="date_start" id="date_start" size="50"
                               placeholder="<?php echo $this->lang->line('entry_date_start'); ?>"
                               value="<?php echo isset($date_start) && $date_start ? date('Y-m-d', strtotime(datetimeMongotoReadable($date_start))) : ''; ?>"/>
                    </span>
                    <span>&nbsp;-&nbsp;</span>
                    <span>
                        <input type="text" class="date" name="date_end" id="date_end" size="50"
                               placeholder="<?php echo $this->lang->line('entry_date_end'); ?>"
                               value="<?php echo isset($date_end) && $date_end ? date('Y-m-d', strtotime(datetimeMongotoReadable($date_end))) : ''; ?>"/>
                    </span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"
                       for="image"><?php echo $this->lang->line('entry_image'); ?>
                </label>
                <div class="controls">
                    <div class="image">
                        <img src="<?php echo $thumb; ?>" alt="" id="thumb"
                             onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                        <input type="hidden" name="image" value="<?php echo $image; ?>"
                               id="image"/>
                        <br/><a
                            onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                        <a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"
                       for="inputCategory"><?php echo $this->lang->line('entry_category'); ?></label>
                <div class="controls">
                    <span><input type='hidden' name="category" id="inputCategory" style="width:50%;"
                                 value="<?php echo isset($category) ? $category : set_value('category'); ?>"></span>

                </div>
            </div>

            <div class="control-group">
                <label class="control-label"
                       for="inputTitle"><?php echo $this->lang->line('entry_tags'); ?></label>
                <div class="controls">
                    <input class="tags" type="text" name="tags" size="100"
                           value="<?php echo isset($tags) ? implode($tags,',')  : set_value('tags'); ?>"/>
                </div>
            </div>

            <?php if($org_status){?>
                <div class="control-group">
                       <label class="control-label"
                              for="inputTitle"><?php echo $this->lang->line('entry_organization'); ?></label>
                    <?php for($i = 0;$i<count($organize_node);$i++){?>
                       <div class="controls">
                               <?php if(isset($organize_id)){?>
                                   <input type='hidden' name="organize_id[]"   id="<?php echo "organize_id".$i ?>"   style="width:220px;" value="<?php echo isset($organize_id[$i]) ? $organize_id[$i] : set_value('organize_id'); ?>">
                               <?php }?>

                               <input type='hidden' name="organize_type[]" id="<?php echo "organize_type".$i ?>" style="width:220px;" value="<?php echo isset($organize_type[$i]) ? $organize_type[$i] : set_value('organize_type'); ?>">
                               <input type='hidden' name="organize_node[]" id="<?php echo "organize_node".$i ?>" style="width:220px;" value="<?php echo isset($organize_node[$i]) ? $organize_node[$i] : set_value('organize_node'); ?>">
                               <input class="tags" type="text"   name="organize_role[]" id="<?php echo "organize_role".$i ?>" style="width:220px;" placeholder="Role" value="<?php echo isset($organize_role[$i]) ? $organize_role[$i] :  set_value('organize_role'); ?>" />
                               <br>
                       </div>
                    <?php }?>
                </div>
            <?php }?>

            <div class="control-group">
                <label class="control-label"
                       for="status"><?php echo $this->lang->line('entry_status'); ?></label>
                <div class="controls">
                    <input type="checkbox" name="status" id="status" data-handle-width="40" <?php echo isset($status) ? ( $status ? "checked" : '') : set_checkbox('status','',true); ?>>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"
                       for="inputPin"><?php echo $this->lang->line('entry_pin'); ?></label>
                <div class="controls">
                    <input type="text" name="pin" size="100" id="inputPin"
                           placeholder="<?php echo $this->lang->line('entry_pin'); ?>"
                           value="<?php echo isset($pin) ? $pin : set_value('$pin'); ?>"/>
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

    var $startDateTextBox = $('#date_start'),
        $endDateTextBox = $('#date_end'),
        $inputCategory = $('#inputCategory'),
        $formCategoryModal = $('#formCategoryModal'),
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
    function initComponents() {
        CKEDITOR.replace('inputDetail', {
            filebrowserImageBrowseUrl : 'mediamanager/dialog/'
        });

        $inputCategory.select2({
            allowClear: true,
            placeholder: "Select category",
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "content/category",
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
                    $.ajax(baseUrlPath + "content/category/" + id, {
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

        $("[name='status']").bootstrapSwitch();

        $startDateTextBox.datepicker({
            onClose: function (dateText, inst) {
                if ($endDateTextBox.val() != '') {
                    var testStartDate = $startDateTextBox.datepicker('getDate');
                    var testEndDate = $endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        $endDateTextBox.datepicker('setDate', testStartDate);
                }
                else {
                    $endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                $endDateTextBox.datepicker('option', 'minDate', $startDateTextBox.datepicker('getDate'));
            }
        });
        $endDateTextBox.datepicker({
            onClose: function (dateText, inst) {
                if ($startDateTextBox.val() != '') {
                    var testStartDate = $startDateTextBox.datepicker('getDate');
                    var testEndDate = $endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        $startDateTextBox.datepicker('setDate', testEndDate);
                }
                else {
                    $startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                $startDateTextBox.datepicker('option', 'maxDate', $endDateTextBox.datepicker('getDate'));
            }
        });

        initCategoryTable();
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
//                $categoryContentTable.bootstrapTable('refresh');
                var result = JSON.parse(data).rows;
                $categoryContentTable.bootstrapTable('append', result);
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
        $('#formCategoryModalLabel').html("Edit new Node");
        $formCategoryModal.find("#category-id").val(data._id);
        $formCategoryModal.find("#category-name").val(data.name);
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
        initComponents();

        preventEnterKeyDown();
    });

    Pace.on("done", function(){
        $(".cover").fadeOut(1000);
    });
</script>

<script type="text/javascript">

    var $nodeOrganizeSearch = new Array();
    //var $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html();

    function organizeFormatResult(organize) {
        return '<div class="row-fluid">' +
            '<div>' + organize.name;

    }
    function organizeFormatSelection(organize) {
        return organize.name;
    }

    function nodeFormatResult(node) {
        return '<div class="row-fluid">' +
            '<div>' + node.name;
    }

    function nodeFormatSelection(node) {
        return node.name;
    }

    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });

        <?php for($i = 0;$i<count($organize_node);$i++){?>

        $nodeOrganizeSearch[<?php echo $i ?>] = "";

        $("#<?php echo "organize_type".$i ?>").select2({
            placeholder: "Select Organization type",
            allowClear: false,
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "store_org/organize/",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {return {
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
                    $.ajax(baseUrlPath + "store_org/organize/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $("#<?php echo "organize_type".$i ?>")
                                .select2('enable', false)
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                        $nodeOrganizeSearch[<?php echo $i ?>] = id;
                    }).always(function () {
                        $("#<?php echo "organize_type".$i ?>")
                            .select2('enable', true)
                    });
                }else{
                    $("#<?php echo "organize_node".$i ?>")
                        .select2('enable', false);
                }
            },
            formatResult: organizeFormatResult,
            formatSelection: organizeFormatSelection,

        });

        $("#<?php echo "organize_node".$i ?>").select2({
            placeholder: "Select Node",
            //allowClear: true,
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "store_org/node/",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        search: term, // search term
                        organize: $nodeOrganizeSearch[<?php echo $i ?>]
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
                    $.ajax(baseUrlPath + "store_org/node/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $("#<?php echo "organize_node".$i ?>")
                                .select2('enable', false)
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                    }).always(function () {
                        $("#<?php echo "organize_node".$i ?>")
                            .select2('enable', true)
                    });
                }else{
                    $("#<?php echo "organize_node".$i ?>")
                        .select2('enable', false);
                }
            },
            formatResult: nodeFormatResult,
            formatSelection: nodeFormatSelection,
        });


        if(document.getElementById("<?php echo "organize_type".$i ?>").value==""){
            $("#<?php echo "organize_node".$i ?>")
                .select2('enable', false);
        }
        <?php }?>
    });

    <?php for($i = 0;$i<count($organize_node);$i++){?>
    $("#<?php echo "organize_type".$i ?>")
        .on("change", function (e) {
            var $nodeParent = $("#<?php echo "organize_node".$i ?>");

            if (e.val === "") {
                $nodeParent
                    .select2("val", "")
                    .select2("enable", false);
            }
            else {
                $.ajax(baseUrlPath + "store_org/organize/" + e.val, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $nodeParent
                                .select2("enable", false)
                                .select2("val", "")
                        }
                    })
                    .done(function (data) {
                        $nodeOrganizeSearch[<?php echo $i ?>] = data._id;
                        $nodeParent.select2("enable", true);
                    })
                    .always(function () {

                    });
            }
        });

    <?php }?>

</script>
