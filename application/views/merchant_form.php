<div id="content" class="span10">

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
                    <li><a href="#merchant-branch" data-toggle="tab"><?php echo $this->lang->line('tab_branch'); ?></a>
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
                        <div class="row" style="padding-top: 10px">
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
                                    class="control-label"><?php echo $this->lang->line('entry_status'); ?></label>

                                <div class="controls">
                                    <input type="checkbox" name="merchant-status" data-handle-width="40" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .tab-pane -->
                    <div class="tab-pane" id="merchant-branch">
                        <div class="row well" style="padding-top: 10px">
                            <div class="control-group">
                                <label
                                    class="control-label"><?php echo $this->lang->line('entry_branch_name'); ?></label>

                                <div class="controls">
                                    <input type="text"
                                           placeholder="<?php echo $this->lang->line('entry_branch_name'); ?>">
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
<link href="<?php echo base_url();?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url();?>javascript/custom/bootstrap-switch.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#merchantTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $("[name='merchant-status']").bootstrapSwitch();
    });
</script>
