<div id="content" class="span10">

<div class="box">
    <div class="heading">
        <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        <div class="buttons">
            <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
            <button class="btn btn-info" onclick="location = baseUrlPath+'plan'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
        </div>
    </div>
    <div class="content">
        <div id="tabs" class="htabs">
            <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            <a href="#tab-feature"><?php echo $this->lang->line('tab_feature'); ?></a>
            <a href="#tab-reward"><?php echo $this->lang->line('tab_reward'); ?></a>
            <a href="#tab-jigsaw"><?php echo $this->lang->line('tab_jigsaw'); ?></a>
            <a href="#tab-action"><?php echo $this->lang->line('tab_action'); ?></a>
            <a href="#tab-notification"><?php echo $this->lang->line('tab_notification'); ?></a>
            <a href="#tab-requests"><?php echo $this->lang->line('tab_requests'); ?></a>
            <a href="#tab-limits"><?php echo $this->lang->line('tab_limits'); ?></a>
            <a href="#tab-widget"><?php echo $this->lang->line('tab_widget'); ?></a>
            <a href="#tab-cms"><?php echo $this->lang->line('tab_cms'); ?></a>
            <?php if($name!=""){?>
                <a href="#tab-clients"><?php echo $this->lang->line('tab_clients'); ?></a>
            <?php }?>

        </div>
        <?php
        if(validation_errors() || isset($message)) {
            ?>
            <div class="content messages half-width">
                <?php
                echo validation_errors('<div class="warning">','</div>');

                if (isset($message) && $message) {
                    ?>
                    <div class="warning"><?php echo $message; ?></div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        $attributes = array('id' => 'form');
        echo form_open($form ,$attributes);
        ?>
            <div id="tab-general">
                <table class="form">
                    <tr>
                        <td><span class="required">*</span> <?php echo $this->lang->line('entry_name'); ?>:</td>
                        <td><input type="text" name="name" value="<?php echo $name; ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_description'); ?>:</td>
                        <td><textarea rows='7' name="description"><?php echo $description; ?></textarea></td>
                        <!-- <td><input type="text" name="description" value="<?php //echo $description; ?>" size="50" /></td> -->
                    </tr>
                    <tr>
                        <td><span class="required">*</span> <?php echo $this->lang->line('entry_price'); ?>:</td>
                        <td><input type="text" name="price" value="<?php echo $price; ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> <?php echo $this->lang->line('entry_display'); ?>:</td>
                        <td><select name="display">
                            <?php if ($display) { ?>
                            <option value="1" selected="selected"><?php echo $this->lang->line('text_displayed'); ?></option>
                            <option value="0"><?php echo $this->lang->line('text_not_displayed'); ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $this->lang->line('text_displayed'); ?></option>
                            <option value="0" selected="selected"><?php echo $this->lang->line('text_not_displayed'); ?></option>
                            <?php } ?>
                        </select></td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> <?php echo $this->lang->line('entry_status'); ?>:</td>
                        <td><select name="status">
                            <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                            <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                            <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                            <?php } ?>
                        </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_limit_num_clients') ?></td>
                        <td><input type="text" data-placement="right" class="tooltips" title='Number of clients that can subscribe to this plan. If left blank means unlimited number of clients.' name="limit_num_client" value = "<?php echo $limit_num_client; ?>"/></td>
                    </tr>
                </table>
            </div>
            <div id="tab-feature">
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'feature_data\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_name_feature'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($plan_features) && $plan_features) { ?>
                        <?php foreach ($plan_features as $feature) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (in_array($feature['feature_id'], $feature_data)) { ?>
                                <input type="checkbox" name="feature_data[]" value="<?php echo $feature['feature_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="feature_data[]" value="<?php echo $feature['feature_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo $feature['name']; ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="tab-action">
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"></td>
                        <td class="left"><?php echo $this->lang->line('column_name_action'); ?></td>
                        <td class="left"><?php echo $this->lang->line('column_name_owner'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($plan_actions) && $plan_actions) { ?>
                        <?php foreach ($plan_actions as $action) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (in_array($action['action_id'], $action_data)) { ?>
                                <input type="checkbox" name="action_data[]" value="<?php echo $action['action_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="action_data[]" value="<?php echo $action['action_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo ucfirst($action['name']); ?></td>
                            <td class="left"><?php echo ucfirst($action['description']); ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                        <tr>
                            <td class="center" colspan="3"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="tab-jigsaw">
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'jigsaw_data\']').attr('checked', this.checked);" /></td>
                        <td class="left"><?php echo $this->lang->line('column_name_jigsaw'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($plan_jigsaws) && $plan_jigsaws) { ?>
                        <?php foreach ($plan_jigsaws as $jigsaw) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (in_array($jigsaw['jigsaw_id'], $jigsaw_data)) { ?>
                                <input type="checkbox" name="jigsaw_data[]" value="<?php echo $jigsaw['jigsaw_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="jigsaw_data[]" value="<?php echo $jigsaw['jigsaw_id']; ?>" />
                                <?php } ?></td>
                            <td class="left"><?php echo ucfirst($jigsaw['name']); ?></td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="tab-reward">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name_reward'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($plan_rewards) && $plan_rewards) { ?>
                        <?php foreach ($plan_rewards as $reward) { ?>
                        <tr>
                            <td class="left"><?php echo ucfirst($reward['name']); ?></td>
                            <td class="left">
                                <input type="text"
                                        name="reward_data[<?php echo $reward['reward_id']; ?>][limit]"
                                        value="<?php echo $reward['limit']; ?>"
                                        style="width:50px;"
                                        class="tooltips"
                                        data-placement="left"
                                        title="Maximum number of
                                        <?php
                                            switch ($reward['name']){
                                                case "badge": echo ucfirst($reward['name']).'s';
                                                break;
                                                case "exp": echo ucfirst($reward['name']).'s';
                                                break;
                                                case "point": echo ucfirst($reward['name']).'s';
                                                break;
                                        }?>. if left blank, it is considered unlimited"/>
                                <input type="hidden" name="reward_data[<?php echo $reward['reward_id']; ?>][reward_id]" value="<?php echo $reward['reward_id']; ?>" />
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-notification">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name_notification'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($limit_noti) && $limit_noti) { ?>
                        <?php foreach ($limit_noti as $key=>$value) { ?>
                        <tr>
                            <td class="left"><?php echo strtoupper($key); ?></td>
                            <td class="left">
                                <input type="text"
                                        name="limit_noti[<?php echo $key; ?>][limit]"
                                        value="<?php echo $value; ?>"
                                        style="width:50px;"
                                        class="tooltips"
                                        data-placement="left"
                                        title="Maximum number of
                                        <?php echo strtoupper($key).'s'; ?>. if left blank, it is considered unlimited"/>
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-limits">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name_feature'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($limit_others) && $limit_others) { ?>
                        <?php foreach ($limit_others as $key=>$value) { ?>
                        <tr>
                            <td class="left"><?php echo ucfirst($key); ?></td>
                            <td class="left">
                                <input type="text"
                                        name="limit_others[<?php echo $key; ?>][limit]"
                                        value="<?php echo $value; ?>"
                                        style="width:50px;"
                                        class="tooltips"
                                        data-placement="left"
                                        title="Maximum number of
                                        <?php echo strtoupper($key).'s'; ?>. if left blank, it is considered unlimited"/>
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } else { ?>
                    <tr>
                        <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-widget">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name_feature'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($limit_widget) && $limit_widget) { ?>
                        <?php foreach ($limit_widget as $key=>$value) { ?>
                            <tr>
                                <td class="left"><?php echo ucfirst($key); ?></td>
                                <td class="left">
                                    <?php
                                    echo form_checkbox('limit_widget['.$key.'][limit]', 'true', (isset($value)&&($value=='true'||$value))?true:false);
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-cms">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name_feature'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($limit_cms) { ?>
                        <?php foreach ($limit_cms as $key=>$value) { ?>
                            <tr>
                                <td class="left"><?php echo ucfirst($key); ?></td>
                                <td class="left">
                                    <?php
                                    echo form_checkbox('limit_cms['.$key.'][limit]', 'true', (isset($value)&&($value=='true'||$value))?true:false);
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="2"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-requests">
                <table class="list">
                    <thead>
                    <tr>
                        <td class="left"><?php echo $this->lang->line('column_name_requests'); ?></td>
                        <td class="left" style="width: 150px;"><?php echo $this->lang->line('column_limit'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($limit_req): ?>
                    <?php for ($i=0; $i<=sizeof($limit_req); $i++): ?>
                        <tr class="form_req">
                            <td class="left">
                                <input type="text"
                                        data-index="<?php echo $i; ?>",
                                        name="limit_req[<?php echo $i; ?>][field]"
                                        value="<?php echo isset($limit_req[$i]) ? $limit_req[$i]['field'] : ""; ?>"
                                        class="tooltips"
                                        data-placement="left"
                                        title="URL" />
                            </td>
                            <td class="left">
                                <input type="text"
                                        data-index="<?php echo $i; ?>",
                                        name="limit_req[<?php echo $i; ?>][limit]"
                                        value="<?php echo isset($limit_req[$i]) ? $limit_req[$i]['limit'] : ""; ?>"
                                        style="width:50px;"
                                        class="tooltips"
                                        data-placement="left"
                                        title="Maximum number of
                                        <?php echo strtoupper($key).'s'; ?>. if left blank, it is considered unlimited"/>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    <?php else: ?>
                        <tr class="form_req">
                            <td class="left">
                                <input type="text"
                                        data-index="0",
                                        name="limit_req[0][field]"
                                        value=""
                                        class="tooltips"
                                        data-placement="left"
                                        title="URL" />
                            </td>
                            <td class="left">
                                <input type="text"
                                        data-index="0",
                                        name="limit_req[0][limit]"
                                        value=""
                                        style="width:50px;"
                                        class="tooltips"
                                        data-placement="left"
                                        title="Maximum number. if left blank, it is considered unlimited"/>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="center" colspan="2">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php if($name!=""){?>
                <div id="tab-clients">
                    <table class="list">
                        <thead>
                        <tr>
                            <td class="left">Company Name</td>
                            <td class="left">Main contact Person</td>
                            <td class="left">Email</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($clients_in_plan) { ?>
                            <?php foreach ($clients_in_plan as $client) { ?>
                            <tr>
                                <td class="left"><?php echo $client['company'];?></td>
                                <td class="left"><?php echo $client['first_name']." ".$client['last_name'];?></td>
                                <td class="left"><?php echo $client['email'];?></td>
                            </tr>
                                <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td class="center" colspan="3"><?php echo $this->lang->line('text_no_clients'); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php }?>
        <?php
        echo form_close();
        ?>
    </div>
</div>
</div>

<script type="text/javascript"><!--
$('#users .pagination a').live('click', function() {
    $('#users').fadeIn('slow');

    $('#users').load(this.href);

    $('#users').fadeOut('slow');

    return false;
});

$('#users').load('index.php?route=client/client/users&token=<?php echo $token; ?>&client_id=<?php echo $client_id; ?>');

//--></script>

<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>

<script type="text/javascript"><!--
    $('table').on('focus', '.form_req:last', function() {
        var index = parseInt($("input:last", this).data("index")) + 1;
        $(this).after(
            "<tr class='form_req'>"+
            "<td class='left'>"+
            "<input type='text' data-index='"+index+"'"+
            "name='limit_req["+index+"][field] value='' class='tooltips' data-placement='left' title='URL' />"+
            "</td><td class='left'>"+
            "<input type='text' data-index='"+index+"' "+
            "name='limit_req["+index+"][limit] value='' style='width:50px;' class='tooltips' data-placement='left' title='Maximum number' /></tr>"
        );
    });
//--></script>
