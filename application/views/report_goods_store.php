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
                <a href="<?php echo site_url('report/goods_store');?>" class="selected" style="display:inline;">Goods Store</a>
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
                    <?php echo $this->lang->line('filter_status'); ?>
                        <select class="chosen-select" id="filter_goods_status" name="filter_goods_status" style="height: 30px; width:80px">
                        <option <?php if(!isset($filter_status) || (isset($filter_status) && ($filter_status == "enable"))) echo "selected"; ?> value="enable"><?php echo "Enable";?></option>
                        <option <?php if(isset($filter_status) && $filter_status == "disable") echo "selected"; ?> value="disable"><?php echo "Disable"; ?></option>
                            <option <?php if(isset($filter_status) && $filter_status == "all") echo "selected"; ?> value="all"><?php echo "All"; ?></option>
                    </select>
                </span>
                <span>
                    <?php echo $this->lang->line('filter_tags'); ?>
                        <input type="text" name="filter_tags" value="<?php echo $filter_tags; ?>" id="filter_tags" size="12" />
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
                <div>

                </div>
            </div>

            <table class="list">
                <thead>
                <tr>
                    <td width="200" class="center"><?php echo $this->lang->line('column_goods_name'); ?></td>
                    <td width="40" class="center"><?php echo $this->lang->line('column_goods_group'); ?></td>
                    <td width="120" class="center"><?php echo $this->lang->line('column_goods_batch'); ?></td>
                    <td width="115"  class="center"><?php echo $this->lang->line('column_goods_date_start'); ?></td>
                    <td width="115"  class="center"><?php echo $this->lang->line('column_goods_date_end'); ?></td>
                    <td width="115"  class="center"><?php echo $this->lang->line('column_goods_date_expire'); ?></td>
                    <td width="50"  class="center"><?php echo $this->lang->line('column_goods_unit_price'); ?></td>
                    <td width="50" class="center"><?php echo $this->lang->line('column_goods_quantity'); ?></td>
                    <td width="69"  class="center"><?php echo $this->lang->line('column_goods_total_price'); ?></td>
                    <td width="60" class="center"><?php echo $this->lang->line('column_goods_remaining'); ?></td>
                    <td width="50"  class="center"><?php echo $this->lang->line('column_goods_granted'); ?></td>
                    <td width="50"  class="center"><?php echo $this->lang->line('column_goods_expired'); ?></td>
                    <td width="50"  class="center"><?php echo $this->lang->line('column_goods_unused'); ?></td>
                    <td width="50"  class="center"><?php echo $this->lang->line('column_goods_used'); ?></td>
                    <td width="50"  class="center"><?php echo $this->lang->line('column_goods_balance'); ?></td>

                </tr>
                </thead>
                <tbody>
                <?php if ($reports) { ?>
                    <?php foreach ($reports as $report) { ?>
                        <tr>
                            <?php if(isset($report['total_batch']) && $report['total_batch'] > 1 && (!is_null($report['goods_name'])) ){ ?>
                                <td rowspan="<?php echo $report['total_batch'] ?>" style="word-wrap:break-word;" class="right"><?php echo isset($report['goods_name'])&&$report['goods_name']!=null ? $report['goods_name']: ""; ?></td>
                                <td rowspan="<?php echo $report['total_batch'] ?>" style="word-wrap:break-word;" class="right"><?php echo isset($report['group'])&&$report['group']? "true": "false"; ?></td>
                            <?php } elseif(isset($report['total_batch']) && $report['total_batch'] > 1 && is_null($report['goods_name'])) { ?>
                            <?php } else { ?>
                                <td style="word-wrap:break-word;" class="right"><?php echo isset($report['goods_name'])&&$report['goods_name']!=null ? $report['goods_name']: ""; ?></td>
                                <td style="word-wrap:break-word;" class="right"><?php echo isset($report['group'])&&$report['group']? "true": "false"; ?></td>
                            <?php }  ?>

                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['batch'])&&$report['batch']!=null ? implode("<br>", $report['batch']): ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['date_start']) ? implode("<br>", $report['date_start']) : ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['date_end']) ? implode("<br>", $report['date_end']) : ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['date_expire']) ? implode("<br>", $report['date_expire']) : ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['price'])&&!is_null($report['price']) ? $report['price']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['quantity'])&&!is_null($report['quantity']) ? $report['quantity']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['total_value'])&&!is_null($report['total_value']) ? $report['total_value']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['remaining'])&&!is_null($report['remaining']) ? $report['remaining']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['granted'])&&!is_null($report['granted']) ? $report['granted']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['expired'])&&!is_null($report['expired']) ? $report['expired']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['unused'])&&!is_null($report['unused']) ? $report['unused']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['used'])&&!is_null($report['used']) ? $report['used']: ""; ?></td>
                            <td style="word-wrap:break-word;" class="right"><?php echo isset($report['used_balance'])&&!is_null($report['granted']) ? $report['used_balance']: ""; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="center" colspan="14"><?php echo $text_no_results; ?></td>
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
        url = baseUrlPath+'report_goods_store/goods_store_filter?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
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
        url = baseUrlPath+'report_goods_store/actionDownload?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
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
    
    $("#filter_goods_id").chosen({max_selected_options: 5});
</script>

