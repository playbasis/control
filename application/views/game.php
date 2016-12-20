<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
            <?php if($game == 'campaign') { ?>
                <button class="btn btn-info" onclick="showCampaignModalForm();" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            <?php } else { ?>
                <button class="btn btn-primary btn-lg" onclick="game_validation();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-primary btn-lg" onclick="location = baseUrlPath+'game'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            <?php } ?>
            </div>
        </div>

        <div class="content">
            <div id="tabs" class="htabs">
                <?php if($is_enable_campaign) { ?>
                <a id="tab_campaign" href="<?php echo site_url('game/campaign_index');?>" style="display: inline;"><?php echo $this->lang->line('tab_campaign'); ?></a>
                <?php } ?>
                <a id="tab_farm" href="<?php echo site_url('game/farm');?>" style="display: inline;"><?php echo $this->lang->line('tab_farm'); ?></a>
                <a id="tab_bingo" href="<?php echo site_url('game/bingo');?>" style="display: inline;"> <?php echo $this->lang->line('tab_bingo'); ?></a>
                <a id="tab_egg" href="<?php echo site_url('game/egg');?>" style="display: inline;"><?php echo $this->lang->line('tab_egg'); ?></a>
                <a id="tab_pairs" href="<?php echo site_url('game/pairs');?>" style="display: inline;"><?php echo $this->lang->line('tab_pairs'); ?></a>
                <a id="tab_catch_item" href="<?php echo site_url('game/catch_item');?>" style="display: inline;"><?php echo $this->lang->line('tab_catch'); ?></a>
            </div>
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php
            if(validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
            <?php if($game == 'campaign') { ?>
            <div id="tab-campaign">
                <?php
                $attributes = array('id' => 'form');
                echo form_open('game/delete',$attributes);
                ?>
                <div>
                <table class="list">
                    <thead>
                    <tr>
                        <td rowspan="2" width="7" style="text-align: center;">
                            <input type="checkbox"
                                   onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
                        </td>
                        <td rowspan="2" class="center" style="width:80px;"><?php echo $this->lang->line('entry_game_name'); ?></td>
                        <td colspan="4" class="center" style="width:150px;"><?php echo $this->lang->line('entry_campaign'); ?></td>
                        <td rowspan="2" class="center" style="width:50px;"><?php echo $this->lang->line('entry_campaign_status'); ?></td>
                    </tr>
                    <tr>
                        <td class="center" style="width:80px;"><?php echo $this->lang->line('entry_campaign_name'); ?></td>
                        <td class="center" style="width:80px;"><?php echo $this->lang->line('entry_campaign_start'); ?></td>
                        <td class="center" style="width:80px;"><?php echo $this->lang->line('entry_campaign_end'); ?></td>
                        <td class="center" style="width:80px;"><?php echo $this->lang->line('entry_campaign_weight'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($campaigns) && $campaigns) { ?>
                        <?php foreach ($campaigns as $campaign) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if ($client_id){?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $campaign['_id']; ?>" />
                                    <?php }?>
                                </td>
                                <td class="left"><?php echo isset($campaign['game_name']) && !empty($campaign['game_name']) ? $campaign['game_name'] : ""; ?></td>
                                <td class="left"><?php echo isset($campaign['name']) && !empty($campaign['name']) ? $campaign['name'] : ""; ?></td>
                                <td class="right"><?php echo isset($campaign['date_start']) && !empty($campaign['date_start'])  ? dateMongotoReadable($campaign['date_start']) : "N/A"; ?></td>
                                <td class="right"><?php echo isset($campaign['date_end']) && !empty($campaign['date_end'])  ? dateMongotoReadable($campaign['date_end']) : "N/A"; ?></td>
                                <td class="right"><?php echo isset($campaign['weight']) && !empty($campaign['weight']) ? $campaign['weight'] : "0"; ?></td>
                                <td class="center"><input class="checkbox" type="checkbox" name="status" id="status" value="<?php echo $campaign['_id']; ?>" <?php echo isset($campaign['status']) && !empty($campaign['status']) && $campaign['status']? "checked" : "" ?>></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="8"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
                <div class="pagination">
                    <ul class='ul_rule_pagination_container'>
                        <li class="page_index_number active"><a>Total Records:</a></li>
                        <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                        <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?>
                                Pages)</a></li>
                        <?php echo $pagination_links; ?>
                    </ul>
                </div>
                <?php
                echo form_close();?>
            </div><!-- #actions -->
            <?php } else {
                $attributes = array('id' => 'form', 'class' => 'form-horizontal game-form');
                echo form_open_multipart($form, $attributes);
            }?>
            <?php if($game == 'farm' || $game == 'bingo') { ?>
            <?php if($game == 'farm') { ?>
            <div id="tab-farm">
            <?php } if($game == 'bingo') { ?>
            <div id="tab-bingo">
            <?php } ?>
                <table class="form">
                    <?php if($game == 'farm') { ?>
                    <input type="hidden" name="name" size="100" value="farm">
                    <?php } if($game == 'bingo') { ?>
                    <input type="hidden" name="name" size="100" value="bingo">
                    <?php } ?>
                    <tr>
                        <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                        <td> <img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                            <input type="hidden" name="image" value="<?php echo $image; ?>" id="image"/>
                                <br/>
                                <a onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                        <td><input class="checkbox" type="checkbox" name="status" id="status" <?php echo  $status == true ? 'checked' : ''; ?>></td>
                        <td></td>
                    </tr>
                </table>
                <div id="table-world">
                    <div class="world-head-wrapper text-center">
                        <a href="javascript:void(0)" class="btn open-world-btn btn-lg">Open All</a>
                        <a href="javascript:void(0)" class="btn close-world-btn btn-lg">Close All</a>
                        <a href="javascript:void(0)" class="btn btn-primary add-world-btn btn-lg">+ New World</a>
                    </div><br>
                    <div class="world-wrapper">
                        <?php if(isset($worlds)){
                            foreach($worlds as $key => $world){ $index = $key +1;?>
                                <div class="world-item-wrapper" data-world-id="<?php echo $index?>" id="world_<?php echo $index?>_item_wrapper">
                                    <div class="box-header box-world-header overflow-visible" style="height: 30px;">
                                        <h2><img src="<?php echo base_url();?>image/default-image.png" width="30"> <?php echo $world['world_name'] ?></h2>
                                        <div class="box-icon">
                                            <a href="javascript:void(0)" class="btn btn-danger right remove-world-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                            <span class="break"></span>
                                            <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>
                                        </div>
                                    </div>
                                <div class="box-content clearfix">
                                    <div class="row-fluid">
                                        <div class="span12 well" style="min-height:500px">
                                            <div class="span6">
                                                <table class="form">
                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_id]" id="worlds_<?php echo $index ?>_id" size="100" value="<?php echo $world['world_id'] ?>">
                                                <tr>
                                                    <td><?php echo $this->lang->line("entry_world_name"); ?>:</td>
                                                    <td><input type="text" name="worlds[<?php echo $index ?>][world_name]" id="worlds_<?php echo $index ?>_name" size="100" value="<?php echo $world['world_name'] ?>"></td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <td><?php echo $this->lang->line("entry_image"); ?>:</td>
                                                    <td> <img src="<?php echo $world['world_thumb'] ?>" alt="" id="world_<?php echo $index ?>_thumb" onerror="$(this).attr("src","<?php echo base_url(); ?>image/default-image.png");"/>
                                                        <input type="hidden" name="worlds[<?php echo $index ?>][world_image]" value="<?php echo $world['world_image'] ?>" id="world_<?php echo $index ?>_image"/>
                                                        <br/>
                                                        <a onclick="image_upload('#world_<?php echo $index ?>_image', 'world_<?php echo $index ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                                        <a onclick="$('world_<?php echo $index ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('world_<?php echo $index ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $this->lang->line('entry_category'); ?>:</td>
                                                    <td><input type="text" name="worlds[<?php echo $index ?>][world_category]" id="worlds_<?php echo $index ?>_category" size="100" value="<?php echo $world['world_category'] ?>"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $this->lang->line("entry_world_width"); ?>:</td>
                                                    <td><input type="number" name=worlds[<?php echo $index ?>][world_width] id="worlds_<?php echo $index ?>_world_width" value="<?php echo $world['world_width'] ?>" size="100" min="1" value="1" onchange="add_thumbnail(<?php echo $index ?>)"></td>
                                                    <td><input type="hidden" id="worlds_<?php echo $index ?>_world_width_temp" size="100" value="0"></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $this->lang->line("entry_world_height"); ?>:</td>
                                                    <td><input type="number" name=worlds[<?php echo $index ?>][world_height] id="worlds_<?php echo $index ?>_world_height" value="<?php echo $world['world_height'] ?>" size="100" min="1" value="1" onchange="add_thumbnail(<?php echo $index ?>)"></td>
                                                    <td><input type="hidden" id="worlds_<?php echo $index ?>_world_height_temp" size="100" value="0"></td>
                                                </tr>
                                                </table>
                                            </div>
                                            <div class="span6">
                                                <table class="form">
                                                    <tr>
                                                        <td><?php echo $this->lang->line("entry_world_level"); ?>:</td>
                                                        <td><input type="number" name="worlds[<?php echo $index ?>][world_level]" id="worlds_<?php echo $index ?>_level" size="100" min="1" value="<?php echo $world['world_level'] ?>"></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->lang->line("entry_world_description"); ?>:</td>
                                                        <td><textarea name="worlds[<?php echo $index ?>][world_description]" rows="6"></textarea></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->lang->line('entry_world_reset_enable'); ?>:</td>
                                                        <td><input class="world_reset" type="checkbox" id="worlds_<?php echo $index ?>_reset_enable" name="worlds[<?php echo $index ?>][reset_enable]" <?php echo (isset($world['world_reset_enable']) && $world['world_reset_enable']) ? "checked" : ''; ?>></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->lang->line('entry_world_date_reset'); ?>:</td>
                                                        <td>
                                                            <input type="text" class="date" id="worlds_<?php echo $index ?>_reset_date"  placeholder="Default is today"
                                                                   name="worlds[<?php echo $index ?>][reset_date]" <?php echo (isset($world['world_reset_enable']) && $world['world_reset_enable']) ?  '': 'disabled'; ?>
                                                                   value="<?php echo (isset($world['world_reset_date']) && $world['world_reset_date'] && strtotime(datetimeMongotoReadable($world['world_reset_date']))) ? date('Y-m-d', strtotime(datetimeMongotoReadable($world['world_reset_date']))) : "";?>" size="50" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->lang->line('entry_world_duration'); ?>:</td>
                                                        <td>
                                                            <input type="number" id="worlds_<?php echo $index ?>_reset_duration" placeholder="Default is 30 days"
                                                                   name="worlds[<?php echo $index ?>][reset_duration]" id="worlds_<?php echo $index ?>_reset_duration" size="100" <?php echo (isset($world['world_reset_enable']) && $world['world_reset_enable']) ?  '': 'disabled'; ?>
                                                                   value="<?php echo isset($world['world_reset_date']) && $world['world_reset_date'] ?$world['world_reset_duration'] : ""?>">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="span12">Item:</div>
                                            <div class="well" id="worlds_<?php echo $index ?>_thumbnails_grids" style="overflow-y:scroll; overflow-x:scroll; height:500px; width:98%;">
                                            <?php for($row = 0;$row < $world['world_height'];$row++){?>
                                                    <ul id="thumbnails_grid_<?php echo $index?>_<?php echo $row ?>" class="thumbnails">
                                                    <?php for($column = 0;$column < $world['world_width'];$column++){?>
                                                        <li id="thumbnails_grid_<?php echo $index?>_<?php echo $row ?>_<?php echo $column ?>">
                                                            <div class="thumbnail tooltips" data-placement="top" title="[<?php echo $row ?>][<?php echo $column ?>]" >
                                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][item_id]" id="worlds_<?php echo $index ?>_item_id_<?php echo $row?>_<?php echo $column?>" size="100" value="<?php echo isset($world['world_item'][$row][$column]['item_id']) ? $world['world_item'][$row][$column]['item_id'] : ""?>">
                                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][rule_id]" id="worlds_<?php echo $index ?>_rule_id_<?php echo $row?>_<?php echo $column?>" size="100" value="<?php echo isset($world['world_item'][$row][$column]['rule_id']) ? $world['world_item'][$row][$column]['rule_id'] : ""?>">
                                                                <?php if($game == 'farm') { ?>
                                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][item_harvest]" id="worlds_<?php echo $index ?>_item_harvest_<?php echo $row?>_<?php echo $column?>" size="100" value=<?php echo isset($world['world_item'][$row][$column]['item_harvest']) ? $world['world_item'][$row][$column]['item_harvest'] : ""?>>
                                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][item_deduct]" id="worlds_<?php echo $index ?>_item_deduct_<?php echo $row?>_<?php echo $column?>" size="100" value=<?php echo isset($world['world_item'][$row][$column]['item_deduct']) ? $world['world_item'][$row][$column]['item_deduct'] : ""?>>
                                                                <?php } ?>
                                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][item_description]" id="worlds_<?php echo $index ?>_item_description_<?php echo $row?>_<?php echo $column?>" size="100" value="<?php echo isset($world['world_item'][$row][$column]['item_description']) ? $world['world_item'][$row][$column]['item_description'] : ""?>">
                                                                <?php if(isset($world['world_item'][$row][$column]['item_id'])) { ?>
                                                                    <img style="padding: 10px" onclick = "showItemModalForm(<?php echo $index ?>,<?php echo $row ?>,<?php echo $column ?>)" src="<?php echo $world['world_item'][$row][$column]['item_thumb'] ?>" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][item_thumb]" alt="" id="worlds_<?php echo $index ?>_item_thumb_<?php echo $row?>_<?php echo $column?>" onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                                                                    <i class="fa fa-plus-circle fa-5x fa-align-center hide" id = "worlds_<?php echo $index ?>_item_add_<?php echo $row?>_<?php echo $column?>" onclick = "showItemModalForm(<?php echo $index ?>,<?php echo $row ?>,<?php echo $column ?>)" class="tooltips" data - placement = "top" title = "[<?php echo $row ?>][<?php echo $column ?>]" style = "padding: 30px" aria - hidden = "true" ></i >
                                                                <?php } else { ?>
                                                                    <i class="fa fa-plus-circle fa-5x fa-align-center" id = "worlds_<?php echo $index ?>_item_add_<?php echo $row?>_<?php echo $column?>" onclick = "showItemModalForm(<?php echo $index ?>,<?php echo $row ?>,<?php echo $column ?>)" class="tooltips" data - placement = "top" title = "[<?php echo $row ?>][<?php echo $column ?>]" style = "padding: 30px" aria - hidden = "true" ></i >
                                                                    <img class="hide" style="padding: 10px" onclick = "showItemModalForm(<?php echo $index ?>,<?php echo $row ?>,<?php echo $column ?>)" src="" name="worlds[<?php echo $index ?>][world_item][<?php echo $row?>][<?php echo $column ?>][item_thumb]" alt="" id="worlds_<?php echo $index ?>_item_thumb_<?php echo $row?>_<?php echo $column?>" onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                                                                <?php } ?>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                    </ul>
                                            <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                      <?php }
                        } ?>
                    </div><!-- .world-wrapper -->
                </div>
            <?php echo form_close();?>
            </div><!-- #actions -->
            <?php }
            if($game == 'egg' || $game == 'pairs' || $game == 'catch_item'){
            ?>
            <?php if($game == 'egg') { ?>
            <div id="tab-egg">
            <?php } if($game == 'pairs') { ?>
            <div id="tab-pairs">
            <?php } if($game == 'catch_item') { ?>
            <div id="tab-catch_item">
            <?php }  ?>
                <table class="form">
                    <?php if($game == 'egg') { ?>
                        <input type="hidden" name="name" size="100" value="egg">
                    <?php } if($game == 'pairs') { ?>
                        <input type="hidden" name="name" size="100" value="pairs">
                    <?php } if($game == 'catch_item') { ?>
                        <input type="hidden" name="name" size="100" value="catch_item">
                    <?php }  ?>

                    
                    <tr>
                        <td><?php echo $this->lang->line('entry_image'); ?>:</td>
                        <td> <img src="<?php echo $thumb; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                            <input type="hidden" name="image" value="<?php echo $image; ?>" id="image"/>
                            <br/>
                            <a onclick="image_upload('#image', 'thumb');"><?php echo $this->lang->line('text_browse'); ?></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a onclick="$('#thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_status'); ?>:</td>
                        <td><input class="checkbox" type="checkbox" name="status" id="status" <?php echo  $status == true ? 'checked' : ''; ?>></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line("entry_duration"); ?>:</td>
                        <td><input type="number" name="duration" id="duration" size="100" min="1" value="<?php echo $duration ? $duration : 1; ?>"></td>
                    </tr>
                    <?php if($game == 'egg'){ ?>
                    <tr>
                        <td><?php echo $this->lang->line("entry_action_time"); ?>:</td>
                        <td><input type="number" name="action_time" id="action_time" size="100" min="1" value="<?php echo $action_time ? $action_time : 1; ?>"></td>
                    </tr>
                    <?php } ?>
                </table>
                <div id="table-world">
                    <div class="world-head-wrapper text-center">
                        <a href="javascript:void(0)" class="btn open-world-btn btn-lg">Open All</a>
                        <a href="javascript:void(0)" class="btn close-world-btn btn-lg">Close All</a>
                        <a href="javascript:void(0)" class="btn btn-primary add-world-btn btn-lg">+ New World</a>
                    </div><br>
                    <div class="world-wrapper">
                        <?php if(isset($worlds)){
                            foreach($worlds as $key => $world){ $index = $key +1;?>
                                <div class="world-item-wrapper" data-world-id="<?php echo $index?>" id="world_<?php echo $index?>_item_wrapper">
                                    <div class="box-header box-world-header overflow-visible" style="height: 30px;">
                                        <h2><img src="<?php echo base_url();?>image/default-image.png" width="30"> <?php echo $world['world_name'] ?></h2>
                                        <div class="box-icon">
                                            <a href="javascript:void(0)" class="btn btn-danger right remove-world-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                            <span class="break"></span>
                                            <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>
                                        </div>
                                    </div>
                                    <div class="box-content clearfix">
                                        <div class="row-fluid">
                                            <div class="span12 well" style="min-height:500px">
                                                <div class="span6">
                                                    <table class="form">
                                                        <input type="hidden" name="worlds[<?php echo $index ?>][world_id]" id="worlds_<?php echo $index ?>_id" size="100" value="<?php echo $world['world_id'] ?>">
                                                        <tr>
                                                            <td><?php echo $this->lang->line("entry_world_name"); ?>:</td>
                                                            <td><input type="text" name="worlds[<?php echo $index ?>][world_name]" id="worlds_<?php echo $index ?>_name" size="100" value="<?php echo $world['world_name'] ?>"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $this->lang->line("entry_world_level"); ?>:</td>
                                                            <td><input type="number" name="worlds[<?php echo $index ?>][world_level]" id="worlds_<?php echo $index ?>_level" size="100" min="1" value="<?php echo $world['world_level'] ?>"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $this->lang->line("entry_image"); ?>:</td>
                                                            <td> <img src="<?php echo $world['world_thumb'] ?>" alt="" id="world_<?php echo $index ?>_thumb" onerror="$(this).attr("src","<?php echo base_url(); ?>image/default-image.png");"/>
                                                                <input type="hidden" name="worlds[<?php echo $index ?>][world_image]" value="<?php echo $world['world_image'] ?>" id="world_<?php echo $index ?>_image"/>
                                                                <br/>
                                                                <a onclick="image_upload('#world_<?php echo $index ?>_image', 'world_<?php echo $index ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                                                <a onclick="$('world_<?php echo $index ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('world_<?php echo $index ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="span6">
                                                    <table class="form">
                                                        <tr>
                                                            <td><?php echo $this->lang->line("entry_range_low"); ?>:</td>
                                                            <td><input type="number" name="worlds[<?php echo $index ?>][world_low]" id="worlds_<?php echo $index ?>_low" size="100" min="1" value="<?php echo $world['world_low'] ?>"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $this->lang->line("entry_range_high"); ?>:</td>
                                                            <td><input type="number" name="worlds[<?php echo $index ?>][world_high]" id="worlds_<?php echo $index ?>_high" size="100" min="1" value="<?php echo $world['world_high'] ?>"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $this->lang->line("entry_world_description"); ?>:</td>
                                                            <td><textarea name="worlds[<?php echo $index ?>][world_description]" rows="6"></textarea></td>
                                                            <td></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div><!-- .world-wrapper -->
                <?php echo form_close();?>
            </div>
            <?php } ?>
        </div>
    </div>
    </div>
</div>
<div class="modal hide" id="savedDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h1>Data Saved</h1>
    </div>
    <div class="modal-body">
        <div>
            <i class="fa fa-save"></i>&nbsp;<span>Data has been saved!</span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1>Please Wait</h1>
    </div>
    <div class="modal-body">
        <div class="offset5 ">
            <i class="fa fa-spinner fa-spin fa-5x"></i>
        </div>
    </div>
</div>

<div id="pleaseWaitSpanDiv" class="hide">
    <span id="pleaseWaitSpan"><i class="fa fa-spinner fa-spin"></i></span>
</div>

<!-- Error Modal -->
<div id="errorModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:100000">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Warning !</h3>
    </div>
    <div class="modal-body red">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:100000">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Warning !</h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<div class="modal hide fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <h3 id="myModalLabel">Warning !</h3>
        <input type="hidden" name="confirm_event" id="confirm_event" value="">
        <input type="hidden" name="confirm_world" id="confirm_world" value="">
        <input type="hidden" name="confirm_param" id="confirm_param" value="">
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" id="confirm_cancel_button" data-dismiss="modal">Cancel</button>
        <a class="btn btn-primary btn-ok" id="confirm_del_button">Confirm</a>
    </div>
</div>

<div id="formItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formItemModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formItemModalLabel">Item</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal item-form')); ?>
            <div class="row-fluid">
                <input type="hidden" name="item_world_id" id="item_world_id">
                <input type="hidden" name="item_row" id="item_row">
                <input type="hidden" name="item_column" id="item_column">
                <div class="control-group">
                    <label for="item_id" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_item_name'); ?></label>
                    <div class="controls">
                        <input type="text" name="item_id" id="item_id" placeholder="<?php echo $this->lang->line('entry_item_name'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="rule_id" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_rule_name'); ?></label>
                    <div class="controls">
                        <input type="text" name="rule_id" id="rule_id" placeholder="<?php echo $this->lang->line('entry_rule_name'); ?>">
                    </div>
                </div>
                <?php if($game == 'farm'){ ?>
                <div class="control-group">
                    <label for="item_harvest" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_item_harvest'); ?></label>
                    <div class="controls">
                        <input type="number" name="item_harvest" id="item_harvest" min="1" placeholder="<?php echo $this->lang->line('entry_item_harvest'); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="item_deduct" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_item_deduct'); ?></label>
                    <div class="controls">
                        <input type="number" name="item_deduct" id="item_deduct" min="1" placeholder="<?php echo $this->lang->line('entry_item_deduct'); ?>">
                    </div>
                </div>
                <?php } ?>
                <div class="control-group">
                    <label for="item-desc" class="control-label"><?php echo $this->lang->line('entry_item_description'); ?></label>
                    <div class="controls">
                        <textarea name="item-desc" id="item-desc" rows="5" placeholder="<?php echo $this->lang->line('entry_item_description') ?>"></textarea>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="item-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
    </div>
</div>

<div id="formCampaignModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="formCampaignModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="formCampaignModalLabel">Item</h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <?php echo form_open(null, array('class' => 'form-horizontal Campaign-form')); ?>
            <div class="row-fluid">
                <div class="control-group">
                    <label for="game_id" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_game_name'); ?></label>
                    <div class="controls">
                        <input type="text" name="game_id" id="game_id" placeholder="<?php echo $this->lang->line('entry_game_name'); ?>">
                    </div><br>
                    <label for="campaign_id" class="control-label"><span class="required">*</span><?php echo $this->lang->line('entry_campaign'); ?></label>
                    <div class="controls">
                        <input type="text" name="campaign_id" id="campaign_id" placeholder="<?php echo $this->lang->line('entry_campaign'); ?>">
                    </div><br>
                    <label for="status" class="control-label"><?php echo $this->lang->line('entry_status'); ?></label>
                    <div class="controls">
                        <input class="checkbox" type="checkbox" name="status" id="status" checked>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="campaign-modal-submit"><i class="fa fa-plus">&nbsp;</i>Save</button>
    </div>
</div>

<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-switch.min.js" type="text/javascript" ></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>stylesheet/select2/select2-bootstrap.css" rel="stylesheet" type="text/css">

<script>
    $(function(){
        $('.checkbox').bootstrapSwitch();
        $('.checkbox').bootstrapSwitch('size', 'small');
        $('.checkbox').bootstrapSwitch('onColor', 'success');
        $('.checkbox').bootstrapSwitch('offColor', 'danger');
        $('.checkbox').bootstrapSwitch('handleWidth', '70');
        $('.checkbox').bootstrapSwitch('labelWidth', '10');
        $('.checkbox').bootstrapSwitch('onText', 'Enable');
        $('.checkbox').bootstrapSwitch('offText', 'Disable');
        $('#tab_<?php echo $game?>').addClass('selected')
    });

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });

    function image_upload(field, thumb) {
        var $mm_Modal = $('#mmModal');

        if ($mm_Modal.length !== 0) $mm_Modal.remove();

        var frameSrc = baseUrlPath + "mediamanager/dialog?field=" + encodeURIComponent(field);
        var mm_modal_str = "";
        mm_modal_str += "<div id=\"mmModal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\">";
        mm_modal_str += " <div class=\"modal-body\">";
        mm_modal_str += "   <iframe src=\"" + frameSrc + "\" style=\"position:absolute; zoom:0.60\" width=\"99.6%\" height=\"99.6%\" frameborder=\"0\"><\/iframe>";
        mm_modal_str += " <\/div>";
        mm_modal_str += "<\/div>";

        $mm_Modal = $(mm_modal_str);
        $('#page-render').append($mm_Modal);

        $mm_Modal.modal('show');

        $mm_Modal.on('hidden', function () {
            var $field = $(field);
            if ($field.attr('value')) {
                $.ajax({
                    url: baseUrlPath + 'mediamanager/image?image=' + encodeURIComponent($field.val()),
                    dataType: 'text',
                    success: function (data) {
                        $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                    }
                });
            }
        });
    }
</script>
<?php if($game == 'campaign') { ?>
<script>
    $('.checkbox').on('switchChange.bootstrapSwitch', function(event, state) {
        var formData = "_id="+$(this).val()+"&status="+$(this)[0].checked;
        $.ajax({
            type: "POST",
            url: baseUrlPath + "game/status",
            data: formData,
            timeout: 3000,
            beforeSend: function (xhr) {
                $waitDialog.modal('show');
            }
        }).done(function (data) {
            $waitDialog.modal('hide');
        }).fail(function (xhr, textStatus, errorThrown) {
            if(JSON.parse(xhr.responseText).status == "error") {
                $('form.Campaign-form').trigger("reset");
                alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
            }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                $waitDialog.modal('hide');
            }
        }).always(function () {
            $waitDialog.modal('hide');
        });
    });
    var $waitDialog = $('#pleaseWaitDialog');
    function showCampaignModalForm() {
        $('#formCampaignModal').modal('show');
        $game = $('#game_id');
        $game.select2({
            width: '220px',
            allowClear: true,
            placeholder: "Select Item",
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "game/game_ajax",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        search: term, // search term
                    };
                },
                results: function (data, page) {
                    return {results: data.rows};
                },
                cache: true
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== ""){
                    $.ajax(baseUrlPath + "game/game_ajax/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $game.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                    }).always(function () {
                        $game.parent().parent().parent().find("#pleaseWaitSpan").remove();
                    });
                }
            },
            formatResult: gameFormatResult,
            formatSelection: gameFormatSelection,
        });
        $campaign = $('#campaign_id');
        $campaign.select2({
            width: '220px',
            allowClear: true,
            placeholder: "Select Rule",
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "game/campaign",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        search: term, // search term
                    };
                },
                results: function (data, page) {
                    return {results: data.rows};
                },
                cache: true
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== ""){
                    $.ajax(baseUrlPath + "game/campaign/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $campaign.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                    }).always(function () {
                        $campaign.parent().parent().parent().find("#pleaseWaitSpan").remove();
                    });
                }
            },
            formatResult: categoryFormatResult,
            formatSelection: categoryFormatSelection,
        });
    }

    function categoryFormatResult(category) {
        return '<div class="row-fluid">' +
            '<div>' + category.name + '</div>'
        '</div>';
    }

    function categoryFormatSelection(category) {
        return category.name;
    }

    function gameFormatResult(category) {
        return '<div class="row-fluid">' +
                '<div>' + category.game_name + '</div>'
               '</div>';
    }

    function gameFormatSelection(category) {
        return '<div class="row-fluid">' +
            '<div>' + category.game_name + '</div>'
            '</div>';
    }

    $('button#campaign-modal-submit').on('click',submitCampaignModalForm);

    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined) {
                $('#errorModal').find('#myModalLabel').html(title);
            }else{
                $('#errorModal').find('#myModalLabel').html("Warning !");
            }
            $('#errorModal').removeClass('hide');
            $('#errorModal').removeClass('in');
            $('#errorModal').modal();
            $('#errorModal .modal-body').html(msg);
        }
    }

    function submitCampaignModalForm() {
        var game_id = $('#game_id').val(),
            campaign_id = $('#campaign_id').val(),
        dialogMsg = "";

        if(game_id == "") dialogMsg += '- Please select Game <br>';
        if(campaign_id == "") dialogMsg += '- Please select Campaign <br>';
        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
            var formData = $('form.Campaign-form').serialize();
            $.ajax({
                type: "POST",
                url: baseUrlPath + "game/insert",
                data: formData,
                timeout: 3000,
                beforeSend: function (xhr) {
                    $('#formCampaignModal').modal('hide');
                    $waitDialog.modal('show');
                }
            }).done(function (data) {
                $waitDialog.modal('hide');
                window.location.reload();
            }).fail(function (xhr, textStatus, errorThrown) {
                if(JSON.parse(xhr.responseText).status == "error") {
                    $('form.Campaign-form').trigger("reset");
                    alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
                }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                    $waitDialog.modal('hide');
                }
            }).always(function () {
                $waitDialog.modal('hide');
            });
        }
    }
