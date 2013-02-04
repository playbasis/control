<!doctype html>
<html>
<head>
<link rel="stylesheet" href="<?php echo base_url();?>resource/css/reset.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
<script type="text/javascript" src="<?php echo base_url();?>resource/js/jQuery-1.9.0.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>node_server/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script>
<title>Playbasis API Test</title>
<style type="text/css">
	body{font: 13px/20px normal Helvetica, Arial, sans-serif; color: #4F5155; }
	h1,h3{color: #444; background-color: transparent; font-size: 19px; font-weight: normal; margin: 0 0 14px 0; padding: 14px 15px 10px 15px;text-decoration: underline;}
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
		margin: 0 20px;
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
		background-color:#ccf; 
		/*background-color: #cff;*/
		width: 30%;
	}
	.main-panel{
		width: 70%;
		background-color:#ccf; 
		/*background-color: #ffc;*/
	}
	.result-panel{
		background-color:#fcc;
		height: auto;
		padding-bottom: 5px; 
	}
	.control-panel{
		/*background-color: #fff;*/
		padding: 15px 0 15px 0;
	}.control-panel > select:first-child{
		margin-left:25px; 
	}
	.control-panel > select,.control-panel > input{
		/*margin-right:25px; */
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
	.option-panel{
		margin: 15px 0 10px 25px;
	}
	
	#method-selector{
		width:515px;
	}
	#api-parameter{
		width: 585px;
	}
	#player-id{
		width: 50px;
	}
	b{
		font-size: 20px;
	}
	#notification-box{
		position: absolute;
		top: 50px;
		right: 20px;
		width: 400px;
		height: 700px;
		/*background-color: #fcf;*/
		border: 1px solid #fcc;
		overflow-x: auto;
		overflow-y: visible;
	}
	#notification-box h2{
		margin: 10px;
		font-weight: bold;
		padding:2px;
		background-color: #ccf;
	}
	.notification-node{
		position: relative;
		width: auto;
		height: 80px;
		margin: 10px;
		padding: 10px;
		background-color: #eee;
	}
	.notification-node img{
		width: 80px;
		height: 80px;
	}
	.notification-message{
		margin-left: 10px;
		/*position: relative;*/
	}
	.notification-message .player-name{
		font-size: 16px;
		font-weight: bold;
		text-decoration: underline;
	}
	.notification-message p.message{
		font-size: 15px;
		margin-top: 10px;
	}
	.notification-message div.time{
		font-size: 12px;
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
						<option value="Badge">Badge</option>
						<option value="Engine">Engine</option>
					</select>
					<b>/</b>
					<input id="method-selector" disabled = "disabled">
					<br/>
					<br/>
					<label for="param" style="margin-left:25px;">PARAMETER : </label>
					<input name="param" id="api-parameter" type="text" placeholder="JSON format {key:val,key:val,...}" disabled = "disabled" />
					<br/>
					<br/>
					<button id="runAPI" style="margin-left:25px;" title="Run Request">Test</button>
					<button id="stopAPI" style="margin-left:25px;"  title="Stop Request">Stop</button>
					
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

	<!-- Notification -->
	<div id="notification-box">
		<h2>NOTIFICATION DEMO</h2>
		<div class="notification-node">
			<img class="left" src="" alt="player-image" />
			<div class="notification-message left">
				<p class="message"><span class="player-name">playername</span><br/>message</p>
				<div class="time">time</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>

<script type="text/javascript">
	

	(function(){
		//var baseURL = '//api.pbapp.net/',
		//var baseURL = '//localhost/api/',
		var baseURL = '//dev.pbapp.net/api/index.php/',
			methodArray = {
				'Player' : [
					'{player ID}',
					'{player ID}/register',
					'{player ID}/login',
					'{player ID}/logout',
					'{player ID}/points',
					'{player ID}/point/{point name}',
					'{player ID}/action[/action name]{/time | count}',
				],
				'Badge'	: [
					'[badge ID]',
					'collection[/collection ID]',
				],
				'Engine' : [
					'rule',
				]
			},
			apiKey,apiSecret,token,option,xhr;

		//bind click set API
		$('#setAPIData').bind('click',function(e){
			e.preventDefault();
			apiKey 		= $('#apiKey').val();
			apiSecret 	= $('#apiSecret').val();
			$('#setting-api input').attr('disabled','disabled');
			
			
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

		//bind click stop API
		$('#stopAPI').bind('click',function(e){
			e.preventDefault();
			if(xhr)
				xhr.abort();
		});

		//toggle action
		$('#api-selector').bind('change',function(e){
			e.preventDefault();
			var api = $('#api-selector :selected').val();
			//console.log(api);
			if(api == 'Auth'){
				$('#api-parameter').attr('disabled','disabled');
				$('#api-parameter').val('');				
			}
			else{
				$('#api-parameter').removeAttr('disabled','disabled');
				$('#api-parameter').val('{"token" :"'+token+'"}');
			}
			if( api	in methodArray){
				
				$('#method-selector').removeAttr('disabled');
				//auto complete
				$( "#method-selector" ).autocomplete({
      				source: methodArray[api]
    			});
			}
			else
				$('#method-selector').attr('disabled','disabled');
		});

		
		//run api
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
				case 'Player':
				case 'Engine':
				case 'Badge':
				{
					requestUrl = baseURL+apiName+'/'+$('#method-selector').val(); 
					break;					
				}
			}

			makeRequest(requestUrl,data,apiName);
		})

		function makeRequest(requestUrl,data,apiName){
			// if('player_id' in data)
			// 	delete data.player_id;
			//console.log(apiName);
			if(apiName != 'Auth')
				data['token'] = token;	//add token
			else{
				data['api_key'] = apiKey;
				data['api_secret'] = apiSecret;
			}

			xhr = $.ajax({
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
						console.log(resp);
						if(resp.status){
							//update token
							if(apiName == 'Auth'){
								token = resp.response.token;
								$('#debug-api-token').html(token);
								//console.log(token);
							}	
						}

						output(syntaxHighlight(JSON.stringify(resp,undefined,5)));
				},
				error 		: function(resp){
					showHideMessage($('.control-process #url'),'ERROR ,Something wrong with API method');
					output(syntaxHighlight(JSON.stringify(resp,undefined,5)));
				},
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
	var socket = io.connect('//pbapp.net:3000');
	socket.on('connect', function(data){
		console.log('client connected');
		socket.emit('subscribe', /*{channel:location.host}*/{channel:'playbasis.com'});
	});
	socket.on('message', function(data){
		data = JSON.parse(data);
		console.log(data);
		$('#notification-box').append('<div class="notification-node"><img class="left" src="'+data.actor.image.url+'" alt="name" /><div class="notification-message left"><p class="message"><span class="player-name">'+data.actor.displayName+'</span><br/>'+data.object.message+' via '+data.verb+' action</p><div class="time">'+data.published+'</div></div><div class="clear"></div></div>');
	});
</script>
</body>
</html>