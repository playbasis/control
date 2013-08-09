///<reference path='typescript-node-definitions-master/node.d.ts'/>

var fs = require('fs');

var options = {
	//key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
	//cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
	//ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
	requestCert: true,
	rejectUnauthorized: false
};

var	request = require('request')
//,	app = require('https').createServer(options, handler);
,	app = require('http').createServer(handler);

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

var GOOGLE_PLUS_API_KEY = 'AIzaSyCsLAX-6RLXKPRsLUzz-0Il81-oMpFeWc8';
var TRACKING = '#facebook';
var POLL_FREQ = 60000;
var MAX_NEXT_PAGE_FETCH = 5;
var REQ_QUERY = 'https://www.googleapis.com/plus/v1/activities?query=' + encodeURIComponent(TRACKING) + '&orderBy=recent&key=' + GOOGLE_PLUS_API_KEY
var latestPostId = null;
var latestPostTime = Date.now() - (7*24*60*60*1000);
var oldestPostTime = latestPostTime;
var nextPageFetchCount = 0;
//var engineUrl = 'http://localhost/api/Engine/rule/twitter';
//var engineUrl = 'https://api.pbapp.net/Engine/rule/twitter';

function processActivityData(error, response, body)
{
	if(error)
	{
		console.log(error);
		return;
	}
	if(!body)
	{
		console.log('no response body');
		return;
	}
	var result = JSON.parse(body);
	if(!result)
	{
		console.log('failed to parse response body');
		return;
	}
	var items = result.items;
	var length = items.length;
	var stop = false;
	if(length <= 0)
	{
		console.log('---------- stopping - no data ----------');
		return;
	}
	for (var i = 0; i < length; i++) {
		var item = items[i];
		var pubTime = Date.parse(item.published);
		if(item.id == latestPostId)
		{
			console.log('---------- stopping - hit lastest post ----------');
			stop = true;
			break;
		}
		if(pubTime < oldestPostTime)
		{
			console.log('---------- stopping - no more history ----------');
			stop = true;
			break;
		}
		console.log(item.published +  item.id);
	}
	var latestTime = Date.parse(items[0].published);
	if(latestTime > latestPostTime)
	{
		latestPostTime = latestTime;
		latestPostId = items[0].id;	
	}
	if(stop)
		return;
	if(result.nextPageToken)
	{
		console.log('---------- next page ----------');
		++nextPageFetchCount;
		if(nextPageFetchCount > MAX_NEXT_PAGE_FETCH)
		{
			console.log('---------- stopping - max page fetch ----------');
			return;
		}
		request(REQ_QUERY + '&pageToken=' + result.nextPageToken, processActivityData);
	}
	return;
}

function pollGooglePlusActivities()
{
	console.log('---------- poll result ----------');
	request(REQ_QUERY, processActivityData);
}
setInterval(pollGooglePlusActivities, 15000);
