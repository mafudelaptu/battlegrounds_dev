<h4>
			Queue-Stats <small class="muted">Best Queue-Time: 19:00 -
				22:30 CET</small>
		</h4>
		<div class="row-fluid">
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-group icon-3x" style="color: #3a87ad"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$threeVsThreeQueueCount}</div>
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
							{$threeVsThreeQueueInMatchCount} {if $openMatches3vs3|count > 0 &&
							$openMatches3vs3|is_array} <a
								href="openMatches.php?guest=true&MTID=9">(live
								{$openMatches3vs3|count})</a> {/if}
						</div>
						<div class="statsDesc">Players in Match</div>
					</div>
				</div>

			</div>
			<div class="span3">
				{if $matchModeStatsMaxMM3vs3 != ""}
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-cog icon-3x" style="color: #f89406"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$matchModeStatsMaxMM3vs3}
							({$matchModeStatsMaxMMCount3vs3})</div>
						<div class="statsDesc">Popular Matchmode now</div>
					</div>
				</div>
				{/if}
			</div>
			<div class="span3">
				{if $regionStatsMaxR3vs3 != ""}
				<div class="row-fluid">
					<div class="span3" style="line-height: 45px;" align="center">
						<i class="icon-globe  icon-3x" style="color: #468847"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$regionStatsMaxR3vs3}
							({$regionStatsRCount3vs3})</div>
						<div class="statsDesc">Popular Region now</div>
					</div>
				</div>
				{/if}
			</div>
		</div>