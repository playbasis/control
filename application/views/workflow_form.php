<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_confirm'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'workflow'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>

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
            <div id="tab-general">
                <table class="form">

                    <tr>
                        <td></span> <?php echo $this->lang->line('form_email'); ?>:</td>
                        <td><?php echo $requester['email']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_firstname'); ?>:</td>
                        <td><?php echo $requester['first_name']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_lastname'); ?>:</td>
                        <td><?php echo $requester['last_name']; ?></td>
                    </tr>


                </table>
            </div>

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--

    CKEDITOR.replace('description', {
        filebrowserBrowseUrl: baseUrlPath+'filemanager',
        filebrowserImageBrowseUrl: baseUrlPath+'filemanager',
        filebrowserFlashBrowseUrl: baseUrlPath+'filemanager',
        filebrowserUploadUrl: baseUrlPath+'filemanager',
        filebrowserImageUploadUrl: baseUrlPath+'filemanager',
        filebrowserFlashUploadUrl: baseUrlPath+'filemanager'
    });

    //--></script>
<script type="text/javascript"><!--
    function image_upload(field, thumb) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+baseUrlPath+'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 200px; height: 100%;" frameborder="no" scrolling="no"></iframe></div>');

        $('#dialog').dialog({
            title: '<?php echo $this->lang->line('text_image_manager'); ?>',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: baseUrlPath+'filemanager/image?image=' + encodeURIComponent($('#' + field).val()),
                        dataType: 'text',
                        success: function(data) {
                            $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
                        }
                    });
                }
            },
            bgiframe: false,
            width: 200,
            height: 100,
            resizable: false,
            modal: false
        });
    };
    //--></script>
<script type="text/javascript"><!--
    $('#tabs a').tabs();
    $('#languages a').tabs();
    //--></script>