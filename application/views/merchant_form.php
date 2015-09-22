<div id="content" class="span10 merchant-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
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
                           data-toggle="tab"><?php echo $this->lang->line('tab_branch'); ?></a>
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
                                    <li><a href="#branches-list">Branches list</a></li>
                                    <li class="active"><a href="#branches-new">New Branches</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="branches-list">
                                        <div class="row-fluid">
                                            <table data-toggle="table" data-pagination="true">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Branch</th>
                                                    <th>PIN Code</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if (!empty($branches_list)) {
                                                    $index = 0;
                                                    foreach ($branches_list as $branch) { ?>
                                                        <tr>
                                                            <td><?php echo ++$index; ?></td>
                                                            <td><?php echo $branch['branch_name']; ?></td>
                                                            <td><?php echo $branch['pin_code']; ?></td>
                                                            <td><?php echo $branch['status'] ? 'Enabled' : 'Disabled'; ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="4" style="text-align: center">No branch found.
                                                            Create new?
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade active in" id="branches-new">
                                        <div class="row-fluid">
                                            <div class="text-center form-inline">
                                                <label
                                                    for="merchant-branch-to-create"><?php echo $this->lang->line('entry_number_to_create'); ?></label>
                                                <input type="text" class="input-small" id="merchant-branch-to-create">
                                                <button type="button" id="merchant-branch-to-create-btn" class="btn">Create</button>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <table class="table" id="new-branches-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Branch</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td id="branch-index">1</td>
                                                    <td><input type="text" name="branches[0][branchName]" value=""></td>
                                                    <td><input type="checkbox" name="branches[0][status]"
                                                               data-handle-width="40" checked="checked"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <span class="pull-right"><a class="btn btn-primary" id="add"><i class="fa fa-plus"></i>&nbsp;Add</a></span>
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
                                <span>x</span>
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
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-table.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var globalNewIndex = 0;

    $(function () {
        $("#add").click(function (e) {
            e.preventDefault();
            $("input[name^='branches'][name$='[status]']:last").bootstrapSwitch("destroy");
            $('#new-branches-table tbody>tr:last').clone(true).insertAfter('#new-branches-table tbody>tr:last');

            var newIndex = globalNewIndex + 1;
            var changeIds = function (i, val) {
                if(val)
                    return val.replace(globalNewIndex, newIndex);
            };

            $('#new-branches-table tbody>tr:last input').attr('name', changeIds).attr('id', changeIds);

            var displayIndex = $('#new-branches-table tbody>tr:last #branch-index').text(); //display index start from 1
            $('#new-branches-table tbody>tr:last #branch-index').html(parseInt(displayIndex)+1);

            globalNewIndex++;
            $(":not(div .bootstrap-switch-container)>input[name^='branches'][name$='[status]']").bootstrapSwitch();
            //return false;
        });

        $("#merchant-branch-to-create-btn").click(function(e){
            e.preventDefault();

            var noBranches = $('#merchant-branch-to-create').val();

            if ($.isNumeric(noBranches) && noBranches>0){
                for(i=0;i<noBranches;i++){
                    $("input[name^='branches'][name$='[status]']:last").bootstrapSwitch("destroy");
                    $('#new-branches-table tbody>tr:last').clone(true).insertAfter('#new-branches-table tbody>tr:last');

                    var newIndex = globalNewIndex + 1;
                    var changeIds = function (i, val) {
                        if(val)
                            return val.replace(globalNewIndex, newIndex);
                    };

                    $('#new-branches-table tbody>tr:last input').attr('name', changeIds).attr('id', changeIds);

                    var displayIndex = $('#new-branches-table tbody>tr:last #branch-index').text(); //display index start from 1
                    $('#new-branches-table tbody>tr:last #branch-index').html(parseInt(displayIndex)+1);

                    globalNewIndex++;
                }
                $(":not(div .bootstrap-switch-container)>input[name^='branches'][name$='[status]']").bootstrapSwitch();
                //return false;
            }
        });

        $(".merchant-page ul.nav-tabs a").click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $("[name='merchant-status']").bootstrapSwitch();
        $("[name^='branches'][name$='[status]']").bootstrapSwitch();
    });
</script>
