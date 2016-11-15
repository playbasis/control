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
                <button class="btn btn-info" onclick="location = baseUrlPath+'language'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
                        <span class="required">*</span> <?php echo $this->lang->line('entry_language'); ?>:
                    </td>
                    <td>
                        <input type="text" name="language" size="100"
                               placeholder="<?php echo $this->lang->line('entry_language'); ?>"
                               value="<?php echo isset($language) ? $language : set_value('language'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="required">*</span> <?php echo $this->lang->line('entry_abbreviation'); ?>:
                    </td>
                    <td>
                        <input type="text" name="abbreviation" size="100"
                               placeholder="<?php echo $this->lang->line('entry_abbreviation'); ?>"
                               value="<?php echo isset($abbreviation) ? $abbreviation : set_value('abbreviation'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                    <td><select name="status">
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
                        <input type="text" class="tags" name="tags" value="<?php echo !empty($tags) ? implode($tags,',') : set_value('tags'); ?>"
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
    $(document).ready(function(){

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>
