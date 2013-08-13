
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('https')
  , path = require('path')
  , fs = require('fs')
  , io = require('socket.io');

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');

var FbEntry;
var FbKey;
db = mongoose.createConnection('db.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){

    var schemaEntry = mongoose.Schema({
        page_id: 'string',
        id: 'string',
        name: 'string',
        object_id: 'string',
        type: 'string',
        message: 'string',
        created_time: {type: Date, default: Date.now}
    });
    FbEntry = db.model('FbEntry', schemaEntry);

    var schemaKey = mongoose.Schema({
        client_id: 'string',
        site_id: 'string',
        page_id: 'string',
        app_id: 'string',
        secret: 'string',
        app_name: 'string'
    });
    FbKey = db.model('FbAppKey', schemaKey);

    dbReady = true;
    console.log('db connected!');
});
console.log('connecting to db...');

var app = express();

// all environments
app.set('port', process.env.PORT || 3006);
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

var server = http.createServer(options, app);
io = io.listen(server);
server.listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});

io.sockets.on('connection', function(socket){
	console.log('new connection...');
});

app.get('/facebook', function(req, res){
    res.send(req.query['hub.challenge']);
});

var fbsdk = require('facebook-sdk');

function checkFacebookPostId(page_id,post_id){
    if(post_id.toString().indexOf("_") >= 0){
        return post_id;
    }else{
        return page_id+"_"+post_id;
    }
}

function getFacebookPostData(page_id, post_id, type){
    FbKey.findOne({page_id: page_id}, function (err, data) {
        if (data) {
            facebook = new fbsdk.Facebook({
                appId  : data.app_id,
                secret : data.secret
            });
            facebook._graph('/'+post_id, 'GET', function(data) {
                var entry = new FbEntry({
                    page_id: page_id,
                    id: data.from.id,
                    name: data.from.name,
                    object_id: post_id,
                    type: type,
                    message: data.message,
                    created_time: data.created_time,
                });
                console.log('saving entry...');
                entry.save(function(err){
                    if(err){
                        console.log(err);
                        return;
                    }
                    console.log('fb saved!');
                    dateObj = new Date();
                    //tell clients to update data
                    io.sockets.emit('newtweet', {'time': dateObj.getTime()});
                });
            });
        }
    })
}

function getFacebookCommentData(page_id, sender_id, comment_id){
    var commentId = checkFacebookPostId(page_id, comment_id);
    getFacebookPostData(page_id, sender_id+"_"+commentId, "comment");
}

function getFacebookLikeData(page_id, sender_id, parent_id){
    FbKey.findOne({page_id: page_id}, function (err, data) {
        if (data) {
            facebook = new fbsdk.Facebook({
                appId  : data.app_id,
                secret : data.secret
            });
            facebook._graph('/'+sender_id, 'GET', function(data) {
                var entry = new FbEntry({
                    page_id: page_id,
                    id: data.id,
                    name: data.name,
                    object_id: parent_id,
                    type: 'like',
                    message: '',
                });
                console.log('saving entry...');
                entry.save(function(err){
                    if(err){
                        console.log(err);
                        return;
                    }
                    console.log('fb saved!');
                    dateObj = new Date();
                    //tell clients to update data
                    io.sockets.emit('newtweet', {'time': dateObj.getTime()});
                });
            });
        }
    })
}

app.post('/facebook', function(req, res){
    for(x in req.body.entry){
        var entry = req.body.entry[x]
        for(y in entry.changes){
            var change = entry.changes[y];
            var value = change.value;
            var item = value.item;
            var verb = value.verb;
            if(item == 'status' && verb == 'add'){
                var postId = checkFacebookPostId(entry.id, value.post_id);
                getFacebookPostData(entry.id, postId, item);
            }else if(item == 'post' && verb == 'add'){
                var postId = checkFacebookPostId(entry.id, value.post_id);
                getFacebookPostData(entry.id, postId, item);
            }else if(item == 'comment' && verb == 'add'){
                getFacebookCommentData(entry.id, value.sender_id, value.comment_id)
            }else if(item == 'like' && verb == 'add'){
                getFacebookLikeData(entry.id, value.sender_id,value.parent_id)
            }
        }
    }
});

io.sockets.on('connection', function(socket){
    socket.emit('newtweet', {'time': dateObj.getTime()});
});
