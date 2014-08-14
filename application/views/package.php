<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->
        <div class="content">
		Subscription plan: <?php echo $currentPlan['name'];?>
		<br/>
		Description plan: <?php echo $currentPlan['description']; ?>
		<br/>
		<?php 
			foreach($rewards as $reward){
				echo $reward['name'].": ";
				echo !empty($reward['limit'])?$reward['limit']:'unlimited';
				echo "<br/>";
			}
		?>
		<br/>
		Limit users: <?php echo $currentLimitPlayers['limit_users']; ?>
		<br/>
		Number of users: <?php echo $num_users; ?>
		
        </div><!-- .content -->
    </div><!-- .box -->




    


</div><!-- #content .span10 -->


