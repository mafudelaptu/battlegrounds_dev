function initChatNew(chatname, socket) {
    l("updateOLD ROOMS");
    lastSendTime = new Date().getTime() - 5000;
    // on connection to server, ask for user's name with an anonymous callback
    socket.on('connect', function() {
        l("connect!");
        // call the server-side function 'adduser' and send one parameter (value of prompt)
        var user = USER_ID + ":::" + AVATAR + ":::" + USERNAME + ":::" + REGION + ":::" + TOKEN;
        l(user);
        socket.emit('adduser', user, chatname);
        socket.on("adduserDone", function() {
            socket.emit("updateOldRooms");
            // get old msg's
            //insertOlderMessages(chatname, "General");
        });

        initEnterPressSendChat(chatname);
        // set focus
        //$("#send-data-" + chatname).focus();
    });


    // listener, whenever the server emits 'updatechat', this updates the chat body
    socket.on('updatechat', function(chat, room, user_id, username, avatar, time, msg) {
        l("UPDATE CHAT: chat:" + chat + " room:" + room + " user_id:" + user_id + " username:" + username + " avatar:" + avatar + " time:" + time + " msg:" + msg);
        if (typeof time != "undefined") {
            var forTimeago = new Date(time * 1000).toISOString();
            $.ajax({
                url: ARENA_PATH + "/chat/getMessage",
                type: "GET",
                dataType: 'json',
                data: {
                    fake: false,
                    user_id: user_id,
                    username: username,
                    avatar: avatar,
                    time: forTimeago,
                    msg: msg
                },
                success: function(result) {
                    // div
                    var div = $("#" + chat + room + " .conversation");

                    // clear
                    div.append(result.html);

                    // timeago init
                    initTimeago();

                    div[0].scrollTop = div[0].scrollHeight;
                }
            });
        } else {
            l("time -> undefined");
        }

    });

    // listener, whenever the server emits 'updaterooms', this updates the room the client is in
    socket.on('updaterooms', function(chat, rooms, current_room) {
        l("UPDATE ROOMS: chat:" + chat + " current_room:" + current_room);
        $('#' + chat + 'Tabs').empty();
        l(rooms);
        keys = [];
        var k;
        var i;
        var len;

        for (k in rooms) {
            if (rooms.hasOwnProperty(k)) {
                keys.push(k);
            }
        }

        keys.sort();
        keys.reverse();
        len = keys.length;
        sorted_array = {};
        for (i = 0; i < len; i++) {
            var k = keys[i];
            sorted_array[k] = rooms[k];
        }
        l(sorted_array);
        $.each(sorted_array, function(key, value) {
            if (key == current_room) {
                addTabToUser(chat, key, value.name, value.avatar, true, false);
            } else {
                addTabToUser(chat, key, value.name, value.avatar, false, false);
                $("#" + chat + key).removeClass("active");
            }
        });
        socket.emit("updateRoomsDone");
    });

    socket.on("updateusers", function(chat, room, users, rooms) {
        l("update USERS! chat:" + chat + " room:" + room + " rooms:" + rooms);
        l(users);
        var usersBox = $("#" + chat + room + " .count_chatusers").parent();
        usersBox.block({
            message: "<p>updating users</p><p>please wait a moment</p>"
        });

        $.ajax({
            url: ARENA_PATH + "/chat/getChatUsers",
            type: "GET",
            dataType: 'json',
            data: {
                fake: true,
                users: users,
                chat: chat,
                rooms: rooms
            },
            success: function(result) {
                // div
                var div = $("#" + chat + room + " .chatusers");
                l(div);
                // clear
                div.html(result.html);

                updateChatUserCount(chat, room, result.count);

                showUserInChatMenuForHoveredUser(chat);

                usersBox.unblock();
            }
        });
    });

    socket.on("whisperMessage", function(chat, room, username, avatar) {
        l("whisperMessage= chat:" + chat + " room:" + room + " username:" + username + " avatar:" + avatar);
        // check already whispering
        if ($("#" + chat + room).size() === 0) {
            addTabToUser(chat, room, username, avatar, false, false);
        }

    });

    // socket.on("addUserToChatList", function(chat, room, user) {
    //     var usersBox = $("#" + chat + room + " .count_chatusers").parent();
    //     usersBox.block({
    //         message: "<p>updating users</p><p>please wait a moment</p>"
    //     });

    //     $.ajax({
    //         url: ARENA_PATH + "/chat/getChatUser",
    //         type: "GET",
    //         dataType: 'json',
    //         data: {
    //             fake: true,
    //             user: user,
    //             chat: chat
    //         },
    //         success: function(result) {
    //             // div
    //             var div = $("#" + chat + room + " .chatusers");
    //             l(div);
    //             // clear
    //             div.html(result.html);

    //             updateChatUserCount(chat, room, result.count);

    //             showUserInChatMenuForHoveredUser(chat);

    //             usersBox.unblock();
    //         }
    //     });
    // });
}

