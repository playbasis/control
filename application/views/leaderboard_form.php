<div id="content" class="span10">

    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url(); ?>image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'leaderboard'"
                        type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if ($this->session->flashdata('limit_reached')) { ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('limit_reached'); ?></div>
                </div>
            <?php } ?>
            <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
            </div>
            <?php
            if (validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">', '</div>');

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
            echo form_open($form, $attributes);
            ?>
            <div id="tab-general">
                <table class="form">
                    <tbody>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_name'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="name" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_name'); ?>"
                                   value="<?php echo isset($name) ? $name : set_value('name'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_description'); ?>&nbsp;:
                        </td>
                        <td>
                            <textarea name="description" rows="4"
                                      placeholder="<?php echo $this->lang->line('entry_description'); ?>"><?php echo isset($description) ? $description : set_value('description'); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_organization'); ?>&nbsp;:</td>
                        <td>
                            <select id="org_list" class="span3"  name ="selected_org" onchange="org_change(this)">
                                <option label="None" value="" <?php echo isset($selected_org)?"":"selected"?>>
                                <?php foreach ($org_lists as $key => $org){?>
                                    <option label="<?php echo $org['name'] ?> " value="<?php echo $org['_id'] ?>" <?php echo $selected_org==$org['_id']?"selected":""?>>
                                <?php } ?>

                            </select>
                        </td>
                    </tr>
                    <tr id="role_list" <?php echo (isset($selected_org) && $selected_org!="")? "":"style='display:none;'";?>>
                        <td>
                            <?php echo $this->lang->line('entry_role'); ?>&nbsp;:
                        </td>
                        <td>
                            <input type="text" name="role" size="100"
                                   placeholder="<?php echo $this->lang->line('entry_role'); ?>"
                                   value="<?php echo isset($role) ? $role : set_value('role'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                                <span
                                    class="required">*&nbsp;</span><?php echo $this->lang->line('entry_rankby'); ?>&nbsp;:
                        </td>
                        <td>
                            <span class="dropdown">
                            <select id="rankby_list" class="span2"  name ="rankBy" onchange="rankby_change(this)">
                                <option label="Action" value="action" <?php echo $rankBy=="action"?"selected":""?>>
                                <option label="Reward" value="reward" <?php echo $rankBy=="reward"?"selected":""?>>
                            </select>
                            </span>
                            <span class="dropdown">
                            <select id="action_list" class="span2"  name ="selected_action" onchange="action_change(this)"
                                <?php echo (isset($rankBy) && $rankBy=="reward")? "style='display:none;'":"";?>>
                                <?php foreach ($actions as $key => $action){?>
                                <option label="<?php echo $action['name'] ?> " value="<?php echo $action['name'] ?>" <?php echo $selected_action==$action['name']?"selected":""?>>
                                    <?php } ?>

                            </select>
                            </span>
                            <span class="dropdown">
                            <select id="mode_list" class="span2"  name ="mode" <?php echo (isset($rankBy) && $rankBy=="reward")? "style='display:none;'":"";?>>
                                <?php if(isset($selected_org) && $selected_org!="") {?>
                                <option label="Accumulate of" value="sum" <?php echo mode=="sum"?"selected":""?>>
                                    <?php }else {?>
                                <option label="Summary of" value="sum" <?php echo mode=="sum"?"selected":""?>>
                                <option label="Count of" value="mode" <?php echo mode=="mode"?"selected":""?>>
                                    <?php }?>
                            </select>
                            </span>
                            <span class="dropdown">
                            <select id="dataset" class="span3"  name ="selected_param">
                                <?php if ($rankBy !="reward") {
                                foreach ($actions as $index => $action) {
                                    if ($action['name'] == $selected_action)break;
                                }
                                if (!isset($selected_action) ) $index = 0;
                                foreach ($actions[$index]['init_dataset'] as $key => $dataset){?>
                                <option label="<?php echo $dataset['label'] ?> " value="<?php echo $dataset['param_name'] ?>" <?php echo $selected_param==$dataset['param_name']?"selected":""?>>
                                    <?php }}else {foreach ($rewards_list as $key => $dataset){?>
                                <option label="<?php echo $dataset['name'] ?> " value="<?php echo $dataset['name'] ?>" <?php echo $selected_param==$dataset['name']?"selected":""?>>
                                    <?php }} ?>
                            </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="required">*&nbsp;</span><?php echo $this->lang->line('entry_status'); ?>&nbsp;:
                        </td>
                        <td>
                            <div class="control-group">
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="status" id="radio_status_enable" value="enable"
                                            <?php echo $status == true ? "checked=\"checked\"" : set_radio('status',
                                                'enable', true); ?>>
                                        <?php echo $this->lang->line('entry_status_enable'); ?>
                                    </label>
                                </div>
                                <div class="controls">
                                    <label class="control-label">
                                        <input type="radio" name="status" id="radio_status_disabled" value="disable"
                                            <?php echo $status == false ? "checked=\"checked\"" : set_radio('status',
                                                'disable'); ?>>
                                        <?php echo $this->lang->line('entry_status_disable'); ?>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_occur'); ?>:</td>
                        <td>
                            <div class="control-group">
                                <div class="btn-group" data-toggle="buttons-radio" >
                                    <button type="button" class="btn btn-primary <?php echo (isset($occur_once) && $occur_once)?"active":"" ?>" onclick="occurence_change(this)" value="once">Once at</button>
                                    <button type="button" class="btn btn-primary <?php echo (isset($occur_once) && $occur_once)?"":"active" ?>" onclick="occurence_change(this)" value="repeat">Repeat Until</button>
                                    <input type="hidden" id="occurence_id" name="occur_once" value="<?php echo (isset($occur_once) && $occur_once)?"true":"false" ?>">
                                </div>
                            </div>
                            <span>
                                <input type="text" class="date" name="month" id="monthpicker" size="50"
                                       placeholder="<?php echo $this->lang->line('entry_month'); ?>"
                                       value="<?php echo isset($month) && $month ? date('Y-m',
                                           strtotime(datetimeMongotoReadable($month))) : ''; ?>"/>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_reward'); ?>:</td>
                        <td>
                            <div class="row-fluid">
                                <table class="table table-bordered" id="new-branches-table">

                                    <tbody>
                                    <div class="rank-wrapper">
                                        <?php if(isset($rewards) && !empty($rewards)){ ?>
                                        <?php foreach($rewards as $key => $reward){ ?>
                                        <div class="rank-item-wrapper" dataRankId="<?php echo $key ?>">
                                            <div class="box-header box-rank-header overflow-visible">
                                                <h2> Rank <?php echo $key ?></h2>
                                                <div class="box-icon">
<!--                                                    <a href="javascript:void(0)" class="btn btn-danger right remove-rank-btn dropdown-toggle" data-toggle="dropdown">Delete </a>-->
                                                    <span class="break"></span>
                                                    <a href="javaScript:void()"><i class="icon-chevron-up"></i></a>
                                                </div>
                                            </div>
                                            <div class="box-content">
                                            <div class="box box-add-item rewards-wrapper">
                                                <div class="box-header overflow-visible">
                                                    <h2><i class="icon-certificate"></i><span class="break"></span> Rewards </h2>
                                                    <div class="box-icon box-icon-action">
                                                        <a href="javascript:void(0)" class="btn btn-primary right add-rewards-btn dropdown-toggle" data-toggle="dropdown" > + Add Reward</a>
                                                        <ul class="dropdown-menu add-rewards-menu" role="menu" aria-labelledby="dropdownMenu">
                                                            <li class="add-point"><a tabindex="-1" href="javascript:void(0)" >POINT</a></li>
                                                            <li class="add-exp"><a tabindex="-1" href="javascript:void(0)" >EXP</a></li>
                                                            <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CURRENCY</a></li>
                                                            <li class="add-goods"><a tabindex="-1" href="javascript:void(0)">GOODS</a></li>
                                                            <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">ITEM</a></li>
                                                            <?php if( isset($emails) && $emails !== null ){ ?>
                                                                <li class="add-email"><a tabindex="-1" href="javascript:void(0)">EMAIL</a></li>
                                                            <?php } ?>
                                                            <?php if( isset($smses) && $smses !== null ){ ?>
                                                                <li class="add-sms"><a tabindex="-1" href="javascript:void(0)">SMS</a></li>
                                                            <?php } ?>
                                                            <?php if( isset($pushes) && $pushes !== null ){ ?>
                                                                <li class="add-push"><a tabindex="-1" href="javascript:void(0)">PUSH</a></li>
                                                            <?php } ?>
                                                        </ul>
                                                        <span class="break"></span>
                                                        <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                                                    </div>
                                                </div>
                                                <div class="box-content">
                                                    <div class='rewards-container' dataRankId="<?php echo $key ?>">
                                                        <?php if(isset($rewards[$key]['point']['reward_value'])){ ?>
                                                            <div class="points-wrapper rewards-type well">
                                                                <h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                                                <label class="span4">Points:</label>
                                                                <input type="text" name="rewards[<?php echo $key ?>][point][reward_value]" placeholder="Points" value = "<?php echo $rewards[$key]['point']['reward_value'] ?>">
                                                                <input type="hidden" name="rewards[<?php echo $key ?>][point][reward_type]" value="POINT">
                                                                <input type="hidden" name="rewards[<?php echo $key ?>][point][reward_id]" value="<?php echo $rewards[$key]['point']['reward_id']; ?>">
                                                            </div>
                                                        <?php } ?>

                                                        <?php if(isset($rewards[$key]['exp']['reward_value'])){ ?>
                                                            <div class="exp-wrapper rewards-type well">
                                                                <h3>Exp <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                                                <label class="span4">Exp:</label>
                                                                <input type="text" name="rewards[<?php echo $key ?>][exp][reward_value]" placeholder="Exp" value = "<?php echo $rewards[$key]['exp']['reward_value']; ?>">
                                                                <input type="hidden" name="rewards[<?php echo $key ?>][exp][reward_type]" value="EXP">
                                                                <input type="hidden" name="rewards[<?php echo $key ?>][exp][reward_id]" value="<?php echo $rewards[$key]['exp']['reward_id'] ?>">
                                                            </div>
                                                        <?php } ?>
                                                        <?php if(isset($rewards[$key]['custompoints'])){ ?>
                                                            <div class="custompoints-wrapper rewards-type well">
                                                                <h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Currency</a></h3>
                                                                <?php foreach($rewards[$key]['custompoints'] as $point){ ?>
                                                                    <div class="item-container">
                                                                        <div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="<?php echo $point['reward_id'] ?>">
                                                                            <div class="span7"><?php foreach($customPoints as $p){if($p['reward_id']==$point['reward_id']){echo $p['name'];}} ?></div><div class="span3"><small>value</small>
                                                                                <!-- <div class="span7"><?php //foreach($customPoints as $p){if($p['_id']==$point['reward_id']){echo $p['name'];}} ?></div><div class="span3"><small>value</small>                                 -->
                                                                                <input type="text" name="rewards[<?php echo $key ?>][custompoints][<?php echo $point['reward_id'] ?>][reward_value]" placeholder="Value" value="<?php echo $point['reward_value']; ?>">
                                                                                <input type="hidden" name="rewards[<?php echo $key ?>][custompoints][<?php echo $point['reward_id'] ?>][reward_type]" value="CUSTOM_POINT">
                                                                                <input type="hidden" name="rewards[<?php echo $key ?>][custompoints][<?php echo $point['reward_id'] ?>][reward_id]" value="<?php echo $point['reward_id'] ?>">
                                                                            </div>
                                                                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if(isset($rewards[$key]['goods'])){ ?>
                                                            <div class="goods-wrapper rewards-type well">
                                                                <h3>Goods  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-goods-btn">+ Add Goods</a></h3>
                                                                <div class="item-container">
                                                                    <?php foreach($rewards[$key]['goods'] as $item){ ?>
                                                                        <div class="clearfix item-wrapper goods-item-wrapper" data-id-goods="<?php echo $item['reward_id'] ?>">
                                                                            <div class="span2 text-center">
                                                                                <img src="<?php echo $item['reward_data']['image'];?>" alt="" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');">
                                                                            </div>
                                                                            <div class="span7"><?php echo isset($item['reward_data']['group']) ? $item['reward_data']['group'] : $item['reward_data']['name'];?></div>
                                                                            <div class="span1">
                                                                                <small>value</small>
                                                                                <input type="text" name="rewards[<?php echo $key ?>][goods][<?php echo $item['reward_id'] ?>][reward_value]" placeholder="Value" value="<?php echo $item['reward_value'] ?>">
                                                                                <input type="hidden" name="rewards[<?php echo $key ?>][goods][<?php echo $item['reward_id'] ?>][reward_id]" value="<?php echo $item['reward_id'] ?>">
                                                                                <input type="hidden" name="rewards[<?php echo $key ?>][goods][<?php echo $item['reward_id'] ?>][reward_type]" value="GOODS"></div>
                                                                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if(isset($rewards[$key]['badges'])){ ?>
                                                            <div class="badges-wrapper rewards-type well">
                                                                <h3>Items  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Items</a></h3>
                                                                <div class="item-container">
                                                                    <?php foreach($rewards[$key]['badges'] as $badge){ ?>
                                                                        <div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="<?php echo $badge['reward_id'] ?>">
                                                                            <div class="span2 text-center">
                                                                                <img src="<?php echo $badge['reward_data']['image'];?>" alt="" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');">
                                                                            </div>
                                                                            <div class="span7"><?php echo $badge['reward_data']['name'];?></div>
                                                                            <div class="span1">
                                                                                <small>value</small>
                                                                                <input type="text" name="rewards[<?php echo $key ?>][badges][<?php echo $badge['reward_id'] ?>][reward_value]" placeholder="Value" value="<?php echo $badge['reward_value'] ?>">
                                                                                <input type="hidden" name="rewards[<?php echo $key ?>][badges][<?php echo $badge['reward_id'] ?>][reward_id]" value="<?php echo $badge['reward_id'] ?>">
                                                                                <input type="hidden" name="rewards[<?php echo $key ?>][badges][<?php echo $badge['reward_id'] ?>][reward_type]" value="BADGE"></div>
                                                                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>


                                                        <?php if(isset($rewards[$key]['feedbacks']['email'])){ ?>
                                                            <div class="emails-wrapper rewards-type well">
                                                                <h3>Emails  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-email-btn">+ Add Emails</a></h3>
                                                                <div class="item-container">
                                                                    <?php foreach($rewards[$key]['feedbacks']['email'] as $email){ ?>


                                                                        <div class="clearfix item-wrapper emails-item-wrapper" data-id-email="<?php echo $email['template_id'] ?>">
                                                                            <h4 class="span10"><?php echo $email['feedback_data']['name'];?><a href="#" data-toggle="modal" data-backdrop="false" data-target="#modal-preview-quest-<?php echo $email['template_id'] ?>">[Preview]</a></h4>
                                                                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                            <div class="clearfix"></div>
                                                                            <div class="clearfix">
                                                                                <div class="span3">Subject: </div>
                                                                                <div class="span8">
                                                                                    <input type="text" name ="rewards[<?php echo $key ?>][feedbacks][email][<?php echo $email['template_id'] ?>][subject]" placeholder="Value" value="<?php echo $email['subject'] ?>"/>
                                                                                    <input type="hidden" name="rewards[<?php echo $key ?>][feedbacks][email][<?php echo $email['template_id'] ?>][template_id]" value="<?php echo $email['template_id'] ?>"/>
                                                                                    <input type="hidden" name="rewards[<?php echo $key ?>][feedbacks][email][<?php echo $email['template_id'] ?>][feedback_type]" value="EMAIL"/>
                                                                                </div>
                                                                                <div id="modal-preview-quest-<?php echo $email['template_id'] ?>"  class="modal hide fade modal-select in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                                    <div class="modal-header">
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                                        <h4 id="myModalLabel">Preview: <?php echo $email['feedback_data']['name'];?></h4>
                                                                                    </div>
                                                                                    <div class="modal-body"><?php echo $email['feedback_data']['message'];?></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if(isset($rewards[$key]['feedbacks']['sms'])){ ?>
                                                            <div class="smses-wrapper rewards-type well">
                                                                <h3>SMSes  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-sms-btn">+ Add SMSes</a></h3>
                                                                <div class="item-container">
                                                                    <?php foreach($rewards[$key]['feedbacks']['sms'] as $sms){ ?>

                                                                        <div class="clearfix item-wrapper smses-item-wrapper" data-id-sms="<?php echo $sms['template_id'] ?>">
                                                                            <h4 class="span10"><?php echo $sms['feedback_data']['name'];?></h4>
                                                                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                            <div class="clearfix"></div>
                                                                            <div class="clearfix">
                                                                                <div class="span2">Body: </div>
                                                                                <div class="span10">
                                                                                    <input type="hidden" name="rewards[<?php echo $key ?>][feedbacks][sms][<?php echo $sms['template_id'] ?>][template_id]" value="<?php echo $sms['template_id'] ?>"/>
                                                                                    <input type="hidden" name="rewards[<?php echo $key ?>][feedbacks][sms][<?php echo $sms['template_id'] ?>][feedback_type]" value="SMS"/>
                                                                                    <?php echo $sms['feedback_data']['message'];?></div>
                                                                            </div>
                                                                        </div>

                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if(isset($rewards[$key]['feedbacks']['push'])){ ?>
                                                            <div class="pushes-wrapper rewards-type well">
                                                                <h3>PUSHes  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-push-btn">+ Add PUSHes</a></h3>
                                                                <div class="item-container">
                                                                    <?php foreach($rewards[$key]['feedbacks']['push'] as $push){ ?>

                                                                        <div class="clearfix item-wrapper pushes-item-wrapper" data-id-push="<?php echo $push['template_id'] ?>">
                                                                            <h4 class="span10"><?php echo $push['feedback_data']['name'];?></h4>
                                                                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                            <div class="clearfix"></div>
                                                                            <div class="clearfix">
                                                                                <div class="span2">Body: </div>
                                                                                <div class="span10">
                                                                                    <input type="hidden" name="rewards[<?php echo $key ?>][feedbacks][push][<?php echo $push['template_id'] ?>][template_id]" value="<?php echo $push['template_id'] ?>"/>
                                                                                    <input type="hidden" name="rewards[<?php echo $key ?>][feedbacks][push][<?php echo $push['template_id'] ?>][feedback_type]" value="PUSH"/>
                                                                                    <?php echo $push['feedback_data']['message'];?></div>
                                                                            </div>
                                                                        </div>

                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        <?php } ?> <!-- end foreach loop -->
                                        <?php } ?> <!-- end check if ranks exists -->
                                    </div><!-- .rank-wrapper -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" style="text-align: center">
                                                <div class="row-fluid">
                                                    <div class="offset3 span3">
                                                        <a class="btn btn-primary add-rank-btn btn-block" id="add"><i class="fa fa-plus"></i>&nbsp;Add Rank</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<!-- Modal Badge -->
<div id="modal-select-badge" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Item</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php for($i=0 ; $i < count($badges) ; $i++){ ?>
                <label>

                    <div class="select-item clearfix" data-id="<?php echo $i; ?>" data-id-badge="<?php echo $badges[$i]['badge_id'] ?>">
                        <div class="span1 text-center">
                            <input type="checkbox" name="selected[]" value="<?php $badges[$i]['_id']; ?>">
                        </div>
                        <div class="span2 image text-center">
                            <img height="50" width="50" src="<?php echo S3_IMAGE.$badges[$i]['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                        </div>
                        <div class="span9 title"><?php echo $badges[$i]['name'];?></div>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-badge-btn" data-dismiss="modal">Select</button>
    </div>
</div>

<!-- Modal Goods -->
<div id="modal-select-goods" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Goods</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php for($i=0 ; $i < count($goods_items) ; $i++){ ?>
                <label>

                    <div class="select-item clearfix" data-id="<?php echo $i; ?>" data-id-goods="<?php echo $goods_items[$i]['goods_id'] ?>">
                        <div class="span1 text-center">
                            <input type="checkbox" name="selected[]" value="<?php $goods_items[$i]['goods_id']; ?>">
                        </div>
                        <div class="span2 image text-center">
                            <img height="50" width="50" src="<?php echo S3_IMAGE.$goods_items[$i]['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                        </div>
                        <div class="span9 title"><?php echo isset($goods_items[$i]['group']) ? $goods_items[$i]['group'] : $goods_items[$i]['name'];?></div>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-goods-btn" data-dismiss="modal">Select</button>
    </div>
</div>


<!-- Modal Email -->
<div id="modal-select-email" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Email</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php
            if( is_array($emails) ) foreach ($emails as $key => $email) {
                if( empty( $email['status'] ) || $email['status'] == false ){
                    continue;
                }
                ?>
                <label>
                    <div class="select-item clearfix" data-id="<?php echo $key; ?>" data-id-email="<?php echo $email['_id'] ?>">
                        <div class="span1 text-center">
                            <input type="checkbox" name="selected[]" value="<?php echo $email['_id']; ?>">
                        </div>
                        <div class="span11 title"><?php echo $email['name'];?></div>
                        <div class="data-email-body" style="display:none"><?php echo $email['body'] ?></div>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-email-btn" data-dismiss="modal">Select</button>
    </div>
</div>

<!-- Modal SMS -->
<div id="modal-select-sms" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select SMS</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php
            foreach ($smses as $key => $sms) {
                if( empty( $sms['status'] ) || $sms['status'] == false ){
                    continue;
                }
                ?>
                <label>
                    <div class="select-item clearfix" data-id="<?php echo $key; ?>" data-id-sms="<?php echo $sms['_id'] ?>" >
                        <div class="span1 text-center">
                            <input type="checkbox" name="selected[]" value="<?php echo $sms['_id']; ?>">
                        </div>
                        <div class="span11 title"><?php echo $sms['name'];?></div>
                        <div class="data-sms-body" style="display:none"><?php echo $sms['body'] ?></div>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-sms-btn" data-dismiss="modal">Select</button>
    </div>
</div>

<!-- Modal PUSH -->
<div id="modal-select-push" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select PUSH</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php
            foreach ($pushes as $key => $push) {
                if( empty( $push['status'] ) || $push['status'] == false ){
                    continue;
                }
                ?>
                <label>
                    <div class="select-item clearfix" data-id="<?php echo $key; ?>" data-id-push="<?php echo $push['_id'] ?>" >
                        <div class="span1 text-center">
                            <input type="checkbox" name="selected[]" value="<?php echo $push['_id']; ?>">
                        </div>
                        <div class="span11 title"><?php echo $push['name'];?></div>
                        <div class="data-push-body" style="display:none"><?php echo $push['body'] ?></div>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-push-btn" data-dismiss="modal">Select</button>
    </div>
</div>


<!-- Modal Custom Points -->
<div id="modal-select-custompoint" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Currency</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php for($i=0 ; $i < count($customPoints) ; $i++){ ?>
                <label>
                    <div class="select-item clearfix" data-id="<?php echo $i; ?>" data-id-custompoint = "<?php echo $customPoints[$i]['reward_id']; ?>">
                        <!-- <div class="select-item clearfix" data-id="<?php //echo $i; ?>" data-id-custompoint = "<?php //echo $customPoints[$i]['_id']; ?>"> -->
                        <div class="span1 text-center">
                            <input type="checkbox" name="selected[]" value="<?php $customPoints[$i]['reward_id']; ?>">
                            <!-- <input type="checkbox" name="selected[]" value="<?php //$customPoints[$i]['_id']; ?>"> -->
                        </div>
                        <div class="span11 title"><?php echo $customPoints[$i]['name'];?></div>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-custompoint-btn" data-dismiss="modal">Select</button>
    </div>
</div>

<link id="base-style" rel="stylesheet" type="text/css"
      href="<?php echo base_url(); ?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css"/>
<script type="text/javascript"
        src="<?php echo base_url(); ?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">

    //======================== Declare Global Variable ========================
    var startDateTextBox = $('#date_start');
    var endDateTextBox = $('#date_end');

    //======================== Rank ========================
    var countRankId = <?php echo isset($rewards)?count($rewards):0?>;
    var itemRankId = 0;
    $(function () {
        $('#tabs a').tabs();

        startDateTextBox.datepicker({
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datepicker('setDate', testStartDate);
                }
                else {
                    endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
            }
        });
        endDateTextBox.datepicker({
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datepicker('setDate', testEndDate);
                }
                else {
                    startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
            }
        });
    });
    function buildDropdownModeList(isAccumulated){
        var dropdown_modelist = document.getElementById("mode_list");
        while (dropdown_modelist.firstChild) {
            dropdown_modelist.removeChild(dropdown_modelist.firstChild);
        }

        if (isAccumulated){

            var o = document.createElement("option");

            o.value = "sum";
            o.text = "Accumulate of";
            dropdown_modelist.add(o);
        }
        else{
            var o = document.createElement("option");
            o.value = "sum";
            o.text = "Summary of";
            dropdown_modelist.add(o);

            o = document.createElement("option");
            o.value = "count";
            o.text = "Count of";
            dropdown_modelist.add(o);
        }

    }
    function buildDropdownDatasetList(name, isAction){
        var dropdown_dataset = document.getElementById("dataset");
        while (dropdown_dataset.firstChild) {
            dropdown_dataset.removeChild(dropdown_dataset.firstChild);
        }

        if (isAction){
            var action_list = <?php echo json_encode($actions); ?>;
            for (var i in action_list ){
                if (name == action_list[i]['name']){
                    break;
                }
            }
            var data_set = action_list[i]['init_dataset']
            var parameter_name = [];
            var parameter_label = [];


            for (var j in data_set){
                var o = document.createElement("option");

                o.value = data_set[j]['param_name'];
                o.text = data_set[j]['label'];
                dropdown_dataset.add(o);
            }
        }
        else{
            var reward_list = <?php echo json_encode($rewards_list); ?>;
            for (var j in reward_list){
                var o = document.createElement("option");
                o.value = o.text = reward_list[j]['name'];
                dropdown_dataset.add(o);
            }
        }

    }
    function action_change(elem){
        buildDropdownDatasetList(elem.value,true);
    }
    function rankby_change(elem){
        var act = document.getElementById('action_list');
        var mode = document.getElementById('mode_list');
        if (elem.value == "action"){
            act.style.display = "";
            mode.style.display = "";
            buildDropdownDatasetList(act.value, true);
        }
        else{
            act.style.display = "none";
            mode.style.display = "none";
            buildDropdownDatasetList(null, false);
        }
    }
    function org_change(elem){
        var role = document.getElementById('role_list');
        if (elem.value == ""){
            role.style.display = "none";
            buildDropdownModeList(false);
        }
        else{
            role.style.display = "";
            buildDropdownModeList(true);
        }
    }



    $('.add-rank-btn').click(function(){

        countRankId++;

        itemRankId = countRankId;

        var itemRankHtml = '<div class="rank-item-wrapper" dataRankId="'+itemRankId+'">\
                        <div class="box-header box-rank-header overflow-visible">\
                            <h2> Rank '+itemRankId+'</h2>\
                            <div class="box-icon">\
                                <span class="break"></span>\
                                <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                            </div>\
                        </div>\
                        <div class="box-content clearfix">\
                            <div class="span6">\
                                <div class="box box-add-item rewards-wrapper">\
                                    <div class="box-header overflow-visible">\
                                        <h2><i class="icon-certificate"></i><span class="break"></span>Rewards</h2>\
                                        <div class="box-icon box-icon-action">\
                                            <a href="javascript:void(0)" class="btn btn-primary right add-rewards-btn dropdown-toggle" data-toggle="dropdown"> + Add Reward</a>\
                                            <ul class="dropdown-menu add-rewards-menu" role="menu" aria-labelledby="dropdownMenu">\
                                              <li class="add-point"><a tabindex="-1" href="javascript:void(0)" >POINT</a></li>\
                                              <li class="add-exp"><a tabindex="-1" href="javascript:void(0)" >EXP</a></li>\
                                              <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CURRENCY</a></li>\
                                              <li class="add-goods"><a tabindex="-1" href="javascript:void(0)">GOODS</a></li>\
                                              <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">ITEM</a></li>\
                                            <?php if( $emails !== null ){ ?>
                                                <li class="add-email"><a tabindex="-1" href="javascript:void(0)">EMAIL</a></li>\
                                            <?php } ?>
                                            <?php if( $smses !== null ){ ?>
                                            <li class="add-sms"><a tabindex="-1" href="javascript:void(0)">SMS</a></li>\
                                            <?php } ?>
                                            <?php if( $pushes !== null ){ ?>
                                            <li class="add-push"><a tabindex="-1" href="javascript:void(0)">PUSH</a></li>\
                                            <?php } ?>
                                            </ul>\
                                            <span class="break"></span>\
                                            <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>\
                                         </div>\
                                     </div>\
                                     <div class="box-content">\
                                     <div class="rewards-container" dataRankId="'+itemRankId+'"></div>\
                                    </div>\
                                     </div>\
                                </div>\
                            </div>\
                        </div>';
        $('.rank-item-wrapper>.box-content').slideUp();
        $('.rank-wrapper').append(itemRankHtml);

        init_additem_event({
            type:'completion',
            parent:'ranks',
            id:itemRankId
        });

        init_additem_event({
            type:'rewards',
            parent:'ranks',
            id:itemRankId
        });
        init_rank_event();
    });
    function init_rank_event(){

        $('.rank-item-wrapper .box-rank-header').unbind().bind('click',function(data){
            var $target = $(this).next('.box-content');

            if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.remove-rank-btn').unbind().bind('click',function(data){
            var $target = $(this).parent().parent().parent();

            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
                init_rank_event()
            }
        });
    }
    init_rank_event();
    $(this).toggleClass("active");
    function occurence_change(elem){
        var occur_radio = document.getElementById('occurence_id');
        if (elem.value == "once")
        {
            occur_radio.value = true;
        }
        else{
            occur_radio.value = false;
        }

    }
    init_additem_event({type:'rewards'});
    $('.rank-item-wrapper').each(function(){
        itemRankId++;
        init_additem_event({
            type:'rewards',
            id:itemRankId
        });
    })
    function init_additem_event(target){
        $('.date').datepicker({dateFormat: 'yy-mm'});
        $('[data-toggle=modalObj]').modal({show:false});

        var type = target.type;
        var parent = target.parent || 'quests';
        var id = target.id || null;

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+id+']' );
        var containerObj = $('.rewards-container[dataRankId='+id+']');


        var menuBtn = wrapperObj.find('.add-'+type+'-btn'),
            menuObj = wrapperObj.find('.add-'+type+'-menu'),

            addGoodsObj = menuObj.find('.add-goods'),
            addPointObj = menuObj.find('.add-point'),
            addExpObj = menuObj.find('.add-exp'),
            addCustomPointObj = menuObj.find('.add-custompoint'),
            addBadgeObj = menuObj.find('.add-badge'),
            addQuizObj = menuObj.find('.add-quiz'),
            addActionObj = menuObj.find('.add-action'),
            addEmailObj = menuObj.find('.add-email'),
            addSmsObj = menuObj.find('.add-sms'),
            addPushObj = menuObj.find('.add-push')

        menuBtn.unbind().bind('click',function(data){
            wrapperObj.find('.box-content').show();
        });

        containerObj.find('.no-item').remove();


        if(containerObj.children().length <= 0){
            containerObj.append('<h3 class="no-item">No Item</h3>');
        }




        if(containerObj.has('.points-wrapper').length){
            addPointObj.addClass('disabled');
            addPointObj.unbind();
        }else{
            addPointObj.removeClass('disabled');
            addPointObj.unbind().bind('click',function(data){
                addPoints(target);
            });
        }

        if(containerObj.has('.exp-wrapper').length){
            addExpObj.addClass('disabled');
            addExpObj.unbind();
        }else{
            addExpObj.removeClass('disabled');
            addExpObj.unbind().bind('click',function(data){
                addExp(target);
            });
        }
        //Add Goods

        if(containerObj.has('.goods-wrapper').length){
            addGoodsObj.removeClass('disabled');
            addGoodsObj.unbind().bind('click',function(data){
                setModalGoodsItem(target);
            });
            containerObj.find('.goods-wrapper .add-goods-btn').bind('click',function(data){
                setModalGoodsItem(target);
            });
        }else{
            addGoodsObj.removeClass('disabled');
            addGoodsObj.unbind().bind('click',function(data){
                addGoods(target);
                setModalGoodsItem(target);
            });
        }
        $('.select-goods-btn').unbind().bind('click',function(data){
            selectGoodsItem();
        });
        //Add Badges

        if(containerObj.has('.badges-wrapper').length){
            addBadgeObj.removeClass('disabled');
            addBadgeObj.unbind().bind('click',function(data){
                setModalBadgesItem(target);
            });
            containerObj.find('.badges-wrapper .add-badge-btn').bind('click',function(data){
                setModalBadgesItem(target);
            });
        }else{
            addBadgeObj.removeClass('disabled');
            addBadgeObj.unbind().bind('click',function(data){
                addBadges(target);
                setModalBadgesItem(target);
            });
        }
        $('.select-badge-btn').unbind().bind('click',function(data){
            selectBadgesItem();
        });



        //Add Email
        if(containerObj.has('.emails-wrapper').length){
            addEmailObj.removeClass('disabled');
            addEmailObj.unbind().bind('click',function(data){
                setModalEmailsItem(target);
            });
            containerObj.find('.emails-wrapper .add-email-btn').bind('click',function(data){
                setModalEmailsItem(target);
            });
        }else{
            addEmailObj.removeClass('disabled');
            addEmailObj.unbind().bind('click',function(data){
                addEmails(target);
                setModalEmailsItem(target);
            });
        }
        $('.select-email-btn').unbind().bind('click',function(data){
            selectEmailsItem();
        });

        //Add Sms
        if(containerObj.has('.smses-wrapper').length){
            addSmsObj.removeClass('disabled');
            addSmsObj.unbind().bind('click',function(data){
                setModalSmsesItem(target);
            });
            containerObj.find('.smses-wrapper .add-sms-btn').bind('click',function(data){
                setModalSmsesItem(target);
            });
        }else{
            addSmsObj.removeClass('disabled');
            addSmsObj.unbind().bind('click',function(data){
                addSmses(target);
                setModalSmsesItem(target);
            });
        }
        $('.select-sms-btn').unbind().bind('click',function(data){
            selectSmsesItem();
        });
        //Add Push
        if(containerObj.has('.pushes-wrapper').length){
            addPushObj.removeClass('disabled');
            addPushObj.unbind().bind('click',function(data){
                setModalPushesItem(target);
            });
            containerObj.find('.pushes-wrapper .add-push-btn').bind('click',function(data){
                setModalPushesItem(target);
            });
        }else{
            addPushObj.removeClass('disabled');
            addPushObj.unbind().bind('click',function(data){
                addPushes(target);
                setModalPushesItem(target);
            });
        }
        $('.select-push-btn').unbind().bind('click',function(data){
            selectPushesItem();
        });

        //Add Custom point
        if(containerObj.has('.custompoints-wrapper').length){
            addCustomPointObj.removeClass('disabled');
            addCustomPointObj.unbind().bind('click',function(data){
                setModalCustompointsItem(target);
            });
            containerObj.find('.custompoints-wrapper .add-custompoint-btn').bind('click',function(data){
                setModalCustompointsItem(target);
            });
        }else{
            addCustomPointObj.removeClass('disabled');
            addCustomPointObj.unbind().bind('click',function(data){
                addCustompoints(target);
                setModalCustompointsItem(target);
            });
        }

        $('.select-custompoint-btn').unbind().bind('click',function(data){
            selectCustompointsItem();
        });


        containerObj.find('.remove').unbind('click').bind('click',function(data){
            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $(this).parent().parent().remove();
                init_additem_event(target);
            }
        });

        containerObj.find('.item-remove').unbind('click').bind('click',function(data){
            var r = confirm("Are you sure to remove!");
            if (r == true) {
                var containObj = $(this).parent().parent().parent().parent();

                $(this).parent().parent().remove();
                if(containObj.find('.item-remove').length <= 0 ){
                    containObj.remove();
                }

                init_additem_event(target);
            }
        });

    }

    function addPoints(target){
        var type = target.type;
        var typeElement = checkTypeReward(type);
        var id = target.id || null;
        var parent = 'rewards';
        var inputHtml = '';


        inputHtml = '<input type="text" name = "'+parent+'['+id+'][point]['+typeElement+'_value]" placeholder = "Points">\
                <input type="hidden" name = "'+parent+'['+id+'][point]['+typeElement+'_type]" value = "POINT"/>\
                <input type="hidden" name = "'+parent+'['+id+'][point]['+typeElement+'_id]" value = "<?php echo $point_id; ?>"/>';


        var pointsHead = '<h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>';


        var inputCompletionHtml = '';
        if(type == 'completion'){
            inputCompletionHtml = '<br><label class="span4">Title:</label><input type="text" name ="'+parent+'['+id+'][point]['+typeElement+'_title]" placeholder="Title" value="">';
        }

        var pointsHtml = ' <div class="points-wrapper '+type+'-type well">'+pointsHead+'<label class="span4">Points:</label>'+inputHtml+inputCompletionHtml+'</div>';

        target.html = pointsHtml;
        render(target);
    }

    function addExp(target){
        var type = target.type;

        var typeElement = checkTypeReward(type);
        var id = target.id || null;
        var parent = 'rewards';
        var inputHtml = '';

        inputHtml = '<input type="text" name = "'+parent+'['+id+'][exp]['+typeElement+'_value]" placeholder = "Exp">\
                <input type="hidden" name = "'+parent+'['+id+'][exp]['+typeElement+'_type]" value = "EXP"/>\
                <input type="hidden" name = "'+parent+'['+id+'][exp]['+typeElement+'_id]" value = "<?php echo $exp_id; ?>"/>';


        var expHead = '<h3>Exp <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

        var expHtml = ' <div class="exp-wrapper '+type+'-type well">'+expHead+'<label class="span4">Exp:</label>'+inputHtml+'</div>';

        target.html = expHtml;

        render(target);
    }

    function addCustompoints(target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quest';

        var customPointsHead = '<h3>Currency  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Currency</a></h3>';
        var customPointsHtml = '<div class="custompoints-wrapper '+type+'-type well">'+customPointsHead+'<div class="item-container"></div></div>';

        target.html = customPointsHtml;

        render(target);
    }

    function addGoods(target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quest';

        var goodsHead = '<h3>Goods  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-goods-btn">+ Add Goods</a></h3>';
        var goodsHtml = '<div class="goods-wrapper '+type+'-type well">'+goodsHead+'<div class="item-container"></div></div>';

        target.html = goodsHtml;

        render(target);
    }
    function addBadges(target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quest';

        var badgesHead = '<h3>Items  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Items</a></h3>';
        var badgesHtml = '<div class="badges-wrapper '+type+'-type well">'+badgesHead+'<div class="item-container"></div></div>';

        target.html = badgesHtml;

        render(target);
    }


    function addEmails(target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quest';

        var emailsHead = '<h3>Emails  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-email-btn">+ Add Emails</a></h3>';
        var emailsHtml = '<div class="emails-wrapper '+type+'-type well">'+emailsHead+'<div class="item-container"></div></div>';

        target.html = emailsHtml;

        render(target);
    }

    function addSmses(target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quest';

        var smsesHead = '<h3>Smses  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-sms-btn">+ Add SMSes</a></h3>';
        var smsesHtml = '<div class="smses-wrapper '+type+'-type well">'+smsesHead+'<div class="item-container"></div></div>';

        target.html = smsesHtml;

        render(target);
    }

    function addPushes(target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quest';

        var pushesHead = '<h3>Pushes  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-push-btn">+ Add PUSHes</a></h3>';
        var pushesHtml = '<div class="pushes-wrapper '+type+'-type well">'+pushesHead+'<div class="item-container"></div></div>';

        target.html = pushesHtml;

        render(target);
    }
    function render(target){
         $('.rewards-container[dataRankId='+target.id+']').append(target.html);
        init_additem_event(target);
    }



    // setModalBadgesItem
    function setModalBadgesItem(target){
        setModalTarget($('#modal-select-badge'),target);
        var type = target.type;

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );


        $('#modal-select-badge input[type=checkbox]').prop('checked', false);
        wrapperObj.find('.badges-item-wrapper').each(function(){
            var idBadgesSelect = $(this).data('id-badge');
            $('#modal-select-badge .select-item[data-id-badge='+idBadgesSelect+'] input[type=checkbox]').prop('checked', true);
        })

        $('#modal-select-badge').modal('show');
    }

    function selectBadgesItem(){
        var modalObj = $('#modal-select-badge');
        var target = {
            "type":modalObj.attr('data-type'),
            "id":modalObj.attr('data-mission-id'),
            "parent":modalObj.attr('data-parent')
        }

        var type = target.type;
        var taget_id = target.id || null;
        var parent = 'rewards';
        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );


        $('#modal-select-badge .select-item').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){

                if(wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').length <= 0) {

                    var id = $(this).data('id-badge');
                    var img = $(this).find('.image img').attr('src');
                    var title = $(this).find('.title').html();
                    var typeElement = checkTypeReward(type);


                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+'][badges]['+id+']['+typeElement+'_value]" placeholder="Value" value="1"/>\
                                <input type="hidden" name="'+parent+'['+taget_id+'][badges]['+id+']['+typeElement+'_id]" value = "'+id+'"/>\
                                <input type="hidden" name="'+parent+'['+taget_id+'][badges]['+id+']['+typeElement+'_type]" value = "BADGE"/>'


                    var inputCompletionHtml = '';
                    if(type == 'completion'){
                        inputCompletionHtml = '<div class="title-row"><div class="span2">Title : </div><div class="span10"><input type="text" name ="'+parent+'['+taget_id+']['+id+']['+typeElement+'_title]" placeholder="Title" value=""></div></div>';
                    }

                    var badgesItemHtml = '<div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="'+id+'">\
                                    <div class="span2 text-center"><img src="'+img+'" alt="" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');">\
                                    </div>\
                                    <div class="span7">'+title+'</div>\
                                    <div class="span1">\
                                    <small>value</small>\
                                    '+inputHtml+'</div>\
                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a>\
                                    </div>'+inputCompletionHtml+'</div>';


                    wrapperObj.find('.badges-wrapper .item-container').append(badgesItemHtml);


                    init_additem_event(target);
                }
            }else{
                if(wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').length >= 1) {
                    wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').remove();
                }
            }
        })
    }

    // setModalGoodsItem
    function setModalGoodsItem(target){
        setModalTarget($('#modal-select-goods'),target);
        var type = target.type;


        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );


        $('#modal-select-goods input[type=checkbox]').prop('checked', false);
        wrapperObj.find('.goods-item-wrapper').each(function(){
            var idGoodsSelect = $(this).data('id-goods');
            $('#modal-select-goods .select-item[data-id-goods='+idGoodsSelect+'] input[type=checkbox]').prop('checked', true);
        })

        $('#modal-select-goods').modal('show');
    }

    function selectGoodsItem(){
        var modalObj = $('#modal-select-goods');
        var target = {
            "type":modalObj.attr('data-type'),
            "id":modalObj.attr('data-mission-id'),
            "parent":modalObj.attr('data-parent')
        }

        var type = target.type;
        var taget_id = target.id || null;
        var parent = 'rewards';
        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );



        $('#modal-select-goods .select-item').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){

                if(wrapperObj.find('.goods-item-wrapper[data-id-goods='+$(this).data('id-goods')+']').length <= 0) {

                    var id = $(this).data('id-goods');
                    var img = $(this).find('.image img').attr('src');
                    var title = $(this).find('.title').html();
                    var typeElement = checkTypeReward(type);

                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+'][goods]['+id+']['+typeElement+'_value]" placeholder="Value" value="1"/>\
                        <input type="hidden" name="'+parent+'['+taget_id+'][goods]['+id+']['+typeElement+'_id]" value = "'+id+'"/>\
                        <input type="hidden" name="'+parent+'['+taget_id+'][goods]['+id+']['+typeElement+'_type]" value = "GOODS"/>'


                    var inputCompletionHtml = '';
                    if(type == 'completion'){
                        inputCompletionHtml = '<div class="title-row"><div class="span2">Title : </div><div class="span10"><input type="text" name ="'+parent+'['+taget_id+']['+id+']['+typeElement+'_title]" placeholder="Title" value=""></div></div>';
                    }

                    var goodsItemHtml = '<div class="clearfix item-wrapper goods-item-wrapper" data-id-goods="'+id+'">\
                            <div class="span2 text-center"><img width="100" src="'+img+'" alt="" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');">\
                            </div>\
                            <div class="span7">'+title+'</div>\
                            <div class="span1">\
                            <small>value</small>\
                            '+inputHtml+'</div>\
                            <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a>\
                            </div>'+inputCompletionHtml+'</div>';


                    wrapperObj.find('.goods-wrapper .item-container').append(goodsItemHtml);


                    init_additem_event(target);
                }
            }else{
                if(wrapperObj.find('.goods-item-wrapper[data-id-goods='+$(this).data('id-goods')+']').length >= 1) {
                    wrapperObj.find('.goods-item-wrapper[data-id-goods='+$(this).data('id-goods')+']').remove();
                }
            }
        })
    }


    // setModalEmailsItem
    function setModalEmailsItem(target){

        setModalTarget($('#modal-select-email'),target);
        var type = target.type;

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-email input[type=checkbox]').prop('checked', false);
        wrapperObj.find('.emails-item-wrapper').each(function(){
            var idEmailsSelect = $(this).data('id-email');
            $('#modal-select-email .select-item[data-id-email='+idEmailsSelect+'] input[type=checkbox]').prop('checked', true);
        })

        $('#modal-select-email').modal('show');
    }

    function selectEmailsItem(){
        var modalObj = $('#modal-select-email');
        var target = {
            "type":modalObj.attr('data-type'),
            "id":modalObj.attr('data-mission-id'),
            "parent":modalObj.attr('data-parent')
        }

        var type = target.type;
        var taget_id = target.id || null;
        var parent = 'rewards';

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-email .select-item').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){

                if(wrapperObj.find('.emails-item-wrapper[data-id-email='+$(this).data('id-email')+']').length <= 0) {

                    var id = $(this).data('id-email');
                    var img = $(this).find('.image img').attr('src');
                    var title = $(this).find('.title').html();
                    var typeElement = 'email';
                    var emailBody = $(this).find('.data-email-body').html();

                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+'][feedbacks][email]['+id+'][subject]" placeholder="Value" value=""/>\
                                <input type="hidden" name="'+parent+'['+taget_id+'][feedbacks][email]['+id+'][template_id]" value="'+id+'"/>\
                                <input type="hidden" name="'+parent+'['+taget_id+'][feedbacks][email]['+id+'][feedback_type]" value="EMAIL"/>'

                    var modelPreviewId = 'modal-preview-'+parent+'-'+taget_id+'-'+id;





                    var emailPreview = '<div id="'+modelPreviewId+'"  class="modal hide fade modal-select in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
                <div class="modal-header">\
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>\
                    <h4 id="myModalLabel">Preview: '+title+'</h4>\
                </div>\
                <div class="modal-body">'+emailBody+'</div>\
            </div>'

                    var emailsItemHtml = '<div class="clearfix item-wrapper emails-item-wrapper" data-id-email="'+id+'">\
                                    <h4 class="span10">'+title+' <a href="#" data-toggle="modal" data-backdrop="false" data-target="#'+modelPreviewId+'">[Preview]</a></h4>\
                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>\
                                    <div class="clearfix"></div>\
                                    <div class="clearfix">\
                                        <div class="span3">Subject: </div>\
                                        <div class="span8">\
                                        '+inputHtml+'</div>\
                                    '+emailPreview+'</div>\
                </div>';


                    wrapperObj.find('.emails-wrapper .item-container').append(emailsItemHtml);

                    init_additem_event(target);
                }
            }else{
                if(wrapperObj.find('.emails-item-wrapper[data-id-email='+$(this).data('id-email')+']').length >= 1) {
                    wrapperObj.find('.emails-item-wrapper[data-id-email='+$(this).data('id-email')+']').remove();
                }
            }
        })
    }



    // setModalSmsesItem
    function setModalSmsesItem(target){

        setModalTarget($('#modal-select-sms'),target);
        var type = target.type;

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-sms input[type=checkbox]').prop('checked', false);
        wrapperObj.find('.smses-item-wrapper').each(function(){
            var idEmailsSelect = $(this).data('id-sms');
            $('#modal-select-sms .select-item[data-id-sms='+idEmailsSelect+'] input[type=checkbox]').prop('checked', true);
        })

        $('#modal-select-sms').modal('show');
    }

    function selectSmsesItem(){
        var modalObj = $('#modal-select-sms');
        var target = {
            "type":modalObj.attr('data-type'),
            "id":modalObj.attr('data-mission-id'),
            "parent":modalObj.attr('data-parent')
        }

        var type = target.type;
        var taget_id = target.id || null;
        var parent = 'rewards';
        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-sms .select-item').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){

                if(wrapperObj.find('.smses-item-wrapper[data-id-sms='+$(this).data('id-sms')+']').length <= 0) {

                    var id = $(this).data('id-sms');
                    var img = $(this).find('.image img').attr('src');
                    var title = $(this).find('.title').html();
                    var typeElement = 'sms';

                    var smsBody = $(this).find('.data-sms-body').html();


                    inputHtml = '<input type="hidden" name="'+parent+'['+taget_id+'][feedbacks][sms]['+id+'][template_id]" value="'+id+'"/>\
                                <input type="hidden" name="'+parent+'['+taget_id+'][feedbacks][sms]['+id+'][feedback_type]" value="SMS"/>'


                    var smsesItemHtml = '<div class="clearfix item-wrapper smses-item-wrapper" data-id-sms="'+id+'">\
                                    <h4 class="span10">'+title+'</h4>\
                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>\
                                    <div class="clearfix"></div>\
                                    <div class="clearfix">\
                                        <div class="span2">Body: </div>\
                                        <div class="span10">'+inputHtml+'\
                                        '+smsBody+'</div>\
                                    </div>\
                </div>';


                    wrapperObj.find('.smses-wrapper .item-container').append(smsesItemHtml);

                    init_additem_event(target);
                }
            }else{
                if(wrapperObj.find('.smses-item-wrapper[data-id-sms='+$(this).data('id-sms')+']').length >= 1) {
                    wrapperObj.find('.smses-item-wrapper[data-id-sms='+$(this).data('id-sms')+']').remove();
                }
            }
        })
    }
    // setModalPushesItem
    function setModalPushesItem(target){

        setModalTarget($('#modal-select-push'),target);
        var type = target.type;

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-push input[type=checkbox]').prop('checked', false);
        wrapperObj.find('.pushes-item-wrapper').each(function(){
            var idEmailsSelect = $(this).data('id-push');
            $('#modal-select-push .select-item[data-id-push='+idEmailsSelect+'] input[type=checkbox]').prop('checked', true);
        })

        $('#modal-select-push').modal('show');
    }

    function selectPushesItem(){
        var modalObj = $('#modal-select-push');
        var target = {
            "type":modalObj.attr('data-type'),
            "id":modalObj.attr('data-mission-id'),
            "parent":modalObj.attr('data-parent')
        }

        var type = target.type;
        var taget_id = target.id || null;
        var parent = 'rewards';

        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-push .select-item').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){

                if(wrapperObj.find('.pushes-item-wrapper[data-id-push='+$(this).data('id-push')+']').length <= 0) {

                    var id = $(this).data('id-push');
                    var img = $(this).find('.image img').attr('src');
                    var title = $(this).find('.title').html();
                    var typeElement = 'push';

                    var pushBody = $(this).find('.data-push-body').html();


                    inputHtml = '<input type="hidden" name="'+parent+'['+taget_id+'][feedbacks][push]['+id+'][template_id]" value="'+id+'"/>\
                            <input type="hidden" name="'+parent+'['+taget_id+'][feedbacks][push]['+id+'][feedback_type]" value="PUSH"/>'

                    var pushesItemHtml = '<div class="clearfix item-wrapper pushes-item-wrapper" data-id-push="'+id+'">\
                                <h4 class="span10">'+title+'</h4>\
                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>\
                                <div class="clearfix"></div>\
                                <div class="clearfix">\
                                    <div class="span2">Body: </div>\
                                    <div class="span10">'+inputHtml+'\
                                    '+pushBody+'</div>\
                                </div>\
            </div>';


                    wrapperObj.find('.pushes-wrapper .item-container').append(pushesItemHtml);

                    init_additem_event(target);
                }
            }else{
                if(wrapperObj.find('.pushes-item-wrapper[data-id-push='+$(this).data('id-push')+']').length >= 1) {
                    wrapperObj.find('.pushes-item-wrapper[data-id-push='+$(this).data('id-push')+']').remove();
                }
            }
        })
    }

    // setModalCustompointsItem
    function setModalCustompointsItem(target){

        setModalTarget($('#modal-select-custompoint'),target);

        var type = target.type;
        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-custompoint input[type=checkbox]').prop('checked', false);
        wrapperObj.find('.custompoints-item-wrapper').each(function(){
            var idSelect = $(this).data('id-custompoint');
            $('#modal-select-custompoint .select-item[data-id-custompoint='+idSelect+'] input[type=checkbox]').prop('checked', true);
        })

        $('#modal-select-custompoint').modal('show');
    }

    function selectCustompointsItem(){
        var modalObj = $('#modal-select-custompoint');
        var target = {
            "type":modalObj.attr('data-type'),
            "id":modalObj.attr('data-mission-id'),
            "parent":modalObj.attr('data-parent')
        }

        var type = target.type;
        var taget_id = target.id || null;
        var parent = 'rewards';
        var wrapperObj = $('.rank-item-wrapper[dataRankId='+target.id+']' );

        $('#modal-select-custompoint .select-item').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){

                if(wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').length <= 0) {

                    var id = $(this).data('id-custompoint');
                    var title = $(this).find('.title').html();
                    var typeElement = checkTypeReward(type);

                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+'][custompoints]['+id+']['+typeElement+'_value]" placeholder="Value" value="1">\
                            <input type="hidden" name = "'+parent+'['+taget_id+'][custompoints]['+id+']['+typeElement+'_type]" value = "CUSTOM_POINT"/>\
                            <input type="hidden" name = "'+parent+'['+taget_id+'][custompoints]['+id+']['+typeElement+'_id]" value = "'+id+'"/>';


                    var inputCompletionHtml = '';
                    if(type == 'completion'){
                        inputCompletionHtml = '<div class="title-row"><div class="span2">Title : </div><div class="span10"><input type="text" name ="'+parent+'['+taget_id+']['+id+']['+typeElement+'_title]" placeholder="Title" value=""></div></div>';
                    }

                    var itemHtml = '<div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="'+id+'">\
                                <div class="span7">'+title+'</div><div class="span3"><small>value</small>\
                                '+inputHtml+'</div>\
                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>'+inputCompletionHtml+'</div>';

                    wrapperObj.find('.custompoints-wrapper .item-container').append(itemHtml);
                    init_additem_event(target);
                }
            }else{
                if(wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').length >= 1) {
                    wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').remove();
                }
            }
        })
    }


    function setModalTarget(modalObj,target){
        var type = target.type;
        var id = target.id || null;
        var parent = target.parent || 'quests';

        modalObj.attr('data-type',type);
        modalObj.attr('data-mission-id',id);
        modalObj.attr('data-parent',parent);
    }

    function checkTypeReward(type){
        if(type == "rewards"){
            return "reward";
        }else{
            return type;
        }
    }
</script>
