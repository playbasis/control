<!doctype html>
<html>
<head>
<title>Playbasis API</title>
<style type="text/css">
	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }
	body {
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}
	.container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}
	p{
		margin: 1em;
	}
	.deco{
		width:700px;
		margin: 0 auto;
	}
</style>
</head>
<body>
	<div id="fb-root"></div>
	<script>
		
		function login() {
			FB.login(function(response) {
				if (response.authResponse) {
					// connected
					testAPI();
				} else {
					// cancelled
				}
			});
		}

		function testAPI() {
			console.log('Welcome!  Fetching your information.... ');
			FB.api('/me', function(response) {
				console.log('Good to see you, ' + response.name + '.');
			});
		}

		window.fbAsyncInit = function() {
			FB.init({
				appId      : '421530621269210', // App ID
				channelUrl : '//api.pbapp.net/channel.html', // Channel File
				status     : true, // check login status
				cookie     : true, // enable cookies to allow the server to access the session
				xfbml      : true  // parse XFBML
			});

			FB.getLoginStatus(function(response) {
				if (response.status === 'connected') {
					// connected
				} else if (response.status === 'not_authorized') {
					// not_authorized
					login();
				} else {
					// not_logged_in
					login();
				}
			});
		};

		// Load the SDK Asynchronously
		(function(d){
			ar js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/en_US/all.js";
			ref.parentNode.insertBefore(js, ref);
		}(document));

	</script>
	<div class="container deco">
		<h1>This facebook page is powered by Playbasis Gamification Engine.</h1>
		<p>This page is underconstruction. Stay tuned and keep in touch.</p>
		<br/>
	</div>
</body>
</html>