</script>
<?php }
if($game == 'farm' || $game == 'bingo'){
?>
<script>
    $(function(){
        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
        $('.world_reset').on( "change", function() {
            var world = $(this).attr('id');
            var data = world.split('_');
            var id = data[1];
            var reset_enable = $('#'+world+'');
            var reset_date = $('#worlds_'+id+'_reset_date');
            var reset_duration = $('#worlds_'+id+'_reset_duration');
            if (reset_enable.attr('checked')) {
                reset_date.attr('disabled',false);
                reset_duration.attr('disabled',false);
            } else {
                reset_date.attr('disabled',true);
                reset_duration.attr('disabled',true);
            }
        });
    });

    var countWorldId = 0,
        $waitDialog = $('#pleaseWaitDialog'),
        $savedDialog = $('#savedDialog'),
        temp_category,
        $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html();

    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined) {
                $('#errorModal').find('#myModalLabel').html(title);
            }else{
                $('#errorModal').find('#myModalLabel').html("Warning !");
            }
            $('#errorModal').removeClass('hide');
            $('#errorModal').removeClass('in');
            $('#errorModal').modal();
            $('#errorModal .modal-body').html(msg);
        }
    }

    function game_validation(){
        var dialogMsg = "",
            check_level = false;

        for (var i=1; i<= countWorldId; i++) {
            var $world_name = $('#worlds_'+ i +'_name').val(),
                $world_level = $('#worlds_'+ i +'_level').val(),
                $world_width = $('#worlds_'+ i +'_world_width').val(),
                $world_height = $('#worlds_'+ i +'_world_height').val();

            if ($world_name == "") dialogMsg += '- Name is require, Please select name of world '+ i +'<br>';
            for (var j=i+1; j<= countWorldId; j++){
                var $world_name_temp = $('#worlds_'+ j +'_name').val(),
                    $world_level_temp = $('#worlds_'+ j +'_level').val();
                if (($world_name == $world_name_temp) && ($world_name != null)) dialogMsg += '- Name is required unique value, Name of world '+ i + ' is same as world ' + j + '<br>';
                if (($world_level == $world_level_temp) && ($world_name != null)) dialogMsg += '- Level is required unique value, Level of world '+ i + ' is same as world ' + j + '<br>';
            }
            if ($world_level == 1) check_level = true;
            if ($world_level < 0) dialogMsg += '- Level is require at least 1<br>';
            if ($world_width < 0) dialogMsg += '- Width is require at least 1<br>';
            if ($world_height < 0) dialogMsg += '- Height is require at least 1<br>';
        }

        if(!check_level) dialogMsg += '- Level 1 is require<br>';
        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
            var formData = $('form.game-form').serialize();
            $.ajax({
                type: "POST",
                url: baseUrlPath + "game/edit/",
                data: formData,
                beforeSend: function (xhr) {
                    $waitDialog.modal();
                }
            }).done(function (data) {
                location.reload();
            }).fail(function (xhr, textStatus, errorThrown) {
                if(JSON.parse(xhr.responseText).status == "error") {
                    $('form.game-form').trigger("reset");
                    alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
                }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                    $waitDialog.modal('hide');

                }
            }).always(function () {
                $waitDialog.modal('hide');
            });
        }
    }

    //======================== World ========================
    $('.world-item-wrapper').each(function () {
        countWorldId++;
    })

    for(var iWorld=1;iWorld <= countWorldId;iWorld++){
        init_world_event(iWorld);
    }
    $('.open-world-btn').click(function () {
        $('.world-item-wrapper>.box-content').show();
    })
    $('.close-world-btn').click(function () {
        $('.world-item-wrapper>.box-content').hide();
    })

    $('.add-world-btn').click(function () {
        countWorldId++;
        var itemWorldId = countWorldId;
        var itemWorldHtml = '<div class="world-item-wrapper" data-world-id="'+ itemWorldId +'" id="world_'+ itemWorldId +'_item_wrapper">\
                                <div class="box-header box-world-header overflow-visible" style="height: 30px;">\
                                    <h2><img src="<?php echo base_url();?>image/default-image.png" width="30"> World</h2>\
                                    <div class="box-icon">\
                                        <a href="javascript:void(0)" class="btn btn-danger right remove-world-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
                                        <span class="break"></span>\
                                        <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                                    </div>\
                                </div>\
                                <div class="box-content clearfix">\
                                    <div class="row-fluid">\
                                        <div class="span12 well" style="min-height:500px">\
                                            <div class="span6">\
                                            <table class="form">\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_name"); ?>:</td>\
                                                    <td><input type="text" name="worlds['+itemWorldId+'][world_name]" id="worlds_'+itemWorldId+'_name" size="100" value=""></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_image"); ?>:</td>\
                                                    <td> <img src="<?php echo S3_IMAGE . "cache/no_image-100x100.jpg" ?>" alt="" id="world_'+itemWorldId+'_thumb" onerror="$(this).attr(\'src\', \'<?php echo base_url(); ?>image/default-image.png\' )"/>\
                                                        <input type="hidden" name="worlds['+itemWorldId+'][world_image]" value="no_image.jpg" id="world_'+itemWorldId+'_image"/>\
                                                        <br/>\
                                                        <a onclick="image_upload(\'#world_'+itemWorldId+'_image\', \'world_'+itemWorldId+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
                                                        <a onclick="$(\'world_'+itemWorldId+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'world_'+itemWorldId+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a>\
                                                    </td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line('entry_category'); ?>:</td>\
                                                    <td><input type="text" name="worlds['+itemWorldId+'][world_category]" id="worlds_'+itemWorldId+'_category" size="100" value=""></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_width"); ?>:</td>\
                                                    <td><input type="number" name=worlds['+itemWorldId+'][world_width] id="worlds_'+itemWorldId+'_world_width" size="100" min="1" value="1" onchange="add_thumbnail('+itemWorldId+')"></td>\
                                                    <td><input type="hidden" id="worlds_'+itemWorldId+'_world_width_temp" size="100" value="0"></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_height"); ?>:</td>\
                                                    <td><input type="number" name=worlds['+itemWorldId+'][world_height] id="worlds_'+itemWorldId+'_world_height" size="100" min="1" value="1" onchange="add_thumbnail('+itemWorldId+')"></td>\
                                                    <td><input type="hidden" id="worlds_'+itemWorldId+'_world_height_temp" size="100" value="0"></td>\
                                                </tr>\
                                            </table>\
                                            </div>\
                                            <div class="span6">\
                                            <table class="form">\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_level"); ?>:</td>\
                                                    <td><input type="number" name="worlds['+itemWorldId+'][world_level]" id="worlds_'+itemWorldId+'_level" size="100" min="1" value="1"></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_description"); ?>:</td>\
                                                    <td><textarea name="worlds['+itemWorldId+'][world_description]" rows="6"></textarea></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line('entry_world_reset_enable'); ?>:</td>\
                                                    <td><input class="world_reset" type="checkbox" id="worlds_'+itemWorldId+'_reset_enable" name="worlds['+itemWorldId+'][reset_enable]"></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line('entry_world_date_reset'); ?>:</td>\
                                                    <td>\
                                                        <input type="text" class="date" id="worlds_'+itemWorldId+'_reset_date" name="worlds['+itemWorldId+'][reset_date]" disabled value="" size="50" />\
                                                    </td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line('entry_world_duration'); ?>:</td>\
                                                    <td>\
                                                        <input type="number" id="worlds_'+itemWorldId+'_reset_duration" name="worlds['+itemWorldId+'][reset_duration]" id="worlds_'+itemWorldId+'_reset_duration" size="100" disabled value="">\
                                                    </td>\
                                                </tr>\
                                            </table>\
                                            </div>\
                                            <div class="span12">Item:</div>\
                                            <div class="well" id="worlds_'+itemWorldId+'_thumbnails_grids" style="overflow-y:scroll; overflow-x:scroll; height:500px; width:98%;"></div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>';

        $('.world-item-wrapper>.box-content').slideUp();
        $('.world-wrapper').append(itemWorldHtml);
        init_world_event(itemWorldId);
        add_thumbnail(itemWorldId);
    });

    function init_world_event(id) {
        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
        $('.world-item-wrapper .box-world-header').unbind().bind('click', function (data) {
            var $target = $(this).next('.box-content');

            if ($target.is(':visible')) $('i', $(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i', $(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.world_reset').on( "change", function() {
            var reset_enable = $('#worlds_'+id+'_reset_enable');
            var reset_date = $('#worlds_'+id+'_reset_date');
            var reset_duration = $('#worlds_'+id+'_reset_duration');
            if (reset_enable.attr('checked')) {
                reset_date.attr('disabled',false);
                reset_duration.attr('disabled',false);
            } else {
                reset_date.attr('disabled',true);
                reset_duration.attr('disabled',true);
            }
        });

        $('.remove-world-btn').unbind().bind('click', function (data) {
            $('#confirm-delete .modal-body').html('Are you sure to remove!!');
            document.getElementById('confirm_event').value = "world";
            document.getElementById('confirm_world').value = $(this).parent().parent().parent().data('world-id');
            $('#confirm-delete').modal('show');
        });
        get_item_category(id);
    }

    function get_item_category(world){
        $inputCategory = $('#worlds_'+world+'_category');
        $inputCategory.select2({
            width: '220px',
            allowClear: true,
            placeholder: "Select category",
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "badge/category",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        search: term, // search term
                    };
                },
                results: function (data, page) {
                    for(var i=1; i <= countWorldId; i++){
                        for(var j=0; j< data.total; j++){
                            if(data.rows[j] != undefined){
                                if($('#worlds_'+i+'_category').val() == data.rows[j]._id){
                                    data.rows.splice(j, 1);
                                    data.total--;
                                }
                            }
                        }
                    }
                    return {results: data.rows};
                },
                cache: true
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== ""){
                    $.ajax(baseUrlPath + "badge/category/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $inputCategory.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                    }).always(function () {
                        $inputCategory.parent().parent().parent().find("#pleaseWaitSpan").remove();
                    });
                }
            },
            formatResult: categoryFormatResult,
            formatSelection: categoryFormatSelection,
        }).on('select2-clearing', function (e) {
            temp_category = $('#worlds_'+world+'_category').val();
            $('#confirm-delete .modal-body').html('Select new catagory will be clear all selected items');
            document.getElementById('confirm_event').value = "category";
            document.getElementById('confirm_world').value = world;
            $('#confirm-delete').modal('show');
        }).on('select2-selecting', function (e) {
            temp_category = $('#worlds_'+world+'_category').val();
        }).on('select2-selected', function (e) {
            var category = $('#worlds_'+world+'_category').val();
            if (category != temp_category && temp_category != ""){
                $('#confirm-delete .modal-body').html('Select new catagory will be clear all selected items');
                document.getElementById('confirm_event').value = "category";
                document.getElementById('confirm_world').value = world;
                $('#confirm-delete').modal('show');
            }
        });
    }

    $('button#confirm_cancel_button').on('click',cancelConfirm);

    function cancelConfirm(){
        var event = $('#confirm_event').val();
        var world = $('#confirm_world').val();
        var param = $('#confirm_parameter').val();

        if(event == 'category'){
            document.getElementById('worlds_'+world+'_category').value = temp_category;
            get_item_category(world);
            $('#confirm-delete').modal('hide');
        }
    }

    function categoryFormatResult(category) {
        return '<div class="row-fluid">' +
                    '<div>' + category.name + '</div>'
               '</div>';
    }

    function categoryFormatSelection(category) {
        return category.name;
    }

