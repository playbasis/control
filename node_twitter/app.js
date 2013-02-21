///<reference path='typescript-node-definitions-master/node.d.ts'/>

var fs = require('fs');

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

app.listen(8080);

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

var twit = new twitter({
	consumer_key: 'zPUFYp3SERsStcB6lsLjg',
	consumer_secret: 'QUtQz6ID4BMj5tQZwXHueDOqhByngSQ16Jmm4BprRs',
	access_token_key: '19943348-YSYM1cQI8EzUvf6Oepx78LBInnrhENpsQYSTrQCNu',
	access_token_secret: '66Tmt1uAObidzLUS86rwjnCrkT4n0l9w5XcklizOQ'
});

var userIndex = [];
var rank = [];
var tracking = '#facebook';
var tweetCount = 0;

function rankSort(a, b){
	return b.score - a.score;
}

twit.stream('statuses/filter', {'track': tracking}, function(stream){
	stream.on('data', function(data){

		console.log('---------- tweet tweet ----------');
		console.log(data.user.name);
		console.log(data.user.id_str);
		console.log(data.user.profile_image_url);
		console.log(data.text);

		if (userIndex[data.user.id_str] == undefined) {
			userIndex[data.user.id_str] = rank.length;
			rank.push({'user': data.user.name, 'id':data.user.id_str, 'image':data.user.profile_image_url, 'score':0});
			console.log('+++++ new player! +++++');
			io.sockets.emit('totalplayers', {'count':rank.length});
		}
		rank[userIndex[data.user.id_str]].score += 1;
		rank.sort(rankSort);
		tweetCount++;

		console.log('total tweets: ' + tweetCount);
		io.sockets.emit('rank', rank);
		io.sockets.emit('totaltweets', {'count':tweetCount});
	});
});
