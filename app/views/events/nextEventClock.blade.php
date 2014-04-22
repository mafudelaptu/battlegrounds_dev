@if($state == "not_started_yet")
<dt style="text-align: left">Time till sign-in</dt>
<dd>
	<div id="eventClock" class="t" title="time left till sign-in" data-date="{{strtotime($event->start_submission_at)}}"></div>
</dd>
@elseif( $state=="sign_in_possible" || $state=="signed_in_in_signed_in_time")
<dt style="text-align: left">Possible Time to Sign-in till Event starts</dt>
<dd>
	<div id="eventClock" class="t" title="Possible Time to Sign-in till Event starts" data-date="{{strtotime($event->end_submission_at)}}"></div>
</dd>
@elseif( $state=="user_not_allowed_to_join" || $state == "event_started_user_not_rdy" || $state == "event_started_user_in_event" || $state == "event_started_user_not_signed_in")
<dt style="text-align: left">Event started</dt>
<dd>Signed-in Players are playing the Tournament</dd>
@elseif($state=="signed_in_after_signintime_before_start")
	<dt style="text-align: left">Event will be created soon</dt>
<dd></dd>
@endif
