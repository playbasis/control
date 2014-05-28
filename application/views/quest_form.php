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
          <div id="tab-general">
            <div class="span6">
              <table class="form ">
                 <tr>
                    <td><span class="required">*</span> <?php echo $this->lang->line('form_quest_name'); ?>:</td>
                    <td><input type="text" name="quest_name" size="100" value="<?php echo isset($quest['quest_name']) ? $quest['quest_name'] :  set_value('name'); ?>" /></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('form_quest_description'); ?>:</td>
                    <td><textarea name ="description" rows="4"><?php echo isset($quest['description']) ? $quest['description'] :  set_value('description'); ?></textarea>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_quest_hint'); ?>:</td>
                        <td><input type="text" name="hint" size="100" value="<?php echo isset($quest['hint']) ? $quest['hint'] :  set_value('hint'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_quest_image'); ?>:</td>
                        <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                            <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                            <br /><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_quest_missionordering'); ?>:</td>
                        <td><input type="checkbox" name="mission_order" <?php echo isset($quest['status']) ?'checked':'unchecked'; ?> size="1" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_quest_status'); ?>:</td>
                        <td><input type="checkbox" name="status" <?php echo isset($quest['status']) ?'checked':'unchecked'; ?> size="1" /></td>
                    </tr>
                </table>
            </div>

                    <div class="box box-add-item condition-wrapper span6">
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
                            </div>
                        </div>
                    </div>

                    <div class="box box-add-item reward-wrapper span6">
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
                            <div class = 'reward-container'>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="tab-mission">
                    <table>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_condition'); ?>:</td>
                            <td>
                                
                            </td>
                        </tr>
                </table>
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


    <script type="text/javascript">
        $('#tabs a').tabs();

        $(function(){
            $('.date').datepicker({dateFormat: 'yy-mm-dd'});
        })

        function init_additem_event(type){

            var wrapperObj = $('.'+type+'-wrapper'),
            menuBtn = $('.add-'+type+'-btn'),
            menuObj = $('.add-'+type+'-menu'),

            addDatetimeObj = menuObj.find('.add-datetime'),
            addLevelObj = menuObj.find('.add-level'),
            addQuestObj = menuObj.find('.add-quest'),

            addPointObj = menuObj.find('.add-point'),
            addExpObj = menuObj.find('.add-exp'),
            addCustomPointObj = menuObj.find('.add-custompoint'),
            addBadgeObj = menuObj.find('.add-badge'),
            containerObj = $('.'+type+'-container');


            menuBtn.unbind().bind('click',function(data){
                wrapperObj.find('.box-content').show();
            });

            containerObj.find('.no-item').remove();

            if(containerObj.children().length <= 0){
                containerObj.append('<h3 class="no-item">No Item</h3>')
            }

            if(containerObj.has('.datetime-wrapper').length){
                addDatetimeObj.addClass('disabled');
                addDatetimeObj.unbind();
            }else{
                addDatetimeObj.removeClass('disabled');
                addDatetimeObj.unbind().bind('click',function(data){
                    addDatetime(type);
                });
            }

            if(containerObj.has('.level-wrapper').length){
                addLevelObj.addClass('disabled');
                addLevelObj.unbind();
            }else{
                addLevelObj.removeClass('disabled');
                addLevelObj.unbind().bind('click',function(data){
                    addLevel(type);
                });
            }


            if(containerObj.has('.points-wrapper').length){
                addPointObj.addClass('disabled');
                addPointObj.unbind();
            }else{
                addPointObj.removeClass('disabled');
                addPointObj.unbind().bind('click',function(data){
                    addPoints(type);
                });
            }
            
            if(containerObj.has('.exp-wrapper').length){
                addExpObj.addClass('disabled');
                addExpObj.unbind();
            }else{
                addExpObj.removeClass('disabled');
                addExpObj.unbind().bind('click',function(data){
                    addExp(type);
                });
            }


            //Add Badges

            if(containerObj.has('.badges-wrapper').length){
                addBadgeObj.removeClass('disabled');
                addBadgeObj.unbind().bind('click',function(data){
                    setModalBadgesItem(type);
                });
                containerObj.find('.badges-wrapper .add-badge-btn').bind('click',function(data){
                    setModalBadgesItem(type);
                });
            }else{
                addBadgeObj.removeClass('disabled');
                addBadgeObj.unbind().bind('click',function(data){
                    addBadges(type);
                    setModalBadgesItem(type);
                });
            }

            $('.select-badge-btn').unbind().bind('click',function(data){
                selectBadgesItem(type);
            });


            //Add Custom point
            if(containerObj.has('.custompoints-wrapper').length){
                addCustomPointObj.removeClass('disabled');
                addCustomPointObj.unbind().bind('click',function(data){
                    setModalCustompointItem(type);
                });
                containerObj.find('.custompoints-wrapper .add-custompoint-btn').bind('click',function(data){
                    setModalCustompointsItem(type);
                });
            }else{
                addCustomPointObj.removeClass('disabled');
                addCustomPointObj.unbind().bind('click',function(data){
                    addCustompoints(type);
                    setModalCustompointsItem(type);
                });
            }

            $('.select-custompoint-btn').unbind().bind('click',function(data){
                selectCustompointsItem(type);
            });


            //Add Quests point
            
            if(containerObj.has('.quests-wrapper').length){
                addQuestObj.removeClass('disabled');
                addQuestObj.unbind().bind('click',function(data){
                    setModalQuestsItem(type);
                });
                containerObj.find('.quests-wrapper .add-quest-btn').bind('click',function(data){
                    setModalQuestsItem(type);
                });
            }else{
                addQuestObj.removeClass('disabled');
                addQuestObj.unbind().bind('click',function(data){
                    addQuest(type);
                    setModalQuestsItem(type);
                });
            }

            $('.select-quest-btn').unbind().bind('click',function(data){
                selectQuestsItem(type);
            });


            //END TESTING

            containerObj.find('.remove').unbind('click').bind('click',function(data){
                var r = confirm("Are you sure to remove!");
                if (r == true) {
                    $(this).parent().parent().remove();
                    init_additem_event(type);
                }
            });

            containerObj.find('.item-remove').unbind('click').bind('click',function(data){
                var r = confirm("Are you sure to remove!");
                if (r == true) {
                    $(this).parent().parent().remove();
                    init_additem_event(type);
                }
            });
            
        }

