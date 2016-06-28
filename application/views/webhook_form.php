<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'webhook'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('limit_reached')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php }?>

            <div id="tabs" class="htabs">
                <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
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
            $attributes = array('id' => 'form');
            echo form_open($form ,$attributes);
            ?>
            <div id="tab-general">
                <table class="form">
                    <tr>
                        <td><span class="required">*</span> <?php echo $this->lang->line('entry_name'); ?>:</td>
                        <td><input type="text" name="name" size="100" value="<?php echo isset($name) ? $name :  set_value('name'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> <?php echo $this->lang->line('entry_url'); ?>:</td>
                        <td><input type="text" name="url" size="100" value="<?php echo isset($url) ? $url :  set_value('url'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><span class="text"></span> <?php echo $this->lang->line('entry_body'); ?>:</td>
                        <td>

                            <div class="span8 http-body-wrapper">
                                <!--<textarea name="body" id="body" rows="4" style="min-width:400px;"><?php echo isset($body) ? $body : set_value('body'); ?></textarea>-->

                                        <table class="table table-bordered table-hover table-sortable" id="tab_logic">
                                            <thead>
                                            <tr >
                                                <th class="text-center">
                                                    Key
                                                </th>
                                                <th class="text-center">
                                                    Value
                                                </th>

                                                <th class="text-center" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr id='htmlBody0' data-id="0" class="hidden">
                                                <td data-name="body_key">
                                                    <input type="text" name='body_key[]'  placeholder='Key' class="form-control"/>
                                                </td>
                                                <td data-name="body_value">
                                                    <input type="text" name='body_value[]' placeholder='Value' class="form-control"/>
                                                </td>

                                                <td data-name="del">
                                                    <a href="javascript:void(0)" data-name="del0" class='btn btn-danger row-remove'><i class="fa fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <?php
                                            $body_key = isset($body) && is_array($body) ? array_keys($body) : array();
                                            $body_value = isset($body) && is_array($body) ? array_values($body) : array();
                                            ?>
                                            <?php foreach( $body_key as $index => $key_name): ?>
                                                <?php $rowIndex = $index+1; ?>
                                                <tr id='htmlBody<?php echo $rowIndex; ?>' data-id="<?php echo $rowIndex; ?>" >
                                                    <td data-name="body_key">
                                                        <input type="text" name='body_key[]' value="<?php echo $key_name ?>" placeholder='Key' class="form-control"/>
                                                    </td>
                                                    <td data-name="body_value">
                                                        <input type="text" name='body_value[]' value="<?php echo $body_value[$index] ?>" placeholder='Value' class="form-control"/>
                                                    </td>

                                                    <td data-name="del">
                                                        <a href="javascript:void(0)" data-name="del<?php echo $rowIndex; ?>" class='btn btn-danger row-remove'><i class="fa fa-times"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                <a id="add_row" class="btn btn-default pull-right">Add Row</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_sort_order'); ?>:</td>
                        <td><input type="text" name="sort_order" value="<?php echo isset($sort_order) ? $sort_order : set_value('sort_order'); ?>" size="1" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_status'); ?></td>
                        <td><select name="status">
                                <?php if ($status) { ?>
                                    <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                                    <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                </table>

            </div>

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $("#add_row").on("click", function() {
            // Dynamic Rows Code
            // Get max row id and set new id
            var newid = 0;
            $.each($("#tab_logic tr"), function() {
                if (parseInt($(this).data("id")) > newid) {
                    newid = parseInt($(this).data("id"));
                }
            });
            newid++;

            var tr = $("<tr></tr>", {
                id: "htmlBody"+newid,
                "data-id": newid
            });

            // loop through each td and create new elements with name of newid
            $.each($("#tab_logic tbody tr:nth(0) td"), function() {
                var cur_td = $(this);

                var children = cur_td.children();

                // add new td and element if it has a nane
                if ($(this).data("name") != undefined) {
                    var td = $("<td></td>", {
                        "data-name": $(cur_td).data("name")
                    });

                    var c = $(cur_td).find($(children[0]).prop('tagName')).clone().val("");
                    c.attr("name", $(cur_td).data("name") +'[]');
                    c.appendTo($(td));
                    td.appendTo($(tr));
                } else {
                    var td = $("<td></td>", {
                        'text': $('#tab_logic tr').length
                    }).appendTo($(tr));
                }
            });

            // add the new row
            $(tr).appendTo($('#tab_logic'));

            $(tr).find("td a.row-remove").on("click", function() {
                $(this).closest("tr").remove();
            });
        });

        $("#add_row").trigger("click");

        $('.http-body-wrapper').find("td a.row-remove").on("click", function() {
            $(this).closest("tr").remove();
        });

    });

</script>