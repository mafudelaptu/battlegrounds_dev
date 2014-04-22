<div class="row">
	<div class="col-sm-2">
		{{HTML::image("img/findMatch/".$mt->id.".png", "matchtype", array("width"=>"100%"));}}
	</div>
	<div class="col-sm-10">
		@include("findMatch.queuestats", array("data" => $queueStats1vs1))
		<hr>
		@if(!$inMatch)
		<button type="button" class="btn btn-lg btn-block btn-success" id="join1vs1Button">
			<i class="fa fa-user"></i> vs <i class="fa fa-user"></i> Join
		</button>
		@else
		@include("findMatch.inMatchError")
		@endif
	</div>
	
</div>
