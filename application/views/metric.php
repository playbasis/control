        <noscript>
            <div class="alert alert-block span10">
                <h4 class="alert-heading">Warning!</h4>
                <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
            </div>
        </noscript>

        <div id="content" class="span10">
            <!-- start: Content -->

	        <!-- start: second panel -->
	        <div id="actionPanel" class="row-fluid dashboard-panel">

		        <div class="box span12" onTablet="span12" onDesktop="span12">
			        <div class="box-header">
				        <h2><i class="icon-star"></i><span class="break"></span>Requests to API</h2>
				        <div class="box-icon">
					        <a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
				        </div>
			        </div>
			        <div class="box-content">
				        <div class="row-fluid control-row">
					        <div class="span2 side-columm">Client Selection</div>
					        <div class="span10">
								<?php
								$attributes = array('class' => 'form-inline span6');
								echo form_open('', $attributes);?>
<!--						        <form class="form-inline span6">-->
							        <label for="reward-start">From</label>
							        <input type="text" class="input span3 datepicker" id="reward-start" value="">
							        <label for="reward-end">To</label>
							        <input type="text" class="input span3 datepicker" id="reward-end" value="">
							        <input type="button" class="btn" id="submitRewardDate" value="GO">
								<?php echo form_close();?>
<!--						        </form>-->
								<?php
								$attributes = array('class' => 'form-inline span5 has-border');
								echo form_open('', $attributes);?>
<!--						        <form class="form-inline span5 has-border">-->
							        <label for="reward-unit-type">Unit Type</label>
							        <select id="reward-unit-type" class="span6">
								        <option value="day" selected>Daily</option>
								        <option value="month">Monthly</option>
							        </select>
							        <input type="button" class="btn" id="submitRewardUnit" value="GO">
<!--						        </form>-->
								<?php echo form_close();?>
					        </div>
				        </div>
				        <div class="row-fluid result-row">
					        <div class="span2 side-column">
<!--						        <form>-->
								<?php
								echo form_open();?>
							        <!--div class="row-fluid control-row">
								        <div class="span12">
									        <select id="reward-type" class="span11">
										        <option value="badge">Badge</option>
									        </select>
								        </div>
							        </div-->
							        <div class="scroll-container">
								        <ul id="reward-badge-panel" class="reward pull-left">
								        </ul>
							        </div>
							        <div id="reward-badge-action" class="action-container">
								        <input type="checkbox" checked="checked" id="allBadges">All Clients
							        </div>
<!--						        </form>-->
								<?php echo form_close();?>
					        </div>
					        <div class="span7">
						        <div id="playbasis-reward-stack-chart" class="api-result-container"></div>
					        </div>
					        <div class="span3 chart-canvas">
						        <div class="row-fluid">
							        <div class="span12">
								        <div id="playbasis-reward-compare-stack-chart" class="chart api-result-container"></div>
							        </div>
						        </div>
						        <div class="row-fluid control-row line-up">
							        <div class="span12">
										<?php
										$attributes = array('class' => 'form-inline');
										echo form_open('', $attributes);?>
<!--								        <form class="form-inline">-->
									        <select id="reward-compare-type" class="span8">
										        <option value="percentage" selected>Percentage</option>
										        <option value="gross">Value</option>
									        </select>
									        <input type="button" class="btn" id="submitRewardCompareType" value="GO">
<!--								        </form>-->
										<?php echo form_close();?>
							        </div>
						        </div>
					        </div>
				        </div>
			        </div>
		        </div>
	        </div>
	        <!-- end: second panel -->

            <hr>

            <!-- end: Content -->
        </div>
        <!--/#mainContent-->
    </div>
    <!--/fluid-row-->

    <div class="clearfix"></div>

    <footer>

    </footer>

</div><!--/.fluid-container-->

<!-- required by the tooltip plugin -->
<div id="action-tool-tip" class="tooltip fade top in" style="display: none;">
    <div class="tooltip-arrow"></div>
    <div class="tooltip-inner">Tooltip on top</div>
</div>

<!-- start: JavaScript-->

<script src="<?php echo base_url();?>javascript/insights/jquery.ui.touch-punch.js"></script>
<script src="<?php echo base_url();?>javascript/insights/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.cookie.js"></script>
<script src='<?php echo base_url();?>javascript/insights/fullcalendar.min.js'></script>
<script src='<?php echo base_url();?>javascript/insights/jquery.dataTables.min.js'></script>
<script src="<?php echo base_url();?>javascript/insights/excanvas.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.flot.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.flot.pie.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.flot.stack.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.flot.resize.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.flot.time.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.chosen.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.uniform.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.cleditor.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.noty.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.elfinder.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.raty.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.iphone.toggle.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.uploadify-3.1.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.gritter.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.imagesloaded.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.masonry.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.knob.js"></script>
<script src="<?php echo base_url();?>javascript/insights/jquery.sparkline.min.js"></script>
<script src="<?php echo base_url();?>javascript/insights/colorchain.js"></script>
<script src="<?php echo base_url();?>javascript/insights/custom.js"></script>
<script src="<?php echo base_url();?>javascript/insights/playbasis-metric.js"></script>

<!-- end: JavaScript-->