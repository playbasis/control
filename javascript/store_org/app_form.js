var $formOrganizeModal = $('#formOrganizeModal'),
    $waitDialog = $('#pleaseWaitDialog'),
    $savedDialog = $('#savedDialog'),
    $storeOrganizeTable = $('#storeOrganizeTable'),
    $storeOrganizeToolbarRemove = $('#storeOrganizeToolbar').find('#remove'),
    storeOrganizeSelections = [];

function init_input_general_tab() {
    // store tab
    $("[name='store-status']").bootstrapSwitch();
    $("#store-organize").select2({
        placeholder: "Search for a organize",
        allowClear: true,
        minimumInputLength: 0,
        id: function (data) {
            return data._id.$id;
        },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: baseUrlPath + "store_org/organize/",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    search: term, // search term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter the remote JSON data
                return {results: data.rows};
            },
            cache: true
        },
        formatResult: organizeFormatResult, // omitted for brevity, see the source of this page
        formatSelection: organizeFormatSelection,  // omitted for brevity, see the source of this page
    });

    // organize tab
    $("[name='store-organize-status']").bootstrapSwitch();
    $("#store-organize-parent").select2({
        placeholder: "Search for a organize parent",
        allowClear: true,
        minimumInputLength: 0,
        id: function (data) {
            return data._id.$id;
        },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: baseUrlPath + "store_org/organize/",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    search: term, // search term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter the remote JSON data
                return {results: data.rows};
            },
            cache: true
        },
        initSelection: function(element, callback) {
            // the input tag has a value attribute preloaded that points to a preselected repository's id
            // this function resolves that id attribute to an object that select2 can render
            // using its formatResult renderer - that way the repository name is shown preselected
            var id = $(element).val();
            if (id !== "") {
                $.ajax(baseUrlPath + "store_org/organize/" + id, {
                    dataType: "json"
                }).done(function(data) {
                    if(data.length > 0)
                        callback(data[0]);
                });
            }
        },
        formatResult: organizeFormatResult, // omitted for brevity, see the source of this page
        formatSelection: organizeFormatSelection,  // omitted for brevity, see the source of this page
    });
}

function resetOrganizeModalForm(){
    $('form.store-organize-form').trigger("reset");
    $("#store-organize-parent").select2('val',"");
}

function organizeFormatResult(organize) {
    return '<div class="row-fluid">' +
        '<div>' + organize.name +
        '<small class="text-muted">&nbsp;(' + organize.description +
        ')</small></div></div>';
}

function organizeFormatSelection(organize) {
    return organize.name;
}

function initStoreOrganizeTable() {
    $storeOrganizeTable.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                title: 'Organize Name',
                field: 'name',
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Status',
                field: 'status',
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
        $storeOrganizeTable.bootstrapTable('resetView');
    }, 200);
    $storeOrganizeTable.on('check.bs.table uncheck.bs.table ' +
        'check-all.bs.table uncheck-all.bs.table', function () {
        $storeOrganizeToolbarRemove.prop('disabled', !$storeOrganizeTable.bootstrapTable('getSelections').length);
        // save your data, here just save the current page
        storeOrganizeSelections = getIdSelections();
        // push or splice the selections if you want to save all data selections
    });
    //$storeOrganizeTable.on('expand-row.bs.table', function (e, index, row, $detail) {
    //    if (index % 2 == 1) {
    //        $detail.html('Loading from ajax request...');
    //        $.get('LICENSE', function (res) {
    //            $detail.html(res.replace(/\n/g, '<br>'));
    //        });
    //    }
    //});
    //$storeOrganizeTable.on('all.bs.table', function (e, name, args) {
    //    console.log(name, args);
    //});
    $storeOrganizeToolbarRemove.click(function () {
        var ids = getIdSelections();
        $storeOrganizeTable.bootstrapTable('remove', {
            field: 'id',
            values: ids
        });
        $storeOrganizeToolbarRemove.prop('disabled', true);
    });
    $(window).resize(function () {
        $storeOrganizeTable.bootstrapTable('resetView', {
            height: getHeight()
        });
    });
}
function getIdSelections() {
    return $.map($storeOrganizeTable.bootstrapTable('getSelections'), function (row) {
        return row.id
    });
}
function responseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row.id, storeOrganizeSelections) !== -1;
    });
    return res;
}
function detailFormatter(index, row) {
    var html = [];
    $.each(row, function (key, value) {
        html.push('<p><b>' + key + ':</b> ' + value + '</p>');
    });
    return html.join('');
}
function operateFormatter(value, row, index) {
    return [
        '<a class="edit-organize" title="Edit">',
        '<i class="fa fa-edit fa-2x"></i>',
        '</a>  ',
        '<a class="remove" href="javascript:void(0)" title="Remove">',
        '<i class="fa fa-remove fa-2x"></i>',
        '</a>'
    ].join('');
}
function editOrganizeModalForm(data) {
    $('#formOrganizeModalLabel').html("Edit new Organize");
    $formOrganizeModal.find("#store-organize-id").val(data._id.$id);
    $formOrganizeModal.find("#store-organize-name").val(data.name);
    $formOrganizeModal.find("#store-organize-desc").val(data.description);
    if (typeof data.parent != "undefined") {
        $("#store-organize-parent").select2('val', data.parent._id.$id);
    }

    if (data.status)
        $formOrganizeModal.find("#store-organize-status").prop('checked', true);
    else
        $formOrganizeModal.find("#store-organize-status").prop('checked', false);
}
window.operateEvents = {
    'click .edit-organize': function (e, value, row, index) {
        //console.log('You click edit action, row: ' + JSON.stringify(row));
        resetOrganizeModalForm();
        editOrganizeModalForm(row);
        $formOrganizeModal.modal('show');
    },
    'click .remove': function (e, value, row, index) {
        $storeOrganizeTable.bootstrapTable('remove', {
            field: 'id',
            values: [row.id]
        });
    }
};
function getHeight() {
    return $(window).height() - $('h1').outerHeight(true);
}

function submitOrganizeModalForm() {
    // todo: Add client validation here!
    var organizeId = $formOrganizeModal.find("#store-organize-id").val() || null;

    $.ajax({
            type: "POST",
            url: baseUrlPath + "store_org/organize/" + organizeId,
            data: $('form.store-organize-form').serialize(),
            beforeSend: function (xhr) {
                $formOrganizeModal.modal('hide');
                $waitDialog.modal();
            }
        })
        .done(function () {
            $waitDialog.modal('hide');
            $storeOrganizeTable.bootstrapTable('refresh');
            $savedDialog.modal();
        })
        .fail(function (xhr, textStatus, errorThrown) {
            alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
        })
        .always(function () {
            $('form.store-organize-form').trigger("reset");
            $waitDialog.modal('hide');
        });
}

$(function () {
    init_input_general_tab();
    initStoreOrganizeTable();
});

$('#page-render')
    .on('click', 'button#store-organize-modal-submit', submitOrganizeModalForm)
    .on('click','#addNewParentLink',function () {
        $('#mainTab').find('a[href="#storeOrganizeTabContent"]').tab('show');
    });

$("[data-toggle]").filter("[href='#formOrganizeModal'],[data-target='#formOrganizeModal']")
    .on('click', function (e) {
        //console.log($(this).hasClass('add-organize'));
        resetOrganizeModalForm();
        if($(this).hasClass('add-organize'))
            $('#formOrganizeModalLabel').html("Add new Organize");
    });