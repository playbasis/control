<div id="content" class="signup-page-wrapper">
	
		<div class="signup-page">
			<div class="container page" data-page="signup">
				<div class="text-center">
					<p class="lead">
				        	Join our developer program create an account and start to use gamification.
				        </p>
				</div>
			</div>

			<div class="row-fluid">
			      <div class="offset2 span8 well">
                      <?php
                      $chosenPlan = null;;
                      if(isset($_GET['plan'])){
                          $chosenPlan = $_GET['plan'];
                      }
                      if(isset($temp_fields['plan'])){
                          $chosenPlan = $temp_fields['plan'];
                      }
                      ?>
                      <?php if(in_array($chosenPlan, $availablePlans)){ ?>
                          <input type = 'hidden' value = '<?php echo $chosenPlan; ?>' name = 'plan'/>
                      <?php }else{ ?>
                          <?php //echo "STOP, WHAT ARE YOU DOING?"; ?>
                          <?php  header( 'Location: http://www.playbasis.com/plans.html' ) ;  ?>
                          <?php exit(); ?>
                      <?php } ?>
			        <form class="validate" role="form" action="" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="" novalidate>
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

			                <script type="text/javascript"
			                src="//www.google.com/recaptcha/api/challenge?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>">
			                </script>
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
			                    <button class="btn btn-not-login" type="button" onclick="javascript:Recaptcha.reload()" id="captcha_button"><span class="fa fa-refresh btn-not-login"></span></button>
			                    <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
			                    <span class="recaptcha_only_if_image btn-not-login"></span>
			                    <span class="recaptcha_only_if_audio btn-not-login"></span>
			                    <br/>
			                    <div class="input-append">
			                        <input class="form-control btn-not-login" type="text" id="recaptcha_response_field" name="recaptcha_response_field" style="color:black;" placeholder="Enter what you see"/>
			                    </div>

			                </div>

			                <script type="text/javascript"
			                src="//www.google.com/recaptcha/api/challenge?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>">
			                </script>
			                <noscript>
			                    <iframe src="//www.google.com/recaptcha/api/noscript?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>"
			                    height="300" width="500" frameborder="0"></iframe><br>
			                    <textarea name="recaptcha_challenge_field" rows="3" cols="40" class="btn-not-login"></textarea>
			                    <input type="hidden" name="recaptcha_response_field"  value="manual_challenge" class="btn-not-login">
			                </noscript>

			            </div>

			            <div class="form-group span12">
                        		<input type = 'hidden' value = 'new' name = 'version'/>
                        		<input type = 'hidden' value = '<?php echo $chosenPlan; ?>' name = 'plan'/>
						<button type="submit" class="btn btn-primary offset4 span4 btn-not-login">Sign Up</button>
					</div>

			      </form>
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

        
    }

});

</script>


