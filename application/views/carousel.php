<?php if (isset($events) && $events) { foreach ($events as $event) { ?>
<div class="circleStatsItemContainer">
    <div class="circleStatsItem <?php echo $event['color']; ?>">
        <i class="<?php echo $event['class']; ?>"></i>
        <span class="plus"><?php echo $event['advancement_direction']; ?></span>
        <span class="percent">%</span>
        <input type="text" value="<?php echo $event['advancement_rate']; ?>" class="<?php echo $event['circle']; ?>" />
    </div>
    <div class="box-small-title">
        <?php echo $event['value']; ?>
        <?php echo $event['name']; ?>
    </div>
</div>
<?php } } ?>