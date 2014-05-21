<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'action'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div><!-- .buttons -->
        </div><!-- .heading -->
        <div class="content">
        	<?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
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
            		<table class="form">
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_quest_name'); ?>:</td>
            				<td><input type="text" name="name" size="100" value="<?php echo isset($action['name']) ? $action['name'] :  set_value('name'); ?>" /></td>
            			</tr>
            			<tr>
            				<td><?php echo $this->lang->line('form_quest_description'); ?>:</td>
            				<td><textarea name ="description" rows="4"><?php echo isset($action['description']) ? $action['description'] :  set_value('description'); ?></textarea>
            			</tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_hint'); ?>:</td>
                            <td><input type="text" name="hint" size="100" value="<?php echo isset($action['hint']) ? $action['hint'] :  set_value('hint'); ?>" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_image'); ?>:</td>
                            <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                <br /><a onclick="image_upload('image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                        </tr>
            			<tr>
                            <td><?php echo $this->lang->line('form_quest_condition'); ?>:</td>
                            <td>
                                <div class = 'conditioncontainer'>
                                    <div class="conditionsubcontainer" data-item-id=0>
                                        <select name = 'condition_type[]' class="conditiontype">
                                            <option value = 'noselect'>SELECT ONE</option>
                                            <option value = 'datetime'>DATETIME</option>
                                            <option value = 'level'>LEVEL</option>
                                            <option value = 'quest'>QUEST</option>
                                            <option value = 'point'>POINT</option>
                                            <option value = 'custompoint'>CUSTOM POINT</option>
                                            <option value = 'badge'>BADGE</option>
                                        </select>
                                        <div class="condition"></div>
                                    </div>    
                                </div>
                                <a onclick=addcondition() >+ Condition</a>
                            </td>
            			</tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_rewards');?></td>
                            <td>To do...</td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_missions');?></td>
                            <td>To do...</td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_missionordering'); ?>:</td>
                            <td><input type="checkbox" name="status" <?php echo isset($action['status']) ?'checked':'unchecked'; ?> size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_status'); ?>:</td>
                            <td><input type="checkbox" name="status" <?php echo isset($action['status']) ?'checked':'unchecked'; ?> size="1" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_start'); ?>:</td>
                            <td><input type="text" class="date" name="date_start" value="<?php if (strtotime(datetimeMongotoReadable($date_start))) {echo date('Y-m-d', strtotime(datetimeMongotoReadable($date_start)));} else { echo $date_start; } ?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('form_quest_end'); ?>:</td>
                            <td><input type="text" class="date" name="date_end" value="<?php if (strtotime(datetimeMongotoReadable($date_end))) {echo date('Y-m-d', strtotime(datetimeMongotoReadable($date_end)));} else { echo $date_end; } ?>" size="50" /></td>
                        </tr>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

<script type="text/javascript"> 
    $(function(){
        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
    })
</script>

<script type="text/javascript">

function init_event(){

    $('.conditiontype').unbind().bind('change',function (data){

        var id = $(this).parent().attr('data-item-id');
    

        // var chosenCondition = $('.conditiontype :selected').attr('value');

        var chosenCondition = $('div[data-item-id="' + id + '"]').children('.conditiontype').attr('value');

        var num = $('.conditiontype :selected').data('index');

        switch (chosenCondition){
            case "datetime":
                datetime(id);
                break;
            case "level":
                level(id);
                break;
            case "quest":
                quest(id);
                break;
            case "point":
                points(id);                
                break;
            case "custompoint":
                custompoints(id);
                break;
            case "badge":
                badges(id);
                break;
            default:
                alert('default');
        }
    });
}

init_event();

function datetime(id){

    // $('.condition').empty();
    // $("div").find("[data-item-id='" + id + "']").children('.condition').empty();
    var datetimestart = "<input type='text' name = 'datetimestart' class='date' placeholder = 'datetime start'>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(datetimestart);
    // $('.condition').append(datetimestart);
    var datetimeend = "<input type='text' name = 'datetimeend' class='date' placeholder = 'datetime end'>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(datetimeend);
}

function level(id){
    // $("div").find("[data-item-id='" + id + "']").children('.condition').empty();
    var levelstart = "<input type='text' name = 'levelstart' placeholder = 'Level start'>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(levelstart);
    // $('.condition').append(datetimestart);
    var levelend = "<input type='text' name = 'levelend' placeholder = 'Level end'>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(levelend);
}

function quest(id){
    <?php $dummyQuests = array('quest1', 'quest2', 'quest3', 'quest4', 'quest5'); ?>
    var quest = "<select><?php foreach($dummyQuests as $quest){echo '<option>'.$quest.'</option>';}?></select>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(quest);
}

function points(id){
    var points = "<input type='text' name = 'points' placeholder = 'Points'>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(points);
}

function custompoints(id){
    <?php $dummyCustomPoints = array('custom1', 'custom2', 'custom3'); ?>
    var customPoints = "<select><?php foreach($dummyCustomPoints as $custom){echo '<option>'.$custom.'</option>';}?></select>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(customPoints);

    var custompointvalue = "<input type='text' name = 'custompointvalue' placeholder = 'Custom Point'>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(custompointvalue);
}

function badges(id){
    <?php $dummyBadges = array('badge1', 'badge2', 'badge3'); ?>
    var dummyBadges = "<select><?php foreach($dummyBadges as $badge){echo '<option>'.$badge.'</option>';}?></select>";
    $("div").find("[data-item-id='" + id + "']").children('.condition').append(dummyBadges);
}

</script>

<script type="text/javascript">
    var count = 1;
    function addcondition(){
        var toAppend = "<div class='conditionsubcontainer' data-item-id="+count+"><select name = 'condition_type[]' class='conditiontype'><option value = 'noselect'>SELECT ONE</option><option value = 'datetime'>DATETIME</option><option value = 'level'>LEVEL</option><option value = 'quest'>QUEST</option><option value = 'point'>POINT</option><option value = 'custompoint'>CUSTOM POINT</option><option value = 'badge'>BADGE</option></select><div class='condition'></div>";
        $('.conditioncontainer').append(toAppend);        
        count++;
        init_event();
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