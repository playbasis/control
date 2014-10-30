
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
			<?php //$attributes = array('id' => 'form', 'class' => 'pbf-form');?>
			<?php //echo form_open_multipart($form, $attributes);?>

            <form id="form" class="pbf-form">
            <fieldset>
              

                <div class="pbf-field-group">
                    <label for="password">Password</label>
                    <input type = "password" name="password" class="btn-not-login" size="50" value = "" required="" placeholder="<?php echo $this->lang->line('form_password');?>">
                </div>
                <div class="pbf-field-group">
                    <label for="password">Confirm Password</label>
                    <input type = "password" name="password_confirm" class="btn-not-login" size="50" value ="" required="" placeholder="<?php echo $this->lang->line('form_confirm_password');?>">
                </div>


              <hr>


                <div class="pbf-field-group">
                    <a href="<?php echo base_url();?>"  class="btn-not-login" id="cancel">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;
		            <button type="submit" class="button btn-not-login" id="submit">Change Password</button>
                </div>
            </fieldset>
            

        <?php echo form_close(); ?>
    </div><!-- .content -->

    <div class="clearfix"></div>
  

</div><!-- #content .span10 -->
<script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery.validate.min.js"></script>
<script type="text/javascript"><!--
    $('.pbf-form').validate({
        messages: {
            password: "Required",
            password_confirm: "Required"
        },
        submitHandler: function(form) {

            $.ajax({
                url: baseUrlPath+"<?php echo $form; ?>",
                type: "POST",
                cache: false,
                dataType: "json",
                data: $( form ).serialize()+'&format=json'
            }).done(function(data) {
                    $('.pb-alert').remove();
                    $('.regis-content').prepend('<div class="pb-alert pb-alert--add"><div class="pb-alert--close" title="Close Alert"></div>'+data.message+'</div>');
                    $('.pb-alert--close').on('click', function( e ) {
                        setTimeout( function() { $('.pb-alert').remove(); }, 1000);
                    });
                });
            return false;
        }
    });
    $('.pb-alert--close').on('click', function( e ) {
        setTimeout( function() { $('.pb-alert').remove(); }, 1000);
    });
//--></script>