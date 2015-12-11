function init_input_general_tab() {
    $("[name='store-status']").bootstrapSwitch();
    $("[name='store-brand-status']").bootstrapSwitch();

    $("#store-brand").select2();
    $("#store-district").select2();
    $("#store-area").select2();
    $("#store-franchise").select2();
}

var $storeBrandTable = $('#storeBrandTable'),
    $storeBrandToolbarRemove = $('#storeBrandToolbar').find('#remove'),
    storeBrandSelections = [];
function initStoreBrandTable() {
    $storeBrandTable.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                title: 'Brand Name',
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
        $storeBrandTable.bootstrapTable('resetView');
    }, 200);
    $storeBrandTable.on('check.bs.table uncheck.bs.table ' +
        'check-all.bs.table uncheck-all.bs.table', function () {
        $storeBrandToolbarRemove.prop('disabled', !$storeBrandTable.bootstrapTable('getSelections').length);
        // save your data, here just save the current page
        storeBrandSelections = getIdSelections();
        // push or splice the selections if you want to save all data selections
    });
    $storeBrandTable.on('expand-row.bs.table', function (e, index, row, $detail) {
        if (index % 2 == 1) {
            $detail.html('Loading from ajax request...');
            $.get('LICENSE', function (res) {
                $detail.html(res.replace(/\n/g, '<br>'));
            });
        }
    });
    $storeBrandTable.on('all.bs.table', function (e, name, args) {
        console.log(name, args);
    });
    $storeBrandToolbarRemove.click(function () {
        var ids = getIdSelections();
        $storeBrandTable.bootstrapTable('remove', {
            field: 'id',
            values: ids
        });
        $storeBrandToolbarRemove.prop('disabled', true);
    });
    $(window).resize(function () {
        $storeBrandTable.bootstrapTable('resetView', {
            height: getHeight()
        });
    });
}
function getIdSelections() {
    return $.map($storeBrandTable.bootstrapTable('getSelections'), function (row) {
        return row.id
    });
}
function responseHandler(res) {
    $.each(res.rows, function (i, row) {
        row.state = $.inArray(row.id, storeBrandSelections) !== -1;
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
        '<i class="glyphicon glyphicon-heart"></i>',
        '</a>  ',
        '<a class="remove" href="javascript:void(0)" title="Remove">',
        '<i class="glyphicon glyphicon-remove"></i>',
        '</a>'
    ].join('');
}
window.operateEvents = {
    'click .like': function (e, value, row, index) {
        alert('You click like action, row: ' + JSON.stringify(row));
    },
    'click .remove': function (e, value, row, index) {
        $storeBrandTable.bootstrapTable('remove', {
            field: 'id',
            values: [row.id]
        });
    }
};
function getHeight() {
    return $(window).height() - $('h1').outerHeight(true);
}

$(function () {
    init_input_general_tab();
    initStoreBrandTable();

    $('button#store-brand-modal-submit').click(function(){
        $.ajax({
            type: "POST",
            url: "/store_org/brand/",
            data: $('form.store-brand-add').serialize(),
            //success: function(msg){
            //    $("#thanks").html(msg);
            //    $("#form-content").modal('hide');
            //},
            error: function(){
                alert("failure");
            }
        });
    });
});