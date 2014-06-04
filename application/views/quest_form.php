<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'quest'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div><!-- .buttons -->
        </div><!-- .heading -->
        <div class="content">
        	<?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
            
            <div id="tabs" class="htabs">
                <a href="#tab-general"><?php echo $this->lang->line('tab_general'); ?></a>
                <a href="#tab-mission"><?php echo $this->lang->line('tab_mission'); ?></a>
            </div>

            <?php if(validation_errors() || isset($message)){?>
            <div class="content messages half-width">
              <?php echo validation_errors('<div class="warning">', '</div>');?>
              <?php if (isset($message) && $message){?>
              <div class="warning"><?php //echo $message;?></div>
              <?php }?>
            </div>
            <?php }?>
            <?php $attributes = array('id' => 'form');?>
            <?php echo form_open($form, $attributes);?>
            <div id="tab-general" class="data-quest-wrapper">
                <div class="span6">
                    <table class="form ">
                        <tr>
                            <td><span class="required">*</span> <?php echo $this->lang->line('form_quest_name'); ?>:</td>
                            <td><input type="text" name="quest_name" size="100" value="<?php echo isset($editQuest['quest_name']) ? $editQuest['quest_name'] :  set_value('name'); ?>" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_description'); ?>:</td>
                            <td><textarea name ="description" rows="4"><?php echo isset($editQuest['description']) ? $editQuest['description'] :  set_value('description'); ?></textarea>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_hint'); ?>:</td>
                            <td><input type="text" name="hint" size="100" value="<?php echo isset($editQuest['hint']) ? $editQuest['hint'] :  set_value('hint'); ?>" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="quest_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="quest_image" />
                                <br /><a onclick="image_upload('quest_image', 'quest_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#quest_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quest_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_missionordering'); ?>:</td>
                            <td><input type="checkbox" name="mission_order" <?php echo (isset($editQuest['mission_order']) && $editQuest['mission_order'])?'checked':'unchecked'; ?> size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_status'); ?>:</td>
                            <td><input type="checkbox" name="status" <?php echo (isset($editQuest['status']) && $editQuest['status'])?'checked':'unchecked'; ?> size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_sort_order'); ?>:</td>
                            <td><input type="number" name="sort_order" value="<?php echo isset($editQuest['sort_order'])?$editQuest['sort_order']:''; ?>"/>    </td>
                        </tr>
                    </table>
                </div>
                <div class="span6">
                    <div class="box box-add-item condition-wrapper">
                        <div class="box-header overflow-visible">
                            <h2><i class="icon-cog"></i><span class="break"></span>Condition</h2>
                            <div class="box-icon box-icon-action">
                                <a href="javascript:void(0)" class="btn btn-primary right add-condition-btn dropdown-toggle" data-toggle="dropdown"> + Add Condition</a>
                                <ul class="dropdown-menu add-condition-menu" role="menu" aria-labelledby="dropdownMenu">
                                    <li class="add-datetime"><a tabindex="-1" href="javascript:void(0)" >DATE TIME</a></li>
                                    <li class="add-level"><a tabindex="-1" href="javascript:void(0)" >LEVEL</a></li>
                                    <li class="add-quest"><a tabindex="-1" href="javascript:void(0)" >QUEST</a></li>
                                    <li class="add-point"><a tabindex="-1" href="javascript:void(0)" >POINT</a></li>
                                    <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CUSTOM POINT</a></li>
                                    <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">BADGE</a></li>
                                </ul>
                                <span class="break"></span>
                                <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="box-content">
                            <div class = 'condition-container'>
                                <?php if(isset($editDateStartCon) && isset($editDateEndCon)){ ?>
                                    <div class="datetime-wrapper condition-type well">
                                        <h3>Data time <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                        <label class="span4">Date Start:</label> 
                                        <input type="text" name="condition[datetimestart][condition_value]" class="date" placeholder="datetime start" id="dp1401709709268" value="<?php echo $editDateStartCon['condition_value'] ?>">  
                                        <input type="hidden" name="condition[datetimestart][condition_type]" value="DATETIME_START">                        
                                        <input type="hidden" name="condition[datetimestart][condition_id]" value=""><br>
                                        <label class="span4">Date End:</label> 
                                        <input type="text" name="condition[datetimeend][condition_value]" class="date" placeholder="datetime end" id="dp1401709709269" value="<?php echo $editDateEndCon['condition_value'] ?>">                    
                                        <input type="hidden" name="condition[datetimeend][condition_type]" value="DATETIME_END">                    
                                        <input type="hidden" name="condition[datetimeend][condition_id]" value="">
                                    </div>
                                <?php } ?>

                                <?php if(isset($editLevelStartCon) && isset($editLevelEndCon)){ ?>
                                    <div class="level-wrapper condition-type well">
                                        <h3>Level <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                        <label class="span4">Level Start:</label>
                                        <select name="condition[levelstart][condition_value]">
                                            <?php 
                                            foreach($levels as $level){
                                                if($editLevelStartCon['condition_value'] == $level['level']){
                                                    echo "<option selected value = ".$level["level"].">".$level["level"]." ".$level["level_title"]."</option>";
                                                }
                                                echo "<option value = ".$level["level"].">".$level["level"]." ".$level["level_title"]."</option>";
                                            }
                                            ?>
                                        </select>                    
                                        <input type="hidden" name="condition[levelstart][condition_type]" value="LEVEL_START">                    
                                        <input type="hidden" name="condition[levelstart][condition_id]" value="">
                                        <br>
                                        <label class="span4">Level End:</label> 
                                        <select name="condition[levelend][condition_value]">
                                            <?php 
                                            foreach($levels as $level){
                                                if($editLevelEndCon['condition_value'] == $level['level']){
                                                    echo "<option selected value = ".$level["level"].">".$level["level"]." ".$level["level_title"]."</option>";
                                                }
                                                echo "<option value = ".$level["level"].">".$level["level"]." ".$level["level_title"]."</option>";
                                            }
                                            ?>
                                        </select>                    
                                        <input type="hidden" name="condition[levelend][condition_type]" value="LEVEL_END">                    
                                        <input type="hidden" name="condition[levelend][condition_id]" value="">
                                    </div>
                                <?php } ?>
                                <?php if(isset($editQuestConditionCon)){ ?>
                                    <div class="quests-wrapper condition-type well">
                                        <h3>Quest <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-quest-btn">+ Add Quest</a></h3>
                                        <?php foreach($editQuestConditionCon as $quest){ ?>    
                                            <div class="item-container">
                                                <div class="clearfix item-wrapper quests-item-wrapper" data-id-quest="<?php echo $quest['condition_id']; ?>">                                
                                                    <div class="span2 text-center">
                                                        <img src="http://images.pbapp.net/no_image.jpg" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">                                
                                                    </div>
                                                    <div class="span7"><?php foreach($quests as $q){if($q['_id']==$quest['condition_id']){echo $q['quest_name'];}} ?></div>                                
                                                    <div class="span1">
                                                        <input type="hidden" name="condition[<?php echo $quest['condition_id'];?>][condition_id]" value="<?php echo $quest['condition_id']; ?>">
                                                    </div>                                
                                                    <input type="hidden" name="condition[<?php echo $quest['condition_id']; ?>][condition_type]" value="QUEST">                                
                                                    <input type="hidden" name="condition[<?php echo $quest['condition_id']; ?>][condition_value]" value="">                                
                                                    <div class="span2 col-remove">
                                                    <a class="item-remove">
                                                        <i class="icon-remove-sign"></i>
                                                    </a>
                                                    </div>
                                                </div>                                        
                                            </div>
                                        <?php } ?>    
                                    </div>
                                <?php } ?>
                                <?php if(isset($editPointsCon)){ ?>
                                    <div class="points-wrapper condition-type well">
                                        <h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                        <label class="span4">Points:</label>
                                        <input type="text" name="condition[point][condition_value]" placeholder="Points" value = "<?php echo $editPointsCon['condition_value'] ?>">                    
                                        <input type="hidden" name="condition[point][condition_type]" value="POINT">                    
                                        <input type="hidden" name="condition[point][condition_id]" value="<?php echo $editPointsCon['condition_id']; ?>">
                                    </div>
                                <?php } ?>
                                <?php if(isset($editCustomPointsCon)){ ?>
                                    <div class="custompoints-wrapper condition-type well">
                                        <h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Custom Points</a></h3>
                                        <?php foreach($editCustomPointsCon as $point){ ?>
                                            <div class="item-container">
                                                <div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="<?php echo $point['condition_id'] ?>">                                
                                                    <div class="span7"><?php foreach($customPoints as $p){if($p['_id']==$point['condition_id']){echo $p['name'];}} ?></div><div class="span3"><small>value</small>                                
                                                    <input type="text" name="condition[custompoints][condition_value]" placeholder="Value" value="<?php echo $point['condition_value']; ?>">
                                                    <input type="hidden" name="condition[custompoints][condition_type]" value="CUSTOM_POINT">                                
                                                    <input type="hidden" name="condition[custompoints][condition_id]" value="<?php echo $point['condition_id'] ?>">
                                                    </div>                                
                                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if(isset($editBadgeCon)){ ?>
                                    <div class="badges-wrapper condition-type well">
                                        <h3>Badges  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Badges</a></h3>
                                        <div class="item-container">
                                            <?php foreach($editBadgeCon as $badge){ ?>    
                                                <div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="<?php echo $badge['condition_id'] ?>">                                    
                                                <div class="span2 text-center">
                                                    <img src="http://images.pbapp.net/<?php foreach($badges as $b){if($b['badge_id'] == $badge['condition_id']){echo $b['image'];}} ?>" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">                                    
                                                </div>                                    
                                                <div class="span7"><?php foreach($badges as $b){if($b['_id'] == $badge['condition_id']){echo $b['name'];}} ?></div>                                    
                                                <div class="span1">                                    
                                                    <small>value</small>                                    
                                                    <input type="text" name="condition[<?php echo $badge['condition_id'] ?>][condition_value]" placeholder="Value" value="<?php echo $badge['condition_value'] ?>">                                    
                                                    <input type="hidden" name="condition[<?php echo $badge['condition_id'] ?>][condition_id]" value="<?php echo $badge['condition_id'] ?>">                                    
                                                    <input type="hidden" name="condition[<?php echo $badge['condition_id'] ?>][condition_type]" value="BADGE"></div>                                    
                                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="box box-add-item reward-wrapper">
                        <div class="box-header overflow-visible">
                            <h2><i class="icon-certificate"></i><span class="break"></span>Rewards</h2>
                            <div class="box-icon box-icon-action">
                                <a href="javascript:void(0)" class="btn btn-primary right add-reward-btn dropdown-toggle" data-toggle="dropdown"> + Add Reward</a>
                                <ul class="dropdown-menu add-reward-menu" role="menu" aria-labelledby="dropdownMenu">
                                  <li class="add-point"><a tabindex="-1" href="javascript:void(0)" >POINT</a></li>
                                  <li class="add-exp"><a tabindex="-1" href="javascript:void(0)" >EXP</a></li>
                                  <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CUSTOM POINT</a></li>
                                  <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">BADGE</a></li>
                                </ul>
                                <span class="break"></span>
                                <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="box-content">
                            <div class='reward-container'>
                                <?php if(isset($editPointsRew)){ ?>
                                    <div class="points-wrapper reward-type well">
                                        <h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                        <label class="span4">Points:</label>
                                        <input type="text" name="reward[point][reward_value]" placeholder="Points" value = "<?php echo $editPointsRew['reward_value'] ?>">                    
                                        <input type="hidden" name="reward[point][reward_type]" value="POINT">                    
                                        <input type="hidden" name="reward[point][reward_id]" value="<?php echo $editPointsRew['reward_id']; ?>">
                                    </div>
                                <?php } ?>

                                <?php if(isset($editExpRew)){ ?>
                                    <div class="exp-wrapper reward-type well">
                                        <h3>Exp <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                        <label class="span4">Exp:</label>
                                        <input type="text" name="reward[exp][reward_value]" placeholder="Exp" value = "<?php echo $editExpRew['reward_value']; ?>">                    
                                        <input type="hidden" name="reward[exp][reward_type]" value="EXP">                    
                                        <input type="hidden" name="reward[exp][reward_id]" value="<?php echo $editExpRew['reward_id'] ?>">
                                    </div>
                                <?php } ?>
                                <?php if(isset($editCustomPointsRew)){ ?>
                                    <div class="custompoints-wrapper reward-type well">
                                        <h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Custom Points</a></h3>
                                        <?php foreach($editCustomPointsRew as $point){ ?>
                                            <div class="item-container">
                                                <div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="<?php echo $point['reward_id'] ?>">                                
                                                    <div class="span7"><?php foreach($customPoints as $p){if($p['_id']==$point['reward_id']){echo $p['name'];}} ?></div><div class="span3"><small>value</small>                                
                                                    <input type="text" name="reward[custompoints][reward_value]" placeholder="Value" value="<?php echo $point['reward_value']; ?>">
                                                    <input type="hidden" name="reward[custompoints][reward_type]" value="CUSTOM_POINT">                                
                                                    <input type="hidden" name="reward[custompoints][reward_id]" value="<?php echo $point['reward_id'] ?>">
                                                    </div>                                
                                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if(isset($editBadgeRew)){ ?>
                                    <div class="badges-wrapper reward-type well">
                                        <h3>Badges  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Badges</a></h3>
                                        <div class="item-container">
                                            <?php foreach($editBadgeRew as $badge){ ?>    
                                                <div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="<?php echo $badge['reward_id'] ?>">                                    
                                                <div class="span2 text-center">
                                                    <img src="http://images.pbapp.net/data/dc2efb2d903008f9d7e0d5e8024981d2.png" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">                                    
                                                </div>                                    
                                                <div class="span7"><?php foreach($badges as $b){if($b['_id'] == $badge['reward_id']){echo $b['name'];}} ?></div>                                    
                                                <div class="span1">                                    
                                                    <small>value</small>                                    
                                                    <input type="text" name="reward[<?php echo $badge['reward_id'] ?>][reward_value]" placeholder="Value" value="<?php echo $badge['reward_value'] ?>">                                    
                                                    <input type="hidden" name="reward[<?php echo $badge['reward_id'] ?>][reward_id]" value="<?php echo $badge['reward_id'] ?>">                                    
                                                    <input type="hidden" name="reward[<?php echo $badge['reward_id'] ?>][reward_type]" value="BADGE"></div>                                    
                                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
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

            <div id="tab-mission">
                <div class="mission-head-wrapper">
                    <a href="javascript:void(0)" class="btn  open-mission-btn btn-lg">Open All</a>
                    <a href="javascript:void(0)" class="btn close-mission-btn btn-lg">Close All</a>
                    <a href="javascript:void(0)" class="btn btn-primary add-mission-btn btn-lg">+ New Mission</a>
                </div>
                <div class="mission-wrapper">
                    <?php if(isset($editMission) && !empty($editMission)){ ?>
                        <?php foreach($editMission as $mission){ ?>
                        <div class="mission-item-wrapper" data-mission-id="<?php echo $mission['mission_id'] ?>">                        
                            <div class="box-header box-mission-header overflow-visible">                            
                                <h2><img src="http://images.pbapp.net/cache/no_image-100x100.jpg" width="50"><?php echo $mission['mission_name'] ?></h2>                            
                                <div class="box-icon">                                
                                    <a href="javascript:void(0)" class="btn btn-danger right remove-mission-btn dropdown-toggle" data-toggle="dropdown">Delete </a>                                
                                    <span class="break"></span>                                
                                    <a href="javaScript:void()"><i class="icon-chevron-up"></i></a>                            
                                </div>                        
                            </div>                        
                            <div class="box-content clearfix">                            
                                <div class="span6">                                
                                    <table class="form">                                    
                                        <tbody>
                                            <tr>                                        
                                                <td><span class="required">*</span> Mission Name:</td>                                        
                                                <td><input type="text" name="missions[<?php echo $mission['mission_id'] ?>][mission_name]" size="100" value="<?php echo $mission['mission_name'] ?>"></td>                                    
                                            </tr>                                    
                                            <tr>                                        
                                                <td><span class="required">*</span> Mission Number:</td>                                        
                                                <td><input type="text" name="missions[<?php echo $mission['mission_id'] ?>][mission_number]" size="100" value="<?php echo $mission['description'] ?>"></td>                                    
                                            </tr>                                    
                                            <tr>                                        
                                                <td>Description:</td>                                        
                                                <td><textarea name="missions[<?php echo $mission['mission_id'] ?>][description]" rows="4"><?php echo $mission['description'] ?></textarea></td>
                                            </tr>                                    
                                            <tr>                                        
                                                <td>Hint:</td>                                        
                                                <td><input type="text" name="missions[<?php echo $mission['mission_id'] ?>][hint]" size="100" value="<?php echo $mission['hint'] ?>"></td>                                    
                                            </tr>                                    
                                            <tr>                                        
                                                <td>Mission Image:</td>                                        
                                                <td valign="top">
                                                    <div class="image">
                                                        <img src="http://images.pbapp.net/cache/no_image-100x100.jpg" alt="" id="thumb_mission_<?php echo $mission['mission_id'] ?>" onerror="$(this).attr(" src","http:="" localhost="" playbasis="" control="" image="" default-image.png");"="">                                            
                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][image]" value="no_image.jpg" id="image_mission_<?php echo $mission['mission_id'] ?>">                                            
                                                        <br>
                                                        <a onclick="image_upload('image_mission_<?php echo $mission['mission_id'] ?>', 'thumb_mission_<?php echo $mission['mission_id'] ?>');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb_mission_<?php echo $mission['mission_id'] ?>').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image_mission_<?php echo $mission['mission_id'] ?>').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                                    </div>                                        
                                                </td>                                    
                                            </tr>                                
                                        </tbody>
                                    </table>                            
                                </div>                            
                                <div class="span6">                                
                                    <div class="box box-add-item completion-wrapper">                                    
                                        <div class="box-header overflow-visible">                                        
                                            <h2><i class="icon-trophy"></i><span class="break"></span>Completion</h2>                                        
                                            <div class="box-icon box-icon-action">                                            
                                                <a href="javascript:void(0)" class="btn btn-primary right add-completion-btn dropdown-toggle" data-toggle="dropdown"> + Add Completion</a>                                            
                                                <ul class="dropdown-menu add-completion-menu" role="menu" aria-labelledby="dropdownMenu">                                                
                                                    <li class="add-action"><a tabindex="-1" href="javascript:void(0)">ACTION</a></li>                                                
                                                    <li class="add-point"><a tabindex="-1" href="javascript:void(0)">POINT</a></li>                                                
                                                    <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CUSTOM POINT</a></li>                                                
                                                    <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">BADGE</a></li>                                            
                                                </ul>                                            
                                                <span class="break"></span>                                            
                                                <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>                                        
                                            </div>                                    
                                        </div>                                    
                                        <div class="box-content">                                        
                                            <div class="completion-container">                                        
                                                <?php if(isset($mission['editAction'])){ ?>
                                                    <div class="actions-wrapper completion-type well">
                                                        <h3>Actions  <a class="remove"><i class="icon-remove-sign"></i></a> 
                                                            <a class="btn add-action-btn">+ Add Actions</a>
                                                        </h3>
                                                        <div class="item-container">
                                                        <?php foreach($mission['editAction'] as $action){ ?>
                                                            
                                                                <div class="clearfix item-wrapper actions-item-wrapper" data-id-action="<?php echo $action['completion_id']; ?>">                                    
                                                                    <div class="span2 text-center">
                                                                        <i class="<?php foreach($actions as $aa){if($aa['action_id'] == $action['completion_id']){echo $aa['icon'];}} ?> icon-4x"></i>                                    
                                                                    </div>                                    
                                                                    <div class="span5"><?php foreach($actions as $aa){if($aa['action_id'] == $action['completion_id']){echo $aa['name'];}} ?></div>                                    
                                                                    <div class="span2">
                                                                        <small>filter</small>
                                                                    <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $action['completion_id']; ?>][completion_filter]" value = "<?php echo $action['completion_filter'] ?>">
                                                                    </div>                                    
                                                                    <div class="span1">                                    
                                                                        <small>value</small>                                    
                                                                        <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $action['completion_id']; ?>][completion_value]" placeholder="Value" value="<?php echo $action['completion_value']; ?>">                                    
                                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $action['completion_id']; ?>][completion_id]" value="<?php echo $action['completion_id']; ?>">                                    
                                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $action['completion_id']; ?>][completion_type]" value="ACTION">
                                                                    </div>                                    
                                                                    <div class="span2 col-remove">
                                                                        <a class="item-remove"><i class="icon-remove-sign"></i></a>                                    
                                                                    </div>
                                                                </div>
                                                            
                                                        <?php } ?><!-- end of foreach -->
                                                        </div>
                                                    </div>
                                                <?php } ?><!-- end of editAction isset -->
                                                <?php if(isset($mission['editPoint'])){ ?>
                                                    <div class="points-wrapper completion-type well">                                                    
                                                            <h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                                            <label class="span4">Points:</label>
                                                            <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][completion][point][completion_value]" placeholder="Points" value = "<?php echo $mission['editPoint']['completion_value'] ?>">                    
                                                            <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][point][completion_type]" value="POINT">                    
                                                            <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][point][completion_id]" value="<?php echo $mission['editPoint']['completion_id'] ?>">
                                                    </div>
                                                <?php } ?><!-- end of editPoint isset -->

                                                <?php if(isset($mission['editCustomPoint'])){ ?>
                                                    <div class="custompoints-wrapper completion-type well">
                                                        <h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Custom Points</a></h3>
                                                        <div class="item-container">

                                                        <?php foreach ($mission['editCustomPoint'] as $cp){ ?>

                                                            <div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="<?php echo $cp['completion_id'] ?>">                                
                                                                <div class="span7"><?php foreach($customPoints as $c){if($c['_id'] == $cp['completion_id']){echo $c['name'];}} ?></div>
                                                                <div class="span3">
                                                                    <small>value</small>                                
                                                                    <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][completion][custompoints][completion_value]" placeholder="Value" value="<?php echo $cp['completion_value']; ?>"></div>
                                                                    <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][custompoints][completion_type]" value="CUSTOM_POINT">                                
                                                                    <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][custompoints][completion_id]" value="<?php echo $cp['completion_id'] ?>">                                
                                                                    <div class="span2 col-remove">
                                                                        <a class="item-remove"><i class="icon-remove-sign"></i></a>
                                                                    </div>
                                                            </div>

                                                        <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?> <!-- end of editCustomPoint isset -->

                                                <?php if(isset($mission['editBadge'])){ ?>
                                                    <div class="badges-wrapper completion-type well">
                                                        <h3>Badges  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Badges</a></h3>
                                                        <div class="item-container">

                                                            <?php foreach($mission['editBadge'] as $eBadge){ ?>
                                                                <div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="<?php echo $eBadge['completion_id'] ?>">                                    
                                                                    <div class="span2 text-center">
                                                                        <img src="http://images.pbapp.net/<?php foreach($badges as $b){if($b['badge_id'] == $eBadge['completion_id']){echo $b['image'];}} ?>" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">                                    
                                                                    </div>                                    
                                                                    <div class="span7"><?php foreach($badges as $b){if($b['badge_id'] == $eBadge['completion_id']){echo $b['name'];}} ?></div>                                    
                                                                    <div class="span1">                                    
                                                                        <small>value</small>                                    
                                                                        <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $eBadge['completion_id'] ?>][completion_value]" placeholder="Value" value="<?php echo $eBadge['completion_value']; ?>">                                    
                                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $eBadge['completion_id'] ?>][completion_id]" value="<?php echo $eBadge['completion_id'] ?>">                                    
                                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][completion][<?php echo $eBadge['completion_id'] ?>][completion_type]" value="BADGE">
                                                                    </div>                                    
                                                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                </div>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                <?php } ?> <!-- end of editBadge isset -->

                                                <h3 class="no-item">No Item</h3>
                                            </div>                                    
                                        </div>                                
                                    </div>                                
                                    <div class="box box-add-item reward-wrapper">                                    
                                        <div class="box-header overflow-visible">                                        
                                            <h2><i class="icon-certificate"></i><span class="break"></span>Rewards</h2>                                        
                                            <div class="box-icon box-icon-action">                                            
                                                <a href="javascript:void(0)" class="btn btn-primary right add-reward-btn dropdown-toggle" data-toggle="dropdown"> + Add Reward</a>                                            
                                                <ul class="dropdown-menu add-reward-menu" role="menu" aria-labelledby="dropdownMenu">                                              
                                                    <li class="add-point"><a tabindex="-1" href="javascript:void(0)">POINT</a></li>                                              
                                                    <li class="add-exp"><a tabindex="-1" href="javascript:void(0)">EXP</a></li>                                              
                                                    <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CUSTOM POINT</a></li>                                              
                                                    <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">BADGE</a></li>                                            
                                                </ul>                                            
                                                <span class="break"></span>                                            
                                                <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>                                        
                                            </div>                                    
                                        </div>                                    
                                        <div class="box-content">                                        
                                            <div class="reward-container">

                                                <?php if(isset($mission['editPointRew'])){ ?>
                                                    <div class="points-wrapper reward-type well">
                                                        <h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                                        <label class="spans4">Points:</label>
                                                        <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][reward][point][reward_value]" placeholder="Points" value = "<?php echo $mission['editPointRew']['reward_value'] ?>">                    
                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][point][reward_type]" value="POINT">                    
                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][point][reward_id]" value="<?php echo $mission['editPointRew']['reward_id'] ?>">
                                                    </div>
                                                <?php } ?> <!-- end of editPointRew isset -->

                                                <?php if(isset($mission['editExpRew'])){ ?>
                                                    <div class="exp-wrapper reward-type well">
                                                        <h3>Exp <a class="remove"><i class="icon-remove-sign"></i></a></h3>
                                                        <label class="span4">Exp:</label><input type="text" name="missions[<?php echo $mission['mission_id'] ?>][reward][exp][reward_value]" placeholder="Exp" value = "<?php echo $mission['editExpRew']['reward_value'] ?>">                    
                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][exp][reward_type]" value="EXP">                    
                                                        <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][exp][reward_id]" value="value = "<?php echo $mission['editExpRew']['reward_id'] ?>"">
                                                    </div>
                                                <?php } ?> <!-- end of editExpRew isset -->

                                                <?php if(isset($mission['editCustomPointRew'])){ ?>
                                                    <div class="custompoints-wrapper reward-type well">
                                                        <h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Custom Points</a></h3>
                                                        <div class="item-container">

                                                            <?php foreach($mission['editCustomPointRew'] as $eCustomPoint){ ?>
                                                                <div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="<?php echo $eCustomPoint['reward_id'] ?>">                                
                                                                    <div class="span7"><?php foreach($customPoints as $pp){if($pp['_id'] == $eCustomPoint['reward_id']){echo $pp['name'];}} ?></div>
                                                                    <div class="span3"><small>value</small>                                
                                                                        <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][reward][custompoints][reward_value]" placeholder="Value" value="<?php echo $eCustomPoint['reward_value'] ?>">
                                                                    </div>                                
                                                                    <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][custompoints][reward_type]" value="CUSTOM_POINT">                                
                                                                    <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][custompoints][reward_id]" value="<?php echo $eCustomPoint['reward_id'] ?>">                                
                                                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div>
                                                                </div>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                <?php } ?> <!-- end of editCustomPointRew isset -->

                                                <?php if(isset($mission['editBadgeRew'])){ ?>

                                                    <div class="badges-wrapper reward-type well">
                                                        <h3>Badges  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Badges</a></h3>
                                                        <div class="item-container">
                                                            <?php foreach($mission['editBadgeRew'] as $eBadge){ ?>
                                                            <div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="<?php echo $eBadge['reward_id'] ?>">                                    
                                                                <div class="span2 text-center"><img src="http://images.pbapp.net/<?php foreach($badges as $bb){if($bb['badge_id'] == $eBadge['reward_id']){echo $bb['image'];}} ?>" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">                                    
                                                                </div>                                    
                                                                <div class="span7"><?php foreach($badges as $bb){if($bb['badge_id'] == $eBadge['reward_id']){echo $bb['name'];}} ?></div>                                    
                                                                <div class="span1">                                    
                                                                <small>value</small>                                    
                                                                <input type="text" name="missions[<?php echo $mission['mission_id'] ?>][reward][<?php echo $eBadge['reward_id'] ?>][reward_value]" placeholder="Value" value="<?php echo $eBadge['reward_value'] ?>">                                    
                                                                <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][<?php echo $eBadge['reward_id'] ?>][reward_id]" value="<?php echo $eBadge['reward_id'] ?>">                                    
                                                                <input type="hidden" name="missions[<?php echo $mission['mission_id'] ?>][reward][<?php echo $eBadge['reward_id'] ?>][reward_type]" value="BADGE"></div>                                    
                                                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a>                                    
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>

                                                <?php } ?> <!-- end of editCustomPointRew isset -->

                                                <h3 class="no-item">No Item</h3>
                                            </div><!-- .reward-container -->                                    
                                        </div>                                
                                    </div>                            
                                </div>                        
                            </div>                    
                        </div>
                        <?php } ?> <!-- end foreach loop -->
                    <?php } ?> <!-- end check if missions exists -->
                </div><!-- .mission-wrapper -->
            </div>
                <?php echo form_close();?>
            </div><!-- .content -->
        </div><!-- .box -->
    </div><!-- #content .span10 -->


 
