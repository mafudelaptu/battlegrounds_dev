<!-- Modal -->
<style type="text/css">
	.group{
		width:45%;
		margin-right:5px;
		margin-bottom:5px;
		float:left;
	}
</style>
{* Auslesen ob erste ma oder nicht *}
{if $groupData|count > 0 && $groupData|is_array}
	{assign "active" "active"}
	{assign "activeCreate" ""}
{else}
	{assign "activeCreate" "active"}
	{assign "active" ""}
{/if}
<div id="selectDuoPartnerModal" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="selectDuoPartnerModal"
	aria-hidden="true">
	<div class="modal-header">
		<h3 id="selectDuoPartnerModalLabel">Select your Duo-Queue-Partner</h3>
	</div>
	<div class="modal-body">
		<div id="createGroupError"></div>
		<a href="#tab2" data-toggle="tab" class="btn btn-info" onclick="window.location = 'profile.php?ID={$steamID}#teams'">create a new Duo-Team</a>
		<br>
		<h4>Your Teams</h4>
				<div class="tab-pane {$active}" id="tab1">
					{if $groupData|count > 0 && $groupData|is_array}
						{foreach key=k item=v from=$groupData name=data_array}
							{if $v.data.count == 2}
								{if $v.data.allowed == true}
								<div class="well well-small group">
									<div class="row-fluid">
										<div class="span2">#{$k}</div>
										<div class="span10">
											<label class="radio"><input type="radio" name="radioAlreadyCreatedGroups" value="{$k}" />
												{if $v.members|count > 0 && $v.members|is_array}
													{foreach key=kk item=vv from=$v.members name=data_array}
														<p><img src="{$vv.Avatar}" alt="Avatar" />&nbsp;{$vv.Name}</p>
												    {/foreach}
												{/if}
											</label>
										</div>
									</div>
								</div>
								{else}
								<div class="well well-small group" style="color:#CCC">
									<div class="alert" align="center">Teammembers dont have the same Skill-Bracket anymore.</div>
									<div class="row-fluid">
										<div class="span2">#{$k}</div>
										<div class="span10">
											<label class="radio">
												{if $v.members|count > 0 && $v.members|is_array}
													{foreach key=kk item=vv from=$v.members name=data_array}
														<p><img src="{$vv.Avatar}" alt="Avatar" />&nbsp;{$vv.Name}</p>
												    {/foreach}
												{/if}
											</label>
										</div>
									</div>
								</div>
								{/if}
								
							{/if}
							
					    {/foreach}
					{/if}
				</div>
				{**
				<div class="tab-pane {$activeCreate}" id="tab2">
				
					<!--Suchschlitz zum user suchen -->
					<form class="form-inline">
					<label for="searchForPlayerInput">
								Search for Player:
					</label>
					<input class="span3" id="searchForPlayerInput" type="text" placeholder="type in your friends Steam-Name" >
					</form>
					<!-- wird durch JS befüllt -->
					<div id="searchForPlayerResults">	
						
					</div>
				</div>
				*}
	
	</div>
	<div class="modal-footer">
		<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"
			onclick="leaveQueue2();">Cancel</button>
		<button class="btn btn-success"
			 id="selectPartnerSubmitButton" data-value="false">Select Partner
			and Join Queue!</button>
	</div>
</div>