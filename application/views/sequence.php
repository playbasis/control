<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <?php //if($user_group_id != $setting_group_id){ ?>
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'sequence/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
            <?php //}?>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open($form,$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td width="35%" class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <td width="35%" class="left"><?php echo $this->lang->line('column_file'); ?></td>
                            <td width="20%" class="left"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td width="10%" class="center" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($sequences) && $sequences) { ?>
                            <?php foreach ($sequences as $sequence) { ?>
                            <tr>
                                <td style="text-align: center;"> <input type="checkbox" name="selected[]" value="<?php echo $sequence['_id']; ?>" /> </td>
                                <td class="left"><?php echo $sequence['name']; ?></td>
                                <td class="left"><?php echo $sequence['file_name']; ?></td>
                                <td class="left" ><?php echo (((isset($sequence['tags'])) && $sequence['tags'])? implode(',',$sequence['tags']) : null); ?></td>
                                <td class="center" >
                                    <?php
                                        echo anchor('sequence/update/'.$sequence['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                                    array('class'=>'tooltips',
                                                        'title' => 'Edit',
                                                        'data-placement' => 'top'
                                                    ));
                                    ?>
                                </td>
                            </tr>
                                <?php } ?>
                            <?php } else { ?>
                        <tr>
                            <td class="center" colspan="9"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    Pace.on("done", function () {
    $(".cover").fadeOut(1000);
    });
</script>

