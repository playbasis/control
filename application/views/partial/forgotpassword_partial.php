
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
          Forgot Password
        </h1>
      </div>
    </header><!-- /header -->

    <main class="pbr-main" role="main">
      <div class="row">
        <div class="registration-page registration-forgotpassword">
          <div class="registration-form-bg"></div>
          <div class="registration-form">
          <?php
          $attributes = array('class' => 'pbf-form', 'id' => 'forgotpassword_form');
          echo form_open('', $attributes);?>
              <fieldset>

                <legend>Please provide your email<br><small>We will send you a link via email to reset your password.</small></legend>



                <div class="pbf-field-group">
                  <label for="email-address">Email Address <span>*</span></label>
                  <input type="email" name="email" placeholder="Email Address" id="email-address" required>
                </div>


              </fieldset>

              <hr>

              <fieldset>

                <div class="pbf-field-group pbf-half ">
                  <input type="submit" value="Reset my password">
                </div>

                <div class="pbf-field-group pbf-half pbf-checked">
                    <a href="#login" role="tab" data-toggle="tab">Cancel</a>
                </div>

              </fieldset>
              <?php echo form_close();?>

          </div><!-- /form -->

          <div class="registration-benefits">

            <div class="benefits-block">
              <h4>Password Recovery</h4>
              <p>If you have forgotten your password of Playbasis gamification platform, you can request an e-mail with a link to reset your password. If you don’t get e-mail from us within [XXXXX], please be sure to check in both mail box and junk mail.</p>
            </div>

          </div><!-- /benefits -->

        </div><!-- /registration -->

      </div>
    </main><!-- /main -->