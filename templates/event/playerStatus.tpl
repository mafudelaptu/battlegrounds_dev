<h3>Your Status</h3>
		<div class="row-fluid">
			{if $data|count > 0 && $data|is_array}
				{foreach key=k item=v from=$data name=data_array}
				{if $v.mdData != ""}
				<div class="span6">
						<h4 class="muted">< Round {$smarty.foreach.data_array.iteration} ></h4>
						<div>
							<strong>MatchID: </strong> {$v.matchesData.MatchID}
						</div>
						<div>
							<strong>You are in Team: </strong> {$v.playerTeam.EventTeamID}
						</div>
						<div>
							<strong>You have to Play vs. Team: </strong> {$v.opponentTeam}
						</div>
						<div>
							<strong>Status: </strong> <span class="text-error">{$v.status}</span>
						</div>
					</div>
				{/if}
				{/foreach}
			{/if}

		</div>