<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick=fromcheck() type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'data'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>

        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('data');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_import'); ?></a>
            </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
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
            echo form_open_multipart($form ,$attributes);
            ?>
            <div id="actions">
                <table class="form">
                    <tr>
                        <td><?php echo $this->lang->line('entry_name') ?>:</td>
                        <td><input type="text" name="name" size="100" value="<?php echo set_value('group')?>" /></td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>:</td>
                        <td><input id="file" type="file" name="file" size="100" /></td>
                    </tr>
                </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->

        </div>
    </div>
</div>

<script>
    function fromcheck(){
        var file = document.getElementById('file').files[0];

        if(file){
            if(file.size < 2097152) { // 2MB (this size is in bytes)
                //Submit form
                $('#form').submit();
            } else {
                //Prevent default and display error
                $(".content").prepend('<div class="content messages half-width"><div class="warning"><?php echo $this->lang->line('error_file_too_large'); ?></div> </div>');
            }
        }else{
            $(".content").prepend('<div class="content messages half-width"><div class="warning"><?php echo $this->lang->line('error_file'); ?></div> </div>');
        }
    }
</script>