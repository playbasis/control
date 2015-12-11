<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt=""/>&nbsp;<?php echo $heading_title; ?></h1>

            <div class="buttons">
                <a class="btn btn-info"
                   onclick="location =  baseUrlPath+'store_org/insert'"><?php echo $this->lang->line('button_insert'); ?></a>
                <a class="btn btn-info"
                   onclick="$('#form').submit();"><?php echo $this->lang->line('button_delete'); ?></a>
            </div>
        </div>
        <!-- .heading -->
        <div class="content">
            <?php if ($this->session->flashdata('success')) { ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php } ?>
            <div id="store_org">
                <div class="row-fluid">
                    <div id="toolbar">
                        <button id="remove" class="btn btn-danger" disabled>
                            <i class="fa fa-remove"></i> Delete
                        </button>
                    </div>
                    <table id="stores-table"
                           data-toolbar="#toolbar"
                           data-search="true">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-table.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table.min.js" type="text/javascript"></script>
<script type="text/javascript">
    function filter() {
        url = baseUrlPath + 'store_org';

        var filter_name = $('input[name=\'filter_name\']').attr('value');

        if (filter_name) {
            url += '?filter_name=' + encodeURIComponent(filter_name);
        }

        location = url;
    }

    <?php if (!isset($_GET['filter_name'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'store_org');
    }

    var $storesTable = $("#stores-table"),
        $remove = $('#remove'),
        selections = [];

    $(function () {
        $storesTable.bootstrapTable({
            url: baseUrlPath + 'store_org/listBranch/',
            idField: '_id',
            columns: [{
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                field: 'store_name',
                title: 'Store Name',
                align: 'center',
                valign: 'middle'
            }, {
                field: 'area_name',
                title: 'Area Name',
                align: 'center',
                valign: 'middle'
            }, {
                field: 'district_name',
                title: 'District Name',
                align: 'center',
                valign: 'middle'
            }, {
                field: 'company_name',
                title: 'Company Name',
                align: 'center',
                valign: 'middle'
            }, {
                field: 'status',
                title: 'Status',
                align: 'center',
                valign: 'middle'
            }, {
                field: 'operate',
                title: 'Item Operate',
                align: 'center',
                events: operateEvents,
                formatter: operateFormatter
            }],
            pagination: true,
            search: true,
            showRefresh: true,
            iconPrefix: 'fa',
            detailView: true,
            detailFormatter: detailFormatter
        });

        $storesTable.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$branchesTable.bootstrapTable('getSelections').length);

            // save your data, here just save the current page
            selections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });
        $remove.click(function (e) {
            e.preventDefault();
            var ids = getIdSelections();
            console.log(ids);
            $.ajax({
                    type: "POST",
                    url: baseUrlPath + 'merchant/removeBranch/',
                    data: {'id': ids}
                })
                .done(function (msg) {
                    console.log("Entry removed: " + msg);
                    $branchesTable.bootstrapTable('remove', {
                        field: '_id',
                        values: ids
                    });
                })
                .fail(function () {
                    console.log("Error!");
                });
            $remove.prop('disabled', true);
        });

        $storesTable.on('editable-save.bs.table', function (e) {
            console.log("SAVE!");
        });

    });

    function getIdSelections() {
        return $.map($branchesTable.bootstrapTable('getSelections'), function (row) {
            return row._id
        });
    }

    function statusFormatter(value, row) {
        return (value ? 'Enabled' : 'Disabled');
    }

    function detailFormatter(index, row) {
        var html = [];
        $.each(row, function (key, value) {
            switch (key) {
//                case 'branch_name':
//                    html.push('<p><b>Branch Name:</b> ' + value + '</p>');
//                    break;
//                case 'pin_code':
//                    html.push('<p><b>PIN Code:</b> <span class="label" style="letter-spacing: 2px;">' + value + '</span></p>');
//                    break;
                case 'status':
                    html.push('<p><b>Status:</b> ' + (value ? 'Enabled' : 'Disabled') + '</p>');
                    break;
                case 'date_modified':
                    var dateObj = new Date(value.sec * 1000);
                    html.push('<p><b>Last Modified:</b> ' + dateObj.toLocaleString() + '</p>');
                    break;
                default :
                    break;
            }
        });
        return html.join('');
    }

    function operateFormatter(value, row, index) {
        return [
            '<a class="remove" href="javascript:void(0)" title="Remove">',
            '<i class="fa fa-remove"></i>',
            '</a>'
        ].join('');
    }

    window.operateEvents = {
        'click .remove': function (e, value, row, index) {
            $.ajax({
                    type: "POST",
                    url: baseUrlPath + 'merchant/removeBranch/',
                    data: {'id': row._id}
                })
                .done(function (msg) {
                    console.log("Entry removed: " + msg);
                    $branchesTable.bootstrapTable('remove', {
                        field: '_id',
                        values: [row._id]
                    });
                })
                .fail(function () {
                    console.log("Error!");
                });
        }
    };

</script>