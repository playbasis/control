<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Media Manager 2</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?php echo base_url(); ?>javascript/mediaManager2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>javascript/mediaManager2/css/main.css">
    <script src="<?php echo base_url(); ?>javascript/mediaManager2/js/vendor/modernizr-2.8.3.min.js"></script>
    <link href="<?php echo base_url(); ?>javascript/pace/flash.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<div class="container-fluid media-manager2-wrapper">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li class="active"><a href="#">Insert Media</a></li>
            </ul>
            <ul class="nav nav-sidebar">
                <li class="disabled"><a href="#">Insert from URL <span class="text-danger">(Soon)</span></a></li>
            </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="page-header"><?php echo $this->lang->line('heading_title'); ?></h3>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation"><a href="#upload-tab" role="tab" data-toggle="tab">Upload Files</a></li>
                <li role="presentation" class="active"><a href="#media-manager-tab" role="tab" data-toggle="tab">Media
                        Manager</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade" id="upload-tab">
                    <div id="dropzone" style="padding-top: 20px">
                        <?php
                        $attributes = array('id' => 'dz-upload', 'class' => 'dropzone needsclick dz-clickable');
                        echo form_open(base_url() ."mediamanager2/upload_s3" ,$attributes);
                        ?>
                            <div class="dz-message needsclick">
                                Drop files here or click to upload.<br>
                            </div>
                        <?php
                        echo form_close();
                        ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade in active" id="media-manager-tab">
                    <div class="row" style="padding-top: 20px">
                        <div class="col-xs-12" id="thumbnail-grid"></div>

                        <div class="collapse" id="detail-panel"></div>

                    </div>
                    <div class="row">
                        <div class="footer-div col-md-offset-2 col-sm-offset-3">
                            <div class="selected-wrapper col-xs-8"></div>
                            <div class="select-btn-wrapper col-xs-4">
                                <button class="btn btn-primary pull-right disabled" id="select-photo">Insert</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hiddenThumbs" class="hidden">
    <div class="col-xs-6 col-md-3 thumbnail-wrapper">
        <a class="thumbnail" href="#" data-id="{{img_id}}" data-file_name="{{file_name}}" data-file_size="{{file_size}}"
           data-url="{{img_url}}" data-sm_url="{{img_sm_url}}" data-lg_url="{{img_lg_url}}">
            <img src="" style="display: block;">
        </a>
    </div>
</div>

<div id="hiddenSelectedThumbs" class="hidden">
    <div class="col-xs-12">
        <div class="col-xs-6 col-sm-3">
            <span>1 Selected</span>
            <span><a href="#" id="clear-selected" class="text-danger">Clear</a></span>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="thumbnail active" href="#" data-id="{{img_id}}" data-file_name="{{file_name}}" style="width: 40px; height: 40px; color: #23527c">
                <img src="" style="display: block;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pleaseWaitDialog" tabindex="-1"
     role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span class="glyphicon glyphicon-time"></span>&nbsp;Please Wait
                </h4>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-info progress-bar-striped active" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hidden_detail_panel" class="hidden">
    <div class="thumbnail" data-id="{{img_id}}">
        <img src="" style="border: black solid 1px">
        <div class="caption">
            <h3 style="word-wrap: break-word">{{file_name}}</h3>

            <div style="padding-bottom: 10px">
                <label for="img_url">Image URL:</label>
                <div class="input-group">
                    <input type="text" id="img_url" class="form-control" readonly
                           value="{{img_url}}">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button"
                                onclick="copyToClipboard('#img_url')"><span
                                class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div style="padding-bottom: 10px">
                <label for="sm_thumb_url">Small thumbnail URL:</label>
                <div class="input-group">
                    <input type="text" id="sm_thumb_url" class="form-control" readonly
                           value="{{img_sm_url}}">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button"
                                onclick="copyToClipboard('#sm_thumb_url')"><span
                                class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div style="padding-bottom: 10px">
                <label for="lg_thumb_url">Large thumbnail URL:</label>

                <div class="input-group">
                    <input type="text" id="lg_thumb_url" class="form-control" readonly
                           value="{{img_lg_url}}">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button"
                                onclick="copyToClipboard('#lg_thumb_url')"><span
                                class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div style="padding-bottom: 10px">File Size: {{file_size}} bytes</div>
        </div>
    </div>
</div>

<!--<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>-->
<script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>javascript/mediaManager2/js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>-->
<script>window.Pace || document.write('<script src="<?php echo base_url(); ?>javascript/pace/pace.min.js"><\/script>')</script>
<script type="text/javascript">
    var baseUrlPath = "<?php echo base_url();?><?php echo (index_page() == '') ? '' : index_page() . "/" ?>";
    var parentField = "<?php echo ($field == '') ? '' : $field ?>";
    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>
<script src="<?php echo base_url(); ?>javascript/mediaManager2/js/vendor/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>javascript/mediaManager2/js/plugins.js"></script>
<script src="<?php echo base_url(); ?>javascript/mediaManager2/js/main.js"></script>

</body>
</html>