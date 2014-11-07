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
		padding-bottom: 50px;
	}
	.landingpage-content h1{
		color: #86559c;
		font-size: 32px;
		font-weight: normal;
		text-transform: uppercase;
		line-height: 32px;
	}
	.landingpage-content h2{
		color: #86559c;
		font-weight: normal;
		font-size: 24px;
		margin-bottom: 40px;
		line-height: 26px;
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
	.getstarted-wrapper{
		margin-bottom: 50px;	
	}
	.getstarted-wrapper h1{
		color: #86559c;
		font-weight: normal;
		font-size: 24px;
		line-height: 26px;
	}
	.getstarted-wrapper h2{
		font-weight: normal;
		font-size: 14px;
	}
	.sdk-list-wrapper{
		max-width: 820px;
		margin-left: auto;
		margin-right: auto; 
		list-style-type: none;
	}
	.sdk-list-wrapper li{
		display: inline-block;
		margin: 15px;
	}
	.sdk-list-wrapper li img{
		width: 90px;
	}
	.free-plan-features-wrapper{
		max-width: 320px;
		width: 320px;
		color: #86559c;
	}
	.free-plan-features-wrapper h3{
		text-transform: uppercase;
	}
	.free-plan-features-wrapper table td{
		padding: 5px 10px;
		color: #86559c;
	}
	.free-plan-features-wrapper i.fa-check{
		color: #13c48c;
	}
	.free-plan-features-wrapper i.fa-times{
		color: #f05a1b;
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
		.landingpage-content h1{
			font-size: 22px;
			line-height: 22px;
		}
		.landingpage-content h2{
			font-size: 16px;
			line-height: 16px;
		}
	}
</style>

<div class="noti-plan-wrapper">
	You're now in <strong>"Free Plan"</strong> <a href="javascript:void(0)"  data-toggle="tooltip" data-placement="bottom" class="free-plan-info" ><i class="fa fa-question-circle"></i></a> , upgrade to higher plan to get more features! <a href="<?php echo site_url(); ?>/account/subscribe" >Upgrade Now</a>
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
      		<ul class="sdk-list-wrapper">
      			<li><a href="https://github.com/playbasis/sdk-android/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/android.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-ios/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/ios.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-php/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/php.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-ruby/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/rb.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-python/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/py.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-pblib.NET/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/net.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-pblib.js/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/js.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-sharepoint/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/sharepoint.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-wordpress/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/wp.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-magento/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/magento.png"></a>
      			</li>
      			<li><a href="https://github.com/playbasis/sdk-java/archive/master.zip" target="_blank">
      				<img src="<?php echo base_url(); ?>/image/sdk-icon/java.png"></a>
      			</li>
      		</ul>
      	</div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.free-plan-info').popover({
			html: true,
			container: 'body',
			template: '<div class="popover free-plan-features-wrapper" ><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>',
			title: function(){
				return 'Free plan features:';
			},
		   content: function(){ 
		      return '<table>\
				<tr>\
					<td><i class="fa fa-check"></i> Upto 1,000 users</td>\
					<td><i class="fa fa-times"></i> Social login</td>\
				</tr>\
				<tr>\
					<td><i class="fa fa-check"></i> 5 quests available</td>\
					<td><i class="fa fa-times"></i> Analytics & insights</td>\
				</tr>\
				<tr>\
					<td><i class="fa fa-check"></i> 1 admin user</td>\
					<td><i class="fa fa-times"></i> Email & Messaging</td>\
				</tr>\
			</table>';
		   }
		});
	});
</script>