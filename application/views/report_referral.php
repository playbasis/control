<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('report/action');?>" style="display:inline;">Actions</a>
                <a href="<?php echo site_url('report/rewards_badges');?>" style="display:inline;">Badges</a>
                <a href="<?php echo site_url('report/rewards_custompoint');?>" style="display:inline;">Custompoints</a>
                <a href="<?php echo site_url('report/goods');?>" style="display:inline;">Goods</a>
                <a href="<?php echo site_url('report/goods_store');?>" style="display:inline;">Goods Store</a>
                <a href="<?php echo site_url('report/gift');?>" style="display:inline;">Gift</a>
                <a href="<?php echo site_url('report/registration');?>" style="display:inline;">Registration</a>
                <a href="<?php echo site_url('report/referral');?>"  class="selected" style="display:inline;">Referral</a>
                <a href="<?php echo site_url('report/quest');?>" style="display:inline;">Quest</a>
                <a href="<?php echo site_url('report/quiz');?>" style="display:inline;">Quiz</a>
            </div>
            <div class="report-filter">
                <span>
                        <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" style="width:100px;"/>
                </span>
                <span>
                        <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" style="width:100px;"/>
                </span>
                <span>
                    <?php echo $this->lang->line('filter_time_zone'); ?>
                    <select name="filter_timezone" style="height: 30px">
                        <option value="0">Select timezone</option>
                        <?php foreach($time_zone as $t) {
                            if ($filter_time_zone == $t) { ?>
                                <option selected value="<?php echo $t ?>"><?php echo $t ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $t ?>"><?php echo $t ?></option>
                            <?php }
                        }?>
                    </select>
                </span>
                <span>
                        <?php echo $this->lang->line('filter_email_username'); ?>
                    <input type="text" name="filter_username" value="<?php echo $filter_username; ?>" id="username" size="12" />
                </span>

                <span>
                    <a onclick="filter();" class="button"><i class="fa fa-filter"></i></a>
                </span>
                <span>
                    <a onclick="downloadFile();return true;" class="button"><i class="fa fa-download"></i></a>
                </span>
            </div>

            <table class="list">
                <thead>
                <tr>
                    <td class="left"><?php echo $this->lang->line('column_player_id'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_referrer'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_referral_code'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_registered'); ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if ($reports) { ?>
                    <?php foreach ($reports as $report) { ?>
                        <tr>
                            <td style="word-wrap:break-word;" class="left"><?php echo $report['cl_player_id']; ?></td>
                            <td style="word-wrap:break-word;" class="left"><?php echo isset($report['cl_player_id-2']) ? $report['cl_player_id-2'] : '' ; ?></td>
                            <td style="word-wrap:break-word;" class="left"><?php echo isset($report['code']) ? $report['code'] : '' ; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo $report['date_added']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="center" colspan="3"><?php echo $text_no_results; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
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
        var d = new Date().getTime();
        // url = baseUrlPath+'report_reward/reward_badge?t='+d;
        url = baseUrlPath+'report_referral/referral_filter?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
        }

        var filter_time_zone = $('select[name=\'filter_timezone\']').attr('value');

        if (filter_time_zone) {
            url += '&time_zone=' + encodeURIComponent(filter_time_zone);
        }

        var filter_username = $('input[name=\'filter_username\']').attr('value');

        if (filter_username) {
            url += '&username=' + encodeURIComponent(filter_username);
        }

        var filter_action_id = $('select[name=\'filter_action_id\']').attr('value');

        // if (filter_action_id != 0) {
        //     url += '&goods_id=' + encodeURIComponent(filter_action_id);
        // }

        location = url;
    }

    function downloadFile() {
        var d = new Date().getTime();
        url = baseUrlPath+'report_referral/actionDownload?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
        }

        var filter_time_zone = $('select[name=\'filter_timezone\']').attr('value');

        if (filter_time_zone) {
            url += '&time_zone=' + encodeURIComponent(filter_time_zone);
        }

        var filter_username = $('input[name=\'filter_username\']').attr('value');

        if (filter_username) {
            url += '&username=' + encodeURIComponent(filter_username);
        }

        location = url;
    }
    //--></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript"><!--
    $(document).ready(function() {
        $('#date-start').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: "HH:mm:ss"});

        $('#date-end').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: "HH:mm:ss"});
    });
    //--></script>
