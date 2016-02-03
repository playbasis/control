var $detail_panel = $("#detail-panel"),
    $thumbnail_grid = $("#thumbnail-grid"),
    $waitDialog = $("#pleaseWaitDialog");

$("#media-manager-tab")
    .on("click", "a.thumbnail", function (e) {
        e.preventDefault();
        console.log("a.thumbnail clicked!");

        //todo: to check if clicked 'a' is has same 'data-id'
        if ($detail_panel.is(':visible')) {
            $detail_panel.hide().removeClass("col-xs-4");
            $thumbnail_grid.addClass("col-xs-12").removeClass("col-xs-8");
        } else {
            displayThumbnailPreview($(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));

            $thumbnail_grid.removeClass("col-xs-12").addClass("col-xs-8");
            $detail_panel.addClass("col-xs-4").show();
        }
    });

$thumbnail_grid.on("selection-changed", function (event, selection) {
    console.log(selection);
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

    var hidden_detail_panelHTML = $('#hidden_detail_panel').html();

    if (filename || url || filesize) {
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_url}}', 'g'), url);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('src=""', 'g'), 'src="' + lg_thumb + '"');
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{file_name}}', 'g'), filename);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{file_size}}', 'g'), filesize);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_sm_url}}', 'g'), sm_thumb);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_lg_url}}', 'g'), lg_thumb);
    }

    $detail_panel.empty().append(hidden_detail_panelHTML).show();
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

            $("#thumbnail-grid").thumbnailSelectable();

            $waitDialog.modal('hide');
        })
        .always(function () {
            $waitDialog.modal('hide');
        });
}

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

jQuery.fn.extend({
    thumbnailSelectable: function () {
        var thumbnailSelectable = this;
        thumbnailSelectable.getSelection = function () {
            var selection = [];
            thumbnailSelectable.find(".active").each(function (ix, el) {
                selection.push($(el)[0]);
            });
            return selection;
        };
        var selectionChanged = function () {
            $(this).toggleClass("active");
            thumbnailSelectable.trigger("selection-changed", [thumbnailSelectable.getSelection()]);
        };
        $(thumbnailSelectable).find(".thumbnail").on("click", selectionChanged);
        return thumbnailSelectable;
    }
});

$(function () {
    ajaxGetMediaList();

    $('#thumbnail-grid')
        .on('mouseenter', 'a.thumbnail', function () {
            $(this).find('.caption').fadeIn(250);
        })
        .on('mouseleave', 'a.thumbnail', function () {
            $(this).find('.caption').fadeOut(250);
        });
});