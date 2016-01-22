<!DOCTYPE html>
<html lang="en">
<head>

    <!-- start: Meta -->
    <meta charset="utf-8">
    <title>Playbasis Dashboard</title>
    <meta name="description" content="Playbasis Dashboard">
    <meta name="author" content="Łukasz Holeczek">
    <!-- end: Meta -->

    <!-- start: Mobile Specific -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- end: Mobile Specific -->

    <!-- start: CSS -->
    <link id="bootstrap-style" href="<?php echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link id="base-style" href="<?php echo base_url();?>assets/css/style.css" rel="stylesheet">
    <link id="base-style-responsive" href="<?php echo base_url();?>assets/css/style-responsive.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/playbasis-dashboard.css" rel="stylesheet">

    <!--[if lt IE 7 ]>
    <link id="ie-style" href="<?php echo base_url();?>assets/css/style-ie.css" rel="stylesheet">
    <![endif]-->
    <!--[if IE 8 ]>
    <link id="ie-style" href="<?php echo base_url();?>assets/css/style-ie.css" rel="stylesheet">
    <![endif]-->
    <!--[if IE 9 ]>
    <![endif]-->

    <!-- end: CSS -->

    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- start: Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.ico">
    <!-- end: Favicon -->

</head>

<body>

<!--<div id="overlay">-->
<!--    <ul>-->
<!--        <li class="li1"></li>-->
<!--        <li class="li2"></li>-->
<!--        <li class="li3"></li>-->
<!--        <li class="li4"></li>-->
<!--        <li class="li5"></li>-->
<!--        <li class="li6"></li>-->
<!--    </ul>-->
<!--</div>-->

<!-- start: Header -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo base_url() ?>"><span class="hidden-phone">Playbasis</span></a>

            <!-- start: Header Menu -->
            <div class="nav-no-collapse header-nav">
            </div>
            <!-- end: Header Menu -->
        </div>
    </div>
</div>
<!-- start: Header -->

