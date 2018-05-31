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
            <a href="<?php echo site_url('report/goods');?>" class="selected" style="display:inline;">Goods</a>
            <!-- <a href="<?php echo site_url('report/goods_store');?>" style="display:inline;">Goods Store</a>-->
            <a href="<?php echo site_url('report/gift');?>" style="display:inline;">Gift</a>
            <a href="<?php echo site_url('report/registration');?>" style="display:inline;">Registration</a>
            <a href="<?php echo site_url('report/referral');?>" style="display:inline;">Referral</a>
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
                    <select class="chosen-select" id="filter_goods_status" name="filter_goods_status" style="height: 30px; width:80px">
                        <option <?php if($filter_status == "all") echo "selected"; ?> value="all"><?php echo "All";    ?></option>
                        <option <?php if($filter_status == "active") echo "selected"; ?> value="active"><?php echo "Active"; ?></option>
                        <option <?php if($filter_status == "used") echo "selected"; ?> value="used"><?php echo "Used";   ?></option>
                        <option <?php if($filter_status == "gifted") echo "selected"; ?> value="gifted"><?php echo "Gifted";    ?></option>
                        <option <?php if($filter_status == "expired") echo "selected"; ?> value="expired"><?php echo "Expired";?></option>
                    </select>
                </span>
                <span>
                    <a onclick="filter();" class="button"><i class="fa fa-filter"></i></a>
                </span>
                <span>
                    <a onclick="downloadFile();return true;" class="button"><i class="fa fa-download"></i></a>
                </span>
                </div>
                <div>
                <span>
                    <?php echo $this->lang->line('filter_tags'); ?>
                    <input type="text" name="filter_tags" value="<?php echo $filter_tags; ?>" id="filter_tags" size="12" />
                </span>
                <span>
                    <?php echo $this->lang->line('filter_goods_id'); ?>
                    <select class="chosen-select" multiple id="filter_goods_id" name="filter_goods_id" style="width:70%">
                        <?php foreach ($goods_available as $good){
                            $match =  array_search($good['_id'], $filter_goods_id);
                            if (!is_null($match) && $match !== false) { ?>
                                <option selected="selected" value="<?php echo $good['_id'] ?>"><?php echo $good['name']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $good['_id'] ?>"><?php echo $good['name']; ?></option>
                            <?php }
                        }?>
                    </select>
                </span>
                </div>
            </div>

            <table class="list">
                <thead>
                <tr>
                    <td class="left"><?php echo $this->lang->line('column_player_id'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_goods_name'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_goods_code'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_tags'); ?></td>
                    <td width="50" class="right"><?php echo $this->lang->line('column_goods_amount'); ?></td>
                    <td width="50" class="right"><?php echo $this->lang->line('column_goods_status'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_added'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_used'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_gifted'); ?></td>
                    <td width="120" class="right"><?php echo $this->lang->line('column_date_expire'); ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if ($reports) { ?>
                    <?php foreach ($reports as $report) { ?>
                    <tr>
                        <td style="word-wrap:break-word;" class="left"><?php echo $report['cl_player_id']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo isset($report['goods_name'])&&$report['goods_name']!=null ? $report['goods_name']: ""; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['code']; ?></td>
                        <td style="word-wrap:break-word;" class="right">
                        <?php if(isset($report['tags']) && $report['tags']){
                            foreach ($report['tags'] as $val ){ ?>
                                <span class="label" data-toggle="tooltip" data-placement="right" title="<?php echo $val ?>" style="float:left; max-width: 95%; overflow: hidden; margin-right: 1px;margin-bottom: 1px;"><?php echo $val ?></span>
                            <?php }
                        } ?>
                        </td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['value']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo isset($report['status']) ? $report['status']: ""; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo $report['date_added']; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo isset($report['date_used']) ? $report['date_used'] : ""; ?></td>
                        <td style="word-wrap:break-word;" class="right"><?php echo isset($report['date_gifted']) ? $report['date_gifted'] : ""; ?></td>
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
<script type="text/javascript">
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

    var filter_tags = $('input[name=\'filter_tags\']').attr('value');

    if (filter_tags) {
        url += '&tags=' + encodeURIComponent(filter_tags);
    }

    var filter_goods_id = $('select[name=\'filter_goods_id\']').val();

    if (filter_goods_id != null) {
        var goods = ""
        filter_goods_id.forEach(function(element) {
            if(goods == ""){
                goods = encodeURIComponent(element);
            } else {
                goods += encodeURIComponent(',' + element);
            }
        });
        if(goods != ""){
            url += '&goods_id=' + goods;
        }
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

    var filter_tags = $('input[name=\'filter_tags\']').attr('value');

    if (filter_tags) {
        url += '&tags=' + encodeURIComponent(filter_tags);
    }
    var filter_goods_id = $('select[name=\'filter_goods_id\']').val();

    if (filter_goods_id != null) {
        var goods = ""
        filter_goods_id.forEach(function(element) {
            if(goods == ""){
                goods = encodeURIComponent(element);
            } else {
                goods += encodeURIComponent(',' + element);
            }
        });
        url += '&goods_id=' + goods;
    }

    var filter_goods_status = $('select[name=\'filter_goods_status\']').attr('value');

    if (filter_goods_status != 0) {
        url += '&status=' + encodeURIComponent(filter_goods_status);
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

