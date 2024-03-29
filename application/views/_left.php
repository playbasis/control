<?php
if (isset($username)) {
?>
<!-- start: Main Menu -->
<div class="main-menu-span span2" style="margin: 5px;">
    <div class="nav-collapse sidebar-nav in collapse" style="height: auto;">
        <ul class="nav nav-tabs nav-stacked main-menu">
            <?php
            if($this->session->userdata('client_id') && !$this->session->userdata('site_id')){
                ?>
                <li>
                    <?php
                    echo anchor("first_app", '<i class="fa fa-th-list"></i><span class="hidden-tablet">Get Started</span>');
                    ?>
                </li>
            <?php }else{ ?>
            <?php if (isset($features['controller'])) { ?>
            <li>
                <a class="Control_Config animated" style="border-radius: 3px;">
                    <i class="fa fa-cogs"></i>
                    <span class="hidden-tablet">Controller Configuration</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-left tab_left_group">
                    <?php
                    foreach ($features['controller'] as $feature){
                        echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                    }
                    ?>
                </div>
            </li>
            <?php } ?>

            <?php if (isset($features['reward'])) { ?>
            <li>
                <a class="Reward animated" style="border-radius: 3px;">
                    <i class="fa fa-trophy"></i>
                    <span class="hidden-tablet">Reward</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-left tab_left_group">
                    <?php
                    foreach ($features['reward'] as $feature){
                        echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                    }
                    ?>
                </div>
            </li>
            <?php } ?>

            <?php if (isset($features['data_content'])) { ?>
            <li>
                <a class="Data_Content animated" style="border-radius: 3px;">
                    <i class="fa fa-database"></i>
                    <span class="hidden-tablet">Data Content</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-left tab_left_group">
                    <?php
                    foreach ($features['data_content'] as $feature){
                        echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                    }
                    ?>
                </div>
            </li>
            <?php } ?>

            <?php if (isset($features['communication'])) { ?>
            <li>
                <a class="Communication animated" style="border-radius: 3px;">
                    <i class="fa fa-star"></i>
                    <span class="hidden-tablet">Communication</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-left tab_left_group">
                    <?php
                    foreach ($features['communication'] as $feature){
                        echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                    }
                    ?>
                </div>
            </li>
            <?php } ?>

            <?php if (isset($features['report'])) { ?>
            <li>
                <a class="Report animated" style="border-radius: 3px;">
                    <i class="fa fa-pie-chart"></i>
                    <span class="hidden-tablet">Report</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-left tab_left_group">
                    <?php
                    foreach ($features['report'] as $feature){
                        echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                    }
                    ?>
                </div>
            </li>
            <?php } ?>

            <?php if (isset($features['manage'])) { ?>
            <li>
                <a class="Admin animated" style="border-radius: 3px;">
                    <i class="fa fa-user"></i>
                    <span class="hidden-tablet">Admin</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-left tab_left_group">
                    <?php
                    foreach ($features['manage'] as $feature){
                        echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                    }
                    ?>
                </div>
            </li>
            <?php } ?>

            <?php if (isset($features['others'])) { ?>
                <?php foreach ($features['others'] as $feature){?>
                    <li>
                    <?php
                    echo anchor($feature['link'], '<i class="fa '.$feature["icon"].'"></i><span class="hidden-tablet">'.$feature['name'].'</span></br>');
                    ?>
                    </li>
                    <?php
                }
                ?>
            <?php } ?>
            <li>
                <?php
                echo anchor('logout', '<i class="icon-off icon-whit"></i><span class="hidden-tablet">'.$text_logout.'</span>');
                ?>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>
<!-- end: Main Menu -->
<style>
    .styleSubMenu{
        border-left: 4px solid;
        padding: 7px;
        margin: 7px 0px 5px 0px;
        background-color: rgba(10, 12, 14, 0.57);
        color: #ddd;
    }
    .styleSubMenu:hover .hidden-tablet{
        color: rgb(66, 101, 231);
    }
    a:link{
        text-decoration: none;
    }
    .iconMenu{
        float: right;
    }
    .tab-left{
        margin: 0px 15px 0px 35px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }
    .tagStyle{
        overflow: hidden;
    }
    .label + .tooltip > .tooltip-inner {
        max-width: 150px;
        white-space: normal;
        text-align: left;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $('.left-menu').click(function(){
            var url = $(this).attr('href');
            url = baseUrlPath+url.substring(1);
            $.ajax({
                type: "GET",
                url: url,
                beforeSend: function( xhr ) {
                    $("#top-header").prepend('<div class="ajax-loading"><span class="text-ajax-loading">Loading...</span></div>');
                }
            }).done(function( res ) {
                        $("#page-render").html(res);
                        $(".ajax-loading").remove();
                    });
        });
    });
</script>
<?php
}
?>

<script type="text/javascript">
    $('ul.main-menu > li > a').on("click",function () {

        var tapContent = this.nextElementSibling;
            if (tapContent.style.maxHeight){
                tapContent.style.maxHeight = null;
                $(this).css('transition','0.4s');
                $(this).css('borderLeft','0px solid #4265E7');
                $(this).css('background','none');
                $(this).children('i.iconMenu').css('transition', '0.4s');
                $(this).children('i.iconMenu').css('transform', 'rotate(0deg)');
            } else {
                tapContent.style.maxHeight = tapContent.scrollHeight + "px";
                $(this).css('transition','0.4s');
                $(this).css('borderLeft','6px solid #4265E7');
                $(this).css('background','#000');
                $(this).children('i.iconMenu').css('transition', '0.4s');
                $(this).children('i.iconMenu').css('transform', 'rotate(90deg)');
            }
    });

    $(function(){
        var current = location.href;
        var end_url = current.replace(baseUrlPath, "");
        var check_url = end_url.split("/");

        $('li .tab_left_group a > div').each(function(){
            var $this = $(this);
            if(($this.attr('data-url') == '/' && (current.toString() == $this.parent().attr('href'))) ){
                $this.addClass('active');
                this.style.borderLeftColor = '#4265E7';
                this.style.color = '#4265E7';
                $(this).parent().parent().siblings().click();
                return false;
            }else{
                var $tab_url = $this.attr('data-url');
                if(typeof $tab_url == "string"){
                    $tab_url = $tab_url.split("/");
                    if(check_url[0] == $tab_url[0]){
                        $this.addClass('active');
                        this.style.borderLeftColor = '#4265E7';
                        this.style.color = '#4265E7';
                        $(this).parent().parent().siblings().click();
                        return false;
                    }
                }
            }
        })

    });
</script>