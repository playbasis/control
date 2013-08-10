
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('http')
  , path = require('path')
  , io = require('socket.io');

var app = express();

// all environments
app.set('port', process.env.PORT || 3006);
app.set('views', __dirname + '/views');
app.set('view engine', 'jade');
app.use(express.favicon());
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

// development only
if ('development' == app.get('env')) {
  app.use(express.errorHandler());
}

app.get('/', routes.index);
app.get('/users', user.list);

var options = {
//	key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
//	cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
//	ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
	requestCert: true,
	rejectUnauthorized: false
};

//var server = http.createServer(options, app);
var server = http.createServer(app);
io = io.listen(server);
server.listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});

io.sockets.on('connection', function(socket){
	console.log('new connection...');
});

app.get('/facebook', function(req, res){
    res.send(req.query['hub.challenge']);
});

app.post('/facebook', function(req, res){
    var fbsdk = require('facebook-sdk');

    var facebook = new fbsdk.Facebook({
        appId  : '528536277199443',
        secret : '9f5f62191b8d592ed322305c9b202837'
    });

    for(x in req.body.entry){
        var entry = req.body.entry[x]
        for(y in entry.changes){
            var change = entry.changes[y];
            var value = change.value;
            var item = value.item;
            var verb = value.verb;
            if(item == 'status' && verb == 'add'){
                facebook._graph('/'+value.post_id, 'POST', function(data) {
                    console.log(data);
                });
            }else if(item == 'post' && verb == 'add'){
                facebook._graph('/'+value.post_id, 'POST', function(data) {
                    console.log(data);
                });
            }else if(item == 'comment' && verb == 'add'){
                facebook._graph('/'+value.post_id, 'POST', function(data) {
                    console.log(data);
                });
            }else if(item == 'like' && verb == 'add'){
                facebook._graph('/'+value.post_id, 'POST', function(data) {
                    console.log(data);
                });
            }
        }
    }
});


