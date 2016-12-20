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
                <button class="btn btn-info" onclick="location = baseUrlPath+'campaign/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
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
                echo form_open('campaign/delete',$attributes);
                ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_image'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="center"><?php echo $this->lang->line('column_date_start'); ?></td>
                        <td class="center"><?php echo $this->lang->line('column_date_end'); ?></td>
                        <td class="center"><?php echo $this->lang->line('column_weight'); ?></td>
                        <td class="center" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($campaigns) && $campaigns) { ?>
                        <?php foreach ($campaigns as $campaign) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if ($client_id){?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $campaign['_id']; ?>" />
                                    <?php }?>
                                </td>
                                <td class="left" width="5%">
                                    <div class="image"><img src="<?php echo isset($campaign['image']) ? $campaign['image'] : ""; ?>" alt=""
                                         id="thumb"
                                         onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image-48.png');"/>
                                    </div>
                                </td>
                                <td class="left" width="40%"><?php echo isset($campaign['name']) && !empty($campaign['name']) ? $campaign['name'] : ""; ?></td>
                                <td class="left" width="10%"><?php echo isset($campaign['date_start']) && !empty($campaign['date_start'])  ? datetimeMongotoReadable($campaign['date_start']) : "N/A"; ?></td>
                                <td class="left" width="10%"><?php echo isset($campaign['date_end']) && !empty($campaign['date_end'])  ? datetimeMongotoReadable($campaign['date_end']) : "N/A"; ?></td>
                                <td class="left" width="10%"><?php echo isset($campaign['weight']) && !empty($campaign['weight']) ? $campaign['weight'] : "0"; ?></td>
                                <td class="center" width="10%">
                                    <?php
                                    echo anchor('campaign/update/'.$campaign['_id'], "<i class='fa fa-edit fa-lg''></i>",
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
                            <td class="center" colspan="7"><?php echo $this->lang->line('text_no_results'); ?></td>
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