<div class="container-fluid">
    <div class="row-fluid">

        <!-- start: Main Menu -->
        <div class="span2 main-menu-span">
            <div class="nav-collapse sidebar-nav">
                <ul class="nav nav-tabs nav-stacked main-menu">
                    <li><a href="/index"><i class="icon-home icon-white"></i><span class="hidden-tablet"> Dashboard</span></a></li>
                </ul>
            </div><!--/.well -->
        </div><!--/span-->
        <!-- end: Main Menu -->

        <noscript>
            <div class="alert alert-block span10">
                <h4 class="alert-heading">Warning!</h4>
                <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
            </div>
        </noscript>

        <div id="content" class="span10">
            <!-- start: Content -->

            <!-- start: first panel -->
            <div id="actionPanel" class="row-fluid dashboard-panel">

                <div class="box span12" onTablet="span12" onDesktop="span12">
                    <div class="box-header">
                        <h2><i class="icon-star"></i><span class="break"></span>Actions</h2>
                        <div class="box-icon">
                            <a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content">
                        <div class="row-fluid control-row">
                            <div class="span2 side-columm">Action Selection</div>
                            <div class="span10">
                                <?php
                                $attributes = array('class' => 'form-inline span6');
                                echo form_open('', $attributes);?>
                                    <label for="action-start">From</label>
                                    <input type="text" class="input span3 datepicker" id="action-start" value="">
                                    <label for="action-end">To</label>
                                    <input type="text" class="input span3 datepicker" id="action-end" value="">
                                    <input type="button" class="btn" id="submitActionDate" value="GO">
                                <?php echo form_close();?>
                                <?php
                                $attributes = array('class' => 'form-inline span5 has-border');
                                echo form_open('', $attributes);?>
                                    <label for="action-unit-type">Unit Type</label>
                                    <select id="action-unit-type" class="span6">
                                        <option value="day" selected>Daily</option>
                                        <option value="week">Weekly</option>
                                        <option value="month">Monthly</option>
                                    </select>
                                    <input type="button" class="btn" id="submitActionUnit" value="GO">
                                <?php echo form_close();?>
                            </div>
                        </div>
                        <div class="row-fluid result-row">
                            <div class="span2 side-column">
                                <?php echo form_open();?>
                                    <div class="scroll-container">
                                        <ul id="action-panel" class="action pull-left api-result-container">
                                        </ul>
                                    </div>
                                    <div id="actions-action" class="action-container">
                                        <input type="checkbox" checked="checked" id="allActions">All Actions
                                    </div>
                                <?php echo form_close();?>
                            </div>
                            <div class="span7">
                                <div id="playbasis-action-stack-chart" class="api-result-container"></div>
                            </div>
                            <div class="span3 chart-canvas">
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div id="playbasis-action-compare-stack-chart" class="chart api-result-container"></div>
                                    </div>
                                </div>
                                <div class="row-fluid control-row line-up">
                                    <div class="span12">
                                        <?php
                                        $attributes = array('class' => 'form-inline');
                                        echo form_open('', $attributes);?>
                                            <select id="action-compare-type" class="span8">
                                                <option value="percentage" selected>Percentage</option>
                                                <option value="gross">Value</option>
                                            </select>
                                            <input type="button" class="btn" id="submitActionCompareType" value="GO">
                                        <?php echo form_close();?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- end: first panel -->

            <hr>

            <!-- start: second panel -->
            <div id="rewardPanel" class="row-fluid dashboard-panel">

                <div class="box span12" onTablet="span12" onDesktop="span12">
                    <div class="box-header">
                        <h2><i class="icon-star"></i><span class="break"></span>Rewards</h2>
                        <div class="box-icon">
                            <a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content">
                        <div class="row-fluid control-row">
                            <div class="span2 side-columm">Type Selection</div>
                            <div class="span10">
                                <?php
                                $attributes = array('class' => 'form-inline span6');
                                echo form_open('', $attributes);?>
                                    <label for="reward-start">From</label>
                                    <input type="text" class="input span3 datepicker" id="reward-start" value="">
                                    <label for="reward-end">To</label>
                                    <input type="text" class="input span3 datepicker" id="reward-end" value="">
                                    <input type="button" class="btn" id="submitRewardDate" value="GO">
                                <?php echo form_close();?>
                                <?php
                                $attributes = array('class' => 'form-inline span5 has-border');
                                echo form_open('', $attributes);?>
                                    <label for="reward-unit-type">Unit Type</label>
                                    <select id="reward-unit-type" class="span6">
                                        <option value="day" selected>Daily</option>
                                        <option value="week">Weekly</option>
                                        <option value="month">Monthly</option>
                                    </select>
                                    <input type="button" class="btn" id="submitRewardUnit" value="GO">
                                <?php echo form_close();?>
                            </div>
                        </div>
                        <div class="row-fluid result-row">
                            <div class="span2 side-column">
                                <?php
                                 echo form_open('');?>
                                    <div class="row-fluid control-row">
                                        <div class="span12">
                                            <select id="reward-type" class="span11">
                                                <option value="point" selected>Point</option>
                                                <option value="exp">Experience</option>
                                                <option value="level">Level</option>
                                                <option value="badge">Badge</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="scroll-container">
                                        <ul id="reward-badge-panel" class="reward pull-left">
                                        </ul>
                                    </div>
                                    <div id="reward-badge-action" class="action-container">
                                        <input type="checkbox" checked="checked" id="allBadges">All Badges
                                    </div>
                                <?php echo form_close();?>
                            </div>
                            <div class="span7">
                                <div id="playbasis-reward-stack-chart" class="api-result-container"></div>
                            </div>
                            <div class="span3 chart-canvas">
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div id="playbasis-reward-compare-stack-chart" class="chart api-result-container"></div>
                                    </div>
                                </div>
                                <div class="row-fluid control-row line-up">
                                    <div class="span12">
                                        <?php
                                        $attributes = array('class' => 'form-inline');
                                        echo form_open('', $attributes);?>
                                            <select id="reward-compare-type" class="span8">
                                                <option value="percentage" selected>Percentage</option>
                                                <option value="gross">Value</option>
                                            </select>
                                            <input type="button" class="btn" id="submitRewardCompareType" value="GO">
                                        <?php echo form_close();?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end: second panel -->

            <hr>

            <!-- start: third panel -->
            <div id="userPanel" class="row-fluid dashboard-panel">

                <div class="box span12" onTablet="span12" onDesktop="span12">
                    <div class="box-header">
                        <h2><i class="icon-star"></i><span class="break"></span>User</h2>
                        <div class="box-icon">
                            <a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content">
                        <div class="row-fluid control-row">
                            <div class="span12">
                                <?php
                                $attributes = array('class' => 'form-inline span3');
                                echo form_open('', $attributes);?>
                                    <select id="user-type" class="span9">
                                        <option value="new" selected>New Registration</option>
                                        <option value="dau">DAU</option>
                                        <option value="mau">MAU</option>
                                    </select>
                                    <input type="button" class="btn" id="submitUserType" value="GO">
                                <?php echo form_close();?>
                                <?php
                                $attributes = array('class' => 'form-inline span5 has-border');
                                echo form_open('', $attributes);?>
                                    <label for="user-start">From</label>
                                    <input type="text" class="input span3 datepicker" id="user-start" value="">
                                    <label for="user-end">To</label>
                                    <input type="text" class="input span3 datepicker" id="user-end" value="">
                                    <input type="button" class="btn" id="submitUserDate" value="GO">
                                <?php echo form_close();?>
                                <?php
                                $attributes = array('class' => 'form-inline span4 has-border');
                                echo form_open('', $attributes);?>
                                    <label for="user-unit-type">Unit Type</label>
                                    <select id="user-unit-type" class="span6">
                                        <option value="day" selected>Daily</option>
                                        <option value="week">Weekly</option>
                                        <option value="month">Monthly</option>
                                    </select>
                                    <input type="button" class="btn" id="submitUserUnit" value="GO">
                                <?php echo form_close();?>
                            </div>
                        </div>
                        <div class="row-fluid result-row">
                            <div class="span12">
                                <div id="playbasis-user-line-chart" class="chart-line api-result-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- end: third panel -->

            <hr>


            <!-- end: Content -->
        </div>
        <!--/#mainContent-->
    </div>
    <!--/fluid-row-->

    <div class="clearfix"></div>

    <footer>

    </footer>

