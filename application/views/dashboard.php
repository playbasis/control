<div id="content" class="span10">

<!-- Start player insight row -->
<div class="row-fluid">
    <div class="box span8" onTablet="span12" onDesktop="span8">
        <div class="box-header">
            <h2><i class="icon-signal"></i><span class="break"></span>Insight</h2>
            <div class="box-icon">
                <span class="pull-right insight_time_interval">
                    <span>
                        <i class="icon-calendar"></i>&nbsp;
                        <input type="text" class="datepicker " id="start_date" value="<?php echo $sample_start_date ?>"/>

                    </span>
                    <span style="margin:0 10px 10px">TO</span>
                    <i class="icon-calendar"></i>&nbsp;
                        <input type="text" class="datepicker " id="end_date" value="<?php echo $sample_end_date ?>"/>

                    <input type="submit" id="submitdate_filter" class="btn" value="submit" style="margin:0 0 10px 10px" />
                </span>
            </div>
        </div>
        <div class="box-content">
            <div id="stats-chart"  class="center" style="height:300px" ></div>
        </div>
    </div>

    <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
        <div class="box-header">
            <h2><i class="icon-comment"></i><span class="break"></span>Live Feed</h2>
        </div>

        <div class="box-content">
            <div id="noti-stream-container">
                <div id="noti-stream"></div>


            </div>
        </div>
    </div>

</div>
<!-- End player insight row -->

<hr>

<!-- Start player stats row -->
<div class="row-fluid">
    <div class="carousel-container">

        <div id="panel-carousel-day" class="panel-carousel">
            <!-- Start circleStats -->
            <div id="stats-carousel-day" class="circleStats">

            </div>
            <!-- End circleStats -->

            <a id="carousel-prev-day" class="left carousel-control" href="#stats-carousel-day">&lsaquo;</a>
            <a id="carousel-next-day" class="right carousel-control" href="#stats-carousel-day">&rsaquo;</a>

        </div>

        <div id="panel-carousel-weekly" class="panel-carousel">
            <!-- Start circleStats -->
            <div id="stats-carousel-weekly" class="circleStats">

            </div>
            <!-- End circleStats -->

            <a id="carousel-prev-weekly" class="left carousel-control" href="#stats-carousel-weekly">&lsaquo;</a>
            <a id="carousel-next-weekly" class="right carousel-control" href="#stats-carousel-weekly">&rsaquo;</a>

        </div>

        <div id="panel-carousel-month" class="panel-carousel">
            <!-- Start circleStats -->
            <div id="stats-carousel-month" class="circleStats">

            </div>
            <!-- End circleStats -->

            <a id="carousel-prev-month" class="left carousel-control" href="#stats-carousel-month">&lsaquo;</a>
            <a id="carousel-next-month" class="right carousel-control" href="#stats-carousel-month">&rsaquo;</a>

        </div>

        <div id="stats-sort" class="btn-group" data-toggle="buttons-radio">
            <button rel="day" type="button" class="btn options btn-mini">Daily</button>
            <button rel="weekly" type="button" class="btn options btn-mini">Weekly</button>
            <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
        </div>

    </div>



</div>
<!-- End player stats row -->

<hr>

<!-- Start player masonry row -->
<div class="row-fluid">

