
var REDIS_SERVER_PORT = 6379;
var REDIS_SERVER_ADDRESS = '127.0.0.1';//'46.137.248.96';
var METHOD_PUBLISH_FEED = '/activitystream';
var CHANNEL_PREFIX = 'as_';

/**
 * Module dependencies.
 */

var express = require('express')
	, routes = require('./routes')
	, user = require('./routes/user')
    , http = require('http')
	, https = require('https')
	, path = require('path')
	, io = require('socket.io')
	, redis = require('redis')
	//, mysql = require('mysql')
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

var app = express();

app.configure(function(){
	app.set('port', process.env.PORT || 3000);
	app.set('views', __dirname + '/views');
	app.set('view engine', 'jade');
	app.use(express.favicon());
	app.use(express.logger('dev'));
	app.use(express.bodyParser());
	app.use(express.methodOverride());
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
//var server = http.createServer(app);
io = io.listen(server);
server.listen(app.get('port'), function(){
	console.log("Express server listening on port " + app.get('port'));
});

/*var sqlcon = mysql.createConnection({
  host     : 'db.pbapp.net',
  user     : 'playbasis_admin',
  password : 'databaseplaybasisproduction',
  database : 'core'
});
sqlcon.connect();*/

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');

var ClientSite;
db = mongoose.createConnection('dbv2.pbapp.net', 'core', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis', auth: { authSource: "admin" } });
//db = mongoose.createConnection('localhost', 'core', 27017);
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){

    var schemaKey = mongoose.Schema({
        api_key: String,
        api_secret: String,
        client_id: mongoose.Schema.Types.ObjectId,
        date_added: Date,
        date_expire: Date,
        date_modified: Date,
        date_start: Date,
        deleted: Boolean,
        domain_name: String,
        image: String,
        last_send_limit_users: Date,
        limit_users: Number,
        site_name: String,
        status: Boolean
    });
    ClientSite = db.model('playbasis_client_site', schemaKey, 'playbasis_client_site');

    dbReady = true;
    console.log('db connected!');
});
console.log('connecting to db...');

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

function verifyChannel(channel, callback)
{
	if(!channel)
	{
		callback('channel is not invalid', channel);
		return;
	}
	if(channel.slice(0, 'http'.length) == 'http')
	{
		callback('channel cannot begins with [http]', channel);
		return;
	}
	if(channel.slice(0, 'www'.length) == 'www')
	{
		callback('channel cannot begins with [www]', channel);
		return;
	}
	/*var sql = 'SELECT domain_name FROM playbasis_client_site WHERE domain_name = ' + sqlcon.escape(channel) + ' OR domain_name = ' + sqlcon.escape('www.' + channel);
	sqlcon.query(sql, function(err, rows){
		if(err){
			console.log(err);
			callback(err);
			return;
		}
		console.log(rows);
		if(rows.length < 1)
		{
			callback('channel does not exist', channel);
			return;
		}
		console.log('domain valid: ' + rows[0].domain_name);
		callback(null, channel)
	});*/


    ClientSite.findOne({$or : [{domain_name: channel}, {domain_name: 'www.' + channel}]}, function (err, data) {
        if(err){
            console.log(err);
            callback(err);
            return;
        }
        console.log(channel);
        console.log(data);
        if(data && data.domain_name){
            console.log('domain valid: ' + data.domain_name);
            callback(null, channel);
        }else{
            callback('channel does not exist', channel);
            return;
        }
    });
}

io.sockets.on('connection', function(socket){
	socket.on('subscribe', function(data){
		if(!data || !data.channel)
			return;
		verifyChannel(data.channel, function(err, channel){
			if(err){
				console.log(err);
				return;
			}
			host = CHANNEL_PREFIX + channel;
			if(!redisSubClients[host]){
				createRedisSubClient(host);
				redisSubClients[host].subscribe(host);
				redisSubClients[host].on('message', function(channel, message){
					io.sockets.in(channel).emit('message', message);
				});
			}
			socket.join(host);
			console.log('new client subscribed at: ' + channel);
		});
	});
});

var auth = express.basicAuth(function(user, pass){
	return user === 'planes' && pass === 'capetorment852456';
});

//publish event through post request
app.post(METHOD_PUBLISH_FEED + '/:channel', auth, function(req, res){
	if(req.body)
		redisPubClient.publish(CHANNEL_PREFIX + req.params.channel, req.body);
	res.send(200);
});
