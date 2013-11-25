<!-- Modal -->
<div id="editUserPointsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editUserPointsModal" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Ã—</button>
    <h3 id="editUserPointsModalLabel">Edit User Points For Match</h3>
  </div>
  <div class="modal-body">
  <div id="mRSteamIDIdentifyer" class="hide">{* steamID befüllt durch mREditPointsChanges() *}</div>
  <button type="button" class="btn btn-success" onclick="addUserPointFormsForModal()"><i class="icon-plus-sign"></i></button>
  <br>
  <br>
   	<div id="mREditUserPointsContent">
   		{* gefüllt durch Ajax - admin/editMatchResult/editUserPoints.tpl *}
   	</div>
   	
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button class="btn btn-success" data-dismiss="modal" aria-hidden="true" onclick="mRSaveUserPointsChanges()">Save Changes</button>
  </div>
</div>
</div>
