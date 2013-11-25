<h3>
	<button type="button" class="btn btn-mini" data-toggle="collapse"
		data-target="#mRGeneralDataArea">+/-</button>
	General Match Data
</h3>
<div id="mRGeneralDataArea" class="collapse in">
	<div class="row-fluid">
		<div class="span3">
			<h4>general</h4>
			<div class="row-fluid">
				<div class="span3">
					<div>
						<strong>Mode</strong>
					</div>
					<div>
						<div class="badge badge-info t" title="{$data.MatchMode} ">{$data.MatchModeShortcut}</div>
					</div>
				</div>
				<div class="span3">
					<div>
						<strong>Region</strong>
					</div>
					<div>
						<div class="badge badge-important t" title="{$data.Region}">{$data.RegionShortcut}</div>
					</div>
				</div>
				<div class="span6">
					<div>
						<strong>Created at:</strong>
					</div>
					<div>
						<div class="badge t" title="MatchID">{$data.TimestampCreated|date_format:'%Y-%m-%d<br>%H:%M:%S'}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="span3">
			<h4>Leaver - Votes</h4>
			{if $leaverVotes|is_array && $leaverVotes|count > 0}
			
				
					{foreach key=k item=v from=$leaverVotes name=data_array}
					<div class="row-fluid">
						<div class="span3">
							<img alt="Avatar" src="{$v.userData.Avatar}" width="25" height="25">
							{include "prototypes/creditBasedName.tpl" name=$v.userData.Name playerSteamID=$k}
						</div>
						<div class="span1">{$leaverVotes.countArray[$k]}</div>
						<div class="span8" style="overflow: auto; max-height: 75px;">
							{if $v.VotedByPlayers|is_array && $v.VotedByPlayers|count > 0}
								{foreach key=kk item=vv from=$v.VotedByPlayers name=data_array}
									<div>
										{if $vv.Avatar != ""}
											<img alt="Avatar" src="{$vv.Avatar}" width="25" height="25">
											{include "prototypes/creditBasedName.tpl" name=$vv.Name playerSteamID=$vv.VotedBy}
										{else}
											Admin / deleted User
										{/if}
										 <br><small class="muted small">at {$vv.Timestamp|date_format:'%Y-%m-%d %H:%M:%S'}</small>
									</div>
								{/foreach}
							{/if}
						</div>
					</div>
					{/foreach}
				
				{else}
					<div>no Votes</div>
				{/if}
		</div>
		<div class="span3">
		<h4>Cancel Leaver - Votes</h4>
			{if $cancelLeaverVotes|is_array && $cancelLeaverVotes|count > 0}
			
				
					{foreach key=k item=v from=$cancelLeaverVotes name=data_array}
					<div class="row-fluid">
						<div class="span3">
							<img alt="Avatar" src="{$v.userData.Avatar}" width="25" height="25">
							{include "prototypes/creditBasedName.tpl" name=$v.userData.Name playerSteamID=$k}
						</div>
						<div class="span1">{$cancelLeaverVotes.countArray[$k]}</div>
						<div class="span8" style="overflow: auto; max-height: 75px;">
							{if $v.VotedByPlayers|is_array && $v.VotedByPlayers|count > 0}
								{foreach key=kk item=vv from=$v.VotedByPlayers name=data_array}
									<div>
										{if $vv.Avatar != ""}
											<img alt="Avatar" src="{$vv.Avatar}" width="25" height="25">
											{include "prototypes/creditBasedName.tpl" name=$vv.Name playerSteamID=$vv.VotedBy}
										{else}
											Admin / deleted User
										{/if}
										 <br><small class="muted small">at {$vv.Timestamp|date_format:'%Y-%m-%d %H:%M:%S'}</small>	
									</div>
								{/foreach}
							{/if}
						</div>
					</div>
					{/foreach}
					
				{else}
					<div>no Votes</div>
				{/if}
			
		</div>
		<div class="span3">
		<h4>Screenshots</h4>
			{if $screenshots|is_array && $screenshots|count > 0}
				{foreach key=k item=v from=$screenshots name=data_array}
					<a href="{$v}" target="_blank"><img src="{$v}" width="100"></a>
				{/foreach}
			{else}
				<div>no screenshots</div>
			{/if}
		</div>
	</div>
</div>
