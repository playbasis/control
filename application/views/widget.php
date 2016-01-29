<div id="content" class="span10 widget-page">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->

        <div class="content">
        
        <h1><?php echo $this->lang->line('text_choose_type'); ?></h1>
        <?php
        $attributes = array('class' => 'form-horizontal well', 'id' => 'widget-globel');
        echo form_open('', $attributes);?>

            <div class="control-group hide">
            <label class="control-label" ><?php echo $this->lang->line('text_choose_key'); ?></label>
                <div class="controls">
                    <select class="wg-apikey" >
                        <?php
                        foreach($platform_data as $pfd){
                            ?>
                            <option  value="<?php echo $pfd["api_key"]; ?>"><?php echo $pfd["api_key"]; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" ><?php echo $this->lang->line('form_player_id'); ?></label>
                <div class="controls">
                    <input type="text" class="wg-player-id" placeholder="<?php echo $this->lang->line('text_require'); ?>" value="<?php echo TEST_PLAYER_ID; ?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" ><?php echo $this->lang->line('form_color'); ?></label>
                <div class="controls">
                    <div class="input-prepend">
                      <span class="colorSelectorHolder add-on"></span>
                      <input class="span6 colorSelector wg-color"  type="text" placeholder="#ffaa00">
                    </div>
                </div>
            </div>
            
            <div class="control-group">
                    <label class="control-label" ><?php echo $this->lang->line('form_cssfile'); ?></label>
                    <div class="controls">
                        <input type="text" width="50%" class="wg-globel-cssfile" placeholder="<?php echo $this->lang->line('text_cssfile'); ?>">
                    </div>
            </div>
        <?php echo form_close();?>
    <ul class="nav nav-tabs">
        <?php if(isset($plan_widget['social']) && $plan_widget['social']){ ?>
            <li> <?php echo anchor('widget/social_login', $this->lang->line('column_social_login'));?></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_social_login'); ?></a></li>
        <?php } ?>
        
        <?php if(isset($plan_widget['profile']) && $plan_widget['profile']){ ?>
            <li><a href="#widget-profile" data-toggle="tab"><?php echo $this->lang->line('column_profile'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_profile'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['leaderboard']) && $plan_widget['leaderboard']){ ?>
            <li><a href="#widget-leaderboard"  data-toggle="tab"><?php echo $this->lang->line('column_leaderboard'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_leaderboard'); ?></a></li>
        <?php } ?>

        

        <?php if(isset($plan_widget['achievement']) && $plan_widget['achievement']){ ?>
            <li><a href="#widget-achievement" data-toggle="tab"><?php echo $this->lang->line('column_achievement'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_achievement'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['quiz']) && $plan_widget['quiz']){ ?>
            <li><a href="#widget-quiz" data-toggle="tab"><?php echo $this->lang->line('column_quiz'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_quiz'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['quest']) && $plan_widget['quest']){ ?>
            <li><a href="#widget-quest" data-toggle="tab"><?php echo $this->lang->line('column_quest'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_quest'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['feed']) && $plan_widget['feed']){ ?>
            <li><a href="#widget-feed" data-toggle="tab"><?php echo $this->lang->line('column_feed'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_feed'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['rewardstore']) && $plan_widget['rewardstore']){ ?>
            <li><a href="#widget-rewardstore" data-toggle="tab"><?php echo $this->lang->line('column_rewardstore'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_rewardstore'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['treasure']) && $plan_widget['treasure']){ ?>
            <li><a href="#widget-treasure" data-toggle="tab"><?php echo $this->lang->line('column_treasure'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_treasure'); ?></a></li>
        <?php } ?>

        <?php if(isset($plan_widget['trackevent']) && $plan_widget['trackevent']){ ?>
            <li><a href="#widget-trackevent" data-toggle="tab"><?php echo $this->lang->line('column_trackevent'); ?></a></li>
        <?php }else{ ?>
            <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_trackevent'); ?></a></li>
        <?php } ?>

    </ul>

        <div class="tab-content">
        <?php if(isset($plan_widget['social']) && $plan_widget['social']){ ?>
        <div class="tab-pane" id="widget-social-login">

        </div><!-- .tab-pane -->
        <?php } ?>
    
                <?php if(isset($plan_widget['profile']) && $plan_widget['profile']){ ?>
                <div class="tab-pane" id="widget-profile">

                    <h3><?php echo $this->lang->line('text_profile_widget'); ?></h3>
                    
                    <div class="row">
                        <div class="span6 offset3">
                            <?php
                            $attributes = array('class' => 'form-horizontal');
                            echo form_open('', $attributes);?>
                                <div class="control-group">
                                    <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                                    <div class="controls">
                                        <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="wg-displaypoint"><?php echo $this->lang->line('form_type_display'); ?></label>
                                    <div class="controls">
                                        <select class="wg-displaypoint" >
                                          <option value="point">Point</option>
                                          <option value="exp">EXP</option>
                                            <?php foreach($points_data as $p){ ?>
                                                <option  value="<?php echo $p["name"]; ?>"><?php echo ucfirst($p["name"]); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" ></label>
                                    <div class="controls">
                                        <a href="javascript:void(0);" onclick="reloadProfile()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                        <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-profile" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                                    </div>
                                </div>

                            <?php echo form_close();?>
                        </div>
                        <div class="span11">
                            <iframe id="iframe-profile" src="<?php echo base_url();?>index.php/widget/preview?type=profile" width="100%" height="280" frameborder="0"></iframe>
                        </div>
                    </div>
                </div><!-- .tab-pane -->
                <?php } ?>

        <?php if(isset($plan_widget['leaderboard']) && $plan_widget['leaderboard']){ ?>
		<div class="tab-pane" id="widget-leaderboard">
			
			<h3><?php echo $this->lang->line('text_leaderboard_widget'); ?></h3>
			
			<div class="row">
		        	<div class="span5">
				<?php
				$attributes = array('class' => 'form-horizontal');
				echo form_open('', $attributes);?>
					<div class="control-group">
						<label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
						<div class="controls">
							<input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
						</div>
					</div>

					
					<div class="control-group">
						<label class="control-label" for="wg-rank"><?php echo $this->lang->line('form_rankby'); ?></label>
						<div class="controls">
							<select class="wg-rankby" >
							  <option value="point">Point</option>
							  <option value="exp">Exp</option>
                                <?php foreach($points_data as $p){ ?>
                                    <option  value="<?php echo $p["name"]; ?>"><?php echo ucfirst($p["name"]); ?></option>
                                <?php } ?>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" ></label>
						<div class="controls">
							<a href="javascript:void(0);" onclick="reloadLeaderboard()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
							<a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-leaderboard" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
						</div>
					</div>

                <?php echo form_close();?>

		        	</div>
                <div class="span7">
                    <iframe id="iframe-leaderboard" src="<?php echo base_url();?>index.php/widget/preview?type=leaderboard" width="100%" height="400" frameborder="0"></iframe>
                </div>
            </div>
        </div><!-- .tab-pane -->
        <?php } ?>
     
       
        <?php if(isset($plan_widget['achievement']) && $plan_widget['achievement']){ ?>
        <div class="tab-pane" id="widget-achievement">

            <h3><?php echo $this->lang->line('text_achievement_widget'); ?></h3>

            <div class="row">
                <div class="span5">
                    <?php
                    $attributes = array('class' => 'form-horizontal');
                    echo form_open('', $attributes);?>
                        <div class="control-group">
                            <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                            <div class="controls">
                                <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" ><?php echo $this->lang->line('form_height'); ?></label>
                            <div class="controls">
                                <input type="text" class="wg-height" placeholder="<?php echo $this->lang->line('text_pixel_height'); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" ></label>
                            <div class="controls">
                                <a href="javascript:void(0);" onclick="reloadAchievement()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-achievement" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                            </div>
                        </div>
                    <?php echo form_close();?>

                </div>
                <div class="span7">
                    <iframe id="iframe-achievement" src="<?php echo base_url();?>index.php/widget/preview?type=achievement" width="100%" height="500" frameborder="0"></iframe>
                </div>
            </div>
        </div><!-- .tab-pane -->
        <?php } ?>

        <?php if(isset($plan_widget['quiz']) && $plan_widget['quiz']){ ?>
            <div class="tab-pane" id="widget-quiz">

                <h3><?php echo $this->lang->line('text_quiz_widget'); ?></h3>

                <div class="row">
                    <div class="span5">
                        <?php
                        $attributes = array('class' => 'form-horizontal');
                        echo form_open('', $attributes);?>
                            <div class="control-group">
                                <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                                <div class="controls">
                                    <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" ><?php echo $this->lang->line('form_height'); ?></label>
                                <div class="controls">
                                    <input type="text" class="wg-height" placeholder="<?php echo $this->lang->line('text_pixel_height'); ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" ></label>
                                <div class="controls">
                                    <a href="javascript:void(0);" onclick="reloadQuiz()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                    <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-quiz" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                                </div>
                            </div>
                        <?php echo form_close();?>

                    </div>
                    <div class="span7">
                        <iframe id="iframe-quiz" src="<?php echo base_url();?>index.php/widget/preview?type=quiz" width="100%" height="400" frameborder="0"></iframe>
                    </div>
                </div>
            </div><!-- .tab-pane -->
            <?php } ?>



            <?php if(isset($plan_widget['quest']) && $plan_widget['quest']){ ?>
                <div class="tab-pane" id="widget-quest">

                    <h3><?php echo $this->lang->line('text_quest_widget'); ?></h3>

                    <div class="row">
                        <div class="span5">
                            <?php
                            $attributes = array('class' => 'form-horizontal');
                            echo form_open('', $attributes);?>
                                <div class="control-group">
                                    <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                                    <div class="controls">
                                        <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" ><?php echo $this->lang->line('form_height'); ?></label>
                                    <div class="controls">
                                        <input type="text" class="wg-height" placeholder="<?php echo $this->lang->line('text_pixel_height'); ?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" ></label>
                                    <div class="controls">
                                        <a href="javascript:void(0);" onclick="reloadQuest()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                        <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-quest" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                                    </div>
                                </div>
                            <?php echo form_close();?>

                        </div>
                        <div class="span7">
                            <iframe id="iframe-quest" src="<?php echo base_url();?>index.php/widget/preview?type=quest" width="100%" height="400" frameborder="0"></iframe>
                        </div>
                    </div>
                </div><!-- .tab-pane -->
                <?php } ?>

                
                <?php if(isset($plan_widget['feed']) && $plan_widget['feed']){ ?>
                    <div class="tab-pane" id="widget-feed">

                        <h3><?php echo $this->lang->line('text_feed_widget'); ?></h3>

                        <div class="row">
                            <div class="span6 offset3">
                                <?php
                                $attributes = array('class' => 'form-horizontal');
                                echo form_open('', $attributes);?>
                                    <div class="control-group">
                                        <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                                        <div class="controls">
                                            <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" ><?php echo $this->lang->line('form_height'); ?></label>
                                        <div class="controls">
                                            <input type="text" class="wg-height" placeholder="<?php echo $this->lang->line('text_pixel_height'); ?>">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" ></label>
                                        <div class="controls">
                                            <a href="javascript:void(0);" onclick="reloadFeed()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                            <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-feed" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                                        </div>
                                    </div>
                                <?php echo form_close();?>

                            </div>
                            <div class="span11">
                                <iframe id="iframe-feed" src="<?php echo base_url();?>index.php/widget/preview?type=feed" width="100%" height="400" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div><!-- .tab-pane -->
                    <?php } ?>

                    <?php if(isset($plan_widget['rewardstore']) && $plan_widget['rewardstore']){ ?>
                        <div class="tab-pane" id="widget-rewardstore">

                            <h3><?php echo $this->lang->line('text_rewardstore_widget'); ?></h3>

                            <div class="row">
                                <div class="span6 offset3">
                                    <?php
                                    $attributes = array('class' => 'form-horizontal');
                                    echo form_open('', $attributes);?>
                                        <div class="control-group">
                                            <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                                            <div class="controls">
                                                <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" ><?php echo $this->lang->line('form_height'); ?></label>
                                            <div class="controls">
                                                <input type="text" class="wg-height" placeholder="<?php echo $this->lang->line('text_pixel_height'); ?>">
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" ></label>
                                            <div class="controls">
                                                <a href="javascript:void(0);" onclick="reloadRewardstore()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                                <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-rewardstore" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                                            </div>
                                        </div>
                                    <?php echo form_close();?>

                                </div>
                                <div class="span11">
                                    <iframe id="iframe-rewardstore" src="<?php echo base_url();?>index.php/widget/preview?type=rewardstore" width="100%" height="400" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div><!-- .tab-pane -->
                        <?php } ?>

                        
                        <?php if(isset($plan_widget['treasure']) && $plan_widget['treasure']){ ?>
                            <div class="tab-pane" id="widget-treasure">

                                <h3><?php echo $this->lang->line('text_treasure_widget'); ?></h3>

                                <div class="row">
                                    <div class="span5">
                                        <?php
                                        $attributes = array('class' => 'form-horizontal');
                                        echo form_open('', $attributes);?>
                                            <div class="control-group">
                                                <label class="control-label" ><?php echo $this->lang->line('form_width'); ?></label>
                                                <div class="controls">
                                                    <input type="text" class="wg-width" placeholder="<?php echo $this->lang->line('text_pixel_width'); ?>">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" ><?php echo $this->lang->line('form_height'); ?></label>
                                                <div class="controls">
                                                    <input type="text" class="wg-height" placeholder="<?php echo $this->lang->line('text_pixel_height'); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="control-group">
                                                <label class="control-label" ><?php echo $this->lang->line('form_rule_id'); ?></label>
                                                <div class="controls">
                                                    <input type="text" class="wg-rule-id" placeholder="<?php echo $this->lang->line('text_rule_id'); ?>">
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" ></label>
                                                <div class="controls">
                                                    <a href="javascript:void(0);" onclick="reloadTreasure()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                                                    <a href="#getcode-modal" role="button" data-toggle="modal" data-href="#widget-treasure" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                                                </div>
                                            </div>
                                        <?php echo form_close();?>

                                    </div>
                                    <div class="span7">
                                        <iframe id="iframe-treasure" src="<?php echo base_url();?>index.php/widget/preview?type=treasure" width="100%" height="400" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div><!-- .tab-pane -->
                            <?php } ?>


                        

                        <?php if(isset($plan_widget['trackevent']) && $plan_widget['trackevent']){ ?>
                            <div class="tab-pane" id="widget-trackevent">

                                <h3><?php echo $this->lang->line('text_trackevent_widget'); ?></h3>

                                <div class="row">
                                    <div class="span6 offset3">

                                        For "Visit" Customs page name Event, put attribute "data-pb-page" in &#x3C;body&#x3E tag<br><br>
                                        <pre class="prettyprint">
&#x3C;body data-pb-page=&#x22;### PAGE NAME ###&#x22;&#x3E;
                                        </pre>
                                        
                                        For Customs Event when "Click", put attribute "data-pb-action" and "data-pb-url" in &#x3C;a&#x3E; or &#x3C;button&#x3E; tag for example<br><br>
                                        <pre class="prettyprint">
&#x3C;a href=&#x22;http://www.exemple.com&#x22; data-pb-action=&#x22;view&#x22; data-pb-url=&#x22;exemple&#x22; &#x3E;exemple.com&#x3C;/a&#x3E;
                                        </pre>

                                    </div>
                                </div>
                            </div><!-- .tab-pane -->
                            <?php } ?>

	</div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->



<script>
$(document).ready(function(){
            $(".wg-apikey option:first").attr('selected','selected');

            if(location.hash){
                var tabclick = location.hash;
                $('.nav-tabs a[href='+tabclick+']').tab('show') ;
                
                if( typeof reloadFunc[tabActive] != 'undefined' ){
                    reloadFunc[tabActive]();
                 };

            }else{
                $('a[data-toggle="tab"]:first').tab('show');
            }

            $('.colorSelector').ColorPicker({
                 onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                },
                onShow: function (colpkr) {
                    $(colpkr).fadeIn(200);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(200);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    $('.colorSelectorHolder').css('backgroundColor', '#' + hex);
                    $('.colorSelector').val('#' +hex);
                },
                onSubmit: function(hsb, hex, rgb, el) {
                    $('.colorSelectorHolder').css('backgroundColor', '#' + hex);
                    $('.colorSelectorHolder').val('#' +hex);
                    $(el).ColorPickerHide();
                }
            }).bind('keyup', function(){
             $(this).ColorPickerSetColor(this.value);
             $('.colorSelectorHolder').css('backgroundColor', this.value);
             if(event.keyCode == 13){
                $(this).ColorPickerHide();
            }
        });
        $('.colorSelectorHolder').click(function(){
         $('.colorSelector').focus();
     });

        $('form').submit(function(e){
         e.preventDefault();
     });

        $('.controls .btn-primary').click(function(){
            tabActive = $(this).attr('data-href');

            if( typeof reloadFunc[tabActive] != 'undefined' ){
                reloadFunc[tabActive]();
             };

        });

        $('#getcode-modal').on('show', function () {

        })

        $('a[data-toggle="tab"]').on('shown', function (e) {
         tabActive = $(this).attr('href');

         if( typeof reloadFunc[tabActive] != 'undefined' ){
            reloadFunc[tabActive]();
         };

    })

    });
    var tabActive = '#widget-profile';
    var urlPreview = '<?php echo base_url();?>index.php/widget/preview';
    var timeBuffer = 500;
    var isReload = false;
    var timeout = setTimeout(void(0),0);
    var codeHeaderTemplate= "&lt;script&gt;\nwindow.PBAsyncInit = function(){\n\tPB.init({\n\t\tapi_key:'abc'{{theme_color}}{{player_id}}{{cssfile}}\n\t});\n};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://widget.pbapp.net/sdk.js?version=v1';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','playbasis-js'));&lt;/script&gt;";
    var codeHeaderPlayerTestTemplate= "&lt;script&gt;\nwindow.PBAsyncInit = function(){\n\tPB.init({\n\t\tapi_key:'abc',\n\t\ttheme_color :'#0e9ce4',\n\t\tplayerId :'playertest'\n\t});\n};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://widget.pbapp.net/playbasis/en/all.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','playbasis-js'));&lt;/script&gt;";

    var reloadFunc =[];
    reloadFunc['#widget-profile'] = function(){ reloadProfile(); };
    reloadFunc['#widget-leaderboard'] = function(){ reloadLeaderboard(); };
    reloadFunc['#widget-achievement'] = function(){ reloadAchievement(); };
    reloadFunc['#widget-quiz'] = function(){ reloadQuiz(); };
    reloadFunc['#widget-quest'] = function(){ reloadQuest(); };
    reloadFunc['#widget-feed'] = function(){ reloadFeed(); };
    reloadFunc['#widget-rewardstore'] = function(){ reloadRewardstore(); };
    reloadFunc['#widget-treasure'] = function(){ reloadTreasure(); };
    reloadFunc['#widget-trackevent'] = function(){ reloadTrackevent(); };

    reloadFunc['#widget-profile']();

    for( var widget_id in reloadFunc){
        $(widget_id+' input, '+widget_id+' select').bind("change paste blur", function() {
            var $tab = $(this).closest('.tab-pane');
            var _widget_id = $tab.attr('id');
            clearTimeout(timeout);
            timeout = setTimeout( reloadFunc['#'+_widget_id] ,timeBuffer);
        });
    }
    
    function updateParamForGlobal(url, codeHeader){
        var apikey = $('.wg-apikey').val();
        var player_id = $('.wg-player-id').val();
        var cssfile = $('.wg-cssfile').val();
        var color =getColor($('#widget-globel .wg-color').val());

        url+='&apikey='+apikey;
        codeHeader = codeHeader.replace('abc', apikey);

        if(typeof color != 'undefined' && color != ""){
            url+='&color='+color;
            var jsonColor = ',\n\t\ttheme_color : "#'+color+'"';
            codeHeader = codeHeader.replace("{{theme_color}}", jsonColor);
        }else{
            codeHeader = codeHeader.replace("{{theme_color}}", '');
        }

        if(typeof player_id != 'undefined' && player_id != ""){
            url+='&player_id='+player_id;
            var jsonPlayerId = ',\n\t\tplayer_id : "'+player_id+'"';
            codeHeader = codeHeader.replace("{{player_id}}", jsonPlayerId);
        }else{
            codeHeader = codeHeader.replace("{{player_id}}", '');
        }

        if(typeof cssfile != 'undefined' && cssfile != ""){
            url+='&cssfile='+cssfile;
            var jsonColor = ',\n\t\cssfile : "'+cssfile+'"';
            codeHeader = codeHeader.replace("{{cssfile}}", cssfile);
        }else{
            codeHeader = codeHeader.replace("{{cssfile}}", '');
        }

        return {url, codeHeader};
    }


    function reloadLeaderboard(){
      var width = getVal($('#widget-leaderboard .wg-width').val());
      var rankby =$('#widget-leaderboard .wg-rankby').val();
      var url = urlPreview+'?type=leaderboard';
      var codeElement = '&lt;div class="pb-leaderboard" ';
      var codeHeader = codeHeaderTemplate;

      var apikey = $('.wg-apikey').val();
      if(apikey){
            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            
            if(typeof rankby != 'undefined'  && rankby != ""){
                url+='&rankby='+rankby;
                codeElement += 'data-pb-rankBy="'+rankby+'" ';
            }
            codeElement += '&gt;&lt;/div&gt;';

            $('#iframe-leaderboard').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }

    function reloadProfile(){
        var width = getVal($('#widget-profile .wg-width').val());
        var displaypoint =getColor($('#widget-profile .wg-displaypoint').val());
        var url = urlPreview+'?type=profile';
        var codeElement = '&lt;div class="pb-profile" ';
        var codeHeader = codeHeaderTemplate;
        
        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            
            if(typeof displaypoint != 'undefined'  && displaypoint != ""){
                url+='&displaypoint='+displaypoint;
                codeElement += 'data-pb-displayPoint="'+displaypoint+'" ';
            }
            
            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-profile').attr('src',url);
            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }

    function reloadAchievement(){
        var width = getVal($('#widget-achievement .wg-width').val());
        var height = getVal($('#widget-achievement .wg-height').val());
        var url = urlPreview+'?type=achievement';
        var codeElement = '&lt;div class="pb-achievement" ';
        var codeHeader = codeHeaderTemplate;

        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            if(typeof height != 'undefined' && height != ""){
                url+='&height='+height;
                codeElement += 'data-pb-height="'+height+'" ';
            }

            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-achievement').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }
    function reloadQuiz(){
        var width = getVal($('#widget-quiz .wg-width').val());
        var height = getVal($('#widget-quiz .wg-height').val());
        var url = urlPreview+'?type=quiz';
        var codeElement = '&lt;div class="pb-quiz" ';
        var codeHeader = codeHeaderTemplate;
        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            if(typeof height != 'undefined' && height != ""){
                url+='&height='+height;
                codeElement += 'data-pb-height="'+height+'" ';
            }
            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-quiz').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }


    function reloadQuest(){
        var width = getVal($('#widget-quest .wg-width').val());
        var height = getVal($('#widget-quest .wg-height').val());
        var url = urlPreview+'?type=quest';
        var codeElement = '&lt;div class="pb-quest" ';
        var codeHeader = codeHeaderTemplate;
        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            if(typeof height != 'undefined' && height != ""){
                url+='&height='+height;
                codeElement += 'data-pb-height="'+height+'" ';
            }
            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-quest').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }

    function reloadFeed(){
        var width = getVal($('#widget-feed .wg-width').val());
        var height = getVal($('#widget-feed .wg-height').val());
        var url = urlPreview+'?type=feed';
        var codeElement = '&lt;div class="pb-feed" ';
        var codeHeader = codeHeaderTemplate;
        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            if(typeof height != 'undefined' && height != ""){
                url+='&height='+height;
                codeElement += 'data-pb-height="'+height+'" ';
            }
            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-feed').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }

    function reloadRewardstore(){
        var width = getVal($('#widget-rewardstore .wg-width').val());
        var height = getVal($('#widget-rewardstore .wg-height').val());
        var url = urlPreview+'?type=rewardstore';
        var codeElement = '&lt;div class="pb-rewardstore" ';
        var codeHeader = codeHeaderTemplate;
        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            if(typeof height != 'undefined' && height != ""){
                url+='&height='+height;
                codeElement += 'data-pb-height="'+height+'" ';
            }
            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-rewardstore').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }

    function reloadTreasure(){
        var width = getVal($('#widget-treasure .wg-width').val());
        var height = getVal($('#widget-treasure .wg-height').val());
        var url = urlPreview+'?type=treasure';
        var codeElement = '&lt;div class="pb-treasure" ';
        var codeHeader = codeHeaderTemplate;
        var apikey = $('.wg-apikey').val();

        var rule_id = getVal($('#widget-treasure .wg-rule-id').val());

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            if(typeof width != 'undefined' && width != ""){
                url+='&width='+width;
                codeElement += 'data-pb-width="'+width+'" ';
            }
            if(typeof height != 'undefined' && height != ""){
                url+='&height='+height;
                codeElement += 'data-pb-height="'+height+'" ';
            }
            if(typeof rule_id != 'undefined' && rule_id != ""){
                url+='&rule_id='+rule_id;
                codeElement += 'data-pb-rule-id="'+rule_id+'" ';
            }

            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-treasure').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }

    function reloadTrackevent(){
        
        var url = urlPreview+'?type=trackevent';
        var codeElement = '&lt;div class="pb-trackevent" ';
        var codeHeader = codeHeaderTemplate;
        var apikey = $('.wg-apikey').val();

        if(apikey){

            url = updateParamForGlobal(url, codeHeader).url;
            codeHeader = updateParamForGlobal(url, codeHeader).codeHeader;

            codeElement += '&gt;&lt;/div&gt;';
            $('#iframe-trackevent').attr('src',url);

            $('#getcode-modal .code-element').html(codeElement);
            $('#getcode-modal .code-header').html(codeHeader);
        }
    }


    function getVal(val){
      if(typeof val == 'undefined' ){
         return '';
     }
     var lastStr = val.slice(-1);
     if(lastStr == '%'){
         return val;
     }else{
         return val.replace(/[^0-9%]/g,'');
     }
    }
    function getColor(val){
      var color = val.replace(/#/g, '');
      return color;
    }


</script>

<div id="getcode-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:800px;margin-left:-400px">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3>Get Code</h3>
	  </div>
	  <div class="modal-body">
	  <p>Include the JavaScript SDK on your page once, ideally right after the opening &lt;body&gt; tag.</p>
	 	<pre class="prettyprint code-header">
&lt;script&gt;
window.PBAsyncInit = function(){
    PB.init({
        api_key:'abc',
        theme_color :'#52b398'
    });
};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://widget.pbapp.net/playbasis/en/all.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","playbasis-js"));&lt;/script&gt;
	            </pre>
	            <p>Place the code for your widget wherever you want the widget to appear on your page.</p>
	            <pre class="prettyprint code-element">
&lt;div class="pb-leaderboard"  data-pb-width="360" data-pb-rankBy="point" &gt;&lt;/div&gt;
	            </pre>
	  </div>
	</div>