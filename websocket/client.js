const io = require('socket.io')();

var socket = io.connect('http://localhost:3000');
socket.on('message', function (data) {
    //Just console the message and user right now.
    console.log(data.message+" " + data.user);
});