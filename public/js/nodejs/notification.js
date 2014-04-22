$(function() {
    // Handler for .ready() called.
    if (document.URL.indexOf("/start") === false) {
        var socket = io.connect(SOCKET);

        socket.on('connect', function(data) {
            socket.emit('subscribe', {
                channel: 'score.update'
            });
            socket.emit('subscribe', {
                channel: 'user'
            });
        });

        socket.on('score.update', function(data) {
            //Do something with data
            console.log('Score updated: ', data);
        });

        socket.on('user', function(data) {

            console.log('User: ', data);
        });
    }
});