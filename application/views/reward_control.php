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
            <div class="buttons">
                <button class="btn btn-info" onclick="location = baseUrlPath+'reward_control/insert/'+ '<?php echo $tab_status ?>'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('reward_control');?>" <?php if ($tab_status == "sequence_reward") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_sequence_reward'); ?></a>
                <a href="<?php echo site_url('reward_control/custom_reward');?>" <?php if ($tab_status == "custom_reward") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_custom_reward'); ?></a>
                <a href="<?php echo site_url('reward_control/custom_param_condition');?>" <?php if ($tab_status == "custom_param_condition") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_custom_param_condtion'); ?></a>
            </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if($this->session->flashdata('warning')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('warning'); ?></div>
                </div>
            <?php }?>
            <?php if ($tab_status == "sequence_reward"){ ?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open($form,$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left" style="min-width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="left"><?php echo $this->lang->line('column_file'); ?></td>
                            <td class="left" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td class="center" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($data_list) && $data_list) { ?>
                            <?php foreach ($data_list as $item) { ?>
                            <tr>
                                <td style="text-align: center;"> <input type="checkbox" name="selected[]" value="<?php echo $item['_id']; ?>" /> </td>
                                <td class="left"><?php echo $item['name']; ?></td>
                                <td class="left"><?php echo $item['file_name']; ?></td>
                                <td class="right" style="word-wrap:break-word;"><?php echo (isset($item['tags']) && $item['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $item['tags']).'</span>' : null); ?></td>
                                <td class="center" >
                                    <?php
                                        echo anchor('reward_control/update/'.$tab_status.'/'.$item['_id'], "<i class='fa fa-edit fa-lg''></i>",
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
                            <td class="center" colspan="5"><?php echo $this->lang->line('text_no_results'); ?></td>
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
            <?php } elseif($tab_status == "custom_reward") { ?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open($form,$attributes);
                ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left" style="min-width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_file'); ?></td>
                        <td class="left" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
                        <td class="center" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($data_list) && $data_list) { ?>
                        <?php foreach ($data_list as $item) { ?>
                            <tr>
                                <td style="text-align: center;"> <input type="checkbox" name="selected[]" value="<?php echo $item['_id']; ?>" /> </td>
                                <td class="left"><?php echo $item['name']; ?></td>
                                <td class="left"><?php echo $item['file_name']; ?></td>
                                <td class="right" style="word-wrap:break-word;"><?php echo (isset($item['tags']) && $item['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $item['tags']).'</span>' : null); ?></td>
                                <td class="center" >
                                    <?php
                                    echo anchor('reward_control/update/'.$tab_status.'/'.$item['_id'], "<i class='fa fa-edit fa-lg''></i>",
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
                            <td class="center" colspan="5"><?php echo $this->lang->line('text_no_results'); ?></td>
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
            <?php } elseif ($tab_status == "custom_param_condition"){ ?>
                <div id="actions">
                    <?php
                    $attributes = array('id' => 'form');
                    echo form_open($form,$attributes);
                    ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left" style="min-width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                            <td class="left"><?php echo $this->lang->line('column_file'); ?></td>
                            <td class="left" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td class="center" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($data_list) && $data_list) { ?>
                            <?php foreach ($data_list as $item) { ?>
                                <tr>
                                    <td style="text-align: center;"> <input type="checkbox" name="selected[]" value="<?php echo $item['_id']; ?>" /> </td>
                                    <td class="left"><?php echo $item['name']; ?></td>
                                    <td class="left"><?php echo $item['file_name']; ?></td>
                                    <td class="right" style="word-wrap:break-word;"><?php echo (isset($item['tags']) && $item['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $item['tags']).'</span>' : null); ?></td>
                                    <td class="center" >
                                        <?php
                                        echo anchor('reward_control/update/'.$tab_status.'/'.$item['_id'], "<i class='fa fa-edit fa-lg''></i>",
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
                                <td class="center" colspan="5"><?php echo $this->lang->line('text_no_results'); ?></td>
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
            <?php }?>
        </div>
    </div>
</div>

<script type="text/javascript">
    Pace.on("done", function () {
    $(".cover").fadeOut(1000);
    });
</script>

