<div id="content" class="span10 cms-page">
    <!-- Messages to display
    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>-->

    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div class="content" style="text-align: center">
            <?php if($create && ($role == 'editor')):?>
                <a class="btn btn-info" id="btn-create"><?php echo $this->lang->line('button_create'); ?></a>
            <?php endif;?>
            <?php if((isset($role) != null)&&($create)):?>
                <a class="btn btn-info" id="btn-site" style="display: none" href=<?php echo isset($link)?$link:''?> target=_blank><?php echo $this->lang->line('button_site'); ?></a>
            <?php else:?>
                <a class="btn btn-info" id="btn-site" href=<?php echo isset($link)?$link:''?> target=_blank><?php echo $this->lang->line('button_site'); ?></a>
            <?php endif;?>


        </div>

        <!--a class="btn" id="btn-login">Login</a-->

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
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}
            success: function(json) {
                var notification = $('#notification');

                $('#btn-create').hide();
                $('#btn-site').show();
                /*
                if (json['error']) {
                    $('#notification').html(json['error']).addClass('warning').show();
                } else {

                    $('#notification').html(json['success']).addClass('success').show().removeClass('warning');

                }*/
                window.location.href=window.location.href;
            }

        });

        return true;
    });

    $("#btn-login").click(function(e){
        $.ajax({
            url: baseUrlPath+'User/cms_login',
            type: 'POST',
            dataType: 'json',
            data :{
                '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>',
                username : "jk_ae@hotmail.com",
                password : "12345",
                site_slug : 'demomobile'
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
                '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>',
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
