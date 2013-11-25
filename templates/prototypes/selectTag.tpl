{if $data|is_array && $data|count > 0}
		<select name="{$id}" id="{$id}">
			{html_options options=$data selected=$selected}
		</select>
{/if}
	