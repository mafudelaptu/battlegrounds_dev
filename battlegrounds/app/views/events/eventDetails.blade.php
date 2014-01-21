<div class="row">
	<div class="col-sm-6">
		<dl class="dl-horizontal dlEventStyle">
			<dt>sign-in:</dt>
			<dd>{{$data->start_submission_at}}</dd>
			<dt>start:</dt>
			<dd>{{$data->start_at}}</dd>
			<dt class="t" title="Tournament-Type">TT:</dt>
			<dd>
				<span class="t badge badge-warning"
					data-original-title="{{$data->tournamenttype}}">{{$data->tt_shortcut}}</span>
				(Bo1)
			</dd>
			@if( $userAlreadyInEvent)
			<dt>Signed-in:</dt>
			<dd>
				<span class="label label-default">{{$eventSubsCount}}</span>
			</dd>
			<dt>Reached min:</dt>
			<dd>
				@if( $eventSubsCount >= $data->min_submissions) 
					<?php 
						$reachedText = "yes";
						$reachedClass = "success";
					 ?>
				 @else 
				 <?php 
						$reachedText = "no";
						$reachedClass = "danger";
					 ?>
				 @endif 
				 <span class="label label-{{$reachedClass}}">{{$reachedText}}</span>
			</dd>
			@endif
		</dl>

	</div>
	<div class="col-sm-6">
		<dl class="dl-horizontal dlEventStyle">
			<dt>Matchmode:</dt>
			<dd>
				<span class="t badge badge-info"
					data-original-title="{$data->matchmode}">{{$data->mm_shortcut}}</span>
			</dd>
			<dt>Region:</dt>
			<dd>
				<span class="t badge badge-danger"
					data-original-title="{{$data->region}}">{{$data->r_shortcut}}</span>
			</dd>
			<dt>Min Players:</dt>
			<dd>
				<span class="t label label-default" title="Minimum Players">{{$data->min_submissions}}</span>
			</dd>
			@if( $userAlreadyInEvent)
			<dt>Stacks:</dt>
			<dd>
				<?php 
					$vielfaches = $eventSubsCount/$data->min_submissions;
				 ?>
				@if(floor($vielfaches) == 0) 
				<?php 
					$vielfaches = 0;
				 ?>	
				@else 
			<?php 
				$vielfaches = floor($vielfaches);
			 ?>
				@endif 
				<span class="label label-default t" title="{{$vielfaches}} parallel Events will be created">{{$vielfaches}}</span>
			</dd>
			@endif
		</dl>
	</div>
</div>