<?php 
if(!isset($disableChat)){
	$disableChat = false;
}
if(!isset($height)){
	$height = "200px";
}

$heightUsers = (int) ($height -29)."px";

?>
@if(!isset($noHeader))
<div class="page-header">
	<h2><i class="fa fa-comment"></i>&nbsp;Chat</h2>
</div>
@endif

<div id="{{$chatname}}">
	<div class="row" style="height: {{$height}};">
		@if($disableChat)
		<div class="col-sm-12">
			<div class="conversation" style="max-height: {{$height}}; height: {{$height}};">

			</div>
		</div>

		@else
		<div class="col-sm-9">
			<div class="conversation" style="max-height: {{$height}}; height: {{$height}};">
				<div align="center" class="showPreviousMessagesBox">
					<button class="btn btn-link showPreviousMessages" data-chat="{{$chatname}}">show previous messages</button>
				</div>
			</div>
		</div>
		<div class="col-sm-3" style="max-height: {{$height}}; height: {{$height}};">
			<div class="count_chatusers text-center">
				Users in Chat <span class="count">0</span><i class="fa fa-refresh t btn btn-link userlistRefreshButton" title="refresh userlist"></i> 
			</div>
			<div class="chatusers" style="max-height: {{$heightUsers}}; height: {{$heightUsers}};">

			</div>
		</div>
		@endif

	</div>

	@if(!$disableChat)
	<div class="row pt_10">
		<div class="col-sm-9">
			<div class="input-group chat-input">
				<span class="input-group-addon allChat">
					All Chat:
				</span>
				<input id="send-data-{{$chatname}}" maxlength='100' placeholder='Message' type="text" class="chat-message-input">
			</div>
			<form class="form-inline showMenu" role="form">
				<div class="form-group">
					show:&nbsp;
				</div>
				<div class="radio">
					<label>
						<input type="radio" name="{{$chatname}}ShowMenu" id="{{$chatname}}ShowMenuFilterAll" value="all" checked> All
					</label>
				</div>&nbsp;
				<div class="radio">
					<label>
						<input type="radio" name="{{$chatname}}ShowMenu"  id="{{$chatname}}ShowMenuFilterAllChat" value="allchat"> just All-Chat
					</label>
				</div>&nbsp;
				<div class="radio">
					<label>
						<input type="radio" name="{{$chatname}}ShowMenu"  id="{{$chatname}}ShowMenuFilterWhisperChat" value="whisper"> just Whisper-Chat
					</label>
				</div>
			</form>
		</div>
		<div class="col-sm-3 lh_58">
			<button class="button btn-block" id="send-{{$chatname}}" onclick="sendMessage('{{$chatname}}')">Send</button>
		</div>
	</div>
	@endif
</div>