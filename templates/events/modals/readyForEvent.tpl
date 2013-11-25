<!-- Modal  Ready Match -->
{assign "secondsTillStart" {math equation="start - now" start=$nextEvent.StartTimestamp now=$smarty.now}}
<div id="readyForEvent" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="readyForEvent" aria-hidden="true">
  <div class="modal-header">
    <h3 id="readyForEventLabel">At {$nextEvent.StartTimestamp|date_format:"%H:%M"} starts an Event where you are signed-in to!</h3>
  </div>
  <div class="modal-body">
    <p>Are you ready to play in {$secondsTillStart|date_format:"%M"} minutes for propably 2 hours?</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" onclick="setEventReadyStatus({$nextEvent.EventID}, -1)">I have no time, decline Event</button>
    <button id="myModalReadyMatchAcceptButton" class="btn btn-success" onclick="setEventReadyStatus({$nextEvent.EventID}, 1)">I have enought time and ready to play the Event!</button>
  </div>
</div>
