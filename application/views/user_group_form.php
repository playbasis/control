<div id="content" class="span10">
	<div class="box">
		<div class="heading">
			<h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'user_group'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
			</div><!-- .buttons -->
		</div><!-- .heading -->
		

		<div class = "content">
			<div id = "tab-general">
				<table class = "form">
					<tr>
						<td><span class="required">*</span> <?php echo $this->lang->line('form_usergroup_name'); ?></td>
						<td><input type ="text" name = "usergroup_name" size = "100" value = "<?php echo isset($user_group_info['name'])? $user_group_info['name'] : set_value('user group name') ?>" /></td>
					</tr>
					<tr>
						<td><span class="required">*</span> <?php echo $this->lang->line('form_access_permission'); ?></td>
						<td>
							<div class="scrollbox">
								<?php if(isset($all_features)&&(isset($user_group_info))){?>
									<?php 
										$permissions = array();
										$permissions = unserialize($user_group_info['permission']);
										$permissions_access = $permissions['access'];
									?>
									<?php for($i = 0; $i<count($all_features); $i++){?>
									<?php $feature_lowercase = implode("_", explode(" ",strtolower($all_features[$i]['name'])));?>
										<?php if(in_array($feature_lowercase, $permissions_access)){?>
											<div class = '<?php if(($i%2) == 0){echo "even";}else{echo "odd";}?>'>
												<input type="checkbox" checked = "checked" name="permission[access][]" value="<?php echo $feature_lowercase?>"> <?php echo $all_features[$i]['name'];?>
											</div>
										<?php }else{?>
											<div class = '<?php if(($i%2) == 0){echo "even";}else{echo "odd";}?>'>
												<input type="checkbox" name="permission[access][]" value="<?php echo $feature_lowercase?>"> <?php echo $all_features[$i]['name'];?>
											</div>
										<?php }?>
									<?php }?>
								<?php }elseif(isset($all_features)){?>
										
									<?php for($i = 0; $i<count($all_features); $i++){?>
									<?php $feature_lowercase = implode("_", explode(" ",strtolower($all_features[$i]['name'])));?>
										<div class = '<?php if(($i%2) == 0){echo "even";}else{echo "odd";}?>'>
										<input type="checkbox" value ="check1" name="permission[access][]" value="<?php echo $feature_lowercase?>"> <?php echo $all_features[$i]['name'];?>
										</div>
									<?php }?>
								<?php }?>
							</div> <!-- .scrollbox -->
							<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $this->lang->line('text_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $this->lang->line('text_unselect_all'); ?></a>
						</td>
					</tr>
					<tr>
						<td><span class="required">*</span> <?php echo $this->lang->line('form_modify_permission'); ?></td>
						<td>
							<div class = "scrollbox">
								<?php if(isset($all_features)&&(isset($user_group_info))){?>
									<?php 
										$permissions = array();
										$permissions = unserialize($user_group_info['permission']);
										$permissions_modify = $permissions['modify'];
									?>
									<?php for($i = 0; $i<count($all_features); $i++){?>
									<?php $feature_lowercase = implode("_", explode(" ",strtolower($all_features[$i]['name'])));?>
										<?php if(in_array($feature_lowercase, $permissions_modify)){?>
											<div class = '<?php if(($i%2) == 0){echo "even";}else{echo "odd";}?>'>
												<input type="checkbox" checked = "checked" name="permission[modify][]" value="<?php echo $feature_lowercase?>"> <?php echo $all_features[$i]['name'];?>
											</div>
										<?php }else{?>
											<div class = '<?php if(($i%2) == 0){echo "even";}else{echo "odd";}?>'>
												<input type="checkbox" name="permission[modify][]" value="<?php echo $feature_lowercase?>"> <?php echo $all_features[$i]['name'];?>
											</div>
										<?php }?>
									<?php }?>
								<?php }elseif(isset($all_features)){?>
										
									<?php for($i = 0; $i<count($all_features); $i++){?>
									<?php $feature_lowercase = implode("_", explode(" ",strtolower($all_features[$i]['name'])));?>
										<div class = '<?php if(($i%2) == 0){echo "even";}else{echo "odd";}?>'>
										<input type="checkbox" value ="check1" name="permission[modify][]" value="<?php echo $feature_lowercase?>"> <?php echo $all_features[$i]['name'];?>
										</div>
									<?php }?>
								<?php }?>
							</div> <!--.scrollbox -->
							<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $this->lang->line('text_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $this->lang->line('text_unselect_all'); ?></a>
						</td>
					</tr>
				</table>				
			</div> <!-- #tab-general -->
		</div><!-- .content -->
	</div><!-- .box -->
</div><!-- #content .span10 -->
