<div id="content" class="span10 account-purchase-page">

        
        <?php if($this->session->flashdata('fail')){ ?>
                    <div class="content messages half-width">
                        <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                    </div>
                <?php }?>
                <?php if(validation_errors() || isset($message)){?>
                    <div class="content messages half-width">
                        <?php echo validation_errors('<div class="warning">', '</div>');?>
                        <?php if (isset($message) && $message){?>
                            <div class="warning"><?php echo $message;?></div>
                        <?php }?>
                    </div>
                <?php }?>

                <?php $attributes = array('id' => 'form');?>
                <?php echo form_open($form ,$attributes); ?>

                                    <input type="hidden" name="plan" >
                                    <input type="hidden" name="channel" value="<?php echo PAYMENT_CHANNEL_PAYPAL; ?>">
                                    
                                
                <?php echo form_close(); ?>
            
            <!-- Start plans -->
              <div class="plans-wrapper">
                    
                        <h1>
                                Get started with Gamification today!
                        </h1>
                    

                  <section id="pricePlans">

                  <div class="pricePlansPro">
                    
                    Improve the level of your customers' engagement by getting all the gamification features your business needs with our Playbasis gamification platform
                    
                  </div>

                  
                  <ul id="plans">
                    
                  <?php
                    $n = 0;
                   foreach ($plans as $key => $plan) {
                      
                      if($plan['price'] <= 0){
                        ?>
                        <li class="plan plan-try plan-free plan-left">
                          <ul class="planContainer">
                            <li class="title">
                              
                                <h2>Free</h2>
                             
                            </li>
                            <li class="price"><p><?php echo $plan['name'] ?></p></li>
                            <li>
                              <ul class="options">
                                <li><?php echo $plan['limit_others']['player'] ?> Register Users</li>
                                <li><?php echo $plan['limit_others']['user'] ?> Admin Users</li>
                                <li><?php echo !empty( $plan['limit_requests']['/player'] ) ? $plan['limit_requests']['/player'].' Player API Requests' : '-' ?> </li>
                                <li><?php echo !empty( $plan['limit_requests']['/engine'] ) ? $plan['limit_requests']['/engine'].' Engine API Requests' : '-' ?> </li>
                              </ul>
                            </li>
                            <li class="button">
                            <?php if ($plan['_id'] != $user_plan['_id']) { ?>
                              <a  href="javascript:void(0)" class="plan-btn free-plan-btn" data-plan-id="<?php echo $plan['_id'] ?>">
                                Choose Plan</a>
                              <?php } else { ?>
                                <span class="plan-current-btn">Current Plan</span>
                              <?php } ?>
                              </li>
                          </ul>
                        </li>

                        <?php
                      }else{
                            $n++;
                            ?>
                            
                            <li class="plan plan-pro ">
                              <ul class="planContainer">
                                <li class="title"><h2>$<?php echo $plan['price'] ?><span>/month</span></h2></li>
                                <li class="price"><p><?php echo $plan['name'] ?></p></li>
                                <li>
                                  <ul class="options">
                                    <li><?php echo $plan['limit_others']['player'] ?> Register Users</li>
                                    <li><?php echo $plan['limit_others']['user'] ?> Admin Users</li>
                                    <li><?php echo !empty( $plan['limit_requests']['/player'] ) ? $plan['limit_requests']['/player'].' Player API Requests' : '-' ?> </li>
                                    <li><?php echo !empty( $plan['limit_requests']['/engine'] ) ? $plan['limit_requests']['/engine'].' Engine API Requests' : '-' ?> </li>
                                  </ul>
                                </li>
                                <li class="button">
                                <?php if ($plan['_id'] != $user_plan['_id']) { ?><a  href="javascript:void(0)" class="plan-btn" data-plan-id="<?php echo $plan['_id'] ?>">Choose Plan</a><?php } else { ?><span class="plan-current-btn">Current Plan</span><?php } ?></li>
                              </ul>
                            </li>

                            <?php

                      }

                  } ?>

                  <li class="plan plan-enterprise plan-right">
                        <ul class="planContainer">
                          <li class="title"><h2>Call us</h2></li>
                          <li class="price"><p>ENTERPRISE</p></li>
                          <li>
                            <ul class="options">
                              <li>- Register Users</li>
                              <li>- Admin Users</li>
                              <li>- Player API Requests</li>
                              <li>- Engine API Requests</li>
                            </ul>
                          </li>
                          <li class="button"><a href="mailto:info@playbasis.com">Call us</a></li>
                        </ul>
                      </li>
                    
                  </ul> <!-- End ul#plans -->
                </section>
              </div>
              <!-- End plans -->

    <div class="regis-plan-table-btn"><a href="javascript:void()">View Compare Plans</a></div>
    <div class="regis-plan-table-wrapper" style="display:none">




      <table cellspacing="0" cellpadding="0" class="t1">
        <tbody>
          <tr class="table-header">
            <th>
              FEATURES
            </th>
            <th>
              FREE
            </th>
            <th>
              PRO 1
            </th>
            <th>
              PRO 2
            </th>
            <th>
              PRO 3
            </th>
            <th>
              ENTERPRISE
            </th>
          </tr>
          <tr class="row-price">
            <th>
              Price <span>(monthly)</span>
            </th>

            <?php foreach ($plans as $key => $plan): ?>
            <td>
              <?php if($plan['price'] == 0): ?>
                FREE
              <?php else: ?>
                  <?php echo $plan['price']; ?> USD
             <?php endif; ?>
             <?php endforeach; ?>
             </td>
             <td>CALL US</td>
          </tr>
          <tr>
            <td colspan="6" class="row-head">
              Support
            </td>
          </tr>
          <tr>
            <th>
              Email
            </th>

             <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Dedicated Account Manager
            </th>
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-minus"></i>
                <?php else: ?>
                    <i class="fa fa-minus"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>
          </tr>
          <tr>
            <th>
              Custom Setup &amp; Training
            </th>

          <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-minus"></i>
                <?php else: ?>
                    <i class="fa fa-minus"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <td colspan="6" class="row-head">
              Core Features
            </td>
          </tr>
          <tr>
            <th>
              Admin interface
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Gamification Engine
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              User Profile
            </th>
            
          <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Engagement points
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Virtual Currencies
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Badges
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Rules
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Capture &amp; store Users Actions
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Registered Users
            </th>


            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <p><span ><?php echo $plan['limit_others']['player'] ?> Users</span></p>
                <?php else: ?>
                   <p><span ><?php echo $plan['limit_others']['player'] ?> Users</span></p>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><p><span >Unlimited Users</span></p></td>



          </tr>
          <tr>
            <th>
              Allowed Requests per second to gamification API
            </th>
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <p><span ><?php echo $plan['limit_requests']['/engine'] ?> Users</span></p>
                <?php else: ?>
                   <p><span ><?php echo $plan['limit_requests']['/engine'] ?> Users</span></p>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><p><span >Unlimited</span></p></td>

          </tr>
          <tr>
            <th>
              Leaderboards
            </th>
            
             <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Live Feed
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              History Feed
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Social CRM
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Reporting
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              SDK
            </th>

            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Levels
            </th>
            
             <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Analytics &amp; insight
            </th>
            
             <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-minus"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            
            <td colspan="6" class="row-head">
              Advanced Features
            </td>
            
          </tr>
          <tr>
            <th>
              Social login
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-minus"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Data Exporting capabilities
            </th>
            
             <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Quests &amp; Missions
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>

              <td>
                <?php if($plan['price'] == 0): ?>
                  
                  <?php if($plan['limit_others']['quest'] === 0 ): ?>
                      <i class="fa fa-minus"></i>
                  <?php else: ?>
                       <?php if( !empty( $plan['limit_others']['quest'] ) ): ?>
                            <p ><span ><?php echo $plan['limit_others']['quest']; ?><br>Quests</span></p>
                        <?php else: ?>
                            <p ><span >Unlimited<br>Quests</span></p>
                        <?php endif; ?>
                  <?php endif; ?>

                <?php else: ?>
                   
                  <?php if($plan['limit_others']['quest'] === 0): ?>
                      <i class="fa fa-minus"></i>
                  <?php else: ?>
                       <?php if( !empty( $plan['limit_others']['quest'] ) ): ?>
                            <p ><span ><?php echo $plan['limit_others']['quest']; ?><br>Quests</span></p>
                        <?php else: ?>
                            <p ><span >Unlimited<br>Quests</span></p>
                        <?php endif; ?>
                  <?php endif; ?>

               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><p><span >Unlimited<br>Quests</span></p>

          </tr>
          <tr>
            <th>
              Rewards
            </th>
            
         <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-check"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Reward Store
            </th>
            

            <?php foreach ($plans as $key => $plan): ?>

              <td>
                <?php if($plan['price'] == 0): ?>
                  
                  <?php if($plan['limit_others']['goods'] === 0 ): ?>
                      <i class="fa fa-minus"></i>
                  <?php else: ?>
                       <?php if( !empty( $plan['limit_others']['goods'] ) ): ?>
                            <p ><span ><?php echo $plan['limit_others']['goods']; ?><br>Goods</span></p>
                        <?php else: ?>
                            <p ><span >Unlimited<br>Goods</span></p>
                        <?php endif; ?>
                  <?php endif; ?>

                <?php else: ?>
                   
                  <?php if($plan['limit_others']['goods'] === 0): ?>
                      <i class="fa fa-minus"></i>
                  <?php else: ?>
                       <?php if( !empty( $plan['limit_others']['goods'] ) ): ?>
                            <p ><span ><?php echo $plan['limit_others']['goods']; ?><br>Goods</span></p>
                        <?php else: ?>
                            <p ><span >Unlimited<br>Goods</span></p>
                        <?php endif; ?>
                  <?php endif; ?>

               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><p><span >Unlimited<br>Goods</span></p>


           

          </tr>
          <tr>
            <th>
              Messaging
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <i class="fa fa-minus"></i>
                <?php else: ?>
                    <i class="fa fa-check"></i>
               <?php endif; ?>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Emails
            </th>


            <?php foreach ($plans as $key => $plan): ?>
                 <td>
                   <?php if($plan['price'] == 0): ?>
                     <i class="fa fa-minus"></i>
                   <?php else: ?>
                       <p ><span ><?php echo $plan['limit_notifications']['email'] ?>  / Month</span></p>
                  <?php endif; ?>
                  <?php endforeach; ?>
                  </td>
                  <td><p ><span >Unlimited / Month</span></p></td>

          </tr>
         
          <tr class="row-action">
            <th>
              
            </th>

            <?php foreach ($plans as $key => $plan): ?>
              <?php if($plan['price'] == 0): ?>
                <td class="col-try">
                  <?php if ($plan['_id'] != $user_plan['_id']) { ?><a  href="javascript:void(0)" class="plan-btn" data-plan-id="<?php echo $plan['_id'] ?>">Choose</a><?php } else { ?><span class="plan-current-btn">Current</span><?php } ?>

                </td>
              <?php else: ?>
                  <td>
                  <?php if ($plan['_id'] != $user_plan['_id']) { ?><a  href="javascript:void(0)" class="plan-btn" data-plan-id="<?php echo $plan['_id'] ?>">Choose</a><?php } else { ?><span class="plan-current-btn">Current</span><?php } ?>
                </td>
                <?php endif; ?>
              <?php endforeach; ?>

              <td class="col-enterprise">
                  <a href="mailto:info@playbasis.com">Call us</a>
                </td>

          </tr>
        </tbody>
      </table>
      
    </div>












</div><!-- #content .span10 -->

<script type="text/javascript">
    $(document).ready(function(){
        
        $('.plan-btn').click(function(){
            $('input[name=plan]').val($(this).attr('data-plan-id'));
            $('form#form').attr('action', '<?php echo site_url("account/".($user_plan_date_billing ? "upgrade" : "subscribe") ); ?>');

            if( $(this).hasClass('free-plan-btn') ){
                $('form#form').attr('method', 'POST');
                $('form#form').attr('action', '<?php echo site_url("account/cancel_subscription"); ?>' );              
            }
            $('form#form').submit();
        })

        $('.regis-plan-table-btn a').click(function(){
            $planTable = $('.regis-plan-table-wrapper');
            if( $planTable.hasClass('open') ){
                $planTable.slideUp('slow').removeClass('open');
                $(this).text('View Compare Plans');
            }else{
                $planTable.slideDown('slow').addClass('open');
                $(this).text('Close Compare Plans');
            }
          
        })

    });
</script>
