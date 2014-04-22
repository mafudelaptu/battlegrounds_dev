<div class="row">
	<div class="col-sm-6">
		@include("home.home.mm.stats")
	</div>
	<div class="col-sm-6">
		@include("home.home.mm.queueStats", array("data"=>$queueStats5vs5Single))
	</div>
</div>
<hr>
<div class="row">
	<div class="col-sm-6">
		@include("home.home.mm.matchmodes")
	</div>
	<div class="col-sm-6">
		@include("home.home.mm.joinButtons")
	</div>
</div>