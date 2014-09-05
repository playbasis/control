<div id="content">
    <div class="box" style="width: 400px; min-height: 300px; margin-top: 40px; margin-left: auto; margin-right: auto;">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/lockscreen.png" alt="" /> <?php echo $this->lang->line('text_login'); ?></h1>
        </div>
        <div class="content" style="min-height: 150px; overflow: hidden;">
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
            $udata = array('name' => 'username', 'id' => 'username','value' => set_value('username'), 'class'=>'btn-not-login');
            $pdata = array('name' => 'password', 'id' => 'password', 'class'=>'btn-not-login');

            $attributes = array('id' => 'form');
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
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: center;" rowspan="4"><img src="<?php echo base_url();?>image/login.png" alt="<?php echo $this->lang->line('text_login'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('entry_username'); ?>:<br />
                            <?php
                            echo form_input($udata);
                            ?>
                            <br />
                            <br />
                            <?php echo $this->lang->line('entry_password'); ?>:<br />
                            <?php
                            echo form_password($pdata);
                            ?>
                            
                            <br/>
                            <?php
                            echo anchor('register', $this->lang->line('text_register'), array('class' => 'btn-not-login'));
                            echo "<br/>";
                            echo anchor('forgot_password', $this->lang->line('text_forgot_password'), array('class' => 'btn-not-login'));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <a onclick="$('#form').submit();" class="button btn-not-login"><?php echo $this->lang->line('button_login'); ?></a>
                        </td>
                    </tr>
                </table>
                <?php if (isset($redirect)) { ?>
                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <?php } ?>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
    if (e.keyCode == 13) {
        $('#form').submit();
    }
});
//--></script>



