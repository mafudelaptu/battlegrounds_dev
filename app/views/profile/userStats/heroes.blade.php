@if($replayUploadActive=="Dota2")
<div id="heroesPlayed" class="box mb_0 h_247">
<div class="customH2 box_title">HEROES<span>PLAYED</span> @include("icons.info", array("title"=>"data is comming from all uploaded replays on this site"))</div>
<div class="box_content">
@if(!empty($data) && count($data) > 0)
<div class="row" align="center" style="padding-bottom:7px;">
	@foreach($data as $k=>$v)
		@if( $k==0)
			@if( count($data)==1)
				<?php 
					$offset = "col-sm-offset-4";
				 ?>
			
			@elseif(count($data)==2) 
			<?php 
					$offset = "col-sm-offset-3";
				 ?>
			@elseif(count($data)==3) 
			<?php 
					$offset = "col-sm-offset-2";
				 ?>
			@elseif(count($data)==4) 
			<?php 
					$offset = "col-sm-offset-1";
				 ?>
			@elseif(count($data)==5) 
			<?php 
					$offset = "";
				 ?>
			@endif
		@else
			<?php 
					$offset = "";
				 ?>
		@endif
		<div class="col-sm-4 mt_5">
			<div class="t" title="{{$v->heroname}}"><img src="{{$v->src}}" alt="{{$v->heroname}}" width="50"/></div>
			<div align="center">{{$v->value}}%</div>
		</div>
	@endforeach
	</div>
@else
	<div class="alert alert-warning">no uploaded replays yet!</div>
@endif
</div>
</div>
@endif

