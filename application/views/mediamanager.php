<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<style>
    li.thumbfix.span12 + li {
        margin-left: 0;
    }

    li.thumbfix.span6:nth-child(2n + 3) {
        margin-left: 0;
    }

    li.thumbfix.span4:nth-child(3n + 4) {
        margin-left: 0;
    }

    li.thumbfix.span3:nth-child(4n + 5) {
        margin-left: 0;
    }

    li.thumbfix.span2:nth-child(6n + 7) {
        margin-left: 0;
    }

    li.thumbfix.span1:nth-child(12n + 13) {
        margin-left: 0;
    }
</style>
<div id="content" class="span10 mediamanager-page">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <a class="btn btn-primary"
                   onclick="image_upload('image', 'thumb');"><i class="fa fa-upload"></i>&nbsp;<?php echo $this->lang->line('text_upload'); ?></a>
                <a class="btn btn-success" onclick="location = baseUrlPath"><i class="fa fa-home"></i></a>
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
            <div class="row-fluid">
                <div class="span9 well" style="min-height: 500px">
                    <ul id="thumbnails_grid" class="thumbnails">

                    </ul>
                </div>
                <div class="span3">
                    <div class="row-fluid">
                        <ul class="thumbnails">
                            <li class="span12 hide" id="thumb_preview"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
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

<ul id="hiddenThumbs" class="hide">
    <li class="thumbfix span3" data-id="{{img_id}}" data-file_name="{{file_name}}" data-file_size="{{file_size}}"
        data-url="{{img_url}}" data-sm_url="{{img_sm_url}}" data-lg_url="{{img_lg_url}}">
        <a href="#" class="thumbnail">
            <img src="" style="width: 200px; height: 180px;">
        </a>
    </li>
</ul>

<div id="hiddenPreview" class="hide">
    <div class="thumbnail">
        <img src="" style="width: 300px; height: 200px; border: black solid 1px">
        <div class="caption">
            <div class="row-fluid">
                <h3 style="word-wrap: break-word">{{file_name}}</h3>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label for="img_url">Image URL:</label>
                    <textarea id="img_url" rows="3" class="input-block-level" readonly>{{img_url}}</textarea>
                    <button class="btn btn-block" onclick="copyToClipboard('#img_url')">Copy to clipboard</button>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label for="sm_thumb_url">Small thumbnail URL:</label>
                    <textarea id="sm_thumb_url" rows="3" class="input-block-level" readonly>{{img_sm_url}}</textarea>
                    <button class="btn btn-block" onclick="copyToClipboard('#sm_thumb_url')">Copy to clipboard</button>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label for="lg_thumb_url">Large thumbnail URL:</label>
                    <textarea id="lg_thumb_url" rows="3" class="input-block-level" readonly>{{img_lg_url}}</textarea>
                    <button class="btn btn-block" onclick="copyToClipboard('#lg_thumb_url')">Copy to clipboard</button>
                </div>
            </div>
            <div class="row-fluid">
                <p>File Size: {{file_size}} bytes</p>
            </div>
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

<script type="text/javascript">
    function image_upload(field, thumb) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0 0 0;"><iframe src="' + baseUrlPath + 'filemanager'+ '" style="padding:0; margin: 0; display: block; width: 200px; height: 100%;" frameborder="no" scrolling="no"></iframe></div>');

        $('#dialog').dialog({
            title: '<?php echo $this->lang->line('text_image_manager'); ?>',
            close: function (event, ui) {
                $thumbnails_grids.empty();
                ajaxGetMediaList();
            },
            bgiframe: false,
            width: 200,
            height: 100,
            resizable: false,
            modal: false
        });
    }

    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    Pace.on("done", function(){
        $(".cover").fadeOut(1000);
    });

    var $thumbnails_grids = $('#thumbnails_grid');

    function createImageThumbnailGrid(imageDataJSONObject) {
        imageDataJSONObject = typeof imageDataJSONObject !== 'undefined' ? imageDataJSONObject : null;

        var imageThumbnailGrid = $('#hiddenThumbs').html();

        if (imageDataJSONObject != null) {
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_id}}', 'g'), imageDataJSONObject._id);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_url}}', 'g'), imageDataJSONObject.url);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('src=""', 'g'), 'src="' + imageDataJSONObject.url + '"');
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{file_name}}', 'g'), imageDataJSONObject.file_name);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{file_size}}', 'g'), imageDataJSONObject.file_size);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_sm_url}}', 'g'), imageDataJSONObject.sm_thumb);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_lg_url}}', 'g'), imageDataJSONObject.lg_thumb);
        }

        $thumbnails_grids.append(imageThumbnailGrid);
    }

    function displayThumbnailPreview(filename,url,filesize,sm_thumb,lg_thumb) {
        filename = typeof filename !== 'undefined' ? filename : null;
        url = typeof url !== 'undefined' ? url : null;
        filesize = typeof filesize !== 'undefined' ? filesize : null;
        sm_thumb = typeof sm_thumb !== 'undefined' ? sm_thumb : null;
        lg_thumb = typeof lg_thumb !== 'undefined' ? lg_thumb : null;

        var thumbPreview = $('#thumb_preview'),
            hiddenPreviewHTML = $('#hiddenPreview').html();

        if (filename || url || filesize) {
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{img_url}}', 'g'), url);
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('src=""', 'g'), 'src="' + url + '"');
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{file_name}}', 'g'), filename);
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{file_size}}', 'g'), filesize);
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{img_sm_url}}', 'g'), sm_thumb);
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{img_lg_url}}', 'g'), lg_thumb);
        }

        thumbPreview.empty().append(hiddenPreviewHTML).show();
    }

    function ajaxGetMediaList() {
        $.ajax({
                url: baseUrlPath + "mediamanager/media",
                dataType: "json"
            })
            .done(function (data) {
//                if (console && console.log) {
//                    console.log("Sample of data:", data);
//                }

                $.each(data.rows, function (index, value) {
                    createImageThumbnailGrid(value);
                });
            });
    }
    $(function () {
        ajaxGetMediaList();
    });

    $($thumbnails_grids).on("click",".thumbfix", function(){
//       console.log($(this).data('id'));
        displayThumbnailPreview($(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));
    });

    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }
</script>