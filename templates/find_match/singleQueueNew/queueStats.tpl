<div class="media">
	<div align="center" class="pull-left" style="width: 122px;">
		<span class="text-info"><i class="icon-bar-chart icon-4x"></i></span>
	</div>

	<div class="media-body">
		<div class="row-fluid">
			<div class="span6">
				<div id="">
					<h4 class="media-heading">Matchmode Counts</h4>
					{if $matchModeStats|count > 0 && $matchModeStats|is_array}
						{foreach key=k item=v from=$matchModeStats name=mmStats}
							<div class="span5 t" align="center" title="{$v.Count} Players are in {$v.Shortcut}-Queue">
								<strong>{$v.MatchModeName|truncate:15:"...":true}<br><div class="badge badge-info">{$v.Shortcut}</div></strong>
								<div class="label">{$v.Count}</div>
							</div>
					    {/foreach}
					{/if}
				</div>
			</div>
			<div class="span6">
				<div id="">
					<h4 class="media-heading">Region Counts</h4>
					{if $regionStats|count > 0 && $regionStats|is_array}
						{foreach key=k item=v from=$regionStats name=rStats}
							<div class="span5 t" align="center" title="{$v.Count} Players are with Region: {$v.RegionName} in the Queue">
								<strong>{$v.RegionName|truncate:10:"...":true}<br><div class="badge badge-important">{$v.Shortcut}</div></strong>
								<div class="label">{$v.Count}</div>
							</div>
					    {/foreach}
					{/if}
				</div>
			</div>
		</div>
		<div class="clearer"></div>

	</div>
</div>