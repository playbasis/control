
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
          Create a password
        </h1>
      </div>
    </header><!-- /header -->

    <main class="pbr-main" role="main">
      <div class="row">
        <div class="registration-page registration-completeprofile">
          <div class="registration-form-bg"></div>
          <div class="registration-form">
              <?php
              $attributes = array('class' => 'pbf-form', 'id' => 'completeprofile_form');
              echo form_open('', $attributes);?>
              <fieldset>

                <legend>Your information</legend>

                 <div class="pbf-field-group">
                   <label for="password">Password <span>*</span></label>
                   <input type="password" name="password" placeholder="Password must be more than 8 character" id="completeprofile-password" required>
                 </div>

                <div class="pbf-field-group">
                   <label for="confirm-password">Confirm Password <span>*</span></label>
                   <input type="password" name="confirm_password" placeholder="Confirm Password" id="completeprofile-confirm-password" required>
                 </div>

              </fieldset>

              <hr>

              <fieldset>

                <div class="pbf-field-group pbf-half ">
                  <input type="submit" value="Complete Information">
                </div>

              </fieldset>
              <?php echo form_close();?>

          </div><!-- /form -->

          <div class="registration-benefits">

            <div class="benefits-block">
              <h4>Get ready to gamify your application</h4>
              <p>After you completed setting up your profile, you have to set up your application in our dashboard as first step. You will be able to create and set up ‘1’ application in our dashboard for free plan. Upgrade to higher plan to connect more applications! </p>
            </div>


          </div><!-- /benefits -->

        </div><!-- /registration -->


      </div>
    </main><!-- /main -->