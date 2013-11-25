
<div class="row-fluid" style="border-bottom:1px solid #eee; margin:0 0 10px 0px;">
	<div class="span1">
		<a href="profile.php?ID={$userSteamID}" target="_blank"><img src="{$userAvatar}" alt="Avatar of {$userName}" vspace="6"></a>
	</div>
	<div class="span11">
		<div>
			<a href="profile.php?ID={$userSteamID}" target="_blank"><small><strong>{$userName}</strong></small></a><span class="muted pull-right"><small style="font-size:9px">{$time|date_format:"%d.%m.%Y - %H:%M:%S"}</small></span>
		</div>
		<div style="margin:0 0 10px 0;">{$message}</div>
	</div>
</div>