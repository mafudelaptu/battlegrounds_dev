 <div id="general_dialog" class="hidden"></div>
 <div id="queueArea" class="hide">
 		Searching for Match (Players found:<span class="badge" id="playersFound"></span>) <img src="img/ajax-loader.gif" width="126" height="22" alt="loading" />
    <button name="leaveQueue" class="btn-small btn-danger" data-toggle="modal" data-target="#myModalLeaveQueue">Leave Queue</button>
 </div>
 <div id="notification" class="alert alert-block alert-error fade in hide"><button type="button" class="close" onclick="$(this).parent().hide()">&times;</button><p></p></div>
 <?php 
 if(DEBUG){
 	echo "<div><pre>".$_SESSION['debug']."</pre></div>";
 }
 
 ?>