<div class="box span6" onTablet="span12" onDesktop="span8">
    <div class="box-header">
        <h2><i class="icon-user"></i><span class="break"></span>Newest Users</h2>
    </div>
    <div class="box-content">

        <!-- Start latest-masonry -->
        <div id="latest-masonry" class="super-list variable-sizes clearfix">
            <?php if(isset($players)) { foreach ($players as $player) { ?>
            <!-- Start isot-player-container -->
            <div class="isot-player-container">
                <div class="isot-player">

                    <?php //echo ($player['status']==0)? $text_disable : 'Active'; ?>

                    <div class="isot-player-contact-container isot-player-engaged">
                        <div class="isot-player-contact">
                            <?php if($player['first_name'] || $player['last_name']){ ?>
                            <span class="isot-player-name"><?php echo $player['first_name']; ?> <?php echo $player['last_name']; ?></span>
                            <?php }else{ ?>
                            <span class="isot-player-name"><?php echo $player['username']; ?></span>
                            <?php } ?>
                            <br>
                            <span class="isot-player-email"><?php echo $player['email']; ?></span>
                        </div>
                    </div>

                    <div class="isot-player-actions-container">
                        <ul class="isot-player-actions">
                            <li action="setting"><i class="icon-cogs"></i></li>
                            <li action="email"><i class="icon-envelope"></i></li>
                            <li action="gift"><i class="icon-gift"></i></li>
                            <li action="info"><i class="icon-info-sign"></i></li>
                        </ul>
                    </div>

                    <div class="isot-player-dates-container">
                        <div class="isot-player-dates">
                            <div class="isot-player-joined">
                                <h6>joined</h6>
                                <span><?php echo $player['date_added']; ?></span>
                            </div>

                            <div class="isot-player-last-active">
                                <h6>last active</h6>
                                <span><?php echo $player['last_active']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="isot-player-minimize">
                        <span>X</span>
                    </div>

                    <div class="isot-player-portrait">
                        <?php if (!empty($player['social_id'])) { ?>
                        <img class="avatar" alt="<?php echo $player['nickname']; ?>" src="https://graph.facebook.com/<?php echo $player['social_id']; ?>/picture">
                        <?php } else { ?>
                        <img class="avatar" alt="<?php echo $player['nickname']; ?>" src="<?php echo $player['image']; ?>">
                        <?php } ?>
                    </div>

                    <div class="isot-player-common-actions isot-player-activity-list">
                        <h5>Most Common Actions</h5>
                        <ul>
                            <?php if(!empty($player['action'])) { foreach($player['action'] as $action) { ?>
                            <li class="isot-player-common-action">
                                <?php if(!empty($action['icon'])) { ?>
                                <div class="isot-player-common-action-icon">
                                    <i class="<?php echo $action['icon']; ?> icon-2x"></i>
                                </div>
                                <?php } ?>
                                <span class="isot-player-common-action-quantity"><?php //echo $action['value']; ?></span>
                                <span class="isot-player-common-action-name"><?php //echo $action['name']; ?></span>
                            </li>
                            <?php } } ?>
                        </ul>
                    </div>

                    <div class="isot-player-recent-badges isot-player-activity-list">
                        <h5>Recent Badges</h5>
                        <ul>
                            <?php //if(!empty($player['badges'])) { foreach($player['badges'] as $badge) { ?>
                            <li class="isot-player-recent-badge">

                            </li>
                            <?php //} } ?>
                        </ul>
                    </div>

                    <div class="isot-player-stats">

                        <div class="isot-player-points">
                            <span><?php echo $player['point']; ?> <small>points</small></span>
                        </div>

                        <div class="isot-player-level" title="Level">
                            <span><small>lvl</small> <?php echo $player['level']; ?></span>
                        </div>

                    </div>

                </div>
            </div>
            <!-- End isot-player-container -->
        <?php
                }
            }
        ?>
        </div>
        <!-- End latest-masonry -->

    </div>
</div>

