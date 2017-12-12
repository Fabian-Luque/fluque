var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var redis = require('redis');
var r = redis.createClient();

r.subscribe('message');

io.on(
    'connection', 
    function(socket) {
        //console.log('usuario conectado');
    }
);



//cuando llegue un mensaje a redis 
r.on('message', function(channel, messageStr){
    var message = JSON.parse(messageStr);
    //console.log(message);
    console.log(message.data);
    io.emit('canal' + 41, message);    
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



redis.subscribe(
    'message', 
    function(err, count) {
        console.log(err);
        console.log(count);
    }
);


io.on('connection', function(socket){
    console.log('a user connected');

});

redis.on(
    'message', 
    function(channel, message) {
        console.log('Message Recieved: ' + message);
        message = JSON.parse(message);
        //redis.publish("message", "page_viewed");
        io.emit("message", message.data);
    }
);

http.listen(
    3000, 
    function() {
        console.log('Listening on Port 3000');
    }
);
*/
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