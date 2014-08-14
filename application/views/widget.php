<div id="content" class="span10 widget-page">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div><!-- .heading -->

        <div class="content">
        <h1>Choose a type of widget</h1>

        <ul class="nav nav-tabs">
	  <li class="active"><a href="#widget-leaderboard"  data-toggle="tab">Leaderboard</a></li>
	  <li><a href="#widget-livefeed" data-toggle="tab">Livefeed</a></li>
	  <li><a href="#widget-profile" data-toggle="tab">Profile</a></li>
	  <li><a href="#widget-userbar" data-toggle="tab">Userbar</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="widget-leaderboard">
			
			<h3>Leaderboard Wetget</h3>
			
			<div class="row">
		        	<div class="span5">
				<form class="form-horizontal">
					<div class="control-group">
						<label class="control-label" >Width</label>
						<div class="controls">
							<input type="text" class="wg-width" placeholder="The pixel width of the widget">
						</div>
					</div>
					<!-- <div class="control-group">
						<label class="control-label" >Height</label>
						<div class="controls">
							<input type="text" class="wg-height" placeholder="The pixel height of the widget">
						</div>
					</div> -->
					<div class="control-group">
						<label class="control-label" >Color</label>
						<div class="controls">
							<div class="input-prepend">
							  <span class="colorSelectorHolder add-on"></span>
							  <input class="span6 colorSelector wg-color"  type="text" placeholder="#ffaa00">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="wg-rank">Rank by</label>
						<div class="controls">
							<select class="wg-rankby" >
							  <option value="point">Point</option>
							  <option  value="exp">EXP</option>
							</select>
						</div>
					</div>

					<!--
					<div class="control-group">
						<label class="control-label" >Options</label>
						<div class="controls">
							<label class="checkbox">
								<input type="checkbox"> Remember me
							</label>
							<label class="checkbox">
								<input type="checkbox"> Remember me
							</label>
						</div>
					</div>
					-->
					<div class="control-group">
						<label class="control-label" ></label>
						<div class="controls">
							<a href="javascript:void(0);" onclick="reloadLeaderboard()" class="btn">Preview</a>
							<a href="#getcode-modal" role="button" data-toggle="modal" class="btn btn-primary" class="getcode-btn">Get code</a>
						</div>
					</div>
					
				</form>

		        	</div>
		        	<div class="span7">
		        		<iframe id="iframe-leaderboard" src="<?php echo base_url();?>index.php/widget/preview?type=leaderboard" width="100%" height="400" frameborder="0"></iframe>
		        	</div>
		        	</div>
	        	</div><!-- .tab-pane -->
	        	<div class="tab-pane" id="widget-livefeed">
			
			<h3>Livefeed Wetget</h3>
			
			<div class="row">
		        	<div class="span5">
				<form class="form-horizontal">
					<div class="control-group">
						<label class="control-label" >Width</label>
						<div class="controls">
							<input type="text" class="wg-width" placeholder="The pixel width of the widget">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" >Height</label>
						<div class="controls">
							<input type="text" class="wg-height" placeholder="The pixel height of the widget">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" >Color</label>
						<div class="controls">
							<div class="input-prepend">
							  <span class="colorSelectorHolder add-on"></span>
							  <input class="span6 colorSelector wg-color"  type="text" placeholder="#ffaa00">
							</div>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" ></label>
						<div class="controls">
							<a href="javascript:void(0);" onclick="reloadLivefeed()" class="btn">Preview</a>
							<a href="#getcode-modal" role="button" data-toggle="modal" class="btn btn-primary" class="getcode-btn">Get code</a>
						</div>
					</div>
					
				</form>

		        	</div>
		        	<div class="span7">
		        		<iframe id="iframe-livefeed" src="<?php echo base_url();?>index.php/widget/preview?type=livefeed" width="100%" height="500" frameborder="0"></iframe>
		        	</div>
		        	</div>
	        	</div><!-- .tab-pane -->
	        	<div class="tab-pane" id="widget-profile">
	        		<h3>Profile Wetget</h3>
			
			<div class="row">
		        	<div class="span6 offset3">
				<form class="form-horizontal">
					<div class="control-group">
						<label class="control-label" >Width</label>
						<div class="controls">
							<input type="text" class="wg-width" placeholder="The pixel width of the widget">
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" >Color</label>
						<div class="controls">
							<div class="input-prepend">
							  <span class="colorSelectorHolder add-on"></span>
							  <input class="span6 colorSelector wg-color"  type="text" placeholder="#ffaa00">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="wg-displaypoint">Type point display </label>
						<div class="controls">
							<select class="wg-displaypoint" >
							  <option value="point">Point</option>
							  <option  value="exp">EXP</option>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" ></label>
						<div class="controls">
							<a href="javascript:void(0);" onclick="reloadProfile()" class="btn">Preview</a>
							<a href="#getcode-modal" role="button" data-toggle="modal" class="btn btn-primary" class="getcode-btn">Get code</a>
						</div>
					</div>
					
				</form>

		        	</div>
		        	<div class="span11">
		        		<iframe id="iframe-profile" src="<?php echo base_url();?>index.php/widget/preview?type=profile" width="100%" height="280" frameborder="0"></iframe>
		        	</div>
		        	</div>
	        	</div><!-- .tab-pane -->
	        	<div class="tab-pane" id="widget-userbar">
	        		<h3>Userbar Wetget</h3>
			
			<div class="row">
		        	<div class="span6 offset3">
				<form class="form-horizontal">
					<!-- <div class="control-group">
						<label class="control-label" >Width</label>
						<div class="controls">
							<input type="text" class="wg-width" placeholder="The pixel width of the widget">
						</div>
					</div> -->
					
					<div class="control-group">
						<label class="control-label" >Color</label>
						<div class="controls">
							<div class="input-prepend">
							  <span class="colorSelectorHolder add-on"></span>
							  <input class="span6 colorSelector wg-color"  type="text" placeholder="#ffaa00">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="wg-displaypoint">Type point display </label>
						<div class="controls">
							<select class="wg-displaypoint" >
							  <option value="point">Point</option>
							  <option  value="exp">EXP</option>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" ></label>
						<div class="controls">
							<a href="javascript:void(0);" onclick="reloadUserbar()" class="btn">Preview</a>
							<a href="#getcode-modal" role="button" data-toggle="modal" class="btn btn-primary" class="getcode-btn">Get code</a>
						</div>
					</div>
					
				</form>

		        	</div>
		        	<div class="span11">
		        		<iframe id="iframe-userbar" src="<?php echo base_url();?>index.php/widget/preview?type=userbar" width="100%" height="280" frameborder="0"></iframe>
		        	</div>
		        	</div>
	        	</div><!-- .tab-pane -->
	        </div>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->



