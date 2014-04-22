@if(!empty($lastEvents) && count($lastEvents) > 0)
<div class="box">
	<div class="box_title">
		
Last Events
	</div>
	<div class="box_content">
		

@foreach($lastEvents as $k=>$v)
<div class="media">
	<div class="pull-left t upcomingEventTeaser"
		title="{{$v->matchmode}}-Tournament">
		@include("icons.eventtype", array("matchmode_id"=>$v->matchmode_id))
	</div>
	<div class="media-body">
		<h4 class="media-heading">{{$v->name}} #{{$v->event_id}}</h4>
		{{$v->description}}

		<div class="row">
			<div class="col-sm-6">
				<dl>
					<dt style="text-align: left">Prize for Winners</dt>
					<dd>
						@include("icons.prizetype", array("prizetype_id"=>$nextEvent->prizetype_id, "prizecount"=>$nextEvent->prizecount, "prize"=>$nextEvent->prize, "width"=>"100"))<br>
						<div class="pull-left">{{$v->prizecount}}x <strong>{{$v->prize}}</strong> 
						</div>
					</dd>
				</dl>
			</div>
			<div class="col-sm-6">
				<dl>
					<dt style="text-align: left">Requirements:</dt>
					<dd>
						@if( $v->pointReq > 0 || $v->skillbracketReq > 0) 
							@if($v->pointReq > 0)
							<div class="pull-left">
								>= <span class="label t" title="minimum points">{{$v->pointReq}}</span>
							</div>
							@endif 
							@if( $v->skillbracketReq > 0)
							<div class="pull-left t"
								title="minimum League: {$v.LeagueReq}">{include
								file="prototypes/medalIcon.tpl"
								leagueNameSimple=$v.LeagueReq}</div>
							@endif 
						@else Free for all 
						@endif

					</dd>
				</dl>
			</div>
		</div>

		<h4>Details</h4>
		@include("events.eventDetails", array("data"=>$v))
		 @if( count($v->createdEventsData) > 0 && !empty($v->createdEventsData)) 
		 <?php //dd($v->createdEventsData[0]) ?>
			 @foreach($v->createdEventsData as $kc=>$vc)
			<button class="btn btn-success pull-right goToSubEventButton" data-value="{{$v->event_id}}"
				data-ce="{{$vc->id}}">Go to Sub-Event
				#{{$vc->id}}</button>
			@endforeach 
		@endif 

		@if( !empty($v->eventSubData) && count($v->eventSubData) > 0)
		<div class="clearer"></div>
		<hr>
		<div class="btn-link" data-toggle="collapse"
			data-target="#lastEventsignedInPlayers{{$k}}">Signed-in
			Players ({{$v->eventSubsCount}})</div>

		<div
			id="lastEventsignedInPlayers{{$k}}"
			class="collapse">
			@include("events.signedInPlayers", array("data"=>$v->eventSubData, "event"=>$v))
		</div>
		@endif

	</div>
</div>
@endforeach 
</div>
</div>
@endif
<br>
<br>

