<div class="row">
	<div class="col-sm-4">
		{{HTML::image(Auth::user()->avatarFull, "Avatar", array("width"=>"100%"))}}
	</div>
	<div class="col-sm-8">
		@if(!empty($matchtypes) && count($matchtypes)>0)
		<div id="carousel-homeStats" class="carousel slide" data-ride="carousel">
			 <div class="carousel-inner" align="center">
			@foreach($matchtypes as $k=>$mt)
				@if($k===0)
					<?php $active = "active" ?>
				@else
					<?php $active = "" ?>
				@endif
				<div class="item {{$active}}">
					<div><strong>{{$mt->name}}</strong></div>
					<div>points: {{$points[$mt->id]}}</div>
					<div>rank: {{$stats[$mt->id]['Ranking']}}.</div>
				</div>
			@endforeach
			</div>
		 <!-- Controls -->
		  <a class="left carousel-control" href="#carousel-homeStats" data-slide="prev">
		    <span class="glyphicon glyphicon-chevron-left"></span>
		  </a>
		  <a class="right carousel-control" href="#carousel-homeStats" data-slide="next">
		    <span class="glyphicon glyphicon-chevron-right"></span>
		  </a>
		</div>
		@endif
	</div>
</div>