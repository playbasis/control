var $detail_panel = $("#detail-panel"),
    $thumbnail_grid = $("#thumbnail-grid"),
    $waitDialog = $("#pleaseWaitDialog");

$("#media-manager-tab")
    .on("click", "a.thumbnail", function (e) {
        e.preventDefault();
        console.log("a.thumbnail clicked!");

        //todo: to check if clicked 'a' is has same 'data-id'
        if ($detail_panel.is(':visible')) {
            $detail_panel.hide().removeClass("col-xs-3");
            $thumbnail_grid.addClass("col-xs-12").removeClass("col-xs-9");
        } else {
            $thumbnail_grid.removeClass("col-xs-12").addClass("col-xs-9");
            $detail_panel.addClass("col-xs-3").show();
        }
    });

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

    $thumbnail_grid.append(imageThumbnailGrid);
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
            url: baseUrlPath + "mediamanager2/media",
            dataType: "json",
            beforeSend: function (xhr) {
                $waitDialog.modal('show');
            }
        })
        .done(function (data) {
            $.each(data.rows, function (index, value) {
                createImageThumbnailGrid(value);
            });
            $waitDialog.modal('hide');
        })
        .always(function () {
            $waitDialog.modal('hide');
        });
}

$(function () {
    ajaxGetMediaList();
});