<div id="masonry-item" class="super-list variable-sizes clearfix">
    <?php if($players) { foreach ($players as $player) { // print_r($player); ?>

    <!-- Start isot-player-container -->
    <div class="isot-player-container <?php echo $player['gender']; ?>">
        <!-- Start isot-player -->
        <div class="isot-player <?php echo 'level-' . $player['level']; ?>">

            <!-- Start isot-player-contact-container -->
            <div class="isot-player-contact-container isot-player-engaged level-group-<?php echo floor( $player['level'] / 10 ); ?>">

                <div class="isot-player-contact">
                    <span class="isot-player-name"><?php echo $player['firstname'].' '.$player['lastname']; ?></span>
                    <br>
                    <span class="isot-player-email"><?php echo $player['email']; ?></span>
                </div>

            </div>
            <!-- End isot-player-contact-container -->

            <!-- Start isot-player-actions-container -->
            <div class="isot-player-actions-container">

                <ul class="isot-player-actions">

                    <li action="setting"><i class="icon-cogs"></i></li>
                    <li action="email"><i class="icon-envelope"></i></li>
                    <li action="gift"><i class="icon-gift"></i></li>
                    <li action="info"><i class="icon-info-sign"></i></li>

                </ul>

            </div>
            <!-- End isot-player-actions-container -->

            <!-- Start isot-player-dates-container -->
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
            <!-- End isot-player-dates-container -->

            <!-- Start isot-player-minimize -->
            <div class="isot-player-minimize">

                <span>X</span>

            </div>
            <!-- End isot-player-minimize -->

            <!-- Start isot-player-portrait -->
            <div class="isot-player-portrait">

                <img src="<?php echo $player['image']; ?>" alt="avatar">

            </div>
            <!-- End isot-player-portrait -->

            <!-- Start isot-player-common-actions -->
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
                        <span class="isot-player-common-action-quantity"><?php echo $action['value']; ?></span>
                        <span class="isot-player-common-action-name"><?php echo $action['name']; ?></span>

                    </li>
                    <?php } } ?>

                </ul>

            </div>
            <!-- End isot-player-common-actions -->

            <!-- Start isot-player-recent-badges -->
            <div class="isot-player-recent-badges isot-player-activity-list">

                <h5>Recent Badges</h5>

                <ul>
                    <?php if(!empty($player['badges'])) {
                        foreach($player['badges'] as $badge) { ?>
                        <li class="isot-player-recent-badge">
                            <?php if(!empty($badge['image'])) { ?>
                            <img width="40" src="<?php echo $badge['image']; ?>" alt="badge">
                            <?php } ?>
                        </li>
                        <?php }
                    } ?>
                </ul>

            </div>
            <!-- End isot-player-recent-badges -->

            <!-- Start isot-player-points -->
            <div class="isot-player-points">

                <span><small>points</small> <?php echo $player['points']; ?></span>

            </div>
            <!-- End isot-player-points -->

            <!-- Start isot-player-level -->
            <div class="isot-player-level <?php if( $player['level'] >= 100 ) echo "isot-player-level-hundreds"; ?>" title="Level">

                <object data="image/level-group-<?php echo floor( $player['level'] / 10 ); ?>.svg" type="image/svg+xml" />

                <span><?php echo $player['level']; ?></span>

            </div>
            <!-- End isot-player-level -->

        </div>
        <!-- End isot-player -->
    </div>
    <!-- End isot-player-container -->

    <?php } } ?>
</div>