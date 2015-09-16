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
            <?php } ?>
            <div class="tabbable">
                <ul class="nav nav-tabs" id="merchantTab">
                    <li class="active"><a href="#merchant-general"
                                          data-toggle="tab"><?php echo $this->lang->line('tab_general'); ?></a></li>
                    <li><a href="#merchant-branch"
                           data-toggle="tab"><?php echo $this->lang->line('tab_branch'); ?></a>
                    </li>
                </ul>
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
                $attributes = array('id' => 'form', 'class' => 'form-horizontal');
                echo form_open($form, $attributes);
                ?>
                <div class="tab-content">
                    <div class="tab-pane active" id="merchant-general">
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
                    <div class="tab-pane" id="merchant-branch">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div class="text-center form-inline">
                                    <label for="merchant-branch-to-create"><?php echo $this->lang->line('entry_number_to_create'); ?></label>
                                    <input type="text" class="input-small" id="merchant-branch-to-create">
                                    <button type="submit" class="btn">Create</button>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Branch</th>
                                        <th>PIN Code</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
<!--                                    <tr>-->
<!--                                        <td>###</td>-->
<!--                                        <td><input type="text" class="input-small" id="merchant-branch-name" name="merchant-branch-name[]"></td>-->
<!--                                        <td>#code</td>-->
<!--                                        <td><input type="checkbox" name="merchant-branch-status[]" id="branch_status_switch"-->
<!--                                                   data-handle-width="40" checked="checked"></td>-->
<!--                                    </tr>-->
                                    <tr>
                                        <td colspan="4" style="text-align: center">Create new branch?</td>
                                    </tr>
                                    </tbody>
                                </table>
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
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#merchantTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $("[name='merchant-status']").bootstrapSwitch();
        $("#status_switch").bootstrapSwitch();
    });
</script>
