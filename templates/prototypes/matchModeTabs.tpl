<ul class="nav nav-tabs">
	<li class="active"><a href="#{$idTabs}0" data-toggle="tab" onclick="loadLadderDataTable('0', '{$section}', '{$matchTypeID}')">General</a></li>
		{if $matchModes|count > 0 && $matchModes|is_array}
			{foreach key=k item=v from=$matchModes name=matchModes_array}
				<li class=""><a href="#{$idTabs}{$v.MatchModeID}" data-toggle="tab" onclick="loadLadderDataTable('{$v.MatchModeID}', '{$section}', '{$matchTypeID}')">{$v.Name}&nbsp;({$v.Shortcut})</a></li>
		    {/foreach}
		{/if}
</ul>