<div class="box span6" onTablet="span12" onDesktop="span8">
    <div class="box-header">
        <h2><i class="icon-user"></i><span class="break"></span>Leader Board</h2>
    </div>
    <div class="box-content">

        <!-- Start leader-masonry -->
        <div id="leader-masonry" class="super-list variable-sizes clearfix">
            <?php if($leaderboards) { foreach ($leaderboards as $key => $leaderboard) { ?>
            <!-- Start isot-player-container -->
            <div class="isot-player-container">
                <div class="isot-player">

                    <div class="isot-player-contact-container isot-player-engaged">
                        <div class="isot-player-contact">
                            <span class="isot-player-name"><?php echo $leaderboard['name']; ?></span>
                            <br>
                            <span class="isot-player-email"><?php echo $leaderboard['email']; ?></span>
                        </div>
                    </div>

                    <div class="isot-player-actions-container">
                        <ul class="isot-player-actions">
                            <li action="setting"><i class="icon-cogs"></i></li>
                            <li action="email"><i class="icon-envelope"></i></li>
                            <li action="gift"><i class="icon-gift"></i></li>
                            <li action="info"><i class="icon-info-sign"></i></li>
                        </ul>
                    </div>

                    <div class="isot-player-dates-container">
                        <div class="isot-player-dates">
                            <div class="isot-player-joined">
                                <h6>joined</h6>
                                <span><?php echo $leaderboard['date_added']; ?></span>
                            </div>

                            <div class="isot-player-last-active">
                                <h6>last active</h6>
                                <span><?php echo $leaderboard['last_active']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="isot-player-minimize">
                        <span>X</span>
                    </div>

                    <div class="isot-player-portrait">
                        <img src="<?php echo $leaderboard['image']; ?>" alt="Avatar">
                    </div>

                    <div class="isot-player-common-actions isot-player-activity-list">
                        <h5>Most Common Actions</h5>
                        <ul>
                            <?php if(!empty($leaderboard['action'])) { foreach($leaderboard['action'] as $action) { ?>
                            <li class="isot-player-common-action">
                                <?php if(!empty($action['icon'])) { ?>
                                <div class="isot-player-common-action-icon">
                                    <i class="<?php echo $action['icon']; ?> icon-2x"></i>
                                </div>
                                <?php } ?>
                                <span class="isot-player-common-action-quantity"><?php echo $action['value']; ?></span>
                                <span class="isot-player-common-action-name"><?php echo $action['name']; ?></span>
                            </li>
                            <?php } } ?>
                        </ul>
                    </div>

                    <div class="isot-player-recent-badges isot-player-activity-list">
                        <h5>Recent Badges</h5>
                        <ul>
                            <?php //if(!empty($player['badges'])) { foreach($player['badges'] as $badge) { ?>
                            <li class="isot-player-recent-badge">

                            </li>
                            <?php //} } ?>
                        </ul>
                    </div>

                    <div class="isot-player-stats">

                        <div class="isot-player-points">
                            <span><?php echo $leaderboard['point']; ?> <small>points</small></span>
                        </div>

                        <div class="isot-player-level" title="Level">
                            <span><small>lvl</small> <?php echo $leaderboard['level']; ?></span>
                        </div>

                    </div>

                    <div class="isot-player-rank">
                        <span><small>rank</small> <?php echo $key + 1; ?></span>
                    </div>

                </div>
            </div>
            <!-- End isot-player-container -->
            <?php } } ?>
        </div>
        <!-- End leader-masonry -->

    </div>
</div>

</div>
<!-- End player masonry row -->

<hr>

</div>

<script type="text/javascript" src="<?php echo base_url();?>javascript/dashboard/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/dashboard/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/dashboard/jquery.carouFredSel-6.2.0-packed.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/dashboard/jquery.flot.js"></script>
<script type="text/javascript">

    $(function(){

        $('#st1').daterangepicker(
                {
                    opens: 'left',
                    format: 'MM/dd/yyyy',
                    separator: ' to ',
                    startDate: Date.today().add({ days: -29 }),
                    endDate: Date.today(),
                    minDate: '01/01/2012',
                    maxDate: '12/31/2013'
                },
                function(start, end) {
                    //console.log(start);
                });

    })

</script>
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/dashboard/dashboard.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/dashboard/dashboard.js"></script>

<div class="message-dialog modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Message</h3>
    </div>
    <div class="modal-body">
        <p></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-dismiss="modal" >Close</a>
        <!-- <a href="#" class="btn btn-primary">Save changes</a> -->
    </div>
</div>


<!-- start : override - Leader board item -css   -->
<style type="text/css">
    .large { height: 160px !important ; }
    #latest-masonry .large .isot-player, .large .isot-player { height: 160px  !important ; }
    .large .isot-player-contact-container { bottom: 110px !important ; }
    .large .isot-player-actions-container { bottom: 80px !important ; }
    .large .isot-player-dates-container { bottom: 52px !important ; }
    .large .isot-player-minimize { bottom: 125px !important;}

        /*.large .isot-player-portrait ,*/
    .large .isot-player-common-actions ,
    .large .isot-player-recent-badges {display: none !important;}
</style>
<!-- end : override - Leader board item -css   -->
