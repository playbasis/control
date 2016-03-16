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
                <a href="<?php echo site_url('report/quest');?>" style="display:inline;">Quest</a>
                <a href="<?php echo site_url('report/quiz');?>" style="display:inline;" class="selected">quiz</a>
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
                        <?php echo $this->lang->line('filter_quiz_id'); ?>
                    <select name="filter_action_id">
                        <option value="0"><?php echo "All"; ?></option>
                        <?php if(is_array($quiz_list))foreach ($quiz_list as $quiz){?>
                            <?php if ($quiz['_id'] == $filter_action_id) { ?>
                                <option selected="selected" value="<?php echo $quiz['_id']?>"><?php echo $quiz['name'];?></option>
                            <?php }else{?>
                                <option value="<?php echo $quiz['_id']?>"><?php echo $quiz['name'];?></option>
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

                    <td class="right"><?php echo $this->lang->line('column_quiz_name'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_question_name'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_option'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_score'); ?></td>
                    <td class="right"><?php echo $this->lang->line('column_max_score'); ?></td>
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

                            <td class="right">
                                <?php
                                if(isset($report['quiz_name'])&&$report['quiz_name']!=null){
                                    echo $report['quiz_name'];
                                }
                                ?>
                            </td>
                            <td class="right"><?php
                                if(isset($report['question_name'])&&$report['question_name']!=null){
                                    echo $report['question_name'];
                                }
                                ?>
                            </td>
                            <td class="right">
                                <?php
                                if(isset($report['option'])&&$report['option']!=null){
                                    echo $report['option'];
                                }
                                ?>
                            </td>
                            <td class="right">
                                <?php
                                if(isset($report['score'])&&$report['score']!=null){
                                    echo $report['score'];
                                }
                                ?>
                            </td>
                            <td class="right">
                                <?php
                                if(isset($report['max_score'])&&$report['max_score']!=null){
                                    echo $report['max_score'];
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
        url = baseUrlPath+'report_quiz/quiz?t='+d;

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
        url = baseUrlPath+'report_quiz/actionDownload?t='+d;

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
