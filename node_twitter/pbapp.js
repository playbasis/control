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
,	request = require('request')
,	app = require('https').createServer(options, handler);
//,	app = require('http').createServer(handler);

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

var TRACKING = '#pbapp,#playbasis';
//var engineUrl = 'http://localhost/api/Engine/rule/twitter';
var engineUrl = 'https://api.pbapp.net/Engine/rule/twitter';

twit.stream('statuses/filter', {'track': TRACKING}, function(stream){
	stream.on('data', function(data){

		console.log('---------- tweet tweet ----------');
		//console.log(data.user.name);
		//console.log(data.user.screen_name);
		//console.log(data.user.id_str);
		//console.log(data.user.profile_image_url);
		//console.log(data.text);
		//console.log(data);

		var obj = {
			id_str : data.id_str,
			user: {
				screen_name : data.user.screen_name,
				name : data.user.name,
				id_str : data.user.id_str,
				profile_image_url : data.user.profile_image_url
			},
			text: data.text
		};
		console.log(obj);

		request.post({ url: engineUrl, json: obj }, function (error, response, body) {
    		console.log(body);
  		});
	});
});
