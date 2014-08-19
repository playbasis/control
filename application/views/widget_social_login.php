<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/widget/style.css">

<div id="content" class="span10 widget-page">
<div class="box">
<div class="heading">
    <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
</div><!-- .heading -->

<div class="content">
<h1><?php echo $this->lang->line('text_choose_type'); ?></h1>

<ul class="nav nav-tabs">
    <li class="active"><a href="<?php echo base_url();?>widget/social_login"><?php echo $this->lang->line('column_social_login'); ?></a></li>
    <li> <?php echo anchor('widget#widget-leaderboard', $this->lang->line('column_leaderboard'));?></li>
    <li> <?php echo anchor('widget#widget-livefeed', $this->lang->line('column_livefeed'));?></li>
    <li> <?php echo anchor('widget#widget-profile', $this->lang->line('column_profile'));?></li>
    <li> <?php echo anchor('widget#widget-userbar', $this->lang->line('column_userbar'));?></li>
</ul>

<div class="tab-content">
<div class="tab-social">

    <h3><?php echo $this->lang->line('text_social_login_widget'); ?></h3>

    <div class="row">
        <div class="span5">
            <form class="form-horizontal">

                <div id="add-social" class="social-container">
                    <div class="social">

                        <div class="add-social">
                            <i class="icon-plus"></i>
                            <span>Add Social</span>
                        </div>

                        <div class="social-header social-box-header">
                            <i class="icon-plus"></i>
                        </div>

                        <div class="social-minimize">
                            <span title="Minimize Social">X</span>
                        </div>
                    </div>
                </div>
                <div class="social-panel">
                    <div class="social-blue">
                        <div class="social-template social-box social-box-extra">
                            <div class="social-template-provider social-box-header">
                                <i class="icon-bookmark-empty"></i>
                                <span>Facebook</span>
                            </div>
                            <div class="social-template-button social-box-footer social-facebook">
                                <span>Edit</span>
                            </div>
                        </div>
                    </div>

                    <div class="social-azure">
                        <div class="social-template social-box social-box-extra">
                            <div class="social-template-provider social-box-header">
                                <i class="icon-bookmark-empty"></i>
                                <span>Twitter</span>
                            </div>
                            <div class="social-template-button social-box-footer social-twitter">
                                <span>Edit</span>
                            </div>
                        </div>
                    </div>

                    <div class="social-scarlet">
                        <div class="social-template social-box social-box-extra">
                            <div class="social-template-provider social-box-header">
                                <i class="icon-bookmark-empty"></i>
                                <span>Google Plus</span>
                            </div>
                            <div class="social-template-button social-box-footer social-google-plus">
                                <span>Edit</span>
                            </div>
                        </div>
                    </div>

                    <div class="social-cerulean">
                        <div class="social-template social-box social-box-extra">
                            <div class="social-template-provider social-box-header">
                                <i class="icon-bookmark-empty"></i>
                                <span>Linkedin</span>
                            </div>
                            <div class="social-template-button social-box-footer social-twitter">
                                <span>Edit</span>
                            </div>
                        </div>
                    </div>

                    <div class="social-persian-blue">
                        <div class="social-template social-box social-box-extra">
                            <div class="social-template-provider social-box-header">
                                <i class="icon-bookmark-empty"></i>
                                <span>Instagram</span>
                            </div>
                            <div class="social-template-button social-box-footer social-instagram">
                                <span>Edit</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" ></label>
                    <div class="controls">
                        <a href="javascript:void(0);" onclick="reloadProfile()" class="btn"><?php echo $this->lang->line('text_preview'); ?></a>
                        <a href="#getcode-modal" role="button" data-toggle="modal" class="btn btn-primary" class="getcode-btn"><?php echo $this->lang->line('text_get_code'); ?></a>
                    </div>
                </div>

            </form>

        </div>
        <div class="span11">
            <iframe id="iframe-profile" src="<?php echo base_url();?>index.php/widget/preview?type=profile" width="100%" height="280" frameborder="0"></iframe>
        </div>
    </div>
</div><!-- .tab-pane -->
</div>
</div><!-- .content -->
</div><!-- .box -->
</div><!-- #content .span10 -->



<script>
$(document).ready(function(){

    var $container = $('.social-panel').isotope({
        itemSelector: '.element-item',
        layoutMode: 'fitRows',
        getSortData: {
            name: '.name',
            symbol: '.symbol',
            number: '.number parseInt',
            category: '[data-category]',
            weight: function( itemElem ) {
                var weight = $( itemElem ).find('.weight').text();
                return parseFloat( weight.replace( /[\(\)]/g, '') );
            }
        }
    });

    $('form').submit(function(e){
        e.preventDefault();
    });

    $('#getcode-modal').on('show', function () {

    })

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
        api_key:'<?php echo $site_data["api_key"]; ?>',
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