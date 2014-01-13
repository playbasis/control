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
                <button class="btn btn-info" onclick="location =  baseUrlPath+'client/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
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
            echo form_open('client/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <!-- <td class="left" style="width:72px;"><?php //echo $this->lang->line('column_image'); ?></td> -->
                        <td class="left"><?php echo $this->lang->line('column_company_name'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_domain'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <!-- <td></td> -->
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <td class="right"><a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a></td>
                    </tr>
                    <?php if (isset($clients)) { ?>
                        <?php foreach ($clients as $client) { ?>
                        <tr>
                            <td style="text-align: center;"><?php if ($client['selected']) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $client['client_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $client['client_id']; ?>" />
                                <?php } ?></td>
                            <!-- <td class="left"><img src="<?php //echo $client['image']; ?>" alt="" id="thumb" /></td> -->
                            <td class="left"><?php echo ($client['company']) ? $client['company'] : $client['first_name']." ".$client['last_name'] ?></td>
                            <td class="right"><?php echo $client['quantity']; ?></td>
                            <td class="right"><?php echo ($client['status'])? "Enabled" : "Disabled"; ?></td>
                            <td class="right">
                                [ <?php echo anchor('client/update/'.$client['client_id'], 'Edit'); ?> ]
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            echo form_close();
            ?>

            <div class="pagination"><?php echo $pagination_links; ?></div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--
function filter() {
    url = baseUrlPath+'client';

    var filter_name = $('input[name=\'filter_name\']').attr('value');

    if (filter_name) {
        url += '?filter_name=' + encodeURIComponent(filter_name);
    }

    location = url;
}
//--></script>

<script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
    delay: 0,
    source: function(request, response) {
        $.ajax({
            url: baseUrlPath+'client/autocomplete?filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item.company,
                        value: item.client_id,
                        name: item.company
                    }
                }));
            }
        });
    },
    select: function(event, ui) {
        $('input[name=\'filter_name\']').val(ui.item.name);

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
});
//--></script>