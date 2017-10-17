<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info"  onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'reward_control/custom_reward'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
            echo form_open_multipart($form ,$attributes);
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
                        <span class="required">*</span>  <?php  ?><?php echo $this->lang->line('entry_file'); ?>:
                    </td>
                    <td>
                        <?php echo isset($file_name) && $file_name ? $file_name."&nbsp;&nbsp;<a onclick=\"downloadFile()\" title=\"Download files\" class=\"tooltips\" data-placement=\"top\"><i class=\"fa fa-file-text-o fa-lg\"></i></a>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ''; ?><input type="file" id="file" name="file" size="20" />
                    </td>
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

<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/import/d3.v3.min.js"></script>

<script type="text/javascript">

    function downloadFile(){
        file_name = "<?php echo $file_name?>";
        file_id = "<?php echo $file_id?>";

        location = baseUrlPath+'reward_control/getCustomRewardFile?file_name='+file_name+'&file_id='+file_id;

    }

    $(".tags").select2({
        width: 'resolve',
        tags: true,
        tokenSeparators: [',', ' ']
    });

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>
