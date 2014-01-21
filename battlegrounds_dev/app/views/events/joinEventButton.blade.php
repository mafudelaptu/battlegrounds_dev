@if($state == "not_started_yet")
<div class="alert alert-info">
sign-in possible at: {{$data->start_submission_at}}
</div>
@elseif($state == "not_started_but_ended_sign_in_time")
<div class="alert alert-warning">
	the sign-in time ended for this event! You can't join this event anymore...
</div>

@elseif($state == "sign_in_possible")
<button class="btn btn-block btn-primary" id="joinEventButton"
data-time="{{strtotime($data->end_submission_at)}}"
data-value="{{$data->event_id}}" data-mtid="{{$data->matchtype_id}}">
join Event
</button>

@elseif($state == "signed_in_in_signed_in_time")
<button class="btn btn-block btn-danger"
id="signOutEventButton" data-time="{{strtotime($data->end_submission_at)}}"
data-value="{{$data->event_id}}">
Sign-Out of this Event
</button>

@elseif($state == "signed_in_after_signintime_before_start" || $state == "event_should_start_but_not_created")
<div class="alert alert-info">The Event will be created soon! Please wait a moment.</div>

@elseif($state == "event_started_user_in_event")
<button class="btn btn-block btn-success" id="goToEventButton"
data-value="{{$data->event_id}}" data-ce="{{$created_event_id}}">Go to Event!</button>

@elseif($state == "event_started_user_not_rdy")
<div class="alert alert-warning">You don't confirmed that you are ready to play the Event or declined it. Next time be prepared 10 minutes before Event start.</div>
@endif