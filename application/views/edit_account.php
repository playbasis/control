<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
        <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $this->lang->line('text_edit_account'); ?></h1>
	        <div class="buttons">
	            <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
	            <button class="btn btn-info" onclick="location = baseUrlPath+'account'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
	        </div>
        </div><!-- .header -->
        <div class="content">
        <?php if($this->session->flashdata('no_changes')){ ?>
            <div class="content messages half-width">
            <div class="warning"><?php echo $this->session->flashdata('no_changes'); ?></div>
            </div>
        <?php }?>
        <?php if($this->session->flashdata('success')){ ?>
            <div class="content messages half-width">
            <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
            </div>
        <?php }?>
        <?php if(isset($success)){ ?>
            <div class="content messages half-width">
                <div class="success"><?php echo $success; ?></div>
            </div>
        <?php }?>
        <?php if(validation_errors() || isset($message)) {?>
        	<div class="content messages half-width">
        		<?php echo validation_errors("<div class='warning'>","</div>")?>
				<?php if (isset($message) && $message) {?>
					<div class="warning"><?php echo $message; ?></div>
				<?php }?>
        	</div>
        <?php }?>
        <?php $attributes = array('id' => 'form');?>
	        <?php echo form_open($form, $attributes)?>
	        	<table class="form">
	        		<tr>
	                    <td><?php echo $this->lang->line('form_firstname'); ?></td>
	                    <td><input type="text" name="firstname" size="100" value="<?php echo $user_info['firstname'];?>" /></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_lastname'); ?></td>
	                    <td><input type="text" name="lastname" size="100" value="<?php echo $user_info['lastname'];?>" /></td>
	                </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_email'); ?></td>
                        <td><?php echo $user_info['email'];?></td>
                    </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_phone_number'); ?></td>
                        <td>
                            <div id="phone_number" style="display: inline-block;"><?php echo $user_info['phone_number'] ? "+".$user_info['phone_number'] : "";?> </div>
                        <?php if ($user_info['phone_number'] && $user_info['phone_status']) {?>
                            <span id="phone_status_label" class="label label-success">activated</span>
                            <button id="edit_phone_button" type="button" class="btn btn-info" data-toggle="modal" data-target="#setupPhoneModal" ><?php echo $this->lang->line('button_edit_phone'); ?></button>
                            <button id="verify_otp_button" style="display: none;" type="button" class="btn btn-info" data-toggle="modal" data-target="#verifyOTPModal" ><?php echo $this->lang->line('button_activate_phone'); ?></button>
                            <button id="setup_phone_button" style="display: none;" type="button" class="btn btn-info" data-toggle="modal" data-target="#setupPhoneModal" ><?php echo $this->lang->line('button_setup_phone'); ?></button>
                        <?php }elseif($user_info['phone_number'] && !$user_info['phone_status']){?>
                            <span id="phone_status_label" class="label label-important">not activated</span>
                            <button id="verify_otp_button" type="button" class="btn btn-info" onclick="show_verify_otp_modal();" ><?php echo $this->lang->line('button_activate_phone'); ?></button>
                            <button id="setup_phone_button" style="display: none;" type="button" class="btn btn-info" data-toggle="modal" data-target="#setupPhoneModal" ><?php echo $this->lang->line('button_setup_phone'); ?></button>
                            <button id="edit_phone_button" style="display: none;" type="button" class="btn btn-info" data-toggle="modal" data-target="#setupPhoneModal" ><?php echo $this->lang->line('button_edit_phone'); ?></button>
                        <?php }else{?>
                            <span id="phone_status_label" class="label label-success hide">activated</span>
                            <button id="setup_phone_button" type="button" class="btn btn-info" data-toggle="modal" data-target="#setupPhoneModal" ><?php echo $this->lang->line('button_setup_phone'); ?></button>
                            <button id="edit_phone_button" style="display: none;" type="button" class="btn btn-info" data-toggle="modal" data-target="#setupPhoneModal" ><?php echo $this->lang->line('button_edit_phone'); ?></button>
                            <button id="verify_otp_button" style="display: none;" type="button" class="btn btn-info" data-toggle="modal" data-target="#verifyOTPModal" ><?php echo $this->lang->line('button_activate_phone'); ?></button>
                        <?php }?>
                        </td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_user_group'); ?></td>
	                    <td><?php echo ($user_info['user_group'])? $user_info['user_group'] : $this->lang->line('text_default_admin'); ?></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_password'); ?></td>
	                    <td><input type="password" name="password" size="100"/></td>
	                </tr>
	                <tr>
	                    <td><?php echo $this->lang->line('form_confirm_password'); ?></td>
	                    <td><input type="password" name="password_confirm" size="100"/></td>
	                </tr>
	                <tr>
                        <td><?php echo $this->lang->line('form_image_profile'); ?>:</td>
                        <td valign="top"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumbprofile" class="thumbprofile" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                            <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                            <br /><a onclick="image_upload('#image', 'thumbprofile');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('.thumbprofile').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#image').attr('value', ' ');"><?php echo $this->lang->line('text_clear'); ?></a></div></td>
                    </tr>
	        	</table>
	        <?php echo form_close();?>
        </div>
    </div><!-- .box -->
