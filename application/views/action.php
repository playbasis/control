<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <?php if($isAdmin){ ?>
                <button class="btn btn-info" onclick="location =  baseUrlPath+'action/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
                <?php }?>
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
                        <?php if($isAdmin){ ?>
                    <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <?php }?>
                    <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_added'); ?></td>
                    <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                    <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="filter">
                        <?php if($isAdmin){ ?>
                        <td></td>
                        <?php }?>
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>
                    
                        <?php if(isset($actions)){?>
                            <?php foreach($actions as $action){?>
                                <tr>
                                    <?php if($isAdmin){ ?>
                                    <td style="text-align: center;">
                                        <?php if (isset($action['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" checked="checked" />
                                        <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $action['_id']; ?>" />
                                        <?php } ?></td>
                                    <?php }?>
                                    <td class="left"><?php echo "<i style='color:grey' class='".$action['icon']." icon-4x'></i>"; ?></td>
                                    <!-- <td class="right"><?php //echo ucfirst($action['name']); ?></td> -->
                                    <td class="right"><?php echo $action['name']; ?></td>
                                    <td class="right"><?php echo datetimeMongotoReadable($action['date_added']); ?></td>
                                    <td class="right"><?php echo ($action['status'])? "Enabled" : "Disabled"; ?></td>
                                    <td class="right">
                                        <?php
                                            $action_id = $client_id ? $action['action_id'] : $action['_id'];
                                            echo anchor('action/update/'.$action_id,
                                                    $client_id ? "<i class='fa fa-eye fa-lg''></i>" : "<i class='fa fa-edit fa-lg''></i>",
                                                    $client_id ? array('class'=>'tooltips', 'title' => 'View', 'data-placement' => 'top') :
                                                                 array('class'=>'tooltips', 'title' => 'Edit', 'data-placement' => 'top'));
                                        ?>
                                        <?php
                                        echo anchor('action/increase_order/'.$action_id, '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$action_id, 'style'=>'text-decoration:none'));
                                        echo anchor('action/decrease_order/'.$action_id, '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$action_id, 'style'=>'text-decoration:none'));
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
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
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
        var getListForAjax = 'action/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
        console.log(baseUrlPath+getListForAjax+getNum);
        // $('#actions').load(baseUrlPath+'action/getListForAjax/0');
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
        var getListForAjax = 'action/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
        // $('#actions').load(baseUrlPath+'action/getListForAjax/0');
    });


  return false;
});

</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
        $("#clear_filter").hide();
    <?php }else{?>
        $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        window.location.replace(baseUrlPath+'action');
    }
</script>