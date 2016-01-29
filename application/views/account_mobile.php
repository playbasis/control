<div class="activated-wrapper account-verify-wrapper">
	<div class="activated-content modal-card">
		<div class="modal-card-head" >
			<h3>Please verify Your Account</h3>
			<img src="<?php echo base_url();?>image/beforelogin/header-account-verify.png">
		</div>
		<div class="modal-card-content" >
			<!-- <h3>Please verify your account</h3> -->
			<p>Provide your mobile phone number <br>and receive the validation code by SMS.</p>
			<?php
			$attributes = array('class' => 'form', 'id' => 'form_phonenumber');
			echo form_open('', $attributes);?>
				<div class="form-group input-group">
					<input id="phone" type="tel"  class="form-control phone-number" >
					<button type="submit" class="btn btn-primary">Request Code </button>
				</div>
				<div class="error" style="display:none"></div>
			<?php echo form_close();?>
			
			<hr>
			<p>
				Enter the code received in the field below.
				<?php
				$attributes = array('class' => 'form', 'id' => 'form_verify','method' => 'POST');
				echo form_open('', $attributes);?>
					<div class="form-group input-group">
						
						<input type="text" class="form-control code-input" name="code"><button type="submit" class="btn btn-primary">OK <i class="fa fa-arrow-right"></i> </button>
					</div>
					<div class="error" style="display:block">
						<?php echo isset($message) ? $message : '' ; ?>
					</div>
				<?php echo form_close();?>
			</p>
			<hr>
			<p>
				No SMS received? Please contact <a href="mailto:support@playbasis.com">support@playbasis.com</a>
				<small>The validation code will expire after 30 minutes. <br>Please request a new one if needed.</small>
			</p>
		</div>
	</div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>javascript/intlTelInput/css/intlTelInput.css">
<script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>javascript/intlTelInput/js/intlTelInput.js"></script>
<script type="text/javascript">
//baseUrlPath
	$("#phone").intlTelInput({
		// defaultCountry: "auto",
		nationalMode: false,
	  	utilsScript: "<?php echo base_url();?>javascript/intlTelInput/js/utils.js"
	});
	$(document).ready(function(){

		$('#form_phonenumber').submit(function(e){
			e.preventDefault();

			$('#form_phonenumber .error').slideUp().text('');
			$('#form_phonenumber button').attr('disabled', true);
			$('.phone-number').attr('disabled', true);
			

		    	var user_phone_number = $('.phone-number').val();

		    	// var filter = /\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d| 2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]| 4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/;

		    	// if( user_phone_number == null || user_phone_number == '' || !filter.test(user_phone_number) ){
		        // alert(user_phone_number);
		        // $('#form_phonenumber .error').text('Phone number invalid').slideDown();
		        // $('#form_phonenumber button').attr('disabled', false);
		        // $('.phone-number').attr('disabled', false);
			// }
		    
		    	  
		    	  $('#form_phonenumber button').text('sending...');

		        $.ajax({
		            type:"POST",
		            url: baseUrlPath+'account/request_code',
		            dataType: 'json',
		            data:{
						'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>',
		                phone_number: user_phone_number
		            },
		            success:function(data){
		                console.log(data);
		                if( data.status == 'success' ){
		                		setTimeout(function(){
		                			$('.phone-number').attr('disabled', false);
		                			$('#form_phonenumber button').text('Resend Code').attr('disabled', false);	
		                		},5000);
		                }else{
		                	if( data.message ){
		                		$('#form_phonenumber .error').text(data.message).slideDown();
		                	}else{
		                		$('#form_phonenumber .error').text('Phone number invalid').slideDown();
		                	}
		                	$('.phone-number').attr('disabled', false);
		                	$('#form_phonenumber button').text('Resend Code').attr('disabled', false);	
		                }
		            },
                    error:function(jqXHR, err, errorThrown){
                        $('.phone-number').attr('disabled', false);
                        $('#form_phonenumber button').text('Resend Code').attr('disabled', false);
                        alert('Please contact support@playbasis.com with following message: status = ' + jqXHR.status + ', response = ' + jqXHR.responseText);
                    }
		        });
		    
		});


		// $('#form_verify').submit(function(e){
		// 	e.preventDefault();

		// 	$('#form_verify .error').slideUp().text('');
		// 	$('#form_verify button').attr('disabled', true);
		// 	$('.code-input').attr('disabled', true);

		// 	var code = $('.code-input').val();

		// 	if( code == null || code == '' ){
		// 		$('#form_verify .error').text('Code invalid').slideDown();
		// 		$('#form_verify button').attr('disabled', false);
		// 		$('.code-input').attr('disabled', false);
		// 	}else{
		// 		$('#form_verify button').text('checking...');

		// 		    $.ajax({
		// 		        type:"GET",
		// 		        url: 'http://localhost',
		// 		        // dataType: 'json',
		// 		        data:{
		// 		            code: code
		// 		        },
		// 		        success:function(data){
		// 		            console.log(data);
		// 		            $('.code-input').attr('disabled', false);
		// 		            $('#form_verify button').html('OK <i class="fa fa-arrow-right"></i>').attr('disabled', false);	
		// 		        }
		// 		    });
		// 	}
		// });
	})
</script>
