</div>
</div>
<footer class="footer">
    <p>
        <span style="text-align:left;float:left"></span>
        <span style="text-align:right;float:right"><?php echo $this->lang->line('text_footer'); ?></span>
    </p>

</footer>
</body></html>

<div class="modal hide fade" id="myModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Settings</h3>
    </div>
    <div class="modal-body">
        <p>Here settings can be configured...</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <a href="#" class="btn btn-primary">Save changes</a>
    </div>
</div>

<script type="text/javascript" src="./notification/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script>
<script type="text/javascript">
    var urlHost = 'https://node.pbapp.net';
    var chHost = '<?php echo  preg_replace('/(http[s]?:\/\/)?([w]{3}\.)?/', '', ($domain)? $domain["domain_name"] : ''); ?>';
    // var chHost = 'playbasis.com';

    var socket = io.connect(urlHost);
    socket.on('connect', function(data){
        // console.log('client connected');
        socket.emit('subscribe', {channel: chHost});
    });


    socket.on('message', function(data){
        data = JSON.parse(data);
        var url = data.actor.image.url;
        data.actor.image.url = url.replace(/(\\)/g, '');
        var icons = '';

        var notification = '<section class="noti-stream-item" style="">';

        var d = new Date();
        var tz = d.getTimezoneOffset()*60;

        notification += '<img class="noti-stream-item-portrait" alt="avatar" src=" ' + data.actor.image.url + '" width="45" height="45" onerror="<?php echo base_url();?>image/default-image.png" /><div class="noti-stream-item-name"><h4> ' + data.actor.displayName + ' </h4><span class="noti-action-act"> ' + data.object.message + ' </span><div class="noti-stream-item-time-stamp" title="'+(Date.parse(data.published).getTime()-(tz*1000))+'"> just now </div></div>';

        if (data.object.badge) {
            notification += '<img class="noti-stream-item-badge" width="35" height="35" alt="badge" src="' + data.object.badge.image.url + '">';
        } else {

            switch (data.verb) {
                case 'visit':
                    icons = 'fa-icon-map-marker';
                    break;
                case 'read':
                    icons = 'fa-icon-bookmark-empty';
                    break;
                case 'like':
                    icons = 'fa-icon-thumbs-up';
                    break;
                case 'share':
                    icons = 'fa-icon-share';
                    break;
                case 'want':
                    icons = 'fa-icon-star';
                    break;
                case 'love':
                    icons = 'fa-icon-heart';
                    break;
                case 'review':
                    icons = 'fa-icon-flag';
                    break;
                case 'spotreview':
                    icons = 'fa-icon-globe';
                    break;
                case 'comment':
                    icons = 'fa-icon-comment';
                    break;
                case 'following':
                    icons = 'fa-icon-plus-sign';
                    break;
                case 'follower':
                    icons = 'fa-icon-group';
                    break;
                case 'pernah':
                    icons = 'fa-icon-cogs';
                    break;
                case 'timeonsite':
                    icons = 'fa-icon-cogs';
                    break;
                case 'login':
                    icons = 'fa-icon-signin';
                    break;
                case 'logout':
                    icons = 'fa-icon-signout';
                    break;

            }

            notification += '<div class="noti-stream-item-badge ' + icons + '"></div>';
        }



        console.log(notification);

        $('#noti-stream').prepend(notification);

    });
</script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-39586205-1', 'pbapp.net');
    ga('send', 'pageview');
</script>

<script type="text/javascript">

$(function(){
    $('.tooltips').tooltip();    
});

</script>