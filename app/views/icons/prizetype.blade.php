<?php 
	if(!isset($width)){
		$width = "100";
	}
 ?>
{{HTML::image('img/prizetype/prizeTypeID_'.$prizetype_id.'.png', $prizecount."x ".$prize, array("class"=>"t", "title"=>$prizecount."x ".$prize, "width"=>$width))}}
