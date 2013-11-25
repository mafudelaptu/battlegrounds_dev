<?php 
	// FAVICON
	echo "<!-- FAVICON -->";
	echo '<link rel="shortcut icon" href="dotaleague_favicon.ico" type="image/x-icon" />';
	
	// CSS	
	echo "<!-- CSS -->";
		// Root
		echo "<!-- Root -->";
		$alle1 = glob(DR.HOME_PATH_REL.'/css/*.css');
		$alle2 = glob(DR.HOME_PATH_REL.'/css/*/*.css', GLOB_NOSORT);
		$alle = array_merge($alle1,$alle2);
		//p($alle);
		if(count($alle)>0){
			$catUrl = "";
			foreach($alle as $datei){
				$tmp = explode(DR.HOME_PATH_REL."/", $datei);
				//$url = HOME_PATH.$tmp[1];
				$url = $tmp[1];
				if($catUrl != ""){
					$catUrl .= ",";
				}
				$catUrl .= $url;
			} 
			$endURL = HOME_PATH."min/?f=".$catUrl;
			echo '<link rel="stylesheet" type="text/css" href="'.$endURL.'"/>';
		}
	// JS
	echo "<!-- JS -->";
		// Root
		echo "<!-- ROOT -->";
		$alle1 = glob(DR.HOME_PATH_REL.'/js/*.js');
		$alle2 = glob(DR.HOME_PATH_REL.'/js/*/*.js');
		$alle = array_merge($alle1,$alle2);
		//p($alle);
		if(count($alle)>0){
			$catUrl = "";
			foreach($alle as $datei){
// 				$tmp = explode(DR.HOME_PATH_REL."/", $datei);
// 				$url = HOME_PATH.$tmp[1];
				$tmp = explode(DR.HOME_PATH_REL."/", $datei);
				//$url = HOME_PATH.$tmp[1];
				$url = $tmp[1];
				if($catUrl != ""){
					$catUrl .= ",";
				}
				$catUrl .= $url;
			}
			$endURL = HOME_PATH."min/?f=".$catUrl;
			echo '<script type="text/javascript" src="'.$endURL.'"></script>';
		}
?>

    <script type="text/javascript">

$(document).ready(function() {
		// Matchmaking 
		//l("Matchmaking Start");
		/** $.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "queue",
				mode : "inQueue"
			},
			success : function(result) {
				l("cleanBansOfPlayer success");
				l(result);
				// Wenn in Queue  -> Matchmaking machen
				if (result) {
					var queueType = $.cookie("queueType");
					l("hier! QUeuue");
					switch(queueType){
					case "1":
							
						break;
					case "8":
						break;
					case "9":
						break;
					}
				}

			}
		}); 
		l("Matchmaking End");*/
});

</script>