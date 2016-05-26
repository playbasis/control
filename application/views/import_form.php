<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'import'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php } ?>
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
            <?php
            if (validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">', '</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            $attributes = array('id' => 'form');
            echo form_open($form, $attributes);
            ?>
            <div id="tab-general">
                <table class="form">
                    <tbody>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_name'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                   value="<?php echo isset($name) ? $name : set_value('name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_hosttype'); ?>&nbsp;:</td>
                        <td>
                            <span class="dropdown">
                                <select id="hostType" name ="host_type">
                                    <option label="FTP"   value="FTP"   <?php echo isset($host_type) && $host_type == "FTP"?"selected":""?>>
                                    <option label="HTTPS"   value="HTTPS"   <?php echo isset($host_type) && $host_type == "HTTPS"?"selected":""?>>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_hostname'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="host_name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_hostname'); ?>"
                                   value="<?php echo isset($host_name) ? $host_name : set_value('host_name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>&nbsp;</span><?php echo $this->lang->line('entry_port'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="port" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_port'); ?>"
                                   value="<?php echo isset($port) ? $port : set_value('port'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span&nbsp;</span><?php echo $this->lang->line('entry_username'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="user_name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_username'); ?>"
                                   value="<?php echo isset($user_name) ? $user_name : set_value('user_name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>&nbsp;</span><?php echo $this->lang->line('entry_password'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="password" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_password'); ?>"
                                   value="<?php echo isset($password) ? $password : set_value('password'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_filename'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="file_name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_filename'); ?>"
                                   value="<?php echo isset($file_name) ? $file_name : set_value('file_name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>&nbsp;</span><?php echo $this->lang->line('entry_directory'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="directory" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_directory'); ?>"
                                   value="<?php echo isset($directory) ? $directory : set_value('directory'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_import_type'); ?>&nbsp;:</td>
                        <td>
                            <span class="dropdown">
                                <select id="import_type" name ="import_type">
                                    <option label="Player"         value="player"      <?php echo $import_type =="player"?"selected":""?>>
                                    <option label="Transaction"    value="transaction" <?php echo $import_type =="transaction"?"selected":""?>>
                                    <option label="Store organize" value="storeorg"    <?php echo $import_type =="storeorg"?"selected":""?>>
                                    <option label="Content"        value="content"     <?php echo $import_type =="content"?"selected":""?>>
                                </select>
                            </span>
                            <a onclick="showDemo()" title="Show file example" class="tooltips" data-placement="top"><i class="fa fa-file-text-o fa-lg"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_occur'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="number" name="routine" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_occur'); ?>"
                                   value="<?php echo isset($routine) ? $routine : set_value('routine'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>&nbsp;</span><?php echo $this->lang->line('entry_execution_time'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" class="input_execution_time" id="input_execution_time" name="execution_time"
                                   data-format="HH:mm" data-template="HH : mm"
                                   value="<?php echo isset($execution_time) ? $execution_time : set_value('execution_time'); ?>" >
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<div id="formDemoModal" class="modal hide fade"   tabindex="-1" role="dialog" aria-labelledby="formDemoModalLabel"  aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formDemoModalLabel">File demo</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <div class="row-fluid">

                <table  id="example-table" border="2"></table>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary"  onclick='downloadCSV();'><i class="">&nbsp;</i>Download</button>

        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

    </div>
</div>

<style type="text/css">
    .modal {
        width: 60%;
        margin-left:-30%;
    }
</style>

<script src="<?php echo base_url(); ?>javascript/bootstrap/combodate.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/bootstrap/moment.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>javascript/import/d3.v3.min.js"></script>

<script type="text/javascript">

    var csvData, filename;

    function showDemo(){
        var el = document.getElementById('import_type');
        var import_type = el.options[el.selectedIndex].label;
        $('#formDemoModalLabel').html("File demo for "+import_type.fontcolor( 'DB6A6A' )+" importing");
        if(import_type == "Player"){
            filename = "player-example.csv";
        }else if(import_type == "Transaction"){
            filename = "transaction-example.csv";
        }else if(import_type == "Store organize"){
            filename = "store_organize-example.csv";
        }else if(import_type == "Content"){
            filename = "content-example.csv";
        }

        $("#example-table").empty();

        d3.text("<?php echo base_url();?>image/import/"+filename, function(data) {
            var parsedCSV = d3.csv.parseRows(data);
            csvData = data;

            var container = d3.select('#example-table')

                .selectAll("tr")
                .data(parsedCSV).enter()
                .append("tr")

                .selectAll("td")
                .data(function(d) { return d; }).enter()
                .append("td")
                .text(function(d) { return d; });
        });


        $('#formDemoModal').modal('show');
    }

    function downloadCSV() {
        var data, link;

        var csv = csvData;
        if (csv == null) return;

        if (!csv.match(/^data:text\/csv/i)) {
            csv = 'data:text/csv;charset=utf-8,' + csv;
        }
        data = encodeURI(csv);

        link = document.createElement('a');
        link.setAttribute('href', data);
        link.setAttribute('download', filename);
        link.click();
    }

    $(function(){
        $('.input_execution_time').combodate({
            firstItem: 'name', //show 'hour' and 'minute' string at first item of dropdown
            minuteStep: 1
        });
    });
</script>

