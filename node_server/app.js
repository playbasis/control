
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('http')
  , path = require('path')
  , io = require('socket.io')
  , redis = require('redis');

var app = express();

app.configure(function(){
  app.set('port', process.env.PORT || 3000);
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.favicon());
  app.use(express.logger('dev'));
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
  app.use(express.static(path.join(__dirname, 'public')));
});

app.configure('development', function(){
  app.use(express.errorHandler());
});

app.get('/', routes.index);
app.get('/users', user.list);

var server = http.createServer(app);
io = io.listen(server);
server.listen(app.get('port'), function(){
  console.log("Express server listening on port " + app.get('port'));
});

var redisSubClients = Object(); //an object holding redis clients that subscribed to a channel
var redisPubClient = redis.createClient(); //redis client for publishing feeds

redisPubClient.on('error', function(err){
  console.log('redis pub-client err: ' + err);
});

io.sockets.on('connection', function(socket){

  socket.on('subscribe', function(data){

    if(!data.channel)
      return;
      
    if(!redisSubClients[data.channel]){
      createRedisSubClient(data.channel);
      assert(redisSubClients[data.channel]);
      redisSubClients[data.channel].subscribe(data.channel);

      //set callback to emit message to connected clients
      redisSubClients[data.channel].on('message', function(channel, message){
        io.sockets.in(channel).emit('message', message);
      });
    }
    socket.join(data.channel);
  });
});

//publish event through get request
app.get('/publish', function(req, res){
  redisPubClient.publish(req.params.channel, req.params.msg);
  res.send(200, 'event published');
});

//create redis sub-client for the specified channel
function createRedisSubClient(channel){

  assert(!redisSubClients[channel]);
  redisSubClients[channel] = redis.createClient();

  redisSubClients[channel].on('error', function(err){
    console.log('redis sub-client err: ' + err);
  });
}
