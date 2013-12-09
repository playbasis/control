<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <link rel="icon" type='image/x-icon' href="<?php echo base_url();?>image/favicon.ico">
    <meta charset="UTF-8" />
    <title><?php echo $title; ?></title>
    <base href="<?php echo base_url(); ?>" />
    <?php if (isset($description)) { ?>
        <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if (isset($keywords)) { ?>
        <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/stylesheet.css" />

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery-ui-1.8.21.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/custom/jquery-ui-1.8.21.custom.css" />

    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/custom/bootstrap.css" />
    <style type="text/css">
        #domain-list input{
            width: 80%;
        }
    </style>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.pie.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.stack.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.sparkline.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.resize.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.knob.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/custom.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>javascript/bootstrap/daterangepicker.css" />
    <script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/date.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/daterangepicker.js"></script>


    <script type="text/javascript">
        //-----------------------------------------
        // Confirm Actions (delete, uninstall)
        //-----------------------------------------
        $(document).ready(function(){
            // Confirm Delete
            $('#form').submit(function(){
                if ($(this).attr('action').indexOf('delete',1) != -1) {
                    var ItemSelected = false;
                    $('#form input[type="checkbox"]').each(function(){
                        if($(this).is(':checked')){
                            ItemSelected = true;
                        }
                    });
                    if(!ItemSelected) {
                        alert('<?php echo $text_retry; ?>');
                        return false;
                    }
                    else if (!confirm('<?php echo $text_confirm; ?>')) {
                        return false;
                    }
                }
            });

            // Confirm Uninstall
            $('a').click(function(){
                if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
                    if (!confirm('<?php echo $text_confirm; ?>')) {
                        return false;
                    }
                }
            });

            // Add class .active to current link
            $('ul.main-menu li a').each(function(){
                if(this.href === window.location.href) {
                    $(this).parent().addClass('active');
                }
            });
        });

        var imageUrlPath = "<?php echo S3_IMAGE ?>";
        var baseUrlPath = "<?php echo base_url();?><?php echo (index_page() == '')? '' : index_page()."/" ?>";
        var SiteId = "<?php echo $site_id;?>";
        var ClientId = "<?php echo $client_id;?>";
    </script>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/strip_tags.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/html5.js"></script>
</head>
<body>

<table id="user-list" class="list">
    <thead>
    <tr>
        <td class="left"><?php echo $this->lang->line('column_title'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_username'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_group'); ?></td>
        <td class="right"><?php echo $this->lang->line('column_status'); ?></td>
        <td></td>
    </tr>
    </thead>
    <?php $user_row = 0; ?>
    <?php foreach ($users as $user) { ?>
        <tbody id="user-row<?php echo $user_row; ?>">
        <tr>
            <td class="left"><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></td>
            <td class="left"><?php echo anchor('user/update/'.$user['user_id'], $user['username']); ?></td>
            <td class="left">
                <select name="user_value[<?php echo $user_row; ?>][user_group_id]">
                    <?php if ($groups) { ?>
                        <?php foreach ($groups as $group) { ?>
                            <?php if ($group['_id']==$user['user_group_id']) { ?>
                                <option value="<?php echo $group['_id']; ?>" selected="selected"><?php echo $group['name']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $group['_id']; ?>"><?php echo $group['name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
            </td>
            <td class="right"><select name="user_value[<?php echo $user_row; ?>][status]">
                    <?php if ($user['status']==1) { ?>
                        <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                        <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                    <?php } else { ?>
                        <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                        <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                    <?php } ?>
                </select></td>
            <td class="left">
                <a onclick="removeUser('<?php echo $user['user_id']; ?>');" class="button"><span><?php echo $this->lang->line('button_remove'); ?></span></a>
                <input type="hidden" name="user_value[<?php echo $user_row; ?>][user_id]" value="<?php echo $user['user_id']; ?>" />
                <input type="hidden" name="user_value[<?php echo $user_row; ?>][client_id]" value="<?php echo $user['client_id']; ?>" />
            </td>
        </tr>
        </tbody>
        <?php $user_row++; ?>
    <?php } ?>
    <tfoot>
    <tr>
        <td colspan="4"></td>
        <td class="left"></td>
    </tr>
    </tfoot>
</table>

<div class="pagination"><?php echo $pagination_links; ?></div>

<script type="text/javascript">
    function removeUser(user_id) {

        if (!confirm('<?php echo $text_confirm; ?>')) {
            return false;
        } else {

            $.ajax({
                url: baseUrlPath+'user/delete',
                type: 'POST',
                dataType: 'json',
                data: ({'user_id' : user_id, 'client_id' : '<?php echo $client_id; ?>'}),
                success: function(json) {
                    var notification = $('#notification');

                    if (json['error']) {

                        $('#notification').html(json['error']).addClass('warning').show();

                    } else {

                        $('#notification').html(json['success']).addClass('success').show();
                        location.reload(true);
                    }
                }

            });

        }

        return false;

    }
</script>

</body>
</html>