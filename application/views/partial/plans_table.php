<link rel="stylesheet" type="text/css" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<style type="text/css">
	#content.plan-price-wrapper{
		background: #fff;
	}
	

	.item-plan-wrapper {
		width: 16%;
		float: left;
		box-sizing: border-box;
		-mz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-o-transition: all 0.3s ease;
		-webkit-transition: all 0.3s ease;
		transition: all 0.3s ease;
		-webkit-box-shadow: 0 0 0px rgba(0, 0, 0, 0.2);
		box-shadow: 0 0 0px rgba(0, 0, 0, 0.2);
	}
	.item-plan-head {
		padding: 20px;
		margin-bottom: 10px;
		background: #fff;
		height: 80px;
		border-bottom: 1px solid #f5f5f5;
	}
	.item-plan-head .title {
		color: #9547d7;
		font-weight: lighter;
		margin-bottom: 5px;
	}
	.item-plan-head .price {
		color: #9547d7;
		font-weight: lighter;
		line-height: 1em;
		font-size: 2em;
	}
	.item-plan-head .price span {
		font-size: 12px;
	}
	.pricing-wrapper{
		padding-bottom: 80px;
	}
	.pricing-wrapper:after{
		display: block;
		clear: both;
		content: '';
	}
	.pricing-wrapper ul li{
		line-height: 25px;
		list-style: none;
		padding-left: 20px;
		font-size: 12px;
		text-align: center;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.item-plan-wrapper.enterprise .title {
		color: #ff8b2e;
	}
	.item-plan-wrapper.enterprise .price {
		color: #ff8b2e;
	}
	.item-plan-wrapper:not(.item-plan-wrapper-header):hover {
		-webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3);
		box-shadow: 0 0 1px rgba(0, 0, 0, 0.3);
	}
	.item-plan-wrapper:not(.item-plan-wrapper-header):hover .item-plan-head {
		background: #9547d7;
	}
	.item-plan-wrapper:not(.item-plan-wrapper-header):hover .item-plan-head .title {
		color: #fff;
	}
	.item-plan-wrapper:not(.item-plan-wrapper-header):hover .item-plan-head .price {
		color: #fff;
	}
	.item-plan-wrapper.enterprise:hover .item-plan-head {
		background: #ff8b2e;
	}
	.item-plan-main-feature {
		border-bottom: 2px solid #f4f4f4;
		padding-bottom: 15px;
	}
	.item-plan-kit {
		border-bottom: 1px solid #f4f4f4;
		padding-bottom: 20px;
	}
	.item-plan-kit-check {
		height: 100px;
	}
	.item-plan-kit-check {
		text-align: center;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		padding-top: 30px;
	}
	.item-plan-kit-check img.check {
		display: none;
	}
	.item-plan-kit-check > a {
		display: block;
	}
	.item-plan-kit-check > a i {
		font-size: 46px;
		line-height: 1em;
	}
	.build_kit .item-plan-kit-check i {
		color: #2270b9;
	}
	.game_kit .item-plan-kit-check i {
		color: #ff7929;
	}
	.reward_kit .item-plan-kit-check i {
		color: #98c102;
	}
	.data_kit .item-plan-kit-check i {
		color: #9547d7;
	}
	.grow_kit .item-plan-kit-check i {
		color: #00c9a3;
	}
	.item-plan-kit-detail {
		overflow: hidden;
		max-height: 0;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		-o-transition: all 0.3s ease;
		-webkit-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	.item-plan-kit-detail li {
		-o-transition: all 0.3s ease;
		-webkit-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	.item-plan-kit-detail li.hover {
		background: #f5f5f5;
	}
	.item-plan-kit-detail.show {
		max-height: 600px;
	}
	
	.plan-header .item-plan-kit,
	.plan-header .item-plan-main-feature {
		border-bottom: none;
	}
	.plan-header .item-plan-kit-check {
		padding-top: 0;
	}
	.plan-header .item-plan-kit-detail li.hover {
		background: transparent;
	}
	.item-plan-action{
		padding-top: 20px;
	}
	.current-plan-text{
		border-radius: 4px;
		border: 1px solid #ddd;
	}
	.plan-btn,
	.current-plan-text{
		height: 30px;
		line-height: 30px;
		padding: 4px 12px;
		display: inline-block;
	}
	.plan-btn{
		background: #86559C;
		color: #fff;
		border: 0;
		text-shadow: none;
	}
	.plan-btn:hover{
		background: #734388;
		color: #fff;
	}
	.plan-btn:focus{
		background: #86559C;
		color: #fff;	
	}
	.plan-btn.plan-enterprise-btn,
	.plan-btn.plan-enterprise-btn:focus{
		background: #ff8b2e;
	}
	.plan-btn.plan-enterprise-btn:hover{
		background: #ff7929;
	}
	@media all and (max-width: 1145px) {
		.item-plan-main-feature ul li{
				font-size: 10px;
		}
	}
	
	@media all and (max-width: 980px) {
		.plan-header {
			display: none;
		}
		.plan-content {
			width: 100%;
		}
		.item-plan-wrapper {
			margin-left: auto;
			margin-right: auto;
			margin-bottom: 40px;
			width: 420px;
			max-width: 90%;
			float: none;
			-webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}
		.item-plan-wrapper .item-plan-head {
			padding: 10px 20px;
			background: #9547d7;
			min-height: auto;
		}
		.item-plan-wrapper .item-plan-head .title {
			color: #fff;
			text-align: center;
		}
		.item-plan-wrapper .item-plan-head .price {
			color: #fff;
			text-align: center;
		}
		.item-plan-wrapper .item-plan-kit {
			border-bottom: none;
			padding-bottom: 0px;
		}
		.item-plan-wrapper .item-plan-kit-check {
			height: auto;
			text-align: center;
			padding: 0;
		}
		.item-plan-wrapper .item-plan-kit-check img.check {
			margin-top: 10px;
			margin-bottom: 10px;
			display: inline-block;
		}
		.item-plan-wrapper .item-plan-kit-check a i {
			display: none;
		}
		.item-plan-wrapper.enterprise .item-plan-head {
			background: #ff8b2e;
		}
		.item-plan-wrapper.free .title {
			display: none;
		}
		.item-plan-wrapper.free .price {
			margin-top: 10px;
		}
		.item-plan-main-feature ul li{
			font-size: 13px;
			text-align: center;
		}
		.item-plan-action{
			border-top: 1px solid #f4f4f4;
			
		}
		.item-plan-action>a,
		.item-plan-action>span{
			margin-bottom: 20px;		
		}
		
	}
</style>

<?php

    	$plans = json_decode( file_get_contents( base_url().'json/plans.json') , true );
    	$full_feature_plan = end($plans);

    	$res_plans_detail = json_decode( file_get_contents( API_SERVER.'/playbasis/plans' ) );

    	$plans_id = array();
    	if( !empty( $res_plans_detail->response ) ){
    		$plans_detail = $res_plans_detail->response;
    		$plans_id = get_plans_id($plans_detail);	
    	}
    	
    	function get_plans_id($plans_detail){
    		$plans_id = array();
    		foreach ($plans_detail as $value) {

    			switch ($value->name) {
    				case 'Basic':
    					$plans_id['free'] = $value->_id;
    					break;

    				case 'Startup Plan':
    					$plans_id['s'] = $value->_id;
    					break;

    				case 'Business Plan':
    					$plans_id['m'] = $value->_id;
    					break;

    				case 'Professional Plan':
    					$plans_id['l'] = $value->_id;
    					break;

    				case 'Enterprise':
    					$plans_id['enterprise'] = $value->_id;
    					break;

    				default:
    					
    					break;
    			}
    		}
    		return $plans_id;
    	}

?>
<section class="pricing-wrapper plan-wrapper">
	<div class="plan-container">

		<div class="plan-header">
			<div class="item-plan-wrapper item-plan-wrapper-header">

				<div class="item-plan-head">
					<div class="title">&nbsp;</div>
					<h3 class="price">&nbsp;</h3>
				</div>
				<div class="item-plan-main-feature">
					<ul>
						<li>&nbsp;</li>
						<li>&nbsp;</li>
						<li>&nbsp;</li>
					</ul>
				</div>

				<?php foreach ($full_feature_plan['kits'] as $kit) : ?>
					<div class="item-plan-kit <?php echo $kit['name'] ?>">
						<div class="item-plan-kit-check">
							<img src="<?php echo base_url();?>image/kits-banner/<?php echo $kit['name'] ?>.jpg">
						</div>
						<div class="item-plan-kit-detail">
							<ul>
								<?php foreach ($kit['feature'] as $feature) : ?>
									<li>&nbsp;</li>
								<?php endforeach; ?>
							</ul>
						</div>                    
					</div>
				<?php endforeach; ?>

			</div>

		</div>

		<div class="plan-content">

			<?php foreach ($plans as $key => $plan) : ?>
				<div class="item-plan-wrapper <?php echo $plan['name'] ?>">
					<div class="item-plan-head">
						<?php if( $plan['name'] == 'free' ): ?>
							<div class="title">&nbsp;</div>
							<h3 class="price"><?php echo $plan['price'] ?><span></span></h3>
						<?php elseif ($plan['name'] == 'enterprise' ): ?>
							<div class="title"><?php echo $plan['title'] ?></div>
							<h3 class="price"><?php echo $plan['price'] ?><span></span></h3>
						<?php else: ?>
							<div class="title"><?php echo $plan['title'] ?></div>
							<h3 class="price"><?php echo $plan['price'] ?><span>/mo</span></h3>
						<?php endif; ?>
					</div>
					<div class="item-plan-main-feature">
						<ul>
							<?php foreach ($plan['main_feature'] as $main_feature) : ?>
								<li><?php echo $main_feature; ?></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<?php foreach ($plan['kits'] as $kit) : ?>
						<div class="item-plan-kit <?php echo $kit['name'] ?>" kit-name="<?php echo $kit['name'] ?>">
							<div class="item-plan-kit-check">
								<?php if( !empty( array_filter($kit['feature']) ) ): ?>
									<a href="javascript:void(0)" title="View Detail" data-toggle="tooltip" class="btn-view-kit-detail"><i class="ion-ios-checkmark-outline"></i>
										<img class="check" src="<?php echo base_url();?>image/kits-banner/<?php echo $kit['name'] ?>.jpg">
									</a>
								<?php endif; ?>
							</div>
							<div class="item-plan-kit-detail">
								<ul>
									<?php foreach ($kit['feature'] as $feature) : ?>
										<li><?php echo !empty($feature)? $feature :  '&nbsp;' ; ?></li>
									<?php endforeach; ?>
								</ul>
							</div>                    
						</div>
					<?php endforeach; ?>

					<?php $current_plan_id = !empty( $user_plan['_id']->{'$id'} ) ? $user_plan['_id']->{'$id'} : ''; ?>
					<div class="item-plan-kit text-center item-plan-action">
						<?php if( !empty($plans_id[ $plan['name'] ]) ): ?>
							<?php if( $plan['name'] == 'enterprise' ): ?>
								
								<?php if( $current_plan_id != $plans_id[ $plan['name'] ] ): ?>
									<a href="mailto:info@playbasis.com" data-plan-id="<?php echo $plans_id[ $plan['name'] ];  ?>" class="btn plan-btn plan-enterprise-btn">Email us</a>
								<?php else: ?>
									<span class="current-plan-text">Current Plan</span>
								<?php endif; ?>

							<?php else: ?>
								
								<?php if( $current_plan_id != $plans_id[ $plan['name'] ] ): ?>
									<a href="javascript:void(0)"  data-plan-id="<?php echo $plans_id[ $plan['name'] ];  ?>" class="btn plan-btn">Choose Plan</a>
								<?php else: ?>
									<span class="current-plan-text">Current Plan</span>
								<?php endif; ?>
								
							<?php endif; ?>
						<?php endif; ?>

					</div>

				</div>
			<?php endforeach; ?>

		</div>

	</div>
</section>

<script type="text/javascript">
	function init_layout(){
		$full_plan_feture = $('.item-plan-wrapper.enterprise');
		$full_plan_feture.find('.item-plan-kit').each(function(){
			var kitName = $(this).attr('kit-name');
			$(this).find('li').each(function(){
				var index = $(this).index();
				var elementH = $(this).height();
				$( '.'+kitName+' li:nth-child('+(index+1)+')' ).height(elementH);

			});
		});

		$('.item-plan-kit li').unbind('hover').hover(function(){
			var index = $(this).index();
			var kitName = $(this).closest('.item-plan-kit').attr('kit-name');
			$( '.'+kitName+' li:nth-child('+(index+1)+')' ).addClass('hover');
		},
		function(){
			var kitName = $(this).closest('.item-plan-kit').attr('kit-name');
			$( '.'+kitName+' li').removeClass('hover');
		});

		$('.btn-view-kit-detail').unbind('click').click(function(){
			var kitName = $(this).closest('.item-plan-kit').attr('kit-name');
			$('.'+kitName+' .item-plan-kit-detail').toggleClass('show');
		});

		$('[data-toggle=tooltip]').tooltip();

	}


	if( $( document ).width() >= 980 ){
		init_layout();
	}else{
		clean_layout();
	}

	function clean_layout(){
		$( '.item-plan-kit li').removeClass('hover');
		$( '.item-plan-kit li').css("height", null);
		$('.item-plan-kit li').unbind('hover');

		$('.btn-view-kit-detail').unbind('click').click(function(){
			$(this).closest('.item-plan-kit').find('.item-plan-kit-detail').toggleClass('show');
		});
	}

	$( window ).resize(function() {
		
		var docWidth =  $( document ).width();
		console.log(docWidth);
		if(docWidth >= 980){
			init_layout();
		}else{
			clean_layout();
		}
	});
</script>