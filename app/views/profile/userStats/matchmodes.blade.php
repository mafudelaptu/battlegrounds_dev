@if($replayUploadActive=="Dota2")
<div id="matchmodesPlayed" class="box">
<div class="customH2 box_title">MATCHMODES<span>PLAYED</span></div>
<div class="box_content p_10">
@if(!empty($data) && count($data) > 0)
<div class="row" align="center" style="padding-bottom:7px;">
	@foreach($data as $k=>$v)
	@if( $k==0)
	@if( count($data)==1)
	<?php 
	$offset = "col-sm-offset-5";
	?>
	
	@elseif(count($data)==2) 
	<?php 
	$offset = "col-sm-offset-4";
	?>
	@elseif(count($data)==3) 
	<?php 
	$offset = "col-sm-offset-3";
	?>
	@elseif(count($data)==4) 
	<?php 
	$offset = "col-sm-offset-2";
	?>
	@elseif(count($data)==5) 
	<?php 
	$offset = "col-sm-offset-1";
	?>
	@elseif(count($data)==6) 
	<?php 
	$offset = "";
	?>
	@endif
	@else
	<?php 
	$offset = "";
	?>
	@endif
	<div class="col-sm-2 {{$offset}}">
		<h4 class="t" title="{{$v->matchmode}}"><strong>{{$v->mm_shortcut}}</strong></h4>
		<div>{{$v->value}}%</div>
	</div>
	@endforeach
</div>
@else
<div class="alert alert-warning">no matches played yet!</div>
@endif
</div>
</div>
@endif