function updateChatUserCount(chat, room, count) {
    $("#" + chat + room + " .count_chatusers > .count").html(count);
}

function insertOlderMessages(chat, room) {
    l("insertOlderMessages chat:" + chat + " room:" + room);
    $.ajax({
        url: ARENA_PATH + "/chat/getOlderMessages",
        type: "GET",
        dataType: 'json',
        data: {
            fake: false,
            section: chat + REGION + room,
            last: lastSendTime
        },
        success: function(result) {
            l(result);
            // div
            // var div = $("#" + chat + room + " .conversation");
            var div = $("#" + chat + room + " .conversation > .showPreviousMessagesBox");

            // clear
            div.after(result.html);

            // hide: show previous messages
            $("#" + chat + room + " .showPreviousMessagesBox").hide();
            if (typeof div[0] != "undefined") {
                // timeago init
                initTimeago();
                div[0].scrollTop = div[0].scrollHeight;
            } else {
                l("div");
                l(div);
            }
        }
    });
}

function showUserInChatMenuForHoveredUser(chat) {
    l("showUserInChatMenuForHoveredUser");
    $("." + chat + "userInChat").hover(
        function() { // mouseenter handler
            var menu = $(this).find("> span.userChatMenu");
            // show menu
            menu.removeClass("hide");
            // buttons init
            whisperLink = $(this).find(".whisper");
            if (whisperLink.size() > 0) {
                $(whisperLink[0]).click(function() {
                    var username = whisperLink.attr("data-name");
                    var user_id = whisperLink.attr("data-id");
                    var avatar = whisperLink.attr("data-avatar");
                    sendWhisperToUser(user_id, username, avatar, chat);
                });
            }
        },
        function() { // mouseleave handler
            var btn_group = $(this).find("> span.userChatMenu > div.btn-group");
            if (!btn_group.hasClass("open")) {
                $(this).find("> span.userChatMenu").addClass("hide");
                whisperLink = $(this).find(".whisper");
                $(whisperLink[0]).off("click");
            }

        }
    );
}

function sendWhisperToUser(user_id, username, avatar, chat) {
    socket.emit("sendWhisper", user_id, username, avatar, chat);
    if (USER_ID > user_id) {
        var room = USER_ID + user_id;
    } else {
        var room = user_id + USER_ID;
    }
    socket.emit("switchRoom", room);
}

function closeTab(chat, room) {
    l("closeTab: room:" + room);
    socket.emit("deleteRoom", room);
    l("deleted Room:" + room);
    // delete room
    //setTimeout(function(){
    l("fire switchRoom!");
    socket.emit("switchRoom", "General");
    //}, 100);
    // delete TabContent
    $("#" + chat + room).remove();
    $("#" + chat + "General").parent().addClass("active");
    $("#" + chat + "General").addClass("active");

}

function addTabToUser(chat, room, username, avatar, active, insertOldMessages) {
    var avatarHTML = "";
    var close = "";
    var activeClass = "";
    var onclickHtml = "";
    if (active) {
        activeClass = "active";
    }

    if (avatar != "") {
        avatarHTML = '<img src="' + avatar + '" alt="Avatar of ' + username + '"> ';
        close = '<span class="close" data-room="' + room + '">×</span>';

    }
    $('#' + chat + 'Tabs').append('<li class="' + activeClass + '"><a href="#' + chat + room + '" data-toggle="tab" data-room="' + room + '">' + avatarHTML + username + close + '</a></li>');

    if (close != "") {
        initSingleCloseTabButton(chat, room);
    }

    if (!active) {
        initSingleSwitchTab(chat, room);
    }

    // add TabContent
    //if ($("#" + chat + room).size() === 0) {
    var height = $("#" + chat + "General").height() + "px";
    l("HEIGHT: " + height);
    // block element
    $("#" + chat).block({
        message: "<p>loading</p><p>please wait a moment</p>"
    });
    $.ajax({
        url: ARENA_PATH + "/chat/getTabContent",
        type: "GET",
        dataType: 'json',
        data: {
            fake: false,
            room: room,
            chat: chat,
            height: height,
            active: activeClass
        },
        success: function(result) {
            l(result);
            if ($("#" + chat + room).size() === 0) {
                $("#" + chat + "TabContent").append(result.html);
                l("INSERT MESSAGES?: " + insertOldMessages);
                if (insertOldMessages == true) {
                    insertOlderMessages(chat, room);
                }
            } else {
                l("#" + chat + room + " != 0 [" + $("#" + chat + room).size() + "]");
                if (insertOldMessages == true) {
                    insertOlderMessages(chat, room);
                }
            }

            // init show previous messages
            var showOld = $("#" + chat + room + " .showPreviousMessages");
            showOld.once().click(function(event) {
                insertOlderMessages(chat, room);
            });

            // init refresh userlist
            var userlistRefresh = $("#" + chat + room + " .userlistRefreshButton");
            userlistRefresh.once().click(function(event) {
                refreshUsersInChat(chat, room);
            });
            $("#" + chat).unblock();
        }
    });
    //}
}

