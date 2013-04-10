<!doctype html>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
(function() {
	if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
    
    var redir = encodeURIComponent("api/janrain/welcome");
	var protocal = "http";
	var param = '?redir=' + redir + '&protocal=' + protocal
    //janrain.settings.tokenUrl = 'https://api.pbapp.net/janrain/token' + param;
	janrain.settings.tokenUrl = 'https://dev.pbapp.net/api/janrain/token' + param;
	//janrain.settings.tokenUrl = 'http://localhost/api/janrain/token' + param;

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
      window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainAuthWidget';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/playbasis/engage.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/playbasis/engage.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();

(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
    if (typeof window.janrain.settings.share !== 'object') window.janrain.settings.share = {};
    if (typeof window.janrain.settings.packages !== 'object') janrain.settings.packages = [];
    janrain.settings.packages.push('share');

    /* _______________ can edit below this line _______________ */

    janrain.settings.share.message = "Playbasis <3 Janrain\n";
    janrain.settings.share.title = "Playbasis";
    janrain.settings.share.url = "http://www.playbasis.com";
    janrain.settings.share.description = "gamifying asia";

    /* _______________ can edit above this line _______________ */

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
        document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
        window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainWidgets';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/pbapp/widget.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/pbapp/widget.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();

function janrainWidgetOnload(){
    janrain.events.onProviderLoginToken.addHandler(function(response) {
		console.log("onProviderLoginToken");
		console.log(response);
		$.ajax({
			type: "POST",
			//url: "https://api.pbapp.net/janrain/token/ajax",
			url: "https://dev.pbapp.net/api/janrain/token/ajax",
			//url: "http://localhost/api/janrain/token/ajax",
			data: "token=" + response.token,
			success: function(res) {
				console.log("ajax complete");
				console.log(res);
			}
        });
    });
	console.log("janrainWidgetOnload - events attached");
}

function janrainShareOnload(){
	janrain.events.onShareSendComplete.addHandler(function(response) {
		console.log("onShareSendComplete");
		console.log(response);
	});
	janrain.events.onShareLoginComplete.addHandler(function(response) {
		console.log("onShareLoginComplete");
		console.log(response);
	});
	console.log("janrainShareOnload - events attached");
}
</script>
</head>
<body>
	<?php
	if(isset($user))
		var_dump($user);
	if(isset($provider))
		var_dump($provider);
	?>
	<!--div id="janrainEngageEmbed"></div-->
	<a class="janrainEngage" href="#">Sign-In</a>
	<div id="janrainEngageShare">[Share]</div>
</body>
</html>