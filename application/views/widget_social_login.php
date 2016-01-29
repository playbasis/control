<?php 
/* HARD CODE FOR TEST WIDGET */
$plan_widget['quest'] = true;
$plan_widget['feed'] = true;
$plan_widget['rewardstore'] = true;
$plan_widget['treasure'] = true;
$plan_widget['trackevent'] = true;
/* HARD CODE FOR TEST WIDGET */
?>

<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/widget/style.css">

<div id="content" class="span10 widget-page">
<div class="box">
<div class="heading">
    <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
</div><!-- .heading -->

<div class="content">
    <h1 class="hide"><?php echo $this->lang->line('text_choose_key'); ?></h1>

    <select class="wg-apikey hide" >
        <?php
        foreach($platform_data as $pfd){
            ?>
            <option  value="<?php echo $pfd["api_key"]; ?>"><?php echo $pfd["api_key"]; ?></option>
        <?php
        }
        ?>
    </select>

    <h1><?php echo $this->lang->line('text_choose_type'); ?></h1>

    <ul class="nav nav-tabs">

        <?php
        if(isset($plan_widget['social']) && $plan_widget['social']){
        ?>
        <li class="active"> <?php echo anchor('widget/social_login', $this->lang->line('column_social_login')); ?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_social_login'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['profile']) && $plan_widget['profile']){
        ?>
        <li> <?php echo anchor('widget#widget-profile', $this->lang->line('column_profile'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_profile'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['leaderboard']) && $plan_widget['leaderboard']){
        ?>
        <li> <?php echo anchor('widget#widget-leaderboard', $this->lang->line('column_leaderboard'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_leaderboard'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['achievement']) && $plan_widget['achievement']){
        ?>
        <li> <?php echo anchor('widget#widget-achievement', $this->lang->line('column_achievement'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_achievement'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['quiz']) && $plan_widget['quiz']){
        ?>
        <li> <?php echo anchor('widget#widget-quiz', $this->lang->line('column_quiz'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_quiz'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['quest']) && $plan_widget['quest']){
        ?>
        <li> <?php echo anchor('widget#widget-quest', $this->lang->line('column_quest'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_quest'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['feed']) && $plan_widget['feed']){
        ?>
        <li> <?php echo anchor('widget#widget-feed', $this->lang->line('column_feed'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_feed'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['rewardstore']) && $plan_widget['rewardstore']){
        ?>
        <li> <?php echo anchor('widget#widget-rewardstore', $this->lang->line('column_rewardstore'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_rewardstore'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['treasure']) && $plan_widget['treasure']){
        ?>
        <li> <?php echo anchor('widget#widget-treasure', $this->lang->line('column_treasure'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_treasure'); ?></a></li>
        <?php
        }
        ?>

        <?php
        if(isset($plan_widget['trackevent']) && $plan_widget['trackevent']){
        ?>
        <li> <?php echo anchor('widget#widget-trackevent', $this->lang->line('column_trackevent'));?></li>
        <?php
        }else{
        ?>
        <li> <a href="javascript:void(0)" class="disabled" disabled style="color:#646464;cursor: default;" ><?php echo $this->lang->line('column_trackevent'); ?></a></li>
        <?php
        }
        ?>
    </ul>

    <div class="tab-content">
    <div class="tab-social">

    <h3>Social Login<?php echo $this->lang->line('text_social_login_widget'); ?></h3>

    <div class="row">

        <div class="span11">
            <div class="controls">
                <h3>Manage Social</h3>
                <label>CallBackURL :<input type="text" id="social-callback" name="callback" placeholder="callback" value="<?php echo $callback? $callback : ''; ?>"></label>

                <a href="javascript:void(0);" onclick="saveSocial()" class="btn btn-primary">Save All</a>
            </div>
        </div>

        <div class="span11">
            <?php
            $attributes = array('class' => 'form-horizontal');
            echo form_open('', $attributes);?>
                <div id="social-panel">
                <?php
                $social_system = array(
                    "facebook" => array(
                        "color" => "social-blue",
                        "icon" => "fa-facebook",
                        "key" => $social_widget&&isset($social_widget['facebook']['key'])?$social_widget['facebook']['key']:"",
                        "secret" => $social_widget&&isset($social_widget['facebook']['secret'])?$social_widget['facebook']['secret']:"",
                        "sort_order" => $social_widget&&isset($social_widget['facebook']['sort_order'])?$social_widget['facebook']['sort_order']:"0",
                        "status" => $social_widget&&isset($social_widget['facebook']['status'])?($social_widget['facebook']['status']?"enabled":"disabled"):"enabled",
                        "tooltip" => htmlentities("<p>
                                        1. Go to https://developers.facebook.com/apps and create a new application by clicking 'Create New App'.<br>
                                        2. Fill out any required fields such as the application name and description.<br>
                                        3. Put 'https://widget.pbapp.net/' in the Site Url field.<br><br>
                                        <img src='".base_url("image/widgets/facebook-siteurl.png")."' ></p>")
                    ),
                    "twitter" => array(
                        "color" => "social-azure",
                        "icon" => "fa-twitter",
                        "key" => $social_widget&&isset($social_widget['twitter']['key'])?$social_widget['twitter']['key']:"",
                        "secret" => $social_widget&&isset($social_widget['twitter']['secret'])?$social_widget['twitter']['secret']:"",
                        "sort_order" => $social_widget&&isset($social_widget['twitter']['sort_order'])?$social_widget['twitter']['sort_order']:"0",
                        "status" => $social_widget&&isset($social_widget['twitter']['status'])?($social_widget['twitter']['status']?"enabled":"disabled"):"disabled",
                        "tooltip" => "<p>
                                        1. Go to https://dev.twitter.com/apps and create a new application.<br>
                                        2. Fill out any required fields such as the application name and description.<br>
                                        3. Put 'https://widget.pbapp.net/' in the Website field.<br>
                                        4. Provide this URL as the Callback URL for your application: 'https://widget.pbapp.net/playbasis/login/auth/?auth=Twitter'.<br><br>
                                        <img src='".base_url("image/widgets/twitter-siteurl.png")."' ></p>"
                    ),
                    "google-plus" => array(
                        "color" => "social-scarlet",
                        "icon" => "fa-google-plus",
                        "key" => $social_widget&&isset($social_widget['google-plus']['key'])?$social_widget['google-plus']['key']:"",
                        "secret" => $social_widget&&isset($social_widget['google-plus']['secret'])?$social_widget['google-plus']['secret']:"",
                        "sort_order" => $social_widget&&isset($social_widget['google-plus']['sort_order'])?$social_widget['google-plus']['sort_order']:"0",
                        "status" => $social_widget&&isset($social_widget['google-plus']['status'])?($social_widget['google-plus']['status']?"enabled":"disabled"):"disabled",
                        "tooltip" => "<p>
                                        1. Go to https://code.google.com/apis/console/ and create a new project.<br>
                                        2. Go to API Access under API Project. After that click on Create an OAuth 2.0 client ID to create a new application.<br>
                                        3. A pop-up named 'Create Client ID' will appear, fill out any required fields such as the application name and description.<br>
                                        4. Click on Next.<br>
                                        5. On the popup set Application type to Web application and switch to advanced settings by clicking on (more options).<br>
                                        6. Provide this URL as the Callback URL for your application: 'https://widget.pbapp.net/playbasis/login/auth/?auth=Google' .<br><br>
                                        <img src='".base_url("image/widgets/google-siteurl.png")."' ></p>"
                    ),
                    "linkedin" => array(
                        "color" => "social-cesocialan",
                        "icon" => "fa-linkedin",
                        "key" => $social_widget&&isset($social_widget['linkedin']['key'])?$social_widget['linkedin']['key']:"",
                        "secret" => $social_widget&&isset($social_widget['linkedin']['secret'])?$social_widget['linkedin']['secret']:"",
                        "sort_order" => $social_widget&&isset($social_widget['linkedin']['sort_order'])?$social_widget['linkedin']['sort_order']:"0",
                        "status" => $social_widget&&isset($social_widget['linkedin']['status'])?($social_widget['linkedin']['status']?"enabled":"disabled"):"disabled",
                        "tooltip" => "<p>
                                        1. Go to https://www.linkedin.com/secure/developer (or https://www.linkedin.com/secure/developer?newapp=) and create a new application.<br>
                                        2. Fill out any required fields such as the application name and description.<br>
                                        3. Put 'https://widget.pbapp.net/' in the Integration URL and OAuth Redirect URL fields.<br><br>
                                        <img src='".base_url("image/widgets/linkedin-siteurl.png")."' ></p>"
                    ),
                    "instagram" => array(
                        "color" => "social-persian-blue",
                        "icon" => "fa-instagram",
                        "key" => $social_widget&&isset($social_widget['instagram']['key'])?$social_widget['instagram']['key']:"",
                        "secret" => $social_widget&&isset($social_widget['instagram']['secret'])?$social_widget['instagram']['secret']:"",
                        "sort_order" => $social_widget&&isset($social_widget['instagram']['sort_order'])?$social_widget['instagram']['sort_order']:"0",
                        "status" => $social_widget&&isset($social_widget['instagram']['status'])?($social_widget['instagram']['status']?"enabled":"disabled"):"disabled",
                        "tooltip" => "<p>
                                        1. Go to http://instagram.com/developer and Register a new Client.<br>
                                        2. Fill out any required fields such as the application name and description.<br>
                                        3. Put 'https://widget.pbapp.net/' in the Website field.<br>
                                        4. Put https://widget.pbapp.net/playbasis/login/auth/?auth=Instagram OAuth Redirect URL fields.<br><br>
                                        <img src='".base_url("image/widgets/instagram-siteurl.png")."' ></p>"
                    ),
                );
                ?>
                <?php
                foreach($social_system as $k=>$s){
                ?>
                    <!-- start -->
                    <div class="social-container <?php echo $s["color"]; ?>">
                        <div class="social" id="social-<?php echo $k; ?>">

                            <div class="social-header social-box-header">
                                <i class="fa <?php echo $s["icon"]; ?>"></i>
                            </div>

                            <div class="social-name">
                                <h4 title="<?php echo $k; ?>"><?php echo $k; ?></h4>
                            </div>

                            <div class="social-minimize">
                                <span title="Minimize social">X</span>
                            </div>

                            <div class="social-content">

                                <div class="social-key">
                                    <input type="text" name="key" placeholder="api key" value="<?php echo $s["key"]; ?>">
                                    <i class="icon-pencil"></i>
                                </div>

                                <div class="social-secret">
                                    <input type="text" name="secret" placeholder="api secret" value="<?php echo $s["secret"]; ?>">
                                    <i class="icon-pencil"></i>
                                </div>

                                <div class="social-sort_order">
                                    <input type="number" name="sort_order" placeholder="sort order" value="<?php echo $s["sort_order"]; ?>">
                                    <i class="icon-pencil"></i>
                                </div>

                            </div>

                            <div class="social-footer">

                                <div class="social-controls">
                                    <button class="btn btn-primary social-controls-save" type="submit">Save</button>
                                    <button class="btn social-controls-cancel" type="button">Cancel</button>
                                    <button class="btn social-help" type="button" title="<?php echo $s["tooltip"] ?>">?</button>
                                </div>

                                <div class="social-status <?php echo $s["status"]; ?>">
                                    <div class="social-status-toggle">
                                        <span><?php echo $s["status"]; ?></span>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    <!-- end -->
                <?php
                }
                ?>
                </div>

                <div class="control-group">
                    <label class="control-label" ></label>
                    <div class="controls">

                        <input type="checkbox" id="no-logout" name="no-logout" checked  /> Show Logout <br><br>

                        <a href="javascript:void(0);" onclick="reloadSocial()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                        <a href="#getcode-modal" role="button" data-toggle="modal" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                    </div>
                </div>
            <?php echo form_close();?>

        </div>
        <div class="span11">
            <iframe id="iframe-login" src="<?php echo base_url();?>index.php/widget/preview?type=login" width="100%" height="280" frameborder="0"></iframe>
        </div>
    </div>
</div><!-- .tab-pane -->
</div>
</div><!-- .content -->
</div><!-- .box -->
</div><!-- #content .span10 -->

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/widget/tooltipster.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/widget/jquery.tooltipster.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/widget/social.js"></script>
<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
$(document).ready(function(){

    $('form').submit(function(e){
        e.preventDefault();
    });

    $('#getcode-modal').on('show', function () {
        reloadSocial();
    })

    reloadSocial();
});
var tabActive = '#widget-leaderboard';
var timeBuffer = 500;
var isReload = false;
var timeout = setTimeout(void(0),0);
var codeHeaderTemplate= "&lt;script&gt;\nwindow.PBAsyncInit = function(){\n\tPB.init({\n\t\tapi_key:'abc',\n\t\ttheme_color :'#0e9ce4'\n\t});\n};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://widget.pbapp.net/playbasis/en/all.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','playbasis-js'));&lt;/script&gt;";
var codeHeaderPlayerTestTemplate= "&lt;script&gt;\nwindow.PBAsyncInit = function(){\n\tPB.init({\n\t\tapi_key:'abc',\n\t\ttheme_color :'#0e9ce4',\n\t\tplayerId :'playertest'\n\t});\n};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://widget.pbapp.net/playbasis/en/all.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','playbasis-js'));&lt;/script&gt;";

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
function reloadSocial(){
    var url = baseUrlPath+'widget/preview?type=login';
    var codeElement = '&lt;div class="pb-login" ';
    var codeHeader = codeHeaderTemplate;

    var apikey = $('.wg-apikey').val();

    if(apikey){

        url+='&apikey='+apikey;
        codeHeader = codeHeader.replace('abc', apikey);
        if(!$('#no-logout').is(':checked')){
            codeElement+= ' data-pb-nologout ';
            url+= '&nologin=true'
        }
        codeElement += '&gt;&lt;/div&gt;';

        $('#iframe-login').attr('src',url);
        $('#getcode-modal .code-element').html(codeElement);
        $('#getcode-modal .code-header').html(codeHeader);
    }
}

$(document).ready(function(){
    $(".wg-apikey option:first").attr('selected','selected');

    $('.social-help').tooltipster({
        contentAsHTML: true,
        position: 'bottom',
        trigger: 'click'
    });

});


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
        api_key:'abc'
    });
};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://widget.pbapp.net/playbasis/en/all.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","playbasis-js"));&lt;/script&gt;
	            </pre>
        <p>Place the code for your widget wherever you want the widget to appear on your page.</p>
	            <pre class="prettyprint code-element">
&lt;div class="pb-login" &gt;&lt;/div&gt;
	            </pre>
    </div>
</div>