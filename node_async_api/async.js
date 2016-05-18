require('newrelic');
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
  ,	request = require('request')
  , redis = require('redis')
  , mysql = require('mysql')
  , fs = require('fs');

var app = express();

// all environments
app.set('port', process.env.PORT || 3002);
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
    key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
    cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
    ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
    requestCert: true,
    rejectUnauthorized: false
};

var server = https.createServer(options, app);
io = io.listen(server);
server.listen(app.get('port'), function(){
	console.log('Express server listening on port ' + app.get('port'));
});

io.set('resource', '/socket.io');

var REDIS_SERVER_PORT = 6379;
var REDIS_SERVER_ADDRESS = '127.0.0.1';//'46.137.248.96';
var BASE_URL = 'https://api.pbapp.net/';
var CHANNEL_PREFIX = 'res_';

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
function createRedisSubClient(channel)
{
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

    ClientSite.findOne({$or : [{site_name: channel}, {site_name: 'www.' + channel}]}, function (err, data) {
        if(err){
            console.log(err);
            callback(err);
            return;
        }
        console.log(data);
        if(data && data.site_name){
            console.log('domain valid: ' + data.site_name);
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

function postRequest(reqURL, data, channel)
{
    process.env['NODE_TLS_REJECT_UNAUTHORIZED'] = '0';
	request.post({ url: reqURL, form: data }, function(error, response, body){
		console.log('result:');
		console.log(JSON.parse(body));
		if(channel)
			redisPubClient.publish(CHANNEL_PREFIX + channel, body);
	});
}

app.post('/call', function(req, res)
{
	console.log(req.body);
	if(!req.body.endpoint)
	{
		res.send('endpoint is not defined', 200);
		return;
	}
	var reqURL = BASE_URL + req.body.endpoint;
	console.log('request to: ' + reqURL);
	console.log('data:');
	console.log(req.body.data);
	console.log('channel: ' + req.body.channel);
	postRequest(reqURL, req.body.data, req.body.channel);
	res.send(200);
});

app.get('/channel/verify/:channel', function(req, res){
	var chan = req.params.channel;
	console.log('verifying channel: ' + chan);
	verifyChannel(chan, function(err, channel){
		if(err){
			console.log(err);
			res.send(false, 200);
			return;
		}
		console.log('channel is valid: ' + channel);
		res.send(true, 200);
	});
});