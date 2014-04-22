var express = require('express'),
    http = require('http'),
    crypto = require('crypto'),
    server = http.createServer(app);

var app = express();
// increase pool size
http.globalAgent.maxSockets = Infinity;

const redis = require('redis');
const io = require('socket.io');
io_serv = io.listen(3000, 'localhost');
//io_serv.disable('heartbeats');

// usernames which are currently connected to the chat
var clients = {};

encryption_key = "5uatH3Q2TqWnHYvJLGZbP8Fm";

// io_serv.set('heartbeat timeout', 10);
// io_serv.set('heartbeat interval', 10);
// io_serv.set("polling duration", 5);
// io_serv.set("close timeout", 60);
io_serv.set('log level', 1); // reduce logging
r = redis.createClient();
io_serv.sockets.on('connection', function(client) {
    redisClient = redis.createClient();
    redisClient.subscribe('notification');

    // when the client emits 'adduser', this listens and executes
    client.on('adduser', function(user, chat, admin) {
        var time = Math.round(new Date().getTime() / 1000);

        var userData = {};
        userData['username'] = user['username'];
        userData['user_id'] = user['user_id'];
        userData['avatar'] = user['avatar'];
        userData['region'] = user['region'];
        userData['admin'] = admin;
        userData['lastpost'] = (time - 5);
        userData['chat'] = chat;
        userData['socket_id'] = client.id;
        // console.log("SOCKET_ID ");
        // console.log(client.id);
        client = merge_options(client, userData);

        var token = user['token'];
        var genToken = crypto.createHmac('SHA256', encryption_key).update("" + client['user_id'] + client['region']).digest('base64');

        // add to global clients Obj
        if (typeof clients[client.chat + client.region] == "undefined") {
            clients[client.chat + client.region] = {};
        }

        clients[client.chat + client.region][client.user_id] = userData;

        if (token != genToken) {
            console.log("token mismatch");
            console.log("t:".token);
            console.log("gt:".genToken);
            client.disconnect();
        } else {
            // join room
            client.join(client.chat + client.region);
            console.log("JOINED " + client.username + " (" + client.id + ")");
            // emit to all -> add user to chat
            client.emit("dirty_fix");
            // get just important chat info
            // if (admin == 1) {
            client.broadcast.to(client.chat + client.region).emit('addUserToChat', client.chat, user, admin);
            //}
            // update for user
            var retClients = getChatuserUpdateData(client.chat + client.region);
            io_serv.sockets.socket(client.id).emit('updateusers', client.chat, retClients);

        }

    });

    // set achat of client
    client.on('setchat', function(data) {
        console.log("\n\r");
        console.log("SET CHAT");
        client.chat = data;
    });

    // when the client emits 'sendchat', this listens and executes
    client.on('sendchat', function(data, whisper_user_id) {
        console.log("\n\r");
        console.log("SEND CHAT: " + client.chat + " " + data + " " + whisper_user_id + " name:" + client.username + " id: " + client.id);
        var time = Math.round(new Date().getTime() / 1000);
        if (client.lastpost + 5 <= time) {
            client.lastpost = time;
            if (whisper_user_id == 0) {
                console.log("normal");
                // normal chat
                io_serv.sockets. in (client.chat + client.region).emit('updatechat', client.chat, client.user_id, client.username, client.avatar, time, data, false);
            } else {
                console.log("whisper");
                // whisper
                var whisper_socket = clients[client.chat + client.region][whisper_user_id]['socket_id'];
                var whisper_client = io_serv.sockets.socket(whisper_socket);

                whisper_client.emit("dirty_fix");
                whisper_client.emit("updatechat", client.chat, client.user_id, client.username, client.avatar, time, data, true);

            }
        } else {
            console.log("OMG too fast");
        }
        console.log("\n\r");
        console.log("END SEND CHAT");
    });


    client.on("refreshUsersInChatForUser", function(chat) {
        console.log("REFRESH USERS");
        console.log("init from ", client.username);

        io_serv.sockets.socket(client.id).emit('dirty_fix');

        // get just important chat info
        var retClients = getChatuserUpdateData(chat + client.region);
        console.log(retClients);
        io_serv.sockets.socket(client.id).emit('updateusers', chat, retClients);
    });

    client.on("switchChat", function() {
        console.log("SWITCH CHAT");

        //leave old
        client.leave(client.chat + client.region);
        try {
            delete clients[client.chat + client.region][client.user_id];
            // send to all -> remove user out of chat
            io_serv.sockets. in (client.chat + client.region).emit('removeUserOfChat', client.chat, client.user_id);
        } catch (e) {
            console.log("already null");
        }
    });

    redisClient.on("message", function(channel, message) {
        console.log("\n\r");
        console.log("MESSAGE");
        //Channel is e.g 'score.update'
        client.emit(channel, message);
    });

    client.on('disconnect', function() {
        console.log("\n\r");
        console.log("DISCONNECT");
        redisClient.quit();
        r.quit();

        // send to all -> remove user out of chat
        io_serv.sockets. in (client.chat + client.region).emit('removeUserOfChat', client.chat, client.user_id);

        // echo globally that this client has left
        client.leave(client.chat + client.region);
        console.log(client.user_id);
        //clients = deleteKey(clients, [client.user_id]);
        try {
            delete clients[client.chat + client.region][client.user_id];
        } catch (e) {
            console.log("already null");
        }
        console.log("DISCONNECT END");
    });

});

function merge_options(obj1, obj2) {
    var obj3 = {};
    for (var attrname in obj1) {
        obj3[attrname] = obj1[attrname];
    }
    for (var attrname in obj2) {
        obj3[attrname] = obj2[attrname];
    }
    return obj3;
}

function getChatuserUpdateData(chatname) {
    console.log(chatname);
    // get just important chat info
    var tmpClients = clients[chatname];
    var retClients = {};
    for (key in tmpClients) {
        var tmpClient = tmpClients[key];
        var tmpObj = {};
        //if (tmpClient['admin'] == 1) {
        tmpObj['username'] = tmpClient['username'];
        tmpObj['user_id'] = tmpClient['user_id'];
        tmpObj['avatar'] = tmpClient['avatar'];
        tmpObj['admin'] = tmpClient['admin'];
        retClients[tmpClient['user_id']] = tmpObj;
        //}

    }
    console.log("retClients:");
    console.log(retClients);

    return retClients;
}