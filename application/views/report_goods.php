<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div class="content">
        <div id="tabs" class="htabs">
            <a href="<?php echo site_url('report/action');?>" style="display:inline;">Actions</a>
            <a href="<?php echo site_url('report/rewards_badges');?>" style="display:inline;">Rewards</a>
            <a href="<?php echo site_url('report/goods');?>" class="selected" style="display:inline;">Goods</a>
            <a href="<?php echo site_url('report/registration');?>" style="display:inline;">Registration</a>
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
                    <select name="filter_timezone" style="height: 30px; width: 150px">
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
                        <?php echo $this->lang->line('filter_reward_id'); ?>
                    <select name="filter_goods_id" style="height: 30px; width: 180px">
                        <option value="0"><?php echo "All"; ?></option>
                        <?php foreach ($goods_available as $good){?>
                            <?php if ($good['_id'] == $filter_goods_id) { ?>
                            <option selected="selected" value="<?php echo $good['_id']?>"><?php echo $good['name'];?></option>
                            <?php }else{?>
                            <option value="<?php echo $good['_id']?>"><?php echo $good['name'];?></option>
                            <?php }?>
                        <?php }?>
                    </select>
                </span>
                <span>
                    <?php echo $this->lang->line('filter_status'); ?>
                    <select name="filter_goods_status" style="height: 30px; width:80px">
                        <option <?php if($filter_status == "all") echo "selected"; ?> value="all"><?php echo "All";    ?></option>
                        <option <?php if($filter_status == "active") echo "selected"; ?> value="active"><?php echo "Active"; ?></option>
                        <option <?php if($filter_status == "used") echo "selected"; ?> value="used"><?php echo "Used";   ?></option>
                        <option <?php if($filter_status == "expired") echo "selected"; ?> value="expired"><?php echo "Expired";?></option>
                    </select>
                </span>
                <span>
                    <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                </span>
                <span>
                    <a onclick="downloadFile();return true;" class="button"><?php echo $this->lang->line('button_download'); ?></a>
                </span>
            </div>

            <table class="list">
                <thead>
                <tr>
                    <td width="40" class="left"><?php echo $this->lang->line('column_avatar'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_player_id'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_username'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_email'); ?></td>
                    <!-- <td class="left"><?php //echo $this->lang->line('column_level'); ?></td>
                    <td class="left"><?php //echo $this->lang->line('column_exp'); ?></td> -->
                    <td class="right"><?php echo $this->lang->line('column_goods_name'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_goods_code'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_goods_amount'); ?></td>
                    <td width="80" class="right"><?php echo $this->lang->line('column_goods_status'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_added'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_expire'); ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if ($reports) { ?>
                    <?php foreach ($reports as $report) { ?>
                    <tr>
                        <td style="word-wrap:break-word;" class="left"><img width="40" height="40" src="<?php echo $report['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></td>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['cl_player_id']; ?></td>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['username']; ?></td>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['email']; ?></td>
                        <!-- <td class="left"><?php //echo $report['level']; ?></td>
                        <td class="left"><?php //echo $report['exp']; ?></td> -->
                        <td style="word-wrap:break-word;" class="right">
                            <?php 
                            if(isset($report['goods_name'])&&$report['goods_name']!=null){
                                // echo $report['goods_name'];
                                echo $report['goods_name'];
                            }                            
                            ?>
                        </td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['code']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['value']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo isset($report['status']) ? $report['status']: ""; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['date_added']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo isset($report['date_expire']) ? $report['date_expire'] : ""; ?></td>
                    </tr>
                        <?php } ?>
                    <?php } else { ?>
                <tr>
                    <td class="center" colspan="10"><?php echo $text_no_results; ?></td>
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
    url = baseUrlPath+'report_goods/goods_filter?t='+d;

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

    var filter_goods_id = $('select[name=\'filter_goods_id\']').attr('value');

    if (filter_goods_id != 0) {
        url += '&goods_id=' + encodeURIComponent(filter_goods_id);
    }

    var filter_goods_status = $('select[name=\'filter_goods_status\']').attr('value');

    if (filter_goods_status != 0) {
        url += '&status=' + encodeURIComponent(filter_goods_status);
    }
    location = url;
}

function downloadFile() {
    var d = new Date().getTime();
    url = baseUrlPath+'report_goods/actionDownload?t='+d;

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

    var filter_goods_id = $('select[name=\'filter_goods_id\']').attr('value');

    if (filter_goods_id != 0) {
        url += '&goods_id=' + encodeURIComponent(filter_goods_id);
    }

    location = url;
}
//--></script>
<script type="text/javascript"><!--
$(document).ready(function() {
    $('#date-start').datepicker({dateFormat: 'yy-mm-dd'});

    $('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script>
