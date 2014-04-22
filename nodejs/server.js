var express = require('express'),
    http = require('http'),
    crypto = require('crypto'),
    server = http.createServer(app);

var app = express();
// increase pool size
http.globalAgent.maxSockets = Infinity;

const redis = require('redis');
const io = require('socket.io');
var deleteKey = require('key-del');
io_serv = io.listen(3000, 'localhost');
//io_serv.disable('heartbeats');

// usernames which are currently connected to the chat
var usernames = {};
var clients = {};
// rooms which are currently available in chat
var rooms = ['General'];

encryption_key = "5uatH3Q2TqWnHYvJLGZbP8Fm";

// io_serv.set('heartbeat timeout', 10);
// io_serv.set('heartbeat interval', 10);
// io_serv.set("polling duration", 5);
// io_serv.set("close timeout", 60);
io_serv.set('log level', 1); // reduce logging
io_serv.sockets.on('connection', function(client) {
    redisClient = redis.createClient();
    redisClient.subscribe('notification');
    r = redis.createClient();
    // when the client emits 'adduser', this listens and executes
    client.on('adduser', function(user, chat) {
        var userTmp = user.split(":::");

        var user_id = userTmp[0];
        var avatar = userTmp[1];
        var username = userTmp[2];
        var region = userTmp[3];
        var token = userTmp[4];

        var genToken = crypto.createHmac('SHA256', encryption_key).update("" + user_id + region + username).digest('base64');
        console.log("token:" + token);
        console.log("generated Token: " + genToken);

        var time = Math.round(new Date().getTime() / 1000);

        // store the username in the socket session for this client
        client.username = username;
        client.user_id = user_id;
        client.avatar = avatar;
        client.region = region;
        client.lastpost = (time - 5);
        client.chat = chat;


        var userData = {};
        userData['name'] = username;
        userData['avatar'] = avatar;
        userData['socket_id'] = client.id;
        userData['region'] = region;


        // start room
        var start_room = "General";
        // usernames 
        if (typeof usernames[chat] == "undefined") {
            usernames[chat] = {};
        }
        if (typeof usernames[chat][region] == "undefined") {
            usernames[chat][region] = {};
        }
        if (typeof usernames[chat][region][start_room] == "undefined") {
            usernames[chat][region][start_room] = {};
        }

        if (typeof usernames[chat][region][start_room][user_id] == "undefined") {

            // add the client's username to the global list
            usernames[chat][region][start_room][user_id] = userData;

        } else {
            console.log("usernames already set");
        }


        console.log("\n\r");
        console.log("ADD NEW USER: " + username);

        clients[user_id] = {};
        clients[user_id] = userData;

        r.hmset("client:" + user_id + ":" + client.chat + ":" + region + ":rooms", start_room, '{"name":"' + start_room + '","avatar": ""}', function(err) {
            if (err) {
                // console.log("\n\r");
                console.log(err);
            } else {
                // console.log("\n\r");
                // console.log("ADD GENERALL!!");
                // console.log("client:" + client.user_id + ":" + client.chat + ":rooms");
            }
        });
        //r.sadd("client:" + user_id + ":" + client.chat + ":rooms", start_room, function(err) {
        r.hgetall("client:" + user_id + ":" + client.chat + ":" + region + ":rooms", function(err, rooms) {

            if (err) {
                console.log("\r\n");
                console.log("error hgetall addusers:");
                console.log(err);
            } else {
                // console.log("\r\n");
                // console.log("rooms:");
                // console.log(rooms);
                client.rooms = {};
                client.rooms[chat] = {};
                client.rooms[chat][region] = {};

                for (key in rooms) {
                    var roomData = JSON.parse(rooms[key]);
                    client.rooms[chat][region][key] = {};
                    client.rooms[chat][region][key]['name'] = roomData.name;
                    client.rooms[chat][region][key]['avatar'] = roomData.avatar;
                }
                console.log("client.rooms:");
                console.log(client.rooms);



                // store the room name in the socket session for this client
                client.room = start_room;

                // send client to room 1
                client.join(client.chat + client.region + start_room);

                sendUpdateUsersToAllInRoom(region, start_room, client.chat, usernames[client.chat][region][start_room], client.rooms[client.chat][region]);

                client.emit("adduserDone");
                console.log("\n\r");
                console.log(client.rooms[client.chat][region]);
                console.log("END ADD USER");
                //console.log(usernames[start_room][client.user_id]);

                if (token != genToken) {
                    client.disconnect();
                }
            }

        });



    });
    // set achat of client
    client.on('setchat', function(data) {
        console.log("\n\r");
        console.log("SET CHAT");
        client.chat = data;
    });

    client.on("getRoom", function() {
        console.log("\n\r");
        console.log("GET ROOM");
        console.log(client.room);
        client.emit("getRoomCallback", client.room);
    });

    // when the client emits 'sendchat', this listens and executes
    client.on('sendchat', function(data) {
        console.log("\n\r");
        console.log("SEND CHAT: " + client.chat);
        var time = Math.round(new Date().getTime() / 1000);
        if (client.lastpost + 5 <= time) {
            client.lastpost = time;
            console.log("ROOM:" + client.room);
            if (client.room != "General") {
                console.log("room:" + client.room);
                // get whisper user_id
                var tmp = client.room.split(client.user_id);
                console.log(tmp);
                if (tmp[0] != "") {
                    var whisper_client_id = tmp[0];
                } else {
                    var whisper_client_id = tmp[1];
                }

                console.log(whisper_client_id);

                // -> whisper
                console.log("\n\r");
                console.log(clients);
                console.log("\n\r");
                if (typeof clients[whisper_client_id] != "undefined") {
                    var whisper_client_socket_id = clients[whisper_client_id]['socket_id'];
                    var whisper_client = io_serv.sockets.socket(whisper_client_socket_id);
                    console.log("\n\r");
                    console.log(whisper_client.rooms);
                    console.log("\n\r");

                    r.hmset("client:" + whisper_client_id + ":" + client.chat + ":" + client.region + ":rooms", client.room, '{"name": "' + client.username + '","avatar": "' + client.avatar + '"}');

                    whisper_client.rooms[client.chat][client.region][client.room] = {};
                    whisper_client.rooms[client.chat][client.region][client.room]['name'] = client.username;
                    whisper_client.rooms[client.chat][client.region][client.room]['avatar'] = client.avatar;

                    whisper_client.emit("whisperMessage", client.chat, client.room, client.username, client.avatar);

                } else {
                    console.log("whisper_client is undefined");
                }
            }
            // we tell the client to execute 'updatechat' with 2 parameters
            console.log("broadcast to " + client.chat + client.room);
            client.broadcast.to(client.chat + client.region + client.room).emit('updatechat', client.chat, client.room, client.user_id, client.username, client.avatar, time, data);
        }
        console.log("\n\r");
        console.log("END SEND CHAT");
    });

    client.on('switchRoom', function(newroom) {
        console.log("\n\r");
        console.log("NEW ROOM:" + newroom);
        var time = Math.round(new Date().getTime() / 1000);
        // leave the current room (stored in session)
        client.leave(client.chat + client.region + client.room);

        console.log("\n\r OLD ROOM:" + client.room + "\n\r");
        // delete aus room array
        //console.log("\n\r");
        //console.log(usernames[client.chat][client.region][client.room]);
        usernames[client.chat][client.region][client.room] = deleteKey(usernames[client.chat][client.region][client.room], [client.user_id]);


        // delete aus room array
        //console.log("\n\r");
        //console.log(usernames[client.chat][client.region][client.room]);
        //sendUpdateUsersToAllInRoom(client.region, client.room, client.chat, usernames[client.chat][client.region][client.room], client.rooms[client.chat][client.region]);

        // join new room, received as function parameter
        client.join(client.chat + client.region + newroom);

        //client.emit('updatechat', client.chat, client.room, 0, 'SERVER', "", time, 'you have connected to room:' + newroom);
        // sent message to OLD room
        //client.broadcast.to(client.room).emit('updatechat', client.chat, client.room, 0, 'SERVER', "", time, client.username + ' has left this room');
        // update socket session room title
        client.room = newroom;

        // insert into new room
        var userdata = {};
        userdata['name'] = client.username;
        userdata['avatar'] = client.avatar;
        userdata['socket_id'] = client.id;

        if (typeof usernames[client.chat][client.region][newroom] == "undefined") {
            usernames[client.chat][client.region][newroom] = {};
        }
        if (typeof usernames[client.chat][client.region][newroom][client.user_id] == "undefined") {
            usernames[client.chat][client.region][newroom][client.user_id] = {};
        }

        usernames[client.chat][client.region][newroom][client.user_id] = userdata;

        console.log("\n\rROOM OR USER NOW:" + client.room + "\n\r");
        client.emit('updaterooms', client.chat, client.rooms[client.chat][client.region], client.room);
        //client.broadcast.to(newroom).emit('updatechat', client.chat, client.room, 0, 'SERVER', "", time, client.username + ' has joined this room');
        //client.on("updateRoomsDone", function(){
        sendUpdateUsersToAllInRoom(client.region, newroom, client.chat, usernames[client.chat][client.region][client.room], client.rooms[client.chat][client.region]);
        //});
        console.log("\n\r");
        console.log("END NEW ROOM");
    });

    client.on("sendWhisper", function(user_id, username, avatar, chat) {
        console.log("\n\r");
        console.log("SEND WHISPER");
        if (user_id > client.user_id) {
            var room = user_id + client.user_id;
        } else {
            var room = client.user_id + user_id;
        }
        console.log("\n\r");
        console.log("SEND WISPER - room:" + room);
        console.log("\n\r");
        client.rooms[client.chat][client.region][room] = {};
        client.rooms[client.chat][client.region][room]['name'] = username;
        client.rooms[client.chat][client.region][room]['avatar'] = avatar;

        r.hmset("client:" + client.user_id + ":" + client.chat + ":" + client.region + ":rooms", room, '{"name": "' + username + '","avatar":"' + avatar + '"}');
    });

    client.on("deleteRoom", function(room) {
        console.log("\n\r");
        console.log("DELETE ROOM");
        client.rooms[client.chat][client.region] = deleteKey(client.rooms[client.chat][client.region], [room]);
        r.hdel("client:" + client.user_id + ":" + client.chat + ":" + client.region + ":rooms", room);
    });

    client.on("sendUpdateusers", function(room) {
        console.log("\n\r");
        console.log("SEND UPDATE USERS");
        sendUpdateUsersToAllInRoom(client.region, client.region, room, client.chat, usernames[client.chat][client.region][room], client.rooms[client.chat][client.region]);
    });

    client.on("refreshUsersInChatForUser", function(chat, room) {
        client.emit('updateusers', client.chat, room, usernames[client.chat][client.region][room], client.rooms[client.chat][client.region]);
    });

    client.on("updateOldRooms", function() {
        console.log("\n\r");
        console.log("UPDATE OLD ROOMS");
        r.hgetall("client:" + client.user_id + ":" + client.chat + ":" + client.region + ":rooms", function(err, rooms) {
            if (err) {
                console.log("\r\n");
                console.log("error smembers:");
                console.log(err);
                console.log("\r\n");
            } else {
                // console.log("\r\n");
                // console.log("rooms:");
                // console.log(rooms);
                // console.log("\r\n");
                var retRooms = {};
                for (key in rooms) {
                    var roomData = JSON.parse(rooms[key]);
                    retRooms[key] = {};
                    retRooms[key]['name'] = roomData.name;
                    retRooms[key]['avatar'] = roomData.avatar;
                }
                // console.log("\r\n");
                // console.log("RETROOMS:");
                console.log(retRooms);
                client.emit('updaterooms', client.chat, retRooms, "General");

            }

        });

    });
    // client.on("addUserToChatList", function(user_id) {
    //     var userData = clients[user_id];
    //     io_serv.sockets. in (client.chat + client.region + client.room).emit('addUserToChatList', chat, room, userData);
    // });
    //redisClient.psubscribe('chat:*');
    redisClient.on("message", function(channel, message) {
        console.log("\n\r");
        console.log("MESSAGE");
        //Channel is e.g 'score.update'
        client.emit(channel, message);
    });

    client.on("switchChat", function() {
        console.log("SWITCH CHAT");

        //leave old
        client.leave(client.chat + client.room);
        if (typeof usernames[client.chat][client.region] != "undefined" && typeof usernames[client.chat][client.region][client.room]) {
            usernames[client.chat][client.region][client.room] = deleteKey(usernames[client.chat][client.region][client.room], [client.user_id]);
            client.leave(client.chat + client.region + client.room);
            //client.broadcast.emit('updatechat', 'SERVER', client.username + ' has disconnected');
            sendUpdateUsersToAllInRoom(client.region, client.room, client.chat, usernames[client.chat][client.region][client.room], client.rooms[client.chat][client.region]);
        }
    });

    client.on('disconnect', function() {
        console.log("\n\r");
        console.log("DISCONNECT");
        redisClient.quit();
        r.quit();
        var time = Math.round(new Date().getTime() / 1000);
        if (typeof usernames[client.chat][client.region] != "undefined" && typeof usernames[client.chat][client.region][client.room]) {

            usernames[client.chat][client.region][client.room] = deleteKey(usernames[client.chat][client.region][client.room], [client.user_id]);
            // update list of users in chat, client-side
            sendUpdateUsersToAllInRoom(client.region, client.room, client.chat, usernames[client.chat][client.region][client.room], client.rooms[client.chat][client.region]);

            // echo globally that this client has left
            //client.broadcast.emit('updatechat', 'SERVER', client.username + ' has disconnected');
            client.leave(client.chat + client.region + client.room);
            clients = deleteKey(clients, [client.user_id]);
        }
    });

    client.on("test", function(data) {
        // var savedSocketId = usernames[client.room][data]['socket_id'];
        // console.log("USERNAME:"+io_serv.sockets.socket(savedSocketId).username);
    });

    function sendUpdateUsersToAllInRoom(region, room, chat, usernames, rooms) {

        console.log("\r\n");
        console.log("sendUpdateUsersToAllInRoom room:" + room + " chat:" + chat);
        console.log(usernames);

        io_serv.sockets. in (chat + region + room).emit('updateusers', chat, room, usernames, rooms);
    }
});