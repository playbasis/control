
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
          Login Playbasis
        </h1>
      </div>
    </header><!-- /header -->

    <main class="pbr-main" role="main">
      <div class="row">
        <div class="registration-page registration-login">
          <div class="registration-form-bg"></div>
          <div class="registration-form">
            <form id="login_form" class="pbf-form">
              <fieldset>

                <legend>Login</legend>

                <div class="pbf-field-group">
                  <label for="username">Email Address <span>*</span></label>
                  <input type="text" name="username" placeholder="Email Address" id="username-login" required>
                </div>

               <div class="pbf-field-group">
                  <label for="password-name">Password <span>*</span></label>
                  <input type="password" name="password" placeholder="Password" id="Password-login" required>
                </div>

              </fieldset>

              <hr>

              <fieldset>

                <div class="pbf-field-group pbf-half ">
                  <input type="submit" value="Login">
                </div>

                <div class="pbf-field-group pbf-half pbf-link-2-line">
                  <a href="#forgotpassword" role="tab" data-toggle="tab">Forgot Password?</a><br>
                    <?php if(!DISABLE_SIGN_UP){ ?>
                  Don't have an account? <a href="#register" role="tab" data-toggle="tab">Sign Up</a>
                    <?php } ?>
                </div>

              </fieldset>

            </form>

          </div><!-- /form -->

          <div class="registration-benefits">

            <div class="benefits-block">
              <h4>New level of engagement for your application</h4>
              <p>Playbasis platform allows you to configure your application to be more engaging. With mechanics of gamification, you can easily create fun and addicted elements with our platform.</p>
            </div>


          </div><!-- /benefits -->

        </div><!-- /registration -->


      </div>
    </main><!-- /main -->