$('.reward-wrapper').each(function(){
    init_additem_event('reward');
});
$('.condition-wrapper').each(function(){
    init_additem_event('condition');
});


var conditionCount = 1,
    rewardCount = 1;


function addDatetime(type){
    var datetimeHead = '<h3>Data time <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var datetimestart = '<label class="span4">Date Start:</label> <input type="text" name ="'+type+'[datetimestart][condition_value]"  class="date" placeholder = "datetime start">\
                        <input type="hidden" name = "'+type+'[datetimestart][condition_type]" value="DATETIME_START">\
                        <input type = "hidden" name = "'+type+'[datetimestart][condition_id]" value="">';

    var datetimeend = '<label class="span4">Date End:</label> <input type="text" name = "'+type+'[datetimeend][condition_value]"  class="date" placeholder = "datetime end" >\
                    <input type="hidden" name = "'+type+'[datetimeend][condition_type]" value="DATETIME_END">\
                    <input type = "hidden" name = "'+type+'[datetimeend][condition_id]" value="">';
    var datetimeHtml = '<div class="datetime-wrapper '+type+'-type well">'+datetimeHead+datetimestart+'<br>'+datetimeend+'</div>';
    
    render[type](datetimeHtml);
    $('.date').datepicker({dateFormat: 'yy-mm-dd'});

}

function addLevel(type){

    var levelHead = '<h3>Level <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var levelstart = "<label class='span4'>Level Start:</label>\<select name='"+type+"[levelstart][condition_value]'>\<?php foreach($levels as $level){echo '<option value = '.$level['level'].'>'.$level['level'].' '.$level['level_title'].'</option>';}?></select>\
                    <input type='hidden' name = '"+type+"[levelstart][condition_type]' value = 'LEVEL_START'/>\
                    <input type='hidden' name = '"+type+"[levelstart][condition_id]' value = ''/>";

    var levelend = "<label class='span4'>Level End:</label> <select name='"+type+"[levelend][condition_value]'><?php foreach($levels as $level){echo '<option value = '.$level['level'].'>'.$level['level'].' '.$level['level_title'].'</option>';}?></select>\
                    <input type='hidden' name = '"+type+"[levelend][condition_type]' value = 'LEVEL_END'/>\
                    <input type='hidden' name = '"+type+"[levelend][condition_id]' value = ''/>";

    var levelHtml = '<div class="level-wrapper '+type+'-type well">'+levelHead+levelstart+'<br>'+levelend+'</div>';

    render[type](levelHtml);
}

