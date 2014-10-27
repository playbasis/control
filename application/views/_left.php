<?php
if (isset($username)) {
?>
<!-- start: Main Menu -->
<div class="main-menu-span span2">
    <div class="nav-collapse sidebar-nav in collapse" style="height: auto;">
        <ul class="nav nav-tabs nav-stacked main-menu">
            <?php if (isset($features)) { ?>
            <?php foreach ($features as $feature) { ?>
                <li>
                    <?php
                    echo anchor($feature['link'], '<i class="fa '.$feature["icon"].'"></i><span class="hidden-tablet">'.$feature['name'].'</span>');
                    ?>
                    <?php
                    if(false){
                    ?>
                    <a href="#<?php echo $feature['link']; ?>" class="left-menu"><i class="<?php echo $feature["icon"]; ?>"></i><span class="hidden-tablet"><?php echo $feature['name']; ?></span></a>
                    <?php
                    }
                    ?>
                </li>
                <?php } ?>
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