</script>
<?php }
if ($game == 'farm'){
    //////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //            ///////////     //////     ///////////    /////////////           //
    //            //             //    //    //       //   //    //    //           //
    //            //            //      //   //       //   //    //    //           //
    //            /////////     //////////   //////////    //    //    //           //
    //            //            //      //   //   //       //    //    //           //
    //            //            //      //   //    //      //    //    //           //
    //            //            //      //   //     ///    //    //    //           //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////
?>
<script>
    var list_badge;
    $('button#item-modal-submit').on('click',submitItemModalForm);
    $('a#confirm_del_button').on('click',deleteConfirm);
    function add_thumbnail(world_id){
        var $world_widths = $('#worlds_'+ world_id +'_world_width'),
            $world_widths_temp = $('#worlds_'+ world_id +'_world_width_temp'),
            $world_heights = $('#worlds_'+ world_id +'_world_height'),
            $world_heights_temp = $('#worlds_'+ world_id +'_world_height_temp'),
            $thumbnails_grids = $('#worlds_'+ world_id +'_thumbnails_grids');
        $waitDialog.modal('show');
        if($world_heights.val() > $world_heights_temp.val() || $world_widths.val() > $world_widths_temp.val()){
            for(var i =0;i<$world_heights.val();i++){
                var myElemi = document.getElementById('thumbnails_grid_'+ world_id +'_'+ i);
                if (myElemi === null) $thumbnails_grids.append('<ul id="thumbnails_grid_'+ world_id +'_'+ i +'" class="thumbnails">');
                for(var j=0;j<$world_widths.val();j++){
                    var myElemj = document.getElementById('thumbnails_grid_'+ world_id +'_' + i + '_' + j);
                    if (myElemj === null) $('#thumbnails_grid_'+ world_id +'_'+ i).append('<li id="thumbnails_grid_'+ world_id +'_' + i + '_' + j + '">\
                                                    <div class="thumbnail tooltips" data-placement="top" title="['+i+']['+j+']" style="width:120px;height:120px;">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_id] id="worlds_'+world_id+'_item_id_' + i + '_' + j + '" size="100" value="">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][rule_id] id="worlds_'+world_id+'_rule_id_' + i + '_' + j + '" size="100" value="">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_harvest] id="worlds_'+world_id+'_item_harvest_' + i + '_' + j + '" size="100" value="1">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_deduct] id="worlds_'+world_id+'_item_deduct_' + i + '_' + j + '" size="100" value="1">\
                                                        <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_description] id="worlds_'+world_id+'_item_description_' + i + '_' + j + '" size="100" value="">\
                                                        <i class="fa fa-plus-circle fa-5x fa-align-center" onclick="showItemModalForm('+world_id+','+i+','+j+')" class="tooltips" data-placement="top" title="['+i+']['+j+']" id = "worlds_'+world_id+'_item_add_'+i+'_'+j+'" style="padding: 30px" aria-hidden="true"></i>\
                                                        <img class="hide" style="padding: 10px" onclick = "showItemModalForm('+world_id+','+i+','+j+')" src="" name="worlds['+world_id+'][world_item]['+i+']['+j+'][item_thumb]" alt="" id="worlds_'+world_id+'_item_thumb_'+i+'_'+j+'" onerror="$(this).attr(\'src\', \'<?php echo base_url(); ?>image/default-image.png\' )"/>\
                                                    </div>\
                                                    <div id="world_'+ world_id +'_item_image_' + i + '_' + j + '"></div>\
                                                 </li>');
                }
                if (myElemi === null) $thumbnails_grids.append('</ul>');
            }
            if($world_heights.val() > $world_heights_temp.val()) document.getElementById('worlds_'+world_id+'_world_height_temp').value = $world_heights.val();
            if($world_widths.val() > $world_widths_temp.val()) document.getElementById('worlds_'+world_id+'_world_width_temp').value = $world_widths.val();
        } else {
            for(var i =0; i<$world_heights_temp.val(); i++){
                for(var j=0; j<$world_widths_temp.val(); j++){
                    if(i > ($world_heights.val()-1) || j > ($world_widths.val()-1)){
                        $('#thumbnails_grid_'+ world_id +'_' + i +'_'+ j).remove();
                    }
                }
            }
            if($world_heights.val() < $world_heights_temp.val()) document.getElementById('worlds_'+world_id+'_world_height_temp').value = $world_heights.val();
            if($world_widths.val() < $world_widths_temp.val()) document.getElementById('worlds_'+world_id+'_world_width_temp').value = $world_widths.val();
        }
        $waitDialog.modal('hide');
    }

    function deleteConfirm(){
        var event = $('#confirm_event').val();
        var world = $('#confirm_world').val();
        var $world_widths = $('#worlds_'+ world +'_world_width'),
            $world_heights = $('#worlds_'+ world +'_world_height');

        if(event == "category"){
            for(var i =0;i<$world_heights.val();i++){
                for(var j=0;j<$world_widths.val();j++){
                    document.getElementById('worlds_'+ world +'_item_id_' + i + '_' + j).value =  "";
                    document.getElementById('worlds_'+ world +'_rule_id_' + i + '_' + j).value =  "";
                    document.getElementById('worlds_'+ world +'_item_harvest_' + i + '_' + j).value =  1;
                    document.getElementById('worlds_'+ world +'_item_deduct_' + i + '_' + j).value =  1;
                    document.getElementById('worlds_'+ world +'_item_description_' + i + '_' + j).value =  "";
                    $('#worlds_'+ world +'_item_thumb_' + i + '_' + j).addClass('hide');
                    $('#worlds_'+ world +'_item_add_' + i + '_' + j).removeClass('hide');
                }
            }
            $('#confirm-delete').modal('hide');
        }
        if(event == "world"){
            $('#world_'+world+'_item_wrapper').remove();
            init_world_event(world);
            $('#confirm-delete').modal('hide');
        }
    }

    function submitItemModalForm() {
        var world_id   = $('#item_world_id').val(),
            row    = $('#item_row').val(),
            column    = $('#item_column').val(),
            item_id = $('#item_id').val(),
            rule_id = $('#rule_id').val(),
            item_harvest = $('#item_harvest').val(),
            item_deduct  = $('#item_deduct').val(),
            item_desc = $('#item-desc').val();
        dialogMsg = "";

        if(item_id == "") dialogMsg += '- Please select Item <br>';
        if(rule_id == "") dialogMsg += '- Please select Rule <br>';
        if(item_harvest < 1) dialogMsg += '- Days to harvest is require at least 1 day <br>';
        if(item_deduct < 1) dialogMsg += '- Days to die is require at least 1 day <br>';
        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
            $('#formItemModal').modal('hide');

            document.getElementById('worlds_'+ world_id +'_item_id_' + row + '_' + column).value =  item_id;
            document.getElementById('worlds_'+ world_id +'_rule_id_' + row + '_' + column).value =  rule_id;
            document.getElementById('worlds_'+ world_id +'_item_harvest_' + row + '_' + column).value =  item_harvest;
            document.getElementById('worlds_'+ world_id +'_item_deduct_' + row + '_' + column).value =  item_deduct;
            document.getElementById('worlds_'+ world_id +'_item_description_' + row + '_' + column).value = item_desc;
            if(list_badge != undefined) for(var i = 0; i<  list_badge.length; i++){
                var path = list_badge[i]['image'].split(".");
                if (item_id == list_badge[i]['_id']){
                    document.getElementById('worlds_'+ world_id +'_item_thumb_' + row + '_' + column).src = "https://images.pbapp.net/cache/"+path[0]+"-100x100."+path[1];
                }
            }

            $('#worlds_'+ world_id +'_item_thumb_' + row + '_' + column).removeClass('hide');
            $('#worlds_'+ world_id +'_item_add_' + row + '_' + column).addClass('hide');
            document.getElementById('item_id').value = "";
            document.getElementById('rule_id').value = "";
            document.getElementById('item_harvest').value = "";
            document.getElementById('item_deduct').value = "";
            document.getElementById('item-desc').value = "";
        }
    }

    function showItemModalForm(world_id,row,column) {
        var $world_category = $('#worlds_'+world_id+'_category');
        var $world_width = $('#worlds_'+ world_id +'_world_width');
        var $world_height = $('#worlds_'+ world_id +'_world_height');
        document.getElementById('worlds_'+world_id+'_world_width').disabled = false;
        document.getElementById('worlds_'+world_id+'_world_height').disabled = false;
        document.getElementById('item_world_id').value = world_id;
        document.getElementById('item_row').value = row;
        document.getElementById('item_column').value = column;
        document.getElementById('item_id').value = ($('#worlds_'+ world_id +'_item_id_'+ row +'_' + column).val() != "") ? $('#worlds_'+ world_id +'_item_id_'+ row +'_' + column).val() : "";
        document.getElementById('rule_id').value = ($('#worlds_'+ world_id +'_rule_id_'+ row +'_' + column).val() != "") ? $('#worlds_'+ world_id +'_rule_id_'+ row +'_' + column).val() : "";
        document.getElementById('item_harvest').value = ($('#worlds_'+ world_id +'_item_harvest_'+ row +'_' + column).val() != "") ? $('#worlds_'+ world_id +'_item_harvest_'+ row +'_' + column).val() : 1;
        document.getElementById('item_deduct').value  = ($('#worlds_'+ world_id +'_item_deduct_' + row +'_' + column).val() != "") ? $('#worlds_'+ world_id +'_item_deduct_'+ row +'_' + column).val() : 1;
        document.getElementById('item-desc').value = ($('#worlds_'+ world_id +'_item_desc_'+ row +'_' + column).val() != null) ? $('#worlds_'+ world_id +'_item_desc_'+ row +'_' + column).val() : "";


        if ($world_category.val() != ""){
            $('#formItemModal').modal('show');
            $inputCategory = $('#item_id');
            $inputCategory.select2({
                width: '220px',
                allowClear: true,
                placeholder: "Select Item",
                minimumInputLength: 0,
                id: function (data) {
                    return data._id;
                },
                ajax: {
                    url: baseUrlPath + "badge/items?filter_category=" + $world_category.val(),
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (term, page) {
                        return {
                            search: term, // search term
                        };
                    },
                    results: function (data, page) {
                        list_badge = data.rows;
                        for(var i=0; i < Number($world_height.val()); i++){
                            for(var j=0; j< Number($world_width.val()); j++){
                                for(var k=0; k< data.total; k++){
                                    if(data.rows[k] != undefined){
                                        if($('#worlds_'+world_id+'_item_id_' + i + '_' + j).val() == data.rows[k]._id){
                                            data.rows.splice(k, 1);
                                            data.total--;
                                        }
                                    }
                                }
                            }
                        }
                        return {results: data.rows};
                    },
                    cache: true
                },
                initSelection: function (element, callback) {
                    var id = $(element).val();
                    if (id !== ""){
                        $.ajax(baseUrlPath + "badge/items/" + id, {
                            dataType: "json",
                            beforeSend: function (xhr) {
                                $inputCategory.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                            }
                        }).done(function (data) {
                            if (typeof data != "undefined")
                                callback(data);
                        }).always(function () {
                            $inputCategory.parent().parent().parent().find("#pleaseWaitSpan").remove();
                        });
                    }
                },
                formatResult: categoryFormatResult,
                formatSelection: categoryFormatSelection,
            });
            $rule = $('#rule_id');
            $rule.select2({
                width: '220px',
                allowClear: true,
                placeholder: "Select Rule",
                minimumInputLength: 0,
                id: function (data) {
                    return data._id;
                },
                ajax: {
                    url: baseUrlPath + "game/rule",
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (term, page) {
                        return {
                            search: term, // search term
                        };
                    },
                    results: function (data, page) {
                        return {results: data.rows};
                    },
                    cache: true
                },
                initSelection: function (element, callback) {
                    var id = $(element).val();
                    if (id !== ""){
                        $.ajax(baseUrlPath + "game/rule/" + id, {
                            dataType: "json",
                            beforeSend: function (xhr) {
                                $rule.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                            }
                        }).done(function (data) {
                            if (typeof data != "undefined")
                                callback(data);
                        }).always(function () {
                            $rule.parent().parent().parent().find("#pleaseWaitSpan").remove();
                        });
                    }
                },
                formatResult: categoryFormatResult,
                formatSelection: categoryFormatSelection,
            });
        }
        else{
            preventUnusual.message("please select category" , "Fail!");
        }
    }
