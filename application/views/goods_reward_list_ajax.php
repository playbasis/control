<?php
if($point_list){
    ?>
    <br>
    <button id="reward-entry" type="button" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_rewards'); ?></button>
    <div class="rewards">
        <div class="goods-panel">
            <?php
            foreach($point_list as $point){
                ?>
                <?php echo $point['name']; ?>
                <input type="text" name="reward_reward[<?php echo $point['_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="" /><br/>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>