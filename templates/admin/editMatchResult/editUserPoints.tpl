
{if $data|is_array && $data|count > 0}
	{foreach key=k item=v from=$data name=data_array}
		<div class="row-fluid" id="mRRow{$smarty.foreach.data_array.iteration}">
			<div class="span2"><input class="input-mini" type="text" value="{$v.PointsChange}" name="mRPC{$smarty.foreach.data_array.iteration}" id="mRPC{$smarty.foreach.data_array.iteration}"/></div>
			<div class="span8">
				<select name="mRPT{$smarty.foreach.data_array.iteration}">
				    {html_options options=$pointsTypeArr selected=$v.PointsTypeID}
				</select>
			</div>
			<div class="span2">
				<button type="button" class="btn btn-danger" onclick="mRRemoveUserPointsInputs({$smarty.foreach.data_array.iteration})"><i class="icon-remove-sign"></i></button>
			</div>
		</div>
	{/foreach}
{/if}
