var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var redis = require('redis');
var r = redis.createClient();
var r2 = redis.createClient();

r.subscribe('message');

io.on(
    'connection', 
    function(socket) {
        //console.log('usuario conectado');
    }
);

//cuando llegue un mensaje a redis 
r.on('message', function(channel, messageStr){
    console.log(channel);
    var message = JSON.parse(messageStr);
    console.log(message);
    io.emit(message.evento +'-canal' + message.data, message);    
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});