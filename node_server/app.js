
var REDIS_SERVER_PORT = 6379;
var REDIS_SERVER_ADDRESS = '127.0.0.1';//'46.137.248.96';
var METHOD_PUBLISH_FEED = '/activitystream';
/**
 * Module dependencies.
 */

var express = require('express')
	, routes = require('./routes')
	, user = require('./routes/user')
	, https = require('https')
	, path = require('path')
	, io = require('socket.io')
	, redis = require('redis')
	, fs = require('fs');

var options = {
	key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
	cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
	ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
	requestCert: true,
	rejectUnauthorized: false
};

//special parser for the activity feed
function feedParser(req, res, next){
	if(req.originalUrl.substr(0, METHOD_PUBLISH_FEED.length).toLowerCase() != METHOD_PUBLISH_FEED)
		return next();
	var data = '';
	req.setEncoding('utf8');
	req.on('data', function(chunk){
		data += chunk;
	});
	req.on('end', function(){
		req.body = data;
		next();
	})
}

function allowCrossDomain(req, res, next) {
//    res.header('Access-Control-Allow-Origin', '*');
//    res.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
//    res.header('Access-Control-Allow-Headers', 'Content-Type');
    res.setHeader("Access-Control-Allow-Origin", "*");       // Need to add the correct domain in here!!
    res.setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE');
//    res.setHeader("Access-Control-Max-Age", "300");          // Cache response for 5 minutes
    res.setHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");         // Ensure this header is also allowed!

    console.log("Now use allow");

    next();
}

var app = express();

app.configure(function(){
//	app.set('port', process.env.PORT || 3000);
    app.set('host', 'node.pbapp.net');
    app.set('port', process.env.PORT || 80);
	app.set('views', __dirname + '/views');
	app.set('view engine', 'jade');
	app.use(express.favicon());
	app.use(express.logger('dev'));
	app.use(express.bodyParser());
	app.use(express.methodOverride());
    app.use(allowCrossDomain);
	app.use(feedParser);
	app.use(app.router);
	app.use(express.static(path.join(__dirname, 'public')));
});

app.configure('development', function(){
	app.use(express.errorHandler());
});

app.get('/', routes.index);
app.get('/users', user.list);

var server = https.createServer(options, app);
io = io.listen(server, {origins: '*'});
server.listen(app.get('port'), function(){
	console.log("Express server listening on port " + app.get('port'));
});

var redisSubClients = Object(); //an object holding redis clients that subscribed to a channel
var redisPubClient = redis.createClient(); //redis client for publishing feeds

redisPubClient.on('error', function(err){
	console.log('redis pub-client err: ' + err);
});

//create redis sub-client for the specified channel
function createRedisSubClient(channel){

	//assert(!redisSubClients[channel]);
	redisSubClients[channel] = redis.createClient(REDIS_SERVER_PORT, REDIS_SERVER_ADDRESS);

	redisSubClients[channel].on('error', function(err){
		console.log('redis sub-client err: ' + err);
	});
}

io.sockets.on('connection', function(socket){

	socket.on('subscribe', function(data){

		if(!data.channel)
			return;
			
		if(!redisSubClients[data.channel]){
			createRedisSubClient(data.channel);
			//assert(redisSubClients[data.channel]);
			redisSubClients[data.channel].subscribe(data.channel);

			//set callback to emit message to connected clients
			redisSubClients[data.channel].on('message', function(channel, message){
				io.sockets.in(channel).emit('message', message);
			});
		}
		socket.join(data.channel);
	});
});

var auth = express.basicAuth(function(user, pass){
	return user === 'planes' && pass === 'capetorment852456';
});

//publish event through post request
app.post(METHOD_PUBLISH_FEED + '/:channel', auth, function(req, res){
	if(req.body)
		redisPubClient.publish(req.params.channel, req.body);
	res.send(200);
});
