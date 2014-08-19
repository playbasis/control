<div id="content" >
	<div class = "box" style = "position: relative; max-width: 750px; margin:0 auto;">
		<div class="heading">
			<h1><img src="<?php echo base_url('image/user-group.png')?>" alt="" /><?php echo $heading_title_register;?></h1>
		</div><!-- .heading -->
		<div class="content" >
			<?php //if($this->session->flashdata('fail')){ ?>
			<?php if(isset($fail_email_exists)){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $fail_email_exists; ?></div>
                </div>
            <?php }?>
            <?php //if($this->session->flashdata('fail_domain_exists')){ ?>
            <?php if(isset($fail_domain_exists)){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $fail_domain_exists; ?></div>
                </div>
            <?php }?>
            <?php if(isset($incorrect_captcha)){?>
            	<div class="content messages half-width">
                <div class="warning"><?php echo $incorrect_captcha; ?></div>
                </div>
            <?php }?>
			<?php if(validation_errors() || isset($message)) {?>
                <div class="content messages half-width">
                    <?php echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
            <?php }?>
			<?php $attributes = array('id' => 'form');?>
			<?php echo form_open_multipart($form, $attributes);?>
				<div id="pg1">
					<table class="form">
						<!-- Chosen plan -->
						
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
							<?php echo "STOP, WHAT ARE YOU DOING?"; ?>
							<?php exit(); ?>
						<?php } ?>





						<!-- End chosen plan -->
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_email');?>: </td>
							<td><input type = "text" name="email" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['email'];}?>" class="tooltips" data-placement="right" title="Your Email address is used to log into the system"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_password');?>: </td>
							<td><input type = "password" name="password" size="50" value = ""></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_confirm_password');?>: </td>
							<td><input type = "password" name="password_confirm" size="50" value =""></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_firstname');?>: </td>
							<td><input type = "text" name="firstname" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['firstname'];}?>"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_lastname');?>: </td>
							<td><input type = "text" name="lastname" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['lastname'];}?>"></td>
						</tr>
						
						<!--<tr>
							<td><span class="required">*</span> <?php //echo $this->lang->line('form_username');?>: </td>
							<td><input type = "text" name="username" size="50" value ="<?php //if(isset($temp_fields)){echo $temp_fields['username'];}?>"></td>
						</tr>-->
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_company_name');?>:</td>
							<td><input type = "text" name="company_name" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['company_name'];}?>" class="tooltips" data-placement="right" title="Please provide your company name in full (example: Playbasis Pte. Ltd.)"></td>			
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_domain');?>:</td>
							<td><input type = "text" name="domain_name" size="50" value = "<?php if(isset($temp_fields)){echo $temp_fields['domain_name'];}?>" class="tooltips" data-placement="right" title="Your domain name (example: http://www.playbasis.com)"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_site');?>:</td>
							<td><input type = "text" name="site_name" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['site_name'];}?>" class="tooltips" data-placement="right" title="Your Site name (example: Playbasis Official Website)"></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $this->lang->line('form_captcha');?>:</td>
							<td>
								<?php //echo $recaptcha;?>	

								<script type="text/javascript">
									var RecaptchaOptions = {
										theme : 'custom',
										custom_theme_widget: 'recaptcha_widget'
									};
								</script>
								<div id="recaptcha_widget" style="display:none">

								<div id="recaptcha_image"></div>
								<div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

								<span class="recaptcha_only_if_image"></span>
								<span class="recaptcha_only_if_audio"></span>
								<br/>
								<div class="input-append">
									<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
									<button class="btn btn-not-login" type="button" onclick="javascript:Recaptcha.reload()"><span class="icon-refresh"></span></button>
<!-- 									<button class="btn" type="button" onclick="javascript:Recaptcha.switch_type('audio')"><span class="icon-volume-up"></span></button>
									<button class="btn" type="button" onclick="javascript:Recaptcha.switch_type('image')"><span class="icon-font"></span></button> -->
								</div>
								

								<!-- <div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div> -->
								<!-- <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div> -->
								<!-- <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div> -->

								</div>

								<script type="text/javascript" src="//www.google.com/recaptcha/api/challenge?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>">
								</script>
								<noscript>
								<iframe src="//www.google.com/recaptcha/api/noscript?k=<?php echo CAPTCHA_PRIVATE_KEY; ?>" height="300" width="500" frameborder="0"></iframe><br>
								<textarea name="recaptcha_challenge_field" rows="3" cols="40">
								</textarea>
								<input type="hidden" name="recaptcha_response_field"
								value="manual_challenge">
								</noscript>
							</td>
						</tr>
					</table>
				</div>
					<p style="float:left">
                        <a href="<?php echo base_url();?>" class="btn-not-login" id="cancel">Cancel</a>
                    </p>
                    <p style="float:right">
                        <a onclick="$('#form').submit();" class="button btn-not-login" id="submit">Register</a>
                    </p>
			<?php echo form_close();?>
		</div><!-- .content-->
	</div><!-- .box -->
</div><!-- #content -->