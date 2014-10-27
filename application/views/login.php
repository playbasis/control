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
            $udata = array('name' => 'username', 'id' => 'username','value' => set_value('username'), 'class'=>'btn-not-login', 'placeholder'=>'E-mail');
            $pdata = array('name' => 'password', 'id' => 'password', 'class'=>'btn-not-login', 'placeholder'=>'Password');

            $attributes = array('id' => 'form', 'class' => 'pbf-form');
            echo form_open('login',$attributes);
            ?>
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

              
                <div class="input-center">
                    <?php echo form_input($udata); ?>
                    <?php echo form_password($pdata); ?><br>
                    <?php echo anchor('forgot_password', $this->lang->line('text_forgot_password'), array('class' => 'btn-not-login')); ?>
                </div>



              <hr>

              <button onclick="$('#form').submit();" type="submit" class=" btn-not-login"><?php echo $this->lang->line('button_login'); ?></button>

            </fieldset>
            

        <?php echo form_close(); ?>
    </div><!-- .content -->

    <div class="clearfix"></div>
  

</div><!-- #content .span10 -->








<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
    if (e.keyCode == 13) {
        $('#form').submit();
    }
});
//--></script>