</div><!--/.fluid-container-->

<!-- required by the tooltip plugin -->
<div id="action-tool-tip" class="tooltip fade top in" style="display: none;">
    <div class="tooltip-arrow"></div>
    <div class="tooltip-inner">Tooltip on top</div>
</div>

<!-- start: JavaScript-->

<script src="<?php echo base_url();?>assets/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery-migrate-1.0.0.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.0.custom.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.ui.touch-punch.js"></script>
<script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.cookie.js"></script>
<script src='<?php echo base_url();?>assets/js/fullcalendar.min.js'></script>
<script src='<?php echo base_url();?>assets/js/jquery.dataTables.min.js'></script>
<script src="<?php echo base_url();?>assets/js/excanvas.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.flot.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.flot.pie.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.flot.stack.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.flot.resize.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.flot.time.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.chosen.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.uniform.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.cleditor.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.noty.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.elfinder.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.raty.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.iphone.toggle.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.uploadify-3.1.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.gritter.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.imagesloaded.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.masonry.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.knob.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.sparkline.min.js"></script>
<script src="<?php echo base_url();?>assets/js/colorchain.js"></script>
<script src="<?php echo base_url();?>assets/js/custom.js"></script>
<script src="<?php echo base_url();?>assets/js/playbasis-dashboard.js"></script>

<!-- end: JavaScript-->

</body>
</html>
