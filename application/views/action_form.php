<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        	<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <?php if($isAdmin){ ?>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'action'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
                 <?php }else{ ?>
                <button class="btn btn-info" onclick="location = baseUrlPath+'action'" type="button"><?php echo $this->lang->line('button_back'); ?></button>
                <?php }?>
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
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_action_name'); ?>:</td>
            				<td><input type="text" name="name" size="100" value="<?php echo isset($action['name']) ? $action['name'] :  set_value('name'); ?>" /></td>
            			</tr>
            			<tr>
            				<td><?php echo $this->lang->line('form_description'); ?>:</td>
            				<td><textarea name ="description" rows="4"><?php echo isset($action['description']) ? $action['description'] :  set_value('description'); ?></textarea>
            			</tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_status'); ?>:</td>
            				<td>
            					<select name = 'status'>
            					<?php if($action['status'] || set_value('status')){?>
            						<option selected='selected' value="1">Enabled</option>
            						<option value="0">Disabled</option>
            					<?php }else{?>
                                    <option value="1">Enabled</option>
            						<option selected='selected' value="0">Disabled</option>
            					<?php }?>
            					</select>
            				</td>
            			</tr>
                        <tr>
                            <?php if(!$client_id && !isset($action['_id'])){?>
                            <td><?php echo $this->lang->line('form_client');?>:</td>
                            <td>
                                <select name = 'client_id'>
                                    <?php if(isset($clients)){?>
                                    <option value = 'admin_only'>Admin Only</option>
                                        <?php foreach($clients as $client){?>
                                            <?php if(trim($client['company'])=="" || !isset($client['company'])){?>
                                                <option value ="<?php echo $client['_id']?>"><?php echo $client['first_name']." ".$client['last_name'];?></option>    
                                            <?php }else{?>
                                                <option value ="<?php echo $client['_id']?>"><?php echo $client['company'];?></option>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>
                                </select>
                            </td>    
                            <?php }?>
                        </tr>
            			<tr>
                            <td><?php echo $this->lang->line('form_sort'); ?>:</td>
                            <td><input type="text" name="sort_order" value="<?php echo isset($action['sort_order']) ? $action['sort_order'] : set_value('sort_order'); ?>" size="1" /></td>
            			</tr>
            			<?php if($isAdmin){ ?>
            			<tr>

            				<td><span class="required">*</span> <?php echo $this->lang->line('form_icon'); ?>: <i style="color: grey" class="<?php echo $action['icon']?> icon-4x"></i> <?php //echo substr($action['icon'], 8);?></td>
            				<td>
            					<div class="scrollbox" style="height: 200px">
            						<?php if(isset($icons)){?>
            							<?php for($i = 0 ; $i<count($icons); $i++){?>
            								<div class="<?php if($i%2==0){echo 'even';}else{echo 'odd';}?>">
            									<!--input type="radio" <?php if($icons[$i]==$action['icon'] || $icons[$i]==set_value('icon')){echo "checked = 'checked'";}?> name="icon" value="<?php echo $icons[$i];?>"> <i style="color:grey" class="<?php echo $icons[$i];?> icon-large"></i> <?php echo ucfirst(substr($icons[$i], 8));?> -->
            									<input type="radio" <?php if($icons[$i]==$action['icon'] || $icons[$i]==set_value('icon')){echo "checked = 'checked'";}?> name="icon" value="<?php echo $icons[$i];?>"> <i style="color:grey" class="fa <?php echo $icons[$i];?> fa-lg"></i> <?php echo substr($icons[$i], 3);?>
            								</div>
            							<?php }?>
            						<?php }?>
            					</div>
            				</td>
            			</tr>
            			<tr>
            				<td><span class="required">*</span> <?php echo $this->lang->line('form_color'); ?>:</td>
            				<td>
            					<div class="scrollbox">
            						<?php if(isset($colors)){?>
            							<?php for($i=0; $i<count($colors);$i++){?>
            								<div class="<?php if($i%2==0){echo 'even';}else{echo 'odd';}?>">
            									<input type="radio" name="color" <?php if($colors[$i]==$action['color'] || $colors[$i]==set_value('color')){echo "checked = 'checked'";}?> value="<?php echo $colors[$i];?>"> <span class="<?php echo $colors[$i];?>"><?php echo ucfirst($colors[$i]);?></span>		
            								</div>
            							<?php }?>
            						<?php }?>
            					</div>
            				</td>
            			</tr>
                        <?php }?>
                        <?php if(isset($action['init_dataset']) || ($isAdmin)) {?>
                        <tr>

                            <td><span class="required">*</span> <?php echo $this->lang->line('form_text_field'); ?>:</td>
                            <td>
                            <div class="row-fluid">
                                <table class="table table-bordered" id="new-branches-table">
                                    <thead>
                                        <th>Name</th>
                                        <th>Label</th>
                                        <th>Required </th>
                                    </thead>
                                    <tbody>

                                    <?php if(isset($action['init_dataset'])){?>
                                        <?php foreach($action['init_dataset'] as $key => $param){?>
                                            <tr>
                                                <td><input type="text" name="<?php echo "init_dataset[".$key."][param_name]" ?>"
                                                           value="<?php echo isset($param['param_name']) ? $param['param_name']: set_value('parameter'); ?>"
                                                        <?php if(!($isAdmin)){echo "readonly";}?>>
                                                </td>
                                                <td><input type="text" name="<?php echo "init_dataset[".$key."][label]" ?>"
                                                           value="<?php echo isset($param['label']) ? $param['label']: set_value('parameter'); ?>"
                                                        <?php if(!($isAdmin)){echo "readonly";}?>>
                                                </td>
                                                <td><input type="checkbox" name="<?php echo "init_dataset[".$key."][required]" ?>" data-handle-width="40" value="true"
                                                           <?php echo isset($param['required']) ? (($param['required'])? 'checked="checked"':''): ''; ?>
                                                    <?php if(!($isAdmin)){echo "disabled readonly";}?>>
                                                </td>
                                            </tr>
                                        <?php }?>
                                    <?php }?>

                                    </tbody>
                                    <tfoot>
                                    <?php if($isAdmin){ ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center">
                                            <div class="row-fluid">
                                                <div class="offset3 span3">
                                                    <a class="btn btn-primary btn-block" id="add" onclick="createParameterRow()"><i class="fa fa-plus"></i>&nbsp;Add</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php }?>
                                    </tfoot>
                                </table>
                            </div>
                            </td>
                        </tr>
                        <?php }?>
            		</table>
            	</div>
            <?php echo form_close();?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

<div id="newParam_emptyElement" class="hide invisible">
    <table>
        <tr>
            <td><input type="text" name="init_dataset[{{id}}][param_name]" value=""></td>
            <td><input type="text" name="init_dataset[{{id}}][label]" value=""></td>
            <td><input type="checkbox" name="init_dataset[{{id}}][required]" data-handle-width="40" checked="checked" value="true"</td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    var globalNewIndex = <?php echo isset($action['init_dataset']) ?count($action['init_dataset']):0; ?>;
    function createParameterRow(numToCreate){
        numToCreate = typeof numToCreate !== 'undefined' ? numToCreate : 1;

        if ($.isNumeric(numToCreate) && numToCreate > 0) {
            for (idx = 0; idx < numToCreate; idx++) {
                var tableRowHTML = $('#newParam_emptyElement').find('tbody').html();
                var newIndex = globalNewIndex;

                tableRowHTML = tableRowHTML.replace(new RegExp('{{id}}', 'g'), newIndex);

                $('#new-branches-table').find('tbody').append(tableRowHTML);

                globalNewIndex++;
            }
            //$(":not(div .bootstrap-switch-container)>input[name^='newBranches'][name$='[status]']:not([name*='id'])").bootstrapSwitch();
        }
    };
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