<style type="text/css">
	.landingpage-wrapper{
		margin-top: 0;
		padding-top: 0;
	}
	.noti-plan-wrapper{
		background: #86559c;
		min-height: 40px;
		line-height: 40px;
		color: #fff;
		text-align: center;
		float: left;
		width: 82.90598290598291%;
		position: relative;
		z-index: 1;
		margin: -20px 0 0 -4px;
		
	}
	.noti-plan-wrapper > a{
		color: #fff;
		text-decoration: underline;
	}
	#content{
		z-index: 0;	
	}
	.getstarted-wrapper{
		background: #e6e6e6;
		padding: 30px;
		text-align: center;
	}
	.landingpage-content{
		text-align: center;
		padding-top: 50px;
		padding-bottom: 40px;
	}
	.landingpage-content h1{
		color: #86559c;
		font-size: 32px;
		font-weight: normal;
		text-transform: uppercase;
		margin-bottom: 20px;
	}
	.landingpage-content h2{
		color: #86559c;
		font-weight: normal;
		font-size: 24px;
		margin-bottom: 30px;
	}
	.landingpage-content a{
		margin-top: 50px;
		font-size: 16px;
		font-weight: 600;
		line-height: normal;
		white-space: normal;
		text-align: center;
		color: #FFF;
		text-shadow: 0px -1px #BF4C1B;
		box-shadow: 0px 1px #BF4C1B;
		background: linear-gradient(#F26023, #ED500E) repeat scroll 0% 0% #F26023;
		width: 100%;
		border: medium none;
		padding: 10px 20px;
		border-radius: 5px;
		text-transform: uppercase;
	}
	.landingpage-content a:hover, .landingpage-content a:focus {
		text-decoration: none;
		outline: medium none;
		background: linear-gradient(#FF5B16, #FE5B17) repeat scroll 0% 0% #FF5B16;
	}
	@media (max-width: 979px) and (min-width: 768px){
		.noti-plan-wrapper {
			width: 91.43646408839778%;
		}
	}
	@media (max-width: 767px){
		.noti-plan-wrapper {
			width: 100%;
			padding: 10px;
			box-sizing: border-box;
			moz-box-sizing: border-box;
			margin-bottom: 20px;
			line-height: 18px;
		}
		.noti-plan-wrapper i{
			display: none;
		}
	}
</style>

<div class="noti-plan-wrapper">
	You're now in <strong>"Free Plan"</strong> <i class="fa fa-question-circle"></i> , upgrade to higher plan to get more features! <a href="<?php echo site_url(); ?>/account/subscribe" >Upgrade Now</a>
</div>

<div id="content" class="span10 landingpage-wrapper">
	<div class="box">
		
		<div class="landingpage-content">
			<h1>To start using Playbasis Platform, </h1>
			<h2>please create application below:</h2>
			<a href="<?php echo site_url("app"); ?>">Create App</a>
		</div>
      	
      	<div class="getstarted-wrapper">
      		<h1>Getting Started with Playbasis</h1>
      		<h2>We provide several SDKs for faster and easier integrations with your framework.</h2>
      	</div>
    </div>
</div>