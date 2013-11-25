<style type="text/css">
	.selectpicker{
		width: 100px;
	}
</style>
<!-- <h3>Point-History</h3> -->
<!-- <img src="img/profile/pointhistory.jpg"> -->
<div class="blackH2">Point<green>History</green></div>
<div class="row-fluid">
	<div class="span4">
		Matchtype:<select class="selectpicker"
			onchange="drawEloHistoryChart()" id="eloHistoryMTSelect">
			{if $matchtypes|count > 0 && $matchtypes|is_array} {foreach key=k
			item=v from=$matchtypes name=matchtypes_array}
			<option value="{$v.MatchTypeID}">{$v.Name}</option> {/foreach} {/if}
		</select>
	</div>
	<div class="span4">
		Matchmode:<select class="selectpicker"
			onchange="drawEloHistoryChart()" id="eloHistoryMMSelect">
			{if $matchmodes|count > 0 && $matchmodes|is_array} 
			<option value="*">All Matchmodes</option> 
			{foreach key=k
			item=v from=$matchmodes name=matchmodes_array}
			<option value="{$v.MatchModeID}">{$v.Name}</option> {/foreach} {/if}
		</select>
	</div>
	<div class="span4">
	Count 
	<select class="selectpicker"
			onchange="drawEloHistoryChart()" id="eloHistoryCountSelect">
			
			<option value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="100">100</option>
			<option value="*">all Values</option>
		</select>
	</div>


</div>
<div id="eloHistoryChart"
	style="width: 100%; height: 300px; margin-top: 5px;">
	<!--     Chart -->
</div>