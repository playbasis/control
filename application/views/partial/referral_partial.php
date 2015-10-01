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
      Playbasis Referral Program
    </h1>

  </div>
</header><!-- /header -->

<main class="pbr-main" role="main">
  <div class="row">
    <div  class="registration-page registration-register">
      <div class="registration-form-bg"></div>
      <div class="registration-form">
        <form id="referral_form" class="pbf-form">
          <fieldset>

            <legend>New User Registration</legend>

            <div class="pbf-field-group pbf-half">
              <label for="user-name">Username <span>*</span></label>
              <input type="text" name="username" placeholder="Username" id="user-name" required>
            </div>

            <div class="pbf-field-group pbf-half">
              <label for="email-address">Email <span>*</span></label>
              <input type="email" name="email" placeholder="Email Address" id="email-address" required>
            </div>

            <div class="pbf-field-group pbf-half">
              <label for="first-name">First Name <span>*</span></label>
              <input type="text" name="firstname" placeholder="Username" id="user-name" required>
            </div>

            <div class="pbf-field-group pbf-half">
              <label for="last-name">Last Name <span>*</span></label>
              <input type="text" name="lastname" placeholder="Last Name" id="last-name" required>
            </div>

            <div class="pbf-field-group">
              <label for="company-name">Company Name</label>
              <input type="text" placeholder="Company Name" value="<?php echo $app_name; ?>" readonly>
            </div>

            <div class="pbf-field-group">
              <label for="company-name">Referral Code</label>
              <input type="text" placeholder="Referral Code" value="<?php echo $referral_code; ?>" id="referral-code" readonly>
            </div>

          </fieldset>

          <hr>

          <fieldset>

            <div class="pbf-field-group pbf-half">
              <input type="submit" value="Register">
            </div>

          </fieldset>

        </form>

      </div><!-- /form -->

      <div class="registration-benefits">

        <div class="benefits-block">
          <h4>Benefit of having referral code</h4>
          <p>Enjoy the benefit right into your inventory after your invited friends have successfully registered.</p>
        </div>

        <div class="benefits-block">
          <h4>You can also!</h4>
          <p>Start inviting your friends and earn the reward from Playbasis referral program.</p>
        </div>

      </div><!-- /benefits -->

    </div><!-- /registration -->

    <div class="registration-policy">By clicking on "Register", you agree to the <a href="http://www.playbasis.com/terms-of-service.html">Terms of Service</a> and the <a href="http://www.playbasis.com/privacy.html">Privacy Policy</a></div>

  </div>
</main><!-- /main -->