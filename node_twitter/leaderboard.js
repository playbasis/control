///<reference path='typescript-node-definitions-master/node.d.ts'/>

var fs = require('fs');

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');
var schema;
var TweetEntry;
db = mongoose.createConnection('db.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){
	schema = mongoose.Schema({
		user: 'string',
		name: 'string',
		id: 'string',
		image: 'string',
		tweet: 'string',
        tag: 'tag'
	});
	TweetEntry = db.model('TweetEntry', schema);
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

var twitter = require('ntwitter')
	, app = require('https').createServer(options, handler)
	, io = require('socket.io').listen(app);

app.listen(8081);

function handler(req, res) {
	fs.readFile(__dirname + '/index.html', function (err, data) {
		if (err) {
			res.writeHead(500);
			return res.end('Error loading index.html');
		}
		var headers = {};
		headers["Access-Control-Allow-Credentials"] = true;
		headers["Access-Control-Allow-Origin"] = req.headers.origin;
		res.writeHead(200, headers);
		//res.writeHead(200);
		res.end(data);
	});
}

var twit = new twitter({
	consumer_key: 'TtqjsKAIuGTs2fqDCTv3rA',
	consumer_secret: '72pEMZYZQIJ0RKlPql9ENWr9emjeBopfkb6nMfwN0',
	access_token_key: '205222126-Fa7P5OFT2k4WJndxfuWq9D1ie3lMXEnugTDphc97',
	access_token_secret: 'yBiEFLNcVXmOEj320lrE92SppPwY4ejA2cQSRv5FLM'
});

var dateObj = new Date();
var TRACKING = '#facebook';
//var TRACKING = '#webwedth,#wwth12,#wwth';

twit.stream('statuses/filter', {'track': TRACKING}, function(stream){
	stream.on('data', function(data){

		console.log('---------- tweet tweet ----------');
		//console.log(data);
		//console.log(data.user.name);
		//console.log(data.user.screen_name);
		//console.log(data.user.id_str);
		//console.log(data.user.profile_image_url);
		//console.log(data.text);

		//save data to mongodb
		if(!dbReady)
			return;
		var entry = new TweetEntry({
			'user': data.user.screen_name,
			'name': data.user.name,
			'id':data.user.id_str,
			'image':data.user.profile_image_url,
			'tweet':data.text,
            'tag': TRACKING
		});
		console.log('saving entry...');
		entry.save(function(err){
			if(err){
				console.log(err);
				return;
			}
			console.log('tweet saved!');
            dateObj = new Date();
            //tell clients to update data
            io.sockets.emit('newtweet', {'time': dateObj.getTime()});
		});
	});
});

io.sockets.on('connection', function(socket){
	socket.emit('newtweet', {'time': dateObj.getTime()});
});
