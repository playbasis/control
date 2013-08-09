///<reference path='typescript-node-definitions-master/node.d.ts'/>

var fs = require('fs');

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');
var schema;
var GPEntry;
var GPMeta;
var metas = {};
db = mongoose.createConnection('db.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){
	schema = mongoose.Schema({
		user_id: String,
		display_name: String,
		profile_image: String,
		verb: String,
		type: String,
		title: String,
		published: Date,
		content: String,
		content_url : String,
		attachments : String,
		tag: String
	});
	GPEntry = db.model('GPEntry', schema);

	var metaSchema = mongoose.Schema({
		search_query: String,
		last_updated: Date
	});
	GPMeta = db.model('GPMeta', metaSchema);

	dbReady = true;
	console.log('db connected!');
});
console.log('connecting to db...');

var options = {
	//key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
	//cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
	//ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
	requestCert: true,
	rejectUnauthorized: false
};

var	request = require('request')
//,	app = require('https').createServer(options, handler);
,	app = require('http').createServer(handler)
,	io = require('socket.io').listen(app);

app.listen(3005);

function handler(req, res) {
	fs.readFile(__dirname + '/index.html', function (err, data) {
		if (err) {
			res.writeHead(500);
			return res.end('Error loading index.html');
		}
		res.writeHead(200);
		res.end(data);
	});
}

var dateObj = new Date();
var GOOGLE_PLUS_API_KEY = 'AIzaSyCsLAX-6RLXKPRsLUzz-0Il81-oMpFeWc8';
var TRACKING = '#facebook';
var POLL_FREQ = 60000;
var MAX_NEXT_PAGE_FETCH = 5;
var REQ_QUERY = 'https://www.googleapis.com/plus/v1/activities?query=' + encodeURIComponent(TRACKING) + '&orderBy=recent&key=' + GOOGLE_PLUS_API_KEY
var latestPostId = null;
var latestPostTime = Date.now() - (7*24*60*60*1000);
var oldestPostTime = latestPostTime;
var nextPageFetchCount = 0;

function processActivityData(error, response, body)
{
	if(error) {
		console.log(error);
		return;
	}
	if(!body) {
		console.log('no response body');
		return;
	}
	var result = JSON.parse(body);
	if(!result) {
		console.log('failed to parse response body');
		return;
	}
	var items = result.items;
	var length = items.length;
	var stop = false;
	if(length <= 0) {
		console.log('---------- stopping - no data ----------');
		return;
	}
	for (var i = 0; i < length; i++) {
		var item = items[i];
		var pubTime = Date.parse(item.published);
		if(item.id == latestPostId)	{
			console.log('---------- stopping - hit lastest post ----------');
			stop = true;
			break;
		}
		if(pubTime < oldestPostTime) {
			console.log('---------- stopping - no more history ----------');
			stop = true;
			break;
		}
		console.log(item.published +  item.id);
		var entry = new GPEntry({
			user_id: item.actor.id,
			display_name: item.actor.displayName,
			profile_image: item.actor.image.url,
			verb: item.verb,
			type: item.object.objectType,
			title: item.title,
			published: new Date(pubTime),
			content: item.object.content,
			content_url : item.object.url,
			tag: TRACKING
		});
		if(item.object.attachments){
			entry.attachments = JSON.stringify(item.object.attachments);
		}
		console.log('saving entry...');
		entry.save(function(err) {
			if(err) {
				console.log(err);
				return;
			}
			console.log('entry saved!');
			dateObj = new Date();
            //tell clients to update data
            io.sockets.emit('activity', {'time': dateObj.getTime()});
		});
	}
	var latestTime = Date.parse(items[0].published);
	if(latestTime > latestPostTime) {
		latestPostTime = latestTime;
		latestPostId = items[0].id;	
	}
	if(stop)
		return;
	if(result.nextPageToken) {
		console.log('---------- next page ----------');
		++nextPageFetchCount;
		if(nextPageFetchCount > MAX_NEXT_PAGE_FETCH) {
			console.log('---------- stopping - max page fetch ----------');
			return;
		}
		request(REQ_QUERY + '&pageToken=' + result.nextPageToken, processActivityData);
	}
	return;
}

function pollGooglePlusActivities()
{
	if(!dbReady)
		return;
	console.log('---------- poll result ----------');
	request(REQ_QUERY, processActivityData);
}
setInterval(pollGooglePlusActivities, 15000);

io.sockets.on('connection', function(socket){
	socket.emit('activity', {'time': dateObj.getTime()});
});
