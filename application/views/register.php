<?php define('PLAYBASIS', 'http://www.playbasis.com'); ?>

<div id="content" class="signup-page-wrapper">

		<div class="signup-page">
			<div class="container page" data-page="signup">
				<div class="text-center">
					<img class="register-hero-img" src="<?php echo base_url(); ?>image/register/hero-img.png">
				</div>
			</div>

			<div class="row-fluid">
				
			      <div class="offset2 span8 well">
			      <div class="span12 signup-header">
					<h3><strong><?php echo $plan['name']; ?> : ( <?php echo $plan['price'] <= 0 ? 'FREE' : '$'.$plan['price'].' /'; ?> )</strong> You can cancel or upgrade at any time.</h3>
					<a href="<?php echo PLAYBASIS; ?>/plans.html" class="btn btn-primary pull-right">Change Plan</a>
				</div>
					  <?php
					  $attributes = array('class' => 'validate', 'role' => 'form', 'action' => '', 'method' => 'post', 'id' => 'mc-embedded-subscribe-form', 'name' => 'mc-embedded-subscribe-form',
							  'target' => '', 'novalidate');
					  echo form_open('', $attributes);?>
			      	<div id="message"></div>

			          <div class="form-group offset2 span8">
			            <label class="sr-only" for="email">Email Address</label>
			            <input type="email" class="form-control btn-not-login" id="email" name="email" placeholder="Email Address" required data-validation="email">
			            <span class="fa fa-times form-control-feedback btn-not-login"></span>
			          </div>

			            <div class="form-group offset2 span8">
			                <label class="sr-only" for="firstname">Firstname</label>
			                <input type="text" class="form-control btn-not-login" id="firstname" name="firstname" placeholder="Firstname" required minlength="2" data-validation="required">
			                <span class="fa fa-times form-control-feedback btn-not-login"></span>
			            </div>

			            <div class="form-group offset2 span8">
			                <label class="sr-only" for="lastname">Lastname</label>
			                <input type="text" class="form-control btn-not-login" id="lastname" name="lastname" placeholder="Lastname" required minlength="2" data-validation="required">
			                <span class="fa fa-times form-control-feedback btn-not-login"></span>
			            </div>

			            <div class="form-group offset2 span8">
			                <label class="sr-only" for="company_name">Company Name</label>
			                <input type="text" class="form-control btn-not-login" id="company_name" name="company_name" placeholder="Company Name" required minlength="2" data-validation="required">
			                <span class="fa fa-times form-control-feedback btn-not-login"></span>
			            </div>

			            <div class="form-group offset2 span8">
			                <label class="sr-only" for="domain_name">Domain (URL)</label>
			                <input type="text" class="form-control btn-not-login" id="domain_name" name="domain_name" placeholder="Domain (URL)" data-validation="custom" data-validation-regexp="[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)">
			                <span class="fa fa-times form-control-feedback btn-not-login"></span>
			            </div>

			            <div class="form-group offset2 span8">
			            <!--
			                <script type="text/javascript"> 
			                    var RecaptchaOptions = {
			                        theme : 'custom',
			                        custom_theme_widget: 'recaptcha_widget'
			                    };
			                </script>
			                <div id="recaptcha_widget" style="display:none;">

			                    <div id="recaptcha_image" style="margin:0 auto"></div>
			                    <button class="btn btn-not-login hide" onclick="javascript:Recaptcha.reload()" id="captcha_button">
                                    <i class="fa fa-refresh btn-not-login"></i>
                                </button>
			                    <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
			                    <span class="recaptcha_only_if_image btn-not-login"></span>
			                    <span class="recaptcha_only_if_audio btn-not-login"></span>
			                    <br/>
			                    <div class="recaptcha-wrapper">
			                        <input class="form-control btn-not-login" type="text" id="recaptcha_response_field" name="recaptcha_response_field" style="color:black;" placeholder="Enter what you see"/>
			                        <input type="hidden" name="recaptcha_response_field" value="manual_challenge"><button class="btn btn-not-login recaptcha-reload-btn" onclick="javascript:Recaptcha.reload()"><i class="icon-refresh"></i></button>
			                    </div>
			                </div>

			                <script type="text/javascript" src="//www.google.com/recaptcha/api/challenge?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>"></script>
			                <noscript>
			                    <iframe src="//www.google.com/recaptcha/api/noscript?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>"
			                    height="300" width="500" frameborder="0"></iframe><br>
			                    <textarea name="recaptcha_challenge_field" class="btn-not-login" rows="3" cols="40"></textarea>
			                </noscript>-->

			                <script type="text/javascript"> 
			                    var RecaptchaOptions = {
			                        theme : 'custom',
			                        custom_theme_widget: 'recaptcha_widget'
			                    };
			                </script>
			                <div id="recaptcha_widget" style="display:none;">

			                    <div id="recaptcha_image" style="margin:0 auto"></div>
			                    
			                    <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
			                    <span class="recaptcha_only_if_image btn-not-login"></span>
			                    <span class="recaptcha_only_if_audio btn-not-login"></span>
			                    <br/>
			                    <div class="form-recaptcha-wrapper">
			                        <input class="form-control btn-not-login" type="text" id="recaptcha_response_field" name="recaptcha_response_field" style="color:black;" placeholder="Enter what you see"/>

			                        <button class="btn btn-not-login recaptcha-reload-btn" type="button" onclick="javascript:Recaptcha.reload()" id="captcha_button"><span class="fa fa-refresh btn-not-login"></span></button>
			                    </div>
			                </div>

			                <script type="text/javascript" src="//www.google.com/recaptcha/api/challenge?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>"></script>
			                <noscript>
			                    <iframe src="//www.google.com/recaptcha/api/noscript?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>"
			                    height="300" width="500" frameborder="0"></iframe><br>
			                    <textarea name="recaptcha_challenge_field" rows="3" cols="40" class="btn-not-login"></textarea>
			                    <input type="hidden" name="recaptcha_response_field"  value="manual_challenge" class="btn-not-login">
			                </noscript>

			            </div>
			            <div class="form-group span12 text-center">
			            	<small>By clicking on "Sign Up" below, you agree to the <a href="<?php echo PLAYBASIS; ?>/privacy.html" target="_blank">Terms of Service</a> and the <a href="<?php echo PLAYBASIS; ?>/terms-of-service.html" target="_blank">Privacy Policy</a></small>
			            </div>
			            <div class="form-group span12">
                        		<input type = 'hidden' value = 'new' name = 'version'/>
						<button type="submit" class="btn btn-primary offset4 span4 btn-not-login">Sign Up</button>
					</div>

				<?php echo form_close();?>
		      </div>
		</div>
	</div><!-- signup-page -->