<!-- Modal Badge -->
<div id="modal-select-badge" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Badge</h3>
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
                        <!-- <img src="http://images.pbapp.net/cache/data/cdc156da5ee5ffd5380855a4eca923be-50x50.png" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');"> -->
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

<!-- Modal Quest -->
<div id="modal-select-quest" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Quest</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php for($i=0 ; $i < count($quests) ; $i++){ ?>
                <label>
                <div class="select-item clearfix" data-id="<?php echo $i; ?>" data-id-quest = "<?php echo $quests[$i]['_id']; ?>">
                    <div class="span1 text-center">
                        <input type="checkbox" name="selected[]" value="<?php $quests[$i]['_id']; ?>">
                    </div>
                    <div class="span2 image text-center">
                        <img height="50" width="50" src="<?php echo S3_IMAGE.$quests[$i]['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                        <!-- <img src="http://images.pbapp.net/cache/data/cdc156da5ee5ffd5380855a4eca923be-50x50.png" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');"> -->
                    </div>
                    <div class="span9 title"><?php echo $quests[$i]['quest_name'];?></div>
                </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-quest-btn" data-dismiss="modal">Select</button>
    </div>
</div>

<!-- Modal Custom Points -->
<div id="modal-select-custompoint" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select Custom Point</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php for($i=0 ; $i < count($customPoints) ; $i++){ ?>
                <label>
                <div class="select-item clearfix" data-id="<?php echo $i; ?>" data-id-custompoint = "<?php echo $customPoints[$i]['_id']; ?>">
                    <div class="span1 text-center">
                        <input type="checkbox" name="selected[]" value="<?php $customPoints[$i]['_id']; ?>">
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

<!-- Modal Actions -->
<div id="modal-select-action" class="modal hide fade modal-select" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Select action</h3>
    </div>
    <div class="modal-body">
        <div class="select-list">
            <?php for($i=0 ; $i < count($actions) ; $i++){ ?>
                <label>

                <div class="select-item clearfix" data-id="<?php echo $i; ?>" data-id-action="<?php echo $actions[$i]['action_id'] ?>">
                    <div class="span1 text-center">
                        <input type="checkbox" name="selected[]" value="<?php $actions[$i]['_id']; ?>">
                    </div>
                    <div class="span2">
                        <i style='color:grey' class='<?php echo $actions[$i]['icon']; ?> icon-4x'></i>

                        <!-- <img height="50" width="50" src="<?php //echo S3_IMAGE.$actions[$i]['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /> -->
                        <!-- <img src="http://images.pbapp.net/cache/data/cdc156da5ee5ffd5380855a4eca923be-50x50.png" alt="" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');"> -->
                    </div>
                    <div class="span9 title"><?php echo $actions[$i]['name'];?></div>
                </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('.modal-select input[name*=\'selected\']').attr('checked', false);" >Clear Selection</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary select-action-btn" data-dismiss="modal">Select</button>
    </div>
</div>


<script type="text/javascript">
    $('#tabs a').tabs();

        $(function(){
            $('.date').datepicker({dateFormat: 'yy-mm-dd'});
        })


        //======================== Mission ========================
        var countMissionId = 0;

        $('.mission-item-wrapper').each(function(){
            countMissionId++;
        })

        $('.open-mission-btn').click(function(){
            $('.mission-item-wrapper>.box-content').show();
        })
        $('.close-mission-btn').click(function(){
            $('.mission-item-wrapper>.box-content').hide();
        })

        $('.add-mission-btn').click(function(){
            
            countMissionId++;

            var itemMissionId = countMissionId;

            var itemMissionHtml = '<div class="mission-item-wrapper" data-mission-id="'+itemMissionId+'">\
                        <div class="box-header box-mission-header overflow-visible">\
                            <h2><img src="<?php echo $thumb; ?>" width="50"> Mission Name</h2>\
                            <div class="box-icon">\
                                <a href="javascript:void(0)" class="btn btn-danger right remove-mission-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
                                <span class="break"></span>\
                                <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                            </div>\
                        </div>\
                        <div class="box-content clearfix">\
                            <div class="span6">\
                                <table class="form">\
                                    <tr>\
                                        <td><span class="required">*</span> <?php echo $this->lang->line("form_mission_name"); ?>:</td>\
                                        <td><input type="text" name="missions['+itemMissionId+'][mission_name]" size="100" value="" /></td>\
                                    </tr>\
                                    <tr>\
                                        <td><span class="required">*</span> <?php echo $this->lang->line("form_mission_number"); ?>:</td>\
                                        <td><input type="text" name="missions['+itemMissionId+'][mission_number]" size="100" value="" /></td>\
                                    </tr>\
                                    <tr>\
                                        <td><?php echo $this->lang->line("form_mission_description"); ?>:</td>\
                                        <td><textarea name ="missions['+itemMissionId+'][description]" rows="4"></textarea>\
                                    </tr>\
                                    <tr>\
                                        <td><?php echo $this->lang->line("form_mission_hint"); ?>:</td>\
                                        <td><input type="text" name="missions['+itemMissionId+'][hint]" size="100" value="" /></td>\
                                    </tr>\
                                    <tr>\
                                        <td><?php echo $this->lang->line("form_mission_image"); ?>:</td>\
                                        <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb_mission_'+itemMissionId+'" onerror="$(this).attr("src","<?php echo base_url();?>image/default-image.png");" />\
                                            <input type="hidden" name="missions['+itemMissionId+'][image]" value="" id="image_mission_'+itemMissionId+'" />\
                                            <br /><a onclick="image_upload(\'image_mission_'+itemMissionId+'\', \'thumb_mission_'+itemMissionId+'\');"><?php echo $this->lang->line("text_browse"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$(\'#thumb_mission_'+itemMissionId+'\').attr(\'src\', \'<?php echo $this->lang->line("no_image"); ?>\'); $(\'#image_mission_'+itemMissionId+'\').attr(\'value\', \'\');"><?php echo $this->lang->line("text_clear"); ?></a></div>\
                                        </td>\
                                    </tr>\
                                </table>\
                            </div>\
                            <div class="span6">\
                                <div class="box box-add-item completion-wrapper">\
                                    <div class="box-header overflow-visible">\
                                        <h2><i class="icon-trophy"></i><span class="break"></span>Completion</h2>\
                                        <div class="box-icon box-icon-action">\
                                            <a href="javascript:void(0)" class="btn btn-primary right add-completion-btn dropdown-toggle" data-toggle="dropdown"> + Add Completion</a>\
                                            <ul class="dropdown-menu add-completion-menu" role="menu" aria-labelledby="dropdownMenu">\
                                                <li class="add-action"><a tabindex="-1" href="javascript:void(0)" >ACTION</a></li>\
                                                <li class="add-point"><a tabindex="-1" href="javascript:void(0)" >POINT</a></li>\
                                                <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CUSTOM POINT</a></li>\
                                                <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">BADGE</a></li>\
                                            </ul>\
                                            <span class="break"></span>\
                                            <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>\
                                        </div>\
                                    </div>\
                                    <div class="box-content">\
                                        <div class = "completion-container">\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="box box-add-item reward-wrapper">\
                                    <div class="box-header overflow-visible">\
                                        <h2><i class="icon-certificate"></i><span class="break"></span>Rewards</h2>\
                                        <div class="box-icon box-icon-action">\
                                            <a href="javascript:void(0)" class="btn btn-primary right add-reward-btn dropdown-toggle" data-toggle="dropdown"> + Add Reward</a>\
                                            <ul class="dropdown-menu add-reward-menu" role="menu" aria-labelledby="dropdownMenu">\
                                              <li class="add-point"><a tabindex="-1" href="javascript:void(0)" >POINT</a></li>\
                                              <li class="add-exp"><a tabindex="-1" href="javascript:void(0)" >EXP</a></li>\
                                              <li class="add-custompoint"><a tabindex="-1" href="javascript:void(0)">CUSTOM POINT</a></li>\
                                              <li class="add-badge"><a tabindex="-1" href="javascript:void(0)">BADGE</a></li>\
                                            </ul>\
                                            <span class="break"></span>\
                                            <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>\
                                        </div>\
                                    </div>\
                                    <div class="box-content">\
                                        <div class="reward-container"></div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';
            $('.mission-item-wrapper>.box-content').slideUp();
            $('.mission-wrapper').append(itemMissionHtml);
            
            init_additem_event({
                type:'completion',
                parent:'missions',
                id:itemMissionId
            });

            init_additem_event({
                type:'reward',
                parent:'missions',
                id:itemMissionId
            });

            init_mission_event();
            //$('.mission-item-wrapper[data-mission-id='+countMissionId+'] .box-content').show();
        });

        function init_mission_event(){
           
            $('.mission-item-wrapper .box-mission-header').unbind().bind('click',function(data){
                var $target = $(this).next('.box-content');

                if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
                else                       $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
                $target.slideToggle();
            });

            $('.remove-mission-btn').unbind().bind('click',function(data){
                var $target = $(this).parent().parent().parent();
                
                var r = confirm("Are you sure to remove!");
                if (r == true) {
                    $target.remove();
                    init_mission_event()
                }
            });
        }
        init_mission_event();

        function init_additem_event(target){
            $('.date').datepicker({dateFormat: 'yy-mm-dd'});

            var type = target.type;
            var parent = target.parent || 'quests';
            var id = target.id || null;
            


            if(parent == 'missions'){
                var wrapperObj = $('.mission-item-wrapper[data-mission-id='+id+'] .'+type+'-wrapper');
                var containerObj = $('.mission-item-wrapper[data-mission-id='+id+'] .'+type+'-container');
            }else{
                var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
                var containerObj = $('.data-quest-wrapper .'+type+'-container');
            }


            var menuBtn = wrapperObj.find('.add-'+type+'-btn'),
            menuObj = wrapperObj.find('.add-'+type+'-menu'),

            addDatetimeObj = menuObj.find('.add-datetime'),
            addLevelObj = menuObj.find('.add-level'),
            addQuestObj = menuObj.find('.add-quest'),

            addPointObj = menuObj.find('.add-point'),
            addExpObj = menuObj.find('.add-exp'),
            addCustomPointObj = menuObj.find('.add-custompoint'),
            addBadgeObj = menuObj.find('.add-badge'),
            addActionObj = menuObj.find('.add-action');

            menuBtn.unbind().bind('click',function(data){
                wrapperObj.find('.box-content').show();
            });

            containerObj.find('.no-item').remove();


            if(containerObj.children().length <= 0){
                containerObj.append('<h3 class="no-item">No Item</h3>');
            }

            if(containerObj.has('.datetime-wrapper').length){
                addDatetimeObj.addClass('disabled');
                addDatetimeObj.unbind();
            }else{
                addDatetimeObj.removeClass('disabled');
                addDatetimeObj.unbind().bind('click',function(data){
                    addDatetime(target);
                });
            }

            if(containerObj.has('.level-wrapper').length){
                addLevelObj.addClass('disabled');
                addLevelObj.unbind();
            }else{
                addLevelObj.removeClass('disabled');
                addLevelObj.unbind().bind('click',function(data){
                    addLevel(target);
                });
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

            //Add Actions

            if(containerObj.has('.actions-wrapper').length){
                addActionObj.removeClass('disabled');
                addActionObj.unbind().bind('click',function(data){
                    setModalActionsItem(target);
                });
                containerObj.find('.actions-wrapper .add-action-btn').bind('click',function(data){
                    setModalActionsItem(target);
                });
            }else{
                addActionObj.removeClass('disabled');
                addActionObj.unbind().bind('click',function(data){
                    addActions(target);
                    setModalActionsItem(target);
                });
            }

            $('.select-action-btn').unbind().bind('click',function(data){
                selectActionsItem();
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


            //Add Quests point
            
            if(containerObj.has('.quests-wrapper').length){
                addQuestObj.removeClass('disabled');
                addQuestObj.unbind().bind('click',function(data){
                    setModalQuestsItem(target);
                });
                containerObj.find('.quests-wrapper .add-quest-btn').bind('click',function(data){
                    setModalQuestsItem(target);
                });
            }else{
                addQuestObj.removeClass('disabled');
                addQuestObj.unbind().bind('click',function(data){
                    addQuest(target);
                    setModalQuestsItem(target);
                });
            }

            $('.select-quest-btn').unbind().bind('click',function(data){
                selectQuestsItem();
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



init_additem_event({type:'reward'});
init_additem_event({type:'condition'});

$('.mission-item-wrapper').each(function(){
    var itemMissionId = $(this).data('mission-id');
    init_additem_event({
        type:'completion',
        parent:'missions',
        id:itemMissionId
    });

    init_additem_event({
        type:'reward',
        parent:'missions',
        id:itemMissionId
    });
})
countMissionId = $('.mission-item-wrapper').length;


var conditionCount = 1,
    rewardCount = 1;


function addDatetime(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';
    var inputHtml = '';

    var datetimeHead = '<h3>Data time <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var datetimestart = '<label class="span4">Date Start:</label> <input type="text" name ="'+type+'[datetimestart][condition_value]"  class="date" placeholder = "datetime start">\
                        <input type="hidden" name = "'+type+'[datetimestart][condition_type]" value="DATETIME_START">\
                        <input type = "hidden" name = "'+type+'[datetimestart][condition_id]" value="">';

    var datetimeend = '<label class="span4">Date End:</label> <input type="text" name = "'+type+'[datetimeend][condition_value]"  class="date" placeholder = "datetime end" >\
                    <input type="hidden" name = "'+type+'[datetimeend][condition_type]" value="DATETIME_END">\
                    <input type = "hidden" name = "'+type+'[datetimeend][condition_id]" value="">';
    var datetimeHtml = '<div class="datetime-wrapper '+type+'-type well">'+datetimeHead+datetimestart+'<br>'+datetimeend+'</div>';
    
    target.html = datetimeHtml;
    render(target);

    $('.date').datepicker({dateFormat: 'yy-mm-dd'});

}

function addLevel(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';
    var inputHtml = '';


    var levelHead = '<h3>Level <a class="remove"><i class="icon-remove-sign"></i></a></h3>';



    var levelstart = '<label class="span4">Level Start:</label>\<select name="'+type+'[levelstart][condition_value]">\<?php foreach($levels as $level){echo "<option value = ".$level["level"].">".$level["level"]." ".$level["level_title"]."</option>";}?></select>\
                    <input type="hidden" name = "'+type+'[levelstart][condition_type]" value = "LEVEL_START"/>\
                    <input type="hidden" name = "'+type+'[levelstart][condition_id]" value = ""/>';

    var levelend = "<label class='span4'>Level End:</label> <select name='"+type+"[levelend][condition_value]'><?php foreach($levels as $level){echo '<option value = '.$level['level'].'>'.$level['level'].' '.$level['level_title'].'</option>';}?></select>\
                    <input type='hidden' name = '"+type+"[levelend][condition_type]' value = 'LEVEL_END'/>\
                    <input type='hidden' name = '"+type+"[levelend][condition_id]' value = ''/>";
    


    var levelHtml = '<div class="level-wrapper '+type+'-type well">'+levelHead+levelstart+'<br>'+levelend+'</div>';

    target.html = levelHtml;

    render(target);
}

function addQuest(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';

    var questHead = '<h3>Quest <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-quest-btn">+ Add Quest</a></h3>';
    var questHtml = '<div class="quests-wrapper '+type+'-type well">'+questHead+'<div class="item-container"></div></div>';

    target.html = questHtml;
    render(target);

}

function addPoints(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';
    var inputHtml = '';

    if(parent == 'missions'){
        inputHtml = '<input type="text" name = "'+parent+'['+id+']['+type+'][point]['+type+'_value]" placeholder = "Points">\
                    <input type="hidden" name = "'+parent+'['+id+']['+type+'][point]['+type+'_type]" value = "POINT"/>\
                    <input type="hidden" name = "'+parent+'['+id+']['+type+'][point]['+type+'_id]" value = "<?php echo $point_id; ?>"/>';
    }else{
        inputHtml = '<input type="text" name = "'+type+'[point]['+type+'_value]" placeholder = "Points">\
                    <input type="hidden" name = "'+type+'[point]['+type+'_type]" value = "POINT"/>\
                    <input type="hidden" name = "'+type+'[point]['+type+'_id]" value = "<?php echo $point_id; ?>"/>';
    }

    var pointsHead = '<h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var pointsHtml = ' <div class="points-wrapper '+type+'-type well">'+pointsHead+'<label class="span4">Points:</label>'+inputHtml+'</div>';

    target.html = pointsHtml;
    render(target);
}

function addExp(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';
    var inputHtml = '';

    if(parent == 'missions'){
        inputHtml = '<input type="text" name = "'+parent+'['+id+']['+type+'][exp]['+type+'_value]" placeholder = "Exp">\
                    <input type="hidden" name = "'+parent+'['+id+']['+type+'][exp]['+type+'_type]" value = "EXP"/>\
                    <input type="hidden" name = "'+parent+'['+id+']['+type+'][exp]['+type+'_id]" value = "<?php echo $exp_id; ?>"/>';
    }else{
        inputHtml = '<input type="text" name = "'+type+'[exp]['+type+'_value]" placeholder = "Exp">\
                    <input type="hidden" name = "'+type+'[exp]['+type+'_type]" value = "EXP"/>\
                    <input type="hidden" name = "'+type+'[exp]['+type+'_id]" value = "<?php echo $exp_id; ?>"/>';
    }

    var expHead = '<h3>Exp <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var expHtml = ' <div class="exp-wrapper '+type+'-type well">'+expHead+'<label class="span4">Exp:</label>'+inputHtml+'</div>';

    target.html = expHtml;

    render(target);
}

function addCustompoints(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';

    var customPointsHead = '<h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Custom Points</a></h3>';
    var customPointsHtml = '<div class="custompoints-wrapper '+type+'-type well">'+customPointsHead+'<div class="item-container"></div></div>';
    
    target.html = customPointsHtml;

    render(target);
}

function addBadges(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';

    var badgesHead = '<h3>Badges  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Badges</a></h3>';
    var badgesHtml = '<div class="badges-wrapper '+type+'-type well">'+badgesHead+'<div class="item-container"></div></div>';

    target.html = badgesHtml;

    render(target);
}

function addActions(target){
    var type = target.type;
    var id = target.id || null;
    var parent = target.parent || 'quest';

    var actionsHead = '<h3>Actions  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-action-btn">+ Add Actions</a></h3>';
    var actionsHtml = '<div class="actions-wrapper '+type+'-type well">'+actionsHead+'<div class="item-container"></div></div>';

    target.html = actionsHtml;

    render(target);
}

function render(target){

    if(target.parent == 'missions'){
        $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+target.type+'-container').append(target.html);
    }else{
        $('.data-quest-wrapper .'+target.type+'-container').append(target.html);
    }

    init_additem_event(target);
}



// setModalBadgesItem
function setModalBadgesItem(target){
    setModalTarget($('#modal-select-badge'),target);
    var type = target.type;

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }
    
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
    var parent = target.parent || 'quest';
    var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }
    
    $('#modal-select-badge .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').length <= 0) {

                var id = $(this).data('id-badge');
                var img = $(this).find('.image img').attr('src');
                var title = $(this).find('.title').html();

                if(parent == 'missions'){
                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_value]" placeholder="Value" value="1"/>\
                                    <input type="hidden" name="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_id]" value = "'+id+'"/>\
                                    <input type="hidden" name="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_type]" value = "BADGE"/>'
                }else{
                    inputHtml = '<input type="text" name ="'+type+'['+id+']['+type+'_value]" placeholder="Value" value="1"/>\
                                    <input type="hidden" name="'+type+'['+id+']['+type+'_id]" value = "'+id+'"/>\
                                    <input type="hidden" name="'+type+'['+id+']['+type+'_type]" value = "BADGE"/>'
                }

                var badgesItemHtml = '<div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="'+id+'">\
                                    <div class="span2 text-center"><img src="'+img+'" alt="" onerror="$(this).attr(\'src\',\'http://localhost/control/image/default-image.png\');">\
                                    </div>\
                                    <div class="span7">'+title+'</div>\
                                    <div class="span1">\
                                    <small>value</small>\
                                    '+inputHtml+'</div>\
                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a>\
                                    </div></div>';

                   
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

// setModalCustompointsItem
function setModalCustompointsItem(target){
    
    setModalTarget($('#modal-select-custompoint'),target);

    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }
    
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
    var parent = target.parent || 'quest';
    var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }

    $('#modal-select-custompoint .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').length <= 0) {
                
                var id = $(this).data('id-custompoint');
                var title = $(this).find('.title').html();

                if(parent == 'missions'){
                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_value]" placeholder="Value" value="1"></div>\
                                <input type="hidden" name = "'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_type]" value = "CUSTOM_POINT"/>\
                                <input type="hidden" name = "'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_id]" value = "'+id+'"/>'
                }else{
                    inputHtml = '<input type="text" name ="'+type+'['+id+']['+type+'_value]" placeholder="Value" value="1"></div>\
                                <input type="hidden" name = "'+type+'['+id+']['+type+'_type]" value = "CUSTOM_POINT"/>\
                                <input type="hidden" name = "'+type+'['+id+']['+type+'_id]" value = "'+id+'"/>'
                }

                var itemHtml = '<div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="'+id+'">\
                                <div class="span7">'+title+'</div><div class="span3"><small>value</small>\
                                '+inputHtml+'\
                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div></div>';

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

// setModalQuestsItem
function setModalQuestsItem(target){
    setModalTarget($('#modal-select-quest'),target);

    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }
    
    $('#modal-select-quest input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.quests-item-wrapper').each(function(){
        var idSelect = $(this).data('id-quest');
        $('#modal-select-quest .select-item[data-id-quest='+idSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-quest').modal('show');
}

function selectQuestsItem(){
    var modalObj = $('#modal-select-quest');
    var target = {
        "type":modalObj.attr('data-type'),
        "id":modalObj.attr('data-mission-id'),
        "parent":modalObj.attr('data-parent')
    }

    var type = target.type;
    var taget_id = target.id || null;
    var parent = target.parent || 'quest';
    var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }

    $('#modal-select-quest .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.quests-item-wrapper[data-id-quest='+$(this).data('id-quest')+']').length <= 0) {

                var id = $(this).data('id-quest');
                var image = $(this).find('.image img').attr('src');
                var title = $(this).find('.title').html();

                if(target.parent == 'missions'){
                    var inputHtml = '<div class="span1"><input type="hidden" name ="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_id]" value="'+id+'"></div>\
                                     <input type="hidden" name = "'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_type]" value = "QUEST"/>\
                                     <input type="hidden" name = "'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_value]" value = ""/>'

                }else{
                    var inputHtml = '<div class="span1"><input type="hidden" name ="'+type+'['+id+']['+type+'_id]" value="'+id+'"></div>\
                                <input type="hidden" name = "'+type+'['+id+']['+type+'_type]" value = "QUEST"/>\
                                <input type="hidden" name = "'+type+'['+id+']['+type+'_value]" value = ""/>'
                }

                var itemHtml = '<div class="clearfix item-wrapper quests-item-wrapper" data-id-quest="'+id+'">\
                                <div class="span2 text-center"><img src="'+image+'" alt="" onerror="$(this).attr(\'src\',\'http://localhost/control/image/default-image.png\');">\
                                </div><div class="span7">'+title+'</div>\
                                '+inputHtml+'\
                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div></div>';

                    wrapperObj.find('.quests-wrapper .item-container').append(itemHtml);
                    init_additem_event(target);
            }
        }else{
            if(wrapperObj.find('.quests-item-wrapper[data-id-quest='+$(this).data('id-quest')+']').length >= 1) {
                wrapperObj.find('.quests-item-wrapper[data-id-quest='+$(this).data('id-quest')+']').remove();
            }
        }
    })
}

