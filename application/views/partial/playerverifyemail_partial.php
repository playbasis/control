<header class="pbr-header" role="banner">
    <div class="row" role="navigation">

        <a href="http://www.playbasis.com">
            <svg title="Playbasis" class="pbr-header-logo">
                <use xlink:href="<?php echo base_url(); ?>image/beforelogin/logo.svg#logo"></use>
            </svg>
        </a>

    </div>

    <div class="row">

        <h1>
            <small>Get started with Gamification today!</small>
            Verify Email Address
        </h1>

    </div>
</header><!-- /header -->

<main class="pbr-main" role="main">
    <div class="row">
        <div class="registration-page registration-verifyemail">
            <div class="registration-form-bg"></div>
            <div class="registration-form">
                <?php
                $attributes = array('class' => 'pbf-form', 'id' => 'playerverifyemail_form');
                echo form_open('', $attributes);?>
                <fieldset>

                    <legend>Verify
                        Email <?php echo isset($player_info['cl_player_id']) && $player_info['cl_player_id'] ? "for " . $player_info['cl_player_id'] : null; ?></legend>

                    <div class="pbf-field-group">
                        <label >Your email address is</label>
                        <label > <?php echo isset($player_info['email']) && $player_info['email'] ? $player_info['email'] : null; ?></label>
                    </div>

                    <input type="hidden" value="<?php echo isset($password_recovery_code) && $password_recovery_code ? $password_recovery_code : null; ?>" id="password-recovery-code" readonly>

                </fieldset>

                <hr>

                <fieldset>

                    <div class="pbf-field-group pbf-half">
                        <input type="submit" value="Verify Email">
                    </div>

                </fieldset>
                <?php echo form_close();?>

            </div>
            <!-- /form -->

            <div class="registration-benefits">

                <div class="benefits-block">
                    <h4>Ensure security of your account</h4>

                    <p>Confirm that it is an actual email for your account. Everything else about your account will remain unchanged. </p>
                </div>


            </div>
            <!-- /benefits -->

        </div>
        <!-- /registration -->


    </div>
</main><!-- /main -->