
<div id="content" class="regis-page-wrapper regis-login-page">


<div class="row regis-header">
    <h1>
            <small>Get started with Gamification today!</small>
            <?php echo $heading_forgot_password;?>
    </h1>
</div>
    <div class="regis-content span6 offset3">
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
			<?php $attributes = array('id' => 'form', 'class' => 'pbf-form');?>
			<?php echo form_open_multipart($form, $attributes);?>

           
            <fieldset>
              
                <div class="input-center">
                <input type = "password" name="password" class="btn-not-login" size="50" value = "" placeholder="<?php echo $this->lang->line('form_password');?>">

                <input type = "password" name="password_confirm" class="btn-not-login" size="50" value ="" placeholder="<?php echo $this->lang->line('form_confirm_password');?>">



                </div>


              <hr>


             <a href="<?php echo base_url();?>"  class="btn-not-login" id="cancel">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<button onclick="$('#form').submit();" type="submit" class="button btn-not-login" id="submit">Change Password</button>

            </fieldset>
            

        <?php echo form_close(); ?>
    </div><!-- .content -->

    <div class="clearfix"></div>
  

</div><!-- #content .span10 -->