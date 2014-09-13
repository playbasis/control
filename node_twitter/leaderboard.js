require('newrelic');
///<reference path='typescript-node-definitions-master/node.d.ts'/>

var fs = require('fs');

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');

var TweetEntry;
var FbLeader;

var TRACKING = '';

var express = require('express');

var twitter = require('ntwitter')
    , routes = require('./routes')
    , user = require('./routes/user')
    , path = require('path')
    , fs = require('fs')
    , io = require('socket.io');

var app = express();

// all environments
app.set('port', process.env.PORT || 8081);
app.set('views', __dirname + '/views');
app.set('view engine', 'jade');
app.use(express.favicon());
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

app.get('/', routes.index);
app.get('/users', user.list);

var options = {
    key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
    cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
    ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
    requestCert: true,
    rejectUnauthorized: false
};

var server = require('https').createServer(options, app);
//var server = require('http').createServer(app);
io = io.listen(server);

server.listen(app.get('port'), function(){
    console.log('Express server listening on port ' + app.get('port'));
});

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
//var TRACKING = '#acnwave,#acnwaves,#hyatt';
//var TRACKING = '#webwedth,#wwth12,#wwth';

function stringObj(s){
    var o = new Array();
    if(s.entities){
        if(s.entities.hashtags){
            var t = s.entities.hashtags;
            console.log(t);
            for (var key in t){
                if(t[key] && t[key]['text']){
                    o.push(t[key]['text']);
                }
            }
        }
    }
    if(o){
        o.join();
    }
    console.log(o);
    return o;
};

var auth = express.basicAuth(function(user, pass){
    return user === 'abc' && pass === '777';
});

function getTweetFeed(){
    console.log('connecting to db...');
    db = mongoose.createConnection('dbv2.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
    db.on('error', console.error.bind(console, 'connection error:'));
    db.once('open', function callback(){
        var schema = mongoose.Schema({
            user: 'string',
            name: 'string',
            id: 'string',
            image: 'string',
            tweet_id: 'string',
            tweet: 'string',
            tag: { type: 'string', index: true },
            retweet: 'boolean',
            created_time: {type: Date, default: Date.now}
        });
        TweetEntry = db.model('TweetEntry', schema);

        var schemaKey = mongoose.Schema({
            bg: 'string',
            bg526: 'string',
            bg980: 'string',
            date_added: {type: Date, default: Date.now},
            date_modified: {type: Date, default: Date.now},
            fb_id: { type: 'string', unique: true },
            htag: { type: 'string', index: true },
            r1box: 'string',
            r1boxs: 'string',
            r2box: 'string',
            r2boxs: 'string',
            r3box: 'string',
            r3boxs: 'string',
            sbox: 'string',
            sboxs: 'string',
            sfc: 'string'
        });
        FbLeader = db.model('FbLeader', schemaKey);

        dbReady = true;
        console.log('db connected!');

        FbLeader.find({}, function (err, data) {

            if (data) {
                for (d in data) {
                    TRACKING = TRACKING+"#"+data[d].htag;
                    if((data.length-1) > d){
                        TRACKING = TRACKING+",";
                    }
                }

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
                        if(data.retweeted_status){
                            saveTweet(data.retweeted_status, true);
                            saveTweet(data, true);
                        }else{
                            saveTweet(data, data.retweeted);
                        }
                    });
                });
            }
        });
    });
}

app.get('/restart', auth, function(req, res)
{
    twit.stream.destroy;
    getTweetFeed();
    res.send(200);
});

function saveTweet(data, retweet){
    if(data.hasOwnProperty('user') && data.user.hasOwnProperty('screen_name') && data.user.hasOwnProperty('name') && data.user.hasOwnProperty('id_str')
        && data.user.hasOwnProperty('profile_image_url') && data.hasOwnProperty('id_str')){
        var entry = new TweetEntry({
            'user': data.user.screen_name,
            'name': data.user.name,
            'id':data.user.id_str,
            'image':data.user.profile_image_url,
            'tweet_id': data.id_str,
            'tweet':data.text,
            'tag': stringObj(data),
            'retweet':retweet
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
    }
};

io.sockets.on('connection', function(socket){
	socket.emit('newtweet', {'time': dateObj.getTime()});
});
