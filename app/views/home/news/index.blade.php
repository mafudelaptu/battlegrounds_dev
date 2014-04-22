@if(!empty($newsData) && !empty($newsData[0]))
<div class="row">
	<div class="col-sm-4">
		{{HTML::image("img/dc/svun.png", "Svun", array('width' => "100%"))}}
	</div>
	<div class="col-sm-8">
		
		@foreach($newsData as $k => $v)
		<div class="custom2H2">
			{{$v->title}}
		</div>
		<div class="newsContent">
			{{$v->content}}
		</div>
		@endforeach
</div>
</div>
@else
<div align="center">
{{HTML::image("img/dc/svun.png", "Svun", array('height' => "143"))}}
</div>
	<!-- 
	<div class="alert alert-warning">
		No active news!
	</div> -->
@endif