<script>
	$(document).ready(function(){
		$('#widget-leaderboard a:first').tab('show');
		$('.colorSelector').ColorPicker({
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
				console.log($(this));
			},
			onShow: function (colpkr) {
				$(colpkr).fadeIn(200);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(200);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('.colorSelectorHolder').css('backgroundColor', '#' + hex);
				$('.colorSelector').val('#' +hex);
				//TODO:
				// reloadLeaderboard();
				// reloadLivefeed();
				// reloadProfile();
				// reloadUserbar();
			}
		}).bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
			$('.colorSelectorHolder').css('backgroundColor', this.value);
			if(event.keyCode == 13){
				$(this).ColorPickerHide();
			}
		});
		$('.colorSelectorHolder').click(function(){
			$('.colorSelector').focus();
		});

		$('form').submit(function(e){
			e.preventDefault();
		});

		$('#widget-leaderboard input, #widget-leaderboard select').bind("change paste blur", function() {
			clearTimeout(timeout);
			timeout = setTimeout(reloadLeaderboard,timeBuffer);
		});
		$('#widget-livefeed input, #widget-livefeed select').bind("change paste blur", function() {
			clearTimeout(timeout);
			timeout = setTimeout(reloadLivefeed,timeBuffer);
		});
		$('#widget-profile input, #widget-profile select').bind("change paste blur", function() {
			clearTimeout(timeout);
			timeout = setTimeout(reloadProfile,timeBuffer);
		});
		$('#widget-userbar input, #widget-userbar select').bind("change paste blur", function() {
			clearTimeout(timeout);
			timeout = setTimeout(reloadUserbar,timeBuffer);
		});

		$('#getcode-modal').on('show', function () {

		})
		
		$('a[data-toggle="tab"]').on('shown', function (e) {
			tabActive = $(this).attr('href');
			switch(tabActive){
				case "#widget-leaderboard":
					reloadLeaderboard();
				break;
				case "#widget-livefeed":
					reloadLivefeed();
				break;
				case "#widget-profile":
					reloadProfile();
				break;
				case "#widget-userbar":
					reloadUserbar();
				break;
			}
		})

	});
	var tabActive = '#widget-leaderboard';
	reloadLeaderboard();
	var timeBuffer = 500;
	var isReload = false;
	var timeout = setTimeout(void(0),0);
	function updateWidget(type){
		clearTimeout(timeout);
		timeout = setTimeout(reloadLeaderboard,timeBuffer);
	}
	function reloadLeaderboard(){
		var width = getVal($('#widget-leaderboard .wg-width').val());
		// var height = getVal($('#widget-leaderboard .wg-height').val());
		var color =getColor($('#widget-leaderboard .wg-color').val());
		var rankby =$('#widget-leaderboard .wg-rankby').val();
		var url = '<?php echo base_url();?>index.php/widget/preview?type=leaderboard';
		var codeElement = '&lt;div class="pb-leaderboard" ';
		var codeHeader = codeHeaderTemplate;

		if(typeof width != 'undefined' && width != ""){
			url+='&width='+width;
			codeElement += 'data-pb-width="'+width+'" ';
		}
		// if(typeof height != 'undefined' && height != ""){
		// 	url+='&height='+height;
		// }
		if(typeof color != 'undefined' && color != ""){
			url+='&color='+color;
			codeHeader = codeHeader.replace(/#0e9ce4/, color);
		}
		if(typeof rankby != 'undefined'  && rankby != ""){
			url+='&rankby='+rankby;
			codeElement += 'data-pb-rankBy="'+rankby+'" ';
		}
		codeElement += '&gt;&lt;/div&gt;';

		$('#iframe-leaderboard').attr('src',url);

		$('#getcode-modal .code-element').html(codeElement);
		$('#getcode-modal .code-header').html(codeHeader);
	}
	function reloadLivefeed(){
		var width = getVal($('#widget-livefeed .wg-width').val());
		var height = getVal($('#widget-livefeed .wg-height').val());
		var color =getColor($('#widget-livefeed .wg-color').val());
		var url = '<?php echo base_url();?>index.php/widget/preview?type=livefeed';
		var codeElement = '&lt;div class="pb-livefeed" ';
		var codeHeader = codeHeaderTemplate;

		if(typeof width != 'undefined' && width != ""){
			url+='&width='+width;
			codeElement += 'data-pb-width="'+width+'" ';
		}
		if(typeof height != 'undefined' && height != ""){
			url+='&height='+height;
			codeElement += 'data-pb-height="'+height+'" ';
		}
		if(typeof color != 'undefined' && color != ""){
			url+='&color='+color;
			codeHeader = codeHeader.replace(/#0e9ce4/, color);
		}
		codeElement += '&gt;&lt;/div&gt;';
		$('#iframe-livefeed').attr('src',url);

		$('#getcode-modal .code-element').html(codeElement);
		$('#getcode-modal .code-header').html(codeHeader);
	}
	function reloadProfile(){
		var width = getVal($('#widget-profile .wg-width').val());
		var color =getColor($('#widget-profile .wg-color').val());
		var displaypoint =getColor($('#widget-profile .wg-displaypoint').val());
		var url = '<?php echo base_url();?>index.php/widget/preview?type=profile';
		var codeElement = '&lt;div class="pb-profile" ';
		var codeHeader = codeHeaderTemplate;

		if(typeof width != 'undefined' && width != ""){
			url+='&width='+width;
			codeElement += 'data-pb-width="'+width+'" ';
		}
		if(typeof color != 'undefined' && color != ""){
			url+='&color='+color;
			codeHeader = codeHeader.replace(/#0e9ce4/, color);
		}
		if(typeof displaypoint != 'undefined'  && displaypoint != ""){
			url+='&displaypoint='+displaypoint;
			codeElement += 'data-pb-displayPoint="'+displaypoint+'" ';
		}
		codeElement += '&gt;&lt;/div&gt;';
		$('#iframe-profile').attr('src',url);
		$('#getcode-modal .code-element').html(codeElement);
		$('#getcode-modal .code-header').html(codeHeader);
	}
	function reloadUserbar(){
		var color =getColor($('#widget-userbar .wg-color').val());
		var displaypoint =getColor($('#widget-profile .wg-displaypoint').val());
		var url = '<?php echo base_url();?>index.php/widget/preview?type=userbar';
		var codeElement = '&lt;div class="pb-profile" ';
		var codeHeader = codeHeaderTemplate;

		if(typeof color != 'undefined' && color != ""){
			url+='&color='+color;
			codeHeader = codeHeader.replace(/#0e9ce4/, color);
		}
		if(typeof displaypoint != 'undefined'  && displaypoint != ""){
			url+='&displaypoint='+displaypoint;
			codeElement += 'data-pb-displayPoint="'+displaypoint+'" ';
		}
		codeElement += '&gt;&lt;/div&gt;';
		$('#iframe-userbar').attr('src',url);
		$('#getcode-modal .code-element').html(codeElement);
		$('#getcode-modal .code-header').html(codeHeader);
	}
	function getVal(val){
		if(typeof val == 'undefined' ){
			return '';
		}
		var lastStr = val.slice(-1);
		if(lastStr == '%'){
			return val;
		}else{
			return val.replace(/[^0-9%]/g,'');
		}
	}
	function getColor(val){
		var color = val.replace(/#/g, '');
		return color;
	}

	var codeHeaderTemplate= "&lt;script&gt;\nwindow.PBAsyncInit = function(){\n\tPB.init({\n\t\tapi_key:'abc',\n\t\ttheme_color :'#0e9ce4'\n\t});\n};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://widget.pbapp.net/playbasis/en/all.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','playbasis-js'));&lt;/script&gt;";


</script>

<div id="getcode-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:800px;margin-left:-400px">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3>Get Code</h3>
	  </div>
	  <div class="modal-body">
	  <p>Include the JavaScript SDK on your page once, ideally right after the opening &lt;body&gt; tag.</p>
	 	<pre class="prettyprint code-header">
&lt;script&gt;
window.PBAsyncInit = function(){
    PB.init({
        api_key:'abc',
        theme_color :'#52b398'
    });
};(!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://widget.pbapp.net/playbasis/en/all.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","playbasis-js"));&lt;/script&gt;
	            </pre>
	            <p>Place the code for your widget wherever you want the widget to appear on your page.</p>
	            <pre class="prettyprint code-element">
&lt;div class="pb-leaderboard"  data-pb-width="360" data-pb-rankBy="point" &gt;&lt;/div&gt;
	            </pre>
	  </div>
	</div>