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
    .tab-content{
        margin: 0px 15px 0px 35px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }
</style>
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
            <li>
                <a class="Control_Config animated" style="border-radius: 3px;">
                    <i class="fa fa-cogs"></i>
                    <span class="hidden-tablet">Controller Configuration</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-content" id="tab_control_config">
                    <?php if (isset($features)) { ?>
                        <?php foreach ($features as $feature){
                            if($feature['type'] == 'controller') {
                                ?>
                                <?php
                                echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                                ?>
                                <?php
                            }
                        }
                        ?>
                    <?php } ?>
                    <div class="test"></div>
                </div>
            </li>
            <li>
                <a class="Reward animated" style="border-radius: 3px;">
                    <i class="fa fa-trophy"></i>
                    <span class="hidden-tablet">Reward</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-content" id="tab_reward">
                    <?php if (isset($features)) { ?>
                        <?php foreach ($features as $feature){
                            if($feature['type'] == 'reward'){
                                ?>
                                <?php
                                echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                                ?>
                                <?php
                            }
                        }
                        ?>
                    <?php } ?>

                </div>
            </li>
            <li>
                <a class="Data_Content animated" style="border-radius: 3px;">
                    <i class="fa fa-database"></i>
                    <span class="hidden-tablet">Data Content</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-content" id="tab_Data_Content">
                                <?php if (isset($features)) { ?>
                                    <?php foreach ($features as $feature){
                                        if($feature['type'] == 'data_content'){
                                            ?>
                                                <?php
                                                echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                                                ?>
                                            <?php
                                        }
                                    }
                                    ?>
                                <?php } ?>
                                <?php
                            }
                            ?>
                </div>
            </li>
            <li>
                <a class="Comunication animated" style="border-radius: 3px;">
                    <i class="fa fa-star"></i>
                    <span class="hidden-tablet">Comunication</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-content" id="tab_comunication">
                        <?php if (isset($features)) { ?>
                            <?php foreach ($features as $feature){
                                if($feature['type'] == 'comunication'){
                                    ?>
                                    <?php
                                    echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                                    ?>
                                    <?php
                                }
                            }
                            ?>
                        <?php } ?>
                </div>
            </li>
            <li>
                <a class="Report animated" style="border-radius: 3px;">
                    <i class="fa fa-pie-chart"></i>
                    <span class="hidden-tablet">Report</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-content" id="tab_report">
                    <?php if (isset($features)) { ?>
                        <?php foreach ($features as $feature){
                            if($feature['type'] == 'report'){
                                ?>
                                <?php
                                echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                                ?>
                                <?php
                            }
                        }
                        ?>
                    <?php } ?>
                </div>
            </li>
            <li>
                <a class="Admin animated" style="border-radius: 3px;">
                    <i class="fa fa-user"></i>
                    <span class="hidden-tablet">Admin</span>
                    <i class="iconMenu fa fa-caret-right"></i>
                </a>
                <div class="tab-content" id="tab_admin">
                    <?php if (isset($features)) { ?>
                        <?php foreach ($features as $feature){
                            if($feature['type'] == 'manage'){
                                ?>
                                <?php
                                echo anchor($feature['link'], '<div class="styleSubMenu" data-url="'.$feature['link'].'"><i class="hidden-tablet fa '.$feature["icon"].'"></i> <span class="hidden-tablet">'.$feature['name'].'</span></br></div>');
                                ?>
                                <?php
                            }
                        }
                        ?>
                    <?php } ?>

                </div>
            </li>
            <?php if (isset($features)) { ?>
                <?php foreach ($features as $feature){
                    if($feature['type'] == null){
                        ?>
                        <li>
                        <?php
                        echo anchor($feature['link'], '<i class="fa '.$feature["icon"].'"></i><span class="hidden-tablet">'.$feature['name'].'</span></br>');
                        ?>
                        </li>
                        <?php
                    }
                }
                ?>
            <?php } ?>
            <li>
                <?php
                echo anchor('logout', '<i class="icon-off icon-whit"></i><span class="hidden-tablet">'.$text_logout.'</span>');
                ?>
            </li>
        </ul>
    </div>
</div>
<!-- end: Main Menu -->
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
       console.log($(this).siblings().attr('id'))
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

        $('li #tab_control_config a > div').each(function(){
            var $this = $(this);
            if(current.toString().includes($this.attr('data-url'))){
                $this.addClass('active');
                this.style.borderLeftColor = '#4265E7';
                this.style.color = '#4265E7';
                $(this).parent().parent().siblings().click();
            }
        })
        $('li #tab_reward a > div').each(function(){
            var $this = $(this);
            if(current.toString().includes($this.attr('data-url'))){
                $this.addClass('active');
                this.style.borderLeftColor = '#4265E7';
                this.style.color = '#4265E7';
                $(this).parent().parent().siblings().click();
            }
        })
        $('li #tab_Data_Content a > div').each(function(){
            var $this = $(this);
            if(current.toString().includes($this.attr('data-url'))){
                $this.addClass('active');
                this.style.borderLeftColor = '#4265E7';
                this.style.color = '#4265E7';
                $(this).parent().parent().siblings().click();
            }
        })
        $('li #tab_comunication a > div').each(function(){
            var $this = $(this);
            if(current.toString().includes($this.attr('data-url'))){
                $this.addClass('active');
                this.style.borderLeftColor = '#4265E7';
                this.style.color = '#4265E7';
                $(this).parent().parent().siblings().click();
            }
        })
        $('li #tab_report a > div').each(function(){
            var $this = $(this);
            if(($this.attr('data-url') == '/' && (current.toString() == $this.parent().attr('href'))) || $this.attr('data-url') != '/' && (current.toString().includes($this.attr('data-url'))) ){
                    $this.addClass('active');
                    this.style.background = '#4265E7';
                    $(this).parent().parent().siblings().click();
            }
        })
        $('li #tab_admin a > div').each(function(){
            var $this = $(this);
            if(current.toString().includes($this.attr('data-url'))){
                $this.addClass('active');
                this.style.borderLeftColor = '#4265E7';
                this.style.color = '#4265E7';
                $(this).parent().parent().siblings().click();
            }
        })
    });
</script>


