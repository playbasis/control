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
                                        <td><input type="text" name="missions['+itemMissionId+'][mission_name]" size="100" value="<?php echo isset($mission["mission_name"]) ? $mission["mission_name"] :  set_value("name"); ?>" /></td>\
                                    </tr>\
                                    <tr>\
                                        <td><span class="required">*</span> <?php echo $this->lang->line("form_mission_number"); ?>:</td>\
                                        <td><input type="text" name="missions['+itemMissionId+'][mission_number]" size="100" value="<?php echo isset($mission["mission_number"]) ? $mission["mission_number"] :  set_value("number"); ?>" /></td>\
                                    </tr>\
                                    <tr>\
                                        <td><?php echo $this->lang->line("form_mission_description"); ?>:</td>\
                                        <td><textarea name ="missions['+itemMissionId+'][description]" rows="4"><?php echo isset($mission["description"]) ? $mission["description"] :  set_value("description"); ?></textarea>\
                                    </tr>\
                                    <tr>\
                                        <td><?php echo $this->lang->line("form_mission_hint"); ?>:</td>\
                                        <td><input type="text" name="missions['+itemMissionId+'][hint]" size="100" value="<?php echo isset($mission["hint"]) ? $mission["hint"] :  set_value("hint"); ?>" /></td>\
                                    </tr>\
                                    <tr>\
                                        <td><?php echo $this->lang->line("form_mission_image"); ?>:</td>\
                                        <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb_mission" onerror="$(this).attr("src","<?php echo base_url();?>image/default-image.png");" />\
                                            <input type="hidden" name="missions['+itemMissionId+'][image]" value="<?php echo $image; ?>" id="image" />\
                                            <br /><a onclick="image_upload("image", "thumb_mission");"><?php echo $this->lang->line("text_browse"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$("#thumb_mission").attr("src", "<?php echo $this->lang->line("no_image"); ?>"); $("#image").attr("value", "");"><?php echo $this->lang->line("text_clear"); ?></a></div>\
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

            var type = target.type;
            var parent = target.parent;
            var id = target.id || null;
            

            if(parent == 'missions'){
                var wrapperObj = $('.mission-item-wrapper[data-mission-id='+id+'] .'+type+'-wrapper');
                var containerObj = $('.mission-item-wrapper[data-mission-id='+id+'] .'+type+'-container');
            }else{
                var wrapperObj = $('.'+type+'-wrapper');
                var containerObj = $('.'+type+'-container');
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
                containerObj.append('<h3 class="no-item">No Item</h3>')
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
                selectBadgesItem(target);
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
                selectActionsItem(target);
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
                selectCustompointsItem(target);
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
                selectQuestsItem(target);
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
        $('.'+target.type+'-container').append(target.html);
    }

    init_additem_event(target);
}



// setModalBadgesItem
function setModalBadgesItem(target){
    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
    }
    
    $('#modal-select-badge input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.badges-item-wrapper').each(function(){
        var idBadgesSelect = $(this).data('id-badge');
        $('#modal-select-badge .select-item[data-id-badge='+idBadgesSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-badge').modal('show');
}

function selectBadgesItem(target){
    var type = target.type;
    var taget_id = target.id || null;
    var parent = target.parent || 'quest';
    var wrapperObj = $('.'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
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
    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
    }
    
    $('#modal-select-custompoint input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.custompoints-item-wrapper').each(function(){
        var idSelect = $(this).data('id-custompoint');
        $('#modal-select-custompoint .select-item[data-id-custompoint='+idSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-custompoint').modal('show');
}

function selectCustompointsItem(target){
    var type = target.type;
    var taget_id = target.id || null;
    var parent = target.parent || 'quest';
    var wrapperObj = $('.'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
    }

    $('#modal-select-custompoint .select-item').each(function(){
        if($(this).find('input[type=checkbox]').is(':checked')){
            
            if(wrapperObj.find('.custompoints-item-wrapper[data-id-custompoint='+$(this).data('id-custompoint')+']').length <= 0) {
                
                var id = $(this).data('id-custompoint');
                var title = $(this).find('.title').html();

                if(parent == 'missions'){
                    inputHtml = '<input type="text" name ="'+parent+'['+taget_id+']['+type+'][custompoints]['+type+'_value]" placeholder="Value" value="1"></div>\
                                <input type="hidden" name = "'+parent+'['+taget_id+']['+type+'][custompoints]['+type+'_type]" value = "CUSTOM_POINT"/>\
                                <input type="hidden" name = "'+parent+'['+taget_id+']['+type+'][custompoints]['+type+'_id]" value = "'+id+'"/>'
                }else{
                    inputHtml = '<input type="text" name ="'+type+'[custompoints]['+type+'_value]" placeholder="Value" value="1"></div>\
                                <input type="hidden" name = "'+type+'[custompoints]['+type+'_type]" value = "CUSTOM_POINT"/>\
                                <input type="hidden" name = "'+type+'[custompoints]['+type+'_id]" value = "'+id+'"/>'
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
    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
    }
    
    $('#modal-select-quest input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.quests-item-wrapper').each(function(){
        var idSelect = $(this).data('id-quest');
        $('#modal-select-quest .select-item[data-id-quest='+idSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-quest').modal('show');
}

function selectQuestsItem(target){
    var type = target.type;
    var taget_id = target.id || null;
    var parent = target.parent || 'quest';
    var wrapperObj = $('.'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
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
    var type = target.type;
    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
    }
    
    $('#modal-select-action input[type=checkbox]').prop('checked', false);
    wrapperObj.find('.actions-item-wrapper').each(function(){
        var idActionsSelect = $(this).data('id-action');
        $('#modal-select-action .select-item[data-id-action='+idActionsSelect+'] input[type=checkbox]').prop('checked', true);
    })

    $('#modal-select-action').modal('show');
}

function selectActionsItem(target){
    var type = target.type;
    var taget_id = target.id || null;
    var parent = target.parent || 'quest';
    var wrapperObj = $('.'+type+'-wrapper');

    if(target.parent == 'missions'){
        var wrapperObj = $('.mission-item-wrapper[data-mission-id='+target.id+'] .'+type+'-wrapper');
    }else{
        var wrapperObj = $('.'+type+'-wrapper');
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