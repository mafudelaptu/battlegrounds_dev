@section("content")
	<h1>{{$heading}}</h1>
	<div class="row">
	
	<div class="col-sm-8">
		{{-- Upcoming Event --}}
		@include("events.nextEvent")
		
		{{-- Last Events --}}
		@include("events.lastEvents")
	</div>
	<div class="col-sm-4">
		
		{{-- Calendar --}}
		@include("events.info.calendar")
		
		{{-- Stats--}}
		@include("events.info.stats")
		
		{{-- Last Event Winners --}}
		@include("events.info.lastEventWinners")
		
		{{-- Most Event Wins--}}
		@include("events.info.mostEventWins")
	</div>
</div>
@stop