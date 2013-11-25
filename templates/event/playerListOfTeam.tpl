{if $data|count > 0 && $data|is_array}
		{foreach key=k item=v from=$data name=playerList_array}
			{if $v.SteamID == $smarty.session.user.steamID}
				{assign "style" "background-color:#d9edf7"}
			{else}
				{assign "style" ""}
			{/if}
			{if $v.Name|count_characters >= 15}
				{assign "titleClass" "t"}
			{else}
				{assign "titleClass" ""}
			{/if}
				<div style="width:200px;{$style}">
					<img src="{$v.Avatar}">&nbsp;<span class="{$titleClass}" title="{$v.Name}"><a href="profile.php?ID={$v.SteamID}" target="_blank">
					{$v.Name|truncate:15:"...":true}</a></span>
						<div class="badge {$rdy} pull-right" style="margin-top: 7px;">
							{$v.Elo}
							
					</div>
				</div>	
	    {/foreach}
{else}
	???
{/if}