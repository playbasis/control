<!doctype html>
<html>
<head>
<script type="text/javascript">
(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
    
    //janrain.settings.tokenUrl = 'https://api.pbapp.net/janrain/token';
	janrain.settings.tokenUrl = 'https://dev.pbapp.net/api/janrain/token';
	//janrain.settings.tokenUrl = 'http://localhost/api/janrain/token';

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
      e.src = 'https://rpxnow.com/js/lib/pbapp/engage.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/pbapp/engage.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();
</script>
<script type="text/javascript">
(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
    if (typeof window.janrain.settings.share !== 'object') window.janrain.settings.share = {};
    if (typeof window.janrain.settings.packages !== 'object') janrain.settings.packages = [];
    janrain.settings.packages.push('share');

    /* _______________ can edit below this line _______________ */

    janrain.settings.share.message = "Playbasis <3 Janrain\n";
    janrain.settings.share.title = "Playbasis";
    janrain.settings.share.url = "www.playbasis.com";
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
</script>
</head>
<body>
	<a class="janrainEngage" href="#">Sign-In</a>
	<!--div id="janrainEngageEmbed"></div-->
	<div id="janrainEngageShare">[Share]</div>
</body>
</html>