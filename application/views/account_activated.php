<div class="activated-wrapper">
	<div class="activated-content modal-card">
		<div class="modal-card-head" >
			<h3>Thanks for signing up to Playbasis!</h3>
			<i class="fa fa-envelope-o big-icon"></i>
		</div>
		<div class="modal-card-content" >
			<h3>Please confirm your email address</h3>
			<p>We sent the verification email to:</p>
			<p>
				<strong><?php echo $user_before_info['email']; ?></strong>
			</p>
			<hr>
			<p>
				No message received? <a href="<?php echo $url_resend; ?>">Resend Sign-Up Email</a>
			</p>
		</div>
	</div>
</div>

