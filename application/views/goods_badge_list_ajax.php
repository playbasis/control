<?php
if($badge_list){
    ?>
    <br>
    <button id="badge-entry" type="button" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>
    <div class="badges">
        <div class="goods-panel">
            <?php
            foreach($badge_list as $badge){
                ?>
                <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="<?php echo base_url();?>image/default-image.png" />
                <input type="text" name="reward_badge[<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="" /><br/>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>