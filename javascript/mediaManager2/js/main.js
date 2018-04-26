var $detail_panel = $("#detail-panel"),
    $thumbnail_grid = $("#thumbnail-grid"),
    $waitDialog = $("#pleaseWaitDialog"),
    $selected_wrapper = $(".selected-wrapper"),
    $footer_div = $(".footer-div");

function clearSelected() {
    $selected_wrapper.empty();
    if ($footer_div.find('.thumbnail').length >= 0) {
        $('button#select-photo').addClass('disabled');
    }
}
function hideDetailPanel() {
    $detail_panel.hide().removeClass("col-xs-4");
    $thumbnail_grid.addClass("col-xs-12").removeClass("col-xs-8");
}
function showDetailPanel() {
    $thumbnail_grid.removeClass("col-xs-12").addClass("col-xs-8");
    $detail_panel.addClass("col-xs-4").show();
}
$("#media-manager-tab")
    .on("click", "a.thumbnail", function (e) {
        e.preventDefault();
        //console.log("a.thumbnail clicked!");

        if ($detail_panel.is(':visible')) {
            if ($(this).data('id') === $detail_panel.children(".thumbnail").data('id')) {
                hideDetailPanel();
            } else {
                displayThumbnailPreview($(this).data('id'), $(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));
            }
        } else {
            displayThumbnailPreview($(this).data('id'), $(this).data('file_name'), $(this).data('url'), $(this).data('file_size'), $(this).data('sm_url'), $(this).data('lg_url'));

            showDetailPanel();
        }

        displaySelectedThumbnail($(this).data('id'), $(this).data('sm_url'), $(this).data('file_name'), $(this).data('url'));
        if ($footer_div.find('.thumbnail').length >= 0) {
            $('button#select-photo').removeClass('disabled');
        }
    })
    .on("click", "a#clear-selected", function (e) {
        clearSelected();
    })
    .on("click", "button#select-photo", function (e) {
        if ($footer_div.find('.thumbnail') !== 0) {
            var file_name = $footer_div.find('.thumbnail').data('file_name'),
                url = $footer_div.find('.thumbnail').data('url');
            if (parentField !== '') {
                parent.$(parentField).val('data/' + file_name);
            }

            if (funcNum !== ''){
                window.opener.CKEDITOR.tools.callFunction(funcNum, url);
                window.close();
            }

            parent.$('#mmModal').modal('hide');
        }
    })
    .on("click", "button.delete-media", function (e) {
        //console.log('Delete!', $(this).closest('.thumbnail').data('id'))
        var _id = $(this).closest('.thumbnail').data('id');
        bootbox.confirm("Are you sure to remove this media?", function (result) {
            if (result) {
                $.ajax({
                        url: baseUrlPath + "mediamanager/media/" + _id,
                        method: "DELETE",
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $waitDialog.modal('show');
                        }
                    })
                    .done(function (data) {
                        clearSelected();
                        //todo: should create function to remove  thumbnail instead reload
                        ajaxGetMediaList();
                        hideDetailPanel();
                        $waitDialog.modal('hide');
                        bootbox.alert("Media Deleted!");
                    })
                    .fail(function(xhr,status,error){
                        bootbox.alert("Remove Error!")
                    })
                    .always(function () {
                        $waitDialog.modal('hide');
                    });
            }
        });
    });