function switchRoom(room) {
    l("switchRoom: room:" + room);
    socket.emit("switchRoom", room);
}

function sendMessage(chatname) {
    l("sendMessage:" + chatname);
    var message = $('#send-data-' + chatname).val();

    l(message);
    var now = new Date().getTime();
    l("LAST SEND TIME:" + lastSendTime + " NOW:" + now);
    if (message != "" && lastSendTime + 5000 < now) {
        lastSendTime = now;
        $('#send-data-' + chatname).val('');
        console.log('check 2', socket.socket.connected);
        socket.emit("getRoom");
        var i = 0;
        socket.on("getRoomCallback", function(room) {
            if (i < 1) {
                var forTimeago = new Date().toISOString();
                $.ajax({
                    url: ARENA_PATH + "/chat/getMessage",
                    type: "GET",
                    dataType: 'json',
                    data: {
                        fake: false,
                        user_id: USER_ID,
                        username: USERNAME,
                        avatar: AVATAR,
                        time: forTimeago,
                        msg: message
                    },
                    success: function(result) {
                        l("ROOM:" + room);
                        // div
                        var div = $("#" + chatname + room + " .conversation");

                        // clear
                        div.append(result.html);

                        // timeago init
                        initTimeago();

                        div[0].scrollTop = div[0].scrollHeight;
                    }
                });
                l("getRoomCallback");
                $.ajax({
                    url: ARENA_PATH + "/chat/postMessage",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        fake: false,
                        msg: message,
                        section: chatname + REGION + room,
                    },
                    success: function(result) {
                        l(result);
                    }
                });
                i++;
            }
        });
        // tell server to execute 'sendchat' and send along one parameter
        socket.emit('sendchat', message);


    }
}

function testSocket() {
    socket.emit("test", '76561197981132932');
}

function initCloseTabButtons(chat) {
    var tabs = $("#" + chat + "Tabs > li");
    $.each(tabs, function(key, tab) {
        if (!tab.hasClass("active")) {
            tab.once().click(function() {
                room = tab.attr("data-room");
                l("click switch room: " + room);
                closeTab(chat, room);
            });
        }
    });
}

function initSingleCloseTabButton(chat, room) {
    var close = $("#" + chat + "Tabs > li > a > span.close[data-room='" + room + "']");
    l("initSingleCloseTabButton: chat:" + chat + " room:" + room);
    l(close);
    close.once().click(function(event) {
        room = close.attr("data-room");
        l("click close room: " + room);
        closeTab(chat, room);
        event.stopPropagation();

    });
}

function initSingleSwitchTab(chat, room) {
    var tab = $("#" + chat + "Tabs > li > a[data-room='" + room + "']");
    l("initSingleSwitchTab: chat:" + chat + " room:" + room);
    l(tab);
    tab.once().click(function(event) {
        var tgt = $(event.target);
        l(tgt);
        if (!tgt.is('span.close')) {
            l("JOOOOO! NICHT CLOSE");
            room = tab.attr("data-room");
            l("click switch room: " + room);
            switchRoom(room);
        } else {
            l("OHH NOES! CLOOOSE!");
        }

    });
}

function initEnterPressSendChat(chatname) {
    l("initEnterPressSendChat: " + chatname);
    // when the client hits ENTER on their keyboard
    $('#send-data-' + chatname).keypress(function(e) {
        if (e.which == 13) {
            sendMessage(chatname);
        }
    });
}

function switchChat(newChat, socket) {
    l("switchChat to " + newChat + "!");
    socket.emit("switchChat");
    // call the server-side function 'adduser' and send one parameter (value of prompt)
    var user = USER_ID + ":::" + AVATAR + ":::" + USERNAME + ":::" + REGION + ":::" + TOKEN;
    l(user);
    socket.emit('adduser', user, newChat);
    socket.on("adduserDone", function() {
        socket.emit("updateOldRooms");
        // get old msg's
        //insertOlderMessages(newChat, "General");
    });

    initEnterPressSendChat(newChat);
}

function showPreviousMessages(chat, room) {
    insertOlderMessages(chat, room);
}

function refreshUsersInChat(chat, room) {

    socket.emit("refreshUsersInChatForUser", chat, room);
}