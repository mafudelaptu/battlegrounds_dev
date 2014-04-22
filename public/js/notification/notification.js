 $(function() {
     // Handler for .ready() called.
     if (document.URL.indexOf("/start") === -1) {
         if (USER['user_id'] != "") {
             if (typeof io != "undefined") {
                 socket = io.connect(SOCKET);

                 socket.on('notification', function(data) {
                     l('notificationType: ' + data);
                     var notData = null;
                     switch (data) {
                         case "event_start":
                             //Do something with data
                             notData = getEventStartNotification();
                             break;
                         case "ping":
                             notData = getPingNotification();
                             break;
                         case "event_checkIn":
                             notData = getEventCheckInNotification();
                             break;
                     }
                     l(notData);
                     if (notData != null) {
                         notData.success(function(result) {
                             if (result.status == true) {
                                 switch (result.alertType) {
                                     case "bootbox":
                                         var text = result.html;
                                         bootbox.alert(text, function() {
                                             switch (result.callback) {
                                                 case "reload":
                                                     window.location.reload();
                                                     break;
                                                 case false:
                                                     break;

                                                 default:
                                                     window.location.href = result.callback;
                                                     break;
                                             }
                                         });
                                         break;
                                     case "modal":
                                         $("#generalModal .modal-content").html(result.html);
                                         $("#generalModal").modal("show");
                                         break;

                                     case "ping":
                                         playPingNotificationSound();
                                         break;
                                 }
                                 if (result.audio == true) {
                                     playNotificationSound();
                                 }
                             }
                         });
                     }
                 });
             }

         }
     }
 });



 function switchRoom(room) {
     socket.emit('switchRoom', room);
 }

 function getEventStartNotification() {
     var ret = $.ajax({
         url: ARENA_PATH + "/notification/getEventStart",
         type: "GET",
         dataType: 'json'
     });
     return ret;
 }

 function getEventCheckInNotification() {
     var ret = $.ajax({
         url: ARENA_PATH + "/notification/getEventCheckIn",
         type: "GET",
         dataType: 'json'
     });
     return ret;
 }

 function getPingNotification() {
     var ret = $.ajax({
         url: ARENA_PATH + "/notification/getPing",
         type: "GET",
         dataType: 'json'
     });
     return ret;
 }

 /*
  * Copyright 2013 Artur Leinweber
  * Date: 2013-01-01
  */

 function playPingNotificationSound() {
     var mySound = new buzz.sound([
         ARENA_PATH + "/files/sound/notification.ogg",
         ARENA_PATH + "/files/sound/notification.mp3",
         ARENA_PATH + "/files/sound/notification.wav",
         ARENA_PATH + "/files/sound/notification.aac"
     ]);
     mySound.play();
     mySound.setVolume(100);
 }

 /*
  * Copyright 2013 Artur Leinweber
  * Date: 2013-01-01
  */

 function playNotificationSound() {
     playPingNotificationSound();
 }