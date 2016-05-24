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
                <?php if ($tab_status == "cron") { ?>
                    <button class="btn btn-info" onclick="location = baseUrlPath+'import/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                    <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
                <?php }elseif ($tab_status == "adhoc") { ?>
                    <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_execute'); ?></button>
                    <button class="btn btn-info" onclick="location = baseUrlPath+'import/data'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
                <?php }  ?>
            </div>
        </div>


        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('import');?>"      <?php if ($tab_status == "cron") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_cron'); ?></a>
                <a href="<?php echo site_url('import/adhoc');?>" <?php if ($tab_status == "adhoc") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_adhoc'); ?></a>
                <a href="<?php echo site_url('import/log');?>" <?php if ($tab_status == "log") { ?>class="selected"<?php }?> style="display: inline;"><?php echo $this->lang->line('tab_log'); ?></a>
            </div>

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

            <?php
            if(validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>

    <!--tab1 -->
            <?php if ($tab_status == "cron"){ ?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open('import/delete',$attributes);
                ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;">
                            <input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
                        </td>
                        <td class="center" ><?php echo $this->lang->line('entry_name'); ?></td>
                        <td class="center" width="60"><?php echo $this->lang->line('entry_hosttype'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_hostname'); ?></td>
                        <td class="center" width="40"><?php echo $this->lang->line('entry_port'); ?></td>
                        <td class="center" ><?php echo $this->lang->line('entry_username'); ?></td>
                        <td class="center" width="130"><?php echo $this->lang->line('entry_filename'); ?></td>
                        <td class="center" width="130"><?php echo $this->lang->line('column_directory'); ?></td>
                        <td class="center" width="80"><?php echo $this->lang->line('entry_import_type'); ?></td>
                        <td class="center" width="60"><?php echo $this->lang->line('entry_occur'); ?></td>
                        <td class="center" width="60"><?php echo $this->lang->line('entry_execution_time'); ?></td>
                        <td class="center" width="130"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="center"><input type="text" name="filter_import_type" placeholder="by Type" value="<?php echo (isset($_GET['filter_import_type']) && !is_null($_GET['filter_import_type']))?$_GET['filter_import_type']:''; ?>" style="width:70px;"/></td>
                        <td></td>
                        <td></td>
                        <td class="center">
                            <a onclick="clear_filter();" class="button"
                               id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>
                    <?php if (isset($importData) && $importData) { ?>
                        <?php foreach ($importData as $cs) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="selected[]" value="<?php echo $cs['_id']; ?>" />
                                </td>
                                <td class="left"><?php echo isset($cs['name']) ? $cs['name'] : null; ?></td>
                                <td class="center"><?php echo isset($cs['host_type']) ? $cs['host_type'] : null; ?></td>
                                <td class="left"><?php echo isset($cs['host_name']) ? $cs['host_name'] : null; ?></td>
                                <td class="center"><?php echo isset($cs['port']) ? $cs['port'] : null; ?></td>
                                <td class="left"><?php echo isset($cs['user_name']) ? $cs['user_name'] : null; ?></td>
                                <td class="left"><?php echo isset($cs['file_name']) ? $cs['file_name'] : null; ?></td>
                                <td class="left"><?php echo isset($cs['directory']) ? $cs['directory'] : null; ?></td>
                                <td class="left"><?php echo isset($cs['import_type']) ? $cs['import_type'] : null; ?></td>
                                <td class="center"><?php echo isset($cs['routine']) ? $cs['routine'] : null; ?></td>
                                <td class="center"><?php echo isset($cs['execution_time']) ? $cs['execution_time'] : null; ?></td>
                                <td class="center">
                                    <a href="<?php echo site_url('import/update/'.$cs['_id']) ?>" title="Edit" class="tooltips" data-placement="top"><i class="fa fa-edit fa-lg"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="12"><?php echo $this->lang->line('text_no_results'); ?></td>
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

    <!--tab2 -->
            <?php }elseif($tab_status == "adhoc"){?>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open_multipart($form ,$attributes);
                ?>
                    <table class="form">
                        <tr>
                            <td><?php echo $this->lang->line('entry_name') ?>:</td>
                            <td><input type="text" name="name" size="100" value="<?php echo set_value('group')?>" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_import_type'); ?>&nbsp;:</td>
                            <td>
                            <span class="dropdown">
                                <select id="importType" class="span3"  name ="import_type">
                                    <option label="Player"         value="player"      <?php echo $import_type =="player"?"selected":""?>>
                                    <option label="Transaction"    value="transaction" <?php echo $import_type =="transaction"?"selected":""?>>
                                    <option label="Store organize" value="storeorg"    <?php echo $import_type =="storeorg"?"selected":""?>>
                                    <option label="Content"        value="content"     <?php echo $import_type =="content"?"selected":""?>>
                                </select>
                            </span>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>:</td>
                            <td><input id="file" type="file" name="file" size="100" /></td>
                        </tr>
                    </table>
                    <?php
                    echo form_close();?>
            </div>

    <!--tab3 -->
            <?php }elseif($tab_status == "log"){?>
                <div class="report-filter">
                <span>
                    <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" />
                </span>
                <span>
                    <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" />
                </span>

                </div>
                <div id="actions">
                    <table class="list">
                        <thead>
                        <tr>
                            <td class="center" ><?php echo $this->lang->line('entry_name'); ?></td>
                            <td class="center" width="100"><?php echo $this->lang->line('entry_import_method'); ?></td>
                            <td class="center" width="130"><?php echo $this->lang->line('entry_import_type'); ?></td>
                            <td class="center" width="110"><?php echo $this->lang->line('entry_occur'); ?></td>
                            <td class="center" width="150"><?php echo $this->lang->line('entry_date_execute'); ?></td>
                            <td class="center" ><?php echo $this->lang->line('entry_results'); ?></td>
                            <td class="center" ><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="filter">
                            <td></td>
                            <td></td>
                            <td></td>
                            <!-- <td class="center"><input type="text" name="filter_name" value="" style="width:50%;"/></td> -->
                            <td></td>
                            <td></td>
                            <td class="center"><?php echo $this->lang->line('entry_results_log'); ?></td>
                            <td class="center">
                                <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                                <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                            </td>
                        </tr>
                        <?php if (isset($logDatas) && $logDatas) { ?>
                            <?php foreach ($logDatas as $logData) { ?>
                                <tr>
                                    <td class="center"><?php echo isset($logData['name']) ? $logData['name'] : null; ?></td>
                                    <td class="center"><?php echo isset($logData['import_method']) ? $logData['import_method'] : null; ?></td>
                                    <td class="center"><?php echo isset($logData['import_type']) ? $logData['import_type'] : null; ?></td>
                                    <td class="center"><?php echo isset($logData['routine']) ? $logData['routine'] : null; ?></td>
                                    <td class="center"><?php echo isset($logData['date_added']) ? $logData['date_added'] : null; ?></td>
                                    <td class="center"><?php echo isset($logData['result']) ? $logData['result'] : null; ?></td>
                                    <td class="center">
                                        <?php if (isset($logData['log_results']) && !is_null($logData['log_results'])) { ?>


                                            <a href="javascript:void()" onclick="showLog('<?php echo $logData['import_key']?>',<?php echo htmlspecialchars(json_encode($logData['log_results']), ENT_QUOTES, 'UTF-8'); ?>)" title="Show full result" class="tooltips" data-placement="top"><i class="fa fa-file-text-o fa-lg"></i></a>


                                        <?php } ?>

                                        <!--<a data-toggle="modal" href="#formLogModal" title="Show full result" class="tooltips" data-placement="top"><i class="fa fa-file-text-o fa-lg"></i></a>-->
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
                </div>
                <div class="pagination">
                    <ul class='ul_rule_pagination_container'>
                        <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                        <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                        <?php echo $pagination_links; ?>
                    </ul>
                </div>
            <?php }?>
    <!-- end tab3 -->
        </div>
    </div>
</div>

<div id="formLogModal" class="modal hide fade"   tabindex="-1" role="dialog" aria-labelledby="formLogModalLabel"  aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formLogModalLabel">Imported result</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">

            <div class="row-fluid">
                <table id="example" class="display" width="100%"></table>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

    </div>
</div>

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">


<style type="text/css">
    .modal {
        width: 60%;
        margin-left:-30%;
    }
</style>

<script type="text/javascript">

    function showLog(import_key,dataSet){

        $('#example').DataTable( {
            data: dataSet,
            destroy: true,
            columns: [
                { title: "line" },
                { title: import_key },
                { title: "result" }
            ]
        } );
        $('#formLogModal').modal('show');
    }


</script>

<script type="text/javascript"><!--
    function filter() {
        url = baseUrlPath + 'import';

        //tab1 Cron
        <?php if($tab_status == "cron"){?>
            var filter_import_type = $('input[name=\'filter_import_type\']').attr('value');

            url += '?filter_import_type=' + encodeURIComponent(filter_import_type);

        //tab3 Log
        <?php }elseif($tab_status == "log"){?>
            url += '/log';

            var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
            var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

            url += '?filter_date_start=' + encodeURIComponent(filter_date_start)+'&filter_date_end=' + encodeURIComponent(filter_date_end);
        <?php }?>

        location = url;
    }
    //-->
</script>

<script type="text/javascript">

    <?php if($tab_status == "cron"){?>
        <?php if ( !isset($_GET['filter_import_type'])){?>
        $("#clear_filter").hide();
        <?php }else{?>
        $("#clear_filter").show();
        <?php }?>

    <?php }elseif($tab_status == "log"){?>
        <?php if ( !isset($_GET['filter_type'])){?>
        $("#clear_filter").hide();
        <?php }else{?>
        $("#clear_filter").show();
        <?php }?>
    <?php }?>




    function clear_filter() {
        <?php if($tab_status == "cron"){?>
        window.location.replace(baseUrlPath+'import');
        <?php }elseif($tab_status == "log"){?>
        window.location.replace(baseUrlPath+'import/log');
        <?php }?>
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#date-start').datepicker({dateFormat: 'yy-mm-dd'});
        $('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>

