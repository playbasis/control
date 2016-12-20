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
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <!-- <td class="left" style="width:72px;"><?php //echo $this->lang->line('column_image'); ?></td> -->
                        <td class="left" style="width:60px;"><?php echo $this->lang->line('column_first_name'); ?></td>
                        <td class="right" style="width:70px;"><?php echo $this->lang->line('column_last_name'); ?></td>
                        <td class="right" style="width:60px;"><?php echo $this->lang->line('column_company_name'); ?></td>
                        <td class="right" style="width:110px;"><?php echo $this->lang->line('column_email'); ?></td>
                        <td class="right" style="width:40px;"><?php echo $this->lang->line('column_plan_name'); ?></td>
                        <td class="right" style="width:20px;"><?php echo $this->lang->line('column_app'); ?></td>
                        <td class="right" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:50px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <!-- <td></td> -->
                        <td>Email: <input type="text" name="filter_name" value="" style="width:80%;" /></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" id="clear_filter" class="button"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
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
                            <td class="left"><?php echo $client['first_name']; ?></td>
                            <td class="right"><?php echo $client['last_name']; ?></td>
                            <td class="right"><?php echo $client['company']; ?></td>
                            <td class="right"><?php echo $client['email']; ?></td>
                            <td class="right"><?php echo $client['plan_name']; ?></td>
                            <td class="right"><?php echo $client['quantity']; ?></td>
                            <td class="right"><?php echo ($client['status'])? "Enabled" : "Disabled"; ?></td>
                            <td class="right">
                                <?php
                                echo anchor('client/update/'.$client['client_id'], "<i class='fa fa-edit fa-lg''></i>",
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
                        <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
                    </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php echo form_close(); ?>

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
                        label: item.email,
                        value: item.client_id,
                        name: item.email
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

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
        $("#clear_filter").hide();
    <?php }else{?>
        $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        window.location.replace(baseUrlPath+'client');
    }
</script>