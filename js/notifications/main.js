/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function playNotificationSound(){
	l("Start playNotificationSound");
	var mySound = new buzz.sound([
	                              "files/sound/notification.ogg",
	                              "files/sound/notification.mp3",
	                              "files/sound/notification.wav",
	                              "files/sound/notification.aac"
	                          ]);
	mySound.play();
	mySound.setVolume( 100 );
	l(mySound.getVolume());
	l("End playNotificationSound");
} 

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function playPingNotificationSound(){
	l("Start playNotificationSound");
	var mySound = new buzz.sound([
	                              "files/sound/notification.ogg",
	                              "files/sound/notification.mp3",
	                              "files/sound/notification.wav",
	                              "files/sound/notification.aac"
	                          ]);
	mySound.play();
	mySound.setVolume( 100 );
	l(mySound.getVolume());
	l("End playNotificationSound");
} 