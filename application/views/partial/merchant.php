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
        <?php
        $attributes = array('class' => 'pbf-form', 'id' => 'coupon_form');
        echo form_open('', $attributes);?>
          <fieldset>

            <legend>Coupon Validation</legend>

            <div class="pbf-field-group pbf-half">
              <label for="group">Goods <span>*</span></label>
              <select name="group">
                <?php foreach ($group_list as $group) { ?>
                <option value="<?php echo $group; ?>"><?php echo $group; ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="pbf-field-group pbf-half">
              <label for="coupon">Coupon <span>*</span></label>
              <input type="text" name="coupon" placeholder="Coupon" id="coupon" required>
            </div>

            <div class="pbf-field-group">
              <label for="merchant-name">Merchant Name</label>
              <input type="text" placeholder="Merchant Name" value="<?php echo $merchant; ?>" id="merchant-name" readonly>
            </div>

            <div class="pbf-field-group">
              <label for="branch-name">Branch Name</label>
              <input type="text" placeholder="Branch Name" value="<?php echo $branch; ?>" id="branch-name" readonly>
            </div>

          </fieldset>

          <hr>

          <fieldset>

            <div class="pbf-field-group pbf-half">
              <input type="submit" value="Validate">
            </div>

            <div class="pbf-field-group pbf-half pbf-link-2-line">
              <input type="checkbox" name="mark" id="mark-used">
              <label for="mark-used">Mark as used if valid</label><br>
              <span id="merchant-logout"><a href="#" role="tab" data-toggle="tab">Logout?</a></span>
            </div>

          </fieldset>
        <?php echo form_close();?>

      </div><!-- /form -->

  </div>
</main><!-- /main -->