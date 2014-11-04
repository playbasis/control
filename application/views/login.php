<!-- Nav tabs -->
<ul class="nav nav-tabs" id="tab-page-nav" role="tablist" >
  <li class="active"><a href="#login" role="tab" data-toggle="tab">Login</a></li>
  <li ><a href="#register" role="tab" data-toggle="tab">Register</a></li>
  <li ><a href="#forgotpassword" role="tab" data-toggle="tab">Forgot Password</a></li>
  <li ><a href="#resetpassword" role="tab" data-toggle="tab">Reset Password</a></li>
  <li ><a href="#completeprofile" role="tab" data-toggle="tab">Complete Profile</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="login">
      <?php $this->load->view('partial/login_partial'); ?>
  </div>
  <div role="tabpanel" class="tab-pane in" id="register">
        <?php $this->load->view('partial/register_partial'); ?>
  </div>
  <div role="tabpanel" class="tab-pane in" id="forgotpassword">
        <?php $this->load->view('partial/forgotpassword_partial'); ?>
  </div>
  <div role="tabpanel" class="tab-pane in" id="resetpassword">
        <?php $this->load->view('partial/resetpassword_partial'); ?>
  </div>
  <div role="tabpanel" class="tab-pane in" id="completeprofile">
        <?php $this->load->view('partial/completeprofile_partial'); ?>
  </div>
</div>