</script>
<?php }
if($game == 'bingo'){
    /////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                                                             //
    //            ///////////    /////////    //       //    ///////////   ///////////             //
    //            //       //       //        ////     //    //            //       //             //
    //            //       //       //        //  //   //    //            //       //             //
    //            /////////         //        //    // //    //    /////   //       //             //
    //            //       //       //        //     ////    //       //   //       //             //
    //            //       //       //        //       //    //       //   //       //             //
    //            //////////     ////////     //       //    ///////////   ///////////             //
    //                                                                                             //
    /////////////////////////////////////////////////////////////////////////////////////////////////
?>
<script>
    var list_badge;
    $('button#item-modal-submit').on('click',submitItemModalForm);
    $('a#confirm_del_button').on('click',deleteConfirm);
    function add_thumbnail(world_id){
        var $world_widths = $('#worlds_'+ world_id +'_world_width'),
            $world_widths_temp = $('#worlds_'+ world_id +'_world_width_temp'),
            $world_heights = $('#worlds_'+ world_id +'_world_height'),
            $world_heights_temp = $('#worlds_'+ world_id +'_world_height_temp'),
            $thumbnails_grids = $('#worlds_'+ world_id +'_thumbnails_grids');
        $waitDialog.modal('show');

        if($world_heights.val() > $world_heights_temp.val() || $world_widths.val() > $world_widths_temp.val()){

            for(var i =0;i<$world_heights.val();i++){
                var myElemi = document.getElementById('thumbnails_grid_'+ world_id +'_'+ i);
                if (myElemi === null) $thumbnails_grids.append('<ul id="thumbnails_grid_'+ world_id +'_'+ i +'" class="thumbnails">');

                for(var j=0;j<$world_widths.val();j++){
                    var myElemj = document.getElementById('thumbnails_grid_'+ world_id +'_' + i + '_' + j);
                    if (myElemj === null) $('#thumbnails_grid_'+ world_id +'_'+ i).append('<li id="thumbnails_grid_'+ world_id +'_' + i + '_' + j + '">\
                                                <div class="thumbnail tooltips" data-placement="top" title="['+i+']['+j+']" style="width:120px;height:120px;">\
                                                    <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_id] id="worlds_'+world_id+'_item_id_' + i + '_' + j + '" size="100" value="">\
                                                    <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][rule_id] id="worlds_'+world_id+'_rule_id_' + i + '_' + j + '" size="100" value="">\
                                                    <input type="hidden" name=worlds['+world_id+'][world_item]['+i+']['+j+'][item_description] id="worlds_'+world_id+'_item_description_' + i + '_' + j + '" size="100" value="">\
                                                    <i class="fa fa-plus-circle fa-5x fa-align-center" onclick="showItemModalForm('+world_id+','+i+','+j+')" class="tooltips" data-placement="top" title="['+i+']['+j+']" id = "worlds_'+world_id+'_item_add_'+i+'_'+j+'" style="padding: 30px" aria-hidden="true"></i>\
                                                    <img class="hide" style="padding: 10px" onclick = "showItemModalForm('+world_id+','+i+','+j+')" src="" name="worlds['+world_id+'][world_item]['+i+']['+j+'][item_thumb]" alt="" id="worlds_'+world_id+'_item_thumb_'+i+'_'+j+'" onerror="$(this).attr(\'src\', \'<?php echo base_url(); ?>image/default-image.png\' )"/>\
                                                </div>\
                                                <div id="world_'+ world_id +'_item_image_' + i + '_' + j + '"></div>\
                                             </li>');
                }
                if (myElemi === null) $thumbnails_grids.append('</ul>');
            }

            if($world_heights.val() > $world_heights_temp.val()) document.getElementById('worlds_'+world_id+'_world_height_temp').value = $world_heights.val();
            if($world_widths.val() > $world_widths_temp.val()) document.getElementById('worlds_'+world_id+'_world_width_temp').value = $world_widths.val();
        } else {
            for(var i =0; i<$world_heights_temp.val(); i++){
                for(var j=0; j<$world_widths_temp.val(); j++){
                    if(i > ($world_heights.val()-1) || j > ($world_widths.val()-1)){
                        $('#thumbnails_grid_'+ world_id +'_' + i +'_'+ j).remove();
                    }
                }
            }
            if($world_heights.val() < $world_heights_temp.val()) document.getElementById('worlds_'+world_id+'_world_height_temp').value = $world_heights.val();
            if($world_widths.val() < $world_widths_temp.val()) document.getElementById('worlds_'+world_id+'_world_width_temp').value = $world_widths.val();
        }
        $waitDialog.modal('hide');
    }

    function deleteConfirm(){
        var event = $('#confirm_event').val();
        var world = $('#confirm_world').val();
        var $world_widths = $('#worlds_'+ world +'_world_width'),
            $world_heights = $('#worlds_'+ world +'_world_height');

        if(event == "category"){
            for(var i =0;i<$world_heights.val();i++){
                for(var j=0;j<$world_widths.val();j++){
                    document.getElementById('worlds_'+ world +'_item_id_' + i + '_' + j).value =  "";
                    document.getElementById('worlds_'+ world +'_rule_id_' + i + '_' + j).value =  "";
                    document.getElementById('worlds_'+ world +'_item_description_' + i + '_' + j).value =  "";
                    $('#worlds_'+ world +'_item_thumb_' + i + '_' + j).addClass('hide');
                    $('#worlds_'+ world +'_item_add_' + i + '_' + j).removeClass('hide');
                }
            }
            $('#confirm-delete').modal('hide');
        }
        if(event == "world"){
            $('#world_'+world+'_item_wrapper').remove();
            init_world_event(world);
            $('#confirm-delete').modal('hide');
        }
    }

    function submitItemModalForm() {
        var world_id   = $('#item_world_id').val(),
            row    = $('#item_row').val(),
            column    = $('#item_column').val(),
            item_id = $('#item_id').val(),
            rule_id = $('#rule_id').val(),
            item_desc = $('#item-desc').val();
        dialogMsg = "";

        if(item_id == "") dialogMsg += '- Please select Item <br>';
        if(rule_id == "") dialogMsg += '- Please select Rule <br>';
        if(dialogMsg != ""){
            preventUnusual.message(dialogMsg , "Fail!");
        } else {
            $('#formItemModal').modal('hide');

            document.getElementById('worlds_'+ world_id +'_item_id_' + row + '_' + column).value =  item_id;
            document.getElementById('worlds_'+ world_id +'_rule_id_' + row + '_' + column).value =  rule_id;
            document.getElementById('worlds_'+ world_id +'_item_description_' + row + '_' + column).value = item_desc;
            if(list_badge != undefined) for(var i = 0; i<  list_badge.length; i++){
                var path = list_badge[i]['image'].split(".");
                if (item_id == list_badge[i]['_id']){
                    document.getElementById('worlds_'+ world_id +'_item_thumb_' + row + '_' + column).src = "https://images.pbapp.net/cache/"+path[0]+"-100x100."+path[1];
                }
            }

            $('#worlds_'+ world_id +'_item_thumb_' + row + '_' + column).removeClass('hide');
            $('#worlds_'+ world_id +'_item_add_' + row + '_' + column).addClass('hide');
            document.getElementById('item_id').value = "";
            document.getElementById('rule_id').value = "";
            document.getElementById('item-desc').value = "";
        }
    }

    function showItemModalForm(world_id,row,column) {
        var $world_category = $('#worlds_' + world_id + '_category');
        var $world_width = $('#worlds_' + world_id + '_world_width');
        var $world_height = $('#worlds_' + world_id + '_world_height');
        document.getElementById('worlds_' + world_id + '_world_width').disabled = false;
        document.getElementById('worlds_' + world_id + '_world_height').disabled = false;
        document.getElementById('item_world_id').value = world_id;
        document.getElementById('item_row').value = row;
        document.getElementById('item_column').value = column;
        document.getElementById('item_id').value = ($('#worlds_' + world_id + '_item_id_' + row + '_' + column).val() != "") ? $('#worlds_' + world_id + '_item_id_' + row + '_' + column).val() : "";
        document.getElementById('rule_id').value = ($('#worlds_' + world_id + '_rule_id_' + row + '_' + column).val() != "") ? $('#worlds_' + world_id + '_rule_id_' + row + '_' + column).val() : "";
        document.getElementById('item-desc').value = ($('#worlds_' + world_id + '_item_desc_' + row + '_' + column).val() != null) ? $('#worlds_' + world_id + '_item_desc_' + row + '_' + column).val() : "";


        if ($world_category.val() != "") {
            $('#formItemModal').modal('show');
            $inputCategory = $('#item_id');
            $inputCategory.select2({
                width: '220px',
                allowClear: true,
                placeholder: "Select Item",
                minimumInputLength: 0,
                id: function (data) {
                    return data._id;
                },
                ajax: {
                    url: baseUrlPath + "badge/items?filter_category=" + $world_category.val(),
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (term, page) {
                        return {
                            search: term, // search term
                        };
                    },
                    results: function (data, page) {
                        list_badge = data.rows;
                        for (var i = 0; i < Number($world_height.val()); i++) {
                            for (var j = 0; j < Number($world_width.val()); j++) {
                                for (var k = 0; k < data.total; k++) {
                                    if (data.rows[k] != undefined) {
                                        if ($('#worlds_' + world_id + '_item_id_' + i + '_' + j).val() == data.rows[k]._id) {
                                            data.rows.splice(k, 1);
                                            data.total--;
                                        }
                                    }
                                }
                            }
                        }
                        return {results: data.rows};
                    },
                    cache: true
                },
                initSelection: function (element, callback) {
                    var id = $(element).val();
                    if (id !== "") {
                        $.ajax(baseUrlPath + "badge/items/" + id, {
                            dataType: "json",
                            beforeSend: function (xhr) {
                                $inputCategory.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                            }
                        }).done(function (data) {
                            if (typeof data != "undefined")
                                callback(data);
                        }).always(function () {
                            $inputCategory.parent().parent().parent().find("#pleaseWaitSpan").remove();
                        });
                    }
                },
                formatResult: categoryFormatResult,
                formatSelection: categoryFormatSelection,
            });
            $rule = $('#rule_id');
            $rule.select2({
                width: '220px',
                allowClear: true,
                placeholder: "Select Rule",
                minimumInputLength: 0,
                id: function (data) {
                    return data._id;
                },
                ajax: {
                    url: baseUrlPath + "game/rule",
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (term, page) {
                        return {
                            search: term, // search term
                        };
                    },
                    results: function (data, page) {
                        return {results: data.rows};
                    },
                    cache: true
                },
                initSelection: function (element, callback) {
                    var id = $(element).val();
                    if (id !== "") {
                        $.ajax(baseUrlPath + "game/rule/" + id, {
                            dataType: "json",
                            beforeSend: function (xhr) {
                                $rule.parent().parent().parent().find('.control-label').append($pleaseWaitSpanHTML);
                            }
                        }).done(function (data) {
                            if (typeof data != "undefined")
                                callback(data);
                        }).always(function () {
                            $rule.parent().parent().parent().find("#pleaseWaitSpan").remove();
                        });
                    }
                },
                formatResult: categoryFormatResult,
                formatSelection: categoryFormatSelection,
            });
        }
        else {
            preventUnusual.message("please select category", "Fail!");
        }
    }
</script>
<?php }

