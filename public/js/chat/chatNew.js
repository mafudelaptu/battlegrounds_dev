function initChatNew(chatname, socket) {
    lastSendTime = new Date().getTime() - 5000;
    msgScaffold = "";
    chatuserScaffold = "";


    // on connection to server, ask for user's name with an anonymous callback
    socket.on('connect', function() {
        connectToChat(chatname, socket);
    });


    // listener, whenever the server emits 'updatechat', this updates the chat body
    socket.on('updatechat', function(chat, user_id, username, avatar, time, msg, whisper) {
        l("UPDATE CHAT: chat:" + chat + " user_id:" + user_id + " username:" + username + " avatar:" + avatar + " time:" + time + " msg:" + msg);
        if (typeof time != "undefined") {
            var forTimeago = new Date(time * 1000).toISOString();
            console.log("WHISPER:" + whisper);
            if (whisper) {
                appendMessage(chat, user_id, username, avatar, forTimeago, msg, true, USER.user_id, USER.username, USER.avatar);
            } else {
                appendMessage(chat, user_id, username, avatar, forTimeago, msg, false, 0, "", "");
            }

        } else {
            l("time -> undefined");
        }

    });

    socket.on("addUserToChat", function(chat, user, admin) {
        l("addUserToChat: chat-" + chat + " user:" + user.username);
        var chatUserDiv = $("#" + chat + " .chatusers");

        var check = $("#" + chat + user.user_id).length;
        if (check == 0) {
            // insert info
            insertDataIntoChatuserScaffold(chat, user, admin);

            // add Scaffold
            chatUserDiv.append(chatuserScaffold);

            // caret toggle
            initCaretToggleForUser(chat, user.user_id);

            // init Menu Buttons
            initMenuButtonsForUser(chat, user.user_id);

            // increment user chat count
            incrementChatUsers(chat);

            sortChatusersByAdmin(chat);

            initTooltips();
        }

    });

    socket.on("removeUserOfChat", function(chat, user_id) {
        l("removeUserOfChat: chat-" + chat + " user_id-" + user_id);
        if ($("#" + chat + " .chatusers > div[id='" + chat + user_id + "']").length > 0) {
            $("#" + chat + " .chatusers > div[id='" + chat + user_id + "']").remove();
            decrimentChatUsers(chat);
        }
    });


    socket.on("updateusers", function(chat, users) {
        l("update USERS! chat:" + chat);
        l(users);
        var usersBox = $("#" + chat + " .count_chatusers").parent();
        usersBox.block({
            message: "<p>updating users</p><p>please wait a moment</p>"
        });

        // $.ajax({
        //     url: ARENA_PATH + "/chat/getChatUsers",
        //     type: "GET",
        //     dataType: 'json',
        //     data: {
        //         fake: true,
        //         users: users,
        //         chat: chat
        //     },
        //     success: function(result) {
        //         // div
        //         var div = $("#" + chat + " .chatusers");
        //         l(div);
        //         // clear
        //         div.html(result.html);

        //         updateChatUserCount(chat, result.count);

        //         initCaretToggle(chat);

        //         initMenuButtons(chat);

        //         usersBox.unblock();
        //     }
        // });
        var chatUserDiv = $("#" + chat + " .chatusers");
        chatUserDiv.html("");
        var z = 0;
        $.each(users, function(key, user) {
            l(user);
            var check = $("#" + chat + user.user_id).length;
            if (check == 0) {
                // insert info
                insertDataIntoChatuserScaffold(chat, user, user.admin);

                // add Scaffold
                chatUserDiv.append(chatuserScaffold);

                z++;
            }
        });
        sortChatusersByAdmin(chat);
        updateChatUserCount(chat, z);
        initTooltips();
        initCaretToggle(chat);
        initMenuButtons(chat);
        usersBox.unblock();
    });
}

function updateChatUserCount(chat, count) {
    $("#" + chat + " .count_chatusers > .count").html(count);
}

function insertOlderMessages(chat) {
    l("insertOlderMessages chat:" + chat);
    $.ajax({
        url: ARENA_PATH + "/chat/getOlderMessages",
        type: "GET",
        dataType: 'json',
        data: {
            fake: false,
            section: chat,
            last: lastSendTime
        },
        success: function(result) {
            l(result);
            // div
            // var div = $("#" + chat + room + " .conversation");
            var div = $("#" + chat + " .conversation > .showPreviousMessagesBox");

            // clear
            div.after(result.html);

            // hide: show previous messages
            $("#" + chat + " .showPreviousMessagesBox").hide();
            if (typeof div[0] != "undefined") {
                // timeago init
                initTimeago();
                div[0].scrollTop = div[0].scrollHeight;
            } else {
                l("div");
                l(div);
            }
            scrollChatConversationToBottom(chat);
        }
    });
}

