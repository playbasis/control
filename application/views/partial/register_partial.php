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
      Sign up for Playbasis
    </h1>

    <!-- <div class="plan">
    <div class="plan-type">Professional Plan</div>
    <div class="plan-price">$99 / month</div>
    </div> -->

  </div>
</header><!-- /header -->

<main class="pbr-main" role="main">
  <div class="row">
    <div  class="registration-page registration-register">
      <div class="registration-form-bg"></div>
      <div class="registration-form">
        <?php
        $attributes = array('class' => 'pbf-form', 'id' => 'register_form');
        echo form_open('', $attributes);?>
          <fieldset>

            <legend>Basic User Information</legend>

            <div class="pbf-field-group pbf-half">
              <label for="first-name">First Name <span>*</span></label>
              <input type="text" name="firstname" placeholder="First Name" id="first-name" required>
            </div>

           <div class="pbf-field-group pbf-half">
              <label for="last-name">Last Name <span>*</span></label>
              <input type="text" name="lastname" placeholder="Last Name" id="last-name" required>
            </div>

            <div class="pbf-field-group">
              <label for="email-address">Email Address <span>*</span></label>
              <input type="email" name="email" placeholder="Email Address" id="email-address" required>
            </div>

           <div class="pbf-field-group">
              <label for="company-name">Company Name</label>
              <input type="text" name="company_name" placeholder="Company Name" id="company-name">
            </div>

          </fieldset>

          <hr>

          <fieldset>

            <div class="pbf-field-group pbf-half">
              <input type="submit" value="Take me to my dashboard">
            </div>

            <div class="pbf-field-group pbf-half pbf-link-2-line">
              <input type="checkbox" name="mailing-list" id="mailing-list" checked>
              <label for="mailing-list">Signup for our mailing list.</label><br>
              <span>Already have an account? <a href="#login" role="tab" data-toggle="tab">Login</a></span>
            </div>

          </fieldset>
        <?php echo form_close();?>

      </div><!-- /form -->

      <div class="registration-benefits">

        <div class="benefits-block">
          <h4>First 30 days free with every plan</h4>
          <p>We're so confident you'll love our service that all our plans come with the first 30 days free. Come have a look around.</p>
        </div>

        <div class="benefits-block">
          <h4>Quick and easy to setup</h4>
          <p>Get up and running quickly with preset rules, or tailor your rules to create a custom framework that aligns with your key objectives.</p>
        </div>

      </div><!-- /benefits -->

    </div><!-- /registration -->

    <div class="registration-policy">By clicking on "Take me to my dashboard", you agree to the <a href="http://www.playbasis.com/terms-of-service.html">Terms of Service</a> and the <a href="http://www.playbasis.com/privacy.html">Privacy Policy</a></div>

  </div>
</main><!-- /main -->