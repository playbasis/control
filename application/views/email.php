<div id="content" class="span10">
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
                <a class="btn btn-success" onclick="location = baseUrlPath"><i class="fa fa-home"></i></a>
            </div>
        </div>
        <div class="content">

            <div class="tabbable">
                <ul class="nav nav-tabs" id="mainTab">
                    <li class="active"><a href="#tabTemplate" data-toggle="tab"><?php echo $this->lang->line('tab_template'); ?></a></li>
                    <li><a href="#tabDomain" data-toggle="tab"><?php echo $this->lang->line('tab_domain'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tabTemplate">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <?php //if($user_group_id != $setting_group_id){ ?>
                                <div class="buttons">
                                    <button class="btn btn-info " onclick="location = baseUrlPath+'email/insert'" type="button"><i class="fa fa-plus"></i> <?php echo $this->lang->line('button_insert'); ?></button>
                                    <button class="btn btn-info " onclick="$('#form').submit();" type="button"><i class="fa fa-remove"></i> <?php echo $this->lang->line('button_delete'); ?></button><br><br>
                                </div>
                                <?php //}?>
                                <?php if($this->session->flashdata('success')){ ?>
                                    <div class="content messages half-width">
                                        <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                                    </div>
                                <?php }?>
                                <div id="actions">
                                    <?php
                                    $attributes = array('id' => 'form');
                                    echo form_open('email/delete',$attributes);
                                    ?>
                                        <table class="list">
                                            <thead>
                                            <tr>
                                                <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                                                <td class="left"><?php echo $this->lang->line('column_name'); ?></td>
                                                <td class="left" ><?php echo $this->lang->line('column_body'); ?></td>
                                                <td class="left" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                                                <td class="right" style="width:100px;"><?php echo $this->lang->line('column_sort_order'); ?></td>
                                                <td class="right" style="width:100px;"><?php echo $this->lang->line('column_action'); ?></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($templates) && $templates) { ?>
                                                <?php foreach ($templates as $email) { ?>
                                                <tr <?php if (isset($email["is_template"]) && $email["is_template"]) {?> class="email_template" <?php } ?>>
                                                    <td style="text-align: center;">
                                                        <?php if ($email['selected']) { ?>
                                                            <input type="checkbox" name="selected[]" value="<?php echo $email['_id']; ?>" checked="checked" />
                                                        <?php } else { ?>
                                                            <input type="checkbox" name="selected[]" value="<?php echo $email['_id']; ?>" />
                                                        <?php } ?>
                                                    </td>
                                                    <td class="left"><?php echo $email['name']; ?></td>
                                                    <td class="left"><?php echo $email['body']; ?>  </td>
                                                    <td class="left"><?php echo ($email['status'])? "Enabled" : "Disabled"; ?></td>
                                                    <td class="right"><?php echo $email['sort_order']; ?></td>
                                                    <td class="right">

                                                        <?php echo anchor('email/update/'.$email['_id'], "<i class='fa fa-edit fa-lg''></i>",
                                                                 array('class'=>'tooltips',
                                                                     'title' => 'Edit',
                                                                     'data-placement' => 'top'
                                                                 ));
                                                        ?>
                                                        <?php echo anchor('email/increase_order/'.$email['_id'], '<i class="icon-chevron-down icon-large"></i>', array('class'=>'push_down', 'alt'=>$email['_id'], 'style'=>'text-decoration:none'));?>
                                                        <?php echo anchor('email/decrease_order/'.$email['_id'], '<i class="icon-chevron-up icon-large"></i>', array('class'=>'push_up', 'alt'=>$email['_id'], 'style'=>'text-decoration:none' ));?>
                                                    </td>
                                                </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                <tr>
                                                    <td class="center" colspan="6"><?php echo $this->lang->line('text_no_results'); ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php
                                    echo form_close();?>
                                </div><!-- #actions -->
                                <div class="pagination">
                                    <ul class='ul_rule_pagination_container'>
                                        <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                                        <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                                        <?php echo $pagination_links; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="tabDomain">
                        <div class="container-fluid">
                            <div class="row-fluid">
                                <div id="domain">
                                    <div class="buttons">
                                        <button class="btn btn-info " id="edit-domain-button" onclick="edit_email_domain();" type="button"><i class="fa fa-edit"></i> <?php echo $this->lang->line('button_edit'); ?></button>
                                        <button class="btn btn-info " id="default-domain-button" onclick="$('#setDefaultModal').modal({'backdrop': true});" type="button"><i class="fa fa-gears"></i> <?php echo $this->lang->line('button_default'); ?></button>
                                        <button class="btn btn-info hidden" id="save-domain-button" onclick="save_email_domain();" type="button"><i class="fa fa-save"></i> <?php echo $this->lang->line('button_save'); ?></button>
                                        <button class="btn btn-info hidden" id="cancel-domain-button" onclick="cancel_email_domain();" type="button"> <?php echo $this->lang->line('button_cancel'); ?></button><br><br>
                                    </div>
                                    <table class="form">
                                        <tr>
                                            <td><?php echo $this->lang->line('entry_email') ?>:</td>
                                            <td>
                                                <label id="email-label" >
                                                    <?php echo (isset($domain["email"]) && $domain["email"]) ? $domain["email"] : ""; ?>
                                                </label>
                                                <div id="div-domain-edit" class="hidden">
                                                    <input type="text" name="email-input" id="email-input"/>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $this->lang->line('entry_status') ?>:</td>
                                            <td>
                                                <label id="status-label" >
                                                    <?php echo (isset($domain["verification_status"]) && $domain["verification_status"]) ? $domain["verification_status"] : ""; ?>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

<!-- Set default Modal -->
<div id="setDefaultModal" class="modal hide fade"   tabindex="-1" role="dialog" aria-labelledby="setDefaultModalLabel"  aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="setDefaultLabel">Warning !</h3>
    </div>
    <div class="modal-body red">
        <p>Please confirm to set the domain as default</p>
        <p>All the email will be sent to player by Playbasis's email</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary"  onclick='confirm_set_default();'>Confirm</button>

        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

    </div>
</div>

<style type="text/css">
    .modal {
        width: 40%;
        margin-left:-20%;
    }
</style>

<script type="text/javascript">

    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined)
                $('#errorModal').find('#myModalLabel').html(title);
            $('#errorModal').modal({'backdrop': true});
            $('#errorModal .modal-body').html(msg);
        }
    }

    var progressDialog = (function($){
        var obj = {};
        obj.show = function(text){

            $('body').prepend('<div class="custom_blackdrop"><img src="./image/white_loading.gif" /><br><span>'+text+'</span></div>');
        }

        obj.hide = function(){

            setTimeout(function(){
                $('.custom_blackdrop').remove();
            },1000)
        }

        return obj;
    }(jQuery))

    function edit_email_domain(){

        $('#domain').find('#email-input').val(document.getElementById('email-label').innerText);
        $('#domain').find('#email-label').hide();
        $('#domain').find('#div-domain-edit').removeClass("hidden");
        $('#domain').find('#edit-domain-button').addClass("hidden");
        $('#domain').find('#default-domain-button').addClass("hidden");
        $('#domain').find('#save-domain-button').removeClass("hidden");
        $('#domain').find('#cancel-domain-button').removeClass("hidden");
    }

    function save_email_domain(){

        var email = $('#domain').find('#email-input').val();
        var result_status = false;
        var dialogMsg = "";

        $.ajax({
            url: baseUrlPath+'email/setDomain',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>','email': email},
            type:'POST',
            // dataType:'json',
            beforeSend:function(){
                progressDialog.show('Setting domain ...');
            },
            success:function(data){
                //console.log('on success')
                //console.log(data);

                if(($.parseJSON(data).status=='success')){

                    result_status = true;
                    if($.parseJSON(data).data['email_sent'] != false){
                        dialogMsg = "The domain need to be verified." +
                            "<br>We have sent the verification token to your email." +
                            "<br>The token must be placed in the DNS settings for the domain in order to complete domain verification." +
                            "<br><br>Note: ".bold()+"All the email will be sent to player by "+$.parseJSON(data).data['email_sent'].fontcolor( 'DB6A6A' ).bold()+" until your domain is successfully verified";
                    }else {
                        dialogMsg = "The domain has already been set." +
                            "<br>All the email will be sent to player by "+$.parseJSON(data).data['email'].fontcolor( 'DB6A6A' ).bold();
                    }
                    document.getElementById('email-label').innerHTML = $.parseJSON(data).data['email'];
                    document.getElementById('status-label').innerHTML = $.parseJSON(data).data['status'];
                }
                else {
                    dialogMsg = $.parseJSON(data).message;
                }
            },
            error:function(){
                //console.log('on error')
                dialogMsg = 'Unable to set the domain,\n Please try again later.';

            },
            complete:function(){
                //console.log('on complete')
                progressDialog.hide();

                if(result_status){
                    $('#domain').find('#email-label').show();
                    $('#domain').find('#div-domain-edit').addClass("hidden");
                    $('#domain').find('#edit-domain-button').removeClass("hidden");
                    $('#domain').find('#default-domain-button').removeClass("hidden");
                    $('#domain').find('#save-domain-button').addClass("hidden");
                    $('#domain').find('#cancel-domain-button').addClass("hidden");
                    preventUnusual.message(dialogMsg.fontcolor( '010040' ), "Success !");

                }else{
                    preventUnusual.message(dialogMsg, "Error !!!");
                }
            }
        });
    }

    function cancel_email_domain(){

        $('#domain').find('#email-label').show();
        $('#domain').find('#edit-domain-button').removeClass("hidden");
        $('#domain').find('#default-domain-button').removeClass("hidden");
        $('#domain').find('#div-domain-edit').addClass("hidden");
        $('#domain').find('#save-domain-button').addClass("hidden");
        $('#domain').find('#cancel-domain-button').addClass("hidden");
    }

    function confirm_set_default(){

        var result_status = false;
        var dialogMsg = "";
        $.ajax({
            url: baseUrlPath+'email/setDefaultDomain',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
            type:'POST',
            // dataType:'json',
            beforeSend:function(){
                $('#setDefaultModal').modal('hide');
                progressDialog.show('Setting default domain ...');
            },
            success:function(data){
                //console.log('on success')
                //console.log(data);

                if(($.parseJSON(data).status=='success')){

                    result_status = true;

                    dialogMsg = "The domain has been set to default." +
                        "<br>All the email will be sent to player by "+$.parseJSON(data).data['email'].fontcolor( 'DB6A6A' ).bold();

                    document.getElementById('email-label').innerHTML = $.parseJSON(data).data['email'];
                    document.getElementById('status-label').innerHTML = $.parseJSON(data).data['status'];
                }
                else {
                    dialogMsg = $.parseJSON(data).message;
                }
            },
            error:function(){
                //console.log('on error')
                dialogMsg = 'Unable to set the default domain,\n Please try again later.';

            },
            complete:function(){
                //console.log('on complete')
                progressDialog.hide();

                if(result_status){

                    preventUnusual.message(dialogMsg.fontcolor( '010040' ), "Success !");

                }else{
                    preventUnusual.message(dialogMsg, "Error !!!");
                }
            }
        });
    }

</script>

<script type="text/javascript">

$('.push_down').live("click", function(){

    $.ajax({
        url : baseUrlPath+'email/increase_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'email/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;

});
</script>


<script type="text/javascript">
$('.push_up').live("click", function(){
    $.ajax({
        url : baseUrlPath+'email/decrease_order/'+ $(this).attr('alt'),
        dataType: "json"
    }).done(function(data) {
        console.log("Testing");
        var getListForAjax = 'email/getListForAjax/';
        var getNum = '<?php echo $this->uri->segment(3);?>';
        if(!getNum){
            getNum = 0;
        }
        $('#actions').load(baseUrlPath+getListForAjax+getNum);
    });


  return false;
});

</script>

<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/blackdrop/blackdrop.css" />
