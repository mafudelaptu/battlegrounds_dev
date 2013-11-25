
/* 
Created by: Kenrick Beckett

Name: Chat Engine
*/

var instanse = false;
var state;
var mes;
var file;
var scrolled = false;

function Chat() {
    this.update = updateChat;
    this.send = sendChat;
	this.getState = getStateOfChat;
}

//gets the state of the chat
function getStateOfChat(bereich){
	switch(bereich){
		case "match":
			// fileName bestimmen
			var matchID = getParameterByName("matchID");
			file = "chat/match/match_"+matchID+".txt";
			break;
		case "singleQueue":
			var date = new Date();
			var y = date.getFullYear();
			var m = date.getUTCMonth()+1;
			var d = date.getUTCDate();
			// fileName bestimmen
			file = "chat/singleQueue/queue_"+y+"_"+m+"_"+d+".txt";
			break;
		case "event":
			var eventID = getParameterByName("eventID");
			var createdEventID = getParameterByName("cEID");
			// fileName bestimmen
			file = "chat/event/queue_"+eventID+"_"+createdEventID+".txt";
			break;
		case "shoutBox":
			var date = new Date();
			var y = date.getFullYear();
			var m = date.getUTCMonth()+1;
			var d = date.getUTCDate();
			// fileName bestimmen
			file = "chat/shoutBox/shoutBox_"+y+"_"+m+"_"+d+".txt";
			break;
	}
	

	if(!instanse){
		 instanse = true;
		 $.ajax({
			   type: "POST",
			   url: "ajax.php",
			   data: {  
					   'type' : "chat",
						'mode' : "matchChat",
			   			'function': 'getState',
						'file': file
						},
			   dataType: "json",
			
			   success: function(data){
				   state = data.state;
				   instanse = false;
			   },
			});
	}	 
}

//Updates the chat
function updateChat(){
	 if(!instanse){
		 instanse = true;
		// l(file);
		// file = "match_460.txt";
	     $.ajax({
			   type: "POST",
			   url: "ajax.php",
			   data: {  
				   'type' : "chat",
					'mode' : "matchChat",
			   			'function': 'update',
						'state': state,
						'file': file
						},
			   dataType: "json",
			   success: function(data){
				   if(data.text){
						for (var i = 0; i < data.text.length; i++) {
                            $('#chat-area').append($(data.text[i]));
                        }								  
				   }
				   container = $("#chat-area");
			        var height = container.height();
			        var scrollHeight = container[0].scrollHeight;
			        var st = container.scrollTop();
			        
			       // if(st == 0 && scrolled == false){
			        	scrolled = true;
			        	var hovered = $("#chat-wrap").find("#chat-area:hover").length;
			        	if(hovered == 0){
			        		setTimeout(function(){
						   		
							  	  $('#chat-area').stop().animate({ scrollTop: $("#chat-area")[0].scrollHeight }, 200);
							     },10);
			        	}
			        	 
//			   		}
//			        else{
//			        	l(st);
//			        	l(scrolled);
//			        }
				  
				   instanse = false;
				   state = data.state;
			   },
			});
	 }
	 else {
		 setTimeout(updateChat, 1500);
		 l("testChat");
	 }
}

//send the message
function sendChat(message, nickname)
{       
    updateChat();
     $.ajax({
		   type: "POST",
		   url: "ajax.php",
		   data: {  
			   'type' : "chat",
				'mode' : "matchChat",
		   			'function': 'send',
					'message': message,
					'nickname': nickname,
					'file': file
				 },
		   dataType: "json",
		   success: function(data){
			   updateChat();
			     setTimeout(function(){

			  	  $('#chat-area').stop().animate({ scrollTop: $("#chat-area")[0].scrollHeight }, 200);
			     },10);
			     
			    
		   },
		});

     
}
