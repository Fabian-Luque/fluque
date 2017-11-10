var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

//creación de cliente de redis
var redis = require('redis');
var r = redis.createClient();

//suscripción a canal de redis
r.subscribe('message');

//evento de conección de cliente de sockets
io.on('connection', function(socket){
  console.log('usuario conectado');
});

//cuando llegue un mensaje a redis 
r.on('message', function(channel, messageStr){
    var message = JSON.parse(messageStr);
	console.log(message);
	io.emit('message', message);    
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});

/*

var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

app.get('/', function(req, res){
    res.send('Hello world');
});

io.on('connection', function(socket){
    console.log('a user connected');


    setInterval(function(){
        console.log('Emit');
        socket.emit('message', {some: 'data', here: 'bar'});
    }, 5000);
});

http.listen(3000, function(){
    console.log('listening on *:3010');
});

*/









/*
var redis = require("redis"),
  http = require("http"),
  socketio = require("socket.io"),
  client = redis.createClient();


var io = socketio.listen(3000, "127.0.0.1")

// Redis channel to listen on
var channel = "message";
if(!channel) {
  console.log("Usage: node app.js [redis channel]");
  process.exit(1);
}


io.on('connection', function(socket){
    console.log('a user connected');

});

// For debugging only
io.sockets.on("connection", function(socket) {
  console.log("New client!");
});

client.on(
	"message", 
	function(channel, message) {
		console.log(message);
  		io.sockets.emit('message', message.toString());
	}
);

// Watch redis
client.subscribe(channel);
console.log("Watching channel: " + channel);


*/
