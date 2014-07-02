<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> Available Plans</h1>
        </div><!-- .heading -->
        <div class="content">
		
		<?php 
			foreach($allPlans as $plan){
				echo "<pre>";
				var_dump($plan);
				echo "</pre>";
			}
		?>
		
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->