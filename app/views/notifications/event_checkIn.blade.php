<div class="modal-header">
    <h3 id="myModalLabel">Event check-in</h3>
  </div>
  <div class="modal-body">
    <h4>At {{$start_at}} starts an Event(#{{$event_id}}) where you are signed-in to!</h4>
     <p>Are you ready to play at {{$start_at}} for propably 2 hours?</p>
  </div>
  <div class="modal-footer">
   <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" onclick="setEventReadyStatus({{$event_id}}, -1)">I have no time, leave Event</button>
    <button class="btn btn-success" data-dismiss="modal" aria-hidden="true" onclick="setEventReadyStatus({{$event_id}}, 1)">I have enought time and ready to play the Event!</button>
  </div>