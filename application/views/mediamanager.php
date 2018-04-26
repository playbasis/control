<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>stylesheet/custom/media_manager_tanasak.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10 mediamanager-page">
    <div class="box">
        <div class="heading">
            <div id="headerBar">
                <img style="margin-right:10px;" src="<?php echo base_url(); ?>image/category.png" alt=""/>
                <div class="navigate_name" data-directory="root"><?php echo $heading_title; ?></div>
                <i class="fa fa-sort-asc" style="margin-right: 10px;-webkit-transform: rotate(90deg);-moz-transform: rotate(90deg);-ms-transform: rotate(90deg);-o-transform: rotate(90deg);transform: rotate(90deg);"></i>
                <div class="navigate_name" id="current_folder" data-directory="root">Default</div>
            </div>

            <div class="buttons">
                <a class="btn btn-success" onclick="location = baseUrlPath"><i class="fa fa-home"></i></a>
            </div>
        </div>
        <div class="content">
            <div class="box" style="margin-top: 0px;">
                <div class="heading" style="padding: 5px;padding-right: 10px;background-size: contain;">
                    <h1></h1>
                    <div class="buttons">
                        <button class="btn btn-info" onclick="adddFolder()" type="button">New Folder</button>
                        <button class="btn btn-info" onclick="uploadFile()" type="button">Upload File</button>
                    </div>
                </div>
                <div class="content">
                    <div class="" id="mediaLibrary">
                        <div class="row-fluid">
                            <div class="well" style="min-height: 700px" id="spanto9">
                                <div id="thumbnails_grid" class="thumbnails" style="overflow-y: auto;max-height: 800px;position: relative;transition: all 0.5s">

                                </div>
                            </div>
                            <div class="span3" id="imgDetail" style="display: none;">
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
<div class="modal fade" id="newFolder" data-backdrop="static" data-keyboard="false">
    <div class="modal-header" style="display: flex;align-items: center;">
        <i class="fa fa-folder" style="font-size: 24px;margin-top: 2px;"></i>&nbsp;&nbsp;<h1>New Folder</h1>
    </div>
    <div class="modal-body" style="text-align: center;">
        <div class="" style="display: flex;justify-content: center;">
            <input type="text" id="newFolder_Name" placeholder="Folder Name" style="margin-bottom:0px;width: 60%;">&nbsp;
        </div>
        <span id="minMaxDisplay" style="margin-right: 125px;font-size: 10px;display: none;"></span>
    </div>
    <div class="modal-footer" style="padding: 10px 20px 10px;">
        <button type="button" onclick="createFolder()" class="btn btn-info">Create</button>
        <button type="button" data-dismiss="modal"  class="btn btn-info">Close</button>
    </div>
</div>
<div class="modal fade" id="uploadFile" data-backdrop="static" data-keyboard="false">
    <div class="modal-header" style="display: flex;align-items: center;">
        <i class="fa fa-file-image-o" style="font-size: 24px;margin-top: 2px;"></i>&nbsp;&nbsp;<h1>Upload File</h1>
    </div>
    <div class="modal-body" style="">
        <div class="" id="Upload">
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
    <div class="modal-footer" style="padding: 10px 20px 10px;">
        <button type="button" data-dismiss="modal"  class="btn btn-info">Close</button>
    </div>
</div>
<div class="modal fade" id="deleteOption" data-backdrop="static" data-keyboard="false">
    <div class="modal-header" style="display: flex;align-items: center;">
        <i class="fa fa-trash" style="font-size: 24px;margin-top: 2px;"></i>&nbsp;&nbsp;<h1>Delete Confirmation</h1>
    </div>
    <div class="modal-body" style="text-align: left;">
        <h4 style="margin-left: 60px;">Are you sure you want to delete this folder?</h4><br>
        <div class="delete-answer-list" style="text-align: center;">
            <button type="button" id="box_deleteAll" style="width: 60%;margin-bottom: 10px;" class="delete-box btn btn-info">Yes, with all files inside</button><br>
            <button type="button" id="box_deleteMove" style="width: 60%;margin-bottom: 10px;" class="delete-box btn btn-info">Yes, and all files inside will move to "Default"</button><br>
            <button type="button" data-dismiss="modal" style="width: 60%;margin-bottom: 10px;" class="btn btn-info">No, i do not want to delete</button><br>
        </div>
        <br>
    </div>
