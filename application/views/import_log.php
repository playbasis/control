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
                <button class="btn btn-info" onclick="location = baseUrlPath+'import'" type="button"><?php echo $this->lang->line('button_back'); ?></button>
            </div>
            <?php //}?>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('import');?>"<?php if (1) { ?>class="selected"<?php }?> style="display: inline;"><?php echo $importData['name']; ?></a>
            </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div class="report-filter">
                <span>
                    <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" />
                </span>
                <span>
                    <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" />
                </span>
                <span>
                    <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                    <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                </span>
            </div>
            <div id="actions">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="center" ><?php echo $this->lang->line('entry_name'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_import_type'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_occur'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_date_execute'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_results'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                       <!-- <td class="center"><input type="text" name="filter_name" value="" style="width:50%;"/></td> -->
                        <td></td>
                        <td></td>
                        <td class="center"><?php echo $this->lang->line('entry_results_name'); ?></td>
                        <td class="center">
                            <!-- <a onclick="clear_filter();" class="button" id="clear_filter"><?php //echo $this->lang->line('button_clear_filter'); ?></a> -->
                            <!-- <a onclick="filter()" class="button"><?php //echo $this->lang->line('button_filter'); ?></a> -->
                        </td>
                    </tr>
                    <?php if (isset($importData) && $importData) { ?>
                        <?php foreach ($importData['logs'] as $cs) { ?>
                            <tr>
                                <td class="center"><?php echo $importData['name']; ?></td>
                                <td class="center"><?php echo $importData['import_type']; ?></td>
                                <td class="center"><?php echo $importData['routine']; ?></td>
                                <td class="center"><?php
                                    if ($cs['date_added'] != null) {
                                        echo datetimeMongotoReadable($cs['date_added']);
                                    }else{
                                        echo null;
                                    }
                                    ?>
                                </td>
                                <td class="center">
                                    <div style="width:100%; max-height:150px; overflow:auto">
                                        <?php
                                            if ($cs['results'] != null) {
                                                if ($cs['results'] == 'Duplicate'){
                                                    echo $this->lang->line('entry_duplicate');
                                                }else {
                                                    foreach ($cs['results'] as $key => $val) {
                                                        echo $key.' => '.$val; ?><br><?php
                                                    }
                                                }
                                            }else{
                                                echo null;
                                            }
                                        ?>
                                    </div>
                                </td>
                                <td></td>
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
        var d = new Date().getTime();
        var import_id = '<?php echo $importData['_id'].""; ?>';
        url = baseUrlPath + 'import/displayLog/' + import_id + '?t' + d;

        var filter_name = $('input[name=\'filter_name\']').attr('value');

        if (filter_name) {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
        }

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name']) && !isset($_GET['date_start']) && !isset($_GET['date_expire'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'import/displayLog/' + '<?php echo $importData['_id'].""; ?>');
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#date-start').datepicker({dateFormat: 'yy-mm-dd'});
        $('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>


