<div id="content" >

    <div class="box register-success-wrapper">
        <div class="heading">
                <h1>Registration Successful</h1>
            </div>
        <div class="content">
            	Your account has been activated and your password has been sent to your email.

            <div class="button-wrapper">
                <?php
                echo anchor('login'.$action['action_id'], 'Go to Login', array('class'=>'button'));
                ?>
            </div>

        </div>
    </div>
</div>

