<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <?php //if($user_group_id != $setting_group_id){ ?>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_spin'); ?></button>
            </div>
            <?php //}?>
        </div>
        <div class="content" >
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div class="report-filter">
                <span>
                        <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo isset($filter_date_start) ? $filter_date_start : null; ?>" id="date-start" size="12" />
                </span>
                <span>
                        <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo isset($filter_date_end) ? $filter_date_end : null; ?>" id="date-end" size="12" />
                </span>
                <span>
                    <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                </span>
            </div>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form');
                echo form_open('spin/action',$attributes);
                ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td class="center" width="1%"><?php echo $this->lang->line('column_number'); ?></td>
                        <td width="5%" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left" width="40%"><?php echo $this->lang->line('column_player_id'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_eligible'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_out_standing_spin'); ?></td>
                    </tr>
                    </thead>
                    <tbody style="overflow-y:scroll">
                    <?php if (isset($report) && $report) { ?>
                        <?php foreach ($report as $index => $r) { ?>
                            <tr>
                                <td class="center" width="1%"><?php echo $index +1 ?></td>
                                <td style="text-align: center;"><input type="checkbox" name="selected[]" value="<?php echo $r['_id']; ?>" /></td>
                                <td class="left" width="40%"><?php echo isset($r['_id']) ? $r['_id'] : ""; ?></td>
                                <td class="left" width="10%"><?php echo isset($r['n']) ? $r['n'] : 0; ?></td>
                                <input type="hidden" name="out_standing_spin[]" value="<?php echo $r['out_standing_spin']; ?>" />
                                <td class="left" width="10%"><?php echo isset($r['out_standing_spin']) ? $r['out_standing_spin'] : 0 ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="7"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php
                echo form_close();?>
            </div><!-- #actions -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#date-start').datepicker({dateFormat: 'yy-mm-dd'});
        $('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
    });

    function filter() {
        var d = new Date().getTime();
        url = baseUrlPath+'spin?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_end=' + encodeURIComponent(filter_date_end);
        }
        location = url;
    }
</script>