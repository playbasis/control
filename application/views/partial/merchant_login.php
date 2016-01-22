
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
          Coupon Validation for Merchants
        </h1>
      </div>
    </header><!-- /header -->

    <main class="pbr-main" role="main">
      <div class="row">
        <div class="registration-page registration-login">
          <div class="registration-form-bg"></div>
          <div class="registration-form">
              <?php
              $attributes = array('class' => 'pbf-form', 'id' => 'merchant_form');
              echo form_open('', $attributes);?>
              <fieldset>

                <legend>Merchant Login</legend>

               <div class="pbf-field-group">
                  <label for="pin">PIN <span>*</span></label>
                  <input type="password" name="pin" placeholder="Branch PIN" id="pin" required>
                </div>

              </fieldset>

              <hr>

              <fieldset>

                <div class="pbf-field-group pbf-half ">
                  <input type="submit" value="Login">
                </div>

              </fieldset>
              <?php echo form_close();?>

          </div><!-- /form -->

      </div>
    </main><!-- /main -->