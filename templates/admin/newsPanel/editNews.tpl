<div>
		<input type="text" placeholder="Title" id="nPETitle" name="nPETitle" value="{$data.Title}"/>
	</div>
	<div>
		<textarea rows="10" style="width: 100%;" name="nPContent"
			id="nPEContent">{$data.Content}</textarea>
	</div>
	<div class="row-fluid">
		<div class="span3">
			<label for="nPEOrder">Order of News</label>
			<input size="16" type="text" value="{$data.Order}" id="nPEOrder">
		</div>
		<div class="span3">
			<label for="nPEShowDate">Show Date <small class="mute">*(0 = disabled)</small></label>
			{if $data.ShowTimestamp > 0}
				{assign "showDate" {$data.ShowTimestamp|date_format:'d-m-Y'}}
			{else}
				{assign "showDate" 0}
			{/if}
			<input class="datepicker" size="16" type="text" value="{$showDate}" data-date-format="dd-mm-yyyy" id="nPEShowDate">
		</div>
		<div class="span3">
			<label for="nPEEndDate">End Date <small class="mute">*(0 = disabled)</small></label>
			{if $data.EndTimestamp > 0}
				{assign "endDate" {$data.EndTimestamp|date_format:'d-m-Y'}}
			{else}
				{assign "endDate" 0}
			{/if}
			<input class="datepicker" size="16" type="text" value="{$endDate}" data-date-format="dd-mm-yyyy" id="nPEEndDate">
		</div>
		<div class="span3">
			<label for="nPEActive">Active:</label>
			{if $data.Active==1}
				{assign "checked" "checked='checked'"}
			{else}
				{assign "checked" ""}
			{/if}
			<input type="checkbox" id="nPEActive" {$checked}>
		</div>
	</div>