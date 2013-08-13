
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('http')
  , path = require('path')
  , io = require('socket.io');

//connect to mongodb
var dbReady = false;
var mongoose = require('mongoose');
var schema;
var FbEntry;
db = mongoose.createConnection('db.pbapp.net', 'admin', 27017, { user: 'admin', pass: 'mongodbpasswordplaybasis' });
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback(){
    schema = mongoose.Schema({
        id: 'string',
        name: 'string',
        object_id: 'string',
        type: 'string',
        message: 'string',
        created_time: {type: Date, default: Date.now},
    });
    FbEntry = db.model('FbEntry', schema);
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
//	key:  fs.readFileSync('/usr/bin/ssl/pbapp.net.key'),
//	cert: fs.readFileSync('/usr/bin/ssl/pbapp.net.crt'),
//	ca:   fs.readFileSync('/usr/bin/ssl/gd_bundle.crt'),
	requestCert: true,
	rejectUnauthorized: false
};

//var server = http.createServer(options, app);
var server = http.createServer(app);
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

var facebook = new fbsdk.Facebook({
    appId  : '528536277199443',
    secret : '9f5f62191b8d592ed322305c9b202837'
});

function getFacebookPostData(post_id, type){
    facebook._graph('/'+post_id, 'GET', function(data) {
        console.log(data);
        var entry = new FbEntry({
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

function getFacebookCommentData(sender_id, comment_id){
    getFacebookPostData(sender_id+"_"+comment_id, "comment");
}

function getFacebookLikeData(sender_id, parent_id){
    facebook._graph('/'+sender_id, 'GET', function(data) {
        console.log(data);
        var entry = new FbEntry({
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

app.post('/facebook', function(req, res){
    for(x in req.body.entry){
        var entry = req.body.entry[x]
        for(y in entry.changes){
            var change = entry.changes[y];
            var value = change.value;
            var item = value.item;
            var verb = value.verb;
            if(item == 'status' && verb == 'add'){
                getFacebookPostData(value.post_id, item);
            }else if(item == 'post' && verb == 'add'){
                getFacebookPostData(value.post_id, item);
            }else if(item == 'comment' && verb == 'add'){
                getFacebookCommentData(value.sender_id, value.comment_id)
            }else if(item == 'like' && verb == 'add'){
                getFacebookLikeData(value.sender_id,value.parent_id)
            }
        }
    }
});

io.sockets.on('connection', function(socket){
    socket.emit('newtweet', {'time': dateObj.getTime()});
});
