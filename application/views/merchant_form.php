<div id="content" class="span10 merchant-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" id="form-submit-btn"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'merchant'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }
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
            ?>
            <div class="tabbable">
                <ul class="nav nav-tabs" id="merchantTab">
                    <li class="active"><a href="#merchant-general"
                                          data-toggle="tab"><?php echo $this->lang->line('tab_general'); ?></a></li>
                    <li><a href="#merchant-branch"
                           data-toggle="tab"><?php echo $this->lang->line('tab_branch'); ?>&nbsp;<span class="badge"><?php echo !empty($branches_list) ? count($branches_list) : '0';?></span></a>
                    </li>
                    <li><a href="#merchant-goods_group"
                           data-toggle="tab"><?php echo $this->lang->line('tab_goods_group'); ?></a>
                    </li>
                </ul>
                <?php
                $attributes = array('id' => 'form', 'class' => 'form-horizontal');
                echo form_open($form, $attributes);
                ?>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="merchant-general">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label
                                        class="control-label"><?php echo $this->lang->line('entry_merchant_name'); ?></label>

                                    <div class="controls">
                                        <input type="text" name="merchant-name"
                                               placeholder="<?php echo $this->lang->line('entry_merchant_name'); ?>"
                                               value="<?php echo isset($merchant_name) ? $merchant_name : set_value('merchant-name'); ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label
                                        class="control-label"><?php echo $this->lang->line('entry_description'); ?></label>

                                    <div class="controls">
                                    <textarea name="merchant-desc" rows="4"
                                              placeholder="<?php echo $this->lang->line('entry_description'); ?>"><?php echo isset($merchant_desc) ? $merchant_desc : set_value('merchant-desc'); ?></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label
                                        class="control-label"
                                        for="status_switch"><?php echo $this->lang->line('entry_status'); ?></label>

                                    <div class="controls">
                                        <input type="checkbox" name="merchant-status" id="status_switch"
                                               data-handle-width="40" <?php echo isset($merchant_status) && $merchant_status ? ($merchant_status? "checked='checked'" : '') : set_checkbox('merchant-status','',true); ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .tab-pane -->
                    <div class="tab-pane fade" id="merchant-branch">
                        <div class="container-fluid">
                            <div class="tabbable">
                                <ul class="nav nav-tabs">
                                    <li><a href="#branches-list">Branches list&nbsp;<span class="badge"><?php echo !empty($branches_list) ? count($branches_list) : '0';?></span></a></li>
                                    <li class="active"><a href="#branches-new">New Branches</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="branches-list">
                                        <div class="row-fluid">
                                            <div id="toolbar">
                                                <button id="remove" class="btn btn-danger" disabled>
                                                    <i class="fa fa-remove"></i> Delete
                                                </button>
                                            </div>
                                            <table id="branches-table"
                                                   data-toolbar="#toolbar"
                                                   data-search="true">
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade active in" id="branches-new">
                                        <div class="row-fluid">
                                            <table class="table table-bordered" id="new-branches-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Branch</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="3" style="text-align: center">
                                                        <div class="row-fluid">
                                                            <div class="offset3 span3">
                                                                <a class="btn btn-primary btn-block" id="add"><i class="fa fa-plus"></i>&nbsp;Add 1</a>
                                                            </div>
                                                            <div class="span3">
                                                                <a class="btn btn-primary btn-block" id="add5"><i class="fa fa-plus"></i>&nbsp;Add 5</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .tab-pane -->
                    <div class="tab-pane fade" id="merchant-goods_group">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="mc-goodsgroup-header" style="margin-bottom: 15px">
                                    <div class="row-fluid">
                                        <div class="offset3 span2">
                                            <a id="open-mc-goodsgroup-btn" class="btn btn-block">
                                                <?php echo $this->lang->line('entry_goods_group_header_open_all'); ?>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a id="close-mc-goodsgroup-btn" class="btn btn-block">
                                                <?php echo $this->lang->line('entry_goods_group_header_close_all'); ?>
                                            </a>
                                        </div>
                                        <div class="span2">
                                            <a id="add-mc-goodsgroup-btn" class="btn btn-primary btn-block">
                                                <?php echo $this->lang->line('entry_goods_group_header_add_goodsgroup'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="row-fluid" style="margin-top: 15px;">
                                    <div class="alert alert-info">
                                        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-close"></i></button>
                                        <h4><?php echo $this->lang->line('text_goods_group_info_header'); ?></h4>
                                        <?php echo $this->lang->line('text_goods_group_info_text'); ?>
                                    </div>
                                </div>
                                <div id="mc-goodsgroup-wrapper"></div>
                            </div>
                        </div>
                    </div>
                    <!-- .tab-pane -->
                </div>
                <?php
                echo form_close();
                ?>
            </div>
        </div>
    </div>
</div>

<div id="newBranch_emptyElement" class="hide invisible">
    <table>
        <tr>
            <td>{{id_num}}</td>
            <td><input type="text" name="newBranches[{{id}}][branchName]" value=""></td>
            <td><input type="checkbox" name="newBranches[{{id}}][status]" data-handle-width="40" checked="checked"></td>
        </tr>
    </table>
</div>
<div id="newGoodGroups_emptyElement" class="hide invisible">
    <div class="mc-goodsgroup-item-wrapper" data-mc-goodsgroup-id="{{id}}">
        <div class="box-header box-goodsgroup-header overflow-visible">
            <div class="row-fluid">
                <h2><img src="<?php echo base_url(); ?>image/default-image.png" width="50">{{header}}</h2>

                <div class="box-icon">
                    <input type="checkbox" name="mc_goodsGroups[{{id}}][status]" data-handle-width="40" data-size="normal" {{status}}>
                    <a class="btn btn-danger right remove-goodsgroup-btn">Delete</a>
                    <span class="break"></span>
                    <a><i class="fa fa-chevron-up"></i></a>
                </div>
            </div>
        </div>
        <div class="box-content clearfix">
            <div class="row-fluid" style="margin-top: 15px;">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo $this->lang->line('entry_goods_groups_select'); ?>
                            </label>

                            <div class="controls">
                                <select style="width: 80%" name="mc_goodsGroups[{{id}}][goodsGroup]">
                                    <?php
                                    if (isset($goodsgroups)) {
                                        foreach ($goodsgroups as $goodsgroup) { ?>
                                            <option
                                                value="mc_gg_<?php echo $goodsgroup ?>"><?php echo $goodsgroup ?></option>
                                        <?php }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo $this->lang->line('entry_allow_branches_select'); ?>
                            </label>

                            <div class="controls">
                                <select multiple="multiple" style="width: 80%"
                                        name="mc_goodsGroups[{{id}}][allowBranches][]">
                                    <?php
                                    if (!empty($branches_list)) {
                                        //TODO: Need to check if branch status is disabled.
                                        foreach ($branches_list as $branch) { ?>
                                            <option
                                                value="<?php echo $branch['_id'] . ':' . $branch['branch_name'] ?>"><?php echo $branch['branch_name'] ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hide fade" id="inputEmptyModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Input is empty</h3>
    </div>
    <div class="modal-body">
        <p>Please make sure all input is not empty.</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
    </div>
</div>

<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-table.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>javascript/bootstrap/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/bootstrap/bootstrap-editable/js/bootstrap-editable.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table-editable.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/md5.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/mongoid.js" type="text/javascript"></script>
<script type="text/javascript">
    var globalNewIndex = 0;

    var merchantGoodsGroupsJSON = <?php echo isset($merchantGoodsGroupsJSON) ? $merchantGoodsGroupsJSON : "null"; ?>;

    var $branchesTable = $('#branches-table'),
        $remove = $('#remove'),
        selections = [];

    $(function () {
        function init_mc_goodgroups_item_box() {
            $.each(merchantGoodsGroupsJSON, function (index, value) {
                createGoodsGroupItemBox(value._id.$id,value);
            });
        }

        function init_mc_goodgroups_event() {
            $('.mc-goodsgroup-item-wrapper .box-goodsgroup-header').unbind().bind('click', function (data) {
                var $target = $(this).next('.box-content');

                if ($target.is(':visible')) {
                    $('i', $(this)).removeClass('fa-chevron-up').addClass('fa-chevron-down');
                } else {
                    $('i', $(this)).removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }

                $target.slideToggle();
            });

            $('.remove-goodsgroup-btn').unbind().bind('click', function (data) {
                var $target = $(this).parent().parent().parent().parent();
                console.log($target);

                var r = confirm("Are you sure to remove!");
                if (r == true) {
                    $target.remove();
                    init_mc_goodgroups_event()
                }
            });

            $("[name^='mc_goodsGroups['][name$='][goodsGroup]']:not([name*='id'])").select2();
            $("[name^='mc_goodsGroups['][name$='][allowBranches][]']:not([name*='id'])").select2({closeOnSelect: false});
            $(":not(div .bootstrap-switch-container)>input[name^='mc_goodsGroups['][name$='][status]']:not([name*='id'])").bootstrapSwitch();
        }

        function createGoodsGroupItemBox(merchantGoodsGroupId, merchantGoodsGroupDataJSONObject) {
            merchantGoodsGroupId = typeof merchantGoodsGroupId !== 'undefined' ? merchantGoodsGroupId : mongoIDjs();
            merchantGoodsGroupDataJSONObject = typeof merchantGoodsGroupDataJSONObject !== 'undefined' ? merchantGoodsGroupDataJSONObject : null;

            var goodsGroupsHtml = $('#newGoodGroups_emptyElement').html();
            goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('{{id}}', 'g'), merchantGoodsGroupId);

            if (merchantGoodsGroupDataJSONObject != null) {
                goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('{{header}}', 'g'), merchantGoodsGroupDataJSONObject.goods_group);
                goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('{{status}}', 'g'), merchantGoodsGroupDataJSONObject.status ? 'checked="checked"' : null);
                //replace merchant_goodsgroup select2
                goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('option value="mc_gg_' + merchantGoodsGroupDataJSONObject.goods_group + '"', 'g'),
                    'option value="mc_gg_' + merchantGoodsGroupDataJSONObject.goods_group + '" selected');
                $.each(merchantGoodsGroupDataJSONObject.branches_allow, function (index, value) {
                    goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('option value="' + value.b_id.$id + ':' + value.b_name + '"', 'g'),
                        'option value="' + value.b_id.$id + ':' + value.b_name + '" selected');
                });
            } else {
                goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('{{header}}', 'g'), 'New good groups');
                goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('{{status}}', 'g'), 'checked="checked"');
            }

            var globalGoodsGroupSelectedArray = $("[name^='mc_goodsGroups['][name$='][goodsGroup]']:not([name*='id'])");
            $.each(globalGoodsGroupSelectedArray, function (index, value) {
                var selectedArray = $(value).val().toString().split(",");
                $.each(selectedArray, function (index, value) {
                    goodsGroupsHtml = goodsGroupsHtml.replace(new RegExp('option value="' + value + '"', 'g'), 'option disabled value="' + value + '"');
                });
            });

            var $goodsGroupWrapper = $('#mc-goodsgroup-wrapper');
            $goodsGroupWrapper.append(goodsGroupsHtml);

            var element_position = $goodsGroupWrapper.find('[data-mc-goodsgroup-id="' + merchantGoodsGroupId + '"]').offset();
            $("html, body").animate({scrollTop: (element_position.top - 20)}, 600);
        }

        function isAllGoodsGroupFilled() {
            var isFilled = true;
            $.each($("[name^='mc_goodsGroups['][name$='][allowBranches][]']:not([name*='id'])"), function (key, value) {
                var select_val = $(value).find("option:selected").val();
                console.log("select_val", select_val);
                if (select_val == null){
                    isFilled = false;
                }
            });
            return isFilled;
        }

        $('#add-mc-goodsgroup-btn').click(function () {
            createGoodsGroupItemBox();

            init_mc_goodgroups_event();
        });

        $('#open-mc-goodsgroup-btn').click(function () {
            $('.mc-goodsgroup-item-wrapper>.box-content').show();
        });
        $('#close-mc-goodsgroup-btn').click(function () {
            $('.mc-goodsgroup-item-wrapper>.box-content').hide();
        });

        function createBranchTableRow(numToCreate){
            numToCreate = typeof numToCreate !== 'undefined' ? numToCreate : 1;

            if ($.isNumeric(numToCreate) && numToCreate > 0) {
                for (idx = 0; idx < numToCreate; idx++) {
                    var tableRowHTML = $('#newBranch_emptyElement').find('tbody').html();
                    var newIndex = globalNewIndex;

                    tableRowHTML = tableRowHTML.replace(new RegExp('{{id}}', 'g'), newIndex);
                    tableRowHTML = tableRowHTML.replace(new RegExp('{{id_num}}', 'g'), newIndex+1);

                    $('#new-branches-table').find('tbody').append(tableRowHTML);

                    globalNewIndex++;
                }
                $(":not(div .bootstrap-switch-container)>input[name^='newBranches'][name$='[status]']:not([name*='id'])").bootstrapSwitch();
            }
        }

        $("#add").click(function (e) {
            e.preventDefault();
            createBranchTableRow();
        });

        $("#add5").click(function (e) {
            e.preventDefault();
            createBranchTableRow(5);
        });

        $("#form-submit-btn").click(function (e) {
            if (isAllGoodsGroupFilled()) {
                $('#form').submit();
            } else {
                e.preventDefault();
                $('#inputEmptyModal').modal('show')
            }
        });

        $(".merchant-page ul.nav-tabs a").click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $("[name='merchant-status']").bootstrapSwitch();
        $(":not(div .bootstrap-switch-container)>input[name^='newBranches'][name$='[status]']:not([name*='id'])").bootstrapSwitch();
        <?php echo isset($merchantGoodsGroupsJSON) ? $merchantGoodsGroupsJSON : "null"; ?>;
        $branchesTable.bootstrapTable({
            url: baseUrlPath + 'merchant/listBranch/<?php echo isset($merchant_id) ? $merchant_id : '';?>',
            idField: '_id',
            columns: [{
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                field: 'branch_name',
                title: 'Branch Name',
                align: 'center',
                valign: 'middle',
                editable: {
                    placement: 'right',
                    url: baseUrlPath + 'merchant/updateBranch/',
                    validate: function(value) {
                        if($.trim(value) == '') {
                            return 'This field is required';
                        }
                    }
                }
            }, {
                field: 'pin_code',
                title: 'PIN Code',
                align: 'center',
                valign: 'middle',
                formatter: pinCodeFormatter
            }, {
                field: 'status',
                title: 'Status',
                align: 'center',
                valign: 'middle',
                formatter: statusFormatter,
                editable: {
                    type: 'select',
                    source: [
                        {value: 'Enabled', text: 'Enabled'},
                        {value: 'Disabled', text: 'Disabled'}
                    ],
                    url: baseUrlPath + 'merchant/updateBranch/',
                    validate: function(value) {
                        var val = $.trim(value);
                        if(val != 'Enabled' && val != 'Disabled') {
                            return 'This field is required';
                        }
                    }
                }
            }, {
                field: 'operate',
                title: 'Item Operate',
                align: 'center',
                events: operateEvents,
                formatter: operateFormatter
            }],
            //data: branchesListJSON,
            //resresponseHandler: responseHandler,
            pagination: true,
            search: true,
            showRefresh: true,
            iconPrefix: 'fa',
            detailView: true,
            detailFormatter: detailFormatter
        });

        $branchesTable.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {
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
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','id': ids}
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

        $branchesTable.on('editable-save.bs.table', function(e){
            console.log("SAVE!");
        });

        init_mc_goodgroups_item_box();
        init_mc_goodgroups_event();
    });

    function responseHandler(res) {
        $.each(res.rows, function (i, row) {
            row.state = $.inArray(row.id, selections) !== -1;
        });
        return res;
    }

    function getIdSelections() {
        return $.map($branchesTable.bootstrapTable('getSelections'), function (row) {
            return row._id
        });
    }

    function pinCodeFormatter(value, row) {
        return '<span class="label" style="font-size: 150%; letter-spacing: 2px;">' + value + '</span>';
    }

    function statusFormatter(value, row) {
        return (value ? 'Enabled' : 'Disabled');
    }

    function detailFormatter(index, row) {
        var html = [];
        $.each(row, function (key, value) {
            switch (key) {
                case 'branch_name':
                    html.push('<p><b>Branch Name:</b> ' + value + '</p>');
                    break;
                case 'pin_code':
                    html.push('<p><b>PIN Code:</b> <span class="label" style="letter-spacing: 2px;">' + value + '</span></p>');
                    break;
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
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','id': row._id}
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
