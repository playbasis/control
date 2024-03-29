<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10 store_org-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <a class="btn btn-info"
                   onclick="location = baseUrlPath"><i
                        class="fa fa-home"></i>&nbsp;<?php echo $this->lang->line('button_home'); ?></a>
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
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active"><a href="#storeTabContent"
                                          data-toggle="tab"><?php echo $this->lang->line('tab_node'); ?></a></li>
                    <li><a href="#storeOrganizeTabContent"
                           data-toggle="tab"><?php echo $this->lang->line('tab_organize'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="storeTabContent">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="storeNodeToolbar">
                                    <button id="remove" class="btn btn-danger" disabled>
                                        <i class="fa fa-remove"></i> Delete
                                    </button>
                                    <a href="#formNodeModal" id="add" role="button" class="btn btn-info add-node"
                                       data-toggle="modal"><i class="fa fa-plus"></i> Add</a>
                                </div>
                                <table id="storeNodeTable"
                                       data-toolbar="#storeNodeToolbar"
                                       data-search="true"
                                       data-show-refresh="true"
                                       data-show-columns="true"
                                       data-minimum-count-columns="2"
                                       data-pagination="true"
                                       data-id-field="_id"
                                       data-page-list="[10, 25, 50, 100, ALL]"
                                       data-side-pagination="server"
                                       data-url="<?php echo site_url(); ?>/store_org/node/"
                                       data-response-handler="nodeResponseHandler">
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="storeOrganizeTabContent">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="storeOrganizeToolbar">
                                    <button id="remove" class="btn btn-danger" disabled>
                                        <i class="fa fa-remove"></i> Delete
                                    </button>
                                    <a href="#formOrganizeModal" id="add" role="button"
                                       class="btn btn-info add-organize" data-toggle="modal"><i class="fa fa-plus"></i>
                                        Add</a>
                                </div>
                                <table id="storeOrganizeTable"
                                       data-toolbar="#storeOrganizeToolbar"
                                       data-search="true"
                                       data-show-refresh="true"
                                       data-show-columns="true"
                                       data-minimum-count-columns="2"
                                       data-pagination="true"
                                       data-id-field="_id"
                                       data-page-list="[10, 25, 50, 100, ALL]"
                                       data-side-pagination="server"
                                       data-url="<?php echo site_url(); ?>/store_org/organize/"
                                       data-response-handler="organizeResponseHandler">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="formNodeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formNodeModalLabel"
     aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formNodeModalLabel">Node</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal node-form')); ?>
                <div class="row-fluid">
                    <input type="hidden" name="node-id" id="node-id">

                    <div class="control-group">
                        <label for="node-name"
                               class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_node_name'); ?></label>

                        <div class="controls">
                            <input type="text" name="node-name" id="node-name"
                                   placeholder="<?php echo $this->lang->line('entry_node_name'); ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="node-desc"
                               class="control-label"><?php echo $this->lang->line('entry_node_description'); ?></label>

                        <div class="controls">
                            <textarea name="node-desc" id="node-desc" rows="5"
                                      placeholder="<?php echo $this->lang->line('entry_node_description') ?>"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="node-organize"
                               class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_node_organize'); ?></label>

                        <div class="controls">
                            <p><input type='hidden' name="node-organize" id="node-organize" style="width:80%;"></p>

                            <p><a href="#storeOrganizeTabContent" data-toggle="tab" data-dismiss="modal"
                                  id="addNewParentLink"><?php echo $this->lang->line('entry_node_add_organize'); ?></a>
                            </p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="node-parent"
                               class="control-label"><?php echo $this->lang->line('entry_node_parent'); ?></label>

                        <div class="controls">
                            <p><input type='hidden' name="node-parent" id="node-parent" style="width:80%;"></p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"
                               for="node-status"><?php echo $this->lang->line('entry_node_status'); ?></label>

                        <div class="controls">
                            <input type="checkbox" name="node-status" id="node-status"
                                   data-handle-width="40" checked>
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="node-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
    </div>
</div>

<div id="formOrganizeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formOrganizeModalLabel"
     aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formOrganizeModalLabel">Organize</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal store-organize-form')); ?>
                <div class="row-fluid">
                    <input type="hidden" name="store-organize-id" id="store-organize-id">

                    <div class="control-group">
                        <label for="store-organize-name"
                               class="control-label "><span class="required">*</span><?php echo $this->lang->line('entry_organize_name'); ?></label>

                        <div class="controls">
                            <input type="text" name="store-organize-name" id="store-organize-name"
                                   placeholder="<?php echo $this->lang->line('entry_organize_name'); ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="store-organize-desc"
                               class="control-label"><?php echo $this->lang->line('entry_organize_desc'); ?></label>

                        <div class="controls">
                            <textarea name="store-organize-desc" id="store-organize-desc" rows="5"
                                      placeholder="<?php echo $this->lang->line('entry_organize_desc') ?>"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="store-organize-parent"
                               class="control-label"><?php echo $this->lang->line('entry_organize_parent'); ?></label>

                        <div class="controls">
                            <input type='hidden' name="store-organize-parent" id="store-organize-parent"
                                   style="width:80%;">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"
                               for="store-organize-status"><?php echo $this->lang->line('entry_organize_status'); ?></label>

                        <div class="controls">
                            <input type="checkbox" name="store-organize-status" id="store-organize-status"
                                   data-handle-width="40" checked>
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="store-organize-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
    </div>
</div>

<div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1>Please Wait</h1>
    </div>
    <div class="modal-body">
        <div class="offset5 ">
            <i class="fa fa-spinner fa-spin fa-5x"></i>
        </div>
    </div>
</div>

<div id="pleaseWaitSpanDiv" class="hide">
    <span id="pleaseWaitSpan"><i class="fa fa-spinner fa-spin"></i></span>
</div>

<div class="modal hide" id="savedDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h1>Data Saved</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-save"></i>&nbsp;<span>Data has been saved!</span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<div class="modal hide" id="orgErrorDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
        <h1>Error</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-times"></i>&nbsp;<span id="org_error_message"></span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="org-error-dialog-close">Close</button>
    </div>
</div>

<div class="modal hide" id="nodeErrorDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
        <h1>Error</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-times"></i>&nbsp;<span id="node_error_message"></span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="node-error-dialog-close">Close</button>
    </div>
</div>

<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-table.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<!--<link href="--><?php //echo base_url(); ?><!--javascript/bootstrap/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css">-->
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table.min.js" type="text/javascript"></script>
<!--<script src="--><?php //echo base_url(); ?><!--javascript/bootstrap/bootstrap-editable/js/bootstrap-editable.min.js" type="text/javascript"></script>-->
<!--<script src="--><?php //echo base_url(); ?><!--javascript/custom/bootstrap-table-editable.min.js" type="text/javascript"></script>-->
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<!--<script src="--><?php //echo base_url(); ?><!--javascript/md5.js" type="text/javascript"></script>-->
<!--<script src="--><?php //echo base_url(); ?><!--javascript/mongoid.js" type="text/javascript"></script>-->
<script src="<?php echo base_url(); ?>javascript/store_org/app_form.js" type="text/javascript"></script>

<script type="text/javascript">
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    Pace.on("done", function(){
        $(".cover").fadeOut(1000);
    });
</script>