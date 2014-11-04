<div id="content" class="regis-page-wrapper regis-login-page">



<div class="row regis-header">

    <div class="regis-site-header">
        <svg title="Playbasis" class="pbr-header-logo">
              <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo base_url();?>image/logo.svg#logo"></use>
        </svg>    
    </div>
    

    <h1>
            <small>Get started with Gamification today!</small>
            Login
    </h1>
</div>
    <div class="regis-content span6 offset3">
       <?php
            if(validation_errors() || isset($message)) {
                ?>
                <div class="content messages half-width">
                    <?php
                    echo validation_errors('<div class="warning">','</div>');

                    if (isset($message) && $message) {
                    ?>
                        <div class="warning"><?php echo $message; ?></div>
                    <?php
                    }
                    ?>
                </div>
                <?php
            }
            $udata = array('name' => 'username', 'id' => 'username','value' => set_value('username'), 'class'=>'btn-not-login', 'placeholder'=>'E-mail', 'required'=>"");
            $pdata = array('name' => 'password', 'id' => 'password', 'class'=>'btn-not-login', 'placeholder'=>'Password', 'required'=>"");

            $attributes = array('id' => 'form', 'class' => 'pbf-form');
            //echo form_open('login',$attributes);
            ?>
            <form id="form" class="pbf-form">
            <?php if($this->session->flashdata('email_sent')){ ?>
                <div class="content messages half-width">
                <div class="success"><?php echo $this->session->flashdata('email_sent'); ?></div>
                </div>
            <?php }?>
            <?php if($this->session->flashdata('multi_login')){ ?>
                <div class="content messages half-width">
                <div class="warning"><?php echo $this->session->flashdata('multi_login'); ?></div>
                </div>
            <?php }?>

            
            <fieldset>

              
                <!--div class="input-center">
                    <?php echo form_input($udata); ?>
                    <?php echo form_password($pdata); ?><br>
                    <?php echo anchor('forgot_password', $this->lang->line('text_forgot_password'), array('class' => 'btn-not-login')); ?>
                </div-->
                <div class="pbf-field-group">
                    <label for="username">E-mail</label>
                    <?php echo form_input($udata); ?>
                </div>
                <div class="pbf-field-group">
                    <label for="password">Password</label>
                    <?php echo form_password($pdata); ?>
                </div>
                <div class="pbf-field-group">
                    <?php echo anchor('forgot_password', $this->lang->line('text_forgot_password'), array('class' => 'btn-not-login')); ?>
                </div>
              <hr>

                <div class="pbf-field-group">
                    <button id="pbf-login" type="submit" class="btn-not-login"><?php echo $this->lang->line('button_login'); ?></button>
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
            username: "Required",
            password: "Required"
        },
        submitHandler: function(form) {

            $.ajax({
                url: baseUrlPath+"login",
                type: "POST",
                cache: false,
                dataType: "json",
                data: $( form ).serialize()+'&format=json'
            }).done(function(data) {
                if(data.status == 'error'){
                    $('.pb-alert').remove();
                    $('.regis-content').prepend('<div class="pb-alert pb-alert--add"><div class="pb-alert--close" title="Close Alert"></div>'+data.message+'</div>');
                    $('.pb-alert--close').on('click', function( e ) {
                        setTimeout( function() { $('.pb-alert').remove(); }, 1000);
                    });
                }else{
                    window.location = baseUrlPath;
                }
            });
            return false;
        }
    });
    $('.pb-alert--close').on('click', function( e ) {
        setTimeout( function() { $('.pb-alert').remove(); }, 1000);
    });
//--></script>