function addQuest(type){
    var questHead = '<h3>Quest <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-quest-btn">+ Add Quest</a></h3>';
    var questHtml = '<div class="quests-wrapper '+type+'-type well">'+questHead+'<div class="item-container"></div></div>';

    render[type](questHtml);

}

function addPoints(type){
    var pointsHead = '<h3>Points <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var pointsHtml = ' <div class="points-wrapper '+type+'-type well">'+pointsHead+'<label class="span4">Points:</label>\
                    <input type="text" name = "'+type+'[levelend]['+type+'_value]" placeholder = "Points"></div>\
                    <input type="hidden" name = "'+type+'[levelend]['+type+'_type]" value = "POINT"/>\
                    <input type="hidden" name = "'+type+'[levelend]['+type+'_id]" value = "<?php echo $point_id; ?>"/>';

    render[type](pointsHtml);
}

function addExp(type){
    var expHead = '<h3>Exp <a class="remove"><i class="icon-remove-sign"></i></a></h3>';

    var expHtml = ' <div class="exp-wrapper '+type+'-type well">'+expHead+'<label class="span4">Exp:</label>\
                    <input type="text" name = "'+type+'[exp]['+type+'_value]" placeholder = "Exp">\
                    <input type="hidden" name = "'+type+'[exp]['+type+'_type]" value = "EXP"/>\
                    <input type="hidden" name = "'+type+'[exp]['+type+'_id]" value = "<?php echo $exp_id; ?>"/>\
                    </div>';

    render[type](expHtml);
}

function addCustompoints(type){

    var customPointsHead = '<h3>Custom Points  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-custompoint-btn">+ Add Custom Points</a></h3>';
    
    var customPointsHtml = '<div class="custompoints-wrapper '+type+'-type well">'+customPointsHead+'<div class="item-container"></div></div>';
    
    render[type](customPointsHtml);
}

function addBadges(type){
    
    var badgesHead = '<h3>Badges  <a class="remove"><i class="icon-remove-sign"></i></a> <a class="btn add-badge-btn">+ Add Badges</a></h3>';
    var badgesHtml = '<div class="badges-wrapper '+type+'-type well">'+badgesHead+'<div class="item-container"></div></div>';

    render[type](badgesHtml);
}

var render = [];
render['condition'] = function(html){
    $('.condition-container').append(html);
    init_additem_event('condition');
}
render['reward'] = function(html){
    $('.reward-container').append(html);
    init_additem_event('reward');
}



// setModalBadgesItem
function setModalBadgesItem(type){
    var wrapperObj = $('.'+type+'-wrapper');
    
    $('#modal-select-badge input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.badges-item-wrapper').each(function(){
        var idBadgesSelect = $(this).data('id-badge');
        $('#modal-select-badge .select-item[data-id-badge='+idBadgesSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-badge').modal('show');
}

function selectBadgesItem(type){
    var wrapperObj = $('.'+type+'-wrapper');

    $('#modal-select-badge .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').length <= 0) {
                console.log($(this).data('id-badge'));
                var badgesItemHtml = '<div class="clearfix item-wrapper badges-item-wrapper" data-id-badge="'+$(this).data('id-badge')+'">\
                                    <div class="span2 text-center"><img src="'+$(this).find('.image img').attr('src')+'" alt="" onerror="$(this).attr(\'src\',\'http://localhost/control/image/default-image.png\');">\
                                    </div>\
                                    <div class="span7">'+$(this).find('.title').html()+'</div>\
                                    <div class="span1">\
                                    <small>value</small>\
                                    <input type="text" name ="'+type+'['+$(this).data('id-badge')+'][condition_value]" placeholder="Value" value="1"/>\
                                    <input type="hidden" name="'+type+'['+$(this).data('id-badge')+']['+type+'_id]" value = "'+$(this).data('id-badge')+'"/>\
                                    <input type="hidden" name="'+type+'['+$(this).data('id-badge')+']['+type+'_type]" value = "BADGE"/>\
                                    </div>\
                                    <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a>\
                                    </div></div>';

                    $('.'+type+'-wrapper .badges-wrapper .item-container').append(badgesItemHtml);
                    init_additem_event(type);
            }
        }else{
            if(wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').length >= 1) {
                wrapperObj.find('.badges-item-wrapper[data-id-badge='+$(this).data('id-badge')+']').remove();
            }
        }
    })
}

