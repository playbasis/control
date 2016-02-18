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
                <a class="btn btn-success" onclick="location = baseUrlPath"><i class="fa fa-home"></i></a>
            </div>
        </div>
        <div class="content">
            <div class="tabbable">
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active"><a href="#mediaLibrary"
                                          data-toggle="tab"><?php echo $this->lang->line('tab_library'); ?></a></li>
                    <li><a href="#Upload" data-toggle="tab"><?php echo $this->lang->line('tab_upload'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="mediaLibrary">
                        <div class="row-fluid">
                            <div class="span9 well" style="min-height: 700px">
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
                    <div class="tab-pane fade in" id="Upload">
                        <div class="row-fluid">
                            <div id="dropzone">
                                <?php
                                $attributes = array('id' => 'dz-upload', 'class' => 'dropzone needsclick dz-clickable');
                                echo form_open("mediamanager/upload_s3" ,$attributes);
                                ?>
                                <div class="dz-message needsclick">
                                    <?php echo $this->lang->line('text_dropzone'); ?><br>
                                </div>
                                <?php
                                echo form_close();
                                ?>
                            </div>
                        </div>
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
            <img src="">
        </a>
    </li>
</ul>

<div id="hiddenPreview" class="hide">
    <div class="thumbnail" data-id="{{img_id}}">
        <img src="" style="border: black solid 1px">
        <div class="caption">
            <div class="row-fluid">
                <h4 style="word-wrap: break-word">{{file_name}}</h4>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label for="img_url"><?php echo $this->lang->line('entry_img_url'); ?></label>
                    <textarea id="img_url" rows="3" class="input-block-level" readonly>{{img_url}}</textarea>
                    <button class="btn btn-block" onclick="copyToClipboard('#img_url')"><?php echo $this->lang->line('btn_copy_to_clipboard'); ?></button>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label for="sm_thumb_url"><?php echo $this->lang->line('entry_sm_thumb_url'); ?></label>
                    <textarea id="sm_thumb_url" rows="3" class="input-block-level" readonly>{{img_sm_url}}</textarea>
                    <button class="btn btn-block" onclick="copyToClipboard('#sm_thumb_url')"><?php echo $this->lang->line('btn_copy_to_clipboard'); ?></button>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label for="lg_thumb_url"><?php echo $this->lang->line('entry_lg_thumb_url'); ?></label>
                    <textarea id="lg_thumb_url" rows="3" class="input-block-level" readonly>{{img_lg_url}}</textarea>
                    <button class="btn btn-block" onclick="copyToClipboard('#lg_thumb_url')"><?php echo $this->lang->line('btn_copy_to_clipboard'); ?></button>
                </div>
            </div>
            <div class="row-fluid">
                <p>File Size: {{file_size}} bytes</p>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <button class="delete-media btn btn-danger btn-block"><?php echo $this->lang->line('btn_delete'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>javascript/dropzone/dropzone-4.2.0.min.js"></script>
<link href="<?php echo base_url(); ?>javascript/dropzone/dropzone-4.2.0.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });

    var $thumbnails_grids = $('#thumbnails_grid'),
        $waitDialog = $('#pleaseWaitDialog');

    function createImageThumbnailGrid(imageDataJSONObject) {
        imageDataJSONObject = typeof imageDataJSONObject !== 'undefined' ? imageDataJSONObject : null;

        var imageThumbnailGrid = $('#hiddenThumbs').html();

        if (imageDataJSONObject != null) {
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_id}}', 'g'), imageDataJSONObject._id);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_url}}', 'g'), imageDataJSONObject.url);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('src=""', 'g'), 'src="' + imageDataJSONObject.lg_thumb + '"');
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{file_name}}', 'g'), imageDataJSONObject.file_name);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{file_size}}', 'g'), imageDataJSONObject.file_size);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_sm_url}}', 'g'), imageDataJSONObject.sm_thumb);
            imageThumbnailGrid = imageThumbnailGrid.replace(new RegExp('{{img_lg_url}}', 'g'), imageDataJSONObject.lg_thumb);
        }

        $thumbnails_grids.append(imageThumbnailGrid);
    }

    function displayThumbnailPreview(id, filename, url, filesize, sm_thumb, lg_thumb) {
        filename = typeof filename !== 'undefined' ? filename : null;
        url = typeof url !== 'undefined' ? url : null;
        filesize = typeof filesize !== 'undefined' ? filesize : null;
        sm_thumb = typeof sm_thumb !== 'undefined' ? sm_thumb : null;
        lg_thumb = typeof lg_thumb !== 'undefined' ? lg_thumb : null;

        var thumbPreview = $('#thumb_preview'),
            hiddenPreviewHTML = $('#hiddenPreview').html();

        if (filename || url || filesize) {
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{img_id}}', 'g'), id);
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('{{img_url}}', 'g'), url);
            hiddenPreviewHTML = hiddenPreviewHTML.replace(new RegExp('src=""', 'g'), 'src="' + lg_thumb + '"');
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
                dataType: "json",
                beforeSend: function (xhr) {
                    $waitDialog.modal();
                }
            })
            .done(function (data) {
                $thumbnails_grids.empty();

                $.each(data.rows, function (index, value) {
                    createImageThumbnailGrid(value);
                });
                if (data.rows !== undefined)
                    $(".thumbfix")[0].click();
                $waitDialog.modal('hide');
            })
            .always(function () {
                $waitDialog.modal('hide');
            });
    }
    $(function () {
        ajaxGetMediaList();
    });

    $($thumbnails_grids).on("click", ".thumbfix", function () {
//       console.log($(this).data('id'));
        displayThumbnailPreview($(this).data('id'), $(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));
    });

    $("#thumb_preview").on("click", "button.delete-media", function (e) {
        //console.log('Delete!', $(this).closest('.thumbnail').data('id'))
        var _id = $(this).closest('.thumbnail').data('id');

        if (confirm("Are you sure to remove this media?")) {
            $.ajax({
                    url: baseUrlPath + "mediamanager/media/" + _id,
                    type: "DELETE",
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $waitDialog.modal('show');
                    }
                })
                .done(function (data) {
                    //todo: should create function to remove  thumbnail instead reload
                    ajaxGetMediaList();
                    $waitDialog.modal('hide');
                })
                .fail(function (xhr, status, error) {
                    alert("Deletion Error!")
                })
                .always(function () {
                    $waitDialog.modal('hide');
                });
        }
    });

    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }

    $('a[data-toggle="tab"]').on('show', function (e) {
        if ($(e.target).attr("href") === "#mediaLibrary") {
            ajaxGetMediaList();
        }
    });

    Dropzone.options.dzUpload = {
        maxFilesize: 3, // MB
        addRemoveLinks: true,
        init: function() {
            this.on("sending", function(file, xhr, formData){
                formData.append(csrf_token_name, csrf_token_hash);
            });
        },
    };
</script>