@if(!$inMatch)
<div class="row">
	<div class="col-sm-6">
		<button type="button" class="button btn-block" id="join5vs5SingleButton">
			Play 5vs5
		</button>
		
	</div>
	<div class="col-sm-6">
		<button type="button" class="button btn-block" id="join1vs1Button">
			Play 1vs1
		</button>
	</div>
</div>

@else
@include("findMatch.inMatchError")
@endif