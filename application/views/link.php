<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'link'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>

        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if ($this->session->flashdata("fail")){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata("fail"); ?></div>
                </div>
            <?php }?>

            <?php if ($error_warning) { ?>
                <div class="warning"><?php echo $error_warning; ?></div>
            <?php }

            $attributes = array('id' => 'form');
            echo form_open_multipart($form ,$attributes);
            ?>
            <div id="actions">
                <table class="form">
                    <tr>
                        <td><?php echo $this->lang->line('entry_type'); ?>:</td>
                        <td>
                            <select class="span5" name="type" >
                                <option value="branch.io" <?php if (isset($link_type) && $link_type == "branch.io")  { ?>selected<?php }?>>branch.io</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_key'); ?>:</td>
                        <td> <input type="text" name="key" class="span5" id="key"
                                    placeholder="<?php echo $this->lang->line('placeholder_branch_key'); ?>"
                                    value="<?php echo (isset($link_key)) ? $link_key : ''; ?>"/>
                        </td>
                    </tr>
                </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->

        </div>
    </div>
</div>
