<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'location'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">

            <?php
            if(validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            $attributes = array('id' => 'form');
            echo form_open($form ,$attributes);
            ?>

            <table class="form">
                <tr>
                    <td>
                        <span class="required">*</span> <?php echo $this->lang->line('entry_name'); ?>:
                    </td>
                    <td>
                        <input type="text" name="name" size="100"
                               placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                               value="<?php echo isset($name) ? $name : set_value('name'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="required">*</span> <?php echo $this->lang->line('column_latitude'); ?>:
                    </td>
                    <td>
                        <input type="text" name="latitude" size="100"
                               placeholder="<?php echo $this->lang->line('column_latitude'); ?>"
                               value="<?php echo isset($latitude) ? $latitude : set_value('latitude'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="required">*</span> <?php echo $this->lang->line('column_longitude'); ?>:
                    </td>
                    <td>
                        <input type="text" name="longitude" size="100"
                               placeholder="<?php echo $this->lang->line('column_longitude'); ?>"
                               value="<?php echo isset($longitude) ? $longitude : set_value('longitude'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="required">*</span><?php echo $this->lang->line('entry_object'); ?>:
                    </td>
                    <td>
                        <select name="object_type" id="object_type">
                            <option value="item" <?php if (isset($object_type) && $object_type == "item") { ?>selected="selected"<?php }?>><?php echo $this->lang->line('option_item'); ?></option>
                            <option value="store" <?php if (isset($object_type) && $object_type == "store") { ?>selected="selected"<?php }?>><?php echo $this->lang->line('option_store'); ?></option>
                        </select>
                        <input type="hidden" name="update_id" id="update_id" value="<?php if (isset($object_id) && $object_id){ echo $object_id; }else{ echo ""; }?>" />
                        <select name="object_id" id="object_id">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->lang->line('entry_image'); ?> :
                    </td>
                    <td>
                        <div class="image">
                            <img width="100" height="100" src="<?php echo isset($image)? S3_IMAGE.$image : S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="location_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                            <input type="hidden" name="image" value="<?php echo isset($image)? $image : "no_image.jpg"; ?>" id="location_image" />
                            <br />
                            <a onclick="image_upload('#location_image', 'location_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a onclick="$('#location_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#location_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                    <td><select name="status" >
                            <?php if ($status) { ?>
                                <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                            <?php } else { ?>
                                <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                            <?php } ?>
                        </select></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('entry_tags'); ?>:</td>
                    <td>
                        <input type="text" class="tags" name="tags" value="<?php echo !empty($tags) ? implode(',',$tags) : set_value('tags'); ?>"
                               size="5" class="tooltips" data-placement="right" title="Tag(s) input"/>
                    </td>
                </tr>
            </table>

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">


<script type="text/javascript">

    function setOption(object_id) {

        $.ajax(baseUrlPath + "location/object/"+$("#object_type").val() , {
                dataType: "json",
                beforeSend: function (xhr) {
                    object_id.disabled = true;
                    while (object_id.options.length) {
                        object_id.remove(0);
                    }
                }
            })
            .done(function (data) {
                data.forEach(function(object) {
                    var opt = document.createElement('option');
                    opt.value = object._id;
                    opt.innerHTML = object.name;
                    if(update_id.value != "" && object._id == update_id.value){
                        opt.selected = true;
                    }
                    object_id.appendChild(opt);

                });
            })
            .always(function () {
                object_id.disabled = false;
            });

    }

    $(document).ready(function(){
        var object_id = document.getElementById('object_id');
        var update_id = document.getElementById('update_id');
        setOption(object_id);

        $("#object_type")
            .on("change", function (e) {
                setOption(object_id);
            });

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

    function image_upload(field, thumb) {
        var $mm_Modal = $('#mmModal');

        if ($mm_Modal.length !== 0) $mm_Modal.remove();

        var frameSrc = baseUrlPath + "mediamanager/dialog?field=" + encodeURIComponent(field);
        var mm_modal_str = "";
        mm_modal_str += "<div id=\"mmModal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\">";
        mm_modal_str += " <div class=\"modal-body\">";
        mm_modal_str += "   <iframe src=\"" + frameSrc + "\" style=\"position:absolute; zoom:0.60\" width=\"99.6%\" height=\"99.6%\" frameborder=\"0\"><\/iframe>";
        mm_modal_str += " <\/div>";
        mm_modal_str += "<\/div>";

        $mm_Modal = $(mm_modal_str);
        $('#page-render').append($mm_Modal);

        $mm_Modal.modal('show');

        $mm_Modal.on('hidden', function () {
            var $field = $(field);
            if ($field.attr('value')) {
                $.ajax({
                    url: baseUrlPath + 'mediamanager/image?image=' + encodeURIComponent($field.val()),
                    dataType: 'text',
                    success: function (data) {
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                    }
                });
            }
        });
    }


    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>
