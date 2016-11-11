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
                <button class="btn btn-info" onclick="location = baseUrlPath+'location/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
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
                            <td rowspan="2" width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td rowspan="2" class="center"><?php echo $this->lang->line('column_name'); ?></td>
                            <td rowspan="2" class="center"><?php echo $this->lang->line('column_latitude'); ?></td>
                            <td rowspan="2" class="center"><?php echo $this->lang->line('column_longitude'); ?></td>
                            <td colspan="2" class="center"><?php echo $this->lang->line('column_object'); ?></td>
                            <td rowspan="2" class="center"><?php echo $this->lang->line('column_status'); ?></td>
                            <td rowspan="2" class="center"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td rowspan="2" class="center" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        <tr>
                            <td class="center"><?php echo $this->lang->line('column_object_type'); ?></td>
                            <td class="center"><?php echo $this->lang->line('column_object_name'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($locations) && $locations) { ?>
                            <?php foreach ($locations as $location) { ?>
                            <tr>
                                <td style="text-align: center;"> <input type="checkbox" name="selected[]" value="<?php echo $location['_id']; ?>" /> </td>
                                <td class="left" width="18%"><?php echo $location['name']; ?></td>
                                <td class="left" width="15%"><?php echo $location['latitude']; ?></td>
                                <td class="left" width="15%"><?php echo $location['longitude']; ?></td>
                                <td class="left" width="14%"><?php echo $location['object_type']; ?></td>
                                <td class="left" width="14%"><?php echo $location['object_name']; ?></td>
                                <td class="left" width="9%"><?php echo isset($location['status']) && $location['status'] ? "enable" : "disable"; ?></td>
                                <td class="left" width="11%"><?php echo (((isset($location['tags'])) && $location['tags'])? implode($location['tags'],',') : null); ?></td>
                                <td class="center" width="4%">
                                    <?php
                                        echo anchor('location/update/'.$location['_id'], "<i class='fa fa-edit fa-lg''></i>",
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