function initMenuButtonsForUser(chat, user_id) {
    var menu = $("#" + chat + user_id + " .dropdown-menu");
    //whisper
    var whisperLink = menu.find(".whisper");
    $(whisperLink[0]).once().click(function() {
        var username = whisperLink.attr("data-name");
        var user_id = whisperLink.attr("data-id");
        var avatar = whisperLink.attr("data-avatar");
        switchInWhisperMode(user_id, username, avatar, chat);
    });
}

function initMenuButtons(chat) {
    var menu = $("#" + chat + " .chatusers .dropdown-menu");
    //whisper
    var whisperLinks = menu.find(".whisper");
    whisperLinks.once().click(function() {
        var username = $(this).attr("data-name");
        var user_id = $(this).attr("data-id");
        var avatar = $(this).attr("data-avatar");
        switchInWhisperMode(user_id, username, avatar, chat);
    });
}

function initCaretToggleForUser(chat, user_id) {
    var div = $("#" + chat + user_id).get(0);
    $(div).mouseenter(function() {
        var caret = $(this).find(".caret");
        caret.removeClass("hide");
    }).mouseleave(function() {
        var caret = $(this).find(".caret");
        caret.addClass("hide");
    });
}

function initCaretToggle(chat) {
    var div = $("#" + chat + " .chatusers > div");
    $(div).mouseenter(function() {
        var caret = $(this).find(".caret");
        caret.removeClass("hide");
    }).mouseleave(function() {
        var caret = $(this).find(".caret");
        caret.addClass("hide");
    });
}

function switchInWhisperMode(user_id, username, avatar, chat) {
    var check = $("#" + chat + " .chat-input > .input-group-addon.whisperUser[data-id='" + user_id + "']");
    if (check.length == 0) {
        $.ajax({
            url: ARENA_PATH + "/chat/getWhisperModeInputAddon",
            type: "GET",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id,
                avatar: avatar,
                username: username,
            },
            success: function(result) {
                var input = $("#" + chat + " .chat-input");

                if (input.find("> .input-group-addon.switch").length == 0) {
                    // hide all Chat            
                    var all_chat = input.find(" >.input-group-addon:not(.whisperUser)");
                    all_chat.hide();

                    // add whisperMenu
                    input.prepend(result.html);

                    initTooltips();

                    initWhisperButtons(chat);

                    // focus Input
                    focusInput(chat);

                }

            }
        });
    } else {
        l("have already user in whisper mode!");
    }

}

function initWhisperButtons(chat) {
    var div = $("#" + chat + " .chat-input");
    //switch to all chat
    var switchButton = div.find("> .switch");
    switchButton.once().click(function() {
        var mode = $(this).attr("data-mode");
        switch (mode) {
            case "AllChat":
                div.find("> .cancelWhisper").hide();
                $(this).find("> i.fa-comments").removeClass("fa-comments").addClass("fa-user");
                $(this).attr("data-mode", "Whisper");
                $(this).attr("data-original-title", "whisper last user");
                div.find("> .allChat").show();
                div.find("> .whisperUser").hide();

                break;
            case "Whisper":
                $(div).find("> .cancelWhisper").show();
                $(this).find("> i.fa-user").removeClass("fa-user").addClass("fa-comments");
                $(this).attr("data-mode", "AllChat");
                $(this).attr("data-original-title", "change back to All-Chat");
                div.find("> .allChat").hide();
                div.find("> .whisperUser").show();
                break;
        }
    });

    //cancelWhisper
    var cancelButton = div.find("> .cancelWhisper");
    cancelButton.once().click(function() {
        $(this).tooltip("hide");
        all_addons = div.find("> .input-group-addon:not(.allChat)").remove();
        div.find("> .allChat").show();
    });
}

function initInChatWisperForUser(chat, user_id) {
    l("initInChatWisperForUser " + user_id);
    var button = $("#" + chat + " .inChatWhisper" + user_id);
    l(button);
    button.once().click(function() {
        var username = button.attr("data-name");
        var avatar = button.attr("data-avatar");
        switchInWhisperMode(user_id, username, avatar, chat);
    });
}

