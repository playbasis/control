function init_input_general_tab() {
    // store page
    $("[name='store-status']").bootstrapSwitch();
    $("#store-parent").select2({
        placeholder: "Search for a organize parent",
        allowClear: true,
        minimumInputLength: 0,
        id: function(data){
            return data._id.$id;
        },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: "/store_org/organize/",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    search: term, // search term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter the remote JSON data
                return { results: data.rows };
            },
            cache: true
        },
        formatResult: organizeFormatResult, // omitted for brevity, see the source of this page
        formatSelection: organizeFormatSelection,  // omitted for brevity, see the source of this page
    });

    // modal
    $("[name='store-organize-status']").bootstrapSwitch();
    $("#store-organize-parent").select2({
        placeholder: "Search for a organize parent",
        allowClear: true,
        minimumInputLength: 0,
        id: function(data){
            return data._id.$id;
        },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: "/store_org/organize/",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    search: term, // search term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter the remote JSON data
                return { results: data.rows };
            },
            cache: true
        },
        formatResult: organizeFormatResult, // omitted for brevity, see the source of this page
        formatSelection: organizeFormatSelection,  // omitted for brevity, see the source of this page
    });
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

var $storeOrganizeTable = $('#storeOrganizeTable'),
    $storeOrganizeToolbarRemove = $('#storeOrganizeToolbar').find('#remove'),
    storeOrganizeSelections = [];
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
        '<a class="like" href="javascript:void(0)" title="Like">',
        '<i class="fa fa-edit fa-2x"></i>',
        '</a>  ',
        '<a class="remove" href="javascript:void(0)" title="Remove">',
        '<i class="fa fa-remove fa-2x"></i>',
        '</a>'
    ].join('');
}
window.operateEvents = {
    'click .like': function (e, value, row, index) {
        alert('You click like action, row: ' + JSON.stringify(row));
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

var $addOrganizeModal = $('#addOrganizeModal'),
    $waitDialog = $('#pleaseWaitDialog');

$(function () {
    init_input_general_tab();
    initStoreOrganizeTable();

});

$('#page-render').on('click', 'button#store-organize-modal-submit', function () {
    // todo: Add client validation here!
    $.ajax({
            type: "POST",
            url: "/store_org/organize/",
            data: $('form.store-organize-add').serialize(),
            beforeSend: function (xhr) {
                $addOrganizeModal.modal('hide');
                $waitDialog.modal();
            }
        })
        .done(function () {
            $storeOrganizeTable.bootstrapTable('refresh');
        })
        .fail(function (xhr, textStatus, errorThrown) {
            alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
        })
        .always(function () {
            $('form.store-organize-add').trigger("reset");
            $waitDialog.modal('hide');
        });
});