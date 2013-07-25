<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <link rel="icon" type='image/x-icon' href="<?php echo base_url();?>image/favicon.ico">
    <meta charset="UTF-8" />
    <title><?php echo $title; ?></title>
    <base href="<?php echo base_url(); ?>" />
    <?php if (isset($description)) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if (isset($keywords)) { ?>
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/stylesheet.css" />

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery-ui-1.8.21.custom.min.js"></script>
    <!-- touch events for jquery ui -->
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.ui.touch-punch.min.js"></script>


    <script type="text/javascript" src="<?php echo base_url();?>javascript/jquery/tabs.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/jquery/superfish/js/superfish.js"></script>

    <!-- CUSTOM -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/custom/jquery-ui-1.8.21.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/custom/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/custom/style.css" />

    <link id="base-style-responsive" href="<?php echo base_url();?>stylesheet/custom/style-responsive.css" rel="stylesheet">
    <link id="base-style-responsive" href="<?php echo base_url();?>stylesheet/custom/bootstrap-responsive.css" rel="stylesheet">
    <!-- <link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/custom/override.css" /> -->

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.pie.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.stack.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.sparkline.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.flot.resize.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.knob.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/custom.js"></script>

    <!-- ISOTOPE -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/isotope/style.css" media="screen, projection"  />

    <script type="text/javascript" src="<?php echo base_url();?>javascript/isotope/jquery.isotope.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/isotope/jquery.infinitescroll.min.js"></script>

    <!-- INFOGRAPH -->
    <script type="text/javascript" src="<?php echo base_url();?>javascript/infograph/infograph.js"></script>
    <!-- /INFOGRAPH -->

    <!-- Feed -->
    <script type="text/javascript" src="<?php echo base_url();?>javascript/feed/jquery.timeago.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/feed/basic_operation.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/feed/jquery.ddslick.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/feed/jquery.mCustomScrollbar.concat.min.js"></script>

    <link href="<?php echo base_url();?>javascript/feed/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

    <link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>javascript/feed/feed.css" />

    <!-- Leader Board -->
    <link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>javascript/leaderboard/style.css" />
    <link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>javascript/leaderboard/zulazman.css" />

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>javascript/bootstrap/daterangepicker.css" />
    <script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/date.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>javascript/bootstrap/daterangepicker.js"></script>


    <script type="text/javascript">
        //-----------------------------------------
        // Confirm Actions (delete, uninstall)
        //-----------------------------------------
        $(document).ready(function(){
            // Confirm Delete
            $('#form').submit(function(){
                if ($(this).attr('action').indexOf('delete',1) != -1) {
                    var ItemSelected = false;
                    $('#form input[type="checkbox"]').each(function(){
                        if($(this).is(':checked')){
                            ItemSelected = true;
                        }
                    });
                    if(!ItemSelected) {
                        alert('<?php echo $text_retry; ?>');
                        return false;
                    }
                    else if (!confirm('<?php echo $text_confirm; ?>')) {
                        return false;
                    }
                }
            });

            // Confirm Uninstall
            $('a').click(function(){
                if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
                    if (!confirm('<?php echo $text_confirm; ?>')) {
                        return false;
                    }
                }
            });

            // Fix click link # cross opencart unautherize
            $('a[href="#"],a[href^="#"]').live('click',function(event){event.preventDefault();console.log('prevent redirected');});

            // Add class .active to current link
            $('ul.main-menu li a').each(function(){
                if(this.href === window.location.href) {
                    $(this).parent().addClass('active');
                }
            });
        });

        var imageUrlPath = "<?php echo IMG_PATH ?>";
    </script>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/custom/strip_tags.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>javascript/html5.js"></script>
</head>
<body>

<!-- start: Header -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo base_url(); ?>"> <img src="<?php echo base_url();?>image/playbasis_logo_120_white.png" title="<?php echo $heading_title; ?>" onclick="location = '<?php echo base_url(); ?>'" /> <span class="hidden-phone"></span></a>

            <!-- start: Header Menu -->
            <div class="nav-no-collapse header-nav">
                <?php if (isset($username)) { ?>
          <ul class="nav pull-right">
            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                  <i>
                      <?php echo $username; ?>
                      <?php echo (isset($domain_name['site_name'])) ? '[' . $domain_name['site_name'] .']' : '' ; ?>
                  </i>
                  <span class="caret"></span>
              </a>
                <?php if(isset($domains)) { ?>
                <ul class="dropdown-menu">
                  <?php foreach ($domains as $domain) { ?>
                        <?php echo ($domain['site_id'] == $site_id) ?  '<li selected="selected">' : '<li>' ; ?>
                        <a href="<?php echo $domain['href']; ?>">
                            <i class="icon-user"></i>
                            <?php echo $domain['site_name']; ?>
                        </a>
                    </li>
                  <?php } ?>
                </ul>
              <?php } ?>
                </li>
                <li class="dropdown">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon-user icon-white"></i>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $logout; ?>"><i class="icon-off"></i> Logout</a></li>
                    </ul>
                </li>
                </ul>
                <?php } ?>
            </div>
            <!-- end: Header Menu -->

        </div>
    </div>
</div>
<!-- start: Body -->
<div class="container-fluid">
    <div class="row-fluid">
