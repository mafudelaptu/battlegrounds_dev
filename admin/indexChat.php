<?php
session_start();
require_once("../inc/inc_general_php_functions_for_admin.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dota2 Lone Wolf League</title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
<script type="text/javascript" charset="utf-8">
    function addmsgs(chatID, msgs){
    	if(msgs){
			for (var i = 0; i < msgs.length; i++) {
                $('#chat-'+chatID+"-area").append($(msgs[i]));
            }								  
	   }
	   container = $("#chat-"+chatID+"-area");
        var height = container.height();
        var scrollHeight = container[0].scrollHeight;
        var st = container.scrollTop();
        
       // if(st == 0 && scrolled == false){
        	scrolled = true;
        	var hovered = $("#chat-"+chatID+"-wrap").find("#chat"+chatID+"-area:hover").length;
        	if(hovered == 0){
        		setTimeout(function(){
			   		
				  	  $('#chat-"+chatID+"-area').stop().animate({ scrollTop: $("#chat-"+chatID+"-area")[0].scrollHeight }, 200);
				     },10);
        	}
    }

function initChat(section, special, chatID){
	l("initChat Start");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "chat",
			mode : "initChat",
			section : section,
			special: special
		},
		success : function(result) {
			l("initChat success");
			l(result);
			if (result.status) {
				updateChat(section, special, chatID); /* Start the inital request */
			}
		}
	});
	l("initChat End");
}
    
    function updateChat(section, special, chatID, linesAdded){
        l("updateChat");
        /* This requests the url "msgsrv.php"
        When it complete (or errors)*/
        if(typeof linesAdded == "undefined"){
				linesAdded = 0;
        }
        $.ajax({
            type: "GET",
            url: "../ajaxAdmin.php",
            data : {
				type : "chat",
				mode : "updateChat",
				section : section,
				special: special,
				lines: linesAdded
			},
            async: true, /* If set to non-async, browser shows page as "Loading.."*/
            cache: false,
            timeout:20000, /* Timeout in ms */

            success: function(data){ /* called when request to barge.php completes */
                l("success - new msg");
                l(data);
            	addmsgs(chatID, data.html); /* Add response to a .msg div (with the "new" class)*/
                setTimeout(
                		//updateChat(section, special, chatID, data.linesAdded), /* Request next message */
                    1000 /* ..after 1 seconds */
                );
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
               // addmsg("error", textStatus + " (" + errorThrown + ")");
                setTimeout(
                	//updateChat(section, special, chatID, 0), /* Try again after.. */
                    15000); /* milliseconds (15seconds) */
            }
        });
        l("END updateChat");
    };

function postMessage(section, special, chatID){
	l("postMessage Start");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "chat",
			mode : "postComment",
			section : section,
			special: special
		},
		success : function(data) {
			l("postMessage success");
			l(data);
		}
	});
	l("postMessage End");
}
    
    $(document).ready(function(){
    	updateChat("match","123", "adminChat"); /* Start the inital request */
    });
    </script>
</head>

<body>
  <div id="wrap">
   <?php 
	 			$smarty->display('admin/top_navi.tpl');
			?>
	<div id="globalBG">
      <div class="container">
      	<?php 
	 			$smarty->display('admin/indexChat.tpl');
				?>
      </div> <!-- /container -->
    </div>
    </div>
    <div id="footer">
      <div class="container">
        <?php $smarty->display('footer.tpl'); ?>
      </div>
    </div>
    <?php $smarty->display('general_stuff.tpl');?>
</body>
</html>