</div><!-- #content .span10 -->

<!-- Request OTP modal -->
<div id="setupPhoneModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="setupPhoneLabel" aria-hidden="true" style="max-width: 800px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="setupPhoneLabel">Setup User's phone number</h3>
    </div>
    <div class="modal-body" style="height: 300px;">
        <div align="center">
            <br><label class="text-info" type="text" style="text-align: center"><h2>Enter your phone number to receive OTP for activation</h2></label><br>
            +<input type="text" maxlength="11" id="input_phone_number" placeholder="60123456789" size="100" value="" /><br><br>
            <button class="btn btn-primary" width="100px" onclick="request_otp();" >Request OTP</button>
        </div>
    </div>
    <div class="modal-footer">
        <div>
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" >Close</button>

        </div>
    </div>
</div>

<!-- Verify OTP modal -->
<div id="verifyOTPModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="verifyOTPLabel" aria-hidden="true" style="max-width: 800px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="verifyOTPLabel">Verify OTP to activate the phone number</h3>
    </div>
    <div class="modal-body" style="height: 300px;">
        <div align="center">
            <br><label id="verify_phone_label" class="text-info" type="text" style="text-align: center"><h2>Enter OTP sent to your phone number :\n</h2></label><br>
            <input type="text" maxlength="6" id="input_otp_number" size="100" value="" /><br><br>
            <button class="btn btn-primary" width="100px" onclick="verify_otp();" >Verify OTP</button>

        </div>
    </div>
    <div class="modal-footer">
        <div>
            <button class="btn btn-primary" onclick="change_number();" >Change number</button>
            <button class="btn btn-primary" onclick="resend_otp();" >Resend OTP</button>
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" >Close</button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:100000">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Warning !</h3>
    </div>
    <div class="modal-body red bold">
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

<!-- Waiting Modal -->
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

<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/blackdrop/blackdrop.css" />

<script type="text/javascript">
    preventUnusual ={
        message:function(msg,title){
            if(msg=='' || msg== undefined)return;

            if(title!='' && title!= undefined) {
                $('#errorModal').find('#myModalLabel').html(title);
            }else{
                $('#errorModal').find('#myModalLabel').html("Warning !");
            }
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
                },300)
            }

            return obj;
        }(jQuery))
</script>

