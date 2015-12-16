<div id="content" class="span10 store_org-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <a class="btn btn-info" id="form-submit-btn"><?php echo $this->lang->line('button_save'); ?></a>
                <a class="btn btn-info"
                   onclick="location = baseUrlPath+'store_org'"><?php echo $this->lang->line('button_cancel'); ?></a>
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
                                          data-toggle="tab"><?php echo $this->lang->line('tab_store'); ?></a></li>
                    <li><a href="#storeOrganizeTabContent"
                           data-toggle="tab"><?php echo $this->lang->line('tab_organize'); ?></a>
                    </li>
                </ul>
                <?php //$attributes = array('id' => 'form', 'class' => 'form-horizontal');
                //echo form_open($form, $attributes); ?>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="storeTabContent">
                        <div class="container-fluid">
                            <div class="row-fluid form-horizontal">
                                <div class="control-group">
                                    <label for="store-name"
                                        class="control-label"><?php echo $this->lang->line('entry_store_name'); ?></label>

                                    <div class="controls">
                                        <input type="text" name="store-name" id="store-name"
                                               placeholder="<?php echo $this->lang->line('entry_store_name'); ?>"
                                               value="<?php echo set_value('store-name', $store_name_default); ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="store-id"
                                        class="control-label"><?php echo $this->lang->line('entry_store_id'); ?></label>

                                    <div class="controls">
                                        <input type="text" name="store-id" id="store-id"
                                               placeholder="<?php echo $this->lang->line('entry_store_id'); ?>"
                                               value="<?php echo set_value('store-id', $store_id_default); ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="store-desc"
                                        class="control-label"><?php echo $this->lang->line('entry_store_description'); ?></label>

                                    <div class="controls">
                                    <textarea name="store-desc" rows="4" id="store-desc"
                                              placeholder="<?php echo $this->lang->line('entry_store_description'); ?>"><?php echo set_value('store-desc',
                                            $store_desc_default); ?></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="store-parent"
                                        class="control-label"><?php echo $this->lang->line('entry_store_parent'); ?></label>

                                    <div class="controls">
                                        <input type='hidden' name="store-parent" id="store-parent" style="width:50%;">
                                        <a href="#storeOrganizeTabContent" data-toggle="tab" id="addNewParentLink">Add new parent?</a>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label"
                                           for="store-status"><?php echo $this->lang->line('entry_store_status'); ?></label>

                                    <div class="controls">
                                        <input type="checkbox" name="store-status" id="store-status"
                                               data-handle-width="40" <?php echo isset($store_status_default) && $store_status_default ? ($store_status_default ? "checked='checked'" : '') : set_checkbox('merchant-status',
                                            '', $store_status_default); ?>>
                                    </div>
                                </div>
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
                                    <a href="#formOrganizeModal" id="add" role="button" class="btn btn-info add-organize"
                                       data-toggle="modal"><i class="fa fa-plus"></i> Add</a>
                                </div>
                                <table id="storeOrganizeTable"
                                       data-toolbar="#storeOrganizeToolbar"
                                       data-search="true"
                                       data-show-refresh="true"
                                       data-detail-view="true"
                                       data-detail-formatter="detailFormatter"
                                       data-show-columns="true"
                                       data-minimum-count-columns="2"
                                       data-pagination="true"
                                       data-id-field="_id"
                                       data-page-list="[10, 25, 50, 100, ALL]"
                                       data-show-footer="false"
                                       data-side-pagination="server"
                                       data-url="<?php echo site_url()?>/store_org/organize/"
                                       data-response-handler="responseHandler">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                //echo form_close();
                ?>
            </div>
        </div>
    </div>
</div>

<div id="formOrganizeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formOrganizeModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formOrganizeModalLabel">Organize</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <form class="form-horizontal store-organize-form">
                <div class="row-fluid">
                    <div class="control-group">
                        <label for="store-organize-name"
                               class="control-label"><?php echo $this->lang->line('entry_store_organize_name'); ?></label>

                        <div class="controls">
                            <input type="text" name="store-organize-name" id="store-organize-name"
                                   placeholder="<?php echo $this->lang->line('entry_store_organize_name'); ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="store-organize-desc"
                               class="control-label"><?php echo $this->lang->line('entry_store_organize_desc'); ?></label>

                        <div class="controls">
                            <input type="text" name="store-organize-desc" id="store-organize-desc"
                                   placeholder="<?php echo $this->lang->line('entry_store_organize_desc'); ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="store-organize-parent"
                               class="control-label"><?php echo $this->lang->line('entry_store_organize_parent'); ?></label>

                        <div class="controls">
                            <input type='hidden' name="store-organize-parent" id="store-organize-parent" style="width:80%;">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"
                               for="store-organize-status"><?php echo $this->lang->line('entry_store_organize_status'); ?></label>

                        <div class="controls">
                            <input type="checkbox" name="store-organize-status" id="store-organize-status"
                                   data-handle-width="40" checked>
                        </div>
                    </div>
                </div>
            </form>
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
<script src="<?php echo base_url(); ?>javascript/md5.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/mongoid.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/store_org/app_form.js" type="text/javascript"></script>