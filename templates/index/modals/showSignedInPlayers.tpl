<div id="showSignedInPlayersModal" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="showSignedInPlayersModal"
	aria-hidden="true">
	<div class="modal-header">
		<h3 id="showSignedInPlayersModalLabel" align="center">Already
			signed-in Players in Event</h3>
	</div>
	<div class="modal-body" align="center">{include
		"index/loggedIn/event/signedInPlayerList.tpl" data=$eventSubsData}</div>
	<!-- ModalBody-End -->
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Back</button>
	</div>
</div>
</div>
