{if $matchChat}
	{assign "mS" $matchSubmitted}
	{assign "iPIM" $isPlayerInMatch}
{/if}

<div class="page-header">
	<h2><i class="icon-comment"></i>&nbsp;Chat</h2>
</div>
<div>
<div id="chat-{$chatID}-wrap">
	{if $maxHeight!=""}
		{assign "maxHeightValue" $maxHeight}
{else}
	{assign "maxHeightValue" "200"}
{/if}
	<div id="chat-{$chatID}-area" style="overflow: auto; max-height: {$maxHeightValue}px;"></div>
</div>
<br />
{if $isPermaBanned != true}
	{if !$matchChat}
	<form id="send-message-area">
		<div class="row-fluid">
			<div class="span1">
				<img src="{$userAvatar}" alt="Avatar">
			</div>
			<div class="span11">
				<textarea id="postComment-{$chatID}" maxlength='300'
					style="width: 100%; padding-right: 8px;"></textarea>
				<div class="muted pull-right">
					<small>Hit "Enter" to submit your message!</small>
				</div>
			</div>
		</div>
	</form>
	{else}
		{if !$mS && $iPIM}
			<form id="send-message-area">
				<div class="row-fluid">
					<div class="span1">
						<img src="{$userAvatar}" alt="Avatar">
					</div>
					<div class="span11">
						<textarea id="postComment-{$chatID}" maxlength='300'
							style="width: 100%; padding-right: 8px;"></textarea>
						<div class="muted pull-right">
							<small>Hit "Enter" to submit your message!</small>
						</div>
					</div>
				</div>
			</form>
		{/if}
	{/if}
{/if}

</div>