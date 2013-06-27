
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('https')
  , path = require('path')
  , fs = require('fs')
  , io = require('socket.io')
  , mongoose = require('mongoose')
  , instagram = require('instagram-node-lib');

var app = express();

// all environments
app.set('port', process.env.PORT || 3003);
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
var schema;
var IGFeed;
db = mongoose.createConnection('db.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){
	schema = mongoose.Schema({
		user: 'string',
		name: 'string',
		id: 'string',
		profile_image: 'string',
		photo: 'string',
		caption: 'string',
		tag: 'string'
	});
	IGFeed = db.model('IGFeed', schema);
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

var server = http.createServer(options, app);
io = io.listen(server);
server.listen(app.get('port'), function(){
	console.log('Express server listening on port ' + app.get('port'));
});

instagram.set('client_id', '0aa6b0bcdff544e0b8f202797b0c117e');
instagram.set('client_secret', 'a610840147e54cb981f53da99a78d975');
instagram.set('callback_url', 'http://instagram.pbapp.net/feed');

io.sockets.on('connection', function(socket){
	var dateObj = new Date();
	socket.emit('newigpost', {'time': dateObj.getTime()});
});

app.get('/hello', function(req, res){
	res.send('Hello World');
});

var auth = express.basicAuth(function(user, pass){
	return user === 'abc' && pass === '777';
});

app.get('/subscribe/tag/:tag', auth, function(req, res){
	instagram.subscriptions.subscribe({ object : 'tag', object_id : req.params.tag });
	res.send(200);
});

app.get('/unsubscribe/:subid', auth, function(req, res){
	instagram.subscriptions.unsubscribe({ id: req.params.subid });
	res.send(200);
});

app.get('/subscription', function(req, res){
	instagram.subscriptions.list();
	res.send(200);
});

app.get('/feed', function(req, res){
	console.log(req.query);
	if(req.query['hub.mode'] == 'subscribe'){
		res.send(req.query['hub.challenge']);
		return;
	}
	res.send(200);
});

app.post('/feed', function(req, res){
	console.log('---------- ig post ----------');
	console.log(req.body);
	//save data to mongodb
	if(!dbReady)
	{
		res.send(200);
		return;
	}
	var entry = new IGFeed({
		'user': 'test_user',
		'name': 'test_name',
		'id': 'test_id',
		'profile_image': 'test_profile_image',
		'photo': 'test_photo',
		'caption': 'test_caption',
		'tag': 'test_tag'
	});
	console.log('saving entry...');
	entry.save(function(err){
		if(err){
			console.log(err);
			res.send(200);
			return;
		}
		console.log('post saved!');
		var dateObj = new Date();
		//tell clients to update data
		io.sockets.emit('newigpost', {'time': dateObj.getTime()});
	});
	res.send(200);
});
