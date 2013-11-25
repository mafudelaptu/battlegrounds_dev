<!-- <img src="img/find_match/chat.jpg" height="33"> -->
<h4 class="blackH4">
<i class="icon-comment"></i> N-GAGE<green>CHAT</green>
</h4>
<div>
<div id="chat-{$chatID}-wrap">
	<div id="chat-{$chatID}-area" style="overflow: auto; max-height: 200px;"></div>
</div>
<br />

<form id="send-message-area">
	<div class="row-fluid">
		<div class="span1">
			<img src="{$userAvatar}" alt="Avatar">
		</div>
		<div class="span11">
			<textarea id="postComment-{$chatID}" maxlength='300'
				style="width: 90%; padding-right: 8px;"></textarea>
			<div class="muted pull-right">
				<small>Hit "Enter" to submit your message!</small>
			</div>
		</div>
	</div>
</form>
</div>