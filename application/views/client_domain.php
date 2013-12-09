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

<table id="domain-list" class="list">
    <thead>
    <tr>
        <td class="left" style="width:10%;"><?php echo $this->lang->line('column_domain_name'); ?></td>
        <td class="left" style="width:10%;"><?php echo $this->lang->line('column_domain_limit_users'); ?></td>
        <td class="left" style="width:15%"><?php echo $this->lang->line('column_domain_date_start'); ?></td>
        <td class="left" style="width:15%;"><?php echo $this->lang->line('column_domain_date_expire'); ?></td>
        <td class="left" style="width:10%"><?php echo $this->lang->line('column_domain_plan'); ?></td>
        <td class="right" style="width:10%"><?php echo $this->lang->line('column_domain_status'); ?></td>
        <td style="width:10%;"></td>
    </tr>
    </thead>
    <?php $domain_row = 0; ?>
    <?php if ($domains_data) { ?>
        <?php foreach ($domains_data as $domain) { ?>
            <tbody id="domain-row<?php echo $domain_row; ?>">
            <tr>
                <td class="left"><?php echo $domain['domain_name']; ?> [ <a href="#" class="button_reset_token" onclick="return resetToken('<?php echo $domain['site_id']; ?>');" ><?php echo $this->lang->line('text_reset_token'); ?></a> ]
                    <br /><span class="help">Keys:</span> <?php echo $domain['keys']; ?>
                    <br /><span class="help">Secret:</span> <?php echo $domain['secret']; ?>
                </td>
                <td class="left">
                    <input type="text" name="domain_value[<?php echo $domain_row; ?>][limit_users]" value="<?php echo $domain['limit_users']; ?>" size="50" />
                </td>
                <td class="left">
                    <input type="text" class="date" name="domain_value[<?php echo $domain_row; ?>][domain_start_date]" value="<?php if (strtotime(datetimeMongotoReadable($domain['date_start']))) { ?><?php echo date('Y-m-d', strtotime(datetimeMongotoReadable($domain['date_start']))); ?><?php } else { ?>-<?php } ?>" size="50" />
                </td>
                <td class="left">
                    <input type="text" class="date" name="domain_value[<?php echo $domain_row; ?>][domain_expire_date]" value="<?php if (strtotime(datetimeMongotoReadable($domain['date_expire']))) { ?><?php echo date('Y-m-d', strtotime(datetimeMongotoReadable($domain['date_expire']))); ?><?php } else { ?>-<?php } ?>" size="50" />
                </td>
                <td class="left">
                    <select name="domain_value[<?php echo $domain_row; ?>][plan_id]">
                        <option value="0" selected="selected"><?php echo $this->lang->line('text_select'); ?></option>
                        <?php if ($plan_data) { ?>
                            <?php foreach ($plan_data as $plan) { ?>
                                <?php if ($domain['plan_id']==$plan['_id']) { ?>
                                    <option value="<?php echo $plan['_id']; ?>" selected="selected"><?php echo $plan['name']; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $plan['_id']; ?>"><?php echo $plan['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </td>
                <td class="right"><select name="domain_value[<?php echo $domain_row; ?>][status]">
                        <?php if ($domain['status']==1) { ?>
                            <option value="1" selected="selected"><?php echo $this->lang->line('text_enabled'); ?></option>
                            <option value="0"><?php echo $this->lang->line('text_disabled'); ?></option>
                        <?php } else { ?>
                            <option value="1"><?php echo $this->lang->line('text_enabled'); ?></option>
                            <option value="0" selected="selected"><?php echo $this->lang->line('text_disabled'); ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <a onclick="deleteDomain('<?php echo $domain['client_id']; ?>', '<?php echo $domain['site_id']; ?>');$('#domain-row<?php echo $domain_row; ?>').remove();" class="button"><span><?php echo $this->lang->line('button_remove'); ?></span></a>
                    <input type="hidden" name="domain_value[<?php echo $domain_row; ?>][client_id]" value="<?php echo $domain['client_id']; ?>" />
                    <input type="hidden" name="domain_value[<?php echo $domain_row; ?>][site_id]" value="<?php echo $domain['site_id']; ?>" />
                </td>
            </tr>
            </tbody>
            <?php $domain_row++; ?>
        <?php } ?>
    <?php } ?>
</table>

<div class="pagination"><?php echo $pagination_links; ?></div>
<script type="text/javascript">
    $(function(){

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

    })
</script>
<script type="text/javascript">

    function deleteDomain(clientId , siteId) {
        var client_id = clientId;
        var site_id = siteId;

        $.ajax({
            url: baseUrlPath+'domain/delete',
            type: 'POST',
            dataType: 'json',
            data: ({'client_id' : client_id, 'site_id' : site_id}),
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

        return false;

    }

    function resetToken(site_id) {

        $.ajax({
            url: baseUrlPath+'domain/reset',
            type: 'post',
            data: 'site_id=' + site_id,
            dataType: 'json',
            success: function(json) {
                location.reload(true);
            }
        });

        return false;

    }
</script>

</body>
</html>