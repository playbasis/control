<?php if (!$plan['paid_enterprise_flag'] && $plan['free_flag']) { ?>
<div class="noti-plan-wrapper">
	You're now in <strong>"Free Plan"</strong> <a href="javascript:void(0)"  data-toggle="tooltip" data-placement="bottom" class="free-plan-info" ><i class="fa fa-question-circle"></i></a> , upgrade to higher plan to get more features! <a href="<?php echo site_url(); ?>/account/subscribe" >Upgrade Now</a>
</div>
<?php } ?>

<div id="content" class="span10 landingpage-wrapper">
	<div class="box">
		
		<div class="landingpage-content">
			<h1>To start using Playbasis Platform, </h1>
			<h2>please create application below:</h2>
			<a href="<?php echo site_url("app/add"); ?>" class="btn-hero">Create App</a>
		</div>
      	
      	<div class="getstarted-wrapper box-gray">
      		<h1>Getting Started with Playbasis</h1>
                                    <a href="http://dev.playbasis.com/docs.php" class="subbox" target="_blank">
                                          <i class="fa fa-file-text-o"></i>
                                          <p>Read documentation<br>how to use our API</p>
                                    </a>
                                    <a href="http://dev.playbasis.com/io-docs/" class="subbox" target="_blank">
                                          <i class="fa fa-cogs"></i>
                                          <p>Live test with Playbasis API<br>using the secret / keys</p>
                                    </a>
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
