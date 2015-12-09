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


 
<!-- Modal -->
<div id="modal_survey" class="modal hide fade" tabindex="-1" role="dialog" >
  <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h3>Give us a sec…</h3>
  </div>
  <div class="modal-body">
      <form id="survey_form">
            <div class="step step-1">
                  <div class="form-group">
                        <h4>What is your business sector?</h4>
                        <select id="business_sector" name="business_sector">
                              <option value="">---------- Select one ----------</option>
                              <option value="Agencies">Agencies</option>
                              <option value="Banking/Financial">Banking/Financial</option>
                              <option value="Call Centers">Call Centers</option>
                              <option value="Consumer Brands">Consumer Brands</option>
                              <option value="E-Commerce">E-Commerce</option>
                              <option value="Education">Education</option>
                              <option value="Enterprise">Enterprise</option>
                              <option value="Food and Beverage">Food and Beverage</option>
                              <option value="Publishers/Media">Publishers/Media</option>
                              <option value="Retail/Wholesale">Retail/Wholesale</option>
                              <option value="Sales/CRM">Sales/CRM</option>
                              <option value="Telecommunications">Telecommunications</option>
                              <option value="Others">Others</option>
                        </select>
                  </div>
                  <div class="modal-action">
                        <a href="javascript:void(0)" class="btn btn-primary next-btn" >Next</a>
                  </div>
            </div>
            <div class="step step-2">
                  <div class="form-group form-feature-wrapper">
                        <h4>Which features you are looking for?</h4>
                        <div class="clearfix">
                              <div class="span6">
                                    <label><input type="checkbox" name="feature[api]" > API</label>
                                    <label><input type="checkbox" name="feature[sdk]" > SDK</label>
                                    <label><input type="checkbox" name="feature[widgets]" > Widgets</label>
                                    <label><input type="checkbox" name="feature[dashboardAnalytics]"> Dashboard Analytics</label>
                                    <label><input type="checkbox" name="feature[referralProgram]" > Referral Program</label>
                                    <label><input type="checkbox" name="feature[loyaltyProgram]" > Loyalty Program</label>
                                    <label><input type="checkbox" name="feature[ruleEngine]" > Rule Engine</label>
                              </div>
                              <div class="span6">
                                    <label><input type="checkbox" name="feature[socialMediaIntegration]"> Social Media Integration</label>
                                    <label><input type="checkbox" name="feature[rewardStore]" > Reward Store</label>
                                    <label><input type="checkbox" name="feature[whiteLabelPlatform]"> White Label Platform</label>
                                    <label><input type="checkbox" name="feature[consultationService]"> Consultation Service</label>
                                    <label><input type="checkbox" name="feature[leaderboardLeaderboard]" > Leaderboard</label>
                                    <label><input type="checkbox" name="feature[transactionIntegration]" > Transaction Integration</label>
                                    <label><input type="checkbox" name="feature[miniGames]" > Mini Games</label>
                                    <label><input type="checkbox" name="feature[others]" > Others</label>
                              </div>
                        </div>
                  </div>
                  <div class="modal-action">
                        <a href="javascript:void(0)" class="btn back-btn">Back</a>
                        <a href="javascript:void(0)" class="btn btn-primary next-btn" >Next</a>
                  </div>
            </div>
            <div class="step step-3">
                 <div class="form-group form-objective-wrapper">
                       <h4>What is the objective of your project?</h4>
                       <div class="clearfix">
                               <div class="span4 offset4">
                                    <label><input type="checkbox" name="objective[motivateUsers]" > Motivate Users</label>
                                    <label><input type="checkbox" name="objective[motivateEmployees]" > Motivate Employees</label>
                                    <label><input type="checkbox" name="objective[increaseEngagement]" > Increase Engagement</label>
                                    <label><input type="checkbox" name="objective[customerLoyalty]" > Customer Loyalty</label>
                                    <label><input type="checkbox" name="objective[digitalTransformation]" > Digital Transformation</label>
                                    <label><input type="checkbox" name="objective[others]" > Others</label>
                              </div>
                       </div>
                 </div>
                  <div class="modal-action">
                        <a href="javascript:void(0)" class="btn back-btn">Back</a>
                        <a href="javascript:void(0)" class="btn btn-primary next-btn" >Finish</a>
                  </div>
            </div>
    </form>
    
  </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

                                    $('#modal_survey').modal({
                                          backdrop: 'static',
                                          show: true
                                    });

                                    $('.next-btn').click(function(){
                                          if( $(this).hasClass('disabled')  ){
                                                return;
                                          }
                                          $step = $(this).closest('.step');
                                          
                                          if( $step.hasClass('step-1') ){
                                                if( $("#business_sector").val() == ""){
                                                      alert('Please select business sector');
                                                }else{
                                                      $('.step').hide();
                                                      $('.step-2').show();      
                                                }
                                                

                                          }else if( $step.hasClass('step-2') ){

                                                if ( $(".form-feature-wrapper input[type=checkbox]:checked").length <= 0 ){ 
                                                      alert('Please select atleast one feature');
                                                }else{
                                                      $('.step').hide();
                                                      $('.step-3').show();      
                                                }
                                                

                                          }else if( $step.hasClass('step-3') ){
                                              
                                                if ( $(".form-objective-wrapper input[type=checkbox]:checked").length <= 0 ){ 
                                                      alert('Please select atleast one objective');
                                                }else{
                                                      
                                                      $("#survey_form").submit();
                                                      $step.find('.btn').addClass('disabled');
                                                }
                                          }
                                    });
                                    

                                    $("#survey_form").submit(function(e){
                                          e.preventDefault();
                                          var data = $( this ).serializeArray();
                                          $.ajax({
                                            type: "POST",
                                            url: 'https://www.pbapp.net',
                                            data: data,
                                            success: function(data){
                                                setTimeout(function(){
                                                      $('.step .btn').removeClass('disabled');
                                                      $('#modal_survey').modal('hide');     
                                                },5000);
                                            }
                                          });
                                    });

                                    $('#modal_survey').on('hidden', function () {
                                          $.ajax({
                                            type: "POST",
                                            url: 'https://www.pbapp.net',
                                            data: {
                                                dosurvey: true
                                            },
                                            success: function(data){
                                                
                                            }
                                          });
                                    });
                                    

                                    $('.back-btn').click(function(){
                                          if( $(this).hasClass('disabled')  ){
                                                return;
                                          }
                                          $step = $(this).closest('.step');
                                          
                                          if( $step.hasClass('step-1') ){


                                          }else if( $step.hasClass('step-2') ){

                                                $('.step').hide();
                                                $('.step-1').show();

                                          }else if( $step.hasClass('step-3') ){
                                                $('.step').hide();
                                                $('.step-2').show();
                                          }
                                    });

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
