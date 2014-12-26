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
                                <li><?php echo $plan['limit_others']['user'] ?> Admin Users</li>
                                <li><?php echo $plan['limit_others']['player'] ?> Register Users</li>
                                
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
                                    <li><?php echo $plan['limit_others']['user'] ?> Admin Users</li>
                                    <li><?php echo $plan['limit_others']['player'] ?> Register Users</li>
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
                          <li class="title"><h2>Email Us</h2></li>
                          <li class="price"><p>ENTERPRISE</p></li>
                          <li>
                            <ul class="options">
                              <li>Call us for Admin Users</li>
                              <li>- Register Users</li>
                              <li>- Player API Requests</li>
                              <li>- Engine API Requests</li>
                            </ul>
                          </li>
                          <li class="button"><a href="mailto:info@playbasis.com">Email us</a></li>
                        </ul>
                      </li>
                    
                  </ul> <!-- End ul#plans -->
                </section>
              </div>
              <!-- End plans -->

    <div class="regis-plan-table-btn"><a href="javascript:void(0);">View Compare Plans</a></div>
    <div class="regis-plan-table-wrapper" style="display:block">




      <table cellspacing="0" cellpadding="0" class="t1">
        <tbody>
          <tr class="table-header">
            <th>
              Features
            </th>
            <?php foreach ($plans as $key => $plan): ?>
            <th>
              <?php echo $plan['name']; ?>
            </th>
            <?php endforeach; ?>
            <th>
              Enterprise
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
             </td>
             <?php endforeach; ?>
             
             <td>EMAIL US</td>
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
               </td>
               <?php endforeach; ?>
               
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
               </td>
               <?php endforeach; ?>
               
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
              Apps
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
 
               <?php echo $plan['limit_others']['app'].' '; echo $plan['limit_others']['app'] >1 ? 'Apps' : 'App' ?> 
               </td>

               <?php endforeach; ?>
               
               <td>Email Us</td>

          </tr>
          <tr>
            <th>
              Dashboard Admin Users
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php if($plan['price'] == 0): ?>
                  <?php echo $plan['limit_others']['user'] ?> Admin User
                <?php else: ?>
                   <?php echo $plan['limit_others']['user'] ?> Admin Users
               <?php endif; ?>
               </td>
               <?php endforeach; ?>
               <td>Email Us</td>

          </tr>
          <tr>
            <th>
              Rules Execution (monthly)
            </th>
            
          <?php foreach ($plans as $key => $plan): ?>
              <td>
                 <?php echo number_format($plan['limit_others']['rule']) ?>
                </td>
          <?php endforeach; ?>
               
               <td>Email Us</td>

          </tr>
          <tr>
            <th>
              Activity Streams
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php 
                if (in_array("Dashboard", $plan['feature_to_plan'])){
                    echo '<i class="fa fa-check"></i>';
                  }else{
                    echo '<i class="fa fa-minus"></i>';
                  }
               ?>
               </td>
               <?php endforeach; ?>
               
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Levels
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php 
                if (in_array("Level", $plan['feature_to_plan'])){
                    echo '<i class="fa fa-check"></i>';
                  }else{
                    echo '<i class="fa fa-minus"></i>';
                  }
               ?>
               </td>
               <?php endforeach; ?>
               
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Badges
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php 
                if (in_array("Rewards", $plan['feature_to_plan'])){
                    echo '<i class="fa fa-check"></i>';
                  }else{
                    echo '<i class="fa fa-minus"></i>';
                  }
               ?>
                </td>
               <?php endforeach; ?>
              
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Actions
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                 <?php 
                if (in_array("Action", $plan['feature_to_plan'])){
                    echo '<i class="fa fa-check"></i>';
                  }else{
                    echo '<i class="fa fa-minus"></i>';
                  }
               ?>
               </td>
               <?php endforeach; ?>
               
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
              Widgets
            </th>
            
           <?php foreach ($plans as $key => $plan): ?>
              <td>
                 <?php 
                if (in_array("Widget", $plan['feature_to_plan'])){
                    echo '<i class="fa fa-check"></i>';
                  }else{
                    echo '<i class="fa fa-minus"></i>';
                  }
               ?>
               </td>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
             Custom points
            </th>


            <?php foreach ($plans as $key => $plan): ?>
              <td>
                 <?php 
                if (in_array("Custom Point", $plan['feature_to_plan'])){
                    echo '<i class="fa fa-check"></i>';
                  }else{
                    echo '<i class="fa fa-minus"></i>';
                  }
               ?>
               </td>
               <?php endforeach; ?>
               </td>
               <td><i class="fa fa-check"></i></td>



          </tr>
          <tr>
            <th>
              Quiz
            </th>
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                  <?php
                     echo $plan['limit_others']['quiz'] == 0 ?  '<i class="fa fa-minus"></i>' : number_format($plan['limit_others']['quiz']);
                  ?>
              </td>
               <?php endforeach; ?>
               
               <td>Email Us</td>

          </tr>
          
          <tr>
            
            <td colspan="6" class="row-head">
              Advanced Features
            </td>
            
          </tr>
          <tr>
            <th>
              Quests Execution (monthly)
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
              <?php
                 echo $plan['limit_others']['quest'] == 0 ?  '<i class="fa fa-minus"></i>' : number_format($plan['limit_others']['quest']);
              ?> 
               <?php endforeach; ?>
               </td>
               <td>Email Us</td>


          </tr>
          <tr>
            <th>
              Sms API sending
            </th>
            
             <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php
                   echo $plan['limit_notifications']['sms'] == 0 ?  '<i class="fa fa-minus"></i>' : '<i class="fa fa-check"></i>';
                ?> 
                  </td>
               <?php endforeach; ?>
             
               <td><i class="fa fa-check"></i></td>


          </tr>
          <tr>
            <th>
              Reward Store / Redeem Goods (monthly)
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php
                   echo $plan['limit_others']['redeem'] == 0 ?  '<i class="fa fa-minus"></i>' : number_format($plan['limit_others']['redeem']);
                ?>
                </td>
               <?php endforeach; ?>
               
               <td>Email Us</p>

          </tr>
          <tr>
            <th>
              Emails (monthly)
            </th>
            
         <?php foreach ($plans as $key => $plan): ?>
              <td>
                <?php
                   echo $plan['limit_notifications']['email'] == 0 ?  '<i class="fa fa-minus"></i>' : number_format($plan['limit_notifications']['email']);
                ?>
                </td>
               <?php endforeach; ?>
               <td>Email Us</td>
          </tr>
          <tr>
            <th>
              CRM
            </th>

            <?php foreach ($plans as $key => $plan): ?>

              <td>
                  <?php 
                 if (in_array("Audience", $plan['feature_to_plan'])){
                     echo '<i class="fa fa-check"></i>';
                   }else{
                     echo '<i class="fa fa-minus"></i>';
                   }
                ?>
                  
                </td>
               <?php endforeach; ?>
               
               <td><i class="fa fa-check"></i></p>


           

          </tr>
          <tr>
            <th>
              Report
            </th>
            
            <?php foreach ($plans as $key => $plan): ?>
            <td>
                  <?php 
                 if (in_array("Report", $plan['feature_to_plan'])){
                     echo '<i class="fa fa-check"></i>';
                   }else{
                     echo '<i class="fa fa-minus"></i>';
                   }
                ?>
                </td>
               <?php endforeach; ?>
               
               <td><i class="fa fa-check"></i></td>

          </tr>
          <tr>
            <th>
             Insights
            </th>


            <?php foreach ($plans as $key => $plan): ?>
                 <td>
                    <?php 
                 if (in_array("Insights", $plan['feature_to_plan'])){
                     echo '<i class="fa fa-check"></i>';
                   }else{
                     echo '<i class="fa fa-minus"></i>';
                   }
                ?>
                 </td>
                  <?php endforeach; ?>
                 
                  <td><i class="fa fa-check"></i></td>

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
                  <a href="mailto:info@playbasis.com">Email Us</a>
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
            $('form#form').attr('action', '<?php echo site_url("account/".(isset($user_plan_date_billing) ? "upgrade" : "subscribe") ); ?>');

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