function sendMessage(chatname) {
    l("sendMessage:" + chatname);
    var message = $('#send-data-' + chatname).val();

    l(message);
    var now = new Date().getTime();
    l("LAST SEND TIME:" + lastSendTime + " NOW:" + now);
    if (message != "" && lastSendTime + 5000 < now) {
        removeSpamError(chatname);
        lastSendTime = now;
        $('#send-data-' + chatname).val('');
        var forTimeago = new Date().toISOString();
        var section = chatname;
        // check if whisper message
        if ($("#" + chatname + " .allChat").is(":hidden")) {
            l("whisper-msg");
            // whisper
            var user_id = $("#" + chatname + " .whisperUser").attr("data-id");
            var username = $("#" + chatname + " .whisperUser").attr("data-name");
            var avatar = $("#" + chatname + " .whisperUser").attr("data-avatar");
            l("USER_ID:" + user_id);
            if ($.isNumeric(user_id)) {
                if (USER.user_id > user_id) {
                    section = "" + USER.user_id + user_id;
                } else {
                    section = "" + user_id + USER.user_id;
                }
                //append msg to chat
                appendMessage(chatname, USER.user_id, USER.username, USER.avatar, forTimeago, message, true, user_id, username, avatar);

                // tell server to execute 'sendchat' and send along one parameter
                socket.emit('sendchat', message, user_id);
            } else {
                l("NOT NUMERIC");
            }
        } else {
            l("normal-msg");
            // normal msg

            //append msg to chat
            //appendMessage(chatname, USER.user_id, USER.username, USER.avatar, forTimeago, message, false, 0, "", "");

            // tell server to execute 'sendchat' and send along one parameter
            socket.emit('sendchat', message, 0);
        }
        $.ajax({
            url: ARENA_PATH + "/chat/postMessage",
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                msg: message,
                section: section,
            },
            success: function(result) {
                l(result);
            }
        });
    } else {
        addSpamError(chatname);
    }
}

function initEnterPressSendChat(chatname) {
    l("initEnterPressSendChat: " + chatname);
    // when the client hits ENTER on their keyboard
    $('#send-data-' + chatname).once().keypress(function(e) {
        if (e.which == 13) {
            l("hit enter");
            sendMessage(chatname);
        }
    });
}

function switchChat(newChat, socket) {
    l("switchChat to " + newChat + "!");
    socket.emit("switchChat", newChat);
    connectToChat(newChat, socket);
}

function refreshUsersInChat(chat) {
    socket.emit("refreshUsersInChatForUser", chat);
}

function insertDataIntoChatuserScaffold(chat, user, admin) {
    l("insertDataIntoChatuserScaffold user:" + user.username);
    var div = $(chatuserScaffold).clone();
    div.attr("id", chat + user.user_id);

    //## username and avatar
    var html = '<img src="' + user.avatar + '"" width="22">&nbsp;<span style="truncate">' + user.username + '</span> <span class="caret hide"></span>';
    //l(div.find("> .dropdown-toggle").first());
    div.find("> .dropdown-toggle").first().html(html);

    //## dropdown
    // whisper
    var whisper = " > ul li > a.whisper";
    div.find(whisper).attr("data-name", user.username);
    div.find(whisper).attr("data-avatar", user.avatar);
    div.find(whisper).attr("data-id", user.user_id);
    if (user.user_id == USER.user_id) {
        div.find(whisper).addClass("hide");
    } else {
        div.find(whisper).removeClass("hide");
    }
    // profile
    var profile = " > ul li > a.profile";
    div.find(profile).attr("href", ARENA_PATH + "/profile/" + user.user_id);

    // steam-profile
    var steamprofile = " > ul li > a.steamprofile";
    if (typeof div.find(steamprofile) != "undefined") {
        div.find(steamprofile).attr("href", "http://steamcommunity.com/profiles/" + user.user_id);
    }

    // isAdmin
    if (admin) {
        if (!div.hasClass("admin")) {
            div.addClass("admin");
            div.addClass("t");
            div.attr("title", "Admin");
        }
    } else {
        div.removeClass("admin");
        div.removeClass("t");
        div.removeAttr("title");
    }
    chatuserScaffold = div.get(0);
}

function incrementChatUsers(chat) {
    var countElem = $("#" + chat + " .count_chatusers > span");
    var current_count = parseInt(countElem.html());
    current_count++;
    countElem.html(current_count);
}

function decrimentChatUsers(chat) {
    var countElem = $("#" + chat + " .count_chatusers > span");
    var current_count = parseInt(countElem.html());
    current_count--;
    countElem.html(current_count);
}

function inituserlistRefresh(chat) {
    var userlistRefresh = $("#" + chat + " .userlistRefreshButton");
    userlistRefresh.once().click(function(event) {
        refreshUsersInChat(chat);
    });
}

