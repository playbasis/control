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
    		<h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
    		<div class="buttons">
	    		<button class="btn btn-info" onclick="location =  baseUrlPath+'user_group/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
	            <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
    		</div>
    	</div><!-- .heading -->

    	<div class = "content">
    		<?php
            $attributes = array('id' => 'form');
            echo form_open('user_group/delete',$attributes);
            ?>

            	<table class = "list">
            		<thead>
            			<tr>
            				<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            				<td class="left" style="width:200px;"><?php echo $this->lang->line('column_name'); ?></td>
            				<td class="right" style="width:72px;"><?php echo $this->lang->line('column_action'); ?></td>
            			</tr>
            		</thead>

            		<tbody>
            			<tr class="filter">
	                        <td></td>
	                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
	                        <td class="right"><a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a></td>
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
                                <td class="right">[ <?php echo anchor('user_group/update/'.$user_group['_id'], "Edit");?> ]</td>
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
    	</div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->