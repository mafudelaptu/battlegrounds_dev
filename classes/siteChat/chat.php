<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Chat{

	private $fileDirectory = "chat/";
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function handleChat($function, $inputState, $text, $fileName, $inputMessage, $smarty){
		$log = array();
		//echo "asdasda";
		$userName = $_SESSION['user']['name'];
		$avatar = $_SESSION['user']['avatar'];

		switch($function) {
			## GET STATE ##
			case('getState'):
				//echo $fileName;
				if(file_exists($fileName)){
					$lines = file($fileName);
					//echo "asdasdasdasda";
				}
				//              $log['state'] = count($lines);
				$log['state'] = 0;
				break;

				## UPDATE ##
			case('update'):
				$state = $inputState;
				if(file_exists($fileName)){
					$lines = file($fileName);
				}
				$count =  count($lines);
				if($state == $count){
					$log['state'] = $inputState;
					$log['text'] = false;

				}
				else{
					$text= array();

					$log['state'] = $state + count($lines) - $state;
					foreach ($lines as $line_num => $line)
					{
						if($line_num >= $state){
							//$text[] =  $line = str_replace("\n", "", $line);
							$line = str_replace("\n", "", $line);
							$tmp = explode("|||", $line);

							$userName = $tmp[0];
							$avatar = $tmp[1];
							$time = $tmp[2];
							$message = $tmp[3];

							$smarty->assign("message", $message);
							$smarty->assign("userName", $userName);
							$smarty->assign("userAvatar", $avatar);
							$smarty->assign("time", $time);
							$chatMessage = $smarty->fetch("prototypes/chatMessage.tpl");
							$text[] = $chatMessage;
						}

					}
					$log['text'] = $text;
				}

				break;

				## SEND ##
			case('send'):
				//echo "MM:".$inputMessage;
				$nickname = htmlentities(strip_tags($nickName));
				$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
				$message = htmlentities(strip_tags($inputMessage));
				if(($message) != "\n"){

					if(preg_match($reg_exUrl, $message, $url)) {
						$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
					}
					fwrite(fopen($fileName, 'a'), $userName . "|||".$avatar."|||".time()."|||". $message = str_replace("\n", " ", $message) . "\n");
				}
				break;

		}

		return $log;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function createChatFile($matchID, $bereich="match", $createdMatchID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start createChatFile <br>\n";
		switch($bereich){
			case "match":
				if($matchID > 0){
					$my_file = $this->fileDirectory.$bereich."/match_".(int) $matchID.".txt";
					if(!file_exists($my_file)){
						$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly
					}
					else{
						// Datei schon vorhanden
					}

					$ret['status'] = true;
				}
				else{

					$ret['status'] = "MatchID = 0";
				}
				break;
			case "singleQueue":
				$my_file = $this->fileDirectory.$bereich."/queue_".date("Y_n_j").".txt";
				if(!file_exists($my_file)){
					$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly
				}
				else{
					// Datei schon vorhanden
				}

				$ret['status'] = true;
				break;
			case "event":
				$my_file = $this->fileDirectory.$bereich."/event_".$matchID."_".$createdMatchID.".txt";
				if(!file_exists($my_file)){
					$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly
				}
				else{
					// Datei schon vorhanden
				}

				$ret['status'] = true;
				break;
			case "shoutBox":
				$my_file = $this->fileDirectory.$bereich."/shoutBox_".date("Y_n_j").".txt";
				if(!file_exists($my_file)){
					$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly
				}
				else{
					// Datei schon vorhanden
				}

				$ret['status'] = true;
				break;
		}


		$ret['debug'] .= "End createChatFile <br>\n";

		return $ret;

	}

}
?>