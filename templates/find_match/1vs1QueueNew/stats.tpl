<h4>
			Queue-Stats 
			{* <small class="muted">Best Queue-Time: 19:00 -
				22:30 CET</small> *}
		</h4>
		<div class="row-fluid">
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-group icon-3x" style="color: #3a87ad"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$oneVsOneQueueCount}</div>
						<div class="statsDesc">Players in Queue</div>
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-gamepad icon-3x" style="color: #b94a48"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">
							{* $oneVsOneQueueInMatchCount *} 
							{if $openMatches1vs1|count > 0 && $openMatches1vs1|is_array} 
							<a href="openMatches.php?guest=true&MTID=8">{$openMatches1vs1|count} (live)</a> 
							{else}
							0
							{/if}
						</div>
						<div class="statsDesc">Players in Match</div>
					</div>
				</div>

			</div>
			<div class="span3">
				{if $matchModeStatsMaxMM1vs1 != ""}
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-cog icon-3x" style="color: #f89406"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$matchModeStatsMaxMM1vs1}
							({$matchModeStatsMaxMMCount1vs1})</div>
						<div class="statsDesc">Popular Matchmode now</div>
					</div>
				</div>
				{/if}
			</div>
			<div class="span3">
				{if $regionStatsMaxR1vs1 != ""}
				<div class="row-fluid">
					<div class="span3" style="line-height: 45px;" align="center">
						<i class="icon-globe  icon-3x" style="color: #468847"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$regionStatsMaxR1vs1}
							({$regionStatsRCount1vs1})</div>
						<div class="statsDesc">Popular Region now</div>
					</div>
				</div>
				{/if}
			</div>
		</div>