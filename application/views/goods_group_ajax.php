<?php if (isset($members)) { ?>
    <table id="members" class="display form no-footer" cellspacing="0" border="1" width="100%">
        <thead>
        <tr>
            <th style="width:120px;"><?php echo $this->lang->line('entry_goods_id'); ?></th>
            <th style="width:80px;"><?php echo $this->lang->line('entry_batch_name'); ?></th>
            <th style="width:200px;"><?php echo $this->lang->line('entry_name'); ?></th>
            <th style="width:200px;"><?php echo $this->lang->line('entry_code'); ?></th>
            <th style="width:80px;"><?php echo $this->lang->line('entry_start_date'); ?></th>
            <th style="width:80px;"><?php echo $this->lang->line('entry_expire_date'); ?></th>
            <th style="width:80px;"><?php echo $this->lang->line('entry_expire_date_coupon'); ?></th>
            <th style="width:30px;"><?php echo $this->lang->line('entry_action'); ?></th>
        </tr>
        <tr class="filter">
            <td class="left" ><input style="width:150px;" title="filter_goods" type="text" name="filter_goods" value="<?php echo isset($_GET['filter_goods']) ? $_GET['filter_goods'] : "" ?>"/></td>
            <td class="left" >
                <select name="filter_batch" style="width:80px;">
                    <?php foreach ($members_batch as $batch) {?>
                        <option value="<?php echo $batch;?>" <?php echo isset($_GET['filter_batch']) && $_GET['filter_batch'] == $batch? 'selected' : "" ?>><?php echo $batch;?></option>
                    <?php } ?>
                </select>
            </td>
            <td class="left" ><input style="width:180px;" title="filter_coupon_name" type="text" name="filter_coupon_name" value="<?php echo isset($_GET['filter_coupon_name']) ? $_GET['filter_coupon_name'] : "" ?>"/></td>
            <td class="left" ><input style="width:180px;" title="filter_voucher_code" type="text" name="filter_voucher_code" value="<?php echo isset($_GET['filter_voucher_code']) ? $_GET['filter_voucher_code'] : "" ?>"/></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="width:80px;">
                <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                <a onclick="update_table();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                <?php if (isset($members[0])){ ?>
                <a onclick="delete_filtered_coupon('<?php echo $members[0]['goods_id']->{'$id'}?>',);" class="button" id="delete_filtered" title="Delete All Match Filtered"><i class='fa fa-trash fa-lg' title="Delete All Match Filtered"></i></a>
                <?php } ?>
            </td>
        </tr>
        </thead>
        <tbody>
        <?php if (is_array($members)){
            $count = 0;
            foreach ($members as $member) { ?>
                <tr class="<?php echo (++$count%2 ? "odd" : "even") ?>">
                    <td style="width:120px;"><?php echo $member['goods_id']->{'$id'}; ?></td>
                    <td style="width:80px;"><?php echo isset($member['batch_name']) ? $member['batch_name'] : 'default'; ?></td>
                    <td style="width:200px;"><?php echo $member['name']; ?></td>
                    <td style="width:200px;"><?php echo isset($member['code']) ? $member['code'] : ''; ?></td>
                    <td style="width:80px;" align="center"><?php echo isset($member['date_start']) ? $member['date_start'] : ""; ?></td>
                    <td style="width:80px;" align="center"><?php echo isset($member['date_expire']) ? $member['date_expire'] : ""; ?></td>
                    <td style="width:80px;" align="center"><?php echo isset($member['date_expired_coupon']) ? $member['date_expired_coupon'] : ''; ?></td>
                    <td align="center">
                        <a onclick="showCouponModalForm('<?php echo $member['goods_id']->{'$id'}?>',
                            '<?php echo isset($member['batch_name']) ? $member['batch_name'] : 'default'; ?>',
                            '<?php echo $member['name']; ?>',
                            '<?php echo isset($member['code']) ? $member['code'] : ''; ?>',
                            '<?php echo isset($member['date_start']) ? $member['date_start'] : ""; ?>',
                            '<?php echo isset($member['date_expire']) ? $member['date_expire'] : ""; ?>',
                            '<?php echo isset($member['date_expired_coupon']) ? $member['date_expired_coupon'] : ""; ?>'
                            );" class="button"><i class='fa fa-edit fa-lg''></i></a>
                        <a onclick="delete_coupon('<?php echo $member['goods_id']->{'$id'}?>',);" class="button" title="Delete"><i class='fa fa-times fa-lg' title=""></i></a>
                    </td>
                </tr>
            <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div id="members_info" class="paging_info" role="status" aria-live="polite">Showing <?php echo $members_current_start_page; ?> to <?php echo $members_current_total_page; ?> of <?php echo $members_total; ?> entries</div>
    <div class="paging_simple_numbers" id="members_paginate">
        <span>
            <?php echo $total_page; ?>
        </span>
    </div>
<?php } ?>