</div>
<div id="hiddenThumbs" class="hide">
    <div class="thumbfix span3 draggable li-element" data-id="{{img_id}}" data-file_name="{{file_name}}" data-file_size="{{file_size}}"
        data-url="{{img_url}}" data-sm_url="{{img_sm_url}}" data-lg_url="{{img_lg_url}}">
        <a href="#" class="thumbnail">
            <img src="">
        </a>
    </div>
</div>

<div id="hiddenFolder" class="hide">
    <div class="thumbfix span3 folder-box" id="{{folder_id}}" data-id="{{folder_id}}" data-file_name="{{folder_name}}"">
        <div class="box-hover">
            <i class="fa fa-wrench" onclick="editBoxModal('{{folder_id}}')" style="font-size: 20px;margin-right: 5px;color: white;"></i><i onclick="deleteBox_Modal('{{folder_id}}')" style="font-size: 24px;color: white;margin-right: 5px;" class="fa fa-remove"></i>
        </div>
        {{folder_name}}
    </div>
</div>

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
                <div class="span6">
                    <button class="btn btn-info btn-block" onclick="closePreviewImage()">Close</button>
                </div>
                <div class="span6">
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
    Array.prototype.max = function() {
        return Math.max.apply(null, this);
    };

    Array.prototype.min = function() {
        return Math.min.apply(null, this);
    };
    var myList = document.querySelector('#thumbnails_grid');
    interact('.li-element').draggable({
            // enable inertial throwing
            inertia: true,
            // keep the element within the area of it's parent
            restrict: {
                restriction: "parent",
                endOnly: true,
                elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
            },
            // enable autoScroll
            autoScroll: true,

            // call this function on every dragmove event
            onmove: dragMoveListener,
            // call this function on every dragend event
            onend: function (event) {
                var textEl = event.target.querySelector('p');
                textEl && (textEl.textContent =
                    'moved a distance of '
                    + (Math.sqrt(Math.pow(event.pageX - event.x0, 2) +
                        Math.pow(event.pageY - event.y0, 2) | 0))
                        .toFixed(2) + 'px');
            }
        }).on('doubletap', function (event) {
            $('#spanto9').addClass('span9');
            $('#imgDetail').css('display','block');
            console.log($(event.target).parent().parent())
            displayThumbnailPreview($(event.target).parent().parent().attr('data-id'), $(event.target).parent().parent().attr('data-file_name'), $(event.target).parent().parent().attr('data-url'), $(event.target).parent().parent().attr('data-file_size'), $(event.target).parent().parent().attr('data-sm_url'), $(event.target).parent().parent().attr('data-lg_url'));
        });;
    
    function uploadFile() {
        $('#uploadFile').modal();
    }
        
    function closePreviewImage() {
        $('#spanto9').removeClass('span9');
        $('#imgDetail').css('display','none');
    }
        
    $('#editFolder').on('hidden.bs.modal', function () {
        $('#editFolder_Name').val("");
        $('#editFolder_Order').val("");
        $('#save-box-btn').removeAttr('edit-id');
    });

    $('#newFolder').on('hidden.bs.modal', function () {
        $('#newFolder_Name').val("");
        $('#newFolder > .modal-body > h5').remove();
    });

    function editBoxModal(editID) {
        var temp_folderName = $('#'+editID).text().trim();
        var active_folder = $('.folder-box.active').attr('data-id');
        console.log(active_folder)
        var text = '<input type="text" id="editInput_'+editID+'" value="'+temp_folderName+'" style="width: 60%;">';
        var button = '<div style="position: absolute;left: 0px;bottom: 5px;width: 100%;text-align: center;">' +
                '<button onclick="updateBoxName(\''+editID+'\',\''+active_folder+'\')" style="margin-right: 10px;background-color: aliceblue;outline: none;border-radius: 4px;"><i class="fa fa-check"></i></button>'+
            '<button onclick="closeBoxEdit(\''+editID+'\',\''+temp_folderName+'\')" style="background-color: aliceblue;outline: none;border-radius: 4px;"><i class="fa fa-times"></i></button>'+
            '</div>';
        $('#'+editID).empty();
        $('#'+editID).removeClass('folder-box').addClass('folder-box-temp');
        $('#'+editID).parent().append('<div class="modal-backdrop fade in"></div>');
        $('#'+editID).css('z-index','1050');
        $('#'+editID).append(button);
        $('#'+editID).append(text);
        var editInput = $('#'+editID+'> input');
        editInput.putCursorAtEnd().on("focus", function () {
            editInput.putCursorAtEnd();
        });
    }

    function updateBoxName(editID, active) {
        var name = $('#editInput_'+editID).val();
        console.log(editID+' | '+active)
        $.ajax({
            url: baseUrlPath + "mediamanager/updateFolderName?elementID="+editID+"&new_name="+name,
            dataType: "json",
            success: function(data){
                if (data){
                    ajaxGetMediaList(active);
                    if (editID === active){
                        console.log('true')
                        $('#current_folder').text(name);
                    }
                }
            },
            error: function (xhr, textStatus, errorThrown){
                console.log(errorThrown);
            }
        });
    }

    function closeBoxEdit(editID, oldName) {
        console.log('close')
        var text = '<div class="box-hover">'+
            '<i class="fa fa-wrench" onclick="editBoxModal(\''+editID+'\')" style="font-size: 20px;margin-right: 5px;color: white;"></i><i onclick="deleteBox_Modal(\''+editID+'\')" style="font-size: 24px;color: white;margin-right: 5px;" class="fa fa-remove"></i>'+
            '</div>'+oldName;
        $('#'+editID).removeClass('folder-box-temp').addClass('folder-box');
        $('#inner_grid_folder > .modal-backdrop').remove();
        $('#'+editID).css('z-index','unset');
        $('#'+editID).empty();
        $('#'+editID).append(text);
    }
    
    $('#box_deleteAll').on("click",function () {
        deleteBox($(this).attr('data-id'))
    });

    $('#box_deleteMove').on("click",function () {
        moveFileToDefault($(this).attr('data-id'), "delete")
    });
    
    function deleteBox_Modal(deleteID) {
        $('#deleteOption').modal();
        $('.delete-box').attr('data-id', deleteID)
    }
    
    function deleteBox(deleteID) {
        var active_folder = $('.folder-box.active').attr('data-id');
        console.log("Active: "+active_folder)
        console.log("Remove: "+deleteID)
            $.ajax({
                url: baseUrlPath + "mediamanager/deleteFolder?elementID=" + deleteID,
                dataType: "json",
                success: function (data) {
                    if (data) {
                        if (deleteID === active_folder) {
                            console.log('Removed on actived')
                            ajaxGetMediaList("root");
                            $('#current_folder').text($('#box_root').text());
                        } else {
                            ajaxGetMediaList(active_folder);
                            $('#current_folder').text($('#' + active_folder).text());
                        }
                    }
                    $('#deleteOption').modal('hide');
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
    }

    function moveFileToDefault(deleteID, optionParam) {
        $.ajax({
            url: baseUrlPath + "mediamanager/unsetAllFile?elementID=" + deleteID,
            dataType: "json",
            success: function (data) {
                console.log(data)
                    if (optionParam === "delete"){
                        deleteBox(deleteID);
                        $('#deleteOption').modal('hide');
                    }else{
                        ajaxGetMediaList("root");
                    }
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    interact('.folder-box').dropzone({
        // only accept elements matching this CSS selector
        accept: '.li-element',
        // Require a 75% element overlap for a drop to be possible

        // listen for drop related events:

        ondragenter: function (event) {
            var draggableElement = event.relatedTarget,
                dropzoneElement = event.target;

            // feedback the possibility of a drop
            draggableElement.classList.add('can-drop');
            dropzoneElement.classList.add('drop-target');
        },
        ondragleave: function (event) {
            // remove the drop feedback style
            event.relatedTarget.classList.remove('can-drop');
            event.target.classList.remove('drop-target');
        },
        ondrop: function (event) {
            console.log($(event.relatedTarget).attr('data-id')+" is Drop on: "+$(event.target).text().trim());
            console.log("Folder_ID: "+$(event.target).attr('data-id'));
            ajaxUpdateImageCategory($(event.relatedTarget).attr('data-id'), $(event.target).attr('data-id'));
            $(event.relatedTarget).animate({
                opacity: 0,
                width: "0px",
                marginLeft: "50px",
                marginTop: "50px"
            }, 500, function() {
                $('#spanto9').removeClass('span9');
                $('#imgDetail').css('display','none');
            });
        }
    }).on('click', function (event) {
        console.log($(event.target).hasClass('box-hover'))
        console.log('Get in: '+$(event.target).text().trim()+' | ID: '+$(event.target).attr('data-id'))
        var target = $(event.target);
        if ($(event.target).hasClass('box-hover')){
            target = $(event.target).parent();
        }
        if ($(event.target).hasClass('fa-remove') || $(event.target).hasClass('fa-wrench')){
            target = $(event.target).parent().parent();
        }else{
            $('#current_folder').html(target.text());
            $('#current_folder').attr('data-directory',target.attr('data-id'));
            var data_id = target.attr('data-id') == "root" ? false : target.attr('data-id');
            ajaxGetMediaList(data_id);
        }
    });

    function getMaxOrder() {
        $('#newFolder_Order').val(folder_order.max()+1);
    }

    function ajaxUpdateImageCategory(elementID, dropId){
        $.ajax({
            url: baseUrlPath + "mediamanager/updateImageCategory?elementID="+elementID+"&folder_id="+dropId,
            dataType: "json",
            success: function(data){
                ajaxGetMediaList($('#current_folder').attr('data-directory'),function () {

                });
            },
            error: function (xhr, textStatus, errorThrown){
                console.log(errorThrown);
            }
        });
    }

    function createFolder(){
        var folder_name = $('#newFolder_Name').val() == "" ? "Untitled" : $('#newFolder_Name').val();
        console.log($('#newFolder_Name').val())
        if(validFoldername_duplicate(folder_name) && validEmpty()) {
            $.ajax({
                url: baseUrlPath + "mediamanager/insertFolder?name=" + folder_name,
                dataType: "json",
                success: function (data) {
                    $('#newFolder').modal('hide');
                    $('#newFolder_Name').val("");
                    $('#newFolder > .modal-body > h5').remove();
                    ajaxGetMediaList();
                },
                error: function (xhr, textStatus, errorThrown) {
                    //                window.location.reload(true)
                    console.log(errorThrown);
                }
            });
        }
        else{
            $('#newFolder > .modal-body > h5').remove();
            $('#newFolder > .modal-body').append('<h5 style="color: red;">Invalid input!</h5>')
        }
    }

    function validFoldername_duplicate(name){
        var result = true;
        $('.folder-box').each(function(){
            console.log($(this).attr('data-file_name')+' | '+name)
            if ($(this).attr('data-file_name') === name){
                result = false;
                return false;
            }
        });
        return result;
    }
    
    function validEmpty() {
        if($('#newFolder_Name').val() == undefined || $('#newFolder_Name').val() == ""){
            return false;
        }else{
            return true;
        }
    }
    
    function dragMoveListener (event) {
        var target = event.target,
            // keep the dragged position in the data-x/data-y attributes
            x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
            y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

        // translate the element
        target.style.webkitTransform =
            target.style.transform =
                'translate(' + x + 'px, ' + y + 'px)';

        // update the posiion attributes
        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
    }

    // this is used later in the resizing and gesture demos
    window.dragMoveListener = dragMoveListener;

    var $thumbnails_grids = $('#thumbnails_grid'),
        $waitDialog = $('#pleaseWaitDialog');

    function adddFolder(){
//        console.log(folder_order)
//        $('#minMaxDisplay').html('* Min: '+folder_order.min()+' | Max: '+folder_order.max());
        $('#newFolder').modal();
    }
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

//    var file_gridTop;
//    var file_gridHeight;
//    var folder_height;
//    var main_gridTop;
//    <i id="btn-file-expand" style="float: right;margin-right: 24px;font-size: 24px;cursor: pointer;" class="fa fa-sort-down"></i>
    function ajaxGetMediaList(criteria,callback) {
        var text = '<div id="thumbnails_grid_folder" style="position: sticky;background-color: #f6f6f6;top: 0px;" class="thumbnails">\
                    <div><h5 style="float: left">Folders</h5><div style="clear: both;"><hr style="width: 99%;"></div></div><br>\
                        <div id="inner_grid_folder" style="max-height: 250px;overflow-y: auto;transition: all 0.5s;" class="thumbnails">\
                            <div class="span" style="display: none;"></div>\
                            <div class="thumbfix span3 folder-box" id="box_root" data-id="root" data-file_name="Default">Default</div>\
                        </div>\
                        <br><br><div><h5 style="float: left">Files</h5><div style="clear: both;"><hr style="width: 99%;"></div></div><br>\
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
                $thumbnails_grids.empty();
                $thumbnails_grids.prepend(text);
//                $('#btn-folder-expand').on("click",function () {
//                    if ($(this).hasClass('fa-sort-down')){
//                        console.log('Down')
//                        $(this).removeClass('fa-sort-down').addClass('fa-sort-asc')
//                        $(this).css('marginTop','8px');
//                        $('#inner_grid_folder').css('height','0px')
//                        if ($('#btn-file-expand').hasClass('fa-sort-asc')){
//                            setTimeout(function () {
//                                console.log('has'+$('#thumbnails_grid_folder').height())
//                                $('#thumbnails_grid').css('height',$('#thumbnails_grid_folder').height())
//                            },500)
//                        }
//                    }else{
//                        console.log('Up')
//                        $(this).removeClass('fa-sort-asc').addClass('fa-sort-down')
//                        $(this).css('marginTop','0px');
//                        $('#inner_grid_folder').css('height',folder_height)
//                        if ($('#btn-file-expand').hasClass('fa-sort-asc')){
//
//                                console.log('has'+main_gridTop)
//                                $('#thumbnails_grid').css('height',main_gridTop)
//
//                        }
//                    }
//                });
//
//                $('#btn-file-expand').on("click",function () {
//                    file_gridTop = $('#thumbnails_grid_folder').height();
//                    console.log(file_gridTop+' | '+file_gridHeight)
//                    if ($(this).hasClass('fa-sort-down')){
//                        console.log('Down')
//                        $(this).removeClass('fa-sort-down').addClass('fa-sort-asc')
//                        $(this).css('marginTop','8px');
//                        $('#thumbnails_grid').css('height',file_gridTop).css('overflow','hidden')
//                    }else{
//                        console.log('Up')
//                        $(this).removeClass('fa-sort-asc').addClass('fa-sort-down')
//                        $(this).css('marginTop','0px');
//                        $('#thumbnails_grid').css('height',file_gridHeight).css('overflow','auto')
//                    }
//                });
                console.log(data.rows.length)
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
                    var elmnt = document.getElementById(criteria);
                    elmnt.scrollIntoView(false);
                }
                if (data.rows.length > 0){
                    $.each(data.rows, function (index, value) {
                        createImageThumbnailGrid(value);
                    });
                }else{
                    $thumbnails_grids.append('<h2 style="text-align: center;font-weight: normal;color: inherit;">Empty folder</h2>');
                }
//                setTimeout(function () {
//                    main_gridTop = $('#thumbnails_grid_folder').height();
//                    file_gridHeight = $('#thumbnails_grid').height();
//                    folder_height = $('#inner_grid_folder').height();
//                    console.log(file_gridHeight+' |||||| '+folder_height)
//                    $('#inner_grid_folder').css('height',folder_height)
//                    $('#thumbnails_grid').css('height',file_gridHeight)
//                },500)
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
    
    $(function () {
        ajaxGetMediaList();
    });

    $('.navigate_name').on("click",function () {
        console.log($(this).data('directory'));
        ajaxGetMediaList();
    });

    $("#thumb_preview").on("click", "button.delete-media", function (e) {
//        console.log('Delete!', $(this).closest('.thumbnail').data('id'))
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
    jQuery.fn.putCursorAtEnd = function() {

        return this.each(function() {

            // Cache references
            var $el = $(this),
                el = this;

            // Only focus if input isn't already
            if (!$el.is(":focus")) {
                $el.focus();
            }

            // If this function exists... (IE 9+)
            if (el.setSelectionRange) {

                // Double the length because Opera is inconsistent about whether a carriage return is one character or two.
                var len = $el.val().length * 2;

                // Timeout seems to be required for Blink
                setTimeout(function() {
                    el.setSelectionRange(len, len);
                }, 1);

            } else {

                // As a fallback, replace the contents with itself
                // Doesn't work in Chrome, but Chrome supports setSelectionRange
                $el.val($el.val());

            }

            // Scroll to the bottom, in case we're in a tall textarea
            // (Necessary for Firefox and Chrome)
            this.scrollTop = 999999;

        });

    };
</script>