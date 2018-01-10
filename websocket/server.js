var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var redis = require('redis');
var r = redis.createClient();
var r2 = redis.createClient();

r.subscribe('message');
r2.subscribe('chat');

io.on(
    'connection', 
    function(socket) {
        //console.log('usuario conectado');
    }
);

//cuando llegue un mensaje a redis 
r.on('chat', function(channel, messageStr) {
    var message = JSON.parse(messageStr);
    console.log(message.data);
    console.log('chat-canal' + message.data);
    io.emit('chat-canal' + message.data, message);    
});

//cuando llegue un mensaje a redis 
r.on('message', function(channel, messageStr){
    console.log(channel);
    var message = JSON.parse(messageStr);
    console.log(message.data);
    console.log('canal' + message.data);
    io.emit('canal' + message.data, message);    
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});