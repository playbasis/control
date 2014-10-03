<div id="content" class="regis-page-wrapper">


<div class="row regis-header">
    <h1>
            <small>Get started with Gamification today!</small>
            <?php echo $heading_title; ?>
    </h1>
</div>
    <div class="regis-content span8 offset2">
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
        <?php $attributes = array('id' => 'form', 'class' => 'pbf-form text-center');?>
        <?php echo form_open($form ,$attributes); ?>
            
            <fieldset>

              
                <div class="row ">
                    <p>
                    You are just one step away for finalizing your account. <br>Please click the button above to proceed.
                    </p>
                    <ul class="span4 offset4">
                        <li><i class="fa fa-check-circle-o fa-lg"></i> 5000 Register Users</li>
                        <li><i class="fa fa-check-circle-o fa-lg"></i> 2 Admin Users</li>
                        <li><i class="fa fa-check-circle-o fa-lg"></i> 10000 Player Req API/Sec. </li>
                        <li><i class="fa fa-check-circle-o fa-lg"></i> 100000Engine Req API/Sec.</li>
                    </ul>
                </div>



              <hr>

              <button class="btn btn-primary" type="submit">Start</button>

            </fieldset>
            

        <?php echo form_close(); ?>
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
                <div class="step-wrapper">
                    <h6>Step 2</h6>
                    <div class="step-icon"><i class="fa fa-check-square-o"></i></div>
                    <h4>Choose plan</h4>
                </div>
            </div>
            <div class="span4" >
                <div class="step-wrapper  current-step">
                    <h6>Step 3</h6>
                    <div class="step-icon"><i class="fa fa-trophy"></i></div>
                    <h4>Let's Start</h4>
                </div>
            </div>
        </div>
    </div>
     <div class="clearfix"></div>

</div><!-- #content .span10 -->
