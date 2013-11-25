<div class="blackH2">
	Live<green>Streams</green>
</div>

<div style="background-color: #292929; color:#fff; padding:0 15px 5px">
{if $data|is_array && $data|count > 0}
	{if $data.featured|is_array && $data.featured|count > 0}
	<div align="center">OFFICIAL</div>
		{foreach key=k item=v from=$data.featured name=data_array}
			{include "index/loggedIn/streams/streamer_row.tpl" v= $v}
		{/foreach}
	{/if}
	{if $data.player|is_array && $data.player|count > 0}
	<div align="center">COMMUNITY</div>
		{foreach key=k item=v from=$data.player name=data_array}
			{include "index/loggedIn/streams/streamer_row.tpl" v= $v}
		{/foreach}
	{/if}
{else}
	<div align="center">no Live-Streams now</div>
{/if}
</div>