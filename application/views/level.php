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
            Number of Levels:
            <input id="template_max" size="4" maxlength="4" type="text" value="<?php echo $all_levels; ?>">
                <!-- Start Level Template -->
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Template<span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                    <li><a class="template_sel" data-template="Curve"
                            data-client_id="<?php echo $client_id; ?>"
                            data-site_id="<?php echo $site_id; ?>" href="#">Curve</a></li>
                        <li><a class="template_sel" data-template="Pokemon"
                            data-client_id="<?php echo $client_id; ?>"
                            data-site_id="<?php echo $site_id; ?>" href="#">Pokemon</a></li>
                        <li><a class="template_sel" data-template="Disgea"
                            data-client_id="<?php echo $client_id; ?>"
                            data-site_id="<?php echo $site_id; ?>" href="#">Disgea</a></li>
                    </ul>
                </div>
                <!-- End Level Template -->
                <button class="btn btn-info" onclick="location = baseUrlPath+'level/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
        <?php if($this->session->flashdata('success')){ ?>
            <div class="content messages half-width">
            <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
            </div>
        <?php }?>
            <?php
            $attributes = array('id' => 'form');
            echo form_open('level/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="right" style="width:50px;"><?php echo $this->lang->line('column_level'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_exp'); ?></td>
                        <td class="left" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($levels) && $levels) { ?>
                        <?php foreach ($levels as $level) { ?>
                        <tr>
                            <td style="text-align: center;"><?php if ($level['selected']) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $level['level_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $level['level_id']; ?>" />
                                <?php } ?></td>
                            <td class="right"><?php echo $level['level']; ?></td>
                            <td class="left"><?php echo $level['title']; ?></td>
                            <td class="right"><?php echo $level['exp']; ?></td>
                            <td class="left"><?php echo $level['status']; ?></td>
                            <td class="right">
                                <?php echo anchor('level/update/'.$level['level_id'], "<i class='fa fa-edit fa-lg''></i>",
                                    array('class'=>'tooltips',
                                        'title' => 'Edit',
                                        'data-placement' => 'top'
                                    )); ?>
                             </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="8"><?php echo str_replace('[img]', '<img src="'.base_url().'image/walk_through/level.png" data-thumb="'.base_url().'image/walk_through/step1.png" alt="" />', $this->lang->line('text_guide_level')); ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php echo form_close();?>
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
<script>
    $(document).ready(function(){
        $(".template_sel").click(function(){
            var id = $(this).data("template"),
                client_id = $(this).data("client_id"),
                site_id = $(this).data("site_id"),
                max_num = $("#template_max").val();
            $("<div></div>").appendTo("body")
                .html(
                    "<div>" +
                    "<span class='text-error'>" +
                    "This will reset all current level.</span>" +
                    "<h6>Are you sure?</h6></div>")
                .dialog({
                    modal: true,
                        title: "Do you want to use " + id,
                        zIndex: 10000,
                        autoOpen: true,
                        width: "auto",
                        resizable: false,
                        buttons: {
                            Yes: function () {
                                 $("<form method='POST' action='index.php/level/useTemplate/" + id + "'>" +
                                   "<input type='text' name='max' value='" + max_num + "' />" +
                                   "<input type='hidden' name='client_id' value='" + client_id + "' />" +
                                   "<input type='hidden' name='site_id' value='" + site_id + "' />" +
                                   "</form>").appendTo('body').submit();
                                $(this).dialog("close");
                            },
                                No: function () {
                                    $(this).dialog("close");
                                }
                        },
                            close: function (event, ui) {
                                $(this).remove();
                            }
                });
        });
    });
</script>
