<h2>Next Event</h2>
@if($nextEvent)

<div class="media">
	<div class="pull-left t upcomingEventTeaser"
		title="{{$nextEvent->matchmode}}-Tournament">
		@include("icons.eventtype", array("matchmode_id"=>$nextEvent->matchmode_id))	
	</div>
	<div class="media-body">
		<h4 class="media-heading">{{$nextEvent->name}} #{{$nextEvent->event_id}}</h4>
		{{$nextEvent->description}}
		<div class="row">
			<div class="col-sm-4">
				<dl>
					@if(new DateTime < $nextEvent->start_submission_at)
						<dt style="text-align: left">Time till sign-in</dt>
						<dd>
							<div id="eventClock" style="height: 40px; padding-top: 3px;" class="t" title="time left till sign-in"></div>
							
						@elseif( new DateTime >= $nextEvent->start_submission_at && new DateTime < $nextEvent->start_at)
							<dt style="text-align: left">Possible Time to Sign-in till Event starts</dt>
								<dd>
								<div id="eventClock" style="height: 40px; padding-top: 3px;" class="t" title="Possible Time to Sign-in till Event starts"></div>
								
						@elseif( new DateTime >= $nextEvent->start_at)
							<dt style="text-align: left">Event started</dt>
							<dd>Signed-in Players are playing the Tournament</dd>
						@endif
					</dd>
				</dl>
			</div>
			<div class="col-sm-4">
				<dl>
					<dt style="text-align: left">Prize for Winners</dt>
					<dd>
						@include("icons.prizetype", array("prizetype_id"=>$nextEvent->prizetype_id, "prizecount"=>$nextEvent->prizecount, "prize"=>$nextEvent->prize))
						<div class="pull-left">{{$nextEvent->prizecount}}x <strong>{{$nextEvent->prize}}</strong>
						</div>
					</dd>
				</dl>
			</div>
			<div class="col-sm-4">
				<dl>
					<dt style="text-align: left">Requirements:</dt>
					<dd>
						@if($nextEvent->pointReq > 0 || $nextEvent->skillbracketReq > 0)
							@if($nextEvent->pointReq > 0)
								<div class="pull-left">>= <span class="label t" title="minimum points">{$nextEvent->pointReq}</span></div>
							@endif
							@if($nextEvent->skillbracketReq > 0)
								<div class="pull-left t" title="minimum League: {{$nextEvent->skillbracketReq}}">

								</div>
							@endif
						@else
							Free for all
						@endif
						
					</dd>
				</dl>
			</div>
		</div>

		<h4>Details</h4>
		@include("events.eventDetails", array("data"=>$nextEvent))
		
		@include("events.joinEventButton", array("state"=>$nextEventState, "data"=>$nextEvent))

		@if(!empty($eventSubData) && count($eventSubData) > 0)
				<hr>
				<div class="btn-link" data-toggle="collapse"
					data-target="#signedInPlayers">Signed-in Players ({{$eventSubsCount}})</div>
		
				<div id="signedInPlayers" class="collapse in">
					@include("events.signedInPlayers", array("data"=>$eventSubData, "event"=>$nextEvent))
				</div>
		@endif
	</div>

</div>
@else
<div class="alert alert-warning">No upcoming Event.</div>
@endif