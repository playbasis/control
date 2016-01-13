<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10 goods-page">
    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt=""/> <?php echo $heading_title; ?></h1>
            <?php //if($user_group_id != $setting_group_id){ ?>
            <div class="buttons">
                <?php if ($client_id) { ?>
                    <button class="btn btn-info" onclick="location = baseUrlPath+'goods/import'"
                            type="button"><?php echo $this->lang->line('button_import'); ?></button>
                <?php } ?>
                <button class="btn btn-info" onclick="location = baseUrlPath+'goods/insert'"
                        type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
            <?php //}?>
        </div>
        <div class="content">
            <div class="tabbable">
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active"><a href="#goodsListTab" data-toggle="tab"><?php echo $this->lang->line('heading_title_goods_list'); ?></a></li>
                    <li><a href="#MarkAsUsedTab" data-toggle="tab"><?php echo $this->lang->line('heading_title_mark_as_used'); ?></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="goodsListTab">
                    <?php if ($this->session->flashdata('success')) { ?>
                        <div class="content messages half-width">
                            <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                        </div>
                    <?php } ?>
                    <div id="goods">
                        <?php
                        $attributes = array('id' => 'form');
                        echo form_open('goods/delete', $attributes);
                        ?>
                        <table class="list">
                            <thead>
                            <tr>
                                <td width="1" style="text-align: center;"><input type="checkbox"
                                                                                 onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
                                </td>
                                <td class="left"
                                    style="width:72px;"><?php echo $this->lang->line('column_image'); ?></td>
                                <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                                <?php if ($org_status) { ?>
                                    <td class="left"
                                        style="width:50px;"><?php echo $this->lang->line('column_organization'); ?></td>
                                <?php } ?>
                                <?php if (!$client_id) { ?>
                                    <td class="left"><?php echo $this->lang->line('column_owner'); ?></td>
                                <?php } ?>
                                <?php if ($client_id) { ?>
                                    <td class="left"
                                        style="width:50px;"><?php echo $this->lang->line('column_is_group'); ?></td>
                                <?php } ?>
                                <td class="left"
                                    style="width:50px;"><?php echo $this->lang->line('column_peruser'); ?></td>
                                <td class="left"
                                    style="width:50px;"><?php echo $this->lang->line('column_quantity'); ?></td>
                                <td class="left"
                                    style="width:50px;"><?php echo $this->lang->line('column_status'); ?></td>
                                <td class="right"
                                    style="width:60px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                                <td class="right"
                                    style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($goods_list) && $goods_list) { ?>
                                <?php foreach ($goods_list as $goods) { ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php if ($client_id) { ?>
                                                <?php if (!(isset($goods['sponsor']) && $goods['sponsor'])) { ?>
                                                    <?php if ($goods['selected']) { ?>
                                                        <input type="checkbox" name="selected[]"
                                                               value="<?php echo $goods['goods_id']; ?>"
                                                               checked="checked"/>
                                                    <?php } else { ?>
                                                        <input type="checkbox" name="selected[]"
                                                               value="<?php echo $goods['goods_id']; ?>"/>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($goods['selected']) { ?>
                                                    <input type="checkbox" name="selected[]"
                                                           value="<?php echo $goods['goods_id']; ?>" checked="checked"/>
                                                <?php } else { ?>
                                                    <input type="checkbox" name="selected[]"
                                                           value="<?php echo $goods['goods_id']; ?>"/>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                        <td class="left">
                                            <div class="image"><img src="<?php echo $goods['image']; ?>" alt=""
                                                                    id="thumb"
                                                                    onerror="$(this).attr('src','<?php echo base_url(); ?>image/default-image.png');"/>
                                            </div>
                                        </td>
                                        <td class="left"><?php echo $goods['name']; ?></td>
                                        <?php if ($org_status) { ?>
                                            <td class="left"><?php echo (isset($goods['organize_name']) && !is_null($goods['organize_name'])) ? $goods['organize_name'] : ''; ?></td>
                                        <?php } ?>
                                        <?php if (!$client_id) { ?>
                                            <td class="left"><?php echo ($goods['is_public']) ? "Public" : "Private"; ?></td>
                                        <?php } ?>
                                        <?php if ($client_id) { ?>
                                            <td class="left"><?php echo ($goods['is_group']) ? "Yes" : ""; ?></td>
                                        <?php } ?>
                                        <td class="right"><?php echo (isset($goods['per_user']) && !is_null($goods['per_user'])) ? $goods['per_user'] : 'Unlimited'; ?></td>
                                        <td class="right"><?php echo (isset($goods['quantity']) && !is_null($goods['quantity'])) ? $goods['quantity'] : 'Unlimited'; ?></td>
                                        <td class="left"><?php echo ($goods['status']) ? "Enabled" : "Disabled"; ?></td>
                                        <td class="right"><?php echo $goods['sort_order']; ?></td>
                                        <td class="right">
                                            <?php if ($client_id) { ?>
                                                <?php if (!(isset($goods['sponsor']) && $goods['sponsor'])) { ?>
                                                    [ <?php echo anchor('goods/update/' . $goods['goods_id'],
                                                        'Edit'); ?> ]
                                                <?php } ?>
                                            <?php } else { ?>
                                                [ <?php echo anchor('goods/update/' . $goods['goods_id'], 'Edit'); ?> ]
                                            <?php } ?>
                                            <?php echo anchor('goods/increase_order/' . $goods['goods_id'],
                                                '<i class="icon-chevron-down icon-large"></i>', array(
                                                    'class' => 'push_down',
                                                    'alt' => $goods['goods_id'],
                                                    'style' => 'text-decoration:none'
                                                )); ?>
                                            <?php echo anchor('goods/decrease_order/' . $goods['goods_id'],
                                                '<i class="icon-chevron-up icon-large"></i>', array(
                                                    'class' => 'push_up',
                                                    'alt' => $goods['goods_id'],
                                                    'style' => 'text-decoration:none'
                                                )); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td class="center"
                                        colspan="9"><?php echo $this->lang->line('text_no_results'); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php
                        echo form_close(); ?>
                    </div><!-- #actions -->
                    <div class="pagination">
                        <ul class='ul_rule_pagination_container'>
                            <li class="page_index_number active"><a>Total Records:</a></li>
                            <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a>
                            </li>
                            <li class="page_index_number active">
                                <a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                            <?php echo $pagination_links; ?>
                        </ul>
                    </div>
                </div>
                <div class="tab-pane fade in" id="MarkAsUsedTab">
                    <table data-toggle="table"
                           data-search="true"
                           data-pagination="true">
                        <thead>
                        <!--                            <th>-->
                        <?php //echo $this->lang->line('entry_system_id'); //hide ?><!--</th>-->
                        <?php if ($org_status) { ?>
                            <tr>
                                <th rowspan="2" data-align="center" data-valign="middle"><?php echo $this->lang->line('entry_player_id'); //cl_id+name+lastname[+node_name+node_type+store_id]?></th>
                                <th rowspan="2" data-align="center" data-valign="middle"><?php echo $this->lang->line('entry_player_name'); ?></th>
                                <th colspan="3" data-align="center"><?php echo $this->lang->line('entry_node_detail'); ?></th>
                                <th rowspan="2" data-align="center" data-valign="middle"><?php echo $this->lang->line('entry_operate'); ?></th>
                            </tr>
                            <tr>
                                <th data-align="center"><?php echo $this->lang->line('entry_node_name'); ?></th>
                                <th data-align="center"><?php echo $this->lang->line('entry_node_type'); ?></th>
                                <th data-align="center"><?php echo $this->lang->line('entry_store_id'); ?></th>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <th><?php echo $this->lang->line('entry_player_id'); //cl_id+name+lastname?></th>
                                <th><?php echo $this->lang->line('entry_player_name'); //cl_id+name+lastname?></th>
                                <th><?php echo $this->lang->line('entry_operate'); ?></th>
                            </tr>
                        <?php } ?>
                        </thead>
                        <tbody>
                        <?php
                        if (isset($redeemed_goods_list) && !empty($redeemed_goods_list)) {
                            foreach ($redeemed_goods_list as $redeemed_goods) { ?>
                                <?php if ($org_status) { ?>
                                    <tr data-id="<?php echo $redeemed_goods['_id'] ?>">
                                        <td><?php echo $redeemed_goods['cl_player_id'] ?></td>
                                        <td><?php echo $redeemed_goods['player_info']['first_name'] . " " . $redeemed_goods['player_info']['last_name']; ?></td>
                                        <td><?php echo $redeemed_goods['player_node_info']['name'] ?></td>
                                        <td><?php echo $redeemed_goods['player_organize_info']['name'] ?></td>
                                        <td><?php echo $redeemed_goods['cl_player_id'] ?></td>
                                        <td><a href="#" role="button" class="btn btn-primary">Used</a></td>
                                    </tr>
                                <?php } else { ?>
                                    <tr data-id="<?php echo $redeemed_goods['_id'] ?>">
                                        <td><?php echo $redeemed_goods['cl_player_id'] ?></td>
                                        <td><?php echo $redeemed_goods['player_info']['first_name'] . " " . $redeemed_goods['player_info']['last_name']; ?></td>
                                        <td><a href="#" role="button" class="btn btn-primary">Used</a></td>
                                    </tr>
                                <?php } ?>
                            <?php }
                        }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="<?php echo base_url(); ?>stylesheet/custom/bootstrap-table.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/custom/bootstrap-table.min.js" type="text/javascript"></script>
<script type="text/javascript">

    $('#goods')
        .on("click", ".push_down", function (e) {
            e.preventDefault();

            $.ajax({
                url: baseUrlPath + 'goods/increase_order/' + $(this).attr('alt'),
                dataType: "json"
            }).done(function (data) {
//            console.log("Testing");
                var getListForAjax = 'goods/getListForAjax/';
                var getNum = '<?php echo $this->uri->segment(3);?>';
                if (!getNum) {
                    getNum = 0;
                }
                $('#goods').load(baseUrlPath + getListForAjax + getNum);
            });
        })
        .on("click", '.push_up', function (e) {
            e.preventDefault();

            $.ajax({
                url: baseUrlPath + 'goods/decrease_order/' + $(this).attr('alt'),
                dataType: "json"
            }).done(function (data) {
//            console.log("Testing");
                var getListForAjax = 'goods/getListForAjax/';
                var getNum = '<?php echo $this->uri->segment(3);?>';
                if (!getNum) {
                    getNum = 0;
                }
                $('#goods').load(baseUrlPath + getListForAjax + getNum);
            });
        });

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });
</script>