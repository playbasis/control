<div id="content" class="regis-page-wrapper">


<div class="row regis-header">
    <h1>
            <small>Get started with Gamification today!</small>
            Choose plan
    </h1>
</div>
    
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
                                
                                <input type="hidden" name="plan_id" >
                                
                            
            <?php echo form_close(); ?>
        
        <!-- Start plans -->
          <div class="plans-wrapper">
              <section id="pricePlans">

              <div class="pricePlansPro">
                
                Improve the level of your customers' engagement by getting all the gamification features your business needs with our Playbasis gamification platform
                
              </div>

              
              <ul id="plans">
                
              <?php
                $n = 0;
               foreach ($plan_data as $key => $plan) {
                  
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
                        <li class="button"><a  href="javascript:void(0)" class="plan-btn" data-plan-id="<?php echo $plan['_id'] ?>">Try it Free</a></li>
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
                            <li class="button"><a href="javascript:void(0)" class="plan-btn" data-plan-id="<?php echo $plan['_id'] ?>">Start</a></li>
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

          

<div class="regis-content span8 offset2">
    </div><!-- .content -->

    <div class="clearfix"></div>
    <div class="regis-step-wrapper text-center span6 offset3">
        
        <div class="row">
            <div class="span4 " >
                <div class="step-wrapper">
                    <h6>Step 1</h6>
                    <div class="step-icon"><i class="fa fa-globe"></i></div>
                    <h4>Add a<br> Site Name</h4>
                </div>
            </div>
            <div class="span4" >
                <div class="step-wrapper  current-step">
                    <h6>Step 2</h6>
                    <div class="step-icon"><i class="fa fa-check-square-o"></i></div>
                    <h4>Choose plan</h4>
                </div>
            </div>
            <div class="span4" >
                <div class="step-wrapper">
                    <h6>Step 3</h6>
                    <div class="step-icon"><i class="fa fa-trophy"></i></div>
                    <h4>Let's Start</h4>
                </div>
            </div>
        </div>
    </div>
     <div class="clearfix"></div>

</div><!-- #content .span10 -->





<script type="text/javascript">
    $(document).ready(function(){
        
        $('.plan-btn').click(function(){
            $('input[name=plan_id]').val($(this).attr('data-plan-id'));
            $('form#form').submit();
        })

    });
</script>