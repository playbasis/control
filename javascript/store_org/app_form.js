var $formOrganizeModal = $('#formOrganizeModal'),
    $formNodeModal = $('#formNodeModal'),
    $waitDialog = $('#pleaseWaitDialog'),
    $savedDialog = $('#savedDialog'),
    $storeOrganizeTable = $('#storeOrganizeTable'),
    $storeOrganizeToolbarRemove = $('#storeOrganizeToolbar').find('#remove'),
    storeOrganizeSelections = [],
    $storeNodeTable = $('#storeNodeTable'),
    $storeNodeToolbarRemove = $('#storeNodeToolbar').find('#remove'),
    storeNodeSelections = [];

function initNodeTabInputs() {
    $("[name='node-status']").bootstrapSwitch();
    $("#node-organize").select2({
        placeholder: "Search for a organize",
        allowClear: true,
        minimumInputLength: 0,
        id: function (data) {
            return data._id.$id;
        },
        ajax: {
            url: baseUrlPath + "store_org/organize/",
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
                $.ajax(baseUrlPath + "store_org/organize/" + id, {
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $waitDialog.modal('show');
                    }
                }).done(function (data) {
                    $waitDialog.modal('hide');
                    if (data.length > 0)
                        callback(data[0]);
                });
            }
        },
        formatResult: organizeFormatResult,
        formatSelection: organizeFormatSelection,
    });
    $("#node-parent").select2({
        placeholder: "Search for a parent",
        allowClear: true,
        minimumInputLength: 0,
        id: function (data) {
            return data._id.$id;
        },
        ajax: {
            url: baseUrlPath + "store_org/node/",
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
                $.ajax(baseUrlPath + "store_org/node/" + id, {
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $waitDialog.modal('show');
                    }
                }).done(function (data) {
                    $waitDialog.modal('hide');
                    if (data.length > 0)
                        callback(data[0]);
                });
            }
        },
        formatResult: nodeFormatResult,
        formatSelection: nodeFormatSelection,
    });
}
function initOrganizeTabInputs() {
    $("[name='store-organize-status']").bootstrapSwitch();
    $("#store-organize-parent").select2({
        placeholder: "Search for a organize parent",
        allowClear: true,
        minimumInputLength: 0,
        id: function (data) {
            return data._id.$id;
        },
        ajax: {
            url: baseUrlPath + "store_org/organize/",
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
                $.ajax(baseUrlPath + "store_org/organize/" + id, {
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $waitDialog.modal('show');
                    }
                }).done(function (data) {
                    $waitDialog.modal('hide');
                    if (data.length > 0)
                        callback(data[0]);
                });
            }
        },
        formatResult: organizeFormatResult,
        formatSelection: organizeFormatSelection,
    });
}
function initPageInputs() {
    initNodeTabInputs();
    initOrganizeTabInputs();
}

function resetOrganizeModalForm() {
    var $storeOrganizeForm = $('form.store-organize-form');
    $storeOrganizeForm.trigger("reset");
    $storeOrganizeForm.find('store-organize-id').val('');
    $("#store-organize-parent").select2('val', "");
}

function resetNodeModalForm() {
    var $nodeForm = $('form.node-form');
    $nodeForm.trigger("reset");
    $nodeForm.find('node-id').val('');
    $("#node-organize").select2('val', "");
    $("#node-parent").select2('val', "");
}

function nodeFormatResult(node) {
    return '<div class="row-fluid">' +
        '<div>' + node.name +
        '<small class="text-muted">&nbsp;(' + node.description +
        ')</small></div></div>';
}

