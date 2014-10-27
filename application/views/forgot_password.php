
<div id="content" class="regis-page-wrapper regis-login-page">


<div class="row regis-header">
    <div class="regis-site-header">
        <svg title="Playbasis" class="pbr-header-logo">
              <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo base_url();?>image/logo.svg#logo"></use>
        </svg>    
    </div>
    <h1>
            <small>Get started with Gamification today!</small>
            <?php echo $heading_forgot_password;?>
    </h1>
</div>
    <div class="regis-content span6 offset3">
      <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>
			<?php if(validation_errors() || isset($message)) {?>
                <div class="content messages half-width">
                    <?php echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                        ?>
                        <div class="warning"><?php echo $message; ?></div>
                        <?php
                    }
                    ?>
                </div>
            <?php }?>
			<?php $attributes = array('id' => 'form','class' => 'pbf-form');?>
			<?php echo form_open_multipart($form, $attributes);?>

            <fieldset>
            <p>Please provide your email, we will send you a link via email to reset your password.</p>
              
                <div class="input-center">
                  
                  <input type = "text" name="email" placeholder="E-mail" size="50" value="<?php if(isset($temp_fields)){echo $temp_fields['email'];}?>" class="tooltips btn-not-login" data-placement="bottom" title="Email address is used to log into the system">

                </div>


              <hr>


             <a href="<?php echo base_url();?>" class="btn-not-login" id="cancel">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<button onclick="$('#form').submit();" type="submit" class="button btn-not-login" id="submit">Submit</button>

            </fieldset>
            

        <?php echo form_close(); ?>
    </div><!-- .content -->

    <div class="clearfix"></div>
  

</div><!-- #content .span10 -->
