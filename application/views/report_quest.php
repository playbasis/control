<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="<?php echo site_url('report/action');?>" style="display:inline;">Actions</a>
                <a href="<?php echo site_url('report/rewards_badges');?>" style="display:inline;">Rewards</a>
                <a href="<?php echo site_url('report/goods');?>" style="display:inline;">Goods</a>
                <a href="<?php echo site_url('report/registration');?>" style="display:inline;">Registration</a>
                <a href="<?php echo site_url('report/quest');?>" style="display:inline;" class="selected">Quest</a>
                <a href="<?php echo site_url('report/quiz');?>" style="display:inline;">Quiz</a>
            </div>
            <div class="report-filter">
                <span>
                        <?php echo $this->lang->line('filter_date_start'); ?>
                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" />
                </span>
                <span>
                        <?php echo $this->lang->line('filter_date_end'); ?>
                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" />
                </span>
                <span>
                        <?php echo $this->lang->line('filter_email_username'); ?>
                    <input type="text" name="filter_username" value="<?php echo $filter_username; ?>" id="username" size="12" />
                </span>
                <span>
                        <?php echo $this->lang->line('filter_quest_id'); ?>
                    <select name="filter_action_id">
                        <option value="0"><?php echo "All"; ?></option>
                        <?php if(is_array($quest_list))foreach ($quest_list as $quest){?>
                            <?php if ($quest['_id'] == $filter_action_id) { ?>
                                <option selected="selected" value="<?php echo $quest['_id']?>"><?php echo $quest['quest_name'];?></option>
                            <?php }else{?>
                                <option value="<?php echo $quest['_id']?>"><?php echo $quest['quest_name'];?></option>
                            <?php }?>
                        <?php }?>
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
                    <td class="left"><?php echo $this->lang->line('column_avatar'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_player_id'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_username'); ?></td>
                    <td class="left"><?php echo $this->lang->line('column_email'); ?></td>

                    <td class="right"><?php echo $this->lang->line('column_quest_name'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_mission_name'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_mission_number'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_date_added'); ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if ($reports) { ?>
                    <?php foreach ($reports as $report) { ?>
                        <tr>
                            <td class="left"><img width="40" height="40" src="<?php echo $report['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></td>
                            <td class="left"><?php echo $report['cl_player_id']; ?></td>
                            <td class="left"><?php echo $report['username']; ?></td>
                            <td class="left"><?php echo $report['email']; ?></td>
                            <!-- <td class="left"><?php //echo $report['level']; ?></td>
                        <td class="left"><?php //echo $report['exp']; ?></td> -->
                            <td class="right">
                                <?php
                                if(isset($report['quest_name'])&&$report['quest_name']!=null){
                                    echo $report['quest_name'];
                                }
                                ?>
                            </td>
                            <td class="right"><?php
                                if(isset($report['mission_name'])&&$report['mission_name']!=null){
                                    echo $report['mission_name'];
                                }
                                ?>
                            </td>
                            <td class="right">
                                <?php
                                if(isset($report['mission_number'])&&$report['mission_number']!=null){
                                    echo $report['mission_number'];
                                }
                                ?>
                            </td>
                            <td class="right"><?php echo $report['date_added']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
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
        url = baseUrlPath+'report_quest/quest?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
        }

        var filter_username = $('input[name=\'filter_username\']').attr('value');

        if (filter_username) {
            url += '&username=' + encodeURIComponent(filter_username);
        }

        var filter_action_id = $('select[name=\'filter_action_id\']').attr('value');

        if (filter_action_id != 0) {
            url += '&action_id=' + encodeURIComponent(filter_action_id);
        }

        location = url;
    }

    function downloadFile() {
        var d = new Date().getTime();
        url = baseUrlPath+'report_quest/actionDownload?t='+d;

        var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

        if (filter_date_start) {
            url += '&date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

        if (filter_date_end) {
            url += '&date_expire=' + encodeURIComponent(filter_date_end);
        }

        var filter_username = $('input[name=\'filter_username\']').attr('value');

        if (filter_username) {
            url += '&username=' + encodeURIComponent(filter_username);
        }

        var filter_action_id = $('select[name=\'filter_action_id\']').attr('value');

        if (filter_action_id != 0) {
            url += '&action_id=' + encodeURIComponent(filter_action_id);
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

