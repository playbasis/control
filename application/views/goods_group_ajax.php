<?php if (isset($members)) { ?>
    <table id="members" class="display form no-footer" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th><?php echo $this->lang->line('entry_system_id'); ?></th>
            <th><?php echo $this->lang->line('entry_name'); ?></th>
            <th><?php echo $this->lang->line('entry_code'); ?></th>
            <th><?php echo $this->lang->line('entry_expire_date_coupon'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (is_array($members)){
            $count = 0;
            foreach ($members as $member) { ?>
                <tr class="<?php echo (++$count%2 ? "odd" : "even") ?>">
                    <td><?php echo $member['goods_id']->{'$id'}; ?></td>
                    <td><?php echo $member['name']; ?></td>
                    <td><?php echo isset($member['code']) ? $member['code'] : ''; ?></td>
                    <td align="center"><?php echo isset($member['date_expired_coupon']) ? $member['date_expired_coupon'] : ''; ?></td>
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