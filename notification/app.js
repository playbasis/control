
var REDIS_SERVER_PORT = 6379;
var REDIS_SERVER_ADDRESS = '127.0.0.1';
var METHOD_PUBLISH_FEED = '/activitystream';
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , https = require('http')
  , path = require('path')
  , io = require('socket.io')
  , redis = require('redis')
  , fs = require('fs');

//special parser for the activity feed
function feedParser(req, res, next){
  if(req.originalUrl.substr(0, METHOD_PUBLISH_FEED.length).toLowerCase() != METHOD_PUBLISH_FEED)
    return next();
  var data = '';
  req.setEncoding('utf8');
  req.on('data', function(chunk){
    data += chunk;
  });
  req.on('end', function(){
    req.body = data;
    next();
  })
}

var app = express();

app.configure(function(){
  app.set('port', process.env.PORT || 3000);
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.favicon());
  app.use(express.logger('dev'));
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(feedParser);
  app.use(app.router);
  app.use(express.static(path.join(__dirname, 'public')));
});

app.configure('development', function(){
  app.use(express.errorHandler());
});

app.get('/', routes.index);
app.get('/users', user.list);

// https.createServer(app).listen(app.get('port'), function(){
//   console.log("Express server listening on port " + app.get('port'));
// });
var options = '';

var server = https.createServer(app);
io = io.listen(server);
server.listen(app.get('port'), function() {
  console.log("Express server listening on port: " + app.get('port'));
});


var redisSubClients = Object();
var redisPubClient = redis.createClient();


redisPubClient.on('error', function(err) {
  console.log('redis pub-Client error: ' + err);
});

io.sockets.on('connection', function(socket) {

  socket.on('subscribe', function(data) { 
    console.log('Connected ' + data.channel);

    var channel = data.channel;

    if (!channel)
      return;

    if(!redisSubClients[channel]) {
      
      createRedisSubClient(channel);

      redisSubClients[channel].subscribe(channel);

      redisSubClients[channel].on('message', function(data) {
        io.sockets.in(channel).emit('message', channel);
      });

    };

    socket.join(channel);

  });

  socket.on('disconnect', function () {
    console.log('user disconnected');
  });
})


//create redis sub-client for the specified channel
function createRedisSubClient(channel){

  //assert(!redisSubClients[channel]);
  redisSubClients[channel] = redis.createClient(REDIS_SERVER_PORT, REDIS_SERVER_ADDRESS);

  redisSubClients[channel].on('error', function(err){
    console.log('redis sub-client err: ' + err);
  });
}





