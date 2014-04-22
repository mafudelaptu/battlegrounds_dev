<div class="text-center">
@if($status == "eventNotReachedEnoughPlayers")
	<h3>Sorry</h3>
	<div class="alert alert-danger">
	<p>
		the Event(#{{$event_id}}) didn't reached enough players for starting the Event. The Event got canceled.
	</p>
	</div>
@endif
@if($status == "declined")
	<h3>Sorry</h3>
	<div class="alert alert-danger">
	<p>
		Sorry, you signed-in too late to the Event(#{{$event_id}}). Enough other Players were found before you signed-in.
	</p>
	</div>
@endif

@if($status == "tooLate")
	<h3>Sorry</h3>
	<div class="alert alert-danger">
	<p>
		you don't confirmed that you are ready to play the Event(#{{$event_id}}) or declined it. Next time be prepared 10 minutes before Event start.
	</p>
	</div>
@endif
@if($status == "inEvent")
	<div class="modal-header">
    <h3 id="myModalLabel">Event created!</h3>
  </div>
  <div class="modal-body">
    <p>Event successfully created. You are in Sub-Event: {{$created_event_id}}.</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal"
	aria-hidden="true">
    	hide message
    </button>
    <button id="readyMatchAcceptButton" class="btn btn-success" onclick="window.location.href = '{{$href}}';">Go to Event!</button>
  </div>
@endif

</div>