// setModalActionsItem
function setModalActionsItem(target){

    setModalTarget($('#modal-select-action'),target);

    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }

    $('#modal-select-action input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.actions-item-wrapper').each(function(){
        var idActionsSelect = $(this).data('id-action');
        $('#modal-select-action .select-item[data-id-action='+idActionsSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-action').modal('show');
}

function selectActionsItem(){

    var modalObj = $('#modal-select-action');
    var target = {
        "type":modalObj.attr('data-type'),
        "id":modalObj.attr('data-mission-id'),
        "parent":modalObj.attr('data-parent')
    }

    var type = target.type;
    var taget_id = target.id || null;
    var parent = target.parent || 'quest';
    var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');



    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.data-quest-wrapper .'+type+'-wrapper');
    }
    

    $('#modal-select-action .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            

            if(wrapperObj.find('.actions-item-wrapper[data-id-action='+$(this).data('id-action')+']').length <= 0) {

                var id = $(this).data('id-action');
                var img = $(this).find('.image img').attr('src');
                var title = $(this).find('.title').html();
                var icon = $(this).find('i').attr('class');
                


                if(parent == 'missions'){
                    
                    inputFilterHtml = '<small>filter</small><input type="text" name ="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_filter]"/>';

                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_value]" placeholder="Value" value="1"/>\
                                    <input type="hidden" name="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_id]" value = "'+id+'"/>\
                                    <input type="hidden" name="'+parent+'['+taget_id+']['+type+']['+id+']['+type+'_type]" value = "ACTION"/>';
                }else{
                    inputHtml = '<input type="text" name ="'+type+'['+id+']['+type+'_value]" placeholder="Value" value="1"/>\
                                    <input type="hidden" name="'+type+'['+id+']['+type+'_id]" value = "'+id+'"/>\
                                    <input type="hidden" name="'+type+'['+id+']['+type+'_type]" value = "ACTION"/>';
                }

                var actionsItemHtml = '<div class="clearfix item-wrapper actions-item-wrapper" data-id-action="'+id+'">\
                                    <div class="span2 text-center"><i class="'+icon+'"></i>\
                                    </div>\
                                    <div class="span5">'+title+'</div>\
                                    <div class="span2">'+inputFilterHtml+'</div>\
                                    <div class="span1">\
                                    <small>value</small>\
                                    '+inputHtml+'</div>\
                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a>\
                                    </div></div>';

                   
                wrapperObj.find('.actions-wrapper .item-container').append(actionsItemHtml);


                init_additem_event(target);
            }
        }else{

            if(wrapperObj.find('.actions-item-wrapper[data-id-action='+$(this).data('id-action')+']').length >= 1) {
                wrapperObj.find('.actions-item-wrapper[data-id-action='+$(this).data('id-action')+']').remove();
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
</script>



<script type="text/javascript">
    $('input[name=\'name\']').autocomplete({
        delay: 0,
        source: function(request, response) {
            $.ajax({
                url: baseUrlPath+'action/autocomplete?filter_name=' +  encodeURIComponent(request.term),
                dataType: 'json',
                success: function(json) {
//                console.log(json);
response($.map(json, function(item) {
    return {
        label: item.name,
        name: item.name,
        description: item.description,
        icon: item.icon,
        color: item.color,
        sort_order: item.sort_order,
        status: item.status
    }
}));
//                console.log(response);
}
});
        },
        select: function(event, ui) {
            $('input[name=\'name\']').val(ui.item.name);
            $('textarea[name=\'description\']').val(ui.item.description);
            $('select[name=\'status\']').val(ui.item.status);
            $('input[name=\'sort\']').val(ui.item.sort_order);
            $('input:radio[name=\'icon\'][value='+ui.item.icon+']').click();
            $('input:radio[name=\'color\'][value='+ui.item.color+']').click();

            return false;
        },
        focus: function(event, ui) {
            return false;
        }
    });
</script>

<script type="text/javascript">
    function image_upload(field, thumb) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+baseUrlPath+'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 200px; height: 100%;" frameborder="no" scrolling="no"></iframe></div>');

        $('#dialog').dialog({
            title: '<?php echo $this->lang->line('text_image_manager'); ?>',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: baseUrlPath+'filemanager/image?image=' + encodeURIComponent($('#' + field).val()),
                        dataType: 'text',
                        success: function(data) {
                            $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                        }
                    });
                }
            },
            bgiframe: false,
            width: 200,
            height: 100,
            resizable: false,
            modal: false
        });
    };
</script>