// setModalCustompointsItem
function setModalCustompointsItem(type){
    var wrapperObj = $('.'+type+'-wrapper');
    $('#modal-select-custompoint input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.custompoints-item-wrapper').each(function(){
        var idSelect = $(this).data('id-custompoint');
        $('#modal-select-custompoint .select-item[data-id-custompoint='+idSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-custompoint').modal('show');
}

function selectCustompointsItem(type){
    var wrapperObj = $('.'+type+'-wrapper');

    $('#modal-select-custompoint .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').length <= 0) {
                
                var id = $(this).data('id-custompoint');
                
                var title = $(this).find('.title').html();

                var itemHtml = '<div class="clearfix item-wrapper custompoints-item-wrapper" data-id-custompoint="'+id+'">\
                                <div class="span7">'+title+'</div><div class="span3"><small>value</small>\
                                <input type="text" name ="'+type+'[custompoints]['+type+'_value]" placeholder="Value" value="1"></div>\
                                <input type="hidden" name = "'+type+'[custompoints]['+type+'_type]" value = "CUSTOM_POINT"/>\
                                <input type="hidden" name = "'+type+'[custompoints]['+type+'_id]" value = "'+id+'"/>\
                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div></div>';

                    $('.'+type+'-wrapper .custompoints-wrapper .item-container').append(itemHtml);
                    init_additem_event(type);
            }
        }else{
            if(wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').length >= 1) {
                wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').remove();
            }
        }
    })
}

// setModalQuestsItem
function setModalQuestsItem(type){
    
    var wrapperObj = $('.'+type+'-wrapper');
    $('#modal-select-quest input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.quests-item-wrapper').each(function(){
        var idSelect = $(this).data('id-quest');
        $('#modal-select-quest .select-item[data-id-quest='+idSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-quest').modal('show');
}

function selectQuestsItem(type){
    
    var wrapperObj = $('.'+type+'-wrapper');

    $('#modal-select-quest .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.quests-item-wrapper[data-id-quest='+$(this).data('id-quest')+']').length <= 0) {

                var id = $(this).data('id-quest');
                var image = $(this).find('.image img').attr('src');
                var title = $(this).find('.title').html();

                var itemHtml = '<div class="clearfix item-wrapper quests-item-wrapper" data-id-quest="'+id+'">\
                                <div class="span2 text-center"><img src="'+image+'" alt="" onerror="$(this).attr(\'src\',\'http://localhost/control/image/default-image.png\');">\
                                </div><div class="span7">'+title+'</div>\
                                <div class="span1"><input type="hidden" name ="'+type+'['+id+']['+type+'_id]" value="'+id+'"></div>\
                                <input type="hidden" name = "'+type+'['+id+']['+type+'_type]" value = "QUEST"/>\
                                <input type="hidden" name = "'+type+'['+id+']['+type+'_value]" value = ""/>\
                                <div class="span2 col-remove"><a class="item-remove"><i class="icon-remove-sign"></i></a></div></div>';

                    $('.'+type+'-wrapper .quests-wrapper .item-container').append(itemHtml);
                    init_additem_event(type);
            }
        }else{
            if(wrapperObj.find('.quests-item-wrapper[data-id-quest='+$(this).data('id-quest')+']').length >= 1) {
                wrapperObj.find('.quests-item-wrapper[data-id-quest='+$(this).data('id-quest')+']').remove();
            }
        }
    })
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