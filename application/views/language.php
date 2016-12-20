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
                <button class="btn btn-info" onclick="location = baseUrlPath+'language/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" type="button" id="delete"><?php echo $this->lang->line('button_delete'); ?></button>
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
                            <td class="center"><?php echo $this->lang->line('column_language'); ?></td>
                            <td class="center"><?php echo $this->lang->line('column_abbreviation'); ?></td>
                            <td class="center"><?php echo $this->lang->line('column_status'); ?></td>
                            <td class="center"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td class="center" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td class="left" width="30%">English (default)</td>
                                <td class="left" width="20%">en</td>
                                <td class="left" width="20%">enable</td>
                                <td class="left" width="20%">english</td>
                                <td class="center" width="10%"></td>
                            </tr>

                            <?php if (isset($languages) && $languages) { ?>
                            <?php foreach ($languages as $language) { ?>
                            <tr>
                                <td style="text-align: center;" > <input type="checkbox" name="selected[]" value="<?php echo $language['_id']; ?>" /> </td>
                                <td class="left" width="30%"><?php echo $language['language']; ?></td>
                                <td class="left" width="20%"><?php echo $language['abbreviation']; ?></td>
                                <td class="left" width="20%"><?php echo isset($language['status']) && $language['status'] ? "enable" : "disable"; ?></td>
                                <td class="left" width="20%"><?php echo (((isset($language['tags'])) && $language['tags'])? implode(',',$language['tags']) : null); ?></td>
                                <td class="center" width="10%">
                                    <?php
                                        echo anchor('language/update/'.$language['_id'], "<i class='fa fa-edit fa-lg''></i>",
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
                            <td class="center" colspan="6"><?php echo $this->lang->line('text_no_results'); ?></td>
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


<script>

    $("#delete").click(function(e){
        e.preventDefault();

        var checkboxes = document.querySelectorAll('input[name="selected[]"]:checked'), values = [];
        Array.prototype.forEach.call(checkboxes, function(el) {
            values.push(el.value);
        });
        if(values != '') {
            if(confirm("Are you sure to delete this language?")){
                $("#action").val("delete");
                //alert($("#action").val());
                $("#form").submit();
            }
        }else{
            alert("Please select language(s) to delete");
        }
    });

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });

</script>

