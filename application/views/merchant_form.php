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
                                               placeholder="<?php echo $this->lang->line('entry_merchant_name'); ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label
                                        class="control-label"><?php echo $this->lang->line('entry_description'); ?></label>

                                    <div class="controls">
                                    <textarea name="merchant-desc" rows="4"
                                              placeholder="<?php echo $this->lang->line('entry_description'); ?>"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label
                                        class="control-label"
                                        for="status_switch"><?php echo $this->lang->line('entry_status'); ?></label>

                                    <div class="controls">
                                        <input type="checkbox" name="merchant-status" id="status_switch"
                                               data-handle-width="40" checked="checked">
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
                                    <li class="active"><a href="#branches-list">Branches list</a></li>
                                    <li><a href="#branches-new">New Branches</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade active in" id="branches-list">
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
                                                <tr>
                                                    <td colspan="4" style="text-align: center">No branch found. Create new?</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="branches-new">
                                        <div class="row-fluid">
                                            <div class="text-center form-inline">
                                                <label
                                                    for="merchant-branch-to-create"><?php echo $this->lang->line('entry_number_to_create'); ?></label>
                                                <input type="text" class="input-small" id="merchant-branch-to-create">
                                                <button type="submit" class="btn">Create</button>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <a id="add">+</a>
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
                                                    <td><input type="text" name="branches[0][branchName]"></td>
                                                    <td><input type="checkbox" name="branches[0][status]"
                                                               data-handle-width="40" checked="checked"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
        $("#add").click(function () {
            $("[name^='branches'][name$='[status]']").bootstrapSwitch("destroy");
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
            $("[name^='branches']").filter("[name$='[status]']").bootstrapSwitch();
            return false;
        });

        $("ul.nav-tabs a").click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $("[name='merchant-status']").bootstrapSwitch();
        $("[name^='branches']").filter("[name$='[status]']").bootstrapSwitch();
    });
</script>
