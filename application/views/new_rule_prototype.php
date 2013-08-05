
<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/new_rule/style.css">
<script src="<?php echo base_url();?>javascript/new_rule/actionTransfrom.js"></script>
<script src="<?php echo base_url();?>javascript/new_rule/scripts.js"></script>
<script src="<?php echo base_url();?>javascript/new_rule/handlebars.runtime.min.js"></script>
<script src="<?php echo base_url();?>javascript/new_rule/rule.js"></script>
<script src="<?php echo base_url();?>javascript/new_rule/rule-box.js"></script>


<div class="row-fluid">
	<div class="box span12">
		<div class="box-header">
			<h2><i class="icon-th-large"></i><span class="break"></span>Rule Manager</h2>
		</div>
		<div class="box-content">

<!-- start container -->
<div id="rule-masonry">

	<!-- start rule -->
	<div id="add-rule" class="rule-container">
	<div class="rule">

		<div class="add-rule">
			<i class="icon-plus"></i>
			<span>Add Rule</span>
		</div>

		<!-- start rule-header -->
		<div class="rule-header rule-box-header">
			<i class="icon-plus"></i>
		</div>
		<!-- end rule-header -->

		<div class="rule-minimize">
			<span title="Minimize Rule">X</span>
		</div>

		<!-- start rule-content -->
		<div class="rule-content">

			<div class="rule-title">
				<input type="text" name="Rule Title" id="add-rule-title" placeholder="Give your rule a name" value="">
				<i class="icon-pencil"></i>
			</div>

			<div class="rule-description">
				<textarea id="add-rule-description" class="" placeholder="Add a description here, so you can remember what your rule is about." rows="4" cols="60"></textarea>
				<i class="icon-pencil"></i>
			</div>

			<!-- start rule-templates -->
			<div class="rule-templates">

				<h4>Choose an action for your rule from the list below:</h4>

				<div class="rule-templates-low">

					<div class="rule-purple">
					<div class="rule-template rule-box rule-box-extra">
						<div class="rule-template-action rule-box-header">
							<i class="icon-map-marker"></i>
							<span>Visit</span>
						</div>
						<div class="rule-template-reward rule-box-footer">
							<span class="rule-template-reward-quantity rule-box-highlight">100</span>
							<span class="rule-template-reward-type">Points</span>
						</div>
					</div>
					</div>

					<div class="rule-orange">
					<div class="rule-template rule-box rule-box-extra">
						<div class="rule-template-action rule-box-header">
							<i class="icon-bookmark-empty"></i>
							<span>Read</span>
						</div>
						<div class="rule-template-reward rule-box-footer">
							<span class="rule-template-reward-quantity rule-box-highlight">100</span>
							<span class="rule-template-reward-type">Points</span>
						</div>
					</div>
					</div>

					<div class="rule-green">
					<div class="rule-template rule-box rule-box-extra">
						<div class="rule-template-action rule-box-header">
							<i class="icon-thumbs-up"></i>
							<span>Like</span>
						</div>
						<div class="rule-template-reward rule-box-footer">
							<span class="rule-template-reward-quantity rule-box-highlight">100</span>
							<span class="rule-template-reward-type">Points</span>
						</div>
					</div>
					</div>

				</div>

				<div class="rule-templates-medium">

					<div class="rule-yellow">
					<div class="rule-template rule-box">
						<div class="rule-template-action rule-box-header">
							<i class="icon-share"></i>
							<span>Share</span>
						</div>
						<div class="rule-template-reward rule-box-footer">
							<span class="rule-template-reward-quantity rule-box-highlight">300</span>
							<span class="rule-template-reward-type">Points</span>
						</div>
					</div>
					</div>

					<div class="rule-pink">
					<div class="rule-template rule-box">
						<div class="rule-template-action rule-box-header">
							<i class="icon-comment"></i>
							<span>Comment</span>
						</div>
						<div class="rule-template-reward rule-box-footer">
							<span class="rule-template-reward-quantity rule-box-highlight">300</span>
							<span class="rule-template-reward-type">Points</span>
						</div>
					</div>
					</div>

				</div>

				<div class="rule-templates-high">

					<div class="rule-blue">
					<div class="rule-template rule-box">
						<div class="rule-template-action rule-box-header">
							<i class="icon-flag"></i>
							<span>Review</span>
						</div>
						<div class="rule-template-reward rule-box-footer">
							<span class="rule-template-reward-quantity rule-box-highlight">500</span>
							<span class="rule-template-reward-type">Points</span>
						</div>
					</div>
					</div>

				</div>

			</div>
			<!-- end rule-templates -->

		</div>
		<!-- end rule-content -->

	</div>
	</div>
	<!-- end rule -->

</div>
<!-- end container -->

		</div>
	</div>
</div>

<hr>
