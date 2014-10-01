<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'account/add_site'" type="button"><?php echo $this->lang->line('button_back'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_next'); ?></button>
            </div><!-- .buttons -->
        </div><!-- .heading -->
        <div class="content">
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
            <?php if(validation_errors() || isset($message)){?>
                <div class="content messages half-width">
                    <?php echo validation_errors('<div class="warning">', '</div>');?>
                    <?php if (isset($message) && $message){?>
                        <div class="warning"><?php echo $message;?></div>
                    <?php }?>
                </div>
            <?php }?>
            <?php $attributes = array('id' => 'form');?>
            <?php echo form_open($form ,$attributes); ?>
                <div id="tab-general">
                    <table class="form">
                        <tr>
                            <td><span class="required">*</span> <?php echo $this->lang->line('entry_plan'); ?>:</td>
                            <td>
                                <select name="plan_id">
                                    <option value=""><?php echo $this->lang->line('text_select'); ?></option>
                                    <?php if ($plan_data) { ?>
                                        <?php foreach ($plan_data as $each) { ?>
                                            <?php if ($plan_id==$each['_id']) { ?>
                                                <option value="<?php echo $each['_id']; ?>" selected="selected"><?php echo $each['name']; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $each['_id']; ?>"><?php echo $each['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php echo form_close(); ?>
        </div><!-- .content -->
        1 [2] 3
    </div><!-- .box -->
</div><!-- #content .span10 -->
