<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <?php //if($user_group_id != $setting_group_id){ ?>
            <div class="buttons">
                <button class="btn btn-info" onclick="formCheck();" type="button"><?php echo $this->lang->line('button_upload'); ?></button>
                <button class="btn btn-info" onclick="$('#form.spin-form').submit();" type="button"><?php echo $this->lang->line('button_spin'); ?></button>
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
                <!--<span>
                        <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo isset($filter_date_start) ? $filter_date_start : null; ?>" id="date-start" size="12" />
                </span>
                <span>
                        <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo isset($filter_date_end) ? $filter_date_end : null; ?>" id="date-end" size="12" />
                </span>
                <span>
                    <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                </span>-->
                <?php
                $attributes = array('id' => 'form', 'class' => 'form-horizontal upload-form');
                echo form_open_multipart('spin',$attributes);
                ?>
                <span>
                <?php echo $this->lang->line('entry_file'); ?>:
                <input id="file" type="file" name="file" size="100" />
                </span>
                <?php echo form_close();?>
            </div>
            <div id="actions">
                <?php
                $attributes = array('id' => 'form', 'class' => 'form-horizontal spin-form');
                echo form_open_multipart('spin/action',$attributes);
                ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td class="center" width="1%"><?php echo $this->lang->line('column_number'); ?></td>
                        <td width="5%" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="left" width="85%"><?php echo $this->lang->line('column_player_id'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_manual_grant'); ?></td>
                        <!--
                        <td class="center" width="10%"><?php echo $this->lang->line('column_granted_token'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_offer_game'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_actived_spin'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_received_reward'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_out_standing_spin'); ?></td>
                        <td class="center" width="10%"><?php echo $this->lang->line('column_current_token'); ?></td>-->
                    </tr>
                    </thead>
                    <tbody style="overflow-y:scroll">
                    <?php if (isset($report) && $report) { ?>
                        <?php foreach ($report as $index => $r) { ?>
                            <tr>
                                <td class="center" width="1%"><?php echo $index +1 ?></td>
                                <td width="5%" style="text-align: center;"><input type="checkbox" name="selected[]" value="<?php echo $r['_id'].','.$r['manual_grant']?>" /></td>
                                <td class="left" width="30%"><?php echo isset($r['_id']) ? $r['_id'] : ""; ?></td>
                                <td class="right" width="10%"><?php echo isset($r['manual_grant']) ? $r['manual_grant'] : 0; ?></td>
                                <!--
                                <td class="right" width="10%"><?php echo isset($r['n']) ? $r['n'] : 0; ?></td>
                                <td class="right" width="10%"><?php echo isset($r['n']) ? $r['n'] : 0; ?></td>
                                <td class="right" width="10%"><?php echo isset($r['n']) ? $r['n'] : 0; ?></td>
                                <td class="right" width="10%"><?php echo isset($r['out_standing_spin']) ? $r['out_standing_spin'] : 0 ?></td>
                                <input type="hidden" name="out_standing_spin[]" value="<?php echo $r['out_standing_spin']; ?>" />
                                <td class="right" width="10%"><?php echo isset($r['n']) ? $r['n'] : 0; ?></td>-->
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

    function formCheck(){
        var file = document.getElementById('file').files[0];

        if(file){
            if(file.size < 2097152) { // 2MB (this size is in bytes)
                //Submit form
                $('form.upload-form').submit();
            }
        }
    }
</script>