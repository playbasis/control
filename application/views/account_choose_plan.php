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

              <?php echo $this->view('partial/plans_table'); ?>

            <?php $attributes = array('id' => 'form');?>
            <?php echo form_open($form ,$attributes); ?>
                                
                                <input type="hidden" name="plan_id" >
                                
                            
            <?php echo form_close(); ?>
        


        
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

        $('.regis-plan-table-btn').click(function(){
          $('.regis-plan-table-wrapper').slideDown('slow');
        })

    });
</script>