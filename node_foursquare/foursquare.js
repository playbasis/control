
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  //, http = require('http')
  , http = require('https')
  , path = require('path')
  , fs = require('fs')
  , mongoose = require('mongoose')
  , io = require('socket.io');

var app = express();

// all environments
app.set('port', process.env.PORT || 3007);
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

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');
var FsqFeed;
db = mongoose.createConnection('db.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){
	
	var schema = mongoose.Schema({
		user: 'string',
		checkin: 'string',
        created_time: {type: Date, default: Date.now}
	});
	FsqFeed = db.model('FsqFeed', schema);

	dbReady = true;
	console.log('db connected!');
});
console.log('connecting to db...');

var options = {
	key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
	cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
	ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
	requestCert: true,
	rejectUnauthorized: false
};

//var server = http.createServer(app);
var server = http.createServer(options, app);
io = io.listen(server);
server.listen(app.get('port'), function(){
	console.log('Express server listening on port ' + app.get('port'));
});

io.sockets.on('connection', function(socket){
	var dateObj = new Date();
	socket.emit('fsqcheckin', {'time': dateObj.getTime()});
});

var BASE_URL = 'https://foursquare.pbapp.net';

var config =
{
	'secrets' :
	{
		'clientId' : 'UGPSGBGD20UCCN2QFHK14YXRVB0Q3IEVLECC30OY4ASDLTGV',
		'clientSecret' : 'IG0KHUMO12AVMKR2QB2Z0RNQI0WUWG54GLO4VUGSSQFOQLCJ',
		'redirectUrl' : BASE_URL + '/login/callback'
	}
}
var foursquare = require('node-foursquare')(config);

app.get('/hello', function (req, res)
{
	res.send('Hello, Foursquare');
});

app.get('/user/login', function (req, res)
{
	res.writeHead(303, { 'location': foursquare.getAuthClientRedirectUrl() });
	res.end();
});

app.get('/login/callback', function (req, res)
{
	foursquare.getAccessToken({	code: req.query.code }, function (error, accessToken)
	{
		if(error) {
			res.send('An error was thrown: ' + error.message);
		}
		else {
			// Save the accessToken and redirect.
			foursquare.Users.getUser('self', accessToken, function(error, results){
				console.log('-------- getUser --------');
				console.log(results.user);
				res.writeHead(303, { 'location': BASE_URL + '/hello' });
				res.end();
			});
		}
	});
});

app.post('/feed/callback', function (req, res)
{
	console.log('---------- checkin ----------');
	console.log(req.body);
	var entry = new FsqFeed({
		'user' : req.body.user,
		'checkin' : req.body.checkin
	});
	console.log('saving checkin...');
	entry.save(function(err){
		if(err){
			console.log(err);
			return;
		}
		console.log('checkin saved!');
		var dateObj = new Date();
		io.sockets.emit('fsqcheckin', {'time': dateObj.getTime()});
	});
	res.send(200);
});