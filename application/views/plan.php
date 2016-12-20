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
                <button class="btn btn-info" onclick="location = baseUrlPath+'plan/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
            <?php
            $attributes = array('id' => 'form');
            echo form_open('plan/delete',$attributes);
            ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_plan_name'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_description'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_trial'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_price'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_display'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($plans) && $plans) { ?>
                        <?php foreach ($plans as $plan) { ?>
                        <tr>
                            <td style="text-align: center;"><?php if ($plan['selected']) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $plan['plan_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $plan['plan_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo $plan['name']; ?></td>
                            <td class="left"><?php echo $plan['description']; ?></td>
	                        <td class="right"><?php echo $plan['trial']; ?></td>
	                        <td class="right"><?php echo $plan['price']; ?></td>
	                        <td class="right"><?php echo ($plan['display']==0)? $this->lang->line('text_not_displayed') : $this->lang->line('text_displayed'); ?></td>
                            <td class="right"><?php echo ($plan['status']==0)? $this->lang->line('text_disabled') : $this->lang->line('text_enabled'); ?></td>
                            <td class="right">
                                [ <?php echo anchor('plan/update/'.$plan['plan_id'], 'Edit'); ?> ]
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="8"><?php echo $this->lang->line('text_no_results'); ?></td>
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
<script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
    delay: 0,
    source: function(request, response) {
        $.ajax({
            url: 'client/autocomplete?filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item.fullname,
                        value: item.client_id,
                        name: item.name
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