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
                <button class="btn btn-info" onclick="location = baseUrlPath+'import/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
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
                echo form_open('import/delete',$attributes);
                ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;">
                            <input type="checkbox"
                                   onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
                        </td>
                        <td class="center" ><?php echo $this->lang->line('entry_name'); ?></td>
                        <td class="center"
                            ><?php echo $this->lang->line('entry_url'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_port'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_username'); ?></td>
                        <td class="center"
                            ><?php echo $this->lang->line('entry_import_type'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_occur'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="center"><input type="text" name="filter_name" value="" style="width:50%;"/></td>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" class="button"
                               id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>
                    <?php if (isset($importData) && $importData) { ?>
                        <?php foreach ($importData as $cs) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if ($client_id){?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $cs['_id']; ?>" />
                                    <?php }?>
                                </td>
                                <td class="center"><?php echo $cs['name']; ?></td>
                                <td class="left"><?php echo $cs['url']; ?></td>
                                <td class="center"><?php echo $cs['port']; ?></td>
                                <td class="center"><?php echo $cs['user_name']; ?></td>
                                <td class="center"><?php echo $cs['import_type']; ?></td>
                                <td class="center"><?php echo $cs['routine']; ?></td>
                                <td class="center">
                                    <?php if(!$client_id){?>
                                        [ <?php echo anchor('import/update/'.$cs['_id'], 'Edit'); ?> ]
                                    <?php }else{?>
                                        [ <?php echo anchor('import/update/'.$cs['_id'], 'Edit'); ?> ]
                                    <?php }?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="3"><?php echo $this->lang->line('text_no_results'); ?></td>
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

    $('.push_down').live("click", function(){

        $.ajax({
            url : baseUrlPath+'badge/increase_order/'+ $(this).attr('alt'),
            dataType: "json"
        }).done(function(data) {
            console.log("Testing");
            var getListForAjax = 'badge/getListForAjax/';
            var getNum = '<?php echo $this->uri->segment(3);?>';
            if(!getNum){
                getNum = 0;
            }
            $('#actions').load(baseUrlPath+getListForAjax+getNum);
        });


        return false;

    });
</script>

<script type="text/javascript"><!--
    function filter() {
        url = baseUrlPath + 'import';

        var filter_name = $('input[name=\'filter_name\']').attr('value');

        if (filter_name) {
            url += '?filter_name=' + encodeURIComponent(filter_name);
        }

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'import');
    }
</script>

