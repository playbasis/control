<?php
                $attributes = array('id' => 'form');
                echo form_open('badge/delete',$attributes);
                ?>
                    <table class="list">
                        <thead>
                        <tr>
                            <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                            <td class="left" style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                            <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                            <?php if(!$client_id){?>
                                <td class="left"><?php echo $this->lang->line('column_owner'); ?></td>
                            <?php }?>
                            <td class="right" style="width:100px;"><?php echo $this->lang->line('column_category'); ?></td>
                            <td class="right" style="width:50px;"><?php echo $this->lang->line('column_peruser'); ?></td>
                            <td class="right" style="width:50px;"><?php echo $this->lang->line('column_quantity'); ?></td>
                            <td class="right" style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                            <td class="right" style="width:50px;"><?php echo $this->lang->line('column_visible'); ?></td>
                            <td class="right" style="width:60px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                            <td class="right" style="min-width:60px;"><?php echo $this->lang->line('column_tags'); ?></td>
                            <td class="right" style="width:70px;"><?php echo $this->lang->line('column_action'); ?></td>
                        </tr>
                        </thead>
                        <tr class="filter">
                            <td></td>
                            <td></td>
                            <td><input title="name" style="width: 95%;" placeholder="Filter Name" type="text" name="filter_name" value="<?php echo isset($_GET['filter_name']) ? $_GET['filter_name'] : "" ?>"/></td>
                            <?php if(!$client_id){?>
                                <td class="left"><?php echo $this->lang->line('column_owner'); ?></td>
                            <?php }?>
                            <td class="right"><input title="category" style="width: 90px;" type="text" name="filter_category" value="<?php echo isset($_GET['filter_category']) ? $_GET['filter_category'] : "" ?>"/></td>
                            <td>
                                <select name="filter_per_user" style="width:95%;margin-bottom: 0px;">
                                    <?php if (isset($_GET['filter_per_user']) && $_GET['filter_per_user'] == 'limited') { ?>
                                        <option value="">All</option>
                                        <option value="limited" selected="selected"><?php echo $this->lang->line('text_limit'); ?></option>
                                        <option value="unlimited" ><?php echo $this->lang->line('text_unlimit'); ?></option>
                                    <?php } elseif (isset($_GET['filter_per_user']) && $_GET['filter_per_user'] == 'unlimited') { ?>
                                        <option value="">All</option>
                                        <option value="limited"><?php echo $this->lang->line('text_limit'); ?></option>
                                        <option value="unlimited" selected="selected"><?php echo $this->lang->line('text_unlimit'); ?></option>
                                    <?php } else{?>
                                        <option value="" selected="selected">All</option>
                                        <option value="limited"><?php echo $this->lang->line('text_limit'); ?></option>
                                        <option value="unlimited"><?php echo $this->lang->line('text_unlimit'); ?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <select name="filter_quantity" style="width:95%;margin-bottom: 0px;">
                                    <?php if (isset($_GET['filter_quantity']) && $_GET['filter_quantity'] == 'limited') { ?>
                                        <option value="">All</option>
                                        <option value="limited" selected="selected"><?php echo $this->lang->line('text_limit'); ?></option>
                                        <option value="unlimited" ><?php echo $this->lang->line('text_unlimit'); ?></option>
                                    <?php } elseif (isset($_GET['filter_quantity']) && $_GET['filter_quantity'] == 'unlimited') { ?>
                                        <option value="">All</option>
                                        <option value="limited"><?php echo $this->lang->line('text_limit'); ?></option>
                                        <option value="unlimited" selected="selected"><?php echo $this->lang->line('text_unlimit'); ?></option>
                                    <?php } else{?>
                                        <option value="" selected="selected">All</option>
                                        <option value="limited"><?php echo $this->lang->line('text_limit'); ?></option>
                                        <option value="unlimited"><?php echo $this->lang->line('text_unlimit'); ?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <select name="filter_status" style="width:95%;margin-bottom: 0px;">
                                    <?php if (isset($_GET['filter_status']) && $_GET['filter_status'] == 'enable') { ?>
                                        <option value="">All</option>
                                        <option value="enable" selected="selected"><?php echo $this->lang->line('text_enable'); ?></option>
                                        <option value="disable" ><?php echo $this->lang->line('text_disable'); ?></option>
                                    <?php } elseif (isset($_GET['filter_status']) && $_GET['filter_status'] == 'disable') { ?>
                                        <option value="">All</option>
                                        <option value="enable"><?php echo $this->lang->line('text_enable'); ?></option>
                                        <option value="disable" selected="selected"><?php echo $this->lang->line('text_disable'); ?></option>
                                    <?php } else{?>
                                        <option value="" selected="selected">All</option>
                                        <option value="enable"><?php echo $this->lang->line('text_enable'); ?></option>
                                        <option value="disable"><?php echo $this->lang->line('text_disable'); ?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <select name="filter_visibility" style="width:95%;margin-bottom: 0px;">
                                    <?php if (isset($_GET['filter_visibility']) && $_GET['filter_visibility'] == 'enable') { ?>
                                        <option value="">All</option>
                                        <option value="enable" selected="selected"><?php echo $this->lang->line('text_enable'); ?></option>
                                        <option value="disable" ><?php echo $this->lang->line('text_disable'); ?></option>
                                    <?php } elseif (isset($_GET['filter_visibility']) && $_GET['filter_visibility'] == 'disable') { ?>
                                        <option value="">All</option>
                                        <option value="enable"><?php echo $this->lang->line('text_enable'); ?></option>
                                        <option value="disable" selected="selected"><?php echo $this->lang->line('text_disable'); ?></option>
                                    <?php } else{?>
                                        <option value="" selected="selected">All</option>
                                        <option value="enable"><?php echo $this->lang->line('text_enable'); ?></option>
                                        <option value="disable"><?php echo $this->lang->line('text_disable'); ?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <select name="sort_order" style="width:95%;margin-bottom: 0px;">
                                    <?php if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'asc') { ?>
                                        <option value="" disabled>Sort</option>
                                        <option value="asc" selected="selected"><?php echo $this->lang->line('asc'); ?></option>
                                        <option value="desc" ><?php echo $this->lang->line('desc'); ?></option>
                                    <?php } elseif (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') { ?>
                                        <option value="" disabled>Sort</option>
                                        <option value="asc"><?php echo $this->lang->line('asc'); ?></option>
                                        <option value="desc" selected="selected"><?php echo $this->lang->line('desc'); ?></option>
                                    <?php } else { ?>
                                        <option value="" disabled selected="selected">Sort</option>
                                        <option value="asc"><?php echo $this->lang->line('asc'); ?></option>
                                        <option value="desc"><?php echo $this->lang->line('desc'); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <input title="name" style="width: 95%;" placeholder="Filter tags" type="text" name="filter_tags" value="<?php echo isset($_GET['filter_tags']) ? $_GET['filter_tags'] : "" ?>"/>
                            </td>
                            <td class="right">
                                <a onclick="clear_filter();" style="margin-bottom: 5px;" class="button" id="clear_filter"><i class="fa fa-refresh"></i></a>
                                <a onclick="filter();" class="button"><i class="fa fa-filter"></i></a>
                            </td>
                        </tr>
                        <tbody>
                            <?php if (isset($badges) && $badges) { ?>
                            <?php foreach ($badges as $badge) { ?>
                            <tr>
                                <td style="text-align: center;">
                                <?php if (!$client_id){?>
                                    <?php if ($badge['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
                                    <?php } ?>
                                <?php }else{?> 
                                    <?php if(!(isset($badge['sponsor']) && $badge['sponsor'])){?> 
                                    <?php if ($badge['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
                                    <?php } ?>
                                    <?php }?>
                                <?php }?>
                                </td>
                                <td class="left"><div class="image"><img src="<?php echo $badge['image']; ?>" alt="" id="thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" /></div></td>
                                <td class="left"><?php echo $badge['name']; ?></td>
                                <?php if(!$client_id){?>
                                    <td class="left"><?php echo ($badge['is_public'])?"Public":"Private"; ?></td>
                                <?php }?>
                                <td class="right"><?php echo (isset($badge['category']) && !empty($badge['category'])) ? $badge['category'] : ''; ?></td>
                                <td class="right"><?php echo (isset($badge['per_user']) && !is_null($badge['per_user'])) ? $badge['per_user'] : 'Unlimited'; ?></td>
                                <td class="right"><?php echo (isset($badge['quantity']) && !is_null($badge['quantity'])) ? $badge['quantity'] : 'Unlimited'; ?></td>
                                <td class="left"><?php echo ($badge['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="left"><?php echo ($badge['visible'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo $badge['sort_order']; ?></td>
                                <td class="right" style="word-wrap:break-word;"><?php echo (isset($badge['tags']) && $badge['tags'] ? '<span class="label">'.implode('</span> <span class="label">', $badge['tags']).'</span>' : null); ?></td>
                                <td class="right">
                                    <?php
                                    if((!$client_id) || (!(isset($badge['sponsor']) && $badge['sponsor']))) {
                                        echo anchor('badge/update/'.$badge['badge_id'], "<i class='fa fa-edit fa-lg''></i>",
                                            array('class'=>'tooltips',
                                                'title' => 'Edit',
                                                'data-placement' => 'top'
                                            ));
                                    }
                                    ?>
                                    <?php echo anchor('badge/increase_order/'.$badge['badge_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$badge['badge_id'], 'style'=>'text-decoration:none'));?>
                                    <?php echo anchor('badge/decrease_order/'.$badge['badge_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$badge['badge_id'], 'style'=>'text-decoration:none' ));?>
                                </td>
                            </tr>
                                <?php } ?>
                            <?php } else { ?>
                        <tr>
                            <td class="center" colspan="<?php echo !$client_id ? 12 : 11; ?>"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                echo form_close();?>

<script type="text/javascript">

    var $formCategoryModal = $('#formCategoryModal'),
        $waitDialog = $('#pleaseWaitDialog'),
        $savedDialog = $('#savedDialog'),
        $categoryItemTable = $('#categoryItemTable'),
        $categoryItemToolbarRemove = $('#categoryItemToolbar').find('#remove'),
        categorySelections = [],
        $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html(),
        $categoryErrorDialog = $('#categoryErrorDialog');

    <?php if (!isset($_GET['filter_category']) && !isset($_GET['filter_name']) && !isset($_GET['filter_per_user']) && !isset($_GET['filter_quantity']) && !isset($_GET['filter_status']) && !isset($_GET['filter_visibility']) && !isset($_GET['filter_visibility']) && !isset($_GET['sort_order']) && !isset($_GET['filter_tags'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'badge');
    }
    function filter() {
        url = baseUrlPath + 'badge?';

        var filter_category = $('input[name=\'filter_category\']').attr('value');
        var filter_name = $('input[name=\'filter_name\']').attr('value');
        var filter_per_user = $('select[name=\'filter_per_user\']').attr('value');
        var filter_quantity = $('select[name=\'filter_quantity\']').attr('value');
        var filter_status = $('select[name=\'filter_status\']').attr('value');
        var filter_visibility = $('select[name=\'filter_visibility\']').attr('value');
        var sort_order = $('select[name=\'sort_order\']').attr('value');
        var filter_tags = $('input[name=\'filter_tags\']').attr('value');

        if (filter_category) {
            url += '&filter_category=' + encodeURIComponent(filter_category);
        }
        if (filter_name) {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
        if (filter_per_user) {
            url += '&filter_per_user=' + encodeURIComponent(filter_per_user);
        }
        if (filter_quantity) {
            url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
        }
        if (filter_status) {
            url += '&filter_status=' + encodeURIComponent(filter_status);
        }
        if (filter_visibility) {
            url += '&filter_visibility=' + encodeURIComponent(filter_visibility);
        }
        if (sort_order) {
            url += '&sort_order=' + encodeURIComponent(sort_order);
        }
        if (filter_tags) {
            url += '&filter_tags=' + encodeURIComponent(filter_tags);
        }

        location = url;
    }
</script>
