@if(!empty($screenshots) && count($screenshots)>0)<div class="page-header">
<h2>Screenshots</h2>

</div>
<div>
	@foreach ($screenshots as $key => $value) 
	<div class="screenshot pull-left">
		<a href="{{$value['src']}}" target="_blank"><img src="{{$value['src']}}" class="t" title="{{$value['realname']}}">
		</a><br>
		<div align="center">
			<a href="{{$value['src']}}" target="_blank">
				{{$value['name']}}
			</a>
		</div>
	</div>
	

	@endforeach
</div>
@endif