<header class="pbr-header" role="banner" <?php if(isset($site_color)){ ?>style="background:<?php echo $site_color?>" <?php }?>>
    <div class="row" role="navigation">

        <?php if(isset($thumb) && !empty($thumb)) {?>
            <a><img src="<?php echo $thumb; ?>" alt="" id="thumb"/></a>
        <?php }else{ ?>
            <a href="http://www.playbasis.com">
                <svg title="Playbasis" class="pbr-header-logo">
                    <use xlink:href="<?php echo base_url(); ?>image/beforelogin/logo.svg#logo"></use>
                </svg>
            </a>
        <?php } ?>

    </div>

    <div class="row">
        <h1>
            <small>Get started with Gamification today!</small>
        </h1>
    </div>
</header><!-- /header -->

<main class="pbr-main" role="main">
    <div class="row">
        <div class="somethingwrong-page">

            <div class="somethingwrong-benefits">

                <div class="benefits-block">
                    <h4><?php echo $topic_message; ?></h4>
                    <p><?php echo $message; ?></p>
                </div>


            </div><!-- /benefits -->

        </div><!-- /registration -->


    </div>
</main><!-- /main -->