function nodeFormatSelection(node) {
    return node.name;
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

function initStoreNodeTable() {
    $storeNodeTable.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                title: 'Node Name',
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
                formatter: operateNodeFormatter
            }
        ]
    });
    // sometimes footer render error.
    setTimeout(function () {
        $storeNodeTable.bootstrapTable('resetView');
    }, 200);
    $storeNodeTable.on('check.bs.table uncheck.bs.table ' +
        'check-all.bs.table uncheck-all.bs.table', function () {
        $storeOrganizeToolbarRemove.prop('disabled', !$storeOrganizeTable.bootstrapTable('getSelections').length);
        // save your data, here just save the current page
        storeOrganizeSelections = getIdSelections();
        // push or splice the selections if you want to save all data selections
    });
    $storeNodeToolbarRemove.click(function () {
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
                formatter: operateOrganizeFormatter
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
function organizeResponseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row.id, storeOrganizeSelections) !== -1;
    });
    return res;
}
function nodeResponseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row.id, storeNodeSelections) !== -1;
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
function operateOrganizeFormatter(value, row, index) {
    return [
        '<a class="edit-organize" title="Edit">',
        '<i class="fa fa-edit fa-2x"></i>',
        '</a>  ',
        '<a class="remove" href="javascript:void(0)" title="Remove">',
        '<i class="fa fa-remove fa-2x"></i>',
        '</a>'
    ].join('');
}
function operateNodeFormatter(value, row, index) {
    return [
        '<a class="edit-node" title="Edit">',
        '<i class="fa fa-edit fa-2x"></i>',
        '</a>  ',
        '<a class="remove" href="javascript:void(0)" title="Remove">',
        '<i class="fa fa-remove fa-2x"></i>',
        '</a>'
    ].join('');
}
function editOrganizeModalForm(data) {
    resetOrganizeModalForm();
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
function editNodeModalForm(data) {
    resetNodeModalForm();
    $('#formNodeModalLabel').html("Edit new Node");
    $formNodeModal.find("#node-id").val(data._id.$id);
    $formNodeModal.find("#node-name").val(data.name);
    $formNodeModal.find("#node-desc").val(data.description);
    if (typeof data.store_id != "undefined") {
        $formNodeModal.find("#node-store-id-control-group").removeClass('hide');
        $formNodeModal.find("#node-store-id").val(data.store_id);
    } else {
        $formNodeModal.find("#node-store-id-control-group").addClass('hide');
    }
    if (typeof data.organize != "undefined") {
        $("#node-organize").select2('val', data.organize._id.$id);
    }
    if (typeof data.parent != "undefined") {
        $("#node-parent").select2('val', data.parent._id.$id);
    }

    if (data.status)
        $formOrganizeModal.find("#store-organize-status").prop('checked', true);
    else
        $formOrganizeModal.find("#store-organize-status").prop('checked', false);
}
window.operateEvents = {
    'click .edit-node': function (e, value, row, index) {
        //console.log('You click edit action, row: ' + JSON.stringify(row));
        editNodeModalForm(row);
        $formNodeModal.modal('show');
    },
    'click .edit-organize': function (e, value, row, index) {
        //console.log('You click edit action, row: ' + JSON.stringify(row));
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
    var organizeId = $formOrganizeModal.find("#store-organize-id").val() || "";

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

function submitNodeModalForm() {
    // todo: Add client validation here!
    var nodeId = $formNodeModal.find("#node-id").val() || "";

    $.ajax({
            type: "POST",
            url: baseUrlPath + "store_org/node/" + nodeId,
            data: $('form.node-form').serialize(),
            beforeSend: function (xhr) {
                $formNodeModal.modal('hide');
                $waitDialog.modal();
            }
        })
        .done(function () {
            $waitDialog.modal('hide');
            $storeNodeTable.bootstrapTable('refresh');
            $savedDialog.modal();
        })
        .fail(function (xhr, textStatus, errorThrown) {
            alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
        })
        .always(function () {
            $('form.node-form').trigger("reset");
            $waitDialog.modal('hide');
        });
}

$(function () {
    initPageInputs();
    initStoreOrganizeTable();
    initStoreNodeTable();
});

$('#page-render')
    .on('click', 'button#node-modal-submit', submitNodeModalForm)
    .on('click', 'button#store-organize-modal-submit', submitOrganizeModalForm)
    .on('click', '#addNewParentLink', function () {
        $('#mainTab').find('a[href="#storeOrganizeTabContent"]').tab('show');
        $formNodeModal.modal('hide');
        $formOrganizeModal.modal('show');
    });
//.on('click', $("[data-toggle]").filter("[href='#formOrganizeModal'],[data-target='#formOrganizeModal']"), function(){
//    resetOrganizeModalForm();
//    if($(this).hasClass('add-organize'))
//        $('#formOrganizeModalLabel').html("Add new Organize");
//})
//.on('click', $("[data-toggle]").filter("[href='#formNodeModal'],[data-target='#formNodeModal']"), function(){
//    resetNodeModalForm();
//    if($(this).hasClass('add-node'))
//        $('#formOrganizeModalLabel').html("Add new Node");
//});

$("[data-toggle]")
    .filter("[href='#formOrganizeModal'],[data-target='#formOrganizeModal']")
    .on('click', function (e) {
        //console.log($(this).hasClass('add-organize'));
        resetOrganizeModalForm();
        if ($(this).hasClass('add-organize'))
            $('#formOrganizeModalLabel').html("Add new Organize");
    });
$("[data-toggle]")
    .filter("[href='#formNodeModal'],[data-target='#formNodeModal']")
    .on('click', function (e) {
        //console.log($(this).hasClass('add-organize'));
        resetNodeModalForm();
        if ($(this).hasClass('add-node'))
            $('#formNodeModalLabel').html("Add new Node");
    });