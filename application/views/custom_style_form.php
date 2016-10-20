<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10 custom_style-page">
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
                                          data-toggle="tab"><?php echo $this->lang->line('style'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="storeTabContent">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="storeToolbar">
                                    <button id="remove" class="btn btn-danger" disabled>
                                        <i class="fa fa-remove"></i> Delete
                                    </button>
                                    <a href="#formModal" id="add" role="button" class="btn btn-info add-node"
                                       data-toggle="modal"><i class="fa fa-plus"></i> Add</a>
                                </div>
                                <table id="storeTable"
                                       data-toolbar="#storeToolbar"
                                       data-search="true"
                                       data-show-refresh="true"
                                       data-show-columns="true"
                                       data-minimum-count-columns="2"
                                       data-pagination="true"
                                       data-id-field="_id"
                                       data-page-list="[10, 25, 50, 100, ALL]"
                                       data-side-pagination="server"
                                       data-url="<?php echo site_url(); ?>/custom_style/style/"
                                       data-response-handler="responseHandler">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="formModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
     aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formModalLabel">Node</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal style-form')); ?>
            <div class="row-fluid">
                <input type="hidden" name="style-id" id="style-id">

                <div class="control-group">
                    <label for="style-name"
                           class="control-label"><span class="required">*</span><?php echo $this->lang->line('name'); ?></label>

                    <div class="controls">
                        <input type="text" name="style-name" id="style-name"
                               placeholder="<?php echo $this->lang->line('name'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="style-key"
                           class="control-label"><span class="required">*</span><?php echo $this->lang->line('key'); ?></label>

                    <div class="controls">
                        <input type="text" name="style-key" id="style-key"
                               placeholder="<?php echo $this->lang->line('key'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="style-value"
                           class="control-label"><?php echo $this->lang->line('value'); ?></label>

                    <div class="controls">
                            <textarea name="style-value" id="style-value" rows="5"
                                      placeholder="<?php echo $this->lang->line('value') ?>"></textarea>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
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

<div class="modal hide" id="errorDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
        <h1>Error</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-times"></i>&nbsp;<span id="error_message"></span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="error-dialog-close">Close</button>
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
<script src="<?php echo base_url(); ?>javascript/custom_style/app_form.js" type="text/javascript"></script>

<script type="text/javascript">
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    Pace.on("done", function(){
        $(".cover").fadeOut(1000);
    });
</script>