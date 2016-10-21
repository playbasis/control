var $formModal = $('#formModal'),
    $waitDialog = $('#pleaseWaitDialog'),
    $savedDialog = $('#savedDialog'),
    $errorDialog = $('#errorDialog'),
    $storeTable = $('#storeTable'),
    $storeToolbarRemove = $('#storeToolbar').find('#remove'),
    storeSelections = []

function resetModalForm() {
    var $styleForm = $('form.style-form');
    $styleForm.trigger("reset");
    $styleForm.find('#style-id').val('');
}

function formatSelection(node) {
    return node.name;
}

function initStoreTable() {
    $storeTable.bootstrapTable({
        columns: [
            {
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                title: 'Group Name',
                field: 'name',
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Key',
                field: 'key',
                align: 'center',
                valign: 'middle',
                sortable: true
            },{
                title: 'Value',
                field: 'value',
                align: 'center',
                valign: 'middle'
            }, {
                field: 'operate',
                title: 'Item Operate',
                align: 'center',
                events: operateEvents,
                formatter: operateItemFormatter
            }
        ]
    });
    // sometimes footer render error.
    setTimeout(function () {
        $storeTable.bootstrapTable('resetView');
    }, 200);
    $storeTable.on('check.bs.table uncheck.bs.table ' +
        'check-all.bs.table uncheck-all.bs.table', function () {
        $storeToolbarRemove.prop('disabled', !$storeTable.bootstrapTable('getSelections').length);
        // save your data, here just save the current page
        storeSelections = getIdSelections();
        // push or splice the selections if you want to save all data selections
    });
    $storeToolbarRemove.click(function () {
        var ids = getIdSelections();
        var _data = {'id': ids, 'action': "delete"};
        _data[csrf_token_name] = csrf_token_hash;
        //console.log("id selected", ids);
        $.ajax({
                type: "POST",
                url: baseUrlPath + 'custom_style/style/',
                data: _data
            })
            .done(function (msg) {
                //console.log("Entry removed: " + JSON.parse(msg).status);
                $storeTable.bootstrapTable('remove', {
                    field: '_id',
                    values: ids
                });
            })
            .fail(function () {
                console.log("Error!");
            });
        $storeToolbarRemove.prop('disabled', true);
    });
}

function getIdSelections() {
    return $.map($storeTable.bootstrapTable('getSelections'), function (row) {
        return row._id;
    });
}

function editModalForm(data) {
    resetModalForm();
    $('#formModalLabel').html("Edit Custom Style");
    $formModal.find("#style-id").val(data._id);
    $formModal.find("#style-name").val(data.name);
    $formModal.find("#style-key").val(data.key);
    $formModal.find("#style-value").val(data.value);
}
window.operateEvents = {
    'click .edit-style': function (e, value, row, index) {
        //console.log('You click edit action, row: ' + JSON.stringify(row));
        editModalForm(row);
        $formModal.modal('show');
    },
    'click .remove-style': function (e, value, row, index) {
        //console.log("REMOVE NODE");
        var _data = {'action': "delete"};
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
                type: "POST",
                url: baseUrlPath + 'custom_style/style/' + row._id,
                data: _data
            })
            .done(function (msg) {
                //console.log("Entry removed: " + JSON.parse(msg).status);
                $storeTable.bootstrapTable('remove', {
                    field: '_id',
                    values: [row._id]
                });
            })
            .fail(function () {
                console.log("Error!");
            });
    }
};

function submitModalForm() {
    // todo: Add client validation here!
    var styleId = $formModal.find("#style-id").val() || "";
    $.ajax({
            type: "POST",
            url: baseUrlPath + "custom_style/style/" + styleId,
            data: $('form.style-form').serialize(),
            beforeSend: function (xhr) {
                $formModal.modal('hide');
                $waitDialog.modal();
            }
        })
        .done(function () {
            $waitDialog.modal('hide');
            $storeTable.bootstrapTable('refresh');
            $savedDialog.modal();
        })
        .fail(function (xhr, textStatus, errorThrown) {
            if(JSON.parse(xhr.responseText).status == "error") {
                $('form.store-organize-form').trigger("reset");
                alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
            }else if(JSON.parse(xhr.responseText).status == "name require"){
                $waitDialog.modal('hide');
                $errorDialog.find("#error_message").text("Group name is require!");
                $errorDialog.modal();
            }else if(JSON.parse(xhr.responseText).status == "key require"){
                $waitDialog.modal('hide');
                $errorDialog.find("#error_message").text("Key name is require!");
                $errorDialog.modal();
            }else if(JSON.parse(xhr.responseText).status == "key duplicate"){
                $waitDialog.modal('hide');
                $errorDialog.find("#error_message").text("Key of this style group is already exist!");
                $errorDialog.modal();
            }

        })
        .always(function () {
            //$('form.style-form').trigger("reset");
            $waitDialog.modal('hide');
        });
}

function operateItemFormatter(value, row, index) {
    return [
        '<a class="edit-style" title="Edit">',
        '<i class="fa fa-edit fa-2x"></i>',
        '</a>  ',
        '<a class="remove-style" href="javascript:void(0)" title="Remove">',
        '<i class="fa fa-remove fa-2x"></i>',
        '</a>'
    ].join('');
}

function responseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row._id, storeSelections) !== -1;
    });
    return res;
}

$(function () {
    initStoreTable();
});

$('#page-render')
    .on('click', 'button#modal-submit', submitModalForm)
    .on('click', 'button#error-dialog-close', function () {
        $errorDialog.modal('hide');
        $formModal.modal('show');
    });

$("[data-toggle]")
    .filter("[href='#formModal'],[data-target='#formModal']")
    .on('click', function (e) {
        //console.log($(this).hasClass('add-organize'));
        resetModalForm();
        if ($(this).hasClass('add-node'))
            $('#formModalLabel').html("Add new Custom style");
    });
