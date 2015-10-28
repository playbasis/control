<div id="content" class="span10 cms-page">
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
                <a class="btn btn-info" id="btn-create"><?php echo $this->lang->line('button_create'); ?></a>
                <a class="btn btn-info" id="btn-site"><?php echo $this->lang->line('button_site'); ?></a>
            </div>
        </div>
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if ($this->session->flashdata("fail")): ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata("fail"); ?></div>
                </div>
            <?php endif; ?>
            <?php
            $attributes = array('id' => 'form');
            ?>
            <table class="list">
                <thead>
                <tr>
                    <td class="center" style="width:72px;"><?php echo $this->lang->line('column_username'); ?></td>
                    <td class="center" style="width:100px;"><?php echo $this->lang->line('column_role'); ?></td>
                    <td class="center" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                </tr>
                </thead>
                <tbody>

                <?php if (isset($users)) { ?>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td class="left"><?php echo $user['username']; ?></td>
                            <td class="left"><?php echo ($user['status'])? "Enabled" : "Disabled"; ?></td>
                            <td class="right">[ <?php echo anchor('cms/update/'.$user['_id'], 'Edit'); ?> ]</td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
            echo form_close();
            ?>

            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
            </div>

        </div>
        <a class="btn" id="btn-login">Login</a>
        <a class="btn" id="btn-updateUser">Update User</a>

        <!--div-- class="content">
            <div class="control-group">
                <h2><?php echo $this->lang->line('cms_site_title'); ?></h2>

            </div>
            <div class="span4">
                <h3><?php echo  $this->lang->line('role_editor');?></h3>
                <div>
                    <ol class='editor' id="editor">
                        <li>First</li>
                        <li>Second</li>
                        <li>Third</li>
                    </ol>
                </div>
            </div>
            <div class="span4">
                <h3><?php echo  $this->lang->line('role_contributor');?></h3>
                <div></div>
                <ol class='contributor' id="contributor">
                    <li>First</li>
                    <li>Second</li>
                    <li>Third</li>
                </ol>
            </div>




        </div--><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->


<link href="<?php echo base_url(); ?>javascript/jquery/sortable/css/bootstrap-switch.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>javascript/jquery/sortable/css/coderay.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/jquery/sortable/js/jquery-sortable-min.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        var adjustment;

        $("ol.editor").sortable({
            group: 'simple_with_animation',
            pullPlaceholder: false,
            // animation on drop
            onDrop: function  ($item, container, _super) {
                var $clonedItem = $('<li/>').css({height: 0});
                $item.before($clonedItem);
                $clonedItem.animate({'height': $item.height()});

                $item.animate($clonedItem.position(), function  () {
                    $clonedItem.detach();
                    _super($item, container);
                });
            },

            // set $item relative to cursor position
            onDragStart: function ($item, container, _super) {
                var offset = $item.offset(),
                    pointer = container.rootGroup.pointer;

                adjustment = {
                    left: pointer.left - offset.left,
                    top: pointer.top - offset.top
                };

                _super($item, container);
            },
            onDrag: function ($item, position) {
                $item.css({
                    left: position.left - adjustment.left,
                    top: position.top - adjustment.top
                });
            }
        });
        $("ol.contributor").sortable({
            group: 'simple_with_animation',
            pullPlaceholder: false,
            // animation on drop
            onDrop: function  ($item, container, _super) {
                var $clonedItem = $('<li/>').css({height: 0});
                $item.before($clonedItem);
                $clonedItem.animate({'height': $item.height()});

                $item.animate($clonedItem.position(), function  () {
                    $clonedItem.detach();
                    _super($item, container);
                });
            },

            // set $item relative to cursor position
            onDragStart: function ($item, container, _super) {
                var offset = $item.offset(),
                    pointer = container.rootGroup.pointer;

                adjustment = {
                    left: pointer.left - offset.left,
                    top: pointer.top - offset.top
                };

                _super($item, container);
            },
            onDrag: function ($item, position) {
                $item.css({
                    left: position.left - adjustment.left,
                    top: position.top - adjustment.top
                });
            }
        });

    });

    $("#btn-create").click(function(e){
        $.ajax({
            url: baseUrlPath+'CMS/createCMS',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                var notification = $('#notification');

                if (json['error']) {
                    $('#notification').html(json['error']).addClass('warning').show();
                } else {

                    $('#notification').html(json['success']).addClass('success').show().removeClass('warning');

                }
            }

        });

        return false;

        return false;
    });

    $("#btn-login").click(function(e){
        $.ajax({
            url: baseUrlPath+'CMS/login',
            type: 'POST',
            dataType: 'json',
            data :{
                username : "jk_ae@hotmail.com",
                password : "12345"
            },
            success: function(json) {
                var notification = $('#notification');

                if (json['error']) {
                    $('#notification').html(json['error']).addClass('warning').show();
                } else {

                    $('#notification').html(json['success']).addClass('success').show().removeClass('warning');

                }
            }

        });

        return false;
    });

    $("#btn-updateUser").click(function(e){
        $.ajax({
            url: baseUrlPath+'CMS/updateUserPermisison',
            type: 'POST',
            dataType: 'json',
            data :{
                username : "jk_ae@hotmail.com",
                password : "12345"
            },
            success: function(json) {
                var notification = $('#notification');

                if (json['error']) {
                    $('#notification').html(json['error']).addClass('warning').show();
                } else {

                    $('#notification').html(json['success']).addClass('success').show().removeClass('warning');

                }
            }

        });

        return false;

    });





</script>
