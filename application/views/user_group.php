<div id="content" class="span10">

	<!-- Messages to display -->
	<?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <div class="box">

    	<div class="heading">
    		<h1><img src="image/user-group.png" alt="" /> <?php echo $heading_title; ?></h1>
    		<div class="buttons">
	    		<button class="btn btn-info" onclick="location =  baseUrlPath+'user_group/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
	            <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
    		</div>
    	</div><!-- .heading -->

    	<div class = "content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
    		<?php
            $attributes = array('id' => 'form');
            echo form_open('user_group/delete',$attributes);
            ?>

            	<table class = "list">
            		<thead>
            			<tr>
            				<td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            				<td class="left" style="width:200px;"><?php echo $this->lang->line('column_name'); ?></td>
            				<td class="right" style="width:72px;"><?php echo $this->lang->line('column_action'); ?></td>
            			</tr>
            		</thead>

            		<tbody>
            			<tr class="filter">
	                        <td></td>
	                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
	                        <td class="right">
                                <a onclick="clear_filter();" id="clear_filter" class="button"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                                <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                            </td>
	                    </tr>
                        <?php if (isset($user_groups)){?>
                            <?php foreach($user_groups as $user_group){?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if (isset($user['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $user_group['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $user_group['_id']; ?>" />
                                    <?php } ?>
                                </td>
                                <td class="left"><?php echo $user_group['name'];?></td>
                                <td class="right">
                                    <?php echo anchor('user_group/update/'.$user_group['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                        array('class'=>'tooltips',
                                            'title' => 'Edit',
                                            'data-placement' => 'top'
                                        ));
                                    ?>
                                </td>
                            </tr>
                            <?php }?>
                        <?php }else{?>
                            <tr>
                                <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
                            </tr>
                        <?php }?>
            		</tbody>
            	</table>
            <?php form_close(); ?>
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
            </div><!-- .pagination -->
    	</div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

<script type="text/javascript"><!--
function filter() {
    url = baseUrlPath+'user_group';

    var filter_name = $('input[name=\'filter_name\']').attr('value');

    if (filter_name) {
        url += '?filter_name=' + encodeURIComponent(filter_name);
    }

    location = url;
}
//--></script>

<script type="text/javascript">
$('input[name=\'filter_name\']').autocomplete({
    delay: 0,
    source: function(request, response) {
        $.ajax({
            url: baseUrlPath+'user_group/autocomplete?filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                response($.map(json, function(item) {
                    return {
                        label: item.username,
                        username: item.username
                    }
                }));
                console.log(response);
            }
        });
    },
    select: function(event, ui) {
        $('input[name=\'filter_name\']').val(ui.item.username);

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
});
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
        $("#clear_filter").hide();
    <?php }else{?>
        $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        window.location.replace(baseUrlPath+'user_group');
    }
</script>