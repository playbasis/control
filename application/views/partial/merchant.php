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
    <div  class="registration-page registration-login">
      <div class="registration-form-bg"></div>
      <div class="registration-form">
        <form id="coupon_form" class="pbf-form">
          <fieldset>

            <legend>Coupon Validation</legend>

            <div class="pbf-field-group pbf-half">
              <label for="user-name">Goods <span>*</span></label>
              <input type="text" name="goods" placeholder="Goods" id="goods" required>
            </div>

            <div class="pbf-field-group pbf-half">
              <label for="coupon">Coupon <span>*</span></label>
              <input type="text" name="coupon" placeholder="Coupon" id="coupon" required>
            </div>

          </fieldset>

          <hr>

          <fieldset>

            <div class="pbf-field-group pbf-half">
              <input type="submit" value="Validate">
            </div>

            <div class="pbf-field-group pbf-half pbf-link-2-line">
              <span id="merchant-logout"><a href="#" role="tab" data-toggle="tab">Logout?</a></span>
            </div>

          </fieldset>

        </form>

      </div><!-- /form -->

  </div>
</main><!-- /main -->