if($game == 'egg' || $game == 'pairs' || $game == 'catch_item' ){
    ?>
<script>
    var countWorldId = 0,
        $waitDialog = $('#pleaseWaitDialog');

    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined) {
                $('#errorModal').find('#myModalLabel').html(title);
            }else{
                $('#errorModal').find('#myModalLabel').html("Warning !");
            }
            $('#errorModal').removeClass('hide');
            $('#errorModal').removeClass('in');
            $('#errorModal').modal();
            $('#errorModal .modal-body').html(msg);
        }
    }

    function game_validation(){
        var formData = $('form.game-form').serialize();
        $.ajax({
            type: "POST",
            url: baseUrlPath + "game/edit/",
            data: formData,
            beforeSend: function (xhr) {
                $waitDialog.modal();
            }
        }).done(function (data) {
            location.reload();
        }).fail(function (xhr, textStatus, errorThrown) {
            if(JSON.parse(xhr.responseText).status == "error") {
                $('form.game-form').trigger("reset");
                alert('Save error: ' + errorThrown + '. Please contact Playbasis!');
            }else if(JSON.parse(xhr.responseText).status == "name duplicate"){
                $waitDialog.modal('hide');

            }
        }).always(function () {
            $waitDialog.modal('hide');
        });
    }
    //======================== World ========================
    $('.world-item-wrapper').each(function () {
        countWorldId++;
    })

    for(var iWorld=1;iWorld <= countWorldId;iWorld++){
        init_world_event(iWorld);
    }
    $('.open-world-btn').click(function () {
        $('.world-item-wrapper>.box-content').show();
    })
    $('.close-world-btn').click(function () {
        $('.world-item-wrapper>.box-content').hide();
    })

    $('.add-world-btn').click(function () {
        countWorldId++;
        var itemWorldId = countWorldId;
        var itemWorldHtml = '<div class="world-item-wrapper" data-world-id="'+ itemWorldId +'" id="world_'+ itemWorldId +'_item_wrapper">\
                                <div class="box-header box-world-header overflow-visible" style="height: 30px;">\
                                    <h2><img src="<?php echo base_url();?>image/default-image.png" width="30"> World</h2>\
                                    <div class="box-icon">\
                                        <a href="javascript:void(0)" class="btn btn-danger right remove-world-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
                                        <span class="break"></span>\
                                        <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                                    </div>\
                                </div>\
                                <div class="box-content clearfix">\
                                    <div class="row-fluid">\
                                        <div class="span12 well" style="min-height:500px">\
                                            <div class="span6">\
                                            <table class="form">\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_name"); ?>:</td>\
                                                    <td><input type="text" name="worlds['+itemWorldId+'][world_name]" id="worlds_'+itemWorldId+'_name" size="100" value=""></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_level"); ?>:</td>\
                                                    <td><input type="number" name="worlds['+itemWorldId+'][world_level]" id="worlds_'+itemWorldId+'_level" size="100" min="1" value="1"></td>\
                                                    <td></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_image"); ?>:</td>\
                                                    <td> <img src="<?php echo S3_IMAGE . "cache/no_image-100x100.jpg" ?>" alt="" id="world_'+itemWorldId+'_thumb" onerror="$(this).attr(\'src\', \'<?php echo base_url(); ?>image/default-image.png\' )"/>\
                                                        <input type="hidden" name="worlds['+itemWorldId+'][world_image]" value="no_image.jpg" id="world_'+itemWorldId+'_image"/>\
                                                        <br/>\
                                                        <a onclick="image_upload(\'#world_'+itemWorldId+'_image\', \'world_'+itemWorldId+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
                                                        <a onclick="$(\'world_'+itemWorldId+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'world_'+itemWorldId+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a>\
                                                    </td>\
                                                    <td></td>\
                                                </tr>\
                                            </table>\
                                            </div>\
                                            <div class="span6">\
                                            <table class="form">\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_range_low"); ?>:</td>\
                                                    <td><input type="number" name="worlds['+itemWorldId+'][world_low]" id="worlds_'+itemWorldId+'_low" size="100" min="1" value="1"></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_range_high"); ?>:</td>\
                                                    <td><input type="number" name="worlds['+itemWorldId+'][world_high]" id="worlds_'+itemWorldId+'_high" size="100" min="1" value="1"></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><?php echo $this->lang->line("entry_world_description"); ?>:</td>\
                                                    <td><textarea name="worlds['+itemWorldId+'][world_description]" rows="6"></textarea></td>\
                                                    <td></td>\
                                                </tr>\
                                            </table>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>';

        $('.world-item-wrapper>.box-content').slideUp();
        $('.world-wrapper').append(itemWorldHtml);
        init_world_event(itemWorldId);
    });

    function init_world_event(id) {
        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
        $('.world-item-wrapper .box-world-header').unbind().bind('click', function (data) {
            var $target = $(this).next('.box-content');

            if ($target.is(':visible')) $('i', $(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i', $(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.remove-world-btn').unbind().bind('click', function (data) {
            $('#confirm-delete .modal-body').html('Are you sure to remove!!');
            document.getElementById('confirm_event').value = "world";
            document.getElementById('confirm_world').value = $(this).parent().parent().parent().data('world-id');
            $('#confirm-delete').modal('show');
        });
    }

    $('#page-render').on('click', 'a#confirm_del_button', deleteConfirm);

    function deleteConfirm(){
        var event = $('#confirm_event').val();
        var world = $('#confirm_world').val();

        if(event == "world"){
            $('#world_'+world+'_item_wrapper').remove();
            init_world_event(world);
            $('#confirm-delete').modal('hide');
        }
    }
</script>
<?php } ?>



