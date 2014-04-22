@if(!empty($data) && count($data) > 0)

	@foreach($data as $k => $v)
		@include("prototypes.chat.chatUser", array("data"=>$v, "chatname"=>$chatname, "isSteamGame"=>$isSteamGame))
	@endforeach
@else
	<div class="alert alert-warning">
		no users in chat
	</div>
@endif