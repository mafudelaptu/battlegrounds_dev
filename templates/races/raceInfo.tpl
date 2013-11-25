<style type="text/css">
.statsNumber {
	font-size: 18px;
	color: #333;
	font-weight: bold;
}

.statsDesc {
	font-size: 12px;
	color: #999;
}
</style>
<div class="blackH2">RACE<green>DETAILS</green></div>
<!-- <h3>Race-Details</h3> 
<h3 style="margin-bottom:0"><img src="img/races/racedetails.jpg"></h3>-->
<div class="row-fluid" style="background-color: #E9E9E9; padding-top:5px;">
	<div class="span3">
		<div class="row-fluid">
			<div class="span4" align="center">
				<i class="icon-time icon-3x" style="color: #aaa"></i>
			</div>
			<div class="span8">
				<div class="statsNumber">{$raceData.EndTimestamp|date_format:"%d.%m.%Y  %H:%M"} CET</div>
				<div class="statsDesc">Enddate of Race</div>
			</div>
		</div>

	</div>
	<div class="span3">
		<div class="row-fluid">
			<div class="span4" align="center">
				<i class="icon-group icon-3x" style="color: #3a87ad"></i>
			</div>
			<div class="span8">
				<div class="statsNumber">{$raceStats.ActivePlayersCount}</div>
				<div class="statsDesc">Active Players</div>
			</div>
		</div>
	</div>
	<div class="span3">
		<div class="row-fluid">
			<div class="span4" align="center">
				<i class="icon-trophy icon-3x" style="color: #f89406"></i>
			</div>
			<div class="span8">
				<div class="statsNumber">{$raceStats.WinnersCount}</div>
				<div class="statsDesc">
					Players in <br>Winning-List
				</div>
			</div>
		</div>
	</div>
	<div class="span3">
		<div class="row-fluid">
			<div class="span4" style="line-height: 45px;" align="center">
				<i class="icon-gamepad  icon-3x" style="color: #b94a48"></i>
			</div>
			<div class="span8">
				<div class="statsNumber">{$raceStats.MatchesPlayedCount}</div>
				<div class="statsDesc">
					Matches played <br>from start
				</div>
			</div>
		</div>

	</div>
</div>
<br>