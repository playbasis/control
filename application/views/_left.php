<!-- start: Main Menu -->
<div class="main-menu-span span2">
    <div class="nav-collapse sidebar-nav in collapse" style="height: auto;">
        <ul class="nav nav-tabs nav-stacked main-menu">
            <?php if (isset($features)) { ?>
            <?php foreach ($features as $feature) { ?>
                <li>
                    <?php
                    echo anchor($feature['link'], '<i class="'.$feature["icon"].'"></i><span class="hidden-tablet">'.$feature['name'].'</span>');
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