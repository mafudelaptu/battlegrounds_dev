@if(!empty($matchtypes))
<ul class="nav nav-pills" id="ladderNavi">
	@foreach($matchtypes as $key => $mt)
		@if(empty($_GET['ladder']))
			@if($key === 0)
			<?php 
				$active = "active";
			?>
			@else
			<?php 
				$active = "";
			?>
			@endif
		@else
			@if($mt->id == $_GET['ladder'])
			<?php 
				$active = "active";
			?>
			@else
			<?php 
				$active = "";
			?>
			@endif
		@endif
	
	<li class="{{$active}}" data-mtid="{{$mt->id}}">
		<a href="?f=0&ladder={{$mt->id}}">{{$mt->name}}-Ladder</a>
	</li>
	@endforeach
</ul>
@endif