function connectToChat(chatname, socket) {
    allChatSelector = "#" + chatname + " .allChatMessage";
    whisperSelector = "#" + chatname + " .whisperMessage";

    l("connect!");
    // call the server-side function 'adduser' and send one parameter (value of prompt)
    l(USER);
    $.ajax({
        url: ARENA_PATH + "/chat/getScaffoldMessageAndChatuser",
        type: "GET",
        dataType: 'json',
        data: {
            fake: false,
            user: USER,
            chat: chatname
        },
        success: function(result) {
            l(result);
            msgScaffold = result.msg;
            chatuserScaffold = result.chatuser;

            if (msgScaffold != "" && chatuserScaffold != "") {
                l("adduser");
                socket.emit('adduser', USER, chatname, result.admin);
            }

            // init show previous messages
            var showOld = $("#" + chatname + " .showPreviousMessages");
            showOld.once().click(function(event) {
                insertOlderMessages(chatname);
            });
        }
    });

    initEnterPressSendChat(chatname);

    inituserlistRefresh(chatname);

    // init show-menu filter
    initShowMenuFilter(chatname);
}

function appendMessage(chat, user_id, username, avatar, time, msg, whisperMessage, whisper_user_id, whisper_username, whisper_avatar) {
    $.ajax({
        url: ARENA_PATH + "/chat/getMessage",
        type: "GET",
        dataType: 'json',
        data: {
            fake: false,
            user_id: user_id,
            avatar: avatar,
            username: username,
            time: time,
            msg: msg,
            whisper: whisperMessage,
            whisper_user_id: whisper_user_id,
            whisper_username: whisper_username,
            whisper_avatar: whisper_avatar
        },
        success: function(result) {
            // div
            var div = $("#" + chat + " .conversation");

            // clear
            div.append(result.html);

            // timeago init
            initTimeago();

            scrollChatConversationToBottom(chat);

            if (whisperMessage && user_id != USER.user_id) {
                initInChatWisperForUser(chat, user_id);
            }
            var msgType = "allchat";
            if (whisperMessage) {
                msgType = "whisper";
            }

            if (!checkShowFilter(chat, msgType)) {
                switch (msgType) {

                    case "allchat":
                        $("#" + chat + allChatSelector).hide();
                        break;
                    case "whisper":
                        $("#" + chat + whisperSelector).hide();
                        break;
                }
            }

        }
    });
}

function addSpamError(chat) {

    var html = '<span class="input-group-addon t bg_danger spamError" title="spam detected. You are sending messages too fast!"><i class="fa fa-exclamation text-danger"></i></span>';
    if ($("#" + chat + " .chat-input > .spamError").length == 0) {
        $("#" + chat + " .chat-input").append(html);
        initTooltips();
    }
}

function removeSpamError(chat) {
    $("#" + chat + " .chat-input > .spamError").remove();
}

function focusInput(chat) {
    $("#send-data-" + chat).focus();
}

function initShowMenuFilter(chat) {
    var all = $("#" + chat + "ShowMenuFilterAll");
    var allchat = $("#" + chat + "ShowMenuFilterAllChat");
    var whisperchat = $("#" + chat + "ShowMenuFilterWhisperChat");


    all.once().click(function() {
        handleShowMenuFilterClick(chat, allChatSelector, whisperSelector, "show", "show");
    });

    allchat.once().click(function() {
        handleShowMenuFilterClick(chat, allChatSelector, whisperSelector, "show", "hide");
    });

    whisperchat.once().click(function() {
        handleShowMenuFilterClick(chat, allChatSelector, whisperSelector, "hide", "show");
    });
}

function handleShowMenuFilterClick(chat, allChatSelector, whisperSelector, allChatFkt, whisperFkt) {
    var allChatMessages = $(allChatSelector);
    var whisperMessages = $(whisperSelector);

    (allChatFkt == "show") ? allChatMessages.show() : allChatMessages.hide();
    (whisperFkt == "show") ? whisperMessages.show() : whisperMessages.hide();

    scrollChatConversationToBottom(chat);
}

function checkShowFilter(chat, type) {
    var checked = $("#" + chat + " input[name='" + chat + "ShowMenu']:checked").val();
    switch (checked) {
        case "all":
            return true;
            break;
        case "allchat":
            if (type == "allchat") {
                return true;
            } else {
                return false;
            }
            break;
        case "whisper":
            if (type == "whisper") {
                return true;
            } else {
                return false;
            }
            break;
    }
}

function scrollChatConversationToBottom(chat) {
    var div = $("#" + chat + " .conversation");
    div[0].scrollTop = div[0].scrollHeight;
}

function sortChatusersByAdmin(chat) {
    var divUsers = $("#" + chat + " .chatusers > div");
    $(divUsers).tsort("", {
        attr: "class",
        order: "desc"
    });
}