</div><!-- signup-page-wrapper -->

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.47/jquery.form-validator.min.js"></script>

<script type="text/javascript">
var $form = $('#mc-embedded-subscribe-form');

// Validate form
$.validate({
    form: '#mc-embedded-subscribe-form',
    borderColorOnError: '',
    onError : function() {
        $("#message").html('<p class="has-warning text-center lead">Error connecting to server.</p>');
    },
    onSuccess: function() {
    	/*var data = $form.serializeArray();
        data.push({name: 'internal', value: 'playbasis'});
        console.log(data);*/

        var data = $form.serialize()+"&format=json";

        $.ajax({
            url: "<?php echo current_url(); ?>?plan=<?php echo $_GET['plan']; ?>",
            type: "POST",
            data: data,
            dataType: 'json',
            cache: false,
            crossDomain:true,
            beforeSend: function( data ) {
                $("#message").html('<p class="text-center">Sending : <img alt="loading" src="image/register/loading.gif" /></p>');
                $("button.btn-primary").attr('disabled', 'disabled');
            },
            error: function() {
                $("#message").html('<p class="has-warning text-center lead">Error connecting to server.</p>');
                $("button.btn-primary").removeAttr("disabled");
            },
            success: function(data) {
                if (data.response == 'success') {
                    $form.replaceWith('<p class="has-success text-center lead">Thank you for registering! Please check your email to activate your account.</p>');
                } else {
                    $('#captcha_button').click();
                    $("#message").html('<p class="has-error text-center lead">'+data+'</p>');
                    $("button.btn-primary").removeAttr("disabled");
                }
            }
        });

        return false; // Will stop the submission of the form
    }

});

</script>
