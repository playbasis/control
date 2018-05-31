<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div class="content">
        <div id="tabs" class="htabs">
            <a href="<?php echo site_url('report/action');?>" style="display:inline;">Actions</a>
            <a href="<?php echo site_url('report/rewards_badges');?>" style="display:inline;">Rewards</a>
            <a href="<?php echo site_url('report/rewards_custompoint');?>" style="display:inline;">Custompoints</a>
            <a href="<?php echo site_url('report/goods');?>" style="display:inline;">Goods</a>
            <!--<a href="<?php echo site_url('report/goods_store');?>" style="display:inline;">Goods Store</a>-->
            <a href="<?php echo site_url('report/gift');?>" class="selected" style="display:inline;">Gift</a>
            <a href="<?php echo site_url('report/registration');?>" style="display:inline;">Registration</a>
            <a href="<?php echo site_url('report/referral');?>"  style="display:inline;">Referral</a>
            <a href="<?php echo site_url('report/quest');?>" style="display:inline;">Quest</a>
            <a href="<?php echo site_url('report/quiz');?>" style="display:inline;">Quiz</a>
        </div>
            <div class="report-filter">
                <div>
                <span>
                    <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" style="width:150px;"/>
                </span>
                <span>
                    <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" style="width:150px;"/>
                </span>
                <span>
                    <?php echo $this->lang->line('filter_time_zone'); ?>
                    <select id="filter_timezone" name="filter_timezone" style="height: 30px;">
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
                    <?php echo $this->lang->line('filter_status'); ?>
                        <select class="chosen-select" id="filter_gift_status" name="filter_gift_status" style="height: 30px; width:80px">
                        <option <?php if($filter_status == "all") echo "selected"; ?> value="all"><?php echo "All";    ?></option>
                        <option <?php if($filter_status == "GOODS") echo "selected"; ?> value="GOODS"><?php echo "GOODS"; ?></option>
                        <option <?php if($filter_status == "BADGE") echo "selected"; ?> value="BADGE"><?php echo "BADGE";   ?></option>
                        <option <?php if($filter_status == "CUSTOM_POINT") echo "selected"; ?> value="CUSTOM_POINT"><?php echo "CUSTOM_POINT";    ?></option>
                    </select>
                </span>
                    <span>
                    <a onclick="filter();" class="button"><i class="fa fa-filter"></i></a>
                </span>
                    <span>
                    <a onclick="downloadFile();return true;" class="button"><i class="fa fa-download"></i></a>
                </span>
                </div>
            </div>

            <table class="list">
                <thead>
                <tr>
                    <td class="left"><?php echo $this->lang->line('column_sender'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_receiver'); ?></td>
                    <td width="80" class="center"><?php echo $this->lang->line('column_gift_type'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_gift_name'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_gift_code'); ?></td>
                    <td width="50" class="center"><?php echo $this->lang->line('column_gift_amount'); ?></td>
                    <td width="120" class="left"><?php echo $this->lang->line('column_date_gifted'); ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if ($reports) { ?>
                    <?php foreach ($reports as $report) { ?>
                    <tr>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['sender']; ?></td>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['receiver']; ?></td>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['type']; ?></td>
                        <td style="rword-wrap:break-word;" class="right"><?php echo $report['name']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['code']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['value'] ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['date_gifted']; ?></td>
                    </tr>
                        <?php } ?>
                    <?php } else { ?>
                <tr>
                    <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
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
<script type="text/javascript">
function filter() {
    var d = new Date().getTime();
    // url = baseUrlPath+'report_reward/reward_badge?t='+d;
    url = baseUrlPath+'report_gift/gift_filter?t='+d;

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

    var filter_gift_status = $('select[name=\'filter_gift_status\']').attr('value');

    if (filter_gift_status != 0) {
        url += '&status=' + encodeURIComponent(filter_gift_status);
    }
    location = url;
}

function downloadFile() {
    var d = new Date().getTime();
    url = baseUrlPath+'report_gift/giftDownload?t='+d;

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

    var filter_gift_status = $('select[name=\'filter_gift_status\']').attr('value');

    if (filter_gift_status != 0) {
        url += '&status=' + encodeURIComponent(filter_gift_status);
    }

    location = url;
}
</script>
<link id="bootstrap-style2" href="<?php echo base_url();?>javascript/bootstrap/chosen.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date-start').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: "HH:mm:ss"});

        $('#date-end').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: "HH:mm:ss"});
    });

    $("#filter_timezone").chosen({max_selected_options: 1});
    $("#filter_goods_id").chosen({max_selected_options: 5});
    $("#filter_goods_status").chosen({disable_search_threshold: 10});
</script>

