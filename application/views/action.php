<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
            <button class="btn btn-info" onclick="location =  baseUrlPath+'action/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div><!-- .heading -->
        <div class="content">
        <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="actions">
        <?php $attributes = array('id'=>'form');?>
        <?php echo form_open('action/delete', $attributes);?>
            <table class="list">
                <thead>
                    <tr>
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_added'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_order'); ?></td>
                    <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="filter">
                        <td></td>
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right"><a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a></td>
                    </tr>
                    
                        <?php if(isset($actions)){?>
                            <?php foreach($actions as $action){?>
                                <tr>
                                    <td style="text-align: center;"><?php if (isset($action['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" checked="checked" />
                                        <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" />
                                        <?php } ?></td>
                                    <td class="left"><?php echo "<i style='color:grey' class='".$action['icon']." icon-4x'></i>"; ?></td>
                                    <td class="left"><?php echo $action['name']; ?></td>
                                    <td class="right"><?php echo datetimeMongotoReadable($action['date_added']); ?></td>
                                    <td class="right"><?php echo ($action['status'])? "Enabled" : "Disabled"; ?></td>
                                    <td class="right"><?php echo $action['sort_order'];?></td>
                                    <td class="right">[ <?php if($client_id){
                                            echo anchor('action/update/'.$action['action_id'], 'Edit');
                                        }else{
                                            echo anchor('action/update/'.$action['_id'], 'Edit');
                                        }
                                        ?> ]

                                        <?php if($client_id){

                                            echo anchor('action/increase_order/'.$action['action_id'], 'Push Down', array('class'=>'push_down', 'alt'=>$action['action_id']));
                                        }else{
                                            echo anchor('action/increase_order/'.$action['_id'], 'Push Down', array('class'=>'push_down', 'alt'=>$action['_id']));
                                        }
                                        ?>
                                        <?php if($client_id){
                                            echo anchor('action/decrease_order/'.$action['action_id'], 'Push Up', array('class'=>'push_up', 'alt'=>$action['action_id']));
                                        }else{
                                            echo anchor('action/decrease_order/'.$action['_id'], 'Push Up', array('class'=>'push_up', 'alt'=>$action['_id']));
                                        }
                                        ?>
                                        </td>   

                                </tr>
                            <?php }?>
                        <?php }?>
                    
                </tbody>
            </table>
        <?php echo form_close();?>
        </div>
            <div class="pagination">
                <?php 
                if(!isset($_GET['filter_name'])){
                    echo $this->pagination->create_links();    
                }
                ?>
            </div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

<script type="text/javascript"><!--
function filter() {
    url = baseUrlPath+'action';

    var filter_name = $('input[name=\'filter_name\']').attr('value');

    if (filter_name) {
        url += '?filter_name=' + encodeURIComponent(filter_name);
    }

    location = url;
}
//--></script>

<script type="text/javascript">
    $('input[name=\'filter_name\']').live("focus", function (event) {
        $(this).autocomplete({
            delay: 0,
            source: function(request, response) {
                $.ajax({
                    url: baseUrlPath+'action/autocomplete?filter_name=' +  encodeURIComponent(request.term),
                    dataType: 'json',
                    success: function(json) {
                        console.log(json);
                        response($.map(json, function(item) {
                            return {
                                label: item.name,
                                name: item.name
                            }
                        }));
                        console.log(response);
                    }
                });
            },
            select: function(event, ui) {
                $('input[name=\'filter_name\']').val(ui.item.name);

                return false;
            },
            focus: function(event, ui) {
                return false;
            }
        });
    });
</script>


<script type = "text/javascript">

$( ".push_down" ).live( "click", function() {
  
    $.ajax({
        url: baseUrlPath+'action/increase_order/'+$(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("We are herer");
        $('#actions').load(baseUrlPath+'action/getListForAjax/0');
    });


  return false;
});

</script>

<script type = "text/javascript">

$( ".push_up" ).live( "click", function() {
  
    $.ajax({
        url: baseUrlPath+'action/decrease_order/'+$(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("We are herer");
        $('#actions').load(baseUrlPath+'action/getListForAjax/0');
    });


  return false;
});

</script>