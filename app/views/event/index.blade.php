@section("content")
<div class="page-header">
	<h1>
		Event #{{$event_id}}: {{$eventData->name}} <small>Sub-EventID: {{$created_event_id}}</small>
	</h1>
</div>

<div class="row">
	<div class="col-sm-4">
		<div class="box">
			<!-- Default panel contents -->
			<div class="box_title">Eventdetails</div>
			<div class="box_content">
				{{$eventData->description}}
			</div>
			<!-- Table -->
			<table class="table">
				<tr>
					<td>Started at:</td>
					<td>{{$eventData->start_at}}</td>
				</tr>
				<tr>
					<td>Tournamenttype:</td>
					<td><span class="badge badge-warning t" title="{{$eventData->tournamenttype}}">{{$eventData->tt_shortcut}}</span></td>
				</tr>
				<tr>
					<td>Matchmode::</td>
					<td><span class="badge badge-info t" title="{{$eventData->matchmode}}">{{$eventData->mm_shortcut}}</span></td>
				</tr>
				<tr>
					<td>Region:</td>
					<td><span class="badge badge-danger t" title="{{$eventData->region}}">{{$eventData->r_shortcut}}</span></td>
				</tr>
				<tr>
					<td>Total Players:</td>
					<td><span class="label label-default">{{$eventData->min_submissions}}</span></td>
				</tr>

			</table>
		</div>
	</div>
	@if(!empty($playerStatus) && count($playerStatus) > 0) 
	<div class="col-sm-8">
		@include("event.playerStatus", array("data"=>$playerStatus))
	</div>
	@else
	<div class="col-sm-4">
		@if(!empty($playerStatus) && count($playerStatus) > 0) 
		<h3>Your Team</h3>
		@include("event.teamData", array("data"=>$teamOfPlayer))
		@else
		@if($createdEventData->canceled == 1) 
		<div class="alert alert-danger">Event got canceled!</div>
		@elseif($createdEventData->team_won_id == 0)
		<div class="alert alert-info">Event in progress</div>
		@else
		<h3>Winner</h3>
		@include("event.teamData", array("data"=>$winnerTeam))
		@endif
		@endif
	</div>
	<div class="col-sm-4">
		<script>
		$(function() {
			if (document.URL.indexOf("/event/") >= 0) {

				var json = '<?php echo $bracketJson;?>';
				var obj = $.parseJSON( json );
				l("jsonBracket");
				l(obj);
				$("#bracket").bracket({
					skipConsolationRound: true,
					init: obj
				});
			}
		});
		</script>
		<h3>Bracket</h3>
		<div id="bracket">
			
		</div>
		<div>
			<h3>Prize for Winners</h3>

			@include("icons.prizetype", array("prizetype_id"=>$eventData->prizetype_id, "prizecount"=>$eventData->prizecount, "prize"=>$eventData->prize))
			<div>
				{{$eventData->prizecount}}x <strong>{{$eventData->prize}}</strong>
			</div>
		</div>
	</div>
	@endif
</div>
@if($createdEventData->ended_at == "0000-00-00 00:00:00" || (!empty($playerStatus) && count($playerStatus) > 0) ) 
<div class="row">
	<div class="col-sm-4">
		<script>
		$(function() {
			if (document.URL.indexOf("/event/") >= 0) {

				var json = '<?php echo $bracketJson;?>';
				var obj = $.parseJSON( json );
				l(obj);
				$("#bracket").bracket({
					skipConsolationRound: true,
					init: obj
				});
			}
		});
		</script>
		<h3>Bracket</h3>
		<div id="bracket">
			
		</div>
	</div>
	<div class="col-sm-4">
		@if(!empty($playerStatus) && count($playerStatus) > 0) 
		<h3>Your Team</h3>
		@include("event.teamData", array("data"=>$teamOfPlayer))
		@else
		@if($createdEventData->canceled == 1) 
		<div class="alert alert-danger">Event got canceled!</div>
		@elseif($createdEventData->team_won_id == 0)
		<div class="alert alert-info">Event in progress</div>
		@else
		<h3>Winner</h3>
		@include("event.teamData", array("data"=>$winnerTeam))
		@endif
		@endif
	</div>
	<div class="col-sm-4">
		<div>
			<h3>Prize for Winners</h3>

			@include("icons.prizetype", array("prizetype_id"=>$eventData->prizetype_id, "prizecount"=>$eventData->prizecount, "prize"=>$eventData->prize))
			<div>
				{{$eventData->prizecount}}x <strong>{{$eventData->prize}}</strong>
			</div>
		</div>
	</div>
</div>
<div>
	@if(!empty($playerStatus) && count($playerStatus) > 0 && $createdEventData->ended_at == "0000-00-00 00:00:00")
			<?php $disableChat = null; ?>
		@else
			<?php $disableChat = true; ?>
		@endif
			@include("prototypes/chat/chatNew", array("chatname"=>"eventChat".$created_event_id, "disableChat"=>$disableChat))
	</div>
@endif
@stop