//$thumbnail_grid.on("selection-changed", function (event, selection) {
//    console.log(selection);
//});

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

    if (id && filename && url && filesize && sm_thumb && lg_thumb) {
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

function displaySelectedThumbnail(id, sm_thumb, filename, url) {
    id = typeof id !== 'undefined' ? id : null;
    sm_thumb = typeof sm_thumb !== 'undefined' ? sm_thumb : null;
    filename = typeof filename !== 'undefined' ? filename : null;
    url = typeof url !== 'undefined' ? url : null;

    var hidden_selected_thumbHTML = $('#hiddenSelectedThumbs').html();

    if (id && sm_thumb && filename && url) {
        hidden_selected_thumbHTML = hidden_selected_thumbHTML.replace(new RegExp('{{img_id}}', 'g'), id);
        hidden_selected_thumbHTML = hidden_selected_thumbHTML.replace(new RegExp('src=""', 'g'), 'src="' + sm_thumb + '"');
        hidden_selected_thumbHTML = hidden_selected_thumbHTML.replace(new RegExp('{{file_name}}', 'g'), filename);
        hidden_selected_thumbHTML = hidden_selected_thumbHTML.replace(new RegExp('{{img_url}}', 'g'), url);

        $selected_wrapper.empty().append(hidden_selected_thumbHTML).show();
    }
}
function createFolderGrid(folderDataJSONObject) {
    folderDataJSONObject = typeof folderDataJSONObject !== 'undefined' ? folderDataJSONObject : null;

    var folder = $('#hiddenFolder').html();
    if (folderDataJSONObject != null) {
        folder = folder.replace(new RegExp('{{folder_id}}', 'g'), folderDataJSONObject._id);
        folder = folder.replace(new RegExp('{{folder_name}}', 'g'), folderDataJSONObject.folder_name);
    }
    $('#inner_grid_folder').append(folder);
    $( ".folder-box" ).hover(
        function() {
            $(this).children('.box-hover').css('top','0%');
        }, function() {
            $(this).children('.box-hover').css('top','-30%');
        }
    );
}

function ajaxGetMediaList(criteria,callback) {
    var text = '<div id="thumbnails_grid_folder" style="z-index:10;position: sticky;background-color: #ffffff;top: 0px;" class="thumbnails">\
                    <h5>Folders</h5><br>\
                        <div id="inner_grid_folder" style="max-height: 250px;overflow-y: auto;padding: 4px;" class="thumbnails">\
                            <div class="span" style="display: none;"></div>\
                            <div class="thumbfix span3 folder-box" id="box_root" data-id="root" data-file_name="Default">Default</div>\
                        </div>\
                        <br><br><h5>Files</h5><br>\
                    </div>';
    if (criteria == undefined || criteria == "root"){
        criteria = false;
    }
    console.log("Criteria: "+criteria);

    $.ajax({
        url: baseUrlPath + "mediamanager/media?folder="+criteria,
        dataType: "json",
        beforeSend: function (xhr) {
//                    $waitDialog.modal();
        }
    })
        .done(function (data) {
            $thumbnail_grid.empty();
            $thumbnail_grid.prepend(text);
            if (data.folders.length > 0){
                $.each(data.folders, function (index, value) {
                    createFolderGrid(value);
                });
            }
            if (!criteria){
                $('#box_root').addClass('active');
            }else{
                $('.folder-box').removeClass('active');
                $('#'+criteria).addClass('active');
            }
            $.each(data.rows, function (index, value) {
                createImageThumbnailGrid(value);
            });
            if (data.rows !== undefined)
//                    $(".thumbfix")[0].click();
                $waitDialog.modal('hide');
            if (callback != undefined){
                callback(criteria);
            }
        })
        .always(function () {
            $waitDialog.modal('hide');
        });
}

interact('.folder-box').on('click', function (event) {
    console.log($(event.target).hasClass('box-hover'))
    console.log('Get in: '+$(event.target).text().trim()+' | ID: '+$(event.target).attr('data-id'))
    var target = $(event.target);
    if ($(event.target).hasClass('box-hover')){
        target = $(event.target).parent();
    }
    $('#current_folder').html(target.text());
    $('#current_folder').attr('data-directory',target.attr('data-id'));
    var data_id = target.attr('data-id') == "root" ? false : target.attr('data-id');
    ajaxGetMediaList(data_id,function (data) {

    });
});
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

    var clipboard = new Clipboard(".btn[data-clipboard-target]");

    //$('#thumbnail-grid')
    //    .on('mouseenter', 'a.thumbnail', function () {
    //        $(this).find('.caption').fadeIn(250);
    //    })
    //    .on('mouseleave', 'a.thumbnail', function () {
    //        $(this).find('.caption').fadeOut(250);
    //    });
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    if ($(e.target).attr("href") === "#media-manager-tab") {
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