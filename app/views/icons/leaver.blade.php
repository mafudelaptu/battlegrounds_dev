@if($leaver)
<?php 
	if(!isset($width)){
		$width = 20;
	}
 ?>
	{{HTML::image("img/icons/leaver.png", "Left the Match", array("class"=>"t", "title"=>"Left the Match", "width"=>$width));}}
@endif

