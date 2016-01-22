<header class="pbr-header" role="banner">
  <div class="row" role="navigation">

              <a href="http://www.playbasis.com">
                <svg title="Playbasis" class="pbr-header-logo">
                  <use xlink:href="<?php echo base_url();?>image/beforelogin/logo.svg#logo"></use>
                </svg>
              </a>

  </div>

  <div class="row">

    <h1>
      <small>Get started with Gamification today!</small>
      Reset Password
    </h1>

  </div>
</header><!-- /header -->

<main class="pbr-main" role="main">
  <div class="row">
    <div class="registration-page registration-resetpassword">
      <div class="registration-form-bg"></div>
      <div class="registration-form">
        <?php
        $attributes = array('class' => 'pbf-form', 'id' => 'resetpassword_form');
        echo form_open('', $attributes);?>
          <fieldset>

            <legend>Reset Password</legend>

            <div class="pbf-field-group">
              <label for="password">Password <span>*</span></label>
              <input type="password" name="password" placeholder="Password must be more than 8 character" id="reset-password" required>
            </div>

           <div class="pbf-field-group">
              <label for="confirm-password">Confirm Password <span>*</span></label>
              <input type="password" name="confirm_password" placeholder="Confirm Password" id="reset-confirm-password" required>
            </div>


          </fieldset>

          <hr>

          <fieldset>

            <div class="pbf-field-group pbf-half">
              <input type="submit" value="Change Password">
            </div>

          </fieldset>
        <?php echo form_close();?>

      </div><!-- /form -->

      <div class="registration-benefits">

        <div class="benefits-block">
          <h4>Ensure security of your account</h4>
          <p>Choose a new password for your account. This new password will replace the old one; everything else about your account will remain unchanged. We recommend you to change your password regularly for security.</p>
        </div>


      </div><!-- /benefits -->

    </div><!-- /registration -->


  </div>
</main><!-- /main -->