<!doctype html>
<html>
<head>
<link rel="stylesheet" href="<?php echo base_url();?>resource/css/reset.css" />
<script type="text/javascript" src="<?php echo base_url();?>resource/js/jQuery-1.9.0.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/node_modules/socket.io/node_modules/socket.io-client/distsocket.io.js"></script>
<title>Playbasis API Test</title>
<style type="text/css">
	body{font: 13px/20px normal Helvetica, Arial, sans-serif; color: #4F5155; }
	h1,h3{color: #444; background-color: transparent; font-size: 19px; font-weight: normal; margin: 0 0 14px 0; padding: 14px 15px 10px 15px;}
	h3{font-size: 16px;}
	input{width:180px; }
	table td{padding:3px;}
	pre ,.result-panel div{outline: 1px solid #ccc; padding: 5px; margin: 15px; background-color: #fff}
	.string { color: green; }
	.number { color: darkorange; }
	.boolean { color: blue; }
	.null { color: magenta; }
	.key { color: red; }
	.section{ margin: 0 0 10px 20px; padding: 10px}

	.container{
		width: 1080px;
		margin: 0 auto;
		margin-top: 50px;
	}
	
	.debug-window{
		background-color:#cfc; 
		height: 200px;
	}

	.action-panel{
		background-color:#ccf; 
		/*min-height: 600px;	*/
	}
	.side-panel{
		background-color: #cff;
		width: 30%;
	}
	.main-panel{
		width: 70%;
		background-color: #ffc;
	}
	.result-panel{
		background-color:#fcc;
		height: auto;
		padding-bottom: 5px; 
	}
	.control-panel{
		background-color: #fff;
		padding: 15px 0 15px 0;
	}.control-panel > select:first-child{
		margin-left:25px; 
	}
	.control-panel > select,.control-panel > input{
		margin-right:25px; 
		padding: 3px;
		width: 150px;
	}
	.control-process{
		margin: 20px 0 26px 20px;
	}
	.text{

		color: red;
		font-weight: bold;
		font-size: 15px;
		padding-left: 15px;
	}

	.left{
		float: left;
	}
	.right{
		float: right;
	}
	.clearfix:after { 
	   content: "."; 
	   visibility: hidden; 
	   display: block; 
	   height: 0; 
	   clear: both;
	}
	.clear{
		clear: both;
	}

</style>
</head>
<body>
	<div class="container">
		<div class="debug-window">
			<h1>DEBUG</h1>
			<div class="section left" id="debug-api">
				<table>
					<tr>
						<td>API KEY</td>
						<td id="debug-api-key" class="text">undefined</td>
					</tr>
					<tr>
						<td>API SECRET</td>
						<td id="debug-api-secret" class="text">undefined</td>
					</tr>
					<tr>
						<td>TOKEN</td>
						<td id="debug-api-token" class="text">undefined</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="action-panel">
			<h1>ACTION PANEL</h1>
			<div class="side-panel left ">
				<h3>Setting</h3>
				<div class="section left" id="setting-api">
					<table>
					<tr>
						<td><label for="apiKey">API-KEY</label></td>
						<td><input type="text" name="apiKey" id="apiKey"/></td>
					</tr>
					<tr>
						<td><label for="apiSecret">API-SECRET</label></td>
						<td><input type="text" name="apiSecret" id="apiSecret"/></td>
					</tr>
					<tr>
						<td></td>
						<td><button id="setAPIData">SET</button><button id="resetAPIData">RESET</button></td>
					</tr>
					</table>
				</div>
			</div>
			<div class="main-panel left">
				<h3>Control</h3>
				<div class="control-panel">
					<!-- <label for="api-selector">Select API</label> -->
					<select id="api-selector" name="api-selector">
						<option value="">-- API --</option>
						<option value="Auth">Auth</option>
						<option value="Player">Player</option>
						<option value="Engine">Engine</option>
					</select>
					<select id="method-selector" disabled = "disabled">
						<option value="">-- METHOD --</option>
						<option value="register">Register</option>
						<option value="login">Login</option>
					</select>
					<label for="param">PARAMETER : </label>
					<input name="param" id="api-parameter" type="text" placeholder="format {key:val,key:val,...}" disabled = "disabled" />
					<button id="runAPI">TEST</button>
				</div>
				<div class="control-process">
					<div><span>STATUS :</span><span id="url" class="text"></span></div>
					<!-- <div><span>METHOD : </span><span id="method" class="text">POST</span></div> -->
				</div>
			</div>
			<div class="clear"></div>		
			<div class="result-panel">
				<h1>RESULT</h1>
				<div id="result">
					request detail
				</div>
				<pre id="response">response...</pre>
			</div>
		</div>
	</div>
<script type="text/javascript">
	(function(){
		var baseURL = '//api.pbapp.net/',
			apiKey,apiSecret,token;

		//bind click set API
		$('#setAPIData').bind('click',function(e){
			e.preventDefault();
			apiKey 		= $('#apiKey').val();
			apiSecret 	= $('#apiSecret').val();
			$('#setting-api input').attr('disabled','disabled');
			
			// console.log('key = '+apiKey);
			// console.log(typeof apiKey);
			// console.log('secret = '+apiSecret);
			// console.log(typeof apiSecret);
			$('#debug-api-key').html(apiKey);
			$('#debug-api-secret').html(apiSecret);
		});


		//bind click reset API
		$('#resetAPIData').bind('click',function(e){
			e.preventDefault();
			apiKey = apiSecret = '';
			$('#debug-api td.text').html('undefined');
			$('#setting-api input').val('').removeAttr('disabled');
		});

		//toggle action
		$('#api-selector').bind('change',function(e){
			e.preventDefault();
			var api = $('#api-selector :selected').val();
			if(api == 'Auth')
				$('#api-parameter').attr('disabled','disabled');
			else
				$('#api-parameter').removeAttr('disabled','disabled');
			if( api	== 'Player')
				$('#method-selector').removeAttr('disabled');
			else
				$('#method-selector').attr('disabled','disabled');
		});

		$('#runAPI').bind('click',function(e){
			e.preventDefault();
			var	apiName = $('#api-selector :selected').val(),
				requestUrl = '',
				data;
			if(apiName == ''){
				showHideMessage($('.control-process #url'),'ERROR,Please select API');
				return false;
			}

			if($('#api-parameter').val() != ''){
				try{
		 			var data = JSON.parse($('#api-parameter').val());
		 		}
				catch(err){
					showHideMessage($('.control-process #url'),'ERROR,Parse Parameter Error,Please use Json format');
					return false;
				}
			}
			else
				data={};
			
			switch(apiName){
				case 'Auth' :{
					requestUrl = baseURL+'Auth/';
					break;					
				} 
				case 'Player':{
					requestUrl = baseURL+'Player/';
					if('player_id' in data){
						requestUrl += data.player_id;
					}
					
					var method = $('#method-selector :selected').val();
					if(method)
						requestUrl += '/'+method;
					break;					
				}
				case 'Engine' :{
					requestUrl = baseURL+'Engine/rule/';
					break;					
				}

			}

			makeRequest(requestUrl,data,apiName);
		})

		function makeRequest(requestUrl,data,apiName){
			// if('player_id' in data)
			// 	delete data.player_id;

			if(apiName != 'Auth')
				data['token'] = token;	//add token
			else{
				data['api_key'] = apiKey;
				data['api_secret'] = apiSecret;
			}

			$.ajax({
				url 		: requestUrl,
				data		: $.param(data),
				dataType	: 'json',
				type 		: 'POST',
				beforeSend  : function(){
					$('.control-process #url').html('Requesting...');
				},
				success		: function(resp){
						//show result
						$('.result-panel #result').html(
							'<div> REQUEST URL : '+requestUrl+'</div><div> METHOD : POST</div>'
						);
						//update state
						showHideMessage($('.control-process #url'),'Finish');

						if(resp.success){
							//update token
							if(apiName == 'Auth'){
								token = resp.response.token;
								$('#debug-api-token').html(token);
							}	
						}

						output(syntaxHighlight(JSON.stringify(resp,undefined,4)));
				},
				error 		: function(){},
			});
			// console.log(requestUrl);
			// console.log(data);
		}

		function showHideMessage(obj,message){
			obj.queue(function() { obj.html(message).dequeue()}).delay(3000).queue(function() { obj.html('').dequeue() });
		}

		//json pretty
		function output(inp) {
    		$('#response').html(inp);
		}
		function syntaxHighlight(json) {
			json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
			return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
	    
	        	var cls = 'number';
	        	if (/^"/.test(match)) {
	            	if (/:$/.test(match)) {
	                	cls = 'key';
	            	} 
	            	else {
	                	cls = 'string';
	            	}
	        	} 
	        	else if (/true|false/.test(match)) {
	            	cls = 'boolean';
	        	} else if (/null/.test(match)) {
	            	cls = 'null';
	        	}
	        	return '<span class="' + cls + '">' + match + '</span>';
			});
		}

	})();	
</script>
<script type="text/javascript">
	var socket = io.connect('//dev.pbapp.net:3000');
	socket.on('connect', function(data){
		console.log('client connected');
		socket.emit('subscribe', {channel:location.host});
	});
	socket.on('message', function(data){
		//console.log('msgrecv');
		console.log(data);
	});
</script>
</body>
</html>