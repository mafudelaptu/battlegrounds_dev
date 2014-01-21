@if(count($data) > 0 && !empty($data))


<table
	class="table table-striped" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>#</th>
			<th>Player</th>
			<th>Points</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$vielfaches = $eventSubsCount/$event->min_submissions;
	 ?>
	@if(floor($vielfaches) == 0)
		<?php 
			$vielfaches = 1;
		 ?>
	@else
		<?php 
			$vielfaches = floor($vielfaches);
		 ?>
	@endif
	
	<?php 
		$grenzeRot = $vielfaches*$event->min_submissions;
		$i = 1;
	 ?>
	@foreach($data as $k => $v)
		
		@if(Auth::user()->id == $v->user_id)
		<?php 
			$trClass = "info";
		 ?>
		@elseif( $i > $grenzeRot)
		<?php 
			$trClass = "danger";
		 ?>
		@else
			<?php 
			$trClass = "success";
		 ?>
		@endif
		@if(time() >= strtotime($event->end_submission_at))
			@if($v->ready == "1")<?php 
				$trClass = "success";
			 ?>
			@elseif( $v->ready == "-1")
			<?php 
				$trClass = "danger";
			 ?>
			@else
				@if(Auth::user()->id == $v->user_id)
				<?php 
				$trClass = "info";
			 ?>
				@else
				<?php 
				$trClass = "";
			 ?>
			 	@endif
			@endif

		@endif
		
		<tr class="{{$trClass}}">
			<td><strong>{{$i}}.</strong></td>
			<td><a href="profile/{{$v->user_id}}" target="_blank"><img src="{{$v->avatar}}" alt="{{$v->name}}'s Avatar"> {{$v->name}}</a></td>
			<td>{{$v->points}}</td>
		</tr>
		<?php 
		$i++;
		 ?>
    @endforeach
    </tbody>
</table>
@else
	no Data
@endif