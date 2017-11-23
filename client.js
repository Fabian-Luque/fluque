

var io = require('socket.io-client');
var socket = io('http://localhost:3000');

var redis = require("redis"),
//  http = require("http"),
 // socketio = require("socket.io"),
  client = redis.createClient();

var canal = "message";

client.on(
	"message", 
	function(canal, message) {
		console.log(message);
  		
	}
);


socket.on('connection', function(data){
console.log(data);
});

// Watch redis
client.subscribe(canal);
console.log("Watching channel: " + canal);


socket.once('connection', function(data){
console.log(data);
});
socket.once('event', function(data){
    console.log('Got event');
    console.log(data);
});
socket.once('message', function(data){
    console.log('Got foobar');
    console.log(data);
});
socket.once('disconnect', function(){
    console.log('DISCONNECTED')
});

io('http://localhost:3000');