<script type="text/javascript">
    function request_otp() {
        var phone_number= document.getElementById('input_phone_number').value;
        if(phone_number == ""){
            $('#setupPhoneModal').modal('hide');
            preventUnusual.message('Please enter your phone number', "Warning !!!");
        }else{
            $.ajax({
                url: baseUrlPath+'user/requestOTP',
                data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>',
                    'phone_number': phone_number},
                type:'POST',
                dataType: "json",
                beforeSend:function(){
                    $('#setupPhoneModal').modal('hide');
                    progressDialog.show('Sending OTP ...');
                },
                success:function(data){

                    if(data.status=='success'){
                        document.getElementById("phone_number").innerHTML = "+"+phone_number;
                        $('#setup_phone_button').hide();
                        $('#edit_phone_button').hide();
                        $('#verify_otp_button').show();
                        document.getElementById("verify_phone_label").innerHTML = "<h2>Enter OTP sent to your phone number : <br><br>+"+phone_number+"</h2>";
                        document.getElementById("phone_status_label").innerHTML = "not activated"
                        $('#phone_status_label').removeClass("hide label-success").addClass("label-important");
                        $('#verifyOTPModal').modal({'backdrop': true});
                    }
                    else if(data.status=='fail') {
                        var msg = data.msg;
                        preventUnusual.message(msg,'Fail!!!');

                    }else{
                        preventUnusual.message(data.msg);
                    }

                },
                error:function(){
                    //console.log('on error')
                    preventUnusual.message('There is an internal server error,\n Please try again later<br><br><br>', "Error !!!");

                },
                complete:function(){
                    //console.log('on complete')
                    progressDialog.hide();
                }
            });
        }

    }

    function verify_otp() {
        var otp_number= document.getElementById('input_otp_number').value;
        if(otp_number == ""){
            $('#verifyOTPModal').modal('hide');
            preventUnusual.message('Please enter OTP', "Warning !!!");
        }else {
            $.ajax({
                url: baseUrlPath + 'user/verifyOTP',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    'otp_number': otp_number
                },
                type: 'POST',
                dataType: "json",
                beforeSend: function () {
                    $('#verifyOTPModal').modal('hide');
                    progressDialog.show('Verifying OTP ...');
                },
                success: function (data) {

                    if (data.status == 'success') {

                        $('#successModal .modal-body').html('Your phone number is activated');
                        $('#successModal').find('#myModalLabel').html("Success !".fontcolor('12984C'));
                        $('#phone_number').show();
                        $('#verify_otp_button').hide();
                        $('#edit_phone_button').show();
                        $('#verifyOTPModal').modal('hide');
                        document.getElementById("phone_status_label").innerHTML = "activated"
                        $('#phone_status_label').removeClass("hide label-important").addClass("label-success");
                        $('#successModal').modal({'backdrop': true});
                    }
                    else if (data.status == 'fail') {
                        var msg = data.msg;
                        preventUnusual.message(msg, 'Fail!!!');

                    } else {
                        preventUnusual.message(data.msg);
                    }
                },
                error: function () {
                    //console.log('on error')
                    preventUnusual.message('Internal server error,\n Please try again later<br><br><br>', "Error !!!");

                },
                complete: function () {
                    //console.log('on complete')
                    progressDialog.hide();
                }
            });
        }
    }

    function change_number(){
        $('#verifyOTPModal').modal('hide');
        $('#setupPhoneModal').modal('show');
    }

    function resend_otp(){
        $.ajax({
            url: baseUrlPath+'user/resendOTP',
            data: {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
            type:'POST',
            dataType: "json",
            beforeSend:function(){
                $('#setupPhoneModal').modal('hide');
                progressDialog.show('Sending OTP ...');
            },
            success:function(data){

                if(data.status=='success'){
                    document.getElementById('input_otp_number').value = "";
                    $('#successModal .modal-body').html('Sent new OTP to your phone');
                    $('#successModal').find('#myModalLabel').html("Success !".fontcolor( '12984C' ));
                    $('#successModal').modal({'backdrop': true});
                }
                else if(data.status=='fail') {
                    var msg = data.msg;
                    preventUnusual.message(msg,'Fail!!!');

                }else{
                    preventUnusual.message(data.msg);
                }

            },
            error:function(){
                //console.log('on error')
                preventUnusual.message('There is an internal server error,\n Please try again later<br><br><br>', "Error !!!");

            },
            complete:function(){
                //console.log('on complete')
                progressDialog.hide();
            }
        });

    }

    function show_verify_otp_modal(){
        var phone_number = document.getElementById("phone_number").innerHTML;
        document.getElementById("verify_phone_label").innerHTML = "<h2>Enter OTP sent to your phone number : <br><br>"+phone_number+"</h2>";
        $('#verifyOTPModal').modal({'backdrop': true});
    }

    Pace.on("done", function(){
        $(".cover").fadeOut(1000);
    });
    
</script>


<script type="text/javascript"><!--
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
//--></script>