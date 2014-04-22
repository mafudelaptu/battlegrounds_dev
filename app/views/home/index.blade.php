@section("content")
<div class="box">
	<div class="box_title">{{$heading}}</div>
	<div class="box_content">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#news" data-toggle="tab">Home</a></li>
			<li><a href="#stream" data-toggle="tab">Stream</a></li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="news">
				@include("home.home.index")
			</div>
			<div class="tab-pane" id="stream">
				@include("home.stream.index")
			</div>
		</div>
	</div>
</div>

<div class="box">
	<div class="box_title">Chat</div>
	<div class="box_content">
	@include("prototypes.chat.chatNew", array("chatname"=>"homeChat", "noHeader"=>false, "height"=>"500px"))
	</div>
</div>
<div class="box">
	<div class="box_title">Events</div>
	<div class="box_content">
		@include("home.events.index", array("data"=>$events, "limit"=>3))
	</div>
</div>
<div class="box">
	<div class="box_title">Wall of Fame</div>
	<div class="box_content">
		<div class="row">
			<div class="col-sm-4">
				@include("home.wallOfFame.bestPlayers.index", array("data"=>$bestPlayers))
			</div>
			<div class="col-sm-4">
				@include("home.wallOfFame.lastMatches.index", array("data"=>$lastMatches))
			</div>
			<div class="col-sm-4">
				@include("home.wallOfFame.highestCredits.index", array("data"=>$highestCredits))
			</div>
		</div>
	</div>
</div>

@stop