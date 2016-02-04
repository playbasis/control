var $detail_panel = $("#detail-panel"),
    $thumbnail_grid = $("#thumbnail-grid"),
    $waitDialog = $("#pleaseWaitDialog"),
    $selected_wrapper = $(".selected-wrapper");

$("#media-manager-tab")
    .on("click", "a.thumbnail", function (e) {
        e.preventDefault();
        console.log("a.thumbnail clicked!");

        if ($detail_panel.is(':visible')) {
            if ($(this).data('id') === $detail_panel.children(".thumbnail").data('id')) {
                $detail_panel.hide().removeClass("col-xs-4");
                $thumbnail_grid.addClass("col-xs-12").removeClass("col-xs-8");
            } else {
                displayThumbnailPreview($(this).data('id'), $(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));
            }
        } else {
            displayThumbnailPreview($(this).data('id'), $(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));

            $thumbnail_grid.removeClass("col-xs-12").addClass("col-xs-8");
            $detail_panel.addClass("col-xs-4").show();

        }

        displaySelectedThumbnail($(this).data('id'), $(this).data('sm_url'));
    })
    .on("click", "a#clear-selected", function(e){
        $selected_wrapper.empty()
    })
    .on("click", "button#select-photo", function(e){
        $selected_wrapper.empty()
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

function displayThumbnailPreview(id, filename, url, filesize, sm_thumb, lg_thumb) {
    id = typeof id !== 'undefined' ? id : null;
    filename = typeof filename !== 'undefined' ? filename : null;
    url = typeof url !== 'undefined' ? url : null;
    filesize = typeof filesize !== 'undefined' ? filesize : null;
    sm_thumb = typeof sm_thumb !== 'undefined' ? sm_thumb : null;
    lg_thumb = typeof lg_thumb !== 'undefined' ? lg_thumb : null;

    var hidden_detail_panelHTML = $('#hidden_detail_panel').html();

    if (filename || url || filesize || lg_thumb) {
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_id}}', 'g'), id);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_url}}', 'g'), url);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('src=""', 'g'), 'src="' + lg_thumb + '"');
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{file_name}}', 'g'), filename);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{file_size}}', 'g'), filesize);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_sm_url}}', 'g'), sm_thumb);
        hidden_detail_panelHTML = hidden_detail_panelHTML.replace(new RegExp('{{img_lg_url}}', 'g'), lg_thumb);

        $detail_panel.empty().append(hidden_detail_panelHTML).show();
    }
}

function displaySelectedThumbnail(id, sm_thumb){
    id = typeof id !== 'undefined' ? id : null;
    sm_thumb = typeof sm_thumb !== 'undefined' ? sm_thumb : null;

    var hidden_selected_thumbHTML = $('#hiddenSelectedThumbs').html();

    if (id || sm_thumb) {
        hidden_selected_thumbHTML = hidden_selected_thumbHTML.replace(new RegExp('{{img_id}}', 'g'), id);
        hidden_selected_thumbHTML = hidden_selected_thumbHTML.replace(new RegExp('src=""', 'g'), 'src="' + sm_thumb + '"');

        $selected_wrapper.empty().append(hidden_selected_thumbHTML).show();
    }
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
            $thumbnail_grid.empty();

            $.each(data.rows, function (index, value) {
                createImageThumbnailGrid(value);
            });

            //$("#thumbnail-grid").thumbnailSelectable();

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

//jQuery.fn.extend({
//    thumbnailSelectable: function () {
//        var thumbnailSelectable = this;
//        thumbnailSelectable.getSelection = function () {
//            var selection = [];
//            thumbnailSelectable.find(".active").each(function (ix, el) {
//                selection.push($(el)[0]);
//            });
//            return selection;
//        };
//        var selectionChanged = function () {
//            $(this).toggleClass("active");
//            thumbnailSelectable.trigger("selection-changed", [thumbnailSelectable.getSelection()]);
//        };
//        $(thumbnailSelectable).find(".thumbnail").on("click", selectionChanged);
//        return thumbnailSelectable;
//    }
//});

$(function () {
    ajaxGetMediaList();

    //$('#thumbnail-grid')
    //    .on('mouseenter', 'a.thumbnail', function () {
    //        $(this).find('.caption').fadeIn(250);
    //    })
    //    .on('mouseleave', 'a.thumbnail', function () {
    //        $(this).find('.caption').fadeOut(250);
    //    });
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    if($(e.target).attr("href") === "#media-manager-tab"){
        ajaxGetMediaList();
    }
});