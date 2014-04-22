@section('content')

    <h1><a name="top"></a>{{$title}}</h1>
 @if(!empty($data) && count($data)>0)
    <div id="rulesInhaltsverzeichnis" class="well well-sm">
	<ol>
		@foreach($data as $k=>$v)
			<li>
				<a href="#anker_{{$k}}" alt="{{$v->caption}}">
					{{$v->caption}}
				</a>
			</li>
		@endforeach
	</ol>
</div>
<div id="faqContent">
	
		@foreach($data as $k=>$v)
			<h3><a name="anker_{{$k}}"></a>Section {{($k+1)}}: {{$v->caption}}</h3>
			{{$v->content}}
			<p class="text-right">
				<a href="#top">go to top</a>
			</p>
		@endforeach
	
</div>

<div class"clearer"><br></div>
@else
	<div class="alert alert-warning">
		no rules
	</div>
@endif
@stop